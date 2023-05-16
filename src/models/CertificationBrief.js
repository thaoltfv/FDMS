//
import Model from './Model.js'

export default class CertificateBrief extends Model {
	buildUrl (request) {
		const { params } = request
		return ['certification_brief/certificate-paging', ...params]
	}
	static async getStepAvailable (id = '') {
		return (new this()).request({ method: 'GET', url: `certification_asset/step/${id}`, isStatic: true })
	}
	static async getDetailCertificate (id = '') {
		return (new this()).request({ method: 'GET', url: `certification_brief/certificate-general-infomation/${id}`, isStatic: true })
	}
	static async updateDetailCertificate (id = '', data) {
		return (new this()).request({ method: 'POST', url: `certification_brief/certificate-update-general/${id}`, data, isStatic: true })
	}
	static async getDetailCertificateBrief (id = '') {
		return (new this()).request({ method: 'GET', url: `certification_brief/certificate-infomation/${id}`, isStatic: true })
	}
	static async getUser (id = '') {
		return (new this()).request({ method: 'GET', url: `users`, isStatic: true })
	}
	static async getCustomer (search = '') {
		return (new this()).request({ method: 'GET', url: `customers?key=${search}`, isStatic: true })
	}
	static async submitStep1CertificationBrief (data, id = '') {
		return (new this()).request({ method: 'POST', url: `certification_brief/step1-infomation-certification/${id}`, data: data, isStatic: true })
	}
	static async updateStatusCertificate (id = '', data) {
		return (new this()).request({ method: 'POST', url: `certification_brief/certificate-update-status/${id}`, data: data, isStatic: true })
	}
	static async getListAppraise (id = '') {
		return (new this()).request({ method: 'GET', url: `certification_brief/certificate-appraise-list?certificate_id=${id}`, isStatic: true })
	}
	static async updateAppraiseCertificate (id = '', data) {
		return (new this()).request({ method: 'POST', url: `certification_brief/certificate-update-appraise/${id}`, data: data, isStatic: true })
	}
	static async getListKanbanCertificate (search = '') {
		let searchInput = { search_input: search }
		return (new this()).request({ method: 'GET', url: `workflow/certificate-workflow`, query: searchInput, isStatic: true })
	}
	static async getTimeStamp () {
		return (new this()).request({ method: 'GET', url: `certification_brief/processing-time`, isStatic: true })
	}
	static async updateAppraisers (id = '', data) {
		return (new this()).request({ method: 'POST', url: `certification_brief/certificate-update-appraisers/${id}`, data: data, isStatic: true })
	}
	static async getAppraiseCompare (ids) {
		const data = '[' + ids + ']'
		return (new this()).request({ method: 'GET', url: `certification_brief/comparison-appraise?ids=${data}`, isStatic: true })
	}
	static async exportDataCertificationBrief (data) {
		// if (process.env.CLIENT_ENV === 'trial') {
		// 	return {
		// 		error: {message: 'Hiện tại chức năng này chưa được mở ở phiên bản dùng thử'}
		// 	}
		// }
		const { fromDate, toDate, appraiser_perform_id, appraiser_id, customer_id, createdBy, status } = data
		return (new this()).request({ method: 'GET', url: `certification_brief/brief-export?fromDate=${fromDate}&toDate=${toDate}&status=${status}&created_by=${createdBy}&appraiser_id=${appraiser_id}&appraiser_perform_id=${appraiser_perform_id}&customer_id=${customer_id}`, isStatic: true })
	}
	static async exportDataCertificationBriefCustomize (data) {
		// if (process.env.CLIENT_ENV === 'trial') {
		// 	return {
		// 		error: {message: 'Hiện tại chức năng này chưa được mở ở phiên bản dùng thử'}
		// 	}
		// }
		const { fromDate, toDate, appraiser_perform_id, appraiser_id, customer_id, createdBy, status } = data
		return (new this()).request({ method: 'GET', url: `certification_brief/brief-customize-export?fromDate=${fromDate}&toDate=${toDate}&status=${status}&created_by=${createdBy}&appraiser_id=${appraiser_id}&appraiser_perform_id=${appraiser_perform_id}&customer_id=${customer_id}`, isStatic: true })
	}
	static async getHistoryTimeline (id) {
		return (new this()).request({ method: 'GET', url: `activity/get-certificate/${id}`, isStatic: true })
	}
	static async updateAppraiseCertificateVersion (id = '', data) {
		return (new this()).request({ method: 'POST', url: `certification_brief/certificate-update-appraise-version/${id}`, data: data, isStatic: true })
	}
	static async getAssetVersion (ids) {
		const data = '[' + ids + ']'
		return (new this()).request({ method: 'GET', url: `certification_brief/asset-version?ids=${data}`, isStatic: true })
	}
	static async getCertificateStatus (id) {
		return (new this()).request({ method: 'GET', url: `certification_brief/get-status/${id}`, isStatic: true })
	}

	static async updateSubStatusFromConfig (data) {
		return (new this()).request({ method: 'PUT', url: `config/update-certificate-sub-status`, data: data, isStatic: true })
	}
}
