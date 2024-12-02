import Model from './Model.js'

export default class CustomerGroupSecond extends Model {
	buildUrl(request) {
		const { params } = request
		return ['customer-group-second', ...params]
	}
}
