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
			<div
				class="row"
				:key="keyRenderData"
				style="margin-left: 5px; margin-right: 5px"
			>
				<ValidationObserver
					tag="form"
					ref="paymentsForm"
					@submit.prevent="validatePayment"
				>
					<div class="col-12">
						<div class="row justify-content-between">
							<p class="margin_content_inline">Tổng phí dịch vụ:</p>
							<InputCurrency
								v-model="dataForm.service_fee"
								vid="amount"
								:disabled="true"
								:max="99999999999999"
								class="form-group-container"
								style="width: 175px"
							/>
						</div>

						<div class="row justify-content-between row-payment">
							<div style="width: 275px">Nội dung</div>
							<div style="width: 175px">Ngày thanh toán</div>
							<div style="width: 175px">Giá trị thanh toán</div>
						</div>
						<div
							class="row justify-content-between"
							v-for="(payment, index) in dataForm.payments"
							:key="index"
							v-if="!payment.is_deleted"
							:class="index === 0 ? '' : 'row-payment'"
							:style="permissionNotAllowEditHere ? '' : 'margin-right:-35px'"
						>
							<div style="width: 275px">
								<InputTextPrefixCustom
									v-model="payment.for_payment_of"
									id="petitioner_name"
									:vid="'petitioner_name' + index"
									:disabledInput="permissionNotAllowEditHere"
									label="Nội dung"
									:showLabel="false"
									rules="required"
									class="form-group-container input_certification_brief mt-n2"
								/>
							</div>
							<div style="width: 175px">
								<InputDatePicker
									v-model="payment.pay_date"
									:vid="'pay_date' + index"
									label="Ngày thanh toán"
									:showLabel="false"
									placeholder="Ngày / tháng / năm"
									rules="required"
									:disabled="permissionNotAllowEditHere"
									:formatDate="'DD/MM/YYYY'"
									class="form-group-container col-sm-12 col-md-12 mt-n2"
									@change="payment.pay_date = $event"
								/>
							</div>
							<div class="row">
								<div
									style="width: 175px"
									:style="
										permissionNotAllowEditHere ? '' : 'margin-right:-15px'
									"
								>
									<InputCurrencyNegative
										v-model="payment.amount"
										:vid="'amount' + index"
										:max="99999999999999"
										rules="required"
										:disabled="permissionNotAllowEditHere"
										label="Giá trị thanh toán"
										:showLabel="false"
										class="form-group-container col-sm-12 col-md-12 mt-n2"
										@change="paidCompute($event, payment)"
									/>
								</div>
								<div v-if="!permissionNotAllowEditHere">
									<img
										@click="deletePaymentDialog(payment, index)"
										src="@/assets/icons/ic_delete_2.svg"
										alt="tag_2"
										class="img_document_action"
									/>
								</div>
							</div>
						</div>
						<div
							v-if="!permissionNotAllowEditHere"
							class="d-lg-flex d-block justify-content-end align-items-center"
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
								class="form-group-container"
								style="width: 175px"
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
								class="form-group-container"
								style="width: 175px"
							/>
						</div>

						<div
							v-if="!permissionNotAllowEditHere"
							class="row-payment d-lg-flex d-block justify-content-end align-items-center mt-3 mb-2"
						>
							<div class="d-lg-flex d-block button-contain">
								<button
									class="btn btn-orange btn-action-modal"
									type="submit"
									style="width: 152px"
									:class="{ 'btn_loading disabled': isSubmit }"
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
import { usePreCertificateStore } from "@/store/preCertificate";
import _ from "lodash";
import InputCurrency from "@/components/Form/InputCurrency";
import InputCurrencyNegative from "@/components/Form/InputCurrencyNegative";

import InputDatePicker from "@/components/Form/InputDatePicker";
import InputTextPrefixCustom from "@/components/Form/InputTextPrefixCustom";
import ModalDelete from "@/components/Modal/ModalDelete";
import CertificationBrief from "@/models/CertificationBrief";
import moment from "moment";
export default {
	name: "PaymentHistories",
	props: {
		form: {
			type: Object,
			default: () => ({})
		},
		editPayments: {
			type: Boolean,
			default: false
		},
		toast: {
			type: Object,
			default: () => ({})
		},
		user: {
			type: Object,
			default: () => ({})
		}
	},
	data() {
		return { isCloseable: false };
	},

	components: {
		InputCurrencyNegative,
		ModalDelete,
		InputDatePicker,
		InputCurrency,
		InputTextPrefixCustom
	},
	setup(props) {
		const preCertificateStore = usePreCertificateStore();
		const drawer = ref(false);
		const openModalDelete = ref(false);
		const paymentDelete = ref({ id: null, isUpload: false });
		const dataForm = ref(props.form);
		const dataOriginal = ref(null);
		const permissionNotAllowEditHere = ref(true);
		const keyRenderData = ref(0);
		const showDrawer = async () => {
			const user = props.user;
			let haveViewPermission = false;
			permissionNotAllowEditHere.value = true;
			if (user.roles && user.roles[0]) {
				if (
					user.roles[0].name === "ROOT_ADMIN" ||
					user.roles[0].name === "SUB_ADMIN"
				) {
					permissionNotAllowEditHere.value = false;
					haveViewPermission = true;
				} else if (user.roles[0].permissions) {
					user.roles[0].permissions.forEach(value => {
						if (value.name === "VIEW_ACCOUNTING") {
							haveViewPermission = true;
						}
						if (
							value.name === "EDIT_ACCOUNTING" ||
							value.name === "ADD_ACCOUNTING"
						) {
							permissionNotAllowEditHere.value = false;
						}
					});
				}
				if (!props.editPayments) {
					permissionNotAllowEditHere.value = true;
				}
			}
			if (!haveViewPermission) {
				drawer.value = false;
				props.toast.open({
					message: "Bạn không có quyền xem lịch sử thanh toán",
					type: "error",
					position: "top-right",
					duration: 3000
				});
				return;
			}
			await CertificationBrief.getDetailCertificateBrief(props.form.id)
				.then(resp => {
					if (resp.data) {
						dataForm.value = ref(_.cloneDeep({ ...resp.data }));
						if (dataForm.value.payments.length === 0) {
							dataForm.value.payments.push({
								id: null,
								amount: 0,
								pay_date: null,
								for_payment_of: ""
							});
						}
						dataForm.value.debtRemain = dataForm.value.service_fee;
						dataForm.value.amountPaid = 0;
						for (
							let index = 0;
							index < dataForm.value.payments.length;
							index++
						) {
							const element = dataForm.value.payments[index];
							element.pay_date = element.pay_date
								? moment(element.pay_date)
								: "";
							dataForm.value.amountPaid =
								dataForm.value.amountPaid + parseFloat(element.amount);
							dataForm.value.debtRemain -= element.amount;
						}
						dataOriginal.value = ref(_.cloneDeep(dataForm.value.payments));
						drawer.value = true;
						keyRenderData.value++;
					} else if (resp.error && resp.error.statusCode) {
						props.toast.open({
							message: resp.error.statusCode,
							type: "error",
							position: "top-right",
							duration: 5000
						});
					}
				})
				.catch(err => {
					props.toast.open({
						message: err,
						type: "error",
						position: "top-right",
						duration: 5000
					});
				});
			// dataForm.value = ref(_.cloneDeep(props.form));
			// if (dataForm.value.payments.length === 0) {
			// 	dataForm.value.payments.push({
			// 		id: null,
			// 		amount: 0,
			// 		pay_date: null,
			// 		for_payment_of: ""
			// 	});
			// }
			// dataForm.value.debtRemain = dataForm.value.service_fee;
			// dataForm.value.amountPaid = 0;
			// for (let index = 0; index < dataForm.value.payments.length; index++) {
			// 	const element = dataForm.value.payments[index];
			// 	element.pay_date = element.pay_date ? moment(element.pay_date) : "";
			// 	dataForm.value.amountPaid =
			// 		dataForm.value.amountPaid + parseFloat(element.amount);
			// 	dataForm.value.debtRemain -= element.amount;
			// }
			// drawer.value = true;
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
				if (assignTotalServiceFee) dataForm.value.service_fee = event;
			}
			let debt_remain = dataForm.value.service_fee;
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
				props.toast.open({
					message: "Số tiền thanh toán vượt quá số tiền cần thanh toán",
					type: "error",
					position: "top-right",
					duration: 3000
				});
				// return;
			}

			keyRender.value++;
		};
		const addPayment = () => {
			dataForm.value.payments.push({
				pre_date: null,
				amount: 0
			});
			keyRender.value++;
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
		const isSubmit = ref(false);
		return {
			isSubmit,
			permissionNotAllowEditHere,
			openModalDelete,
			keyRender,
			drawer,
			dataForm,
			preCertificateStore,
			dataOriginal,
			keyRenderData,
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
			this.isSubmit = true;
			const isValid = await this.$refs.paymentsForm.validate();
			if (isValid) {
				this.handleAction();
			} else {
				this.isSubmit = false;
			}
		},
		async handleAction() {
			this.isSubmit = true;
			// if (this.dataForm.debtRemain < 0) {
			// 	this.$toast.open({
			// 		message: "Số tiền thanh toán vượt quá số tiền cần thanh toán",
			// 		type: "error",
			// 		position: "top-right",
			// 		duration: 3000
			// 	});
			// 	this.isSubmit = false;
			// 	return;
			// }
			const temp = _.differenceWith(
				this.dataForm.payments,
				this.dataOriginal,
				_.isEqual
			);
			for (let index = 0; index < temp.length; index++) {
				const element = temp[index];
				if (!element.pay_date || !element.amount) {
					this.$toast.open({
						message: "Vui lòng nhập đầy đủ thông tin thanh toán",
						type: "error",
						position: "top-right",
						duration: 3000
					});
					this.isSubmit = false;
					return;
				}
				if (!this.dataForm.id) {
					this.$toast.open({
						message: "Có lỗi xảy ra vui lòng thử lại sau",
						type: "error",
						position: "top-right",
						duration: 3000
					});
					this.isSubmit = false;
					return;
				}
				element.pre_certificate_id = this.dataForm.pre_certificate_id || null;
				element.certificate_id = this.dataForm.id;
			}
			if (temp && temp.length === 0) {
				this.$toast.open({
					message: "Không có thay đổi nào để cập nhật thông tin thanh toán",
					type: "error",
					position: "top-right",
					duration: 3000
				});
				this.isSubmit = false;
				return;
			}
			const res = await this.preCertificateStore.updatePaymentFunction(
				temp,
				true
			);
			if (res.data === null) {
				this.$toast.open({
					message: "Lưu thông tin thanh toán thành công",
					type: "success",
					position: "top-right"
				});
				await this.showDrawer();
				this.$emit("getDetail");
				// this.drawer = false;
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
			this.isSubmit = false;
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
