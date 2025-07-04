import DynamicDocumentService from '../../services/DynamicDocumentService.js'

const documentController = {
  // List documents for a blueprint
  async listDocuments(request, reply) {
    const { blueprintCode } = request.params
    const { page, limit, sort, order, search, stage, ...filters } = request.query

    const documentService = new DynamicDocumentService(this.knex, this.log)

    try {
      const result = await documentService.getDocuments(blueprintCode, {
        page: parseInt(page) || 1,
        limit: parseInt(limit) || 10,
        sort,
        order,
        filters,
        search,
        stage
      })

      return result
    } catch (error) {
      if (error.statusCode) {
        return reply.status(error.statusCode).send({
          error: error.message,
          details: error.details
        })
      }
      throw error
    }
  },

  // Get single document
  async getDocument(request, reply) {
    const { blueprintCode, id } = request.params
    const documentService = new DynamicDocumentService(this.knex, this.log)

    try {
      const document = await documentService.getDocument(blueprintCode, parseInt(id), request.user.id)
      return { document }
    } catch (error) {
      if (error.statusCode) {
        return reply.status(error.statusCode).send({
          error: error.message,
          details: error.details
        })
      }
      throw error
    }
  },

  // Create new document
  async createDocument(request, reply) {
    const { blueprintCode } = request.params
    const documentService = new DynamicDocumentService(this.knex, this.log)

    try {
      const document = await documentService.createDocument(blueprintCode, request.body, request.user.id)
      
      return reply.status(201).send({
        message: 'Document created successfully',
        document
      })
    } catch (error) {
      if (error.statusCode) {
        return reply.status(error.statusCode).send({
          error: error.message,
          details: error.details
        })
      }
      throw error
    }
  },

  // Update document
  async updateDocument(request, reply) {
    const { blueprintCode, id } = request.params
    const documentService = new DynamicDocumentService(this.knex, this.log)

    try {
      const document = await documentService.updateDocument(
        blueprintCode, 
        parseInt(id), 
        request.body, 
        request.user.id
      )
      
      return {
        message: 'Document updated successfully',
        document
      }
    } catch (error) {
      if (error.statusCode) {
        return reply.status(error.statusCode).send({
          error: error.message,
          details: error.details
        })
      }
      throw error
    }
  },

  // Delete document
  async deleteDocument(request, reply) {
    const { blueprintCode, id } = request.params
    const documentService = new DynamicDocumentService(this.knex, this.log)

    try {
      const result = await documentService.deleteDocument(blueprintCode, parseInt(id), request.user.id)
      return result
    } catch (error) {
      if (error.statusCode) {
        return reply.status(error.statusCode).send({
          error: error.message,
          details: error.details
        })
      }
      throw error
    }
  },

  // Transition document stage
  async transitionStage(request, reply) {
    const { blueprintCode, id } = request.params
    const { target_stage, comment } = request.body
    const documentService = new DynamicDocumentService(this.knex, this.log)

    try {
      const document = await documentService.transitionStage(
        blueprintCode, 
        parseInt(id), 
        target_stage, 
        request.user.id, 
        comment
      )
      
      return {
        message: 'Document stage transitioned successfully',
        document
      }
    } catch (error) {
      if (error.statusCode) {
        return reply.status(error.statusCode).send({
          error: error.message,
          details: error.details
        })
      }
      throw error
    }
  },

  // Get document versions
  async getDocumentVersions(request, reply) {
    const { blueprintCode, id } = request.params
    const { page = 1, limit = 10 } = request.query

    try {
      // Get blueprint to get table name
      const blueprint = await this.knex('blueprints')
        .where('code', blueprintCode)
        .whereNull('deleted_at')
        .first()

      if (!blueprint) {
        return reply.status(404).send({
          error: 'Blueprint not found'
        })
      }

      const offset = (page - 1) * limit

      const versions = await this.knex('document_versions')
        .where('document_table_name', blueprint.table_name)
        .where('document_id', parseInt(id))
        .orderBy('version_number', 'desc')
        .limit(limit)
        .offset(offset)

      const total = await this.knex('document_versions')
        .where('document_table_name', blueprint.table_name)
        .where('document_id', parseInt(id))
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
    } catch (error) {
      this.log.error('Failed to get document versions:', error)
      throw error
    }
  },

  // Get specific document version
  async getDocumentVersion(request, reply) {
    const { blueprintCode, id, versionId } = request.params

    try {
      // Get blueprint to get table name
      const blueprint = await this.knex('blueprints')
        .where('code', blueprintCode)
        .whereNull('deleted_at')
        .first()

      if (!blueprint) {
        return reply.status(404).send({
          error: 'Blueprint not found'
        })
      }

      const version = await this.knex('document_versions')
        .where('document_table_name', blueprint.table_name)
        .where('document_id', parseInt(id))
        .where('id', parseInt(versionId))
        .first()

      if (!version) {
        return reply.status(404).send({
          error: 'Document version not found'
        })
      }

      // Parse the data snapshot
      version.data = JSON.parse(version.data_snapshot)
      delete version.data_snapshot

      return { version }
    } catch (error) {
      this.log.error('Failed to get document version:', error)
      throw error
    }
  },

  // Restore document version
  async restoreVersion(request, reply) {
    const { blueprintCode, id, versionId } = request.params
    const documentService = new DynamicDocumentService(this.knex, this.log)

    try {
      // Get the version data
      const blueprint = await this.knex('blueprints')
        .where('code', blueprintCode)
        .whereNull('deleted_at')
        .first()

      if (!blueprint) {
        return reply.status(404).send({
          error: 'Blueprint not found'
        })
      }

      const version = await this.knex('document_versions')
        .where('document_table_name', blueprint.table_name)
        .where('document_id', parseInt(id))
        .where('id', parseInt(versionId))
        .first()

      if (!version) {
        return reply.status(404).send({
          error: 'Document version not found'
        })
      }

      // Restore the document with version data
      const versionData = JSON.parse(version.data_snapshot)
      const document = await documentService.updateDocument(
        blueprintCode,
        parseInt(id),
        versionData,
        request.user.id
      )

      // Log the restoration
      await this.knex('activity_logs').insert({
        user_id: request.user.id,
        action: 'restore_version',
        entity_type: 'document',
        entity_id: parseInt(id),
        entity_table: blueprint.table_name,
        data: { 
          blueprint_code: blueprintCode,
          restored_version_id: parseInt(versionId),
          restored_version_number: version.version_number
        }
      })

      return {
        message: 'Document version restored successfully',
        document
      }
    } catch (error) {
      this.log.error('Failed to restore document version:', error)
      if (error.statusCode) {
        return reply.status(error.statusCode).send({
          error: error.message,
          details: error.details
        })
      }
      throw error
    }
  },

  // Advanced document search
  async searchDocuments(request, reply) {
    const { blueprintCode } = request.params
    const documentService = new DynamicDocumentService(this.knex, this.log)

    try {
      const result = await documentService.searchDocuments(blueprintCode, request.body, request.user.id)
      return result
    } catch (error) {
      if (error.statusCode) {
        return reply.status(error.statusCode).send({
          error: error.message,
          details: error.details
        })
      }
      throw error
    }
  },

  // Export documents
  async exportDocuments(request, reply) {
    const { blueprintCode } = request.params
    const { format = 'csv', filters = {}, fields } = request.query

    try {
      const blueprint = await this.knex('blueprints')
        .where('code', blueprintCode)
        .whereNull('deleted_at')
        .first()

      if (!blueprint) {
        return reply.status(404).send({
          error: 'Blueprint not found'
        })
      }

      const tableName = blueprint.table_name
      let query = this.knex(tableName).whereNull('deleted_at')

      // Apply filters
      for (const [field, value] of Object.entries(filters)) {
        if (value !== null && value !== undefined && value !== '') {
          query = query.where(field, value)
        }
      }

      const documents = await query.orderBy('created_at', 'desc')

      // Format data based on export format
      if (format === 'csv') {
        const csvData = this.convertToCSV(documents, fields)
        reply
          .header('Content-Type', 'text/csv')
          .header('Content-Disposition', `attachment; filename="${blueprintCode}_documents.csv"`)
          .send(csvData)
      } else if (format === 'json') {
        reply
          .header('Content-Type', 'application/json')
          .header('Content-Disposition', `attachment; filename="${blueprintCode}_documents.json"`)
          .send(JSON.stringify(documents, null, 2))
      } else {
        return reply.status(400).send({
          error: 'Unsupported export format',
          supported: ['csv', 'json']
        })
      }
    } catch (error) {
      this.log.error('Failed to export documents:', error)
      throw error
    }
  },

  // Get document analytics
  async getAnalytics(request, reply) {
    const { blueprintCode } = request.params
    const { period = '30d' } = request.query

    try {
      const blueprint = await this.knex('blueprints')
        .where('code', blueprintCode)
        .whereNull('deleted_at')
        .first()

      if (!blueprint) {
        return reply.status(404).send({
          error: 'Blueprint not found'
        })
      }

      const tableName = blueprint.table_name

      // Calculate date range
      const now = new Date()
      let startDate
      switch (period) {
        case '7d':
          startDate = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000)
          break
        case '30d':
          startDate = new Date(now.getTime() - 30 * 24 * 60 * 60 * 1000)
          break
        case '90d':
          startDate = new Date(now.getTime() - 90 * 24 * 60 * 60 * 1000)
          break
        default:
          startDate = new Date(now.getTime() - 30 * 24 * 60 * 60 * 1000)
      }

      // Get total counts
      const totalDocuments = await this.knex(tableName)
        .whereNull('deleted_at')
        .count('* as count')
        .first()

      const newDocuments = await this.knex(tableName)
        .whereNull('deleted_at')
        .where('created_at', '>=', startDate)
        .count('* as count')
        .first()

      // Get documents by stage
      const documentsByStage = await this.knex(tableName)
        .join('blueprint_stages', 'blueprint_stages.id', `${tableName}.stage_id`)
        .select('blueprint_stages.code', 'blueprint_stages.title')
        .count('* as count')
        .whereNull(`${tableName}.deleted_at`)
        .groupBy('blueprint_stages.id', 'blueprint_stages.code', 'blueprint_stages.title')

      // Get creation trends
      const creationTrends = await this.knex(tableName)
        .select(this.knex.raw('DATE(created_at) as date'))
        .count('* as count')
        .whereNull('deleted_at')
        .where('created_at', '>=', startDate)
        .groupBy(this.knex.raw('DATE(created_at)'))
        .orderBy('date')

      return {
        summary: {
          total_documents: parseInt(totalDocuments.count),
          new_documents: parseInt(newDocuments.count),
          period
        },
        documents_by_stage: documentsByStage.map(item => ({
          stage_code: item.code,
          stage_title: item.title,
          count: parseInt(item.count)
        })),
        creation_trends: creationTrends.map(item => ({
          date: item.date,
          count: parseInt(item.count)
        }))
      }
    } catch (error) {
      this.log.error('Failed to get document analytics:', error)
      throw error
    }
  },

  // Bulk operations
  async bulkOperations(request, reply) {
    const { blueprintCode } = request.params
    const { operation, document_ids, data } = request.body

    try {
      const blueprint = await this.knex('blueprints')
        .where('code', blueprintCode)
        .whereNull('deleted_at')
        .first()

      if (!blueprint) {
        return reply.status(404).send({
          error: 'Blueprint not found'
        })
      }

      const tableName = blueprint.table_name
      const results = { success: 0, failed: 0, errors: [] }

      await this.knex.transaction(async (trx) => {
        for (const documentId of document_ids) {
          try {
            switch (operation) {
              case 'delete':
                await trx(tableName)
                  .where('id', documentId)
                  .update({
                    deleted_at: trx.fn.now(),
                    updated_by: request.user.id
                  })
                break

              case 'update':
                await trx(tableName)
                  .where('id', documentId)
                  .update({
                    ...data,
                    updated_by: request.user.id,
                    updated_at: trx.fn.now()
                  })
                break

              case 'transition':
                const targetStage = await trx('blueprint_stages')
                  .where('blueprint_id', blueprint.id)
                  .where('code', data.target_stage)
                  .first()

                if (targetStage) {
                  await trx(tableName)
                    .where('id', documentId)
                    .update({
                      stage_id: targetStage.id,
                      updated_by: request.user.id,
                      updated_at: trx.fn.now()
                    })
                }
                break

              default:
                throw new Error(`Unsupported operation: ${operation}`)
            }

            results.success++
          } catch (error) {
            results.failed++
            results.errors.push({
              document_id: documentId,
              error: error.message
            })
          }
        }

        // Log bulk operation
        await trx('activity_logs').insert({
          user_id: request.user.id,
          action: 'bulk_operation',
          entity_type: 'document',
          entity_table: tableName,
          data: {
            blueprint_code: blueprintCode,
            operation,
            affected_documents: document_ids.length,
            results
          }
        })
      })

      return {
        message: 'Bulk operation completed',
        results
      }
    } catch (error) {
      this.log.error('Failed to perform bulk operation:', error)
      throw error
    }
  },

  // Helper method to convert data to CSV
  convertToCSV(data, fields = null) {
    if (data.length === 0) return ''

    const keys = fields ? fields.split(',') : Object.keys(data[0])
    const csvHeader = keys.join(',')
    
    const csvRows = data.map(row => {
      return keys.map(key => {
        const value = row[key]
        if (value === null || value === undefined) return ''
        if (typeof value === 'string' && value.includes(',')) {
          return `"${value.replace(/"/g, '""')}"`
        }
        return value
      }).join(',')
    })

    return [csvHeader, ...csvRows].join('\n')
  }
}

export default documentController