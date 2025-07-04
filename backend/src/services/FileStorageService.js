import AWS from 'aws-sdk'
import crypto from 'crypto'
import path from 'path'
import { v4 as uuidv4 } from 'uuid'

/**
 * File Storage Service
 * 
 * Handles file operations with S3-compatible storage (Garage)
 */
class FileStorageService {
  constructor(knex, logger) {
    this.knex = knex
    this.logger = logger
    
    // Configure S3 client for Garage
    this.s3 = new AWS.S3({
      endpoint: process.env.S3_ENDPOINT || 'http://localhost:3900',
      accessKeyId: process.env.S3_ACCESS_KEY || 'fdms_access_key',
      secretAccessKey: process.env.S3_SECRET_KEY || 'fdms_secret_key',
      region: process.env.S3_REGION || 'us-east-1',
      s3ForcePathStyle: true, // Required for Garage
      signatureVersion: 'v4'
    })

    this.bucketName = process.env.S3_BUCKET || 'fdms-files'
    
    // Supported file types
    this.allowedMimeTypes = new Set([
      // Images
      'image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp',
      // Documents
      'application/pdf', 'application/msword', 
      'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
      'application/vnd.ms-excel',
      'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
      'application/vnd.ms-powerpoint',
      'application/vnd.openxmlformats-officedocument.presentationml.presentation',
      // Text
      'text/plain', 'text/csv',
      // Other
      'application/zip', 'application/x-zip-compressed'
    ])

    this.maxFileSize = parseInt(process.env.MAX_FILE_SIZE) || 104857600 // 100MB
  }

  /**
   * Initialize storage (create bucket if needed)
   */
  async initialize() {
    try {
      await this.s3.headBucket({ Bucket: this.bucketName }).promise()
      this.logger.info(`S3 bucket ${this.bucketName} exists`)
    } catch (error) {
      if (error.statusCode === 404) {
        try {
          await this.s3.createBucket({ Bucket: this.bucketName }).promise()
          this.logger.info(`Created S3 bucket ${this.bucketName}`)
        } catch (createError) {
          this.logger.error('Failed to create S3 bucket:', createError)
          throw createError
        }
      } else {
        this.logger.error('Failed to check S3 bucket:', error)
        throw error
      }
    }
  }

  /**
   * Upload a file
   */
  async uploadFile(fileData, metadata = {}) {
    const { 
      buffer, 
      filename, 
      mimetype, 
      size,
      blueprint_id = null,
      document_id = null,
      document_table_name = null,
      field_code = null,
      uploaded_by
    } = fileData

    // Validate file
    this.validateFile(buffer, filename, mimetype, size)

    try {
      const fileUuid = uuidv4()
      const fileExtension = path.extname(filename)
      const baseName = path.basename(filename, fileExtension)
      const sanitizedFilename = this.sanitizeFilename(baseName) + fileExtension
      
      // Generate storage path
      const datePath = new Date().toISOString().split('T')[0].replace(/-/g, '/')
      const storagePath = `${datePath}/${fileUuid}_${sanitizedFilename}`

      // Calculate checksum
      const checksum = crypto.createHash('md5').update(buffer).digest('hex')

      // Upload to S3
      const uploadParams = {
        Bucket: this.bucketName,
        Key: storagePath,
        Body: buffer,
        ContentType: mimetype,
        ContentLength: size,
        Metadata: {
          'original-filename': filename,
          'uploaded-by': uploaded_by.toString(),
          'checksum': checksum,
          ...(blueprint_id && { 'blueprint-id': blueprint_id.toString() }),
          ...(document_id && { 'document-id': document_id.toString() }),
          ...(field_code && { 'field-code': field_code })
        }
      }

      await this.s3.upload(uploadParams).promise()

      // Save metadata to database
      const [fileRecord] = await this.knex('file_metadata')
        .insert({
          uuid: fileUuid,
          filename: sanitizedFilename,
          original_filename: filename,
          mime_type: mimetype,
          size_bytes: size,
          storage_path: storagePath,
          storage_provider: 'garage',
          checksum,
          blueprint_id,
          document_id,
          document_table_name,
          field_code,
          uploaded_by,
          metadata: metadata || {}
        })
        .returning('*')

      this.logger.info(`File uploaded successfully`, { 
        fileId: fileRecord.id, 
        filename, 
        size, 
        user: uploaded_by 
      })

      return fileRecord
    } catch (error) {
      this.logger.error('Failed to upload file:', error)
      throw error
    }
  }

  /**
   * Download a file
   */
  async downloadFile(fileId, userId = null) {
    try {
      // Get file metadata
      const fileRecord = await this.knex('file_metadata')
        .where('id', fileId)
        .whereNull('deleted_at')
        .first()

      if (!fileRecord) {
        const error = new Error('File not found')
        error.statusCode = 404
        throw error
      }

      // TODO: Check permissions based on blueprint/document access

      // Get file from S3
      const downloadParams = {
        Bucket: this.bucketName,
        Key: fileRecord.storage_path
      }

      const s3Object = await this.s3.getObject(downloadParams).promise()

      // Log download activity
      if (userId) {
        await this.knex('activity_logs').insert({
          user_id: userId,
          action: 'download_file',
          entity_type: 'file',
          entity_id: fileId,
          data: {
            filename: fileRecord.filename,
            size: fileRecord.size_bytes
          }
        })
      }

      return {
        fileRecord,
        buffer: s3Object.Body,
        contentType: s3Object.ContentType || fileRecord.mime_type
      }
    } catch (error) {
      this.logger.error('Failed to download file:', error)
      throw error
    }
  }

  /**
   * Delete a file
   */
  async deleteFile(fileId, userId) {
    try {
      return await this.knex.transaction(async (trx) => {
        // Get file metadata
        const fileRecord = await trx('file_metadata')
          .where('id', fileId)
          .whereNull('deleted_at')
          .first()

        if (!fileRecord) {
          const error = new Error('File not found')
          error.statusCode = 404
          throw error
        }

        // TODO: Check permissions

        // Soft delete in database
        await trx('file_metadata')
          .where('id', fileId)
          .update({ deleted_at: trx.fn.now() })

        // Delete from S3
        try {
          await this.s3.deleteObject({
            Bucket: this.bucketName,
            Key: fileRecord.storage_path
          }).promise()
        } catch (s3Error) {
          this.logger.warn('Failed to delete file from S3, but marked as deleted in DB:', s3Error)
        }

        // Log deletion
        await trx('activity_logs').insert({
          user_id: userId,
          action: 'delete_file',
          entity_type: 'file',
          entity_id: fileId,
          data: {
            filename: fileRecord.filename,
            size: fileRecord.size_bytes
          }
        })

        this.logger.info(`File deleted successfully`, { fileId, filename: fileRecord.filename, user: userId })
        return { message: 'File deleted successfully' }
      })
    } catch (error) {
      this.logger.error('Failed to delete file:', error)
      throw error
    }
  }

  /**
   * Get file metadata
   */
  async getFileMetadata(fileId, userId = null) {
    try {
      const fileRecord = await this.knex('file_metadata')
        .where('id', fileId)
        .whereNull('deleted_at')
        .first()

      if (!fileRecord) {
        const error = new Error('File not found')
        error.statusCode = 404
        throw error
      }

      // TODO: Check permissions

      return fileRecord
    } catch (error) {
      this.logger.error('Failed to get file metadata:', error)
      throw error
    }
  }

  /**
   * List files with filtering
   */
  async listFiles(options = {}) {
    const {
      page = 1,
      limit = 20,
      blueprint_id = null,
      document_id = null,
      document_table_name = null,
      field_code = null,
      mime_type_filter = null,
      search = ''
    } = options

    const offset = (page - 1) * limit

    try {
      let query = this.knex('file_metadata')
        .whereNull('deleted_at')

      // Apply filters
      if (blueprint_id) {
        query = query.where('blueprint_id', blueprint_id)
      }

      if (document_id && document_table_name) {
        query = query.where('document_id', document_id)
          .where('document_table_name', document_table_name)
      }

      if (field_code) {
        query = query.where('field_code', field_code)
      }

      if (mime_type_filter) {
        if (mime_type_filter === 'images') {
          query = query.where('mime_type', 'like', 'image/%')
        } else if (mime_type_filter === 'documents') {
          query = query.where(function() {
            this.where('mime_type', 'like', 'application/%')
              .orWhere('mime_type', 'like', 'text/%')
          })
        } else {
          query = query.where('mime_type', mime_type_filter)
        }
      }

      if (search) {
        query = query.where(function() {
          this.where('filename', 'ilike', `%${search}%`)
            .orWhere('original_filename', 'ilike', `%${search}%`)
        })
      }

      // Get total count
      const total = await query.clone().count('* as count').first()

      // Get files
      const files = await query
        .select('id', 'uuid', 'filename', 'original_filename', 'mime_type', 'size_bytes', 'uploaded_at', 'uploaded_by')
        .orderBy('uploaded_at', 'desc')
        .limit(limit)
        .offset(offset)

      return {
        files,
        pagination: {
          page: parseInt(page),
          limit: parseInt(limit),
          total: parseInt(total.count),
          pages: Math.ceil(total.count / limit)
        }
      }
    } catch (error) {
      this.logger.error('Failed to list files:', error)
      throw error
    }
  }

  /**
   * Get storage usage statistics
   */
  async getUsageStats() {
    try {
      // Total files and size
      const totalStats = await this.knex('file_metadata')
        .whereNull('deleted_at')
        .select(
          this.knex.raw('COUNT(*) as total_files'),
          this.knex.raw('SUM(size_bytes) as total_size')
        )
        .first()

      // Files by type
      const filesByType = await this.knex('file_metadata')
        .whereNull('deleted_at')
        .select('mime_type')
        .count('* as count')
        .sum('size_bytes as total_size')
        .groupBy('mime_type')
        .orderBy('count', 'desc')

      // Files by date (last 30 days)
      const thirtyDaysAgo = new Date(Date.now() - 30 * 24 * 60 * 60 * 1000)
      const uploadTrends = await this.knex('file_metadata')
        .whereNull('deleted_at')
        .where('uploaded_at', '>=', thirtyDaysAgo)
        .select(this.knex.raw('DATE(uploaded_at) as date'))
        .count('* as count')
        .sum('size_bytes as total_size')
        .groupBy(this.knex.raw('DATE(uploaded_at)'))
        .orderBy('date')

      return {
        summary: {
          total_files: parseInt(totalStats.total_files),
          total_size: parseInt(totalStats.total_size) || 0,
          total_size_mb: Math.round((parseInt(totalStats.total_size) || 0) / 1024 / 1024)
        },
        files_by_type: filesByType.map(item => ({
          mime_type: item.mime_type,
          count: parseInt(item.count),
          total_size: parseInt(item.total_size) || 0
        })),
        upload_trends: uploadTrends.map(item => ({
          date: item.date,
          count: parseInt(item.count),
          total_size: parseInt(item.total_size) || 0
        }))
      }
    } catch (error) {
      this.logger.error('Failed to get usage stats:', error)
      throw error
    }
  }

  /**
   * Cleanup orphaned files
   */
  async cleanupOrphaned() {
    try {
      // Find files that are not referenced by any document
      const orphanedFiles = await this.knex('file_metadata')
        .whereNull('deleted_at')
        .whereNotNull('document_id')
        .whereNotNull('document_table_name')
        .whereNotExists(function() {
          // This is a complex query that would need to be dynamic
          // For now, we'll just identify potential orphans
          this.select('*')
            .from('document_versions')
            .whereRaw('document_versions.document_table_name = file_metadata.document_table_name')
            .whereRaw('document_versions.document_id = file_metadata.document_id')
        })

      const orphanCount = orphanedFiles.length
      let deletedCount = 0

      for (const file of orphanedFiles) {
        try {
          await this.deleteFile(file.id, null) // System cleanup
          deletedCount++
        } catch (error) {
          this.logger.warn(`Failed to delete orphaned file ${file.id}:`, error)
        }
      }

      this.logger.info(`Cleanup completed: ${deletedCount}/${orphanCount} orphaned files deleted`)

      return {
        message: 'Cleanup completed',
        orphaned_found: orphanCount,
        deleted: deletedCount
      }
    } catch (error) {
      this.logger.error('Failed to cleanup orphaned files:', error)
      throw error
    }
  }

  /**
   * Validate file before upload
   */
  validateFile(buffer, filename, mimetype, size) {
    // Check file size
    if (size > this.maxFileSize) {
      const error = new Error(`File size exceeds maximum allowed size of ${this.maxFileSize} bytes`)
      error.code = 'FILE_TOO_LARGE'
      error.statusCode = 413
      throw error
    }

    // Check file type
    if (!this.allowedMimeTypes.has(mimetype)) {
      const error = new Error(`File type ${mimetype} is not allowed`)
      error.code = 'INVALID_FILE_TYPE'
      error.statusCode = 400
      throw error
    }

    // Check filename
    if (!filename || filename.length === 0) {
      const error = new Error('Filename is required')
      error.statusCode = 400
      throw error
    }

    if (filename.length > 255) {
      const error = new Error('Filename is too long (max 255 characters)')
      error.statusCode = 400
      throw error
    }

    // Check for dangerous file extensions
    const dangerousExtensions = ['.exe', '.bat', '.cmd', '.scr', '.pif', '.vbs', '.js']
    const extension = path.extname(filename).toLowerCase()
    if (dangerousExtensions.includes(extension)) {
      const error = new Error(`File extension ${extension} is not allowed`)
      error.code = 'INVALID_FILE_TYPE'
      error.statusCode = 400
      throw error
    }
  }

  /**
   * Sanitize filename to make it safe for storage
   */
  sanitizeFilename(filename) {
    return filename
      .replace(/[^a-zA-Z0-9._-]/g, '_') // Replace unsafe characters
      .replace(/_{2,}/g, '_') // Replace multiple underscores with single
      .replace(/^_|_$/g, '') // Remove leading/trailing underscores
      .substring(0, 200) // Limit length
  }

  /**
   * Generate signed URL for temporary access
   */
  async getSignedUrl(fileId, expiresIn = 3600) {
    try {
      const fileRecord = await this.getFileMetadata(fileId)
      
      const params = {
        Bucket: this.bucketName,
        Key: fileRecord.storage_path,
        Expires: expiresIn
      }

      const url = await this.s3.getSignedUrlPromise('getObject', params)
      return { url, expires_in: expiresIn }
    } catch (error) {
      this.logger.error('Failed to generate signed URL:', error)
      throw error
    }
  }
}

export default FileStorageService