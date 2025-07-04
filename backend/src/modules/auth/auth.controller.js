import bcrypt from 'bcrypt'
import { v4 as uuidv4 } from 'uuid'

const authController = {
  // Register new user
  async register(request, reply) {
    const { email, password, full_name } = request.body

    // Check if user already exists
    const existingUser = await this.knex('users')
      .where('email', email.toLowerCase())
      .whereNull('deleted_at')
      .first()

    if (existingUser) {
      return reply.status(409).send({
        error: 'User exists',
        message: 'User with this email already exists'
      })
    }

    // Hash password
    const password_hash = await bcrypt.hash(password, 12)

    // Create user
    const [user] = await this.knex('users')
      .insert({
        uuid: uuidv4(),
        email: email.toLowerCase(),
        password_hash,
        full_name,
        is_active: true
      })
      .returning(['id', 'uuid', 'email', 'full_name', 'created_at'])

    // Assign default viewer role
    const viewerRole = await this.knex('roles')
      .where('code', 'viewer')
      .first()

    if (viewerRole) {
      await this.knex('user_roles').insert({
        user_id: user.id,
        role_id: viewerRole.id
      })
    }

    // Generate JWT token
    const token = this.jwt.sign({ 
      id: user.id, 
      email: user.email 
    })

    // Log activity
    await this.knex('activity_logs').insert({
      user_id: user.id,
      action: 'register',
      entity_type: 'user',
      entity_id: user.id,
      ip_address: request.ip,
      user_agent: request.headers['user-agent']
    })

    return {
      user: {
        id: user.id,
        uuid: user.uuid,
        email: user.email,
        full_name: user.full_name,
        created_at: user.created_at
      },
      token
    }
  },

  // Login user
  async login(request, reply) {
    const { email, password } = request.body

    // Find user
    const user = await this.knex('users')
      .where('email', email.toLowerCase())
      .whereNull('deleted_at')
      .first()

    if (!user || !user.is_active) {
      return reply.status(401).send({
        error: 'Invalid credentials',
        message: 'Email or password is incorrect'
      })
    }

    // Verify password
    const validPassword = await bcrypt.compare(password, user.password_hash)
    if (!validPassword) {
      return reply.status(401).send({
        error: 'Invalid credentials',
        message: 'Email or password is incorrect'
      })
    }

    // Update last login
    await this.knex('users')
      .where('id', user.id)
      .update({ last_login_at: this.knex.fn.now() })

    // Generate JWT token
    const token = this.jwt.sign({ 
      id: user.id, 
      email: user.email 
    })

    // Load user roles and groups
    const roles = await this.knex('user_roles')
      .join('roles', 'roles.id', 'user_roles.role_id')
      .select('roles.code', 'roles.title')
      .where('user_roles.user_id', user.id)

    const groups = await this.knex('group_users')
      .join('groups', 'groups.id', 'group_users.group_id')
      .select('groups.code', 'groups.title')
      .where('group_users.user_id', user.id)
      .whereNull('groups.deleted_at')

    // Log activity
    await this.knex('activity_logs').insert({
      user_id: user.id,
      action: 'login',
      entity_type: 'user',
      entity_id: user.id,
      ip_address: request.ip,
      user_agent: request.headers['user-agent']
    })

    return {
      user: {
        id: user.id,
        uuid: user.uuid,
        email: user.email,
        full_name: user.full_name,
        roles: roles.map(r => r.code),
        groups: groups.map(g => g.code),
        last_login_at: user.last_login_at
      },
      token
    }
  },

  // Refresh token
  async refresh(request, reply) {
    try {
      await request.jwtVerify()
      
      const user = await this.knex('users')
        .where('id', request.user.id)
        .whereNull('deleted_at')
        .first()

      if (!user || !user.is_active) {
        return reply.status(401).send({
          error: 'Invalid user',
          message: 'User not found or inactive'
        })
      }

      // Generate new token
      const token = this.jwt.sign({ 
        id: user.id, 
        email: user.email 
      })

      return { token }
    } catch (error) {
      return reply.status(401).send({
        error: 'Invalid token',
        message: 'Token is invalid or expired'
      })
    }
  },

  // Get current user profile
  async getProfile(request, reply) {
    const { user } = request
    return { user }
  },

  // Update current user profile
  async updateProfile(request, reply) {
    const { full_name } = request.body
    const userId = request.user.id

    const [updatedUser] = await this.knex('users')
      .where('id', userId)
      .update({ 
        full_name,
        updated_at: this.knex.fn.now()
      })
      .returning(['id', 'uuid', 'email', 'full_name', 'updated_at'])

    // Log activity
    await this.knex('activity_logs').insert({
      user_id: userId,
      action: 'update_profile',
      entity_type: 'user',
      entity_id: userId,
      ip_address: request.ip,
      user_agent: request.headers['user-agent'],
      data: { full_name }
    })

    return { user: updatedUser }
  },

  // Change password
  async changePassword(request, reply) {
    const { current_password, new_password } = request.body
    const userId = request.user.id

    // Get current user
    const user = await this.knex('users')
      .where('id', userId)
      .first()

    // Verify current password
    const validPassword = await bcrypt.compare(current_password, user.password_hash)
    if (!validPassword) {
      return reply.status(400).send({
        error: 'Invalid password',
        message: 'Current password is incorrect'
      })
    }

    // Hash new password
    const password_hash = await bcrypt.hash(new_password, 12)

    // Update password
    await this.knex('users')
      .where('id', userId)
      .update({ 
        password_hash,
        updated_at: this.knex.fn.now()
      })

    // Log activity
    await this.knex('activity_logs').insert({
      user_id: userId,
      action: 'change_password',
      entity_type: 'user',
      entity_id: userId,
      ip_address: request.ip,
      user_agent: request.headers['user-agent']
    })

    return { message: 'Password changed successfully' }
  },

  // List users (admin)
  async listUsers(request, reply) {
    const { page = 1, limit = 10, search = '' } = request.query
    const offset = (page - 1) * limit

    let query = this.knex('users')
      .select('id', 'uuid', 'email', 'full_name', 'is_active', 'created_at', 'last_login_at')
      .whereNull('deleted_at')

    if (search) {
      query = query.where(function() {
        this.where('email', 'ilike', `%${search}%`)
          .orWhere('full_name', 'ilike', `%${search}%`)
      })
    }

    const users = await query
      .orderBy('created_at', 'desc')
      .limit(limit)
      .offset(offset)

    const total = await this.knex('users')
      .whereNull('deleted_at')
      .modify(function(queryBuilder) {
        if (search) {
          queryBuilder.where(function() {
            this.where('email', 'ilike', `%${search}%`)
              .orWhere('full_name', 'ilike', `%${search}%`)
          })
        }
      })
      .count('* as count')
      .first()

    return {
      users,
      pagination: {
        page: parseInt(page),
        limit: parseInt(limit),
        total: parseInt(total.count),
        pages: Math.ceil(total.count / limit)
      }
    }
  },

  // Get user by ID (admin)
  async getUser(request, reply) {
    const { id } = request.params

    const user = await this.knex('users')
      .select('id', 'uuid', 'email', 'full_name', 'is_active', 'created_at', 'last_login_at')
      .where('id', id)
      .whereNull('deleted_at')
      .first()

    if (!user) {
      return reply.status(404).send({
        error: 'User not found',
        message: 'User does not exist'
      })
    }

    // Load user roles
    const roles = await this.knex('user_roles')
      .join('roles', 'roles.id', 'user_roles.role_id')
      .select('roles.id', 'roles.code', 'roles.title')
      .where('user_roles.user_id', user.id)

    // Load user groups
    const groups = await this.knex('group_users')
      .join('groups', 'groups.id', 'group_users.group_id')
      .select('groups.id', 'groups.code', 'groups.title')
      .where('group_users.user_id', user.id)
      .whereNull('groups.deleted_at')

    return {
      ...user,
      roles,
      groups
    }
  },

  // Update user (admin)
  async updateUser(request, reply) {
    const { id } = request.params
    const { full_name, is_active } = request.body

    const [updatedUser] = await this.knex('users')
      .where('id', id)
      .whereNull('deleted_at')
      .update({ 
        full_name,
        is_active,
        updated_at: this.knex.fn.now()
      })
      .returning(['id', 'uuid', 'email', 'full_name', 'is_active', 'updated_at'])

    if (!updatedUser) {
      return reply.status(404).send({
        error: 'User not found',
        message: 'User does not exist'
      })
    }

    // Log activity
    await this.knex('activity_logs').insert({
      user_id: request.user.id,
      action: 'update_user',
      entity_type: 'user',
      entity_id: id,
      ip_address: request.ip,
      user_agent: request.headers['user-agent'],
      data: { full_name, is_active }
    })

    return { user: updatedUser }
  },

  // Update user status (admin)
  async updateUserStatus(request, reply) {
    const { id } = request.params
    const { is_active } = request.body

    const [updatedUser] = await this.knex('users')
      .where('id', id)
      .whereNull('deleted_at')
      .update({ 
        is_active,
        updated_at: this.knex.fn.now()
      })
      .returning(['id', 'is_active'])

    if (!updatedUser) {
      return reply.status(404).send({
        error: 'User not found',
        message: 'User does not exist'
      })
    }

    // Log activity
    await this.knex('activity_logs').insert({
      user_id: request.user.id,
      action: is_active ? 'activate_user' : 'deactivate_user',
      entity_type: 'user',
      entity_id: id,
      ip_address: request.ip,
      user_agent: request.headers['user-agent']
    })

    return { 
      message: `User ${is_active ? 'activated' : 'deactivated'} successfully`,
      user: updatedUser
    }
  },

  // Assign role to user (admin)
  async assignRole(request, reply) {
    const { id } = request.params
    const { role_id } = request.body

    // Check if user exists
    const user = await this.knex('users')
      .where('id', id)
      .whereNull('deleted_at')
      .first()

    if (!user) {
      return reply.status(404).send({
        error: 'User not found',
        message: 'User does not exist'
      })
    }

    // Check if role exists
    const role = await this.knex('roles')
      .where('id', role_id)
      .first()

    if (!role) {
      return reply.status(404).send({
        error: 'Role not found',
        message: 'Role does not exist'
      })
    }

    // Check if already assigned
    const existingAssignment = await this.knex('user_roles')
      .where({ user_id: id, role_id })
      .first()

    if (existingAssignment) {
      return reply.status(409).send({
        error: 'Role already assigned',
        message: 'User already has this role'
      })
    }

    // Assign role
    await this.knex('user_roles').insert({
      user_id: id,
      role_id,
      assigned_by: request.user.id
    })

    // Log activity
    await this.knex('activity_logs').insert({
      user_id: request.user.id,
      action: 'assign_role',
      entity_type: 'user',
      entity_id: id,
      ip_address: request.ip,
      user_agent: request.headers['user-agent'],
      data: { role_id, role_code: role.code }
    })

    return { message: 'Role assigned successfully' }
  },

  // Remove role from user (admin)
  async removeRole(request, reply) {
    const { id, roleId } = request.params

    const deleted = await this.knex('user_roles')
      .where({ user_id: id, role_id: roleId })
      .del()

    if (!deleted) {
      return reply.status(404).send({
        error: 'Assignment not found',
        message: 'User does not have this role'
      })
    }

    // Log activity
    await this.knex('activity_logs').insert({
      user_id: request.user.id,
      action: 'remove_role',
      entity_type: 'user',
      entity_id: id,
      ip_address: request.ip,
      user_agent: request.headers['user-agent'],
      data: { role_id: roleId }
    })

    return { message: 'Role removed successfully' }
  }
}

export default authController