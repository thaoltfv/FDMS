// @ts-nocheck
import { defineStore } from "pinia";
import { ref } from "vue";
import PreCertificateConfig from "@/models/PreCertificateConfig";
import { usePreCertificateStore } from "@/store/preCertificate";
export const useWorkFlowConfig = defineStore("workFlowConfig", () => {
	const configs = ref({ hstdConfig: {}, ycsbConfig: {} });
	const preCertificateStore = usePreCertificateStore();
	async function getConfig() {
		const respconfig = await PreCertificateConfig.getConfig();

		respconfig.data.forEach(element => {
			element.config = JSON.parse(element.config);
			element.config.principle.forEach(elementx => {
				const { process_time, expire_in } = elementx;

				const calculateTime = time => {
					const days = Math.floor(time / (60 * 24));
					const hours = Math.floor((time % (60 * 24)) / 60);
					const minutes = time % 60;

					return { days, hours, minutes };
				};

				elementx.day_process = process_time
					? calculateTime(process_time).days
					: 0;
				elementx.hour_process = process_time
					? calculateTime(process_time).hours
					: 0;
				elementx.minute_process = process_time
					? calculateTime(process_time).minutes
					: 0;

				elementx.day_process_original = elementx.day_process;
				elementx.hour_process_original = elementx.hour_process;
				elementx.minute_process_original = elementx.minute_process;

				elementx.day_expire = expire_in ? calculateTime(expire_in).days : 0;
				elementx.hour_expire = expire_in ? calculateTime(expire_in).hours : 0;
				elementx.minute_expire = expire_in
					? calculateTime(expire_in).minutes
					: 0;

				elementx.day_expire_original = elementx.day_expire;
				elementx.hour_expire_original = elementx.hour_expire;
				elementx.minute_expire_original = elementx.minute_expire;
			});

			if (element.name === "workflowHSTD") {
				configs.value.hstdConfig = element.config;
			} else {
				configs.value.ycsbConfig = element.config;
				preCertificateStore.functionUpdateWorkflow(element.config);
			}
		});

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
		const res = await PreCertificateConfig.updateConfig(name, tempConfig);
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
