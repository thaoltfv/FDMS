// @ts-nocheck
import { defineStore } from "pinia";
import { ref } from "vue";
import PreCertificate from "@/models/PreCertificate";
import PreCertificateConfig from "@/models/PreCertificateConfig";
import e from "cors";
export const usePreCertificateStore = defineStore(
	"preCertificate",
	() => {
		const dataPC = ref({
			id: 16,
			certificate_id: null,
			petitioner_name: "Ông / Bà ",
			petitioner_phone: null,
			petitioner_address: null,
			petitioner_identity_card: null,
			customer_id: null,
			status: 1,
			appraise_purpose_id: null,
			note: null,
			appraiser_sale_id: null,
			business_manager_id: null,
			appraiser_perform_id: null,
			total_preliminary_value: null,
			cancel_reason: null,

			customer: {
				name: null,
				address: null,
				phone: null
			},
			business_manager: {
				name: null,
				id: null
			},
			appraiser_performance: {
				name: null,
				id: null
			},
			appraiser_sale: {
				name: null,
				id: null
			},
			pre_type: "Cơ bản",
			uploadFile: null
		});

		const permission = ref({
			allowDelete: true
		});

		const other = ref({
			isSubmit: false,
			toast: null,
			router: null
		});
		const preCertificateOtherDocuments = ref([
			{
				pre_certificate_id: null,
				name: null,
				link: null,
				type: null,
				size: null,
				description: null,
				type_document: null
			}
		]);

		const lstData = ref({
			business_managers: [],
			appraiser_sales: [],
			appraiser_performances: [],
			appraiser_purposes: [],
			customers: [],
			preTypes: null,
			workflow: null
		});
		async function getCustomer() {
			let res = await PreCertificate.getCustomer();
			if (res.data) {
				lstData.value.customers = res.data;
			}
		}
		async function getStartData() {
			const resp = await PreCertificate.getAppraisers();
			let dataAppraise = [...resp.data];
			lstData.value.business_managers = dataAppraise;
			lstData.value.appraiser_sales = dataAppraise;
			lstData.value.appraiser_performances = dataAppraise;

			const resp2 = await PreCertificate.getAppraiseOthers();
			lstData.value.appraiser_purposes = [...resp2.data.muc_dich_tham_dinh_gia];

			const respconfig = await PreCertificateConfig.getConfig();
			for (let index = 0; index < respconfig.data.length; index++) {
				const element = respconfig.data[index];
				if (element.name === "pre_types")
					lstData.value.preTypes = element.config;

				if (element.name === "workflow")
					lstData.value.workflow = element.config;
			}
			getCustomer();
		}

		getStartData();
		function updateRouteToast(router, toast) {
			other.value.router = router;
			other.value.toast = toast;
		}
		async function getPreCertificate(id) {
			const getDataCertificate = await PreCertificate.getDetailPreCertificate(
				id
			);
			dataPC.value = getDataCertificate.data;
		}
		async function createUpdatePreCertificateion(id = "") {
			other.value.isSubmit = true;
			// dataPC.value.pre_certificate_other_documents = preCertificateOtherDocuments.value;
			if (!dataPC.id) dataPC.value.status = 1;
			const res = await PreCertificate.createUpdatePreCertification(
				dataPC.value,
				id
			);
			if (res.data) {
				dataPC.value.id = res.data.id;

				await uploadFile();
				other.value.toast.open({
					message: "Lưu hồ sơ thẩm định thành công",
					type: "success",
					position: "top-right",
					duration: 3000
				});
				await other.value.router
					.push({
						name: "certification_brief.detail",
						query: { id: res.data.id }
					})
					.catch(_ => {});
			} else if (res.error) {
				other.value.toast.open({
					message: `${res.error.message}`,
					type: "error",
					position: "top-right"
				});
			} else {
				other.value.toast.open({
					message: "Lưu thất bại",
					type: "error",
					position: "top-right"
				});
			}
			other.value.isSubmit = false;
		}
		function resetData() {
			data.value = {
				id: null,
				certificate_id: null,
				petitioner_name: "Ông / Bà ",
				petitioner_phone: null,
				petitioner_address: null,
				petitioner_identity_card: null,
				customer_id: null,
				status: 1,
				appraise_purpose_id: null,
				note: null,
				appraiser_sale_id: null,
				business_manager_id: null,
				appraiser_perform_id: null,
				total_preliminary_value: null,
				cancel_reason: null,

				customer: {
					name: null,
					address: null,
					phone: null
				},

				business_manager: {
					name: null,
					id: null
				},
				appraiser_performance: {
					name: null,
					id: null
				},
				appraiser_sale: {
					name: null,
					id: null
				},
				pre_type: "Cơ bản",
				uploadFile: null
			};
			other.value.isSubmit = false;
		}
		async function uploadFile() {
			if (dataPc.value.uploadFile) {
				const res = await File.uploadFilePreCertificate(
					dataPc.value.uploadFile,
					16
				);
				if (res.data) {
					// await this.$emit('handleChangeFile', res.data.data)
					preCertificateOtherDocuments.value = res.data.data;
					other.value.toast.open({
						message: "Thêm file thành công",
						type: "success",
						position: "top-right",
						duration: 3000
					});
				} else {
					other.value.toast.open({
						message: "Thêm file thất bại",
						type: "error",
						position: "top-right",
						duration: 3000
					});
				}
			}
			return;
		}
		return {
			dataPC,
			lstData,
			preCertificateOtherDocuments,
			permission,
			other,

			resetData,
			getPreCertificate,
			createUpdatePreCertificateion,
			updateRouteToast,
			uploadFile
		};
	},
	{
		persist: true
	}
);
