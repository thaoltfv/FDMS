import Model from './Model.js'

export default class AppraiserCompany extends Model {
	buildUrl(request) {
		const { params } = request
		return ['appraiser-company', ...params]
	}
	static async deleteApartment(id) {
		return (new this()).request({ method: 'DELETE', url: `dictionary/${id}`, isStatic: true })
	}
	static async update(params) {
		if (params.id) { return (new this()).request({ method: 'PUT', url: `appraiser-company/${params.id}`, data: params, isStatic: true }) }
	}
	static async detail() {
		return (new this()).request({ method: 'GET', url: `appraiser-company`, isStatic: true })
	}
	static async delete(id) {
		return (new this()).request({ method: 'DELETE', url: `appraiser-company/${id}`, isStatic: true })
	}
	static async getAppraisersManager() {
		// return (new this()).request({ method: 'GET', url: `appraisers?search=150`, isStatic: true })
		// return (new this()).request({ method: 'GET', url: `appraisers`, isStatic: true })
		return (new this()).request({ method: 'GET', url: `appraisers?is_legal_representative=1`, isStatic: true })
	}
	static async detailNoAuth() {
		return (new this()).request({ method: 'GET', url: `appraiser-company-noauth`, isStatic: true })
	}
	static async getCompanyLogo() {
		return (new this()).request({ method: 'GET', url: `get-company-logo`, isStatic: true })
	}
	static async lookUpCertificate(data) {
		const dataSend = { data: data }
		return (new this()).request({ method: 'POST', url: `look-up-certificate`, data: dataSend, isStatic: true })
	}
	static async lookUpEmployee(data) {
		const dataSend = { data: data }
		return (new this()).request({ method: 'POST', url: `look-up-employee`, data: dataSend, isStatic: true })
	}
}
