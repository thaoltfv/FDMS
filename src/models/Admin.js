import Model from './Model.js'

export default class Admin extends Model {
	buildUrl ({ params }) {
		return ['admins', ...params]
	}

	static async profile (user_id, configs = {}) {
		return (new this()).request({ method: 'GET', url: `profile/${user_id}`, ...configs, isStatic: true })
	}

	static async delete_admin (id) {
		return (new this()).request({ method: 'DELETE', url: `admins/${id}`, isStatic: true })
	}

	static async login (email, token) {
		const data = { email: email, token: token }
		return (new this()).request({ method: 'POST', url: `auth/login`, data, isStatic: true })
	}

	static async logout () {
		return (new this()).request({ method: 'POST', url: `auth/logout`, isStatic: true })
	}
	static async getPing () {
		return (new this()).request({ method: 'GET', url: `ping`, isStatic: true })
	}

	afterRequest ({ data }, { action, isStatic }) {
		if (action === 'custom') {
			return data
		}
		if (action === 'paginate') {
			data.data = this.make(data.data)
			return data
		}
		return isStatic ? this.make(data.data) : this.fill(data.data)
	}
}
