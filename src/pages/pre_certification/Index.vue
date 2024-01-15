<template>
	<div style="overflow-x: auto">
		<div class="container-fluid mt-3 kanban_board">
			<div class="d-flex scroll_board" id="infinite-list">
				<div
					class="col p-2 container_kanban mr-3"
					:class="`border-${config.css.color}`"
					v-for="config in principleConfig"
					:key="config.id"
				>
					<div class="p-2 mt-3 ml-2 mb-2 d-flex justify-content-between">
						<h3 class="mr-3 title" :class="`text-${config.css.color}`">
							{{ config.description }}
						</h3>
						<div class="quatity text-white" :class="`bg-${config.css.color}`">
							{{
								subStatusDataTmp[config.id]
									? subStatusDataTmp[config.id].length
									: 0
							}}
						</div>
					</div>
					<draggable
						:id="config.id"
						:key="key_dragg"
						:animation="500"
						:group="{ name: config.id, put: config.put_draggable }"
						:list="subStatusData[config.id]"
						:move="checkMove"
						@remove="changedDraggable"
						ghost-class="ghost"
						class="list-group kanban-column"
					>
						<b-card
							:class="{
								border_expired: checkDateExpired(element),
								['border-' + config.css.color]: true
							}"
							class="card_container mb-3"
							v-for="element in subStatusData[config.id]"
							:key="element.id + '_' + element.status"
						>
							<div class="col-12 d-flex mb-2 justify-content-between">
								<div class="row ml-0">
									<span
										@click="handleDetailPreCertificate(element.id)"
										class="content_id"
										:class="
											`bg-${config.css.color}-15 text-${config.css.color}`
										"
										>{{ element.slug }}</span
									>
									<span
										v-if="element.certificate_id"
										@click="handleDetailCertificate(element.certificate_id)"
										class=" card-status-certificate ml-2"
										:id="`${element.certificate_id + element.id}`"
									>
										<icon-base
											name="nav_hstd"
											class="item-icon svg-inline--fa"
										/>
										<b-tooltip
											:target="`${element.certificate_id + element.id}`"
											placement="right"
											>{{
												`Được chuyển chính thức: HTSD_${element.certificate_id}`
											}}</b-tooltip
										>
									</span>
								</div>
								<img
									v-if="checkDateExpired(element)"
									class="mr-2 icon_expired"
									src="@/assets/icons/ic_expire_calender.svg"
									alt="ic_expire_calender"
								/>
							</div>
							<div class="property-content mb-2 d-flex color_content">
								<div class="label_container d-flex">
									<img
										style="min-width:15px"
										width="15px"
										height="21px"
										class="mr-2"
										src="@/assets/icons/ic_user_2.svg"
										alt="user"
									/>
									<div class="d-flex">
										<span style="font-weight: 500"
											><strong class="d-none d_inline mr-1">Khách hàng:</strong
											>{{ element.petitioner_name }}</span
										>
									</div>
								</div>
							</div>
							<div class="property-content mb-2 d-flex color_content">
								<img
									class="mr-2"
									src="@/assets/icons/ic_price.svg"
									alt="user"
								/>
								<div class="label_container d-flex">
									<strong class="d-none d_inline mr-1">Tổng giá trị:</strong
									><span style="font-weight: 500">{{
										element.total_preliminary_value
											? `${formatPrice(element.total_preliminary_value)}`
											: "-"
									}}</span>
								</div>
							</div>
							<div class="property-content mb-2 d-flex color_content">
								<img
									class="mr-2"
									src="@/assets/icons/ic_clock.svg"
									alt="user"
								/>
								<div class="label_container d-flex">
									<strong class="d-none d_inline mr-1">Thời hạn:</strong
									><span style="font-weight: 500">{{
										getExpireDate(element)
									}}</span>
								</div>
							</div>
							<div class="property-content d-flex justify-content-between mb-0">
								<div class="label_container d-flex">
									<img
										width="15px"
										class="mr-2"
										src="@/assets/icons/ic_taglink.svg"
										alt="user"
									/><span style="color:#8B94A3">{{
										element.document_count
									}}</span>
								</div>
								<img
									class="img_user"
									:src="
										element.image
											? element.image
											: 'https://upload.wikimedia.org/wikipedia/commons/b/bc/Unknown_person.jpg'
									"
								/>
							</div>
						</b-card>
					</draggable>
				</div>
			</div>
			<ModalDetailPreCertificate
				v-if="showDetailPopUp"
				:idData="idData"
				:edit="edit"
				:add="add"
				:user_id="user_id"
				:appraiser_number="appraiser_number"
				:profile="profile"
				:data="detailData"
				@cancel="showDetailPopUp = false"
				@action="handleUpdateStatus"
				@handleFooterAccept="handleFooterAccept"
			/>

			<ModalNotificationPreCertificateNote
				v-if="isMoved"
				:notification="
					confirm_message == 'Từ chối' ||
					confirm_message == 'Khôi phục' ||
					confirm_message == 'Hủy'
						? `Bạn có muốn '${confirm_message}' hồ sơ này?`
						: `Bạn có muốn chuyển yêu cầu này sang trạng thái '${confirm_message}'`
				"
				@action="handleChangeAccept2"
				@cancel="handleCancelAccept2"
			/>
			<ModalNotificationPreCertificateNote
				v-if="isHandleAction"
				@cancel="isHandleAction = false"
				:notification="
					confirm_message == 'Từ chối' ||
					confirm_message == 'Khôi phục' ||
					confirm_message == 'Hủy'
						? `Bạn có muốn '${confirm_message}' hồ sơ này?`
						: `Bạn có muốn chuyển yêu cầu này sang trạng thái '${confirm_message}'`
				"
				@action="handleChangeAccept2"
			/>
		</div>
	</div>
</template>

<script>
import { ref } from "vue";
import { storeToRefs } from "pinia";
import { usePreCertificateStore } from "@/store/preCertificate";

import { PERMISSIONS } from "@/enum/permissions.enum";
import { FormWizard, TabContent } from "vue-form-wizard";
import {
	UserIcon,
	DollarSignIcon,
	HomeIcon,
	ClockIcon
} from "vue-feather-icons";
import {
	BCard,
	BRow,
	BCol,
	BFormGroup,
	BFormInput,
	BTooltip
} from "bootstrap-vue";
import draggable from "vuedraggable";
import JsonExcel from "vue-json-excel";
import Vue from "vue";
import ModalDetailPreCertificate from "@/components/PreCertificate/ModalDetailPreCertificate";
import ModalSendVerify from "@/components/Modal/ModalSendVerify";
import PreCertificate from "@/models/PreCertificate";
import moment from "moment";
import ModalNotificationCertificate from "@/components/Modal/ModalNotificationCertificate";
import ModalNotificationPreCertificateNote from "@/components/PreCertificate/ModalNotificationPreCertificateNote";
import IconBase from "@/components/IconBase.vue";

// const jsonConfig = require("../../../config/pre_certificate_workflow.json");
Vue.component("downloadExcel", JsonExcel);
export default {
	name: "Index",
	props: ["search_kanban"],
	data() {
		return {
			theme: {
				navItem: "#000000",
				navActiveItem: "#FAA831",
				slider: "#FAA831",
				arrow: "#000000"
			},
			now: new Date(),
			total_amount: "",
			width: "",
			currency: "",
			listTest: [
				{
					name: "test",
					id: 1
				}
			],
			idUpdate: "",
			idData: "",
			key_render_appraisal: 321000,
			render_lock: 1234,
			render_open: 5678,
			listCertificateDraft: [],
			listCertificateOpen: [],
			listCertificateLock: [],
			listCertificatesClose: [],
			listCertificatesCanceled: [],

			listCertificateTemp: [],
			listCertificateDraftTemp: [],
			listCertificateOpenTemp: [],
			listCertificateLockTemp: [],
			listCertificatesCloseTemp: [],
			listCertificatesCanceledTemp: [],
			filter: {},
			status: "",
			activeStatus: false,
			showAppraisalDialog: false,
			showModalSearch: false,
			view: false,
			add: false,
			edit: false,
			deleted: false,
			accept: false,
			export: false,
			showDetailPopUp: false,
			showVerifyCertificate: false,
			idDraft: "",
			elementDragger: "",
			position_profile: "",
			appraise_number: "",
			checkRole: false,
			profile: {},
			user_id: "",
			isAccept: false,
			next_status: "",
			confirm_message: "",
			isMoved: false,
			config: {},
			detailData: [],
			isHandleAction: false,
			isCheckPrice: false,
			isCheckLegal: false,
			isCheckVersion: false,
			changeStatusRequire: {}
		};
	},
	components: {
		IconBase,
		"b-tooltip": BTooltip,
		draggable,
		BCard,
		FormWizard,
		TabContent,
		BRow,
		BCol,
		BFormGroup,
		BFormInput,
		UserIcon,
		DollarSignIcon,
		HomeIcon,
		ClockIcon,
		ModalDetailPreCertificate,
		ModalSendVerify,
		ModalNotificationCertificate,
		ModalNotificationPreCertificateNote
	},
	created() {
		// fix_permission
		this.profile = this.$store.getters.profile;
		const profile = this.$store.getters.profile;
		if (profile.data.user) {
			this.position_profile =
				profile.data.user.appraiser.appraise_position.acronym;
			this.appraiser_number = profile.data.user.appraiser.appraiser_number;
		}
		this.user_id = profile.data.user.id;
		const permission = this.$store.getters.currentPermissions;
		permission.forEach(value => {
			if (value === PERMISSIONS.VIEW_CERTIFICATE_BRIEF) {
				this.view = true;
			}
			if (value === PERMISSIONS.ADD_CERTIFICATE_BRIEF) {
				this.add = true;
			}
			if (value === PERMISSIONS.EDIT_CERTIFICATE_BRIEF) {
				this.edit = true;
			}
			if (value === PERMISSIONS.DELETE_CERTIFICATE_BRIEF) {
				this.deleted = true;
			}
			if (value === PERMISSIONS.ACCEPT_CERTIFICATE_BRIEF) {
				this.accept = true;
			}
			if (value === PERMISSIONS.EXPORT_CERTIFICATE_BRIEF) {
				this.export = true;
			}
		});
	},
	computed: {
		updateDate() {
			return dateUpdate => {
				return moment(dateUpdate).fromNow();
			};
		}
	},
	setup(props) {
		const checkMobile = () => {
			if (
				/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
					navigator.userAgent
				)
			) {
				return true;
			} else {
				return false;
			}
		};
		const isMobile = ref(checkMobile());

		const preCertificateStore = usePreCertificateStore();
		const { lstDataConfig, dataPC, jsonConfig, filter } = storeToRefs(
			preCertificateStore
		);

		const principleConfig = ref([]);

		const countData = ref(0);
		const subStatusDataReturn = ref([]);
		const subStatusData = ref({});
		const key_dragg = ref(1);
		const pushSubStatusData = () => {
			let count = countData.value ? countData.value : 0;
			let tmp = subStatusDataTmp.value;
			let config = principleConfig.value;
			let data = [];
			let dataReturn = subStatusDataReturn.value;
			config.forEach(item => {
				data = subStatusData.value[item.id] ? subStatusData.value[item.id] : [];
				if (tmp[item.id] && tmp[item.id].length > count) {
					for (var i = count; i < count + 10; i++) {
						tmp[item.id][i] &&
							data.push(tmp[item.id][i]) &&
							dataReturn.push(tmp[item.id][i]);
					}
				}
				subStatusData.value[item.id] = data;
			});

			key_dragg.value++;
			countData.value += 10;
		};
		const lstPreCertificateKanban = ref([]);
		const subStatusDataTmp = ref({});
		const getDataWorkFlow2 = async (isRefresh = false, search = "") => {
			try {
				const resp = await preCertificateStore.getPreCertificateAll("kanban");

				if (resp.data) {
					lstPreCertificateKanban.value = resp.data.HSTD;
					if (isRefresh) {
						subStatusDataReturn.value = [];
						subStatusDataTmp.value = [];
						subStatusData.value = [];
						countData.value = 0;
						key_dragg.value = 1;
					}
					if (principleConfig.value.length > 0) {
						let dataTmp = [];
						principleConfig.value.forEach(item => {
							dataTmp = lstPreCertificateKanban.value.filter(
								i => i.status === item.status
							);
							subStatusDataTmp.value[item.id] = dataTmp;
						});
						pushSubStatusData();
					}
				}
			} catch (e) {}
		};
		const startSetup = async () => {
			if (jsonConfig.value === null) {
				jsonConfig.value = await preCertificateStore.getConfig();
			}
			if (jsonConfig.value && jsonConfig.value.principle) {
				principleConfig.value = jsonConfig.value.principle.filter(
					i => i.isActive === 1
				);
			}
			if (props.search_kanban) {
				getDataWorkFlow2(false, props.search_kanban.search);
			} else getDataWorkFlow2();
		};
		startSetup();

		return {
			filter,
			principleConfig,
			jsonConfig,
			isMobile,
			lstDataConfig,
			dataPC,
			preCertificateStore,

			key_dragg,
			lstPreCertificateKanban,
			subStatusDataTmp,
			countData,
			subStatusDataReturn,
			subStatusData,

			getDataWorkFlow2,
			pushSubStatusData
		};
	},
	methods: {
		async handleActionStage3() {
			if (this.search_kanban) {
				await this.getDataWorkFlow2(true, this.search_kanban.search, isRefresh);
			} else await this.getDataWorkFlow2(true);
			this.isMoved = false;
			this.showDetailPopUp = false;
			this.isHandleAction = false;
		},
		getExpireDate(element) {
			let strExpire = "";
			switch (element.status) {
				case 6:
					strExpire = "Đã hủy";
					break;
				case 5:
					strExpire = "Đã hoàn thành";
					break;
				default:
					strExpire = element.status_expired_at
						? this.updateDate(element.status_expired_at, new Date())
						: "Đã hết hạn";
					break;
			}
			return strExpire;
		},
		checkDateExpired(element) {
			let check = false;
			switch (element.status) {
				case 1:
				case 2:
				case 3:
					if (element.status_expired_at) {
						if (
							this.updateDate(element.status_expired_at, this.now).includes(
								"Đã hết hạn"
							)
						) {
							check = true;
						}
					} else {
						check = true;
					}
					break;
			}
			return check;
		},
		formatPrice(value) {
			let num = parseFloat(value / 1)
				.toFixed(0)
				.replace(".", ",");
			if (num.length > 3 && num.length <= 6) {
				return (
					parseFloat(num / 1000)
						.toFixed(1)
						.replace(".", ",") + " Nghìn"
				);
			} else if (num.length > 6 && num.length <= 9) {
				return (
					parseFloat(num / 1000000)
						.toFixed(1)
						.replace(".", ",") + " Triệu"
				);
			} else if (num.length > 9) {
				return (
					parseFloat(num / 1000000000)
						.toFixed(1)
						.replace(".", ",") + " Tỷ"
				);
			} else if (num < 900) {
				return num + " đ"; // if value < 1000, nothing to do
			}
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
		},
		handleMoveDraft(event) {
			if (
				event.draggedContext.element.appraiser_sale &&
				(this.user_id === event.draggedContext.element.appraiser_sale.user_id ||
					event.draggedContext.element.created_by === this.profile.data.user.id)
			) {
				this.idDragger = event.draggedContext.element.id;
				this.elementDragger = event.draggedContext.element;
			} else return false;
		},
		checkMove(evt) {
			let draggerElement = evt.draggedContext.element;
			let configId = parseInt(evt.from.id);
			let config = this.principleConfig.find(i => i.id === configId);
			let user = this.profile.data.user;
			let check = false;
			if (evt.from.id !== evt.to.id) {
				if (config && config.put_require.length > 0) {
					config.put_require.forEach(i => {
						if (
							(i === "created_by" && draggerElement[i] === user.id) ||
							(i !== "created_by" && draggerElement[i] === user.appraiser.id)
						) {
							check = true;
						}
					});
				}
			}
			this.elementDragger = draggerElement;
			this.idDragger = draggerElement.id;
			this.dataPC = draggerElement;
			this.config = config;
			return check;
		},
		changedDraggable(evt) {
			let targetId = parseInt(evt.to.id);
			let check = false;
			let targetConfig = this.principleConfig.find(i => i.id === targetId);
			let draggableDescription = this.config.target_description.find(
				i => i.id === targetId
			);
			let message = draggableDescription
				? draggableDescription.description
				: "";
			check = this.checkRequired(targetConfig.require, this.elementDragger);
			if (check) {
				if (targetConfig.status === 3 && this.dataPC.status === 2) {
					const checkStage = this.checkDataBeforeChangeToStage3();
					if (!checkStage) {
						this.openMessage(
							"Vui lòng bổ sung file kết quả sơ bộ và Tổng giá trị sơ bộ",
							"error"
						);
						this.returnData();
						return;
					}
				}
				this.next_status = targetConfig.status;
				this.dataPC.target_status = targetConfig.status;
				this.confirm_message = message;

				this.isMoved = check;
			} else {
				this.returnData();
				this.key_dragg++;
			}
		},
		checkDataBeforeChangeToStage3() {
			let resultDocumentsLength = 0;
			if (this.dataPC.other_documents) {
				let resultDocuments = this.dataPC.other_documents.filter(
					doc => doc.type_document === "Result"
				);
				resultDocumentsLength = resultDocuments.length;
			}
			if (
				this.dataPC.total_preliminary_value > 0 &&
				resultDocumentsLength > 0
			) {
				return true;
			} else {
				return false;
			}
		},
		openMessage(
			message,
			type = "error",
			position = "top-right",
			duration = 3000
		) {
			this.$toast.open({
				message: message,
				type: type,
				position: position,
				duration: duration
			});
		},
		checkRequired(require, data) {
			let check = true;
			if (require) {
				this.changeStatusRequire = require;
				this.isCheckPrice = require.check_price ? require.check_price : false;
				this.isCheckLegal = require.check_legal ? require.check_legal : false;
				this.isCheckVersion = require.check_version
					? require.check_version
					: false;
			}
			return check;
		},
		checkAppraiser(data) {
			if (
				data.appraiser_perform_id &&
				data.appraiser_sale_id &&
				data.business_manager_id
			) {
				return true;
			} else {
				return false;
			}
		},
		checkItemList(draggerElement) {
			if (
				draggerElement.personal_properties.length > 0 ||
				draggerElement.real_estate.length > 0
			) {
				return true;
			} else {
				return false;
			}
		},
		async handleRemoveStatusDraft(event, element) {
			this.status = 2;
			this.showAppraisalDialog = true;
		},
		async updateAppraisal(data, id, status_expired_at) {
			this.isAccept = true;
			this.listCertificateTemp.forEach(item => {
				if (item.id === id) {
					item.status = 2;
				}
			});
			// await this.updateDataWorkFlow()
		},
		handleCancelAppraisal() {
			this.showAppraisalDialog = false;
			if (!this.isAccept) {
				this.listCertificateDraft = this.listCertificateTemp.filter(
					item => item.status === 1
				);
				this.listCertificateOpen = this.listCertificateTemp.filter(
					item => item.status === 2
				);
			}
			this.isAccept = false;
		},
		handleMoveOpen(event) {
			if (
				this.user_id === event.draggedContext.element.appraiser_perform.user_id
			) {
				this.idDragger = event.draggedContext.element.id;
				this.elementDragger = event.draggedContext.element;
			} else return false;
		},
		// send accept
		handleRemoveStatusOpen(event) {
			this.status = 3;
			this.showVerifyCertificate = true;
		},
		async handleChangeVerify(data, id, status_expired_at) {
			this.isAccept = true;
			this.listCertificateTemp.forEach(item => {
				if (item.id === id) {
					item.status = 3;
				}
			});
			// await this.updateDataWorkFlow()
		},
		handleCancelVerify() {
			this.showVerifyCertificate = false;
			if (!this.isAccept) {
				this.listCertificateOpen = this.listCertificateTemp.filter(
					item => item.status === 2
				);
				this.listCertificateLock = this.listCertificateTemp.filter(
					item => item.status === 3
				);
				this.idUpdate = "";
			}
			this.isAccept = false;
		},
		// accpet the request
		handleMoveVerify(event) {
			if (this.appraiser_number) {
				this.idDragger = event.draggedContext.element.id;
				this.elementDragger = event.draggedContext.element;
			} else return false;
		},

		async handleChangeAccept2(note, reason_id) {
			if (this.dataPC.target_code == "chuyen_chinh_thuc") {
				this.updateToOffical(note);
				return;
			}
			const res = await this.preCertificateStore.updateStatus(
				this.idDragger,
				note,
				reason_id
			);

			if (res.data) {
				if (this.search_kanban) {
					await this.getDataWorkFlow2(
						true,
						this.search_kanban.search,
						isRefresh
					);
				} else await this.getDataWorkFlow2(true);
				await this.$toast.open({
					message:
						this.confirm_message == "Từ chối" ||
						this.confirm_message == "Hủy" ||
						this.confirm_message == "Khôi phục"
							? this.confirm_message + " thành công"
							: "Chuyển trạng thái " +
							  `'${this.confirm_message}'` +
							  " thành công",
					type: "success",
					position: "top-right",
					duration: 3000
				});
				// this.key_dragg++;
			} else {
				await this.$toast.open({
					message: `${res.error.message}`,
					type: "error",
					position: "top-right",
					duration: 3000
				});
				this.handleCancelAccept2();
			}
			this.isMoved = false;
			this.showDetailPopUp = false;
			this.isHandleAction = false;
		},
		async updateToOffical(note) {
			const res = await PreCertificate.updateToOfficalPreCertificate(
				this.dataPC.id,
				{ note }
			);
			if (res.data && res.data.error === false) {
				if (this.search_kanban) {
					await this.getDataWorkFlow2(
						true,
						this.search_kanban.search,
						isRefresh
					);
				} else await this.getDataWorkFlow2(true);
				await this.$toast.open({
					message: this.confirm_message + " thành công",
					type: "success",
					position: "top-right",
					duration: 3000
				});
				// this.key_dragg++;
			} else {
				await this.$toast.open({
					message: `${res.data.message}`,
					type: "error",
					position: "top-right",
					duration: 3000
				});
				this.handleCancelAccept2();
			}
			this.isMoved = false;
			this.showDetailPopUp = false;
			this.isHandleAction = false;
		},
		async handleUpdateStatus(id, data, message) {
			const res = await PreCertificate.updateStatusPreCertificate(id, data);
			if (res.data) {
				let returnData = this.subStatusDataReturn.find(i => i.id === id);
				if (returnData) {
					returnData.status = data.status;
					returnData.sub_status = data.sub_status;
					returnData.image = res.data.image;
				}
				this.returnData();
				await this.$toast.open({
					message: message + " thành công",
					type: "success",
					position: "top-right",
					duration: 3000
				});
				this.key_dragg++;
			} else {
				await this.$toast.open({
					message: `${res.error.message}`,
					type: "error",
					position: "top-right",
					duration: 3000
				});
				this.showDetailPopUp = false;
			}
			this.showDetailPopUp = false;
		},
		getExpireStatusDate() {
			let dateConvert = new Date();
			let minutes = this.config.process_time ? this.config.process_time : 1440;
			let dateConverted = new Date(dateConvert.getTime() + minutes * 60000);
			let status_expired_at = moment(dateConverted).format("DD-MM-YYYY HH:mm");
			return status_expired_at;
		},

		handleCancelAccept2() {
			this.isMoved = false;
			this.isHandleAction = false;
			this.showDetailPopUp = false;
			this.returnData();
		},
		returnData() {
			this.principleConfig.forEach(item => {
				this.subStatusData[item.id] = this.subStatusDataReturn.filter(
					i => i.status === item.status
				);
				this.subStatusDataTmp[item.id] = this.lstPreCertificateKanban.filter(
					i => i.status === item.status
				);
			});
			this.key_dragg++;
		},
		handleDetailCertificate(id) {
			this.$router
				.push({
					name: "certification_brief.detail",
					query: {
						id: id.toString()
					}
				})
				.catch(_ => {});
		},
		checkMoveVerify() {
			return true;
		},
		handleDetailPreCertificate(id) {
			this.idData = id;
			this.getDetailCertificate(id);
		},
		capitalizeFirstLetter(string) {
			return string.charAt(0).toUpperCase() + string.slice(1);
		},
		timeSince(date, now) {
			var minutes = Math.floor((now - new Date(date)) / 60000);
			var interval = minutes / 525600;
			if (minutes > 0) {
				if (interval > 1) {
					return Math.floor(interval) + " năm trước";
				}
				interval = minutes / 2592000;
				if (interval > 1) {
					return Math.floor(interval) + " Tháng trước";
				}
				interval = minutes / 86400;
				if (interval > 1) {
					return Math.floor(interval) + " Ngày trước";
				}
				interval = minutes / 3600;
				if (interval > 1) {
					return Math.floor(interval) + " giờ trước";
				}
				interval = minutes / 60;
				if (interval > 1) {
					return Math.floor(interval) + " phút trước";
				}
				return Math.floor(minutes) + " giây trước";
			} else {
				interval = Math.abs(interval);
				if (interval > 1) {
					return "Còn lại " + Math.floor(interval) + " năm";
				}
				interval = Math.abs(minutes / 2592000);
				if (interval > 1) {
					return "Còn lại " + Math.floor(interval) + " Tháng";
				}
				interval = Math.abs(minutes / 86400);
				if (interval > 1) {
					return "Còn lại " + Math.floor(interval) + " Ngày";
				}
				interval = Math.abs(minutes / 3600);
				if (interval > 1) {
					return "Còn lại " + Math.floor(interval) + " giờ";
				}
				interval = Math.abs(minutes / 60);
				if (interval > 1) {
					return "Còn lại " + Math.floor(interval) + " phút";
				}
				return "Còn lại " + Math.floor(minutes) + " giây";
			}
		},
		format(value) {
			let num = (value / 1).toFixed(0).replace(",", ".");
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
		},
		async getProfiles() {
			const profile = this.$store.getters.profile;
			if (profile && profile.data.user.roles[0].name.slice(-5) === "ADMIN") {
				this.activeStatus = true;
			}
		},
		async handleUpdateListKanBan(id, status) {
			this.showDetailPopUp = false;
			// await this.updateDataWorkFlow()
			this.listCertificateTemp.forEach(item => {
				if (item.id === id) {
					item.status = status;
				}
			});
			this.listCertificateDraft = this.listCertificateTemp.filter(
				item => item.status === 1
			);
			this.listCertificateOpen = this.listCertificateTemp.filter(
				item => item.status === 2
			);
			this.listCertificateOpen = this.listCertificateTemp.filter(
				item => item.status === 2
			);
			this.listCertificateLock = this.listCertificateTemp.filter(
				item => item.status === 3
			);
		},

		handleChange(value) {
			this.total_amount = value;
		},
		handleChangeWidth(value) {
			if (isNaN(value)) {
				this.width = "";
			} else this.width = value;
		},
		handleChangeCurrency(value) {
			this.currency = value;
		},
		loadMore() {
			setTimeout(e => {
				let count = this.countData;
				for (var i = count, j = count; i < j + 10; i++) {
					this.listCertificateDraftTemp[i] &&
						this.countData < this.listCertificateDraftTemp.length &&
						this.listCertificateDraft.push(this.listCertificateDraftTemp[i]) &&
						this.listCertificateTemp.push(this.listCertificateDraftTemp[i]);
					this.listCertificateOpenTemp[i] &&
						this.countData < this.listCertificateOpenTemp.length &&
						this.listCertificateOpen.push(this.listCertificateOpenTemp[i]) &&
						this.listCertificateTemp.push(this.listCertificateOpenTemp[i]);
					this.listCertificateLockTemp[i] &&
						this.countData < this.listCertificateLockTemp.length &&
						this.listCertificateLock.push(this.listCertificateLockTemp[i]) &&
						this.listCertificateTemp.push(this.listCertificateLockTemp[i]);
					this.listCertificatesCloseTemp[i] &&
						this.countData < this.listCertificatesCloseTemp.length &&
						this.listCertificatesClose.push(
							this.listCertificatesCloseTemp[i]
						) &&
						this.listCertificateTemp.push(this.listCertificatesCloseTemp[i]);
					this.listCertificatesCanceledTemp[i] &&
						this.countData < this.listCertificatesCanceledTemp.length &&
						this.listCertificatesCanceled.push(
							this.listCertificatesCanceledTemp[i]
						) &&
						this.listCertificateTemp.push(this.listCertificatesCanceledTemp[i]);
					this.countData++;
				}
			}, 200);
		},
		loadMore2() {
			setTimeout(e => {
				this.pushSubStatusData();
			}, 200);
		},

		getConfigByStatus(status) {
			return this.principleConfig.filter(
				item => item.status === status && item.isActive === 1
			);
		},
		async getDetailCertificate(id) {
			const temp = await this.preCertificateStore.getPreCertificate(id);
			if (temp) {
				this.detailData = await temp;
				this.showDetailPopUp = true;
				this.idDragger = id;
				this.dataPC = this.detailData;
			} else {
				await this.$toast.open({
					message: "Lấy dữ liệu thất bại",
					type: "error",
					position: "top-right"
				});
			}
		},
		handleFooterAccept(target) {
			if (target.code && target.code === "chuyen_chinh_thuc") {
				const checkStage = this.checkDataBeforeChangeToStage3();
				if (!checkStage) {
					this.openMessage(
						"Vui lòng bổ sung file kết quả sơ bộ và Tổng giá trị sơ bộ",
						"error"
					);
					return;
				}
				this.dataPC.target_code = target.code;
				this.confirm_message = target.description;
				this.isHandleAction = true;

				return;
			}
			let check = true;
			let config = this.principleConfig.find(i => i.id === target.id);
			this.elementDragger = this.detailData;
			if (config) {
				this.config = config;
				check = this.checkRequired(config.require, this.detailData);
			}
			if (check) {
				if (config.status === 3 && this.dataPC.status === 2) {
					const checkStage = this.checkDataBeforeChangeToStage3();
					if (!checkStage) {
						this.openMessage(
							"Vui lòng bổ sung file kết quả sơ bộ và Tổng giá trị sơ bộ",
							"error"
						);
						return;
					}
				}
				this.next_status = config.status;
				this.dataPC.target_status = config.status;
				this.dataPC.target_code = target.code;
				this.confirm_message = target.description;
				this.isHandleAction = check;
			}
		},
		handleFooterReject(status, subStatus, text) {
			this.next_status = status;
			this.confirm_message = text;
			this.isHandleAction = true;
		},
		changeHeight() {
			let maxHeight = 0;
			this.principleConfig.forEach(i => {
				let ElementHeight = document.getElementById(i.id).offsetHeight;
				if (maxHeight < ElementHeight) {
					maxHeight = ElementHeight;
				}
			});
			this.principleConfig.forEach(i => {
				document.getElementById(i.id).style.height = maxHeight + "px";
			});
		}
	},
	updated() {
		this.changeHeight();
	},
	async mounted() {
		this.preCertificateStore.updateRouteToast(this.$router, this.$toast);

		const listElm = document.querySelector("#infinite-list");
		listElm.addEventListener("scroll", e => {
			if (
				listElm.scrollTop + listElm.clientHeight >=
				listElm.scrollHeight - 5
			) {
				// this.loadMore()
				this.loadMore2();
			}
		});
	},
	beforeMount() {
		this.getProfiles();
	}
};
</script>

<style scoped lang="scss">
.scroll_board {
	// transform:rotateX(180deg);
	// -ms-transform:rotateX(180deg); /* IE 9 */
	// -webkit-transform:rotateX(180deg); /* Safari and Chrome */
	scroll-snap-align: start;
	overflow: auto;
	overflow-y: auto;
	overflow-x: auto;
	margin-bottom: 1px;
	max-height: 71vh !important;
	@media (max-height: 800px) and (min-height: 660px) {
		// M-MD Screen
		max-height: 75vh !important;
	}
	@media (max-height: 970px) and (min-height: 800px) {
		// FD Screen
		max-height: 78vh !important;
	}
	@media (min-height: 970px) {
		// >2k Screen
		max-height: 85vh !important;
	}
}
.name_card {
	text-align: left;
	width: 50%;
	white-space: nowrap;
	text-overflow: ellipsis;
	overflow: hidden;
}
.badge {
	border-radius: 10px;
	display: inline-block;
	text-transform: none;
	padding: 0.3rem 0.5rem;
	font-size: 85%;
	color: #fff;
	font-weight: 600;
	line-height: 1;
}
.badgeSuccess {
	background-color: rgba(40, 199, 111, 0.12);
	color: #28c76f !important;
}
.badgeWarning {
	background-color: rgba(255, 159, 67, 0.12);
	color: #ff9f43 !important;
}
.badgeDanger {
	background-color: rgba(234, 84, 85, 0.12);
	color: #ea5455 !important;
}
.badgeInfo {
	background-color: rgba(0, 207, 232, 0.12);
	color: #00cfe8 !important;
}
.badgePrimary {
	background-color: rgba(115, 103, 240, 0.12);
	color: #7367f0 !important;
}
.content_id {
	border-radius: 5px;
	padding: 0px 3px;
	font-weight: 500;
	cursor: pointer;
	&_primary {
		color: #007ec6;
		background-color: #e3f5ff;
	}
	&_secondary {
		color: #ffffff;
		background-color: #8b94a3;
	}
	&_warning {
		color: #ff963d;
		background-color: #fff1e6;
	}
	&_danger {
		color: #ff5e7b;
		background-color: #ffebef;
	}
	&_success {
		color: #ffffff;
		background-color: #26bf7f;
	}
}

.content_certificate_id {
	border-radius: 5px;
	padding: 0px 3px;
	font-weight: bold;
	color: #007ec6;
	cursor: pointer;
	border: 1px solid #a7d9fb;
}
.img_user {
	border-radius: 50%;
	height: 20px;
	width: 20px;
}
.appraise-container {
	padding: 0 1.25rem;
}
.kanban-column {
	min-height: 300px;
}
.height_icon {
	height: 1.3rem;
}
.card-body {
	padding: 0.75rem 0.75rem !important;
}
.card_container {
	border-radius: 5px;
	&_primary {
		border: 1px solid #b5e5ff;
	}
	&_secondary {
		border: 1px solid #8b94a3;
	}
	&_warning {
		border: 1px solid #ffd1ad;
	}
	&_danger {
		border: 1px solid #ffc8d3;
	}
	&_success {
		border: 1px solid #26bf7f;
		background-color: #eafff6;
	}
}
.container_kanban {
	height: fit-content;
	background-color: #f6f7fb;
	border-radius: 5px;
	border: 1px solid #e8e8e8;
	border-top: 4px solid;
	border-bottom: none;
	border-left: none;
	border-right: none;
	min-width: 17rem;
}
// border
.border {
	&_primary {
		color: #72cdff;
	}
	&_secondary {
		color: #9ea6b4;
	}
	&_danger {
		color: #ff7e9b;
	}
	&_warning {
		color: #ffb880;
	}
	&_success {
		color: #3ddc99;
	}
}
// title
.title {
	font-weight: 600;
	&_primary {
		color: #00507c;
	}
	&_secondary {
		color: #9ea6b4;
	}
	&_warning {
		color: #ffb880;
	}
	&_danger {
		color: #ff5e7b;
	}
	&_success {
		color: #3ddc99;
	}
}
//quatity
.quatity {
	min-width: 32px;
	height: 22px;
	padding: 0px 5px;
	align-items: center;
	text-align: center;
	border-radius: 5px;
	color: white;
	font-weight: 600;
	&_primary {
		background-color: #007ec6;
	}
	&_warning {
		background-color: #ff963d;
	}
	&_danger {
		background-color: #ff5e7b;
	}
	&_success {
		background-color: #26bf7f;
	}
	&_secondary {
		background-color: #8b94a3;
	}
}

.title_kanban {
	font-weight: 600;
}
.title_group {
	border: 1px solid #d9d9d9;
	border-radius: 5px;
	text-align: center;
}
.kanban_board {
	font-size: 0.875rem !important;
	min-width: 1200px;
}
.d_inline {
	@media (min-width: 1500px) {
		display: inline !important;
		min-width: 4.7rem;
	}
}
.label_container {
	@media (min-width: 1500px) {
		min-width: 120px;
	}
}
.icon_expired {
	margin-inline-end: 1rem;
	width: 1rem;
	justify-content: end;
}
.card-status-certificate {
	border-radius: 5px;
	padding: 0px;
	box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
	color: darkgray;
	cursor: pointer;
}
.container_card_success {
	background: white;
	margin-bottom: 1rem;
	.card {
		margin-bottom: unset !important;
	}
}
.border_expired {
	border-color: red !important;
}
</style>
