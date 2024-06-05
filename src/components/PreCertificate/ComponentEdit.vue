<template
	><div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-title">
					<div class="d-flex justify-content-between align-items-center">
						<h3 class="title">Chỉnh sửa yêu cầu sơ bộ</h3>
						<div class="color_content card-status">
							{{ dataPC && dataPC.id ? `YCSB_${dataPC.id}` : "YCSB" }} | Mới
						</div>
					</div>
				</div>
				<div class="card-body card-info row" v-show="showCardDetailAppraise">
					<div class="col-md-12 order-1 col-lg-6">
						<InputTextPrefixCustom
							id="petitioner_name"
							placeholder="Ông / Bà"
							v-model="dataPC.petitioner_name"
							vid="petitioner_name"
							:iconUser="true"
							:showIcon="true"
							:requiredIcon="true"
							label="Tên khách hàng yêu cầu (trên chứng thư)"
							rules="required"
							class="form-group-container input_certification_brief"
						/>
						<div class="row justify-content-between">
							<InputTextPrefixCustomIcon
								id="petitioner_identity_card"
								placeholder="Nhập MST/CMND/CCCD/Passport"
								v-model="dataPC.petitioner_identity_card"
								class="form-group-container col-sm-12 col-md-6"
								vid="petitioner_identity_card"
								icon="ic_id_card_2"
								:showCustomIcon="true"
								label="MST/CMND/CCCD/Passport"
							/>
							<InputTextPrefixCustom
								id="petitioner_phone"
								placeholder="Nhập số điện thoại"
								v-model="dataPC.petitioner_phone"
								class="form-group-container col-sm-12 col-md-6"
								vid="petitioner_phone"
								:iconPhone="true"
								:showIcon="true"
								label="Điện thoại"
							/>
						</div>
						<InputTextPrefixCustom
							id="petitioner_address"
							placeholder="Nhập địa chỉ của khách hàng"
							v-model="dataPC.petitioner_address"
							vid="petitioner_address"
							:iconLocation="true"
							:showIcon="true"
							label="Địa chỉ"
							class="form-group-container input_certification_brief"
						/>

						<div class="row justify-content-between">
							<InputCategory
								v-model="dataPC.appraise_purpose_id"
								class="form-group-container input_certification_brief  col-sm-12 col-md-12"
								vid="appraise_purpose_id"
								:requiredIcon="true"
								label="Mục đích thẩm định"
								rules="required"
								:options="optionsAppraisalPurposes"
								@change="handleChangeAppraisePurpose"
							/>
						</div>
					</div>
					<div class="col-md-12 order-3 order-lg-2 col-lg-6">
						<div class="row justify-content-between">
							<InputCurrency
								:key="keyRender"
								v-model="dataPC.total_service_fee"
								vid="total_service_fee"
								:max="99999999999999"
								label="Tổng phí dịch vụ"
								class="form-group-container col-sm-12 col-md-6"
								@change="paidCompute($event, null, true)"
							/>
							<InputPercent
								:key="keyRender"
								v-model="dataPC.commission_fee"
								label="Chiết khấu"
								vid="test"
								:max="100"
								:decimal="0"
								rules="required"
								class="form-group-container col-sm-12 col-md-6"
								@change="dataPC.commission_fee = $event"
							/>
						</div>

						<div class="row justify-content-between">
							<InputCategory
								v-model="dataPC.pre_type_id"
								vid="pre_type_id"
								:requiredIcon="true"
								rules="required"
								label="Loại sơ bộ"
								class="form-group-container col-sm-12 col-md-6"
								:options="optionsPreTypes"
							/>
							<InputDatePicker
								v-model="dataPC.pre_date"
								vid="pre_date"
								label="Thời điểm sơ bộ"
								placeholder="Ngày / tháng / năm"
								:requiredIcon="true"
								rules="required"
								:formatDate="'DD/MM/YYYY'"
								class="form-group-container col-sm-12 col-md-6"
								@change="handleChangeTime"
							/>
						</div>
						<div>
							<InputTextarea
								:rows="4"
								:disableInput="false"
								v-model="dataPC.pre_asset_name"
								label="Ghi chú"
								:requiredIcon="true"
								class="form-group-container"
								rules="required"
							/>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-6">
			<div
				class="card"
				:style="isMobile ? { 'margin-bottom': '70px' } : { height: '56vh' }"
			>
				<div class="card-title">
					<div class="d-flex justify-content-between align-items-center">
						<h3 class="title">Đối tác</h3>
						<img
							class="img-dropdown"
							:class="!showCardDetailTraffic ? 'img-dropdown__hide' : ''"
							src="@/assets/images/icon-btn-down.svg"
							alt="dropdown"
							@click="showCardDetailTraffic = !showCardDetailTraffic"
						/>
					</div>
				</div>
				<div class="card-body card-info" v-show="showCardDetailTraffic">
					<div class="d-flex-column">
						<div class="row">
							<InputCategory
								v-model="dataPC.customer_group_id"
								vid="customer_group_id"
								label="Nhóm đối tác"
								class="form-group-container col-12"
								:options="optionsCustomerGroup"
							/>
							<InputCategorySearch
								vid="appraiser"
								class="form-group-container col-12"
								label="Tìm đối tác"
								@change="handleChangeCustomer"
								@search="debounceSearchCustomer"
								:options="optionsCustomer"
							/>
						</div>
						<div class="row">
							<div class="col-lg-6">
								<InputTextPrefixCustom
									id="customer_name"
									placeholder="Ông / Bà"
									v-model="dataPC.customer.name"
									vid="customer_name"
									:iconUser="true"
									:showIcon="true"
									label="Họ tên đối tác"
									class="form-group-container input_certification_brief"
								/>
							</div>
							<div class="col-lg-6">
								<InputTextPrefixCustom
									id="customer_phone"
									placeholder="Nhập số điện thoại"
									v-model="dataPC.customer.phone"
									class="form-group-container input_certification_brief"
									vid="customer_phone"
									:iconPhone="true"
									:showIcon="true"
									label="Điện thoại"
								/>
							</div>
						</div>
						<InputTextPrefixCustom
							id="customer_address"
							placeholder="Nhập địa chỉ của đối tác"
							v-model="dataPC.customer.address"
							vid="customer_address"
							:iconLocation="true"
							:showIcon="true"
							label="Địa chỉ"
							class="form-group-container input_certification_brief"
						/>
					</div>
				</div>
			</div>
		</div>
		<div class="col-6">
			<div
				class="card"
				:style="isMobile ? { 'margin-bottom': '70px' } : { height: '56vh' }"
			>
				<div class="card-title">
					<div class="d-flex justify-content-between align-items-center">
						<h3 class="title">Tổ thẩm định</h3>
						<img
							class="img-dropdown"
							:class="
								!showCardDetailEconomicAndSocial ? 'img-dropdown__hide' : ''
							"
							src="@/assets/images/icon-btn-down.svg"
							alt="dropdown"
							@click="
								showCardDetailEconomicAndSocial = !showCardDetailEconomicAndSocial
							"
						/>
					</div>
				</div>
				<div
					class="card-body card-info"
					v-show="showCardDetailEconomicAndSocial"
				>
					<div class="d-flex-column">
						<div class="row justify-content-between">
							<InputCategory
								v-model="appraiser_sale_compute"
								vid="appraiser_sale_id"
								label="Nhân viên kinh doanh"
								rules="required"
								:requiredIcon="true"
								class="form-group-container col-12"
								:options="optionsAppraiserSales"
							/>
							<InputCategory
								v-model="business_manager_compute"
								vid="business_manager_id"
								label="Quản lý nghiệp vụ"
								rules="required"
								:requiredIcon="true"
								class="form-group-container col-12"
								:options="optionsBusinessManager"
							/>
						</div>
						<div class="row justify-content-between">
							<InputCategory
								v-model="appraiser_perform_compute"
								vid="appraiser_perform_id"
								label="Chuyên viên thực hiện"
								:rules="dataPC.status > 1 ? 'required' : ''"
								:requiredIcon="dataPC.status > 1"
								class="form-group-container col-12"
								:options="optionsAppraiserPerformance"
							/>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div
			v-if="dataPC.id && dataPC.status >= 2"
			class="col-6"
			:style="isMobile ? { padding: '0' } : {}"
		>
			<div class="card">
				<div class="card-title">
					<div class="d-flex justify-content-between align-items-center">
						<div class="row d-flex justify-content-between align-items-center">
							<h3 class="title">Kết quả sơ bộ</h3>
						</div>

						<img
							class="img-dropdown"
							:class="!showCardDetailFileResult ? 'img-dropdown__hide' : ''"
							src="@/assets/images/icon-btn-down.svg"
							alt="dropdown"
							@click="showCardDetailFileResult = !showCardDetailFileResult"
						/>
					</div>
				</div>
				<OtherFile
					v-show="showCardDetailFileResult"
					type="Result"
					@action="showCardDetailFileResult = true"
				/>
			</div>
		</div>
		<!-- <div v-if="dataPC.id && dataPC.status >= 2" class="col-6">
			<div class="card" :style="isMobile ? { 'margin-bottom': '70px' } : {}">
				<div class="card-title">
					<div class="d-flex justify-content-between align-items-center">
						<h3 class="title">Thông tin thanh toán</h3>
						<img
							class="img-dropdown"
							:class="!showCardDetailPayments ? 'img-dropdown__hide' : ''"
							src="@/assets/images/icon-btn-down.svg"
							alt="dropdown"
							@click="showCardDetailPayments = !showCardDetailPayments"
						/>
					</div>
				</div>
				<div class="card-body card-info" v-show="showCardDetailPayments">
					<div class="d-flex-column">
						<div
							class="row "
							v-for="(payment, index) in dataPC.payments"
							:key="index"
							v-if="!payment.is_deleted"
						>
							<div class="row justify-content-between col-10">
								<InputDatePicker
									v-model="payment.pay_date"
									vid="pay_date"
									label="Ngày thanh toán"
									placeholder="Ngày / tháng / năm"
									rules="required"
									:formatDate="'DD/MM/YYYY'"
									class="form-group-container col-sm-12 col-md-6"
									@change="payment.pay_date = $event"
								/>
								<InputCurrency
									v-model="payment.amount"
									vid="amount"
									:max="99999999999999"
									label="Giá trị thanh toán"
									class="form-group-container col-sm-12 col-md-6"
									@change="paidCompute($event, payment)"
								/>
							</div>
							<div class="mt-5 col-2 d-flex  justify-content-between">
								<span
									style="font-style: italic; color: orange; cursor: pointer"
									@click="addPayment"
									>+Thêm</span
								>
								<span
									v-if="
										dataPC.payments.filter(
											payment =>
												payment.is_deleted === undefined || !payment.is_deleted
										).length > 1
									"
									style="font-style: italic; color: red; cursor: pointer"
									@click="removePayment(index, payment)"
								>
									-Xóa
								</span>
							</div>
						</div>

						<div class="row justify-content-between mt-4">
							<strong class="margin_content_inline">Đã thanh toán:</strong>
							<InputCurrency
								:key="keyRender"
								v-model="dataPC.paid"
								vid="amount"
								:disabled="true"
								:max="99999999999999"
								class="form-group-container col-6 mt-n1"
							/>
						</div>
						<div class="row justify-content-between mt-4">
							<strong class="margin_content_inline">Còn nợ:</strong>
							<InputCurrency
								:key="keyRender"
								v-model="dataPC.debtRemain"
								vid="amount"
								:disabled="true"
								:max="99999999999999"
								class="form-group-container col-6 mt-n1"
							/>
						</div>
					</div>
				</div>
			</div>
		</div> -->
		<div
			v-if="dataPC.id"
			class="col-12"
			:style="isMobile ? { padding: '0' } : {}"
		>
			<OtherFile type="Appendix" />
		</div>
	</div>
</template>
<script>
import { ref } from "vue";
import { storeToRefs } from "pinia";
import { usePreCertificateStore } from "@/store/preCertificate";

import { debounce } from "lodash-es";
import InputCategoryMulti from "@/components/Form/InputCategoryMulti";
import InputPercent from "@/components/Form/InputPercent";
import InputDatePicker from "@/components/Form/InputDatePicker";
import InputCategory from "@/components/Form/InputCategory";
import InputCategorySearch from "@/components/Form/InputCategorySearch";
import InputText from "@/components/Form/InputText";
import InputTextarea from "@/components/Form/InputTextarea";
import InputTextPrefixCustom from "@/components/Form/InputTextPrefixCustom";
import InputCurrency from "@/components/Form/InputCurrency";
import InputTextPrefixCustomIcon from "@/components/Form/InputTextPrefixCustomIcon";
import OtherFile from "./OtherFile.vue";
import moment from "moment";
export default {
	props: {
		routeId: {
			type: String
		}
	},
	components: {
		OtherFile,
		InputCategory,
		InputCategorySearch,
		InputText,
		InputTextarea,
		InputTextPrefixCustom,
		InputDatePicker,
		InputCurrency,
		InputPercent,
		InputCategoryMulti,
		InputTextPrefixCustomIcon
	},
	setup(props) {
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

		const theme = ref({
			navItem: "#000000",
			navActiveItem: "#FAA831",
			slider: "#FAA831",
			arrow: "#000000"
		});
		const showCardDetailFileResult = ref(true);
		const showCardDetailAppraise = ref(true);
		const showCardDetailFile = ref(true);
		const showCardDetailTraffic = ref(true);
		const showCardDetailEconomicAndSocial = ref(true);
		const showCardDetailPayments = ref(true);
		const showCardDetailImage = ref(true);
		const customers_step_1 = ref(null);
		const form = ref({
			appraise_date: "",
			appraise_purpose_id: "",
			certificate_date: "",
			document_date: "",
			id: "",
			base: "",
			num: "",
			date: "",
			num_certificate: "",
			date_certificate: "",
			customer_request: "",
			phone: "",
			address: "",
			asset_type_id: "",
			full_address: "",
			province_id: "",
			district_id: "",
			ward_id: "",
			street_id: "",
			distance_id: "",
			coordinates: "",
			land_no: "",
			doc_no: "",
			topographic: "",
			land_no_old: "",
			doc_no_old: "",
			status: 1,
			sub_status: 1
		});

		const keyRender = ref(0);
		const preCertificateStore = usePreCertificateStore();

		const { dataPC, lstDataConfig, preCertificateOtherDocuments } = storeToRefs(
			preCertificateStore
		);
		const handleChangeTime = event => {
			dataPC.value.pre_date = event;
		};
		const getStartData = async () => {
			preCertificateStore.resetData();
			dataPC.value = await preCertificateStore.getPreCertificate(props.routeId);
			await handleChangeTime(
				moment(dataPC.value.pre_date).format("DD/MM/YYYY")
			);
			keyRender.value++;
		};
		getStartData();
		const handleChangeAppraisePurpose = event => {
			dataPC.value.appraise_purpose_id = event;
		};
		const debounceSearchCustomer = debounce(function(e) {
			if (e) {
				// preCertificateStore.getCustomer(e);
			}
		}, 400);

		const handleChangeCustomer = async event => {
			let bindCustomer = await lstDataConfig.value.customers.filter(
				item => item.id === event
			);
			if (bindCustomer && bindCustomer.length > 0) {
				dataPC.value.customer_id = bindCustomer[0].id;
				dataPC.value.customer.name = bindCustomer[0].name;
				dataPC.value.customer.address = bindCustomer[0].address;
				dataPC.value.customer.phone = bindCustomer[0].phone;
			}
		};
		const paidCompute = (
			event,
			payment,
			booltotal_service_fee = false,
			runCompute = false
		) => {
			if (!runCompute) {
				if (!booltotal_service_fee) payment.amount = event;
				if (booltotal_service_fee) dataPC.value.total_service_fee = event;
			}
			let debt_remain = dataPC.value.total_service_fee;
			let paid = 0;
			for (let index = 0; index < dataPC.value.payments.length; index++) {
				const element = dataPC.value.payments[index];
				if (element.is_deleted) continue;
				debt_remain -= element.amount;
				paid += parseFloat(element.amount);
			}
			dataPC.value.debtRemain = debt_remain;
			dataPC.value.paid = paid;

			keyRender.value++;
		};

		const addPayment = () => {
			dataPC.value.payments.push({
				pre_date: null,
				amount: 0
			});
		};
		const removePayment = (index, payment) => {
			if (!payment.id) dataPC.value.payments.splice(index, 1);
			else {
				Vue.set(payment, "is_deleted", true);
			}
			paidCompute(0, 0, false, true);
		};

		return {
			keyRender,
			isMobile,
			theme,
			showCardDetailAppraise,
			showCardDetailFile,
			showCardDetailTraffic,
			showCardDetailEconomicAndSocial,
			showCardDetailPayments,
			showCardDetailImage,
			customers_step_1,
			form,
			dataPC,
			lstDataConfig,
			preCertificateOtherDocuments,
			preCertificateStore,
			showCardDetailFileResult,

			handleChangeAppraisePurpose,
			handleChangeCustomer,
			debounceSearchCustomer,
			paidCompute,
			addPayment,
			removePayment,
			handleChangeTime
		};
	},
	computed: {
		appraiser_perform_compute: {
			// getter
			get: function() {
				if (this.lstDataConfig.appraiser_performances.length > 0) {
					return this.dataPC.appraiser_perform_id;
				} else {
					return this.dataPC.appraiser_performance.name;
				}
			},
			// setter
			set: function(newValue) {
				this.dataPC.appraiser_perform_id = newValue;
			}
		},
		business_manager_compute: {
			// getter
			get: function() {
				if (this.lstDataConfig.appraiser_business_managers.length > 0) {
					return this.dataPC.business_manager_id;
				} else {
					return this.dataPC.appraiser_business_manager.name;
				}
			},
			// setter
			set: function(newValue) {
				this.dataPC.business_manager_id = newValue;
			}
		},
		appraiser_sale_compute: {
			// getter
			get: function() {
				if (this.lstDataConfig.appraiser_sales.length > 0) {
					return this.dataPC.appraiser_sale_id;
				} else {
					return this.dataPC.appraiser_sale.name;
				}
			},
			// setter
			set: function(newValue) {
				this.dataPC.appraiser_sale_id = newValue;
			}
		},
		optionsAppraisalPurposes() {
			return {
				data: this.lstDataConfig.appraiser_purposes,
				id: "id",
				key: "name"
			};
		},
		optionsBusinessManager() {
			return {
				data: this.lstDataConfig.appraiser_business_managers,
				id: "id",
				key: "name"
			};
		},
		optionsAppraiserPerformance() {
			return {
				data: this.lstDataConfig.appraiser_performances,
				id: "id",
				key: "name"
			};
		},
		optionsAppraiserSales() {
			return {
				data: this.lstDataConfig.appraiser_sales,
				id: "id",
				key: "name"
			};
		},
		optionsPreTypes() {
			return {
				data: this.lstDataConfig.preTypes,
				id: "id",
				key: "description"
			};
		},
		optionsCustomer() {
			return {
				data: this.lstDataConfig.customers,
				id: "id",
				key: "full_info"
			};
		},
		optionsCustomerGroup() {
			return {
				data: this.lstDataConfig.customerGroups,
				id: "id",
				key: "description"
			};
		}
	}
};
</script>
<style scoped lang="scss">
.div_radio {
	margin-bottom: 0.5rem;
}
.form-map {
	height: 100%;
	flex: 1;
}

.card {
	border-radius: 5px;
	box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
	background: #ffffff;
	margin-bottom: 1rem;

	&-footer {
		padding: 15px 24px;
	}

	&-title {
		padding: 15px;
		margin-bottom: 0;
		color: #e8e8e8;
		border-bottom: 2px solid;
		&__img {
			padding: 8px 20px;
		}
		h3 {
			color: #007ec6;
		}
		@media (max-width: 768px) {
			padding: 12px;
		}

		.title {
			font-size: 1.125rem;
			font-weight: 700;
			margin-bottom: 0;
		}
	}

	&-body {
		@media (max-width: 787px) {
			padding: 15px;
		}
	}

	&-info {
		.title {
			font-size: 1.125rem;
			font-weight: 700;
			margin-top: 28px;

			&-highlight {
				background: rgba(252, 194, 114, 0.53);
				text-align: center;
				padding: 10px 0;
				border-radius: 2px;
			}
		}
	}

	&-land {
		position: relative;
		padding: 0;
	}
}
.card-status {
	border-radius: 5px;
	box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
	background: #ffffff;
	margin-bottom: 10px;
	font-weight: 600;
	padding: 10px;
	font-size: 16px !important;

	@media (max-width: 768px) {
		margin-bottom: 10px;
	}

	@media (max-width: 418px) {
		margin-bottom: 10px;
	}
}

.form-group-container {
	margin-top: 10px;
}

.color-black {
	color: #333333;
}

.btn-delete {
	cursor: pointer;
	display: flex;
	align-items: end;
	background: #ffffff;
	// border: 0.777778px solid #000000;
	border-radius: 5.88235px;
	padding: 0.5rem;
	// margin: auto;
	// width: 36px;
	// height: 36px;

	img {
		width: 100%;
		height: auto;
		min-width: 0.75rem;
	}
}

.btn {
	&-orange {
		background: #faa831;
		text-align: center;
		border-radius: 5px;
		box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25) !important;
		height: 35px;
		width: 100px;
		color: #fff;
		margin: 15px 0 0;
		box-sizing: border-box;

		&:hover {
			border-color: #dc8300;
		}
	}
}

.img-dropdown {
	cursor: pointer;
	width: 18px;

	&__hide {
		transform: rotate(90deg);
		transition: 0.3s;
	}
}

.img-locate {
	cursor: pointer;
	position: absolute;
	right: 14px;
	top: 2.1rem;
	background-color: #f5f5f5;
	height: 2.1rem;
	width: 32px;
	display: grid;
	place-items: center;

	img {
		height: 60%;
	}
}

.text-error {
	color: #cd201f;
}

.select-group {
	background-color: #f6f7fb;
	border: 1px solid #e8e8e8;
	border-radius: 3px;
	padding: 16px 22px;

	.select-title {
		color: #00507c;
		font-weight: 700;
		white-space: nowrap;
		margin-bottom: unset !important;
	}
}
.img_add {
	width: 100%;
	height: 100% !important;
	cursor: pointer;
}
.container_input {
	border-radius: 10px;
	border: 2px solid #617f9e;
	width: 100%;
	height: 100%;
	position: relative;
}
.input_file_4 {
	left: 0;
	opacity: 0;
	height: 100%;
	width: 100%;
	cursor: pointer;
	position: absolute;
}
// map
.btn-map {
	background: #ffffff;
	border-radius: 5px;
	border: 3px solid #ffffff;
	padding: 0;
	box-sizing: border-box;
	img {
		max-width: 50px;
		height: auto;
	}
}

.icon_marker {
	width: 25px;
}
.content_economy {
	font-weight: 500;
	margin-left: 1.5rem;
}
.main-map {
	position: relative;
	height: 100%;
	width: 100%;
	transition-timing-function: ease;
	transition-duration: 0.25s;
	overflow-x: hidden;
	@media (max-width: 1023px) {
		width: 100%;
	}
	.layer-map {
		position: absolute;
		top: 0;
		bottom: 0;
		left: 0;
		right: 0;
		z-index: 0;
		transition-timing-function: ease;
		transition-duration: 0.25s;
	}
}
.content_form {
	padding-left: 1.5rem;
}
.border_disable {
	border-color: #d9d9d9 !important;
}
/deep/ .ant-input-affix-wrapper {
	height: 2.295rem !important;
}
</style>
