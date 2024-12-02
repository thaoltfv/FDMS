<template>
 <div class="certification-asset">
  <form-wizard
      ref="wizard"
      color="#99D161"
      :title="`TSTD${idData ? `_${idData}` : '' }`"
      :subtitle="status_text"
      layout="vertical"
      finish-button-text="Hoàn Thành"
      back-button-text="Trở lại"
      next-button-text="Lưu"
      :startIndex="step_active || 0"
      class="vertical-steps steps-transparent"
  >
  <tab-content title="Thông tin chung" icon="" >
    <ValidationObserver tag="div" class="height_form_wizard" ref="step_1" @submit.prevent="validateSubmitStep1">
      <Step1
				:key="key_step_1"
				:data="form.step_1"
				:propertyTypes="propertyTypes"
				:manufacturer="manufacturer"
				:manufacturerCountryType="manufacturerCountryType"
				:fuelTypes="fuelTypes"
			/>
      <div class="btn-footer d-md-flex d-block justify-content-end align-items-center">
        <div class="d-lg-flex d-block button-contain">
          <button @click.prevent="handleChangeBack" class="btn btn-white text-nowrap" >
            <img src="@/assets/icons/ic_cancel.svg" style="margin-right: 12px" alt="save" />Trở lại
          </button>
          <button class="btn btn-white btn-orange text-nowrap" :class="{ 'btn_loading disabled': isSubmit }" @click.prevent="validateSubmitStep1" type="submit">
            <img src="@/assets/icons/ic_save.svg" style="margin-right: 12px" alt="save"/>Lưu
          </button>
        </div>
      </div>
    </ValidationObserver>
  </tab-content>
  <tab-content title="Pháp lý tài sản" icon="" >
    <ValidationObserver tag="div" class="height_form_wizard" ref="step_2" @submit.prevent="validateSubmitStep2">
      <Step2
				:key="key_step_2"
				:data="form.step_2"
				@createLegal="createLegal"
				@updateLegal="updateLegal"
				@deleteLegal="deleteLegal"
			/>
      <div class="btn-footer d-md-flex d-block justify-content-end align-items-center">
        <div class="d-lg-flex d-block button-contain">
          <button @click.prevent="handleChangeBack" class="btn btn-white text-nowrap" >
            <img src="@/assets/icons/ic_cancel.svg" style="margin-right: 12px" alt="save" />Trở lại
          </button>
          <button class="btn btn-white btn-orange text-nowrap" :class="{ 'btn_loading disabled': isSubmit }" @click.prevent="validateSubmitStep2" type="submit">
            <img src="@/assets/icons/ic_save.svg" style="margin-right: 12px" alt="save"/>Lưu
          </button>
        </div>
      </div>
    </ValidationObserver>
  </tab-content>
  <tab-content title="Cơ sở thẩm định" icon="" >
    <ValidationObserver tag="div"  class="height_form_wizard" ref="step_3" @submit.prevent="validateSubmitStep3">
      <Step3
				:key="key_step_3"
				:data="this.form.step_3.other_infomation"
				:appraisalPurposes="appraisalPurposes"
				:appraisalFacility="appraisalFacility"
				:appraisalPrinciples="appraisalPrinciples"
				:approach="approach"
				:methodsUsed="methodsUsed"
				:unifyIndicativePrice="unifyIndicativePrice"
			/>
      <div class="btn-footer d-md-flex d-block justify-content-end align-items-center">
        <div class="d-lg-flex d-block button-contain">
          <button @click.prevent="handleChangeBack" class="btn btn-white text-nowrap" >
            <img src="@/assets/icons/ic_cancel.svg" style="margin-right: 12px" alt="save" />Trở lại
          </button>
          <button class="btn btn-white btn-orange text-nowrap" :class="{ 'btn_loading disabled': isSubmit }" @click.prevent="validateSubmitStep3" type="submit">
            <img src="@/assets/icons/ic_save.svg" style="margin-right: 12px" alt="save"/>Lưu
          </button>
        </div>
      </div>
    </ValidationObserver>

  </tab-content>
  <tab-content title="Giá trị tài sản" icon="" >
		<div class="height_form_wizard">
			<Step4
				:key="key_step_4"
				:data="form.step_4.price"
				:idData="idData"
				@updateTotalOtherAsset="updateTotalOtherAsset"
			/>
		</div>
  </tab-content>
  </form-wizard>
	<ModalNotificationAppraisal
		v-if="showConfirmEdit"
		@cancel="showConfirmEdit = false"
		v-bind:notification="messageConfirm"
		@action="confirmEditStep"
	/>
  </div>
</template>
<script>
import Step1 from './component/Step1'
import Step2 from './component/Step2'
import Step3 from './component/Step3'
import Step4 from './component/Step4'
import { FormWizard, TabContent } from 'vue-form-wizard'
import ModalNotificationAppraisal from '@/components/Modal/ModalNotificationAppraisal'
import 'vue-form-wizard/dist/vue-form-wizard.min.css'
import store from '@/store'
import * as types from '@/store/mutation-types'
import WareHouse from '@/models/WareHouse'
import CertificateAsset from '@/models/CertificateAsset'
import Certificate from '@/models/Certificate'
export default {
	components: {
		FormWizard,
		TabContent,
		Step1,
		Step2,
		Step3,
		Step4,
		ModalNotificationAppraisal
	},
	data () {
		return {
			idData: null,
			step_active: null,
			status_text: '',
			isSubmit: false,
			showConfirmEdit: false,
			messageConfirm: '',
			step_edit: '',
			key_step_1: 100000,
			key_step_2: 200,
			key_step_3: 3000,
			key_step_4: 10000,
			propertyTypes: [],
			appraisalPurposes: [],
			appraisalFacility: [],
			appraisalPrinciples: [],
			approach: [],
			methodsUsed: [],
			unifyIndicativePrice: [],
			manufacturer: [],
			manufacturerCountryType: [],
			fuelTypes: [],
			form: {
				step_1: {
					name: '',
					asset_type_id: '',
					model: '',
					manufacturer_id: '',
					manufacturer_country_id: '',
					fuel_id: '',
					manufacturer_year: '',
					using_year: '',
					description: '+ Tình trạng:\n'
				},
				step_2: {
					law: []
				},
				step_3: {
					other_infomation: {
						approach_id: '',
						basis_property_id: '',
						method_used_id: '',
						principle_id: '',
						document_description: '- Giả thiết:\n- Giả thiết đặc biệt:'
					}
				},
				step_4: {
					price: {
						name: '',
						quantity: '',
						unit: '',
						unit_price: '',
						total_price: '',
						remaining_quality: 100
					}

				}
			}
		}
	},
	async created () {
		// await this.getProfiles()
		const permission = this.$store.getters.currentPermissions
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
		if ('id' in this.$route.query && this.$route.name === 'certification_asset.machine.edit') {
			if (this.$route.meta['step']) {
				let bindDataStep = this.$route.meta['step']
				// step 1
				this.form.step_1.name = bindDataStep.name
				this.form.step_1.asset_type_id = bindDataStep.asset_type_id
				this.form.step_1.description = bindDataStep.description
				this.form.step_1.model = bindDataStep.model
				this.form.step_1.manufacturer_id = bindDataStep.manufacturer_id
				this.form.step_1.manufacturer_country_id = bindDataStep.manufacturer_country_id
				this.form.step_1.fuel_id = bindDataStep.fuel_id
				this.form.step_1.manufacturer_year = bindDataStep.manufacturer_year
				this.form.step_1.using_year = bindDataStep.using_year
				// step 2
				this.form.step_2.law = bindDataStep.law
				// step 3
				if (bindDataStep.other_infomation) {
					this.form.step_3.other_infomation = bindDataStep.other_infomation
				}
				// step 4
				if (bindDataStep.price) this.form.step_4.price = await bindDataStep.price
				this.form.step_4.price.name = bindDataStep.name
				this.status_text = bindDataStep.status_text
				this.form.status = bindDataStep.status
				this.idData = bindDataStep.id
				if (bindDataStep.step >= 3) {
					this.step_active = 3
				} else this.step_active = bindDataStep.step

				await this.$refs.wizard.tabs.forEach((tab, index) => {
					if (index <= this.step_active) {
						tab.checked = true
					}
				})
				await this.$refs.wizard.changeTab(0, this.step_active)
			}
			if (this.$route.params.step || this.$route.params.step === 0) {
				await this.$refs.wizard.changeTab(0, this.$route.params.step)
			}
			this.key_step_1 += 1
			this.key_step_2 += 1
			this.key_step_3 += 1
			this.key_step_4 += 1
		}
		// else if ('id' in this.$route.params && this.$route.name === 'certification_asset.create') {
		// }
	},
	methods: {
		confirmSavePreviousStep (step_edit) {
			this.step_edit = step_edit
			if (this.step_active > step_edit - 1) {
				this.showConfirmEdit = true
				this.messageConfirm = `Dữ liệu ở sau bước ${step_edit} sẽ phải xác nhận lại. Bạn vẫn muốn tiếp tục ?`
			} else {
				this.confirmEditStep()
			}
		},
		confirmEditStep () {
			let step = this.step_edit
			if (step === 1) {
				this.handleSubmitStep_1(this.idData, this.form.step_1)
			} else if (step === 2) {
				this.handleSubmitStep_2(this.idData, this.form.step_2)
			} else if (step === 3) {
				this.handleSubmitStep_3(this.idData, this.form.step_3)
			}
			this.step_active = step
			this.showConfirmEdit = false
		},
		async handleChangeBack () {
			return this.$router.go(-1)
		},
		// ----------------------------------------------------- STEP 1 ---------------------------------------------------------------- //
		async validateSubmitStep1 () {
			// console.log('form', this.form)
			const isValid = await this.$refs.step_1.validate()
			if (isValid) {
				if ('id' in this.$route.query && this.$route.name === 'certification_asset.machine.edit') {
					this.confirmSavePreviousStep(1)
				} else if (this.$route.name === 'certification_asset.machine.create') {
					if (this.isDuplicate) {
						this.showConfirmDuplicate = true
					} else {
						this.confirmSavePreviousStep(1)
					}
				}
			} else {
				await this.$toast.open({
					message: 'Vui lòng nhập đầy đủ các trường bắt buộc',
					type: 'error',
					position: 'top-right'
				})
			}
		},
		async handleSubmitStep_1 (id, data) {
			this.isSubmit = true
			const res = await CertificateAsset.submitMachineStep1(id || '', data)
			if (res.data) {
				await this.$toast.open({
					message: 'Lưu thông tin chung',
					type: 'success',
					position: 'top-right',
					duration: 3000
				})
				this.idData = res.data.id
				this.form.step_4.price.name = data.name
				this.$refs.wizard.maxStep = 1
				this.$refs.wizard.tabs.forEach((tab, index) => {
					if (index > 1) {
						tab.checked = false
					}
				})
				await this.$refs.wizard.nextTab()
				this.step_active = 1
				this.key_step_4 += 1
				this.status_text = 'Mới'
			} else if (res.error) {
				await this.$toast.open({
					message: `${res.error.message}`,
					type: 'error',
					position: 'top-right'
				})
			} else {
				await this.$toast.open({
					message: 'Lưu thất bại',
					type: 'error',
					position: 'top-right'
				})
			}
			this.isSubmit = false
		},
		// ----------------------------------------------------- STEP 2 ---------------------------------------------------------------- //
		createLegal (data) {
			this.form.step_2.law.push(data)
			this.$toast.open({
				message: 'Thêm thành công',
				type: 'success',
				position: 'top-right',
				duration: 3000
			})
		},
		updateLegal (data, index) {
			this.form.step_2.law[index] = data
		},
		deleteLegal (index) {
			this.form.step_2.law.splice(index, 1)
		},
		async validateSubmitStep2 () {
			const isValid = await this.$refs.step_2.validate()
			if (isValid) {
				if (this.form.step_2.law.length === 0) {
					await this.$toast.open({
						message: 'Vui lòng thêm pháp lý cho tài sản',
						type: 'error',
						position: 'top-right'
					})
				} else {
					this.confirmSavePreviousStep(2)
				}
			} else {
				await this.$toast.open({
					message: 'Vui lòng nhập đầy đủ các trường bắt buộc',
					type: 'error',
					position: 'top-right'
				})
			}
		},
		async handleSubmitStep_2 (id, data) {
			this.isSubmit = true
			// call api
			const res = await CertificateAsset.submitMachineStep2(id, data)
			if (res.data) {
				await this.$toast.open({
					message: 'Lưu pháp lý tài sản thành công',
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
				this.step_active = 2
				this.status_text = 'Mới'
			} else if (res.error) {
				await this.$toast.open({
					message: `${res.error.message}`,
					type: 'error',
					position: 'top-right'
				})
			} else {
				await this.$toast.open({
					message: 'Lưu thất bại',
					type: 'error',
					position: 'top-right'
				})
			}
			this.isSubmit = false
		},
		// ----------------------------------------------------- STEP 3 ---------------------------------------------------------------- //
		async validateSubmitStep3 () {
			const isValid = await this.$refs.step_3.validate()
			if (isValid) {
				this.confirmSavePreviousStep(3)
			} else {
				await this.$toast.open({
					message: 'Vui lòng nhập đầy đủ các trường bắt buộc',
					type: 'error',
					position: 'top-right'
				})
			}
		},
		async handleSubmitStep_3 (id, data) {
			this.isSubmit = true
			// call api
			const res = await CertificateAsset.submitMachineStep3(id, data)
			if (res.data) {
				await this.$toast.open({
					message: 'Lưu Cơ sở thẩm định thành công',
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
				this.step_active = 2
				this.status_text = 'Mới'
			} else if (res.error) {
				await this.$toast.open({
					message: `${res.error.message}`,
					type: 'error',
					position: 'top-right'
				})
			} else {
				await this.$toast.open({
					message: 'Lưu thất bại',
					type: 'error',
					position: 'top-right'
				})
			}
			await this.$refs.wizard.nextTab()
			this.isSubmit = false
		},
		// ----------------------------------------------------- STEP 4 ---------------------------------------------------------------- //
		updateTotalOtherAsset () {
			this.status_text = 'Đang thực hiện'
			this.form.status = 2
			this.step_active = 3
		},
		async getDictionary () {
			let resp = this.$store.getters.dictionaries
			if (resp && resp.length === 0) {
				resp = await WareHouse.getDictionaries()
				store.commit(types.SET_DICTIONARIES, {...resp})
			}
			// this.manufacturer = await [...resp.data.nha_san_xuat]
			this.manufacturerCountryType = await [...resp.data.noi_san_xuat]
			this.fuelTypes = await [...resp.data.loai_nhien_lieu]
			let manufacturerTem = await [...resp.data.nha_san_xuat]
			let manufacturerOther = manufacturerTem.filter(item => item.dictionary_acronym === 'MMTB')
			let propertyTypesTem = await [...resp.data.loai_tai_san]
			let propertyTypesOther = propertyTypesTem.filter(item => item.acronym === 'MMTB')
			if (this.$route.name === 'certification_asset.machine.create') {
				this.form.step_1.asset_type_id = propertyTypesOther[0].id
			}
			this.manufacturer = manufacturerOther
			this.propertyTypes = propertyTypesOther
			this.propertyTypes.forEach(item => {
				item.description = this.formatSentenceCase(item.description)
			})
			this.manufacturer.forEach(item => {
				item.description = this.formatSentenceCase(item.description)
			})
			this.manufacturerCountryType.forEach(item => {
				item.description = this.formatSentenceCase(item.description)
			})
			this.fuelTypes.forEach(item => {
				item.description = this.formatSentenceCase(item.description)
			})
		},
		async getAppraiseOthers () {
			let resp = this.$store.getters.appraiseOther
			if (resp && resp.length === 0) {
				resp = await Certificate.getAppraiseOthers()
				store.commit(types.SET_APPRAISE_OTHER, {...resp})
			}
			let appraisalPurposeTem = [...resp.data.muc_dich_tham_dinh_gia]
			let appraisalFacilityTem = [...resp.data.co_so_tham_dinh]
			let appraisalPrinciplesTem = [...resp.data.nguyen_tac_tham_dinh]
			let approachTem = [...resp.data.cach_tiep_can_chi_phi]
			let methodsUsedTem = [...resp.data.phuong_phap_tham_dinh_su_dung]

			let purposeAppraisal = await appraisalPurposeTem.filter(item => item.dictionary_acronym.includes('DS'))
			let facilityAppraisal = await appraisalFacilityTem.filter(item => item.dictionary_acronym.includes('DS'))
			let principleAppraisal = await appraisalPrinciplesTem.filter(item => item.dictionary_acronym.includes('DS'))
			let approachMethod = await approachTem.filter(item => item.dictionary_acronym.includes('DS'))
			let usedMethod = await methodsUsedTem.filter(item => item.dictionary_acronym.includes('DS'))

			this.appraisalPurposes = purposeAppraisal
			this.appraisalFacility = facilityAppraisal
			this.appraisalPrinciples = principleAppraisal
			this.approach = approachMethod
			this.methodsUsed = usedMethod
			if (!this.form.step_3.other_infomation.basis_property_id) {
				this.findAppraisalFacility()
				this.findAppraisalPrinciples()
				this.findApproach()
				this.findMethodsUsed()
			} else if (this.$route.name === 'certification_asset.machine.create') {
				this.findAppraisalFacility()
				this.findAppraisalPrinciples()
				this.findApproach()
				this.findMethodsUsed()
			}
		},
		formatSentenceCase (phrase) {
			let text = phrase.toLowerCase()
			return text.charAt(0).toUpperCase() + text.slice(1)
		},
		findAppraisalFacility () {
			this.form.step_3.other_infomation.basis_property_id = this.appraisalFacility.find((appraisalFacility) => appraisalFacility.is_defaults === true).id
		},
		findAppraisalPrinciples () {
			this.form.step_3.other_infomation.principle_id = this.appraisalPrinciples.find((appraisalPrinciple) => appraisalPrinciple.is_defaults === true).id
		},
		findApproach () {
			this.form.step_3.other_infomation.approach_id = this.approach.find((approach) => approach.is_defaults === true).id
		},
		findMethodsUsed () {
			this.form.step_3.other_infomation.method_used_id = this.methodsUsed.find((methods) => methods.is_defaults === true).id
		}
	},
	beforeMount () {
		this.getDictionary()
		this.getAppraiseOthers()
	}
}
</script>
<style lang="scss" scoped>
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
