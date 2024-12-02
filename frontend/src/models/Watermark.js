import Model from './Model.js'

export default class Appraise extends Model {
	buildUrl (request) {
		const { params } = request
		return ['water-mark/1', ...params]
	}
	static async getWaterMark () {
		return (new this()).request({ method: 'GET', url: `water-mark`, isStatic: true })
	}
	static async updateWaterMark (params) {
		if (params.id) { return (new this()).request({ method: 'POST', url: `water-mark/${params.id}`, data: params, isStatic: true }) }
	}
	static async uploadImageWaterMark ({ data }) {
		return (new this()).makeRequest({ method: 'POST', url: `/api/water-mark/handle-image`, data })
	}
}
