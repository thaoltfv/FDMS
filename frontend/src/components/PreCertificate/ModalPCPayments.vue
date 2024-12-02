<template>
	<div
		class="modal-detail d-flex justify-content-center align-items-center"
		@click.self="handleCancel"
	>
		<div class="card">
			<div class="container-title">
				<div class="d-flex justify-content-between" style="margin-left:20px;">
					<h2 class="title">Thông tin thanh toán</h2>
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
				<div class="d-flex-column">
					<div
						class="row"
						v-for="(payment, index) in dataForm.payments"
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
									dataForm.payments.filter(
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

					<div class="row justify-content-between mt-4 mx-2">
						<strong style="margin-left:13px;" class="margin_content_inline"
							>Đã thanh toán:</strong
						>
						<InputCurrency
							:key="keyRender"
							v-model="dataForm.amountPaid"
							vid="amount"
							:disabled="true"
							:max="99999999999999"
							class="form-group-container col-6 mt-n1"
						/>
					</div>
					<div class="row justify-content-between mt-4">
						<strong style="margin-left:13px;" class="margin_content_inline"
							>Còn nợ:</strong
						>
						<InputCurrency
							:key="keyRender"
							v-model="dataForm.debtRemain"
							vid="amount"
							:disabled="true"
							:max="99999999999999"
							class="form-group-container col-6 mt-n1"
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
							<button
								class="btn btn-orange btn-action-modal"
								type="submit"
								@click="handleAction"
								style="margin-right: 12px"
							>
								<img
									src="@/assets/icons/ic_save.svg"
									style="margin-right: 12px"
									alt="save"
								/>
								Lưu
							</button>
						</div>
					</div>
				</div>
			</div>
			<!-- <div class="container-title container-title__footer">
            <div class="d-flex justify-content-between justify-content-lg-end">
              <button class="btn btn-white btn-action-modal" type="button" @click="handleCancel"><img src="@/assets/icons/ic_cancel.svg"  style="margin-right: 12px" alt="save">Trở lại</button>
              <button class="btn btn-orange btn-action-modal" type="button" @click="handleAction"> <img src="@/assets/icons/ic_save.svg" style="margin-right: 12px" alt="save"> Lưu</button>
            </div>
          </div> -->
		</div>
	</div>
</template>

<script>
import { ref } from "vue";
import { storeToRefs } from "pinia";
import { usePreCertificateStore } from "@/store/preCertificate";
import _ from "lodash";
import InputCurrency from "@/components/Form/InputCurrency";
import InputDatePicker from "@/components/Form/InputDatePicker";
import moment from "moment";

export default {
	name: "ModalPCAppraiseInformation",

	components: {
		InputDatePicker,
		InputCurrency
	},
	setup() {
		const preCertificateStore = usePreCertificateStore();
		const { dataPC, other } = storeToRefs(preCertificateStore);
		const dataForm = ref(_.cloneDeep(dataPC.value));
		const keyRender = ref(0);

		const paidCompute = (
			event,
			payment,
			booltotal_service_fee = false,
			runCompute = false
		) => {
			if (!runCompute) {
				if (!booltotal_service_fee) payment.amount = event;
				if (booltotal_service_fee) dataForm.value.total_service_fee = event;
			}
			let debt_remain = dataForm.value.total_service_fee;
			let paid = 0;
			for (let index = 0; index < dataForm.value.payments.length; index++) {
				const element = dataForm.value.payments[index];
				if (element.is_deleted) continue;
				debt_remain -= element.amount;
				paid += parseFloat(element.amount);
			}
			dataForm.value.debtRemain = debt_remain;
			dataForm.value.amountPaid = paid;
			if (debt_remain < 0) {
				other.value.toast.open({
					message: "Số tiền thanh toán vượt quá số tiền cần thanh toán",
					type: "error",
					position: "top-right",
					duration: 3000
				});
				return;
			}

			keyRender.value++;
		};

		const addPayment = () => {
			dataForm.value.payments.push({
				pre_date: null,
				amount: 0
			});
		};
		const removePayment = (index, payment) => {
			if (!payment.id) dataForm.value.payments.splice(index, 1);
			else {
				Vue.set(payment, "is_deleted", true);
			}
			paidCompute(0, 0, false, true);
		};
		return {
			keyRender,
			dataForm,
			preCertificateStore,
			addPayment,
			removePayment,
			paidCompute
		};
	},
	computed: {},
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

		handleCancel(event) {
			this.$emit("cancel", event);
		},
		async handleAction() {
			if (this.dataForm.debtRemain < 0) {
				this.$toast.open({
					message: "Số tiền thanh toán vượt quá số tiền cần thanh toán",
					type: "error",
					position: "top-right",
					duration: 3000
				});
				return;
			}

			for (let index = 0; index < this.dataForm.payments.length; index++) {
				const element = this.dataForm.payments[index];
				if (!element.pay_date || !element.amount || element.amount <= 0) {
					this.$toast.open({
						message:
							"Vui lòng nhập đầy đủ thông tin thanh toán và số tiền phải lớn hơn 0",
						type: "error",
						position: "top-right",
						duration: 3000
					});
					return;
				}
			}
			const res = await this.preCertificateStore.updatePaymentFunction(
				this.dataForm.payments,
				this.dataForm.id
			);
			if (res.data === null) {
				this.$toast.open({
					message: "Lưu thông tin thanh toán thành công",
					type: "success",
					position: "top-right"
				});
				this.$emit("updatePayments");
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
		// width: 50%;
		width: 945px;
		max-height: 90vh;
		margin-bottom: 0;
		// padding: 35px 50px;
		padding: 25px 50px 25px 37px;
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
		padding-top: 15px;
		position: relative;
		&::-webkit-scrollbar {
			width: 2px;
		}
		&::before {
			content: "";
			position: absolute;
			left: 0;
			top: 0;
			height: 1px; /* Make this the same as your border */
			width: 100%;
			background: #e8e8e8; /* Make this the same as your border color */
			margin-left: 20px;
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
