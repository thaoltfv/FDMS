<template>
	<div>
				<ValidationObserver
					tag="div"
					ref="step_1"
					@submit.prevent="validateSubmitStep1"
				>
				<div style="margin-bottom:60px">
					<Step1
						:idData="idData"
						:data="form.step_1"
						:typeAppraiseProperty="typeAppraiseProperty"
						:appraisersManager="appraisersManager"
						:appraisersControl="appraisersControl"
						:appraisalPurposes="appraisalPurposes"
						:appraisers="appraisers"
						:signAppraisers="signAppraisers"
						:customers="customers"
						:employeePerformance="employeePerformance"
						:employeeBusiness="employeeBusiness"
						:render_price_fee="render_price_fee"
						@handleChangeAppraiser="handleChangeAppraiser"
						@handleChangeAppraiserManager="handleChangeAppraiserManager"
						:userAppraiserId="userAppraiserId"
					/>
				</div>

					<div v-if="!isMobile()" class="btn-footer d-md-flex d-block justify-content-end align-items-center">
						<div class="d-lg-flex d-block button-contain">
							<button @click.prevent="$router.go(-1)" class="btn btn-white text-nowrap">
								<img src="../../assets/icons/ic_cancel.svg" style="margin-right: 12px" alt="save">
								Trở lại
							</button>
							<button :class="{ 'btn_loading disabled': isSubmit }" class="btn btn-white btn-orange text-nowrap" @click.prevent="validateSubmitStep1" type="submit">
								<img src="../../assets/icons/ic_save.svg" style="margin-right: 12px" alt="save"/>Lưu
							</button>
						</div>
					</div>
					<div v-else class="btn-footer d-md-flex d-block" style="bottom: 60px;">
						<div class="d-lg-flex d-block button-contain row" style="justify-content: space-around;display: flex!important;">
							<button @click.prevent="$router.go(-1)" class="btn btn-white text-nowrap col-6" style="width: unset;margin: 0;padding: 0;">
								<img src="../../assets/icons/ic_cancel.svg" style="margin-right: 12px" alt="save">
								Trở lại
							</button>
							<button :class="{ 'btn_loading disabled': isSubmit }" class="btn btn-white btn-orange text-nowrap col-6" @click.prevent="validateSubmitStep1" type="submit" style="width: unset;margin: 0;padding: 0;">
								<img src="../../assets/icons/ic_save.svg" style="margin-right: 12px" alt="save"/>Lưu
							</button>
						</div>
					</div>
				</ValidationObserver>
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
import Certificate from '@/models/Certificate'
import WareHouse from '@/models/WareHouse'
import CertificationBrief from '@/models/CertificationBrief'
import moment from 'moment'
import store from '@/store'
import * as types from '@/store/mutation-types'
import AppraiserCompany from '@/models/AppraiserCompany'
const jsonConfig = require('../../../config/workflow.json')

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
		Step1
	},
	data () {
		return {
			idData: null,
			isSubmit: false,
			render_price_fee: 12331,
			form: {
				step_1: {
					petitioner_name: 'Ông / Bà',
					petitioner_phone: '',
					petitioner_address: '',
					appraiser_confirm_id: null,
					appraiser_confirm: {
						name: ''
					},
					appraiser_manager_id: null,
					appraiser_manager: {
						name: ''
					},
					appraiser_control_id: null,
					appraiser_control: {
						name: ''
					},
					appraise_purpose_id: '',
					appraiser_id: '',
					appraiser: '',
					document_num: '',
					document_date: '',
					document_type: [],
					appraise_date: '',
					service_fee: 0,
					appraiser_sale_id: null,
					appraiser_sale: {
						name: ''
					},
					appraiser_perform_id: null,
					appraiser_perform: {
						name: ''
					},
					certificate_date: '',
					certificate_num: '',
					commission_fee: 0,
					note: '',
					petitioner_identity_card: '',
					customer: {
						name: '',
						address: '',
						phone: '',
						full_info: ''
					},
					status: 1,
					sub_status: 1
				},
				status: '2',
				created_by: ''
			},
			employeeBusiness: [],
			employeePerformance: [],
			customers: [],
			appraisersManager: [],
			appraisersControl: [],
			appraisers: [],
			signAppraisers: [],
			appraisalPurposes: [],
			appraisalFacility: [],
			approach: [],
			methodsUsed: [],
			appraisalPrinciples: [],
			typeAppraiseProperty: [],
			view: false,
			add: false,
			edit: false,
			deleted: false,
			accept: false,
			jsonConfig: jsonConfig,
			userAppraiserId: ''
		}
	},
	async created () {
		await this.getProfiles()
		const permission = this.$store.getters.currentPermissions
		// fix_permission
		await permission.forEach((value) => {
			if (value === 'VIEW_CERTIFICATE_BRIEF') {
				this.view = true
			}
			if (value === 'ADD_CERTIFICATE_BRIEF') {
				this.add = true
			}
			if (value === 'EDIT_CERTIFICATE_BRIEF') {
				this.edit = true
			}
			if (value === 'DELETE_CERTIFICATE_BRIEF') {
				this.deleted = true
			}
			if (value === 'ACCEPT_CERTIFICATE_BRIEF') {
				this.accept = true
			}
		})
		if ('id' in this.$route.query && this.$route.name === 'certification_brief.edit') {
			if (this.$route.meta['data']) { this.form.step_1 = Object.assign(this.form.step_1, { ...this.$route.meta['data'] }) }
			this.form.step_1.document_date = this.form.step_1.document_date ? moment(this.form.step_1.document_date).format('DD/MM/YYYY') : ''
			this.form.step_1.certificate_date = this.form.step_1.certificate_date ? moment(this.form.step_1.certificate_date).format('DD/MM/YYYY') : ''
			this.form.step_1.appraise_date = this.form.step_1.appraise_date ? moment(this.form.step_1.appraise_date).format('DD/MM/YYYY') : ''
			if (!this.form.step_1.customer) {
				this.form.step_1.customer = {
					name: '',
					address: '',
					phone: '',
					full_info: ''
				}
			}
			this.idData = this.form.step_1.id
		}
	},

	methods: {
		async getProfiles () {
			const profile = this.$store.getters.profile
			this.form.created_by = profile.data.user.id
			this.current_create_by = profile.data.user.id
			if (profile.data.user.appraiser) {
				this.userAppraiserId = profile.data.user.appraiser.id
			}
		},
		async getAppraisers () {
			const resp = await Certificate.getAppraisers()
			const appraiserCompany = await AppraiserCompany.detail()
			let dataAppraise = [...resp.data]
			let managerId = await appraiserCompany.data.data[0].appraiser.id
			this.employeePerformance = dataAppraise
			this.employeeBusiness = dataAppraise
			this.appraisersControl = dataAppraise
			this.appraisersManager = dataAppraise.filter(item => item.is_legal_representative === 1)
			this.form.step_1.appraiser_manager_id = await this.appraisersManager[0].id
			let appraiser = dataAppraise.filter(item => item.appraiser_number !== '')
			this.appraisers = appraiser
			// if (this.form && this.form.step_1.appraiser_manager_id) {
			// 	this.appraisers = appraiser.filter(item => item.id !== this.form.step_1.appraiser_manager_id)
			// }
			if (this.form && this.form.step_1.appraiser_id) {
				const filterData = appraiser.filter(item => item.id !== this.form.step_1.appraiser_id && item.id !== this.form.step_1.appraiser_manager_id)
				this.signAppraisers = filterData
			} else {
				this.signAppraisers = this.appraisers
			}
		},
		formatSentenceCase (phrase) {
			let text = phrase.toLowerCase()
			return text.charAt(0).toUpperCase() + text.slice(1)
		},
		async getDictionary () {
			let resp = this.$store.getters.dictionaries
			if (resp && resp.length === 0) {
				resp = await WareHouse.getDictionaries()
				store.commit(types.SET_DICTIONARIES, {...resp})
			}
			this.typeAppraiseProperty = [...resp.data.loai_tai_san]
			this.typeAppraiseProperty.forEach(item => {
				item.description = this.formatSentenceCase(item.description)
			})
		},
		async getAppraiseOthers () {
			const resp = await Certificate.getAppraiseOthers()
			this.appraisalPurposes = [...resp.data.muc_dich_tham_dinh_gia]
			this.appraisalFacility = [...resp.data.co_so_tham_dinh]
			this.appraisalPrinciples = [...resp.data.nguyen_tac_tham_dinh]
			this.approach = [...resp.data.cach_tiep_can_chi_phi]
			this.methodsUsed = [...resp.data.phuong_phap_tham_dinh_su_dung]
		},
		async getCustomer () {
			const res = await CertificationBrief.getCustomer()
			if (res.data) {
				this.customers = res.data
			}
			this.render_price_fee += 1
		},
		async validateSubmitStep1 () {
			this.form.step_1.status = 1
			this.form.step_1.sub_status = 1
			const isValid = await this.$refs.step_1.validate()
			if (isValid) {
				if ('id' in this.$route.query && this.$route.name === 'certification_brief.edit') {
					await this.handleSubmitStep_1(this.form.step_1, this.idData)
				} else if (this.$route.name === 'certification_brief.create') {
					await this.handleSubmitStep_1(this.form.step_1)
				}
			} else {
				this.$toast.open({
					message: 'Vui lòng nhập đầy đủ các trường bắt buộc',
					type: 'error',
					position: 'top-right'
				})
			}
		},
		async handleSubmitStep_1 (dataStep1, id = '') {
			this.isSubmit = true
			const res = await CertificationBrief.submitStep1CertificationBrief(dataStep1, id)
			if (res.data) {
				this.idData = res.data.id
				this.$toast.open({
					message: 'Lưu hồ sơ thẩm định thành công',
					type: 'success',
					position: 'top-right',
					duration: 3000
				})
				await this.$router.push({ name: 'certification_brief.detail', query: { id: res.data.id } }).catch((_) => {})
				this.isSubmit = false
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
		handleChangeAppraiser (event) {
			// if (event) {
			// 	this.form.step_1.appraiser_confirm_id = ''
			// 	if (event === this.form.appraiser_manager_id) {
			// 		this.signAppraisers = this.appraisers
			// 	} else {
			// 		const filterData = this.appraisers.filter(item => item.id !== event)
			// 		this.signAppraisers = filterData
			// 	}
			// } else {
			// 	this.form.step_1.appraiser_confirm_id = ''
			// 	this.signAppraisers = this.appraisers
			// }
		},
		async handleChangeAppraiserManager (event) {
			// const resp = await Certificate.getAppraisers()
			// let dataAppraise = [...resp.data]
			// let appraiser = dataAppraise.filter(item => item.appraiser_number !== '')
			// if (event){
			// 	// console.log('có event')
			// 	this.appraisers = appraiser.filter(item => item.id !== event)
			// } else {
			// 	// console.log('không evant')
			// 	this.appraisers = appraiser
			// }

		},
		isMobile () {
			if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
				return true
			} else {
				return false
			}
		}
	},
	mounted () {
		// // console.log(this.profile)
	},
	async beforeMount () {
		this.getAppraisers()
		this.getAppraiseOthers()
		this.getProfiles()
		this.getCustomer()
		this.getDictionary()
	}
}
</script>
<style scoped lang="scss">
.certification-asset {
	padding-left: 16px;
	// padding-right: 16px;
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

</style>
