import Model from './Model.js'

export default class AppraiseLaw extends Model {
	buildUrl (request) {
		const { params } = request
		return ['appraise-law', ...params]
	}
	static async update (params) {
		if (params.id) {
			return (new this()).request({ method: 'PUT', url: `appraise-law/${params.id}`, data: params, isStatic: true })
		}
	}
	static async deleteAppraiser (id) {
		return (new this()).request({ method: 'DELETE', url: `appraise-law/${id}`, isStatic: true })
	}
	static async deleteApartment (id) {
		return (new this()).request({ method: 'DELETE', url: `dictionary/${id}`, isStatic: true })
	}
	static async getDictionary (data) {
		return (new this()).request({ method: 'GET', url: `appraise/dictionary/${data}`, isStatic: true })
	}
}
