<template>
	<div>
		<div
			class="modal-detail d-flex justify-content-center align-items-center"
			@click.self="handleCancel"
		>
			<div class="card">
				<div class="container-title">
					<div class="d-flex justify-content-between">
						<h2 class="title">Chỉnh sửa ghi chú</h2>
						<img
							height="35px"
							@click="handleCancel"
							class="cancel"
							src="../../../../assets/icons/ic_cancel_2.svg"
							alt=""
						/>
					</div>
				</div>
				<div class="contain-detail">
					<ValidationObserver
						tag="form"
						ref="appraise_information"
						@submit.prevent="validateAppraiseInformation"
					>
						<div class="row">
							<div class="col-lg-12 col-sm-12">
								<InputTextarea
									:autosize="true"
									v-model="form.note"
									vid="note"
									label="Ghi chú"
									rows="1"
									class="form-group-container label-none"
								/>
							</div>
						</div>
						<div
							class=" d-lg-flex d-block justify-content-end align-items-center mt-3 mb-2"
						>
							<div class="d-lg-flex d-block button-contain">
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
								<button class="btn btn-orange btn-action-modal" type="submit">
									<img
										src="@/assets/icons/ic_save.svg"
										style="margin-right: 12px"
										alt="save"
									/>
									Lưu
								</button>
							</div>
						</div>
					</ValidationObserver>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import InputText from "@/components/Form/InputText";
import InputCategory from "@/components/Form/InputCategory";
import FileUpload from "@/components/file/FileUpload";
import InputTextPrefixCustom from "@/components/Form/InputTextPrefixCustom";
import InputTextPrefixCustomIcon from "@/components/Form/InputTextPrefixCustomIcon";
import InputDatePicker from "@/components/Form/InputDatePicker";
import InputDatePickerV2 from "@/components/Form/InputDatePickerV2";
import InputCurrency from "@/components/Form/InputCurrency";
import InputPercent from "@/components/Form/InputPercent";
import Certificate from "@/models/Certificate";
import moment from "moment";
import CertificateBrief from "@/models/CertificationBrief";
import InputCategoryMulti from "@/components/Form/InputCategoryMulti";
import InputTextarea from "@/components/Form/InputTextarea";

export default {
	name: "ModalAppraiseInformation",
	props: {
		data: Object,
		idData: [String, Number],
		typeAppraiseProperty: String,
		editDocument: {
			type: Boolean,
			default: false
		}
	},
	data() {
		return {
			isOneItem: false,
			isTwoItem: false,
			isThreeItem: false,
			form: this.data ? JSON.parse(JSON.stringify(this.data)) : {},
			appraisalPurposes: []
		};
	},
	components: {
		FileUpload,
		InputCategory,
		InputText,
		InputTextPrefixCustom,
		InputDatePicker,
		InputDatePickerV2,
		InputCurrency,
		InputPercent,
		InputTextPrefixCustomIcon,
		InputCategoryMulti,
		InputTextarea
	},
	computed: {
		optionsLoaiKH() {
			return {
				data: [
					{ id: 0, name: "Khách hàng cá nhân" },
					{ id: 1, name: "Khách hàng doanh nghiệp" }
				],
				id: "id",
				key: "name"
			};
		},
		optionsLoaiHs() {
			return {
				data: [
					{ id: 0, name: "Biểu mẫu gốc" },
					{ id: 1, name: "Biểu mẫu Shinhan" }
				],
				id: "id",
				key: "name"
			};
		},
		optionsAppraisalPurposes() {
			return {
				data: this.appraisalPurposes,
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
		}
	},
	created() {
		this.getAppraiseOthers();
		console.log("form", this.form);
	},
	methods: {
		async getAppraiseOthers() {
			const resp = await Certificate.getAppraiseOthers();
			// console.log('resp', resp)
			this.appraisalPurposes = [...resp.data.muc_dich_tham_dinh_gia];
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
		async getDataEdit() {
			// console.log('vào đây nè')
			this.form.document_date = this.data.document_date
				? moment(this.data.document_date).format("DD/MM/YYYY")
				: "";
			this.form.certificate_date = this.data.certificate_date
				? moment(this.data.certificate_date).format("DD/MM/YYYY")
				: "";
			this.form.appraise_date = this.data.appraise_date
				? moment(this.data.appraise_date).format("DD/MM/YYYY")
				: "";
			this.form.issue_date_card = this.data.issue_date_card
				? moment(this.data.issue_date_card).format("DD/MM/YYYY")
				: "";
			this.form.survey_time = this.data.survey_time
				? moment(this.data.survey_time).format("DD-MM-YYYY HH:mm")
				: "";
			this.form.appraise_purpose_id = this.data.appraise_purpose_id;
			this.form.note = this.data.note;
		},
		disabledDate(current) {
			if (
				this.form.document_date !== "" &&
				this.form.document_date !== undefined &&
				this.form.document_date !== null
			) {
				let dateDoc = (" " + this.form.document_date).slice(1);
				dateDoc = moment(dateDoc, "DD/MM/YYYY").format("YYYY-MM-DD");
				return current <= moment(dateDoc);
			} else {
				return current >= moment().endOf("day");
			}
		},
		changeDocumentDate(event) {
			this.form.document_date = event;
			if (event && event !== "") {
				if (
					moment(this.form.document_date).endOf("day") <
					moment(this.form.certificate_date)
				) {
					this.form.certificate_date = "";
				}
			} else {
				this.form.document_date = "";
			}
			this.form.appraise_date = this.form.document_date;
		},
		changeServiceFee(event) {
			this.form.service_fee = event;
		},
		changeCommissionFee(event) {
			this.form.commission_fee = event;
		},
		changeAppraiseDate(event) {
			this.form.appraise_date = event;
		},
		changeSurveyDate(event) {
			this.form.survey_time = event;
		},
		changeIssueDate(event) {
			this.form.issue_date_card = event;
		},
		changeCertificateDate(event) {
			this.form.certificate_date = event;
		},
		handleChangeAppraisePurpose(event) {
			// this.form.appraise_purpose_id = event
			this.form.appraise_purpose_id = event;
		},
		handleCancel(event) {
			this.$emit("cancel", event);
		},
		async validateAppraiseInformation() {
			this.handleAction();
		},
		async handleAction() {
			let form = this.form;
			const data = {
				// data mới
				issue_date_card: form.issue_date_card,
				issue_place_card: form.issue_place_card,
				name_contact: form.name_contact,
				phone_contact: form.phone_contact,
				survey_location: form.survey_location,
				survey_time: form.survey_time,
				petitioner_name: form.petitioner_name,
				document_num: form.document_num,
				petitioner_address: form.petitioner_address,
				document_date: form.document_date,
				petitioner_phone: form.petitioner_phone,
				service_fee: form.service_fee,
				appraise_purpose_id: form.appraise_purpose_id,
				certificate_num: form.certificate_num,
				certificate_date: form.certificate_date,
				appraise_date: form.appraise_date,
				commission_fee: form.commission_fee,
				petitioner_identity_card: form.petitioner_identity_card,
				document_type: form.document_type,
				document_alter_by_bank: form.document_alter_by_bank,
				is_company: form.is_company,
				note: form.note
			};
			const res = await CertificateBrief.updateDetailCertificate(
				this.idData,
				data
			);
			// console.log('res',res)
			// console.log('this.idData', this.idData)
			if (res.data) {
				this.$toast.open({
					message: "Lưu thông tin hồ sơ thẩm định thành công",
					type: "success",
					position: "top-right"
				});
				// console.log('res.data', res.data)
				this.$emit("updateAppraiseInformation", res.data);
				this.$emit("cancel");
			} else if (res.error) {
				this.$toast.open({
					message: `${res.error.message}`,
					type: "error",
					position: "top-right"
				});
			} else {
				this.$toast.open({
					message: "Lưu thất bại",
					type: "error",
					position: "top-right"
				});
			}
		}
	},
	beforeMount() {
		this.getDataEdit();
	}
};
</script>

<style lang="scss" scoped>
.title {
	font-size: 1.2rem;
	font-weight: 700;
	margin-bottom: 15px;
	color: #000000;
}
.modal-detail {
	position: fixed;
	z-index: 1031;
	left: 0;
	top: 0;
	width: 100%;
	height: 100%;
	background: rgba(0, 0, 0, 0.6);
	.card {
		border-radius: 5px;
		box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
		max-width: 500px;
		width: 100%;
		max-height: 50vh;
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
		padding-top: 15px;
		&::-webkit-scrollbar {
			width: 2px;
		}
	}
	&-title {
		background: #f3f2f7;
		padding: 16px 20px;
		margin-bottom: 0;
		.title {
			font-size: 1.2rem;
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
			font-size: 1.2rem;
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
			font-size: 1.2rem;
			font-weight: 600;
			margin-bottom: 0;
		}
	}
	&-body {
		padding: 35px 30px 40px;
	}
	&-info {
		.title {
			font-size: 1.2rem;
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
		font-size: 1.125rem;
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
		font-size: 1.125rem;
		font-weight: 500;
	}
	&-name {
		font-size: 1.2rem;
		color: #000000;
		margin-bottom: 15px;
		font-weight: 600;
		@media (max-width: 767px) {
			font-size: 1.125rem;
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
	font-size: 1.125rem;
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
			font-size: 1.2rem;
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
.row {
	margin-right: unset !important;
	margin-left: unset !important;
}
.input_right {
	padding-right: 0px;
}
.input_left {
	padding-left: 0px;
}
</style>
