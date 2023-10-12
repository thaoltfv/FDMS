import Model from './Model.js'
import { isEmpty } from 'lodash-es'

export default class WareHouse extends Model {
	buildUrl (request) {
		const { params } = request
		return ['asset-general', ...params]
	}
	static async getDictionaries () {
		// let resp = localStorage.getItem('dictionaries')
		// if (isEmpty(resp)) {
			let resp = await (new this()).request({ method: 'GET', url: `dictionaries`, isStatic: true })
			localStorage.setItem('dictionaries', JSON.stringify(resp))
		// } else resp = JSON.parse(resp)
		return resp
	}
	static async getHistoryTimeline (id) {
		return (new this()).request({ method: 'GET', url: `activity/get-compare/${id}`, isStatic: true })
	}
	static async getDictionariesLand () {
		return (new this()).request({ method: 'GET', url: `dictionary/LOAI_DAT_CHI_TIET`, isStatic: true })
	}
	static async getAssetGenerals () {
		return (new this()).request({ method: 'GET', url: `asset-generals`, isStatic: true })
	}
	static async getAssetGeneralDetail (id) {
		return (new this()).request({ method: 'GET', url: `asset-general/${id}`, isStatic: true })
	}
	static async getAssetGeneralDetailVersion (id, version) {
		return (new this()).request({ method: 'GET', url: `asset-general/${id}?version=${version}`, isStatic: true })
	}
	static async getVersion (id) {
		return (new this()).request({ method: 'GET', url: `asset/version/${id}`, isStatic: true })
	}
	static async getSearchAll (year, province, district, ward, street, transaction, total_area_from, total_area_to, total_amount_from, total_amount_to, distance, location, front_side, isAppraise, property_type) {
		return (new this()).request({ method: 'GET', url: `asset-generals/search?year=${year}&province_id=${province}&district_id=${district}&ward_id=${ward}&street_id=${street}&transaction_type=${transaction}&total_area_from=${total_area_from}&total_area_to=${total_area_to}&total_amount_from=${total_amount_from}&total_amount_to=${total_amount_to}&distance=${distance}&location=${location}&front_side=${front_side}&is_appraise=${isAppraise}&property_type=${property_type}`, isStatic: true })
	}
	static async getPrint (id, certificate_id = '') {
		return (new this()).request({ method: 'GET', url: `asset-generals/print/[${id}]?certificate_id=${certificate_id}`, isStatic: true })
	}
	static async getPrintPdf (id) {
		return (new this()).request({ method: 'GET', url: `asset-generals/print/[${id}]?format=pdf`, isStatic: true })
	}
	static async updateStatus (id, data) {
		return (new this()).request({ method: 'PUT', url: `asset-generals/update-status/${id}`, data: data, isStatic: true })
	}
	static async getProvince () {
		let resp = localStorage.getItem('provinces')
		if (isEmpty(resp)) {
			resp = await (new this()).request({ method: 'GET', url: `provinces`, isStatic: true })
			localStorage.setItem('provinces', JSON.stringify(resp))
		} else {
			resp = JSON.parse(resp)
		}
		return resp
	}
	static async getProvinceAll () {
		return (new this()).request({ method: 'GET', url: `province-all`, isStatic: true })
	}
	static async getDistrict (id) {
		return (new this()).request({ method: 'GET', url: `districts?province_id=${id}`, isStatic: true })
	}
	static async getWard (id) {
		return (new this()).request({ method: 'GET', url: `wards?district_id=${id}`, isStatic: true })
	}
	static async getStreet (id) {
		return (new this()).request({ method: 'GET', url: `streets?district_id=${id}`, isStatic: true })
	}
	static async getDistance (id) {
		return (new this()).request({ method: 'GET', url: `distances?street_id=${id}`, isStatic: true })
	}
	static async getPropertyType () {
		return (new this()).request({ method: 'GET', url: `dictionary/LOAI_TAI_SAN`, isStatic: true })
	}
	static async getInfoSource () {
		return (new this()).request({ method: 'GET', url: `dictionary/NGUON_THONG_TIN`, isStatic: true })
	}
	static async getTransactionType () {
		return (new this()).request({ method: 'GET', url: `dictionary/LOAI_GIAO_DICH`, isStatic: true })
	}
	static async getHousing () {
		return (new this()).request({ method: 'GET', url: `dictionary/CAP_NHA`, isStatic: true })
	}
	static async getLandType () {
		return (new this()).request({ method: 'GET', url: `dictionary/LOAI_DAT`, isStatic: true })
	}
	static async getHousingType () {
		return (new this()).request({ method: 'GET', url: `dictionary/LOAI_NHA`, isStatic: true })
	}
	static async getPropertyOther () {
		return (new this()).request({ method: 'GET', url: `dictionary/LOAI_TAI_SAN_KHAC`, isStatic: true })
	}
	static async getTypePurpose () {
		return (new this()).request({ method: 'GET', url: `dictionary/LOAI_DAT_CHI_TIET`, isStatic: true })
	}
	static async getJurlidical () {
		return (new this()).request({ method: 'GET', url: `dictionary/PHAP_LY`, isStatic: true })
	}
	static async getLandShape () {
		return (new this()).request({ method: 'GET', url: `dictionary/HINH_DANG_DAT`, isStatic: true })
	}
	static async getSocialSecurity () {
		return (new this()).request({ method: 'GET', url: `dictionary/AN_NINH_MOI_TRUONG_SONG`, isStatic: true })
	}
	static async getBusiness () {
		return (new this()).request({ method: 'GET', url: `dictionary/KINH_DOANH`, isStatic: true })
	}
	static async getPaymentMethod () {
		return (new this()).request({ method: 'GET', url: `dictionary/DIEU_KIEN_THANH_TOAN`, isStatic: true })
	}
	static async getTopographic () {
		return (new this()).request({ method: 'GET', url: `dictionary/DIA_HINH`, isStatic: true })
	}
	static async getConditions () {
		return (new this()).request({ method: 'GET', url: `dictionary/DIEU_KIEN_HA_TANG`, isStatic: true })
	}
	static async getFengShui () {
		return (new this()).request({ method: 'GET', url: `dictionary/PHONG_THUY`, isStatic: true })
	}
	static async getZoning () {
		return (new this()).request({ method: 'GET', url: `dictionary/QUY_HOACH_HIEN_TRANG`, isStatic: true })
	}
	static async getMaterial () {
		return (new this()).request({ method: 'GET', url: `dictionary/GIAO_THONG_CHAT_LIEU`, isStatic: true })
	}
	static async getRough () {
		return (new this()).request({ method: 'GET', url: `dictionary/GIAO_THONG`, isStatic: true })
	}
	static async getInterior () {
		return (new this()).request({ method: 'GET', url: `dictionary/CHAT_LUONG_NOI_THAT`, isStatic: true })
	}
	static async getCrane () {
		return (new this()).request({ method: 'GET', url: `dictionary/CAU_TRUC_NHA_XUONG`, isStatic: true })
	}
	static async getUser () {
		return (new this()).request({ method: 'GET', url: `users`, isStatic: true })
	}
	static async getImg (link) {
		return (new this()).request({ method: 'GET', url: `uploads/${link}`, isStatic: true })
	}
	static async getBuildingPrices (building_category, level, rate, structure, crane, aperture, factory_type) {
		return (new this()).request({ method: 'GET', url: `building-prices/average-building-price?building_category=${building_category}&level=${level}&rate=${rate}&structure=${structure}&crane=${crane}&aperture=${aperture}&factory_type=${factory_type}`, isStatic: true })
	}
	static async getApartment () {
		return (new this()).request({ method: 'GET', url: `apartments`, isStatic: true })
	}
	static async getBlock (id) {
		return (new this()).request({ method: 'GET', url: `block-lists?apartment_id=${id}`, isStatic: true })
	}
	static async getUnitPrice (province, district, ward, street, distance, land_type) {
		return (new this()).request({ method: 'GET', url: `unit-prices?province=${province}&district=${district}&ward=${ward}&street=${street}&distance=${distance}&land_type=${land_type}` })
	}
	static async getDistrictsByProvinceId (id) {
		let resp = localStorage.getItem('districts_' + id)
		if (!resp) {
			resp = await (new this()).request({ method: 'GET', url: `DistrictAll?province_id=${id}`, isStatic: true })
			localStorage.setItem('districts_' + id, JSON.stringify(resp))
		} else resp = JSON.parse(resp)
		return resp
		// return (new this()).request({ method: 'GET', url: `DistrictAll?province_id=${id}`, isStatic: true })
	}
	static async getProjects () {
		return (new this()).request({ method: 'GET', url: `projects/active`, isStatic: true })
	}
	static async getApartmentFloor (id) {
		return (new this()).request({ method: 'GET', url: `apartment-by-floor/${id}`, isStatic: true })
	}
	static async getAppraiseDetail (id, data) {
		return (new this()).request({ method: 'GET', url: `asset-generals/appraise-detail/${id}`, data: data, isStatic: true })
	}
	static async getApartmentDetail (id, data) {
		return (new this()).request({ method: 'GET', url: `asset-generals/apartment-detail/${id}`, data: data, isStatic: true })
	}
	static async getProjectsByDistrictId (id) {
		let resp = localStorage.getItem('projects_' + id)
		if (isEmpty(resp)) {
			resp = await (new this()).request({ method: 'GET', url: `projects/get-project-by-district?district_id=${id}`, isStatic: true })
			localStorage.setItem('projects_' + id, JSON.stringify(resp))
		} else {
			resp = JSON.parse(resp)
		}
		return resp
	}
	static async exportDataComparisionAsset (data) {
		const { fromDate, toDate, createdBy } = data
		return (new this()).request({ method: 'GET', url: `asset-generals/asset-export?fromDate=${fromDate}&toDate=${toDate}&created_by=${createdBy}`, isStatic: true })
	}
}
