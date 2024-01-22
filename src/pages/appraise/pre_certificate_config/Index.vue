<template>
	<div>
		<div class="container-fluid appraise-container">
			<Tabs :theme="theme" :navAuto="true">
				<TabItem name="YCSB">
					<Table
						v-if="configs && configs.ycsbConfig && configs.ycsbConfig.principle"
						:lstConfig="configs.ycsbConfig.principle"
						type="workflow"
						@resfreshData="startFunction"
					/>
				</TabItem>
				<TabItem name="HSTDSB">
					<Table
						v-if="configs && configs.hstdConfig && configs.hstdConfig.principle"
						:lstConfig="configs.hstdConfig.principle"
						type="workflowHSTD"
						@resfreshData="startFunction"
					/>
				</TabItem>
			</Tabs>
		</div>
	</div>
</template>

<script>
import { Tabs, TabItem } from "vue-material-tabs";
import Table from "./components/Table";
import { ref } from "vue";
import { storeToRefs } from "pinia";
import { useWorkFlowConfig } from "@/store/workFlowConfig";
export default {
	name: "IndexConfigWorkflow",
	components: {
		Tabs,
		TabItem,
		Table
	},
	data: () => ({
		theme: {
			navItem: "#000000",
			navActiveItem: "#FAA831",
			slider: "#FAA831",
			arrow: "#000000"
		}
	}),
	setup() {
		const workFlowConfigStore = useWorkFlowConfig();
		const { configs } = storeToRefs(workFlowConfigStore);

		const startFunction = async () => {
			await workFlowConfigStore.getConfig();
			console.log("config", configs);
		};
		startFunction();
		return {
			configs,
			workFlowConfigStore,
			startFunction
		};
	},
	methods: {},
	beforeMount() {}
};
</script>

<style scoped>
.appraise-container {
	padding: 0 1.25rem;
	height: 100%;
}
</style>
