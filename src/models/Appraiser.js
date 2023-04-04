import Model from './Model.js'
const nameApi = 'appraiser'
export default class Appraiser extends Model {
	buildUrl (request) {
		const { params } = request
		return [nameApi, ...params]
	}
	static async deleteAppraiser (id) {
		return (new this()).request({ method: 'DELETE', url: `${nameApi}/${id}`, isStatic: true })
	}
	static async update (params) {
		if (params.id) {
			return (new this()).request({ method: 'PUT', url: `${nameApi}/${params.id}`, data: params, isStatic: true })
		}
	}
	static async deleteApartment (id) {
		return (new this()).request({ method: 'DELETE', url: `dictionary/${id}`, isStatic: true })
	}
	static async getDictionary (data) {
		return (new this()).request({ method: 'GET', url: `appraise/dictionary/${data}`, isStatic: true })
	}
}
