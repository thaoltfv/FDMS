<template>
	<div>
		<div
			class="modal-detail d-flex justify-content-center align-items-center"
			@click.self="handleCancel"
		>
			<div class="card">
				<div class="container-title">
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
				<div class="contain-detail">
					<ValidationObserver
						tag="form"
						ref="appraise_information"
						@submit.prevent="validateAppraiseInformation"
					>
						<div class="row">
							<div class="col-6">
								<InputTextPrefixCustom
									id="petitioner_name"
									placeholder="Ông / Bà"
									v-model="dataForm.petitioner_name"
									vid="petitioner_name"
									:iconUser="true"
									:showIcon="true"
									label="Tên khách hàng yêu cầu (trên chứng thư)"
									rules="required"
									class="form-group-container input_certification_brief"
								/>
							</div>

							<div class="col-6">
								<InputTextPrefixCustom
									id="petitioner_address"
									placeholder="Nhập địa chỉ của khách hàng"
									v-model="dataForm.petitioner_address"
									vid="petitioner_address"
									:iconLocation="true"
									:showIcon="true"
									label="Địa chỉ"
									class="form-group-container input_certification_brief"
								/>
							</div>

							<div class="col-6">
								<InputTextPrefixCustomIcon
									id="petitioner_identity_card"
									placeholder="Nhập MST/CMND/CCCD/Passport"
									v-model="dataForm.petitioner_identity_card"
									class="form-group-container input_certification_brief"
									vid="petitioner_identity_card"
									icon="ic_id_card_2"
									:showCustomIcon="true"
									label="MST/CMND/CCCD/Passport"
								/>
							</div>

							<div class="col-6">
								<InputTextPrefixCustom
									id="petitioner_phone"
									placeholder="Nhập số điện thoại"
									v-model="dataForm.petitioner_phone"
									class="form-group-container input_certification_brief"
									vid="petitioner_phone"
									:iconPhone="true"
									:showIcon="true"
									label="Điện thoại"
								/>
							</div>
							<div class="col-6">
								<InputCategory
									v-model="dataForm.appraise_purpose_id"
									class="form-group-container"
									vid="appraise_purpose_id"
									label="Mục đích thẩm định"
									:requiredIcon="true"
									rules="required"
									:options="optionsAppraisalPurposes"
									@change="handleChangeAppraisePurpose"
								/>
							</div>
							<div class="col-6">
								<InputTextarea
									:rows="1"
									:disableInput="false"
									v-model="dataForm.pre_asset_name"
									label="Tên tài sản sơ bộ"
									rules="required"
									:requiredIcon="true"
									class="form-group-container"
								/>
							</div>
						</div>
						<div class="row justify-content-between">
							<InputCurrency
								v-model="dataForm.total_service_fee"
								vid="total_service_fee"
								:max="99999999999999"
								label="Tổng phí dịch vụ"
								class="form-group-container col-sm-12 col-md-6"
								@change="dataForm.total_service_fee = $event"
							/>
							<InputPercent
								v-model="dataForm.commission_fee"
								label="Chiết khấu"
								vid="test"
								:max="100"
								:decimal="0"
								rules="required"
								class="form-group-container col-sm-12 col-md-6"
								@change="dataForm.commission_fee = $event"
							/>
						</div>
						<div class="row justify-content-between">
							<InputCategory
								v-model="dataForm.pre_type_id"
								vid="pre_type_id"
								label="Loại sơ bộ"
								:requiredIcon="true"
								rules="required"
								class="form-group-container col-sm-12 col-md-6"
								:options="optionsPreTypes"
							/>
							<InputDatePicker
								v-model="dataForm.pre_date"
								vid="pre_date"
								label="Thời điểm sơ bộ"
								placeholder="Ngày / tháng / năm"
								rules="required"
								:formatDate="'DD/MM/YYYY'"
								class="form-group-container col-sm-12 col-md-6"
								@change="handleChangeTime"
							/>
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
				<!-- <div class="container-title container-title__footer">
            <div class="d-flex justify-content-between justify-content-lg-end">
              <button class="btn btn-white btn-action-modal" type="button" @click="handleCancel"><img src="@/assets/icons/ic_cancel.svg"  style="margin-right: 12px" alt="save">Trở lại</button>
              <button class="btn btn-orange btn-action-modal" type="button" @click="handleAction"> <img src="@/assets/icons/ic_save.svg" style="margin-right: 12px" alt="save"> Lưu</button>
            </div>
          </div> -->
			</div>
		</div>
	</div>
</template>

<script>
import { ref } from "vue";
import { storeToRefs } from "pinia";
import { usePreCertificateStore } from "@/store/preCertificate";
import _ from "lodash";

import InputText from "@/components/Form/InputText";
import InputTextarea from "@/components/Form/InputTextarea";
import InputCategory from "@/components/Form/InputCategory";
import InputTextPrefixCustom from "@/components/Form/InputTextPrefixCustom";
import InputTextPrefixCustomIcon from "@/components/Form/InputTextPrefixCustomIcon";
import InputCurrency from "@/components/Form/InputCurrency";
import InputDatePicker from "@/components/Form/InputDatePicker";
import moment from "moment";
import InputPercent from "@/components/Form/InputPercent";

export default {
	name: "ModalPCAppraiseInformation",

	components: {
		InputTextarea,
		InputCategory,
		InputText,
		InputTextPrefixCustom,
		InputTextPrefixCustomIcon,
		InputDatePicker,
		InputCurrency,
		InputPercent
	},
	setup() {
		const preCertificateStore = usePreCertificateStore();
		const { dataPC, lstDataConfig } = storeToRefs(preCertificateStore);
		const dataForm = ref(_.cloneDeep(dataPC.value));

		const getStartData = async () => {
			if (!lstDataConfig.value.appraiser_purposes) {
				await preCertificateStore.getStartData(false, true, true, false);
			}
		};
		getStartData();
		const handleChangeAppraisePurpose = event => {
			dataForm.value.appraise_purpose_id = event;
		};
		const handleChangeTime = event => {
			dataForm.value.pre_date = event;
		};
		handleChangeTime(moment(dataForm.value.pre_date).format("DD/MM/YYYY"));
		return {
			dataForm,
			lstDataConfig,
			preCertificateStore,
			handleChangeAppraisePurpose,
			handleChangeTime
		};
	},
	computed: {
		pre_type_compute: {
			get: function() {
				return this.dataForm.pre_type;
			},
			set: function(newValue) {
				this.dataForm.pre_type = newValue.name;
			}
		},
		optionsAppraisalPurposes() {
			return {
				data: this.lstDataConfig.appraiser_purposes,
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
		}
	},
	created() {},
	methods: {
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

		disabledDate(current) {
			if (
				this.dataForm.document_date !== "" &&
				this.dataForm.document_date !== undefined &&
				this.dataForm.document_date !== null
			) {
				let dateDoc = (" " + this.dataForm.document_date).slice(1);
				dateDoc = moment(dateDoc, "DD/MM/YYYY").format("YYYY-MM-DD");
				return current <= moment(dateDoc);
			} else {
				return current >= moment().endOf("day");
			}
		},

		handleCancel(event) {
			this.$emit("cancel", event);
		},
		async validateAppraiseInformation() {
			const isValid = await this.$refs.appraise_information.validate();
			if (isValid) {
				this.handleAction();
			}
		},
		async handleAction() {
			const res = await this.preCertificateStore.createUpdatePreCertificateion(
				this.dataForm.id,
				true,
				this.dataForm
			);
			if (res.data) {
				this.$toast.open({
					message: "Lưu thông tin hồ sơ thẩm định thành công",
					type: "success",
					position: "top-right"
				});
				this.$emit("updateAppraiseInformation");
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
	beforeMount() {}
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
		max-width: 1300px;
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
