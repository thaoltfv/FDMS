import Model from './Model.js'

export default class CustomerGroupFirst extends Model {
	buildUrl(request) {
		const { params } = request
		return ['customer-group-first', ...params]
	}
	static async getCustomerGroupFirstList() {
		return (new this()).request({ method: 'GET', url: `customer-group-first-all`, isStatic: true })
	}

}
