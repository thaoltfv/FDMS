<template>
	<div class="modal-delete d-flex justify-content-center align-items-center">
		<div class="card">
			<div
				class="card-header d-flex justify-content-between align-items-center py-3"
			>
				<h3 class="font-weight-bold mb-0">
					<!--          {{$t('btn_delete')}}-->
					Thông báo
				</h3>

				<img
					@click="handleAction"
					src="../../assets/images/icon-btn-back.svg"
					alt="icon"
				/>
			</div>

			<div class="card-body">
				<p style="font-size: 18px" v-html="notification"></p>

				<div>
					<OtherFile
						style="margin-left:-20px; margin-top:-30px;"
						type="Result"
					/>

					<InputTextarea
						rows="2"
						:autosize="true"
						:maxLength="1000"
						v-model="dataPC.status_note"
						label="Ghi chú"
						class="form-group-container mb-3"
					/>
				</div>
				<div class="btn__group">
					<button
						class="btn btn-white font-weight-normal font-weight-bold"
						@click.prevent="handleAction"
						v-text="$t('popup_btn_no')"
					/>
					<button
						class="btn btn-white btn-orange font-weight-bold mt-md-0 mt-2"
						@click.prevent="verifyToStage3Function"
						v-text="$t('popup_btn_yes')"
					/>
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
		handleAction() {
			this.$emit("action");
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
			const boolResult = await this.preCertificateStore.updateToStage3();
			if (!boolResult) {
				this.handleAction();
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
