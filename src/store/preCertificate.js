// @ts-nocheck
import { defineStore } from "pinia";
import { ref } from "vue";
import PreCertificate from "@/models/PreCertificate";
import PreCertificateConfig from "@/models/PreCertificateConfig";
import File from "@/models/File";
import { convertPagination } from "@/utils/filters";
import moment from "moment";
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
			appraiser_business_manager: {
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
			pre_type: "coban",
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
		const preCertificateOtherDocuments = ref({
			lstDocument: [
				{
					pre_certificate_id: null,
					name: null,
					link: null,
					type: null,
					size: null,
					description: null,
					type_document: null
				}
			],
			Appendix: [],
			Result: []
		});

		const lstData = ref({
			appraiser_business_managers: [],
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
		async function getConfig() {
			const respconfig = await PreCertificateConfig.getConfig();
			for (let index = 0; index < respconfig.data.length; index++) {
				const element = respconfig.data[index];
				element.config = JSON.parse(element.config);
				if (element.name === "pre_types")
					lstData.value.preTypes = element.config;

				if (element.name === "workflow") {
					lstData.value.workflow = element.config;
					jsonConfig.value = lstData.value.workflow;
				}
			}
			return lstData.value.workflow;
		}
		async function getStartData() {
			const resp = await PreCertificate.getAppraisers();
			let dataAppraise = [...resp.data];
			lstData.value.appraiser_business_managers = dataAppraise;
			lstData.value.appraiser_sales = dataAppraise;
			lstData.value.appraiser_performances = dataAppraise;

			const resp2 = await PreCertificate.getAppraiseOthers();
			lstData.value.appraiser_purposes = [...resp2.data.muc_dich_tham_dinh_gia];
			getConfig();
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
			if (dataPC.value.other_documents) {
				preCertificateOtherDocuments.value.lstDocument =
					dataPC.value.other_documents;
				preCertificateOtherDocuments.value.Appendix = dataPC.value.other_documents.filter(
					file => file.type_document === "Appendix"
				);
				preCertificateOtherDocuments.value.Result = dataPC.value.other_documents.filter(
					file => file.type_document === "Result"
				);
			}

			if (!dataPC.value.customer) {
				dataPC.value.customer = {
					name: null,
					address: null,
					phone: null
				};
			}
			if (!dataPC.value.appraiser_business_manager) {
				dataPC.value.appraiser_business_manager = {
					name: null,
					id: null
				};
			}

			if (!dataPC.value.appraiser_performance) {
				dataPC.value.appraiser_performance = {
					name: null,
					id: null
				};
			}

			if (!dataPC.value.appraiser_sale) {
				dataPC.value.appraiser_sale = {
					name: null,
					id: null
				};
			}
			if (dataPC.value.pre_type && lstData.value.preTypes) {
				for (let index = 0; index < lstData.value.preTypes.length; index++) {
					const element = lstData.value.preTypes[index];
					if (element.value === dataPC.value.pre_type)
						dataPC.value.pre_type_string = element.label;
				}
			}
			return dataPC.value;
		}
		async function createUpdatePreCertificateion(id = "") {
			other.value.isSubmit = true;
			// dataPC.value.pre_certificate_other_documents = preCertificateOtherDocuments.value;
			if (!dataPC.value.id) dataPC.value.status = 1;
			const res = await PreCertificate.createUpdatePreCertification(
				dataPC.value,
				dataPC.value.id || ""
			);

			if (res.data) {
				dataPC.value.id = res.data.id;

				await uploadFilePreCertificateFunction("Appendix");
				other.value.toast.open({
					message: "Lưu hồ sơ thẩm định thành công",
					type: "success",
					position: "top-right",
					duration: 3000
				});
				await other.value.router
					.push({
						name: "pre_certification.detail",
						query: { id: `${res.data.id}` }
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
		const lstPreCertificate = ref([]);
		const paginationAll = ref({});
		const filter = ref({ search: "", data: { data: "", type: "" } });
		const selectedStatus = ref([]);
		const isLoading = ref(false);
		async function getPreCertificateAll(params = {}) {
			isLoading.value = true;
			try {
				// const params = {
				// 	page: filter.page,
				// 	limit: filter.limit
				// };
				const resp = await PreCertificate.paginate({
					query: {
						page: 1,
						limit: 20,
						...params,
						...filter.value,
						status: selectedStatus.value
					}
				});
				lstPreCertificate.value = [...resp.data.data];
				paginationAll.value = convertPagination(resp.data);
				isLoading.value = false;
			} catch (e) {
				isLoading.value = false;
			}
		}
		const jsonConfig = ref(null);
		async function updateToStage2() {
			dataPC.value.status = 2;
			dataPC.value.status_expired_at = await getExpireStatusDate(2);
			let dataSend = {
				// appraiser_id: this.elementDragger.appraiser_id,
				// business_manager_id: this.elementDragger.business_manager_id,
				// appraiser_sale_id: this.elementDragger.appraiser_sale_id,
				// appraiser_perform_id: this.elementDragger.appraiser_perform_id,
				// check_price: this.isCheckPrice,
				// check_version: this.isCheckVersion,
				// required: this.changeStatusRequire,

				appraiser_id: null,
				business_manager_id: null,
				appraiser_perform_id: null,
				appraiser_sale_id: null,
				check_price: null,
				check_version: null,
				required: null,
				status: 2,
				status_expired_at: dataPC.value.status_expired_at,
				status_note: dataPC.value.status_note,
				status_reason_id: "",
				status_description: "Định giá sơ bộ",
				status_config: jsonConfig.value.principle
			};
			const res = await PreCertificate.updateStatusPreCertificate(
				dataPC.value.id,
				dataSend
			);

			if (res.data) {
				other.value.toast.open({
					message: `Định giá sơ bộ thành công`,
					type: "success",
					position: "top-right"
				});
			} else if (res.error) {
				other.value.toast.open({
					message: `${res.error.message}`,
					type: "error",
					position: "top-right"
				});
			} else {
				other.value.toast.open({
					message: "Chuyển tiếp thất bại",
					type: "error",
					position: "top-right"
				});
			}
			other.value.isSubmit = false;
		}
		async function updateStatus() {
			dataPC.value.status_expired_at = getExpireStatusDate(dataPC.value.status);
			const res = await PreCertificate.updateStatusPreCertificate(
				dataPC.value.id,
				dataPC.value
			);

			if (res.data) {
				console.log("res.data", res.data);
				return res.data;
			} else if (res.error) {
				other.value.toast.open({
					message: `${res.error.message}`,
					type: "error",
					position: "top-right"
				});
			} else {
				other.value.toast.open({
					message: "Chuyển tiếp thất bại",
					type: "error",
					position: "top-right"
				});
			}
			other.value.isSubmit = false;
		}
		function getExpireStatusDate(status) {
			const config = jsonConfig.value.principle.find(
				item => item.status === status && item.isActive === 1
			);
			let dateConvert = new Date();
			let minutes = config.process_time ? config.process_time : 1440;
			let dateConverted = new Date(dateConvert.getTime() + minutes * 60000);
			let status_expired_at = moment(dateConverted).format("DD-MM-YYYY HH:mm");
			return status_expired_at;
		}
		function resetData() {
			lstPreCertificate.value = [];
			paginationAll.value = {};
			filter.value = { search: "", data: { data: "", type: "" } };
			selectedStatus.value = [];
			isLoading.value = false;

			dataPC.value = {
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

				appraiser_business_manager: {
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
			preCertificateOtherDocuments.value = {
				lstDocument: [
					{
						pre_certificate_id: null,
						name: null,
						link: null,
						type: null,
						size: null,
						description: null,
						type_document: null
					}
				],
				Appendix: [],
				Result: []
			};
		}
		async function uploadFilePreCertificateFunction(type) {
			if (dataPC.value.uploadFile) {
				const formData = new FormData();
				for (let i = 0; i < dataPC.value.uploadFile.length; i++) {
					formData.append("files[" + i + "]", dataPC.value.uploadFile[i]);
				}
				const res = await File.uploadFilePreCertificate(
					formData,
					dataPC.value.id,
					type
				);
				if (res.data) {
					// await this.$emit('handleChangeFile', res.data.data)
					// preCertificateOtherDocuments.value = res.data.data;
					// other.value.toast.open({
					// 	message: "Thêm file thành công",
					// 	type: "success",
					// 	position: "top-right",
					// 	duration: 3000
					// });
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
			lstPreCertificate,
			paginationAll,
			filter,
			selectedStatus,
			isLoading,
			jsonConfig,

			resetData,
			getPreCertificate,
			createUpdatePreCertificateion,
			updateRouteToast,
			getConfig,
			getPreCertificateAll,
			updateToStage2,
			updateStatus
		};
	},
	{
		persist: true
	}
);
