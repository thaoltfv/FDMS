const authService = require('../services/authService');

/**
 * Authentication middleware
 * Verifies JWT token and adds user info to request
 */
async function authenticate(request, reply) {
  try {
    const authHeader = request.headers.authorization;
    
    if (!authHeader || !authHeader.startsWith('Bearer ')) {
      return reply.code(401).send({
        error: 'Unauthorized',
        message: 'No token provided'
      });
    }

    const token = authHeader.substring(7); // Remove 'Bearer ' prefix
    
    try {
      const decoded = authService.verifyToken(token);
      const user = await authService.getUserById(decoded.userId);
      
      if (!user) {
        return reply.code(401).send({
          error: 'Unauthorized',
          message: 'User not found'
        });
      }

      if (!user.is_active) {
        return reply.code(401).send({
          error: 'Unauthorized',
          message: 'Account is deactivated'
        });
      }

      // Add user info to request
      request.user = user;
      request.token = token;
      
    } catch (error) {
      return reply.code(401).send({
        error: 'Unauthorized',
        message: 'Invalid token'
      });
    }
  } catch (error) {
    return reply.code(500).send({
      error: 'Internal Server Error',
      message: 'Authentication error'
    });
  }
}

/**
 * Role-based authorization middleware
 * @param {string[]} requiredRoles - Array of required role codes
 */
function authorize(requiredRoles = []) {
  return async function(request, reply) {
    try {
      // First authenticate
      await authenticate(request, reply);
      
      if (reply.sent) {
        return; // Authentication failed
      }

      // Check if user has required roles
      if (requiredRoles.length > 0) {
        const userRoles = request.user.roles.map(role => role.code);
        const hasRequiredRole = requiredRoles.some(role => userRoles.includes(role));
        
        if (!hasRequiredRole) {
          return reply.code(403).send({
            error: 'Forbidden',
            message: 'Insufficient permissions'
          });
        }
      }
    } catch (error) {
      return reply.code(500).send({
        error: 'Internal Server Error',
        message: 'Authorization error'
      });
    }
  };
}

/**
 * Optional authentication middleware
 * Adds user info if token is provided, but doesn't require it
 */
async function optionalAuth(request, reply) {
  try {
    const authHeader = request.headers.authorization;
    
    if (!authHeader || !authHeader.startsWith('Bearer ')) {
      return; // Continue without authentication
    }

    const token = authHeader.substring(7);
    
    try {
      const decoded = authService.verifyToken(token);
      const user = await authService.getUserById(decoded.userId);
      
      if (user && user.is_active) {
        request.user = user;
        request.token = token;
      }
    } catch (error) {
      // Token is invalid, but continue without authentication
      return;
    }
  } catch (error) {
    // Continue without authentication on error
    return;
  }
}

module.exports = {
  authenticate,
  authorize,
  optionalAuth
}; 