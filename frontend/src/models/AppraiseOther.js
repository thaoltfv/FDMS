import Model from './Model.js'

export default class Appraiser extends Model {
	buildUrl (request) {
		const { params } = request
		return ['appraise-other', ...params]
	}
	static async update (params) {
		if (params.id) {
			return (new this()).request({ method: 'PUT', url: `appraise-other/${params.id}`, data: params, isStatic: true })
		}
	}
	static async deleteAppraiser (id) {
		return (new this()).request({ method: 'DELETE', url: `appraise-other/${id}`, isStatic: true })
	}
	static async deleteApartment (id) {
		return (new this()).request({ method: 'DELETE', url: `dictionary/${id}`, isStatic: true })
	}
	static async getDictionary (data) {
		return (new this()).request({ method: 'GET', url: `appraise/dictionary/${data}`, isStatic: true })
	}
}
