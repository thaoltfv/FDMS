import Model from './Model.js'

export default class Apartment extends Model {
	buildUrl (request) {
		const { params } = request
		return ['project', ...params]
	}
	static async deleteApartment (id) {
		return (new this()).request({ method: 'DELETE', url: `apartment/${id}`, isStatic: true })
	}
	static async getProvince () {
		return (new this()).request({ method: 'GET', url: `provinces`, isStatic: true })
	}
	static async getDistrict (id) {
		return (new this()).request({ method: 'GET', url: `districts?province_id=${id}`, isStatic: true })
	}
	static async getWard (id) {
		return (new this()).request({ method: 'GET', url: `wards?district_id=${id}`, isStatic: true })
	}
	static async getStreet (id) {
		return (new this()).request({ method: 'GET', url: `streets?district_id=${id}`, isStatic: true })
	}
	static async postProject (data, id = '') {
		return (new this()).request({ method: 'POST', url: `project/${id}`, data: data, isStatic: true })
	}
}
