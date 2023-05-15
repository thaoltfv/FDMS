import Model from './Model.js'

export default class AppraiseData extends Model {
	buildUrl (request) {
		const { params } = request
		return ['appraise', ...params]
	}
	static async deleteApartment (id) {
		return (new this()).request({ method: 'DELETE', url: `dictionary/${id}`, isStatic: true })
	}
	static async getAppraiseID (id, version) {
		// return (new this()).request({ method: 'GET', url: `appraise/${id}?version=${version}`, isStatic: true })
		return (new this()).request({ method: 'GET', url: `appraise/${id}`, isStatic: true })
	}
	static async getDictionary (data) {
		return (new this()).request({ method: 'GET', url: `appraise/dictionary/${data}`, isStatic: true })
	}
	static async getAsset (data) {
		return (new this()).request({ method: 'POST', url: `appraises/asset`, data, isStatic: true })
	}
	static async getSearchAll (distance, location, front_side, transaction, assetType, isCheckFrontside) {
		return (new this()).request({ method: 'GET', url: `asset-generals/search?distance=${distance}&location=${location}&front_side=${front_side}&transaction_type=${transaction}&asset_type_ids=${assetType}&is_check_frontside=${isCheckFrontside}`, isStatic: true })
	}
	static async getAppraisals (ids) {
		const data = '[' + ids + ']'
		return (new this()).request({ method: 'GET', url: `appraises/ids/${data}`, isStatic: true })
	}
	static async getImage (data) {
		return (new this()).request({ method: 'POST', url: `appraises/image`, data, isStatic: true })
	}
	static async updateStatusAppraise (id, status) {
		const data = { status: status }
		// const data1 = { status }
		return (new this()).request({ method: 'POST', url: `appraise/status/${id}`, data, isStatic: true })
	}
	static async updateStatusRealestate (id, status) {
		const data = { status: status }
		// const data1 = { status }
		return (new this()).request({ method: 'POST', url: `real_estate/status/${id}`, data, isStatic: true })
	}
	static async postDataComparison (data, dataConstruction, total_desicion_average) {
		const comparison_tangible_factor = { comparison_tangible_factor: data, construction_company: dataConstruction, total_desicion_average }
		return (new this()).request({ method: 'POST', url: `appraises/tangible-comparison`, data: comparison_tangible_factor, isStatic: true })
	}
	static async findAll () {
		return (new this()).request({ method: 'GET', url: `appraises/`, isStatic: true })
	}
}
