<template>
	<div
		class="container-fluid"
		:style="isMobile ? { margin: '0', padding: '0' } : {}"
	>
		<Form v-if="priceEstimates" />
	</div>
</template>
<script>
import Form from "./Form";

import { ref } from "vue";
import { storeToRefs } from "pinia";
import { usePriceEstimatesStore } from "@/store/priceEstimates";
export default {
	name: "Create",
	components: {
		Form
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

		const priceEstimateStore = usePriceEstimatesStore();
		priceEstimateStore.resetVariables();
		priceEstimateStore.getDictionary();
		const { priceEstimates, configThis, miscVariable } = storeToRefs(
			priceEstimateStore
		);

		configThis.value.isMobile = isMobile.value;
		const step_1 = ref(null);
		return {
			isMobile,
			priceEstimates,
			miscVariable,
			configThis,
			priceEstimateStore,
			step_1
		};
	},
	created() {
		this.configThis.toast = this.$toast;
		this.configThis.route = this.$route;
		this.configThis.router = this.$router;

		if (this.$route.query.asset_type_id == 39) {
			this.miscVariable.isApartment = true;
			this.priceEstimates.step_1.asset_type_id = 39;
			this.priceEstimates.step_1.general_infomation.asset_type_id = 39;
		} else {
			this.miscVariable.isApartment = false;
			this.priceEstimates.step_1.asset_type_id = 38;
			this.priceEstimates.step_1.general_infomation.asset_type_id = 38;
		}
	},
	methods: {}
};
</script>
<style></style>
