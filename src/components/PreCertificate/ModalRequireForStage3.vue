<template>
	<div class=" modal-delete d-flex justify-content-center align-items-center">
		<div class="card px-3 pb-1">
			<div class="container-title">
				<h2 class="title ml-3" style="">
					Kết quả sơ bộ
				</h2>
			</div>
			<div class="card-body">
				<div class="row col-12" style="margin-top: 10px;margin-left:0px">
					<OtherFile
						ref="OtherFileComponent"
						type="Result"
						fromComponent="DialogUpdateStatus"
					/>
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
						@click.prevent="verifyToStage3Function"
					>
						Lưu
					</button>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import OtherFile from "./OtherFile.vue";
import InputTextarea from "@/components/Form/InputTextarea";
import InputCurrency from "@/components/Form/InputTextarea";

import { ref } from "vue";
import { storeToRefs } from "pinia";
import { usePreCertificateStore } from "@/store/preCertificate";
export default {
	name: "ModalVerifyToStage3",
	components: {
		OtherFile,
		InputTextarea,
		InputCurrency
	},
	computed: {},
	methods: {
		handleCancel() {
			this.$emit("cancel");
		},
		async verifyToStage3Function() {
			const tempUpdate = this.$refs.OtherFileComponent.dataForm;
			if (!tempUpdate.pre_asset_name) {
				await this.$toast.open({
					message: "Vui lòng bổ sung Tên tài sản sơ bộ",
					type: "error",
					position: "top-right",
					duration: 3000
				});
				return;
			}
			if (tempUpdate.total_preliminary_value > 0) {
			} else {
				await this.$toast.open({
					message: "Vui lòng bổ sung Tổng giá trị sơ bộ",
					type: "error",
					position: "top-right",
					duration: 3000
				});
				return;
			}
			if (this.preCertificateOtherDocuments.Result.length > 0) {
			} else {
				await this.$toast.open({
					message: "Vui lòng bổ sung file kết quả sơ bộ",
					type: "error",
					position: "top-right",
					duration: 3000
				});
				return;
			}

			const res = await this.preCertificateStore.createUpdatePreCertificateion(
				tempUpdate.id,
				true,
				tempUpdate
			);
			if (res.data) {
				await this.preCertificateStore.getPreCertificate(tempUpdate.id);
				await this.$toast.open({
					message: "Lưu thông tin kết quả sơ bộ thành công",
					type: "success",
					position: "top-right"
				});
				await this.$emit("cancel");
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
	setup() {
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
		const { dataPC, preCertificateOtherDocuments } = storeToRefs(
			preCertificateStore
		);
		return {
			dataPC,
			isMobile,
			preCertificateOtherDocuments,
			preCertificateStore
		};
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
		max-width: 450px;
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

	.title {
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
