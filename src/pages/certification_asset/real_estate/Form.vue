<template>
  <div class="certification-asset">
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
      :class="{ step7: isStep7Active }"
    >
      <tab-content title="Thông tin chung" icon="" >
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
            :businesses="businesses"
            :conditions="conditions"
            :socialSecurities="socialSecurities"
            :fengshuies="fengshuies"
            :zones="zones"
            :provinces="provinces"
            :districts="districts"
            :wards="wards"
            :streets="streets"
            :distances="distances"
            :materials="materials"
            :full_address="full_address"
            :full_address_street="full_address_street"
            :imageDescriptions="imageDescriptions"
            :addressName="addressName"
            @getDistrict="changeProvince"
            @getWardStreet="changeDistrict"
            @getWard="changeWard"
            @changeStreet="changeStreet"
            @changeDistance="changeDistance"
            @getAssetType="changeAssetType"
            @addTurning="addTurning"
            @deleteTurning="deleteTurning"
            @changeRoadDistance="changeRoadDistance"
            @changeRoadAlley="changeRoadAlley"
            @uploadImage="uploadImage"
            @changeDescriptionFrontSide="changeDescriptionFrontSide"
          />
          <div class="btn-footer d-md-flex d-block justify-content-end align-items-center">
            <div class="d-lg-flex d-block button-contain">
              <button @click.prevent="handleChangeBack" class="btn btn-white text-nowrap" >
                <img src="@/assets/icons/ic_cancel.svg" style="margin-right: 12px" alt="save" />Trở lại
              </button>
              <button class="btn btn-white btn-orange text-nowrap" :class="{ 'btn_loading disabled': isSubmit }" @click.prevent="validateSubmitStep1" type="submit">
                <img src="@/assets/icons/ic_save.svg" style="margin-right: 12px" alt="save"/>Lưu
              </button>
							<button v-if="isEdit && isCancelEnable" @click.prevent="handleCancelProperty()" class="btn btn-white text-nowrap">
								<img src="@/assets/icons/ic_destroy.svg" style="margin-right: 12px" alt="cancel">Hủy tài sản
							</button>
            </div>
          </div>
        </ValidationObserver>
      </tab-content>

      <tab-content title="Quyền sử dụng đất" icon="">
        <ValidationObserver
          tag="form"
          ref="step_2"
          @submit.prevent="validateSubmitStep2"
        >
        <Step2
			:key="key_step_2"
			:data="form.step_2"
			:coordinates="form.step_1.general_infomation.coordinates"
			:topographic="topographic"
			:landShapes="landShapes"
			:points="points"
			:type_purposes="type_purposes"
			:propertyTypes="propertyTypes"
			@deleteMainArea="deleteMainArea"
			@addMainArea="addMainArea"
			@deletePlanningArea="deletePlanningArea"
			@addPlanningArea="addPlanningArea"
			@changeLandTypePurpose="changeLandTypePurpose"
			@changeUnitPrice="changeUnitPrice"
			@handleChangeMainArea="handleChangeMainArea"
			@handleChangePlanningArea="handleChangePlanningArea"
			@changeStatusPlanning="changeStatusPlanning"
			@changeLandPlanningPurpose="changeLandPlanningPurpose"
			@validateArea="validateArea"
			@checkDuplicateLandType="checkDuplicateLandType"
        />
          <div class="btn-footer d-md-flex d-block justify-content-end align-items-center">
            <div class="d-lg-flex d-block button-contain">
              <button @click.prevent="handleChangeBack" class="btn btn-white text-nowrap" >
                <img src="@/assets/icons/ic_cancel.svg" style="margin-right: 12px" alt="save" />Trở lại
              </button>
              <button v-if="edit || add" class="btn btn-white btn-orange text-nowrap" :class="{ 'btn_loading disabled': isSubmit }" type="submit">
                <img src="@/assets/icons/ic_save.svg" style="margin-right: 12px" alt="save"/>Lưu
              </button>
							<button v-if="isEdit && isCancelEnable" @click.prevent="handleCancelProperty()" class="btn btn-white text-nowrap">
								<img src="@/assets/icons/ic_destroy.svg" style="margin-right: 12px" alt="cancel">Hủy tài sản
							</button>
            </div>
          </div>
       </ValidationObserver>

      </tab-content>

      <tab-content title="Công trình xây dựng" icon="">
        <ValidationObserver
          tag="div"
          ref="step_3"
					class="height_form_wizard"
          @submit.prevent="validateSubmitStep3"
        >
          <Step3
            :data="form.step_3"
            :key="key_step_3"
            :housingTypes="housingTypes"
            :buildingCategories="buildingCategories"
            :buildingStructure="buildingStructure"
            :buildingRates="buildingRates"
            :buildingAperture="buildingAperture"
            :buildingFactoryType="buildingFactoryType"
            :isHaveContruction="isHaveContruction"
            :buildingCrane="buildingCrane"
			@createContruction="createContruction"
			@updateContruction="updateContruction"
			@deleteContruction="deleteContruction"
          />
          <div class="btn-footer d-md-flex d-block justify-content-end align-items-center">
            <div class="d-lg-flex d-block button-contain">
              <button @click.prevent="handleChangeBack" class="btn btn-white text-nowrap" >
                <img src="@/assets/icons/ic_cancel.svg" style="margin-right: 12px" alt="save" />Trở lại
              </button>
               <button v-if="edit || add" class="btn btn-white btn-orange text-nowrap" :class="{ 'btn_loading disabled': isSubmit }" @click.prevent="validateSubmitStep3" type="submit">
                <img src="@/assets/icons/ic_save.svg" style="margin-right: 12px" alt="save"/>Lưu
              </button>
							<button v-if="isEdit && isCancelEnable" @click.prevent="handleCancelProperty()" class="btn btn-white text-nowrap">
								<img src="@/assets/icons/ic_destroy.svg" style="margin-right: 12px" alt="cancel">Hủy tài sản
							</button>
            </div>
          </div>
        </ValidationObserver>
      </tab-content>

      <tab-content title="Pháp lý tài sản" icon="" >
        <ValidationObserver
          tag="div"
          ref="step_4"
					class="height_form_wizard"
        >
          <Step4
            :data="form.step_4"
            :key="key_step_4"
            :juridicals="juridicals"
            :provinceName="provinceName"
			:full_address="full_address"
			@updateLegal="updateLegal"
			@createLegal="createLegal"
			@deleteLegal="deleteLegal"
          />
          <div class="btn-footer d-md-flex d-block justify-content-end align-items-center">
            <div class="d-lg-flex d-block button-contain">
              <button @click.prevent="handleChangeBack" class="btn btn-white text-nowrap" >
                <img src="@/assets/icons/ic_cancel.svg" style="margin-right: 12px" alt="save" />Trở lại
              </button>
              <button v-if="edit || add" class="btn btn-white btn-orange text-nowrap" :class="{ 'btn_loading disabled': isSubmit }" @click.prevent="validateSubmitStep4" type="submit">
                <img src="@/assets/icons/ic_save.svg" style="margin-right: 12px" alt="save"/>Lưu
              </button>
							<button v-if="isEdit && isCancelEnable" @click.prevent="handleCancelProperty()" class="btn btn-white text-nowrap">
								<img src="@/assets/icons/ic_destroy.svg" style="margin-right: 12px" alt="cancel">Hủy tài sản
							</button>
            </div>
          </div>
        </ValidationObserver>
      </tab-content>

      <tab-content title="Cơ sở thẩm định" icon="">
        <ValidationObserver
          tag="div"
          ref="step_5"
          @submit.prevent="validateSubmitStep5"
        >
        <Step5
          :data="form.step_5"
          :key="key_step_5"
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
            <button v-if="edit || add" class="btn btn-white btn-orange text-nowrap" :class="{ 'btn_loading disabled': isSubmit }" @click.prevent="validateSubmitStep5" type="submit">
              <img src="@/assets/icons/ic_save.svg" style="margin-right: 12px" alt="save"/>Lưu
            </button>
						<button v-if="isEdit && isCancelEnable" @click.prevent="handleCancelProperty()" class="btn btn-white text-nowrap">
							<img src="@/assets/icons/ic_destroy.svg" style="margin-right: 12px" alt="cancel">Hủy tài sản
						</button>
          </div>
        </div>
        </ValidationObserver>
      </tab-content>

      <tab-content title="Tài sản so sánh" icon="">
        <ValidationObserver
          tag="div"
          ref="step_6"
        >
        <Step6
          :data="form.step_6"
          :key="key_step_6"
          :step_active="step_active"
          :comparison="comparison"
          :propertyTypes="propertyTypes"
          :type_purposes="type_purposes"
          :frontSide="form.step_1.traffic_infomation.front_side"
          :coordinates="form.step_1.general_infomation.coordinates"
					:asset_type_id="form.step_1.general_infomation.asset_type_id"
          :distance_max="distance_max"
          @choosingAsset="choosingAsset"
          @saveImageMap="saveImageMap"
        />
        <div class="btn-footer d-md-flex d-block justify-content-end align-items-center">
          <div class="d-lg-flex d-block button-contain">
            <button @click.prevent="handleChangeBack" class="btn btn-white text-nowrap" >
              <img src="@/assets/icons/ic_cancel.svg" style="margin-right: 12px" alt="save" />Trở lại
            </button>
            <button v-if="edit || add" class="btn btn-white btn-orange text-nowrap" :class="{ 'btn_loading disabled': isSubmit }" @click.prevent="validateSubmitStep6" type="submit">
              <img src="@/assets/icons/ic_save.svg" style="margin-right: 12px" alt="save"/>Lưu
            </button>
						<button v-if="isEdit && isCancelEnable" @click.prevent="handleCancelProperty()" class="btn btn-white text-nowrap">
							<img src="@/assets/icons/ic_destroy.svg" style="margin-right: 12px" alt="cancel">Hủy tài sản
						</button>
          </div>
        </div>
        </ValidationObserver>
      </tab-content>

      <tab-content title="Giá trị tài sản" icon="">
        <!-- <Step7
          :data="form.step_7"
          :idData="idData"
          :key="key_step_7"
          :dataStep1="form.step_1"
          :dataStep2="form.step_2"
          :dataStep3="form.step_3"
          :dataStep4="form.step_4"
          :dataStep5="form.step_5"
          :dataStep6="form.step_6"
          :full_address_street="full_address_street"
          :landShapes="landShapes"
          :constructions="constructions"
          :construction_company_ids="form.construction_company_ids"
          :edit="edit"
          :add="add"
          :status="form.status"
          @updateDataStep7="updateDataStep7"
        /> -->
      </tab-content>
    </form-wizard>
    <ModalNotificationAppraisal
      v-if="showConfirmEdit"
      @cancel="showConfirmEdit = false"
      v-bind:notification="messageConfirm"
      @action="confirmEditStep"
    />
		<ModalNotificationAppraisal
			v-if="openCancelAppraisal"
			@cancel="openCancelAppraisal = false"
			v-bind:notification="message"
			@action="handleActionCancelAppraise"
		/>
	<ModalNotificationAppraisal
      v-if="showConfirmDuplicate"
      @cancel="showConfirmDuplicate = false"
      :notification="'Bạn có muốn nhân bản tài sản thẩm định không'"
      @action="actionDuplicate"
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
import Step5 from './component/Step5'
import Step6 from './component/Step6'
// import Step7 from './component/Step7'
import ModalNotificationAppraisal from '@/components/Modal/ModalNotificationAppraisal'
import WareHouse from '@/models/WareHouse'
import Certificate from '@/models/Certificate'
import { COMPARISON } from '@/enum/comparison-factor.enum'
import AppraiseData from '@/models/AppraiseData'
import CertificateAsset from '@/models/CertificateAsset'
import moment from 'moment'
import store from '@/store'
import * as types from '@/store/mutation-types'
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
		Step5,
		Step6,
		// Step7,
		ModalNotificationAppraisal
	},
	data () {
		return {
			isAutomation: false,
			isEdit: false,
			isCancelEnable: true,
			idData: null,
			isSave: false,
			openNotification: false,
			isSubmit: false,
			openModalCancel: false,
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
			key_step_6: 50000,
			key_step_7: 60000,
			key_render_formwizard: 70000,
			isHaveContruction: false,
			step_active: null,
			distance_max: null,
			form: {
				step_1: {
					economic_infomation: {
						business_id: '',
						condition_id: '',
						feng_shui_id: '',
						zoning_id: 34,
						social_security_id: ''
					},
					general_infomation: {
						asset_type_id: '',
						province_id: '',
						district_id: '',
						ward_id: '',
						street_id: '',
						distance_id: '',
						appraise_asset: '',
						coordinates: '',
						topographic_id: '',
						full_address: ''
					},
					traffic_infomation: {
						front_side: '',
						individual_road: '',
						main_road_length: '',
						material: '',
						material_id: '',
						two_sides_land: '',
						property_turning_time: [{
							is_alley_with_connection: '',
							main_road_distance: '',
							main_road_length: '',
							material_id: '',
							turning: 'Hẻm số 1'
						}],
						description: ''
					},
					picture_infomation: []
				},
				step_2: {
					land_details: {
						front_side_width: '',
						insight_width: '',
						land_shape_id: '',
						coordinates: '',
						appraise_land_sum_area: 0,
						topographic: { topographic_id: '' }
					},
					total_area: [
						{
							land_type_purpose: {},
							land_type_purpose_id: '',
							total_area: '',
							is_transfer_facility: true
						}
					],
					planning_area: [],
					UBND_price: [],
					real_estate: {
						planning_info: '',
						planning_source: '',
						contact_person: '',
						contact_phone: ''
					}
				},
				step_3: {
					construction: []
					// {
					// 	building_type_id: '',
					// 	gpxd: true,
					// 	building_category_id: '',
					// 	floor: '',
					// 	remaining_quality: '',
					// 	total_construction_base: '',
					// 	total_construction_area: '',
					// 	start_using_year: '',
					// 	duration: '',
					// 	description: '',
					// 	other_building: '',
					// 	rate_id: '',
					// 	structure_id: '',
					// 	crane_id: '',
					// 	aperture_id: '',
					// 	factory_type_id: '',
					// 	created_at: new Date(),
					// 	contruction_description: '+ Móng cột:\n+ Dầm, sàn BTCT chịu lực: \n+ Tường xây: \n+ Mái BTCT: \n+ Nền lát: \n+ Cửa đi, cửa sổ: \n+ Khu vệ sinh: \n+ Khu bếp: \n+ Cầu thang: \n'
					// }
				},
				step_4: {
					law: []
				},
				step_5: {
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
						document_description: 'Các hồ sơ, tài liệu về tài sản do khách hàng cung cấp là đầy đủ và tin cậy',
						appraise_approach_id: '',
						appraise_basis_property_id: '',
						appraise_principle_id: '',
						appraise_method_used_id: ''
					}
				},
				step_6: {
					comparison_factor: ['phap_ly'],
					assets_general: [],
					map_img: ''
				},
				// step_7: {
				// 	appraise_adapter: [],
				// 	asset_price: [],
				// 	asset_unit_area: [],
				// 	asset_unit_price: [],
				// 	comparison_factor: [],
				// 	comparison_tangible_factor: [],
				// 	construction_company: [],
				// 	other_assets: []
				// },
				construction_company_ids: [],
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
			comparison: COMPARISON,
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
			isStep7Active: false,
			isDuplicate: false,
			step2AreaValidate: '',
			step2DuplicateLandType: ''
		}
	},
	async created () {
		await this.getProfiles()
		const permission = this.$store.getters.currentPermissions
		await WareHouse.getProvince()
			.then((resp) => {
				this.provinces = resp.data
			})
			.catch((err) => {
				throw err
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
		if ('id' in this.$route.query && this.$route.name === 'certification_asset.edit') {
			this.isEdit = true
			if (this.$route.meta['step']) {
				let bindDataStep = this.$route.meta['step']

				if (bindDataStep.certificate) { this.isCancelEnable = false }
				// step 1
				if (bindDataStep.economic_infomation) { this.form.step_1.economic_infomation = bindDataStep.economic_infomation }
				if (bindDataStep.general_infomation) { this.form.step_1.general_infomation = bindDataStep.general_infomation }
				if (bindDataStep.traffic_infomation) { this.form.step_1.traffic_infomation = bindDataStep.traffic_infomation }
				if (bindDataStep.picture_infomation && bindDataStep.picture_infomation.length > 0) { this.form.step_1.picture_infomation = bindDataStep.picture_infomation }
				// step 2
				if (bindDataStep.land_details) { this.form.step_2.land_details = bindDataStep.land_details }
				if (bindDataStep.total_area && bindDataStep.total_area.length > 0) { this.form.step_2.total_area = bindDataStep.total_area }
				if (bindDataStep.planning_area && bindDataStep.planning_area.length > 0) { this.form.step_2.planning_area = bindDataStep.planning_area }
				if (bindDataStep.UBND_price && bindDataStep.UBND_price.length > 0) { this.form.step_2.UBND_price = bindDataStep.UBND_price }
				if (bindDataStep.real_estate) { this.form.step_2.real_estate = bindDataStep.real_estate }
				// step 3
				if (bindDataStep.construction && bindDataStep.construction.length > 0) { this.form.step_3.construction = bindDataStep.construction }
				// step 4
				if (bindDataStep.law && bindDataStep.law.length > 0) { this.form.step_4.law = bindDataStep.law }
				// step 5
				if (bindDataStep.appraisal_methods) { this.form.step_5.appraisal_methods = bindDataStep.appraisal_methods }
				if (bindDataStep.value_base_and_approach) { this.form.step_5.value_base_and_approach = bindDataStep.value_base_and_approach }
				// step 6
				if (bindDataStep.comparison_factor && bindDataStep.comparison_factor.length > 0) { this.form.step_6.comparison_factor = bindDataStep.comparison_factor }
				if (bindDataStep.assets_general && bindDataStep.assets_general.length > 0) { this.form.step_6.assets_general = bindDataStep.assets_general }
				if (bindDataStep.map_img) { this.form.step_6.map_img = bindDataStep.map_img }
				if (bindDataStep.distance_max) { this.distance_max = bindDataStep.distance_max }
				this.status_text = bindDataStep.status_text
				this.form.status = bindDataStep.status
				this.idData = await this.form.step_1.general_infomation.id
				if (this.form.step_1.general_infomation.asset_type_id === 38) {
					this.isHaveContruction = true
				} else {
					this.isHaveContruction = false
				}
				if (bindDataStep.step >= 6) {
					this.step_active = 5
				} else this.step_active = bindDataStep.step

				await this.$refs.wizard.tabs.forEach((tab, index) => {
					if (index <= this.step_active) {
						tab.checked = true
					}
				})
				if (this.step_active === 7) {
					await this.$refs.wizard.changeTab(0, 5)
				} else {
					await this.$refs.wizard.changeTab(0, this.step_active)
				}
			}
			// if (this.$route.meta['step7']) { this.form.step_7 = Object.assign(this.form.step_7, { ...this.$route.meta['step7'] }) }
			// if (this.form.step_7.construction_company && this.form.step_7.construction_company.length > 0) {
			// 	await this.form.step_7.tangible_assets[0].construction_company.forEach(item => {
			// 		this.form.construction_company_ids.push(item.construction_company_id)
			// 	})
			// }
			if (this.form.step_6.comparison_factor.length > 1) {
				await this.comparison.forEach(item => {
					this.form.step_6.comparison_factor.forEach(itemFactor => {
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
			this.key_step_6 += 1
			if (this.$route.params.step || this.$route.params.step === 0) {
				await this.$refs.wizard.changeTab(0, this.$route.params.step)
			}
			if (this.step_active < 5) {
				// Remove the automated selection of comparison asset.
				// this.isAutomation = true
			}
		} else if ('id' in this.$route.params && this.$route.name === 'certification_asset.create') {
			if (this.$route.meta['step']) {
				let bindDataStep = this.$route.meta['step']
				this.isDuplicate = true
				// step 1
				if (bindDataStep.economic_infomation) { this.form.step_1.economic_infomation = bindDataStep.economic_infomation }
				if (bindDataStep.general_infomation) { this.form.step_1.general_infomation = bindDataStep.general_infomation }
				if (bindDataStep.traffic_infomation) { this.form.step_1.traffic_infomation = bindDataStep.traffic_infomation }
				if (bindDataStep.picture_infomation && bindDataStep.picture_infomation.length > 0) { this.form.step_1.picture_infomation = bindDataStep.picture_infomation }
				// step 2
				if (bindDataStep.land_details) { this.form.step_2.land_details = bindDataStep.land_details }
				if (bindDataStep.total_area && bindDataStep.total_area.length > 0) { this.form.step_2.total_area = bindDataStep.total_area }
				if (bindDataStep.planning_area && bindDataStep.planning_area.length > 0) { this.form.step_2.planning_area = bindDataStep.planning_area }
				if (bindDataStep.UBND_price && bindDataStep.UBND_price.length > 0) { this.form.step_2.UBND_price = bindDataStep.UBND_price }
				// step 3
				if (bindDataStep.construction && bindDataStep.construction.length > 0) { this.form.step_3.construction = bindDataStep.construction }
				// step 4
				if (bindDataStep.law && bindDataStep.law.length > 0) { this.form.step_4.law = bindDataStep.law }
				// step 5
				if (bindDataStep.appraisal_methods) { this.form.step_5.appraisal_methods = bindDataStep.appraisal_methods }
				if (bindDataStep.value_base_and_approach) { this.form.step_5.value_base_and_approach = bindDataStep.value_base_and_approach }
			}
			if (this.form.step_1.general_infomation.asset_type_id === 38) {
				this.isHaveContruction = true
			} else {
				this.isHaveContruction = false
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
		async handleChange (prevIndex, nextIndex) {
			if (nextIndex === 5 && this.isAutomation) {
				const response = await CertificateAsset.getAssetAutomationStep6(this.idData)
				if (response.data) {
					this.form.step_6.assets_general = await response.data.assets
					this.form.step_6.comparison_factor = await response.data.comparison_factor
					this.form.step_6.map_img = ''
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
				}
			}
			let currentIndex = nextIndex + 1
			if (currentIndex === 1) { this.key_step_1 += 1 }
			if (currentIndex === 2) { this.key_step_2 += 1 }
			if (currentIndex === 6) { this.key_step_6 += 1 }
			// this.key_step_1 += 1
			// this.key_step_2 += 1
			// this.key_step_6 += 1
			this.isStep7Active = !!(this.step_active === 7 && nextIndex === 6)
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
			} else if (step === 5) {
				this.handleSubmitStep_5(this.form.step_5, this.idData)
			} else if (step === 6) {
				this.handleSubmitStep_6(this.form.step_6, this.idData)
			}
			if (step < 6) {
				this.isAutomation = true
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
				let step_1 = this.form.step_1
				if (step_1.traffic_infomation.front_side === '') {
					this.isSubmit = false
					this.$toast.open({
						message: 'Vui lòng chọn mặt tiền tuyến đường chính',
						type: 'error',
						position: 'top-right'
					})
				} else if (step_1.traffic_infomation.front_side === 0 && step_1.traffic_infomation.property_turning_time.length === 0) {
					this.isSubmit = false
					this.$toast.open({
						message: 'Vui lòng nhập thông tin quẹo/hẻm',
						type: 'error',
						position: 'top-right'
					})
				} else {
					if ('id' in this.$route.query && this.$route.name === 'certification_asset.edit') {
						this.confirmSavePreviousStep(1)
					} else if (this.$route.name === 'certification_asset.create') {
						if (this.isDuplicate) {
							this.showConfirmDuplicate = true
						} else {
							this.confirmSavePreviousStep(1)
						}
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
			this.isSubmit = true
			let id = this.idData ? this.idData : ''
			const res = await CertificateAsset.submitStep1(dataStep1, id)
			if (res.data) {
				this.isSubmit = false
				this.idData = res.data.general_infomation.id
				this.form.step_2.land_details.coordinates = res.data.general_infomation.coordinates
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
			this.form.step_1.picture_infomation.push(image)
			this.key_step_1 += 1
		},
		// ------------------------------------------ Ation of STEP 2------------------------------------------------------------//
		addMainArea () {
			let checkIsCheckFacility = this.form.step_2.total_area.filter(item => item.is_transfer_facility === true)
			if (checkIsCheckFacility && checkIsCheckFacility.length > 0) {
				this.form.step_2.total_area.push({
					land_type_purpose_id: '',
					total_area: '',
					is_transfer_facility: false
				})
			} else {
				this.form.step_2.total_area.push({
					land_type_purpose_id: '',
					total_area: '',
					is_transfer_facility: null
				})
			}
			this.handleGetTotalArea()
			this.handleChangeUBNDPrice()
		},
		deleteMainArea (index) {
			if (this.form.step_2.total_area.length > 1) {
				this.form.step_2.total_area.splice(index, 1)
				this.form.step_2.UBND_price.splice(index, 1)
			} else {
				this.$toast.open({
					message: 'Diện tích phù hợp quy hoạch không được rỗng',
					type: 'error',
					position: 'top-right'
				})
			}
			let checkIsCheckFacility = this.form.step_2.total_area.filter(item => item.is_transfer_facility === true)
			if (checkIsCheckFacility && checkIsCheckFacility.length === 0) {
				this.form.step_2.total_area[0].is_transfer_facility = true
			}
			this.handleChangeUBNDPrice()
			this.handleGetTotalArea()
		},
		addPlanningArea () {
			this.form.step_2.planning_area.push({
				land_type_purpose_id: '',
				planning_area: '',
				type_zoning: '',
				land_type_purpose: {}
			})
			this.handleGetTotalArea()
			this.handleChangeUBNDPrice()
		},
		deletePlanningArea (index) {
			if (this.form.step_2.planning_area.length > 0) {
				this.form.step_2.planning_area.splice(index, 1)
			}
			this.handleChangeUBNDPrice()
			this.handleGetTotalArea()
		},
		async changeLandTypePurpose (index, land_type_purpose_id) {
			if (land_type_purpose_id) {
				// set land_type_purpose for total_area
				let getDataTypePrupose = this.type_purposes.filter(item => item.id === land_type_purpose_id)
				if (getDataTypePrupose && getDataTypePrupose.length > 0) {
					this.form.step_2.total_area[index].land_type_purpose = getDataTypePrupose[0]
				}
			} else {
				this.form.step_2.total_area[index].land_type_purpose = {}
			}
			await this.handleChangeUBNDPrice()
			await this.handleGetTotalArea()
		},
		async changeLandPlanningPurpose (index, land_type_purpose_id) {
			if (land_type_purpose_id) {
				// set land_type_purpose for planning_area
				let getDataTypePrupose = this.type_purposes.filter(item => item.id === land_type_purpose_id)
				if (getDataTypePrupose && getDataTypePrupose.length > 0) {
					this.form.step_2.planning_area[index].land_type_purpose = getDataTypePrupose[0]
				} else {
					this.form.step_2.planning_area[index].land_type_purpose = {}
				}
			}
			await this.handleChangeUBNDPrice()
			await this.handleGetTotalArea()
		},

		handleChangeUBNDPrice () {
			const map = new Map()
			const map1 = new Map()
			let allPurposeArray = []
			// check total_area and create data UBND_price
			this.form.step_2.total_area.forEach(itemMainArea => {
				if (!map.has(itemMainArea.land_type_purpose_id)) {
					map.set(itemMainArea.land_type_purpose_id)
					allPurposeArray.push({
						position_type_id: '',
						circular_unit_price: '',
						land_type_purpose_id: itemMainArea.land_type_purpose_id,
						land_type_purpose: itemMainArea.land_type_purpose
					})
				}
			})
			this.form.step_2.planning_area.forEach(itemPlanningArea => {
				if (!map1.has(itemPlanningArea.land_type_purpose_id)) {
					map1.set(itemPlanningArea.land_type_purpose_id)
					let checkUBNDPrice1 = allPurposeArray.filter(item => item.land_type_purpose_id === itemPlanningArea.land_type_purpose_id)
					if (checkUBNDPrice1 && checkUBNDPrice1.length === 0 && itemPlanningArea.land_type_purpose_id) {
						allPurposeArray.push({
							position_type_id: '',
							circular_unit_price: '',
							land_type_purpose_id: itemPlanningArea.land_type_purpose_id,
							land_type_purpose: itemPlanningArea.land_type_purpose
						})
					}
				}
			})
			allPurposeArray.forEach(itemPurpose => {
				let checkUBNDIsExist = this.form.step_2.UBND_price.filter(itemUBND => itemUBND.land_type_purpose_id === itemPurpose.land_type_purpose_id)
				if (checkUBNDIsExist && checkUBNDIsExist.length > 0) {
					itemPurpose.position_type_id = checkUBNDIsExist[0].position_type_id
					itemPurpose.circular_unit_price = checkUBNDIsExist[0].circular_unit_price
				}
			})
			this.form.step_2.UBND_price = allPurposeArray
		},
		changeUnitPrice (value, index) {
			this.form.step_2.UBND_price[index].circular_unit_price = value
		},
		handleChangeMainArea (value, index) {
			if (value) {
				this.form.step_2.total_area[index].total_area = value
				this.handleGetTotalArea()
			}
		},
		handleChangePlanningArea (value, index) {
			if (value) {
				this.form.step_2.planning_area[index].planning_area = value
				this.handleGetTotalArea()
			}
		},
		handleGetTotalArea () {
			let total = 0
			let map = new Map()
			this.form.step_2.total_area.forEach(item => {
				if (!map.has(item.land_type_purpose_id)) { map.set(item.land_type_purpose_id, item.total_area) }
				total += item.total_area
			})
			this.form.step_2.planning_area.forEach(item => {
				if (!map.has(item.land_type_purpose_id)) { total += item.planning_area }
			})

			this.form.step_2.land_details.appraise_land_sum_area = +total
		},
		changeStatusPlanning (status) {
			if (status) {
				this.form.step_2.planning_area.push({
					land_type_purpose_id: '',
					planning_area: '',
					type_zoning: ''
				})
			} else {
				this.form.step_2.planning_area = []
				this.handleGetTotalArea()
				this.handleChangeUBNDPrice()
			}
		},
		async validateSubmitStep2 () {
			const isValid = await this.$refs.step_2.validate()
			let step_2 = this.form.step_2
			let checkValidPurpose = false
			// console.log('step_2.errorCustom', this.step2DuplicateLandType)
			step_2.total_area.forEach(item => {
				if (!item.land_type_purpose_id) {
					checkValidPurpose = true
				}
			})
			step_2.planning_area.forEach(item => {
				if (!item.land_type_purpose_id) {
					checkValidPurpose = true
				}
			})

			if (isValid) {
				if (step_2.total_area.length === 0) {
					this.$toast.open({
						message: 'Vui lòng nhập diện tích theo mục đích sử dụng',
						type: 'error',
						position: 'top-right'
					})
				} else if (checkValidPurpose) {
					this.$toast.open({
						message: 'Vui lòng nhập mục đích sử dụng',
						type: 'error',
						position: 'top-right'
					})
				} else if (this.step2AreaValidate) {
					this.$toast.open({
						message: this.step2AreaValidate,
						type: 'error',
						position: 'top-right'
					})
				} else if (this.step2DuplicateLandType) {
					this.$toast.open({
						message: this.step2DuplicateLandType,
						type: 'error',
						position: 'top-right'
					})
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
			this.isSubmit = true
			const res = await CertificateAsset.submitStep2(dataStep2, this.idData)
			if (res.data) {
				this.isSubmit = false
				this.$toast.open({
					message: 'Lưu chi tiết đất thành công',
					type: 'success',
					position: 'top-right',
					duration: 3000
				})
				this.$refs.wizard.maxStep = 2
				this.$refs.wizard.tabs.forEach((tab, index) => {
					if (index > 2) {
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
		async getCrane () {
			try {
				const resp = await WareHouse.getCrane()
				this.buildingCrane = [...resp.data]
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},
		changeBuildingType (event) {
			this.form.step_3.construction.building_type_id = event
		},
		changeCategoryBuilding () {},
		changeUsingYear (event) {
			if (event) {
				if (this.form.step_3.construction.created_at) {
					if ((+moment(this.form.step_3.construction.created_at).format('YYYY') - this.form.step_3.construction.start_using_year) > this.form.step_3.construction.duration) {
						this.form.step_3.construction.remaining_quality = 0
					} else this.form.step_3.construction.remaining_quality = parseFloat((1 - (+moment(this.form.step_3.construction.created_at).format('YYYY') - this.form.step_3.construction.start_using_year) / this.form.step_3.construction.duration) * 100).toFixed(0)
				} else {
					if (((new Date()).getFullYear() - this.form.step_3.construction.start_using_year) > this.form.step_3.construction.duration) {
						this.form.step_3.construction.remaining_quality = 0
					} else this.form.step_3.construction.remaining_quality = parseFloat((1 - ((new Date()).getFullYear() - this.form.step_3.construction.start_using_year) / this.form.step_3.construction.duration) * 100).toFixed(0)
				}
			}
			this.key_step_3 += 1
		},
		changeDuration (event) {
			if (event) {
				this.form.step_3.construction.duration = parseFloat(event).toFixed(0)
				if (this.form.step_3.construction.created_at) {
					if ((+moment(this.form.step_3.construction.created_at).format('YYYY') - this.form.step_3.construction.start_using_year) > this.form.step_3.construction.duration) {
						this.form.step_3.construction.remaining_quality = 0
					} else this.form.step_3.construction.remaining_quality = +parseFloat((1 - (+moment(this.form.step_3.construction.created_at).format('YYYY') - this.form.step_3.construction.start_using_year) / this.form.step_3.construction.duration) * 100).toFixed(0)
				} else {
					if (((new Date()).getFullYear() - this.form.step_3.construction.start_using_year) > this.form.step_3.construction.duration) {
						this.form.step_3.construction.remaining_quality = 0
					} else this.form.step_3.construction.remaining_quality = +parseFloat((1 - ((new Date()).getFullYear() - this.form.step_3.construction.start_using_year) / this.form.step_3.construction.duration) * 100).toFixed(0)
				}
			} else {
				this.form.step_3.construction.duration = ''
				this.form.step_3.construction.remaining_quality = ''
			}
			this.key_step_3 += 1
		},
		changeAperture () {},
		changeCrane () {},
		changeRate () {},
		changeFactionType () {},
		changeStructure () {},
		createContruction (data) {
			this.form.step_3.construction.push(data)
		},
		updateContruction (data, index) {
			this.form.step_3.construction[index] = data
		},
		deleteContruction (index) {
			this.form.step_3.construction.splice(index, 1)
		},
		async validateSubmitStep3 () {
			if (this.isHaveContruction) {
				if (this.form.step_3.construction.length > 0) {
					this.confirmSavePreviousStep(3)
				} else {
					this.$toast.open({
						message: 'Vui lòng thêm công trình xây dựng',
						type: 'error',
						position: 'top-right'
					})
				}
			} else {
				await this.$refs.wizard.nextTab()
			}
		},
		async handleSubmitStep_3 (dataStep3) {
			this.isSubmit = true
			const res = await CertificateAsset.submitStep3(dataStep3, this.idData)
			if (res.data) {
				this.isSubmit = false
				this.$toast.open({
					message: 'Lưu chi tiết về công trình thành công',
					type: 'success',
					position: 'top-right',
					duration: 3000
				})
				this.$refs.wizard.maxStep = 3
				this.$refs.wizard.tabs.forEach((tab, index) => {
					if (index > 3) {
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
		createLegal (dataLegal) {
			this.form.step_4.law.push(dataLegal)
			this.$toast.open({
				message: 'Thêm thành công',
				type: 'success',
				position: 'top-right',
				duration: 3000
			})
		},
		deleteLegal (index) {
			this.form.step_4.law.splice(index, 1)
			this.$toast.open({
				message: 'Xóa thành công',
				type: 'success',
				position: 'top-right',
				duration: 3000
			})
		},
		updateLegal (dataEdit, index) {
			this.form.step_4.law[index] = dataEdit
			this.$toast.open({
				message: 'Cập nhật thành công',
				type: 'success',
				position: 'top-right',
				duration: 3000
			})
		},
		async validateSubmitStep4 () {
			const isValid = await this.$refs.step_4.validate()
			if (isValid) {
				if (this.form.step_4.law.length === 0) {
					// await this.$toast.open({
					// 	message: 'Vui lòng thêm pháp lý cho tài sản',
					// 	type: 'error',
					// 	position: 'top-right'
					// })
					await this.$refs.wizard.nextTab()
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
			this.isSubmit = true
			const res = await CertificateAsset.submitStep4(dataStep4, this.idData)
			if (res.data) {
				this.isSubmit = false
				this.$toast.open({
					message: 'Lưu pháp lý tài sản thành công',
					type: 'success',
					position: 'top-right',
					duration: 3000
				})
				this.form.step_5.appraisal_methods = res.data.appraisal_methods
				this.form.step_5.value_base_and_approach = res.data.value_base_and_approach
				if (!res.data.value_base_and_approach) {
					await this.findAppraisalFacility()
					await this.findAppraisalPrinciples()
					await this.findApproach()
					await this.findMethodsUsed()
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

		// --------------------------------------------- Ation of STEP 5------------------------------------------------------------//
		changeLandRemaing (event) {
			if (event === 'theo-ty-le-gia-dat-co-so-chinh') {
				this.form.step_5.appraisal_methods.tinh_gia_dat_hon_hop_con_lai.value = 50
			} else {
				this.form.step_5.appraisal_methods.tinh_gia_dat_hon_hop_con_lai.value = ''
			}
			this.key_step_5 += 1
		},
		changeViolationPrice (event) {
			if (event === 'theo-ty-le-gia-dat-thi-truong') {
				this.form.step_5.appraisal_methods.tinh_gia_dat_vi_pham_quy_hoach.value = 80
			} else {
				this.form.step_5.appraisal_methods.tinh_gia_dat_vi_pham_quy_hoach.value = ''
			}
			this.key_step_5 += 1
		},
		changePercentRemain (event) {
			this.form.step_5.appraisal_methods.tinh_gia_dat_hon_hop_con_lai.value = event
		},
		changePercentVio (event) {
			this.form.step_5.appraisal_methods.tinh_gia_dat_vi_pham_quy_hoach.value = event
		},
		async validateSubmitStep5 () {
			const isValid = await this.$refs.step_5.validate()
			if (isValid) {
				this.confirmSavePreviousStep(5)
			} else {
				this.$toast.open({
					message: 'Vui lòng nhập đầy đủ các trường bắt buộc',
					type: 'error',
					position: 'top-right'
				})
			}
		},
		async handleSubmitStep_5 (dataStep5) {
			this.isSubmit = true
			const res = await CertificateAsset.submitStep5(dataStep5, this.idData)
			if (res.data) {
				this.isSubmit = false
				this.$toast.open({
					message: 'Lưu cơ sở thẩm định thành công',
					type: 'success',
					position: 'top-right',
					duration: 3000
				})
				this.$refs.wizard.maxStep = 5
				this.$refs.wizard.tabs.forEach((tab, index) => {
					if (index > 5) {
						tab.checked = false
					}
				})
				await this.$refs.wizard.nextTab()
				this.step_active = 5
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
		// --------------------------------------------- Ation of STEP 6------------------------------------------------------------//
		choosingAsset (assets) {
			this.form.step_6.assets_general = assets
			this.form.step_6.map_img = ''
		},
		saveImageMap (link) {
			this.form.step_6.map_img = 'https://apod.nasa.gov/apod/image/1505/AuroraNorway_Richardsen_2330.jpg'
		},
		async validateSubmitStep6 () {
			const isValid = await this.$refs.step_6.validate()
			let step_6 = this.form.step_6
			if (isValid) {
				if (step_6.comparison_factor.length === 0) {
					this.$toast.open({
						message: 'Vui lòng nhập yếu tố so sánh',
						type: 'error',
						position: 'top-right'
					})
				} else if (step_6.assets_general.length === 0) {
					this.$toast.open({
						message: 'Vui lòng chọn tài sản so sánh',
						type: 'error',
						position: 'top-right'
					})
				} else if (step_6.assets_general.length > 3) {
					this.$toast.open({
						message: 'Chỉ được chọn tối đa 3 tài sản so sánh',
						type: 'error',
						position: 'top-right'
					})
				} else if (step_6.assets_general.length < 3) {
					this.$toast.open({
						message: 'Vui lòng chọn đủ 3 tài sản so sánh',
						type: 'error',
						position: 'top-right'
					})
				} else if (!step_6.map_img) {
					this.$toast.open({
						message: 'Vui lòng chụp hình ảnh',
						type: 'error',
						position: 'top-right'
					})
				} else {
					this.confirmSavePreviousStep(6)
				}
			} else {
				this.$toast.open({
					message: 'Vui lòng nhập đầy đủ các trường bắt buộc',
					type: 'error',
					position: 'top-right'
				})
			}
		},
		async handleSubmitStep_6 (dataStep6) {
			this.isSubmit = true
			const res = await CertificateAsset.submitStep6(dataStep6, this.idData)
			if (res.data) {
				this.$toast.open({
					message: 'Lưu lựa chọn tài sản so sánh thành công',
					type: 'success',
					position: 'top-right',
					duration: 3000
				})
				this.distance_max = res.data.distance_max
				this.$router.push({
					name: 'certification_asset.detail',
					query: { id: this.idData },
					params: { step: 7 }
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
			this.form.created_by = profile.data.user.id
			this.current_create_by = profile.data.user.id
		},

		async handleCancel () {
			if (this.$route.name === 'certificate.create') {
				return this.$router.push({ name: 'certificate.index' })
			} else if (this.$route.name === 'certificate.edit') {
				this.$router.go(-1)
			}
		},
		getAddressEdit () {
			const province = this.form.step_1.general_infomation.province
			const district = this.form.step_1.general_infomation.district
			const ward = this.form.step_1.general_infomation.ward
			const street = this.form.step_1.general_infomation.street
			const distance = this.form.step_1.general_infomation.distance
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
			if (this.form.step_1.general_infomation.province_id) {
				this.getDistrictsByProvinceId(this.form.step_1.general_infomation.province_id)
				if ((this.$route.name === 'certification_asset.edit') || ('id' in this.$route.params && this.$route.name === 'certification_asset.create')) {
					this.getAddressEdit()
				}
			}
		},
		async getDistrictsByProvinceId (id) {
			await WareHouse.getDistrictsByProvinceId(id)
				.then((resp) => {
					this.districts = resp.data
					if (this.form.step_1.general_infomation.district_id) {
						this.getWardsByDistrictId(this.form.step_1.general_infomation.district_id)
						this.getStreetByDistrictId(this.form.step_1.general_infomation.district_id)
					}
				})
				.catch((err) => {
					this.isSubmit = false
					throw err
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
				this.form.step_1.general_infomation.street_id !== '' &&
        this.form.step_1.general_infomation.street_id !== undefined &&
        this.form.step_1.general_infomation.street_id !== null
			) {
				this.getDistanceByStreetId(this.form.step_1.general_infomation.street_id)
			}
		},
		async getDistanceByStreetId (id) {
			let distances = this.streets.filter(item => item.id === id)
			this.distances = distances[0].distances
		},
		findProvince () {
			const province = this.provinces.find(
				(province) => province.id === this.form.step_1.general_infomation.province_id
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
			const district = this.districts.find((district) => district.id === this.form.step_1.general_infomation.district_id)
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
			const ward = this.wards.find((ward) => ward.id === this.form.step_1.general_infomation.ward_id)
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
			const street = this.streets.find((street) => street.id === this.form.step_1.general_infomation.street_id)
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
			const distance = this.distances.find((distances) => distances.id === this.form.step_1.general_infomation.distance_id)
			if (distance) {
				this.addressName.distance = distance.name
			} else {
				this.addressName.distance = null
			}
		},

		// change location
		changeProvince (id) {
			this.form.step_1.general_infomation.district_id = ''
			this.form.step_1.general_infomation.ward_id = ''
			this.form.step_1.general_infomation.street_id = ''
			this.form.step_1.general_infomation.distance_id = ''
			this.districts = []
			this.wards = []
			this.streets = []
			this.distances = []
			if (this.form.step_1.general_infomation.province_id !== 0) {
				this.getDistrictsByProvinceId(id)
			}
			this.findProvince()
			this.getFullAddress()
		},

		changeDistrict (id) {
			this.wards = []
			this.streets = []
			this.distances = []
			this.form.step_1.general_infomation.ward_id = ''
			this.form.step_1.general_infomation.street_id = ''
			this.form.step_1.general_infomation.distance_id = ''
			if (this.form.step_1.general_infomation.district_id) {
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
			this.form.step_1.general_infomation.distance_id = ''
			if (this.form.step_1.general_infomation.street_id) {
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
			if (this.$route.name === 'certification_asset.create') {
				this.getInfo()
			}
		},
		changeAssetType (id) {
			const assetType = this.propertyTypes.find((assetType) => assetType.id === this.form.step_1.general_infomation.asset_type_id)
			if (assetType) {
				this.assetName = assetType.description
			} else {
				this.assetName = null
			}
			if (id === 38) {
				this.isHaveContruction = true
			} else {
				this.isHaveContruction = false
			}
			this.getInfo()
		},
		getInfo () {
			if (this.assetName === 'Đất trống' && this.full_address) {
				this.form.step_1.general_infomation.appraise_asset = `Quyền sử dụng đất tại ${this.full_address}`
			} else if (this.assetName === 'Đất có nhà' && this.full_address) {
				this.form.step_1.general_infomation.appraise_asset = `Quyền sử dụng đất và CTXD tại ${this.full_address}`
			}
			let streetName = this.streetName ? this.streetName.toLowerCase() : ''
			let fullAddress = `${streetName.length > 0 ? (streetName.substring(0, 5) === 'đường' ? '' : 'Đường ') + this.formatCapitalize(streetName) + ', ' : ''}` + this.full_address
			this.form.step_1.general_infomation.full_address = fullAddress
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
					this.landType = resp.data.loai_dat
					this.topographic = resp.data.dia_hinh
					this.landShapes = resp.data.hinh_dang_dat
					this.socialSecurities = resp.data.an_ninh_moi_truong_song
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
					this.propertyTypes = resp.data.loai_tai_san.filter(item => item.dictionary_acronym === 'BDS' && item.acronym !== 'CC')
					this.propertyTypes.forEach(item => {
						item.description = this.formatSentenceCase(item.description)
					})
					this.housingTypes.forEach(item => {
						item.description = this.formatSentenceCase(item.description)
					})
					this.materials.forEach(item => {
						item.description = this.formatSentenceCase(item.description)
					})
					this.landShapes.forEach(item => {
						item.description = this.formatSentenceCase(item.description)
					})
					this.type_purposes = resp.data.loai_dat_chi_tiet.filter(i => i.status === 1)
					this.type_purposes.sort((a, b) => a.id - b.id)
					this.type_purposes.forEach(item => {
						item.description = this.formatSentenceCase(item.description)
					})
					this.buildingCrane = resp.data.cau_truc_nha_xuong
					this.key_step_1 += 1
				})
				.catch((err) => {
					this.isSubmit = false
					throw err
				})
		},
		async getDictionaryLand () {
			const resp = await WareHouse.getDictionariesLand()
			this.type_purposes = [...resp.data]
			this.type_purposes.forEach(item => {
				item.description = this.formatSentenceCase(item.description)
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
		async getAppraiseConstructions () {
			await Certificate.getAppraiseConstructions()
				.then((resp) => {
					this.constructions = resp.data
				})
				.catch((err) => { throw err })
		},
		async getAppraisers () {
			await Certificate.getAppraisers()
				.then((resp) => {
					this.appraisers = resp.data
				})
				.catch((err) => { throw err })
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
				.catch((err) => { throw err })
		},
		findAppraisalFacility () {
			this.form.step_5.value_base_and_approach.appraise_basis_property_id = this.appraisalFacility.find((appraisalFacility) => appraisalFacility.is_defaults === true).id
		},
		findAppraisalPrinciples () {
			this.form.step_5.value_base_and_approach.appraise_principle_id = this.appraisalPrinciples.find((appraisalPrinciple) => appraisalPrinciple.is_defaults === true).id
		},
		findApproach () {
			this.form.step_5.value_base_and_approach.appraise_approach_id = this.approach.find((approach) => approach.is_defaults === true).id
		},
		findMethodsUsed () {
			this.form.step_5.value_base_and_approach.appraise_method_used_id = this.methodsUsed.find((methods) => methods.is_defaults === true).id
		},
		getCompareAssets (data, dataProperty) {
			this.compare_assets = data
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
				province_id: this.form.step_1.general_infomation.province_id,
				district_id: this.form.step_1.general_infomation.district_id,
				ward_id: this.form.step_1.general_infomation.ward_id,
				street_id: this.step_1.general_infomation.form.street_id,
				location: this.form.step_1.general_infomation.coordinates,
				front_side: this.form.properties.length > 0 ? this.form.properties[0].front_side : '',
				main_road_length: this.form.properties.length > 0 && this.form.properties[0].property_detail.length > 0 ? this.form.properties[0].property_detail[this.form.properties[0].property_detail.length - 1].main_road_length : '',
				distance: this.radius,
				unrecognized: unrecognized
			}
			if (
				this.form.step_1.general_infomation.province_id &&
        this.form.step_1.general_infomation.province_id !== '' &&
        this.form.step_1.general_infomation.district_id &&
        this.form.step_1.general_infomation.district_id !== '' &&
        this.form.step_1.general_infomation.ward_id &&
        this.form.step_1.general_infomation.ward_id !== '' &&
        this.form.step_1.general_infomation.street_id &&
        this.form.step_1.general_infomation.street_id !== '' &&
        this.form.step_1.general_infomation.coordinates &&
        this.form.step_1.general_infomation.coordinates !== '' &&
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
			this.form.assets_general = []
			if (this.data) {
				for (const asset of this.data.assets) {
					await this.getAssetGeneralDetail(asset)
				}
			}
		},
		async getAssetGeneralDetail (asset) {
			const resp = await WareHouse.getAssetGeneralDetail(asset.id)
			this.property = resp.data
			await this.getPropertyId(asset.id, asset.version)
			this.isSubmit = false
		},
		getPropertyId (id, version) {
			this.form.assets_general.push({
				assets_general_id: id,
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
			this.form.assets_general.forEach((item, index) => {
				if (item.assets_general_id === asset_id) {
					this.form.assets_general[index].asset_property_detail_id = id
					this.form.assets_general[index].version = property_data.version.length > 0 ? maxVersion : 1
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
		},
		validateArea (errorCustom) {
			this.step2AreaValidate = errorCustom
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
		checkDuplicateLandType (errorCustom) {
			if (this.step2DuplicateLandType === '') { this.step2DuplicateLandType = errorCustom }
		}
	},
	async beforeMount () {
		this.getDictionary()
		// this.getCrane()
		// this.getDictionaryLand()
		this.getAppraiseLaws()
		this.getAppraiseConstructions()
		this.getAppraiseOthers()
	},
	mounted () {
	}
}
</script>

<style scoped lang="scss">
.certification-asset {
  @media (max-width: 449px){
		margin-bottom: 100px;
	}
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
