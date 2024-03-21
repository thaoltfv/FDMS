// @ts-nocheck
import { defineStore } from "pinia";
import { ref } from "vue";
import PreCertificate from "@/models/PreCertificate";
import PreCertificateConfig from "@/models/PreCertificateConfig";
import WareHouse from "@/models/WareHouse";
import File from "@/models/File";
import { convertPagination } from "@/utils/filters";
import moment from "moment";
import _ from "lodash";
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
			pre_type_id: null,
			commission_fee: 0,
			pre_date: null,
			pre_asset_name: null,
			total_service_fee: 0,

			uploadFile: null,

			payments: [
				{
					id: null,
					amount: 0,
					pay_date: null,
					for_payment_of: ""
				}
			]
		});

		const vueStoree = ref({
			currentPermissions: null,
			profile: null,
			user: null
		});

		function updateVueStore(profile, user, currentPermissions) {
			vueStoree.value.profile = profile;
			vueStoree.value.user = user;
			vueStoree.value.currentPermissions = currentPermissions;
		}

		const permission = ref({
			allowDelete: true,
			allowExport: true,
			editPayments: false,
			edit: false
		});
		function updatePermission(data) {
			permission.value.editPayments = data.editPayments;
			permission.value.edit = data.edit;
		}
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
			preTypes: [],
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
				// if (element.name === "pre_types")
				// 	lstDataConfig.value.preTypes = element.config;

				if (element.name === "workflow") {
					lstDataConfig.value.workflow = element.config;
					jsonConfig.value = lstDataConfig.value.workflow;
				}
			}
			return lstDataConfig.value.workflow;
		}
		async function functionUpdateWorkflow(data) {
			jsonConfig.value = data;
			lstDataConfig.value.workflow = data;
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
				lstDataConfig.value.preTypes = resp.data.loai_so_bo;
			}
			return;
		}
		async function getStartData(
			isGetLstAppraisers = true,
			isGetAppraiseOthers = true,
			isGetConfig = true,
			isGetCustomer = true,
			isGetDistionaries = true
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
			if (isGetDistionaries) await getLstDictionaries();
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
				temp.cancel_reason_string = temp.cancel_reason.description || "";
			}
			if (temp.payments.length === 0) {
				temp.payments = [
					{
						id: null,
						amount: 0,
						pay_date: null,
						for_payment_of: ""
					}
				];
				temp.paymentsOriginal = [
					{
						id: null,
						amount: 0,
						pay_date: null,
						for_payment_of: ""
					}
				];
			}

			temp.debtRemain = temp.total_service_fee;
			temp.amountPaid = 0;
			for (let index = 0; index < temp.payments.length; index++) {
				const element = temp.payments[index];
				element.pay_date = element.pay_date ? moment(element.pay_date) : "";
				temp.amountPaid = temp.amountPaid + parseFloat(element.amount);
				temp.debtRemain -= element.amount;
			}
			temp.paymentsOriginal = JSON.parse(JSON.stringify(temp.payments));
			dataPC.value = temp;
			return dataPC.value;
		}
		async function createUpdatePreCertificateion(
			id = "",
			isReturn = false,
			assignObject = null
		) {
			const tempASSign = assignObject ? assignObject : dataPC.value;
			const tempUpdate = _.cloneDeep(tempASSign);
			other.value.isSubmit = true;
			if (moment(tempUpdate.pre_date, "DD/MM/YYYY", true).isValid()) {
				tempUpdate.pre_date = moment(tempUpdate.pre_date, "DD-MM-YYYY").format(
					"YYYY-MM-DD"
				);
			}
			if (!tempUpdate.id) tempUpdate.status = 1;
			if (tempUpdate.cancel_reason) delete tempUpdate.cancel_reason;
			const res = await PreCertificate.createUpdatePreCertification(
				tempUpdate,
				tempUpdate.id || ""
			);
			if (isReturn) {
				return res;
			}
			if (res.data) {
				dataPC.value.id = res.data.id;

				if (dataPC.value.payments && dataPC.value.payments.length > 0) {
					for (let index = 0; index < dataPC.value.payments.length; index++) {
						const element = dataPC.value.payments[index];
						if (moment(element.pay_date, "DD/MM/YYYY", true).isValid()) {
							element.pay_date = moment(element.pay_date, "DD-MM-YYYY").format(
								"YYYY-MM-DD"
							);
						}
					}
					let difference = dataPC.value.payments.filter(
						payment => !dataPC.value.paymentsOriginal.includes(payment)
					);
					const res = await updatePaymentFunction(difference, true);
				}
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

			const temp = {
				search: filter.value.search,
				data: {
					status: filterKanban.value.selectedStatus,
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
			let tempLength = [];
			if (
				preCertificateOtherDocuments.value.Result.length === 0 &&
				dataPC.value.other_documents
			) {
				tempLength = dataPC.value.other_documents.filter(
					file => file.type_document === "Result"
				);
			} else {
				tempLength = preCertificateOtherDocuments.value.Result;
			}
			for (let index = 0; index < tempLength.length; index++) {
				const element = tempLength[index];
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

		async function updateStatus(id, note, reason_id, appraiser = null, estime) {
			const config = jsonConfig.value.principle.find(
				item =>
					item.status === dataPC.value.target_status && item.isActive === 1
			);
			// dataPC.value.status_expired_at_string = config.process_time
			// 	? await getExpireStatusDate(config)
			// 	: null;
			dataPC.value.status_expired_at_string = estime;
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
			if (appraiser) {
				dataSend[appraiser.type] = appraiser.id;
			}
			if (
				(dataPC.value.status == 2 || dataPC.value.status == 7) &&
				dataPC.value.target_status == 1
			) {
				await rejectFromStage2ToStage1();
				dataSend.total_preliminary_value = 0;
			}
			if (dataPC.value.target_status == 7 && reason_id) {
				dataSend.cancel_reason = reason_id;
			}
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

		async function updatePaymentFunction(data, isReturn = false) {
			other.value.isSubmit = true;

			const updatedPayments = data.map(element => {
				const updatedElement = { ...element };
				if (moment(updatedElement.pay_date, "DD/MM/YYYY", true).isValid()) {
					updatedElement.pay_date = moment(
						updatedElement.pay_date,
						"DD/MM/YYYY"
					).format("YYYY-MM-DD");
				}
				return updatedElement;
			});

			const res = await PreCertificate.updatePayments(updatedPayments, 9999);

			if (isReturn) {
				return res;
			}

			if (res.data && res.data.error === false) {
				other.value.toast.open({
					message: "Lưu thông tin thanh toán thành công",
					type: "success",
					position: "top-right",
					duration: 3000
				});
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

				pre_type_id: null,
				commission_fee: 0,
				pre_date: null,
				pre_asset_name: null,
				total_service_fee: 0,
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
			vueStoree,
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
			getStartData,
			updatePaymentFunction,
			updateVueStore,
			updatePermission,
			functionUpdateWorkflow
		};
	},
	{
		persist: true
	}
);
