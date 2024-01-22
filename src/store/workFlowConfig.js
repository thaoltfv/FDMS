// @ts-nocheck
import { defineStore } from "pinia";
import { ref } from "vue";
import PreCertificateConfig from "@/models/PreCertificateConfig";
export const useWorkFlowConfig = defineStore("workFlowConfig", () => {
	const configs = ref({ hstdConfig: {}, ycsbConfig: {} });
	async function getConfig() {
		const respconfig = await PreCertificateConfig.getConfig();
		for (let index = 0; index < respconfig.data.length; index++) {
			const element = respconfig.data[index];
			element.config = JSON.parse(element.config);
			for (let index = 0; index < element.config.principle.length; index++) {
				const elementx = element.config.principle[index];
				if (elementx.process_time) {
					let totalMinutes = elementx.process_time;
					let days = Math.floor(totalMinutes / (60 * 24));
					totalMinutes %= 60 * 24;
					let hours = Math.floor(totalMinutes / 60);
					let minutes = totalMinutes % 60;

					elementx.day_process = days;
					elementx.hour_process = hours;
					elementx.minute_process = minutes;

					elementx.day_process_original = days;
					elementx.hour_process_original = hours;
					elementx.minute_process_original = minutes;
				}
				if (element.expire_in) {
					let totalMinutes2 = elementx.expire_in;
					let days2 = Math.floor(totalMinutes2 / (60 * 24));
					totalMinutes2 %= 60 * 24;
					let hours2 = Math.floor(totalMinutes2 / 60);
					let minutes2 = totalMinutes2 % 60;

					elementx.day_expire = days2;
					elementx.hour_expire = hours2;
					elementx.minute_expire = minutes2;

					elementx.day_expire_original = days2;
					elementx.hour_expire_original = hours2;
					elementx.minute_expire_original = minutes2;
				}
			}

			if (element.name === "workflowHSTD") {
				configs.value.hstdConfig = element.config;
			} else {
				configs.value.ycsbConfig = element.config;
			}
		}
		return configs.value;
	}
	async function getConfigByName(name) {
		const respconfig = await PreCertificateConfig.findByName(name);
		for (let index = 0; index < respconfig.data.length; index++) {
			const element = respconfig.data[index];
			element.config = JSON.parse(element.config);
			if (element.name === "workflowHSTD") {
				configs.value.hstdConfig = element.config;
			} else {
				configs.value.ycsbConfig = element.config;
			}
		}
		return configs.value;
	}
	async function updateConfig(name, data, toast) {
		const response = await PreCertificateConfig.findByName(name);
		const tempConfig = JSON.parse(response.data[0].config);
		tempConfig.principle.forEach(tempItem => {
			const dataItem = data.find(item => item.id === tempItem.id);
			if (dataItem) {
				tempItem.expire_in = dataItem.expire_in;
				tempItem.process_time = dataItem.process_time;
			}
		});
		console.log("data", name, data, tempConfig.principle);
		const res = await PreCertificateConfig.updateConfig(name, data);
		return res;
		if (res.data) {
			console.log("update thành công");
			toast.open({
				message: "Lưu hồ sơ thẩm định thành công",
				type: "success",
				position: "top-right",
				duration: 3000
			});
			// await other.value.router
			// 	.push({
			// 		name: "pre_certification.detail",
			// 		query: { id: `${res.data.id}` }
			// 	})
			// 	.catch(_ => {});
		} else if (res.error) {
			toast.open({
				message: `${res.error.message}`,
				type: "error",
				position: "top-right"
			});
		} else {
			toast.open({
				message: "Lưu thất bại",
				type: "error",
				position: "top-right"
			});
		}
	}
	return {
		configs,

		getConfig,
		getConfigByName,
		updateConfig
	};
});
