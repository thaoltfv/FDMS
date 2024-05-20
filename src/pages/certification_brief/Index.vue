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
								border_expired: checkDateExpired(element).statusExpire,
								['border-' + config.css.color]: true
							}"
							class="card_container mb-3"
							v-for="element in subStatusData[config.id]"
							:key="element.id + '_' + element.status"
						>
							<div class="col-12 d-flex mb-2 justify-content-between">
								<div class="ml-0">
									<span
										@click="handleDetailCertificate(element.id)"
										class="content_id"
										:class="
											`bg-${config.css.color}-15 text-${config.css.color}`
										"
										>{{ element.slug }}</span
									>
								</div>
								<div class="row">
									<div
										v-if="element.pre_certificate_id"
										@click="
											handleDetailPreCertificate(element.pre_certificate_id)
										"
										class="arrowBox arrow-right"
										:id="`${element.pre_certificate_id + element.id}`"
									>
										<icon-base
											name="nav_ycsb"
											class="item-icon svg-inline--fa"
										/>
										<b-tooltip
											:target="`${element.pre_certificate_id + element.id}`"
											placement="right"
											>{{
												`Được chuyển tiếp từ YCSB_${element.pre_certificate_id}`
											}}</b-tooltip
										>
									</div>
									<div>
										<img
											v-if="checkDateExpired(element).statusExpire"
											class="mr-2 icon_expired"
											src="@/assets/icons/ic_expire_calender.svg"
											alt="ic_expire_calender"
											hidden
										/>
									</div>
								</div>
							</div>
							<div class="property-content mb-2 d-flex color_content">
								<div class="label_container d-flex">
									<img
										style="min-width: 15px"
										width="15px"
										height="21px"
										class="mr-2"
										src="@/assets/icons/ic_user_2.svg"
										alt="user"
									/>
									<div class="d-flex">
										<span style="font-weight: 500">{{
											element.petitioner_name
										}}</span>
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
									<span style="font-weight: 500">{{
										element.total_price && isShowPrice(element)
											? `${formatPrice(element.total_price)}`
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
									<span
										v-if="getExpireDate(element).includes('Đã hết')"
										style="font-weight: 500; color: red"
									>
										{{ getExpireDate(element) }}
									</span>
									<span
										v-else
										style="font-weight: 500"
										:class="{
											'text-orange': checkDateExpired(element).inExpiringState
										}"
										>{{ getExpireDate(element) }}</span
									>
								</div>
							</div>
							<div class="property-content d-flex justify-content-between mb-0">
								<div class="label_container d-flex">
									<!-- <img
										width="15px"
										class="mr-2"
										src="@/assets/icons/ic_taglink.svg"
										alt="user"
									/><span style="color: #8b94a3">{{
										element.document_count
									}}</span> -->
									<span class="text-primary">{{ element.name_nv }}</span>
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
			<ModalDetailCertificate
				v-if="showDetailPopUp"
				:idData="idData"
				:edit="edit"
				:add="add"
				:user_id="user_id"
				:appraiser_number="appraiser_number"
				:jsonConfig="jsonConfig"
				:profile="profile"
				:data="detailData"
				@cancel="showDetailPopUp = false"
				@action="handleUpdateStatus"
				@handleFooterAccept="handleFooterAccept"
			/>
			<ModalAppraisal
				:key="key_render_appraisal"
				v-if="showAppraisalDialog"
				:data="elementDragger"
				:idData="idDragger"
				:status="status"
				requiredAppraiserPerform="required"
				:requiredAppraiser="null"
				@cancel="handleCancelAppraisal"
				@updateAppraisal="updateAppraisal"
			/>
			<ModalAppraisal
				:key="key_render_appraisal"
				v-if="showVerifyCertificate"
				:data="elementDragger"
				:idData="idDragger"
				:status="status"
				requiredAppraiserPerform="required"
				requiredAppraiser="required"
				@cancel="handleCancelVerify"
				@updateAppraisal="handleChangeVerify"
			/>
			<ModalNotificationWithAssignHSTD
				v-if="isMoved"
				:notification="
					confirm_message == 'Từ chối' ||
					confirm_message == 'Duyệt' ||
					confirm_message == 'Hủy'
						? `Bạn có muốn '${confirm_message}' hồ sơ này?`
						: `Bạn có muốn chuyển hồ sơ này sang trạng thái`
				"
				:status_text="confirm_message"
				:status_next="next_status"
				workflowName="hstdConfig"
				@action="handleChangeAccept2"
				:appraiser="appraiserChangeStage"
				:dataHSTD="detailData"
				@cancel="handleCancelAccept2"
			/>
			<ModalNotificationWithAssignHSTD
				v-if="isHandleAction"
				@cancel="isHandleAction = false"
				:notification="
					confirm_message == 'Từ chối' ||
					confirm_message == 'Duyệt' ||
					confirm_message == 'Hủy'
						? `Bạn có muốn '${confirm_message}' hồ sơ này?`
						: `Bạn có muốn chuyển hồ sơ này sang trạng thái`
				"
				:dataHSTD="detailData"
				workflowName="hstdConfig"
				:status_next="next_status"
				:status_text="confirm_message"
				:appraiser="appraiserChangeStage"
				@action="handleChangeAccept2"
				@status_expired_at=""
			/>
		</div>
	</div>
</template>

<script>
import { ref } from "vue";
import { storeToRefs } from "pinia";
import { useWorkFlowConfig } from "@/store/workFlowConfig";

import { PERMISSIONS } from "@/enum/permissions.enum";
import { FormWizard, TabContent } from "vue-form-wizard";
import ModalAppraisal from "./component/modals/ModalAppraisal";
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
import ModalDetailCertificate from "./component/modals/ModalDetailCertificate";
import ModalSendVerify from "@/components/Modal/ModalSendVerify";
import CertificationBrief from "@/models/CertificationBrief";
import moment from "moment";
import KanboardStatus from "./component/KanboardStatus.vue";
import ModalNotificationCertificate from "@/components/Modal/ModalNotificationCertificate";
import ModalNotificationWithAssignHSTD from "@/components/Modal/ModalNotificationWithAssignHSTD";
import IconBase from "@/components/IconBase.vue";

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
			listCertificate: [],
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
			countData: 0,
			isAccept: false,
			subStatusData: {},
			subStatusDataTmp: {},
			next_status: "",
			next_sub_status: "",
			confirm_message: "",
			isMoved: false,
			config: {},
			subStatusDataReturn: [],
			key_dragg: 1,
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
		ModalDetailCertificate,
		ModalSendVerify,
		ModalAppraisal,
		KanboardStatus,
		ModalNotificationCertificate,
		ModalNotificationWithAssignHSTD
	},
	setup() {
		const workFlowConfigStore = useWorkFlowConfig();
		const { configs } = storeToRefs(workFlowConfigStore);
		const jsonConfig = ref({});
		const principleConfig = ref([]);
		const startFunction = async () => {
			await workFlowConfigStore.getConfigByName("workflowHSTD");
			jsonConfig.value = configs.value.hstdConfig;
			if (jsonConfig.value && jsonConfig.value.principle) {
				principleConfig.value = jsonConfig.value.principle.filter(
					i => i.isActive === 1
				);
			}
		};
		const appraiserChangeStage = ref(null);
		return {
			appraiserChangeStage,
			jsonConfig,
			principleConfig,
			startFunction
		};
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

	methods: {
		isMobile() {
			if (
				/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
					navigator.userAgent
				)
			) {
				return true;
			} else {
				return false;
			}
		},
		getExpireDate(element) {
			// // console.log('elemt', element)
			let strExpire = "";
			switch (element.status) {
				case 1:
				case 2:
				case 3:
				case 7:
				case 8:
				case 9:
				case 10:
					strExpire = element.status_expired_at
						? this.updateDate(element.status_expired_at, new Date())
						: "Đã hết hạn";
					break;
				case 6:
					strExpire = element.status_expired_at
						? this.updateDate(element.status_expired_at, new Date())
						: "Đã hết hạn";
					break;
				case 4:
					strExpire = "Đã hoàn thành";
					break;
				default:
					strExpire = "Đã hủy";
			}
			return strExpire;
		},
		getNotificationMessage() {
			switch (this.next_status - 1) {
				case 1:
					return "Bạn có muốn chuyển hồ sơ này sang trạng thái 'Thẩm định' ?";
				case 2:
					return "Bạn có muốn chuyển hồ sơ này sang trạng thái 'Duyệt giá' ?";
				case 6:
					return "Bạn có muốn chuyển hồ sơ này sang trạng thái 'Duyệt phát hành' ?";
				case 7:
					return "Bạn có muốn chuyển hồ sơ này sang trạng thái 'In hồ sơ' ?";
				case 8:
					return "Bạn có muốn chuyển hồ sơ này sang trạng thái 'Bàn giao khách hàng' ?";
				case 3:
					return "Bạn có muốn chuyển hồ sơ này sang trạng thái 'Hoàn thành' ?";
				default:
					return "";
			}
		},
		checkDateExpired(element) {
			const check = {
				statusExpire: false,
				inExpiringState: false
			};

			if (element.status_expired_at) {
				const config = this.jsonConfig.principle.find(
					item => item.status === element.status && item.isActive === 1
				);
				if (config.expire_in) {
					const now = new Date();
					const futureTime = new Date(now.getTime() + config.expire_in * 60000); // config.expire_in is assumed to be in minutes

					if (futureTime >= new Date(element.status_expired_at)) {
						check.inExpiringState = true;
					}
				}
				if (
					this.updateDate(element.status_expired_at, this.now).includes(
						"Đã hết hạn"
					)
				) {
					check.statusExpire = true;
					check.inExpiringState = false;
				}
			}
			return check;
			// let check = false;
			// switch (element.status) {
			// 	case 1:
			// 	case 2:
			// 	case 3:
			// 		if (element.status_expired_at) {
			// 			if (
			// 				this.updateDate(element.status_expired_at, this.now).includes(
			// 					"Đã hết hạn"
			// 				)
			// 			) {
			// 				check = true;
			// 			}
			// 		} else {
			// 			check = true;
			// 		}
			// 		break;
			// }
			// return check;
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
			this.config = config;
			return check;
		},
		changedDraggable(evt) {
			this.appraiserChangeStage = null;
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
				if (targetConfig.re_assign)
					this.appraiserChangeStage = {
						id: this.elementDragger[targetConfig.re_assign],
						type: targetConfig.re_assign
					};
				this.isMoved = check;
				this.next_status = targetConfig.status;
				this.next_sub_status = targetConfig.sub_status;
				this.confirm_message = message;
			} else {
				this.returnData();
				this.key_dragg++;
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
				if (require.appraiser) {
					check = this.checkAppraiser(data);
					if (!check) {
						this.openMessage("Chưa có thông tin tổ thẩm định");
					}
				}
				if (check && require.appraise_item_list) {
					check = this.checkItemList(data);
					if (!check) {
						this.openMessage("Chưa có chi tiết tài sản thẩm định");
					}
				}
			}
			return check;
		},
		checkAppraiser(data) {
			if (data.appraiser_perform_id && data.appraiser_id) {
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

		async handleChangeAccept2(note, reason_id, tempAppraiser, estime) {
			const config = this.jsonConfig.principle.find(
				item => item.status === this.next_status && item.isActive === 1
			);
			// let status_expired_at_temp = config.process_time
			// 	? await this.getExpireStatusDate(config)
			// 	: null;
			let status_expired_at_temp = estime;
			let dataSend = {
				appraiser_confirm_id: this.elementDragger.appraiser_confirm_id,
				appraiser_id: this.elementDragger.appraiser_id,
				appraiser_manager_id: this.elementDragger.appraiser_manager_id,
				appraiser_control_id: this.elementDragger.appraiser_control_id,
				appraiser_perform_id: this.elementDragger.appraiser_perform_id,
				administrative_id: this.elementDragger.administrative_id,
				status: this.next_status,
				sub_status: this.next_sub_status,
				check_price: this.isCheckPrice,
				check_legal: this.isCheckLegal,
				check_version: this.isCheckVersion,
				required: this.changeStatusRequire,
				status_expired_at: status_expired_at_temp,
				status_note: note,
				status_reason_id: reason_id,
				status_description: this.message,
				status_config: this.jsonConfig.principle
			};
			if (tempAppraiser) {
				dataSend[tempAppraiser.type] = tempAppraiser.id;
			}
			// console.log('data send', dataSend)
			const res = await CertificationBrief.updateStatusCertificate(
				this.idDragger,
				dataSend
			);
			if (res.data) {
				let returnData = this.subStatusDataReturn.find(
					i => i.id === this.idDragger
				);
				if (returnData) {
					returnData.status = this.next_status;
					returnData.sub_status = this.next_sub_status;
					returnData.status_expired_at = res.data.status_expired_at;
					returnData.updated_at = res.data.updated_at;
					returnData.image = res.data.image;
				}
				this.returnData();
				await this.$toast.open({
					message:
						this.confirm_message == "Từ chối" ||
						this.confirm_message == "Hủy" ||
						this.confirm_message == "Khôi phục"
							? this.confirm_message + " thành công"
							: "Chuyển trạng thái " +
							  `"${this.confirm_message}"` +
							  " thành công",
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
				this.handleCancelAccept2();
			}
			this.isMoved = false;
			this.showDetailPopUp = false;
			this.isHandleAction = false;
		},
		async handleUpdateStatus(id, data, message) {
			const res = await CertificationBrief.updateStatusCertificate(id, data);
			if (res.data) {
				let returnData = this.subStatusDataReturn.find(i => i.id === id);
				if (returnData) {
					returnData.status = data.status;
					returnData.sub_status = data.sub_status;
					returnData.image = res.data.image;
				}
				this.returnData();
				await this.$toast.open({
					message:
						message == "Từ chối" || message == "Hủy" || message == "Khôi phục"
							? message + " thành công"
							: "Chuyển trạng thái " + `"${message}"` + " thành công",
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
		getExpireStatusDate(config) {
			const configTemp = config ? config : this.config;
			let dateConvert = new Date();
			let minutes = configTemp.process_time ? configTemp.process_time : 1440;
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
					i => i.status === item.status && i.sub_status === item.sub_status
				);
				this.subStatusDataTmp[item.id] = this.listCertificate.filter(
					i => i.status === item.status && i.sub_status === item.sub_status
				);
			});
			this.key_dragg++;
		},
		checkMoveVerify() {
			return true;
		},
		handleDetailPreCertificate(id) {
			let url = this.$router.resolve({
				name: "pre_certification.detail",
				query: {
					id: id.toString()
				}
			}).href;

			window.open(url, "_blank");
		},
		handleDetailCertificate(id) {
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
			if (
				profile.data.user.roles[0].name === "ADMIN" ||
				profile.data.user.roles[0].name === "ROOT_ADMIN" ||
				profile.data.user.roles[0].name === "SUB_ADMIN"
			) {
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
		async updateDataWorkFlow(search) {
			this.isLoading = true;
			try {
				const resp = await CertificationBrief.getListKanbanCertificate(search);
				if (resp.data) {
					this.listCertificate = resp.data.HSTD;
					this.listCertificateDraftTemp = resp.data.HSTD.filter(
						item => item.status === 1
					);
					this.listCertificateOpenTemp = resp.data.HSTD.filter(
						item => item.status === 2
					);
					this.listCertificateLockTemp = resp.data.HSTD.filter(
						item => item.status === 3
					);
					this.listCertificatesCloseTemp = resp.data.HSTD.filter(
						item => item.status === 4
					);
					this.listCertificatesCanceledTemp = resp.data.HSTD.filter(
						item => item.status === 5
					);
					this.listCertificateDraft = [];
					this.listCertificateOpen = [];
					this.listCertificateLock = [];
					this.listCertificatesClose = [];
					this.listCertificatesCanceled = [];
					this.listCertificateTemp = [];
					let count = (this.countData = 0);
					for (var i = count, j = count; i < j + 10; i++) {
						this.listCertificateDraftTemp[i] &&
							this.countData < this.listCertificateDraftTemp.length &&
							this.listCertificateDraft.push(
								this.listCertificateDraftTemp[i]
							) &&
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
							this.listCertificateTemp.push(
								this.listCertificatesCanceledTemp[i]
							);
						this.countData++;
					}
				}
			} catch (e) {
				this.isLoading = false;
			}
		},
		async getDataWorkFlow(search = "") {
			this.isLoading = true;
			try {
				const resp = await CertificationBrief.getListKanbanCertificate(
					search,
					this.search_kanban ? this.search_kanban.status : null
				);
				if (resp.data) {
					this.listCertificate = resp.data.HSTD;
					this.listCertificateDraftTemp = resp.data.HSTD.filter(
						item => item.status === 1
					);
					this.listCertificateOpenTemp = resp.data.HSTD.filter(
						item => item.status === 2
					);
					this.listCertificateLockTemp = resp.data.HSTD.filter(
						item => item.status === 3
					);
					this.listCertificatesCloseTemp = resp.data.HSTD.filter(
						item => item.status === 4
					);
					this.listCertificatesCanceledTemp = resp.data.HSTD.filter(
						item => item.status === 5
					);
					this.listCertificateDraft = [];
					this.listCertificateOpen = [];
					this.listCertificateLock = [];
					this.listCertificatesClose = [];
					this.listCertificatesCanceled = [];
					this.listCertificateTemp = [];
					let count = this.countData;
					for (var i = count, j = count; i < j + 10; i++) {
						this.listCertificateDraftTemp[i] &&
							this.countData < this.listCertificateDraftTemp.length &&
							this.listCertificateDraft.push(
								this.listCertificateDraftTemp[i]
							) &&
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
							this.listCertificateTemp.push(
								this.listCertificatesCanceledTemp[i]
							);
						this.countData++;
					}
				}
			} catch (e) {
				this.isLoading = false;
			}
		},
		async getDataWorkFlow2(search = "") {
			this.isLoading = true;
			try {
				const resp = await CertificationBrief.getListKanbanCertificate(
					search,
					this.search_kanban ? this.search_kanban.status : null
				);
				if (resp.data) {
					this.listCertificate = resp.data.HSTD;
					if (this.principleConfig.length > 0) {
						let dataTmp = [];
						this.principleConfig.forEach(item => {
							dataTmp = this.listCertificate.filter(
								i =>
									i.status === item.status && i.sub_status === item.sub_status
							);
							this.subStatusDataTmp[item.id] = dataTmp;
						});
						this.pushSubStatusData();
					}
				}
			} catch (e) {
				this.isLoading = false;
			}
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
		pushSubStatusData() {
			let count = this.countData ? this.countData : 0;
			let tmp = this.subStatusDataTmp;
			let config = this.principleConfig;
			let data = [];
			let dataReturn = this.subStatusDataReturn;
			config.forEach(item => {
				data = this.subStatusData[item.id] ? this.subStatusData[item.id] : [];
				if (tmp[item.id] && tmp[item.id].length > count) {
					for (var i = count; i < count + 10; i++) {
						tmp[item.id][i] &&
							data.push(tmp[item.id][i]) &&
							dataReturn.push(tmp[item.id][i]);
					}
				}
				// data.sort(function (a, b) {
				// if (a.updated_at > b.updated_at) { return a }
				// })
				this.subStatusData[item.id] = data;
			});

			this.key_dragg++;
			this.countData += 10;
		},
		getConfigByStatus(status, sub_status) {
			return this.principleConfig.filter(
				item =>
					item.status === status &&
					item.sub_status === sub_status &&
					item.isActive === 1
			);
		},
		async getDetailCertificate(id) {
			const res = await CertificationBrief.getDetailCertificateBrief(id);
			if (res.data) {
				this.detailData = await res.data;

				if (
					this.detailData.status &&
					(this.detailData.status == 2 ||
						this.detailData.status == 10 ||
						this.detailData.status == 3 ||
						this.detailData.status == 7) &&
					this.detailData.appraiser_sale &&
					this.detailData.appraiser_sale.user_id === this.user_id &&
					!this.checkExistInAppraisalTeam()
				) {
					this.$toast.open({
						message:
							"Nhân viên kinh doanh không có quyền xem chi tiết hồ sơ này ở bước này, vui lòng liên hệ admin",
						type: "error",
						position: "top-right"
					});
					return;
				}
				this.showDetailPopUp = true;
				this.idDragger = id;
			} else {
				await this.$toast.open({
					message: "Lấy dữ liệu thất bại",
					type: "error",
					position: "top-right"
				});
			}
		},
		checkExistInAppraisalTeam() {
			let check = false;
			if (this.user_id) {
				if (
					this.detailData.administrative &&
					this.detailData.administrative.user_id &&
					this.detailData.administrative.user_id === this.user_id
				) {
					check = true;
				} else if (
					this.detailData.appraiser &&
					this.detailData.appraiser.user_id &&
					this.detailData.appraiser.user_id === this.user_id
				) {
					check = true;
				} else if (
					this.detailData.appraiser_business_manager &&
					this.detailData.appraiser_business_manager.user_id &&
					this.detailData.appraiser_business_manager.user_id === this.user_id
				) {
					check = true;
				} else if (
					this.detailData.appraiser_confirm &&
					this.detailData.appraiser_confirm.user_id &&
					this.detailData.appraiser_confirm.user_id === this.user_id
				) {
					check = true;
				} else if (
					this.detailData.appraiser_control &&
					this.detailData.appraiser_control.user_id &&
					this.detailData.appraiser_control.user_id === this.user_id
				) {
					check = true;
				} else if (
					this.detailData.appraiser_manager &&
					this.detailData.appraiser_manager.user_id &&
					this.detailData.appraiser_manager.user_id === this.user_id
				) {
					check = true;
				} else if (
					this.detailData.appraiser_perform &&
					this.detailData.appraiser_perform.user_id &&
					this.detailData.appraiser_perform.user_id === this.user_id
				) {
					check = true;
				}
			}

			return check;
		},
		handleFooterAccept(target) {
			this.appraiserChangeStage = null;
			let check = true;
			let config = this.principleConfig.find(i => i.id === target.id);
			this.elementDragger = this.detailData;
			if (target.description.toUpperCase() === "HOÀN THÀNH") {
				if (this.detailData.payments && this.detailData.payments.length === 0) {
					this.$toast.open({
						message:
							"Vui lòng thanh toán hết dư nợ để chuyển sang trạng thái hoàn thành !",
						type: "error",
						position: "top-right",
						duration: 3000
					});

					return;
				} else if (
					this.detailData.payments &&
					this.detailData.payments.length > 0 &&
					this.detailData.payments[0].id
				) {
					let debt_remain = this.detailData.service_fee
						? Number(this.detailData.service_fee)
						: Number(this.detailData.total_service_fee);
					let amount_paid = 0;
					for (
						let index = 0;
						index < this.detailData.payments.length;
						index++
					) {
						const element = this.detailData.payments[index];
						if (element.amount && element.amount > 0) {
							amount_paid += parseFloat(element.amount);
						}
					}
					if (debt_remain - amount_paid > 0) {
						this.$toast.open({
							message:
								"Vui lòng thanh toán hết dư nợ  để chuyển sang trạng thái hoàn thành !",
							type: "error",
							position: "top-right",
							duration: 3000
						});
						return;
					}
				}
			}
			if (config) {
				this.config = config;
				check = this.checkRequired(config.require, this.detailData);
			}
			// // console.log(check)
			if (check) {
				if (config.re_assign)
					this.appraiserChangeStage = {
						id: this.elementDragger[config.re_assign],
						type: config.re_assign
					};
				this.next_status = config.status;
				this.next_sub_status = config.sub_status;
				this.confirm_message = target.description;
				this.isHandleAction = true;
			}
		},
		handleFooterReject(status, subStatus, text) {
			this.next_status = status;
			this.next_sub_status = subStatus;
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
		},
		isShowPrice(property) {
			if (this.activeStatus) return true;

			if (
				property.status &&
				(property.status == 1 ||
					property.status == 2 ||
					property.status == 3 ||
					property.status == 10) &&
				property.appraiser_sale &&
				property.appraiser_sale.user_id === this.user_id &&
				!this.checkExistInAppraisalTeam2(property)
			) {
				return false;
			}
			return true;
		},
		checkExistInAppraisalTeam2(property) {
			let check = false;
			if (this.user_id) {
				if (
					property.administrative &&
					property.administrative.user_id &&
					property.administrative.user_id === this.user_id
				) {
					check = true;
				} else if (
					property.appraiser &&
					property.appraiser.user_id &&
					property.appraiser.user_id === this.user_id
				) {
					check = true;
				} else if (
					property.appraiser_business_manager &&
					property.appraiser_business_manager.user_id &&
					property.appraiser_business_manager.user_id === this.user_id
				) {
					check = true;
				} else if (
					property.appraiser_confirm &&
					property.appraiser_confirm.user_id &&
					property.appraiser_confirm.user_id === this.user_id
				) {
					check = true;
				} else if (
					property.appraiser_control &&
					property.appraiser_control.user_id &&
					property.appraiser_control.user_id === this.user_id
				) {
					check = true;
				} else if (
					property.appraiser_manager &&
					property.appraiser_manager.user_id &&
					property.appraiser_manager.user_id === this.user_id
				) {
					check = true;
				} else if (
					property.appraiser_perform &&
					property.appraiser_perform.user_id &&
					property.appraiser_perform.user_id === this.user_id
				) {
					check = true;
				}
			}

			return check;
		}
	},
	updated() {
		this.changeHeight();
	},
	mounted() {
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
	async beforeMount() {
		await this.startFunction();
		if (this.search_kanban) {
			this.getDataWorkFlow2(this.search_kanban.search);
		} else this.getDataWorkFlow2();
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
.card-status-certificate {
	border-radius: 5px;
	padding: 0px;
	box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
	color: darkgray;
	cursor: pointer;
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

.arrowBox {
	position: relative;
	background: #007ec6;
	height: 22px;
	line-height: 22px;
	text-align: center;
	color: #fff;
	font-weight: 600;
	font-size: 16px !important;
	display: inline-block;
	cursor: pointer;
	padding: 0 2px 0 0;
	margin-right: -5px;
	margin-top: 1px;
}
.arrow-right:after {
	content: "";
	position: absolute;
	left: -11px;
	top: 0;
	border-top: 11px solid transparent;
	border-bottom: 11px solid transparent;
	border-right: 11px solid #007ec6;
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
.container_card_success {
	background: white;
	margin-bottom: 1rem;
	.card {
		margin-bottom: unset !important;
	}
}
.border_expired {
	// border-color: red !important;
}
</style>
