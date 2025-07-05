const authService = require('../../services/authService');
const { authenticate } = require('../../middleware/auth');

/**
 * Authentication routes
 */
async function authRoutes(fastify, options) {
  // Register schema for Swagger documentation
  const registerSchema = {
    type: 'object',
    required: ['email', 'password', 'full_name'],
    properties: {
      email: { type: 'string', format: 'email' },
      password: { type: 'string', minLength: 6 },
      full_name: { type: 'string', minLength: 2 }
    }
  };

  const loginSchema = {
    type: 'object',
    required: ['email', 'password'],
    properties: {
      email: { type: 'string', format: 'email' },
      password: { type: 'string' }
    }
  };

  const userResponseSchema = {
    type: 'object',
    properties: {
      id: { type: 'integer' },
      uuid: { type: 'string', format: 'uuid' },
      email: { type: 'string', format: 'email' },
      full_name: { type: 'string' },
      is_active: { type: 'boolean' },
      roles: {
        type: 'array',
        items: {
          type: 'object',
          properties: {
            code: { type: 'string' },
            title: { type: 'string' }
          }
        }
      }
    }
  };

  const loginResponseSchema = {
    type: 'object',
    properties: {
      user: userResponseSchema,
      token: { type: 'string' }
    }
  };

  // Register user
  fastify.post('/register', {
    schema: {
      description: 'Register a new user',
      tags: ['Authentication'],
      body: registerSchema,
      response: {
        201: {
          description: 'User registered successfully',
          type: 'object',
          properties: {
            message: { type: 'string' },
            user: userResponseSchema
          }
        },
        400: {
          description: 'Bad request',
          type: 'object',
          properties: {
            error: { type: 'string' },
            message: { type: 'string' }
          }
        }
      }
    }
  }, async (request, reply) => {
    try {
      const userData = request.body;
      
      // Validate password strength
      if (userData.password.length < 6) {
        return reply.code(400).send({
          error: 'Bad Request',
          message: 'Password must be at least 6 characters long'
        });
      }

      const user = await authService.register(userData);
      
      return reply.code(201).send({
        message: 'User registered successfully',
        user
      });
    } catch (error) {
      if (error.message === 'User with this email already exists') {
        return reply.code(400).send({
          error: 'Bad Request',
          message: error.message
        });
      }
      
      fastify.log.error(error);
      return reply.code(500).send({
        error: 'Internal Server Error',
        message: 'Failed to register user'
      });
    }
  });

  // Login user
  fastify.post('/login', {
    schema: {
      description: 'Authenticate user and get JWT token',
      tags: ['Authentication'],
      body: loginSchema,
      response: {
        200: {
          description: 'Login successful',
          type: 'object',
          properties: {
            message: { type: 'string' },
            user: userResponseSchema,
            token: { type: 'string' }
          }
        },
        401: {
          description: 'Authentication failed',
          type: 'object',
          properties: {
            error: { type: 'string' },
            message: { type: 'string' }
          }
        }
      }
    }
  }, async (request, reply) => {
    try {
      const { email, password } = request.body;
      
      const result = await authService.login(email, password);
      
      return reply.send({
        message: 'Login successful',
        user: result.user,
        token: result.token
      });
    } catch (error) {
      if (error.message === 'Invalid credentials' || error.message === 'Account is deactivated') {
        return reply.code(401).send({
          error: 'Unauthorized',
          message: error.message
        });
      }
      
      fastify.log.error(error);
      return reply.code(500).send({
        error: 'Internal Server Error',
        message: 'Login failed'
      });
    }
  });

  // Get current user profile
  fastify.get('/profile', {
    schema: {
      description: 'Get current user profile',
      tags: ['Authentication'],
      security: [{ bearerAuth: [] }],
      response: {
        200: {
          description: 'User profile',
          type: 'object',
          properties: {
            user: userResponseSchema
          }
        },
        401: {
          description: 'Unauthorized',
          type: 'object',
          properties: {
            error: { type: 'string' },
            message: { type: 'string' }
          }
        }
      }
    },
    preHandler: authenticate
  }, async (request, reply) => {
    try {
      return reply.send({
        user: request.user
      });
    } catch (error) {
      fastify.log.error(error);
      return reply.code(500).send({
        error: 'Internal Server Error',
        message: 'Failed to get profile'
      });
    }
  });

  // Refresh token
  fastify.post('/refresh', {
    schema: {
      description: 'Refresh JWT token',
      tags: ['Authentication'],
      body: {
        type: 'object',
        required: ['token'],
        properties: {
          token: { type: 'string' }
        }
      },
      response: {
        200: {
          description: 'Token refreshed',
          type: 'object',
          properties: {
            message: { type: 'string' },
            user: userResponseSchema,
            token: { type: 'string' }
          }
        },
        401: {
          description: 'Invalid token',
          type: 'object',
          properties: {
            error: { type: 'string' },
            message: { type: 'string' }
          }
        }
      }
    }
  }, async (request, reply) => {
    try {
      const { token } = request.body;
      
      const result = await authService.refreshToken(token);
      
      return reply.send({
        message: 'Token refreshed successfully',
        user: result.user,
        token: result.token
      });
    } catch (error) {
      return reply.code(401).send({
        error: 'Unauthorized',
        message: 'Invalid token'
      });
    }
  });
}

module.exports = authRoutes; 