import Model from './Model.js'

export default class Appraise extends Model {
	buildUrl (request) {
		const { params } = request
		return ['appraise/dictionary', ...params]
	}
	static async deleteApartment (id) {
		return (new this()).request({ method: 'DELETE', url: `dictionary/${id}`, isStatic: true })
	}
	static async getDictionary (data) {
		return (new this()).request({ method: 'GET', url: `appraise/dictionary/${data}`, isStatic: true })
	}
	static async deleteDictionary (id) {
		return (new this()).request({ method: 'DELETE', url: `appraise/dictionary/${id}`, isStatic: true })
	}
	static async updateDictionary (params) {
		if (params.id) { return (new this()).request({ method: 'PUT', url: `appraise/dictionary/${params.id}`, data: params, isStatic: true }) }
	}
	static async getPrint (id) {
		return (new this()).request({ method: 'GET', url: `appraises/print/phu-luc-1/${id}`, isStatic: true })
	}
	static async getPrintAppendix (id) {
		return (new this()).request({ method: 'GET', url: `appraises/print/phu-luc-2/${id}`, isStatic: true })
	}
	static async getPrintImage (id) {
		return (new this()).request({ method: 'GET', url: `appraises/print/phu-luc-hinh-anh/${id}`, isStatic: true })
	}
	static async getPrintReport (id) {
		return (new this()).request({ method: 'GET', url: `appraises/print/bao-cao/${id}`, isStatic: true })
	}
}
