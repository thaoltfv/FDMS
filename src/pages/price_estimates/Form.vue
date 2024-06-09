<template>
	<div v-if="!isMobile" class="certification-asset">
		<form-wizard
			:key="key_render_formwizard"
			ref="wizard"
			color="#99D161"
			:title="`TSSB${dataForm.id ? `_${dataForm.id}` : ''}`"
			:subtitle="status_text"
			layout="vertical"
			finish-button-text="Hoàn Thành"
			back-button-text="Trở lại"
			next-button-text="Lưu"
			:startIndex="miscVariable.step_active || 0"
			@on-change="handleChange"
			class="vertical-steps steps-transparent"
			:class="{ step3: isStep3Active }"
		>
			<div
				class="wizard-custom-info"
				v-if="
					dataForm.appraise_id ||
						dataForm.pre_certificate_id ||
						dataForm.apartment_asset_id
				"
			>
				<div class="col-12">
					<!-- <div class="row d-flex">
						<p class="mb-1">Version :</p>
						<p class="mb-1">{{ dataForm.max_version }}</p>
					</div> -->
					<div
						class="row d-flex"
						v-if="dataForm.appraise_id || dataForm.apartment_asset_id"
					>
						<p class="mb-1">Mã TSTĐ :</p>
						<a
							class="mb-1"
							:href="
								dataForm.appraise_id
									? `/certification_asset/real-estate/detail?id=${dataForm.appraise_id}`
									: dataForm.apartment_asset_id
									? `/certification_asset/apartment/detail?id=${dataForm.apartment_asset_id}`
									: ''
							"
							target="_blank"
							>{{ dataForm.appraise_id || dataForm.apartment_asset_id }}</a
						>
					</div>
					<div class="row d-flex" v-if="dataForm.pre_certificate_id">
						<p class="mb-1">Mã HSSB :</p>
						<a
							class="mb-1"
							:href="
								`/pre_certification/detail?id=${dataForm.pre_certificate_id}`
							"
							target="_blank"
							>{{ dataForm.pre_certificate_id }}</a
						>
					</div>
					<!-- <div class="">
						<p class="mb-1">Người được chỉnh sửa :</p>
						<div>
							<p class="mb-1">
								-
								{{ dataForm.createdBy ? dataForm.createdBy.name : "" }}
							</p>
						</div>
					</div> -->
				</div>
			</div>
			<tab-content title="Thông tin chung" icon="">
				<ValidationObserver
					tag="div"
					ref="step_1"
					@submit.prevent="priceEstimateStore.validateSubmitStep1;"
				>
					<Step1 :isEdit="isEdit" :key="miscInfo.key_step_1" />
					<div
						class="btn-footer d-md-flex d-block justify-content-end align-items-center"
					>
						<div class="d-lg-flex d-block button-contain">
							<button
								@click.prevent="handleChangeBack"
								class="btn btn-white text-nowrap"
							>
								<img
									src="@/assets/icons/ic_cancel.svg"
									style="margin-right: 12px"
									alt="save"
								/>Trở lại
							</button>
							<button
								v-if="!dataForm.appraise_id && !dataForm.apartment_id"
								class="btn btn-white btn-orange text-nowrap"
								:class="{ 'btn_loading disabled': isSubmit }"
								@click.prevent="priceEstimateStore.validateSubmitStep1"
								type="submit"
							>
								<img
									src="@/assets/icons/ic_save.svg"
									style="margin-right: 12px"
									alt="save"
								/>Lưu
							</button>
							<!-- <button
								v-if="isEdit && isCancelEnable"
								@click.prevent="handleCancelProperty()"
								class="btn btn-white text-nowrap"
							>
								<img
									src="@/assets/icons/ic_destroy.svg"
									style="margin-right: 12px"
									alt="cancel"
								/>Hủy tài sản
							</button> -->
						</div>
					</div>
				</ValidationObserver>
			</tab-content>

			<tab-content title="Tài sản so sánh" icon="">
				<ValidationObserver tag="div" ref="step_2">
					<!-- <Step6 /> -->
					<Step2 :isEdit="isEdit" :key="miscInfo.key_step_2" />
					<div
						class="btn-footer d-md-flex d-block justify-content-end align-items-center"
					>
						<div class="d-lg-flex d-block button-contain">
							<button
								@click.prevent="handleChangeBack"
								class="btn btn-white text-nowrap"
							>
								<img
									src="@/assets/icons/ic_cancel.svg"
									style="margin-right: 12px"
									alt="save"
								/>Trở lại
							</button>
							<button
								v-if="
									(edit || add) &&
										!dataForm.appraise_id &&
										!dataForm.apartment_id
								"
								class="btn btn-white btn-orange text-nowrap"
								:class="{ 'btn_loading disabled': isSubmit }"
								@click.prevent="validateSubmitStep2"
								type="submit"
							>
								<img
									src="@/assets/icons/ic_save.svg"
									style="margin-right: 12px"
									alt="save"
								/>Lưu
							</button>
							<!-- <button
								v-if="isEdit && isCancelEnable"
								@click.prevent="handleCancelProperty()"
								class="btn btn-white text-nowrap"
							>
								<img
									src="@/assets/icons/ic_destroy.svg"
									style="margin-right: 12px"
									alt="cancel"
								/>Hủy tài sản
							</button> -->
						</div>
					</div>
				</ValidationObserver>
			</tab-content>

			<tab-content title="Giá trị tài sản" icon=""
				><ValidationObserver tag="div" ref="step_3">
					<!-- <Step6 /> -->
					<Step3 :isEdit="isEdit" :key="miscInfo.key_step_3" />
					<div
						class="btn-footer d-md-flex d-block justify-content-end align-items-center"
					>
						<div class="d-lg-flex d-block button-contain">
							<button
								@click.prevent="handleChangeBack"
								class="btn btn-white text-nowrap"
							>
								<img
									src="@/assets/icons/ic_cancel.svg"
									style="margin-right: 12px"
									alt="save"
								/>Trở lại
							</button>
							<button
								v-if="
									(edit || add) &&
										!dataForm.appraise_id &&
										!dataForm.apartment_id
								"
								class="btn btn-white btn-orange text-nowrap"
								:class="{ 'btn_loading disabled': isSubmit }"
								@click.prevent="validateSubmitStep3"
								type="submit"
							>
								<img
									src="@/assets/icons/ic_save.svg"
									style="margin-right: 12px"
									alt="save"
								/>Lưu
							</button>
							<!-- <button
								v-if="isEdit && isCancelEnable"
								@click.prevent="handleCancelProperty()"
								class="btn btn-white text-nowrap"
							>
								<img
									src="@/assets/icons/ic_destroy.svg"
									style="margin-right: 12px"
									alt="cancel"
								/>Hủy tài sản
							</button> -->
						</div>
					</div>
				</ValidationObserver>
			</tab-content>
		</form-wizard>
		<ModalNotificationAppraisal
			v-if="miscVariable.showConfirmEdit"
			@cancel="miscVariable.showConfirmEdit = false"
			v-bind:notification="miscVariable.messageConfirm"
			@action="priceEstimateStore.confirmEditStep"
		/>
	</div>
</template>

<script>
import { FormWizard, TabContent } from "vue-form-wizard";
import "vue-form-wizard/dist/vue-form-wizard.min.css";
import { BCard, BRow, BCol, BFormGroup, BFormInput } from "bootstrap-vue";
import Step1 from "./component/Step1";
import Step2 from "./component/Step2";
import Step3 from "./component/Step3";
import ModalNotificationAppraisal from "@/components/Modal/ModalNotificationAppraisal";
import WareHouse from "@/models/WareHouse";
import Certificate from "@/models/Certificate";
import { COMPARISON } from "@/enum/comparison-factor.enum";
import AppraiseData from "@/models/AppraiseData";
import CertificateAsset from "@/models/CertificateAsset";
import moment from "moment";
import store from "@/store";
import * as types from "@/store/mutation-types";

import { ref } from "vue";
import { storeToRefs } from "pinia";
import { usePriceEstimatesStore } from "@/store/priceEstimates";
export default {
	name: "Index",
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
		ModalNotificationAppraisal
	},
	data() {
		return {
			landType: [],
			key_render_formwizard: 70000,
			topographic: [],
			landShapes: [],
			socialSecurities: [],
			landType: [],
			businesses: [],
			paymentMethods: [],
			conditions: [],
			fengshuies: [],
			zones: [],
			materials: [],
			roughes: [],
			points: [],
			imageDescriptions: [],
			status_text: "",
			isStep3Active: false,
			view: false,
			add: false,
			edit: false,
			delete: false,
			accept: false
		};
	},
	setup() {
		const checkMobile = () => {
			if (
				/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
					navigator.userAgent
				)
			) {
				return true;
			} else {
				return false;
			}
		};
		const isMobile = ref(checkMobile());
		const isEdit = ref(false);
		const priceEstimateStore = usePriceEstimatesStore();
		const {
			priceEstimates,
			isSubmit,
			miscInfo,
			configThis,
			miscVariable
		} = storeToRefs(priceEstimateStore);
		priceEstimateStore.getProvinces();
		const dataForm = ref(priceEstimates.value);
		const step_1 = ref(null);

		return {
			isMobile,
			isSubmit,
			dataForm,
			miscInfo,
			miscVariable,
			priceEstimateStore,
			step_1,
			configThis,
			isEdit
		};
	},
	async created() {
		await this.getProfiles();
		const permission = this.$store.getters.currentPermissions;

		// fix_permission
		await permission.forEach(value => {
			if (value === "VIEW_CERTIFICATE_ASSET") {
				this.view = true;
			}
			if (value === "ADD_CERTIFICATE_ASSET") {
				this.add = true;
			}
			if (value === "EDIT_CERTIFICATE_ASSET") {
				this.edit = true;
			}
			if (value === "DELETE_CERTIFICATE_ASSET") {
				this.deleted = true;
			}
			if (value === "ACCEPT_CERTIFICATE_ASSET") {
				this.accept = true;
			}
		});
		if (
			"id" in this.$route.query &&
			this.$route.name === "price_estimates.edit"
		) {
			this.isEdit = true;
		} else if (
			"id" in this.$route.params &&
			this.$route.name === "price_estimates.create"
		) {
		}
		this.configThis.wizard = this.$refs.wizard;
		this.configThis.step_1 = this.$refs.step_1;
		if (this.$route.name === "price_estimates.edit") {
			await this.$refs.wizard.tabs.forEach((tab, index) => {
				if (index < this.miscVariable.step_active) {
					tab.checked = true;
				} else {
					tab.checked = false;
				}
			});
			if (this.miscVariable.step_active === 2) {
				await this.$refs.wizard.changeTab(0, 1);
			} else {
				await this.$refs.wizard.changeTab(0, this.miscVariable.step_active);
			}

			if (this.$route.params.step || this.$route.params.step === 0) {
				await this.$refs.wizard.changeTab(0, this.$route.params.step);
			}
		}
	},
	methods: {
		async validateSubmitStep2() {
			const isValid = await this.$refs.step_2.validate();
			if (isValid) {
				this.priceEstimateStore.validateSubmitStep2();
			} else {
				this.$toast.open({
					message: "Vui lòng nhập đầy đủ các trường bắt buộc",
					type: "error",
					position: "top-right"
				});
			}
		},
		async validateSubmitStep3() {
			const isValid = await this.$refs.step_3.validate();
			if (isValid) {
				this.priceEstimateStore.validateSubmitStep3();
			} else {
				this.$toast.open({
					message: "Vui lòng nhập đầy đủ các trường bắt buộc",
					type: "error",
					position: "top-right"
				});
			}
		},
		async handleChangeBack() {
			// this.$refs.wizard.prevTab()
			return this.$router.go(-1);
		},
		async handleChange(prevIndex, nextIndex) {
			if (nextIndex === 5 && this.isAutomation) {
				const response = await CertificateAsset.getAssetAutomationStep6(
					this.dataForm.id
				);
				if (response.data) {
					this.form.step_6.assets_general = await response.data.assets;
					this.form.step_6.comparison_factor = await response.data
						.comparison_factor;
					this.form.step_6.map_img = "";
					this.distance_max = response.data.distance_max;
					this.filter_year = response.data.filter_year;
					if (response.data.assets.length === 0) {
						this.$toast.open({
							message: `${response.data.message}`,
							type: "success",
							position: "top-right",
							duration: 6000
						});
					} else {
						this.$toast.open({
							message: `Đã tìm được ${response.data.assets.length} tài sản so sánh`,
							type: "success",
							position: "top-right",
							duration: 6000
						});
					}
					this.isAutomation = false;
				}
			}
			let currentIndex = nextIndex + 1;
			if (currentIndex === 1) {
				this.miscInfo.key_step_1 += 1;
			}
			if (currentIndex === 2) {
				this.miscInfo.key_step_2 += 1;
			}
			if (currentIndex === 3) {
				this.miscInfo.key_step_3 += 1;
			}
			// this.key_step_1 += 1
			// this.key_step_2 += 1
			// this.key_step_6 += 1
			this.isStep7Active = !!(
				this.miscVariable.step_active === 7 && nextIndex === 6
			);
		},
		async getProfiles() {
			const userId = this.$store.getters.profile
				? this.$store.getters.profile.data.user.id
				: this.$store.getters.userId;
			// this.dataForm.created_by = userId;
			this.miscInfo.current_create_by = userId;
		}
	}
};
</script>

<style scoped lang="scss">
.certification-asset {
	@media (max-width: 449px) {
		margin-bottom: 100px;
	}
	.step3 {
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
	content: "";
	display: inline-block;
	vertical-align: text-bottom;
	border: 1px solid wheat;
	border-right-color: transparent;
	border-radius: 50%;
	color: #ffffff;
	position: absolute;
	width: 1rem;
	height: 1rem;
	left: calc(50% - 0.5rem);
	top: calc(50% - 0.5rem);
	-webkit-animation: spinner-border 0.75s linear infinite;
	animation: spinner-border 0.75s linear infinite;
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
