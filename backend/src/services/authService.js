const bcrypt = require('bcryptjs');
const jwt = require('jsonwebtoken');
const db = require('../database/connection');

class AuthService {
  /**
   * Hash a password
   * @param {string} password - Plain text password
   * @returns {Promise<string>} Hashed password
   */
  async hashPassword(password) {
    const saltRounds = 12;
    return await bcrypt.hash(password, saltRounds);
  }

  /**
   * Compare password with hash
   * @param {string} password - Plain text password
   * @param {string} hash - Hashed password
   * @returns {Promise<boolean>} True if password matches
   */
  async comparePassword(password, hash) {
    return await bcrypt.compare(password, hash);
  }

  /**
   * Generate JWT token
   * @param {Object} payload - Token payload
   * @returns {string} JWT token
   */
  generateToken(payload) {
    return jwt.sign(payload, process.env.JWT_SECRET, {
      expiresIn: process.env.JWT_EXPIRATION || '1d'
    });
  }

  /**
   * Verify JWT token
   * @param {string} token - JWT token
   * @returns {Object} Decoded token payload
   */
  verifyToken(token) {
    return jwt.verify(token, process.env.JWT_SECRET);
  }

  /**
   * Register a new user
   * @param {Object} userData - User registration data
   * @returns {Promise<Object>} Created user (without password)
   */
  async register(userData) {
    const { email, password, full_name } = userData;

    // Check if user already exists
    const existingUser = await db('users')
      .where('email', email)
      .whereNull('deleted_at')
      .first();

    if (existingUser) {
      throw new Error('User with this email already exists');
    }

    // Hash password
    const passwordHash = await this.hashPassword(password);

    // Create user
    const [user] = await db('users')
      .insert({
        email,
        password_hash: passwordHash,
        full_name
      })
      .returning(['id', 'uuid', 'email', 'full_name', 'is_active', 'created_at']);

    return user;
  }

  /**
   * Authenticate user login
   * @param {string} email - User email
   * @param {string} password - User password
   * @returns {Promise<Object>} User data and token
   */
  async login(email, password) {
    // Find user
    const user = await db('users')
      .where('email', email)
      .whereNull('deleted_at')
      .first();

    if (!user) {
      throw new Error('Invalid credentials');
    }

    // Check if user is active
    if (!user.is_active) {
      throw new Error('Account is deactivated');
    }

    // Verify password
    const isValidPassword = await this.comparePassword(password, user.password_hash);
    if (!isValidPassword) {
      throw new Error('Invalid credentials');
    }

    // Update last login
    await db('users')
      .where('id', user.id)
      .update({ last_login_at: db.fn.now() });

    // Get user roles
    const roles = await db('user_roles')
      .join('roles', 'user_roles.role_id', 'roles.id')
      .where('user_roles.user_id', user.id)
      .select('roles.code', 'roles.title');

    // Generate token
    const token = this.generateToken({
      userId: user.id,
      email: user.email,
      roles: roles.map(r => r.code)
    });

    return {
      user: {
        id: user.id,
        uuid: user.uuid,
        email: user.email,
        full_name: user.full_name,
        roles: roles
      },
      token
    };
  }

  /**
   * Get user by ID
   * @param {number} userId - User ID
   * @returns {Promise<Object>} User data
   */
  async getUserById(userId) {
    const user = await db('users')
      .where('id', userId)
      .whereNull('deleted_at')
      .first();

    if (!user) {
      return null;
    }

    // Get user roles
    const roles = await db('user_roles')
      .join('roles', 'user_roles.role_id', 'roles.id')
      .where('user_roles.user_id', userId)
      .select('roles.code', 'roles.title');

    return {
      id: user.id,
      uuid: user.uuid,
      email: user.email,
      full_name: user.full_name,
      is_active: user.is_active,
      roles: roles
    };
  }

  /**
   * Refresh user token
   * @param {string} token - Current JWT token
   * @returns {Promise<Object>} New token and user data
   */
  async refreshToken(token) {
    try {
      const decoded = this.verifyToken(token);
      const user = await this.getUserById(decoded.userId);

      if (!user) {
        throw new Error('User not found');
      }

      if (!user.is_active) {
        throw new Error('Account is deactivated');
      }

      const newToken = this.generateToken({
        userId: user.id,
        email: user.email,
        roles: user.roles.map(r => r.code)
      });

      return {
        user,
        token: newToken
      };
    } catch (error) {
      throw new Error('Invalid token');
    }
  }
}

module.exports = new AuthService(); 