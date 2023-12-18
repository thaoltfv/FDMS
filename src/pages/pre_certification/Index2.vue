<template>
	<div v-if="!isMobile()" class="main-wrapper-new">
		<a-tabs @change="callback" default-active-key="2">
			<a-tab-pane key="1">
				<span slot="tab">
					<img src="@/assets/icons/ic_table.svg" alt="table" />
				</span>
				<div class="container-fluid appraise-container mt-3">
					<Tables
						:listCertificates="listCertificatesAll"
						:isLoading="isLoading"
						:pagination="paginationAll"
						@handleChange="onPageChange"
					/>
				</div>
			</a-tab-pane>
			<a-tab-pane key="2">
				<span slot="tab">
					<img src="@/assets/icons/ic_board.svg" alt="board" />
				</span>
				<board
					:search_kanban="search_kanban"
					:key="render_kanban"
					ref="kanban"
				/>
			</a-tab-pane>

			<div slot="tabBarExtraContent">
				<div class="container-button appraise-container">
					<div
						class="button__detail row mx-0 justify-content-between justify-content-lg-end align-items-center"
					>
						<div class="col-12 col-md-6 col-xl-8">
							<button-checkbox
								v-show="showFilter"
								:options="statusOptions"
								:value="selectedStatus"
								@change="onChangeStatus"
							/>
						</div>
						<div
							class="search-block col-12 col-md-6 col-xl-4 d-flex justify-content-end align-items-center"
						>
							<Search @filter-changed="onFilterQuickSearchChange($event)" />
							<router-link
								v-if="add"
								:to="{ name: 'pre_certification.create' }"
								class="btn text-nowrap index-screen-button ml-md-2"
							>
								<img
									src="@/assets/icons/ic_new.svg"
									style="margin-right: 8px"
									alt="search"
								/>Tạo mới
							</router-link>
							<b-dropdown
								class="dropdown-container"
								no-caret
								v-if="this.export"
							>
								<template #button-content>
									<div class="container_image">
										<img src="@/assets/icons/ic_more.svg" alt="" />
									</div>
								</template>
								<!-- <b-dropdown-item @click.prevent="export30daysBefore()">Xuất dữ liệu 30 ngày trước</b-dropdown-item>
								<b-dropdown-item @click.prevent="exportMonthBefore()">Xuất dữ liệu tháng trước</b-dropdown-item>
								<b-dropdown-item @click.prevent="exportQuarter()">Xuất dữ liệu quý trước</b-dropdown-item> -->
								<b-dropdown-item @click.prevent="exportAdjust()"
									>Xuất dữ liệu tùy chỉnh</b-dropdown-item
								>
							</b-dropdown>
						</div>
					</div>
				</div>
			</div>
		</a-tabs>
		<div>
			<ModalExportPreCertificate
				v-if="showAdjustModal"
				@cancel="showAdjustModal = false"
				:statusOptions="statusOptions"
			/>
		</div>
	</div>
	<div v-else class="main-wrapper-new" style="margin: 0;padding-top:0;">
		<div slot="tabBarExtraContent">
			<div class="container-button appraise-container">
				<div
					class="button__detail row mx-0 justify-content-between justify-content-lg-end align-items-center"
				>
					<div
						class="search-block col-7 col-md-6 col-xl-4 d-flex justify-content-end align-items-center"
					>
						<Search @filter-changed="onFilterQuickSearchChange($event)" />
					</div>
					<div
						class="col-4"
						style="padding: 0;
    margin-top: 10px;"
					>
						<router-link
							v-if="add"
							:to="{ name: 'certification_brief.create' }"
							class="btn text-nowrap index-screen-button ml-md-2"
						>
							<img
								src="@/assets/icons/ic_new.svg"
								style="margin-right: 8px"
								alt="search"
							/>Tạo mới
						</router-link>
					</div>
					<div
						class="col-1"
						style="padding: 0;
    margin-top: 15px;"
					>
						<b-dropdown class="dropdown-container" no-caret v-if="this.export">
							<template #button-content>
								<div class="container_image">
									<img src="@/assets/icons/ic_more.svg" alt="" />
								</div>
							</template>
							<!-- <b-dropdown-item @click.prevent="export30daysBefore()">Xuất dữ liệu 30 ngày trước</b-dropdown-item>
								<b-dropdown-item @click.prevent="exportMonthBefore()">Xuất dữ liệu tháng trước</b-dropdown-item>
								<b-dropdown-item @click.prevent="exportQuarter()">Xuất dữ liệu quý trước</b-dropdown-item> -->
							<b-dropdown-item @click.prevent="exportAdjust()"
								>Xuất dữ liệu tùy chỉnh</b-dropdown-item
							>
						</b-dropdown>
					</div>
					<div class="col-12 col-md-6 col-xl-8">
						<button-checkbox
							:options="statusOptions"
							:value="selectedStatus"
							@change="onChangeStatus"
						/>
					</div>
				</div>
			</div>
		</div>
		<div
			class="container-fluid appraise-container mt-3"
			style="margin-top: 0!important;"
		>
			<Tables
				:listCertificates="listCertificatesAll"
				:isLoading="isLoading"
				:pagination="paginationAll"
				@handleChange="onPageChange"
			/>
		</div>
		<ModalExportPreCertificate
			v-if="showAdjustModal"
			@cancel="showAdjustModal = false"
			:statusOptions="statusOptions"
		/>
	</div>
</template>

<script>
import { PERMISSIONS } from "@/enum/permissions.enum";
import ButtonCheckbox from "@/components/Form/ButtonCheckbox";
import ModalExportPreCertificate from "@/components/PreCertificate/ModalExportPreCertificate";
import PreCertificate from "@/models/PreCertificate.js";
import Search from "@/components/PreCertificate/Search";
import Tables from "@/components/PreCertificate/Tables.vue";
import Board from "./Index.vue";
import { convertPagination } from "@/utils/filters";
import { Tabs } from "ant-design-vue";
import { BDropdown, BDropdownItem, BTooltip } from "bootstrap-vue";
import moment from "moment";
export default {
	name: "Index",
	data() {
		return {
			theme: {
				navItem: "#000000",
				navActiveItem: "#FAA831",
				slider: "#FAA831",
				arrow: "#000000"
			},
			listCertificatesAll: [],
			listCertificatesOpen: [],
			listCertificatesLock: [],
			listCertificatesClose: [],
			list: [],
			paginationAll: {},
			paginationOpen: {},
			paginationLock: {},
			paginationClose: {},
			filter: {},
			search_kanban: "",
			render_kanban: 1234543,
			status: 0,
			activeStatus: false,
			showModalSearch: false,
			showAdjustModal: false,
			view: false,
			add: false,
			edit: false,
			deleted: false,
			accept: false,
			export: false,
			selectedStatus: [],
			showFilter: false,
			statusOptions: {
				data: [
					{ label: "Mới", value: "1", class: "bg-info" },
					{ label: "Đang thẩm định", value: "2", class: "bg-primary" },
					{ label: "Đang kiểm soát", value: "6", class: "bg-control" },
					{ label: "Đang duyệt", value: "3", class: "bg-warning" },
					{ label: "Hoàn thành", value: "4", class: "bg-success" },
					{ label: "Hủy", value: "5", class: "bg-secondary" }
				],
				value: "value",
				label: "label"
			},
			form: {
				createdBy: [],
				fromDate: "",
				toDate: "",
				status: [],
				appraiser_perform_id: "",
				appraiser_id: "",
				customer_id: ""
			}
		};
	},
	components: {
		Search,
		Tables,
		ButtonCheckbox,
		Board,
		"a-tabs": Tabs,
		"a-tab-pane": Tabs.TabPane,
		"b-dropdown": BDropdown,
		"b-dropdown-item": BDropdownItem,
		"b-tooltip": BTooltip,
		ModalExportPreCertificate
	},
	created() {
		// fix_permission
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

		if (this.isMobile()) {
			this.selectedStatus = ["3"];
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
		callback(key) {
			if (+key === 2) {
				this.showFilter = false;
			} else {
				this.showFilter = true;
			}
		},
		handleChangeTab(event) {
			if (event === 0) {
				this.status = 1;
			} else if (event === 1) {
				this.status = 2;
			} else if (event === 2) {
				this.status = 3;
			} else this.status = 0;
		},
		async getProfiles() {
			const profile = this.$store.getters.profile;
			if (profile && profile.data.user.roles[0].name.slice(-5) === "ADMIN") {
				this.activeStatus = true;
			}
		},
		async onFilterQuickSearchChange($event) {
			this.search_kanban = { ...$event };
			this.filter = { ...$event };
			await this.getCertificateAll();
			this.render_kanban += 1;
			// await this.$refs.kanban.getDataWorkFlow(this.search_kanban)
		},
		handleSearch() {
			this.showModalSearch = true;
		},
		async getCertificateAll(params = {}) {
			this.isLoading = true;
			try {
				const resp = await PreCertificate.paginate({
					query: {
						page: 1,
						limit: 20,
						...params,
						...this.filter,
						status: this.selectedStatus
					}
				});
				this.listCertificatesAll = [...resp.data.data];
				this.paginationAll = convertPagination(resp.data);
				this.isLoading = false;
			} catch (e) {
				this.isLoading = false;
			}
		},
		async onPageChange(pagination) {
			this.perPage = pagination.pageSize;
			const params = {
				page: pagination.current,
				limit: pagination.pageSize
			};

			await this.getCertificateAll(params);
		},
		onChangeStatus(value) {
			this.selectedStatus = value;
			// console.log('this.selectedStatus',this.selectedStatus)
			this.getCertificateAll();
		},
		async export30daysBefore() {
			this.form.fromDate = await moment(
				new Date(new Date().setDate(new Date().getDate() - 30))
			).format("DD/MM/YYYY");
			this.form.toDate = await moment(new Date()).format("DD/MM/YYYY");
			const res = await PreCertificate.exportDataCertificationBrief(this.form);
			if (res.data) {
				const fileLink = document.createElement("a");
				fileLink.href = res.data.url;
				fileLink.setAttribute("download", res.data.file_name);
				document.body.appendChild(fileLink);
				fileLink.click();
				fileLink.remove();
				window.URL.revokeObjectURL(fileLink);
				this.$toast.open({
					message: "Xuất dữ liệu thành công",
					type: "success",
					duration: 3000,
					position: "top-right"
				});
			} else if (res.error) {
				this.$toast.open({
					message: res.error.message,
					type: "error",
					duration: 3000,
					position: "top-right"
				});
			}
		},
		async exportMonthBefore() {
			let date = new Date();
			let datePrevious = new Date(date.setDate(0));
			let from_date = new Date(new Date(datePrevious).setDate(1));
			let to_date = new Date(datePrevious);
			this.form.fromDate = await moment(from_date).format("DD/MM/YYYY");
			this.form.toDate = await moment(to_date).format("DD/MM/YYYY");
			const res = await PreCertificate.exportDataCertificationBrief(this.form);
			if (res.data) {
				const fileLink = document.createElement("a");
				fileLink.href = res.data.url;
				fileLink.setAttribute("download", res.data.file_name);
				document.body.appendChild(fileLink);
				fileLink.click();
				fileLink.remove();
				window.URL.revokeObjectURL(fileLink);
				this.$toast.open({
					message: "Xuất dữ liệu thành công",
					type: "success",
					duration: 3000,
					position: "top-right"
				});
			} else if (res.error) {
				this.$toast.open({
					message: res.error.message,
					type: "error",
					duration: 3000,
					position: "top-right"
				});
			}
		},
		async exportQuarter() {
			let quarterAdjustment = (moment().month() % 3) + 1;
			let lastQuarterEndDate = moment()
				.subtract({ months: quarterAdjustment })
				.endOf("month");
			let lastQuarterStartDate = lastQuarterEndDate
				.clone()
				.subtract({ months: 2 })
				.startOf("month");
			this.form.fromDate = await moment(lastQuarterStartDate).format(
				"DD/MM/YYYY"
			);
			this.form.toDate = await moment(lastQuarterEndDate).format("DD/MM/YYYY");
			const res = await PreCertificate.exportDataCertificationBrief(this.form);
			if (res.data) {
				const fileLink = document.createElement("a");
				fileLink.href = res.data.url;
				fileLink.setAttribute("download", res.data.file_name);
				document.body.appendChild(fileLink);
				fileLink.click();
				fileLink.remove();
				window.URL.revokeObjectURL(fileLink);
				this.$toast.open({
					message: "Xuất dữ liệu thành công",
					type: "success",
					duration: 3000,
					position: "top-right"
				});
			} else if (res.error) {
				this.$toast.open({
					message: res.error.message,
					type: "error",
					duration: 3000,
					position: "top-right"
				});
			}
		},
		exportAdjust() {
			this.showAdjustModal = true;
		}
	},

	beforeMount() {
		this.getCertificateAll();
		this.getProfiles();
	}
};
</script>

<style scoped lang="scss">
.main-wrapper-new {
	background: #ffffff;
	box-shadow: 0px 0px 20px rgba(0, 80, 124, 0.16);
	border-radius: 5px;
	margin: 12px;
	padding: 22px 12px;

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
.container_image {
	width: 35px;
	height: 30px;
}
</style>
