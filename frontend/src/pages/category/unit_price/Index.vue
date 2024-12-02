<template>
 <div class="container-fluid">
   <div class="pannel">
     <div class="pannel__content d-xl-flex d-block justify-content-between align-items-end">
       <Search @filter-changed="onFilterChange($event)" />
     </div>
   </div>
   <a-table bordered
            :columns="columns"
            :data-source="list"
            :loading="isLoading"
            :rowKey="record => record.id"
            :pagination="{
               ...pagination,
             }"
            class="table__import table__unit-price"
            @change="onPageChange">
     <template slot="id" slot-scope="id">
       <p class="input" @click="handleDetail(id)">{{id}}</p>
     </template>
     <template slot="vt1" slot-scope="vt1">
       <p class="mb-0" >{{formatNumber(vt1) + 'đ'}}</p>
     </template>
     <template slot="vt2" slot-scope="vt2">
       <p class="mb-0">{{formatNumber(vt2) + 'đ'}}</p>
     </template>
     <template slot="vt3" slot-scope="vt3">
       <p class="mb-0" >{{formatNumber(vt3) + 'đ'}}</p>
     </template>
     <template slot="vt4" slot-scope="vt4">
       <p class="mb-0">{{formatNumber(vt4) + 'đ'}}</p>
     </template>
   </a-table>
 </div>
</template>

<script>
import UnitPrice from '@/models/UnitPrice'
import { convertPagination } from '@/utils/filters'
import {replace} from 'lodash-es'
import InputCategory from '@/components/Form/InputCategory'
import ModalDelete from '@/components/Modal/ModalDelete'
import Search from './Search'
export default {
	name: 'index',
	components: {
		Search,
		ModalDelete,
		InputCategory
	},
	data () {
		return {
			isLoading: false,
			list: [],
			filter: {},
			pagination: {},
			perPage: '',
			openModal: false,
			form: {
				province_id: '',
				district_id: '',
				search: ''
			},
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
					align: 'left',
					dataIndex: 'id',
					scopedSlots: {customRender: 'id'},
					width: '4%'
				},
				{
					title: 'Tên Tỉnh/Thành',
					align: 'left',
					dataIndex: 'province'
				},
				{
					title: 'Tên Quận/Huyện',
					align: 'left',
					dataIndex: 'district'
				},
				{
					title: 'Tên Phường/Xã',
					align: 'left',
					dataIndex: 'ward'
				},
				{
					title: 'Tên đường',
					align: 'left',
					dataIndex: 'street'
				},
				{
					title: 'Tên đoạn',
					align: 'left',
					dataIndex: 'distance'
				},
				{
					title: 'Loại đất',
					align: 'left',
					dataIndex: 'land_type'
				},
				{
					title: 'Vị trí 1',
					align: 'left',
					scopedSlots: {customRender: 'vt1'},
					dataIndex: 'vt1'
				},
				{
					title: 'Vị trí 2',
					align: 'left',
					scopedSlots: {customRender: 'vt2'},
					dataIndex: 'vt2'
				},
				{
					title: 'Vị trí 3',
					align: 'left',
					scopedSlots: {customRender: 'vt3'},
					dataIndex: 'vt3'
				},
				{
					title: 'Vị trí 4',
					align: 'left',
					scopedSlots: {customRender: 'vt4'},
					dataIndex: 'vt4'
				}
			]
		}
	},
	methods: {
		formatNumber (value) {
			let num = (value / 1).toFixed(0).replace('.', ',')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
		},
		async onFilterChange ($event) {
			const params = {
				page: 1,
				limit: this.limit || 20
			}

			this.filter = {...$event}
			await this.listUnitPrice(params)
		},
		changeProvince (provinceId) {
			this.districts = []
			this.form.district_id = ''
			this.getDistrictsByProvinceId(+provinceId)
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
			await this.listUnitPrice(params)
		},
		async handleDetail (id) {
			this.$router.push({
				name: 'unit_price.detail',
				query: {
					id: id
				}
			}).catch(_ => {
			})
		},
		async listUnitPrice (params = {}) {
			this.isLoading = true
			const filter = {}

			for (let property in this.filter) {
				filter[`${property}`] = this.filter[property]
			}

			try {
				const resp = await UnitPrice.paginate({
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
		}
	},
	beforeMount () {
		this.listUnitPrice()
	}
}
</script>
<style scoped lang="scss">
  .pannel{
    background: #FFFFFF;
    border-radius: 5px;
    margin-bottom: 20px;
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
        margin-top: 1rem !important;
      }
    }
  }
  .input_category{
    margin-right: 10px;
    width: 20%;
    label{
      display: none;
    }
  }
  .icon-action{
    width: 18px !important;
    height: auto;
  }
  .input {
    color: #FAA831;
    cursor: pointer;
    margin-bottom: 0;
  }
</style>
