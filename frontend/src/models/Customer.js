import Model from './Model.js'

export default class Customer extends Model {
	buildUrl (request) {
		const { params } = request
		return ['customer', ...params]
	}
	static async updateStatus (status, ids) {
		const data = {ids: ids}
		return (new this()).request({ method: 'POST', url: `customers/status?status=${status}`, data: data, isStatic: true })
	}
}
