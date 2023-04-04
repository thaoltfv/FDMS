<template>
  <div>
    <div class="pannel">
      <div class="pannel__content row mx-0 justify-content-between justify-content-lg-end align-items-center">
        <Search @filter-changed="onFilterChange($event)" class="search-box"/>
        <div class="justify-content-between btn-container">
          <button class="btn btn-white text-nowrap index-screen-button" type="button" @click="handlePrintList"><img
            src="../../assets/icons/ic_printer.svg"
            style="margin-right: 8px; width: 13px" alt="search" >In nhiều MSB</button>
          <button class="btn btn-white text-nowrap index-screen-button" :class="selectedRowKeys.length === 0 ? 'disabled' : ''" type="button" @click="exportToExcel"><img
            src="../../assets/icons/ic_export.svg"
            style="margin-right: 8px" alt="search"> Xuất file excel</button>
        </div>
      </div>
    </div>
    <div class="container__table" :class="totalRecord === 0 ? 'empty-data' : ''">
      <a-table bordered
               :columns="columns"
               :data-source="history_estimates"
               :loading="isLoading"
               :row-selection="{ selectedRowKeys: selectedRowKeys, onChange: onSelectChange }"
               :rowKey="record => record.id"
               table-layout="top"
               :pagination="{
                 ...pagination,
               }"
               class="input__data"
               @change="onPageChange"
      >
        <!--Custom type table-->
        <template slot="id" slot-scope="id">
          <p class="input" @click.prevent="handleDetail(id)">{{id}}</p>
        </template>
        <template slot="is_print" slot-scope="is_print">
          <div>{{is_print.report.is_print === true ? 'Có' : 'Không'}}</div>
        </template>
        <template slot="is_update_land_value" slot-scope="is_update_land_value">
          <div>{{is_update_land_value.report.is_update_land_value === true? 'Có' : 'Không'}}</div>
        </template>
        <template slot="is_update_building_value" slot-scope="is_update_building_value">
          <div>{{is_update_building_value.report.is_update_building_value === true? 'Có' : 'Không'}}</div>
        </template>
        <template slot="total_price_update" slot-scope="total_price_update">
          <div>{{formatNumber(total_price_update.report.total_price_update) + ' đ'}}</div>
        </template>
        <template slot="percent_difference_building" slot-scope="percent_difference_building">
          <div>{{percent_difference_building.report.percent_difference_building ? percent_difference_building.report.percent_difference_building + ' %' :  '' }}</div>
        </template>
        <template slot="percent_difference_land" slot-scope="percent_difference_land">
          <div>{{percent_difference_land.report.percent_difference_land ? percent_difference_land.report.percent_difference_land + ' %' :  '' }}</div>
        </template>
        <template slot="average_unit_price" slot-scope="average_unit_price">
          <div>{{formatNumber(average_unit_price.report.average_unit_price) + ' đ'}}</div>
        </template>
        <template slot="main_road_length" slot-scope="main_road_length">
          <div>{{main_road_length? main_road_length + ' m': ''}}</div>
        </template>
        <template slot="create_date" slot-scope="create_date">
          <div>{{formatDate(create_date)}}</div>
        </template>
        <template slot="street" slot-scope="street">
          <div class="text-none">{{street? street : ''}}</div>
        </template>
        <template slot="ward" slot-scope="ward">
          <div class="text-none">{{ward? ward : ''}}</div>
        </template>
        <template slot="district" slot-scope="district">
          <div class="text-none">{{district? district : ''}}</div>
        </template>
        <template slot="province" slot-scope="province">
          <div class="text-none">{{province? province : ''}}</div>
        </template>
        <template slot="user_request" slot-scope="user_request">
          <div class="text-none">{{user_request? user_request : ''}}</div>
        </template>

      </a-table>
    </div>
    <ModalLog v-if="modalLog"
              @cancel="modalLog = false"
              :data="data"
    />
    <ModalPrintList
      v-if="openModalPrintList"
      @cancel="cancelPropertyList"
      :create_by="created_by"
    />
  </div>
</template>

<script>
import {convertPagination} from '@/utils/filters'
import ModalLog from '@/components/Modal/ModalLog'
import moment from 'moment'
import Vue from 'vue'
import JsonExcel from 'vue-json-excel'
import EstimateLog from '@/models/estimateLog'
import Search from './Search'
import XLSX from 'xlsx'
import estimateLog from '../../models/estimateLog'
import ModalPrintList from '@/components/Modal/ModalPrintList'

Vue.filter('formatDate', function (value) {
	if (value) {
		return moment(String(value)).format('DD/MM/YYYY')
	}
})
Vue.component('downloadExcel', JsonExcel)
export default {
	name: 'Index.vue',
	data () {
		return {
			openModalPrintList: false,
			reports: [],
			list_migrate: [],
			totalRecord: 0,
			isLoading: false,
			filter: {},
			pagination: {},
			perPage: '',
			modalLog: false,
			data: '',
			history_estimates: [],
			selectedRowKeys: [],
			assetDetails: [],
			landDetails: [],
			buildingDetails: [],
			idPropertyDetails: [],
			preliminaryCode: [],
			dateNow: '',
			created_by: ''
		}
	},
	components: {
		ModalLog,
		Search,
		ModalPrintList
	},
	computed: {
		columns () {
			return [
				{
					title: 'ID',
					align: 'left',
					scopedSlots: {customRender: 'id'},
					dataIndex: 'id'
				},
				{
					title: 'Loại tài sản',
					align: 'left',
					dataIndex: 'report.estimate_type'
				},
				{
					title: 'Vị trí',
					align: 'left',
					dataIndex: 'report.front_side'
				},
				{
					title: 'Bề rộng hẻm',
					align: 'left',
					scopedSlots: {customRender: 'main_road_length'},
					dataIndex: 'report.main_road_length'
				},
				{
					title: 'Tọa độ',
					align: 'left',
					dataIndex: 'report.location'
				},
				{
					title: 'Đường',
					align: 'left',
					scopedSlots: {customRender: 'street'},
					dataIndex: 'report.street'
				},
				{
					title: 'Phường, Xã',
					align: 'left',
					scopedSlots: {customRender: 'ward'},
					dataIndex: 'report.ward'
				},
				{
					title: 'Quận, Huyện',
					align: 'left',
					scopedSlots: {customRender: 'district'},
					dataIndex: 'report.district'
				},
				{
					title: 'Tỉnh, Thành',
					align: 'left',
					scopedSlots: {customRender: 'province'},
					dataIndex: 'report.province'
				},
				{
					title: 'Số tờ',
					align: 'left',
					dataIndex: 'report.doc_no'
				},
				{
					title: 'Số thửa',
					align: 'left',
					dataIndex: 'report.land_no'
				},
				{
					title: 'Ghi chú',
					align: 'left',
					dataIndex: 'report.note'
				},
				{
					title: 'Người yêu cầu',
					align: 'left',
					scopedSlots: {customRender: 'user_request'},
					dataIndex: 'report.user_request'
				},
				{
					title: 'Kết quả hệ thống',
					align: 'left',
					dataIndex: 'report.result'
				},
				{
					title: 'Chỉnh sửa giá trị đất',
					align: 'left',
					scopedSlots: {customRender: 'is_update_land_value'}
				},
				{
					title: 'Chênh lệch',
					align: 'right',
					scopedSlots: {customRender: 'percent_difference_land'}
				},
				{
					title: 'Chỉnh sửa giá trị nhà',
					align: 'left',
					scopedSlots: {customRender: 'is_update_building_value'}
				},
				{
					title: 'Chênh lệch',
					align: 'right',
					scopedSlots: {customRender: 'percent_difference_building'}
				},
				{
					title: 'Xuất bản in',
					align: 'left',
					scopedSlots: {customRender: 'is_print'}
				},
				{
					title: 'Đơn giá trung bình',
					align: 'right',
					scopedSlots: {customRender: 'average_unit_price'}
				},
				{
					title: 'Tổng giá trị tài sản',
					align: 'right',
					scopedSlots: {customRender: 'total_price_update'}
				},
				{
					title: 'Người tạo',
					align: 'left',
					dataIndex: 'report.create_by'
				},
				{
					title: 'Ngày tạo',
					align: 'left',
					scopedSlots: {customRender: 'create_date'},
					dataIndex: 'report.create_date'
				}
			]
		},
		rowSelection () {
			// eslint-disable-next-line no-unused-vars
			const {selectedRowKeys} = this
			return {
				onChange: (selectedRowKeys, selectedRows) => {
					// eslint-disable-next-line vue/no-side-effects-in-computed-properties
					this.selectedRowKeys = [...selectedRows]
					const rows = this.selectedRowKeys
					const column = []
					rows.forEach(row => {
						column.push(
							row.id
						)
						return column
					})
					// eslint-disable-next-line vue/no-side-effects-in-computed-properties
					this.arr = column
				},
				getCheckboxProps: record => ({
					props: {
						disabled: record.name === 'Disabled User', // Column configuration not to be checked
						name: record.name
					}
				})
			}
		}
	},
	methods: {
		cancelPropertyList () {
			this.openModalPrintList = false
			this.getEstimateLog()
		},
		handlePrintList () {
			this.openModalPrintList = true
		},
		onSelectChange (selectedRowKeys) {
			this.selectedRowKeys = selectedRowKeys
		},
		async getEstimateList () {
			const ids = this.selectedRowKeys
			const resp = await estimateLog.getEstimateLogs(ids)
			this.reports = resp.data
			this.selectedRowKeys = []
		},
		getDateNow () {
			const today = new Date()
			const date = `${today.getDate() < 10 ? '0' + today.getDate() : today.getDate()}` + '_' + `${(today.getMonth() + 1) < 10 ? '0' + (today.getMonth() + 1) : (today.getMonth() + 1)}` + '_' + today.getFullYear()
			this.dateNow = date
		},
		calculateColWidth (sheet) {
			let objectMaxLength = []
			for (let i = 0; i < sheet.length; i++) {
				let value = Object.values(sheet[i])
				for (let j = 0; j < value.length; j++) {
					if (typeof value[j] === 'number') {
						objectMaxLength[j] = 11
					} else {
						if (value[j]) {
							objectMaxLength[j] = (objectMaxLength[j] >= value[j].toString().length ? objectMaxLength[j] + 0.5 : value[j].length + 0.5)
						} else {
							objectMaxLength[j] = 11
						}
					}
				}
			}
			const wscols = []
			objectMaxLength.map((item) => {
				wscols.push({ wch: item })
			})
			return wscols
		},
		async exportToExcel () {
			await this.getEstimateList()
			await this.getLog()
			await this.getDateNow()
			const assetDetails = XLSX.utils.json_to_sheet(this.assetDetails)
			assetDetails['!cols'] = this.calculateColWidth(this.assetDetails)
			const landDetails = XLSX.utils.json_to_sheet(this.landDetails)
			landDetails['!cols'] = this.calculateColWidth(this.landDetails)
			const buildingDetails = XLSX.utils.json_to_sheet(this.buildingDetails)
			buildingDetails['!cols'] = this.calculateColWidth(this.buildingDetails)
			const idPropertyDetails = XLSX.utils.json_to_sheet(this.idPropertyDetails)
			idPropertyDetails['!cols'] = this.calculateColWidth(this.idPropertyDetails)
			const preliminaryCode = XLSX.utils.json_to_sheet(this.preliminaryCode)
			preliminaryCode['!cols'] = this.calculateColWidth(this.preliminaryCode)
			const wb = XLSX.utils.book_new() // make Workbook of Excel

			// add Worksheet to Workbook
			// Workbook contains one or more worksheets
			XLSX.utils.book_append_sheet(wb, assetDetails, 'Chi tiết') // sheetAName is name of Worksheet
			XLSX.utils.book_append_sheet(wb, landDetails, 'Tài sản đất')
			XLSX.utils.book_append_sheet(wb, buildingDetails, 'Công trình xây dựng')
			XLSX.utils.book_append_sheet(wb, idPropertyDetails, 'TSSS')
			if (this.preliminaryCode.length > 0) {
				XLSX.utils.book_append_sheet(wb, preliminaryCode, 'Mã sơ bộ')
			}

			// export Excel file
			XLSX.writeFile(wb, `${'Lich_su_uoc_tinh_gia_' + this.dateNow + '.xlsx'}`, { bookType: 'xlsx', type: 'buffer' }) // name of the file is 'book.xlsx'
		},
		getLog () {
			this.assetDetails = []
			this.landDetails = []
			this.buildingDetails = []
			this.idPropertyDetails = []
			this.preliminaryCode = []
			this.reports.forEach(selectedRowKey => {
				this.assetDetails.push(
					{
						'ID': selectedRowKey.id,
						'Loại tài sản': selectedRowKey.report.estimate_type,
						'Vị trí': selectedRowKey.report.front_side,
						'Bề rộng hẻm': `${selectedRowKey.report.main_road_length ? selectedRowKey.report.main_road_length + ' m' : ''}`,
						'Tọa độ': selectedRowKey.report.location,
						'Đường': selectedRowKey.report.street,
						'Phường, Xã': selectedRowKey.report.ward,
						'Quận, Huyện': selectedRowKey.report.district,
						'Tỉnh, Thành': selectedRowKey.report.province,
						'Số tờ': selectedRowKey.report.doc_no,
						'Số thửa': selectedRowKey.report.land_no,
						'Người yêu cầu': selectedRowKey.report.user_request,
						'Kết quả hệ thống': selectedRowKey.report.result,
						'Chỉnh sửa giá trị đất': `${selectedRowKey.report.is_update_land_value ? 'Có' : 'Không'}`,
						'Chênh lệch đất': `${selectedRowKey.report.percent_difference_land ? selectedRowKey.report.percent_difference_land + ' %' : ''}`,
						'Chỉnh sửa giá trị nhà': `${selectedRowKey.report.is_update_building_value ? 'Có' : 'Không'}`,
						'Chênh lệch nhà': `${selectedRowKey.report.percent_difference_building ? selectedRowKey.report.percent_difference_building + ' %' : ''}`,
						'Xuất bản in': `${selectedRowKey.report.is_print ? 'Có' : 'Không'}`,
						'Đơn giá trung bình': `${selectedRowKey.report.average_unit_price ? this.formatNumber(selectedRowKey.report.average_unit_price) + ' đ' : ''}`,
						'Tổng giá trị tài sản': `${selectedRowKey.report.total_price_update ? this.formatNumber(selectedRowKey.report.total_price_update) + ' đ' : ''}`,
						'Người tạo': selectedRowKey.report.create_by,
						'Ngày tạo': `${this.formatDate(selectedRowKey.report.create_date)}`
					}
				)
				selectedRowKey.report_detail.land.forEach(land => {
					this.landDetails.push(
						{
							'ID': this.landDetails.length + 1,
							'Parent ID': selectedRowKey.id,
							'Loại đất': land.land_type_purpose_name,
							'Loại quy hoạch': land.type,
							'Diện tích': `${land.area ? land.area + `m2` : '-'}`,
							'Đơn giá gốc': `${land.average_unit_price ? this.formatNumber(land.average_unit_price) + ' đ' : '-'}`,
							'Giá trị chỉnh sửa': `${land.average_unit_price_update ? this.formatNumber(land.average_unit_price_update) + ' đ' : '-'}`,
							'Chênh lệch': `${land.average_unit_difference ? this.formatNumber(land.average_unit_difference) + ' đ' : '-'}`,
							'Tỷ lệ': `${land.percent_difference ? land.percent_difference + ' %' : '-'}`,
							'Có chỉnh sửa': `${land.is_update ? 'Có' : 'Không'}`,
							'Trước điều chỉnh': `${land.estimate_price ? this.formatNumber(land.estimate_price) + ' đ' : '-'}`,
							'Sau điều chỉnh': `${land.estimate_price_update ? this.formatNumber(land.estimate_price_update) + ' đ' : '-'}`,
							'Tổng gốc': `${land.total_price_update ? this.formatNumber(land.total_price_update) + ' đ' : '-'}`,
							'Tổng điều chỉnh': `${land.total_price_update ? this.formatNumber(land.total_price_update) + ' đ' : '-'}`,
							'Chênh lệch tổng': `${land.total_percent_difference ? land.total_percent_difference + ' %' : '-'}`
						}
					)
				})
				selectedRowKey.report_detail.building.forEach(building => {
					this.buildingDetails.push(
						{
							'ID': this.buildingDetails.length + 1,
							'Parent ID': selectedRowKey.id,
							'Loại nhà': building.building_category,
							'Đơn giá xây dựng': `${building.average_unit_price ? this.formatNumber(building.average_unit_price) + ' đ' : '-'}`,
							'Diện tích': `${building.area ? building.area + `m2` : '-'}`,
							'Chất lượng còn lại': `${building.remaining_quality ? building.remaining_quality + ' %' : '-'}`,
							'Đơn giá sau điều chỉnh': `${building.average_unit_price_update ? this.formatNumber(building.average_unit_price_update) + ' đ' : '-'}`,
							'Có chỉnh sửa': `${building.is_update ? 'Có' : 'Không'}`,
							'Chênh lệch': `${building.average_unit_difference ? this.formatNumber(building.average_unit_difference) + ' đ' : '-'}`,
							'Tỷ lệ': `${building.percent_difference ? building.percent_difference + ' %' : '-'}`,
							'Trước điều chỉnh': `${building.estimate_price ? this.formatNumber(building.estimate_price) + ' đ' : '-'}`,
							'Sau điều chỉnh': `${building.estimate_price_update ? this.formatNumber(building.estimate_price_update) + ' đ' : '-'}`,
							'Tổng gốc': `${building.total_price_update ? this.formatNumber(building.total_price_update) + ' đ' : '-'}`,
							'Tổng điều chỉnh': `${building.total_price_update ? this.formatNumber(building.total_price_update) + ' đ' : '-'}`,
							'Chênh lệch tổng': `${building.total_percent_difference ? building.total_percent_difference + ' %' : '-'}`
						}
					)
				})
				selectedRowKey.report_detail.assets.forEach(asset => {
					this.idPropertyDetails.push({
						'ID': this.idPropertyDetails.length + 1,
						'Parent ID': selectedRowKey.id,
						'Mã TSSS': `${asset.id > 6600 ? 'TSS_' + asset.id : 'TSC_' + asset.id}`,
						'Độ tin cậy': asset.reliability
					})
				})
				if (selectedRowKey.ids) {
					selectedRowKey.ids.forEach(id => {
						this.preliminaryCode.push({
							'ID': this.preliminaryCode.length + 1,
							'Parent ID': selectedRowKey.id,
							'Mã Sơ Bộ': id
						})
					})
				}
			})
		},
		formatDate (value) {
			return moment(String(value)).format('DD/MM/YYYY')
		},
		formatNumber (value) {
			let num = (value / 1).toFixed(0).replace('.', ',')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
		},
		openModalLogInput (input) {
			this.data = input
			this.modalLog = true
		},
		openModalLogOutput (output) {
			this.data = output
			this.modalLog = true
		},
		async getProfiles () {
			const profile = this.$store.getters.profile
			this.created_by = profile.data.user.name
			this.role = profile.data.user.roles[0].name
		},
		async getEstimateLog (query = {}) {
			this.isLoading = true
			const filter = {}

			for (let property in this.filter) {
				filter[`${property}`] = this.filter[property]
			}

			try {
				if (this.role === 'ADMIN' || this.role === 'ROOT_ADMIN') {
					const resp = await EstimateLog.paginate({
						query: {
							page: 1,
							limit: 20,
							...query,
							...filter
						}
					})
					this.history_estimates = [...resp.data.data]
					this.totalRecord = resp.data.total
					this.pagination = convertPagination(resp.data)
					this.isLoading = false
				} else {
					const resp = await EstimateLog.paginate({
						query: {
							page: 1,
							limit: 20,
							create_by: this.created_by,
							...query,
							...filter
						}
					})
					this.history_estimates = [...resp.data.data]
					this.totalRecord = resp.data.total
					this.pagination = convertPagination(resp.data)
					this.isLoading = false
				}
			} catch (e) {
				this.isLoading = false
			}
		},
		async onPageChange (pagination) {
			this.perPage = pagination.pageSize
			const params = {
				page: pagination.current,
				per_page: pagination.pageSize
			}
			await this.getEstimateLog(params)
		},
		async onFilterChange ($event) {
			// const query = {
			//   query: {
			//     page: 1,
			//     limit: this.limit || 20
			//   }
			// }

			this.filter = {...$event}

			await this.getEstimateLog()
		},
		async handleDetail (id) {
			this.$router.push({
				name: 'log.detail',
				query: {
					id: id
				}
			}).catch(_ => {
			})
		}
	},
	async beforeMount () {
		await this.getProfiles()
		this.getEstimateLog()
	}
}
</script>

<style lang="scss" scoped>
  .pannel{
    background: #FFFFFF;
    border-radius: 5px;
    margin-bottom: 47px;
    &__table{
      padding: 25px 0;
      border-radius: 5px;
    }
    &__input{
      p{
        color: #5a5386;
        font-weight: 600;
      }
    }
  }
.table{
  thead{
    th{
      padding: .5rem  40px;
      background: transparent;
      color: #000000;
      font-weight: 700;

      border-bottom: 2px solid rgba(110,117,130,0.2);
      @media (max-width: 1023px) {
        padding: .5rem ;
      }
    }
  }
  tbody{
    tr{
      &:nth-child(odd){
        background: #FFFFFF;
      }
    }
    td{
      padding: .5rem  40px;
      color: #000000;
      vertical-align: middle !important;

      font-weight: 700;
      @media (max-width: 1023px) {
        padding: .5rem ;
      }
      &:first-child{
        width: 100%;
      }
    }
  }
  &__action{
    padding-right: 20px;
  }
}
.input {
  color: #FAA831;
  cursor: pointer;
  margin-bottom: 0;
}
.status {
  color: red;
  margin-bottom: 0;
  &.green {
    color: green;
  }
}
.container {
  &__table{
    padding: 0 30px;
  }
}
.btn-container{
  padding: 0 30px 0 10px;
}
.text-none{
  text-transform: none;
}
</style>
