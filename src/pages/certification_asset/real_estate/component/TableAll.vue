<template>
  <div v-if="!isMobile()" class="table-wrapper">
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
            {{ "BDS_" + id }}
          </button>
        </template>
        <template slot="description" slot-scope="description">
          <p class="mb-0 text-capitalize">
            {{ description.toLowerCase() }}
          </p>
        </template>
        <template slot="front_side" slot-scope="front_side">
          <p class="status text-none">
            {{ front_side ? 'Mặt tiền' : front_side === 0 ? 'Hẻm' : '-' }}
          </p>
        </template>
        <template slot="status" slot-scope="status">
          <div class="d-flex justify-content-center align-items-center position-relative">
            <div v-if="status === 1" class="status-color bg-info" />
            <div v-if="status === 2" class="status-color bg-primary" />
            <div v-if="status === 3" class="status-color bg-warning" />
            <div v-if="status === 4" class="status-color bg-success" />
            <div v-if="status === 5" class="status-color bg-secondary" />
			<div v-if="status === 6" class="status-color bg-control" />
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
        <template slot="total_area" slot-scope="total_area">
          <p class="text-none mb-0">{{ total_area ? formatNumber(total_area) : 0 }} m
            <sup>2</sup>
          </p>
        </template>
        <!-- <template slot="total_construction_area" slot-scope="total_construction_area">
          <p class="text-none mb-0">{{ total_construction_area ? formatNumber(total_construction_area) : 0 }} m
            <sup>2</sup>
          </p>
        </template> -->
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
  <div v-else class="table-wrapper" style="margin: 0;">
    <div class="table-detail position-relative empty-data" style="overflow: scroll;max-height: 76vh;">
		<b-card :class="{['border-' + configColor(element)]: true}" class="card_container mb-3" v-for="element in listCertificates" :key="element.id+'_'+element.status">
            <div class="col-12 d-flex mb-2 justify-content-between">
              <span @click="handleDetail(element.id, element)" class="content_id" :class="`bg-${configColor(element)}-15 text-${configColor(element)}`">BDS_{{element.id}}</span>
            </div>
			<div class="property-content mb-2 d-flex color_content">
              <div class="label_container d-flex">
                <div class="d-flex">
                <span style="font-weight: 500"><strong class="d_inline mr-1">Tên tài sản:</strong><span :id="element.id + 'all'" class="text-left">{{ element.appraise_asset.substring(25,0)+'...'}}</span></span>
				<b-tooltip :target="(element.id + 'all').toString()">{{ element.appraise_asset }}</b-tooltip>
                </div>
              </div>
            </div>
			<div class="row" style="margin: 0">
				<div class="col-7  property-content mb-2 d-flex color_content" style="padding:0;">
					<div class="label_container d-flex">
						<div class="d-flex">
						<span style="font-weight: 500"><strong class="d_inline mr-1">Loại tài sản:</strong><span class="text-capitalize">{{element.asset_type.description.toLowerCase()}}</span></span>
						</div>
					</div>
				</div>
				<div class="col-5 property-content mb-2 d-flex color_content" style="padding:0;justify-content: right;">
					<div class="label_container d-flex">
						<div class="d-flex">
						<span style="font-weight: 500"><strong class="d_inline mr-1">Vị trí:</strong><span class="text-none">{{element.front_side ? 'Mặt tiền' : element.front_side === 0 ? 'Hẻm' : '-' }}</span></span>
						</div>
					</div>
				</div>
			</div>
			<div class="property-content mb-2 d-flex color_content">
				<div class="label_container d-flex">
					<div class="d-flex">
					<span style="font-weight: 500"><strong class="d_inline mr-1">Tổng diện tích:</strong><span class="text-none">{{ element.total_area ? formatNumber(element.total_area) : 0 }} m<sup>2</sup></span></span>
					</div>
				</div>
			</div>
			<div class="property-content mb-2 d-flex color_content">
				<div class="label_container d-flex">
					<div class="d-flex">
					<span style="font-weight: 500"><strong class="d_inline mr-1">Tổng giá trị(VNĐ):</strong><span class="text-none">{{element.total_price ? formatPrice(element.total_price) : '-' }}</span></span>
					</div>
				</div>
			</div>
			<div class="property-content mb-2 d-flex color_content">
				<div class="label_container d-flex">
					<div class="d-flex">
					<span style="font-weight: 500"><strong class="d_inline mr-1">Ngày tạo:</strong><span class="public_date">{{ formatDate(element.created_at) }}</span></span>
					</div>
				</div>
			</div>
			<div class="property-content mb-2 d-flex color_content">
				<div class="label_container d-flex">
					<div class="d-flex">
					<span style="font-weight: 500"><strong class="d_inline mr-1">Người tạo:</strong><span class="text-capitalize">{{element.created_by.name}}</span></span>
					</div>
				</div>
			</div>
          </b-card>
	</div>
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
  </div>
</template>
<script>

import { BDropdown, BDropdownItem, BTooltip } from 'bootstrap-vue'
import moment from 'moment'
import {
	BCard,
	BRow,
	BCol,
	BFormGroup,
	BFormInput } from 'bootstrap-vue'
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
		'b-tooltip': BTooltip,
		BCard,
		BRow,
		BCol,
		BFormGroup,
		BFormInput,
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
					title: 'Vị trí',
					class: 'optional-data',
					align: 'center',
					scopedSlots: { customRender: 'front_side' },
					dataIndex: 'front_side',
					width: '70px',
					// sorter: (a, b) => a.properties[0].front_side - b.properties[0].front_side,
					// sortDirections: ['descend', 'ascend'],
					hiddenItem: false
				},
				{
					title: 'Tên tài sản',
					class: 'optional-data',
					align: 'left',
					scopedSlots: { customRender: 'property_name' },
					dataIndex: 'appraise_asset',
					width: '140px',
					// sorter: (a, b) => a.appraise_asset.length - b.appraise_asset.length,
					// sortDirections: ['descend', 'ascend'],
					hiddenItem: false
				},
				{
					title: 'Tổng diện tích',
					align: 'right',
					scopedSlots: { customRender: 'total_area' },
					dataIndex: 'total_area',
					width: '30px',
					// sorter: (a, b) => a.total_area - b.total_area,
					// sortDirections: ['descend', 'ascend'],
					hiddenItem: false
				},
				// {
				// 	title: 'Tổng DT xây dựng',
				// 	align: 'right',
				// 	scopedSlots: { customRender: 'total_construction_area' },
				// 	dataIndex: 'total_construction_area',
				// 	width: '30px',
				// 	// sorter: (a, b) => a.total_construction_area - b.total_construction_area,
				// 	// sortDirections: ['descend', 'ascend'],
				// 	hiddenItem: false
				// },
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
    formatPrice (value) {
			let num = parseFloat(value / 1).toFixed(0).replace('.', ',')
			if (num.length > 3 && num.length <= 6) {
				return parseFloat(num / 1000).toFixed(1).replace('.', ',') + ' Nghìn'
			} else if (num.length > 6 && num.length <= 9) {
				return parseFloat(num / 1000000).toFixed(1).replace('.', ',') + ' Triệu'
			} else if (num.length > 9) {
				return parseFloat(num / 1000000000).toFixed(1).replace('.', ',') + ' Tỷ'
			} else if (num < 900) {
				return num + ' đ' // if value < 1000, nothing to do
			}
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.')
		},
		configColor(element) {
			if (element.status == 1) {
				return 'info'
			}
			if (element.status == 2) {
				return 'primary'
			}
			if (element.status == 3) {
				return 'warning'
			}
			if (element.status == 4) {
				return 'success'
			}
			if (element.status == 5) {
				return 'secondary'
			}
			if (element.status == 6) {
				return 'control'
			}
			return 'red'
		},
		isMobile() {
			if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
				return true
			} else {
				return false
			}
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
			if (data.asset_type && data.asset_type.acronym === 'CC') {
				this.$router.push({
					name: 'certification_asset.apartment.detail',
					query: {
						id: data.asset.id
					}
				}).catch(_ => {})
			} else {
				this.$router.push({
					name: 'certification_asset.detail',
					query: { id: data.asset.id }
				}).catch(_ => {})
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
 /deep/
.optional-data {
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
    @media (max-height: 800px) and (min-height: 660px) { // M-MD Screen
      max-height: 75vh !important;
    }
    @media (max-height: 970px) and (min-height: 800px) { // FD Screen
      max-height: 78vh !important;
    }
    @media (min-height: 970px) {  // >2k Screen
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
    color: #FFF;
    font-weight: 600;
    line-height: 1;
  }
  .badgeSuccess {
    background-color: rgba(40,199,111,.12);
    color: #28C76F!important;
  }
  .badgeWarning {
    background-color: rgba(255,159,67,.12);
    color: #FF9F43!important;
  }
  .badgeDanger {
        background-color: rgba(234,84,85,.12);
    color: #EA5455!important;
  }
  .badgeInfo {
    background-color: rgba(0,207,232,.12);
    color: #00CFE8!important;
  }
  .badgePrimary {
    background-color: rgba(115,103,240,.12);
    color: #7367F0!important;
  }
  .content_id {
    border-radius: 5px;
    padding: 0px 3px;
    font-weight: 500;
    cursor: pointer;
    &_primary {
      color: #007EC6;
      background-color: #E3F5FF;
    }
    &_secondary {
      color: #FFFFFF;
      background-color: #8B94A3;
    }
    &_warning {
      color: #FF963D;
      background-color: #FFF1E6;
    }
    &_danger {
      color: #FF5E7B;
      background-color: #FFEBEF;
    }
    &_success {
      color: #FFFFFF;
      background-color: #26BF7F;
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
      border: 1px solid #B5E5FF
    }
    &_secondary {
      border: 1px solid #8B94A3
    }
    &_warning {
      border: 1px solid #FFD1AD
    }
    &_danger {
      border: 1px solid #FFC8D3
    }
    &_success {
      border: 1px solid #26BF7F;
      background-color: #EAFFF6;
    }
  }
  .container_kanban {
	height: fit-content;
    background-color: #F6F7FB;
    border-radius: 5px;
    border: 1px solid #E8E8E8;
    border-top: 4px solid;
    border-bottom: none;
    border-left: none;
    border-right: none;
    min-width: 17rem;
  }
  // border
  .border {
    &_primary {
      color:#72CDFF
    }
    &_secondary {
      color:#9EA6B4
    }
    &_danger {
      color:#FF7E9B
    }
    &_warning {
      color:#FFB880
    }
    &_success {
      color:#3DDC99
    }
  }
  // title
  .title {
    font-weight: 600;
    &_primary{
      color:#00507C;
    }
    &_secondary{
      color:#9EA6B4;
    }
    &_warning {
      color:#FFB880;
    }
    &_danger {
      color:#FF5E7B;
    }
    &_success {
      color:#3DDC99;
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
    &_primary{
      background-color: #007EC6;
    }
    &_warning{
      background-color: #FF963D;
    }
     &_danger{
      background-color: #FF5E7B;
    }
    &_success{
      background-color: #26bf7f;
    }
    &_secondary{
      background-color: #8B94A3;
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
      min-width: 120px
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
    border-color: red !important
  }
}
</style>
