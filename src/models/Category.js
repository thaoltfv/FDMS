import Model from './Model.js'

export default class Category extends Model {
	buildUrl (request) {
		const { params } = request
		return ['province', ...params]
	}
	static async deleteProvince (id) {
		return (new this()).request({ method: 'DELETE', url: `province/${id}`, isStatic: true })
	}
	static async searchProvince (name) {
		return (new this()).request({ method: 'DELETE', url: `province/${name}`, isStatic: true })
	}
}
