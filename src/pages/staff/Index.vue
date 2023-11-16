<template>
  <div class="container-fluid">
    <div class="loading" :class="{'loading__true': isSubmit}">
      <a-spin />
    </div>
    <div class="pannel">
      <div class="pannel__content d-md-flex d-block justify-content-end align-items-center">
        <Search @filter-changed="onFilterChange($event)" class="search-box"/>
        <div class="container__input" v-if="add">
          <button type="button" class="btn btn-create btn-white text-nowrap"><img
            src="../../assets/icons/ic_export.svg" alt="excel" style="margin-right: 8px"/> Tải lên </button>
          <input class="input__excel" type="file" accept=".xls, .xlsx" @change="onExcelChange($event)">
        </div>
        <router-link :to="{name: 'staff.create'}" class="btn btn-create btn-white text-nowrap index-screen-button mt-md-0 mt-2 m-0 px-3" tag="button" v-if="add && can_add"><img
          src="../../assets/icons/ic_add.svg" style="margin-right: 8px" alt="icon add">
          Tạo nhân viên
        </router-link>
      </div>
	  <div class="pannel__content d-md-flex d-block justify-content-start align-items-center">
		<div style="margin-right: 20px;">
			<span style="font-size: 20px;
                font-weight: bold;
                color: white;
                padding: 5px;
                border-radius: 5px;
				background: green">Tổng cộng: {{list_total.length}}</span>
		</div>
		<div style="margin-right: 20px;">
			<span style="font-size: 20px;
                font-weight: bold;
                color: white;
                padding: 5px;
                border-radius: 5px;
				background: #0062AF">Đang kích hoạt: {{count_enable}}</span>
		</div>
		<div>
			<span style="font-size: 20px;
                font-weight: bold;
                color: white;
                padding: 5px;
                border-radius: 5px;
				background: #6E7582">Vô hiệu hóa: {{count_disable}}</span>
		</div>
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
             @change="onPageChange"
             class="table__user table__import"
    >
      <!--Custom type table-->
      <template slot="action"
                slot-scope="action_edit , action">
        <div class="d-flex justify-content-end">
			<a-tooltip placement="bottom"
                     :title="$t('tooltip_is_legal_representative')" v-if="active && action.is_legal_representative == 0 && action.appraiser_number !== ''" class="mr-2">
            <a href="#"
               @click.prevent="handleOpenModalIsLegal(action)"
               class="text-decoration-none action">
              <img class="icon-action" src="../../assets/images/account-tie.svg"
                   alt="icon">
            </a>
          </a-tooltip>
		  <a-tooltip placement="bottom"
                     :title="$t('tooltip_isnt_legal_representative')" v-if="deactive && action.is_legal_representative == 1 && action.appraiser_number !== ''" class="mr-2">
            <a href="#"
               @click.prevent="handleOpenModalIsntLegal(action)"
               class="text-decoration-none action">
              <img class="icon-action" src="../../assets/images/account-convert.svg"
                   alt="icon">
            </a>
          </a-tooltip>
          <a-tooltip placement="bottom"
                     :title="$t('tooltip_edit')" v-if="edit" class="mr-2">
            <a @click.prevent="handleEdit(action_edit.id)"
               href="#"
               class="text-decoration-none action">
              <img class="icon-action" src="../../assets/images/icon-edit.svg"
                   alt="icon">
            </a>
          </a-tooltip>
          <!-- <a-tooltip placement="bottom"
                     :title="$t('tooltip_delete')" v-if="deleted" class="mr-2">
            <a href="#"
               @click.prevent="handleOpenModal(action.id)"
               class="text-decoration-none action">
              <img class="icon-action" src="../../assets/images/icon-delete.svg"
                   alt="icon">
            </a>
          </a-tooltip> -->
		  <a-tooltip placement="bottom"
                     :title="$t('tooltip_active')" v-if="active && action.status_user == 'deactive'" class="mr-2">
            <a href="#"
               @click.prevent="handleOpenModalActive(action)"
               class="text-decoration-none action">
              <img class="icon-action" src="../../assets/images/lock-check-outline.svg"
                   alt="icon">
            </a>
          </a-tooltip>
		  <a-tooltip placement="bottom"
                     :title="$t('tooltip_deactive')" v-if="deactive && action.status_user == 'active'" class="mr-2">
            <a href="#"
               @click.prevent="handleOpenModalDeActive(action)"
               class="text-decoration-none action">
              <img class="icon-action" src="../../assets/images/lock-alert-outline.svg"
                   alt="icon">
            </a>
          </a-tooltip>
          <a-tooltip placement="bottom"
                     :title="$t('tooltip_reset')" v-if="edit" >
            <a href="#"
               @click.prevent="handleOpenModalReset(action)"
               class="text-decoration-none action">
              <img class="icon-action" src="../../assets/icons/ic_reset.svg"
                   alt="icon">
            </a>
          </a-tooltip>
        </div>
      </template>
	  <template slot="status_user"
                slot-scope="status_user">
        <div class="d-flex justify-content-center">
			<span v-if="status_user == 'active'" style="font-size: 14px;
                font-weight: bold;
                color: white;
                padding: 5px;
                border-radius: 5px;
				background: #0062AF"> Đang kích hoạt</span>
			<span v-else style="font-size: 14px;
                font-weight: bold;
                color: white;
                padding: 5px;
                border-radius: 5px;
				background: #6E7582"> Vô hiệu hóa</span>
		</div>
	</template>
	<template slot="is_legal_representative"
                slot-scope="is_legal_representative">
        <div class="d-flex justify-content-center">
			<span v-if="is_legal_representative == 1" style="font-size: 14px;
                font-weight: bold;
                color: white;
                padding: 5px;
                border-radius: 5px;
				background: #9c6c2b"> Đại diện pháp luật</span>
			<!-- <span v-else style="font-size: 14px;
                font-weight: bold;
                color: white;
                padding: 5px;
                border-radius: 5px;
				background: #6E7582"> Vô hiệu hóa</span> -->
		</div>
	</template>
    </a-table>
    <ModalDelete v-if="openModal"
                 @cancel="openModal = false"
                 @action="handleDelete"/>
	<ModalDeActive v-if="openModalDeActive" :name="choose_name"
	@cancel="openModalDeActive = false"
	@action="handleDeActive"/>
	<ModalActive v-if="openModalActive" :name="choose_name"
	@cancel="openModalActive = false"
	@action="handleActive"/>
	<ModalIsntLegal v-if="openModalIsntLegal" :name="choose_name"
	@cancel="openModalIsntLegal = false"
	@action="handleIsntLegal"/>
	<ModalIsLegal v-if="openModalIsLegal" :name="choose_name"
	@cancel="openModalIsLegal = false"
	@action="handleIsLegal"/>
    <ModalReset v-if="openModalReset" :name="choose_name"
                @cancel="openModalReset = false"
                @action="handleReset"
    />
  </div>
</template>

<script>
import Search from './Search'
import User from '@/models/User'
import { convertPagination } from '@/utils/filters'
import {replace} from 'lodash-es'
import ModalDelete from '@/components/Modal/ModalDelete'
import ModalDeActive from '@/components/Modal/ModalDeActive'
import ModalActive from '@/components/Modal/ModalActive'
import ModalIsntLegal from '@/components/Modal/ModalIsntLegal'
import ModalIsLegal from '@/components/Modal/ModalIsLegal'
import ModalReset from '@/components/Modal/ModalReset'
import InputCategory from '@/components/Form/InputCategory'
import AppraiserCompany from '@/models/AppraiserCompany'
import File from '@/models/File'
export default {
	name: 'index',
	components: {
		ModalDelete,
		ModalDeActive,
		ModalActive,
		ModalIsntLegal,
		ModalIsLegal,
		ModalReset,
		InputCategory,
		Search
	},
	data () {
		return {
			choose_name: '',
			openModalActive: false,
			openModalDeActive: false,
			openModalIsLegal: false,
			openModalIsntLegal: false,
			openModalReset: false,
			isSubmit: false,
			excel: '',
			isLoading: false,
			provinces: [],
			list: [],
			list_total1: [],
			filter: {},
			pagination: {},
			perPage: '',
			openModal: false,
			form: {
				id: ''
			},
			add: false,
			deleted: false,
			active: false,
			deactive: false,
			edit: false,
			email: '',
			total_account: null,
			can_add: false
		}
	},
	// async mounted() {
	// 	const appraiserCompany = await AppraiserCompany.detail()
	// 	// console.log(';dsdsad',appraiserCompany )
	// 	this.total_account = appraiserCompany.data.data[0].total_account
	// 	// console.log('total 1', this.total_account)
	// 	// console.log('total 1', this.total_account, this.list_total1.length)
	// 	// if (this.total_account && this.list_total1.length){
	// 	// 	console.log('total 1', this.total_account, this.list.length)
	// 	// }
	// },
	created () {
		this.list = this.$route.query['list']
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
				this.active = true
				this.deactive = true
			}
		})
		// console.log('total 2',this.total_account)
	},
	computed: {
		list_total() {
			return this.list_total1
		},
		columns () {
			return [
				{
					title: 'Họ tên',
					align: 'left',
					dataIndex: 'name'
				},
				{
					title: 'Số điện thoại',
					align: 'left',
					dataIndex: 'phone'
				},
				{
					title: 'Địa chỉ',
					align: 'left',
					dataIndex: 'address',
					width: '50%'
				},
				{
					title: 'Quyền hạn',
					align: 'left',
					dataIndex: 'roles[0].role_name',
					width: '50%'
				},
				{
					title: 'Chi nhánh',
					align: 'left',
					dataIndex: 'branch.name'
				},
				{
					title: 'Pháp lý',
					align: 'center',
					dataIndex: 'is_legal_representative',
					scopedSlots: {customRender: 'is_legal_representative'},
				},
				{
					title: 'Trạng thái',
					align: 'center',
					dataIndex: 'status_user',
					scopedSlots: {customRender: 'status_user'},
				},
				{
					title: 'Thao tác',
					scopedSlots: {customRender: 'action'},
					align: 'right',
					width: '100px'
				}
			]
		},
		count_disable() {
			let count = this.list_total1.filter(function (item) {
				return item.status_user == 'deactive'
			}).length
			return count
		},
		count_enable() {
			let count = this.list_total1.filter(function (item) {
				return item.status_user == 'active'
			}).length
			return count
		},
	},
	methods: {

		async onExcelChange (e) {
			let files = e.target.files || e.dataTransfer.files
			if (!files.length) {
				return
			}
			this.file = e.target.files[0]
			// this.createExcel()
			await this.uploadExcel()
		},
		uploadExcel () {
			this.isLoading = true
			const formData = new FormData()
			formData.append('import_file', this.file)
			return File.uploadExcel({data: formData}).then(response => {
				if (response && response.data && response.data.data) {
					this.isLoading = false
					this.getStaffs()
					this.$toast.open({
						message: 'Cập nhật thành công',
						type: 'success',
						position: 'top-right'
					})
				} else if (response.data.error) {
					this.isLoading = false
					this.$toast.open({
						message: response.data.error.message,
						type: 'error',
						position: 'top-right',
						duration: 5000
					})
				}
			})
		},
		async onFilterChange ($event) {
			const params = {
				page: 1,
				limit: this.limit || 20
			}

			this.filter = {...$event}
			await this.getStaffs(params)
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
			await this.getStaffs(params)
		},

		async handleEdit (id) {
			this.$router.push({
				name: 'staff.edit',
				query: {
					id: id
				}
			}).catch(_ => {})
		},
		async handleDelete () {
			await User.deleteUser(this.id)
			await this.getStaffs()
			await this.getStaffsFull()
			this.$toast.open({
				message: 'Xóa nhân viên thành công',
				type: 'success',
				position: 'top-right'
			})
		},
		async handleDeActive () {
			await User.deActiveUser(this.id)
			await this.getStaffs()
			await this.getStaffsFull()
			this.$toast.open({
				message: 'Vô hiệu hóa tài khoản nhân viên thành công',
				type: 'success',
				position: 'top-right'
			})
		},
		async handleActive () {
			await User.activeUser(this.id)
			await this.getStaffs()
			await this.getStaffsFull()
			this.$toast.open({
				message: 'Kích hoạt lại tài khoản nhân viên thành công',
				type: 'success',
				position: 'top-right'
			})
		},
		async handleIsntLegal () {
			await User.IsntLegalUser(this.id)
			await this.getStaffs()
			await this.getStaffsFull()
			this.$toast.open({
				message: 'Bỏ chọn đại diện pháp luật thành công',
				type: 'success',
				position: 'top-right'
			})
		},
		async handleIsLegal () {
			await User.IsLegalUser(this.id)
			await this.getStaffs()
			await this.getStaffsFull()
			this.$toast.open({
				message: 'Chọn đại diện pháp luật thành công',
				type: 'success',
				position: 'top-right'
			})
		},
		async handleReset () {
			await User.resetUser(this.id)
			await this.getStaffs()
			await this.getStaffsFull()
			this.$toast.open({
				message: 'Đã gửi email đặt lại mật khẩu thành công',
				type: 'success',
				position: 'top-right'
			})
		},
		async getStaffs (params = {}) {
			this.isLoading = true
			const filter = {}

			for (let property in this.filter) {
				filter[`${property}`] = this.filter[property]
			}

			try {
				const resp = await User.paginate({
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
		async getStaffsFull (params = {}) {
			this.isLoading = true
			const filter = {}

			try {
				const resp = await User.paginate({
					query: {
						page: 1,
						limit: 2000000,
						...params,
						...filter
					}
				})

				this.list_total1 = [...resp.data.data]
				const appraiserCompany = await AppraiserCompany.detail()
				// console.log(';dsdsad',appraiserCompany )
				this.total_account = appraiserCompany.data.data[0].total_account
				if (this.list_total1.filter(function (item) {
					return item.status_user == 'active'
				}).length >= this.total_account){
					this.can_add = false
				} else {
					this.can_add = true
				}
				this.isLoading = false
			} catch (e) {
				this.isLoading = false
			}
		},

		async searchStaff () {
			this.loading = true
		},

		handleOpenModal (id) {
			this.openModal = true
			this.id = id
		},
		handleOpenModalActive (action) {
			this.openModalActive = true
			this.id = action.id
			this.choose_name = action.name
		},
		handleOpenModalDeActive (action) {
			this.openModalDeActive = true
			this.id = action.id
			this.choose_name = action.name
		},
		handleOpenModalIsLegal (action) {
			this.openModalIsLegal = true
			this.id = action.id
			this.choose_name = action.name
		},
		handleOpenModalIsntLegal (action) {
			this.openModalIsntLegal = true
			this.id = action.id
			this.choose_name = action.name
		},
		handleOpenModalReset (action) {
			this.openModalReset = true
			this.choose_name = action.name
			this.id = action.id
		}
	},
	beforeMount () {
		this.filter['staff_id'] = 45
		this.getStaffs()
		this.getStaffsFull()
	},
	async handleDelete () {
		try {
			await User.deleteUser(this.id)
		} catch (err) {
			await this.onError(this.$t('message_error'), this.$t('delete_message_error'))
		}
	}
}
</script>
<style scoped lang="scss">
.pannel{
  background: #FFFFFF;
  //box-shadow: 1px 2px 0 #e5eaee;
  border-radius: 5px;
  //padding: 25px;
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
.container{
  &__input {
    position: relative;
    height: 35px;
    cursor: pointer;
    margin-right: 15px;
    @media (max-width: 767px) {
      margin-right: 0;
    }
    .btn-white {
      margin-right: 0;
    }
    .input {
      &__excel{
        cursor: pointer;
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        width: 100%;
        opacity: 0;
        height: 35px;
      }
    }
  }
}
.loading{
  display: none;
  &__true{
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 100dvh;
    background: rgba(0, 0, 0, 0.62);
    z-index: 100000;
    display: flex;
    align-items: center;
    justify-content: center;
    &.btn-loading{
      &:after{
        width: 2rem !important;
        height: 2rem !important;
      }
    }
  }
}
</style>
