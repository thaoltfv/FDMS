//
import Model from './Model.js'

export default class CertificateAsset extends Model {
	buildUrl (request) {
		const { params } = request
		return ['appraise', ...params]
	}
	static async getStepAvailable (id = '') {
		return (new this()).request({ method: 'GET', url: `certification_asset/step/${id}`, isStatic: true })
	}
	static async getDataAllStep (id = '') {
		return (new this()).request({ method: 'GET', url: `certification_asset/appraise-all-step/${id}`, isStatic: true })
	}
	static async updateDistance (data, id = '') {
		return (new this()).request({ method: 'POST', url: `certification_asset/update-distance/${id}`, data: data, isStatic: true })
	}
	static async updateMucdichchinh (data, id = '') {
		return (new this()).request({ method: 'POST', url: `certification_asset/update-mucdichchinh/${id}`, data: data, isStatic: true })
	}
	static async updateNoteHienTrang (data, id = '') {
		return (new this()).request({ method: 'POST', url: `certification_asset/update-note-hientrang/${id}`, data: data, isStatic: true })
	}
	static async submitStep1 (data, id = '') {
		return (new this()).request({ method: 'POST', url: `certification_asset/step1-general-infomation/${id}`, data: data, isStatic: true })
	}
	static async submitStep2 (data, id = '') {
		return (new this()).request({ method: 'POST', url: `certification_asset/step2-land-infomation/${id}`, data: data, isStatic: true })
	}
	static async submitStep3 (data, id = '') {
		return (new this()).request({ method: 'POST', url: `certification_asset/step3-construction-infomation/${id}`, data: data, isStatic: true })
	}
	static async submitStep4 (data, id = '') {
		return (new this()).request({ method: 'POST', url: `certification_asset/step4-law-infomation/${id}`, data: data, isStatic: true })
	}
	static async submitStep5 (data, id = '') {
		return (new this()).request({ method: 'POST', url: `certification_asset/step5-appraisal-infomation/${id}`, data: data, isStatic: true })
	}
	static async getAssetAutomationStep6 (id = '') {
		return (new this()).request({ method: 'GET', url: `certification_asset/assets-automatic/${id}`, isStatic: true })
	}
	static async getDetailAssetStep6 (arrayId = [], version = 0) {
		const data = { assets: arrayId }
		return (new this()).request({ method: 'POST', url: `certification_asset/assets-version-by-id`, data: data, isStatic: true })
	}
	static async getSearchAllAsset (distance, location, front_side, transaction, assetType, bothSide, year) {
		return (new this()).request({ method: 'GET', url: `certification_asset/assets-search?distance=${distance}&location=${location}&front_side=${front_side}&transaction_type=${transaction}&asset_type_ids=${assetType}&is_check_frontside=${bothSide}&year=${year}`, isStatic: true })
	}
	static async submitStep6 (data, id = '') {
		return (new this()).request({ method: 'POST', url: `certification_asset/step6-assets-infomation/${id}`, data: data, isStatic: true })
	}
	static async getDataStep7 (id = '') {
		return (new this()).request({ method: 'GET', url: `certification_asset/appraise-infomation/${id}`, isStatic: true })
	}
	static async submitStep7 (data, id = '') {
		return (new this()).request({ method: 'POST', url: `certification_asset/step7-comparison-factor/${id}`, data: data, isStatic: true })
	}
	static async submitContructionStep7 (data, id = '') {
		return (new this()).request({ method: 'POST', url: `certification_asset/step7-construction-company/${id}`, data: data, isStatic: true })
	}
	static async submitOtherAssetStep7 (data, id = '') {
		return (new this()).request({ method: 'POST', url: `certification_asset/step7-other-asset/${id}`, data: data, isStatic: true })
	}
	static async postDataComparison (tangible_assets, idData, xac_dinh_clcl, xac_dinh_dgxd) {
		const data = {tangible_assets, xac_dinh_clcl, xac_dinh_dgxd}
		return (new this()).request({ method: 'POST', url: `certification_asset/step7-tangible-comparison/${idData}`, data: data, isStatic: true })
	}
	static async exportDataCertificationAsset (data) {
		// if (process.env.CLIENT_ENV === 'trial') {
		// 	return {
		// 		error: {message: 'Hiện tại chức năng này chưa được mở ở phiên bản dùng thử'}
		// 	}
		// }
		const { fromDate, toDate, createdBy, status } = data
		return (new this()).request({ method: 'GET', url: `certification_asset/appraise-export?fromDate=${fromDate}&toDate=${toDate}&status=${status}&created_by=${createdBy}`, isStatic: true })
	}
	// other purpose certificate asset
	static async getDataAllStepOther (id = '', asset_type_id) {
		return (new this()).request({ method: 'GET', url: `personal-property/getData?id=${id}&asset_type_id=${asset_type_id}`, isStatic: true })
	}
	static async getAllStepOther (id = '', asset_type_id) {
		return (new this()).request({ method: 'GET', url: `other-certificate/all-step/${id}`, isStatic: true })
	}
	static async submitOtherStep1 (id = '', data) {
		return (new this()).request({ method: 'POST', url: `other-certificate/step1/${id}`, data: data, isStatic: true })
	}
	static async submitOtherStep2 (id = '', data) {
		return (new this()).request({ method: 'POST', url: `other-certificate/step2/${id}`, data: data, isStatic: true })
	}
	static async submitOtherStep3 (id = '', data) {
		return (new this()).request({ method: 'POST', url: `other-certificate/step3/${id}`, data: data, isStatic: true })
	}
	static async submitOtherStep4 (id = '', data) {
		return (new this()).request({ method: 'POST', url: `other-certificate/step4/${id}`, data: data, isStatic: true })
	}
	// machine certificate asset
	static async getDataAllStepMachine (id = '', asset_type_id) {
		return (new this()).request({ method: 'GET', url: `personal-property/getData?id=${id}&asset_type_id=${asset_type_id}`, isStatic: true })
	}
	static async getAllStepMachine (id = '', asset_type_id) {
		return (new this()).request({ method: 'GET', url: `machine-certificate/all-step/${id}`, isStatic: true })
	}
	static async submitMachineStep1 (id = '', data) {
		return (new this()).request({ method: 'POST', url: `machine-certificate/step1/${id}`, data: data, isStatic: true })
	}
	static async submitMachineStep2 (id = '', data) {
		return (new this()).request({ method: 'POST', url: `machine-certificate/step2/${id}`, data: data, isStatic: true })
	}
	static async submitMachineStep3 (id = '', data) {
		return (new this()).request({ method: 'POST', url: `machine-certificate/step3/${id}`, data: data, isStatic: true })
	}
	static async submitMachineStep4 (id = '', data) {
		return (new this()).request({ method: 'POST', url: `machine-certificate/step4/${id}`, data: data, isStatic: true })
	}
	// vehical certificate asset
	static async getDataAllStepVehicle (id = '', asset_type_id) {
		return (new this()).request({ method: 'GET', url: `personal-property/getData?id=${id}&asset_type_id=${asset_type_id}`, isStatic: true })
	}
	static async getAllStepVehicle (id = '', asset_type_id) {
		return (new this()).request({ method: 'GET', url: `verhicle-certificate/all-step/${id}`, isStatic: true })
	}
	static async submitVehicleStep1 (id = '', data) {
		return (new this()).request({ method: 'POST', url: `verhicle-certificate/step1/${id}`, data: data, isStatic: true })
	}
	static async submitVehicleStep2 (id = '', data) {
		return (new this()).request({ method: 'POST', url: `verhicle-certificate/step2/${id}`, data: data, isStatic: true })
	}
	static async submitVehicleStep3 (id = '', data) {
		return (new this()).request({ method: 'POST', url: `verhicle-certificate/step3/${id}`, data: data, isStatic: true })
	}
	static async submitVehicleStep4 (id = '', data) {
		return (new this()).request({ method: 'POST', url: `verhicle-certificate/step4/${id}`, data: data, isStatic: true })
	}
	static async getHistoryTimeline (id) {
		return (new this()).request({ method: 'GET', url: `activity/get-appraise/${id}`, isStatic: true })
	}
	static async getHistoryTimelineApartment (id) {
		return (new this()).request({ method: 'GET', url: `activity/get-apartment-history/${id}`, isStatic: true })
	}
	// apartment certificate asset
	static async getAllDataApartment (id = '') {
		return (new this()).request({ method: 'GET', url: `apartment-asset/${id}`, isStatic: true })
	}
	static async getAllStepApartment (id = '') {
		return (new this()).request({ method: 'GET', url: `apartment-asset/all-step/${id}`, isStatic: true })
	}
	static async submitApartmentStep1 (data, id = '') {
		return (new this()).request({ method: 'POST', url: `apartment-asset/step1/${id}`, data: data, isStatic: true })
	}
	static async submitApartmentStep2 (data, id = '') {
		return (new this()).request({ method: 'POST', url: `apartment-asset/step2/${id}`, data: data, isStatic: true })
	}
	static async submitApartmentStep3 (data, id = '') {
		return (new this()).request({ method: 'POST', url: `apartment-asset/step3/${id}`, data: data, isStatic: true })
	}
	static async submitApartmentStep4 (data, id = '') {
		return (new this()).request({ method: 'POST', url: `apartment-asset/step4/${id}`, data: data, isStatic: true })
	}
	static async submitApartmentStep5 (data, id = '') {
		return (new this()).request({ method: 'POST', url: `apartment-asset/step5-comparison-factor/${id}`, data: data, isStatic: true })
	}
	static async submitOtherAssetStep5 (data, id = '') {
		return (new this()).request({ method: 'POST', url: `apartment-asset/step5-other-asset/${id}`, data: data, isStatic: true })
	}
	static async submitAdditionalAsset (data, id = '') {
		return (new this()).request({ method: 'POST', url: `real_estate/additional-data/${id}`, data: data, isStatic: true })
	}
	static async getAutomationApartment (id = '') {
		return (new this()).request({ method: 'GET', url: `apartment-asset/automatic-asset/${id}`, isStatic: true })
	}
	static async getAllAssetApartment (distance, location, transaction, assetType, year) {
		return (new this()).request({ method: 'GET', url: `certification_asset/assets-search?distance=${distance}&location=${location}&transaction_type=${transaction}&asset_type_ids=${assetType}&year=${year}`, isStatic: true })
	}
	static async getDetailAssetApartment (arrayId = [], version = 0) {
		const data = { assets: arrayId }
		return (new this()).request({ method: 'POST', url: `apartment-asset/apartment-version-by-id`, data: data, isStatic: true })
	}
	static async postDataSummarizeApartment (id, apartment_round_total) {
		const dataSend = { apartment_round_total: +apartment_round_total }
		return (new this()).request({ method: 'POST', url: `apartment-asset/step5-update-round-total/${id}`, data: dataSend, isStatic: true })
	}
	static async getActivityAppraise (id) {
		return (new this()).request({ method: 'GET', url: `activity/get-appraise/${id}`, isStatic: true })
	}
	static async getActivityMachine (id) {
		return (new this()).request({ method: 'GET', url: `activity/get-machine-certificate-asset/${id}`, isStatic: true })
	}
	static async getActivityVerhicle (id) {
		return (new this()).request({ method: 'GET', url: `activity/get-verhicle-certificate-asset/${id}`, isStatic: true })
	}
	static async getActivityOther (id) {
		return (new this()).request({ method: 'GET', url: `activity/get-other-certificate-asset/${id}`, isStatic: true })
	}
	static async postEstimateAssetPrice (id) {
		return (new this()).request({ method: 'POST', url: `certification_asset/estimate_asset_price/${id}`, isStatic: true })
	}
	static async getPrintPL1 (id) {
		return (new this()).request({ method: 'GET', url: `real_estate/printPL1/${id}`, isStatic: true })
	}
	static async getPrintPL2 (id) {
		return (new this()).request({ method: 'GET', url: `real_estate/printPL2/${id}`, isStatic: true })
	}
	static async getPrintPL3 (id) {
		return (new this()).request({ method: 'GET', url: `real_estate/printPL3/${id}`, isStatic: true })
	}
	static async getPrintTSS (ids, id) {
		return (new this()).request({ method: 'GET', url: `real_estate/printTSS/[${ids}]?real_estate_id=${id}`, isStatic: true })
	}
	static async getExportShinhan (id) {
		return (new this()).request({ method: 'GET', url: `real_estate/exportShinhan/${id}`, isStatic: true })
	}
	static async postEstimateAssetPriceApartment (id) {
		return (new this()).request({ method: 'POST', url: `apartment-asset/estimate_asset_price/${id}`, isStatic: true })
	}
}
