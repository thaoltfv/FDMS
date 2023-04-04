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
					:row-class-name="(_record, index) => (index % 2 === 1 ? 'table-striped' : null)"
					:pagination="false"
				>
				<!--Custom type table-->
				<template slot="id" slot-scope="id, data">
					<button @click.prevent="handleDetail(id, data)" class="link-detail">
						{{ `DS_` + id }}
					</button>
				</template>
				<template slot="description" slot-scope="description">
					<p class="mb-0 text-capitalize">
						{{ description.toLowerCase() }}
					</p>
				</template>
				<template slot="status" slot-scope="status">
					<div class="d-flex justify-content-center align-items-center position-relative">
						<div v-if="status === 1" class="status-color bg-info" />
						<div v-if="status === 2" class="status-color bg-primary" />
						<div v-if="status === 3" class="status-color bg-warning" />
						<div v-if="status === 4" class="status-color bg-success" />
						<div v-if="status === 5" class="status-color bg-secondary" />
						<b-dropdown class="dropdown-container" no-caret>
							<template #button-content>
								<img src="@/assets/icons/ic_more.svg" alt="">
							</template>
							<b-dropdown-item>Action</b-dropdown-item>
						</b-dropdown>
					</div>
				</template>
				<template slot="property_name" slot-scope="property_name, data">
					<p :id="data.id + 'all'" class="full-address text-left">{{ property_name }}</p>
					<b-tooltip :target="(data.id + 'all').toString()">{{ property_name }}</b-tooltip>
				</template>
				<template slot="created_at" slot-scope="created_at">
					<p class="public_date mb-0">{{ formatDate(created_at) }}</p>
				</template>
				<template slot="total_price" slot-scope="total_price">
					<p class="text-none mb-0">{{total_price ? formatNumber(total_price) + ' đ' : '-' }}</p>
				</template>
				<template slot="price" slot-scope="price">
					<p class="text-none mb-0">{{price ? formatNumber(price.unit_price) + ' đ' : '-' }}</p>
				</template>
				<template slot="appraise_land_sum_area" slot-scope="appraise_land_sum_area">
					<p class="text-none mb-0">{{ appraise_land_sum_area ? formatNumber(appraise_land_sum_area) : 0 }} m
						<sup>2</sup>
					</p>
				</template>
				<template slot="total_construction_area" slot-scope="total_construction_area">
					<p class="text-none mb-0">{{ total_construction_area ? formatNumber(total_construction_area) : 0 }} m
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
					<a-select ref="select" :value="Number(pagination.pageSize)" style="width: 71px" :options="pageSizeOptions"
						@change="onSizeChange" />
					hàng
				</div>
				<a-pagination :current="Number(pagination.current)" :page-size="Number(pagination.pageSize)"
					:total="Number(pagination.total)"
					:show-total="(total, range) => `Kết quả hiển thị ${range[0]} - ${range[1]} của ${pagination.total} tài sản`"
					@change="onPaginationChange">
				</a-pagination>
			</div>
			<!-- <div class="total position-absolute">
				(*) Giá trị chỉ mang tính chất tham khảo
		</div> -->
		</div>
	</div>
</template>
<script>

import { BDropdown, BDropdownItem, BTooltip } from 'bootstrap-vue'
import moment from 'moment'
export default {
	name: 'Tables',
	props: ['listCertificates', 'pagination', 'isLoading'],
	data () {
		return {
			selectedRowKeys: [],
			view: false,
			add: false,
			edit: false,
			deleted: false,
			accept: false,
			activeStatus: false,
			pageSizeOptions: [
				{ value: '10', label: '10' },
				{ value: '20', label: '20' },
				{ value: '30', label: '30' }
			]
		}
	},
	components: {
		'b-dropdown': BDropdown,
		'b-dropdown-item': BDropdownItem,
		'b-tooltip': BTooltip
	},
	computed: {
		columns () {
			let dataColumn = [
				{
					title: 'Mã TSTĐ',
					align: 'center',
					scopedSlots: { customRender: 'id' },
					dataIndex: 'id',
					width: '30px',
					// sorter: (a, b) => a.id - b.id,
					// sortDirections: ['descend', 'ascend'],
					hiddenItem: false
				},
				{
					title: 'Loại tài sản',
					align: 'center',
					scopedSlots: { customRender: 'description' },
					dataIndex: 'asset_type.description',
					width: '30px',
					// sorter: (a, b) => a.asset_type.description.length - b.asset_type.description.length,
					// sortDirections: ['descend', 'ascend'],
					hiddenItem: false
				},
				{
					title: 'Tên tài sản',
					align: 'left',
					scopedSlots: { customRender: 'property_name' },
					dataIndex: 'name',
					width: '140px',
					// sorter: (a, b) => a.appraise_asset.length - b.appraise_asset.length,
					// sortDirections: ['descend', 'ascend'],
					hiddenItem: false
				},
				{
					title: 'Đơn giá ',
					align: 'right',
					scopedSlots: { customRender: 'price' },
					dataIndex: 'price',
					width: '30px',
					// sorter: (a, b) => a.total_price - b.total_price,
					// sortDirections: ['descend', 'ascend'],
					hiddenItem: false
				},
				{
					title: 'Tổng giá trị (VNĐ)',
					align: 'right',
					scopedSlots: { customRender: 'total_price' },
					dataIndex: 'total_price',
					width: '30px',
					// sorter: (a, b) => a.total_price - b.total_price,
					// sortDirections: ['descend', 'ascend'],
					hiddenItem: false
				},
				{
					title: 'Người tạo',
					align: 'left',
					scopedSlots: { customRender: 'created_by' },
					dataIndex: 'created_by.name',
					width: '30px',
					// sorter: (a, b) => a.created_by.name.length - b.created_by.name.length,
					// sortDirections: ['descend', 'ascend'],
					hiddenItem: !this.activeStatus
				},
				{
					title: 'Ngày tạo',
					align: 'right',
					scopedSlots: { customRender: 'created_at' },
					dataIndex: 'created_at',
					width: '30px',
					// sorter: (a, b) => moment(a.created_at).format('YYYYMMDD') - moment(b.created_at).format('YYYYMMDD'),
					// sortDirections: ['descend', 'ascend'],
					hiddenItem: false
				},
				{
					title: 'Trạng thái',
					align: 'center',
					scopedSlots: { customRender: 'status' },
					dataIndex: 'status',
					width: '30px',
					// filters: [
					//   { text: 'Nháp', value: 1 },
					//   { text: 'Đã xác nhận', value: 2 },
					//   { text: 'Đã được chọn', value: 3 },
					//   { text: 'Hoàn thành', value: 4 },
					//   { text: 'Đã hủy', value: 5 }
					// ],
					// onFilter: (value, record) => record.status === value,
					// sorter: (a, b) => a.status_text.length - b.status_text.length,
					// sortDirections: ['descend', 'ascend'],
					hiddenItem: false
				}
			]
			return dataColumn.filter(item => item.hiddenItem === false)
		}
	},
	created () {
		// fix_permission
		const permission = this.$store.getters.currentPermissions
		permission.forEach(value => {
			if (value === 'VIEW_PRICE') {
				this.view = true
			}
			if (value === 'ADD_PRICE') {
				this.add = true
			}
			if (value === 'EDIT_PRICE') {
				this.edit = true
			}
			if (value === 'DELETE_PRICE') {
				this.deleted = true
			}
			if (value === 'ACCEPT_PRICE') {
				this.accept = true
			}
		})
	},
	mounted () {
	},
	beforeMount () {
		this.getProfiles()
	},
	methods: {
		showAcronym (acronym) {
			if (acronym === 'KHAC') {
				return 'TSK'
			} else if (acronym === 'DS') {
				return 'DS'
			} else return ''
		},
		async getProfiles () {
			const profile = this.$store.getters.profile
			if (profile && profile.data.user.roles[0].name.slice(-5) === 'ADMIN') {
				this.activeStatus = true
			}
		},
		handleTableChange (pagination, filters, sorter) {
			this.$emit('handleChange', pagination, 'All', filters, sorter)
		},
		onSelectChange (selectedRowKeys) {
			this.selectedRowKeys = selectedRowKeys
		},
		formatDate (value) {
			return moment(String(value)).format('DD/MM/YYYY')
		},
		format (value) {
			let num = (value / 1).toFixed(0).replace(',', '.')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
		},
		formatNumber (num) {
			// convert number to dot formatNumber
			if (num) {
				let formatedNum = num.toString().replace('.', ',')
				return formatedNum.toString().replace(/^[+-]?\d+/, function (int) {
					return int.replace(/(\d)(?=(\d{3})+$)/g, '$1.')
				})
			}
		},
		async handleDetail (id, data) {
			if (data.asset_type.dictionary_acronym === 'DS') {
				if (data.asset_type.acronym === 'MMTB') {
					this.$router.push({ name: 'certification_asset.machine.detail', query: { id: id, asset_type_id: data.asset_type_id } })
				} else if (data.asset_type.acronym === 'PTVT') {
					this.$router.push({ name: 'certification_asset.vehicle.detail', query: { id: id, asset_type_id: data.asset_type_id } })
				}
			} else {
				this.$router.push({ name: 'certification_asset.other_purpose.detail', query: { id: id, asset_type_id: data.asset_type_id } })
			}
		},
		onSizeChange (pageSize) {
			const pagination = { ...this.pagination, pageSize: Number(pageSize) }
			this.handleTableChange(pagination)
		},
		onPaginationChange (current) {
			const pagination = { ...this.pagination, current: Number(current) }
			this.handleTableChange(pagination)
		}
	}
}
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
		color: #FAA831;
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
		border: 1px solid #DEE6EE;
	}

	/deep/ .ant-table-wrapper .ant-spin-container .ant-table {
		border: 1px solid #DEE6EE;
	}

	/deep/ .ant-table-column-title {
		color: #00507C;
		// font-family: 'SVN-Gilroy';
		// font-weight: 600;
		//
		// line-height: 20px;
	}

	/deep/ .table-striped td {
		background-color: #F6F7FB;
		border-color: #DEE6EE;
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
				background: #007EC6;

				a {
					color: #FFFFFF;
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
</style>
