import documentController from './document.controller.js'
import documentValidation from './document.validation.js'

const documentModule = async (fastify, options) => {
  // All document routes require authentication
  fastify.addHook('preHandler', async (request, reply) => {
    await request.requireAuth()
  })

  // List documents for a blueprint
  fastify.get('/:blueprintCode', {
    schema: documentValidation.listDocuments,
    handler: documentController.listDocuments
  })

  // Get document by ID
  fastify.get('/:blueprintCode/:id', {
    schema: documentValidation.getDocument,
    handler: documentController.getDocument
  })

  // Create new document
  fastify.post('/:blueprintCode', {
    schema: documentValidation.createDocument,
    handler: documentController.createDocument
  })

  // Update document
  fastify.put('/:blueprintCode/:id', {
    schema: documentValidation.updateDocument,
    handler: documentController.updateDocument
  })

  // Delete document (soft delete)
  fastify.delete('/:blueprintCode/:id', {
    schema: documentValidation.deleteDocument,
    handler: documentController.deleteDocument
  })

  // Document workflow operations
  fastify.post('/:blueprintCode/:id/transition', {
    schema: documentValidation.transitionStage,
    handler: documentController.transitionStage
  })

  // Document versioning
  fastify.get('/:blueprintCode/:id/versions', {
    schema: documentValidation.getDocumentVersions,
    handler: documentController.getDocumentVersions
  })

  fastify.get('/:blueprintCode/:id/versions/:versionId', {
    schema: documentValidation.getDocumentVersion,
    handler: documentController.getDocumentVersion
  })

  fastify.post('/:blueprintCode/:id/restore/:versionId', {
    schema: documentValidation.restoreVersion,
    handler: documentController.restoreVersion
  })

  // Advanced querying
  fastify.post('/:blueprintCode/search', {
    schema: documentValidation.searchDocuments,
    handler: documentController.searchDocuments
  })

  fastify.get('/:blueprintCode/export', {
    schema: documentValidation.exportDocuments,
    handler: documentController.exportDocuments
  })

  // Document analytics
  fastify.get('/:blueprintCode/analytics', {
    schema: documentValidation.getAnalytics,
    handler: documentController.getAnalytics
  })

  // Bulk operations
  fastify.post('/:blueprintCode/bulk', {
    schema: documentValidation.bulkOperations,
    handler: documentController.bulkOperations
  })
}

export default documentModule