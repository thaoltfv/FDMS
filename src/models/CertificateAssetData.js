import Model from './Model.js'

export default class CertificateAssetData extends Model {
	buildUrl (request) {
		const { params } = request
		return ['appraise', ...params]
	}
	static async deleteApartment (id) {
		return (new this()).request({ method: 'DELETE', url: `dictionary/${id}`, isStatic: true })
	}
	static async getAppraiseID (id, version) {
		return (new this()).request({ method: 'GET', url: `appraise/${id}`, isStatic: true })
	}
	static async getAppraiseAssetID (id, version) {
		return (new this()).request({ method: 'GET', url: `certificate-asset/${id}`, isStatic: true })
	}
	static async getDictionary (data) {
		return (new this()).request({ method: 'GET', url: `appraise/dictionary/${data}`, isStatic: true })
	}
	static async getAsset (data) {
		return (new this()).request({ method: 'POST', url: `certificate-asset/asset`, data, isStatic: true })
	}
	static async getSearchAll (distance, location, front_side, transaction, assetType) {
		return (new this()).request({ method: 'GET', url: `asset-generals/search?distance=${distance}&location=${location}&front_side=${front_side}&transaction_type=${transaction}&asset_type_ids=${assetType}`, isStatic: true })
	}
	static async getAppraisals (ids) {
		const data = '[' + ids + ']'
		return (new this()).request({ method: 'GET', url: `certificate-asset/ids/${data}`, isStatic: true })
	}
	static async getImage (data) {
		return (new this()).request({ method: 'POST', url: `certificate-asset/image`, data, isStatic: true })
	}
	static async updateStatusCertificate (id, status) {
		const dataSend = { status: status }
		// const data1 = { status }
		return (new this()).request({ method: 'POST', url: `certification_brief/certificate-update-status/${id}`, data: dataSend, isStatic: true })
	}
}
