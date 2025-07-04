import Knex from 'knex'
import fp from 'fastify-plugin'

const databasePlugin = async (fastify, options) => {
  const knexConfig = {
    client: 'pg',
    connection: {
      host: process.env.DB_HOST || 'localhost',
      port: parseInt(process.env.DB_PORT) || 5432,
      database: process.env.DB_NAME || 'fdms_db',
      user: process.env.DB_USER || 'fdms_user',
      password: process.env.DB_PASSWORD || 'fdms_password'
    },
    pool: {
      min: 2,
      max: 10,
      acquireTimeoutMillis: 30000,
      createTimeoutMillis: 30000,
      destroyTimeoutMillis: 5000,
      idleTimeoutMillis: 30000,
      reapIntervalMillis: 1000,
      createRetryIntervalMillis: 100
    },
    migrations: {
      directory: './database/migrations',
      tableName: 'knex_migrations'
    },
    seeds: {
      directory: './database/seeds'
    }
  }

  const knex = Knex(knexConfig)

  // Test connection
  try {
    await knex.raw('SELECT 1')
    fastify.log.info('Database connection established successfully')
  } catch (error) {
    fastify.log.error('Failed to connect to database:', error)
    throw error
  }

  // Add to fastify instance
  fastify.decorate('knex', knex)

  // Close connection on app shutdown
  fastify.addHook('onClose', async (instance, done) => {
    await knex.destroy()
    done()
  })
}

export default fp(databasePlugin)