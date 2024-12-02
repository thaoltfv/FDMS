import Model from './Model.js'

export default class PriceEstimate extends Model {
	buildUrl (request) {
		const { params } = request
		return ['price_estimate', ...params]
	}
	static async getSearchAll (transaction, distance, location, asset_type_ids) {
		return (new this()).request({ method: 'GET', url: `asset-generals/search?transaction_type=${transaction}&distance=${distance}&location=${location}&asset_type_ids=${asset_type_ids}`, isStatic: true })
	}
	static async PostPriceEstimate ({ province_id, province, district_id, district, ward_id, ward, street_id, street, location, distance, front_side, main_road_length, recognized, unrecognized, building, estimate_type, transaction_type_ids, doc_num, plot_num, user_request, note }) {
		const data = { recognized: recognized, unrecognized: unrecognized, building: building, province_id: province_id, province: province, district_id: district_id, district: district, ward_id: ward_id, ward: ward, street_id: street_id, street: street, location: location, distance: distance, front_side: front_side, main_road_length: main_road_length, estimate_type: estimate_type, transaction_type_ids: transaction_type_ids, doc_num: doc_num, plot_num: plot_num, user_request: user_request, note: note }
		return (new this()).request({ method: 'POST', url: `asset-generals/estimate-price`, data, isStatic: true })
	}
	static async LogPriceEstimate ({ input }) {
		const data = input
		return (new this()).request({ method: 'POST', url: `estimate-log`, data, isStatic: true })
	}
	static async PostPriceEstimateApartment ({ location, distance, estimate_type, apartment_id, apartment, district_id }) {
		const data = { location: location, distance: distance, estimate_type: estimate_type, apartment_id: apartment_id, apartment: apartment, district_id: district_id }
		return (new this()).request({ method: 'POST', url: `asset-generals/estimate-price`, data, isStatic: true })
	}
	static async getAddress ({ province, district, wards }) {
		const data = { province: province, district: district, wards: wards }
		return (new this()).request({ method: 'POST', url: `address/find`, data, isStatic: true })
	}
	static async getStreet ({ district_id, street }) {
		const data = { district_id: district_id, street: street }
		return (new this()).request({ method: 'POST', url: `address/find/street`, data, isStatic: true })
	}
	static async LogPriceEstimates ({ input }) {
		const data = input
		return (new this()).request({ method: 'POST', url: `estimate-logs`, data, isStatic: true })
	}
	static async logAddressEstimates (input) {
		const data = { input: input }
		return (new this()).request({ method: 'POST', url: `address-log`, data, isStatic: true })
	}
}
