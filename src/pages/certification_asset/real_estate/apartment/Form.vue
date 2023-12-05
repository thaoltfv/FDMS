<template>
  <div v-if="!isMobile()" class="certification-asset">
    <form-wizard
      :key="key_render_formwizard"
      ref="wizard"
      color="#99D161"
      :title="`TSTD${idData ? `_${idData}` : '' }`"
      :subtitle="status_text"
      layout="vertical"
      finish-button-text="Hoàn Thành"
      back-button-text="Trở lại"
      next-button-text="Lưu"
      :startIndex="step_active || 0"
      @on-change="handleChange"
      class="vertical-steps steps-transparent"
    >
      <tab-content title="Thông tin tài sản" icon="" >
        <ValidationObserver
          tag="div"
          ref="step_1"
          @submit.prevent="validateSubmitStep1"
        >
          <Step1
			:isEdit="isEdit"
			:data="form.step_1"
			:key="key_step_1"
			:propertyTypes="propertyTypes"
			:provinces="provinces"
			:districts="districts"
			:wards="wards"
			:streets="streets"
			:full_address="full_address"
			:projects="projects"
			:blocks="blocks"
			:floors="floors"
			:apartments="apartments"
			:directions="directions"
			:furniture_list="furniture_list"
			:basic_utilities="basic_utilities"
			:imageDescriptions="imageDescriptions"
			@getDistrict="changeProvince"
			@getWardStreet="changeDistrict"
			@getWard="changeWard"
			@changeStreet="changeStreet"
			@changeDistance="changeDistance"
			@getAssetType="changeAssetType"
			@handleChangeProject="handleChangeProject"
			@handleChangeBlock="handleChangeBlock"
			@handleChangeFloor="handleChangeFloor"
          />
          <div class="btn-footer d-md-flex d-block justify-content-end align-items-center">
            <div class="d-lg-flex d-block button-contain">
              <button @click.prevent="handleChangeBack" class="btn btn-white text-nowrap" >
                <img src="@/assets/icons/ic_cancel.svg" style="margin-right: 12px" alt="save" />Trở lại
              </button>
              <button class="btn btn-white btn-orange text-nowrap" :class="{ 'btn_loading disabled': isSubmit }" @click.prevent="validateSubmitStep1" type="submit">
                <img src="@/assets/icons/ic_save.svg" style="margin-right: 12px" alt="save"/>Lưu
              </button>
							<button v-if="isCancelEnable" @click.prevent="handleCancelProperty()" class="btn btn-white text-nowrap">
								<img src="@/assets/icons/ic_destroy.svg" style="margin-right: 12px" alt="cancel">Hủy tài sản
							</button>
            </div>
          </div>
        </ValidationObserver>
      </tab-content>

      <tab-content title="Pháp lý tài sản" icon="">
        <ValidationObserver
          tag="form"
          ref="step_2"
					class="height_form_wizard"
          @submit.prevent="validateSubmitStep2"
        >
				<Step2
					:data="form.step_2"
					:key="key_step_2"
					:juridicals="juridicals"
					:provinceName="provinceName"
					:full_address="form.step_1.full_address"
					:apartment_name="getApartmentName"
					@updateLegal="updateLegal"
					@createLegal="createLegal"
					@deleteLegal="deleteLegal"
				/>
          <div class="btn-footer d-md-flex d-block justify-content-end align-items-center">
            <div class="d-lg-flex d-block button-contain">
              <button @click.prevent="handleChangeBack" class="btn btn-white text-nowrap" >
                <img src="@/assets/icons/ic_cancel.svg" style="margin-right: 12px" alt="save" />Trở lại
              </button>
              <button v-if="edit || add" class="btn btn-white btn-orange text-nowrap" :class="{ 'btn_loading disabled': isSubmit }" type="submit">
                <img src="@/assets/icons/ic_save.svg" style="margin-right: 12px" alt="save"/>Lưu
              </button>
							<button v-if="isCancelEnable" @click.prevent="handleCancelProperty()" class="btn btn-white text-nowrap">
								<img src="@/assets/icons/ic_destroy.svg" style="margin-right: 12px" alt="cancel">Hủy tài sản
							</button>
            </div>
          </div>
       </ValidationObserver>

      </tab-content>

      <tab-content title="Cơ sở thẩm định" icon="">
        <ValidationObserver
          tag="div"
          ref="step_3"
					class="height_form_wizard"
          @submit.prevent="validateSubmitStep3"
        >
					<Step3
						:data="form.step_3"
						:key="key_step_3"
						:appraisalFacility="appraisalFacility"
						:approach="approach"
						:methodsUsed="methodsUsed"
						:appraisalPrinciples="appraisalPrinciples"
						:unifyIndicativePrice="unifyIndicativePrice"
						:compositeLandRemaning="compositeLandRemaning"
						:planningViolationPrice="planningViolationPrice"
						@changeLandRemaing="changeLandRemaing"
						@changeViolationPrice="changeViolationPrice"
						@changePercentRemain="changePercentRemain"
						@changePercentVio="changePercentVio"
					/>
          <div class="btn-footer d-md-flex d-block justify-content-end align-items-center">
            <div class="d-lg-flex d-block button-contain">
              <button @click.prevent="handleChangeBack" class="btn btn-white text-nowrap" >
                <img src="@/assets/icons/ic_cancel.svg" style="margin-right: 12px" alt="save" />Trở lại
              </button>
               <button v-if="edit || add" class="btn btn-white btn-orange text-nowrap" :class="{ 'btn_loading disabled': isSubmit }" @click.prevent="validateSubmitStep3" type="submit">
                <img src="@/assets/icons/ic_save.svg" style="margin-right: 12px" alt="save"/>Lưu
              </button>
							<button v-if="isCancelEnable" @click.prevent="handleCancelProperty()" class="btn btn-white text-nowrap">
								<img src="@/assets/icons/ic_destroy.svg" style="margin-right: 12px" alt="cancel">Hủy tài sản
							</button>
            </div>
          </div>
        </ValidationObserver>
      </tab-content>

      <tab-content title="Tài sản so sánh" icon="" >
        <ValidationObserver
          tag="div"
          ref="step_4"
        >
					<Step4
						:data="form.step_4"
						:key="key_step_4"
						:step_active="step_active"
						:comparison="comparison"
						:propertyTypes="propertyTypes"
						:type_purposes="type_purposes"
						:coordinates="form.step_1.coordinates"
						:distance_max="distance_max"
						@choosingAsset="choosingAsset"
						@saveImageMap="saveImageMap"
						@changeDistance="changeDistances"
					/>
          <div class="btn-footer d-md-flex d-block justify-content-end align-items-center">
            <div class="d-lg-flex d-block button-contain">
              <button @click.prevent="handleChangeBack" class="btn btn-white text-nowrap" >
                <img src="@/assets/icons/ic_cancel.svg" style="margin-right: 12px" alt="save" />Trở lại
              </button>
              <button v-if="edit || add" class="btn btn-white btn-orange text-nowrap" :class="{ 'btn_loading disabled': isSubmit }" @click.prevent="validateSubmitStep4" type="submit">
                <img src="@/assets/icons/ic_save.svg" style="margin-right: 12px" alt="save"/>Lưu
              </button>
							<button v-if="isCancelEnable" @click.prevent="handleCancelProperty()" class="btn btn-white text-nowrap">
								<img src="@/assets/icons/ic_destroy.svg" style="margin-right: 12px" alt="cancel">Hủy tài sản
							</button>
            </div>
          </div>
        </ValidationObserver>
      </tab-content>

      <tab-content title="Giá trị tài sản" icon="">
      </tab-content>
    </form-wizard>
    <ModalNotificationAppraisal
      v-if="showConfirmEdit"
      @cancel="showConfirmEdit = false"
      v-bind:notification="messageConfirm"
      @action="confirmEditStep"
    />
		<ModalNotificationAppraisal
      v-if="showConfirmDuplicate"
      @cancel="showConfirmDuplicate = false"
      :notification="'Bạn có muốn nhân bản tài sản thẩm định không'"
      @action="actionDuplicate"
    />
		<ModalNotificationAppraisal
			v-if="openCancelAppraisal"
			@cancel="openCancelAppraisal = false"
			v-bind:notification="message"
			@action="handleActionCancelAppraise"
		/>
  </div>
  <div v-else class="certification-asset" style="margin-bottom: 140px;">

        <ValidationObserver
          tag="div"
          ref="step_1"
          @submit.prevent="validateSubmitStep1"
        >
          <Step1
			:isEdit="isEdit"
			:data="form.step_1"
			:key="key_step_1"
			:propertyTypes="propertyTypes"
			:provinces="provinces"
			:districts="districts"
			:wards="wards"
			:streets="streets"
			:full_address="full_address"
			:projects="projects"
			:blocks="blocks"
			:floors="floors"
			:apartments="apartments"
			:directions="directions"
			:furniture_list="furniture_list"
			:basic_utilities="basic_utilities"
			:imageDescriptions="imageDescriptions"
			@getDistrict="changeProvince"
			@getWardStreet="changeDistrict"
			@getWard="changeWard"
			@changeStreet="changeStreet"
			@changeDistance="changeDistance"
			@getAssetType="changeAssetType"
			@handleChangeProject="handleChangeProject"
			@handleChangeBlock="handleChangeBlock"
			@handleChangeFloor="handleChangeFloor"
          />
		  <div class="btn-footer d-md-flex d-block" style="bottom: 60px;padding-top: 0px;padding-bottom: 10px;">
				<div class="d-lg-flex d-block button-contain row" style="justify-content: space-around;display: flex!important;">
					<div class="col-6">
					<button @click.prevent="handleChangeBack" class="btn btn-white text-nowrap">
						<img src="@/assets/icons/ic_cancel.svg" style="margin-right: 12px" alt="save">
						Trở lại
					</button>
					</div>
					<div class="col-6">
					<button :class="{ 'btn_loading disabled': isSubmit }" class="btn btn-white btn-orange text-nowrap" @click.prevent="validateSubmitStep1" type="submit">
						<img src="@/assets/icons/ic_save.svg" style="margin-right: 12px" alt="save"/>Lưu
					</button>
					</div>
					<div class="col-12">
					<button v-if="isEdit && isCancelEnable" @click.prevent="handleCancelProperty()" class="btn btn-white text-nowrap">
						<img src="@/assets/icons/ic_destroy.svg" style="margin-right: 12px" alt="cancel">Hủy tài sản
					</button>
					</div>
				</div>
			</div>
        </ValidationObserver>
    <ModalNotificationAppraisal
      v-if="showConfirmEdit"
      @cancel="showConfirmEdit = false"
      v-bind:notification="messageConfirm"
      @action="confirmEditStep"
    />
		<ModalNotificationAppraisal
      v-if="showConfirmDuplicate"
      @cancel="showConfirmDuplicate = false"
      :notification="'Bạn có muốn nhân bản tài sản thẩm định không'"
      @action="actionDuplicate"
    />
		<ModalNotificationAppraisal
			v-if="openCancelAppraisal"
			@cancel="openCancelAppraisal = false"
			v-bind:notification="message"
			@action="handleActionCancelAppraise"
		/>
  </div>
</template>

<script>
import { FormWizard, TabContent } from 'vue-form-wizard'
import 'vue-form-wizard/dist/vue-form-wizard.min.css'
import {
	BCard,
	BRow,
	BCol,
	BFormGroup,
	BFormInput
} from 'bootstrap-vue'
import Step1 from './component/Step1'
import Step2 from './component/Step2'
import Step3 from './component/Step3'
import Step4 from './component/Step4'
// import Step7 from './component/Step7'
import ModalNotificationAppraisal from '@/components/Modal/ModalNotificationAppraisal'
import WareHouse from '@/models/WareHouse'
import Certificate from '@/models/Certificate'
import { COMPARISON_APARTMENT } from '@/enum/comparison-factor-apartment.enum'
import AppraiseData from '@/models/AppraiseData'
import CertificateAsset from '@/models/CertificateAsset'
export default {
	name: 'Index',
	components: {
		BCard,
		FormWizard,
		TabContent,
		BRow,
		BCol,
		BFormGroup,
		BFormInput,
		Step1,
		Step2,
		Step3,
		Step4,
		ModalNotificationAppraisal
	},
	data () {
		return {
			isAutomation: false,
			isEdit: false,
			idData: null,
			isSave: false,
			openNotification: false,
			isSubmit: false,
			isCancelEnable: true,
			openCancelAppraisal: false,
			showConfirmEdit: false,
			showConfirmDuplicate: false,
			step_edit: '',
			message: '',
			messageConfirm: '',
			businesses: [],
			conditions: [],
			socialSecurities: [],
			fengshuies: [],
			zones: [],
			landShapes: [],
			points: [],
			key_step_1: 100000,
			key_step_2: 200,
			key_step_3: 3000,
			key_step_4: 10000,
			key_step_5: 40000,
			key_render_formwizard: 70000,
			step_active: null,
			distance_max: null,
			form: {
				step_1: {
					asset_type_id: '',
					province_id: '',
					district_id: '',
					ward_id: '',
					street_id: '',
					coordinates: '',
					appraise_asset: '',
					project: '',
					apartment_asset_properties: {
						handover_year: '',
						project_id: '',
						block_id: '',
						floor_id: '',
						apartment_id: '',
						legal_id: '',
						floor: '',
						block: '',
						area: '',
						bedroom_num: '',
						wc_num: '',
						description: '',
						direction_id: '',
						furniture_quality_id: '',
						utilities: [],
						apartment_name: ''
					},
					pic: [],
					full_address: '',
					real_estate: {
						planning_info: '',
						planning_source: '',
						contact_person: '',
						contact_phone: ''
					}
				},
				step_2: {
					law: []
				},
				step_3: {
					appraisal_methods: {
						thong_nhat_muc_gia_chi_dan: {
							slug_value: '',
							value: null
						},
						tinh_gia_dat_hon_hop_con_lai: {
							slug_value: '',
							value: null
						},
						tinh_gia_dat_vi_pham_quy_hoach: {
							slug_value: '',
							value: null
						}
					},
					value_base_and_approach: {
						description: '- Giả thiết:\n- Giả thiết đặc biệt:',
						approach_id: '',
						basis_property_id: '',
						principle_id: '',
						method_used_id: ''
					}
				},
				step_4: {
					comparison_factor: ['phap_ly'],
					assets_general: [],
					map_img: ''
				},
				status: 1,
				// coordinates: '',
				appraise_asset: '',
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
				created_by: '',
				assets_general: [],
				assets: [],
				comparison_factor: [],
				unify_indicative_price_slug: '',
				composite_land_remaning_slug: '',
				planning_violation_price_slug: '',
				composite_land_remaning_value: '',
				planning_violation_price_value: ''
			},
			comparison_edit: [],
			comparison: COMPARISON_APARTMENT,
			housingTypes: [],
			buildingRates: [],
			buildingCategories: [],
			buildingStructure: [],
			buildingAperture: [],
			buildingFactoryType: [],
			buildingCrane: [],
			propertyTypes: [],
			topographic: [],
			provinces: [],
			districts: [],
			wards: [],
			streets: [],
			distances: [],
			juridicals: [],
			materials: [],
			appraisersManager: [],
			appraisers: [],
			appraisalPurposes: [],
			appraisalFacility: [],
			approach: [],
			methodsUsed: [],
			appraisalPrinciples: [],
			type_purposes: [],
			imageDescriptions: [],
			constructions: [],
			unifyIndicativePrice: [],
			compositeLandRemaning: [],
			planningViolationPrice: [],
			projects: [],
			blocks: [],
			floors: [],
			apartments: [],
			basic_utilities: [],
			directions: [],
			furniture_list: [],
			provinceName: null,
			districtName: null,
			wardName: null,
			streetName: null,
			assetName: null,
			full_address: null,
			addressName: {
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
			current_create_by: '',
			status_text: '',
			sentRequest: false,
			isDuplicate: false
		}
	},
	computed: {
		getApartmentName () {
			const apartment_name = this.form.step_1.apartment_asset_properties.apartment_name ? 'Căn hộ số ' + this.form.step_1.apartment_asset_properties.apartment_name : ''
			const floor_name = this.form.step_1.apartment_asset_properties.floor.name ? ' tầng ' + this.form.step_1.apartment_asset_properties.floor.name : ''
			const block_name = this.form.step_1.apartment_asset_properties.block.name ? ' khu ' + this.form.step_1.apartment_asset_properties.block.name : ''
			const project_name = this.form.step_1.project.name ? ' chung cư ' + this.form.step_1.project.name : ''
			let apartmentName = apartment_name + floor_name + block_name + project_name
			return apartmentName
		}
	},

	async created () {
		await this.getProfiles()
		const permission = this.$store.getters.currentPermissions
		await WareHouse.getProvince()
			.then((resp) => {
				this.provinces = resp.data
			})
		// fix_permission
		await permission.forEach((value) => {
			if (value === 'VIEW_CERTIFICATE_ASSET') {
				this.view = true
			}
			if (value === 'ADD_CERTIFICATE_ASSET') {
				this.add = true
			}
			if (value === 'EDIT_CERTIFICATE_ASSET') {
				this.edit = true
			}
			if (value === 'DELETE_CERTIFICATE_ASSET') {
				this.deleted = true
			}
			if (value === 'ACCEPT_CERTIFICATE_ASSET') {
				this.accept = true
			}
		})
		if (this.$route.query.asset_type_id) {
			this.form.step_1.asset_type_id = +this.$route.query.asset_type_id
		}
		if ('id' in this.$route.query && this.$route.name === 'certification_asset.apartment.edit') {
			this.isEdit = true
			if (this.$route.meta['step']) {
				let bindDataStep = this.$route.meta['step']
				if (bindDataStep.certificate) { this.isCancelEnable = false }
				// step 1
				if (bindDataStep.apartment_asset_properties) { this.form.step_1.apartment_asset_properties = bindDataStep.apartment_asset_properties }
				if (bindDataStep.appraise_asset) { this.form.step_1.appraise_asset = bindDataStep.appraise_asset }
				if (bindDataStep.asset_type_id) { this.form.step_1.asset_type_id = bindDataStep.asset_type_id }
				if (bindDataStep.coordinates) { this.form.step_1.coordinates = bindDataStep.coordinates }
				if (bindDataStep.province_id) { this.form.step_1.province_id = bindDataStep.province_id }
				if (bindDataStep.district_id) { this.form.step_1.district_id = bindDataStep.district_id }
				if (bindDataStep.ward_id) { this.form.step_1.ward_id = bindDataStep.ward_id }
				if (bindDataStep.street_id) { this.form.step_1.street_id = bindDataStep.street_id }
				if (bindDataStep.project_id) { this.form.step_1.project_id = bindDataStep.project_id }
				if (bindDataStep.full_address) { this.form.step_1.full_address = bindDataStep.full_address }
				if (bindDataStep.project) { this.form.step_1.project = bindDataStep.project }
				if (bindDataStep.real_estate) { this.form.step_1.real_estate = bindDataStep.real_estate }

				// step 2
				if (bindDataStep.law && bindDataStep.law.length > 0) { this.form.step_2.law = bindDataStep.law }
				// step 3
				if (bindDataStep.appraisal_methods) { this.form.step_3.appraisal_methods = bindDataStep.appraisal_methods }
				if (bindDataStep.value_base_and_approach) { this.form.step_3.value_base_and_approach = bindDataStep.value_base_and_approach }
				// step 4
				if (bindDataStep.comparison_factors && bindDataStep.comparison_factors.length > 0) { this.form.step_4.comparison_factor = bindDataStep.comparison_factors }
				if (bindDataStep.assets_general && bindDataStep.assets_general.length > 0) { this.form.step_4.assets_general = bindDataStep.assets_general }
				if (bindDataStep.map_img) { this.form.step_4.map_img = bindDataStep.map_img }
				if (bindDataStep.distance_max) { this.distance_max = bindDataStep.distance_max }
				this.status_text = bindDataStep.status_text
				this.form.status = bindDataStep.status
				this.idData = await bindDataStep.id
				if (bindDataStep.step >= 4) {
					this.step_active = 3
				} else this.step_active = bindDataStep.step
				await this.$refs.wizard.tabs.forEach((tab, index) => {
					if (index <= this.step_active) {
						tab.checked = true
					}
				})
				if (this.step_active === 5) {
					await this.$refs.wizard.changeTab(0, 5)
				} else {
					await this.$refs.wizard.changeTab(0, this.step_active)
				}
			}
			if (this.step_active < 3 || (this.step_active === 3 && this.form.step_4.assets_general.length === 0)) {
				// Remove the automated selection of comparison asset.
				// this.isAutomation = true
			}
			// if (this.$route.meta['step7']) { this.form.step_7 = Object.assign(this.form.step_7, { ...this.$route.meta['step7'] }) }
			// if (this.form.step_7.construction_company && this.form.step_7.construction_company.length > 0) {
			// 	await this.form.step_7.tangible_assets[0].construction_company.forEach(item => {
			// 		this.form.construction_company_ids.push(item.construction_company_id)
			// 	})
			// }
			if (this.form.step_4.comparison_factor.length > 1) {
				this.comparison.forEach(item => {
					this.form.step_4.comparison_factor.forEach(itemFactor => {
						if (item.slug === itemFactor && item.visible === false) {
							item.visible = true
						}
					})
				})
			}
			this.key_step_1 += 1
			this.key_step_2 += 1
			this.key_step_3 += 1
			this.key_step_4 += 1
			this.key_step_5 += 1
			if (this.$route.params.step || this.$route.params.step === 0) {
				await this.$refs.wizard.changeTab(0, this.$route.params.step)
			}
		} else if ('id' in this.$route.params && this.$route.name === 'certification_asset.apartment.create') {
			if (this.$route.meta['step']) {
				let bindDataStep = this.$route.meta['step']
				this.isDuplicate = true
				// step 1
				if (bindDataStep.apartment_asset_properties) { this.form.step_1.apartment_asset_properties = bindDataStep.apartment_asset_properties }
				if (bindDataStep.appraise_asset) { this.form.step_1.appraise_asset = bindDataStep.appraise_asset }
				if (bindDataStep.asset_type_id) { this.form.step_1.asset_type_id = bindDataStep.asset_type_id }
				if (bindDataStep.coordinates) { this.form.step_1.coordinates = bindDataStep.coordinates }
				if (bindDataStep.province_id) { this.form.step_1.province_id = bindDataStep.province_id }
				if (bindDataStep.district_id) { this.form.step_1.district_id = bindDataStep.district_id }
				if (bindDataStep.ward_id) { this.form.step_1.ward_id = bindDataStep.ward_id }
				if (bindDataStep.street_id) { this.form.step_1.street_id = bindDataStep.street_id }
				if (bindDataStep.project_id) { this.form.step_1.project_id = bindDataStep.project_id }
				if (bindDataStep.full_address) { this.form.step_1.full_address = bindDataStep.full_address }
				// step 2
				if (bindDataStep.law && bindDataStep.law.length > 0) { this.form.step_2.law = bindDataStep.law }
				// step 3
				if (bindDataStep.appraisal_methods) { this.form.step_3.appraisal_methods = bindDataStep.appraisal_methods }
				if (bindDataStep.value_base_and_approach) { this.form.step_3.value_base_and_approach = bindDataStep.value_base_and_approach }
				// step 4
				if (bindDataStep.comparison_factors && bindDataStep.comparison_factors.length > 0) { this.form.step_4.comparison_factor = bindDataStep.comparison_factors }
				if (bindDataStep.assets_general && bindDataStep.assets_general.length > 0) { this.form.step_4.assets_general = bindDataStep.assets_general }
				if (bindDataStep.map_img) { this.form.step_4.map_img = bindDataStep.map_img }
				if (bindDataStep.distance_max) { this.distance_max = bindDataStep.distance_max }
				// step 5
			}
			this.status_text = 'Mới'
			this.status = 1
			this.key_step_1 += 1
			this.key_step_2 += 1
			this.key_step_3 += 1
			this.key_step_4 += 1
			this.key_step_5 += 1
		}

		this.getProvinces()
	},
	methods: {
		isMobile () {
			if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
				return true
			} else {
				return false
			}
		},
		async handleChange (prevIndex, nextIndex) {
			if (nextIndex === 3 && this.isAutomation) {
				const response = await CertificateAsset.getAutomationApartment(this.idData)
				if (response.data) {
					this.form.step_4.assets_general = await response.data.assets
					this.form.step_4.comparison_factor = await response.data.comparison_factors
					this.form.step_4.map_img = ''
					this.distance_max = response.data.distance_max
					if (response.data.assets.length === 0) {
						this.$toast.open({
							message: `${response.data.message}`,
							type: 'success',
							position: 'top-right',
							duration: 6000
						})
					} else {
						this.$toast.open({
							message: `Đã tìm được ${response.data.assets.length} tài sản so sánh`,
							type: 'success',
							position: 'top-right',
							duration: 6000
						})
					}
					this.isAutomation = false
				} else {
					this.$toast.open({
						message: 'Hệ thống hiện không lấy được tài sản so sánh, vui lòng liên hệ hỗ trợ',
						type: 'error',
						position: 'top-right',
						duration: 6000
					})
				}
			}
			let currentIndex = nextIndex + 1
			if (currentIndex === 1) { this.key_step_1 += 1 }
			if (currentIndex === 2) { this.key_step_2 += 1 }
			if (currentIndex === 4) { this.key_step_4 += 1 }
		},
		confirmSavePreviousStep (step_edit) {
			this.step_edit = step_edit
			if (this.step_active > step_edit) {
				this.showConfirmEdit = true
				this.messageConfirm = `Dữ liệu ở sau bước ${step_edit} sẽ phải xác nhận lại. Bạn vẫn muốn tiếp tục ?`
			} else {
				this.confirmEditStep()
			}
		},
		confirmEditStep () {
			let step = this.step_edit
			if (step === 1) {
				this.handleSubmitStep_1(this.form.step_1, this.idData)
			} else if (step === 2) {
				this.handleSubmitStep_2(this.form.step_2, this.idData)
			} else if (step === 3) {
				this.handleSubmitStep_3(this.form.step_3, this.idData)
			} else if (step === 4) {
				this.handleSubmitStep_4(this.form.step_4, this.idData)
			}
			if (step < 4) {
				// Remove the automated selection of comparison asset.
				// this.isAutomation = true
			}

			this.step_active = step
			this.showConfirmEdit = false
		},
		async handleChangeBack () {
			// this.$refs.wizard.prevTab()
			return this.$router.go(-1)
		},
		// ------------------------------------------ Ation of STEP 1------------------------------------------------------------//
		async validateSubmitStep1 () {
			const isValid = await this.$refs.step_1.validate()
			if (isValid) {
				if ('id' in this.$route.query && this.$route.name === 'certification_asset.apartment.edit') {
					this.confirmSavePreviousStep(1)
				} else if (this.$route.name === 'certification_asset.apartment.create') {
					if (this.isDuplicate) {
						this.showConfirmDuplicate = true
					} else {
						this.confirmSavePreviousStep(1)
					}
				}
			} else {
				this.$toast.open({
					message: 'Vui lòng nhập đầy đủ các trường bắt buộc',
					type: 'error',
					position: 'top-right'
				})
			}
		},
		actionDuplicate () {
			this.handleSubmitStep_1(this.form.step_1, this.idData ? this.idData : '')
			this.isDuplicate = false
		},
		async handleSubmitStep_1 (dataStep1) {
			if (this.isSubmit == true) {
				this.$toast.open({
					message: 'Hệ thống đang xử lý, vui lòng đợi trong giây lát.',
					type: 'warning',
					position: 'top-right'
				})
				return
			} else {
				this.isSubmit = true
			}
			let id = this.idData ? this.idData : ''
			const res = await CertificateAsset.submitApartmentStep1(dataStep1, id)
			if (res.data) {
				this.isSubmit = false
				this.idData = res.data.id
				// this.form.step_2.land_details.coordinates = res.data.general_infomation.coordinates
				this.$toast.open({
					message: 'Lưu thông tin chung thành công',
					type: 'success',
					position: 'top-right',
					duration: 3000
				})
				this.$refs.wizard.maxStep = 1
				this.$refs.wizard.tabs.forEach((tab, index) => {
					if (index > 1) {
						tab.checked = false
					}
				})
				this.key_step_2 += 1
				await this.$refs.wizard.nextTab()
				this.status_text = 'Mới'
			} else if (res.error) {
				this.isSubmit = false
				this.$toast.open({
					message: `${res.error.message}`,
					type: 'error',
					position: 'top-right'
				})
			} else {
				this.isSubmit = false
				this.$toast.open({
					message: 'Lưu thất bại',
					type: 'error',
					position: 'top-right'
				})
			}
		},
		addTurning () {
			this.form.step_1.traffic_infomation.property_turning_time.push({
				is_alley_with_connection: '',
				main_road_distance: '',
				main_road_length: '',
				material_id: '',
				turning: 'Hẻm số ' + (this.form.step_1.traffic_infomation.property_turning_time.length + 1)
			})
		},
		deleteTurning (index) {
			this.form.step_1.traffic_infomation.property_turning_time.splice(index, 1)
		},
		changeRoadDistance (value, index) {
			this.form.step_1.traffic_infomation.property_turning_time[index].main_road_distance = value
		},
		changeRoadAlley (value, index) {
			this.form.step_1.traffic_infomation.property_turning_time[index].main_road_length = value
		},
		changeDescriptionFrontSide (description) {
			this.form.step_1.traffic_infomation.description = description
		},
		uploadImage (image) {
			this.form.step_1.pic.push(image)
			this.key_step_1 += 1
		},
		// --------------------------------------------- Ation of STEP 2------------------------------------------------------------//
		createLegal (dataLegal) {
			this.form.step_2.law.push(dataLegal)
			this.$toast.open({
				message: 'Thêm thành công',
				type: 'success',
				position: 'top-right',
				duration: 3000
			})
		},
		deleteLegal (index) {
			this.form.step_2.law.splice(index, 1)
			this.$toast.open({
				message: 'Xóa thành công',
				type: 'success',
				position: 'top-right',
				duration: 3000
			})
		},
		updateLegal (dataEdit, index) {
			this.form.step_2.law[index] = dataEdit
			this.$toast.open({
				message: 'Cập nhật thành công',
				type: 'success',
				position: 'top-right',
				duration: 3000
			})
		},
		async validateSubmitStep2 () {
			const isValid = await this.$refs.step_2.validate()
			if (isValid) {
				if (this.form.step_2.law.length === 0) {
					// this.$toast.open({
					// 	message: 'Vui lòng thêm pháp lý cho tài sản ABAS',
					// 	type: 'error',
					// 	position: 'top-right'
					// })
					await this.findAppraisalFacility()
					await this.findAppraisalPrinciples()
					await this.findApproach()
					await this.findMethodsUsed()
					await this.$refs.wizard.nextTab()
				} else {
					this.confirmSavePreviousStep(2)
				}
			} else {
				this.$toast.open({
					message: 'Vui lòng nhập đầy đủ các trường bắt buộc',
					type: 'error',
					position: 'top-right'
				})
			}
		},
		async handleSubmitStep_2 (dataStep2) {
			if (this.isSubmit == true) {
				this.$toast.open({
					message: 'Hệ thống đang xử lý, vui lòng đợi trong giây lát.',
					type: 'warning',
					position: 'top-right'
				})
				return
			} else {
				this.isSubmit = true
			}
			const res = await CertificateAsset.submitApartmentStep2(dataStep2, this.idData)
			if (res.data) {
				this.isSubmit = false
				this.$toast.open({
					message: 'Lưu pháp lý tài sản thành công',
					type: 'success',
					position: 'top-right',
					duration: 3000
				})
				if (res.data.appraisal_methods && res.data.value_base_and_approach) {
					this.form.step_3.appraisal_methods = res.data.appraisal_methods
					this.form.step_3.value_base_and_approach = res.data.value_base_and_approach
				}
				if (!res.data.value_base_and_approach) {
					await this.findAppraisalFacility()
					await this.findAppraisalPrinciples()
					await this.findApproach()
					await this.findMethodsUsed()
				} else if (res.data.appraisal_methods) {
					this.form.step_3.appraisal_methods = res.data.appraisal_methods
				}
				if (res.data.appraisal_methods) {
					this.form.step_3.appraisal_methods = res.data.appraisal_methods
				}
				this.key_step_5 += 1
				this.$refs.wizard.maxStep = 4
				this.$refs.wizard.tabs.forEach((tab, index) => {
					if (index > 4) {
						tab.checked = false
					}
				})
				await this.$refs.wizard.nextTab()
				this.status_text = 'Mới'
			} else if (res.error) {
				this.isSubmit = false
				this.$toast.open({
					message: `${res.error.message}`,
					type: 'error',
					position: 'top-right'
				})
			} else {
				this.isSubmit = false
				this.$toast.open({
					message: 'Lưu thất bại',
					type: 'error',
					position: 'top-right'
				})
			}
		},

		// --------------------------------------------- Ation of STEP 3------------------------------------------------------------//
		changeLandRemaing (event) {
			if (event === 'theo-ty-le-gia-dat-co-so-chinh') {
				this.form.step_3.appraisal_methods.tinh_gia_dat_hon_hop_con_lai.value = 50
			} else {
				this.form.step_3.appraisal_methods.tinh_gia_dat_hon_hop_con_lai.value = ''
			}
			this.key_step_5 += 1
		},
		changeViolationPrice (event) {
			if (event === 'theo-ty-le-gia-dat-thi-truong') {
				this.form.step_3.appraisal_methods.tinh_gia_dat_vi_pham_quy_hoach.value = 80
			} else {
				this.form.step_3.appraisal_methods.tinh_gia_dat_vi_pham_quy_hoach.value = ''
			}
			this.key_step_5 += 1
		},
		changePercentRemain (event) {
			this.form.step_3.appraisal_methods.tinh_gia_dat_hon_hop_con_lai.value = event
		},
		changePercentVio (event) {
			this.form.step_3.appraisal_methods.tinh_gia_dat_vi_pham_quy_hoach.value = event
		},
		async validateSubmitStep3 () {
			const isValid = await this.$refs.step_3.validate()
			if (isValid) {
				this.confirmSavePreviousStep(3)
			} else {
				this.$toast.open({
					message: 'Vui lòng nhập đầy đủ các trường bắt buộc',
					type: 'error',
					position: 'top-right'
				})
			}
		},
		async handleSubmitStep_3 (dataStep3) {
			if (this.isSubmit == true) {
				this.$toast.open({
					message: 'Hệ thống đang xử lý, vui lòng đợi trong giây lát.',
					type: 'warning',
					position: 'top-right'
				})
				return
			} else {
				this.isSubmit = true
			}
			const res = await CertificateAsset.submitApartmentStep3(dataStep3, this.idData)
			if (res.data) {
				this.isSubmit = false
				this.$toast.open({
					message: 'Lưu cơ sở thẩm định thành công',
					type: 'success',
					position: 'top-right',
					duration: 3000
				})
				this.form.step_4.comparison_factor = res.data.comparison_factors
				this.form.step_4.assets_general = res.data.assets_general
				this.form.step_4.map_img = res.data.map_img
				this.$refs.wizard.maxStep = 3
				this.$refs.wizard.tabs.forEach((tab, index) => {
					if (index > 3) {
						tab.checked = false
					}
				})
				await this.$refs.wizard.nextTab()
				this.step_active = 3
				this.status_text = 'Mới'
			} else if (res.error) {
				this.isSubmit = false
				this.$toast.open({
					message: `${res.error.message}`,
					type: 'error',
					position: 'top-right'
				})
			} else {
				this.isSubmit = false
				this.$toast.open({
					message: 'Lưu thất bại',
					type: 'error',
					position: 'top-right'
				})
			}
		},
		// --------------------------------------------- Ation of STEP 4------------------------------------------------------------//
		choosingAsset (assets) {
			this.form.step_4.assets_general = assets
			this.form.step_4.map_img = ''
		},
		saveImageMap (link) {
			this.form.step_4.map_img = 'https://apod.nasa.gov/apod/image/1505/AuroraNorway_Richardsen_2330.jpg'
		},
		changeDistances (distance_max) {
			this.distance_max = distance_max
		},
		async validateSubmitStep4 () {
			const isValid = await this.$refs.step_4.validate()
			let step_4 = this.form.step_4
			if (isValid) {
				if (step_4.comparison_factor.length === 0) {
					this.$toast.open({
						message: 'Vui lòng nhập yếu tố so sánh',
						type: 'error',
						position: 'top-right'
					})
				} else if (step_4.assets_general.length === 0) {
					this.$toast.open({
						message: 'Vui lòng chọn tài sản so sánh',
						type: 'error',
						position: 'top-right'
					})
				} else if (step_4.assets_general.length > 3) {
					this.$toast.open({
						message: 'Chỉ được chọn tối đa 3 tài sản so sánh',
						type: 'error',
						position: 'top-right'
					})
				} else if (step_4.assets_general.length < 3) {
					this.$toast.open({
						message: 'Vui lòng chọn đủ 3 tài sản so sánh',
						type: 'error',
						position: 'top-right'
					})
				} else if (!step_4.map_img) {
					this.$toast.open({
						message: 'Vui lòng chụp hình ảnh',
						type: 'error',
						position: 'top-right'
					})
				} else {
					this.confirmSavePreviousStep(4)
				}
			} else {
				this.$toast.open({
					message: 'Vui lòng nhập đầy đủ các trường bắt buộc',
					type: 'error',
					position: 'top-right'
				})
			}
		},
		async handleSubmitStep_4 (dataStep4) {
			if (this.isSubmit == true) {
				this.$toast.open({
					message: 'Hệ thống đang xử lý, vui lòng đợi trong giây lát.',
					type: 'warning',
					position: 'top-right'
				})
				return
			} else {
				this.isSubmit = true
			}
			const res = await CertificateAsset.submitApartmentStep4(dataStep4, this.idData)
			if (res.data) {
				this.$toast.open({
					message: 'Lưu lựa chọn tài sản so sánh thành công',
					type: 'success',
					position: 'top-right',
					duration: 3000
				})
				this.distance_max = res.data.distance_max
				await this.$router.push({
					name: 'certification_asset.apartment.detail',
					query: { id: this.idData },
					params: { step: 4 }
				})
				// const getDataStep7 = await CertificateAsset.getDataStep7(this.idData)
				// if (getDataStep7.data) {
				// 	this.form.step_7 = await getDataStep7.data
				// 	if (this.form.step_7.construction_company && this.form.step_7.construction_company.length > 0) {
				// 		this.form.construction_company_ids = []
				// 		this.form.step_7.construction_company.forEach(item => {
				// 			this.form.construction_company_ids.push(item.construction_company_id)
				// 		})
				// 	}
				// 	this.$refs.wizard.maxStep = 6
				// 	this.$refs.wizard.tabs.forEach((tab, index) => {
				// 		if (index > 6) {
				// 			tab.checked = false
				// 		}
				// 	})
				// 	await this.$refs.wizard.nextTab()
				// 	this.key_step_7 += 1
				// 	this.status_text = 'Đang thực hiện'
				// } else {
				// 	await this.$toast.open({
				// 		message: 'lấy dữ liệu thất bại',
				// 		type: 'error',
				// 		position: 'top-right'
				// 	})
				// }
			} else if (res.error) {
				this.isSubmit = false
				this.$toast.open({
					message: `${res.error.message}`,
					type: 'error',
					position: 'top-right'
				})
			} else {
				this.isSubmit = false
				this.$toast.open({
					message: 'Lưu thất bại',
					type: 'error',
					position: 'top-right'
				})
			}
			this.isSubmit = false
		},

		// --------------------------------------------- Ation of STEP 7------------------------------------------------------------//
		async updateDataStep7 () {
			// const getDataStep7 = await CertificateAsset.getDataStep7(this.idData)
			// if (getDataStep7.data) {
			//   this.form.step_7 = getDataStep7.data
			// }
			// this.key_step_7 += 1
		},
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
		async getAppraiserId (id, version) {
			const res = await AppraiseData.getAppraiseID(id, version)
			if (typeof res.data !== 'undefined') {
				if ('id' in this.$route.params && this.$route.name === 'certificate.create') {
					this.form = res.data
					this.form.id = null
					this.form.status = 1
				} else this.form = res.data
				this.data.assets = this.form.assets_general
				this.form.assets = this.form.assets_general
				this.form.assets_general = []
				if (typeof this.form.appraise_has_assets !== 'undefined' && this.form.appraise_has_assets.length > 0) {
					this.form.appraise_has_assets.forEach((asset) => {
						this.form.assets_general.push({
							assets_general_id: asset.assets_general_id,
							asset_property_detail_id: asset.asset_property_detail_id,
							version: asset.version
						})
					})
				}
			}
		},
		async getProfiles () {
			const profile = this.$store.getters.profile
			this.current_create_by = profile.data.user.id
			this.checkRole = (profile.data.user.id === this.form.created_by) || ['ROOT_ADMIN', 'SUB_ADMIN'].includes(profile.data.user.roles[0].name)
		},

		async handleCancel () {
			if (this.$route.name === 'certificate.create') {
				return this.$router.push({ name: 'certificate.index' })
			} else if (this.$route.name === 'certificate.edit') {
				this.$router.go(-1)
			}
		},
		getAddressEdit () {
			const province = this.form.step_1.province
			const district = this.form.step_1.district
			const ward = this.form.step_1.ward
			const street = this.form.step_1.street
			const distance = this.form.step_1.distance
			if (province) {
				this.provinceName = province.name
				this.addressName.province = province.name
			} else {
				this.provinceName = null
				this.addressName.province = null
			}
			if (district) {
				this.districtName = district.name
				this.addressName.district = district.name
			} else {
				this.districtName = null
				this.addressName.district = null
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
			if (ward) {
				this.wardName = ward.name
				this.addressName.ward = ward.name
			} else {
				this.wardName = null
				this.addressName.ward = null
			}
			if (street) {
				this.streetName = street.name
				this.addressName.street = street.name
			} else {
				this.streetName = null
				this.addressName.street = null
			}
			if (distance) {
				this.addressName.distance = distance.name
			} else {
				this.addressName.distance = null
			}
			this.getFullAddress()
		},
		getProvinces () {
			if (this.form.step_1.province_id) {
				this.getDistrictsByProvinceId(this.form.step_1.province_id)
				if ((this.$route.name === 'certification_asset.apartment.edit') || ('id' in this.$route.params && this.$route.name === 'certification_asset.apartment.create')) {
					this.getAddressEdit()
				}
			}
		},
		async getDistrictsByProvinceId (id) {
			await WareHouse.getDistrictsByProvinceId(id)
				.then((resp) => {
					this.districts = resp.data
					if (this.form.step_1.district_id) {
						this.getProjectsByDistrictId(this.form.step_1.district_id)
						this.getWardsByDistrictId(this.form.step_1.district_id)
						this.getStreetByDistrictId(this.form.step_1.district_id)
					}
				})
				.catch((err) => {
					this.isSubmit = false
					throw err
				})
		},
		async getProjectsByDistrictId (id) {
			await WareHouse.getProjectsByDistrictId(id)
				.then((resp) => {
					this.projects = resp.data
				})
				.catch((err) => {
					throw err
				})
				.finally(() => {
					this.getProjects()
				})
		},
		getWardsByDistrictId (id) {
			let wards = this.districts.filter(item => item.id === id)
			this.wards = wards[0].wards
			this.wards.forEach(item => {
				item.name = this.formatCapitalize(item.name)
			})
		},
		getStreetByDistrictId (id) {
			let streets = this.districts.filter(item => item.id === id)
			this.streets = streets[0].streets
			this.streets.forEach(item => {
				item.name = this.formatCapitalize(item.name)
			})
			if (
				this.form.step_1.street_id !== '' &&
        this.form.step_1.street_id !== undefined &&
        this.form.step_1.street_id !== null
			) {
				this.getDistanceByStreetId(this.form.step_1.street_id)
			}
		},
		async getDistanceByStreetId (id) {
			let distances = this.streets.filter(item => item.id === id)
			this.distances = distances[0].distances
		},
		findProvince () {
			const province = this.provinces.find(
				(province) => province.id === this.form.step_1.province_id
			)
			if (province) {
				this.provinceName = province.name
				this.addressName.province = province.name
			} else {
				this.provinceName = null
				this.addressName.province = null
			}
			this.findDistrict()
			this.getFullAddress()
		},
		findDistrict () {
			const district = this.districts.find((district) => district.id === this.form.step_1.district_id)
			if (district) {
				this.districtName = district.name
				this.addressName.district = district.name
			} else {
				this.districtName = null
				this.addressName.district = null
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
			this.findWard()
			this.findStreet()
			this.getFullAddress()
		},
		findWard () {
			const ward = this.wards.find((ward) => ward.id === this.form.step_1.ward_id)
			if (ward) {
				this.wardName = ward.name
				this.addressName.ward = ward.name
			} else {
				this.wardName = null
				this.addressName.ward = null
			}
			this.getFullAddress()
		},
		findStreet () {
			const street = this.streets.find((street) => street.id === this.form.step_1.street_id)
			if (street) {
				this.streetName = street.name
				this.addressName.street = street.name
			} else {
				this.streetName = null
				this.addressName.street = null
			}
			this.findDistance()
			this.getFullAddress()
		},
		findDistance () {
			const distance = this.distances.find((distances) => distances.id === this.form.step_1.distance_id)
			if (distance) {
				this.addressName.distance = distance.name
			} else {
				this.addressName.distance = null
			}
		},

		// change location
		changeProvince (id) {
			this.form.step_1.district_id = ''
			this.changeDistrict(this.form.step_1.district_id)
			// this.districts = []
			// this.wards = []
			// this.streets = []
			// this.distances = []
			// if (this.form.step_1.province_id !== 0) {
			// 	this.getDistrictsByProvinceId(id)
			// }
			if (id) {
				this.getDistrictsByProvinceId(id)
			}
			this.findProvince()
			this.getFullAddress()
		},

		changeDistrict (id) {
			this.wards = []
			this.streets = []
			this.distances = []
			this.form.step_1.ward_id = ''
			this.form.step_1.street_id = ''
			this.form.step_1.distance_id = ''
			this.form.step_1.project_id = ''
			this.form.step_1.apartment_asset_properties.block_id = ''
			this.form.step_1.apartment_asset_properties.floor_id = ''
			if (id) {
				this.getProjectsByDistrictId(id)
				this.getWardsByDistrictId(id)
				this.getStreetByDistrictId(id)
			}
			this.findDistrict()
		},
		changeWard () {
			this.findWard()
		},
		changeStreet (id) {
			this.distances = []
			this.form.step_1.distance_id = ''
			if (this.form.step_1.street_id) {
				this.getDistanceByStreetId(id)
			}
			this.findStreet()
		},
		changeDistance () {
			this.findDistance()
		},
		getFullAddress () {
			this.full_address = `${this.wardName ? this.wardName + ', ' : ''}` + `${this.districtName ? this.districtName + ', ' : ''}` + `${this.provinceName ? this.provinceName.includes('Thành phố') ? this.provinceName : 'tỉnh ' + this.provinceName.trim() : ''}`
			this.full_address_street = `${this.streetName ? this.streetName + ', ' : ''}` + `${this.wardName ? this.wardName + ', ' : ''}` + `${this.districtName ? this.districtName + ', ' : ''}` + `${this.provinceName ? this.provinceName : ''}`
			if (this.$route.name === 'certification_asset.apartment.create') {
				this.getInfo()
			}
		},
		changeAssetType (id) {
			const assetType = this.propertyTypes.find((assetType) => assetType.id === this.form.step_1.asset_type_id)
			if (assetType) {
				this.assetName = assetType.description
			} else {
				this.assetName = null
			}
			this.getInfo()
		},
		getInfo () {
			if (this.assetName === 'Đất trống' && this.full_address) {
				this.form.step_1.appraise_asset = `Quyền sử dụng đất tại ${this.full_address}`
			} else if (this.assetName === 'Đất có nhà' && this.full_address) {
				this.form.step_1.appraise_asset = `Quyền sử dụng đất và CTXD tại ${this.full_address}`
			}
		},
		formatSentenceCase (phrase) {
			let text = phrase.toLowerCase()
			return text.charAt(0).toUpperCase() + text.slice(1)
		},
		formatCapitalize (word) {
			return word.replace(/(?:^|\s|[-"'([{])+\S/g, function (x) { return x.toUpperCase() })
		},
		async getDictionary () {
			await WareHouse.getDictionaries()
				.then((resp) => {
					this.propertyTypes = resp.data.loai_tai_san.filter(item => item.acronym === 'CC')
					this.landType = resp.data.loai_dat
					this.topographic = resp.data.dia_hinh
					this.landShapes = resp.data.hinh_dang_dat
					this.socialSecurities = resp.data.an_ninh_moi_truong_song
					let basic_utilities = resp.data.tien_ich_co_ban
					this.basic_utilities = basic_utilities.filter(item => item.acronym !== null)
					this.directions = resp.data.huong_can_ho
					this.businesses = resp.data.kinh_doanh
					this.paymentMethods = resp.data.dieu_kien_thanh_toan
					this.conditions = resp.data.dieu_kien_ha_tang
					this.fengshuies = resp.data.phong_thuy
					this.zones = resp.data.quy_hoach_hien_trang
					this.materials = resp.data.giao_thong_chat_lieu
					this.roughes = resp.data.giao_thong
					this.points = resp.data.vi_tri_dat
					this.imageDescriptions = resp.data.mo_ta_hinh_anh
					// data for step 3
					this.buildingCategories = resp.data.cap_nha
					this.housingTypes = resp.data.loai_nha
					this.buildingRates = resp.data.hang_nha
					this.buildingStructure = resp.data.cau_truc_biet_thu
					this.buildingAperture = resp.data.khau_do
					this.buildingFactoryType = resp.data.loai_nha_may
					this.housingTypes.forEach(item => {
						item.description = this.formatSentenceCase(item.description)
					})
					this.directions.forEach(item => {
						item.description = this.formatSentenceCase(item.description)
					})
					this.materials.forEach(item => {
						item.description = this.formatSentenceCase(item.description)
					})
					this.landShapes.forEach(item => {
						item.description = this.formatSentenceCase(item.description)
					})
					this.propertyTypes.forEach(item => {
						item.description = this.formatSentenceCase(item.description)
					})
					// const res = await WareHouse.getInterior()
					this.furniture_list = resp.data.chat_luong_noi_that
					this.furniture_list.forEach(item => {
						item.description = this.formatSentenceCase(item.description)
					})
					this.key_step_1 += 1
				})
				.catch((err) => {
					this.isSubmit = false
					throw err
				})
		},
		async getAppraiseLaws () {
			await Certificate.getAppraiseLaws().then((resp) => {
				if (resp.data && resp.data.phap_ly) {
					this.juridicals = resp.data.phap_ly
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
			})
		},
		async getAppraiseOthers () {
			await Certificate.getAppraiseOthers()
				.then((resp) => {
					this.appraisalPurposes = resp.data.muc_dich_tham_dinh_gia.filter(item => item.dictionary_acronym.includes('BDS'))
					this.appraisalFacility = resp.data.co_so_tham_dinh.filter(item => item.dictionary_acronym.includes('BDS'))
					this.appraisalPrinciples = resp.data.nguyen_tac_tham_dinh.filter(item => item.dictionary_acronym.includes('BDS'))
					this.approach = resp.data.cach_tiep_can_chi_phi.filter(item => item.dictionary_acronym.includes('BDS'))
					this.methodsUsed = resp.data.phuong_phap_tham_dinh_su_dung.filter(item => item.dictionary_acronym.includes('BDS'))

					this.unifyIndicativePrice = resp.data.thong_nhat_muc_gia_chi_dan
					this.compositeLandRemaning = resp.data.tinh_gia_dat_hon_hop_con_lai
					this.planningViolationPrice = resp.data.tinh_gia_dat_vi_pham_quy_hoach
					this.planningViolationPrice = resp.data.tinh_gia_dat_vi_pham_quy_hoach
				})
		},
		findAppraisalFacility () {
			this.form.step_3.value_base_and_approach.basis_property_id = this.appraisalFacility.find((appraisalFacility) => appraisalFacility.is_defaults === true).id
		},
		findAppraisalPrinciples () {
			this.form.step_3.value_base_and_approach.principle_id = this.appraisalPrinciples.find((appraisalPrinciple) => appraisalPrinciple.is_defaults === true).id
		},
		findApproach () {
			this.form.step_3.value_base_and_approach.approach_id = this.approach.find((approach) => approach.is_defaults === true).id
		},
		findMethodsUsed () {
			this.form.step_3.value_base_and_approach.method_used_id = this.methodsUsed.find((methods) => methods.is_defaults === true).id
		},
		async getProjects () {
			if (this.form.step_1.project_id) {
				this.getBlocks(this.form.step_1.project_id)
			}
		},
		getBlocks (id) {
			let project = this.projects.filter(item => item.id === id)
			if (project && project.length > 0) { this.blocks = project[0].block }
			if (this.form.step_1.apartment_asset_properties.block_id) {
				this.getFloors(this.form.step_1.apartment_asset_properties.block_id)
			}
		},
		getFloors (id) {
			let block = this.blocks.filter(item => item.id === id)
			if (block && block.length > 0) { this.floors = block[0].floor }
			// if (this.form.step_1.apartment_asset_properties.floor_id) {
			// 	this.getApartments(this.form.step_1.apartment_asset_properties.floor_id)
			// }
		},
		async getApartments (id) {
			const res = await WareHouse.getApartmentFloor(id)
			if (res.data) {
				this.apartments = [...res.data]
			}
		},
		async handleChangeProject (projectId) {
			this.blocks = []
			this.floors = []
			this.apartments = []
			this.form.step_1.apartment_asset_properties.block_id = ''
			this.form.step_1.apartment_asset_properties.floor_id = ''
			// this.form.step_1.apartment_asset_properties.apartment_id = ''
			if (projectId) {
				let project = this.projects.filter(item => item.id === projectId)
				this.form.step_1.coordinates = project[0].coordinates
				if (project[0].utilities) {
					this.form.step_1.apartment_asset_properties.utilities = project[0].utilities
				}
				this.form.step_1.province_id = project[0].province_id
				this.form.step_1.district_id = project[0].district_id
				this.form.step_1.ward_id = project[0].ward_id
				this.form.step_1.street_id = project[0].street_id
				this.getProvinces()
				let provinceName = ''
				let districtName = ''
				let wardName = ''
				if (project[0].province) {
					provinceName = project[0].province.name
				}
				if (project[0].district) {
					districtName = project[0].district.name
				}
				if (project[0].ward) {
					wardName = project[0].ward.name
				}
				this.form.step_1.full_address = `${wardName}, ` + `${districtName}, ` + provinceName
				this.getBlocks(+projectId)
				this.key_step_1 += 1
			}
		},
		handleChangeBlock (blockId) {
			this.floors = []
			this.apartments = []
			this.form.step_1.apartment_asset_properties.floor_id = ''
			// this.form.step_1.apartment_asset_properties.apartment_id = ''
			if (blockId) {
				let block = this.blocks.filter(item => item.id === blockId)
				this.form.step_1.apartment_asset_properties.handover_year = block[0].handover_year
				this.getFloors(blockId)
			}
		},
		handleChangeFloor (floorId) {
			// this.apartments = []
			// this.form.step_1.apartment_asset_properties.apartment_id = ''
			// if (floorId) {
			// 	this.getApartments(floorId)
			// }
		},
		handleCancelProperty () {
			this.openCancelAppraisal = true
			this.message = 'Bạn có muốn hủy tài sản thẩm định này không ?'
		},

		// function hủy tài sản
		async handleActionCancelAppraise () {
			let status = 5
			const res = await AppraiseData.updateStatusRealestate(this.idData, status)
			if (res.data && res.data.status === 5) {
				await this.$toast.open({
					message: 'Hủy tài sản' + this.idData + ' thành công',
					type: 'success',
					position: 'top-right',
					duration: 3000
				})
				this.openNotification = await false
				await this.$router.push({name: 'certification_asset.index'}).catch(_ => {})
			} else if (res.error) {
				this.$toast.open({
					message: res.error.message,
					type: 'error',
					position: 'top-right',
					duration: 3000
				})
			}
		},
		changeAparment (event) {
			this.form.apartment_id = event
		}
	},
	async beforeMount () {
		this.getDictionary()
		this.getAppraiseLaws()
		this.getAppraiseOthers()
	},
	mounted () {
	}
}
</script>

<style scoped lang="scss">
.certification-asset {
  // padding-left: 16px;
  // padding-right: 16px;
  .step7 {
    /deep/ .wizard-tab-content {
      padding: 5px 5px 40px 0px !important;
    }
  }
  .stepTitle {
    font-size: 18px !important;
  }
}
.btn_loading {
    position: relative;
    color: white !important;
    text-shadow: none !important;
    pointer-events: none;
  }
  .btn_loading:after {
    content: '';
    display: inline-block;
    vertical-align: text-bottom;
    border: 1px solid wheat;
    border-right-color: transparent;
    border-radius: 50%;
    color: #ffffff;
    position: absolute;
    width: 1rem;
    height: 1rem;
    left: calc(50% - .5rem);
    top: calc(50% - .5rem);
    -webkit-animation: spinner-border .75s linear infinite;
    animation: spinner-border .75s linear infinite;
  }
	.height_form_wizard {
		@media (max-height: 660px) {
			height: 72vh !important;
		}
		@media (max-height: 800px) and (min-height: 660px) {
			height: 76vh !important;
		}
		@media (max-height: 970px) and (min-height: 800px) {
			height: 83vh !important;
		}
	}
</style>
