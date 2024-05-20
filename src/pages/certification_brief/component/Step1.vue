<template>
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-title">
					<div class="d-flex justify-content-between align-items-center">
						<h3 class="title">
							{{
								this.$route.name === "certification_brief.edit"
									? "Chỉnh sửa hồ sơ thẩm định"
									: "Tạo mới hồ sơ thẩm định"
							}}
						</h3>
						<div class="color_content card-status">
							{{ idData ? `HSTD_${idData}` : "HSTD" }} | Mới
							<!-- <img class="img-dropdown" :class="!showCardDetailAppraise ? 'img-dropdown__hide' : ''"
                  src="../../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showCardDetailAppraise = !showCardDetailAppraise"> -->
						</div>
					</div>
				</div>
				<div class="card-body card-info row" v-show="showCardDetailAppraise">
					<div class="col-md-12 order-1 col-lg-6">
						<InputTextPrefixCustom
							id="petitioner_name"
							placeholder="Ông / Bà"
							v-model="data.petitioner_name"
							vid="petitioner_name"
							:iconUser="true"
							:showIcon="true"
							label="Tên khách hàng yêu cầu (trên chứng thư)"
							rules="required"
							class="form-group-container input_certification_brief"
						/>
						<div class="row justify-content-between">
							<InputTextPrefixCustomIcon
								id="petitioner_identity_card"
								placeholder="Nhập MST/CMND/CCCD/Passport"
								v-model="data.petitioner_identity_card"
								class="form-group-container col-sm-12 col-md-6"
								vid="petitioner_identity_card"
								icon="ic_id_card_2"
								:showCustomIcon="true"
								label="MST/CMND/CCCD/Passport"
							/>
							<InputDatePicker
								v-model="data.issue_date_card"
								vid="issue_date_card"
								label="Ngày cấp "
								placeholder="Ngày / tháng / năm"
								:formatDate="'DD/MM/YYYY'"
								class="form-group-container col-sm-12 col-md-6"
								@change="changeIssueDate"
							/>
							<InputText
								v-model="data.issue_place_card"
								vid="issue_place_card"
								label="Nơi cấp"
								placeholder="Nhập nơi cấp MST/CMND/CCCD/Passport"
								class="form-group-container col-sm-12 col-md-6"
							/>
							<InputTextPrefixCustom
								id="petitioner_phone"
								placeholder="Nhập số điện thoại"
								v-model="data.petitioner_phone"
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
							v-model="data.petitioner_address"
							vid="petitioner_address"
							:iconLocation="true"
							:showIcon="true"
							label="Địa chỉ"
							class="form-group-container input_certification_brief"
						/>
						<div class="row justify-content-between">
							<InputCategory
								v-model="data.appraise_purpose_id"
								class="form-group-container input_certification_brief  col-sm-12 col-md-6"
								vid="appraise_purpose_id"
								label="Mục đích thẩm định"
								rules="required"
								:options="optionsAppraisalPurposes"
								@change="handleChangeAppraisePurpose"
							/>
							<InputDatePicker
								v-model="data.appraise_date"
								vid="appraise_date"
								label="Thời điểm thẩm định"
								placeholder="Ngày / tháng / năm"
								rules="required"
								:formatDate="'DD/MM/YYYY'"
								class="form-group-container col-sm-12 col-md-6"
								@change="changeAppraiseDate"
							/>
						</div>
						<InputTextarea
							:autosize="true"
							:disableInput="false"
							v-model="data.note"
							label="Ghi chú"
							class="form-group-container"
						/>
						<!-- <InputCategoryMulti
              v-model="data.document_type"
              :maxTagCount="1"
              class="form-group-container input_certification_brief"
              vid="document_type"
              label="Loại thẩm định"
              rules="required"
              :options="optionsTypeAppraiser"
            /> -->
					</div>
					<div class="col-md-12 order-3 order-lg-2 col-lg-6">
						<div :key="render_price_fee" class="row justify-content-between">
							<InputCurrency
								v-model="data.service_fee"
								vid="service_fee"
								:max="99999999999999"
								label="Tổng phí dịch vụ"
								class="form-group-container col-sm-12 col-md-6"
								@change="changeServiceFee($event)"
							/>
							<InputPercent
								:key="render_price_fee"
								v-model="data.commission_fee"
								label="Chiết khấu"
								vid="test"
								:max="100"
								:decimal="0"
								rules="required"
								class="form-group-container col-sm-12 col-md-3"
								@change="changeCommissionFee($event)"
							/>
							<InputCategory
								v-model="data.document_alter_by_bank"
								class="form-group-container col-sm-12 col-md-3"
								vid="document_alter_by_bank"
								label="Loại hồ sơ"
								rules="required"
								:options="optionsLoaiHs"
								@change="data.document_alter_by_bank = $event"
							/>
						</div>
						<div class="row justify-content-between">
							<InputText
								v-model="data.document_num"
								vid="document_num"
								label="Số hợp đồng"
								class="form-group-container col-sm-12 col-md-6"
							/>
							<InputDatePicker
								v-model="data.document_date"
								vid="document_date"
								label="Ngày hợp đồng"
								:formatDate="'DD/MM/YYYY'"
								@change="changeDocumentDate"
								placeholder="Ngày / tháng / năm"
								class="form-group-container col-sm-12 col-md-6"
							/>
						</div>
						<div class="row justify-content-between">
							<InputText
								v-model="data.certificate_num"
								vid="certificate_num"
								:disabledInput="true"
								label="Số chứng thư"
								class="form-group-container col-sm-12 col-md-6"
							/>
							<InputDatePicker
								v-model="data.certificate_date"
								vid="certificate_date"
								label="Ngày chứng thư"
								:disabled="true"
								placeholder="Ngày / tháng / năm"
								class="form-group-container col-sm-12 col-md-6"
								:formatDate="'DD/MM/YYYY'"
								:date="disabledDate"
								@change="changeCertificateDate"
							/>
						</div>

						<div class="row justify-content-between">
							<InputText
								v-model="data.survey_location"
								vid="survey_location"
								label="Địa điểm khảo sát"
								class="form-group-container col-sm-12 col-md-6"
							/>
							<!-- <InputDatePicker
								v-model="data.survey_time"
								vid="survey_time"
								label="Thời điểm khảo sát"
								placeholder="Ngày / tháng / năm"
								:formatDate="'DD/MM/YYYY'"
								class="form-group-container col-sm-12 col-md-6"
								@change="changeSurveyDate"
							/> -->
							<InputDatePickerV2
								v-model="data.survey_time"
								vid="survey_time"
								show-time
								label="Thời điểm khảo sát"
								class="form-group-container col-sm-12 col-md-6"
								@change="changeSurveyDate"
							/>
							<InputTextPrefixCustom
								id="name_contact"
								placeholder="Nhập tên người liên hệ"
								v-model="data.name_contact"
								vid="name_contact"
								:iconUser="true"
								:showIcon="true"
								label="Tên người liên hệ"
								rules="required"
								class="form-group-container col-sm-12 col-md-6"
							/>
							<InputTextPrefixCustom
								id="phone_contact"
								placeholder="Nhập số điện thoại người liên hệ"
								v-model="data.phone_contact"
								class="form-group-container col-sm-12 col-md-6"
								vid="phone_contact"
								:iconPhone="true"
								:showIcon="true"
								label="Điện thoại người liên hệ"
							/>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-12 col-lg-6">
			<div class="card">
				<div class="card-title">
					<div class="d-flex justify-content-between align-items-center">
						<h3 class="title">Đối tác</h3>
						<img
							class="img-dropdown"
							:class="!showCardDetailTraffic ? 'img-dropdown__hide' : ''"
							src="../../../assets/images/icon-btn-down.svg"
							alt="dropdown"
							@click="showCardDetailTraffic = !showCardDetailTraffic"
						/>
					</div>
				</div>
				<div class="card-body card-info" v-show="showCardDetailTraffic">
					<div class="d-flex-column">
						<InputCategorySearch
							vid="appraiser"
							class="form-group-container"
							label="Tìm đối tác"
							@change="handleChangeCustomer"
							@search="debounceSearchCustomer"
							:options="optionsCustomer"
						/>
						<InputTextPrefixCustom
							id="customer_name"
							placeholder="Ông / Bà"
							v-model="data.customer.name"
							vid="customer_name"
							:iconUser="true"
							:showIcon="true"
							label="Họ tên đối tác"
							class="form-group-container input_certification_brief"
						/>
						<InputTextPrefixCustom
							id="customer_address"
							placeholder="Nhập địa chỉ của khách hàng"
							v-model="data.customer.address"
							vid="customer_address"
							:iconLocation="true"
							:showIcon="true"
							label="Địa chỉ"
							class="form-group-container input_certification_brief"
						/>
						<InputTextPrefixCustom
							id="customer_phone"
							placeholder="Nhập số điện thoại"
							v-model="data.customer.phone"
							class="form-group-container input_certification_brief"
							vid="customer_phone"
							:iconPhone="true"
							:showIcon="true"
							label="Điện thoại"
						/>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-12 col-lg-6">
			<div class="card" :style="isMobile() ? { 'margin-bottom': '70px' } : {}">
				<div class="card-title">
					<div class="d-flex justify-content-between align-items-center">
						<h3 class="title">Tổ thẩm định</h3>
						<img
							class="img-dropdown"
							:class="
								!showCardDetailEconomicAndSocial ? 'img-dropdown__hide' : ''
							"
							src="../../../assets/images/icon-btn-down.svg"
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
								class="form-group-container col-sm-12 col-md-6"
								@change="handleChangeAppraiserSale"
								:options="optionsBusiness"
							/>
							<InputCategory
								v-model="business_manager_compute"
								vid="business_manager_id"
								label="Quản lý nghiệp vụ"
								rules="required"
								class="form-group-container col-sm-12 col-md-6"
								:options="optionsBusinessManager"
							/>
						</div>
						<!-- <div class="form-group-container">
              <label class="color-black font-weight-bold">Đại diện theo pháp luật</label>
              <div class="form-control border_disable disabled">
                <p class="mb-0">
                  {{ appraisersManager.length > 0 ? appraisersManager[0].name : ""}}
                </p>
              </div>
            </div> -->
						<div class="row justify-content-between">
							<InputCategory
								v-model="administrative_compute"
								vid="administrative_id"
								label="Hành chính viên"
								class="form-group-container col-sm-12 col-md-6"
								@change="handleChangeAppraiser"
								:options="optionsAdministratives"
							/>
							<InputCategory
								v-model="appraiser_perform_compute"
								vid="appraiser_perform_id"
								label="Chuyên viên thực hiện"
								class="form-group-container col-sm-12 col-md-6"
								@change="handleChangeAppraiserPerform"
								:options="optionsPeformance"
							/>
						</div>
						<div class="row justify-content-between">
							<InputCategory
								v-model="appraiser_control_compute"
								vid="appraiser_control_id"
								label="Kiểm soát viên"
								class="form-group-container col-sm-12 col-md-6"
								@change="handleChangeAppraiserControl"
								:options="optionsAppraiserControl"
							/>
							<InputCategory
								v-model="appraiser_compute"
								vid="appraiser_id"
								label="Thẩm định viên"
								class="form-group-container col-sm-12 col-md-6"
								@change="handleChangeAppraiser"
								:options="optionsAppraiser"
							/>
						</div>

						<div class="row justify-content-between">
							<InputCategory
								v-model="appraiser_manager_compute"
								vid="appraiser_manager_id"
								label="Đại diện theo pháp luật"
								class="form-group-container col-sm-12 col-md-6"
								@change="handleChangeAppraiserManager"
								:options="optionsAppraiserManager"
							/>
							<InputCategory
								v-model="appraiser_confirm_compute"
								vid="appraiser_confirm_id"
								label="Đại diện ủy quyền"
								class="form-group-container col-sm-12 col-md-6"
								:options="optionsSignAppraiser"
							/>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>
<style lang="scss">
@import "../../../../node_modules/leaflet.markercluster/dist/MarkerCluster.css";
@import "../../../../node_modules/leaflet.markercluster/dist/MarkerCluster.Default.css";
@import "../../../../node_modules/leaflet/dist/leaflet.css";
</style>
<script>
import { debounce } from "lodash-es";
import InputCategoryMulti from "@/components/Form/InputCategoryMulti";
import InputPercent from "@/components/Form/InputPercent";
import InputDatePicker from "@/components/Form/InputDatePicker";
import InputCategory from "@/components/Form/InputCategory";
import InputCategorySearch from "@/components/Form/InputCategorySearch";
import InputText from "@/components/Form/InputText";
import InputTextarea from "@/components/Form/InputTextarea";
import InputDatePickerV2 from "@/components/Form/InputDatePickerV2";
import InputTextPrefixCustom from "@/components/Form/InputTextPrefixCustom";
import InputNumberFormat from "@/components/Form/InputNumber";
import InputCurrency from "@/components/Form/InputCurrency";
import { Tabs, TabItem } from "vue-material-tabs";
import {
	LMap,
	LControlZoom,
	LTileLayer,
	LMarker,
	LTooltip,
	LIcon,
	LControl
} from "vue2-leaflet";
import Vue from "vue";
import Icon from "buefy";
import InputLengthArea from "@/components/Form/InputLengthArea.vue";
import CertificationBrief from "@/models/CertificationBrief";
import moment from "moment";
import InputTextPrefixCustomIcon from "@/components/Form/InputTextPrefixCustomIcon";
//
Vue.use(Icon);
export default {
	name: "Step1",
	props: [
		"data",
		"appraisalPurposes",
		"appraisers",
		"signAppraisers",
		"appraisersManager",
		"appraisersControl",
		"administratives",
		"customers",
		"idData",
		"render_price_fee",
		"employeeBusiness",
		"employeePerformance",
		"typeAppraiseProperty",
		"userAppraiserId",
		"businessManagers"
	],
	components: {
		InputCategory,
		InputCategorySearch,
		InputText,
		InputTextarea,
		InputDatePickerV2,
		InputTextPrefixCustom,
		InputNumberFormat,
		InputDatePicker,
		InputCurrency,
		InputPercent,
		Tabs,
		TabItem,
		LMap,
		LControlZoom,
		LTileLayer,
		LMarker,
		LTooltip,
		LControl,
		LIcon,
		InputLengthArea,
		InputCategoryMulti,
		InputTextPrefixCustomIcon
	},
	computed: {
		business_manager_compute: {
			get: function() {
				if (this.businessManagers.length > 0) {
					return this.data.business_manager_id;
				} else {
					return this.data.appraiser_business_manager.name;
				}
			},
			// setter
			set: function(newValue) {
				this.data.business_manager_id = newValue;
			}
		},
		appraiser_perform_compute: {
			// getter
			get: function() {
				if (this.employeePerformance.length > 0) {
					//	 console.log('vô đây trước 1',this.employeePerformance)
					return this.data.appraiser_perform_id;
				} else {
					// // console.log('vô đây trước 2')
					return this.data.appraiser_perform.name;
				}
			},
			// setter
			set: function(newValue) {
				// // console.log('newwww', newValue)
				this.data.appraiser_perform_id = newValue;
			}
		},
		appraiser_control_compute: {
			// getter
			get: function() {
				if (this.appraisersControl.length > 0) {
					return this.data.appraiser_control_id;
				} else {
					return this.data.appraiser_control.name;
				}
			},
			// setter
			set: function(newValue) {
				// // console.log('newwww', newValue)
				this.data.appraiser_control_id = newValue;
			}
		},
		administrative_compute: {
			// getter
			get: function() {
				if (this.appraisersControl.length > 0) {
					return this.data.administrative_id;
				} else {
					return this.data.administrative.name;
				}
			},
			// setter
			set: function(newValue) {
				// // console.log('newwww', newValue)
				this.data.administrative_id = newValue;
			}
		},
		appraiser_compute: {
			// getter
			get: function() {
				if (this.appraisers.length > 0) {
					return this.data.appraiser_id;
				} else {
					return this.data.appraiser.name;
				}
			},
			// setter
			set: function(newValue) {
				// // console.log('newwww', newValue)
				this.data.appraiser_id = newValue;
			}
		},
		appraiser_manager_compute: {
			// getter
			get: function() {
				if (this.appraisersManager.length > 0) {
					return this.data.appraiser_manager_id;
				} else {
					return this.data.appraiser_manager.name;
				}
			},
			// setter
			set: function(newValue) {
				// // console.log('newwww', newValue)
				this.data.appraiser_manager_id = newValue;
			}
		},
		appraiser_confirm_compute: {
			// getter
			get: function() {
				if (this.signAppraisers.length > 0) {
					return this.data.appraiser_confirm_id;
				} else {
					return this.data.appraiser_confirm
						? this.data.appraiser_confirm.name
						: "";
				}
			},
			// setter
			set: function(newValue) {
				// // console.log('newwww', newValue)
				this.data.appraiser_confirm_id = newValue;
			}
		},
		appraiser_sale_compute: {
			// getter
			get: function() {
				if (this.employeeBusiness.length > 0) {
					return this.data.appraiser_sale_id;
				} else {
					return this.data.appraiser_sale.name;
				}
			},
			// setter
			set: function(newValue) {
				// // console.log('newwww', newValue)
				this.data.appraiser_sale_id = newValue;
			}
		},
		optionsLoaiHs() {
			return {
				data: [
					{ id: 0, name: "Hồ sơ mẫu" },
					{ id: 1, name: "Hồ sơ Shinhan" }
				],
				id: "id",
				key: "name"
				// selected: this.userAppraiserId
			};
		},
		optionsAppraisalPurposes() {
			return {
				data: this.appraisalPurposes,
				id: "id",
				key: "name"
			};
		},
		optionsAppraiserManager() {
			return {
				data: this.appraisersManager,
				id: "id",
				key: "name"
			};
		},
		optionsAppraiserControl() {
			return {
				data: this.appraisersControl,
				id: "id",
				key: "name"
			};
		},
		optionsAdministratives() {
			return {
				data: this.administratives,
				id: "id",
				key: "name"
			};
		},
		optionsAppraiser() {
			return {
				data: this.appraisers,
				id: "id",
				key: "name"
			};
		},
		optionsPeformance() {
			return {
				data: this.employeePerformance,
				id: "id",
				key: "name"
			};
		},
		optionsBusiness() {
			return {
				data: this.employeeBusiness,
				id: "id",
				key: "name"
				// selected: this.userAppraiserId
			};
		},
		optionsBusinessManager() {
			return {
				data: this.businessManagers,
				id: "id",
				key: "name"
			};
		},
		optionsSignAppraiser() {
			return {
				data: this.signAppraisers,
				id: "id",
				key: "name"
			};
		},
		optionsTypeAppraiser() {
			return {
				data: this.typeAppraiseProperty,
				id: "acronym",
				key: "description"
			};
		},
		optionsCustomer() {
			return {
				data: this.customers,
				id: "id",
				key: "full_info"
			};
		}
	},
	data() {
		return {
			theme: {
				navItem: "#000000",
				navActiveItem: "#FAA831",
				slider: "#FAA831",
				arrow: "#000000"
			},
			showCardDetailAppraise: true,
			showCardDetailTraffic: true,
			showCardDetailEconomicAndSocial: true,
			showCardDetailImage: true,
			customers_step_1: this.customers,
			form: {
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
			}
		};
	},
	created() {
		// this.getCustomer()
		// console.log('data', this.data)
	},
	methods: {
		isMobile() {
			if (
				/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
					navigator.userAgent
				)
			) {
				return true;
			} else {
				return false;
			}
		},
		disabledDate(current) {
			if (
				this.data.document_date !== "" &&
				this.data.document_date !== undefined &&
				this.data.document_date !== null
			) {
				let dateDoc = (" " + this.data.document_date).slice(1);
				dateDoc = moment(dateDoc, "DD/MM/YYYY").format("YYYY-MM-DD");
				return current <= moment(dateDoc);
			} else {
				return current >= moment().endOf("day");
			}
		},

		debounceSearchCustomer: debounce(function(e) {
			if (e) {
				this.getCustomer(e);
			}
		}, 400),

		async getCustomer(search) {
			const res = await CertificationBrief.getCustomer(search);
			if (res.data) {
				this.customers_step_1 = res.data;
			}
		},
		async handleChangeCustomer(event) {
			// await this.getCustomer()
			let bindCustomer = await this.customers.filter(item => item.id === event);
			if (bindCustomer && bindCustomer.length > 0) {
				this.data.customer.name = bindCustomer[0].name;
				this.data.customer.address = bindCustomer[0].address;
				this.data.customer.phone = bindCustomer[0].phone;
			}
		},
		handleChangeAppraisePurpose(event) {
			// this.form.appraise_purpose_id = event
			this.data.appraise_purpose_id = event;
		},
		handleChangeAppraiserSale() {},
		handleChangeAppraiserPerform() {},
		handleChangeAppraiser(event) {
			this.$emit("handleChangeAppraiser", event);
		},
		handleChangeAppraiserManager(event) {
			this.$emit("handleChangeAppraiserManager", event);
		},
		handleChangeAppraiserControl(event) {
			this.$emit("handleChangeAppraiserControl", event);
		},
		changeDocumentDate(event) {
			this.data.document_date = event;
			if (event && event !== "") {
				if (
					moment(this.data.document_date).endOf("day") <
					moment(this.data.certificate_date)
				) {
					this.data.certificate_date = "";
				}
			} else {
				this.data.document_date = "";
			}
			this.data.appraise_date = this.data.document_date;
		},
		changeServiceFee(event) {
			this.data.service_fee = event;
		},
		changeCommissionFee(event) {
			this.data.commission_fee = event;
		},
		changeAppraiseDate(event) {
			console.log("event", event);
			this.data.appraise_date = event;
		},
		changeSurveyDate(event) {
			this.data.survey_time = event;
		},
		changeIssueDate(event) {
			this.data.issue_date_card = event;
		},
		changeCertificateDate(event) {
			this.data.certificate_date = event;
		},
		async getDataEdit() {
			this.form.document_date = this.data.document_date
				? moment(this.data.document_date).format("DD/MM/YYYY")
				: "";
			this.form.certificate_date = this.data.certificate_date
				? moment(this.data.certificate_date).format("DD/MM/YYYY")
				: "";
			this.form.appraise_date = this.data.appraise_date
				? moment(this.data.appraise_date).format("DD/MM/YYYY")
				: "";
			this.form.survey_time = this.data.survey_time
				? moment(this.data.survey_time).format("DD/MM/YYYY")
				: "";
			this.form.issue_date_card = this.data.issue_date_card
				? moment(this.data.issue_date_card).format("DD/MM/YYYY")
				: "";
			// this.form.appraise_purpose_id = this.data.appraise_purpose_id
		}
	},
	beforeMount() {
		// if ('id' in this.$route.query && this.$route.name === 'certification_brief.edit') {
		//   this.getDataEdit()
		// }
	},
	mounted() {
		this.data.appraiser_sale_id = this.userAppraiserId;
		this.data.appraiser_perform_id = this.userAppraiserId;
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
