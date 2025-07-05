// CommonJs
const fastify = require('fastify')({
    logger: true
})

// Register plugins
fastify.register(require('@fastify/cors'), {
    origin: true,
    credentials: true
})

fastify.register(require('@fastify/jwt'), {
    secret: process.env.JWT_SECRET || 'a-very-strong-and-long-secret-for-jwt'
})

// Swagger configuration
fastify.register(require('@fastify/swagger'), {
    swagger: {
        info: {
            title: 'FDMS API',
            description: 'Fast Document Management System API',
            version: '1.0.0'
        },
        host: 'localhost:3000',
        schemes: ['http'],
        consumes: ['application/json'],
        produces: ['application/json'],
        securityDefinitions: {
            bearerAuth: {
                type: 'apiKey',
                name: 'Authorization',
                in: 'header',
                description: 'JWT Authorization header using the Bearer scheme. Example: "Bearer {token}"'
            }
        },
        security: [
            {
                bearerAuth: []
            }
        ]
    }
})

fastify.register(require('@fastify/swagger-ui'), {
    routePrefix: '/documentation',
    uiConfig: {
        docExpansion: 'full',
        deepLinking: false
    },
    uiHooks: {
        onRequest: function (request, reply, next) {
            next()
        },
        preHandler: function (request, reply, next) {
            next()
        }
    },
    staticCSP: true,
    transformStaticCSP: (header) => header,
    transformSpecification: (swaggerObject, request, reply) => {
        return swaggerObject
    },
    transformSpecificationClone: true
})

// Register routes
fastify.register(require('./modules/auth/routes'), { prefix: '/api/auth' })

// Health check endpoint
fastify.get('/health', {
    schema: {
        description: 'Health check endpoint',
        tags: ['System'],
        response: {
            200: {
                description: 'Service is healthy',
                type: 'object',
                properties: {
                    status: { type: 'string' },
                    timestamp: { type: 'string' },
                    uptime: { type: 'number' }
                }
            }
        }
    }
}, async (request, reply) => {
    return {
        status: 'ok',
        timestamp: new Date().toISOString(),
        uptime: process.uptime()
    }
})

// Root endpoint
fastify.get('/', {
    schema: {
        description: 'API root endpoint',
        tags: ['System'],
        response: {
            200: {
                description: 'API information',
                type: 'object',
                properties: {
                    name: { type: 'string' },
                    version: { type: 'string' },
                    description: { type: 'string' },
                    documentation: { type: 'string' }
                }
            }
        }
    }
}, async (request, reply) => {
    return {
        name: 'FDMS API',
        version: '1.0.0',
        description: 'Fast Document Management System API',
        documentation: '/documentation'
    }
})

// Error handler
fastify.setErrorHandler(function (error, request, reply) {
    fastify.log.error(error)
    
    if (error.validation) {
        return reply.code(400).send({
            error: 'Validation Error',
            message: 'Request validation failed',
            details: error.validation
        })
    }
    
    return reply.code(500).send({
        error: 'Internal Server Error',
        message: 'An unexpected error occurred'
    })
})

// Run the server!
const start = async () => {
    try {
        await fastify.listen({ host: '0.0.0.0', port: process.env.BACKEND_PORT || 3000 })
        fastify.log.info(`Server listening on ${fastify.server.address()}`)
    } catch (err) {
        fastify.log.error(err)
        process.exit(1)
    }
}

start()