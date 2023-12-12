// @ts-nocheck
import { defineStore } from "pinia";
import { ref } from "vue";
import PreCertificate from "@/models/PreCertificate";
export const usePreCertificateStore = defineStore(
	"preCertificate",
	() => {
		const dataPC = ref({
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
			}
		});

		const permission = ref({
			allowDelete: true
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
			customers: []
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
			getCustomer();
		}

		getStartData();
		async function getPreCertificate(id) {
			console.log("runhere23");
			const getDataCertificate = await PreCertificate.getDetailPreCertificate(
				id
			);
			dataPC.value = getDataCertificate.data;
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
				status: null,
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
				}
			};
		}

		return {
			dataPC,
			lstData,
			preCertificateOtherDocuments,
			permission,

			resetData,
			getPreCertificate
		};
	},
	{
		persist: true
	}
);
