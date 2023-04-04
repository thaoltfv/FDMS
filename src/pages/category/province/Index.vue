<template>
 <div class="container-fluid">
   <div class="pannel">
     <div class="pannel__content d-md-flex d-block justify-content-end align-items-center">
       <Search @filter-changed="onFilterChange($event)" />
       <router-link :to="{name: 'province.create'}" class="btn btn-create btn-white text-nowrap index-screen-button mb-0 px-3" tag="button" v-if="add"><img
         src="../../../assets/icons/ic_add.svg" style="margin-right: 8px" alt="icon add">
         Tạo Tỉnh/Thành
       </router-link>
     </div>
   </div>
   <a-table bordered
            :columns="columns"
            :data-source="list"
            :loading="isLoading"
            :rowKey="record => record.id"
            table-layout="top"
            :pagination="{
               ...pagination,
             }"
            @change="onPageChange">
     <!--Custom type table-->
     <template slot="action"
               slot-scope="action_edit , action_delete">
       <div class="d-flex justify-content-end">
         <a-tooltip placement="bottom"
                    :title="$t('tooltip_edit')" v-if="edit" class="mr-2">
           <a @click.prevent="handleEdit(action_edit.id)"
              href="#"
              class="text-decoration-none action">
             <img class="icon-action" src="../../../assets/images/icon-edit.svg"
                  alt="icon">
           </a>
         </a-tooltip>
         <a-tooltip placement="bottom"
                    :title="$t('tooltip_delete')" v-if="deleted">
           <a href="#"
              @click.prevent="handleOpenModal(action_delete.id)"
              class="text-decoration-none action">
             <img class="icon-action" src="../../../assets/images/icon-delete.svg"
                  alt="icon">
           </a>
         </a-tooltip>
       </div>
     </template>
     <template slot="created_at" slot-scope="created_at">
       <p class="created_at mb-0">{{created_at | formatDate}}</p>
     </template>

<!--     <template slot="action_delete"-->
<!--               slot-scope="action_delete">-->
<!--     </template>-->
   </a-table>
   <ModalDelete v-if="openModal"
                @cancel="openModal = false"
                @action="handleDelete"/>
 </div>
</template>

<script>

import Category from '@/models/Category'
import { convertPagination } from '@/utils/filters'
import Search from './Search'
import {replace} from 'lodash-es'
import ModalDelete from '@/components/Modal/ModalDelete'
import Vue from 'vue'
import moment from 'moment'
Vue.filter('formatDate', function (value) {
	if (value) {
		return moment(String(value)).format('DD/MM/YYYY')
	}
})
export default {
	name: 'index',
	components: {
		Search,
		ModalDelete
	},
	data () {
		return {
			isLoading: false,
			list: [],
			filter: {},
			pagination: {},
			perPage: '',
			openModal: false,
			add: false,
			edit: false,
			deleted: false
		}
	},
	created () {
		this.list = this.$route.query['list']
		this.pagination = this.$route.meta['pagination']
		const permission = this.$store.getters.currentPermissions
		permission.forEach(value => {
			if (value === 'ADD_CATEGORY') {
				this.add = true
			}
			if (value === 'EDIT_CATEGORY') {
				this.edit = true
			}
			if (value === 'DELETE_CATEGORY') {
				this.deleted = true
			}
		})
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
					title: 'Tên Tỉnh/Thành ',
					align: 'left',
					dataIndex: 'name'
				},
				{
					title: 'Ngày tạo',
					align: 'left',
					scopedSlots: {customRender: 'created_at'},
					dataIndex: 'created_at'
				},
				{
					title: 'Thao tác',
					scopedSlots: {customRender: 'action'},
					align: 'right',
					width: '100px'
				}
			]
		}
	},
	methods: {

		async onFilterChange ($event) {
			const params = {
				page: 1,
				limit: this.limit || 20
			}
			this.filter = {...$event}
			await this.getProvinces(params)
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
			await this.getProvinces(params)
		},

		async handleEdit (id) {
			this.$router.push({
				name: 'province.edit',
				query: {
					id: id
				}
			}).catch(_ => {})
		},

		async handleDelete () {
			await Category.deleteProvince(this.id)
			await this.getProvinces()
			this.$toast.open({
				message: 'Xóa Tỉnh/Thành thành công',
				type: 'success',
				position: 'top-right'
			})
		},

		async getProvinces (params = {}) {
			this.isLoading = true
			const filter = {}

			for (let property in this.filter) {
				filter[`${property}`] = this.filter[property]
			}

			try {
				const resp = await Category.paginate({
					query: {
						page: 1,
						limit: 20,
						...params,
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

		handleOpenModal (id) {
			this.openModal = true
			this.id = id
		}
	},

	beforeMount () {
		this.getProvinces()
	},

	async handleDelete () {
		try {
			await Category.deleteProvince(this.id)
		} catch (err) {
			await this.onError(this.$t('message_error'), this.$t('delete_message_error'))
		}
	}
}
</script>
<style scoped lang="scss">
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
  .form-control{
    margin-right: 5px;
    width: auto;
    color: #555555;
    border-radius: 5px;

    @media (max-width: 1023px) {
      width: 100%;
    }
    &:focus{
      border-color: #CCCCCC;
      box-shadow: none;
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
  .btn{
    &-create{
      //margin-top: 10px;
      @media (max-width: 1023px) {
        //width: 100%;
      }
    }
  }
  .icon-action{
    width: 18px !important;
    height: auto;
  }
</style>
