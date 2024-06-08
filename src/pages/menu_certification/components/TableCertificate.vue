<template>
	<div v-if="!isMobile()" class="table-wrapper">
		<div class="table-detail position-relative empty-data">
			<a-table
				bordered
				class="table-property"
				@change="handleTableChange"
				:columns="columns"
				:data-source="listCertificates"
				:loading="isLoading"
				:rowKey="record => record.id"
				:filtered="false"
				:row-class-name="
					(_record, index) => (index % 2 === 1 ? 'table-striped' : null)
				"
				:pagination="false"
			>
				<!--Custom type table-->
				<template slot="id" slot-scope="id, property">
					<div class="position-relative">
						<p class="text-main">
							{{ "HSTD_" + id }}
						</p>
					</div>
				</template>
				<template slot="status" slot-scope="status, data">
					<div
						class="d-flex justify-content-center align-items-center position-relative"
					>
						<p class="text-main">
							{{ getStatusDescription(status) }}
						</p>
						<!-- <div
							:id="(data.id + 'status').toString()"
							:class="['status-color', 'bg-' + getStatusColor(status)]"
						/>
						<b-tooltip :target="(data.id + 'status').toString()">
							{{ getStatusDescription(status) }}
						</b-tooltip> -->
						<!-- <b-dropdown class="dropdown-container" no-caret>
							<template #button-content>
								<img src="@/assets/icons/ic_more.svg" alt="" />
							</template>
							<b-dropdown-item>Action</b-dropdown-item>
						</b-dropdown> -->
					</div>
				</template>
				<template slot="created_at" slot-scope="created_at">
					<p class="public_date">
						{{ formatDate(created_at) }}
					</p>
				</template>
				<template
					slot="certificate_date"
					slot-scope="{ certificate_date, certificate_num }"
				>
					<p class="text-main">
						{{ certificate_num }}
					</p>
					<p class="text-secondary">
						{{
							certificate_date ? "Ngày: " + formatDate(certificate_date) : ""
						}}
					</p>
				</template>
				<template
					slot="document_date"
					slot-scope="{ document_date, document_num }"
				>
					<p class="text-main">
						{{ document_num }}
					</p>
					<p class="text-secondary">
						{{ document_date ? "Ngày: " + formatDate(document_date) : "" }}
					</p>
				</template>
				<template slot="detail_appraise" slot-scope="{ real_estate }">
					<div v-for="(item, index) in real_estate" :key="index">
						<p :id="`content${item.id}`" class="appraise_detail text-none mb-2">
							<span v-html="showDetailAppraise(item)"></span>
						</p>
						<div
							v-if="
								item.apartment &&
									item.apartment.law &&
									item.apartment.law.length > 0
							"
							v-for="(law, index) in item.apartment ? item.apartment.law : []"
							:key="law.id"
						>
							<p class="text-secondary text-wrap">
								Chủ sở hữu:
								{{
									item.apartment.law.lemgth > 1
										? "BDS_" + item.real_estate_id + ": "
										: ""
								}}{{ law.legal_name_holder }}
							</p>
						</div>
						<div
							v-if="
								item.appraises &&
									item.appraises.certificate_appraise_law &&
									item.appraises.certificate_appraise_law.length > 0
							"
							v-for="(law, index) in item.appraises
								? item.appraises.certificate_appraise_law
								: []"
							:key="law.id"
						>
							<p class="text-secondary text-wrap">
								Chủ sở hữu:
								{{
									item.appraises.certificate_appraise_law.lemgth > 1
										? "BDS_" + item.real_estate_id + ": "
										: ""
								}}{{ law.legal_name_holder }}
							</p>
						</div>
					</div>
				</template>
				<template slot="appraised_asset" slot-scope="detail_appraise">
					<p
						:id="`content_appraised_asset`"
						class="appraised_asset text-none text-wrap"
						style="width: 360px !important;"
					>
						<span v-html="showAddressAppraise(detail_appraise)"></span>
					</p>
				</template>
				<template slot="total_asset_price" slot-scope="props">
					<p class="text-main__blue" v-if="isShowPrice(props)">
						{{
							props.total_price ? formatNumber(props.total_price) + " đ" : "-"
						}}
					</p>
					<!-- <p
						:class="
							isShowPrice(props)
								? 'text-main__blue'
								: 'text-secondary d-inline-block text-truncate'
						"
						style="max-width: 220px"
						:id="`content_appraise_purpose_${props.id}`"
					>
						Mục đích:
						{{ props.appraise_purpose ? props.appraise_purpose.name : "-" }}
					</p>
					<b-tooltip
						v-if="props.appraise_purpose"
						:target="('content_appraise_purpose_' + props.id).toString()"
						placement="top"
						triggers="hover"
						>{{
							props.appraise_purpose ? props.appraise_purpose.name : ""
						}}</b-tooltip
					> -->
				</template>
				<template
					slot="petitioner_name"
					slot-scope="{ petitioner_name, customer }"
				>
					<p class="name_detail text-main text-wrap">
						{{ petitioner_name }}
					</p>
					<!-- <p class="name_detail text-secondary text-wrap">
						Đối tác: {{ customer ? customer.name : " " }}
					</p> -->
				</template>
				<template
					slot="customer_group"
					slot-scope="{ customer_group, customer }"
				>
					<p class="name_detail text-main text-wrap">
						{{ customer_group ? customer_group.description : "" }}
					</p>
					<p class="name_detail text-secondary text-wrap">
						{{ customer ? customer.name : " " }}
					</p>
				</template>
				<!-- <template slot="appraise_land_sum_area" slot-scope="appraise_land_sum_area">
          <p class="text-none">
            {{
                appraise_land_sum_area ? formatNumber(appraise_land_sum_area) : 0
            }}
            m
            <sup>2</sup>
          </p>
        </template> -->
				<!-- <template slot="total_construction_area" slot-scope="total_construction_area">
          <p class="text-none">
            {{
                total_construction_area
                  ? formatNumber(total_construction_area)
                  : 0
            }}
            m
            <sup>2</sup>
          </p>
        </template> -->
				<template slot="created_by" slot-scope="{ created_by, created_at }">
					<p class="text-main">
						{{ created_by ? created_by.name : " " }}
					</p>
					<p class="text-secondary">
						Ngày tạo: {{ created_at ? formatDate(created_at) : " " }}
					</p>
				</template>
				<template
					slot="appraiser"
					slot-scope="{ appraiser, appraiser_perform }"
				>
					<p class="text-main">
						CV: {{ appraiser_perform ? appraiser_perform.name : "-" }}
					</p>
					<p class="text-secondary">
						TĐV: {{ appraiser ? appraiser.name : "-" }}
					</p>
				</template>
			</a-table>
			<div class="pagination-wrapper">
				<div class="page-size">
					Hiển thị
					<a-select
						ref="select"
						:value="Number(pagination.pageSize)"
						style="width: 71px"
						:options="pageSizeOptions"
						@change="onSizeChange"
					/>
					hàng
				</div>
				<a-pagination
					:current="Number(pagination.current)"
					:page-size="Number(pagination.pageSize)"
					:total="Number(pagination.total)"
					:show-total="
						(total, range) =>
							`Kết quả hiển thị ${range[0]} - ${range[1]} của ${pagination.total} tài sản`
					"
					@change="onPaginationChange"
				>
				</a-pagination>
			</div>
		</div>
	</div>
	<div v-else class="table-wrapper" style="margin: 0">
		<div
			class="table-detail position-relative empty-data"
			style="overflow: scroll; max-height: 76vh"
		>
			<b-card
				:class="{
					border_expired: checkDateExpired(element),
					['border-' + configColor(element)]: true
				}"
				class="card_container mb-3"
				v-for="element in listCertificates"
				:key="element.id + '_' + element.status"
			>
				<div class="col-12 d-flex mb-2 justify-content-between">
					<span
						class="content_id"
						:class="
							`bg-${configColor(element)}-15 text-${configColor(element)}`
						"
						>HSTD_{{ element.id }}</span
					>
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
							style="min-width: 15px"
							width="15px"
							height="21px"
							class="mr-2"
							src="@/assets/icons/ic_user_2.svg"
							alt="user"
						/>
						<div class="d-flex">
							<span style="font-weight: 500"
								><strong class="d_inline mr-1">Khách hàng:</strong
								>{{ element.petitioner_name }}</span
							>
						</div>
					</div>
				</div>
				<div class="property-content mb-2 d-flex color_content">
					<img class="mr-2" src="@/assets/icons/ic_price.svg" alt="user" />
					<div class="label_container d-flex">
						<strong class="d_inline mr-1">Tổng giá trị:</strong
						><span style="font-weight: 500">{{
							element.total_price ? `${formatPrice(element.total_price)}` : "-"
						}}</span>
					</div>
				</div>
				<div class="property-content mb-2 d-flex color_content">
					<img class="mr-2" src="@/assets/icons/ic_clock.svg" alt="user" />
					<div class="label_container d-flex">
						<strong class="d_inline mr-1">Thời hạn:</strong
						><span style="font-weight: 500">{{ getExpireDate(element) }}</span>
					</div>
				</div>
				<div class="property-content mb-2 d-flex color_content">
					<img class="mr-2" src="@/assets/icons/ic_id_card_2.svg" alt="user" />
					<div class="label_container d-flex">
						<strong class="d_inline mr-1">Trạng thái:</strong
						><span style="font-weight: 500">{{ element.status_text }}</span>
					</div>
				</div>
				<div class="property-content d-flex justify-content-between mb-0">
					<div class="label_container d-flex">
						<img
							width="15px"
							class="mr-2"
							src="@/assets/icons/ic_taglink.svg"
							alt="user"
						/><span style="color: #8b94a3">{{ element.document_count }}</span>
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
		</div>
		<div class="pagination-wrapper" style="margin-bottom: 20px">
			<div class="page-size">
				Hiển thị
				<a-select
					ref="select"
					:value="Number(pagination.pageSize)"
					style="width: 71px"
					:options="pageSizeOptions"
					@change="onSizeChange"
				/>
				hàng
			</div>
			<a-pagination
				:current="Number(pagination.current)"
				:page-size="Number(pagination.pageSize)"
				:total="Number(pagination.total)"
				:show-total="
					(total, range) =>
						`Kết quả hiển thị ${range[0]} - ${range[1]} của ${pagination.total} hồ sơ thẩm định`
				"
				@change="onPaginationChange"
			>
			</a-pagination>
		</div>
	</div>
</template>
<script>
import { PERMISSIONS } from "@/enum/permissions.enum";
import {
	BDropdown,
	BDropdownItem,
	BTooltip,
	BCard,
	BRow,
	BCol,
	BFormGroup,
	BFormInput
} from "bootstrap-vue";
import moment from "moment";
import Certificate from "@/models/Certificate";
import CertificationBrief from "@/models/CertificationBrief";

import { ref } from "vue";
import { storeToRefs } from "pinia";
import { useWorkFlowConfig } from "@/store/workFlowConfig";

const jsonConfig = require("../../../../config/workflow.json");
export default {
	name: "Tables",
	props: ["listCertificates", "pagination", "isLoading"],
	components: {
		CertificationBrief,

		"b-dropdown": BDropdown,
		"b-dropdown-item": BDropdownItem,
		"b-tooltip": BTooltip,
		BCard,
		BRow,
		BCol,
		BFormGroup,
		BFormInput
	},
	data() {
		return {
			selectedRowKeys: [],
			activeStatus: false,
			pageSizeOptions: [
				{ value: "10", label: "10" },
				{ value: "20", label: "20" },
				{ value: "30", label: "30" }
			],
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
			showAcceptCertificate: false,
			idDraft: "",
			elementDragger: "",
			position_profile: "",
			appraise_number: "",
			checkRole: false,
			profile: {},
			user_id: "",
			countData: 0,
			isAccept: false,
			jsonConfig: jsonConfig,
			principleConfig: [],
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

	setup() {
		const workFlowConfigStore = useWorkFlowConfig();
		const { configs } = storeToRefs(workFlowConfigStore);
		const jsonConfig = ref({});
		const lstFilterStatus = ref([]);
		const startFunction = async () => {
			if (!configs.value.hstdConfig)
				await workFlowConfigStore.getConfigByName("workflowHSTD");
			jsonConfig.value = configs.value.hstdConfig;
			if (jsonConfig.value && jsonConfig.value.principle) {
				lstFilterStatus.value = jsonConfig.value.principle
					.filter(element => element.isActive === 1)
					.map(element => ({
						...element,
						value: element.status,
						text: element.description
					}));
			}
		};
		startFunction();

		return {
			lstFilterStatus
		};
	},

	created() {
		// fix_permission
		this.profile = this.$store.getters.profile;
		const profile = this.$store.getters.profile;
		if (profile.data.user) {
			this.position_profile =
				profile.data.user.appraiser &&
				profile.data.user.appraiser.appraise_position
					? profile.data.user.appraiser.appraise_position.acronym
					: null;
			this.appraiser_number =
				profile.data.user.appraiser &&
				profile.data.user.appraiser.appraiser_number
					? profile.data.user.appraiser.appraiser_number
					: null;
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
		},
		columns() {
			let dataColumn = [
				{
					title: "Mã HSTD",
					align: "left",
					scopedSlots: { customRender: "id" },
					dataIndex: "id",
					// sorter: (a, b) => a.id - b.id,
					// sortDirections: ['descend', 'ascend'],
					hiddenItem: false
				},
				{
					title: "Mã TSTĐ",
					class: "optional-data",
					align: "left",
					scopedSlots: { customRender: "detail_appraise" },
					// sorter: (a, b) => a.document_num - b.document_num,
					// sortDirections: ['descend', 'ascend'],
					hiddenItem: false
				},
				{
					title: "Số hợp đồng",
					align: "left",
					scopedSlots: { customRender: "document_date" },
					// sorter: (a, b) => a.document_num - b.document_num,
					// sortDirections: ['descend', 'ascend'],
					hiddenItem: false
				},
				{
					title: "Số chứng thư",
					class: "optional-data",
					align: "left",
					scopedSlots: { customRender: "certificate_date" },
					// sorter: (a, b) => a.certificate_num.length - b.certificate_num.length,
					// sortDirections: ['descend', 'ascend'],
					hiddenItem: false
				},
				{
					title: "Khách hàng",
					align: "left",
					scopedSlots: { customRender: "petitioner_name" },
					// sorter: (a, b) => a.petitioner_name - b.petitioner_name,
					// sortDirections: ['descend', 'ascend'],
					width: "15dvw",
					hiddenItem: false
				},
				{
					title: "Nhóm đối tác",
					align: "left",
					width: "15dvw",
					scopedSlots: { customRender: "customer_group" },
					hiddenItem: false
				},
				{
					title: "Địa chỉ tài sản",
					align: "left",
					scopedSlots: { customRender: "appraised_asset" },
					hiddenItem: false
				},
				// {
				// 	title: "Địa chỉ tài sản",
				// 	align: "left",
				// 	scopedSlots: { customRender: "full_addresss" },
				// 	hiddenItem: false
				// },
				{
					title: "Tổng giá trị (VNĐ)",
					align: "left",
					scopedSlots: { customRender: "total_asset_price" },
					// sorter: (a, b) => a.total_asset_price - b.total_asset_price,
					// sortDirections: ['descend', 'ascend'],
					hiddenItem: false
				},
				{
					title: "Tổ thẩm định",
					align: "left",
					scopedSlots: { customRender: "appraiser" },
					// sorter: (a, b) => a.total_asset_price - b.total_asset_price,
					// sortDirections: ['descend', 'ascend'],
					hiddenItem: false
				},
				// {
				// 	title: "Người tạo",
				// 	class: "optional-data",
				// 	align: "left",
				// 	scopedSlots: { customRender: "created_by" },
				// 	// sorter: (a, b) => a.created_by.name.length - b.created_by.name.length,
				// 	// sortDirections: ['descend', 'ascend'],
				// 	hiddenItem: false
				// },
				{
					title: "Trạng thái",
					align: "center",
					scopedSlots: { customRender: "status" },
					dataIndex: "status",
					// filters: [
					//   { text: 'Mới', value: 2 },
					//   { text: 'Đang duyệt', value: 3 },
					//   { text: 'Đã duyệt', value: 4 },
					//   { text: 'Đã hủy', value: 5 }
					// ],
					// onFilter: (value, record) => record.status === value,
					// sorter: (a, b) => a.status - b.status,
					// sortDirections: ['descend', 'ascend'],
					hiddenItem: false
				}
			];
			return dataColumn.filter(item => item.hiddenItem === false);
		}
	},
	beforeMount() {
		this.getDataWorkFlow2();
		this.getProfiles();
	},
	mounted() {
		if (this.jsonConfig && this.jsonConfig.principle) {
			this.principleConfig = this.jsonConfig.principle.filter(
				i => i.isActive === 1
			);
		}
	},
	methods: {
		getStatusDescription(status) {
			const item = this.lstFilterStatus.find(item => item.status === status);
			return item ? item.description : "";
		},
		getStatusColor(status) {
			const item = this.lstFilterStatus.find(item => item.status === status);
			return item && item.css ? item.css.color : "info";
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

		async getDataWorkFlow2(search = "") {
			this.isLoading1 = true;
			try {
				const resp = await CertificationBrief.getListKanbanCertificate(search);
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
				this.isLoading1 = false;
			}
		},
		async handleUpdateStatus(id, data, message) {
			const res = await CertificationBrief.updateStatusCertificate(id, data);
			if (res.data) {
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
		configColor(element) {
			if (element.status == 1) {
				return "info";
			}
			if (element.status == 2) {
				return "primary";
			}
			if (element.status == 3) {
				return "warning";
			}
			if (element.status == 4) {
				return "success";
			}
			if (element.status == 5) {
				return "secondary";
			}
			if (element.status == 6) {
				return "control";
			}
			return "red";
		},
		getExpireStatusDate() {
			let dateConvert = new Date();
			let minutes = this.config.process_time ? this.config.process_time : 1440;
			let dateConverted = new Date(dateConvert.getTime() + minutes * 60000);
			let status_expired_at = moment(dateConverted).format("DD-MM-YYYY HH:mm");
			return status_expired_at;
		},
		getExpireDate(element) {
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
		showDetailAppraise(element) {
			let arconymText1 = "";
			// Đất trống 37 đất có nhà 38
			if (element.asset_type_id === 37 || element.asset_type_id === 38) {
				const arconymText = `BDS_${element.real_estate_id}`;
				// const link = `<a href="/certification_asset/real-estate/detail?id=${element.real_estate_id}" target='_blank'>${arconymText}</a><br/>`;
				const link = `<span>${arconymText}</span><br/>`;
				arconymText1 += link;
			}
			// Chung cư 39
			if (element.asset_type_id === 39) {
				const arconymText = `BDS_${element.real_estate_id}`;
				// const link = `<a href="/certification_asset/apartment/detail?id=${element.real_estate_id}" target='_blank'>${arconymText}</a><br/>`;
				const link = `<span>${arconymText}</span><br/>`;
				arconymText1 += link;
			}
			// Giá trị DN 179 Tài sản khác 180 Dây chuyền CN 183
			if (
				element.asset_type_id === 179 ||
				element.asset_type_id === 180 ||
				element.asset_type_id === 183
			) {
				const arconymText = `DS_${element.real_estate_id}`;
				const link = `<span>${arconymText}</span><br/>`;
				// const link = `<a href="/certification_asset/other-purpose/detail?id=${element.real_estate_id}" target='_blank'>${arconymText}</a><br/>`;
				arconymText1 += link;
			}
			// Máy móc thiết bị 181
			if (element.asset_type_id === 181) {
				const arconymText = `DS_${element.real_estate_id}`;
				// const link = `<a href="/certification_asset/machine/detail?id=${element.real_estate_id}" target='_blank'>${arconymText}</a><br/>`;
				const link = `<span>${arconymText}</span><br/>`;
				arconymText1 += link;
			}
			// Phương tiện vận tải 182
			if (element.asset_type_id === 182) {
				const arconymText = `DS_${element.real_estate_id}`;
				const link = `<span>${arconymText}</span><br/>`;
				// const link = `<a href="/certification_asset/vehicle/detail?id=${element.real_estate_id}" target='_blank'>${arconymText}</a><br/>`;
				arconymText1 += link;
			}

			return arconymText1;
		},
		showAddressAppraise(data) {
			let arconymText1 = "";
			if (data.real_estate && data.real_estate.length > 0) {
				let temp = "";
				for (let index = 0; index < data.real_estate.length; index++) {
					const element = data.real_estate[index];
					if (data.real_estate.length > 1) {
						temp = "BDS_" + element.real_estate_id + ": ";
					}
					// Đất trống 37 đất có nhà 38
					if (
						(element.asset_type_id === 37 || element.asset_type_id === 38) &&
						element.appraises
					) {
						const arconymText = `${temp}${element.appraises.full_address}`;
						const fullAddress = `<p class="text-wrap">${arconymText}</p>`;
						arconymText1 += fullAddress;
					}
					// Chung cư 39
					if (element.asset_type_id === 39 && element.apartment) {
						const arconymText = `${temp}${element.apartment.full_address}`;
						const fullAddress = `<p class="text-wrap">${arconymText}</p>`;
						arconymText1 += fullAddress;
					}
				}

				return arconymText1;
			} else return "";
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
		handleTableChange(pagination, filters, sorter) {
			this.$emit("handleChange", pagination, "All", filters, sorter);
		},
		onSelectChange(selectedRowKeys) {
			this.selectedRowKeys = selectedRowKeys;
		},

		formatDate(value) {
			return moment(String(value)).format("DD/MM/YYYY");
		},
		formatNumber(num) {
			// convert number to dot format
			if (num) {
				let formatedNum = num.toString().replace(".", ",");
				return formatedNum.toString().replace(/^[+-]?\d+/, function(int) {
					return int.replace(/(\d)(?=(\d{3})+$)/g, "$1.");
				});
			}
		},
		format(value) {
			let num = (value / 1).toFixed(0).replace(",", ".");
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
		},
		onSizeChange(pageSize) {
			const pagination = { ...this.pagination, pageSize: Number(pageSize) };
			this.handleTableChange(pagination);
		},
		onPaginationChange(current) {
			const pagination = { ...this.pagination, current: Number(current) };
			this.handleTableChange(pagination);
		},
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
		}
	}
};
</script>

<style scoped lang="scss">
// .total {
//   color: #000000;
//   bottom: 17px;
//   right: 0;

//   @media (max-width: 418px) {
//     position: relative !important;
//     text-align: center;
//     margin-top: 20px;
//   }
// }

// .full-address {
//   width: 200px;
//   white-space: nowrap;
//   -webkit-line-clamp: 2 !important;
//   overflow: hidden;
//   text-overflow: ellipsis;
//   margin-bottom: 0;
//   text-transform: none;

//   &:first-letter {
//     text-transform: none;
//   }
// }

/deep/ .optional-data {
	@media (max-width: 1024px) {
		display: none;
	}
}

.text-none {
	text-transform: none;
}

.text-main {
	font-weight: 500;
	text-transform: capitalize;
	margin-bottom: 0.5rem;

	&__blue {
		color: #007ec6;
	}
}

.text-secondary {
	font-weight: 500;
	font-size: 12px !important;

	@media (max-height: 660px) {
		font-size: 12px !important;
	}

	@media (max-height: 800px) and (min-height: 660px) {
		font-size: 14px !important;
	}

	@media (max-height: 970px) and (min-height: 800px) {
		font-size: 14px !important;
	}
	color: #617f9e;
	text-transform: none;
	margin-bottom: 0;
}

.status {
	// color: #2d9000;
	margin-bottom: 0;

	&.red {
		color: red;
	}

	&.orange {
		color: #faa831;
	}
}

.status-color {
	width: 14px;
	height: 14px;
	border-radius: 3px;
	margin: auto;
}

.link-detail {
	white-space: nowrap;
	text-transform: uppercase;
	background: transparent;
	border: none;

	// &:hover,
	// &:focus,
	// &:active {
	// 	color: #faa831;
	// 	border: none;
	// 	outline: none;
	// }
}

.dropdown-container {
	border-radius: 2px;
	position: absolute;
	right: 0;

	img {
		padding: 7px;
	}
}

.dropdown-item-container {
	color: #555555;
	text-transform: none;

	img {
		width: 30px;
		margin-right: 10px;
	}
}

.table-wrapper {
	.ant-table-filter-dropdown-btns {
		background-color: white !important;
	}

	.ant-table-filter-dropdown-link.confirm {
		color: red;
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

			@media (max-width: 1024px) {
				display: none;
			}
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
					display: none;
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
.name_detail {
	width: 270px !important;
}
.appraise_detail {
	// @media (max-width: 1600px) {
	// 	max-height: 60px;
	// }
	// @media (min-width: 1600px) {
	// 	max-height: 60px;
	// }
	// @media (min-width: 1900px) {
	// 	max-height: 60px;
	// }
	width: 270px !important;
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
.pre_detail {
	margin: unset;
	font-family: unset;
}

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
	border: 1px solid;
	background: aliceblue;
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
	border-color: red !important;
}
</style>
