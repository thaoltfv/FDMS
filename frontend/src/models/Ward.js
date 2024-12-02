import Model from './Model.js'

export default class Ward extends Model {
	buildUrl (request) {
		const { params } = request
		return ['ward', ...params]
	}
	static async deleteWard (id) {
		return (new this()).request({ method: 'DELETE', url: `ward/${id}`, isStatic: true })
	}
	static async getProvince () {
		return (new this()).request({ method: 'GET', url: `provinces`, isStatic: true })
	}
	static async getDistrict (id) {
		return (new this()).request({ method: 'GET', url: `districts?province_id=${id}`, isStatic: true })
	}
}
