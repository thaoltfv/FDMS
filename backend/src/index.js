import Fastify from 'fastify'
import cors from '@fastify/cors'
import jwt from '@fastify/jwt'
import multipart from '@fastify/multipart'
import swagger from '@fastify/swagger'
import swaggerUI from '@fastify/swagger-ui'
import 'dotenv/config'

// Import modules
import databasePlugin from './database/index.js'
import authModule from './modules/auth/index.js'
import blueprintModule from './modules/blueprint/index.js'
import documentModule from './modules/document/index.js'
import fileStorageModule from './modules/file-storage/index.js'

// Import middleware
import authMiddleware from './middleware/auth.js'
import errorHandler from './middleware/errorHandler.js'

const fastify = Fastify({
  logger: {
    level: process.env.LOG_LEVEL || 'info',
    ...(process.env.NODE_ENV === 'development' && {
      transport: {
        target: 'pino-pretty',
        options: {
          colorize: true
        }
      }
    })
  }
})

// Register plugins
async function registerPlugins() {
  // CORS
  await fastify.register(cors, {
    origin: process.env.FRONTEND_URL || true,
    credentials: true
  })

  // JWT
  await fastify.register(jwt, {
    secret: process.env.JWT_SECRET,
    sign: {
      expiresIn: process.env.JWT_EXPIRATION || '1d'
    }
  })

  // Multipart for file uploads
  await fastify.register(multipart, {
    limits: {
      fileSize: parseInt(process.env.MAX_FILE_SIZE) || 104857600 // 100MB
    }
  })

  // Swagger Documentation
  await fastify.register(swagger, {
    swagger: {
      info: {
        title: 'FDMS API',
        description: 'Fast Document Management System API',
        version: '1.0.0'
      },
      host: 'localhost:3000',
      schemes: ['http', 'https'],
      consumes: ['application/json'],
      produces: ['application/json'],
      securityDefinitions: {
        Bearer: {
          type: 'apiKey',
          name: 'Authorization',
          in: 'header'
        }
      }
    }
  })

  await fastify.register(swaggerUI, {
    routePrefix: '/docs',
    uiConfig: {
      docExpansion: 'list',
      deepLinking: false
    }
  })
}

// Register database
async function registerDatabase() {
  await fastify.register(databasePlugin)
}

// Register middleware
async function registerMiddleware() {
  await fastify.register(authMiddleware)
  await fastify.register(errorHandler)
}

// Register routes
async function registerRoutes() {
  // Health check
  fastify.get('/health', async (request, reply) => {
    try {
      // Check database connection
      await fastify.knex.raw('SELECT 1')
      return { status: 'healthy', timestamp: new Date().toISOString() }
    } catch (error) {
      reply.code(503)
      return { status: 'unhealthy', error: error.message }
    }
  })

  // API routes
  await fastify.register(authModule, { prefix: '/api/auth' })
  await fastify.register(blueprintModule, { prefix: '/api/blueprints' })
  await fastify.register(documentModule, { prefix: '/api/documents' })
  await fastify.register(fileStorageModule, { prefix: '/api/files' })
}

// Start server
async function start() {
  try {
    await registerPlugins()
    await registerDatabase()
    await registerMiddleware()
    await registerRoutes()

    const port = process.env.PORT || 3000
    const host = process.env.HOST || '0.0.0.0'

    await fastify.listen({ port, host })
    
    fastify.log.info(`FDMS Backend started on http://${host}:${port}`)
    fastify.log.info(`API Documentation available at http://${host}:${port}/docs`)
  } catch (err) {
    fastify.log.error(err)
    process.exit(1)
  }
}

// Handle shutdown
process.on('SIGTERM', async () => {
  fastify.log.info('Received SIGTERM, shutting down gracefully')
  await fastify.close()
  process.exit(0)
})

process.on('SIGINT', async () => {
  fastify.log.info('Received SIGINT, shutting down gracefully')
  await fastify.close()
  process.exit(0)
})

start()