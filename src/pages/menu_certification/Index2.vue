<template>
	<div>
		<div class="main-wrapper-new mt-n2" style="height: 88vh;">
			<a-tabs default-active-key="1" style="height: 100%;" class="mt-n3">
				<a-tab-pane key="1">
					<span slot="tab">
						<img src="@/assets/icons/ic_chart_lg.svg" alt="table" />
					</span>
					<div class="row m-2" style="margin: 0;padding-top:0;">
						<div class="col-6 row">
							<!-- <div class="col-12">
								<div class="row mt-2 mb-2 mr-1 d-flex justify-content-end">
									<InputCategory
							v-if="isAdmin"
							v-model="customerGroup"
							class="col-3 col-lg-3 input-content"
							vid="customer_group_id"
							label="Nhóm đối tác"
							:options="optionsCustomerGroup"
						/>
									<InputDatePickerRangeCondition
										class="col-8 col-md-8 col-lg-8 form-group-container marginTop"
										vid="search"
										format-date="DD/MM/YYYY"
										:startDateValue="fromDate"
										:endDateValue="toDate"
										@startDate="fromDate = $event"
										@endDate="toDate = $event"
										label="Từ ngày - đến ngày"
									/>
									<button
										style="height:35px; margin-top: 28px;"
										class="btn btn-orange btn-action-modal"
										type="button"
										@click="handleAction"
									>
										Tìm kiếm
									</button>
								</div>
							</div> -->
							<div class="col-12">
								<ProcessProgressChart
									ref="ProcessProgressChart"
									name="Tổng quan"
									:fromDate="fromDate"
									:toDate="toDate"
								/>
							</div>
						</div>
						<div class="col-6">
							<div class="col-12">
								<ConversionRateByMonthChart
									ref="ConversionRateByMonthChart"
									name="Yêu cầu sơ bộ đã hoàn thành"
								/>
							</div>
						</div>
					</div>
				</a-tab-pane>
				<a-tab-pane key="2">
					<span slot="tab">
						<img src="@/assets/icons/ic_table.svg" alt="board" />
					</span>
					<div class="mt-n5" style="margin: 0;padding-top:0;">
						<div class="container-button appraise-container">
							<div
								class="button__detail row mx-0 justify-content-between justify-content-lg-end align-items-center"
							>
								<div
									class="search-block col-12 col-md-6 col-xl-4 d-flex justify-content-end align-items-center"
								>
									<DropdownFilterPreCertificate
										class="mr-5"
										@notifi-kanban="onChangeStatusPreCertificate"
									/>
									<SearchPreCertificate
										@filter-changed="
											onFilterQuickSearchChangePreCertificate($event)
										"
									/>
								</div>
							</div>
						</div>
						<TablePreCertificate
							:listCertificates="listCertificatesAllPreCertificate"
							:isLoading="isLoadingPreCertificate"
							:pagination="paginationAllPreCertificate"
							@handleChange="onPageChangePreCertificate"
						/>
					</div>
				</a-tab-pane>
			</a-tabs>
			<!-- <Tabs :theme="theme" :navAuto="true">
				<TabItem name="Thống kê">
				
				</TabItem>
				<TabItem name="Yêu cầu sơ bộ">
					
				</TabItem>
			</Tabs> -->
		</div>
	</div>
</template>

<script>
import moment from "moment";
import { storeToRefs } from "pinia";
import { usePreCertificateStore } from "@/store/preCertificate";
// import { Tabs, TabItem } from "vue-material-tabs";
import { Tabs } from "ant-design-vue";
import TablePreCertificate from "./components/TablePreCertificate";
import TableCertificate from "./components/TableCertificate";
import PreCertificate from "@/models/PreCertificate.js";
import CertificateBrief from "@/models/CertificationBrief.js";
import { convertPagination } from "@/utils/filters";
import { PERMISSIONS } from "@/enum/permissions.enum";
import DropdownFilter from "@/components/DropdownFilter";
import Search from "./components/SearchCertificate";
import SearchPreCertificate from "@/components/PreCertificate/Search";
import InputDatePickerRangeCondition from "@/components/Form/InputDatePickerRangeCondition";
import InputCategory from "@/components/Form/InputCategory";
import DropdownFilterPreCertificate from "@/components/PreCertificate/DropdownFilter";
import ProcessProgressChart from "./components/ProcessProgressChart";
import ProcessProgressChartCertificate from "./components/ProcessProgressChartCertificate";
import FinishByMonthChart from "./components/FinishByMonthChart";
import ConversionRateByMonthChart from "./components/ConversionRateByMonthChart";
const dashboardConfig = require("../../../config/dashboard.json");
export default {
	name: "Index",
	components: {
		// Tabs,
		// TabItem,
		"a-tabs": Tabs,
		"a-tab-pane": Tabs.TabPane,
		TableCertificate,
		TablePreCertificate,
		DropdownFilter,
		DropdownFilterPreCertificate,
		Search,
		SearchPreCertificate,
		ProcessProgressChart,
		ProcessProgressChartCertificate,
		FinishByMonthChart,
		ConversionRateByMonthChart,
		InputDatePickerRangeCondition,
		InputCategory
	},
	data: () => ({
		theme: {
			navItem: "#000000",
			navActiveItem: "#FAA831",
			slider: "#FAA831",
			arrow: "#000000"
		},
		paginationAll: {},
		paginationAllPreCertificate: {},
		isAdmin: false,
		filter: {},
		filterPreCertificate: {},
		filterSearch: {
			search: "",
			selectedOfficialTransferStatus: [],
			selectedStatus: [],
			status: [],
			ots: null,
			timeFilter: {
				from: null,
				to: null
			}
		},
		isLoadingPreCertificate: false,
		isLoading: false,
		listCertificatesAll: [],
		listCertificatesAllPreCertificate: [],
		selectedStatus: [],
		selectedStatusPreCertificate: [],
		customerGroup: null,
		customerGroups: [],
		fromDate: moment(
			new Date(new Date().setDate(new Date().getDate() - 30))
		).format("DD/MM/YYYY"),
		toDate: moment(new Date()).format("DD/MM/YYYY"),
		dashboard: dashboardConfig.dashboard.filter(i => i.active === true)
	}),
	methods: {
		async getCertificateAll(params = {}) {
			this.isLoading = true;
			try {
				const resp = await CertificateBrief.paginate({
					query: {
						page: 1,
						limit: 20,
						...params,
						...this.filter,
						status: this.selectedStatus,
						is_guest: true
					}
				});
				this.listCertificatesAll = [...resp.data.data];
				this.paginationAll = convertPagination(resp.data);
				this.isLoading = false;
			} catch (e) {
				this.isLoading = false;
			}
		},
		async getCertificateAllPreCertificate(params = {}) {
			this.isLoadingPreCertificate = true;
			try {
				const resp = await PreCertificate.paginate({
					query: {
						page: 1,
						limit: 20,
						...params,
						...this.filterSearch,
						is_guest: true
					}
				});
				const temp = [...resp.data.data];
				this.listCertificatesAllPreCertificate = temp;
				this.paginationAllPreCertificate = convertPagination(resp.data);
				this.isLoadingPreCertificate = false;
			} catch (e) {
				this.isLoadingPreCertificate = false;
			}
		},
		async onPageChange(pagination) {
			const params = {
				page: pagination.current,
				limit: pagination.pageSize
			};

			await this.getCertificateAll(params);
		},
		async onPageChangePreCertificate(pagination) {
			const params = {
				page: pagination.current,
				limit: pagination.pageSize
			};
			await this.getCertificateAllPreCertificate(params);
		},
		onChangeStatus(value) {
			this.selectedStatus = value;
			// if (this.search_kanban) {
			// 	this.search_kanban.status = value;
			// } else {
			this.search_kanban = { status: value };
			// }
			this.getCertificateAll();
		},
		async onChangeStatusPreCertificate(value) {
			this.isLoadingPreCertificate = true;
			let tempots = [];
			if (
				value.selectedOfficialTransferStatus[0] == true &&
				value.selectedOfficialTransferStatus[1] == true
			) {
				tempots = null;
			} else if (value.selectedOfficialTransferStatus[0] == true) {
				tempots = 0;
			} else if (value.selectedOfficialTransferStatus[1] == true) {
				tempots = 1;
			}
			const temp = {
				search: value.search,
				data: {
					status: value.selectedStatus,
					ots: tempots,
					timeFilter: {
						from: value.timeFilter.from,
						to: value.timeFilter.to
					}
				}
			};
			this.filterSearch = temp;
			const resp = await PreCertificate.paginate({
				query: {
					page: 1,
					limit: 20,
					is_guest: true,
					...temp
				}
			});

			this.listCertificatesAllPreCertificate = [...resp.data.data];
			this.paginationAllPreCertificate = convertPagination(resp.data);
			this.isLoadingPreCertificate = false;
		},
		async onFilterQuickSearchChange($event) {
			this.filter = { ...$event };
			await this.getCertificateAll();
		},
		async onFilterQuickSearchChangePreCertificate($event) {
			this.filterPreCertificate = { ...$event };
			await this.getCertificateAllPreCertificate();
		},
		handleAction() {
			this.$refs.ProcessProgressChart[0].getDataProcessProgress(
				this.fromDate,
				this.toDate
			);
			this.$refs.ProcessProgressChartCertificate[0].getDataProcessProgress(
				this.fromDate,
				this.toDate
			);
			this.$refs.FinishByMonthChart[0].getDataCertificationFinishByMonth();
			this.$refs.ConversionRateByMonthChart[0].getReportCertificationConversionRateByMonth();
		}
	},
	computed: {
		optionsCustomerGroup() {
			return {
				data: this.customerGroups,
				id: "id",
				key: "description"
			};
		}
	},
	created() {
		const permission = this.$store.getters.currentPermissions;
		// fix_permission
		let count = 0;
		permission.forEach(value => {
			if (value === "VIEW_MENU_FOLLOW_PROFILE") {
				count += 1;
			}
			if (value === "ADD_MENU_FOLLOW_PROFILE") {
				count += 1;
			}
			if (value === "EDIT_MENU_FOLLOW_PROFILE") {
				count += 1;
			}
			if (value === "DELETE_MENU_FOLLOW_PROFILE") {
				count += 1;
			}
			if (value === "ACCEPT_MENU_FOLLOW_PROFILE") {
				count += 1;
			}
			if (value === "EXPORT_MENU_FOLLOW_PROFILE") {
				count += 1;
			}
		});
		// Admin
		if (count === 6) {
			this.isAdmin = true;
		}
		const preCertificateStore = usePreCertificateStore();
		const { lstDataConfig } = storeToRefs(preCertificateStore);
		this.customerGroups = lstDataConfig.value.customerGroups;
	},
	beforeMount() {
		// this.getCertificateAll();
		this.getCertificateAllPreCertificate();
	}
};
</script>

<style scoped lang="scss">
.appraise-container {
	padding: 0 1.25rem;
}
.main-wrapper-new {
	background: #ffffff;
	box-shadow: 0px 0px 20px rgba(0, 80, 124, 0.16);
	border-radius: 5px;
	margin: 12px;
	padding: 22px 12px;
	height: 100vh;

	.index-screen-button {
		img {
			max-width: 12px;
			height: auto;
		}

		@media (max-width: 1440px) {
			width: 135px;
			margin-right: 0;
		}

		@media (max-width: 767px) {
			width: 100%;
			margin-bottom: 0;
		}
	}

	.btn {
		background: #e2f3fc;
		border: 1px solid #007ec6;
		border-radius: 3px;
		color: #007ec6;
		height: 25px;
	}

	.button__detail {
		@media (max-width: 1024px) {
			align-items: unset !important;
		}
	}

	.search-block {
		@media (max-width: 767px) {
			flex-direction: column;
			align-items: center;

			.search {
				margin: 10px 0;
			}
		}
	}

	/deep/ .ant-tabs {
		.ant-tabs-bar {
			border-bottom: unset;
			margin: 0;

			.ant-tabs-extra-content {
				width: 89%;
				line-height: 30px !important;
				@media (max-width: 1024px) {
					float: unset;
					width: 85%;
				}
			}

			// .ant-tabs-nav-container {
			// 	@media (max-width: 1024px) {
			// 		width: 100%;;
			// 	}
			// }

			.ant-tabs-tab-active {
				img {
					filter: invert(32%) sepia(92%) saturate(2494%) hue-rotate(181deg)
						brightness(90%) contrast(101%);
				}
			}

			.ant-tabs-tab {
				margin-right: 0;
				padding: 9px 16px;
			}

			.ant-tabs-ink-bar {
				display: none !important;
			}
		}
	}
}
</style>
