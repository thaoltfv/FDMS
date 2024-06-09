<template>
	<Step2Land v-if="!miscVariable.isApartment" />
	<Step2Apartment v-else />
</template>
<script>
import Step2Land from "@/components/PriceEstimate/step2/Step2Land";
import Step2Apartment from "@/components/PriceEstimate/step2/Step2Apartment";

import { storeToRefs } from "pinia";
import { usePriceEstimatesStore } from "@/store/priceEstimates";

export default {
	name: "Step2",
	components: {
		Step2Apartment,
		Step2Land
	},
	computed: {},
	setup() {
		const priceEstimateStore = usePriceEstimatesStore();
		const { miscVariable } = storeToRefs(priceEstimateStore);

		return {
			miscVariable
		};
	}
};
</script>
