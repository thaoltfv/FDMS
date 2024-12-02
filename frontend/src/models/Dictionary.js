import Model from './Model.js'

export default class Apartment extends Model {
	buildUrl(request) {
		const { params } = request
		return ['dictionary', ...params]
	}
	static async deleteApartment(id) {
		return (new this()).request({ method: 'DELETE', url: `dictionary/${id}`, isStatic: true })
	}
	static async changeStatusCustomerGroup(id, status) {
		return (new this()).request({ method: 'GET', url: `dictionaries/change-status-customer-group/${id}/${status}`, isStatic: true })
	}
	static async getDictionaries() {
		return (new this()).request({ method: 'GET', url: `dictionaries`, isStatic: true })
	}
}
