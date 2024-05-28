<template>
	<div class="table-wrapper">
		<div class="table-detail position-relative empty-data">
			<a-table
				ref="table"
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
				<template slot="id" slot-scope="id, data">
					<button @click.prevent="handleDetail(id, data)" class="link-detail">
						{{ "TSSB_" + id }}
					</button>
				</template>
				<template slot="description" slot-scope="description">
					<p class="mb-0 text-capitalize">
						{{ description.toLowerCase() }}
					</p>
				</template>
				<template slot="front_side" slot-scope="front_side">
					<p class="status text-none">
						{{ front_side ? "Mặt tiền" : front_side === 0 ? "Hẻm" : "-" }}
					</p>
				</template>
				<template slot="property_name" slot-scope="property_name, data">
					<p :id="data.id + 'all'" class="full-address text-wrap">
						{{ property_name }}
					</p>
					<b-tooltip :target="(data.id + 'all').toString()">{{
						property_name
					}}</b-tooltip>
				</template>
				<template slot="created_at" slot-scope="created_at">
					<p class="public_date mb-0">{{ formatDate(created_at) }}</p>
				</template>
				<template slot="total_price" slot-scope="text, row, index">
					<p class="text-none mb-0">
						{{
							row.land_final_estimate &&
							row.land_final_estimate[0] &&
							row.land_final_estimate[0].total_price
								? formatNumber(row.land_final_estimate[0].total_price) + " đ"
								: row.apartment_final_estimate &&
								  row.apartment_final_estimate[0] &&
								  row.apartment_final_estimate[0].total_price
								? formatNumber(row.apartment_final_estimate[0].total_price) +
								  " đ"
								: "-"
						}}
					</p>
				</template>
				<template slot="total_area" slot-scope="total_area">
					<p class="text-none mb-0">
						{{ total_area ? formatNumber(total_area) : 0 }} m
						<sup>2</sup>
					</p>
				</template>
				<template slot="created_by" slot-scope="created_by">
					<p class="text-none mb-0">{{ created_by }}</p>
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
			<!-- <div class="total position-absolute">
        (*) Giá trị chỉ mang tính chất tham khảo
    </div> -->
		</div>
	</div>
	<!-- <div v-else class="table-wrapper" style="margin: 0;">
		<div
			class="table-detail position-relative empty-data"
			style="overflow: scroll;max-height: 76vh;"
		>
			<b-card
				:class="{ ['border-' + configColor(element)]: true }"
				class="card_container mb-3"
				v-for="element in listCertificates"
				:key="element.id + '_' + element.status"
			>
				<div class="col-12 d-flex mb-2 justify-content-between">
					<span
						@click="handleDetail(element.id, element)"
						class="content_id"
						:class="
							`bg-${configColor(element)}-15 text-${configColor(element)}`
						"
						>TSSB_{{ element.id }}</span
					>
				</div>
				<div class="property-content mb-2 d-flex color_content">
					<div class="label_container d-flex">
						<div v-if="element.appraises" class="d-flex">
							<span style="font-weight: 500"
								><strong class="d_inline mr-1">Địa chỉ:</strong
								><span :id="element.id + 'all'" class="text-left">{{
									element.appraises.full_address.length > 25
										? element.appraises.full_address.substring(25, 0) + "..."
										: element.appraises.full_address
								}}</span></span
							>
							<b-tooltip :target="(element.id + 'all').toString()">{{
								element.appraises.full_address
							}}</b-tooltip>
						</div>
						<div v-if="element.apartment" class="d-flex">
							<span style="font-weight: 500"
								><strong class="d_inline mr-1">Địa chỉ:</strong
								><span :id="element.id + 'all'" class="text-left">{{
									element.apartment.full_address.length > 25
										? element.apartment.full_address.substring(25, 0) + "..."
										: element.appraises.full_address
								}}</span></span
							>
							<b-tooltip :target="(element.id + 'all').toString()">{{
								element.apartment.full_address
							}}</b-tooltip>
						</div>
					</div>
				</div>
				<div class="row" style="margin: 0">
					<div
						class="col-7  property-content mb-2 d-flex color_content"
						style="padding:0;"
					>
						<div class="label_container d-flex">
							<div class="d-flex">
								<span style="font-weight: 500"
									><strong class="d_inline mr-1">Loại tài sản:</strong
									><span class="text-capitalize">{{
										element.asset_type.description.toLowerCase()
									}}</span></span
								>
							</div>
						</div>
					</div>
					<div
						class="col-5 property-content mb-2 d-flex color_content"
						style="padding:0;justify-content: right;"
					>
						<div class="label_container d-flex">
							<div class="d-flex">
								<span style="font-weight: 500"
									><strong class="d_inline mr-1">Vị trí:</strong
									><span class="text-none">{{
										element.front_side
											? "Mặt tiền"
											: element.front_side === 0
											? "Hẻm"
											: "-"
									}}</span></span
								>
							</div>
						</div>
					</div>
				</div>
				<div class="property-content mb-2 d-flex color_content">
					<div class="label_container d-flex">
						<div class="d-flex">
							<span :id="element.id + 'card'" style="font-weight: 500"
								><strong class="d_inline mr-1">Tổng diện tích:</strong
								><span class="text-none"
									>{{
										element.total_area ? formatNumber(element.total_area) : 0
									}}
									m<sup>2</sup></span
								></span
							>
							<b-tooltip
								placement="right"
								:target="(element.id + 'card').toString()"
								>{{ element.appraise_asset }}</b-tooltip
							>
						</div>
					</div>
				</div>
				<div class="property-content mb-2 d-flex color_content">
					<div class="label_container d-flex">
						<div class="d-flex">
							<span style="font-weight: 500"
								><strong class="d_inline mr-1">Tổng giá trị(VNĐ):</strong
								><span class="text-none">{{
									element.total_price ? formatPrice(element.total_price) : "-"
								}}</span></span
							>
						</div>
					</div>
				</div>
				<div class="property-content mb-2 d-flex color_content">
					<div class="label_container d-flex">
						<div class="d-flex">
							<span style="font-weight: 500"
								><strong class="d_inline mr-1">Ngày tạo:</strong
								><span class="public_date">{{
									formatDate(element.created_at)
								}}</span></span
							>
						</div>
					</div>
				</div>
				<div class="property-content mb-2 d-flex color_content">
					<div class="label_container d-flex">
						<div class="d-flex">
							<span style="font-weight: 500"
								><strong class="d_inline mr-1">Người tạo:</strong
								><span class="text-capitalize">{{
									element.created_by.name
								}}</span></span
							>
						</div>
					</div>
				</div>
			</b-card>
		</div>
		<div class="pagination-wrapper" style="margin-bottom: 20px;">
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
	</div> -->
</template>
<script>
import { BDropdown, BDropdownItem, BTooltip } from "bootstrap-vue";
import moment from "moment";
import { BCard, BRow, BCol, BFormGroup, BFormInput } from "bootstrap-vue";
export default {
	name: "Tables",
	props: ["listCertificates", "pagination", "isLoading"],
	data() {
		return {
			selectedRowKeys: [],
			view: false,
			add: false,
			edit: false,
			deleted: false,
			accept: false,
			activeStatus: false,
			pageSizeOptions: [
				{ value: "10", label: "10" },
				{ value: "20", label: "20" },
				{ value: "30", label: "30" }
			]
		};
	},
	components: {
		"b-dropdown": BDropdown,
		"b-dropdown-item": BDropdownItem,
		"b-tooltip": BTooltip,
		BCard,
		BRow,
		BCol,
		BFormGroup,
		BFormInput
	},
	computed: {
		columns() {
			let dataColumn = [
				{
					title: "Mã TSTĐ",
					align: "center",
					scopedSlots: { customRender: "id" },
					dataIndex: "id",
					width: "30px",
					hiddenItem: false
				},
				{
					title: "Loại tài sản",
					align: "center",
					scopedSlots: { customRender: "description" },
					dataIndex: "asset_type.description",
					width: "30px",
					hiddenItem: false
				},
				{
					title: "Vị trí",
					class: "optional-data",
					align: "center",
					scopedSlots: { customRender: "front_side" },
					dataIndex: "properties[0].front_side",
					width: "70px",
					hiddenItem: false
				},
				{
					title: "Địa chỉ tài sản",
					class: "optional-data",
					align: "left",
					scopedSlots: { customRender: "property_name" },
					dataIndex: "full_address",
					width: "220px",
					hiddenItem: false
				},
				{
					title: "Tổng giá trị sơ bộ",
					class: "optional-data",
					align: "center",
					scopedSlots: { customRender: "total_price" },
					dataIndex: "total_price",
					width: "100px",
					hiddenItem: false
				},

				{
					title: "Người tạo",
					align: "left",
					scopedSlots: { customRender: "created_by" },
					dataIndex: "created_by.name",
					width: "30px",
					hiddenItem: !this.activeStatus
				},
				{
					title: "Ngày tạo",
					align: "right",
					scopedSlots: { customRender: "created_at" },
					dataIndex: "created_at",
					width: "30px",
					hiddenItem: false
				}
			];
			return dataColumn.filter(item => item.hiddenItem === false);
		}
	},
	created() {
		// fix_permission
		const permission = this.$store.getters.currentPermissions;
		permission.forEach(value => {
			if (value === "VIEW_PRICE") {
				this.view = true;
			}
			if (value === "ADD_PRICE") {
				this.add = true;
			}
			if (value === "EDIT_PRICE") {
				this.edit = true;
			}
			if (value === "DELETE_PRICE") {
				this.deleted = true;
			}
			if (value === "ACCEPT_PRICE") {
				this.accept = true;
			}
		});
	},
	mounted() {},
	beforeMount() {
		this.getProfiles();
	},
	methods: {
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
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
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
		async getProfiles() {
			const profile = this.$store.getters.profile;
			if (profile && profile.data.user.roles[0].name.slice(-5) === "ADMIN") {
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
		format(value) {
			let num = (value / 1).toFixed(0).replace(",", ".");
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
		},
		formatNumber(num) {
			// convert number to dot formatNumber
			if (num) {
				let formatedNum = num.toString().replace(".", ",");
				return formatedNum.toString().replace(/^[+-]?\d+/, function(int) {
					return int.replace(/(\d)(?=(\d{3})+$)/g, "$1.");
				});
			}
		},
		async handleDetail(id, data) {
			if (data.asset_type && data.asset_type.acronym === "CC") {
				this.$router
					.push({
						name: "price_estimates.detail",
						query: {
							id: data.id
						}
					})
					.catch(_ => {});
			} else {
				this.$router
					.push({
						name: "price_estimates.detail",
						query: { id: data.id }
					})
					.catch(_ => {});
			}
		},
		onSizeChange(pageSize) {
			const pagination = { ...this.pagination, pageSize: Number(pageSize) };
			this.handleTableChange(pagination);
		},
		onPaginationChange(current) {
			const pagination = { ...this.pagination, current: Number(current) };
			this.handleTableChange(pagination);
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

.full-address {
	width: 500px;
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
/deep/ .optional-data {
	@media (max-width: 1024px) {
		display: none;
	}
}
.text-none {
	text-transform: none;
}

.status {
	// color: #610bec;
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

.link-detail {
	white-space: nowrap;
	text-transform: uppercase;
	background: transparent;
	border: none;

	&:hover,
	&:focus,
	&:active {
		color: #faa831;
		border: none;
		outline: none;
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

	/deep/ .ant-table-wrapper .ant-spin-container .ant-table {
		border: 1px solid #dee6ee;
	}

	/deep/ .ant-table-column-title {
		color: #00507c;
		// font-family: 'SVN-Gilroy';
		// font-weight: 600;
		//
		// line-height: 20px;
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
		margin-top: 18px;
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
}
</style>
