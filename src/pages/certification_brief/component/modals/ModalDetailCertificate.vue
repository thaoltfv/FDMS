<template>
	<div>
		<div
			class="modal-detail d-flex justify-content-center align-items-center"
			@click.self="handleCancel"
		>
			<div class="card" :style="isMobile() ? { 'margin-top': '-55px' } : {}">
				<div
					class="container-title"
					:style="
						isMobile() ? { 'padding-bottom': '0', 'margin-bottom': '0' } : {}
					"
				>
					<div class="d-flex justify-content-between">
						<h2 class="title">Thông tin chung</h2>
						<img
							height="35px"
							@click="handleCancel"
							class="cancel"
							src="../../../../assets/icons/ic_cancel_2.svg"
							alt=""
						/>
					</div>
				</div>
				<div
					class="contain-detail"
					:style="isMobile() ? { 'padding-top': '0' } : {}"
				>
					<div class="detail_certificate_1 col-12 mb-2">
						<div class="col-12 d-flex mb-2 row justify-content-between">
							<span class="content_id content_id_primary class_p">{{
								`HSTĐ ${idData}`
							}}</span>
							<span
								v-if="form.pre_certificate_id"
								@click="handleDetailPreCertificate(form.pre_certificate_id)"
								class="  arrowBox arrow-right"
								id="pre_certificate_id"
							>
								<icon-base name="nav_ycsb" class="item-icon svg-inline--fa" />
								{{ `YCSB_${form.pre_certificate_id}` }}
								<b-tooltip target="pre_certificate_id" placement="right">{{
									`Được chuyển tiếp từ YCSB_${form.pre_certificate_id}`
								}}</b-tooltip>
							</span>
						</div>
						<div class="d-flex container_content justify-content-between">
							<div class="d-flex container_content">
								<strong class="margin_content_inline">Khách hàng:</strong>
								<p>{{ form.petitioner_name }}</p>
							</div>
						</div>
						<div class="d-flex container_content">
							<strong class="margin_content_inline">Địa chỉ:</strong>
							<p>{{ form.petitioner_address }}</p>
						</div>
						<div class="d-flex container_content">
							<strong class="margin_content_inline"
								>MST/CMND/CCCD/Passport:</strong
							>
							<p>{{ form.petitioner_identity_card }}</p>
						</div>
						<div class="d-flex container_content">
							<strong class="margin_content_inline">Điện thoại:</strong>
							<p>{{ form.petitioner_phone }}</p>
						</div>
						<div class="d-flex container_content">
							<strong class="margin_content_inline">Mục đích thẩm định:</strong>
							<p>
								{{ form.appraise_purpose ? form.appraise_purpose.name : "" }}
							</p>
						</div>
						<div class="d-flex container_content">
							<strong class="margin_content_inline"
								>Thời điểm thẩm định:</strong
							>
							<p>
								{{ form.appraise_date ? formatDate(form.appraise_date) : "" }}
							</p>
						</div>
						<div class="d-flex container_content">
							<strong class="margin_content_inline">Hợp đồng:</strong>
							<p class="margin_content_inline">Số: {{ form.document_num }}</p>
							<p>
								Ngày:
								{{ form.document_date ? formatDate(form.document_date) : "" }}
							</p>
						</div>
						<div class="d-flex container_content">
							<strong class="margin_content_inline">Chứng thư:</strong>
							<p class="margin_content_inline">
								Số: {{ form.certificate_num }}
							</p>
							<p>
								Ngày:
								{{
									form.certificate_date ? formatDate(form.certificate_date) : ""
								}}
							</p>
						</div>
						<div class="d-flex container_content">
							<strong class="margin_content_inline">Tổng phí dịch vụ:</strong>
							<p>
								{{ form.service_fee ? formatNumber(form.service_fee) : 0 }}đ
							</p>
						</div>
						<div class="d-flex container_content">
							<strong class="margin_content_inline">Chiết khấu:</strong>
							<p>{{ form.commission_fee ? form.commission_fee : 0 }}%</p>
						</div>
						<div class="d-flex container_content">
							<strong class="margin_content_inline">Ghi chú:</strong
							><span id="note" class="text-left">{{
								form.note && form.note.length > 25
									? form.note.substring(25, 0) + "..."
									: form.note
							}}</span>
							<b-tooltip target="note" placement="top-right">{{
								form.note
							}}</b-tooltip>
						</div>
					</div>
					<div class="col-12 mb-2">
						<div class="detail_certificate_2">
							<div class="d-flex container_content">
								<strong class="margin_content_inline">Đối tác:</strong>
								<p>{{ form.customer ? form.customer.name : "" }}</p>
							</div>
							<div class="d-flex container_content">
								<strong class="margin_content_inline">Địa chỉ:</strong>
								<p>{{ form.customer ? form.customer.address : "" }}</p>
							</div>
							<div class="d-flex container_content">
								<strong class="margin_content_inline">Liên hệ:</strong>
								<p>{{ form.customer ? form.customer.phone : "" }}</p>
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
										{{ form.appraiser_sale ? form.appraiser_sale.name : "" }}
									</p>
								</div>
							</div>
							<div class="d-flex container_content">
								<strong class="margin_content_inline"
									>Chuyên viên thực hiện:</strong
								>
								<p>
									{{
										form.appraiser_perform ? form.appraiser_perform.name : ""
									}}
								</p>
							</div>
							<div class="d-flex container_content">
								<strong class="margin_content_inline">Kiểm soát viên:</strong>
								<p>
									{{
										form.appraiser_control ? form.appraiser_control.name : ""
									}}
								</p>
							</div>
							<div class="d-flex container_content">
								<strong class="margin_content_inline">Thẩm định viên:</strong>
								<p>{{ form.appraiser ? form.appraiser.name : "" }}</p>
							</div>

							<div class="d-flex container_content">
								<strong class="margin_content_inline"
									>Đại diện theo pháp luật:</strong
								>
								<p>
									{{
										form.appraiser_manager ? form.appraiser_manager.name : ""
									}}
								</p>
							</div>
							<div class="d-flex container_content">
								<strong class="margin_content_inline"
									>Đại diện ủy quyền:</strong
								>
								<p>
									{{
										form.appraiser_confirm ? form.appraiser_confirm.name : ""
									}}
								</p>
							</div>
						</div>
					</div>
					<div
						v-if="!isMobile()"
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
								/>{{ target.description }}
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
												target.description
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
		<ModalAppraisal
			:key="key_render_appraisal"
			v-if="showAppraisalDialog"
			:data="form"
			:idData="idData"
			:status="2"
			requiredAppraiserPerform="required"
			:requiredAppraiser="null"
			@cancel="handleCancelAppraisal"
			@updateAppraisal="updateAppraisal"
		/>
		<ModalAppraisal
			:key="key_render_appraisal"
			v-if="showVerifyCertificate"
			:data="form"
			:idData="idData"
			:status="3"
			requiredAppraiserPerform="required"
			requiredAppraiser="required"
			@cancel="handleCancelVerify"
			@updateAppraisal="handleChangeVerify"
		/>
		<ModalSendVerify
			v-if="showAcceptCertificate"
			:notification="`Bạn có muốn muốn '${targetMessage}' hồ sơ này`"
			@action="handleChangeAccept"
			@cancel="handleCancelAccept"
		/>
	</div>
</template>

<script>
import ModalAppraisal from "./ModalAppraisal";
import ModalSendVerify from "@/components/Modal/ModalSendVerify";
import InputText from "@/components/Form/InputText";
import InputCategory from "@/components/Form/InputCategory";
import FileUpload from "@/components/file/FileUpload";
import InputTextPrefixCustom from "@/components/Form/InputTextPrefixCustom";
import InputDatePicker from "@/components/Form/InputDatePicker";
import InputCurrency from "@/components/Form/InputCurrency";
import moment from "moment";
import CertificationBrief from "@/models/CertificationBrief";
import {
	BTooltip,
	BDropdown,
	BDropdownItem,
	BButtonGroup
} from "bootstrap-vue";
import IconBase from "@/components/IconBase.vue";

export default {
	name: "ModalAppraiseInformation",
	props: [
		"data",
		"idData",
		"edit",
		"add",
		"user_id",
		"appraiser_number",
		"jsonConfig",
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
			showAcceptCertificate: false,
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
		ModalAppraisal,
		ModalSendVerify,
		"b-tooltip": BTooltip,
		"b-dropdown-item": BDropdownItem,
		"b-button-group": BButtonGroup,
		"b-dropdown": BDropdown
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
		getTargetDescription() {
			let data = [];
			if (this.isPermission) {
				data = this.targetDescription;
			}
			return data;
		},
		handleUpdateStatus(id) {
			if (
				this.form.status === 1 &&
				(this.user_id === this.form.appraiser_sale.user_id ||
					this.$store.getters.profile.data.user.id === this.form.created_by)
			) {
				this.showAppraisalDialog = true;
			} else if (
				this.form.status === 2 &&
				this.user_id === this.form.appraiser_perform.user_id
			) {
				this.showVerifyCertificate = true;
			} else if (this.form.status === 3 && this.appraiser_number) {
				this.showAcceptCertificate = true;
			}
		},
		handleDetailPreCertificate(id) {
			let url = this.$router.resolve({
				name: "pre_certification.detail",
				query: {
					id: id.toString()
				}
			}).href;

			window.open(url, "_blank");
		},
		handleCancelAppraisal() {
			this.showAppraisalDialog = false;
		},
		updateAppraisal() {
			this.$emit("action", this.idData, 2);
		},
		handleChangeVerify() {
			this.$emit("action", this.idData, 3);
		},
		handleCancelVerify() {
			this.showVerifyCertificate = false;
		},

		formatDate(date) {
			return moment(date).format("DD/MM/YYYY");
		},
		formatNumber(num) {
			// convert number to dot format
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
					name: "certification_brief.detail",
					query: {
						id: id
					}
				})
				.catch(_ => {});
		},

		handleCancel(event) {
			this.$emit("cancel", event);
		},
		handleTarget(target) {
			this.targetConfig = this.jsonConfig.principle.find(
				i => i.id === target.id
			);
			this.targetMessage = target.description;
			if (this.targetConfig) {
				this.showAcceptCertificate = true;
			}
		},
		getExpireStatusDate(config) {
			let dateConvert = new Date();
			let minutes = config.process_time ? config.process_time : 1440;
			let dateConverted = new Date(dateConvert.getTime() + minutes * 60000);
			let status_expired_at = moment(dateConverted).format("DD-MM-YYYY HH:mm");
			return status_expired_at;
		},
		async handleChangeAccept() {
			let dataSend = {
				appraiser_confirm_id: this.form.appraiser_confirm_id,
				appraiser_id: this.form.appraiser_id,
				appraiser_manager_id: this.form.appraiser_manager_id,
				appraiser_control_id: this.form.appraiser_control_id,
				appraiser_perform_id: this.form.appraiser_perform_id,
				status: this.targetConfig.status,
				sub_status: this.targetConfig.sub_status,
				check_price: this.targetConfig.require.check_price,
				status_expired_at: this.getExpireStatusDate(this.targetConfig)
			};
			this.$emit("action", this.idData, dataSend, this.targetMessage);
			this.handleCancelAccept();
		},
		handleCancelAccept() {
			this.showAcceptCertificate = false;
		},
		async validateAppraiseInformation() {
			const isValid = await this.$refs.appraise_information.validate();
			if (isValid) {
				this.handleAction();
			}
		},
		async getDetailCertificate() {
			const res = await CertificationBrief.getDetailCertificateBrief(
				this.idData
			);
			if (res.data) {
				this.form = res.data;
			} else {
				await this.$toast.open({
					message: "Lấy dữ liệu thất bại",
					type: "error",
					position: "top-right"
				});
			}
		},
		handleAction() {},
		loadConfigByStatus(status, sub_status) {
			return this.jsonConfig.principle.find(
				item =>
					item.status === status &&
					item.sub_status === sub_status &&
					item.isActive === 1
			);
		},
		loadConfigData(configData) {
			this.config = configData;
			this.requireData = configData.require;
			this.targetDescription = configData.target_description;
		},
		checkPermission(configData) {
			let check = false;
			if (configData.put_require && configData.put_require.length > 0) {
				configData.put_require.forEach(i => {
					if (
						(i === "created_by" && this.form[i] === this.user.id) ||
						(i !== "created_by" && this.form[i] === this.user.appraiser.id)
					) {
						check = true;
					}
				});
			}
			return check;
		},
		configStatus() {
			this.user = this.profile.data.user;
			let configData = this.loadConfigByStatus(
				this.form.status,
				this.form.sub_status
			);
			if (configData) {
				this.loadConfigData(configData);
				this.isPermission = this.checkPermission(configData);
			}
		},
		handleFooterAccept(target) {
			this.$emit("handleFooterAccept", target);
		},
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
.arrowBox {
	position: relative;
	background: #007ec6;
	height: 22px;
	line-height: 22px;
	text-align: center;
	color: #fff;
	font-weight: 400;
	font-size: 13px !important;
	display: inline-block;
	cursor: pointer;
	padding: 0 10px 0 0;
}
.arrow-right:after {
	content: "";
	position: absolute;
	left: -11px;
	top: 0;
	border-top: 11px solid transparent;
	border-bottom: 11px solid transparent;
	border-right: 11px solid #007ec6;
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
