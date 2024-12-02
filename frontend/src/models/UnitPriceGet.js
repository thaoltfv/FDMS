import Model from '@/models/Model'

export default class UnitPriceGet extends Model {
	buildUrl (request) {
		const { params } = request
		return ['get/unit-price', ...params]
	}
}
