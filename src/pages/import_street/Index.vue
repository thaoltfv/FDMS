<template>
<div>
  <ValidationObserver tag="form"
                      ref="observer"
                      @submit.prevent="validateBeforeSubmit">
    <div class="container__configuration d-block d-lg-flex justify-content-end align-items-end">
      <div class="container__input">
        <InputNumberFormat
          v-model="form.size"
          vid="limit"
          label="Số lượng dữ liệu"
          :formatter="valueFormat => `${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
          disabled-input
          class="input__data"
        />
      </div>
      <div class="container__input">
        <InputText
          v-model="form.index"
          vid="page"
          label="Vị trí bắt đầu"
          rules="required"
          type="number"
          :max-length="7"
          class="input__data"
        />
      </div>
      <button class="btn btn-orange">Thêm đường</button>
    </div>
  </ValidationObserver>
    <div class="container__configuration d-flex justify-content-end align-items-end">
      <button class="btn btn-orange btn-orange--reload" type="button" @click="handleReload">Reload</button>
    </div>
    <div class="container__table" :class="totalRecord === 0 ? 'empty-data' : ''">
      <h2>Thông tin đường</h2>
      <a-table bordered
               :columns="columns"
               :data-source="list_import"
               :loading="isLoading"
               :rowKey="record => record.id"
               table-layout="top"
               :pagination="{
                 ...pagination,
               }"
               @change="onPageChange"
               class="table__import"
      >
        <!--Custom type table-->
        <template slot="created_at" slot-scope="created_at">
          <p class="created_at mb-0">{{created_at | formatDate}}</p>
        </template>
        <template slot="status" slot-scope="status">
          <p class="status" :class="status === 0 ? 'orange' : ''">
            {{ status === 0 ? 'Đang chạy đường' : 'Hoàn thành '}}</p>
        </template>
      </a-table>
    </div>
    <div class="container__table" :class="totalRecordDistance === 0 ? 'empty-data' : ''">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="title__info">Thông tin đoạn</h2>
        <div class="d-block d-lg-flex justify-content-end mb-0">
          <button class="btn btn-orange" @click="importDistance">Thêm đoạn</button>
        </div>
      </div>
      <a-table bordered
               :columns="columns_distance"
               :data-source="list_import_distance"
               :loading="isLoading"
               :rowKey="record => record.id"
               table-layout="top"
               :pagination="{
                   ...pagination,
                 }"
               @change="onPageChangeDistance"
               class="table__import"
      >
        <!--Custom type table-->
        <template slot="created_at" slot-scope="created_at">
          <p class="created_at mb-0">{{created_at | formatDate}}</p>
        </template>
        <template slot="status" slot-scope="status">
          <p class="status" :class="status === 0 ? 'orange' : ''">
            {{ status === 0 ? 'Đang chạy' : 'Hoàn thành'}}</p>
        </template>
      </a-table>
    </div>
    <div class="container__table" :class="totalRecordUnitPrice === 0 ? 'empty-data' : ''">
      <div class="d-flex justify-content-between align-items-center mb-3">
      <h2 class="title__info">Thông tin đơn giá</h2>
        <div class="d-block d-lg-flex justify-content-end mb-0">
          <button class="btn btn-orange" @click="importUnitPrice">Thêm đơn giá</button>
        </div>
      </div>
      <a-table bordered
               :columns="columns_unit_price"
               :data-source="list_import_unit_price"
               :loading="isLoading"
               :rowKey="record => record.id"
               table-layout="top"
               :pagination="{
                   ...pagination,
                 }"
               @change="onPageChangeUnitPrice"
               class="table__import"
      >
        <!--Custom type table-->
        <template slot="created_at" slot-scope="created_at">
          <p class="created_at mb-0">{{created_at | formatDate}}</p>
        </template>
        <template slot="status" slot-scope="status">
          <p class="status" :class="status === 0 ? 'orange' : ''">
            {{ status === 0 ? 'Đang chạy' : 'Hoàn thành'}}</p>
        </template>
      </a-table>
    </div>
</div>
</template>

<script>
import InputText from '@/components/Form/InputText'
import InputNumberFormat from '@/components/Form/InputNumber'
import ImportStreet from '@/models/ImportStreet'
import Migrate from '@/models/Migrate'
import {convertPagination} from '@/utils/filters'
import moment from 'moment'
import Vue from 'vue'
import {replace} from 'lodash-es'

Vue.filter('formatDate', function (value) {
	if (value) {
		return moment(String(value)).format('DD/MM/YYYY')
	}
})
export default {
	name: 'Index.vue',
	data () {
		return {
			config: 500,
			list_import: [],
			list_import_distance: [],
			list_import_unit_price: [],
			totalRecord: 0,
			totalRecordDistance: 0,
			totalRecordUnitPrice: 0,
			isLoading: false,
			filter: {},
			pagination: {},
			perPage: '',
			form: {
				size: 10000,
				index: ''
			}
		}
	},
	components: {
		InputNumberFormat,
		InputText
	},
	computed: {
		columns () {
			return [
				{
					title: 'ID',
					align: 'center',
					dataIndex: 'id',
					width: '4%'
				},
				{
					title: 'Số record',
					align: 'left',
					dataIndex: 'total_records'
				},
				{
					title: 'Vị trí bắt đầu',
					align: 'left',
					dataIndex: 'page'
				},
				{
					title: 'Thời điểm khởi chạy',
					align: 'left',
					dataIndex: 'created_at'
				},
				{
					title: 'Thời điểm kết thúc',
					align: 'left',
					dataIndex: 'updated_at'
				},
				{
					title: 'Trạng thái',
					scopedSlots: {customRender: 'status'},
					dataIndex: 'status',
					align: 'left',
					width: '100px'
				}
			]
		},
		columns_distance () {
			return [
				{
					title: 'ID',
					align: 'center',
					dataIndex: 'id',
					width: '4%'
				},
				{
					title: 'Số record',
					align: 'left',
					dataIndex: 'total_records'
				},
				{
					title: 'Thời điểm khởi chạy',
					align: 'left',
					dataIndex: 'created_at'
				},
				{
					title: 'Thời điểm kết thúc',
					align: 'left',
					dataIndex: 'updated_at'
				},
				{
					title: 'Trạng thái',
					scopedSlots: {customRender: 'status'},
					dataIndex: 'status',
					align: 'left',
					width: '100px'
				}
			]
		},
		columns_unit_price () {
			return [
				{
					title: 'ID',
					align: 'center',
					dataIndex: 'id',
					width: '4%'
				},
				{
					title: 'Số record',
					align: 'left',
					dataIndex: 'total_records'
				},
				{
					title: 'Thời điểm khởi chạy',
					align: 'left',
					dataIndex: 'created_at'
				},
				{
					title: 'Thời điểm kết thúc',
					align: 'left',
					dataIndex: 'updated_at'
				},
				{
					title: 'Trạng thái',
					scopedSlots: {customRender: 'status'},
					dataIndex: 'status',
					align: 'left',
					width: '100px'
				}
			]
		}
	},
	methods: {
		async importStreet () {
			const index = this.form.index
			const size = this.form.size
			const resp = await ImportStreet.getStreet(index, size)
			this.$toast.open({
				message: resp.data.message,
				type: 'success',
				position: 'top-right'
			})
			await this.getImportStreet()
		},
		async importDistance () {
			const resp = await ImportStreet.getDistance()
			this.$toast.open({
				message: resp.data.message,
				type: 'success',
				position: 'top-right'
			})
			await this.getImportDistance()
		},
		async importUnitPrice () {
			const resp = await ImportStreet.getUnitPrice()
			this.$toast.open({
				message: resp.data.message,
				type: 'success',
				position: 'top-right'
			})
			await this.getImportUnitPrice()
		},
		handleReload () {
			this.getImportStreet()
			this.getImportDistance()
			this.getImportUnitPrice()
		},
		async getImportStreet (query = {}) {
			this.isLoading = true
			const filter = {}

			for (let property in this.filter) {
				filter[`${property}`] = this.filter[property]
			}

			try {
				const resp = await Migrate.paginate({
					query: {
						page: 1,
						limit: 20,
						type: 1,
						...query,
						...filter
					}
				})

				this.list_import = [...resp.data.data]
				this.totalRecord = resp.data.total
				this.pagination = convertPagination(resp.data)
				this.isLoading = false
			} catch (e) {
				this.isLoading = false
			}
		},
		async onPageChange (pagination, filters, sorter) {
			this.perPage = pagination.pageSize
			const sortBy = `sortBy[${sorter.field}]`
			const sortDesc = replace(sorter.order, 'end', '')

			const params = {
				page: pagination.current,
				per_page: pagination.pageSize,
				[sortBy]: sortDesc
			}
			await this.getImportStreet(params)
		},
		async getImportDistance (query = {}) {
			this.isLoading = true
			const filter = {}

			for (let property in this.filter) {
				filter[`${property}`] = this.filter[property]
			}

			try {
				const resp = await Migrate.paginate({
					query: {
						page: 1,
						limit: 20,
						type: 2,
						...query,
						...filter
					}
				})

				this.list_import_distance = [...resp.data.data]
				this.totalRecordDistance = resp.data.total
				this.pagination = convertPagination(resp.data)
				this.isLoading = false
			} catch (e) {
				this.isLoading = false
			}
		},
		async onPageChangeDistance (pagination, filters, sorter) {
			this.perPage = pagination.pageSize
			const sortBy = `sortBy[${sorter.field}]`
			const sortDesc = replace(sorter.order, 'end', '')

			const params = {
				page: pagination.current,
				per_page: pagination.pageSize,
				[sortBy]: sortDesc
			}
			await this.getImportDistance(params)
		},
		async getImportUnitPrice (query = {}) {
			this.isLoading = true
			const filter = {}

			for (let property in this.filter) {
				filter[`${property}`] = this.filter[property]
			}

			try {
				const resp = await Migrate.paginate({
					query: {
						page: 1,
						limit: 20,
						type: 3,
						...query,
						...filter
					}
				})

				this.list_import_unit_price = [...resp.data.data]
				this.totalRecordUnitPrice = resp.data.total
				this.pagination = convertPagination(resp.data)
				this.isLoading = false
			} catch (e) {
				this.isLoading = false
			}
		},
		async onPageChangeUnitPrice (pagination, filters, sorter) {
			this.perPage = pagination.pageSize
			const sortBy = `sortBy[${sorter.field}]`
			const sortDesc = replace(sorter.order, 'end', '')

			const params = {
				page: pagination.current,
				per_page: pagination.pageSize,
				[sortBy]: sortDesc
			}
			await this.getImportUnitPrice(params)
		},
		async validateBeforeSubmit () {
			const isValid = await this.$refs.observer.validate()
			if (isValid) {
				await this.importStreet()
			} else {
				this.$toast.open({
					message: 'Vui lòng nhập trang',
					type: 'error',
					position: 'top-right'
				})
			}
		}
	},
	beforeMount () {
		this.getImportStreet()
		this.getImportDistance()
		this.getImportUnitPrice()
	}
}
</script>

<style lang="scss" scoped>
.container {
  &__configuration {
    padding: 0 30px;
    width: 100%;
    margin-bottom: 30px;
  }
  &__table{
    padding: 0 30px;
  }
  &__input{
    margin-right: 10px;
    width: 50%;
    @media (max-width: 767px) {
      width: 100%;
    }
  }
}
.btn{
  &-orange{
    white-space: nowrap;
    height: 2.295rem;
    @media (max-width: 767px) {
      margin-top: 20px;
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
.status {
  color: green;
  margin-bottom: 0;
  &.orange {
    color: #FAA831;
  }
}
.container__table{
  margin-bottom: 20px;
}
.title__info {
  white-space: nowrap;
}
</style>
