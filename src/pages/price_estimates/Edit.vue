<template>
	<div
		class="container-fluid"
		:style="isMobile ? { margin: '0', padding: '0' } : {}"
	>
		<Form v-if="tempPriceEstimates" />
	</div>
</template>
<script>
import Form from "./Form";

import { ref } from "vue";
import { storeToRefs } from "pinia";
import { usePriceEstimatesStore } from "@/store/priceEstimates";
export default {
	name: "Edit",
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
		const { configThis } = storeToRefs(priceEstimateStore);

		configThis.value.isMobile = isMobile.value;
		const tempPriceEstimates = ref(null);
		// if (getDataStep.data.step >= 6) {
		// 	const getDataStep7 = await CertificateAsset.getDataStep7(to.query['id'])
		// 	to.meta['step7'] = getDataStep7.data
		// }
		return {
			isMobile,
			tempPriceEstimates,
			configThis,
			priceEstimateStore
		};
	},
	async created() {
		this.configThis.toast = this.$toast;
		this.configThis.route = this.$route;
		this.configThis.router = this.$router;
		if (this.$route.query.id) {
			const response = await this.priceEstimateStore.getDataAllStep(
				this.$route.query.id
			);
			if (response.error) {
				this.$router.push({ name: "error.403" });
			} else {
				this.tempPriceEstimates = response;
			}
		} else {
			this.$router.push({ name: "error.403" });
		}
	},
	methods: {}
};
</script>
<style></style>
