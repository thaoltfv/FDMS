import authController from './auth.controller.js'
import authValidation from './auth.validation.js'

const authModule = async (fastify, options) => {
  // Register
  fastify.post('/register', {
    schema: authValidation.register,
    handler: authController.register
  })

  // Login
  fastify.post('/login', {
    schema: authValidation.login,
    handler: authController.login
  })

  // Refresh token
  fastify.post('/refresh', {
    schema: authValidation.refresh,
    handler: authController.refresh
  })

  // Get current user profile
  fastify.get('/me', {
    schema: authValidation.getProfile,
    preHandler: async (request, reply) => {
      await request.requireAuth()
    },
    handler: authController.getProfile
  })

  // Update current user profile
  fastify.put('/me', {
    schema: authValidation.updateProfile,
    preHandler: async (request, reply) => {
      await request.requireAuth()
    },
    handler: authController.updateProfile
  })

  // Change password
  fastify.put('/change-password', {
    schema: authValidation.changePassword,
    preHandler: async (request, reply) => {
      await request.requireAuth()
    },
    handler: authController.changePassword
  })

  // Admin routes for user management
  fastify.register(async function (fastify) {
    // Admin middleware
    fastify.addHook('preHandler', async (request, reply) => {
      await request.requireAuth()
      request.requireRole(['super_admin', 'blueprint_admin'])
    })

    // List users
    fastify.get('/users', {
      schema: authValidation.listUsers,
      handler: authController.listUsers
    })

    // Get user by ID
    fastify.get('/users/:id', {
      schema: authValidation.getUser,
      handler: authController.getUser
    })

    // Update user
    fastify.put('/users/:id', {
      schema: authValidation.updateUser,
      handler: authController.updateUser
    })

    // Activate/deactivate user
    fastify.patch('/users/:id/status', {
      schema: authValidation.updateUserStatus,
      handler: authController.updateUserStatus
    })

    // Assign/remove roles
    fastify.post('/users/:id/roles', {
      schema: authValidation.assignRole,
      handler: authController.assignRole
    })

    fastify.delete('/users/:id/roles/:roleId', {
      schema: authValidation.removeRole,
      handler: authController.removeRole
    })
  })
}

export default authModule