<template>
  <div>
    <div class="container__table" :class="totalRecord === 0 ? 'empty-data' : ''">
      <a-table bordered
               :columns="columns"
               :data-source="history_estimates"
               :loading="isLoading"
               table-layout="top"
               :pagination="{
                 ...pagination,
               }"
               class="input__data"
               @change="onPageChange"
      >
        <!--Custom type table-->
        <template slot="data" slot-scope="data">
          <div class="text-none">{{data}}</div>
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
		ModalPrintList
	},
	computed: {
		columns () {
			return [
				{
					title: 'ID',
					align: 'left',
					dataIndex: 'id',
					width: '80px'
				},
				{
					title: 'data',
					align: 'left',
					scopedSlots: {customRender: 'data'}
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
				const resp = await EstimateLog.paginate({
					query: {
						page: 1,
						limit: 20,
						all: true,
						...query,
						...filter
					}
				})
				this.history_estimates = [...resp.data.data]
				this.totalRecord = resp.data.total
				this.pagination = convertPagination(resp.data)
				this.isLoading = false
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
  white-space: normal;
}
</style>
