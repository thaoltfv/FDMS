<template>
	<div class="btn-history-payment" style="">
		<button class="btn btn-orange btn-history-payment" @click="showDrawer">
			<img
				src="@/assets/icons/ic_payment_history.svg"
				class="mt-n1"
				alt="history"
			/>
		</button>
		<a-drawer
			width="700"
			title="Lịch sử thanh toán"
			placement="right"
			:visible="drawer"
			@close="closeDrawer"
			:headerStyle="{ 'margin-left': '15px', 'margin-right': '15px' }"
		>
			<ModalDelete
				v-if="openModalDelete"
				@cancel="openModalDelete = false"
				@action="removePayment"
			/>
			<div class="row" style="margin-left:5px;margin-right:5px">
				<ValidationObserver
					tag="form"
					ref="paymentsForm"
					@submit.prevent="validatePayment"
				>
					<div class="col-12">
						<div class="row justify-content-between">
							<p class="margin_content_inline">Tổng phí dịch vụ:</p>
							<InputCurrency
								v-model="dataForm.total_service_fee"
								vid="amount"
								:disabled="true"
								:max="99999999999999"
								class="form-group-container "
								style="width:175px"
							/>
						</div>

						<div class="row justify-content-between row-payment">
							<div style="width:275px">
								Nội dung
							</div>
							<div style="width:175px">
								Ngày thanh toán
							</div>
							<div style="width:175px">
								Giá trị thanh toán
							</div>
						</div>
						<div
							class="row justify-content-between "
							v-for="(payment, index) in dataForm.payments"
							:key="index"
							v-if="!payment.is_deleted"
							:class="index === 0 ? '' : 'row-payment'"
							:style="permissionNotAllowEdit ? '' : 'margin-right:-35px'"
						>
							<div style="width:275px">
								<InputTextPrefixCustom
									v-model="payment.for_payment_of"
									id="petitioner_name"
									:vid="'petitioner_name' + index"
									:disabledInput="permissionNotAllowEdit"
									label="Nội dung"
									:showLabel="false"
									rules="required"
									class="form-group-container input_certification_brief mt-n2"
								/>
							</div>
							<div style="width:175px">
								<InputDatePicker
									v-model="payment.pay_date"
									:vid="'pay_date' + index"
									label="Ngày thanh toán"
									:showLabel="false"
									placeholder="Ngày / tháng / năm"
									rules="required"
									:disabled="permissionNotAllowEdit"
									:formatDate="'DD/MM/YYYY'"
									class="form-group-container col-sm-12 col-md-12 mt-n2"
									@change="payment.pay_date = $event"
								/>
							</div>
							<div class="row">
								<div
									style="width:175px;"
									:style="permissionNotAllowEdit ? '' : 'margin-right:-15px'"
								>
									<InputCurrency
										v-model="payment.amount"
										:vid="'amount' + index"
										:max="99999999999999"
										rules="required"
										:disabled="permissionNotAllowEdit"
										label="Giá trị thanh toán"
										:showLabel="false"
										class="form-group-container col-sm-12 col-md-12 mt-n2"
										@change="paidCompute($event, payment)"
									/>
								</div>
								<div v-if="!permissionNotAllowEdit">
									<img
										@click="deletePaymentDialog(payment, index)"
										src="@/assets/icons/ic_delete_2.svg"
										alt="tag_2"
										class=" img_document_action"
									/>
								</div>
							</div>
						</div>
						<div
							class=" d-lg-flex d-block justify-content-end align-items-center "
						>
							<div class="d-lg-flex d-block button-contain">
								<span
									style="font-style: italic; color: orange; cursor: pointer"
									@click="addPayment"
									>+Thêm</span
								>
							</div>
						</div>
						<div class="row justify-content-between row-payment">
							<p class="margin_content_inline">Đã thanh toán:</p>
							<InputCurrency
								:key="keyRender"
								v-model="dataForm.amountPaid"
								vid="amount"
								:disabled="true"
								:max="99999999999999"
								class="form-group-container "
								style="width:175px"
							/>
						</div>

						<div class="row justify-content-between row-payment">
							<p class="margin_content_inline">Còn nợ:</p>
							<InputCurrency
								:key="keyRender"
								v-model="dataForm.debtRemain"
								vid="amount"
								:disabled="true"
								:max="99999999999999"
								class="form-group-container "
								style="width:175px"
							/>
						</div>

						<div
							v-if="!permissionNotAllowEdit"
							class="row-payment d-lg-flex d-block justify-content-end align-items-center mt-3 mb-2"
						>
							<div class="d-lg-flex d-block button-contain">
								<button
									class="btn btn-orange btn-action-modal"
									type="submit"
									style="width:152px"
								>
									<img src="@/assets/icons/ic_save.svg" alt="save" />
									Lưu
								</button>
							</div>
						</div>
					</div>
				</ValidationObserver>
			</div>
		</a-drawer>
	</div>
</template>
<script>
import { ref } from "vue";
import { storeToRefs } from "pinia";
import { usePreCertificateStore } from "@/store/preCertificate";
import _ from "lodash";
import InputCurrency from "@/components/Form/InputCurrency";
import InputDatePicker from "@/components/Form/InputDatePicker";
import InputTextPrefixCustom from "@/components/Form/InputTextPrefixCustom";
import ModalDelete from "@/components/Modal/ModalDelete";
export default {
	name: "PaymentHistories",
	data() {
		return { isCloseable: false };
	},

	components: {
		ModalDelete,
		InputDatePicker,
		InputCurrency,
		InputTextPrefixCustom
	},
	setup() {
		const preCertificateStore = usePreCertificateStore();
		const { dataPC, other, permission } = storeToRefs(preCertificateStore);
		const drawer = ref(false);
		const openModalDelete = ref(false);
		const paymentDelete = ref({ id: null, isUpload: false });
		const dataForm = ref(_.cloneDeep(dataPC.value));

		const permissionNotAllowEdit = ref(false);
		const showDrawer = async () => {
			permissionNotAllowEdit.value = !(
				permission.value.editPayments && permission.value.edit
			);
			const temp = await preCertificateStore.getPreCertificate(dataPC.value.id);
			dataForm.value = ref(_.cloneDeep(temp));
			drawer.value = true;
		};
		const closeDrawer = () => {
			drawer.value = false;
		};
		const keyRender = ref(0);
		const paidCompute = (
			event,
			payment,
			assignTotalServiceFee = false,
			assignVariable = false
		) => {
			if (!assignVariable) {
				if (!assignTotalServiceFee) payment.amount = event;
				if (assignTotalServiceFee) dataForm.value.total_service_fee = event;
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
		const deletePaymentDialog = (payment, index) => {
			openModalDelete.value = true;
			paymentDelete.value = { id: payment.id, index };
		};
		const removePayment = () => {
			if (!paymentDelete.value.id)
				dataForm.value.payments.splice(paymentDelete.value.index, 1);
			else {
				Vue.set(
					dataForm.value.payments[paymentDelete.value.index],
					"is_deleted",
					true
				);
			}
			paidCompute(0, 0, false, true);
		};
		return {
			permissionNotAllowEdit,
			openModalDelete,
			keyRender,
			drawer,
			dataForm,
			preCertificateStore,

			showDrawer,
			closeDrawer,
			addPayment,
			removePayment,
			paidCompute,
			deletePaymentDialog
		};
	},
	created() {},
	methods: {
		formatNumber(num) {
			if (num) {
				let formatedNum = num.toString().replace(".", ",");
				return formatedNum.toString().replace(/^[+-]?\d+/, function(int) {
					return int.replace(/(\d)(?=(\d{3})+$)/g, "$1.");
				});
			}
		},
		async validatePayment() {
			const isValid = await this.$refs.paymentsForm.validate();
			if (isValid) {
				this.handleAction();
			}
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
				if (!element.pay_date || element.amount < 0) {
					this.$toast.open({
						message:
							"Vui lòng nhập đầy đủ thông tin thanh toán và số tiền phải lớn hơn hoặc bằng 0",
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
				this.drawer = false;
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
	}
};
</script>
<style scoped lang="scss">
.card {
	border-radius: 5px;
	box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
	background: #ffffff;
	margin-bottom: 1rem;

	&-footer {
		padding: 15px 24px;
	}

	&-title {
		padding: 20px 25px 10px;
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
.btn {
	&-orange {
		background: #faa831;
		text-align: center;
		border-radius: 5px;
		box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25) !important;
		height: 35px;
		// width: 100px;
		color: #fff;
		// margin: 15px 0 0;
		box-sizing: border-box;

		&:hover {
			border-color: #dc8300;
		}
	}
}

.btn {
	&-history-payment {
		position: fixed;
		right: 0;
		top: 170px;
		z-index: 100;
		border-radius: 5px 0 0 5px;
		padding: 0.5rem 0.3rem;
	}
}
.row-payment {
	margin-top: 20px;
}

.row-payment-2 {
	margin-top: 0px;
}

.img_document_action {
	cursor: pointer;
	margin-top: 10px;
}
</style>
