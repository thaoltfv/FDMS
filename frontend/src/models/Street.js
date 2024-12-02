import Model from './Model.js'

export default class Street extends Model {
	buildUrl (request) {
		const { params } = request
		return ['street', ...params]
	}
	static async deleteStreet (id) {
		return (new this()).request({ method: 'DELETE', url: `street/${id}`, isStatic: true })
	}
	static async getProvince () {
		return (new this()).request({ method: 'GET', url: `provinces`, isStatic: true })
	}
	static async getDistrict (id) {
		return (new this()).request({ method: 'GET', url: `districts?province_id=${id}`, isStatic: true })
	}
}
