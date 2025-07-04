import SchemaManager from '../../services/SchemaManager.js'

const blueprintController = {
  // List blueprints
  async listBlueprints(request, reply) {
    const { page = 1, limit = 10, search = '', category = '' } = request.query
    const offset = (page - 1) * limit

    let query = this.knex('blueprints')
      .select('id', 'code', 'title', 'description', 'category', 'table_name', 'is_active', 'is_locked', 'created_at', 'updated_at')
      .whereNull('deleted_at')

    if (search) {
      query = query.where(function() {
        this.where('title', 'ilike', `%${search}%`)
          .orWhere('code', 'ilike', `%${search}%`)
          .orWhere('description', 'ilike', `%${search}%`)
      })
    }

    if (category) {
      query = query.where('category', category)
    }

    const blueprints = await query
      .orderBy('created_at', 'desc')
      .limit(limit)
      .offset(offset)

    const total = await this.knex('blueprints')
      .whereNull('deleted_at')
      .modify(function(queryBuilder) {
        if (search) {
          queryBuilder.where(function() {
            this.where('title', 'ilike', `%${search}%`)
              .orWhere('code', 'ilike', `%${search}%`)
              .orWhere('description', 'ilike', `%${search}%`)
          })
        }
        if (category) {
          queryBuilder.where('category', category)
        }
      })
      .count('* as count')
      .first()

    return {
      blueprints,
      pagination: {
        page: parseInt(page),
        limit: parseInt(limit),
        total: parseInt(total.count),
        pages: Math.ceil(total.count / limit)
      }
    }
  },

  // Get blueprint by ID with full details
  async getBlueprint(request, reply) {
    const { id } = request.params

    const blueprint = await this.knex('blueprints')
      .where('id', id)
      .whereNull('deleted_at')
      .first()

    if (!blueprint) {
      return reply.status(404).send({
        error: 'Blueprint not found',
        message: 'Blueprint does not exist'
      })
    }

    // Load sections
    const sections = await this.knex('blueprint_sections')
      .where('blueprint_id', id)
      .orderBy('sequence', 'asc')

    // Load fields
    const fields = await this.knex('blueprint_fields')
      .where('blueprint_id', id)
      .orderBy('sequence', 'asc')

    // Load stages
    const stages = await this.knex('blueprint_stages')
      .where('blueprint_id', id)
      .orderBy('sequence', 'asc')

    return {
      ...blueprint,
      sections,
      fields,
      stages
    }
  },

  // Create new blueprint
  async createBlueprint(request, reply) {
    const { code, title, description, category, sections = [], fields = [], stages = [] } = request.body
    const schemaManager = new SchemaManager(this.knex, this.log)

    try {
      // Validate schema first
      const tempBlueprint = { code, title, description, category }
      schemaManager.validateBlueprintSchema(tempBlueprint, sections, fields, stages)

      // Check if blueprint code already exists
      const existingBlueprint = await this.knex('blueprints')
        .where('code', code)
        .whereNull('deleted_at')
        .first()

      if (existingBlueprint) {
        return reply.status(409).send({
          error: 'Blueprint exists',
          message: 'Blueprint with this code already exists'
        })
      }

      await this.knex.transaction(async (trx) => {
        // Create blueprint
        const [blueprint] = await trx('blueprints')
          .insert({
            code,
            title,
            description,
            category,
            is_active: true,
            created_by: request.user.id,
            updated_by: request.user.id
          })
          .returning('*')

        // Create sections
        const createdSections = []
        for (const section of sections) {
          const [createdSection] = await trx('blueprint_sections')
            .insert({
              blueprint_id: blueprint.id,
              code: section.code,
              title: section.title,
              description: section.description,
              sequence: section.sequence,
              is_collapsible: section.is_collapsible,
              is_visible: section.is_visible,
              metadata: section.metadata || {},
              created_by: request.user.id,
              updated_by: request.user.id
            })
            .returning('*')
          createdSections.push(createdSection)
        }

        // Create fields
        const createdFields = []
        for (const field of fields) {
          const [createdField] = await trx('blueprint_fields')
            .insert({
              blueprint_id: blueprint.id,
              section_id: field.section_id || null,
              code: field.code,
              title: field.title,
              description: field.description,
              field_type: field.field_type,
              sequence: field.sequence,
              is_required: field.is_required,
              is_unique: field.is_unique,
              is_indexed: field.is_indexed,
              is_searchable: field.is_searchable,
              config: field.config || {},
              validation: field.validation || {},
              metadata: field.metadata || {},
              created_by: request.user.id,
              updated_by: request.user.id
            })
            .returning('*')
          createdFields.push(createdField)
        }

        // Create stages
        const createdStages = []
        for (const stage of stages) {
          const [createdStage] = await trx('blueprint_stages')
            .insert({
              blueprint_id: blueprint.id,
              code: stage.code,
              title: stage.title,
              description: stage.description,
              sequence: stage.sequence,
              is_initial: stage.is_initial,
              is_final: stage.is_final,
              metadata: stage.metadata || {},
              created_by: request.user.id,
              updated_by: request.user.id
            })
            .returning('*')
          createdStages.push(createdStage)
        }

        // Create schema version
        const schemaSnapshot = {
          blueprint,
          sections: createdSections,
          fields: createdFields,
          stages: createdStages
        }

        await trx('blueprint_versions')
          .insert({
            blueprint_id: blueprint.id,
            version_number: 1,
            version_name: 'Initial Version',
            schema_snapshot: JSON.stringify(schemaSnapshot),
            change_summary: 'Initial blueprint creation',
            migration_status: 'pending',
            created_by: request.user.id
          })

        // Create database table using Schema Manager
        const tableName = await schemaManager.createTableForBlueprint(
          blueprint, 
          createdSections, 
          createdFields, 
          createdStages
        )

        // Update version as applied
        await trx('blueprint_versions')
          .where('blueprint_id', blueprint.id)
          .where('version_number', 1)
          .update({
            migration_status: 'applied',
            applied_at: trx.fn.now()
          })

        // Log activity
        await trx('activity_logs').insert({
          user_id: request.user.id,
          action: 'create_blueprint',
          entity_type: 'blueprint',
          entity_id: blueprint.id,
          ip_address: request.ip,
          user_agent: request.headers['user-agent'],
          data: { code, title, table_name: tableName }
        })

        return {
          ...blueprint,
          sections: createdSections,
          fields: createdFields,
          stages: createdStages,
          table_name: tableName
        }
      })
    } catch (error) {
      this.log.error('Failed to create blueprint:', error)
      
      if (error.code === 'INVALID_BLUEPRINT_SCHEMA') {
        return reply.status(400).send({
          error: 'Invalid Schema',
          message: error.message,
          details: error.details
        })
      }

      throw error
    }
  },

  // Update blueprint
  async updateBlueprint(request, reply) {
    const { id } = request.params
    const { title, description, category, sections = [], fields = [], stages = [] } = request.body
    const schemaManager = new SchemaManager(this.knex, this.log)

    try {
      // Get existing blueprint
      const blueprint = await this.knex('blueprints')
        .where('id', id)
        .whereNull('deleted_at')
        .first()

      if (!blueprint) {
        return reply.status(404).send({
          error: 'Blueprint not found',
          message: 'Blueprint does not exist'
        })
      }

      if (blueprint.is_locked) {
        return reply.status(409).send({
          error: 'Blueprint locked',
          message: 'Blueprint is locked and cannot be modified'
        })
      }

      // Validate new schema
      const updatedBlueprint = { ...blueprint, title, description, category }
      schemaManager.validateBlueprintSchema(updatedBlueprint, sections, fields, stages)

      // Get existing fields for comparison
      const existingFields = await this.knex('blueprint_fields')
        .where('blueprint_id', id)

      // Analyze schema changes
      const changes = schemaManager.analyzeSchemaChanges(existingFields, fields)

      await this.knex.transaction(async (trx) => {
        // Update blueprint
        await trx('blueprints')
          .where('id', id)
          .update({
            title,
            description,
            category,
            updated_by: request.user.id,
            updated_at: trx.fn.now()
          })

        // Get next version number
        const lastVersion = await trx('blueprint_versions')
          .where('blueprint_id', id)
          .orderBy('version_number', 'desc')
          .first()

        const nextVersionNumber = lastVersion ? lastVersion.version_number + 1 : 1

        // Update sections (simple replace for now)
        await trx('blueprint_sections').where('blueprint_id', id).del()
        for (const section of sections) {
          await trx('blueprint_sections').insert({
            blueprint_id: id,
            code: section.code,
            title: section.title,
            description: section.description,
            sequence: section.sequence,
            is_collapsible: section.is_collapsible,
            is_visible: section.is_visible,
            metadata: section.metadata || {},
            created_by: request.user.id,
            updated_by: request.user.id
          })
        }

        // Update fields (simple replace for now)
        await trx('blueprint_fields').where('blueprint_id', id).del()
        const updatedFields = []
        for (const field of fields) {
          const [updatedField] = await trx('blueprint_fields')
            .insert({
              blueprint_id: id,
              section_id: field.section_id || null,
              code: field.code,
              title: field.title,
              description: field.description,
              field_type: field.field_type,
              sequence: field.sequence,
              is_required: field.is_required,
              is_unique: field.is_unique,
              is_indexed: field.is_indexed,
              is_searchable: field.is_searchable,
              config: field.config || {},
              validation: field.validation || {},
              metadata: field.metadata || {},
              created_by: request.user.id,
              updated_by: request.user.id
            })
            .returning('*')
          updatedFields.push(updatedField)
        }

        // Update stages (simple replace for now)
        await trx('blueprint_stages').where('blueprint_id', id).del()
        for (const stage of stages) {
          await trx('blueprint_stages').insert({
            blueprint_id: id,
            code: stage.code,
            title: stage.title,
            description: stage.description,
            sequence: stage.sequence,
            is_initial: stage.is_initial,
            is_final: stage.is_final,
            metadata: stage.metadata || {},
            created_by: request.user.id,
            updated_by: request.user.id
          })
        }

        // Create new schema version
        const schemaSnapshot = {
          blueprint: updatedBlueprint,
          sections,
          fields: updatedFields,
          stages
        }

        await trx('blueprint_versions')
          .insert({
            blueprint_id: id,
            version_number: nextVersionNumber,
            schema_snapshot: JSON.stringify(schemaSnapshot),
            change_summary: `Blueprint updated - ${Object.keys(changes).filter(k => changes[k].length > 0).join(', ')}`,
            migration_status: 'pending',
            created_by: request.user.id
          })

        // Apply schema changes if table exists
        if (blueprint.table_name && (changes.added.length > 0 || changes.removed.length > 0 || changes.modified.length > 0)) {
          await schemaManager.applySchemaChanges(blueprint, changes)
          
          // Update version as applied
          await trx('blueprint_versions')
            .where('blueprint_id', id)
            .where('version_number', nextVersionNumber)
            .update({
              migration_status: 'applied',
              applied_at: trx.fn.now()
            })
        }

        // Log activity
        await trx('activity_logs').insert({
          user_id: request.user.id,
          action: 'update_blueprint',
          entity_type: 'blueprint',
          entity_id: id,
          ip_address: request.ip,
          user_agent: request.headers['user-agent'],
          data: { 
            title, 
            description, 
            category,
            changes: Object.keys(changes).filter(k => changes[k].length > 0)
          }
        })

        return { message: 'Blueprint updated successfully' }
      })
    } catch (error) {
      this.log.error('Failed to update blueprint:', error)
      
      if (error.code === 'INVALID_BLUEPRINT_SCHEMA') {
        return reply.status(400).send({
          error: 'Invalid Schema',
          message: error.message,
          details: error.details
        })
      }

      if (error.code === 'SCHEMA_MIGRATION_FAILED') {
        return reply.status(500).send({
          error: 'Schema Migration Failed',
          message: error.message
        })
      }

      throw error
    }
  },

  // Apply schema changes to database
  async applySchema(request, reply) {
    const { id } = request.params
    const schemaManager = new SchemaManager(this.knex, this.log)

    try {
      const blueprint = await this.knex('blueprints')
        .where('id', id)
        .whereNull('deleted_at')
        .first()

      if (!blueprint) {
        return reply.status(404).send({
          error: 'Blueprint not found',
          message: 'Blueprint does not exist'
        })
      }

      // Get blueprint components
      const sections = await this.knex('blueprint_sections')
        .where('blueprint_id', id)
        .orderBy('sequence', 'asc')

      const fields = await this.knex('blueprint_fields')
        .where('blueprint_id', id)
        .orderBy('sequence', 'asc')

      const stages = await this.knex('blueprint_stages')
        .where('blueprint_id', id)
        .orderBy('sequence', 'asc')

      if (!blueprint.table_name) {
        // Create table if it doesn't exist
        const tableName = await schemaManager.createTableForBlueprint(
          blueprint, sections, fields, stages
        )

        // Log activity
        await this.knex('activity_logs').insert({
          user_id: request.user.id,
          action: 'apply_schema',
          entity_type: 'blueprint',
          entity_id: id,
          ip_address: request.ip,
          user_agent: request.headers['user-agent'],
          data: { table_name: tableName, action: 'create' }
        })

        return { 
          message: 'Schema applied successfully',
          table_name: tableName,
          action: 'created'
        }
      } else {
        return { 
          message: 'Schema already applied',
          table_name: blueprint.table_name,
          action: 'exists'
        }
      }
    } catch (error) {
      this.log.error('Failed to apply schema:', error)
      throw error
    }
  },

  // Delete blueprint (soft delete)
  async deleteBlueprint(request, reply) {
    const { id } = request.params
    const schemaManager = new SchemaManager(this.knex, this.log)

    try {
      const blueprint = await this.knex('blueprints')
        .where('id', id)
        .whereNull('deleted_at')
        .first()

      if (!blueprint) {
        return reply.status(404).send({
          error: 'Blueprint not found',
          message: 'Blueprint does not exist'
        })
      }

      if (blueprint.is_locked) {
        return reply.status(409).send({
          error: 'Blueprint locked',
          message: 'Blueprint is locked and cannot be deleted'
        })
      }

      // Check if there are any documents
      if (blueprint.table_name) {
        const documentCount = await this.knex(blueprint.table_name)
          .whereNull('deleted_at')
          .count('* as count')
          .first()

        if (parseInt(documentCount.count) > 0) {
          return reply.status(409).send({
            error: 'Blueprint has documents',
            message: `Cannot delete blueprint with ${documentCount.count} documents. Archive the blueprint instead.`
          })
        }
      }

      await this.knex.transaction(async (trx) => {
        // Soft delete blueprint
        await trx('blueprints')
          .where('id', id)
          .update({
            deleted_at: trx.fn.now(),
            updated_by: request.user.id
          })

        // Archive table if it exists
        if (blueprint.table_name) {
          await schemaManager.dropTableForBlueprint(blueprint)
        }

        // Log activity
        await trx('activity_logs').insert({
          user_id: request.user.id,
          action: 'delete_blueprint',
          entity_type: 'blueprint',
          entity_id: id,
          ip_address: request.ip,
          user_agent: request.headers['user-agent'],
          data: { code: blueprint.code, table_name: blueprint.table_name }
        })

        return { message: 'Blueprint deleted successfully' }
      })
    } catch (error) {
      this.log.error('Failed to delete blueprint:', error)
      throw error
    }
  },

  // Get blueprint versions
  async getBlueprintVersions(request, reply) {
    const { id } = request.params
    const { page = 1, limit = 10 } = request.query
    const offset = (page - 1) * limit

    const versions = await this.knex('blueprint_versions')
      .select('id', 'version_number', 'version_name', 'change_summary', 'migration_status', 'created_at', 'applied_at')
      .where('blueprint_id', id)
      .orderBy('version_number', 'desc')
      .limit(limit)
      .offset(offset)

    const total = await this.knex('blueprint_versions')
      .where('blueprint_id', id)
      .count('* as count')
      .first()

    return {
      versions,
      pagination: {
        page: parseInt(page),
        limit: parseInt(limit),
        total: parseInt(total.count),
        pages: Math.ceil(total.count / limit)
      }
    }
  },

  // Blueprint sections
  async getBlueprintSections(request, reply) {
    const { id } = request.params

    const sections = await this.knex('blueprint_sections')
      .where('blueprint_id', id)
      .orderBy('sequence', 'asc')

    return { sections }
  },

  async createSection(request, reply) {
    const { id } = request.params
    const { code, title, description, sequence, is_collapsible = true, is_visible = true, metadata = {} } = request.body

    // Check if blueprint exists
    const blueprint = await this.knex('blueprints')
      .where('id', id)
      .whereNull('deleted_at')
      .first()

    if (!blueprint) {
      return reply.status(404).send({
        error: 'Blueprint not found',
        message: 'Blueprint does not exist'
      })
    }

    const [section] = await this.knex('blueprint_sections')
      .insert({
        blueprint_id: id,
        code,
        title,
        description,
        sequence,
        is_collapsible,
        is_visible,
        metadata,
        created_by: request.user.id,
        updated_by: request.user.id
      })
      .returning('*')

    return { section }
  },

  async updateSection(request, reply) {
    const { id, sectionId } = request.params
    const { title, description, sequence, is_collapsible, is_visible, metadata } = request.body

    const [section] = await this.knex('blueprint_sections')
      .where('id', sectionId)
      .where('blueprint_id', id)
      .update({
        title,
        description,
        sequence,
        is_collapsible,
        is_visible,
        metadata,
        updated_by: request.user.id,
        updated_at: this.knex.fn.now()
      })
      .returning('*')

    if (!section) {
      return reply.status(404).send({
        error: 'Section not found',
        message: 'Section does not exist'
      })
    }

    return { section }
  },

  async deleteSection(request, reply) {
    const { id, sectionId } = request.params

    const deleted = await this.knex('blueprint_sections')
      .where('id', sectionId)
      .where('blueprint_id', id)
      .del()

    if (!deleted) {
      return reply.status(404).send({
        error: 'Section not found',
        message: 'Section does not exist'
      })
    }

    return { message: 'Section deleted successfully' }
  },

  // Blueprint fields
  async getBlueprintFields(request, reply) {
    const { id } = request.params

    const fields = await this.knex('blueprint_fields')
      .where('blueprint_id', id)
      .orderBy('sequence', 'asc')

    return { fields }
  },

  async createField(request, reply) {
    const { id } = request.params
    const { 
      section_id, 
      code, 
      title, 
      description, 
      field_type, 
      sequence, 
      is_required = false, 
      is_unique = false, 
      is_indexed = false, 
      is_searchable = false, 
      config = {}, 
      validation = {}, 
      metadata = {} 
    } = request.body

    const [field] = await this.knex('blueprint_fields')
      .insert({
        blueprint_id: id,
        section_id,
        code,
        title,
        description,
        field_type,
        sequence,
        is_required,
        is_unique,
        is_indexed,
        is_searchable,
        config,
        validation,
        metadata,
        created_by: request.user.id,
        updated_by: request.user.id
      })
      .returning('*')

    return { field }
  },

  async updateField(request, reply) {
    const { id, fieldId } = request.params
    const { 
      title, 
      description, 
      field_type, 
      sequence, 
      is_required, 
      is_unique, 
      is_indexed, 
      is_searchable, 
      config, 
      validation, 
      metadata 
    } = request.body

    const [field] = await this.knex('blueprint_fields')
      .where('id', fieldId)
      .where('blueprint_id', id)
      .update({
        title,
        description,
        field_type,
        sequence,
        is_required,
        is_unique,
        is_indexed,
        is_searchable,
        config,
        validation,
        metadata,
        updated_by: request.user.id,
        updated_at: this.knex.fn.now()
      })
      .returning('*')

    if (!field) {
      return reply.status(404).send({
        error: 'Field not found',
        message: 'Field does not exist'
      })
    }

    return { field }
  },

  async deleteField(request, reply) {
    const { id, fieldId } = request.params

    const deleted = await this.knex('blueprint_fields')
      .where('id', fieldId)
      .where('blueprint_id', id)
      .del()

    if (!deleted) {
      return reply.status(404).send({
        error: 'Field not found',
        message: 'Field does not exist'
      })
    }

    return { message: 'Field deleted successfully' }
  },

  // Blueprint stages
  async getBlueprintStages(request, reply) {
    const { id } = request.params

    const stages = await this.knex('blueprint_stages')
      .where('blueprint_id', id)
      .orderBy('sequence', 'asc')

    return { stages }
  },

  async createStage(request, reply) {
    const { id } = request.params
    const { 
      code, 
      title, 
      description, 
      sequence, 
      is_initial = false, 
      is_final = false, 
      metadata = {} 
    } = request.body

    const [stage] = await this.knex('blueprint_stages')
      .insert({
        blueprint_id: id,
        code,
        title,
        description,
        sequence,
        is_initial,
        is_final,
        metadata,
        created_by: request.user.id,
        updated_by: request.user.id
      })
      .returning('*')

    return { stage }
  },

  async updateStage(request, reply) {
    const { id, stageId } = request.params
    const { title, description, sequence, is_initial, is_final, metadata } = request.body

    const [stage] = await this.knex('blueprint_stages')
      .where('id', stageId)
      .where('blueprint_id', id)
      .update({
        title,
        description,
        sequence,
        is_initial,
        is_final,
        metadata,
        updated_by: request.user.id,
        updated_at: this.knex.fn.now()
      })
      .returning('*')

    if (!stage) {
      return reply.status(404).send({
        error: 'Stage not found',
        message: 'Stage does not exist'
      })
    }

    return { stage }
  },

  async deleteStage(request, reply) {
    const { id, stageId } = request.params

    const deleted = await this.knex('blueprint_stages')
      .where('id', stageId)
      .where('blueprint_id', id)
      .del()

    if (!deleted) {
      return reply.status(404).send({
        error: 'Stage not found',
        message: 'Stage does not exist'
      })
    }

    return { message: 'Stage deleted successfully' }
  },

  // Blueprint permissions
  async getBlueprintPermissions(request, reply) {
    const { id } = request.params

    const permissions = await this.knex('blueprint_permissions')
      .join('groups', 'groups.id', 'blueprint_permissions.group_id')
      .select(
        'blueprint_permissions.*',
        'groups.code as group_code',
        'groups.title as group_title'
      )
      .where('blueprint_permissions.blueprint_id', id)

    return { permissions }
  },

  async setPermission(request, reply) {
    const { id } = request.params
    const { group_id, permission_code, permission_level, target_code, metadata = {} } = request.body

    const [permission] = await this.knex('blueprint_permissions')
      .insert({
        blueprint_id: id,
        group_id,
        permission_code,
        permission_level,
        target_code,
        metadata,
        created_by: request.user.id
      })
      .onConflict(['blueprint_id', 'group_id', 'permission_code', 'permission_level', 'target_code'])
      .merge(['metadata', 'updated_at'])
      .returning('*')

    return { permission }
  },

  async removePermission(request, reply) {
    const { id, permissionId } = request.params

    const deleted = await this.knex('blueprint_permissions')
      .where('id', permissionId)
      .where('blueprint_id', id)
      .del()

    if (!deleted) {
      return reply.status(404).send({
        error: 'Permission not found',
        message: 'Permission does not exist'
      })
    }

    return { message: 'Permission removed successfully' }
  }
}

export default blueprintController