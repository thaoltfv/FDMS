import Model from './Model.js'

export default class Role extends Model {
	buildUrl (request) {
		const { params } = request
		return ['role', ...params]
	}

	static async delete_role (id) {
		return (new this()).request({ method: 'DELETE', url: `role/${id}`, isStatic: true })
	}
}
