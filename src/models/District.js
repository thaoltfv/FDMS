import Model from './Model.js'

export default class District extends Model {
	buildUrl (request) {
		const { params } = request
		return ['district', ...params]
	}
	static async deleteDistrict (id) {
		return (new this()).request({ method: 'DELETE', url: `district/${id}`, isStatic: true })
	}
	static async getProvince () {
		return (new this()).request({ method: 'GET', url: `provinces`, isStatic: true })
	}
}
