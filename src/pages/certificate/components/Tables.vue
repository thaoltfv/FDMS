<template>
<div>
  <div class="table-detail position-relative empty-data">
    <a-table bordered
             :columns="columns"
             :data-source="listCertificates"
             :loading="isLoading"
             class="table-property"
             :rowKey="record => record.id"
             @change="handleTableChange"
             :pagination="{
                 ...pagination,
                 showSizeChanger: true,
                 pageSizeOptions: ['10', '20', '30'],
               }">
      <!--Custom type table-->
      <template slot="id" slot-scope="id, data">
        <p class="mb-0 text-none link-detail" @click="handleDetail(id, data)">
          {{'TSTD_' + id}}
        </p>
      </template>
      <template slot="description" slot-scope="description">
        <p class="text-none mb-0">{{ description }}</p>
      </template>
      <template slot="front_side" slot-scope="front_side">
        <p class="status text-none">
          {{front_side === 1 ? 'Mặt tiền' : 'Hẻm'}}
        </p>
      </template>
      <template slot="description" slot-scope="description">
        <p class="text-none mb-0">{{ description }}</p>
      </template>
      <template slot="property_name" slot-scope="property_name, data">
        <p :id="data.id" class="full-address text-left">{{ property_name }}</p>
        <b-tooltip :target="(data.id).toString()">{{ property_name }}</b-tooltip>
      </template>
      <template slot="total_asset_price" slot-scope="total_asset_price">
        <p class="text-none mb-0">{{ format(total_asset_price) + 'đ' }}</p>
      </template>
      <template slot="created_at" slot-scope="created_at">
        <p class="public_date mb-0">{{ formatDate(created_at) }}</p>
      </template>
      <template slot="appraise_land_sum_area" slot-scope="appraise_land_sum_area">
        <p class="text-none mb-0">{{appraise_land_sum_area ? formatNumber(appraise_land_sum_area) : 0}} m <sup>2</sup></p>
      </template>
      <template slot="total_construction_area" slot-scope="total_construction_area">
        <p class="text-none mb-0">{{total_construction_area ? formatNumber(total_construction_area) : 0}} m <sup>2</sup></p>
      </template>
    </a-table>
    <div class="total position-absolute">
        (*) Giá trị chỉ mang tính chất tham khảo
    </div>
  </div>
</div>
</template>
<script>

import {BTooltip} from 'bootstrap-vue'
import moment from 'moment'
export default {
	name: 'Tables',
	props: ['listCertificates', 'pagination', 'isLoading'],
	data () {
		return {
			selectedRowKeys: [],
			booleanTest: false,
			view: false,
			add: false,
			edit: false,
			deleted: false,
			accept: false,
			activeStatus: false
		}
	},
	components: {
		'b-tooltip': BTooltip
	},
	computed: {
		columns () {
			let dataColumn = [
				{
					title: 'Mã TSTĐ',
					align: 'center',
					scopedSlots: {customRender: 'id'},
					dataIndex: 'id',
					width: '30px',
					sorter: (a, b) => a.id - b.id,
					sortDirections: ['descend', 'ascend'],
					hiddenItem: false
				},
				{
					title: 'Loại tài sản',
					align: 'center',
					scopedSlots: {customRender: 'description'},
					dataIndex: 'asset_type.description',
					width: '30px',
					sorter: (a, b) => a.asset_type.description.length - b.asset_type.description.length,
					sortDirections: ['descend', 'ascend'],
					hiddenItem: false
				},
				{
					title: 'Vị trí',
					align: 'center',
					scopedSlots: {customRender: 'front_side'},
					dataIndex: 'properties[0].front_side',
					width: '30px',
					sorter: (a, b) => a.properties[0] ? a.properties[0].front_side : 0 - b.properties[0] ? b.properties[0].front_side : 0,
					sortDirections: ['descend', 'ascend'],
					hiddenItem: false
				},
				{
					title: 'Tên tài sản',
					align: 'left',
					scopedSlots: {customRender: 'property_name'},
					dataIndex: 'appraise_asset',
					width: '140px',
					sorter: (a, b) => a.appraise_asset.length - b.appraise_asset.length,
					sortDirections: ['descend', 'ascend'],
					hiddenItem: false
				},
				{
					title: 'Tổng DT đất',
					align: 'right',
					scopedSlots: {customRender: 'appraise_land_sum_area'},
					dataIndex: 'properties[0].appraise_land_sum_area',
					width: '140px',
					sorter: (a, b) => a.properties[0] ? a.properties[0].appraise_land_sum_area : 0 - b.properties[0] ? b.properties[0].appraise_land_sum_area : 0,
					sortDirections: ['descend', 'ascend'],
					hiddenItem: false
				},
				{
					title: 'Tổng DT xây dựng',
					align: 'right',
					scopedSlots: {customRender: 'total_construction_area'},
					dataIndex: 'tangible_assets[0].total_construction_area',
					width: '30px',
					sorter: (a, b) => a.tangible_assets[0] ? a.tangible_assets[0].total_construction_area : 0 - b.tangible_assets[0] ? b.tangible_assets[0].total_construction_area : 0,
					sortDirections: ['descend', 'ascend'],
					hiddenItem: false
				},
				{
					title: 'Tổng giá trị (*)',
					align: 'right',
					scopedSlots: {customRender: 'total_asset_price'},
					dataIndex: 'total_asset_price',
					width: '30px',
					sorter: (a, b) => a.total_asset_price - b.total_asset_price,
					sortDirections: ['descend', 'ascend'],
					hiddenItem: false
				},
				{
					title: 'Người tạo',
					align: 'left',
					dataIndex: 'created_by.name',
					width: '30px',
					sorter: (a, b) => a.created_by.name.length - b.created_by.name.length,
					sortDirections: ['descend', 'ascend'],
					hiddenItem: !this.activeStatus
				},
				{
					title: 'Ngày tạo',
					align: 'right',
					scopedSlots: {customRender: 'created_at'},
					dataIndex: 'created_at',
					width: '30px',
					sorter: (a, b) => moment(a.created_at).format('YYYYMMDD') - moment(b.created_at).format('YYYYMMDD'),
					sortDirections: ['descend', 'ascend'],
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
	beforeMount () {
		this.getProfiles()
	},
	methods: {
		async getProfiles () {
			const profile = this.$store.getters.profile
			if (profile && profile.data.user.roles[0].name.slice(-5) === 'ADMIN') {
				this.activeStatus = true
			}
		},
		handleTableChange (pagination, filters, sorter) {
			this.$emit('handleChange', pagination, 'Draft', filters, sorter)
		},
		onSelectChange (selectedRowKeys) {
			this.selectedRowKeys = selectedRowKeys
		},
		formatDate (value) {
			return moment(String(value)).format('DD/MM/YYYY')
		},
		formatNumber (value) {
			let num = (value / 1).toFixed(2).replace('.', ',')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
		},
		format (value) {
			let num = (value / 1).toFixed(0).replace(',', '.')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
		},
		async handleDetail (id, data) {
			this.$router.push({
				name: 'certificate.detail',
				query: {
					id: id,
					version: data.version && data.version.length > 0 ? data.version[data.version.length - 1].version : 1
				}
			}).catch(_ => {
			})
		}
	}
}
</script>

<style scoped lang="scss">
  .full-address {
    width: 350px;
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
    color: #610bec;
    margin-bottom: 0;

    &.red {
      color: red;
    }
    &.orange {
      color: #FAA831;
    }
  }
  .link-detail {
    color: #FAA831;
    font-weight: 600;
    cursor: pointer;
    text-align: center;
  }
  .total {
    color: #000000;
    bottom: 17px;
    right: 0;
    @media (max-width: 418px) {
      position: relative !important;
      text-align: center;
      margin-top: 20px;
    }
  }
</style>
