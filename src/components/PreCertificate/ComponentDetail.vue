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
						<div class="row">
							<div class=" color_content card-status-pre-certificate">
								{{ dataPC.id ? `YCSB_${dataPC.id}` : "YCSB" }} |
								<span v-if="dataPC.status === 1">Yêu cầu sơ bộ</span>
								<span v-if="dataPC.status === 2">Định giá sơ bộ</span>
								<span v-if="dataPC.status === 3">Duyệt giá sơ bộ</span>
								<span v-if="dataPC.status === 4">Thương thảo</span>
								<span v-if="dataPC.status === 5">Hoàn thành</span>
								<span v-if="dataPC.status === 6">Hủy</span>
							</div>
							<div
								v-if="dataPC.certificate_id"
								@click="handleDetailCertificate(dataPC.certificate_id)"
								class=" card-status-certificate ml-3"
								id="certificate_id"
							>
								<icon-base
									name="nav_hstd"
									width="20px"
									height="20px"
									class="item-icon svg-inline--fa"
								/>
								{{ `HTSD_${dataPC.certificate_id}` }}
								<b-tooltip target="certificate_id" placement="top-right">{{
									`Đã chuyển chính thức HTSD_${dataPC.certificate_id}`
								}}</b-tooltip>
							</div>
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
									<strong class="margin_content_inline"
										>Mục đích thẩm định:</strong
									><span id="appraise_purpose" class="text-left">{{
										dataPC.appraise_purpose
											? dataPC.appraise_purpose.name.length > 60
												? dataPC.appraise_purpose.name.substring(60, 0) + "..."
												: dataPC.appraise_purpose.name
											: ""
									}}</span>
									<b-tooltip
										v-if="dataPC.appraise_purpose"
										target="appraise_purpose"
										placement="top-right"
										>{{ dataPC.appraise_purpose.name }}</b-tooltip
									>
								</div>

								<div class="d-flex container_content">
									<strong class="margin_content_inline">Loại sơ bộ:</strong>
									<p>
										{{ dataPC.pre_type ? dataPC.pre_type.description : "" }}
									</p>
								</div>
								<div class="d-flex container_content">
									<strong class="margin_content_inline"
										>Thời điểm sơ bộ:</strong
									>
									<p>
										{{ dataPC.pre_date ? formatDate(dataPC.pre_date) : "" }}
									</p>
								</div>
								<div class="d-flex container_content">
									<strong class="margin_content_inline"
										>Tổng phí dịch vụ:</strong
									>
									<p>
										{{
											dataPC.total_service_fee
												? formatNumber(dataPC.total_service_fee)
												: 0
										}}đ
									</p>
								</div>
								<div class="d-flex container_content">
									<strong class="margin_content_inline ">Chiết khấu:</strong>
									<p>
										{{ dataPC.commission_fee ? dataPC.commission_fee : 0 }}%
									</p>
								</div>
								<div
									v-if="dataPC.status === 1"
									class="d-flex container_content"
								>
									<strong class="margin_content_inline"
										>Tên tài sản sơ bộ:</strong
									><span id="pre_asset_name" class="text-left">{{
										dataPC.pre_asset_name && dataPC.pre_asset_name.length > 25
											? dataPC.pre_asset_name.substring(25, 0) + "..."
											: dataPC.pre_asset_name
									}}</span>
									<b-tooltip target="pre_asset_name" placement="top-right">{{
										dataPC.pre_asset_name
									}}</b-tooltip>
								</div>
								<div
									v-if="dataPC.cancel_reason_string"
									class="d-flex container_content"
								>
									<strong class="margin_content_inline"
										>Lý do hủy sơ bộ:</strong
									>
									<p>
										{{ dataPC.cancel_reason_string }}
									</p>
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
		<PaymentHistories @updatePayments="updatePayments" />
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
			v-if="dataPC.id && dataPC.status >= 2"
			class="col-12"
			:style="isMobile ? { padding: '0' } : {}"
		>
			<div class="card">
				<div class="card-title">
					<div class="d-flex justify-content-between align-items-center">
						<div class="row d-flex justify-content-between align-items-center">
							<h3 class="title">Kết quả sơ bộ</h3>
						</div>
						<div
							v-if="allowEditFile.result && edit"
							@click="dialogRequireForStage3 = true"
							class="btn-edit "
						>
							<img src="@/assets/icons/ic_edit_3.svg" alt="add" />
						</div>
					</div>
				</div>
				<div class="card-body card-info">
					<div class="row">
						<div class="col-12 mt-2 table-wrapper">
							<a-table
								bordered
								:columns="dataColumn"
								:data-source="computedResultPreCertificate"
								table-layout="top"
								class="table_appraise_list"
								:rowKey="record => record.id"
							>
								<template slot="asset" slot-scope="asset">
									<p :id="asset.id" class="text-none mb-0">{{ asset.name }}</p>
								</template>
								<template
									slot="total_preliminary_value"
									slot-scope="total_preliminary_value"
								>
									<p class="text-none mb-0">
										{{
											total_preliminary_value
												? formatNumber(total_preliminary_value)
												: 0
										}}
										đ
									</p>
								</template>
							</a-table>
						</div>
					</div>
				</div>
				<OtherFile
					class="ml-2 mt-n3"
					v-if="showCardDetailFileResult && !dialogRequireForStage3"
					type="Result"
					:allow-edit="false"
				/>
			</div>
		</div>
		<!-- <div
			v-if="dataPC.id && dataPC.status >= 2"
			class="col-6"
			:style="isMobile ? { padding: '0' } : {}"
		>
			<div class="card">
				<div class="card-title">
					<div class="d-flex justify-content-between align-items-center">
						<div class="row d-flex justify-content-between align-items-center">
							<h3 class="title">Thông tin thanh toán</h3>
						</div>
						<div
							v-if="editPayments && edit"
							@click="handleshowCardPCPayments"
							class="btn-edit "
						>
							<img src="@/assets/icons/ic_edit_3.svg" alt="add" />
						</div>
					</div>
				</div>
				<div class="row">
					<div class="card-body card-info ml-2">
						<div
							class="row mb-1 "
							v-if="dataPC.payments"
							v-for="(payment, index) in dataPC.payments"
							:key="index"
						>
							<div class="d-flex container_content">
								<strong class="margin_content_inline">Ngày thanh toán:</strong>
								<p>
									{{ payment.pay_date ? payment.pay_date : "" }}
								</p>
							</div>
							<div class="d-flex container_content">
								<strong class="margin_content_inline"
									>Tổng giá trị thanh toán:</strong
								>
								<p>{{ payment.amount ? formatNumber(payment.amount) : 0 }}đ</p>
							</div>
						</div>
						<div class="d-flex container_content">
							<strong class="margin_content_inline">Đã thanh toán:</strong>
							<p>
								{{ dataPC.amountPaid ? formatNumber(dataPC.amountPaid) : 0 }}đ
							</p>
						</div>
						<div class="d-flex container_content">
							<strong class="margin_content_inline">Còn nợ:</strong>
							<p>
								{{ dataPC.debtRemain ? formatNumber(dataPC.debtRemain) : 0 }}đ
							</p>
						</div>
					</div>
				</div>
			</div>
		</div> -->
		<div
			v-if="dataPC.id"
			class="col-12"
			:style="isMobile ? { padding: '0' } : {}"
		>
			<OtherFile
				:type="'Appendix'"
				:allow-edit="allowEditFile.appendix"
				:from-component="'Detail'"
			/>
		</div>

		<Footer
			v-if="jsonConfig && profile && dataPC && dataPC.id"
			:style="isMobile ? { bottom: '60px' } : {}"
			:key="dataPC.status"
			:form="dataPC"
			:jsonConfig="jsonConfig"
			:status="dataPC.status"
			:profile="profile"
			:idData="dataPC.id"
			:checkVersion="checkVersion"
			:certificateId="dataPC.certificate_id"
			@handleFooterAccept="handleFooterAccept"
			@handleEdit="handleEdit"
			@onCancel="onCancel"
			@viewAppraiseListVersion="viewAppraiseListVersion"
		/>
		<ModalPCAppraisal
			:key="key_render_appraisal"
			v-if="showAppraisalDialog"
			@cancel="showAppraisalDialog = false"
			@updateAppraisal="updateAppraisal"
		/>

		<ModalPCAppraiseInfomation
			v-if="showAppraiseInformationDialog"
			@cancel="showAppraiseInformationDialog = false"
			@updateAppraiseInformation="updateAppraiseInformation"
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
		<ModalNotificationPreCertificateNote
			v-if="isHandleAction"
			@cancel="isHandleAction = false"
			:notification="
				message == 'Từ chối' || message == 'Khôi phục' || message == 'Hủy'
					? `Bạn có muốn '${message}' hồ sơ này?`
					: `Bạn có muốn chuyển yêu cầu này sang trạng thái '${message}'`
			"
			@action="handleAction2"
		/>

		<ModalDelete
			v-if="deleteUploadDocument"
			@cancel="deleteUploadDocument = false"
			@action="deleteDocument"
		/>
		<ModalRequireForStage3
			v-if="dialogRequireForStage3"
			:notification="
				`Bạn có muốn chuyển yêu cầu này sang trạng thái 'Định giá sơ bộ'?`
			"
			@cancel="dialogRequireForStage3 = false"
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
import ModalNotificationPreCertificateNote from "@/components/PreCertificate/ModalNotificationPreCertificateNote";

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
import PreCertificate from "@/models/PreCertificate";
import WareHouse from "@/models/WareHouse";
import { Timeline, Drawer } from "ant-design-vue";
import moment from "moment";
import ModalCustomer from "@/components/PreCertificate/ModalCustomer";
import ModalPCAppraisal from "@/components/PreCertificate/ModalPCAppraisal";
import PaymentHistories from "@/components/PreCertificate/PaymentHistories";
import ModalPCAppraiseInfomation from "@/components/PreCertificate/ModalPCAppraiseInfomation";
import ModalRequireForStage3 from "@/components/PreCertificate/ModalRequireForStage3";
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
import IconBase from "./../IconBase.vue";

Vue.use(Icon);
export default {
	props: {
		routeId: {
			type: String
		}
	},
	name: "detail_pre_certification",
	components: {
		PaymentHistories,
		IconBase,
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
		ModalPCAppraisal,
		ModalPCAppraiseInfomation,
		"b-tooltip": BTooltip,
		ModalNotificationCertificate,
		ModalViewDocument,
		ModalDelete,
		"b-dropdown-item": BDropdownItem,
		"b-button-group": BButtonGroup,
		"b-dropdown": BDropdown,
		Footer,
		ModalNotificationPreCertificateNote,
		ModalRequireForStage3
	},
	data() {
		return {
			dataColumn: [
				{
					title: "Tên tài sản sơ bộ",
					align: "left",
					scopedSlots: { customRender: "asset" },
					hiddenItem: false
				},

				{
					title: "Tổng giá trị",
					align: "right",
					scopedSlots: { customRender: "total_preliminary_value" },
					dataIndex: "total_preliminary_value",
					hiddenItem: false
				}
			],
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
			cancel_certificate: false,
			openNotificationDenined: false,
			filePrint: "",
			isShowPrint: false,
			title: "",
			indexDelete: "",
			id_file_delete: "",

			showDetailAppraise: false,
			dataDetailAppraise: [],
			appraiser_number: "",
			historyList: [],
			isCheckRealEstate: true,
			isCheckConstruction: false,
			isViewAutomationDocument: true,
			targetStatus: "",
			isHandleAction: false,
			checkVersion: true,
			typeAppraiseProperty: [],
			isShowAppraiseListVersion: false,
			isCheckPrice: false,
			isCheckVersion: false,
			isCheckLegal: false,
			changeStatusRequire: {},
			isApartment: false,
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
		const {
			dataPC,
			lstDataConfig,
			preCertificateOtherDocuments,
			jsonConfig,
			vueStoree,
			other
		} = storeToRefs(preCertificateStore);
		const dialogRequireForStage3 = ref(false);
		const config = ref({});
		const editInfo = ref(false);
		const editAppraiser = ref(false);
		const editPayments = ref(false);
		const allowEditFile = ref({ appendix: false, result: false });
		const changeEditStatus = async () => {
			let dataJson = jsonConfig.value.principle.filter(
				item => item.status === dataPC.value.status && item.isActive === 1
			);
			if (dataJson && dataJson.length > 0) {
				config.value = dataJson[0];

				const checkPermissionObject = {
					form: false,
					info: false,
					file_appendix: false,
					file_result: false,
					payments: false,
					appraiser: false
				};
				for (const key of Object.keys(checkPermissionObject)) {
					checkPermissionObject[key] = await checkPermssionRequire(key);
				}

				editAppraiser.value =
					checkPermissionObject.appraiser && dataJson[0].edit.appraiser
						? dataJson[0].edit.appraiser
						: false;

				editInfo.value =
					checkPermissionObject.info && dataJson[0].edit.info
						? dataJson[0].edit.info
						: false;
				editPayments.value =
					checkPermissionObject.payments && dataJson[0].edit.payments
						? dataJson[0].edit.payments
						: false;
				allowEditFile.value.appendix =
					checkPermissionObject.file_appendix && dataJson[0].edit.file_appendix
						? dataJson[0].edit.file_appendix
						: false;
				allowEditFile.value.result =
					checkPermissionObject.file_result && dataJson[0].edit.file_result
						? dataJson[0].edit.file_result
						: false;

				const tempPermission = {
					edit: edit.value,
					editPayments: editPayments.value
				};
				preCertificateStore.updatePermission(tempPermission);
			}
		};

		const checkPermssionRequire = key => {
			const permissionAllowEdit = jsonConfig.value.permissionAllowEdit;
			const user = vueStoree.value.user;
			if (permissionAllowEdit[key]) {
				for (let index = 0; index < permissionAllowEdit[key].length; index++) {
					const element = permissionAllowEdit[key][index];
					if (
						(element === "created_by" && dataPC.value.created_by === user.id) ||
						(element !== "created_by" &&
							dataPC.value[element] === user.appraiser.id)
					) {
						return true;
					}
				}
			}
			return false;
		};
		const profile = ref(null);
		const view = ref(false);
		const add = ref(false);
		const edit = ref(false);
		const deleted = ref(false);
		const accept = ref(false);
		const checkRole = ref(false);
		const exportAction = ref(false);
		const user_id = ref("");

		const permissionFunction = async () => {
			profile.value = vueStoree.value.profile;
			user_id.value = vueStoree.value.user.id;
			if (user_id.value === dataPC.value.created_by) {
				checkRole.value = true;
			}
			const permission = vueStoree.value.currentPermissions;
			permission.forEach(value => {
				if (value === "VIEW_PRE_CERTIFICATE") {
					view.value = true;
				}
				if (value === "ADD_PRE_CERTIFICATE") {
					add.value = true;
				}
				if (value === "EDIT_PRE_CERTIFICATE") {
					edit.value = true;
				}
				if (value === "DELETE_PRE_CERTIFICATE") {
					deleted.value = true;
				}
				if (value === "ACCEPT_PRE_CERTIFICATE") {
					accept.value = true;
				}
				if (value === "EXPORT_PRE_CERTIFICATE") {
					exportAction.value = true;
				}
			});
			if (!view.value) {
				other.value.router.push({ name: "page-not-found" });
				other.value.toast.open({
					message: "Bạn ko có quyền xem yêu cầu sơ bộ",
					type: "error",
					position: "top-right",
					duration: 5000
				});
			}
		};
		const start = async () => {
			if (!jsonConfig.value) {
				jsonConfig.value = await preCertificateStore.getConfig();
			}
			await preCertificateStore.resetData();

			dataPC.value = await preCertificateStore.getPreCertificate(props.routeId);
			await permissionFunction();
			await changeEditStatus();
		};

		start();
		const checkVersion2 = ref([]);
		const showCardDetailFileResult = ref(true);
		const showCardPCPayments = ref(false);

		return {
			showCardPCPayments,
			allowEditFile,
			jsonConfig,
			config,
			editAppraiser,
			editInfo,
			editPayments,
			dialogRequireForStage3,
			isMobile,
			dataPC,
			lstDataConfig,
			preCertificateOtherDocuments,
			preCertificateStore,
			checkVersion2,
			profile,
			view,
			add,
			edit,
			deleted,
			accept,
			checkRole,
			exportAction,
			user_id,

			showCardDetailFileResult,
			changeEditStatus
		};
	},

	created() {},
	computed: {
		getHistoryTextColor() {
			return this.historyList.map(item => {
				return this.loadColor(item);
			});
		},
		computedResultPreCertificate() {
			return [
				{
					name: this.dataPC.pre_asset_name,
					total_preliminary_value: this.dataPC.total_preliminary_value
				}
			];
		}
	},
	methods: {
		handleshowCardPCPayments() {
			if (this.dataPC.total_service_fee > 0) this.showCardPCPayments = true;
			else {
				this.$toast.open({
					message: "Vui lòng bổ sung tổng phí dịch vụ",
					type: "error",
					position: "top-right",
					duration: 5000
				});
			}
		},
		getReport(type) {
			let report = this.dataPC.other_documents.find(
				i => i.description === type
			);
			return report;
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
		handleDetailCertificate(id) {
			let url = this.$router.resolve({
				name: "certification_brief.detail",
				query: {
					id: id.toString()
				}
			}).href;

			window.open(url, "_blank");
		},
		async getHistoryTimeLine() {
			const res = await PreCertificate.getHistoryTimeline(this.dataPC.id);
			if (res.data) {
				const resp = await WareHouse.getDictionaries();
				if (resp) {
					this.historyList = res.data;
					for (let i = 0; i < this.historyList.length; i++) {
						let e = this.historyList[i];
						if (e.properties.reason_id) {
							let result = null;
							if (e.description.includes("Hủy")) {
								result = resp.data.li_do_huy_so_bo.filter(
									item => item.id === e.properties.reason_id
								);
							} else {
								result = resp.data.li_do.filter(
									item => item.id === e.properties.reason_id
								);
							}

							e.reason_description = result[0].description;
						}
					}
				}
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

		handleShowAppraisal() {
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
		async updatePayments() {
			await this.preCertificateStore.getPreCertificate(this.routeId);
		},
		async updateAppraiseInformation() {
			await this.preCertificateStore.getPreCertificate(this.routeId);
		},
		async updateAppraisal() {
			await this.preCertificateStore.getPreCertificate(this.routeId);
			await this.changeEditStatus();
			this.key_render_appraisal += 1;
			this.showAppraisalDialog = false;
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
			}
			return message;
		},
		handleFooterAccept(target) {
			if (target.code && target.code === "chuyen_chinh_thuc") {
				if (
					!this.preCertificateOtherDocuments.Result ||
					this.preCertificateOtherDocuments.Result.length === 0 ||
					this.dataPC.total_preliminary_value == 0 ||
					this.dataPC.total_preliminary_value == null ||
					this.dataPC.total_preliminary_value == undefined
				) {
					this.openMessage(
						"Vui lòng bổ sung file kết quả sơ bộ và Tổng giá trị sơ bộ",
						"error"
					);
					return;
				}
				this.dataPC.target_code = target.code;
				this.message = target.description;
				this.isHandleAction = true;
				return;
			}
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
					this.dataPC.target_status = config.status;
					this.dataPC.target_code = target.code;
					this.message = target.description;

					if (
						this.dataPC.status < this.targetStatus &&
						this.targetStatus == 3
					) {
						if (
							!this.preCertificateOtherDocuments.Result ||
							this.preCertificateOtherDocuments.Result.length === 0 ||
							this.dataPC.total_preliminary_value == 0 ||
							this.dataPC.total_preliminary_value == null ||
							this.dataPC.total_preliminary_value == undefined
						) {
							this.openMessage(
								"Vui lòng bổ sung file kết quả sơ bộ và Tổng giá trị sơ bộ"
							);
							return;
						}
					}

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
		async updateToOffical(note) {
			const res = await PreCertificate.updateToOfficalPreCertificate(
				this.dataPC.id,
				{ note }
			);
			if (res.data && res.data.error === false) {
				await this.preCertificateStore.getPreCertificate(this.routeId);
				this.changeEditStatus();
				await this.$toast.open({
					message: this.message + " thành công",
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
		async handleAction2(note, reason_id) {
			if (this.dataPC.target_code == "chuyen_chinh_thuc") {
				this.updateToOffical(note);
				return;
			}
			const res = await this.preCertificateStore.updateStatus(
				this.dataPC.id,
				note,
				reason_id
			);

			if (res.data) {
				// this.dataPC.status = this.targetStatus;
				await this.preCertificateStore.getPreCertificate(this.routeId);

				this.changeEditStatus();
				this.$toast.open({
					message:
						this.message == "Từ chối" ||
						this.message == "Khôi phục" ||
						this.message == "Hủy"
							? this.message + " thành công"
							: "Chuyển trạng thái " + `'${this.message}'` + " thành công",
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
			if (this.config.check_version && this.checkVersion.length > 0) {
				message =
					"Sai version. Bạn cần cập nhật lại version trước khi chuyển trạng thái.";
			}
			return message;
		},
		viewAppraiseListVersion() {
			this.isShowAppraiseListVersion = true;
		},
		setDocumentViewStatus() {
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
.card-status-pre-certificate {
	border-radius: 5px;
	background: #ffffff;
	margin-bottom: 10px;
	font-weight: 600;
	padding: 10px;
	font-size: 16px !important;
	border: 1px solid #000000;
	@media (max-width: 768px) {
		margin-bottom: 10px;
	}

	@media (max-width: 418px) {
		margin-bottom: 10px;
	}
}
.card-status-certificate {
	border-radius: 5px;
	box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
	background: #ffffff;
	margin-bottom: 10px;
	font-weight: 600;
	padding: 10px;
	font-size: 16px !important;
	color: darkgray;
	cursor: pointer;

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
		top: 210px;
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
