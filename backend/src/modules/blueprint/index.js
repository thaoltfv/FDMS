import blueprintController from './blueprint.controller.js'
import blueprintValidation from './blueprint.validation.js'

const blueprintModule = async (fastify, options) => {
  // All blueprint routes require authentication
  fastify.addHook('preHandler', async (request, reply) => {
    await request.requireAuth()
  })

  // List blueprints
  fastify.get('/', {
    schema: blueprintValidation.listBlueprints,
    handler: blueprintController.listBlueprints
  })

  // Get blueprint by ID
  fastify.get('/:id', {
    schema: blueprintValidation.getBlueprint,
    handler: blueprintController.getBlueprint
  })

  // Admin routes - require blueprint management permissions
  fastify.register(async function (fastify) {
    // Admin middleware
    fastify.addHook('preHandler', async (request, reply) => {
      request.requireRole(['super_admin', 'blueprint_admin'])
    })

    // Create blueprint
    fastify.post('/', {
      schema: blueprintValidation.createBlueprint,
      handler: blueprintController.createBlueprint
    })

    // Update blueprint
    fastify.put('/:id', {
      schema: blueprintValidation.updateBlueprint,
      handler: blueprintController.updateBlueprint
    })

    // Delete blueprint
    fastify.delete('/:id', {
      schema: blueprintValidation.deleteBlueprint,
      handler: blueprintController.deleteBlueprint
    })

    // Blueprint sections management
    fastify.get('/:id/sections', {
      schema: blueprintValidation.getBlueprintSections,
      handler: blueprintController.getBlueprintSections
    })

    fastify.post('/:id/sections', {
      schema: blueprintValidation.createSection,
      handler: blueprintController.createSection
    })

    fastify.put('/:id/sections/:sectionId', {
      schema: blueprintValidation.updateSection,
      handler: blueprintController.updateSection
    })

    fastify.delete('/:id/sections/:sectionId', {
      schema: blueprintValidation.deleteSection,
      handler: blueprintController.deleteSection
    })

    // Blueprint fields management
    fastify.get('/:id/fields', {
      schema: blueprintValidation.getBlueprintFields,
      handler: blueprintController.getBlueprintFields
    })

    fastify.post('/:id/fields', {
      schema: blueprintValidation.createField,
      handler: blueprintController.createField
    })

    fastify.put('/:id/fields/:fieldId', {
      schema: blueprintValidation.updateField,
      handler: blueprintController.updateField
    })

    fastify.delete('/:id/fields/:fieldId', {
      schema: blueprintValidation.deleteField,
      handler: blueprintController.deleteField
    })

    // Blueprint stages management
    fastify.get('/:id/stages', {
      schema: blueprintValidation.getBlueprintStages,
      handler: blueprintController.getBlueprintStages
    })

    fastify.post('/:id/stages', {
      schema: blueprintValidation.createStage,
      handler: blueprintController.createStage
    })

    fastify.put('/:id/stages/:stageId', {
      schema: blueprintValidation.updateStage,
      handler: blueprintController.updateStage
    })

    fastify.delete('/:id/stages/:stageId', {
      schema: blueprintValidation.deleteStage,
      handler: blueprintController.deleteStage
    })

    // Schema operations
    fastify.post('/:id/apply-schema', {
      schema: blueprintValidation.applySchema,
      handler: blueprintController.applySchema
    })

    fastify.get('/:id/versions', {
      schema: blueprintValidation.getBlueprintVersions,
      handler: blueprintController.getBlueprintVersions
    })

    fastify.post('/:id/rollback/:versionId', {
      schema: blueprintValidation.rollbackToVersion,
      handler: blueprintController.rollbackToVersion
    })

    // Blueprint permissions
    fastify.get('/:id/permissions', {
      schema: blueprintValidation.getBlueprintPermissions,
      handler: blueprintController.getBlueprintPermissions
    })

    fastify.post('/:id/permissions', {
      schema: blueprintValidation.setPermission,
      handler: blueprintController.setPermission
    })

    fastify.delete('/:id/permissions/:permissionId', {
      schema: blueprintValidation.removePermission,
      handler: blueprintController.removePermission
    })
  })
}

export default blueprintModule