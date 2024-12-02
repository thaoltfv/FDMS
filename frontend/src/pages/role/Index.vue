<template>
  <div class="container-fluid">
    <div class="d-flex justify-content-end">
      <router-link :to="{name: 'role.create'}" class="btn btn-create btn-white text-nowrap index-screen-button m-0 px-3 w-auto" tag="button" v-if="add"><img
        src="../../assets/icons/ic_add.svg" style="margin-right: 8px" alt="icon add">
        Thêm mới vai trò
      </router-link>
    </div>
    <a-table bordered
             :columns="columns"
             :data-source="listRoles"
             :loading="isLoading"
             :rowKey="record => record.id"
             :pagination="{
               ...pagination,
             }"
             @change="onPageChange"
             class="table__import"
    >
      <!--Custom type table-->
      <template slot="action"
                slot-scope="action_edit , action_delete">
        <div class="d-flex justify-content-end">
          <a-tooltip placement="bottom"
                     :title="$t('tooltip_edit')" v-if="edit" class="mr-2">
            <a @click.prevent="handleEdit(action_edit.id)"
               href="#"
               class="text-decoration-none action">
              <img class="icon-action" src="../../assets/images/icon-edit.svg"
                   alt="icon">
            </a>
          </a-tooltip>
        </div>
      </template>
    </a-table>
    <ModalDelete v-if="openModal"
                 @cancel="openModal = false"
                 @action="handleDelete"/>
  </div>
</template>

<script>
import { convertPagination } from '@/utils/filters'
import {replace} from 'lodash-es'
import ModalDelete from '@/components/Modal/ModalDelete'
import Role from '@/models/Role'
export default {
	name: 'Index',

	components: {
		ModalDelete
	},

	data () {
		return {
			isLoading: false,
			provinces: [],
			listRoles: [],
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
		this.listRoles = this.$route.query['list']
		this.pagination = this.$route.meta['pagination']
		const permission = this.$store.getters.currentPermissions
		permission.forEach(value => {
			if (value === 'ADD_USER') {
				this.add = true
			}
			if (value === 'EDIT_USER') {
				this.edit = true
			}
			if (value === 'DELETE_USER') {
				this.deleted = true
			}
		})
	},

	computed: {
		columns () {
			return [
				{
					title: 'Tên nhóm người dùng',
					align: 'left',
					dataIndex: 'role_name',
					width: '100%'
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
		async onPageChange (pagination, filters, sorter) {
			this.perPage = pagination.pageSize
			const sortBy = `sortBy[${sorter.field}]`
			const sortDesc = replace(sorter.order, 'end', '')

			const params = {
				page: pagination.current,
				per_page: pagination.pageSize,
				[sortBy]: sortDesc
			}
			await this.getRoles(params)
		},

		async getRoles (params = {}) {
			this.isLoading = true
			const filter = {}

			for (let property in this.filter) {
				filter[`filters[${property}]`] = this.filter[property]
			}

			try {
				const resp = await Role.paginate({
					query: {
						page: 1,
						limit: 10,
						...params,
						...filter
					}
				})

				this.listRoles = [...resp.data.data]
				this.pagination = convertPagination(resp.data)
				this.isLoading = false
			} catch (e) {
				this.isLoading = false
			}
		},

		handleOpenModal (id) {
			this.openModal = true
			this.id = id
		},
		async handleDelete () {
			await Role.delete_role(this.id)
			await this.getRoles()
			this.$toast.open({
				message: 'Xóa phân quyền thành công',
				type: 'success',
				position: 'top-right'
			})
		},

		async handleEdit (id) {
			this.$router.push({
				name: 'role.edit',
				query: {
					id: id
				}
			}).catch(_ => {})
		}
	},
	beforeMount () {
		this.getRoles()
	}
}
</script>

<style lang="scss" scoped>
.table {
  margin-bottom: 0;
  th, td {
    padding: 1rem 2rem;
  }
}
.icon-action{
  width: 18px !important;
  height: auto;
}
.btn-create{
  margin-bottom: 47px !important;
}
</style>
