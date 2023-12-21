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

				<div class="btn__group">
					<button
						class="btn btn-white font-weight-normal font-weight-bold"
						@click.prevent="handleAction"
						v-text="$t('popup_btn_no')"
					/>
					<button
						class="btn btn-white btn-orange font-weight-bold mt-md-0 mt-2"
						@click.prevent="verifyToStage2Function"
						v-text="$t('popup_btn_yes')"
					/>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import { ref } from "vue";
import { storeToRefs } from "pinia";
import { usePreCertificateStore } from "@/store/preCertificate";
export default {
	name: "ModalVerifyToStage2",

	props: ["notification"],
	methods: {
		handleAction(event) {
			this.$emit("action", event);
		}
	},
	setup(context) {
		const preCertificateStore = usePreCertificateStore();
		const { dataPC, other, preCertificateOtherDocuments } = storeToRefs(
			preCertificateStore
		);
		const verifyToStage2Function = async () => {
			if (
				dataPC.value.status === 1 &&
				dataPC.value.total_preliminary_value > 0 &&
				preCertificateOtherDocuments.value.result.length > 0
			) {
				other.value.isSubmit = true;
				const res = await preCertificateStore.updateToStage2();
				if (res) {
					other.value.$toast.open({
						message: `Chuyển tiếp thành công`,
						type: "success",
						position: "top-right",
						duration: 3000
					});
					context.emit("action");
				} else {
					other.value.$toast.open({
						message: `Vui lòng kiểm tra lại thông tin`,
						type: "error",
						position: "top-right",
						duration: 3000
					});
					other.value.isSubmit = false;
				}
			} else {
				other.value.$toast.open({
					message: `Vui lòng kiểm tra lại thông tin`,
					type: "error",
					position: "top-right",
					duration: 3000
				});
			}
		};
		return {
			dialogVerifyToStage2,
			isMobile,
			jsonConfig,
			verifyToStage2Function
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
