import Model from './Model.js'

export default class Report extends Model {
	buildUrl(request) {
		const { params } = request
		return ['report', ...params]
	}

	static async getReportStatus(data) {
		const { fromDate, toDate } = data
		return (new this()).request({ method: 'GET', url: `report/certificate-brieft/doughnut-chart?fromDate=${fromDate}&toDate=${toDate}`, isStatic: true })
	}
	static async getReportExpired(data) {
		const { fromDate, toDate } = data
		return (new this()).request({ method: 'GET', url: `report/certificate-brieft/doughnut-chart-expired?fromDate=${fromDate}&toDate=${toDate}`, isStatic: true })
	}
	static async getReportCertificationBrief(data) {
		const { fromDate, toDate, status } = data
		return (new this()).request({ method: 'GET', url: `report/certificate-brieft/bar-chart-status-by-month?fromDate=${fromDate}&toDate=${toDate}?status=${status}`, isStatic: true })
	}
	static async getReportCertificationBacklog() {
		return (new this()).request({ method: 'GET', url: `report/certificate-brieft/doughnut-chart-backlog`, isStatic: true })
	}
	static async getReportProcessProgress() {
		return (new this()).request({ method: 'GET', url: `report/certificate-brieft/doughnut-chart-in-processing`, isStatic: true })
	}
	static async getReportProcessProgressPreCertificate(data) {
		const { fromDate, toDate } = data
		return (new this()).request({ method: 'GET', url: `report/certificate-brieft/doughnut-chart-in-processing-pre-certificate?fromDate=${fromDate}&toDate=${toDate}`, isStatic: true })
	}
	static async getReportProcessProgressCertificate(data) {
		const { fromDate, toDate } = data
		return (new this()).request({ method: 'GET', url: `report/certificate-brieft/doughnut-chart-in-processing-certificate?fromDate=${fromDate}&toDate=${toDate}`, isStatic: true })
	}
	static async getReportCertificationFinishByMonthCustomerGroup() {
		return (new this()).request({ method: 'GET', url: `report/certificate-brieft/bar-chart-finish-byMonth-customer`, isStatic: true })
	}

	static async getReportCertificationConversionRateByMonth() {
		return (new this()).request({ method: 'GET', url: `report/certificate-brieft/bar-chart-conversion-rate`, isStatic: true })
	}
	static async getReportCertificationFinishByQuarters() {
		return (new this()).request({ method: 'GET', url: `report/certificate-brieft/bar-chart-finish-byQuarters`, isStatic: true })
	}
	static async getReportCertificationCancelByQuarters() {
		return (new this()).request({ method: 'GET', url: `report/certificate-brieft/bar-chart-cancel-byQuarters`, isStatic: true })
	}
	static async getReportCertificationFinishByMonth() {
		return (new this()).request({ method: 'GET', url: `report/certificate-brieft/bar-chart-finish-byMonth`, isStatic: true })
	}
	static async getReportCertificationCancelByMonth() {
		return (new this()).request({ method: 'GET', url: `report/certificate-brieft/bar-chart-cancel-byMonth`, isStatic: true })
	}
	static async getReportBranchDept() {
		return (new this()).request({ method: 'GET', url: `report/certificate-brieft/doughnut-chart-branch-dept`, isStatic: true })
	}
	static async getReportBranchRevenue() {
		return (new this()).request({ method: 'GET', url: `report/certificate-brieft/doughnut-chart-branch-revenue`, isStatic: true })
	}
	static async getReportAssetCompare() {
		return (new this()).request({ method: 'GET', url: `report/get-count-compare-asset`, isStatic: true })
	}
	static async getReportAssetAppraiser() {
		return (new this()).request({ method: 'GET', url: `report/get-count-appraise-asset`, isStatic: true })
	}
}
