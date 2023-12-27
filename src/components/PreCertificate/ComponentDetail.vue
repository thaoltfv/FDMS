<template>
	<div
		class="detail_pre_certification row"
		:style="isMobile ? { margin: '0' } : {}"
	>
		<div class="col-12" :style="isMobile ? { padding: '0' } : {}">
			<div class="card">
				<div class="card-title">
					<div class="d-flex justify-content-between align-items-center">
						<h3 class="title">Thông tin chung</h3>
						<div class=" color_content card-status">
							{{ dataPC.id ? `HSTDSB_${dataPC.id}` : "HSTDSB" }} |
							<span v-if="dataPC.status === 1">Yêu cầu sơ bộ</span>
							<span v-if="dataPC.status === 2">Định giá sơ bộ</span>
							<span v-if="dataPC.status === 3">Duyệt giá sơ bộ</span>
							<span v-if="dataPC.status === 4">Thương thảo</span>
							<span v-if="dataPC.status === 5">Hoàn thành</span>
							<span v-if="dataPC.status === 6">Hủy</span>
						</div>
					</div>
				</div>
				<div class="card-body card-info">
					<div class="row justify-content-between">
						<div class="col-md-12 col-lg-6 mt-1">
							<div class="detail_certificate_1 h-100">
								<div class="d-flex container_content justify-content-between">
									<div class="d-flex container_content">
										<strong class="margin_content_inline">Khách hàng:</strong>
										<p>{{ dataPC.petitioner_name }}</p>
									</div>
									<div
										v-if="editInfo && edit"
										@click="handleShowAppraiseInformation"
										class="btn-edit "
									>
										<img src="@/assets/icons/ic_edit_3.svg" alt="add" />
									</div>
								</div>
								<div class="row d-flex container_content">
									<strong class="margin_content_inline"
										>MST/CMND/CCCD/Passport:</strong
									>
									<p>{{ dataPC.petitioner_identity_card }}</p>
									<strong class="margin_content_inline">Điện thoại:</strong>
									<p>{{ dataPC.petitioner_phone }}</p>
								</div>
								<!-- <div class="d-flex container_content">
											<strong class="margin_content_inline">Điện thoại:</strong> <p>{{dataPC.petitioner_phone}}</p>
										</div> -->
								<div class="d-flex container_content">
									<strong class="margin_content_inline">Địa chỉ:</strong>
									<p>{{ dataPC.petitioner_address }}</p>
								</div>
								<div class="d-flex container_content">
									<strong class="margin_content_inline">Mục đích thẩm định:</strong
									><span id="appraise_purpose" class="text-left">{{
											 dataPC.appraise_purpose && dataPC.appraise_purpose.name.length > 25
											? dataPC.appraise_purpose.name.substring(60, 0) + "..."
											: dataPC.appraise_purpose
									}}</span>
									<b-tooltip target="appraise_purpose" placement="top-right">{{
										dataPC.appraise_purpose.name
									}}</b-tooltip>
								</div>
								<!-- <div class="d-flex container_content">
									<strong class="margin_content_inline"
										>Mục đích thẩm định:
									</strong>
									{{
										dataPC.appraise_purpose ? dataPC.appraise_purpose.name : ""
									}}
								</div> -->

								<div class="d-flex container_content">
									<strong class="margin_content_inline">Loại sơ bộ:</strong>
									<p>
										{{ dataPC.pre_type ? dataPC.pre_type : "" }}
									</p>
								</div>

								<div class="d-flex container_content">
									<strong class="margin_content_inline"
										>Tổng giá trị sơ bộ:</strong
									>
									<p>
										{{
											dataPC.total_preliminary_value
												? formatNumber(dataPC.total_preliminary_value)
												: 0
										}}đ
									</p>
								</div>

								<div class="d-flex container_content">
									<strong class="margin_content_inline">Ghi chú:</strong
									><span id="note" class="text-left">{{
										dataPC.note && dataPC.note.length > 25
											? dataPC.note.substring(25, 0) + "..."
											: dataPC.note
									}}</span>
									<b-tooltip target="note" placement="top-right">{{
										dataPC.note
									}}</b-tooltip>
								</div>
							</div>
						</div>
						<div class="col-md-12 col-lg-6 mt-1">
							<div class="row">
								<div class="col-12">
									<div class="detail_certificate_2">
										<div class="d-flex container_content">
											<strong class="margin_content_inline">Đối tác:</strong>
											<p>{{ dataPC.customer ? dataPC.customer.name : "" }}</p>
										</div>
										<div class="d-flex container_content">
											<strong class="margin_content_inline">Địa chỉ:</strong>
											<p>
												{{ dataPC.customer ? dataPC.customer.address : "" }}
											</p>
										</div>
										<div class="d-flex container_content">
											<strong class="margin_content_inline">Liên hệ:</strong>
											<p>{{ dataPC.customer ? dataPC.customer.phone : "" }}</p>
										</div>
									</div>
								</div>
								<div class="col-12 mt-1 mt-lg-4">
									<div class="detail_certificate_2">
										<div
											class="d-flex container_content justify-content-between"
										>
											<div class="d-flex">
												<strong class="margin_content_inline"
													>Nhân viên kinh doanh:</strong
												>
												<p>
													{{
														dataPC.appraiser_sale
															? dataPC.appraiser_sale.name
															: ""
													}}
												</p>
											</div>
											<div
												v-if="editAppraiser && edit"
												@click="handleShowAppraisal"
												class="btn-edit "
											>
												<img src="@/assets/icons/ic_edit_3.svg" alt="add" />
											</div>
										</div>
										<div class="d-flex container_content">
											<strong class="margin_content_inline"
												>Chuyên viên thực hiện:</strong
											>
											<p>
												{{
													dataPC.appraiser_perform
														? dataPC.appraiser_perform.name
														: ""
												}}
											</p>
										</div>
										<div class="d-flex container_content">
											<strong class="margin_content_inline"
												>Quản lý nghiệp vụ:</strong
											>
											<p>
												{{
													dataPC.appraiser_business_manager
														? dataPC.appraiser_business_manager.name
														: ""
												}}
											</p>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="btn-history">
			<button class="btn btn-orange btn-history" @click="showDrawer">
				<img src="@/assets/icons/ic_log_history.svg" alt="history" />
			</button>
		</div>
		<a-drawer
			v-if="!isMobile"
			width="400"
			title="Lịch sử hoạt động"
			placement="right"
			:visible="visible"
			@close="onClose"
		>
			<a-timeline>
				<a-timeline-item
					v-for="(item, index) in historyList"
					:key="index"
					color="red"
				>
					<template #dot>
						<img
							class="dot-image"
							:src="
								item.causer && item.causer.image
									? item.causer.image
									: 'https://upload.wikimedia.org/wikipedia/commons/b/bc/Unknown_person.jpg'
							"
							style="width: 2em"
						/>
					</template>
					<p>
						<strong>{{
							item.causer && item.causer.name
								? item.causer.name
								: "Không xác	định"
						}}</strong>
					</p>
					<p>{{ item.description }}</p>
					<p
						:class="`${getHistoryTextColor[index]}`"
						v-if="item.properties.reason_id && item.reason_description"
					>
						Lí do : {{ item.reason_description }}
					</p>
					<p
						:class="`${getHistoryTextColor[index]}`"
						v-if="item.properties.note"
					>
						Ghi chú : {{ item.properties.note }}
					</p>
					<p>{{ formatDateTime(item.updated_at) }}</p>
				</a-timeline-item>
			</a-timeline>
		</a-drawer>
		<a-drawer
			v-else
			width="100%"
			title="Lịch sử hoạt động"
			placement="right"
			:visible="visible"
			@close="onClose"
		>
			<a-timeline style="padding-bottom: 10px;">
				<a-timeline-item
					v-for="(item, index) in historyList"
					:key="index"
					color="red"
				>
					<template #dot>
						<img
							class="dot-image"
							:src="
								item.causer && item.causer.image
									? item.causer.image
									: 'https://upload.wikimedia.org/wikipedia/commons/b/bc/Unknown_person.jpg'
							"
							style="width: 2em"
						/>
					</template>
					<p>
						<strong>{{
							item.causer && item.causer.name
								? item.causer.name
								: "Không xác	định"
						}}</strong>
					</p>
					<p>{{ item.description }}</p>
					<p
						:class="`${getHistoryTextColor[index]}`"
						v-if="item.properties.reason_id && item.reason_description"
					>
						Lí do : {{ item.reason_description }}
					</p>
					<p
						:class="`${getHistoryTextColor[index]}`"
						v-if="item.properties.note"
					>
						Ghi chú : {{ item.properties.note }}
					</p>
					<p>{{ formatDateTime(item.updated_at) }}</p>
				</a-timeline-item>
			</a-timeline>
		</a-drawer>
		<div
			v-if="dataPC.id"
			class="col-6"
			:style="isMobile ? { padding: '0' } : {}"
		>
			<OtherFile :type="'Appendix'" :from="'Detail'" />
		</div>
		<div
			v-if="dataPC.id && dataPC.status >= 2"
			class="col-6"
			:style="isMobile ? { padding: '0' } : {}"
		>
			<div class="card">
				<div class="card-title">
					<div class="d-flex justify-content-between align-items-center">
						<div class="row d-flex justify-content-between align-items-center">
							<h3 class="title">Kết quả sơ bộ</h3>
						</div>

						<img
							class="img-dropdown"
							:class="!showCardDetailFileResult ? 'img-dropdown__hide' : ''"
							src="@/assets/images/icon-btn-down.svg"
							alt="dropdown"
							@click="showCardDetailFileResult = !showCardDetailFileResult"
						/>
					</div>
				</div>
				<OtherFile
					v-show="showCardDetailFileResult"
					type="Result"
					@action="showCardDetailFileResult = true"
				/>
			</div>
		</div>
		<Footer
			v-if="jsonConfig && dataPC"
			:style="isMobile ? { bottom: '60px' } : {}"
			:key="dataPC.status"
			:form="dataPC"
			:jsonConfig="jsonConfig"
			:status="dataPC.status"
			:profile="profile"
			:idData="dataPC.id"
			:checkVersion="checkVersion"
			@handleFooterAccept="handleFooterAccept"
			@handleEdit="handleEdit"
			@handleCancelCertificate="handleCancelCertificate"
			@onCancel="onCancel"
			@viewDetailAppraise="viewDetailAppraise"
			@viewAppraiseListVersion="viewAppraiseListVersion"
		/>
		<!--
		<div class="btn-footer d-md-flex d-block justify-content-end align-items-center">
			<button v-if="dataPC.status === 3 && dataPC.appraises.length > 0 ? true : false " class="btn btn-white" @click.prevent="viewDetailAppraise">
				<img class="img" src="@/assets/icons/ic_done-orange.svg" alt="edit">
				Cross check
			</button>
			<button v-if="dataPC.status === 3 && dataPC.appraiser && accept && (appraiser_number && user_id === dataPC.appraiser.user_id ) ? true : false "  class="btn btn-white btn-orange" @click.prevent="handleAccept">
					<img class="img" src="@/assets/icons/ic_done.svg" alt="edit">
					Đồng ý phê duyệt
			</button>

			<button v-if="dataPC.status === 3 && dataPC.appraiser && accept && (appraiser_number && user_id === dataPC.appraiser.user_id) ? true : false " class="btn btn-white" @click.prevent="handleDenined">
					<img class="img" src="@/assets/icons/ic_cancel-1.svg" alt="edit">
					Từ chối phê duyệt
			</button>

			<button v-if="dataPC.status === 2 && dataPC.appraiser_perform && (user_id === dataPC.appraiser_perform.user_id) ? true : false " class="btn btn-white btn-orange" @click.prevent="handleSendRequire">
					<img class="img" src="@/assets/icons/ic_done.svg" alt="edit">
					Gửi phê duyệt
			</button>

			<button v-if="dataPC.status === 1 && dataPC.appraiser_sale && (user_id === dataPC.appraiser_sale.user_id || checkRole) ? true : false " class="btn btn-white btn-orange" @click.prevent="handleSendAppraiser">
					<img class="img" src="@/assets/icons/ic_done.svg" alt="edit">
					Gửi thẩm định
			</button>

			<button v-if="dataPC.status === 1 && dataPC.appraiser_sale && (user_id === dataPC.appraiser_sale.user_id || checkRole) ? true : false " class="btn btn-white" @click.prevent="handleEdit(dataPC.id)">
				<img class="img" src="@/assets/icons/ic_edit.svg" alt="edit">
				Chỉnh sửa
			</button>
			<b-button-group  class="btn_group" v-if="(dataPC.status === 1 || dataPC.status === 2)">
					<button style="margin-right: 2px" class="btn btn-white" @click="onCancel" type="button">
						<img class="img" src="@/assets/icons/ic_cancel.svg" alt="cancel">Trở về
					</button>
					<b-dropdown class="btn_dropdown" right dropup>
						<b-dropdown-item @click.prevent="handleCancelCertificate">
							<div class="div_item_dropdown">
								<img style="height: 20px" class="img" src="@/assets/icons/ic_destroy.svg" alt="edit">
									Hủy hồ sơ
							</div>
						</b-dropdown-item>
					</b-dropdown>
			</b-button-group>
				//// trở về
				<button v-if="(dataPC.status !== 1 && dataPC.status !== 2)" class="btn btn-white" @click="onCancel" type="button">
					<img class="img" src="@/assets/icons/ic_cancel.svg" alt="cancel">Trở về
				</button>
		</div> -->

		<!-- <ModalCustomer v-if="showCustomerDialog"/> -->
		<ModalAppraisal
			:key="key_render_appraisal"
			v-if="showAppraisalDialog"
			:data="dataPC"
			:idData="dataPC.id"
			:status="status"
			:requiredAppraiserPerform="null"
			:requiredAppraiser="null"
			:ModalEdit="true"
			@cancel="showAppraisalDialog = false"
			@updateAppraisal="updateAppraisal"
		/>

		<ModalAppraiseInfomation
			v-if="showAppraiseInformationDialog"
			:data="dataPC"
			:idData="dataPC.id"
			:typeAppraiseProperty="typeAppraiseProperty"
			@cancel="showAppraiseInformationDialog = false"
			@updateAppraiseInformation="updateAppraiseInformation"
		/>
		<ModalNotificationCertificateNote
			v-if="openNotification"
			@cancel="handleCancel"
			v-bind:notification="message"
			@action="handleAction"
		/>
		<ModalNotificationCertificate
			v-if="openNotificationDenined"
			@cancel="openNotificationDenined = false"
			v-bind:notification="message"
			@action="handleActionDenined"
		/>
		<ModalViewDocument
			v-if="isShowPrint"
			@cancel="isShowPrint = false"
			:filePrint="filePrint"
			:title="title"
		/>
		<ModalDelete
			v-if="openModalDelete"
			@cancel="openModalDelete = false"
			@action="handleDelete"
		/>
		<ModalAppraisal
			:key="key_render_appraisal"
			v-if="openSendRequire"
			:data="dataPC"
			:idData="dataPC.id"
			:status="3"
			:requiredAppraiserPerform="null"
			:requiredAppraiser="null"
			@cancel="openSendRequire = false"
			@updateAppraisal="updateSendRequired"
		/>
		<ModalAppraisal
			:key="key_render_appraisal"
			v-if="openSendAppraiser"
			:data="dataPC"
			:idData="dataPC.id"
			:status="2"
			:requiredAppraiserPerform="null"
			:requiredAppraiser="null"
			@cancel="openSendAppraiser = false"
			@updateAppraisal="updateSendAppraiser"
		/>
		<!-- <ModalNotificationCertificate
			v-if="isHandleAction"
			@cancel="isHandleAction = false"
			:notification="`Bạn có muốn '${message}' hồ sơ này?`"
			@action="handleAction2"
		/> -->
		<ModalNotificationCertificateNote
			v-if="isHandleAction"
			@cancel="isHandleAction = false"
			:notification="`Bạn có muốn '${message}' hồ sơ này?`"
			@action="handleAction2"
		/>
		<ModalNotificationCertificate
			v-if="isReUpload"
			@cancel="isReUpload = false"
			v-bind:notification="reUploadMessage"
			@action="openFile"
		/>
		<ModalDelete
			v-if="deleteUploadDocument"
			@cancel="deleteUploadDocument = false"
			@action="deleteDocument"
		/>
	</div>
</template>
<style lang="scss">
@import "../../../node_modules/leaflet.markercluster/dist/MarkerCluster.css";
@import "../../../node_modules/leaflet.markercluster/dist/MarkerCluster.Default.css";
@import "../../../node_modules/leaflet/dist/leaflet.css";
</style>
<script>
import { ref } from "vue";
import { storeToRefs } from "pinia";
import { usePreCertificateStore } from "@/store/preCertificate";

import ModalDelete from "@/components/Modal/ModalDelete";
import ModalViewDocument from "@/components/PreCertificate/ModalViewDocument";
import ModalNotificationCertificate from "@/components/Modal/ModalNotificationCertificate";
import ModalNotificationCertificateNote from "@/components/Modal/ModalNotificationCertificateNote";

import InputDatePicker from "@/components/Form/InputDatePicker";
import InputCategory from "@/components/Form/InputCategory";
import InputCategorySearch from "@/components/Form/InputCategorySearch";
import InputText from "@/components/Form/InputText";
import InputDatePickerV2 from "@/components/Form/InputDatePickerV2";
import InputTextPrefixCustom from "@/components/Form/InputTextPrefixCustom";
import InputNumberFormat from "@/components/Form/InputNumber";
import InputCurrency from "@/components/Form/InputCurrency";
import { Tabs, TabItem } from "vue-material-tabs";
import Vue from "vue";
import Icon from "buefy";
import InputLengthArea from "@/components/Form/InputLengthArea.vue";
import CertificationBrief from "@/models/CertificationBrief";
import PreCertificate from "@/models/PreCertificate";
import WareHouse from "@/models/WareHouse";
import { Timeline, Drawer } from "ant-design-vue";
import moment from "moment";
import ModalCustomer from "@/components/PreCertificate/ModalCustomer";
import ModalAppraisal from "@/components/PreCertificate/ModalAppraisal";
import ModalAppraiseInfomation from "@/components/PreCertificate/ModalAppraiseInfomation";
import OtherFile from "@/components/PreCertificate/OtherFile";
import File from "@/models/File";
import axios from "@/plugins/axios";
import {
	BTooltip,
	BDropdown,
	BDropdownItem,
	BButtonGroup
} from "bootstrap-vue";
import Footer from "@/components/PreCertificate/FooterDetail.vue";

import store from "@/store";
import * as types from "@/store/mutation-types";
// const jsonConfig = require("../../../config/pre_certificate_workflow.json");
const jsonConfig = null;
Vue.use(Icon);
export default {
	props: {
		routeId: {
			type: String
		}
	},
	name: "detail_pre_certification",
	components: {
		OtherFile,
		InputCategory,
		InputCategorySearch,
		InputText,
		InputDatePickerV2,
		InputTextPrefixCustom,
		InputNumberFormat,
		InputDatePicker,
		InputCurrency,
		Tabs,
		TabItem,
		Timeline,
		Drawer,
		InputLengthArea,
		ModalCustomer,
		ModalAppraisal,
		ModalAppraiseInfomation,
		"b-tooltip": BTooltip,
		ModalNotificationCertificate,
		ModalViewDocument,
		ModalDelete,
		"b-dropdown-item": BDropdownItem,
		"b-button-group": BButtonGroup,
		"b-dropdown": BDropdown,
		Footer,
		ModalNotificationCertificateNote
	},
	data() {
		return {
			theme: {
				navItem: "#000000",
				navActiveItem: "#FAA831",
				slider: "#FAA831",
				arrow: "#000000"
			},
			status: 1,
			total_price_appraise: "",
			key_render_appraisal: 10000,
			openModalDelete: false,
			showCustomerDialog: false,
			showAppraiseInformationDialog: false,
			showAppraisalDialog: false,
			showAppraiseListDialog: false,
			visible: false,
			showCardDetailImage: true,
			openSendRequire: false,
			openSendAppraiser: false,
			customers_step_1: this.customers,
			appraisersManager: [],
			appraisers: [],
			signAppraisers: [],
			file: "",
			documentAppraise: [],
			message: "",
			openNotification: false,
			cancel_certificate: false,
			openNotificationDenined: false,
			filePrint: "",
			isShowPrint: false,
			title: "",
			indexDelete: "",
			id_file_delete: "",
			position_profile: "",
			view: false,
			add: false,
			edit: false,
			deleted: false,
			accept: false,
			exportAction: false,
			checkRole: false,
			showDetailAppraise: false,
			dataDetailAppraise: [],
			appraiser_number: "",
			user_id: "",
			historyList: [],
			isCheckRealEstate: true,
			isCheckConstruction: false,
			isViewAutomationDocument: true,
			jsonConfig: jsonConfig,
			targetStatus: "",
			isHandleAction: false,
			editAppraiser: false,
			editItemList: false,
			editInfo: false,
			printConfig: false,
			profile: {},
			config: {},
			checkVersion: true,
			typeAppraiseProperty: [],
			isShowAppraiseListVersion: false,
			isCheckPrice: false,
			isCheckVersion: false,
			isCheckLegal: false,
			changeStatusRequire: {},
			isApartment: false,
			isReUpload: false,
			reUploadMessage: "",
			reportType: "",
			deleteUploadDocument: false,
			documentName: [
				"Chứng thư thẩm định",
				"Báo cáo thẩm định",
				"Bảng điều chỉnh QSDĐ",
				"Bảng điều chỉnh CTXD",
				"Hình ảnh hiện trạng",
				"Phiếu thu thập TSSS"
			]
		};
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
		const { dataPC, lstData, preCertificateOtherDocuments } = storeToRefs(
			preCertificateStore
		);
		const start = async () => {
			if (!lstData.value.workflow) {
				await preCertificateStore.getConfig();
			}
			await preCertificateStore.resetData();
			await preCertificateStore.getPreCertificate(props.routeId);
		};
		start();
		const checkVersion2 = ref([]);
		const showCardDetailFileResult = ref(false);
		return {
			isMobile,
			dataPC,
			lstData,
			preCertificateOtherDocuments,
			preCertificateStore,
			checkVersion2,

			showCardDetailFileResult
		};
	},

	created() {
		const profile = this.$store.getters.profile;
		if (profile.data.user) {
			this.position_profile =
				profile.data.user.appraiser.appraise_position.acronym;
			this.appraiser_number = profile.data.user.appraiser.appraiser_number;
		}
		this.user_id = profile.data.user.id;
		this.profile = profile;
		if (profile.data.user.id === this.dataPC.created_by) {
			this.checkRole = true;
		}
		const permission = this.$store.getters.currentPermissions;
		// fix_permission
		permission.forEach(value => {
			if (value === "VIEW_CERTIFICATE_BRIEF") {
				this.view = true;
			}
			if (value === "ADD_CERTIFICATE_BRIEF") {
				this.add = true;
			}
			if (value === "EDIT_CERTIFICATE_BRIEF") {
				this.edit = true;
			}
			if (value === "DELETE_CERTIFICATE_BRIEF") {
				this.deleted = true;
			}
			if (value === "ACCEPT_CERTIFICATE_BRIEF") {
				this.accept = true;
			}
			if (value === "EXPORT_CERTIFICATE_BRIEF") {
				this.exportAction = true;
			}
		});
		this.getDictionary();
	},
	computed: {
		columnAssets() {
			let dataColumn = [
				{
					title: "Mã TSTĐ",
					align: "left",
					scopedSlots: { customRender: "data" },
					hiddenItem: false
				},
				{
					title: "Version",
					align: "center",
					scopedSlots: { customRender: "version" },
					dataIndex: "version",
					hiddenItem: false
				},
				{
					title: "Loại tài sản",
					align: "left",
					dataIndex: "asset_type.description",
					hiddenItem: false
				},
				{
					title: "Tên tài sản",
					align: "left",
					scopedSlots: { customRender: "asset" },
					hiddenItem: false
				},
				// {
				// 	title: 'Loại đất',
				// 	align: 'left',
				// 	scopedSlots: {customRender: 'land'},
				// 	hiddenItem: this.isCheckRealEstate
				// },
				{
					title: "Tổng diện tích",
					align: "right",
					scopedSlots: { customRender: "area" },
					dataIndex: "total_area",
					hiddenItem: !this.isCheckRealEstate
				},
				{
					title: "Tổng giá trị",
					align: "right",
					scopedSlots: { customRender: "price" },
					dataIndex: "total_price",
					hiddenItem: false
				}
				// {
				// 	title: 'Ngày tạo',
				// 	align: 'right',
				// 	scopedSlots: {customRender: 'created_at'},
				// 	dataIndex: 'created_at',
				// 	hiddenItem: false
				// }
			];
			return dataColumn.filter(item => item.hiddenItem === false);
		},
		filterDocumentName() {
			return this.documentName;
		},
		isCertificateReport() {
			let report = this.getReport("certificate_report");
			if (report) {
				return true;
			}
			return false;
		},
		isAppraisalReport() {
			let report = this.getReport("appraisal_report");
			if (report) {
				return true;
			}
			return false;
		},
		isAppendix1Report() {
			let report = this.getReport("appendix1_report");
			if (report) {
				return true;
			}
			return false;
		},
		isAppendix2Report() {
			let report = this.getReport("appendix2_report");
			if (report) {
				return true;
			}
			return false;
		},
		isAppendix3Report() {
			let report = this.getReport("appendix3_report");
			if (report) {
				return true;
			}
			return false;
		},
		isComparisionAssetReport() {
			let report = this.getReport("comparision_asset_report");
			if (report) {
				return true;
			}
			return false;
		},
		certificatReportName() {
			let report = this.getReport("certificate_report");
			if (report) {
				return report.name;
			}
			return "Chứng thư thẩm định";
		},
		appraisalReportName() {
			let report = this.getReport("appraisal_report");
			if (report) {
				return report.name;
			}
			return "Báo cáo thẩm định";
		},
		appendix1ReportName() {
			let report = this.getReport("appendix1_report");
			if (report) {
				return report.name;
			}
			return "Bảng điều chỉnh QSDĐ";
		},
		appendix2ReportName() {
			let report = this.getReport("appendix2_report");
			if (report) {
				return report.name;
			}
			return "Bảng điều chỉnh CTXD";
		},
		appendix3ReportName() {
			let report = this.getReport("appendix3_report");
			if (report) {
				return report.name;
			}
			return "Hình ảnh hiện trạng";
		},
		comparisionAssetReportName() {
			let report = this.getReport("comparision_asset_report");
			if (report) {
				return report.name;
			}
			return "Phiếu thu thập TSSS";
		},
		getHistoryTextColor() {
			return this.historyList.map(item => {
				return this.loadColor(item);
			});
		}
	},
	methods: {
		getReport(type) {
			let report = this.dataPC.other_documents.find(
				i => i.description === type
			);
			return report;
		},
		openFile() {
			const id = "upload_" + this.reportType;
			document.getElementById(id).click();
		},
		loadColor(item) {
			let color = "";
			if (item.log_name == "update_status") {
				if (item.description.includes("từ chối")) color = "text-danger";
				else if (item.description.includes("Hủy")) color = "text-danger";
				else color = "text-success";
			}
			return color;
		},
		formatSentenceCase(phrase) {
			let text = phrase.toLowerCase();
			return text.charAt(0).toUpperCase() + text.slice(1);
		},
		async getDictionary() {
			let resp = this.$store.getters.dictionaries;
			if (resp && resp.length === 0) {
				resp = await WareHouse.getDictionaries();
				store.commit(types.SET_DICTIONARIES, { ...resp });
			}
			this.typeAppraiseProperty = [...resp.data.loai_tai_san];
			this.typeAppraiseProperty.forEach(item => {
				item.description = this.formatSentenceCase(item.description);
			});
		},
		checkNoticeMessage() {
			if (
				this.dataPC.general_asset.length === 0 &&
				this.dataPC.appraiser_perform &&
				this.user_id !== this.dataPC.appraiser_perform.user_id
			) {
				return true;
			} else if (
				this.dataPC.general_asset.length === 0 &&
				this.dataPC.status === 5 &&
				this.dataPC.appraiser_perform &&
				this.user_id === this.dataPC.appraiser_perform.user_id
			) {
				return true;
			} else return false;
		},
		showAcronym(acronym) {
			if (acronym === "KHAC") {
				return "TSK";
			} else if (acronym === "DS") {
				return "DS";
			} else return acronym;
		},
		showDrawer() {
			this.visible = true;
			this.getHistoryTimeLine();
		},
		onClose() {
			this.visible = false;
		},
		async handleDetail(data) {
			let routeData;
			if (data.asset_type.dictionary_acronym === "DS") {
				if (data.asset_type.acronym === "PTVT") {
					routeData = this.$router.resolve({
						name: "certification_asset.vehicle.detail",
						query: {
							id: data.general_asset_id,
							asset_type_id: data.asset_type_id
						}
					});
				} else if (data.asset_type.acronym === "MMTB") {
					routeData = this.$router.resolve({
						name: "certification_asset.machine.detail",
						query: {
							id: data.general_asset_id,
							asset_type_id: data.asset_type_id
						}
					});
				}
			} else if (data.asset_type.dictionary_acronym === "KHAC") {
				routeData = this.$router.resolve({
					name: "certification_asset.other_purpose.detail",
					query: {
						id: data.general_asset_id,
						asset_type_id: data.asset_type_id
					}
				});
			} else if (data.asset_type.acronym === "CC") {
				routeData = this.$router.resolve({
					name: "certification_asset.apartment.detail",
					query: { id: data.asset.asset_id }
				});
			} else {
				routeData = this.$router.resolve({
					name: "certification_asset.detail",
					query: { id: data.asset.asset_id }
				});
			}
			window.open(routeData.href, "_blank");
		},
		async viewDetailAppraise() {
			let ids = [];
			await this.dataPC.real_estate.forEach(item => {
				ids.push(item.real_estate_id);
			});
			const res = await CertificationBrief.getAppraiseCompare(ids);
			if (res.data) {
				if (res.data[0].message) {
					return this.$toast.open({
						message: res.data[0].message,
						type: "error",
						position: "top-right",
						duration: 5000
					});
				}
				this.dataDetailAppraise = res.data;
				this.showDetailAppraise = true;
			} else if (res.error) {
				return this.$toast.open({
					message: res.error.message,
					type: "error",
					position: "top-right",
					duration: 5000
				});
			}
		},
		async getHistoryTimeLine() {
			const res = await CertificationBrief.getHistoryTimeline(this.dataPC.id);
			if (res.data) {
				const resp = await WareHouse.getDictionaries();
				if (resp) {
					this.historyList = res.data;
					for (let i = 0; i < this.historyList.length; i++) {
						let e = this.historyList[i];
						if (e.properties.reason_id) {
							let result = resp.data.li_do.filter(
								item => item.id === e.properties.reason_id
							);
							// console.log('répóne',result)
							e.reason_description = result[0].description;
						}
					}
				}

				// console.log('timeline', this.historyList)
			} else if (res.error) {
				return this.$toast.open({
					message: res.error.message,
					type: "error",
					position: "top-right",
					duration: 5000
				});
			}
		},
		formatDate(value) {
			return moment(String(value)).format("DD/MM/YYYY");
		},
		formatDateTime(value) {
			return moment(String(value)).format("HH:mm DD/MM/YYYY");
		},
		formatNumber(num) {
			if (num) {
				let formatedNum = num.toString().replace(".", ",");
				return formatedNum.toString().replace(/^[+-]?\d+/, function(int) {
					return int.replace(/(\d)(?=(\d{3})+$)/g, "$1.");
				});
			}
		},
		handleEdit() {
			this.$router
				.push({
					name: "pre_certification.edit",
					query: {
						id: `${this.dataPC.id}`
					}
				})
				.catch(_ => {});
		},
		onCancel() {
			return this.$router.push({ name: "pre_certification.index" });
		},
		handleCancelCertificate() {
			this.openNotification = true;
			this.cancel_certificate = true;
			this.message = "Bạn có muốn hủy hồ sơ này?";
		},
		handleAccept() {
			this.openNotification = true;
			this.cancel_certificate = false;
			this.message = "Bạn có muốn duyệt hồ sơ này?";
		},
		handleDenined() {
			this.openNotificationDenined = true;
			this.cancel_certificate = false;
			this.message = "Bạn có muốn từ chối hồ sơ này";
		},
		handleSendRequire() {
			if (this.dataPC.general_asset && this.dataPC.general_asset.length > 0) {
				this.openSendRequire = true;
				this.key_render_appraisal += 1;
			} else {
				this.$toast.open({
					message: "Vui lòng chọn tài sản thẩm định",
					type: "error",
					position: "top-right"
				});
			}
		},
		updateSendRequired() {
			this.$router.push({ name: "pre_certification.index" }).catch(_ => {});
		},
		handleSendAppraiser() {
			this.openSendAppraiser = true;
			this.key_render_appraisal += 1;
			this.status = 2;
		},
		updateSendAppraiser() {
			this.$router.push({ name: "pre_certification.index" }).catch(_ => {});
		},
		handleCancel() {
			this.openNotification = false;
			if (this.cancel_certificate) {
				this.cancel_certificate = false;
			}
		},
		handleShowAppraisal() {
			// console.log('-----------',this.dataPC)
			this.key_render_appraisal += 1;
			this.status = this.dataPC.status;
			this.showAppraisalDialog = true;
		},
		handleShowAppraiseInformation() {
			this.showAppraiseInformationDialog = true;
		},
		handleShowAppraiseList() {
			this.showAppraiseListDialog = true;
		},
		handleImportAppraise() {
			return this.$toast.open({
				message: "Hiện tại chức năng này chưa được mở ở phiên bản dùng thử",
				type: "error",
				position: "top-right",
				duration: 3000
			});
		},
		updateAppraiseInformation(dataAppraiseInformation) {
			this.dataPC.appraise_date = dataAppraiseInformation.appraise_date;
			this.dataPC.appraise_purpose_id =
				dataAppraiseInformation.appraise_purpose_id;
			this.dataPC.appraise_purpose = dataAppraiseInformation.appraise_purpose;
			this.dataPC.certificate_date = dataAppraiseInformation.certificate_date;
			this.dataPC.certificate_num = dataAppraiseInformation.certificate_num;
			this.dataPC.document_date = dataAppraiseInformation.document_date;
			this.dataPC.document_num = dataAppraiseInformation.document_num;
			this.dataPC.petitioner_address =
				dataAppraiseInformation.petitioner_address;
			this.dataPC.petitioner_name = dataAppraiseInformation.petitioner_name;
			this.dataPC.petitioner_phone = dataAppraiseInformation.petitioner_phone;
			this.dataPC.service_fee = dataAppraiseInformation.service_fee;
			this.dataPC.commission_fee = dataAppraiseInformation.commission_fee;
			this.dataPC.petitioner_identity_card =
				dataAppraiseInformation.petitioner_identity_card;
			this.dataPC.document_type = dataAppraiseInformation.document_type;
			this.dataPC.note = dataAppraiseInformation.note;
		},
		updateAppraisal(dataAppraisal) {
			this.dataPC.appraiser_perform = dataAppraisal.appraiser_perform;
			this.dataPC.appraiser_perform_id = dataAppraisal.appraiser_perform_id;
			this.dataPC.appraiser_confirm_id = dataAppraisal.appraiser_confirm_id;
			this.dataPC.appraiser_confirm = dataAppraisal.appraiser_confirm;
			this.dataPC.appraiser_manager_id = dataAppraisal.appraiser_manager_id;
			this.dataPC.appraiser_manager = dataAppraisal.appraiser_manager;
			this.dataPC.appraiser_control_id = dataAppraisal.appraiser_control_id;
			this.dataPC.appraiser_control = dataAppraisal.appraiser_control;
			this.dataPC.appraiser = dataAppraisal.appraiser;
			this.dataPC.appraiser_id = dataAppraisal.appraiser_id;
			this.key_render_appraisal += 1;
			this.dataPC.status = this.status;
			this.showAppraisalDialog = false;
		},
		updateAppraises(data) {
			this.dataPC.appraises = data.general_asset;
			this.dataPC.general_asset = data.general_asset;
			this.dataPC.document_type = data.document_type;
			this.dataPC.real_estate = data.real_estate;
			this.getTotalPrice();
		},
		async updateAppraiseVersion(data) {
			this.isShowAppraiseListVersion = false;
			this.checkVersion = [];
			this.checkVersion2 = [];
			this.dataPC.general_asset = data.general_asset;
			this.getTotalPrice();
		},
		async handleAction(note, reason_id) {
			const {
				appraiser_id,
				appraiser_perform_id,
				appraiser_confirm_id,
				appraiser_manager_id,
				appraiser_perform,
				appraiser_confirm,
				appraiser_manager,
				appraiser,
				appraiser_control,
				appraiser_control_id
			} = this.dataPC;
			let dataSend = {
				appraiser_perform,
				appraiser_id,
				appraiser_perform_id,
				appraiser_confirm_id,
				appraiser_confirm,
				appraiser_manager_id,
				appraiser_manager,
				appraiser_control,
				appraiser_control_id,
				appraiser,
				status: 1,
				status_config: this.jsonConfig.principle,
				status_note: note,
				status_reason_id: reason_id
			};
			if (this.dataPC.status === 2 && !this.cancel_certificate) {
				// change status 2 --> 3
				dataSend.status = 3;
				const res = await PreCertificate.updateStatusPreCertificate(
					this.dataPC.id,
					dataSend
				);
				if (res.data) {
					this.$toast.open({
						message: "Gửi phê duyệt thành công",
						type: "success",
						position: "top-right",
						duration: 3000
					});
					this.dataPC.status = 3;
				} else if (res.error) {
					this.$toast.open({
						message: res.error.message,
						type: "error",
						position: "top-right",
						duration: 5000
					});
				}
				this.openNotification = false;
			} else if (this.dataPC.status === 3 && !this.cancel_certificate) {
				// change status 3 --> 4
				dataSend.status = 4;
				const res = await PreCertificate.updateStatusPreCertificate(
					this.dataPC.id,
					dataSend
				);
				if (res.data) {
					this.$toast.open({
						message: "Xác nhận hồ sơ thành công",
						type: "success",
						position: "top-right",
						duration: 3000
					});
					this.dataPC.status = 4;
				} else if (res.error) {
					this.$toast.open({
						message: res.error.message,
						type: "error",
						position: "top-right",
						duration: 5000
					});
				}
				this.openNotification = false;
			} else if (this.cancel_certificate) {
				// change status 2 --> 5
				dataSend.status = 5;
				const res = await PreCertificate.updateStatusPreCertificate(
					this.dataPC.id,
					dataSend
				);
				if (res.data) {
					this.$toast.open({
						message: "Hủy hồ sơ thành công",
						type: "success",
						position: "top-right",
						duration: 3000
					});
					this.dataPC.status = 5;
				} else if (res.error) {
					this.$toast.open({
						message: res.error.message,
						type: "error",
						position: "top-right",
						duration: 5000
					});
				}
				this.openNotification = false;
				this.cancel_certificate = false;
			}
		},
		checkAppraiser() {
			if (this.dataPC.appraiser_perform_id && this.dataPC.appraiser_id) {
				return true;
			} else {
				return false;
			}
		},
		checkItemList() {
			if (
				this.dataPC.personal_properties.length > 0 ||
				this.dataPC.general_asset.length > 0
			) {
				return true;
			} else {
				return false;
			}
		},
		checkRequired(require, data) {
			let message = "";
			let check = false;
			if (require) {
				this.changeStatusRequire = require;
				this.isCheckPrice = require.check_price ? require.check_price : false;
				this.isCheckLegal = require.check_legal ? require.check_legal : false;
				this.isCheckVersion = require.check_version
					? require.check_version
					: false;
				if (require.appraiser) {
					check = this.checkAppraiser();
					if (!check) {
						return "Chưa có thông tin tổ thẩm định";
					}
				}
				if (check && require.appraise_item_list) {
					check = this.checkItemList();
					if (!check) {
						return "Chưa có chi tiết tài sản thẩm định";
					}
				}
			}
			return message;
		},
		handleFooterAccept(target) {
			let config = this.jsonConfig.principle.find(i => i.id === target.id);
			let message = "";
			if (config) {
				this.config = config;
				let require = config.require;
				message = this.checkDiffVersion();
				if (message === "" && require) {
					message = this.checkRequired(require, this.data);
				}
				if (message === "") {
					this.targetStatus = config.status;
					this.message = target.description;
					this.isHandleAction = true;
				} else {
					this.openMessage(message);
				}
			} else {
				this.openMessage(
					"Không tìm thấy thông tin bước tiếp theo. Vui lòng liên hệ admin để hỗ trợ."
				);
			}
		},
		getExpireStatusDate() {
			let dateConvert = new Date();
			let minutes = this.config.process_time ? this.config.process_time : 1440;
			let dateConverted = new Date(dateConvert.getTime() + minutes * 60000);
			let status_expired_at = moment(dateConverted).format("DD-MM-YYYY HH:mm");
			return status_expired_at;
		},
		async handleAction2(note, reason_id) {
			const {
				appraiser_id,
				appraiser_perform_id,
				appraiser_confirm_id,
				appraiser_manager_id,
				appraiser_perform,
				appraiser_confirm,
				appraiser_manager,
				appraiser,
				appraiser_control,
				appraiser_control_id
			} = this.dataPC;
			let dataSend = {
				appraiser_perform,
				appraiser_id,
				appraiser_perform_id,
				appraiser_confirm_id,
				appraiser_confirm,
				appraiser_manager_id,
				appraiser_manager,
				appraiser_control,
				appraiser_control_id,
				appraiser,
				status: this.targetStatus,
				check_price: this.isCheckPrice,
				check_version: this.isCheckVersion,
				check_legal: this.isCheckLegal,
				required: this.changeStatusRequire,
				status_expired_at: this.getExpireStatusDate(),
				status_note: note,
				status_reason_id: reason_id,
				status_description: this.message,
				status_config: this.jsonConfig.principle
			};
			const res = await PreCertificate.updateStatusPreCertificate(
				this.dataPC.id,
				dataSend
			);
			if (res.data) {
				this.dataPC.status = this.targetStatus;
				this.changeEditStatus();
				this.$toast.open({
					message: this.message + " thành công",
					type: "success",
					position: "top-right",
					duration: 3000
				});
			} else if (res.error) {
				this.$toast.open({
					message: res.error.message,
					type: "error",
					position: "top-right",
					duration: 5000
				});
			}
			this.isHandleAction = false;
		},
		async handleActionDenined() {
			const {
				appraiser_id,
				appraiser_perform_id,
				appraiser_confirm_id,
				appraiser_manager_id,
				appraiser_perform,
				appraiser_confirm,
				appraiser_manager,
				appraiser,
				appraiser_control,
				appraiser_control_id
			} = this.dataPC;
			let dataSend = {
				appraiser_perform,
				appraiser_id,
				appraiser_perform_id,
				appraiser_confirm_id,
				appraiser_confirm,
				appraiser_manager_id,
				appraiser_manager,
				appraiser_control,
				appraiser_control_id,
				appraiser,
				status: 0
			};
			if (this.dataPC.status === 3) {
				// denined change status 3 ---> 2
				dataSend.status = 2;
				const res = await PreCertificate.updateStatusPreCertificate(
					this.dataPC.id,
					dataSend
				);
				if (res.data) {
					this.$toast.open({
						message: "Từ chối phê duyệt thành công",
						type: "success",
						position: "top-right",
						duration: 3000
					});
					this.dataPC.status = 2;
				} else if (res.error) {
					this.$toast.open({
						message: res.error.message,
						type: "error",
						position: "top-right",
						duration: 5000
					});
				}
				this.openNotificationDenined = false;
			}
		},
		async onImageChange(e) {
			const formData = new FormData();
			let check = true;
			let files = e.target.files;
			if (!files.length) {
				return;
			}
			for (let i = 0; i < e.target.files.length; i++) {
				this.file = e.target.files[i];
				if (
					this.file.type === "image/png" ||
					this.file.type === "image/jpeg" ||
					this.file.type === "image/jpg" ||
					this.file.type === "image/gif" ||
					this.file.type ===
						"application/vnd.openxmlformats-officedocument.wordprocessingml.document" ||
					this.file.type ===
						"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" ||
					this.file.type === "application/pdf"
				) {
				} else {
					check = false;
					this.$toast.open({
						message: "Hình không đúng định dạng vui lòng kiểm tra lại",
						type: "error",
						position: "top-right",
						duration: 3000
					});
				}
			}
			if (check) {
				if (files.length) {
					for (let i = 0; i < files.length; i++) {
						formData.append("files[" + i + "]", files[i]);
						console.log("files", files);
					}
					let res = null;
					if (this.dataPC.status === 1) {
						res = await File.saleUploadFileCertificate(
							formData,
							this.dataPC.id
						);
					} else {
						res = await File.uploadFileCertificate(formData, this.dataPC.id);
					}
					console.log("res", res, formData);
					if (res.data) {
						// await this.$emit('handleChangeFile', res.data.data)
						this.dataPC.other_documents = res.data.data;
						this.$toast.open({
							message: "Thêm file thành công",
							type: "success",
							position: "top-right",
							duration: 3000
						});
					}
				}
			}
		},
		checkFileUpload(type) {
			let message = "";
			let isReUpload = false;
			switch (type) {
				case "certificate_report":
					if (this.isCertificateReport) {
						message = "Chứng thư thẩm định";
						isReUpload = true;
					}
					break;
				case "appraisal_report":
					if (this.isAppraisalReport) {
						message = "Báo cáo thẩm định";
						isReUpload = true;
					}
					break;
				case "appendix1_report":
					if (this.isAppendix1Report) {
						message = "Bảng điều chỉnh QSDĐ";
						isReUpload = true;
					}
					break;
				case "appendix2_report":
					if (this.isAppendix2Report) {
						message = "Bảng điều chỉnh CTXD";
						isReUpload = true;
					}
					break;
				case "appendix3_report":
					if (this.isAppendix3Report) {
						message = "Hình ảnh hiện trạng";
						isReUpload = true;
					}
					break;
				case "comparision_asset_report":
					if (this.isComparisionAssetReport) {
						message = "Phiếu thu thập TSSS";
						isReUpload = true;
					}
					break;
			}
			this.reportType = type;
			if (isReUpload) {
				this.isReUpload = isReUpload;
				this.reUploadMessage = isReUpload
					? message + " đã có, bạn có muốn upload " + message + " mới ?"
					: "";
			} else {
				this.openFile();
			}
		},
		async onUploadDocument(type, e) {
			const formData = new FormData();
			let check = true;
			let files = e.target.files;
			if (!files.length) {
				return;
			}
			for (let i = 0; i < e.target.files.length; i++) {
				this.file = e.target.files[i];
				if (
					this.file.type ===
						"application/vnd.openxmlformats-officedocument.wordprocessingml.document" ||
					this.file.type === "application/pdf"
				) {
				} else {
					check = false;
					this.$toast.open({
						message: "File dữ liệu không đúng định dạng vui lòng kiểm tra lại",
						type: "error",
						position: "top-right",
						duration: 3000
					});
				}
			}
			if (check) {
				if (files.length) {
					for (let i = 0; i < files.length; i++) {
						formData.append("files[" + i + "]", files[i]);
					}
					let res = null;
					res = await File.uploadDocument(formData, this.dataPC.id, type);
					if (res.data) {
						this.dataPC.other_documents = res.data.data;
						this.$toast.open({
							message: "Thêm file thành công",
							type: "success",
							position: "top-right",
							duration: 3000
						});
					}
				}
			}
		},
		async viewCertificate() {
			await Certificate.getPrintProof(this.dataPC.id).then(resp => {
				const file = resp.data;
				if (file) {
					this.filePrint = file.url;
				} else {
					this.$toast.open({
						message: "Tải file bị lỗi vui lòng gọi hỗ trợ",
						type: "error",
						position: "top-right",
						duration: 3000
					});
				}
			});
			this.title = "Tài liệu chứng thư thẩm định";
			this.isShowPrint = true;
		},
		async downloadCertificate() {
			await Certificate.getPrintProof(this.dataPC.id).then(resp => {
				const file = resp.data;
				if (file) {
					const fileLink = document.createElement("a");
					fileLink.href = file.url;
					fileLink.setAttribute("download", file.file_name);
					document.body.appendChild(fileLink);
					fileLink.click();
					fileLink.remove();
					window.URL.revokeObjectURL(fileLink);
				} else {
					this.$toast.open({
						message: "Tải file bị lỗi vui lòng gọi hỗ trợ",
						type: "error",
						position: "top-right",
						duration: 3000
					});
				}
			});
		},
		async viewReportCertificate() {
			await Certificate.getPrintReport(this.dataPC.id).then(resp => {
				const file = resp.data;
				if (file) {
					this.filePrint = file.url;
				} else {
					this.$toast.open({
						message: "Tải file bị lỗi vui lòng gọi hỗ trợ",
						type: "error",
						position: "top-right",
						duration: 3000
					});
				}
			});
			this.title = "Tài liệu báo cáo thẩm định";
			this.isShowPrint = true;
		},
		async downloadReportCertificate() {
			await Certificate.getPrintReport(this.dataPC.id).then(resp => {
				const file = resp.data;
				if (file) {
					const fileLink = document.createElement("a");
					fileLink.href = file.url;
					fileLink.setAttribute("download", file.file_name);
					document.body.appendChild(fileLink);
					fileLink.click();
					fileLink.remove();
					window.URL.revokeObjectURL(fileLink);
				} else {
					this.$toast.open({
						message: "Tải file bị lỗi vui lòng gọi hỗ trợ",
						type: "error",
						position: "top-right",
						duration: 3000
					});
				}
			});
		},
		async viewAssetDocument() {
			let arrayAsset = [];
			if (this.dataPC.real_estate && this.dataPC.real_estate.length > 0) {
				await this.dataPC.real_estate.forEach(item => {
					if (
						item.appraises &&
						item.appraises.appraise_has_assets &&
						item.appraises.appraise_has_assets.length > 0
					) {
						item.appraises.appraise_has_assets.forEach(asset => {
							arrayAsset.push(asset.asset_general_id);
						});
					}
					if (
						item.apartment &&
						item.apartment.apartment_has_assets &&
						item.apartment.apartment_has_assets.length > 0
					) {
						item.apartment.apartment_has_assets.forEach(asset => {
							arrayAsset.push(asset.asset_general_id);
						});
					}
				});
			}
			const res = await WareHouse.getPrint(arrayAsset, this.dataPC.id);
			if (res.data) {
				const file = res.data;
				this.filePrint = file.url;
				this.title = "Tài liệu phiếu thu thập TSSS";
				this.isShowPrint = true;
			} else {
				this.$toast.open({
					message: "Xem file bị lỗi vui lòng gọi hỗ trợ",
					type: "error",
					position: "top-right",
					duration: 3000
				});
			}
		},
		async downloadAssetDocument() {
			let arrayAsset = [];
			// console.log(this.dataPC.real_estate)
			if (this.dataPC.real_estate && this.dataPC.real_estate.length > 0) {
				await this.dataPC.real_estate.forEach(item => {
					if (
						item.appraises &&
						item.appraises.appraise_has_assets &&
						item.appraises.appraise_has_assets.length > 0
					) {
						item.appraises.appraise_has_assets.forEach(asset => {
							arrayAsset.push(asset.asset_general_id);
						});
					}
					if (
						item.apartment &&
						item.apartment.apartment_has_assets &&
						item.apartment.apartment_has_assets.length > 0
					) {
						item.apartment.apartment_has_assets.forEach(asset => {
							arrayAsset.push(asset.asset_general_id);
						});
					}
				});
			}
			this.isSubmit = true;
			const res = await WareHouse.getPrint(arrayAsset, this.dataPC.id);
			if (res.data) {
				const file = res.data;
				const fileLink = document.createElement("a");
				fileLink.href = file.url;
				fileLink.setAttribute("download", file.file_name);
				document.body.appendChild(fileLink);
				fileLink.click();
				fileLink.remove();
				window.URL.revokeObjectURL(fileLink);
			} else {
				this.$toast.open({
					message: "Tải file bị lỗi vui lòng gọi hỗ trợ",
					type: "error",
					position: "top-right",
					duration: 3000
				});
			}
		},
		async viewAppendix1() {
			await Certificate.getPrint(this.dataPC.id).then(resp => {
				const file = resp.data;
				if (file) {
					this.filePrint = file.url;
				}
			});
			this.title = "Tài liệu bảng điều chỉnh QSDĐ";
			this.isShowPrint = true;
		},
		async downloadAppendix1() {
			await Certificate.getPrint(this.dataPC.id).then(resp => {
				const file = resp.data;
				if (file) {
					const fileLink = document.createElement("a");
					fileLink.href = file.url;
					fileLink.setAttribute("download", file.file_name);
					document.body.appendChild(fileLink);
					fileLink.click();
					fileLink.remove();
					window.URL.revokeObjectURL(fileLink);
				} else {
					this.$toast.open({
						message: "Tải file bị lỗi vui lòng gọi hỗ trợ",
						type: "error",
						position: "top-right",
						duration: 3000
					});
				}
			});
		},
		async viewAppendix2() {
			await Certificate.getPrintAppendix(this.dataPC.id).then(resp => {
				const file = resp.data;
				if (file) {
					this.filePrint = file.url;
				}
			});
			this.title = "Tài liệu bảng điều chỉnh CTXD";
			this.isShowPrint = true;
		},
		async downloadAppendix2() {
			await Certificate.getPrintAppendix(this.dataPC.id).then(resp => {
				const file = resp.data;
				if (file) {
					const fileLink = document.createElement("a");
					fileLink.href = file.url;
					fileLink.setAttribute("download", file.file_name);
					document.body.appendChild(fileLink);
					fileLink.click();
					fileLink.remove();
					window.URL.revokeObjectURL(fileLink);
				} else {
					this.$toast.open({
						message: "Tải file bị lỗi vui lòng gọi hỗ trợ",
						type: "error",
						position: "top-right",
						duration: 3000
					});
				}
			});
		},
		async viewAppendix3() {
			await Certificate.getPrintImage(this.dataPC.id).then(resp => {
				const file = resp.data;
				if (file) {
					this.filePrint = file.url;
				} else {
					this.$toast.open({
						message: "Tải file bị lỗi vui lòng gọi hỗ trợ",
						type: "error",
						position: "top-right",
						duration: 3000
					});
				}
			});
			this.title = "Tài liệu hình ảnh hiện trạng";
			this.isShowPrint = true;
		},
		async downloadAppendix3() {
			await Certificate.getPrintImage(this.dataPC.id).then(resp => {
				const file = resp.data;
				if (file) {
					const fileLink = document.createElement("a");
					fileLink.href = file.url;
					fileLink.setAttribute("download", file.file_name);
					document.body.appendChild(fileLink);
					fileLink.click();
					fileLink.remove();
					window.URL.revokeObjectURL(fileLink);
				} else {
					this.$toast.open({
						message: "Tải file bị lỗi vui lòng gọi hỗ trợ",
						type: "error",
						position: "top-right",
						duration: 3000
					});
				}
			});
		},
		downloadOtherFile(file) {
			if (this.exportAction) {
				axios({
					url:
						process.env.API_URL +
						"/api/certificate/other-document/download/" +
						file.id,
					method: "GET",
					responseType: "blob"
				}).then(response => {
					const url = window.URL.createObjectURL(new Blob([response.data]));
					const link = document.createElement("a");
					link.href = url;
					link.setAttribute("download", file.name);
					document.body.appendChild(link);
					link.click();
					window.URL.revokeObjectURL(link);
					this.$toast.open({
						message: `Tải xuống thành công`,
						type: "success",
						position: "top-right",
						duration: 3000
					});
				});
			}
		},
		deleteOtherFile(file, index) {
			this.openModalDelete = true;
			this.indexDelete = index;
			this.id_file_delete = file.id;
		},
		async handleDelete() {
			const res = await File.deleteFileCertificate(this.id_file_delete);
			if (res.data) {
				this.dataPC.other_documents.splice(this.indexDelete, 1);
				// this.files = this.dataPC.files
				this.$toast.open({
					message: "Xóa thành công",
					type: "success",
					position: "top-right",
					duration: 3000
				});
			} else if (res.error) {
				this.$toast.open({
					message: res.error.message,
					type: "error",
					position: "top-right",
					duration: 3000
				});
			}
		},
		getTotalPrice() {
			let total_price = 0;
			this.dataPC.general_asset.forEach(item => {
				total_price += +item.total_price;
			});
			this.total_price_appraise = total_price;
		},
		changeEditStatus() {
			let dataJson = this.jsonConfig.principle.filter(
				item => item.status === this.dataPC.status && item.isActive === 1
			);
			if (dataJson && dataJson.length > 0) {
				this.config = dataJson[0];
				this.editAppraiser = dataJson[0].edit.appraiser
					? dataJson[0].edit.appraiser
					: false;
				this.editItemList = dataJson[0].edit.appraise_item_list
					? dataJson[0].edit.appraise_item_list
					: false;
				this.editInfo = dataJson[0].edit.info ? dataJson[0].edit.info : false;
				this.printConfig = dataJson[0].print;
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
		checkDiffVersion() {
			let message = "";
			if (
				this.config.check_version &&
				this.config.check_version &&
				this.checkVersion.length > 0
			) {
				message =
					"Sai version. Bạn cần cập nhật lại version trước khi chuyển trạng thái.";
			}
			return message;
		},
		viewAppraiseListVersion() {
			this.isShowAppraiseListVersion = true;
		},
		setDocumentViewStatus() {
			// console.log('this.dataPC.document_type',this.dataPC.document_type)
			let isExportAutomatic = true;
			let isCheckRealEstate = true;
			let isCheckConstruction = false;
			let isApartment = false;
			if (this.dataPC.document_type && this.dataPC.document_type.length > 0) {
				if (
					this.dataPC.document_type.filter(function(item) {
						return item !== "DCN" && item !== "DT" && item !== "CC";
					}).length > 0
				) {
					isCheckRealEstate = false;
					isExportAutomatic = false;
				}
				if (
					this.dataPC.document_type.find(i => i === "CC") &&
					(this.dataPC.document_type.find(i => i === "DCN") ||
						this.dataPC.document_type.find(i => i === "DT"))
				) {
					isExportAutomatic = false;
				}
				if (
					this.dataPC.document_type.length === 1 &&
					this.dataPC.document_type.find(i => i === "CC")
				) {
					isApartment = true;
				}
				if (this.dataPC.document_type.find(i => i === "DCN")) {
					isCheckConstruction = true;
				}
			} else {
				isCheckRealEstate = false;
				isExportAutomatic = false;
			}
			if (isCheckRealEstate) {
				this.documentName = [
					"Chứng thư thẩm định",
					"Báo cáo thẩm định",
					"Bảng điều chỉnh QSDĐ",
					"Bảng điều chỉnh CTXD",
					"Hình ảnh hiện trạng",
					"Phiếu thu thập TSSS"
				];
			} else {
				this.documentName = [
					"Chứng thư thẩm định",
					"Báo cáo thẩm định",
					"Phụ lục kèm theo",
					"Phụ lục kèm theo",
					"Phụ lục kèm theo",
					"Phiếu thu thập TSSS"
				];
			}
			this.isCheckRealEstate = isCheckRealEstate;
			this.isCheckConstruction = isCheckConstruction;
			this.isViewAutomationDocument = isExportAutomatic;
			this.isApartment = isApartment;
		},
		downloadDocumentFile(type) {
			let file = this.dataPC.other_documents.find(i => i.description === type);
			if (file) {
				// this.downloadDocument(file)
				this.downloadOtherFile(file);
			} else
				this.openMessage(
					"Không tìm thấy file cần tải. Vui lòng xem refesh lại trang."
				);
		},
		deletedDocumentFile(type) {
			let file = this.dataPC.other_documents.find(i => i.description === type);
			if (file) {
				this.deleteUploadDocument = true;
				this.id_file_delete = file.id;
			} else this.openMessage("Không tìm thấy file cần xóa.");
		},
		async deleteDocument() {
			await Certificate.deleteDocument(this.id_file_delete).then(resp => {
				const file = resp;
				if (file.data) {
					this.dataPC.other_documents = file.data;
					this.openMessage("Xóa thành công.", "success");
				} else if (file.error) this.openMessage(file.error.message);
				else this.openMessage("Không tìm thấy file.");
			});
		},
		async downloadDocument(file) {
			await Certificate.downloadDocument(file.id).then(resp => {
				const file = resp.data;
				if (file) {
					const fileLink = document.createElement("a");
					fileLink.href = file.url;
					fileLink.setAttribute("download", file.file_name);
					document.body.appendChild(fileLink);
					fileLink.click();
					fileLink.remove();
					window.URL.revokeObjectURL(fileLink);
				} else {
					this.$toast.open({
						message: "Tải file bị lỗi vui lòng gọi hỗ trợ",
						type: "error",
						position: "top-right",
						duration: 3000
					});
				}
			});
		}
	},
	beforeMount() {
		if (this.dataPC.general_asset && this.dataPC.general_asset.length > 0) {
			this.getTotalPrice();
		}
		this.setDocumentViewStatus();
	},
	async mounted() {
		if (!this.jsonConfig) {
			if (this.lstData.workflow) {
				this.jsonConfig = this.lstData.workflow;
			} else {
				this.jsonConfig = await this.preCertificateStore.getConfig();
			}
		}
		this.changeEditStatus();
		this.checkVersion = this.checkVersion2;
	},
	watch: {
		"dataPC.document_type": {
			deep: true,
			handler(newValue) {
				this.setDocumentViewStatus();
			}
		}
	}
};
</script>
<style scoped lang="scss">
.div_radio {
	margin-bottom: 0.5rem;
}
.dataPC-map {
	height: 100%;
	flex: 1;
}

.card {
	border-radius: 5px;
	box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
	background: #ffffff;
	margin-bottom: 1rem;

	&-footer {
		padding: 15px 24px;
	}

	&-title {
		padding: 20px 25px 10px;
		margin-bottom: 0;
		color: #e8e8e8;
		border-bottom: 2px solid;
		&__img {
			padding: 8px 20px;
		}
		h3 {
			color: #007ec6;
		}
		@media (max-width: 768px) {
			padding: 12px;
		}

		.title {
			font-size: 1.125rem;
			font-weight: 700;
			margin-bottom: 0;
		}
	}

	&-body {
		@media (max-width: 787px) {
			padding: 15px;
		}
	}

	&-info {
		.title {
			font-size: 1.125rem;
			font-weight: 700;
			margin-top: 28px;

			&-highlight {
				background: rgba(252, 194, 114, 0.53);
				text-align: center;
				padding: 10px 0;
				border-radius: 2px;
			}
		}
	}

	&-land {
		position: relative;
		padding: 0;
	}
}
.card-status {
	border-radius: 5px;
	box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
	background: #ffffff;
	margin-bottom: 10px;
	font-weight: 600;
	padding: 10px;
	font-size: 16px !important;

	@media (max-width: 768px) {
		margin-bottom: 10px;
	}

	@media (max-width: 418px) {
		margin-bottom: 10px;
	}
}

.dataPC-group-container {
	margin-top: 10px;
}

.color-black {
	color: #333333;
}

.img_document_action {
	width: 2rem;
	height: 2rem;
	cursor: pointer;
	background: #ffffff;
	min-width: 1.5rem;
	min-height: 1.5rem;
}
.btn-edit {
	cursor: pointer;
	display: flex;
	border-radius: 5.88235px;
	align-items: end;
	img {
		width: 20px;
		height: 14px;
		height: auto;
	}
}
.btn {
	&-orange {
		background: #faa831;
		text-align: center;
		border-radius: 5px;
		box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25) !important;
		height: 35px;
		// width: 100px;
		color: #fff;
		// margin: 15px 0 0;
		box-sizing: border-box;

		&:hover {
			border-color: #dc8300;
		}
	}
}
.btn-upload {
	left: 0;
	opacity: 0;
	width: 100%;
	min-height: 10rem;
	cursor: pointer;
	// position: absolute;
}
.btn-upload-mini {
	left: 0;
	opacity: 0;
	width: 2rem;
	// min-height: 10rem;
	cursor: pointer;
	// position: absolute;
	padding: 0;
	border: 0;
}
.btn_list_appraise {
	&-orange {
		background: #faa831;
		text-align: center;
		border-radius: 5px;
		box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25) !important;
		height: 35px;
		min-width: 150px;
		color: #fff;
		box-sizing: border-box;

		&:hover {
			border-color: #dc8300;
		}
	}
}

.img-dropdown {
	cursor: pointer;
	width: 18px;

	&__hide {
		transform: rotate(90deg);
		transition: 0.3s;
	}
}

.img-locate {
	cursor: pointer;
	position: absolute;
	right: 14px;
	top: 2.1rem;
	background-color: #f5f5f5;
	height: 2.1rem;
	width: 32px;
	display: grid;
	place-items: center;

	img {
		height: 60%;
	}
}

.text-error {
	color: #cd201f;
}

.select-group {
	background-color: #f6f7fb;
	border: 1px solid #e8e8e8;
	border-radius: 3px;
	padding: 16px 22px;

	.select-title {
		color: #00507c;
		font-weight: 700;
		white-space: nowrap;
		margin-bottom: unset !important;
	}
}

.content_form {
	padding-left: 0.75rem;
}
.border_disable {
	border-color: #d9d9d9 !important;
}
.detail_pre_certification {
	// padding: 0 1rem;
	margin-bottom: 60px;
	@media (max-width: 449px) {
		margin-bottom: 120px;
	}
}
.detail_certificate_1 {
	padding: 0.75rem;
	border-radius: 5px;
	border: 1px solid #b5e5ff;
	background-color: #eef9ff;
}
.detail_certificate_2 {
	padding: 0.75rem;
	border-radius: 5px;
	border: 1px solid #e8e8e8;
	background-color: #f6f7fb;
}
.margin_content_inline {
	margin-right: 10px;
}
.container_content {
	min-height: 20px;
	p {
		margin-bottom: unset !important;
	}
}

.btn {
	&-history {
		position: fixed;
		right: 0;
		top: 170px;
		z-index: 100;
		border-radius: 5px 0 0 5px;
		padding: 0.5rem 0.3rem;
	}
}
.input_download_certificate {
	position: relative;
	border: 1px solid #b6d5f3;
	border-radius: 5px;
	height: 3.85rem;
	padding: 0.85rem 0px;
}
.title_input_download {
	color: #00507c;
	font-weight: 600;
}
.img_input_download {
	margin-right: 10px;
	max-width: 2rem;
}

.title_input_content {
	font-size: 18px;
}
.input_upload_file {
	background-image: repeating-linear-gradient(
			0deg,
			#b6d5f3,
			#b6d5f3 10px,
			transparent 10px,
			transparent 20px,
			#b6d5f3 20px
		),
		repeating-linear-gradient(
			90deg,
			#b6d5f3,
			#b6d5f3 10px,
			transparent 10px,
			transparent 20px,
			#b6d5f3 20px
		),
		repeating-linear-gradient(
			180deg,
			#b6d5f3,
			#b6d5f3 10px,
			transparent 10px,
			transparent 20px,
			#b6d5f3 20px
		),
		repeating-linear-gradient(
			270deg,
			#b6d5f3,
			#b6d5f3 10px,
			transparent 10px,
			transparent 20px,
			#b6d5f3 20px
		);
	background-size: 2px 100%, 100% 2px, 2px 100%, 100% 2px;
	background-position: 0 0, 0 0, 100% 0, 0 100%;
	background-repeat: no-repeat;
	// border: dotted 1px solid #B6D5F3;
	cursor: pointer;
	min-height: 8rem;
	border-radius: 5px;
}
.img-upload {
	margin-left: 20px;
	position: relative;
	width: 123px;
	height: 35px;
	color: #fff;
	background: #faa831;
	font-size: 1.125rem;
	border-radius: 5px;
	box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
	display: flex;
	justify-content: center;
	align-items: center;
	box-sizing: border-box;
	cursor: pointer;
	input {
		cursor: pointer !important;
		position: absolute;
		top: 0;
		left: 0;
		bottom: 0;
		width: 100%;
		opacity: 0;
	}
}
.table-wrapper {
	.ant-table-filter-dropdown-btns {
		background-color: white !important;
	}

	.ant-table-filter-dropdown-link.confirm {
		color: red;
	}

	/deep/ .ant-table-thead > tr > th {
		font-weight: 700 !important;
		background-color: #dee6ee !important;
		color: #3d4d65 !important;
		// border-right: 1px solid white !important;
	}
	/deep/ .ant-table-wrapper .ant-spin-container .ant-table {
		border: 1px solid #dee6ee;
	}

	/deep/ .ant-table-column-title {
		color: #00507c;
	}

	/deep/ .table-striped td {
		background-color: #f6f7fb;
		border-color: #dee6ee;
		border-width: 0;
	}

	/deep/ .ant-table-tbody,
	/deep/ .ant-table-body {
		box-shadow: none;
		min-height: 10vh;
	}
	/deep/ .ant-table-pagination {
		display: none;
	}

	.pagination-wrapper {
		margin-top: 16px;
		display: flex;
		justify-content: space-between;
		align-items: center;

		.ant-select {
			margin-left: 11px;
			margin-right: 11px;
		}

		.page-size {
			display: flex;
			align-items: center;
			margin-right: 20px;
		}

		.ant-pagination {
			flex-grow: 1;
			display: flex;
			align-items: center;
			justify-content: center;
			flex-wrap: wrap;
			row-gap: 10px;

			/deep/ .ant-pagination-total-text {
				height: unset;
				flex-grow: 1;

				@media (max-width: 1024px) {
					width: 100%;
					text-align: center;
					margin-bottom: 20px;
				}
			}

			/deep/ .ant-pagination-item-active {
				background: #007ec6;

				a {
					color: #ffffff;
				}
			}

			/deep/ .ant-pagination-prev,
			/deep/ .ant-pagination-next {
				border: 1px solid #d9d9d9;

				&:hover {
					border-color: #1890ff;
					transition: all 0.3s;
				}

				a:hover {
					i {
						color: #1890ff;
					}
				}
			}
		}

		@media (max-width: 1024px) {
			flex-direction: column;
			gap: 20px;
		}
	}
}

.dot-image {
	width: 2em;
	border-radius: 2em;
	height: 2em;
	object-fit: cover;
}
/deep/ .ant-timeline-item-content {
	margin-left: 25px;
	p {
		margin-bottom: 0.2em;
	}
}
/deep/ .ant-timeline-item-tail {
	border-left: 2px solid #26bf5fad;
}
.text-none {
	text-transform: none !important;
}
.btn_group {
	@media (max-width: 767px) {
		width: 100%;
	}
	/deep/ .btn-secondary {
		background-image: none;
		border-color: none !important;
		box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25) !important;
	}
}
.container_total {
	margin-left: 2rem;
	padding: 6px;
	border: 1px solid;
	color: #007ec6;
	font-weight: 600;
	border-radius: 5px;
	background-color: #eef9ff;
}
.total {
	color: #007ec6;
	font-weight: 700;
	font-size: 1.2rem;
}
.full-address {
	width: 200px;
	white-space: nowrap;
	-webkit-line-clamp: 2 !important;
	overflow: hidden;
	text-overflow: ellipsis;
	margin-bottom: 0;
	text-transform: none;

	&:first-letter {
		text-transform: none;
	}
}
.link-detail {
	white-space: nowrap;
	text-transform: uppercase;
	background: transparent;
	border: none;
	cursor: pointer;
	&:hover,
	&:focus,
	&:active {
		color: #faa831;
		border: none;
		outline: none;
	}
}
.btn_dropdown {
	border: white;
	border-radius: 5px;
	height: 35px;
	@media (max-width: 767px) {
		margin-top: 10px;
	}
}
.infor-box {
	padding: 1rem;
	border-radius: 12px 15px;
	background-color: #eef9ff;
	border: 1px solid #007ec6;
	color: #446b92;
	@media (max-height: 660px) {
		font-size: 12px;
	}
	@media (max-height: 970px) and (min-height: 660px) {
	}
	.row_hidden {
		visibility: hidden;
	}
}
.cursor_pointer {
	cursor: pointer;
}
.title_color {
	color: lightgray;
}
.img_filter {
	filter: grayscale(100%) invert(100%);
}
</style>
