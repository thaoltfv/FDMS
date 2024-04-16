<template>
	<div class="modal-delete d-flex justify-content-center align-items-center">
		<div class="card">
			<div class="container-title">
				<div class="d-flex justify-content-between">
					<h2 class="title">
						Tổ thẩm định
					</h2>
					<img
						height="35px"
						@click="handleCancel"
						class="cancel"
						src="@/assets/icons/ic_cancel_2.svg"
						alt=""
					/>
				</div>
			</div>
			<div class="card-body">
				<ValidationObserver
					tag="form"
					ref="appraisal"
					@submit.prevent="validateAppraisal"
				>
					<div class="row" style="margin-left: 10px;margin-right: 10px;">
						<div class="col-12">
							<InputCategory
								v-model="appraiser_sale_compute"
								vid="appraiser_sale_id"
								label="Nhân viên kinh doanh"
								rules="required"
								:requiredIcon="true"
								class="form-group-container col-12"
								:options="optionsAppraiserSales"
							/>
						</div>
						<div class="col-12">
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
						<div class="col-12">
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
					</div>
					<div class="btn__group">
						<button
							class="btn btn-white font-weight-normal font-weight-bold"
							@click.prevent="handleCancel"
						>
							Trở lại
						</button>
						<button
							class="btn btn-white btn-orange font-weight-bold ml-2"
							type="submit"
						>
							Lưu
						</button>
					</div>
				</ValidationObserver>
			</div>
		</div>
	</div>
</template>

<script>
import { ref } from "vue";
import { storeToRefs } from "pinia";
import { usePreCertificateStore } from "@/store/preCertificate";

import InputText from "@/components/Form/InputText";
import InputCategory from "@/components/Form/InputCategory";
import InputNumberFormat from "@/components/Form/InputNumber";
import InputDatePickerV2 from "@/components/Form/InputDatePickerV2";
export default {
	name: "ModalAppraisal",

	components: {
		InputText,
		InputNumberFormat,
		InputCategory,
		InputDatePickerV2
	},
	setup() {
		const preCertificateStore = usePreCertificateStore();
		const { dataPC, lstDataConfig } = storeToRefs(preCertificateStore);
		const getStartData = async () => {
			if (!lstDataConfig.value.appraiser_sales) {
				await preCertificateStore.getLstAppraisers();
			}
		};
		getStartData();
		return {
			dataPC,
			lstDataConfig,
			preCertificateStore
		};
	},
	methods: {
		handleCancel(event) {
			this.$emit("cancel", event);
		},
		async validateAppraisal() {
			const isValid = await this.$refs.appraisal.validate();
			if (isValid) {
				this.handleAction();
			}
		},
		async handleAction(event) {
			const res = await this.preCertificateStore.createUpdatePreCertificateion(
				this.dataPC.id,
				true
			);
			if (res.data) {
				await this.$toast.open({
					message: "Lưu thông tin tổ thẩm định thành công",
					type: "success",
					position: "top-right"
				});
				await this.$emit("updateAppraisal");
				await this.$emit("cancel", event);
			} else if (res.error) {
				this.$toast.open({
					message: res.error.message,
					type: "error",
					position: "top-right",
					duration: 3000
				});
			} else {
				await this.$toast.open({
					message: "Lưu thất bại",
					type: "error",
					position: "top-right"
				});
			}
		}
	},
	computed: {
		appraiser_perform_compute: {
			// getter
			get: function() {
				if (this.lstDataConfig.appraiser_performances.length > 0) {
					// // console.log('vô đây trước 1')
					return this.dataPC.appraiser_perform_id;
				} else {
					// // console.log('vô đây trước 2')
					return this.dataPC.appraiser_performance.name;
				}
			},
			// setter
			set: function(newValue) {
				// // console.log('newwww', newValue)
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
				// // console.log('newwww', newValue)
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
				// // console.log('newwww', newValue)
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
		}
	}
};
</script>

<style lang="scss" scoped>
.modal-delete {
	position: fixed;
	z-index: 300;
	left: 0;
	top: 0;
	width: 100%;
	height: 100%;
	background: rgba(0, 0, 0, 0.6);

	@media (max-width: 787px) {
		padding: 20px;
	}
	.card {
		max-width: 500px;
		width: 100%;
		margin-bottom: 0;
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
			padding: 0px 15px 15px;
			border-top: 1px solid #e8e8e8;
			.btn__group {
				text-align: right;
				.btn {
					max-width: fit-content;
					width: 100%;
					margin: 14px 0 0;
				}
			}
		}
	}
}
.container-title {
	// margin: -35px -95px auto;
	// padding: 35px 95px 0;
	// padding: 15px 50px 10px 95px;
	// box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
	padding-left: 1rem;

	.title {
		margin-left: 10px;
		color: #007ec6;
		font-weight: 600;
		margin-top: 20px;
		margin-bottom: 15px;
		font-size: 1.2rem;
		@media (max-width: 767px) {
			font-size: 1.125rem;
		}
	}
	// &__footer{
	//   margin: auto -95px -35px;
	//   padding: 20px 95px 20px;
	//   @media (max-width: 767px) {
	//     .btn-white{
	//       margin-bottom: 20px;
	//     }
	//   }
	// }
}
.title {
	font-weight: 500;
	font-size: 1.125rem;
	text-align: left;
	margin-bottom: 7px;
}
.btn {
	&-orange {
		background: #faa831;
		color: #ffffff;
		font-weight: 700 !important;
	}
	&-white {
		min-width: auto;
	}
}
.form-group-container {
	margin-top: 10px;
}
.property_content {
	font-weight: 600;
}
.input_checkbox {
	margin-right: 10px;
}
.color-black {
	color: #333333;
}
.border_disable {
	border-color: #d9d9d9 !important;
}
.form-group-container {
	margin-top: 10px;
}
</style>
