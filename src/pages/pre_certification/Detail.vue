<template>
	<ComponentDetail :routeId="$route.query.id" />
</template>
<script>
import ComponentDetail from "@/components/PreCertificate/ComponentDetail";
import { usePreCertificateStore } from "@/store/preCertificate";
export default {
	name: "Detail",
	components: {
		ComponentDetail
	},
	setup() {
		const preCertificateStore = usePreCertificateStore();
		return { preCertificateStore };
	},

	data() {},
	methods: {},

	mounted() {
		this.preCertificateStore.updateRouteToast(this.$router, this.$toast);
	},
	async beforeMount() {}
};
</script>
<style></style>
