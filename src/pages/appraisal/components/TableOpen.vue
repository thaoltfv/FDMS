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
               }">
      <!--Custom type table-->
<!--      <template slot="id" slot-scope="id">-->
<!--        <p class="mb-0 text-none link-detail">-->
<!--          {{'HSTD_' + id}}-->
<!--        </p>-->
<!--      </template>-->
      <template slot="id" slot-scope="id, property">
        <div class="d-flex justify-content-center align-items-center position-relative">
          <button @click.prevent="handleDetail(id, property)" class="link-detail">{{'HSTD_' + id}}</button>
          <!-- <b-dropdown class="dropdown-container" no-caret>
            <template #button-content>
              <img src="../../../assets/icons/ic_more.svg" alt="">
            </template>
            <b-dropdown-item @click.prevent="openPrint(id)">
              <div class="dropdown-item-container"><img src="../../../assets/icons/ic_download.svg" alt="print">Tải phụ lục 1</div>
            </b-dropdown-item>
            <b-dropdown-item @click.prevent="openPrintAppendix(id)">
              <div class="dropdown-item-container"><img src="../../../assets/icons/ic_download.svg" alt="print">Tải phụ lục 2</div>
            </b-dropdown-item>
            <b-dropdown-item @click.prevent="openPrintImage(id)">
              <div class="dropdown-item-container"><img src="../../../assets/icons/ic_download.svg" alt="print">Tải phụ lục hình ảnh</div>
            </b-dropdown-item>
          </b-dropdown> -->
        </div>
      </template>
      <template slot="status" slot-scope="status">
        <p class="status text-none">
          {{status === 2 ? 'Mới' : ''}}
        </p>
      </template>
      <template slot="created_at" slot-scope="created_at">
        <p class="public_date mb-0">{{ formatDate(created_at) }}</p>
      </template>
      <template slot="certificate_date" slot-scope="certificate_date">
        <p class="public_date mb-0">{{ certificate_date ? formatDate(certificate_date) : '' }}</p>
      </template>
       <template slot="document_date" slot-scope="document_date">
        <p class="public_date mb-0">{{ document_date ? formatDate(document_date) : '' }}</p>
      </template>
      <template slot="petitioner_name" slot-scope="petitioner_name">
        <p class="text-none mb-0">{{ petitioner_name }}</p>
      </template>
      <template slot="total_asset_price" slot-scope="total_asset_price">
        <p class="text-none mb-0">{{ format(total_asset_price) + 'đ' }}</p>
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

import {BDropdown, BDropdownItem, BTooltip} from 'bootstrap-vue'
import moment from 'moment'
import Certificate from '@/models/Certificate'
export default {
	name: 'Tables',
	props: ['listCertificates', 'pagination', 'isLoading'],
	data () {
		return {
			selectedRowKeys: [],
			activeStatus: false
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
					title: 'Mã HSTD',
					align: 'center',
					scopedSlots: {customRender: 'id'},
					dataIndex: 'id',
					width: '90px',
					sorter: (a, b) => a.id - b.id,
					sortDirections: ['descend', 'ascend'],
					hiddenItem: false
				},
				{
					title: 'Mã NV',
					align: 'right',
					dataIndex: 'ticket_num',
					width: '90px',
					sorter: (a, b) => a.ticket_num - b.ticket_num,
					sortDirections: ['descend', 'ascend'],
					hiddenItem: false
				},
				{
					title: 'Số CT',
					align: 'right',
					dataIndex: 'certificate_num',
					sorter: (a, b) => a.certificate_num.length - b.certificate_num.length,
					sortDirections: ['descend', 'ascend'],
					hiddenItem: false
				},
				{
					title: 'Ngày CT',
					align: 'right',
					scopedSlots: {customRender: 'certificate_date'},
					dataIndex: 'certificate_date',
					width: '90px',
					sorter: (a, b) => moment(a.certificate_date).format('YYYYMMDD') - moment(b.certificate_date).format('YYYYMMDD'),
					sortDirections: ['descend', 'ascend'],
					hiddenItem: false
				},
				{
					title: 'Số HĐ',
					align: 'right',
					dataIndex: 'document_num',
					width: '90px',
					sorter: (a, b) => a.document_num - b.document_num,
					sortDirections: ['descend', 'ascend'],
					hiddenItem: false
				},
				{
					title: 'Ngày HĐ',
					align: 'right',
					scopedSlots: {customRender: 'document_date'},
					dataIndex: 'document_date',
					width: '90px',
					sorter: (a, b) => moment(a.document_date).format('YYYYMMDD') - moment(b.document_date).format('YYYYMMDD'),
					sortDirections: ['descend', 'ascend'],
					hiddenItem: false
				},
				{
					title: 'Tên khách hàng',
					align: 'left',
					dataIndex: 'petitioner_name',
					sorter: (a, b) => a.petitioner_name - b.petitioner_name,
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
					title: 'Mục đích',
					align: 'left',
					dataIndex: 'appraise_purpose.name',
					sorter: (a, b) => a.appraise_purpose.name.length - b.appraise_purpose.name.length,
					sortDirections: ['descend', 'ascend'],
					hiddenItem: false
				},
				{
					title: 'Ngày tạo',
					align: 'right',
					scopedSlots: {customRender: 'created_at'},
					dataIndex: 'created_at',
					width: '90px',
					sorter: (a, b) => moment(a.created_at).format('YYYYMMDD') - moment(b.created_at).format('YYYYMMDD'),
					sortDirections: ['descend', 'ascend'],
					hiddenItem: false
				},
				{
					title: 'Người thực hiện',
					align: 'left',
					dataIndex: 'created_by.name',
					sorter: (a, b) => a.created_by.name.length - b.created_by.name.length,
					sortDirections: ['descend', 'ascend'],
					hiddenItem: !this.activeStatus
				}
			]
			return dataColumn.filter(item => item.hiddenItem === false)
		}
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
			this.$emit('handleChange', pagination, 'Open', filters, sorter)
		},
		onSelectChange (selectedRowKeys) {
			this.selectedRowKeys = selectedRowKeys
		},
		async openPrint (id) {
			this.isSubmit = true
			await Certificate.getPrint(id).then((resp) => {
				const file = resp.data
				if (file) {
					const fileLink = document.createElement('a')
					fileLink.href = file.url
					fileLink.setAttribute('download', file.file_name)
					document.body.appendChild(fileLink)
					fileLink.click()
					fileLink.remove()
					window.URL.revokeObjectURL(fileLink)
					this.isSubmit = false
				}
			}
			)
		},
		async openPrintAppendix (id) {
			this.isSubmit = true
			await Certificate.getPrintAppendix(id).then((resp) => {
				const file = resp.data
				if (file) {
					const fileLink = document.createElement('a')
					fileLink.href = file.url
					fileLink.setAttribute('download', file.file_name)
					document.body.appendChild(fileLink)
					fileLink.click()
					fileLink.remove()
					window.URL.revokeObjectURL(fileLink)
					this.isSubmit = false
				}
			}
			)
		},
		async openPrintImage (id) {
			this.isSubmit = true
			await Certificate.getPrintImage(id).then((resp) => {
				const file = resp.data
				if (file) {
					const fileLink = document.createElement('a')
					fileLink.href = file.url
					fileLink.setAttribute('download', file.file_name)
					document.body.appendChild(fileLink)
					fileLink.click()
					fileLink.remove()
					window.URL.revokeObjectURL(fileLink)
					this.isSubmit = false
				}
			}
			)
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
		async handleDetail (id) {
			this.$router.push({
				name: 'appraisal.detail',
				query: {
					id: id
				}
			}).catch(_ => {
			})
		}
	}
}
</script>

<style scoped lang="scss">
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
    color: #2d9000;
    margin-bottom: 0;

    &.red {
      color: red;
    }
    &.orange {
      color: #FAA831;
    }
  }
  .link-detail {
    white-space: nowrap;
    color: #FAA831;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    background: transparent;
    border: none;

    &:hover, &:focus, &:active {
      color: #FAA831;
      border: none;
      outline: none;
    }
  }
  .dropdown-container {
    background: #FAA831;
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
</style>
