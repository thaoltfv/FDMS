/**
 * Dynamic Document Service
 * 
 * This service provides CRUD operations for documents across any blueprint table.
 * It dynamically constructs SQL queries based on blueprint definitions.
 */

class DynamicDocumentService {
  constructor(knex, logger) {
    this.knex = knex
    this.logger = logger
  }

  /**
   * Get blueprint and validate access
   */
  async getBlueprintByCode(blueprintCode, userId = null) {
    const blueprint = await this.knex('blueprints')
      .where('code', blueprintCode)
      .whereNull('deleted_at')
      .where('is_active', true)
      .first()

    if (!blueprint) {
      const error = new Error('Blueprint not found')
      error.statusCode = 404
      throw error
    }

    if (!blueprint.table_name) {
      const error = new Error('Blueprint table not created yet')
      error.statusCode = 400
      throw error
    }

    return blueprint
  }

  /**
   * Get blueprint with full schema definition
   */
  async getBlueprintWithSchema(blueprintCode) {
    const blueprint = await this.getBlueprintByCode(blueprintCode)

    // Load sections
    const sections = await this.knex('blueprint_sections')
      .where('blueprint_id', blueprint.id)
      .orderBy('sequence', 'asc')

    // Load fields
    const fields = await this.knex('blueprint_fields')
      .where('blueprint_id', blueprint.id)
      .orderBy('sequence', 'asc')

    // Load stages
    const stages = await this.knex('blueprint_stages')
      .where('blueprint_id', blueprint.id)
      .orderBy('sequence', 'asc')

    return {
      ...blueprint,
      sections,
      fields,
      stages
    }
  }

  /**
   * Check if user has permission for action
   */
  async checkPermission(userId, blueprint, action, documentId = null) {
    // Get user roles and groups
    const userRoles = await this.knex('user_roles')
      .join('roles', 'roles.id', 'user_roles.role_id')
      .select('roles.code')
      .where('user_roles.user_id', userId)

    const userGroups = await this.knex('group_users')
      .join('groups', 'groups.id', 'group_users.group_id')
      .select('groups.id')
      .where('group_users.user_id', userId)
      .whereNull('groups.deleted_at')

    // Super admin can do everything
    if (userRoles.some(r => r.code === 'super_admin')) {
      return true
    }

    // Check blueprint-level permissions
    const groupIds = userGroups.map(g => g.id)
    if (groupIds.length > 0) {
      const permission = await this.knex('blueprint_permissions')
        .where('blueprint_id', blueprint.id)
        .whereIn('group_id', groupIds)
        .where('permission_code', action)
        .where('permission_level', 'blueprint')
        .first()

      if (permission) {
        return true
      }
    }

    return false
  }

  /**
   * Create a new document
   */
  async createDocument(blueprintCode, data, userId) {
    const blueprint = await this.getBlueprintWithSchema(blueprintCode)
    
    // Check permissions
    const hasPermission = await this.checkPermission(userId, blueprint, 'create')
    if (!hasPermission) {
      const error = new Error('Insufficient permissions to create document')
      error.statusCode = 403
      throw error
    }

    const tableName = blueprint.table_name

    try {
      return await this.knex.transaction(async (trx) => {
        // Get initial stage
        const initialStage = blueprint.stages.find(s => s.is_initial)
        if (!initialStage) {
          throw new Error('No initial stage defined for blueprint')
        }

        // Validate and transform data
        const validatedData = this.validateDocumentData(blueprint.fields, data)

        // Prepare document data
        const documentData = {
          ...validatedData,
          blueprint_id: blueprint.id,
          stage_id: initialStage.id,
          created_by: userId,
          updated_by: userId,
          created_at: trx.fn.now(),
          updated_at: trx.fn.now()
        }

        // Insert document
        const [document] = await trx(tableName)
          .insert(documentData)
          .returning('*')

        // Create version record
        await this.createDocumentVersion(trx, blueprint, document.id, documentData, userId, 'created')

        // Log activity
        await trx('activity_logs').insert({
          user_id: userId,
          action: 'create_document',
          entity_type: 'document',
          entity_id: document.id,
          entity_table: tableName,
          data: { blueprint_code: blueprintCode }
        })

        this.logger.info(`Document created in ${tableName}`, { id: document.id, user: userId })
        return document
      })
    } catch (error) {
      this.logger.error(`Failed to create document in ${tableName}:`, error)
      throw error
    }
  }

  /**
   * Get documents with filtering and pagination
   */
  async getDocuments(blueprintCode, options = {}) {
    const blueprint = await this.getBlueprintByCode(blueprintCode)
    const tableName = blueprint.table_name

    const {
      page = 1,
      limit = 10,
      sort = 'created_at',
      order = 'desc',
      filters = {},
      search = '',
      stage = null
    } = options

    const offset = (page - 1) * limit

    try {
      let query = this.knex(tableName)
        .whereNull('deleted_at')

      // Apply stage filter
      if (stage) {
        const stageRecord = await this.knex('blueprint_stages')
          .where('blueprint_id', blueprint.id)
          .where('code', stage)
          .first()
        
        if (stageRecord) {
          query = query.where('stage_id', stageRecord.id)
        }
      }

      // Apply field filters
      for (const [field, value] of Object.entries(filters)) {
        if (value !== null && value !== undefined && value !== '') {
          query = query.where(field, value)
        }
      }

      // Apply search if provided
      if (search) {
        const searchableFields = await this.getSearchableFields(blueprint.id)
        if (searchableFields.length > 0) {
          query = query.where(function() {
            for (const field of searchableFields) {
              this.orWhere(field.code, 'ilike', `%${search}%`)
            }
          })
        }
      }

      // Get total count
      const totalQuery = query.clone()
      const total = await totalQuery.count('* as count').first()

      // Apply sorting and pagination
      const documents = await query
        .orderBy(sort, order)
        .limit(limit)
        .offset(offset)

      // Add stage information
      const documentsWithStages = await this.enrichDocumentsWithStages(documents, blueprint.id)

      return {
        documents: documentsWithStages,
        pagination: {
          page: parseInt(page),
          limit: parseInt(limit),
          total: parseInt(total.count),
          pages: Math.ceil(total.count / limit)
        }
      }
    } catch (error) {
      this.logger.error(`Failed to get documents from ${tableName}:`, error)
      throw error
    }
  }

  /**
   * Get a single document by ID
   */
  async getDocument(blueprintCode, documentId, userId) {
    const blueprint = await this.getBlueprintWithSchema(blueprintCode)
    const tableName = blueprint.table_name

    // Check permissions
    const hasPermission = await this.checkPermission(userId, blueprint, 'read', documentId)
    if (!hasPermission) {
      const error = new Error('Insufficient permissions to read document')
      error.statusCode = 403
      throw error
    }

    const document = await this.knex(tableName)
      .where('id', documentId)
      .whereNull('deleted_at')
      .first()

    if (!document) {
      const error = new Error('Document not found')
      error.statusCode = 404
      throw error
    }

    // Enrich with stage information
    if (document.stage_id) {
      const stage = await this.knex('blueprint_stages')
        .where('id', document.stage_id)
        .first()
      document.stage = stage
    }

    // Add metadata
    document._blueprint = blueprint
    document._permissions = await this.getUserDocumentPermissions(userId, blueprint, documentId)

    return document
  }

  /**
   * Update a document
   */
  async updateDocument(blueprintCode, documentId, data, userId) {
    const blueprint = await this.getBlueprintWithSchema(blueprintCode)
    const tableName = blueprint.table_name

    // Check permissions
    const hasPermission = await this.checkPermission(userId, blueprint, 'update', documentId)
    if (!hasPermission) {
      const error = new Error('Insufficient permissions to update document')
      error.statusCode = 403
      throw error
    }

    try {
      return await this.knex.transaction(async (trx) => {
        // Get current document
        const currentDocument = await trx(tableName)
          .where('id', documentId)
          .whereNull('deleted_at')
          .first()

        if (!currentDocument) {
          throw new Error('Document not found')
        }

        // Validate and transform data
        const validatedData = this.validateDocumentData(blueprint.fields, data)

        // Prepare update data
        const updateData = {
          ...validatedData,
          updated_by: userId,
          updated_at: trx.fn.now()
        }

        // Update document
        const [updatedDocument] = await trx(tableName)
          .where('id', documentId)
          .update(updateData)
          .returning('*')

        // Create version record
        await this.createDocumentVersion(trx, blueprint, documentId, updatedDocument, userId, 'updated')

        // Log activity
        await trx('activity_logs').insert({
          user_id: userId,
          action: 'update_document',
          entity_type: 'document',
          entity_id: documentId,
          entity_table: tableName,
          data: { 
            blueprint_code: blueprintCode,
            changes: this.getChanges(currentDocument, updatedDocument)
          }
        })

        this.logger.info(`Document updated in ${tableName}`, { id: documentId, user: userId })
        return updatedDocument
      })
    } catch (error) {
      this.logger.error(`Failed to update document in ${tableName}:`, error)
      throw error
    }
  }

  /**
   * Delete a document (soft delete)
   */
  async deleteDocument(blueprintCode, documentId, userId) {
    const blueprint = await this.getBlueprintByCode(blueprintCode)
    const tableName = blueprint.table_name

    // Check permissions
    const hasPermission = await this.checkPermission(userId, blueprint, 'delete', documentId)
    if (!hasPermission) {
      const error = new Error('Insufficient permissions to delete document')
      error.statusCode = 403
      throw error
    }

    try {
      return await this.knex.transaction(async (trx) => {
        const [deletedDocument] = await trx(tableName)
          .where('id', documentId)
          .whereNull('deleted_at')
          .update({
            deleted_at: trx.fn.now(),
            updated_by: userId
          })
          .returning('*')

        if (!deletedDocument) {
          throw new Error('Document not found')
        }

        // Log activity
        await trx('activity_logs').insert({
          user_id: userId,
          action: 'delete_document',
          entity_type: 'document',
          entity_id: documentId,
          entity_table: tableName,
          data: { blueprint_code: blueprintCode }
        })

        this.logger.info(`Document deleted in ${tableName}`, { id: documentId, user: userId })
        return { message: 'Document deleted successfully' }
      })
    } catch (error) {
      this.logger.error(`Failed to delete document in ${tableName}:`, error)
      throw error
    }
  }

  /**
   * Transition document to a different stage
   */
  async transitionStage(blueprintCode, documentId, targetStageCode, userId, comment = '') {
    const blueprint = await this.getBlueprintWithSchema(blueprintCode)
    const tableName = blueprint.table_name

    // Check permissions
    const hasPermission = await this.checkPermission(userId, blueprint, 'update', documentId)
    if (!hasPermission) {
      const error = new Error('Insufficient permissions to change document stage')
      error.statusCode = 403
      throw error
    }

    try {
      return await this.knex.transaction(async (trx) => {
        // Get current document
        const document = await trx(tableName)
          .where('id', documentId)
          .whereNull('deleted_at')
          .first()

        if (!document) {
          throw new Error('Document not found')
        }

        // Get target stage
        const targetStage = await trx('blueprint_stages')
          .where('blueprint_id', blueprint.id)
          .where('code', targetStageCode)
          .first()

        if (!targetStage) {
          throw new Error('Target stage not found')
        }

        // Update document stage
        const [updatedDocument] = await trx(tableName)
          .where('id', documentId)
          .update({
            stage_id: targetStage.id,
            updated_by: userId,
            updated_at: trx.fn.now(),
            ...(targetStage.is_final && { approved_at: trx.fn.now(), approved_by: userId })
          })
          .returning('*')

        // Create version record
        await this.createDocumentVersion(trx, blueprint, documentId, updatedDocument, userId, `transitioned to ${targetStageCode}`)

        // Log activity
        await trx('activity_logs').insert({
          user_id: userId,
          action: 'transition_stage',
          entity_type: 'document',
          entity_id: documentId,
          entity_table: tableName,
          data: { 
            blueprint_code: blueprintCode,
            from_stage_id: document.stage_id,
            to_stage_id: targetStage.id,
            comment
          }
        })

        this.logger.info(`Document stage transitioned in ${tableName}`, { 
          id: documentId, 
          stage: targetStageCode, 
          user: userId 
        })

        return updatedDocument
      })
    } catch (error) {
      this.logger.error(`Failed to transition document stage in ${tableName}:`, error)
      throw error
    }
  }

  /**
   * Search documents with advanced filtering
   */
  async searchDocuments(blueprintCode, searchParams, userId) {
    const blueprint = await this.getBlueprintWithSchema(blueprintCode)
    const tableName = blueprint.table_name

    const {
      query = '',
      filters = {},
      dateRange = {},
      spatial = {},
      page = 1,
      limit = 20
    } = searchParams

    const offset = (page - 1) * limit

    try {
      let searchQuery = this.knex(tableName)
        .whereNull('deleted_at')

      // Full-text search
      if (query) {
        const searchableFields = await this.getSearchableFields(blueprint.id)
        if (searchableFields.length > 0) {
          searchQuery = searchQuery.where(function() {
            for (const field of searchableFields) {
              if (field.field_type === 'short_text' || field.field_type === 'long_text') {
                this.orWhereRaw(`to_tsvector('english', ${field.code}) @@ plainto_tsquery('english', ?)`, [query])
              }
            }
          })
        }
      }

      // Apply filters
      for (const [field, value] of Object.entries(filters)) {
        if (value !== null && value !== undefined) {
          if (typeof value === 'object' && value.operator) {
            // Advanced filter with operator
            switch (value.operator) {
              case 'gte':
                searchQuery = searchQuery.where(field, '>=', value.value)
                break
              case 'lte':
                searchQuery = searchQuery.where(field, '<=', value.value)
                break
              case 'contains':
                searchQuery = searchQuery.where(field, 'ilike', `%${value.value}%`)
                break
              case 'in':
                searchQuery = searchQuery.whereIn(field, value.value)
                break
            }
          } else {
            searchQuery = searchQuery.where(field, value)
          }
        }
      }

      // Date range filtering
      if (dateRange.field && (dateRange.from || dateRange.to)) {
        if (dateRange.from) {
          searchQuery = searchQuery.where(dateRange.field, '>=', dateRange.from)
        }
        if (dateRange.to) {
          searchQuery = searchQuery.where(dateRange.field, '<=', dateRange.to)
        }
      }

      // Spatial filtering
      if (spatial.field && spatial.geometry) {
        searchQuery = searchQuery.whereRaw(
          `ST_Intersects(${spatial.field}, ST_GeomFromGeoJSON(?))`,
          [JSON.stringify(spatial.geometry)]
        )
      }

      const total = await searchQuery.clone().count('* as count').first()
      const results = await searchQuery
        .orderBy('created_at', 'desc')
        .limit(limit)
        .offset(offset)

      return {
        results,
        pagination: {
          page: parseInt(page),
          limit: parseInt(limit),
          total: parseInt(total.count),
          pages: Math.ceil(total.count / limit)
        }
      }
    } catch (error) {
      this.logger.error(`Failed to search documents in ${tableName}:`, error)
      throw error
    }
  }

  /**
   * Validate document data against blueprint fields
   */
  validateDocumentData(fields, data) {
    const validatedData = {}
    const errors = []

    for (const field of fields) {
      const value = data[field.code]

      // Check required fields
      if (field.is_required && (value === null || value === undefined || value === '')) {
        errors.push(`Field '${field.code}' is required`)
        continue
      }

      // Skip validation for empty optional fields
      if (!field.is_required && (value === null || value === undefined || value === '')) {
        continue
      }

      // Type validation
      const validatedValue = this.validateFieldValue(field, value)
      if (validatedValue === null && value !== null) {
        errors.push(`Invalid value for field '${field.code}' of type '${field.field_type}'`)
        continue
      }

      validatedData[field.code] = validatedValue
    }

    if (errors.length > 0) {
      const error = new Error('Validation failed')
      error.statusCode = 400
      error.details = errors
      throw error
    }

    return validatedData
  }

  /**
   * Validate individual field value
   */
  validateFieldValue(field, value) {
    try {
      switch (field.field_type) {
        case 'short_text':
        case 'long_text':
        case 'email':
        case 'phone':
        case 'url':
          return String(value)

        case 'integer':
          const intValue = parseInt(value)
          if (isNaN(intValue)) return null
          return intValue

        case 'decimal':
        case 'real':
          const floatValue = parseFloat(value)
          if (isNaN(floatValue)) return null
          return floatValue

        case 'boolean':
          return Boolean(value)

        case 'date':
        case 'datetime':
          if (value instanceof Date) return value
          const dateValue = new Date(value)
          if (isNaN(dateValue.getTime())) return null
          return dateValue

        case 'json':
        case 'array':
        case 'object':
          if (typeof value === 'object') return value
          try {
            return JSON.parse(value)
          } catch {
            return null
          }

        case 'file':
        case 'reference':
          const refValue = parseInt(value)
          if (isNaN(refValue)) return null
          return refValue

        case 'files':
        case 'multi_reference':
          if (Array.isArray(value)) {
            return value.map(v => parseInt(v)).filter(v => !isNaN(v))
          }
          return null

        default:
          return value
      }
    } catch (error) {
      return null
    }
  }

  /**
   * Create document version record
   */
  async createDocumentVersion(trx, blueprint, documentId, documentData, userId, changeSummary) {
    // Get next version number
    const lastVersion = await trx('document_versions')
      .where('document_table_name', blueprint.table_name)
      .where('document_id', documentId)
      .orderBy('version_number', 'desc')
      .first()

    const versionNumber = lastVersion ? lastVersion.version_number + 1 : 1

    await trx('document_versions').insert({
      document_id: documentId,
      document_table_name: blueprint.table_name,
      blueprint_id: blueprint.id,
      version_number: versionNumber,
      data_snapshot: JSON.stringify(documentData),
      stage_id: documentData.stage_id,
      created_by: userId,
      change_summary: changeSummary
    })
  }

  /**
   * Get searchable fields for a blueprint
   */
  async getSearchableFields(blueprintId) {
    return await this.knex('blueprint_fields')
      .where('blueprint_id', blueprintId)
      .where('is_searchable', true)
      .select('code', 'field_type')
  }

  /**
   * Enrich documents with stage information
   */
  async enrichDocumentsWithStages(documents, blueprintId) {
    if (documents.length === 0) return documents

    const stages = await this.knex('blueprint_stages')
      .where('blueprint_id', blueprintId)

    const stageMap = new Map(stages.map(s => [s.id, s]))

    return documents.map(doc => ({
      ...doc,
      stage: doc.stage_id ? stageMap.get(doc.stage_id) : null
    }))
  }

  /**
   * Get user permissions for a specific document
   */
  async getUserDocumentPermissions(userId, blueprint, documentId) {
    // This would implement fine-grained permission checking
    // For now, return basic permissions
    return {
      read: true,
      update: true,
      delete: true,
      transition: true
    }
  }

  /**
   * Get changes between two document states
   */
  getChanges(oldDoc, newDoc) {
    const changes = {}
    for (const key in newDoc) {
      if (oldDoc[key] !== newDoc[key] && !['updated_at', 'updated_by'].includes(key)) {
        changes[key] = {
          from: oldDoc[key],
          to: newDoc[key]
        }
      }
    }
    return changes
  }
}

export default DynamicDocumentService