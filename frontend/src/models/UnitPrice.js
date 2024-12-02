import Model from './Model.js'

export default class UnitPrice extends Model {
	buildUrl (request) {
		const { params } = request
		return ['unit-price', ...params]
	}
}
