<template>
	<div>
		<div class="container-fluid appraise-container">
			<Tabs :theme="theme" :navAuto="true">
				<TabItem name="Yêu cầu sơ bộ">
					<div class="main-wrapper-new" style="margin: 0;padding-top:0;">
						<div class="container-button appraise-container">
							<div
								class="button__detail row mx-0 justify-content-between justify-content-lg-end align-items-center"
							>
								<div
									class="search-block col-12 col-md-6 col-xl-4 d-flex justify-content-end align-items-center"
								>
									<DropdownFilterPreCertificate
										class="mr-5"
										@search="onChangeStatusPreCertificate"
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
				</TabItem>
				<TabItem name="Hồ sơ thẩm định">
					<div class="main-wrapper-new" style="margin: 0;padding-top:0;">
						<div class="container-button appraise-container">
							<div
								class="button__detail row mx-0 justify-content-between justify-content-lg-end align-items-center"
							>
								<div
									class="search-block col-12 col-md-6 col-xl-4 d-flex justify-content-end align-items-center"
								>
									<DropdownFilter class="mr-5" @search="onChangeStatus" />
									<Search @filter-changed="onFilterQuickSearchChange($event)" />
								</div>
							</div>
						</div>
						<TableCertificate
							:listCertificates="listCertificatesAll"
							:isLoading="isLoading"
							:pagination="paginationAll"
							@handleChange="onPageChange"
						/>
					</div>
				</TabItem>
			</Tabs>
		</div>
	</div>
</template>

<script>
import { Tabs, TabItem } from "vue-material-tabs";
import TablePreCertificate from "./components/TablePreCertificate";
import TableCertificate from "./components/TableCertificate";
import PreCertificate from "@/models/PreCertificate.js";
import CertificateBrief from "@/models/CertificationBrief.js";
import { convertPagination } from "@/utils/filters";
import { PERMISSIONS } from "@/enum/permissions.enum";
import DropdownFilter from "@/components/DropdownFilter";
import Search from "./components/SearchCertificate";
import SearchPreCertificate from "@/components/PreCertificate/Search";
import DropdownFilterPreCertificate from "@/components/PreCertificate/DropdownFilter";
export default {
	name: "Index",
	components: {
		Tabs,
		TabItem,
		TableCertificate,
		TablePreCertificate,
		DropdownFilter,
		DropdownFilterPreCertificate,
		Search,
		SearchPreCertificate
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
		filter: {},
		filterPreCertificate: {},
		isLoadingPreCertificate: false,
		isLoading: false,
		listCertificatesAll: [],
		listCertificatesAllPreCertificate: [],
		selectedStatus: [],
		selectedStatusPreCertificate: []
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
						...this.filterPreCertificate,
						status: this.selectedStatusPreCertificate,
						is_guest: true
					}
				});
				this.listCertificatesAllPreCertificate = [...resp.data.data];
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
			await this.getCertificateAllPreCertificate("table", params);
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
		onChangeStatusPreCertificate(value) {
			this.selectedStatusPreCertificate = value;
			this.getCertificateAllPreCertificate();
		},
		async onFilterQuickSearchChange($event) {
			this.filter = { ...$event };
			await this.getCertificateAll();
		},
		async onFilterQuickSearchChangePreCertificate($event) {
			this.filterPreCertificate = { ...$event };
			await this.getCertificateAllPreCertificate();
		}
	},
	beforeMount() {
		this.getCertificateAll();
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
		height: 35px;
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
