<template>
	<div
		class="container-fluid"
		:style="isMobile ? { margin: '0', padding: '0' } : {}"
	>
		<div>
			<div style="margin-bottom:60px">
				<ComponentEdit :routeId="$route.query.id" />
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
						:class="{ 'btn_loading disabled': isSubmit }"
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
						:class="{ 'btn_loading disabled': isSubmit }"
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
import ComponentEdit from "@/components/PreCertificate/ComponentEdit";
import { ref } from "vue";

export default {
	name: "Create",
	components: {
		ComponentEdit
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

		return { isMobile };
	},

	data() {
		return {
			idData: null,
			isSubmit: false
		};
	},
	methods: {},

	mounted() {
		// // console.log(this.profile)
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
