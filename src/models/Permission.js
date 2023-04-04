import Model from './Model.js'

export default class Permission extends Model {
	buildUrl (request) {
		const { params } = request
		return ['permission', ...params]
	}

	static async getPermissionScreen () {
		return (new this()).request({ method: 'GET', url: `permission/screen`, isStatic: true })
	}
}
