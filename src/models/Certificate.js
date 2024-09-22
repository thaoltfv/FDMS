import { isEmpty } from "lodash-es";
import Model from "./Model.js";

export default class Certificate extends Model {
	buildUrl(request) {
		const { params } = request;
		return ["certificate", ...params];
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
	static async getCertificate(id) {
		return new this().request({
			method: "GET",
			url: `certificate/${id}`,
			isStatic: true
		});
	}
	static async getPrint(id) {
		return new this().request({
			method: "GET",
			url: `certificate-asset/print/phu-luc-1/${id}`,
			isStatic: true
		});
	}
	static async getPrintAppendix(id) {
		return new this().request({
			method: "GET",
			url: `certificate-asset/print/phu-luc-2/${id}`,
			isStatic: true
		});
	}
	static async getPrintImage(id) {
		return new this().request({
			method: "GET",
			url: `certificate-asset/print/phu-luc-hinh-anh/${id}`,
			isStatic: true
		});
	}
	static async getPrintReport(id) {
		return new this().request({
			method: "GET",
			url: `certificate-asset/print/bao-cao/${id}`,
			isStatic: true
		});
	}
	static async getPrintProof(id) {
		return new this().request({
			method: "GET",
			url: `certificate-asset/print/chung-thu/${id}`,
			isStatic: true
		});
	}
	static async getPrintGYC(id, is_pc = 0) {
		return new this().request({
			method: "GET",
			url: `certificate-asset/print/giay-yeu-cau/${id}/${is_pc}`,
			isStatic: true
		});
	}
	static async getPrintKHTDG(id, is_pc = 0) {
		return new this().request({
			method: "GET",
			url: `certificate-asset/print/ke-hoach-tdg/${id}/${is_pc}`,
			isStatic: true
		});
	}
	static async getPrintTBGTDB(id, is_pc = 0) {
		return new this().request({
			method: "GET",
			url: `certificate-asset/print/tb-gia-thiet-dac-biet/${id}/${is_pc}`,
			isStatic: true
		});
	}
	static async getPrintTBHCLT(id, is_pc = 0) {
		return new this().request({
			method: "GET",
			url: `certificate-asset/print/tb-han-che-loai-tru/${id}/${is_pc}`,
			isStatic: true
		});
	}
	static async getPrintBBTL(id, is_pc = 0) {
		return new this().request({
			method: "GET",
			url: `certificate-asset/print/bien-ban-thanh-ly/${id}/${is_pc}`,
			isStatic: true
		});
	}
	static async getPrintHDTDG(id, is_pc = 0) {
		return new this().request({
			method: "GET",
			url: `certificate-asset/print/hop-dong-tdg/${id}/${is_pc}`,
			isStatic: true
		});
	}
	static async deleteAfterDownload($name) {
		return new this().request({
			method: "GET",
			url: `certificate-asset/delete-file-after-download/${$name}`,
			isStatic: true
		});
	}
	static async downloadAllOfficial(id, type) {
		return new this().request({
			method: "GET",
			url: `certificate-asset/download-all-official/${id}/${type}`,
			isStatic: true
		});
	}
	static async downloadAllDocumentPreCertificate(id, type) {
		return new this().request({
			method: "GET",
			url: `certificate-asset/download-all-official-pre-certificate/${id}/Appendix`,
			isStatic: true
		});
	}
	static async convertAutoDocumentToOfficial(id) {
		return new this().request({
			method: "POST",
			url: `certificate-asset/convert-auto-document-to-official/${id}`,
			isStatic: true
		});
	}
	static async convertAutoDocumentToOfficialFollowType(id, type) {
		return new this().request({
			method: "POST",
			url: `certificate-asset/convert-auto-document-to-official-type/${id}/${type}`,
			isStatic: true
		});
	}
	static async postData(payloadData) {
		return new this().request({
			method: "POST",
			url: `appraises/comparison-factor`,
			data: payloadData,
			isStatic: true
		});
	}
	static async postDataSummarizationReport(id, round_appraise_total) {
		const dataSend = {
			round_appraise_total: +round_appraise_total
		};
		return new this().request({
			method: "POST",
			url: `appraises/bao-cao/edit/${id}`,
			data: dataSend,
			isStatic: true
		});
	}
	static async postDataSummarize(id, round_appraise_total) {
		const dataSend = {
			round_appraise_total: +round_appraise_total
		};
		return new this().request({
			method: "POST",
			url: `certification_asset/step7-round-appraise-total/${id}`,
			data: dataSend,
			isStatic: true
		});
	}
	static async postDataCertificate(data, other_data, delete_data, price_UBND) {
		const comparison_factor = {
			comparison_factor: data,
			other_comparison: other_data,
			delete_other_comparison: delete_data,
			asset_unit_price: price_UBND
		};
		return new this().request({
			method: "POST",
			url: `certificate-asset/comparison-factor`,
			data: comparison_factor,
			isStatic: true
		});
	}
	// TSTD
	static async postDataComparison(data, dataConstruction) {
		const comparison_tangible_factor = {
			comparison_tangible_factor: data,
			construction_company: dataConstruction
		};
		return new this().request({
			method: "POST",
			url: `appraises/tangible-comparison`,
			data: comparison_tangible_factor,
			isStatic: true
		});
	}
	// HSTD
	static async postDataComparisonCert(data, dataConstruction) {
		const comparison_tangible_factor = {
			comparison_tangible_factor: data,
			construction_company: dataConstruction
		};
		return new this().request({
			method: "POST",
			url: `certificate-asset/tangible-comparison`,
			data: comparison_tangible_factor,
			isStatic: true
		});
	}
	static async getDataComparison(id) {
		return new this().request({
			method: "GET",
			url: `appraises/comparison-factor/${id}`,
			isStatic: true
		});
	}
	static async getDataComparisonCertificateAsset(id) {
		return new this().request({
			method: "GET",
			url: `certificate-asset/comparison-factor/${id}`,
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
}
