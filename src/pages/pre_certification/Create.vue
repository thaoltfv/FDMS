<template>
	<div
		class="container-fluid"
		:style="isMobile ? { margin: '0', padding: '0' } : {}"
	>
		<div>
			<div style="margin-bottom:60px">
				<ValidationObserver
					tag="div"
					ref="step_1"
					@submit.prevent="validateSubmitStep1"
				>
					<ComponentCreate />
				</ValidationObserver>
			</div>

			<div
				v-if="!isMobile"
				class="btn-footer d-md-flex d-block justify-content-end align-items-center"
			>
				<div class="d-lg-flex d-block button-contain">
					<button
						@click.prevent="$router.go(-1)"
						class="btn btn-white text-nowrap"
					>
						<img
							src="../../assets/icons/ic_cancel.svg"
							style="margin-right: 12px"
							alt="save"
						/>
						Trở lại
					</button>
					<button
						:class="{ 'btn_loading disabled': other.isSubmit }"
						class="btn btn-white btn-orange text-nowrap"
						@click.prevent="validateSubmitStep1"
						type="submit"
					>
						<img
							src="../../assets/icons/ic_save.svg"
							style="margin-right: 12px"
							alt="save"
						/>Lưu
					</button>
				</div>
			</div>
			<div v-else class="btn-footer d-md-flex d-block" style="bottom: 60px;">
				<div
					class="d-lg-flex d-block button-contain row"
					style="justify-content: space-around;display: flex!important;"
				>
					<button
						@click.prevent="$router.go(-1)"
						class="btn btn-white text-nowrap col-6"
						style="width: unset;margin: 0;padding: 0;"
					>
						<img
							src="../../assets/icons/ic_cancel.svg"
							style="margin-right: 12px"
							alt="save"
						/>
						Trở lại
					</button>
					<button
						:class="{ 'btn_loading disabled': other.isSubmit }"
						class="btn btn-white btn-orange text-nowrap col-6"
						@click.prevent="validateSubmitStep1"
						type="submit"
						style="width: unset;margin: 0;padding: 0;"
					>
						<img
							src="../../assets/icons/ic_save.svg"
							style="margin-right: 12px"
							alt="save"
						/>Lưu
					</button>
				</div>
			</div>
		</div>
	</div>
</template>
<script>
import ComponentCreate from "@/components/PreCertificate/ComponentCreate";
import { ref } from "vue";

import { storeToRefs } from "pinia";
import { usePreCertificateStore } from "@/store/preCertificate";
export default {
	name: "Create",
	components: {
		ComponentCreate
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
		const { other } = storeToRefs(preCertificateStore);
		const step_1 = ref(null);
		return { isMobile, other, preCertificateStore, step_1 };
	},

	data() {
		return {
			idData: null,
			isSubmit: false
		};
	},
	methods: {
		async validateSubmitStep1() {
			const isValid = await this.step_1.validate();
			if (isValid) {
				await this.preCertificateStore.createUpdatePreCertificateion();
			} else {
				this.$toast.open({
					message: "Vui lòng nhập đầy đủ các trường bắt buộc",
					type: "error",
					position: "top-right"
				});
			}
		}
	},
	created() {
		const permission = this.$store.getters.currentPermissions;
		permission.forEach(value => {
			if (value === "ADD_PRE_CERTIFICATE") {
				this.add = true;
			}
		});
		if (!this.add) {
			this.$router.push({ name: "page-not-found" });
			this.$toast.open({
				message: "Bạn ko có quyền tạo yêu cầu sơ bộ",
				type: "error",
				position: "top-right",
				duration: 5000
			});
		}
	},
	mounted() {
		this.preCertificateStore.updateRouteToast(this.$router, this.$toast);
	},
	async beforeMount() {
		// this.getAppraisers();
		// this.getAppraiseOthers();
		// this.getProfiles();
		// this.getCustomer();
		// this.getDictionary();
	}
};
</script>
<style></style>
