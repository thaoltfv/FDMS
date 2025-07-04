import fp from 'fastify-plugin'

const authMiddleware = async (fastify, options) => {
  // Decorate request with user authentication helper
  fastify.decorateRequest('requireAuth', async function () {
    try {
      await this.jwtVerify()
      
      // Load user data from database
      const user = await fastify.knex('users')
        .select('id', 'uuid', 'email', 'full_name', 'is_active')
        .where('id', this.user.id)
        .whereNull('deleted_at')
        .first()

      if (!user || !user.is_active) {
        throw new Error('User not found or inactive')
      }

      // Load user roles
      const roles = await fastify.knex('user_roles')
        .join('roles', 'roles.id', 'user_roles.role_id')
        .select('roles.code', 'roles.title')
        .where('user_roles.user_id', user.id)

      // Load user groups
      const groups = await fastify.knex('group_users')
        .join('groups', 'groups.id', 'group_users.group_id')
        .select('groups.id', 'groups.code', 'groups.title')
        .where('group_users.user_id', user.id)
        .whereNull('groups.deleted_at')
        .where(function() {
          this.whereNull('group_users.expires_at')
            .orWhere('group_users.expires_at', '>', new Date())
        })

      this.user = {
        ...user,
        roles: roles.map(r => r.code),
        roleObjects: roles,
        groups: groups.map(g => g.code),
        groupObjects: groups
      }

      return this.user
    } catch (error) {
      throw fastify.httpErrors.unauthorized('Invalid or expired token')
    }
  })

  // Helper for role-based authorization
  fastify.decorateRequest('requireRole', function (requiredRoles) {
    if (!this.user) {
      throw fastify.httpErrors.unauthorized('Authentication required')
    }

    const userRoles = this.user.roles || []
    const hasRole = Array.isArray(requiredRoles) 
      ? requiredRoles.some(role => userRoles.includes(role))
      : userRoles.includes(requiredRoles)

    if (!hasRole) {
      throw fastify.httpErrors.forbidden('Insufficient permissions')
    }

    return true
  })

  // Helper for group-based authorization
  fastify.decorateRequest('requireGroup', function (requiredGroups) {
    if (!this.user) {
      throw fastify.httpErrors.unauthorized('Authentication required')
    }

    const userGroups = this.user.groups || []
    const hasGroup = Array.isArray(requiredGroups)
      ? requiredGroups.some(group => userGroups.includes(group))
      : userGroups.includes(requiredGroups)

    if (!hasGroup) {
      throw fastify.httpErrors.forbidden('Insufficient group permissions')
    }

    return true
  })

  // Optional authentication (doesn't throw if no token)
  fastify.decorateRequest('tryAuth', async function () {
    try {
      const authHeader = this.headers.authorization
      if (authHeader && authHeader.startsWith('Bearer ')) {
        await this.requireAuth()
      }
    } catch (error) {
      // Silently fail for optional auth
      this.user = null
    }
  })
}

export default fp(authMiddleware)