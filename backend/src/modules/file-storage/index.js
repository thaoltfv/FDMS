import fileStorageController from './fileStorage.controller.js'
import fileStorageValidation from './fileStorage.validation.js'

const fileStorageModule = async (fastify, options) => {
  // All file storage routes require authentication
  fastify.addHook('preHandler', async (request, reply) => {
    await request.requireAuth()
  })

  // Upload single file
  fastify.post('/upload', {
    schema: fileStorageValidation.uploadFile,
    handler: fileStorageController.uploadFile
  })

  // Upload multiple files
  fastify.post('/upload/multiple', {
    schema: fileStorageValidation.uploadMultipleFiles,
    handler: fileStorageController.uploadMultipleFiles
  })

  // Get file metadata
  fastify.get('/:fileId', {
    schema: fileStorageValidation.getFile,
    handler: fileStorageController.getFile
  })

  // Download file
  fastify.get('/:fileId/download', {
    schema: fileStorageValidation.downloadFile,
    handler: fileStorageController.downloadFile
  })

  // Get file thumbnail (for images)
  fastify.get('/:fileId/thumbnail', {
    schema: fileStorageValidation.getThumbnail,
    handler: fileStorageController.getThumbnail
  })

  // Delete file
  fastify.delete('/:fileId', {
    schema: fileStorageValidation.deleteFile,
    handler: fileStorageController.deleteFile
  })

  // List files with filtering
  fastify.get('/', {
    schema: fileStorageValidation.listFiles,
    handler: fileStorageController.listFiles
  })

  // Update file metadata
  fastify.put('/:fileId', {
    schema: fileStorageValidation.updateFile,
    handler: fileStorageController.updateFile
  })

  // Get file usage statistics
  fastify.get('/stats/usage', {
    schema: fileStorageValidation.getUsageStats,
    handler: fileStorageController.getUsageStats
  })

  // Cleanup orphaned files
  fastify.post('/cleanup/orphaned', {
    schema: fileStorageValidation.cleanupOrphaned,
    preHandler: async (request, reply) => {
      request.requireRole(['super_admin', 'blueprint_admin'])
    },
    handler: fileStorageController.cleanupOrphaned
  })
}

export default fileStorageModule