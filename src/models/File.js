import Model from "./Model.js";

export default class File extends Model {
	buildUrl(request) {
		const { params } = request;
		return ["files", ...params];
	}
	static async downloadFileCertificate(id) {
		return new this().makeRequest({
			method: "GET",
			url: `api/certificate/other-document/download/${id}`
		});
	}
	static async deleteFileCertificate(id) {
		return new this().makeRequest({
			method: "POST",
			url: `api/certificate/other-document/remove/${id}`
		});
	}
	static async uploadFileCertificate(data, id) {
		return new this().makeRequest({
			method: "POST",
			url: `/api/certificate/other-document/upload/${id}`,
			data
		});
	}
	static async saleUploadFileCertificate(data, id) {
		return new this().makeRequest({
			method: "POST",
			url: `/api/certificate/sale-document/upload/${id}`,
			data
		});
	}
	static async upload({ data }) {
		return new this().makeRequest({
			method: "POST",
			url: `/api/asset-generals/image`,
			data
		});
	}
	static async uploadCompany({ data }) {
		return new this().makeRequest({
			method: "POST",
			url: `/api/company-logo`,
			data
		});
	}
	static async uploadImageProfile({ data }) {
		return new this().makeRequest({
			method: "POST",
			url: `/api/local-image`,
			data
		});
	}
	static async uploadExcel({ data }) {
		return new this().makeRequest({
			method: "POST",
			url: `/api/store-users`,
			data
		});
	}
	static async uploadExcelApartment({ data }) {
		return new this().makeRequest({
			method: "POST",
			url: `/api/store-apartments`,
			data
		});
	}
	static async deleteImageAzure(fileId, pageId, type) {
		return new this().makeRequest({
			method: "DELETE",
			url: `/api/cms/files/${fileId}/${pageId}?type=${type}`,
			isStatic: true
		});
	}
	static async uploadDocument(data, id, type) {
		return new this().makeRequest({
			method: "POST",
			url: `/api/certification_brief/upload-document/${id}/${type}`,
			data
		});
	}
	static async getToken() {
		return new this().makeRequest({
			method: "POST",
			url: `/api/get-token`
		});
	}
	static async getInfoByCoord({ data }) {
		return new this().makeRequest({
			method: "POST",
			url: `/api/get-info-by-coord`,
			data
		});
	}
	static async getInfoByLand({ data }) {
		return new this().makeRequest({
			method: "POST",
			url: `/api/get-info-by-land`,
			data
		});
	}
	static async downloadFilePreCertificate(id) {
		return new this().makeRequest({
			method: "GET",
			url: `api/pre-certificate/other-document/download/${id}`
		});
	}
	static async deleteFilePreCertificate(id) {
		return new this().makeRequest({
			method: "POST",
			url: `api/pre-certificate/other-document/remove/${id}`
		});
	}
	static async uploadFilePreCertificate(data, id, typeDocument) {
		return new this().makeRequest({
			method: "POST",
			url: `/api/pre-certificate/other-document/upload/${id}/${typeDocument}`,
			data
		});
	}
}
