import Model from './Model.js'

export default class CustomerGroupFourth extends Model {
	buildUrl(request) {
		const { params } = request
		return ['customer-group-fourth', ...params]
	}
}
