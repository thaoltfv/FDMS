import fp from 'fastify-plugin'

const errorHandler = async (fastify, options) => {
  // Global error handler
  fastify.setErrorHandler(async (error, request, reply) => {
    const { validation, statusCode } = error

    // Log error details
    fastify.log.error({
      error,
      request: {
        method: request.method,
        url: request.url,
        headers: request.headers,
        params: request.params,
        query: request.query
      }
    }, 'Request error')

    // Validation errors
    if (validation) {
      return reply.status(400).send({
        error: 'Validation Error',
        message: 'Invalid input data',
        details: validation,
        statusCode: 400
      })
    }

    // JWT errors
    if (error.code === 'FST_JWT_AUTHORIZATION_TOKEN_EXPIRED') {
      return reply.status(401).send({
        error: 'Token Expired',
        message: 'JWT token has expired',
        statusCode: 401
      })
    }

    if (error.code === 'FST_JWT_NO_AUTHORIZATION_IN_HEADER') {
      return reply.status(401).send({
        error: 'No Authorization',
        message: 'Authorization header is required',
        statusCode: 401
      })
    }

    if (error.code && error.code.startsWith('FST_JWT_')) {
      return reply.status(401).send({
        error: 'Authentication Error',
        message: 'Invalid authentication token',
        statusCode: 401
      })
    }

    // Database constraint errors
    if (error.code === '23505') { // Unique constraint violation
      const detail = error.detail || ''
      let field = 'field'
      
      if (detail.includes('email')) field = 'email'
      else if (detail.includes('code')) field = 'code'
      else if (detail.includes('name')) field = 'name'

      return reply.status(409).send({
        error: 'Conflict',
        message: `${field} already exists`,
        statusCode: 409
      })
    }

    if (error.code === '23503') { // Foreign key constraint violation
      return reply.status(400).send({
        error: 'Reference Error',
        message: 'Referenced record does not exist',
        statusCode: 400
      })
    }

    if (error.code === '23502') { // Not null constraint violation
      return reply.status(400).send({
        error: 'Required Field Missing',
        message: 'Required field cannot be empty',
        statusCode: 400
      })
    }

    // Schema Manager errors
    if (error.code === 'SCHEMA_MIGRATION_FAILED') {
      return reply.status(500).send({
        error: 'Schema Migration Failed',
        message: error.message || 'Failed to migrate database schema',
        details: error.details,
        statusCode: 500
      })
    }

    if (error.code === 'INVALID_BLUEPRINT_SCHEMA') {
      return reply.status(400).send({
        error: 'Invalid Blueprint Schema',
        message: error.message || 'Blueprint schema validation failed',
        details: error.details,
        statusCode: 400
      })
    }

    // File storage errors
    if (error.code === 'FILE_TOO_LARGE') {
      return reply.status(413).send({
        error: 'File Too Large',
        message: 'File exceeds maximum allowed size',
        statusCode: 413
      })
    }

    if (error.code === 'INVALID_FILE_TYPE') {
      return reply.status(400).send({
        error: 'Invalid File Type',
        message: 'File type is not allowed',
        statusCode: 400
      })
    }

    // HTTP errors (from @fastify/sensible)
    if (statusCode && statusCode >= 400) {
      return reply.status(statusCode).send({
        error: error.name || 'HTTP Error',
        message: error.message || 'An error occurred',
        statusCode
      })
    }

    // Default server error
    const isDevelopment = process.env.NODE_ENV === 'development'
    
    return reply.status(500).send({
      error: 'Internal Server Error',
      message: isDevelopment ? error.message : 'An unexpected error occurred',
      ...(isDevelopment && { stack: error.stack }),
      statusCode: 500
    })
  })

  // Not found handler
  fastify.setNotFoundHandler(async (request, reply) => {
    return reply.status(404).send({
      error: 'Not Found',
      message: `Route ${request.method} ${request.url} not found`,
      statusCode: 404
    })
  })
}

export default fp(errorHandler)