<template>
  <div>
    <ValidationObserver
      tag="form"
      ref="data"
      @submit.prevent="validateBeforeSubmit"
    >
      <div class="loading" :class="{ loading__true: isSubmit }">
        <a-spin />
      </div>
      <div style="margin-bottom: 80px">
        <AppraisalInformation
          ref="appraisalInformation"
          :data="form"
          :propertyTypes="propertyTypes"
          :topographic="topographic"
          :provinces="provinces"
          :districts="districts"
          :wards="wards"
          :streets="streets"
          :distances="distances"
          :full_address="full_address"
          :full_address_street="full_address_street"
          :appraisalPurposes="appraisalPurposes"
          @getDistrict="changeProvince"
          @getWardStreet="changeDistrict"
          @getWard="changeWard"
          @getDistance="changeStreet"
          @getDistanceName="changeDistance"
          @getAssetType="changeAssetType"
        />
        <LandProperty
          ref="appraisalLand"
          :key="landKey"
          :properties="form.properties"
          :coordinates="form.coordinates"
          :full_address="full_address"
          :unit_price="unit_price"
          :land_no="form.land_no"
          :doc_no="form.doc_no"
          :landType="landType"
          @compare_assets="getCompareAssets"
          @cancelProperty="handleCancelProperty"
        />
        <BuildingProperty
          :key="buildingKey"
          :tangible_assets="form.tangible_assets"
          :housingTypes="housingTypes"
          :buildingCategories="buildingCategories"
          :compare_assets="compare_assets"
          v-if="assetName === 'ĐẤT CÓ NHÀ'"
          @cancelBuilding="cancelBuilding"
          @saveBuilding="saveBuilding"
        />
        <OtherProperty
          :key="otherAssetKey"
          :other_assets="form.other_assets"
          />
        <PropertyLegal
          ref="propertyLegal"
          :key="legalKey"
          :data="form"
          :juridicals="juridicals"
          :type_purposes="type_purposes"
          :appraise_law="form.appraise_law"
          :full_address="full_address"
          :provinceName="provinceName"
          @handleSave="handleSaveAppraiseLaw"
        />
        <!--      <AppraisalPerson-->
        <!--        :data="form"-->
        <!--        :appraisersManager="appraisersManager"-->
        <!--        :appraisers="appraisers"/>-->
        <AppraiseInfo
          ref="appraiseInfo"
          :data="form"
          :appraisalFacility="appraisalFacility"
          :approach="approach"
          :methodsUsed="methodsUsed"
          :appraisalPrinciples="appraisalPrinciples"
          :unifyIndicativePrice="unifyIndicativePrice"
          :compositeLandRemaning="compositeLandRemaning"
          :planningViolationPrice="planningViolationPrice"
          @changePercentRemain="changePercentRemain"
          @changePercentZoning="changePercentZoning"
          @changeUnifyIndicativePrice="changeUnifyIndicativePrice"
          @changeCompositeLandRemaning="changeCompositeLandRemaning"
          @changePlanningViolationPrice="changePlanningViolationPrice"
        />
        <!--      <LegalBasis-->
        <!--        :expertises="expertises"-->
        <!--        :constructs="constructs"-->
        <!--        :lands="lands"-->
        <!--        :local="local"-->
        <!--        :appraise_documents_valuation="form.appraise_documents_valuation"-->
        <!--        :appraise_documents_land="form.appraise_documents_land"-->
        <!--        :appraise_documents_construction="form.appraise_documents_construction"-->
        <!--        :appraise_documents_local="form.appraise_documents_local"-->
        <!--        @expertise="getExpertise"-->
        <!--        @land="getLand"-->
        <!--        @construct="getConstruct"-->
        <!--        @local="getLocal"-->
        <!--      />-->
        <ConstructionUnit
          v-if="form.asset_type_id === 38"
          :constructions="constructions"
          :construction_company="form.construction_company"
          @construction="getConstruction"
        />

        <CurrentPicture
          ref="currentPicture"
          :imageDescriptions="imageDescriptions"
          :pic="form.pic"
        />
        <PropertySelection
          ref="propertySelection"
          :landTypePurposes="type_purposes"
          :propertyTypes="propertyTypes"
          :data="data.assets"
          :location="form.coordinates"
          :frontSide="form.properties.length > 0 ? form.properties[0].front_side : ''"
          :radius="distance"
          :formData="form"
          :imageMap="imageMap"
          @action="getAsset"
          @updateAsset="updateAsset"
          @updateCheckFrontSide="updateCheckFrontSide"
          @getRadius="getRadius"
          @savePropertyId="getDataID"
          @saveImageMap="saveMap"
        />

        <!-- <div id="document_appraisal">
        <DocumentCertificate
          v-if="isSave"
          ref="documentAppraisal"
          :idData="idData"
          :formData="form"
        />
      </div> -->
        <CompatatorSelectionCertificate
          v-if="data.assets && data.assets.length === 3"
          :comparison="comparison"
          :comparison_factor="comparison_edit"
          @saveComparison="getComparison"
        />
      </div>
      <div class="btn-footer d-md-flex d-block justify-content-end align-items-center">
        <div class="d-lg-flex d-block button-contain">
          <button v-if="isSave === false && (add || edit)" class="btn btn-white btn-orange text-nowrap" :class="{ 'btn-loading disabled': isSubmit }" type="submit">
            <img src="../../assets/icons/ic_save.svg" :class="{ 'd-none': isSubmit }" style="margin-right: 12px" alt="save"/>Xuất bảng tính
          </button>
          <button @click.prevent="handleOpenModalCancel" class="btn btn-white text-nowrap" :class="{ disabled: isSubmit }" >
            <img src="../../assets/icons/ic_destroy.svg" style="margin-right: 12px" alt="save" />Hủy
          </button>
        </div>
      </div>
    </ValidationObserver>
    <ModalCancel
      v-if="openModalCancel"
      @cancel="openModalCancel = false"
      @action="handleCancel"
    />
    <ModalNotification
      v-if="openNotification"
      v-bind:notification="message ? message : ''"
      @cancel="openNotification = false"
      @action="handleHome"
    />
  </div>
</template>

<script>
import AppraisalInformation from './components/AppraisalInformation'
import LandProperty from './components/LandProperty'
import BuildingProperty from './components/BuildingProperty'
import OtherProperty from './components/OtherProperty'
import PropertyLegal from './components/PropertyLegal'
import AppraisalPerson from './components/AppraisalPerson'
import AppraiseInfo from './components/AppraiseInfo'
import CurrentPicture from './components/CurrentPicture'
import LegalBasis from './components/LegalBasis'
import ConstructionUnit from './components/ConstructionUnit'
import PropertySelection from './components/PropertySelection'
import CompatatorSelectionCertificate from './components/CompatatorSelectionCertificate'
import ModalCancel from '@/components/Modal/ModalCancel'
import ModalNotification from '@/components/Modal/ModalNotification'
import DocumentCertificate from './components/DocumentCertificate'
import WareHouse from '@/models/WareHouse'
import Certificate from '@/models/Certificate'
import AppraiseData from '../../models/AppraiseData'
import { COMPARISON } from '@/enum/comparison-factor.enum'
// import { version } from 'process'

export default {
	name: 'Form',
	data () {
		return {
			idData: null,
			isSave: false,
			openNotification: false,
			isSubmit: false,
			openModalCancel: false,
			message: '',
			landKey: 'landKey',
			legalKey: 'legalKey',
			otherAssetKey: 'otherAssetKey',
			buildingKey: 'buildingKey',
			form: {
				status: '1',
				asset_type_id: '',
				province_id: '',
				district_id: '',
				ward_id: '',
				street_id: '',
				distance_id: '',
				topographic_id: '',
				land_no: '',
				doc_no: '',

				land_no_old: '',
				doc_no_old: '',
				coordinates: '',
				appraise_asset: '',
				appraise_basis_property_id: '',
				document_description:
          '- Giả thiết:\n- Giả thiết đặc biệt:',
				appraise_approach_id: '',
				appraise_method_used_id: '',
				appraise_principle_id: '',
				properties: [],
				tangible_assets: [],
				other_assets: [],
				appraise_law: [
					// {
					//   land_details: [
					//     {
					//       doc_no: '',
					//       land_no: ''
					//     }
					//   ]
					// }
				],
				pic: [],
				construction_company: [],
				created_by: '',
				asset_general: [],
				assets: [],
				comparison_factor: [],
				unify_indicative_price_slug: '',
				composite_land_remaning_slug: '',
				planning_violation_price_slug: '',
				composite_land_remaning_value: '',
				planning_violation_price_value: ''
			},
			comparison_edit: [],
			comparison: COMPARISON,
			housingTypes: [],
			buildingCategories: [],
			propertyTypes: [],
			topographic: [],
			provinces: [],
			districts: [],
			wards: [],
			streets: [],
			distances: [],
			juridicals: [],
			appraisersManager: [],
			appraisers: [],
			appraisalPurposes: [],
			appraisalFacility: [],
			approach: [],
			methodsUsed: [],
			appraisalPrinciples: [],
			type_purposes: [],
			expertises: [],
			constructs: [],
			lands: [],
			local: [],
			imageDescriptions: [],
			constructions: [],
			unifyIndicativePrice: [],
			compositeLandRemaning: [],
			planningViolationPrice: [],
			provinceName: null,
			districtName: null,
			wardName: null,
			streetName: null,
			assetName: null,
			full_address: null,
			unit_price: {
				province: null,
				district: null,
				ward: null,
				street: null,
				distance: null
			},
			compare_assets: [],
			landType: [],
			data: {
				assets: []
			},
			radius: 1,
			distance: 1000,
			property: null,
			imageMap: null,
			imageMapDetail: null,
			full_address_noStreet: '',
			full_address_street: '',
			view: false,
			add: false,
			edit: false,
			deleted: false,
			accept: false,
			current_create_by: ''
		}
	},
	components: {
		AppraisalInformation,
		LandProperty,
		BuildingProperty,
		OtherProperty,
		PropertyLegal,
		AppraisalPerson,
		AppraiseInfo,
		CurrentPicture,
		LegalBasis,
		ModalCancel,
		ModalNotification,
		ConstructionUnit,
		PropertySelection,
		CompatatorSelectionCertificate,
		DocumentCertificate
	},
	async created () {
		this.isSubmit = true
		await this.getProfiles()
		const permission = this.$store.getters.currentPermissions
		// fix_permission
		await permission.forEach((value) => {
			if (value === 'VIEW_PRICE') {
				this.view = true
			}
			if (value === 'ADD_PRICE') {
				this.add = true
			}
			if (value === 'EDIT_PRICE') {
				this.edit = true
			}
			if (value === 'DELETE_PRICE') {
				this.deleted = true
			}
			if (value === 'ACCEPT_PRICE') {
				this.accept = true
			}
		})
		if ('id' in this.$route.query && this.$route.name === 'certificate.edit') {
			this.form = Object.assign(this.form, {
				...this.$route.meta['detail']
			})
			this.idData = this.form.id
			this.form.created_by = this.form.created_by.id
			if (this.form.appraise_law && this.form.appraise_law.length > 0) {
				this.form.appraise_law.forEach((item_law, index) => {
					if (!item_law.appraise_law_id) {
						this.form.appraise_law[index].appraise_law_id = 0
					}
				})
			}
			await this.getAppraiserId(this.form.id, this.form.version[this.form.version.length - 1].version)
			await this.$nextTick(() => {
				this.landKey = this.makeID(7)
				this.legalKey = this.makeID(7)
				this.buildingKey = this.makeID(7)
				this.otherAssetKey = this.makeID(7)
				this.$refs.appraisalInformation.getDataEdit()
				this.$refs.propertyLegal.getExpiryDate()
				this.$refs.propertyLegal.getDataUpdate()
			})
		} else if ('id' in this.$route.params && this.$route.name === 'certificate.create') {
			this.form = Object.assign(this.form, {
				...this.$route.meta['detail']
			})
			this.idData = this.form.id
			if (this.form.appraise_law && this.form.appraise_law.length > 0) {
				await this.form.appraise_law.forEach((item_law, index) => {
					if (!item_law.appraise_law_id) {
						this.form.appraise_law[index].appraise_law_id = 0
					}
				})
			}
			await this.getAppraiserId(this.form.id)
			await this.$nextTick(() => {
				this.landKey = this.makeID(7)
				this.legalKey = this.makeID(7)
				this.buildingKey = this.makeID(7)
				this.otherAssetKey = this.makeID(7)
				this.$refs.appraisalInformation.getDataEdit()
				this.$refs.propertyLegal.getExpiryDate()
				this.$refs.propertyLegal.getDataUpdate()
			})
		} else {}
		this.isSubmit = await false
	},
	methods: {
		makeID (length) {
			let result = ''
			let characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'
			let charactersLength = characters.length
			for (let i = 0; i < length; i++) {
				result += characters.charAt(Math.floor(Math.random() * charactersLength))
			}
			return result
		},
		handleSaveAppraiseLaw (data_law) {
			this.form.appraise_law = data_law
		},
		changeUnifyIndicativePrice (event) {},
		changeCompositeLandRemaning (event) {
			if (event === 'theo-ty-le-gia-dat-co-so-chinh') {
				this.form.composite_land_remaning_value = 80
			} else {
				this.form.composite_land_remaning_value = ''
			}
		},
		changePlanningViolationPrice (event) {
			if (event === 'theo-ty-le-gia-dat-thi-truong') {
				this.form.planning_violation_price_value = 50
			} else {
				this.form.planning_violation_price_value = ''
			}
		},
		changePercentRemain (event) {
			if (event) {
				this.form.composite_land_remaning_value = parseFloat(event).toFixed(0)
			} else {
				this.form.composite_land_remaning_value = ''
			}
		},
		changePercentZoning (event) {
			if (event) {
				this.form.planning_violation_price_value = parseFloat(event).toFixed(0)
			} else {
				this.form.planning_violation_price_value = ''
			}
		},
		saveBuilding (dataTangible) {
			this.form.tangible_assets = dataTangible
		},
		cancelBuilding (dataTangible) {
			this.form.tangible_assets = dataTangible
		},
		async getComparisonData () {
			if (this.$route.name === 'certificate.edit') {
				if (this.$route.query.id) {
					const resp = await Certificate.getDataComparison(this.$route.query.id)
					const dataCheck = await resp.data
					if (typeof dataCheck !== 'undefined') {
						this.comparison_edit = []
						resp.data[this.$route.query.id].forEach((item) => {
							this.comparison_edit.push({
								comparison_factor: item.label
							})
						})
					}
				}
			} else if (this.$route.name === 'certificate.create' && 'id' in this.$route.params) {
				if (this.$route.params.id) {
					const resp = await Certificate.getDataComparison(this.$route.params.id)
					const dataCheck = await resp.data
					if (typeof dataCheck !== 'undefined') {
						this.comparison_edit = []
						resp.data[this.$route.params.id].forEach((item) => {
							this.comparison_edit.push({
								comparison_factor: item.label
							})
						})
					}
				}
			} else if (this.$route.name === 'certificate.create') {
				this.comparison_edit = []
				this.comparison.forEach((item) => {
					this.comparison_edit.push({
						comparison_factor: item.name
					})
				})
			}
		},
		getComparison (event) {
			this.comparison_edit = []
			event.forEach((e) => {
				this.comparison_edit.push({
					comparison_factor: e.name
				})
			})
		},
		async getAppraiserId (id, version) {
			const res = await AppraiseData.getAppraiseID(id, version)
			if (typeof res.data !== 'undefined') {
				if ('id' in this.$route.params && this.$route.name === 'certificate.create') {
					this.form = res.data
					this.form.id = null
					this.form.status = 1
				} else this.form = res.data
				this.data.assets = this.form.asset_general
				this.form.assets = this.form.asset_general
				this.form.asset_general = []
				if (typeof this.form.appraise_has_assets !== 'undefined' && this.form.appraise_has_assets.length > 0) {
					this.form.appraise_has_assets.forEach((asset) => {
						this.form.asset_general.push({
							asset_general_id: asset.asset_general_id,
							asset_property_detail_id: asset.asset_property_detail_id,
							version: asset.version
						})
					})
				}
			}
		},
		async saveMap (image) {
			this.isSubmit = true
			const data = {
				data: image
			}
			const res = await AppraiseData.getImage(data)
			this.imageMap = res.data.link
			const item = {
				type_id: this.imageMapDetail.id,
				link: res.data.link
			}
			this.$toast.open({
				message: 'Lưu hình ảnh bản đồ thành công',
				type: 'success',
				position: 'top-right',
				duration: 3000
			})
			await this.getImageMap(item)
			this.isSubmit = false
		},
		getImageMap (item) {
			if (this.form.pic.length > 0) {
				const imageMap = this.form.pic.find(
					(image) => image.type_id === this.imageMapDetail.id
				)
				if (imageMap) {
					this.form.pic.forEach((image, index) => {
						if (image.type_id === this.imageMapDetail.id) {
							this.form.pic[index] = item
						}
					})
				} else {
					this.form.pic.push(item)
				}
			} else {
				this.form.pic.push(item)
			}
		},
		getImageUpdate () {
			this.form.pic.forEach((image) => {
				if (image.type_id === this.imageMapDetail.id) {
					this.imageMap = image.link
				}
			})
		},
		getRadius (data) {
			this.distance = data
		},
		async validateBeforeSubmit (event) {
			const isValid = await this.$refs.data.validate()
			if (isValid) {
				if (
					this.form.ticket_num < 0 ||
          this.form.land_no < 0 ||
          this.form.doc_no < 0 ||
          this.form.doc_no_old < 0 ||
          this.form.land_no_old < 0
				) {
					this.$toast.open({
						message: 'Vui lòng giá trị thích hợp trước khi lưu',
						type: 'error',
						position: 'top-right'
					})
					this.isSubmit = false
				} else if (
					typeof this.form.properties !== 'undefined' &&
          this.form.properties.length === 0
				) {
					this.$toast.open({
						message: 'Vui lòng nhập thông tin thửa đất',
						type: 'error',
						position: 'top-right'
					})
					this.isSubmit = false
				} else if (
					this.assetName === 'ĐẤT CÓ NHÀ' &&
          this.form.tangible_assets.length === 0
				) {
					this.$toast.open({
						message: 'Vui lòng nhập thông tin công trình xây dựng',
						type: 'error',
						position: 'top-right'
					})
					this.isSubmit = false
				} else if (typeof this.form.assets && this.form.assets.length > 0 && this.imageMap === null) {
					this.$toast.open({
						message: 'Vui lòng chụp ảnh bản đồ của tài sản thẩm định',
						type: 'error',
						position: 'top-right'
					})
					this.isSubmit = false
				} else if (+this.form.status !== 1 && this.form.assets && this.form.assets.length === 0) {
					this.$toast.open({
						message: 'Vui lòng Lựa chọn tài sản so sánh',
						type: 'error',
						position: 'top-right'
					})
					this.isSubmit = false
				} else if (this.form.status === 2 && this.form.appraise_law && this.form.appraise_law.length === 0) {
					this.$toast.open({
						message: 'Vui lòng nhập Pháp lý tài sản',
						type: 'error',
						position: 'top-right'
					})
					this.isSubmit = false
				} else if (this.form.assets && this.form.assets.length > 0 && this.form.assets.length < 3) {
					this.$toast.open({
						message: 'Vui lòng chọn đủ 3 Tài sản so sánh',
						type: 'error',
						position: 'top-right'
					})
					this.isSubmit = false
				} else if (
					this.form.composite_land_remaning_slug === 'theo-ty-le-gia-dat-co-so-chinh' && this.form.composite_land_remaning_value < 0
				) {
					this.$toast.open({
						message: 'Vui lòng kiểm tra lại tỷ lệ % đất hỗn hợp còn lại',
						type: 'error',
						position: 'top-right'
					})
					this.isSubmit = false
				} else if (
					this.form.planning_violation_price_slug === 'theo-ty-le-gia-dat-thi-truong' &&
          (this.form.planning_violation_price_value < 0 || this.form.planning_violation_price_value > 100)
				) {
					this.$toast.open({
						message: 'Vui lòng kiểm tra lại tỷ lệ % đất vi phạm quy hoạch',
						type: 'error',
						position: 'top-right'
					})
					this.isSubmit = false
				} else {
					this.isSubmit = true
					await this.handleSubmit(event)
				}
			} else {
				this.isSubmit = false
				this.$toast.open({
					message: 'Vui lòng nhập đầy đủ các trường bắt buộc',
					type: 'error',
					position: 'top-right'
				})
			}
		},
		handleSubmit () {
			this.isSubmit = true
			let data = this.form
			if ('id' in this.$route.params && this.$route.name === 'certificate.create') {
				data.created_by = this.current_create_by
			}
			data.comparison_factor = this.comparison_edit
			if (this.$route.name === 'certificate.edit') {
				this.updateDictionary(data)
			} else {
				this.createDictionary(data)
			}
		},
		async createDictionary (data) {
			data.status = 1
			try {
				const resp = await AppraiseData.create(data)
				if (resp) {
					if (resp.data) {
						this.isSubmit = false
						this.message =
              'Tạo mới mã thẩm định giá ' +
              '<b>' +
              'TSTD_' +
              resp.data.id +
              '</b>' +
              ' thành công.'
						this.isSave = true
						this.idData = resp.data.id
						this.form = resp.data
						this.isSave = true
						this.openNotification = true
						// this.$nextTick(() => {
						//   window.location = '#document_appraisal'
						//   this.$refs.documentAppraisal.getDataAppraises(resp.data.id)
						// })
					} else if (resp.error) {
						this.$toast.open({
							message: resp.error.message,
							type: 'error',
							position: 'top-right',
							duration: 3000
						})
						this.isSubmit = false
					}
				}
			} catch (err) {
				this.isSubmit = false
				if (err.response.data.data.errors.document_num) {
					this.$toast.open({
						message: err.response.data.data.errors.document_num[0],
						type: 'error',
						position: 'top-right',
						duration: 3000
					})
				} else if (err.response.data.data.errors.certificate_num) {
					this.$toast.open({
						message: err.response.data.data.errors.certificate_num[0],
						type: 'error',
						position: 'top-right',
						duration: 3000
					})
				}
			}
		},
		async updateDictionary (data) {
			try {
				const resp = new AppraiseData(data)
				await resp.save()
				if (resp.data) {
					this.isSubmit = false
					this.message =
            'Chỉnh sửa mã thẩm định giá ' +
            '<b>' +
            'TSTD_' +
            resp.data.id +
            '</b>' +
            ' thành công.'
					this.isSave = true
					this.openNotification = true
					this.form = resp.data
				} else if (resp.error) {
					this.$toast.open({
						message: resp.error.message,
						type: 'error',
						position: 'top-right',
						duration: 3000
					})
					this.isSubmit = false
				}
			} catch (err) {
				this.isSubmit = false
				if (err.response.data.data.errors.document_num) {
					this.$toast.open({
						message: err.response.data.data.errors.document_num[0],
						type: 'error',
						position: 'top-right',
						duration: 3000
					})
				} else if (err.response.data.data.errors.certificate_num) {
					this.$toast.open({
						message: err.response.data.data.errors.certificate_num[0],
						type: 'error',
						position: 'top-right',
						duration: 3000
					})
				}
			}
		},
		async getProfiles () {
			const profile = this.$store.getters.profile
			this.form.created_by = profile.data.user.id
			this.current_create_by = profile.data.user.id
		},
		async handleHome () {
			if (this.$route.name === 'certificate.create') {
				const id = this.idData
				this.$router
					.push({ name: 'certificate.detail', query: { id: id } })
					.catch((_) => {})
			} else if (this.$route.name === 'certificate.edit') {
				this.$router.go(-1)
			}
		},
		handleOpenModalCancel () {
			this.openModalCancel = true
		},
		async handleCancel () {
			this.isSubmit = true
			if (this.$route.name === 'certificate.create') {
				return this.$router.push({ name: 'certificate.index' })
			} else if (this.$route.name === 'certificate.edit') {
				this.$router.go(-1)
			}
		},
		async getProvinces (isBindData) {
			try {
				const resp = await WareHouse.getProvince()
				this.provinces = [...resp.data]
				if (
					this.form.province_id !== '' &&
          this.form.province_id !== undefined &&
          this.form.province_id !== null
				) {
					await this.getDistrictsByProvinceId(this.form.province_id)
					if (this.$route.name === 'certificate.edit' || (this.$route.name === 'certificate.create' && 'id' in this.$route.params)) {
						this.findProvince(isBindData)
					}
				}
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},
		async getDistrictsByProvinceId (id) {
			try {
				const resp = await WareHouse.getDistrict(id)
				this.districts = [...resp.data]
				if (
					this.form.district_id !== 0 &&
          this.form.district_id !== '' &&
          this.form.district_id !== undefined &&
          this.form.district_id !== null
				) {
					await this.getWardsByDistrictId(this.form.district_id)
					await this.getStreetByDistrictId(this.form.district_id)
				}
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},
		async getWardsByDistrictId (id) {
			try {
				const resp = await WareHouse.getWard(id)
				this.wards = [...resp.data]
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},
		async getStreetByDistrictId (id) {
			try {
				const resp = await WareHouse.getStreet(id)
				this.streets = [...resp.data]
				if (
					this.form.street_id !== '' &&
          this.form.street_id !== undefined &&
          this.form.street_id !== null
				) {
					await this.getDistanceByStreetId(this.form.street_id)
				}
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},
		async getDistanceByStreetId (id) {
			const resp = await WareHouse.getDistance(id)
			this.distances = [...resp.data]
		},
		async changeProvince (id) {
			this.form.district_id = ''
			this.form.ward_id = ''
			this.form.street_id = ''
			this.form.distance_id = ''
			this.districts = []
			this.wards = []
			this.streets = []
			this.distances = []
			if (
				this.form.province_id !== 0 &&
        this.form.province_id !== '' &&
        this.form.province_id !== undefined &&
        this.form.province_id !== null
			) {
				await this.getDistrictsByProvinceId(id)
			}
			this.findProvince()
		},
		findProvince (isBindData) {
			const province = this.provinces.find(
				(province) => province.id === this.form.province_id
			)
			if (province) {
				this.provinceName = province.name
				this.unit_price.province = province.name
			} else {
				this.provinceName = null
				this.unit_price.province = null
			}
			this.findDistrict(isBindData)
			this.getFullAddress(isBindData)
		},
		async changeDistrict (id) {
			this.wards = []
			this.streets = []
			this.distances = []
			this.form.ward_id = ''
			this.form.street_id = ''
			this.form.distance_id = ''
			if (
				this.form.district_id !== 0 &&
        this.form.district_id !== '' &&
        this.form.district_id !== undefined &&
        this.form.district_id !== null
			) {
				await this.getWardsByDistrictId(id)
				await this.getStreetByDistrictId(id)
			}
			this.findDistrict()
		},
		findDistrict (isBindData) {
			const district = this.districts.find(
				(district) => district.id === this.form.district_id
			)
			if (district) {
				this.districtName = district.name
				this.unit_price.district = district.name
			} else {
				this.districtName = null
				this.unit_price.district = null
			}
			if (
				this.districtName &&
        (this.districtName.toLowerCase() === 'thành phố biên hòa' ||
          this.districtName.toLowerCase() === 'thành phố long khánh')
			) {
				this.radius = 1
				this.distance = 1000
			} else {
				this.radius = 2
				this.distance = 2000
			}
			this.findWard(isBindData)
			this.findStreet(isBindData)
			this.getFullAddress(isBindData)
		},
		changeWard () {
			this.findWard()
		},
		findWard (isBindData) {
			const ward = this.wards.find((ward) => ward.id === this.form.ward_id)
			if (ward) {
				this.wardName = ward.name
				this.unit_price.ward = ward.name
			} else {
				this.wardName = null
				this.unit_price.ward = null
			}
			this.getFullAddress(isBindData)
		},
		async changeStreet (id) {
			this.distances = []
			this.form.distance_id = ''
			if (
				this.form.street_id !== 0 &&
        this.form.street_id !== '' &&
        this.form.street_id !== undefined &&
        this.form.street_id !== null
			) {
				await this.getDistanceByStreetId(id)
			}
			this.findStreet()
		},
		findStreet () {
			const street = this.streets.find((street) => street.id === this.form.street_id)
			if (street) {
				this.streetName = street.name
				this.unit_price.street = street.name
			} else {
				this.streetName = null
				this.unit_price.street = null
			}
			this.findDistance()
		},
		changeDistance () {
			this.findDistance()
		},
		findDistance () {
			const distance = this.distances.find((distances) => distances.id === this.form.distance_id)
			if (distance) {
				this.unit_price.distance = distance.name
			} else {
				this.unit_price.distance = null
			}
		},
		changeAssetType () {
			const assetType = this.propertyTypes.find((assetType) => assetType.id === this.form.asset_type_id)
			if (assetType) {
				this.assetName = assetType.description
			} else {
				this.assetName = null
			}
			this.checkAssetType()
			this.getInfo()
		},
		checkAssetType () {
			if (this.assetName !== 'ĐẤT CÓ NHÀ') {
				this.form.tangible_assets = []
			}
		},
		getFullAddress (isBindData) {
			this.full_address = `${this.wardName ? this.wardName + ', ' : ''}` + `${this.districtName ? this.districtName + ', ' : ''}` + `${this.provinceName ? this.provinceName.includes('Thành phố') ? this.provinceName : 'tỉnh ' + this.provinceName.trim() : ''}`
			this.full_address_street = `${this.streetName ? this.streetName + ', ' : ''}` + `${this.wardName ? this.wardName + ', ' : ''}` + `${this.districtName ? this.districtName + ', ' : ''}` + `${this.provinceName ? this.provinceName : ''}`
			if (!isBindData) {
				this.getInfo()
			}
		},
		getInfo () {
			if (this.assetName === 'ĐẤT TRỐNG' && this.full_address) {
				this.form.appraise_asset = `Quyền sử dụng đất tại ${this.full_address}`
			} else if (this.assetName === 'ĐẤT CÓ NHÀ' && this.full_address) {
				this.form.appraise_asset = `Quyền sử dụng đất và CTXD tại ${this.full_address}`
			}
			// this.form.appraise_asset = `${this.assetName === 'ĐẤT TRỐNG' ? 'Quyền sử dụng đất  ' : this.assetName === 'ĐẤT CÓ NHÀ' ? 'Quyền sử dụng đất và CTXD tại ' : ''}` `${this.full_address ? this.full_address : ''}`
		},
		async getDictionary () {
			let propertyTypeAll = []
			try {
				const resp = await WareHouse.getDictionaries()
				this.landType = await [...resp.data.loai_dat]
				this.topographic = await [...resp.data.dia_hinh]
				propertyTypeAll = await [...resp.data.loai_tai_san]
				this.landShapes = await [...resp.data.hinh_dang_dat]
				this.socialSecurities = await [...resp.data.an_ninh_moi_truong_song]
				this.businesses = await [...resp.data.kinh_doanh]
				this.paymentMethods = await [...resp.data.dieu_kien_thanh_toan]
				this.conditions = await [...resp.data.dieu_kien_ha_tang]
				this.fengshuies = await [...resp.data.phong_thuy]
				this.zones = await [...resp.data.quy_hoach_hien_trang]
				this.materials = await [...resp.data.giao_thong_chat_lieu]
				this.roughes = await [...resp.data.giao_thong]
				this.points = await [...resp.data.vi_tri_dat]
				this.housingTypes = await [...resp.data.loai_nha]
				this.buildingCategories = await [...resp.data.cap_nha]
				this.imageDescriptions = await [...resp.data.mo_ta_hinh_anh]
				await this.$refs.currentPicture.getImageDescriptions(this.imageDescriptions)
				await this.findImageMap(this.imageDescriptions)
				await propertyTypeAll.forEach((propertyType) => {
					if (propertyType.description !== 'CHUNG CƯ') {
						this.propertyTypes.push(propertyType)
					}
				})
				if (this.$route.name === 'certificate.edit' || ('id' in this.$route.params && this.$route.name === 'certificate.create')) {
					await this.changeAssetType()
				}
				if ((this.$route.name === 'certificate.edit' && this.form.properties.length > 0) || ('id' in this.$route.params && this.$route.name === 'certificate.create')) {
					await this.$nextTick(() => { this.$refs.appraisalLand.addLand() })
				}
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},
		findImageMap (data) {
			this.imageMapDetail = data.find((imageDescription) => imageDescription.description.toLowerCase() === 'hình bản đồ')
			if (this.$route.name === 'certificate.edit' || ('id' in this.$route.params && this.$route.name === 'certificate.create')) {
				this.getImageUpdate()
			}
		},
		async getDictionaryLand () {
			const resp = await WareHouse.getDictionariesLand()
			this.type_purposes = [...resp.data]
		},
		async getAppraiseLaws () {
			const resp = await Certificate.getAppraiseLaws()
			if (resp.data.phap_ly) {
				this.juridicals = [...resp.data.phap_ly]
				this.juridicals.push({
					content: 'Văn bản pháp lý khác',
					created_at: new Date(),
					date: '',
					deleted_at: null,
					document_type: '',
					id: 0,
					is_defaults: false,
					provinces: 'Tất cả',
					type: 'PHAP_LY'
				})
			} else {
				this.juridicals = []
			}
			if (resp.data.tham_dinh_gia) {
				this.expertises = [...resp.data.tham_dinh_gia]
			} else {
				this.expertises = []
			}
			if (resp.data.xay_dung) {
				this.constructs = [...resp.data.xay_dung]
			} else {
				this.constructs = []
			}
			if (resp.data.dat_dai) {
				this.lands = [...resp.data.dat_dai]
			} else {
				this.lands = []
			}
			if (resp.data.dia_phuong) {
				this.local = [...resp.data.dia_phuong]
			} else {
				this.local = []
			}
			// this.getData()
		},
		async getAppraiseConstructions () {
			const resp = await Certificate.getAppraiseConstructions()
			this.constructions = await [...resp.data]
			await this.getConstructions()
		},
		getConstructions () {
			if (this.$route.name === 'certificate.create') {
				this.constructions.forEach((construction) => {
					if (
						construction.is_defaults &&
            this.form.construction_company.length < 3
					) {
						this.form.construction_company.push({
							construction_company_id: construction.id
						})
					}
				})
			}
		},
		getData () {
			if (this.$route.name === 'certificate.create') {
				this.expertises.forEach((expertise) => {
					if (expertise.is_defaults) {
						this.form.appraise_documents_valuation.push({
							appraise_law_id: expertise.id
						})
					}
				})
				this.lands.forEach((land) => {
					if (land.is_defaults) {
						this.form.appraise_documents_land.push({
							appraise_law_id: land.id
						})
					}
				})
				this.constructs.forEach((construct) => {
					if (construct.is_defaults) {
						this.form.appraise_documents_construction.push({
							appraise_law_id: construct.id
						})
					}
				})
				this.local.forEach((local) => {
					if (local.is_defaults) {
						this.form.appraise_documents_local.push({
							appraise_law_id: local.id
						})
					}
				})
			}
		},
		getExpertise (event) {
			this.form.appraise_documents_valuation = []
			event.forEach((e) => {
				this.form.appraise_documents_valuation.push({
					appraise_law_id: e
				})
			})
		},
		getLand (event) {
			this.form.appraise_documents_land = []
			event.forEach((e) => {
				this.form.appraise_documents_land.push({
					appraise_law_id: e
				})
			})
		},
		getConstruct (event) {
			this.form.appraise_documents_construction = []
			event.forEach((e) => {
				this.form.appraise_documents_construction.push({
					appraise_law_id: e
				})
			})
		},
		getLocal (event) {
			this.form.appraise_documents_local = []
			event.forEach((e) => {
				this.form.appraise_documents_local.push({
					appraise_law_id: e
				})
			})
		},
		getConstruction (event) {
			this.form.construction_company = []
			event.forEach((e) => {
				this.form.construction_company.push({
					construction_company_id: e
				})
			})
		},
		// async getAppraisersManager () {
		//   const resp = await Certificate.getAppraisersManager()
		//   this.appraisersManager = [...resp.data]
		//   this.form.appraiser_manager_id = this.appraisersManager[0].id
		// },
		async getAppraisers () {
			const resp = await Certificate.getAppraisers()
			this.appraisers = await [...resp.data]
		},
		async getAppraiseOthers () {
			const resp = await Certificate.getAppraiseOthers()
			this.appraisalPurposes = await [...resp.data.muc_dich_tham_dinh_gia]
			this.appraisalFacility = await [...resp.data.co_so_tham_dinh]
			this.appraisalPrinciples = await [...resp.data.nguyen_tac_tham_dinh]
			this.approach = await [...resp.data.cach_tiep_can_chi_phi]
			this.methodsUsed = await [...resp.data.phuong_phap_tham_dinh_su_dung]
			this.unifyIndicativePrice = await [
				...resp.data.thong_nhat_muc_gia_chi_dan
			]
			this.compositeLandRemaning = await [
				...resp.data.tinh_gia_dat_hon_hop_con_lai
			]
			this.planningViolationPrice = await [
				...resp.data.tinh_gia_dat_vi_pham_quy_hoach
			]
			if (this.$route.name === 'certificate.create' && !('id' in this.$route.params)) {
				await this.findAppraisalPurposes()
				await this.findAppraisalFacility()
				await this.findAppraisalPrinciples()
				await this.findApproach()
				await this.findMethodsUsed()
				await this.findUnifyIndicativePrice()
				await this.findCompositeLandRemaning()
				await this.findPlanningViolationPrice()
			}
		},
		findUnifyIndicativePrice () {
			this.form.unify_indicative_price_slug = this.unifyIndicativePrice.find((item) => item.is_defaults === true).slug
		},
		findCompositeLandRemaning () {
			this.form.composite_land_remaning_slug = this.compositeLandRemaning.find((item) => item.is_defaults === true).slug
		},
		findPlanningViolationPrice () {
			this.form.planning_violation_price_slug = this.planningViolationPrice.find((item) => item.is_defaults === true).slug
		},
		findAppraisalPurposes () {
			let tmp = this.appraisalPurposes.find((appraisalPurpose) => appraisalPurpose.is_defaults === true)
			this.form.appraise_purpose_id = typeof tmp !== 'undefined' ? tmp.id : 0
		},
		findAppraisalFacility () {
			this.form.appraise_basis_property_id = this.appraisalFacility.find((appraisalFacility) => appraisalFacility.is_defaults === true).id
		},
		findAppraisalPrinciples () {
			this.form.appraise_principle_id = this.appraisalPrinciples.find((appraisalPrinciple) => appraisalPrinciple.is_defaults === true).id
		},
		findApproach () {
			this.form.appraise_approach_id = this.approach.find((approach) => approach.is_defaults === true).id
		},
		findMethodsUsed () {
			this.form.appraise_method_used_id = this.methodsUsed.find((methods) => methods.is_defaults === true).id
		},
		getCompareAssets (data, dataProperty) {
			this.compare_assets = data
			this.form.properties = dataProperty
		},
		handleCancelProperty (dataProperty) {
			this.form.properties = dataProperty
		},
		async getAsset () {
			let unrecognized = []
			// set image default
			this.imageMap = null
			if (this.form.properties.length > 0) {
				this.form.properties[0].property_detail.forEach((property) => {
					unrecognized.push({
						land_type_purpose: property.land_type_purpose_id,
						area: property.total_area
					})
				})
			}
			let body = {
				province_id: this.form.province_id,
				district_id: this.form.district_id,
				ward_id: this.form.ward_id,
				street_id: this.form.street_id,
				location: this.form.coordinates,
				front_side: this.form.properties.length > 0 ? this.form.properties[0].front_side : '',
				main_road_length: this.form.properties.length > 0 && this.form.properties[0].property_detail.length > 0 ? this.form.properties[0].property_detail[this.form.properties[0].property_detail.length - 1].main_road_length : '',
				distance: this.radius,
				unrecognized: unrecognized
			}
			if (
				this.form.province_id &&
        this.form.province_id !== '' &&
        this.form.district_id &&
        this.form.district_id !== '' &&
        this.form.ward_id &&
        this.form.ward_id !== '' &&
        this.form.street_id &&
        this.form.street_id !== '' &&
        this.form.coordinates &&
        this.form.coordinates !== '' &&
        this.form.properties.length > 0
			) {
				const res = await AppraiseData.getAsset(body)
				if (res && res.data) {
					this.data = res.data
					this.form.assets = this.data ? this.data.assets : []
					await this.getProperties()
					if (res.data && res.data.assets.length === 0) {
						this.$toast.open({
							message:
                'Hiện không có TSSS nào thỏa điều kiện quét. Vui lòng chọn thủ công bằng nút Chỉnh Sửa.',
							type: 'error',
							position: 'top-right'
						})
					}
				} else if (res.error) {
					this.$toast.open({
						message: res.error.message,
						type: 'error',
						position: 'top-right',
						duration: 3000
					})
				}
			} else {
				this.data = {
					assets: []
				}
				this.form.assets = []
				this.$toast.open({
					message: 'Vui lòng kiểm tra lại dữ liệu đầu vào!',
					type: 'error',
					position: 'top-right'
				})
			}
		},
		// get data TSSS khi bấm cập nhập
		async getProperties () {
			this.form.asset_general = []
			if (this.data) {
				for (const asset of this.data.assets) {
					await this.getAssetGeneralDetail(asset)
				}
			}
		},
		async getAssetGeneralDetail (asset) {
			this.isSubmit = true
			const resp = await WareHouse.getAssetGeneralDetail(asset.id)
			this.property = resp.data
			await this.getPropertyId(asset.id, asset.version)
			this.isSubmit = false
		},
		getPropertyId (id, version) {
			this.form.asset_general.push({
				asset_general_id: id,
				asset_property_detail_id: this.property.properties[0] ? this.property.properties[0].id : 0,
				version: version
			})
		},
		getDataID (id, indexProperty, asset_id, property_data) {
			let arrayVersion = []
			let maxVersion = 1
			if (property_data) {
				property_data.version.forEach(item => {
					arrayVersion.push(item.version)
				})
				maxVersion = Math.max(...arrayVersion)
			}
			this.form.asset_general.forEach((item, index) => {
				if (item.asset_general_id === asset_id) {
					this.form.asset_general[index].asset_property_detail_id = id
					this.form.asset_general[index].version = property_data.version.length > 0 ? maxVersion : 1
				}
			})
		},
		async updateAsset (data) {
			this.data.assets = data
			this.form.assets = data
			this.imageMap = null
			await this.getProperties()
		},
		async updateCheckFrontSide (data) {
			this.data.is_check_frontside = data
			this.form.is_check_frontside = data
		}
	},

	async beforeMount () {
		let isBindData = true
		this.getComparisonData()
		this.getDictionary()
		this.getDictionaryLand()
		this.getProvinces(isBindData)
		this.getAppraiseLaws()
		this.getAppraiseConstructions()
		// this.getAppraisersManager()
		this.getAppraisers()
		this.getAppraiseOthers()
		// if ('id' in this.$route.query && this.$route.name === 'certificate.edit') {
		//   if (this.$route.params && this.$route.params.is_edit_asset) {
		//     await this.$refs.propertySelection.handelEditProperty()
		//   }
		// }
	}
}
</script>

<style scoped lang="scss">
.loading {
  display: none;
  &__true {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 100vh;
    background: rgba(0, 0, 0, 0.62);
    z-index: 100000;
    display: flex;
    align-items: center;
    justify-content: center;
    &.btn-loading {
      &:after {
        width: 2rem !important;
        height: 2rem !important;
      }
    }
  }
}
</style>
