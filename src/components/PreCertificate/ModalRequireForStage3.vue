<template>
	<div class="modal-delete d-flex justify-content-center align-items-center">
		<div class="card">
			<div class="container-title">
				<div class="d-flex justify-content-between">
					<h2 class="title">
						Kết quả sơ bộ
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
				<p style="font-size: 18px" v-html="notification"></p>

				<div>
					<OtherFile
						:key="key_render_require_for_stage3"
						style="margin-left:-20px; margin-top:-30px;"
						type="Result"
						@action="key_render_require_for_stage3++"
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
	props: ["notification"],
	computed: {
		optionsReasons() {
			return {
				data: this.reasons,
				id: "id",
				key: "description"
			};
		}
	},
	methods: {
		handleCancel() {
			this.$emit("cancel");
		},
		async verifyToStage3Function() {
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
			if (this.dataPC.total_preliminary_value > 0) {
			} else {
				await this.$toast.open({
					message: "Vui lòng bổ sung Tổng giá trị sơ bộ",
					type: "error",
					position: "top-right",
					duration: 3000
				});
				return;
			}
			const res = await this.preCertificateStore.createUpdatePreCertificateion(
				this.dataPC.id,
				true
			);
			if (res.data) {
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
		const key_render_require_for_stage3 = ref(0);
		return {
			key_render_require_for_stage3,
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
	z-index: 10002;
	left: 0;
	top: 0;
	width: 100%;
	height: 100%;
	background: rgba(0, 0, 0, 0.6);

	@media (max-width: 787px) {
		padding: 20px;
	}
	.card {
		max-width: 600px;
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
			text-align: center;
			padding: 40px;
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
.btn {
	&-orange {
		background: #faa831;
		color: #ffffff;
		font-weight: bold !important;
	}
}
</style>
