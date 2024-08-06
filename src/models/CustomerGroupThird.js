import Model from './Model.js'

export default class CustomerGroupThird extends Model {
	buildUrl(request) {
		const { params } = request
		return ['customer-group-third', ...params]
	}
}
