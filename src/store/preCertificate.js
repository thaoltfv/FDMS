// @ts-nocheck
import { defineStore } from "pinia";
import { ref } from "vue";
import PreCertificate from "@/models/PreCertificate";
import PreCertificateConfig from "@/models/PreCertificateConfig";
import WareHouse from "@/models/WareHouse";
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
			allowDelete: true,
			allowExport: true
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

		const lstDataConfig = ref({
			appraiser_business_managers: [],
			appraiser_sales: [],
			appraiser_performances: [],
			appraiser_purposes: [],
			customers: [],
			preTypes: null,
			workflow: null,
			cancelPCReasons: []
		});
		async function getCustomer() {
			let res = await PreCertificate.getCustomer();
			if (res.data) {
				lstDataConfig.value.customers = res.data;
			}
		}
		async function getConfig() {
			const respconfig = await PreCertificateConfig.getConfig();
			for (let index = 0; index < respconfig.data.length; index++) {
				const element = respconfig.data[index];
				element.config = JSON.parse(element.config);
				if (element.name === "pre_types")
					lstDataConfig.value.preTypes = element.config;

				if (element.name === "workflow") {
					lstDataConfig.value.workflow = element.config;
					jsonConfig.value = lstDataConfig.value.workflow;
				}
			}
			return lstDataConfig.value.workflow;
		}
		async function getLstAppraisers() {
			const resp = await PreCertificate.getAppraisers();
			let dataAppraise = [...resp.data];
			lstDataConfig.value.appraiser_business_managers = dataAppraise;
			lstDataConfig.value.appraiser_sales = dataAppraise;
			lstDataConfig.value.appraiser_performances = dataAppraise;
			return;
		}
		async function getLstDictionaries() {
			const resp = await WareHouse.getDictionaries();
			if (resp.data) {
				lstDataConfig.value.cancelPCReasons = resp.data.li_do_huy_so_bo;
			}
			return;
		}
		async function getStartData(
			isGetLstAppraisers = true,
			isGetAppraiseOthers = true,
			isGetConfig = true,
			isGetCustomer = true,
			isGetDistionaries = false
		) {
			if (isGetLstAppraisers) await getLstAppraisers();
			if (isGetAppraiseOthers) {
				const resp2 = await PreCertificate.getAppraiseOthers();
				lstDataConfig.value.appraiser_purposes = [
					...resp2.data.muc_dich_tham_dinh_gia
				];
			}
			if (isGetConfig) await getConfig();
			if (isGetCustomer) await getCustomer();
			if (isGetCustomer) await getLstDictionaries();
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
			const temp = getDataCertificate.data;
			if (temp.other_documents) {
				preCertificateOtherDocuments.value.lstDocument = temp.other_documents;
				preCertificateOtherDocuments.value.Appendix = temp.other_documents.filter(
					file => file.type_document === "Appendix"
				);
				preCertificateOtherDocuments.value.Result = temp.other_documents.filter(
					file => file.type_document === "Result"
				);
			}

			if (!temp.customer) {
				temp.customer = {
					name: null,
					address: null,
					phone: null
				};
			}
			if (!temp.appraiser_business_manager) {
				temp.appraiser_business_manager = {
					name: null,
					id: null
				};
			}

			if (!temp.appraiser_performance) {
				temp.appraiser_performance = {
					name: null,
					id: null
				};
			}

			if (!temp.appraiser_sale) {
				temp.appraiser_sale = {
					name: null,
					id: null
				};
			}
			if (temp.status == 6 && temp.cancel_reason) {
				if (lstDataConfig.value.cancelPCReasons.length == 0)
					await getLstDictionaries();

				const reason = lstDataConfig.value.cancelPCReasons.find(
					reason => `${reason.id}` === temp.cancel_reason
				);
				temp.cancel_reason_string = reason ? reason.description : "";
			}
			dataPC.value = temp;
			return dataPC.value;
		}
		async function createUpdatePreCertificateion(
			id = "",
			isReturn = false,
			assignObject = null
		) {
			other.value.isSubmit = true;
			// dataPC.value.pre_certificate_other_documents = preCertificateOtherDocuments.value;
			if (!dataPC.value.id) dataPC.value.status = 1;
			const res = await PreCertificate.createUpdatePreCertification(
				assignObject ? assignObject : dataPC.value,
				dataPC.value.id || ""
			);
			if (isReturn) {
				return res;
			}
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
		const lstPreCertificateKanban = ref([]);
		const lstPreCertificateTable = ref([]);
		const paginationAll = ref({});
		const filter = ref({ search: "", data: { data: "", type: "" } });
		const filterKanban = ref({
			search: "",
			selectedOfficialTransferStatus: [],
			selectedStatus: [],
			status: [],
			ots: null,
			timeFilter: {
				from: null,
				to: null
			}
		});
		const selectedStatus = ref([]);
		const isLoading = ref(false);
		async function getPreCertificateAll(type = "table") {
			isLoading.value = true;
			const tempstatus = [];
			let tempots = [];
			if (
				this.filterKanban.selectedOfficialTransferStatus[0] == true &&
				this.filterKanban.selectedOfficialTransferStatus[1] == true
			) {
				tempots = null;
			} else if (this.filterKanban.selectedOfficialTransferStatus[0] == true) {
				tempots = 0;
			} else if (this.filterKanban.selectedOfficialTransferStatus[1] == true) {
				tempots = 1;
			}
			for (
				let index = 0;
				index < filterKanban.value.selectedStatus.length;
				index++
			) {
				const element = filterKanban.value.selectedStatus[index];
				if (element) tempstatus.push(index + 1);
			}
			const temp = {
				search: filter.value.search,
				data: {
					status: tempstatus,
					ots: tempots,
					timeFilter: {
						from: filterKanban.value.timeFilter.from,
						to: filterKanban.value.timeFilter.to
					}
				}
			};
			try {
				if (type == "kanban") {
					const respkanban = await PreCertificate.getListFilterKanbanPreCertificate(
						temp
					);
					isLoading.value = false;
					return respkanban;
				} else {
					const resp = await PreCertificate.paginate({
						query: {
							page: 1,
							limit: 20,
							...temp
						}
					});

					lstPreCertificateTable.value = [...resp.data.data];
					paginationAll.value = convertPagination(resp.data);
					isLoading.value = false;
				}
			} catch (e) {
				isLoading.value = false;
			}
		}

		const jsonConfig = ref(null);
		async function rejectFromStage2ToStage1() {
			for (
				let index = 0;
				index < preCertificateOtherDocuments.value.Result.length;
				index++
			) {
				const element = preCertificateOtherDocuments.value.Result[index];
				const res = await File.deleteFilePreCertificate(element.id);
				if (res.data) {
					// other.value.toast.open({
					// 	message: "Xóa thành công",
					// 	type: "success",
					// 	position: "top-right",
					// 	duration: 3000
					// });
				} else if (res.error) {
					other.value.toast.open({
						message: res.error.message,
						type: "error",
						position: "top-right",
						duration: 3000
					});
				} else {
					other.value.toast.open({
						message: "Có lỗi xảy ra trong lúc xóa file",
						type: "error",
						position: "top-right",
						duration: 3000
					});
				}
			}

			return;
		}

		async function updateStatus(id, note, reason_id) {
			const config = jsonConfig.value.principle.find(
				item =>
					item.status === dataPC.value.target_status && item.isActive === 1
			);
			dataPC.value.status_expired_at_string = await getExpireStatusDate(config);
			let dataSend = {
				business_manager_id: null,
				appraiser_perform_id: null,
				appraiser_sale_id: null,
				check_price: null,
				check_version: null,
				required: {
					appraise_item_list: false,
					appraiser: true,
					check_legal: false,
					check_price: false,
					check_version: false
				},
				status: dataPC.value.target_status,
				status_expired_at: dataPC.value.status_expired_at_string,
				status_note: note,
				status_reason_id: reason_id,
				status_description: config.description,
				status_config: jsonConfig.value.principle
			};
			if (
				(dataPC.value.status == 2 || dataPC.value.status == 6) &&
				dataPC.value.target_status == 1
			) {
				await rejectFromStage2ToStage1();
				dataSend.total_preliminary_value = 0;
			}
			if (dataPC.value.target_status == 6 && reason_id) {
				dataSend.cancel_reason = reason_id;
			}
			// if (dataPC.value.target_status == 1 && dataPC.value.status == 6) {
			// 	dataSend.cancel_reason = null;
			// }
			const res = await PreCertificate.updateStatusPreCertificate(id, dataSend);

			return res;
		}
		function getExpireStatusDate(config) {
			let dateConvert = new Date();
			let minutes = config.process_time ? config.process_time : 1440;
			let dateConverted = new Date(dateConvert.getTime() + minutes * 60000);
			let status_expired_at = moment(dateConverted).format("DD-MM-YYYY HH:mm");
			return status_expired_at;
		}
		function resetData() {
			lstPreCertificateTable.value = [];
			lstPreCertificateKanban.value = [];
			paginationAll.value = {};
			filter.value = { search: "", data: { data: "", type: "" } };
			selectedStatus.value = [];
			isLoading.value = false;
			filterKanban.value = {
				search: "",
				selectedOfficialTransferStatus: [],
				selectedStatus: [],
				status: [],
				ots: null,
				timeFilter: {
					from: null,
					to: null
				}
			};
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
			lstDataConfig,
			preCertificateOtherDocuments,
			permission,
			other,
			lstPreCertificateTable,
			lstPreCertificateKanban,
			paginationAll,
			filter,
			selectedStatus,
			isLoading,
			jsonConfig,
			filterKanban,

			resetData,
			getPreCertificate,
			createUpdatePreCertificateion,
			updateRouteToast,
			getConfig,
			getPreCertificateAll,
			rejectFromStage2ToStage1,
			updateStatus,
			getLstAppraisers,
			getStartData
		};
	},
	{
		persist: true
	}
);
