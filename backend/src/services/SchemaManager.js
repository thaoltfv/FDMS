/**
 * Schema Manager Service
 * 
 * This is the most critical component of the FDMS system.
 * It translates blueprint definitions into SQL DDL statements
 * and manages the lifecycle of dynamic tables.
 */

class SchemaManager {
  constructor(knex, logger) {
    this.knex = knex
    this.logger = logger
  }

  /**
   * Field type mapping from blueprint field types to PostgreSQL types
   */
  getFieldTypeMapping() {
    return {
      // Basic types
      'short_text': 'VARCHAR(255)',
      'long_text': 'TEXT',
      'rich_text': 'TEXT',
      'integer': 'INTEGER',
      'decimal': 'DECIMAL(15,2)',
      'real': 'REAL',
      'boolean': 'BOOLEAN',
      'date': 'DATE',
      'datetime': 'TIMESTAMP WITH TIME ZONE',
      'time': 'TIME',

      // Advanced types
      'email': 'VARCHAR(255)',
      'phone': 'VARCHAR(50)',
      'url': 'TEXT',
      'json': 'JSONB',
      'array': 'JSONB',
      'object': 'JSONB',
      'file': 'INTEGER', // References file_metadata.id
      'files': 'INTEGER[]', // Array of file IDs

      // Geospatial types (PostGIS)
      'point': 'GEOMETRY(POINT, 4326)',
      'polygon': 'GEOMETRY(POLYGON, 4326)',
      'geometry': 'GEOMETRY',

      // Relational types
      'reference': 'INTEGER', // Foreign key to another table
      'multi_reference': 'INTEGER[]', // Array of foreign keys

      // Special types
      'uuid': 'UUID',
      'encrypted': 'TEXT'
    }
  }

  /**
   * Generate a safe table name from blueprint code
   */
  generateTableName(blueprintCode) {
    const sanitized = blueprintCode
      .toLowerCase()
      .replace(/[^a-z0-9_]/g, '_')
      .replace(/_{2,}/g, '_')
      .replace(/^_|_$/g, '')

    // Ensure it doesn't start with a number
    const tableName = sanitized.match(/^[0-9]/) ? `t_${sanitized}` : sanitized

    // Add suffix for documents
    return `${tableName}_documents`
  }

  /**
   * Generate column name from field code
   */
  generateColumnName(fieldCode) {
    return fieldCode
      .toLowerCase()
      .replace(/[^a-z0-9_]/g, '_')
      .replace(/_{2,}/g, '_')
      .replace(/^_|_$/g, '')
  }

  /**
   * Create a new table for a blueprint
   */
  async createTableForBlueprint(blueprint, sections, fields, stages) {
    const tableName = this.generateTableName(blueprint.code)
    const typeMapping = this.getFieldTypeMapping()

    try {
      this.logger.info(`Creating table ${tableName} for blueprint ${blueprint.code}`)

      // Start transaction
      await this.knex.transaction(async (trx) => {
        // Create the main document table
        await trx.schema.createTable(tableName, (table) => {
          // Standard columns
          table.increments('id').primary()
          table.uuid('document_uuid').defaultTo(trx.raw('uuid_generate_v4()')).unique()
          table.integer('blueprint_id').notNullable().references('id').inTable('blueprints')
          table.integer('stage_id').references('id').inTable('blueprint_stages')

          // Add custom fields
          for (const field of fields) {
            const columnName = this.generateColumnName(field.code)
            const fieldType = typeMapping[field.field_type] || 'TEXT'
            
            let column
            
            // Handle specific field types
            if (field.field_type === 'reference') {
              // Foreign key reference
              const refTable = field.config?.reference_table || 'unknown'
              column = table.integer(columnName)
              if (field.config?.reference_table) {
                column.references('id').inTable(refTable)
              }
            } else if (field.field_type === 'file') {
              // File reference
              column = table.integer(columnName).references('id').inTable('file_metadata')
            } else if (fieldType.includes('VARCHAR')) {
              // Handle VARCHAR with custom length
              const maxLength = field.config?.max_length || 255
              column = table.string(columnName, maxLength)
            } else if (fieldType.includes('DECIMAL')) {
              // Handle decimal with custom precision
              const precision = field.config?.precision || 15
              const scale = field.config?.scale || 2
              column = table.decimal(columnName, precision, scale)
            } else {
              // Use raw SQL for complex types
              column = table.specificType(columnName, fieldType)
            }

            // Apply constraints
            if (field.is_required) {
              column.notNullable()
            }

            if (field.is_unique) {
              column.unique()
            }

            // Add default value if specified
            if (field.config?.default_value !== undefined) {
              column.defaultTo(field.config.default_value)
            }

            // Add check constraints if specified
            if (field.validation?.min_value !== undefined && field.validation?.max_value !== undefined) {
              table.check(`${columnName} >= ${field.validation.min_value} AND ${columnName} <= ${field.validation.max_value}`)
            }
          }

          // Metadata columns
          table.timestamp('created_at', { useTz: true }).defaultTo(trx.fn.now())
          table.timestamp('updated_at', { useTz: true }).defaultTo(trx.fn.now())
          table.integer('created_by').references('id').inTable('users')
          table.integer('updated_by').references('id').inTable('users')
          table.timestamp('submitted_at', { useTz: true })
          table.timestamp('approved_at', { useTz: true })
          table.integer('approved_by').references('id').inTable('users')
          table.timestamp('deleted_at', { useTz: true })
          table.jsonb('metadata').defaultTo('{}')
        })

        // Create indexes for performance
        await this.createIndexesForTable(trx, tableName, fields)

        // Create version table for this blueprint
        const versionTableName = `${tableName}_versions`
        await trx.schema.createTable(versionTableName, (table) => {
          table.increments('id').primary()
          table.integer('document_id').notNullable()
          table.integer('version_number').notNullable()
          table.jsonb('data_snapshot').notNullable()
          table.jsonb('diff')
          table.integer('stage_id').references('id').inTable('blueprint_stages')
          table.timestamp('created_at', { useTz: true }).defaultTo(trx.fn.now())
          table.integer('created_by').references('id').inTable('users')
          table.text('change_summary')
          table.jsonb('metadata').defaultTo('{}')

          table.unique(['document_id', 'version_number'])
          table.index('document_id')
          table.index('created_at')
        })

        // Update blueprint with table name
        await trx('blueprints')
          .where('id', blueprint.id)
          .update({ table_name: tableName })

        this.logger.info(`Successfully created table ${tableName}`)
      })

      return tableName
    } catch (error) {
      this.logger.error(`Failed to create table for blueprint ${blueprint.code}:`, error)
      throw new Error(`Schema creation failed: ${error.message}`)
    }
  }

  /**
   * Create indexes for a table based on field configurations
   */
  async createIndexesForTable(trx, tableName, fields) {
    for (const field of fields) {
      const columnName = this.generateColumnName(field.code)

      // Create index if field is marked as indexed
      if (field.is_indexed) {
        await trx.schema.alterTable(tableName, (table) => {
          table.index(columnName, `idx_${tableName}_${columnName}`)
        })
      }

      // Create full-text search index for text fields
      if (field.is_searchable && ['short_text', 'long_text', 'rich_text'].includes(field.field_type)) {
        await trx.raw(`
          CREATE INDEX idx_${tableName}_${columnName}_fts 
          ON ${tableName} 
          USING gin(to_tsvector('english', ${columnName}))
        `)
      }

      // Create spatial index for geometry fields
      if (['point', 'polygon', 'geometry'].includes(field.field_type)) {
        await trx.raw(`
          CREATE INDEX idx_${tableName}_${columnName}_gist 
          ON ${tableName} 
          USING gist(${columnName})
        `)
      }
    }

    // Standard indexes
    await trx.schema.alterTable(tableName, (table) => {
      table.index('blueprint_id')
      table.index('stage_id')
      table.index('created_by')
      table.index('created_at')
      table.index(['blueprint_id', 'stage_id'])
    })
  }

  /**
   * Analyze changes between old and new blueprint schema
   */
  analyzeSchemaChanges(oldFields, newFields) {
    const oldFieldMap = new Map(oldFields.map(f => [f.code, f]))
    const newFieldMap = new Map(newFields.map(f => [f.code, f]))

    const changes = {
      added: [],
      modified: [],
      removed: [],
      renamed: []
    }

    // Find added and modified fields
    for (const newField of newFields) {
      const oldField = oldFieldMap.get(newField.code)
      
      if (!oldField) {
        changes.added.push(newField)
      } else if (this.isFieldModified(oldField, newField)) {
        changes.modified.push({
          old: oldField,
          new: newField,
          changes: this.getFieldChanges(oldField, newField)
        })
      }
    }

    // Find removed fields
    for (const oldField of oldFields) {
      if (!newFieldMap.has(oldField.code)) {
        changes.removed.push(oldField)
      }
    }

    return changes
  }

  /**
   * Check if a field has been modified
   */
  isFieldModified(oldField, newField) {
    const compareProperties = [
      'field_type', 'is_required', 'is_unique', 'is_indexed', 'is_searchable'
    ]

    for (const prop of compareProperties) {
      if (oldField[prop] !== newField[prop]) {
        return true
      }
    }

    // Compare config objects
    return JSON.stringify(oldField.config || {}) !== JSON.stringify(newField.config || {})
  }

  /**
   * Get specific changes between old and new field
   */
  getFieldChanges(oldField, newField) {
    const changes = {}

    if (oldField.field_type !== newField.field_type) {
      changes.type = { from: oldField.field_type, to: newField.field_type }
    }

    if (oldField.is_required !== newField.is_required) {
      changes.required = { from: oldField.is_required, to: newField.is_required }
    }

    if (oldField.is_unique !== newField.is_unique) {
      changes.unique = { from: oldField.is_unique, to: newField.is_unique }
    }

    if (oldField.is_indexed !== newField.is_indexed) {
      changes.indexed = { from: oldField.is_indexed, to: newField.is_indexed }
    }

    const oldConfig = oldField.config || {}
    const newConfig = newField.config || {}
    
    if (JSON.stringify(oldConfig) !== JSON.stringify(newConfig)) {
      changes.config = { from: oldConfig, to: newConfig }
    }

    return changes
  }

  /**
   * Apply schema changes to an existing table
   */
  async applySchemaChanges(blueprint, changes) {
    const tableName = blueprint.table_name
    const typeMapping = this.getFieldTypeMapping()

    try {
      this.logger.info(`Applying schema changes to table ${tableName}`)

      await this.knex.transaction(async (trx) => {
        // Handle added fields
        for (const field of changes.added) {
          await this.addColumn(trx, tableName, field, typeMapping)
        }

        // Handle removed fields (rename to preserve data)
        for (const field of changes.removed) {
          await this.removeColumn(trx, tableName, field)
        }

        // Handle modified fields
        for (const modification of changes.modified) {
          await this.modifyColumn(trx, tableName, modification, typeMapping)
        }

        this.logger.info(`Successfully applied schema changes to ${tableName}`)
      })
    } catch (error) {
      this.logger.error(`Failed to apply schema changes to ${tableName}:`, error)
      throw new Error(`Schema migration failed: ${error.message}`)
    }
  }

  /**
   * Add a new column to the table
   */
  async addColumn(trx, tableName, field, typeMapping) {
    const columnName = this.generateColumnName(field.code)
    const fieldType = typeMapping[field.field_type] || 'TEXT'

    await trx.schema.alterTable(tableName, (table) => {
      let column

      if (field.field_type === 'reference') {
        column = table.integer(columnName)
        if (field.config?.reference_table) {
          column.references('id').inTable(field.config.reference_table)
        }
      } else if (field.field_type === 'file') {
        column = table.integer(columnName).references('id').inTable('file_metadata')
      } else if (fieldType.includes('VARCHAR')) {
        const maxLength = field.config?.max_length || 255
        column = table.string(columnName, maxLength)
      } else if (fieldType.includes('DECIMAL')) {
        const precision = field.config?.precision || 15
        const scale = field.config?.scale || 2
        column = table.decimal(columnName, precision, scale)
      } else {
        column = table.specificType(columnName, fieldType)
      }

      // Only add NOT NULL if there's a default value or it's not required
      if (field.is_required && field.config?.default_value !== undefined) {
        column.notNullable().defaultTo(field.config.default_value)
      } else if (field.config?.default_value !== undefined) {
        column.defaultTo(field.config.default_value)
      }

      if (field.is_unique) {
        column.unique()
      }
    })

    // Add index if needed
    if (field.is_indexed) {
      await trx.schema.alterTable(tableName, (table) => {
        table.index(columnName, `idx_${tableName}_${columnName}`)
      })
    }

    // Create full-text search index if needed
    if (field.is_searchable && ['short_text', 'long_text', 'rich_text'].includes(field.field_type)) {
      await trx.raw(`
        CREATE INDEX idx_${tableName}_${columnName}_fts 
        ON ${tableName} 
        USING gin(to_tsvector('english', ${columnName}))
      `)
    }

    this.logger.info(`Added column ${columnName} to ${tableName}`)
  }

  /**
   * Remove a column (actually rename it to preserve data)
   */
  async removeColumn(trx, tableName, field) {
    const columnName = this.generateColumnName(field.code)
    const archivedColumnName = `archived_${columnName}_${Date.now()}`

    // Rename column instead of dropping to preserve data
    await trx.raw(`ALTER TABLE ${tableName} RENAME COLUMN ${columnName} TO ${archivedColumnName}`)
    
    this.logger.info(`Archived column ${columnName} as ${archivedColumnName} in ${tableName}`)
  }

  /**
   * Modify an existing column
   */
  async modifyColumn(trx, tableName, modification, typeMapping) {
    const { old: oldField, new: newField, changes } = modification
    const columnName = this.generateColumnName(newField.code)

    // Handle type changes
    if (changes.type) {
      await this.changeColumnType(trx, tableName, columnName, oldField, newField, typeMapping)
    }

    // Handle constraint changes
    if (changes.required) {
      await this.changeColumnNullability(trx, tableName, columnName, newField.is_required)
    }

    // Handle index changes
    if (changes.indexed) {
      if (newField.is_indexed) {
        await trx.schema.alterTable(tableName, (table) => {
          table.index(columnName, `idx_${tableName}_${columnName}`)
        })
      } else {
        await trx.raw(`DROP INDEX IF EXISTS idx_${tableName}_${columnName}`)
      }
    }

    this.logger.info(`Modified column ${columnName} in ${tableName}`)
  }

  /**
   * Change column type with safe casting
   */
  async changeColumnType(trx, tableName, columnName, oldField, newField, typeMapping) {
    const oldType = typeMapping[oldField.field_type] || 'TEXT'
    const newType = typeMapping[newField.field_type] || 'TEXT'

    if (this.isTypeChangeCompatible(oldField.field_type, newField.field_type)) {
      // Safe type change
      await trx.raw(`ALTER TABLE ${tableName} ALTER COLUMN ${columnName} TYPE ${newType} USING ${columnName}::${newType}`)
    } else {
      // Incompatible type change - preserve old data
      const archivedColumnName = `archived_${columnName}_${Date.now()}`
      
      // Rename old column
      await trx.raw(`ALTER TABLE ${tableName} RENAME COLUMN ${columnName} TO ${archivedColumnName}`)
      
      // Add new column with new type
      await trx.schema.alterTable(tableName, (table) => {
        table.specificType(columnName, newType)
      })

      this.logger.warn(`Incompatible type change for ${columnName}. Old data preserved in ${archivedColumnName}`)
    }
  }

  /**
   * Check if type change is compatible
   */
  isTypeChangeCompatible(oldType, newType) {
    const compatibleChanges = {
      'integer': ['decimal', 'real'],
      'decimal': ['real'],
      'short_text': ['long_text', 'rich_text'],
      'long_text': ['rich_text'],
      'date': ['datetime'],
      'point': ['geometry'],
      'polygon': ['geometry']
    }

    return compatibleChanges[oldType]?.includes(newType) || false
  }

  /**
   * Change column nullability
   */
  async changeColumnNullability(trx, tableName, columnName, isRequired) {
    if (isRequired) {
      // Check if there are any NULL values
      const nullCount = await trx(tableName).whereNull(columnName).count('* as count').first()
      
      if (parseInt(nullCount.count) > 0) {
        throw new Error(`Cannot make column ${columnName} NOT NULL: contains ${nullCount.count} NULL values`)
      }

      await trx.raw(`ALTER TABLE ${tableName} ALTER COLUMN ${columnName} SET NOT NULL`)
    } else {
      await trx.raw(`ALTER TABLE ${tableName} ALTER COLUMN ${columnName} DROP NOT NULL`)
    }
  }

  /**
   * Drop table for a blueprint (soft delete by renaming)
   */
  async dropTableForBlueprint(blueprint) {
    const tableName = blueprint.table_name
    const archivedTableName = `archived_${tableName}_${Date.now()}`

    try {
      await this.knex.transaction(async (trx) => {
        // Rename table instead of dropping
        await trx.raw(`ALTER TABLE ${tableName} RENAME TO ${archivedTableName}`)
        
        // Also rename version table
        const versionTableName = `${tableName}_versions`
        const archivedVersionTableName = `archived_${versionTableName}_${Date.now()}`
        await trx.raw(`ALTER TABLE ${versionTableName} RENAME TO ${archivedVersionTableName}`)

        this.logger.info(`Archived tables ${tableName} and ${versionTableName}`)
      })
    } catch (error) {
      this.logger.error(`Failed to archive table ${tableName}:`, error)
      throw new Error(`Table archival failed: ${error.message}`)
    }
  }

  /**
   * Validate blueprint schema before applying
   */
  validateBlueprintSchema(blueprint, sections, fields, stages) {
    const errors = []

    // Validate blueprint
    if (!blueprint.code || blueprint.code.length < 2) {
      errors.push('Blueprint code must be at least 2 characters long')
    }

    if (!/^[a-zA-Z][a-zA-Z0-9_]*$/.test(blueprint.code)) {
      errors.push('Blueprint code must start with a letter and contain only letters, numbers, and underscores')
    }

    // Validate fields
    const fieldCodes = new Set()
    const typeMapping = this.getFieldTypeMapping()

    for (const field of fields) {
      if (!field.code || field.code.length < 1) {
        errors.push(`Field code is required`)
        continue
      }

      if (!/^[a-zA-Z][a-zA-Z0-9_]*$/.test(field.code)) {
        errors.push(`Field code '${field.code}' must start with a letter and contain only letters, numbers, and underscores`)
      }

      if (fieldCodes.has(field.code)) {
        errors.push(`Duplicate field code: ${field.code}`)
      }
      fieldCodes.add(field.code)

      if (!typeMapping[field.field_type]) {
        errors.push(`Invalid field type: ${field.field_type}`)
      }

      // Validate reference fields
      if (field.field_type === 'reference' && !field.config?.reference_table) {
        errors.push(`Reference field '${field.code}' must specify a reference_table`)
      }
    }

    // Validate stages
    const stageCodes = new Set()
    const sequences = new Set()

    for (const stage of stages) {
      if (!stage.code || stage.code.length < 1) {
        errors.push('Stage code is required')
        continue
      }

      if (stageCodes.has(stage.code)) {
        errors.push(`Duplicate stage code: ${stage.code}`)
      }
      stageCodes.add(stage.code)

      if (sequences.has(stage.sequence)) {
        errors.push(`Duplicate stage sequence: ${stage.sequence}`)
      }
      sequences.add(stage.sequence)
    }

    // Must have at least one initial stage
    const initialStages = stages.filter(s => s.is_initial)
    if (initialStages.length === 0) {
      errors.push('Blueprint must have at least one initial stage')
    }

    if (errors.length > 0) {
      const error = new Error('Blueprint schema validation failed')
      error.code = 'INVALID_BLUEPRINT_SCHEMA'
      error.details = errors
      throw error
    }

    return true
  }
}

export default SchemaManager