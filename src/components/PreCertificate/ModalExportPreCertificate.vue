<template>
	<div>
		<ValidationObserver
			tag="form"
			ref="observer"
			@submit.prevent="validateBeforeSubmit"
		>
			<div
				class="modal-detail d-flex justify-content-center align-items-center"
			>
				<div class="card">
					<div class="header_title d-flex justify-content-between">
						<h2 class="title">Xuất dữ liệu tùy chỉnh</h2>
						<div class="btn--contain ">
							<div class="btn--cancel" @click="handleCancel">
								<img src="@/assets/icons/ic_cancel-1.svg" alt="cancel" />
							</div>
						</div>
					</div>
					<div style="padding:0px 20px" class="content-detail">
						<div class="input--search">
							<div class="row">
								<InputDatePickerRangeCondition
									class="col-12 col-md-12 col-lg-12 form-group-container marginTop"
									vid="search"
									format-date="DD/MM/YYYY"
									:startDateValue="form.fromDate"
									:endDateValue="form.toDate"
									@startDate="form.fromDate = $event"
									@endDate="form.toDate = $event"
									label="Từ ngày - đến ngày"
								/>
								<!-- <InputCategoryMulti
              v-model="form.createdBy"
              vid="info"
              label="Người tạo"
              :maxTagCount="1"
              class="col-12 col-md-6 col-lg-6 form-group-container marginTop"
              :options="optionCreateBy"
            /> -->
								<InputCategoryMulti
									v-model="form.status"
									vid="info"
									label="Trạng thái"
									:maxTagCount="1"
									class="col-12 form-group-container marginTop"
									:options="optionStatus"
								/>
								<!-- <InputCategory
                v-model="form.appraiser_perform_id"
                vid="appraiser_perform_id"
                label="Chuyên viên thực hiện"
                class="col-12 col-md-6 col-lg-6 form-group-container marginTop"
                :options="optionsPeformance"
            />
            <InputCategory
                v-model="form.appraiser_id"
                vid="appraiser_id"
                label="Thẩm định viên"
                class="col-12 col-md-6 col-lg-6 form-group-container marginTop"
                :options="optionsAppraiser"
            /> -->
								<!-- <InputCategory
                v-model="form.customer_id"
                vid="customer_id"
                class="col-12 col-md-4 col-lg-4 form-group-container marginTop"
                label="Đối tác"
                :options="optionsCustomer"
                /> -->
								<!-- <div class="col-12 mt-4">
              <label class="form-label font-weight-bold">Trạng thái</label>
                <button-checkbox :options="statusOptions" :value="form.status" @change="onChangeStatus" />
            </div> -->
							</div>
						</div>
						<div class="d-flex justify-content-end my-3">
							<button
								:class="{ 'btn_loading disabled': isSubmit }"
								class="btn btn-white btn-orange text-nowrap"
								@click.prevent="exportData(form)"
							>
								Xuất dữ liệu
							</button>
						</div>
					</div>
				</div>
			</div>
		</ValidationObserver>
	</div>
</template>

<script>
import InputText from "@/components/Form/InputText";
import InputNumberFormat from "@/components/Form/InputNumber";
import InputCategoryMulti from "@/components/Form/InputCategoryMulti";
import InputDatePickerRangeCondition from "@/components/Form/InputDatePickerRangeCondition";
import WareHouse from "@/models/WareHouse";
import Certificate from "@/models/Certificate";
import PreCertificate from "@/models/PreCertificate";
import ButtonCheckbox from "@/components/Form/ButtonCheckbox";
import InputDatePicker from "@/components/Form/InputDatePicker";
import InputCategory from "@/components/Form/InputCategory";
import moment from "moment";
export default {
	name: "ModalSearchAppraise",
	components: {
		InputCategoryMulti,
		InputNumberFormat,
		InputDatePickerRangeCondition,
		InputText,
		ButtonCheckbox,
		InputDatePicker,
		InputCategory
	},
	props: ["statusOptions"],
	data() {
		return {
			col_4: "col-12 col-md-4 col-lg-4",
			col_12: "col-12 col-md-12 col-lg-12",
			users: [],
			isSubmit: false,
			employeePerformance: [],
			appraisers: [],
			customers: [],
			form: {
				createdBy: [],
				fromDate: "",
				toDate: "",
				status: [],
				appraiser_perform_id: "",
				business_manager_id: "",
				appraiser_sale_id: "",
				customer_id: ""
			}
		};
	},
	created() {},
	computed: {
		optionCreateBy() {
			return {
				data: this.users,
				id: "id",
				key: "name"
			};
		},
		optionStatus() {
			return {
				data: this.statusOptions.data,
				id: "value",
				key: "label"
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
		optionsCustomer() {
			return {
				data: this.customers,
				id: "id",
				key: "name",
				phone: "phone"
			};
		}
	},
	methods: {
		async getProfiles() {
			const profile = this.$store.getters.profile;
			if (profile && profile.data.user.roles[0].name.slice(-5) === "ADMIN") {
				this.activeStatus = true;
			}
		},
		formatNumber(value) {
			let num = (value / 1).toFixed(0).replace(".", ",");
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
		},
		async exportData(data) {
			await this.handleExportData(data);
			await this.$emit("cancel");
		},
		async handleExportData(data) {
			this.isSubmit = true;
			const res = await PreCertificate.exportDataPreCertification(data);
			if (res.data) {
				const fileLink = document.createElement("a");
				fileLink.href = res.data.url;
				fileLink.setAttribute("download", res.data.file_name);
				document.body.appendChild(fileLink);
				fileLink.click();
				fileLink.remove();
				window.URL.revokeObjectURL(fileLink);
				this.$toast.open({
					message: "Xuất dữ liệu thành công",
					type: "success",
					duration: 3000,
					position: "top-right"
				});
			} else if (res.error) {
				this.$toast.open({
					message: res.error.message,
					type: "error",
					duration: 3000,
					position: "top-right"
				});
			}
			this.isSubmit = false;
		},
		handleCancel(event) {
			this.$emit("cancel", event);
		},
		onChangeStatus(value) {
			this.form.status = value;
		},
		async getCustomer() {
			const res = await CertificationBrief.getCustomer();
			if (res.data) {
				this.customers = res.data;
			}
			this.render_price_fee += 1;
		},
		async getAppraisers() {
			const resp = await Certificate.getAppraisers();
			let dataAppraise = await [...resp.data];
			this.employeePerformance = await dataAppraise.filter(
				item => item.appraise_position.acronym === "CHUYEN-VIEN-THAM-DINH"
			);
			// this.employeeBusiness = await dataAppraise.filter(item => item.appraise_position.acronym === 'CHUYEN-VIEN-KINH-DOANH')
			// this.appraisersManager = await dataAppraise.filter(item => item.appraise_position.acronym === 'TONG-GIAM-DOC')
			let appraiser = dataAppraise.filter(item => item.appraiser_number !== "");
			this.appraisers = await appraiser;
			// if (this.form && this.form.step_1.appraiser_id) {
			// 	const filterData = await appraiser.filter(item => item.id !== this.form.step_1.appraiser_id && item.id !== this.form.step_1.appraiser_manager_id)
			// 	this.signAppraisers = await filterData
			// } else {
			// 	this.signAppraisers = await this.appraisers
			// }
		},
		async validateBeforeSubmit() {
			const isValid = await this.$refs.observer.validate();
			if (isValid) {
			}
		},
		disabledDate(current, type) {
			let from_date = "";
			let from_date_default = "";
			if (this.form.fromDate) {
				from_date = moment(this.form.fromDate, "DD/MM/YYYY").format(
					"YYYY-MM-DD"
				);
				from_date_default = new Date(
					moment(this.form.fromDate, "DD/MM/YYYY").format("YYYY-MM-DD")
				);
				from_date = new Date(
					new Date(from_date).setMonth(new Date(from_date).getMonth() + 3)
				);
				return (
					(current && current.valueOf() > from_date) ||
					(current && current.valueOf() < from_date_default)
				);
			} else {
				return current && current.valueOf() > Date.now();
			}
		},
		changeFromDate(value) {
			if (value) {
				this.form.fromDate = value;
			}
		},
		changeToDate(value) {
			if (value) {
				this.form.toDate = value;
			}
		},
		async getUser() {
			try {
				const resp = await WareHouse.getUser();
				this.users = [...resp.data];
			} catch (err) {
				this.isSubmit = false;
				throw err;
			}
		}
	},
	beforeMount() {
		this.form.fromDate = moment()
			.subtract(3, "months")
			.format("DD/MM/YYYY");
		this.form.toDate = moment().format("DD/MM/YYYY");
		this.getProfiles();
		this.getUser();
		this.getAppraisers();
		// this.getCustomer()
	}
};
</script>

<style lang="scss" scoped>
.modal-detail {
	position: fixed;
	z-index: 1031;
	left: 0;
	top: 0;
	width: 100%;
	height: 100%;
	background: rgba(0, 0, 0, 0.6);
	@media (max-width: 787px) {
		padding: 20px;
	}
	.card {
		border-radius: 5px;
		box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
		max-width: 800px;
		width: 100%;
		max-height: 90vh;
		margin-bottom: 0;
		padding: 20px 20px;
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
.content-detail {
	overflow-y: auto;
	overflow-x: hidden;
}
.btn {
	&--contain {
		display: flex;
		justify-content: flex-end;
		margin-bottom: 18px;
	}
	&--cancel {
		width: 13px;
		height: auto;
		cursor: pointer;
		img {
			width: 100%;
		}
	}
	&--more {
		padding: 9px;
		color: #ffffff;
		background: #faa831;
		box-shadow: 0 1px 4px rgba(250, 168, 49, 0.25);
		border-radius: 5px;
		margin-top: 26px;
		font-weight: 600;
		font-size: 12px;
	}
	&--search {
		padding: 10px 48px;
		color: #ffffff;
		background: #faa831;
		border-radius: 5px;
		margin-top: 40px;
		margin-bottom: 20px;
		font-weight: 700;
		font-size: 1.2rem;
		float: right;
	}
	.icon {
		font-weight: 700;
		margin-left: 10px;
		transition: 0.3s;
		&__hide {
			transform: rotate(180deg) !important;
			transition: 0.3s;
		}
	}
}
.input {
	&-grid {
		margin-top: 28px;
		display: grid;
		grid-template-columns: 1fr 341px 1fr;
		grid-column-gap: 33px;
		grid-row-gap: 25px;
		&__info {
			white-space: nowrap !important;
			grid-template-columns: 1fr 1fr 1fr;
			margin-bottom: 27px;
		}
		@media (max-width: 768px) {
			grid-template-columns: 1fr;
		}
	}
}
.price-range {
	.line {
		margin: auto 11px;
	}
	.input-price {
		font-size: 12px;
		height: 25px;
		border-radius: 5px;
		border: 1px solid #555555;
		box-sizing: border-box;
		width: 48%;
		&:hover,
		&:active,
		&:focus {
			border: 1px solid #555555;
		}
	}
}
.hr {
	border: 1px solid #d9d9d9;
	background: #d9d9d9;
}
.slider {
	margin-top: 24px;
	position: relative;
	width: 80%;
	height: 5px;
	background: #d9d9d9;
	&-range {
		position: absolute;
		top: 0;
		height: 100%;
		width: 80%;
		background: #faa831;
	}
	&-handle {
		height: 14px;
		width: 14px;
		top: -6px;
		border-radius: 2px;
		border: solid 2px #c5a37d;
		background: #c5a37d;
	}
}
.range-slider {
	width: 100%;
	margin: auto;
	text-align: center;
	position: relative;
}

.range-slider input[type="range"] {
	position: absolute;
	left: 0;
	bottom: -24px;
	@media (max-width: 768px) {
		bottom: -55px;
	}
}

input[type="number"] {
	border: 1px solid #ddd;
	text-align: center;
	font-size: 1.6em;
	-moz-appearance: textfield;
}

input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
	-webkit-appearance: none;
}

input[type="number"]:invalid,
input[type="number"]:out-of-range {
	border: 2px solid #ff6347;
}

input[type="range"] {
	-webkit-appearance: none;
	width: 100%;
}

input[type="range"]:focus {
	outline: none;
}

input[type="range"]:focus::-webkit-slider-runnable-track {
	background: #faa831;
}

input[type="range"]:focus::-ms-fill-lower {
	background: #faa831;
}

input[type="range"]:focus::-ms-fill-upper {
	background: #faa831;
}

input[type="range"]::-webkit-slider-runnable-track {
	width: 100%;
	height: 5px;
	cursor: pointer;
	animate: 0.2s;
	background: #faa831;
	border-radius: 1px;
	box-shadow: none;
	border: 0;
}

input[type="range"]::-webkit-slider-thumb {
	z-index: 2;
	position: relative;
	box-shadow: 0 0 0 #000;
	border: 1px solid #faa831;
	height: 18px;
	width: 18px;
	border-radius: 25px;
	background: #f8bd6e;
	cursor: pointer;
	-webkit-appearance: none;
	margin-top: -7px;
}
.row__total {
	margin-top: 44px;
}
.total-title {
	color: #000000;

	font-weight: 600;
	@media (max-width: 768px) {
		padding: 20px 0;
	}
}
//   .row{
//     margin-right: -26px;
//     margin-left: -26px;
//   }
//   .row>*{
//     padding-right: 26px;
//     padding-left: 26px;
//   }
.marginTop {
	margin-top: 10px;
}
.title {
	color: #007ec6;
	font-weight: 600;
	margin-bottom: 15px;
	font-size: 1.2rem;
}
.header_title {
	border-bottom: 1px solid #e8e8e8;
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
/deep/ {
	.form-group-container.disabled {
		background-color: rgba(222, 230, 238, 0.3);

		.ant-input {
			background-color: rgba(222, 230, 238, 0.3) !important;
		}
	}
}
</style>
