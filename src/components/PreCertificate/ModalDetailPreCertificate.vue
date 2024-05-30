<template>
	<div>
		<div
			class="modal-detail d-flex justify-content-center align-items-center"
			@click.self="handleCancel"
		>
			<div class="card" :style="isMobile ? { 'margin-top': '-55px' } : {}">
				<div
					class="container-title"
					:style="
						isMobile ? { 'padding-bottom': '0', 'margin-bottom': '0' } : {}
					"
				>
					<div class="d-flex justify-content-between">
						<h2 class="title">Thông tin chung</h2>
						<img
							height="35px"
							@click="handleCancel"
							class="cancel"
							src="@/assets/icons/ic_cancel_2.svg"
							alt=""
						/>
					</div>
				</div>
				<div
					class="contain-detail"
					:style="isMobile ? { 'padding-top': '0' } : {}"
				>
					<div class="detail_certificate_1 col-12 mb-2">
						<div class="col-12 d-flex mb-2 row  justify-content-between">
							<span class="content_id content_id_primary class_p">{{
								`YCSB_${dataPC.id}`
							}}</span>
							<span
								v-if="dataPC.certificate_id"
								@click="handleDetailCertificate(dataPC.certificate_id)"
								class=" arrowBox arrow-right "
								id="certificate_id"
							>
								<icon-base name="nav_hstd_2" class="item-icon svg-inline--fa" />
								{{ `HTSD_${dataPC.certificate_id}` }}
								<b-tooltip target="certificate_id" placement="right">{{
									`Đã chuyển chính thức HTSD_${dataPC.certificate_id}`
								}}</b-tooltip>
							</span>
						</div>
						<div class="d-flex container_content justify-content-between">
							<div class="d-flex container_content">
								<strong class="margin_content_inline">Khách hàng:</strong>
								<p>{{ dataPC.petitioner_name }}</p>
							</div>
						</div>
						<div class="d-flex container_content">
							<strong class="margin_content_inline">Địa chỉ:</strong>
							<p>{{ dataPC.petitioner_address }}</p>
						</div>
						<div class="d-flex container_content">
							<strong class="margin_content_inline"
								>MST/CMND/CCCD/Passport:</strong
							>
							<p>{{ dataPC.petitioner_identity_card }}</p>
						</div>
						<div class="d-flex container_content">
							<strong class="margin_content_inline">Điện thoại:</strong>
							<p>{{ dataPC.petitioner_phone }}</p>
						</div>
						<div class="d-flex container_content">
							<strong class="margin_content_inline">Mục đích thẩm định:</strong
							><span id="appraise_purpose" class="text-left">{{
								dataPC.appraise_purpose &&
								dataPC.appraise_purpose.name.length > 70
									? dataPC.appraise_purpose.name.substring(70, 0) + "..."
									: dataPC.appraise_purpose.name
							}}</span>
							<b-tooltip target="appraise_purpose" placement="top-right">{{
								dataPC.appraise_purpose.name
							}}</b-tooltip>
						</div>
						<!-- <div class="d-flex container_content">
							<strong class="margin_content_inline">Mục đích thẩm định:</strong>
							<p>
								{{
									dataPC.appraise_purpose ? dataPC.appraise_purpose.name : ""
								}}
							</p>
						</div> -->

						<div class="d-flex container_content">
							<strong class="margin_content_inline">Loại sơ bộ:</strong>
							<p>
								{{ dataPC.pre_type ? dataPC.pre_type.description : "" }}
							</p>
						</div>

						<div class="d-flex container_content">
							<strong class="margin_content_inline">Thời điểm sơ bộ:</strong>
							<p>
								{{ dataPC.pre_date ? formatDate(dataPC.pre_date) : "" }}
							</p>
						</div>
						<div class="d-flex container_content">
							<strong class="margin_content_inline">Tổng phí dịch vụ:</strong>
							<p>
								{{
									dataPC.total_service_fee
										? formatNumber(dataPC.total_service_fee)
										: 0
								}}đ
							</p>
						</div>
						<div class="d-flex container_content">
							<strong class="margin_content_inline">Chiết khấu:</strong>
							<p>{{ dataPC.commission_fee ? dataPC.commission_fee : 0 }}%</p>
						</div>

						<div class="d-flex container_content">
							<strong class="margin_content_inline">Tổng giá trị sơ bộ:</strong>
							<p>
								{{
									dataPC.total_preliminary_value
										? formatNumber(dataPC.total_preliminary_value)
										: 0
								}}đ
							</p>
						</div>
						<div class="d-flex container_content">
							<strong class="margin_content_inline">Ghi chú:</strong
							><span id="note" class="text-left">{{
								dataPC.pre_asset_name && dataPC.pre_asset_name.length > 25
									? dataPC.pre_asset_name.substring(25, 0) + "..."
									: dataPC.pre_asset_name
							}}</span>
							<b-tooltip target="note" placement="top-right">{{
								dataPC.pre_asset_name
							}}</b-tooltip>
						</div>
						<div
							v-if="dataPC.cancel_reason_string"
							class="d-flex container_content"
						>
							<strong class="margin_content_inline">Lý do hủy sơ bộ:</strong>
							<p>
								{{ dataPC.cancel_reason_string }}
							</p>
						</div>
					</div>
					<div class="col-12 mb-2">
						<div class="detail_certificate_2">
							<div class="d-flex container_content">
								<strong class="margin_content_inline">Đối tác:</strong>
								<p>{{ dataPC.customer ? dataPC.customer.name : "" }}</p>
							</div>
							<div class="d-flex container_content">
								<strong class="margin_content_inline">Địa chỉ:</strong>
								<p>{{ dataPC.customer ? dataPC.customer.address : "" }}</p>
							</div>
							<div class="d-flex container_content">
								<strong class="margin_content_inline">Liên hệ:</strong>
								<p>{{ dataPC.customer ? dataPC.customer.phone : "" }}</p>
							</div>
						</div>
					</div>
					<div class="col-12 mb-2">
						<div class="detail_certificate_2">
							<div class="d-flex container_content justify-content-between">
								<div class="d-flex">
									<strong class="margin_content_inline"
										>Nhân viên kinh doanh:</strong
									>
									<p>
										{{
											dataPC.appraiser_sale ? dataPC.appraiser_sale.name : ""
										}}
									</p>
								</div>
							</div>
							<div class="d-flex container_content">
								<strong class="margin_content_inline"
									>Quản lý nghiệp vụ:</strong
								>
								<p>
									{{
										dataPC.appraiser_business_manager
											? dataPC.appraiser_business_manager.name
											: ""
									}}
								</p>
							</div>
							<div class="d-flex container_content">
								<strong class="margin_content_inline"
									>Chuyên viên thực hiện:</strong
								>
								<p>
									{{
										dataPC.appraiser_perform
											? dataPC.appraiser_perform.name
											: ""
									}}
								</p>
							</div>
						</div>
					</div>
				</div>
				<div
					v-if="!isMobile"
					class=" d-flex justify-content-between align-items-center m-2"
				>
					<div
						style="cursor:pointer"
						@click="handleDetail(idData)"
						class="btn-edit"
					>
						<!-- <img src="@/assets/icons/ic_edit_3.svg" alt="add"/> -->
						<span class="color_content content_btn_edit">Xem chi tiết</span>
					</div>
					<div class="button-contain">
						<button
							v-if="!dataPC.certificate_id"
							v-for="(target, index) in getTargetDescription()"
							:key="index"
							class="btn "
							:class="target.css"
							@click="handleFooterAccept(target)"
						>
							<img
								class="img"
								:src="require(`@/assets/icons/${target.img}`)"
								alt="edit"
							/>{{ target.btnDescription || target.description }}
						</button>
						<button
							class="btn btn-white btn-action-modal"
							type="button"
							@click="handleCancel"
						>
							<img
								src="@/assets/icons/ic_cancel.svg"
								style="margin-right: 12px"
								alt="save"
							/>Trở lại
						</button>
					</div>
				</div>
				<div v-else class="row" style="padding: 0;">
					<div
						style="cursor:pointer"
						@click="handleDetail(idData)"
						class="btn-edit col-12"
					>
						<!-- <img src="@/assets/icons/ic_edit_3.svg" alt="add"/> -->
						<span class="color_content content_btn_edit">Xem chi tiết</span>
					</div>
					<div class="button-contain row">
						<div class="col-6">
							<button
								class="btn btn-white"
								type="button"
								@click="handleCancel"
								style="width: fit-content;"
							>
								<img
									src="@/assets/icons/ic_cancel.svg"
									style="margin-right: 12px"
									alt="save"
								/>
								<span style="font-size: 15px;">Trở lại</span>
							</button>
						</div>
						<div class="col-6" style="text-align: right;">
							<!-- <button v-for="(target, index) in getTargetDescription()" :key="index" class="btn" :class="target.css" @click="handleFooterAccept(target)">
										<img class="img" :src="require(`@/assets/icons/${target.img}`)" alt="edit"/>
										<span style="font-size: 15px;">{{target.description}}</span>
									</button> -->
							<!-- <button style="margin-right: 2px" class="btn btn-white" type="button">
										<img class="img" src="@/assets/icons/ic_more.svg" alt="cancel">Hành động
									</button> -->
							<!-- <b-button-group  class="btn_group" > -->
							<b-dropdown
								v-if="getTargetDescription().length > 0"
								class="btn_dropdown"
								no-caret
								right
								dropup
								style="margin-top: 5px;"
							>
								<template #button-content>
									<button
										style="margin-right: 2px"
										class="btn btn-white"
										type="button"
									>
										<img
											class="img"
											src="@/assets/icons/ic_more.svg"
											alt="cancel"
										/>Hành động
									</button>
								</template>
								<b-dropdown-item
									style="margin-right:0;width: 150px;padding: 0;"
									v-for="(target, index) in getTargetDescription()"
									:key="index"
									class="btn"
									:class="target.css"
									@click="handleFooterAccept(target)"
								>
									<div class="div_item_dropdown">
										<img
											class="img"
											:src="require(`@/assets/icons/${target.img}`)"
											alt="edit"
										/>
										<span style="font-size: 15px;">{{
											target.btnDescription || target.description
										}}</span>
										<!-- {{target.description}} -->
									</div>
								</b-dropdown-item>
							</b-dropdown>
							<!-- </b-button-group> -->
						</div>

						<!-- <div class="col-3" style="padding: 0"></div>
								<div class="col-3" style="padding: 0"></div> -->
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import { ref } from "vue";
import { storeToRefs } from "pinia";
import { usePreCertificateStore } from "@/store/preCertificate";
import ModalSendVerify from "@/components/Modal/ModalSendVerify";
import InputText from "@/components/Form/InputText";
import InputCategory from "@/components/Form/InputCategory";
import FileUpload from "@/components/file/FileUpload";
import InputTextPrefixCustom from "@/components/Form/InputTextPrefixCustom";
import InputDatePicker from "@/components/Form/InputDatePicker";
import InputCurrency from "@/components/Form/InputCurrency";
import moment from "moment";
import {
	BTooltip,
	BDropdown,
	BDropdownItem,
	BButtonGroup
} from "bootstrap-vue";
import IconBase from "./../IconBase.vue";

export default {
	name: "ModalAppraiseInformation",
	props: [
		"data",
		"idData",
		"edit",
		"add",
		"user_id",
		"appraiser_number",
		"profile"
	],
	data() {
		return {
			isOneItem: false,
			isTwoItem: false,
			isThreeItem: false,
			form: {
				petitioner_name: "",
				appraise_purpose: "",
				appraiser_confirm: "",
				appraiser_manager: "",
				appraiser_control: "",
				appraiser_perform: "",
				customer: "",
				petitioner_address: "",
				petitioner_phone: "",
				appraise_date: "",
				appraise_purpose_id: "",
				certificate_date: "",
				document_date: "",
				document_num: "",
				certificate_num: "",
				date_certificate: "",
				service_fee: "",
				phone: "",
				address: "",
				petitioner_identity_card: "",
				status: 1,
				sub_status: 1,
				note: ""
			},
			appraisalPurposes: [],
			showAppraisalDialog: false,
			showVerifyCertificate: false,
			key_render_appraisal: 20000000,
			isPermission: false,
			user: "",
			config: "",
			configData: "",
			requireData: "",
			targetDescription: [],
			targetConfig: {},
			targetMessage: ""
		};
	},
	components: {
		IconBase,
		FileUpload,
		InputCategory,
		InputText,
		InputTextPrefixCustom,
		InputDatePicker,
		InputCurrency,
		ModalSendVerify,
		"b-tooltip": BTooltip,
		"b-dropdown-item": BDropdownItem,
		"b-button-group": BButtonGroup,
		"b-dropdown": BDropdown
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
		const preCertificateStore = usePreCertificateStore();
		const { jsonConfig, dataPC } = storeToRefs(preCertificateStore);
		return { dataPC, isMobile, jsonConfig };
	},
	computed: {
		optionsAppraisalPurposes() {
			return {
				data: this.appraisalPurposes,
				id: "id",
				key: "name"
			};
		}
	},
	created() {
		// this.getAppraiseOthers()
		// this.getDetailCertificate()
	},
	methods: {
		handleDetailCertificate(id) {
			let url = this.$router.resolve({
				name: "certification_brief.detail",
				query: {
					id: id.toString()
				}
			}).href;

			window.open(url, "_blank");
		},
		getTargetDescription() {
			let data = [];
			if (this.isPermission) {
				data = this.targetDescription;
			}
			return data;
		},
		handleCancelVerify() {
			this.showVerifyCertificate = false;
		},

		formatDate(date) {
			return moment(date).format("DD/MM/YYYY");
		},
		formatNumber(num) {
			if (num) {
				let formatedNum = num.toString().replace(".", ",");
				return formatedNum.toString().replace(/^[+-]?\d+/, function(int) {
					return int.replace(/(\d)(?=(\d{3})+$)/g, "$1.");
				});
			}
		},
		handleDetail(id) {
			this.$router
				.push({
					name: "pre_certification.detail",
					query: {
						id: id.toString()
					}
				})
				.catch(_ => {});
		},

		handleCancel(event) {
			this.$emit("cancel", event);
		},

		getExpireStatusDate(config) {
			let dateConvert = new Date();
			let minutes = config.process_time ? config.process_time : 1440;
			let dateConverted = new Date(dateConvert.getTime() + minutes * 60000);
			let status_expired_at = moment(dateConverted).format("DD-MM-YYYY HH:mm");
			return status_expired_at;
		},
		loadConfigByStatus(status) {
			return this.jsonConfig.principle.find(
				item => item.status === status && item.isActive === 1
			);
		},
		loadConfigData(configData) {
			this.config = configData;
			this.requireData = configData.require;
			this.targetDescription = configData.target_description;
		},
		checkPermission(configData) {
			let check = false;
			if (
				configData.put_require_roles &&
				this.user.roles &&
				configData.put_require_roles.includes(this.user.roles[0].name)
			) {
				return true;
			}
			if (configData.put_require && configData.put_require.length > 0) {
				configData.put_require.forEach(i => {
					if (
						// (i === "created_by" && this.form[i].id === this.user.id) ||
						i !== "created_by" &&
						this.form[i] === this.user.appraiser.id
					) {
						check = true;
					}
				});
			}
			return check;
		},
		configStatus() {
			this.user = this.profile.data.user;
			let configData = this.loadConfigByStatus(this.dataPC.status);
			if (configData) {
				this.loadConfigData(configData);
				this.isPermission = this.checkPermission(configData);
			}
		},
		handleFooterAccept(target) {
			this.$emit("handleFooterAccept", target);
		}
	},
	beforeMount() {},
	mounted() {
		this.form = this.data;
		this.configStatus();
	}
};
</script>

<style lang="scss" scoped>
/deep/ .dropdown-item {
	min-width: unset !important;
	// padding: 0!important;
}
/deep/ .dropdown-menu.show {
	background: transparent !important;
	box-shadow: none !important;
	text-align: right;
	margin-right: 0;
}
.title {
	font-size: 1.125rem;
	font-weight: 700;
	margin-bottom: 15px;
	color: #000000;
}
.modal-detail {
	position: fixed;
	z-index: 200;
	left: 0;
	top: 0;
	width: 100%;
	height: 100%;
	background: rgba(0, 0, 0, 0.6);
	.card {
		border-radius: 5px;
		box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
		max-width: 900px;
		width: 100%;
		max-height: 90vh;
		margin-bottom: 0;
		// padding: 35px 50px;
		padding: 25px 50px 25px;
		@media (max-width: 787px) {
			padding: 20px 10px;
		}
		&-header {
			border-bottom: 1px solid #dddddd;
			h3 {
				color: #333333;
			}
			img {
				cursor: pointer;
			}
		}
		&-body {
			text-align: center;
			p {
				color: #333333;
				margin-bottom: 40px;
			}

			.btn__group {
				.btn {
					max-width: 150px;
					width: 100%;
					margin: 0 10px;
				}
			}
		}
	}
}
.card {
	.contain-detail {
		overflow-y: auto;
		overflow-x: hidden;
		border-top: 1px solid #e8e8e8;
		padding-top: 10px;
		&::-webkit-scrollbar {
			width: 2px;
		}
	}
	&-title {
		background: #f3f2f7;
		padding: 16px 20px;
		margin-bottom: 0;
		.title {
			font-size: 1.125rem;
			font-weight: 600;
			margin-bottom: 0;
		}
	}
	&-table {
		border-radius: 5px;
		background: #ffffff;
		box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
		width: 99%;
		margin: 50px auto 50px;
	}
	&-body {
		padding: 35px 30px 40px;
	}
	&-info {
		.title {
			font-size: 1.125rem;
			font-weight: 700;
			margin-top: 28px;
		}
	}
	&-land {
		position: relative;
		padding: 0;
	}
}
.img {
	margin-right: 13px;
}
.content_btn_edit {
	min-width: 70px;
	font-weight: 600;
	margin-left: 5px;
	color: #617f9e;
}
.card {
	border-radius: 5px;
	box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
	background: #ffffff;
	margin-bottom: 75px;
	&-title {
		background: #f3f2f7;
		padding: 16px 20px;
		margin-bottom: 0;
		.title {
			font-size: 1.125rem;
			font-weight: 600;
			margin-bottom: 0;
		}
	}
	&-body {
		padding: 35px 30px 40px;
	}
	&-info {
		.title {
			font-size: 1.125rem;
			font-weight: 700;
			margin-top: 28px;
		}
	}
	&-land {
		position: relative;
		padding: 0;
	}
}
.card__order {
	max-width: 50%;
	margin-bottom: 1.25rem;
	@media (max-width: 767px) {
		max-width: 100%;
	}
}
.btn {
	&-white {
		max-height: none;

		line-height: 19.07px;
		margin-right: 15px;
		&:last-child {
			margin-right: 0;
		}
	}
	&-contain {
		margin-bottom: 55px;
	}
}
.d-grid {
	display: grid;
	grid-template-columns: 1fr 1fr;
	grid-column-gap: 8.9%;
	&:first-child {
		margin-top: 0;
	}
	&__checkbox {
		grid-template-columns: 1fr 1fr;
	}
	@media (max-width: 767px) {
		grid-template-columns: 1fr;
	}
}
.content {
	&-detail {
	}
	&-title {
		color: #555555;
		margin-bottom: 5px;

		font-weight: 500;
	}
	&-name {
		font-size: 1.125rem;
		color: #000000;
		margin-bottom: 15px;
		font-weight: 600;
		@media (max-width: 767px) {
		}
		&__code {
			color: #faa831;
		}
	}
}
.contain-table {
	@media (max-width: 767px) {
		overflow-y: hidden;
		overflow-x: auto;
	}
	.table-property {
		width: 100%;
	}
}
.table-property {
	width: 100%;
	font-weight: 500;
	color: #000000;
	text-align: center;
	thead {
		th {
			padding: 12px 5px;
			font-weight: 500;
		}
	}
	tbody {
		td {
			border: 1px solid #e5e5e5;
			&:first-child {
				border-left: none;
				width: 180px;
			}
			&:last-child {
				border-right: none;
			}
			box-sizing: border-box;
			padding: 14px;
		}
	}
}
.img-content {
	color: #000000;

	font-weight: 600;
	span {
		font-weight: 500;
		margin-left: 10px;
	}
}
.input-code {
	color: #000000;
	border-radius: 5px;
	width: 180px;
	border: 1px solid #000000;
	background: #f5f5f5;
	height: 35px;
	display: flex;
	align-items: center;
	justify-content: center;
	cursor: pointer;
}
.img-dropdown {
	cursor: pointer;
	width: 18px;
	&__hide {
		transform: rotate(90deg);
		transition: 0.3s;
	}
}
.img-contain {
	aspect-ratio: 1/1;
	overflow: hidden;
	img {
		height: 100%;
		cursor: pointer;
		object-fit: cover;
	}
	&__table {
		margin: auto;
		max-width: 50px;
		max-height: 50px;
		img {
			object-fit: cover;
			object-position: top;
			cursor: pointer;
			display: flex;
			justify-content: center;
			max-width: 50px;
			max-height: 50px;
		}
	}
}
.container-title {
	margin: -35px -95px auto;
	// padding: 35px 95px 0;
	padding: 15px 50px 10px 95px;
	// box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);

	.title {
		color: #007ec6;
		margin-top: 20px;
		font-size: 1.2rem;
		@media (max-width: 767px) {
			font-size: 1.125rem;
		}
	}
	&__footer {
		margin: auto -95px -35px;
		padding: 20px 95px 20px;
		@media (max-width: 767px) {
			.btn-white {
				margin-bottom: 20px;
			}
		}
	}
}
.container-img {
	padding: 0.75rem 0;
	border: 1px solid #0b0d10;
}
.traffic-light {
	color: black;
	padding: 0 5px;
	background: rgba(252, 194, 114, 0.53);
	width: fit-content;
}
.input-switch__detail {
	margin-bottom: 25px;
}
.container-table {
	border-radius: 5px;
	border: 1px solid #f3f2f7;
}
.heigh_div {
	min-height: 35px;
	border-bottom: 1px solid #e8e8e8;
}
.header_title {
	background: #007ec6;
	color: #f5f5f5;
	font-weight: 600;
	padding-left: 1.2rem;
	padding-top: 0.5rem;
}
.content_details_assets {
	padding-top: 0.5rem;
	padding-bottom: 0.5rem;
	font-weight: 500;
}
.title_details_assets {
	padding-top: 0.5rem;
	padding-bottom: 0.5rem;
	font-weight: 600;
	color: #617f9e;
}
.header_title_detail {
	color: #3d4d65 !important;
	background-color: rgba(222, 230, 238, 0.5);
}
.main_title {
	padding-top: 0.5rem;
	padding-bottom: 0.5rem;
	font-weight: 600;
}

.arrowBox {
	position: relative;
	background: #fbaf1c;
	height: 22px;
	line-height: 22px;
	text-align: center;
	color: #fff;
	font-weight: 400;
	font-size: 13px !important;
	display: inline-block;
	cursor: pointer;
	padding: 0 5px 0 0;
	margin-right: 5px;
}
.arrow-right:after {
	content: "";
	position: absolute;
	right: -11px;
	top: 0;
	border-top: 11px solid transparent;
	border-bottom: 11px solid transparent;
	border-left: 11px solid #fbaf1c;
}

.row {
	margin-right: unset !important;
	margin-left: unset !important;
}
.detail_certification_brief {
	padding: 0 1rem;
	margin-bottom: 80px;
}
.detail_certificate_1 {
	padding: 0.75rem;
	border-radius: 5px;
	border: 1px solid #b5e5ff;
	background-color: #eef9ff;
}
.detail_certificate_2 {
	padding: 0.75rem;
	border-radius: 5px;
	border: 1px solid #e8e8e8;
	background-color: #f6f7fb;
}
.margin_content_inline {
	margin-right: 10px;
}
.container_content {
	min-height: 20px;
	p {
		margin-bottom: unset !important;
	}
}

.content_id {
	border-radius: 5px;
	padding: 2px 5px;
	font-weight: 500;
	// padding-left: 0.8rem;
	cursor: pointer;
	&_primary {
		color: #007ec6;
		border: 1px solid #007ec6;
	}
}
.btn_dropdown {
	border: white;
	border-radius: 5px;
	height: 35px;
	@media (max-width: 767px) {
		margin-top: 10px;
	}
}
.btn_group {
	@media (max-width: 767px) {
		width: 100%;
	}
	/deep/ .btn-secondary {
		background-image: none;
		border-color: none !important;
		box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25) !important;
	}
}
</style>
