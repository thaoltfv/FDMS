import Model from './Model.js'

export default class Migrate extends Model {
	buildUrl (request) {
		const { params } = request
		return ['migrate/list', ...params]
	}
	static async getMigrate (limit, page) {
		return (new this()).request({ method: 'GET', url: `migrate/async-data?limit=${limit}&page=${page}`, isStatic: true })
	}
	static async getS3 (id, limit, page) {
		return (new this()).request({ method: 'GET', url: `migrate/async-s3?id=${id}&limit=${limit}&page=${page}`, isStatic: true })
	}
	static async getElastic (id, limit, page) {
		return (new this()).request({ method: 'GET', url: `migrate/async-elastic?id=${id}&limit=${limit}&page=${page}`, isStatic: true })
	}
	static async getStatus () {
		return (new this()).request({ method: 'GET', url: `migrate/async-migrate-status`, isStatic: true })
	}
}
