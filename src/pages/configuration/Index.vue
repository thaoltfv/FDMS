<template>
<div>
  <ValidationObserver tag="form"
                      ref="observer"
                      @submit.prevent="validateBeforeSubmit">
    <div class="container__configuration d-block d-lg-flex justify-content-end align-items-end">
      <div class="container__input">
        <InputText
          v-model="form.limit"
          vid="limit"
          label="Số lượng dữ liệu"
          disabled-input
          class="input__data"
        />
      </div>
      <div class="container__input">
        <InputText
          v-model="form.page"
          vid="page"
          label="Trang"
          rules="required"
          type="number"
          :max-length="7"
          class="input__data"
        />
      </div>
      <button class="btn btn-orange">Đồng bộ dữ liệu</button>
    </div>
  </ValidationObserver>
    <div class="container__configuration d-flex justify-content-end align-items-end">
      <button class="btn btn-orange btn-orange--reload" type="button" @click="handleReload">Reload</button>
    </div>
    <div class="container__table" :class="totalRecord === 0 ? 'empty-data' : ''">
      <a-table bordered
               :columns="columns"
               :data-source="list_migrate"
               :loading="isLoading"
               :rowKey="record => record.id"
               table-layout="top"
               :pagination="{
                 ...pagination,
               }"
               @change="onPageChange"
      >
        <!--Custom type table-->
        <template slot="created_at" slot-scope="created_at">
          <p class="created_at mb-0">{{created_at | formatDate}}</p>
        </template>
        <template slot="status" slot-scope="status">
          <p class="status" :class="status === 0 || status === 2 || status === 4 ? 'orange' : ''">
            {{ status === 0 ? 'Đang đồng bộ dữ liệu' : status === 1 ? 'Hoàn thành đồng bộ dữ liệu' : status === 2 ? 'Đang đồng bộ hình ảnh' : status === 3 ? 'Hoàn thành đồng bộ hình ảnh' : status === 4 ? 'Đang đồng bộ elastic search' : 'Hoàn thành đồng bộ elastic search'}}</p>
        </template>

        <template slot="action"
                  slot-scope="action_s3">
          <div class="d-flex justify-content-center">
            <button class="btn btn-orange" @click.prevent="migrateS3(action_s3.id, action_s3.limit, action_s3.page)"><img src="../../assets/icons/ic_synchronization.svg" alt="img"/></button>
          </div>
        </template>
        <template slot="elastic"
                  slot-scope="action_elastic">
          <div class="d-flex justify-content-center">
            <button class="btn btn-orange" @click.prevent="migrateElastic(action_elastic.id, action_elastic.limit, action_elastic.page)"><img src="../../assets/icons/ic_synchronization_1.svg" alt="img"/></button>
          </div>
        </template>
      </a-table>
    </div>
  <div class="container__table">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h2 class="title__info">Cập nhật trạng thái</h2>
      <div class="d-block d-lg-flex justify-content-end mb-0">
        <button class="btn btn-orange" @click="updateStatus">Cập nhật</button>
      </div>
    </div>
    <a-table bordered
             :columns="columns_list"
             :data-source="list"
             :loading="isLoading"
             :rowKey="record => record.id"
             table-layout="top"
             :pagination="{
                 ...pagination,
               }"
             @change="onPageChangeList"
             class="table__import"
    >
      <!--Custom type table-->
      <template slot="created_at" slot-scope="created_at">
        <p class="created_at mb-0">{{created_at | formatDate}}</p>
      </template>
      <template slot="status" slot-scope="status">
        <p class="status" :class="status === 0 ? 'orange' : ''">
          {{status === 0 ? 'Đang đồng bộ trạng thái dữ liệu' : 'Hoàn thành'}}</p>
      </template>
    </a-table>
  </div>
</div>
</template>

<script>
import InputText from '@/components/Form/InputText'
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
			list: [],
			list_migrate: [],
			totalRecord: 0,
			isLoading: false,
			filter: {},
			pagination: {},
			perPage: '',
			form: {
				limit: 500,
				page: ''
			},
			elastic: {
				limit: 500,
				page: ''
			}
		}
	},
	components: {
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
					title: 'Trang',
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
				},
				{
					title: 'Đồng bộ dữ liệu S3',
					scopedSlots: {customRender: 'action'},
					align: 'left',
					width: '100px'
				},
				{
					title: 'Đồng bộ dữ liệu elastic',
					scopedSlots: {customRender: 'elastic'},
					align: 'center',
					width: '100px'
				}
			]
		},
		columns_list () {
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
					title: 'Trang',
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
		}
	},
	methods: {
		async Migrate () {
			const limit = this.form.limit
			const page = this.form.page
			const resp = await Migrate.getMigrate(limit, page)
			this.$toast.open({
				message: resp.data.message,
				type: 'success',
				position: 'top-right'
			})
			await this.getMigrate()
		},
		async migrateS3 (id, limit, page) {
			const resp = await Migrate.getS3(id, limit, page)
			this.$toast.open({
				message: resp.data.message,
				type: 'success',
				position: 'top-right'
			})
			await this.getMigrate()
		},
		async migrateElastic (id, limit, page) {
			const resp = await Migrate.getElastic(id, limit, page)
			this.$toast.open({
				message: resp.data.message,
				type: 'success',
				position: 'top-right'
			})
			await this.getMigrate()
		},
		async updateStatus () {
			const resp = await Migrate.getStatus()
			this.$toast.open({
				message: resp.data.message,
				type: 'success',
				position: 'top-right'
			})
			await this.getMigrateList()
		},
		handleReload () {
			this.getMigrate()
			this.getMigrateList()
		},
		async getMigrate (query = {}) {
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
						type: 4,
						...query,
						...filter
					}
				})

				this.list_migrate = [...resp.data.data]
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
			await this.getMigrate(params)
		},
		async getMigrateList (query = {}) {
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
						type: 5,
						...query,
						...filter
					}
				})

				this.list = [...resp.data.data]
				this.pagination = convertPagination(resp.data)
				this.isLoading = false
			} catch (e) {
				this.isLoading = false
			}
		},
		async onPageChangeList (pagination, filters, sorter) {
			this.perPage = pagination.pageSize
			const sortBy = `sortBy[${sorter.field}]`
			const sortDesc = replace(sorter.order, 'end', '')

			const params = {
				page: pagination.current,
				per_page: pagination.pageSize,
				[sortBy]: sortDesc
			}
			await this.getMigrateList(params)
		},
		async validateBeforeSubmit () {
			const isValid = await this.$refs.observer.validate()
			if (isValid) {
				await this.Migrate()
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
		this.getMigrate()
		this.getMigrateList()
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
</style>
