import FileStorageService from '../../services/FileStorageService.js'

const fileStorageController = {
  // Upload single file
  async uploadFile(request, reply) {
    const fileStorageService = new FileStorageService(this.knex, this.log)

    try {
      // Initialize storage if needed
      await fileStorageService.initialize()

      // Get file from multipart
      const data = await request.file()
      
      if (!data) {
        return reply.status(400).send({
          error: 'No file provided',
          message: 'Please select a file to upload'
        })
      }

      // Convert file stream to buffer
      const buffer = await data.toBuffer()
      
      // Extract metadata from query parameters
      const {
        blueprint_id,
        document_id,
        document_table_name,
        field_code
      } = request.query

      const fileData = {
        buffer,
        filename: data.filename,
        mimetype: data.mimetype,
        size: buffer.length,
        blueprint_id: blueprint_id ? parseInt(blueprint_id) : null,
        document_id: document_id ? parseInt(document_id) : null,
        document_table_name,
        field_code,
        uploaded_by: request.user.id
      }

      const fileRecord = await fileStorageService.uploadFile(fileData)

      return reply.status(201).send({
        message: 'File uploaded successfully',
        file: {
          id: fileRecord.id,
          uuid: fileRecord.uuid,
          filename: fileRecord.filename,
          original_filename: fileRecord.original_filename,
          mime_type: fileRecord.mime_type,
          size_bytes: fileRecord.size_bytes,
          uploaded_at: fileRecord.uploaded_at
        }
      })
    } catch (error) {
      if (error.statusCode) {
        return reply.status(error.statusCode).send({
          error: error.message,
          code: error.code
        })
      }
      throw error
    }
  },

  // Upload multiple files
  async uploadMultipleFiles(request, reply) {
    const fileStorageService = new FileStorageService(this.knex, this.log)

    try {
      await fileStorageService.initialize()

      const files = await request.files()
      const uploadedFiles = []
      const errors = []

      // Extract metadata from query parameters
      const {
        blueprint_id,
        document_id,
        document_table_name,
        field_code
      } = request.query

      for await (const data of files) {
        try {
          const buffer = await data.toBuffer()
          
          const fileData = {
            buffer,
            filename: data.filename,
            mimetype: data.mimetype,
            size: buffer.length,
            blueprint_id: blueprint_id ? parseInt(blueprint_id) : null,
            document_id: document_id ? parseInt(document_id) : null,
            document_table_name,
            field_code,
            uploaded_by: request.user.id
          }

          const fileRecord = await fileStorageService.uploadFile(fileData)
          uploadedFiles.push({
            id: fileRecord.id,
            uuid: fileRecord.uuid,
            filename: fileRecord.filename,
            original_filename: fileRecord.original_filename,
            mime_type: fileRecord.mime_type,
            size_bytes: fileRecord.size_bytes,
            uploaded_at: fileRecord.uploaded_at
          })
        } catch (error) {
          errors.push({
            filename: data.filename,
            error: error.message
          })
        }
      }

      return reply.status(201).send({
        message: `${uploadedFiles.length} files uploaded successfully`,
        uploaded_files: uploadedFiles,
        errors: errors.length > 0 ? errors : undefined,
        summary: {
          success: uploadedFiles.length,
          failed: errors.length
        }
      })
    } catch (error) {
      this.log.error('Failed to upload multiple files:', error)
      throw error
    }
  },

  // Get file metadata
  async getFile(request, reply) {
    const { fileId } = request.params
    const fileStorageService = new FileStorageService(this.knex, this.log)

    try {
      const fileRecord = await fileStorageService.getFileMetadata(parseInt(fileId), request.user.id)
      return { file: fileRecord }
    } catch (error) {
      if (error.statusCode) {
        return reply.status(error.statusCode).send({
          error: error.message
        })
      }
      throw error
    }
  },

  // Download file
  async downloadFile(request, reply) {
    const { fileId } = request.params
    const fileStorageService = new FileStorageService(this.knex, this.log)

    try {
      const { fileRecord, buffer, contentType } = await fileStorageService.downloadFile(
        parseInt(fileId), 
        request.user.id
      )

      reply
        .header('Content-Type', contentType)
        .header('Content-Length', buffer.length)
        .header('Content-Disposition', `attachment; filename="${fileRecord.original_filename}"`)
        .send(buffer)
    } catch (error) {
      if (error.statusCode) {
        return reply.status(error.statusCode).send({
          error: error.message
        })
      }
      throw error
    }
  },

  // Get file thumbnail (placeholder for image processing)
  async getThumbnail(request, reply) {
    const { fileId } = request.params
    const { size = '150x150' } = request.query
    const fileStorageService = new FileStorageService(this.knex, this.log)

    try {
      // For now, just return the original file if it's an image
      // In a full implementation, this would generate/cache thumbnails
      const fileRecord = await fileStorageService.getFileMetadata(parseInt(fileId), request.user.id)
      
      if (!fileRecord.mime_type.startsWith('image/')) {
        return reply.status(400).send({
          error: 'File is not an image',
          message: 'Thumbnails are only available for image files'
        })
      }

      // For now, redirect to download - in production you'd implement image resizing
      const { buffer, contentType } = await fileStorageService.downloadFile(parseInt(fileId), request.user.id)
      
      reply
        .header('Content-Type', contentType)
        .header('Cache-Control', 'public, max-age=3600') // Cache for 1 hour
        .send(buffer)
    } catch (error) {
      if (error.statusCode) {
        return reply.status(error.statusCode).send({
          error: error.message
        })
      }
      throw error
    }
  },

  // Delete file
  async deleteFile(request, reply) {
    const { fileId } = request.params
    const fileStorageService = new FileStorageService(this.knex, this.log)

    try {
      const result = await fileStorageService.deleteFile(parseInt(fileId), request.user.id)
      return result
    } catch (error) {
      if (error.statusCode) {
        return reply.status(error.statusCode).send({
          error: error.message
        })
      }
      throw error
    }
  },

  // List files
  async listFiles(request, reply) {
    const {
      page,
      limit,
      blueprint_id,
      document_id,
      document_table_name,
      field_code,
      mime_type_filter,
      search
    } = request.query

    const fileStorageService = new FileStorageService(this.knex, this.log)

    try {
      const result = await fileStorageService.listFiles({
        page: parseInt(page) || 1,
        limit: parseInt(limit) || 20,
        blueprint_id: blueprint_id ? parseInt(blueprint_id) : null,
        document_id: document_id ? parseInt(document_id) : null,
        document_table_name,
        field_code,
        mime_type_filter,
        search
      })

      return result
    } catch (error) {
      this.log.error('Failed to list files:', error)
      throw error
    }
  },

  // Update file metadata
  async updateFile(request, reply) {
    const { fileId } = request.params
    const { metadata = {} } = request.body

    try {
      const [updatedFile] = await this.knex('file_metadata')
        .where('id', parseInt(fileId))
        .whereNull('deleted_at')
        .update({
          metadata: { ...metadata },
          updated_at: this.knex.fn.now()
        })
        .returning('*')

      if (!updatedFile) {
        return reply.status(404).send({
          error: 'File not found'
        })
      }

      // Log activity
      await this.knex('activity_logs').insert({
        user_id: request.user.id,
        action: 'update_file_metadata',
        entity_type: 'file',
        entity_id: parseInt(fileId),
        data: { metadata }
      })

      return {
        message: 'File metadata updated successfully',
        file: updatedFile
      }
    } catch (error) {
      this.log.error('Failed to update file metadata:', error)
      throw error
    }
  },

  // Get usage statistics
  async getUsageStats(request, reply) {
    const fileStorageService = new FileStorageService(this.knex, this.log)

    try {
      const stats = await fileStorageService.getUsageStats()
      return stats
    } catch (error) {
      this.log.error('Failed to get usage stats:', error)
      throw error
    }
  },

  // Cleanup orphaned files (admin only)
  async cleanupOrphaned(request, reply) {
    const fileStorageService = new FileStorageService(this.knex, this.log)

    try {
      const result = await fileStorageService.cleanupOrphaned()
      
      // Log cleanup activity
      await this.knex('activity_logs').insert({
        user_id: request.user.id,
        action: 'cleanup_orphaned_files',
        entity_type: 'system',
        data: result
      })

      return result
    } catch (error) {
      this.log.error('Failed to cleanup orphaned files:', error)
      throw error
    }
  }
}

export default fileStorageController