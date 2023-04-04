import Model from './Model.js'

export default class Branch extends Model {
	buildUrl (request) {
		const { params } = request
		return ['branch', ...params]
	}

	static async deleteBranch (id) {
		return (new this()).request({ method: 'DELETE', url: `branch/${id}`, isStatic: true })
	}
	static async getProvince () {
		return (new this()).request({ method: 'GET', url: `provinces`, isStatic: true })
	}
	static async getDistrict (id) {
		return (new this()).request({ method: 'GET', url: `districts?province_id=${id}`, isStatic: true })
	}
}
