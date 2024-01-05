import { isEmpty } from "lodash-es";
import Model from "./Model.js";

export default class Certificate extends Model {
	buildUrl(request) {
		const { params } = request;
		return ["pre-certificates/pre-certificate-paging", ...params];
	}
	static async getDetailPreCertificate(id = "") {
		return new this().request({
			method: "GET",
			url: `pre-certificates/pre-certification-infomation/${id}`,
			isStatic: true
		});
	}
	static async createUpdatePreCertification(data, id = "") {
		return new this().request({
			method: "POST",
			url: `pre-certificates/pre-certification-brief/${id}`,
			data: data,
			isStatic: true
		});
	}
	static async getAppraiseLaws() {
		let resp = localStorage.getItem("appraise-laws");
		if (isEmpty(resp)) {
			resp = await new this().request({
				method: "GET",
				url: `appraise-laws`,
				isStatic: true
			});
			localStorage.setItem("appraise-laws", JSON.stringify(resp));
		} else resp = JSON.parse(resp);
		return resp;
	}
	static async getAppraisers() {
		// let resp = localStorage.getItem('appraisers')
		// if (isEmpty(resp)) {
		let resp = await new this().request({
			method: "GET",
			url: `appraisers`,
			isStatic: true
		});
		localStorage.setItem("appraisers", JSON.stringify(resp));
		// } else resp = JSON.parse(resp)
		return resp;
	}
	static async getAppraisersManager() {
		// return (new this()).request({ method: 'GET', url: `appraisers?search=150`, isStatic: true })
		// return (new this()).request({ method: 'GET', url: `appraisers?search=TONG-GIAM-DOC`, isStatic: true })
		return new this().request({
			method: "GET",
			url: `appraisers?is_legal_representative=1`,
			isStatic: true
		});
	}
	static async getAppraiseOthers() {
		let resp = localStorage.getItem("appraise-others");
		if (isEmpty(resp)) {
			resp = await new this().request({
				method: "GET",
				url: `appraise-others`,
				isStatic: true
			});
			localStorage.setItem("appraise-others", JSON.stringify(resp));
		} else resp = JSON.parse(resp);
		return resp;
	}
	static async getAppraiseConstructions() {
		let resp = localStorage.getItem("appraisal-constructions");
		if (isEmpty(resp)) {
			resp = await new this().request({
				method: "GET",
				url: `appraisal-constructions`,
				isStatic: true
			});
			localStorage.setItem("appraisal-constructions", JSON.stringify(resp));
		} else resp = JSON.parse(resp);
		return resp;
	}
	static async getPreCertificate() {
		return new this().request({
			method: "GET",
			url: `pre-certificates/`,
			isStatic: true
		});
	}

	static async getCustomer(search = "") {
		return new this().request({
			method: "GET",
			url: `customers?key=${search}`,
			isStatic: true
		});
	}
	static async downloadDocument(id) {
		return new this().request({
			method: "GET",
			url: `certification_brief/download-document/${id}`,
			isStatic: true
		});
	}
	static async deleteDocument(id) {
		return new this().request({
			method: "DELETE",
			url: `certification_brief/delete-document/${id}`,
			isStatic: true
		});
	}

	static async getListKanbanPreCertificate(search = "") {
		let searchInput = { search_input: search };
		return new this().request({
			method: "GET",
			url: `pre-certificates/pre-certificate-workflow`,
			query: searchInput,
			isStatic: true
		});
	}
	static async getListFilterKanbanPreCertificate(search) {
		return new this().request({
			method: "GET",
			url: `pre-certificates/pre-certificate-workflow`,
			query: {
				...search
			}
		});
	}

	static async getTimeStamp() {
		return new this().request({
			method: "GET",
			url: `certification_brief/processing-time`,
			isStatic: true
		});
	}
	static async updateStatusPreCertificate(id = "", data) {
		return new this().request({
			method: "POST",
			url: `pre-certificates/pre-certificate-update-status/${id}`,
			data: data,
			isStatic: true
		});
	}

	static async updateToOfficalPreCertificate(id = "", note = "") {
		return new this().request({
			method: "POST",
			url: `pre-certificates/pre-certificate-update-offical/${id}/${note}`,
			isStatic: true
		});
	}

	static async exportDataCertificationBrief(data) {
		// if (process.env.CLIENT_ENV === 'trial') {
		// 	return {
		// 		error: {message: 'Hiện tại chức năng này chưa được mở ở phiên bản dùng thử'}
		// 	}
		// }
		const {
			fromDate,
			toDate,
			appraiser_perform_id,
			appraiser_id,
			customer_id,
			createdBy,
			status
		} = data;
		return new this().request({
			method: "GET",
			url: `pre-certificates/brief-export?fromDate=${fromDate}&toDate=${toDate}&status=${status}&created_by=${createdBy}&appraiser_id=${appraiser_id}&appraiser_perform_id=${appraiser_perform_id}&customer_id=${customer_id}`,
			isStatic: true
		});
	}
}
