<template>
  <div class="modal-detail d-flex justify-content-center align-items-center"
       @click.self="handleCancel">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="mb-0 title">
          Danh sách mã sơ bộ
        </h3>
        <img @click="handleCancel"
             src="../../assets/icons/ic_cancel-1.svg"
             alt="icon">
      </div>
      <div class="card-body">
        <div class="container__table" :class="totalRecord === 0 ? 'empty-data' : ''">
          <a-table bordered
                   :columns="columns"
                   :data-source="printList"
                   :loading="isLoading"
                   :row-selection="{ selectedRowKeys: selectedRowKeys, onChange: onSelectChange }"
                   :rowKey="record => record.id"
                   table-layout="top"
                   :pagination="{
               ...pagination,
             }"
                   @change="onPageChange"
                   class="input__data"
          >
            <!--Custom type table-->
            <template slot="create_date" slot-scope="create_date">
              <div>{{formatDate(create_date)}}</div>
            </template>
            <template slot="total_price_update" slot-scope="total_price_update">
              <div>{{total_price_update.report.total_price_update ?  formatNumber(total_price_update.report.total_price_update) + ' đ' : formatNumber(total_price_update.report.total_price) + ' đ'}}</div>
            </template>
            <template slot="street" slot-scope="street, id">
              <p :id="'street' + id.id" class="address text-left">{{ street }}</p>
              <b-tooltip :target="('street' + id.id).toString()">{{ street }}</b-tooltip>
            </template>
            <template slot="ward" slot-scope="ward">
              <p class="address-detail text-left">{{ ward }}</p>
            </template>
            <template slot="district" slot-scope="district">
              <p class="address-detail text-left">{{ district }}</p>
            </template>
            <template slot="province" slot-scope="province">
              <p class="address-detail text-left">{{ province }}</p>
            </template>
          </a-table>
        </div>
      </div>
      <div class="card-footer footer-print">
        <button class="btn btn-orange" @click="handleOpenPrintEstimates">Tạo Bản In</button>
      </div>
    </div>
    <ModalPrintEstimates
      v-if="openModalPrintEstimates"
      @cancel="openModalPrintEstimates = false"
      :dataReport="this.reports"
      :idLogs="this.idLogs"
      :created="create_by"
    />
  </div>
</template>

<script>
import EstimateLog from '@/models/estimateLog'
import {convertPagination} from '@/utils/filters'
import ModalPrintEstimates from './ModalPrintEstimates'
import InputText from '@/components/Form/InputText'
import InputCategory from '@/components/Form/InputCategory'
import InputSwitch from '@/components/Form/InputSwitch'
import FileUpload from '@/components/file/FileUpload'
import moment from 'moment'
import VuePdfApp from 'vue-pdf-app'
import 'vue-pdf-app/dist/icons/main.css'
import {replace} from 'lodash-es'
import PriceEstimate from '../../models/PriceEstimate'
import estimateLog from '../../models/estimateLog'
import {BTooltip} from 'bootstrap-vue'

export default {
	name: 'ModalPrintList',
	props: ['create_by', 'warningUpdate'],
	data () {
		return {
			reports: [],
			dateNow: '',
			idLogs: '',
			openModalPrintEstimates: false,
			selectedRowKeys: [],
			totalRecord: 0,
			printList: [],
			pagination: {},
			isLoading: false,
			formReport: {
				id: '',
				ids: [],
				report: {
					estimate_type: '',
					province: '',
					district: '',
					ward: '',
					street: '',
					location: '',
					front_side: '',
					main_road_length: null,
					land_no: '',
					doc_no: '',
					create_by: '',
					create_date: '',
					user_request: '',
					result: '',
					is_print: true,
					total_price: null,
					total_price_update: null,
					total_building_price_update: null,
					total_land_price_update: null
				},
				report_detail: {
					land: [],
					building: [],
					assets: []
				}
			},
			dataTotal: [],
			totalPrice: 0
		}
	},
	computed: {
		columns () {
			return [
				{
					title: 'ID',
					align: 'left',
					dataIndex: 'id'
				},
				{
					title: 'Loại tài sản',
					align: 'left',
					dataIndex: 'report.estimate_type'
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
					title: 'Tổng giá trị',
					align: 'left',
					scopedSlots: {customRender: 'total_price_update'}
				},
				{
					title: 'Kết quả ước tính',
					align: 'left',
					dataIndex: 'report.result'
				},
				{
					title: 'Ngày tạo',
					align: 'left',
					scopedSlots: {customRender: 'create_date'},
					dataIndex: 'report.create_date'
				}
			]
		}
	},
	components: {
		ModalPrintEstimates,
		VuePdfApp,
		FileUpload,
		InputCategory,
		InputText,
		InputSwitch,
		'b-tooltip': BTooltip
	},
	mounted () {
	},
	methods: {
		onSelectChange (selectedRowKeys) {
			this.selectedRowKeys = selectedRowKeys
		},
		async getEstimateList () {
			const ids = this.selectedRowKeys
			const resp = await estimateLog.getEstimateLogs(ids)
			this.reports = resp.data
			this.selectedRowKeys = []
		},
		formatDate (value) {
			return moment(String(value)).format('DD/MM/YYYY')
		},
		formatNumber (value) {
			let num = (value / 1).toFixed(0).replace(',', '.')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
		},
		format (value) {
			let num = (value / 1).toFixed(0).replace('.', ',')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.')
		},
		getTotal () {
			this.reports.forEach(report => {
				let total = 0
				let total_area = 0
				report.report_detail.land.forEach(land => {
					if (land.estimate_price_update) {
						total = total + land.estimate_price_update
					} else {
						total = total + land.estimate_price
					}
					total_area = total_area + parseFloat(land.area)
				})
				report.report_detail.building.forEach(building => {
					if (building.estimate_price_update) {
						total = total + building.estimate_price_update
					} else {
						total = total + building.estimate_price
					}
				})
				this.dataTotal.push(
					{
						total_price: total
					}
				)
			})
			this.getTotalPrice()
		},
		getTotalPrice () {
			this.dataTotal.forEach(total => {
				this.totalPrice = this.totalPrice + total.total_price
			})
		},
		async getEstimateLogs () {
			await this.getDateNow()
			await this.getTotal()
			if (this.reports.length > 0) {
				const input = this.reports
				const resp = await PriceEstimate.LogPriceEstimates({input})
				this.idLogs = resp.data
				this.formReport.ids = []
				this.reports.forEach(selectedRowKey => {
					this.formReport.ids.push(
						selectedRowKey.id
					)
					if (selectedRowKey.report_detail.land.length > 0) {
						selectedRowKey.report_detail.land.forEach(land => {
							this.formReport.report_detail.land.push(
								{
									area: land.area,
									average_unit_difference: land.average_unit_difference,
									average_unit_price: land.average_unit_price,
									average_unit_price_update: land.average_unit_price_update,
									estimate_price: land.estimate_price,
									estimate_price_difference: land.estimate_price_difference,
									estimate_price_update: land.estimate_price_update,
									is_update: land.is_update,
									land_type_purpose_name: land.land_type_purpose_name,
									percent_difference: land.percent_difference,
									total_percent_difference: land.total_percent_difference,
									total_price: land.total_price,
									total_price_update: land.total_price_update,
									type: land.type
								})
						})
					} else {
						this.formReport.report_detail.land = []
					}
					if (selectedRowKey.report_detail.building.length > 0) {
						selectedRowKey.report_detail.building.forEach(building => {
							this.formReport.report_detail.building.push(
								{
									area: building.area,
									average_unit_difference: building.average_unit_difference,
									average_unit_price: building.average_unit_price,
									average_unit_price_update: building.average_unit_price_update,
									building_category: building.building_category,
									estimate_price: building.estimate_price,
									estimate_price_difference: building.estimate_price_difference,
									estimate_price_update: building.estimate_price_update,
									is_update: building.is_update,
									percent_difference: building.percent_difference,
									remaining_quality: building.remaining_quality,
									total_percent_difference: building.total_percent_difference,
									total_price: building.total_price,
									total_price_update: building.total_price_update
								})
						})
					} else {
						this.formReport.report_detail.building = []
					}
					if (selectedRowKey.report_detail.assets.length > 0) {
						selectedRowKey.report_detail.assets.forEach(asset => {
							this.formReport.report_detail.assets.push(
								{
									id: asset.id,
									reliability: asset.reliability
								})
						})
					} else {
						this.formReport.report_detail.assets = []
					}
				})
				this.formReport.report.create_by = this.create_by
				this.formReport.report.create_date = this.dateNow
				this.formReport.id = resp.data
				this.formReport.report.total_price = this.totalPrice
				this.formReport.report.total_price_update = this.totalPrice
				await this.createEstimateLog()
			}
		},
		getDateNow () {
			const today = new Date()
			const date = `${today.getDate() < 10 ? '0' + today.getDate() : today.getDate()}` + '_' + `${(today.getMonth() + 1) < 10 ? '0' + (today.getMonth() + 1) : (today.getMonth() + 1)}` + '_' + today.getFullYear()
			this.dateNow = date
		},
		async createEstimateLog () {
			const input = this.formReport
			await PriceEstimate.LogPriceEstimate({input})
		},
		async handleOpenPrintEstimates () {
			if (this.selectedRowKeys.length > 1) {
				await this.getEstimateList()
				await this.getEstimateLogs()
				this.openModalPrintEstimates = true
			} else {
				this.$toast.open({
					message: 'Vui lòng chọn ít nhất 2 mã sơ bộ trở lên để thực hiện gom bản in',
					type: 'error',
					position: 'top-right'
				})
			}
		},
		async onPageChange (pagination, filters, sorter) {
			this.perPage = pagination.pageSize
			const sortBy = `sortBy[${sorter.field}]`
			const sortDesc = replace(sorter.order, 'end', '')
			const params = {
				page: pagination.current,
				per_page: pagination.pageSize,
				create_by: this.create_by,
				is_print: true,
				[sortBy]: sortDesc
			}
			await this.getEstimateLog(params)
		},
		async getEstimateLog (query = {}) {
			this.isLoading = true
			const filter = {}

			for (let property in this.filter) {
				filter[`${property}`] = this.filter[property]
			}

			try {
				const resp = await EstimateLog.paginate({
					query: {
						page: 1,
						limit: 20,
						create_by: this.create_by,
						is_print: true,
						...query,
						...filter
					}
				})
				this.printList = [...resp.data.data]
				this.totalRecord = resp.data.total
				this.pagination = convertPagination(resp.data)
				this.isLoading = false
			} catch (e) {
				this.isLoading = false
			}
		},
		handleCancel (event) {
			this.$emit('cancel', event)
		}
	},
	beforeMount () {
		this.getEstimateLog()
	}
}
</script>

<style lang="scss" scoped>
.title{
  font-size: 16px;
  font-weight: 700;
  margin-bottom: 25px;
  color: #000000;
}
.modal-detail {
  position: fixed;
  z-index: 1030;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, .6);

  @media (max-width: 787px) {
    padding: 20px;
  }

  .card {
    max-width: 1500px;
    width: 100%;
    margin-bottom: 0;

    &-header {
      border-bottom: none;

      h3 {
        color: #333333;
      }

      img {
        cursor: pointer;
      }
    }

    &-body {
      text-align: center;
      padding: 8px 20px 20px;
      max-height: 80vh;
      overflow-y: auto;

      p {
        color: #333333;
        margin-bottom: 40px;
      }
    }
    &-footer {
      padding: 8px 20px 20px;
    }
  }
  .address {
    max-width: 250px;
    white-space: nowrap;
    -webkit-line-clamp: 2 !important;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-bottom: 0 !important;
    text-transform: none;

    &:first-letter {
      text-transform: none;
    }
    &-detail {
      text-transform: none;
      margin-bottom: 0 !important;
    }
  }
}

.img-content{
  color: #000000;
  font-size: 14px;
  font-weight: 600;
  span{
    font-weight: 400;
    margin-left: 10px;
  }
}
.input-code{
  color: #000000;
  border-radius: 5px;
  width: 180px;
  border: 1px solid #000000;
  background: #f5f5f5;
  height: 35px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}
.img-dropdown{
  cursor: pointer;
  width: 18px;
  &__hide{
    transform: rotate(90deg);
    transition: .3s;
  }
}
.img-contain {
  max-width: 200px;
  max-height: 200px;
  height: auto;
  margin-left: 20px;
  &__table{
    margin: auto;
    max-width: 50px;
    img{
      cursor: pointer;
      display: flex;
      justify-content: center;
    }
  }
  img{
    object-fit: contain;
    width: 100%;
    height: auto;
    max-width: 200px;
    max-height: 200px;
  }
}
.btn{
  &-white{
    max-height: none;
    font-size: 14px;
    line-height: 19.07px;
    min-width: 153px;
    margin-right: 15px;
    &:last-child{
      margin-right: 0;
    }
  }
  &-contain{
    margin-bottom: 55px;
  }
}
.footer-print {
  display: flex;
  justify-content: flex-end;
  padding: 0.75rem 50px;
}
</style>
