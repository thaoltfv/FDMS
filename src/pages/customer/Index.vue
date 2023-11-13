<template>
  <div id="customer" class="container-fluid position-relative">
    <div class="loading" :class="{'loading__true': isSubmit}">
      <a-spin />
    </div>
    <div class="pannel">
      <div class="pannel__content row mx-0 justify-content-between justify-content-lg-end align-items-center">
        <Search @filter-changed="onFilterChange($event)" class="search-box"/>
        <div class="justify-content-between btn-container">
          <button class="btn btn-white text-nowrap index-screen-button" @click.prevent="handleSearch">
            <img src="../../assets/icons/ic_search.svg" style="margin-right: 8px" alt="search">Tìm kiếm
          </button>
          <router-link :to="{name: 'customer.create'}" class="btn btn-white text-nowrap index-screen-button" v-if="add"><img
            src="../../assets/icons/ic_add.svg" style="margin-right: 8px" alt="search">Tạo mới
          </router-link>
          <button class="btn btn-white text-nowrap index-screen-button" @click="handleDisable" v-if="edit"
                  :class="selectedRowKeys.length === 0 ? 'disabled' : ''"><img src="../../assets/icons/ic_disable.svg"
                                                                               style="margin-right: 8px" alt="search"
          > Vô
            hiệu hóa
          </button>
          <button class="btn btn-white text-nowrap index-screen-button" @click="handleActive" v-if="edit"
                  :class="selectedRowKeys.length === 0 ? 'disabled' : ''"><img src="../../assets/icons/ic_active.svg"
                                                                               style="margin-right: 8px" alt="search"
          > Kích hoạt lại
          </button>
          <button class="btn btn-white text-nowrap index-screen-button"
                  :class="selectedRowKeys.length === 0 ? 'disabled' : ''"
                  type="button"
                  @click="onShowSelectedImages"
          ><img
            src="../../assets/icons/ic_img.svg"
            style="margin-right: 12px" alt="img">
            Xem hình ảnh
          </button>
          <download-excel :data="selectedRowKeys"
                          :fields="json_fields"
                          :class="selectedRowKeys.length === 0 ? 'disabled' : ''"
                          worksheet="document"
                          name="Donava.xls"
                          class="btn btn-white text-nowrap index-screen-button"
          ><img
            src="../../assets/icons/ic_export.svg"
            style="margin-right: 8px" alt="search"> Xuất file excel
          </download-excel>
        </div>
      </div>
    </div>
    <div class="table-detail position-relative" :class="totalRecord === 0 ? 'empty-data' : ''">
      <a-table bordered
               :columns="columns"
               :data-source="listCustomer"
               :loading="isLoading"
               class="table-property"
               :row-selection="rowSelection"
               :rowKey="record => record.id"
               :pagination="{
                 ...pagination,
                 showSizeChanger: true,
                 pageSizeOptions: ['10', '20', '30'],
               }"
               @change="onPageChange">
        <div
          slot="filterDropdown"
          slot-scope="{ setSelectedKeys, selectedKeys, confirm, clearFilters, column }"
          class="filter-container"
        >
          <div class="input-filter-container">
            <a-input
              v-ant-ref="c => (searchInput = c)"
              :placeholder="`Tìm theo người tạo`"
              :value="selectedKeys[0]"
              style="width: 188px; margin-bottom: 8px; display: block;"
              @change="e => setSelectedKeys(e.target.value ? [e.target.value] : [])"
              @pressEnter="() => handleSearches(selectedKeys, confirm, column.dataIndex)"
            />
          </div>
          <div class="container-filter">
            <div class="author-list">
              <label class="author-item w-100">
                <span>donava@gmail.com</span>
                <div class="d-flex align-items-center">
                  <input type="checkbox"
                         :value="'donava@gmail.com'"
                         @change="(e) => {
                           setSelectedKeys(e.target.checked ? [e.target.value] : [])
                           handleSearches(selectedKeys, confirm, column.dataIndex)
                         }">
                </div>
              </label>
              <label class="author-item w-100">
                <span>Chọn tất cả</span>
                <div class="d-flex align-items-center"><input type="checkbox"></div>
              </label>
            </div>
            <div class="text-right filter-controls">
              <a-button
                size="small"
                style="width: 69px"
                @click="() => handleSearches(selectedKeys, confirm, column.dataIndex)"
                class="btn-search-dropdown"
              >
                Lọc
              </a-button>
            </div>
          </div>
        </div>
        <a-icon
          slot="filterIcon"
          slot-scope="filtered"
          type="search"
          :style="{ color: filtered ? '#108ee9' : undefined }"
        />
        <!--Custom type table-->
        <template slot="id" slot-scope="id, property">
          <div class="d-flex justify-content-center align-items-center position-relative">
            <button @click.prevent="handleDetail(id)" class="link-detail">{{ 'DT_' + id }}</button>
          </div>
        </template>
        <template slot="address" slot-scope="address, id">
          <p :id="id.id" class=" full-address">{{ address }}</p>
          <b-tooltip :target="(id.id).toString()">{{ address }}</b-tooltip>
        </template>
        <template slot="status" slot-scope="status">
          <p class="status" :class="status === 'inactive'? 'orange' : ''">
            {{ status === 'inactive' ? 'Đang vô hiệu hóa' : status === 'active' ? 'Đang hoạt động' : ''}}</p>
        </template>
        <template slot="created_by" slot-scope="created_by">
          <p class="create_by mb-0">{{ created_by }}</p>
        </template>
        <template slot="created_at" slot-scope="created_at">
          <p class="public_date mb-0">{{ formatDate(created_at) }}</p>
        </template>
      </a-table>
      <div class="total position-absolute" v-if="totalRecord > 0">
        Tổng cộng: {{ totalRecord }} đối tác
      </div>
    </div>
    <ModalSearchCustomer
      v-if="showModalSearch"
      @cancel="showModalSearch = false"
      @filter-changed="onFilterChange($event)"
      v-bind:roles="this.activeStatus"
      :filter_search="this.filter"
    />
    <ModalViewImageList
      v-if="showModalImage"
      @cancel="showModalImage = false"
      v-bind:pics="this.picList"
    />
    <ModalNotification
      v-if="modalNotification"
      @cancel="modalNotification = false"
      v-bind:notification="this.message"
    />
    <ModalNotificationCustomer
      v-if="modalNotificationCustomer"
      @cancel="modalNotificationCustomer = false"
      v-bind:notification="this.messageNotification"
      @action="actionDisable"
    />
  </div>
</template>
<script>
import Search from '@/pages/customer/Search'
import {replace} from 'lodash-es'
import Customer from '@/models/Customer'
import {convertPagination} from '@/utils/filters'
import ModalSearchAdvanced from '@/components/Modal/ModalSearchAdvanced'
import ModalViewImage from '@/components/Modal/ModalViewImage'
import moment from 'moment'
import JsonExcel from 'vue-json-excel'
import Vue from 'vue'
import {STATUS} from '@/enum/status.enum'
import {STATUS_CUSTOMER} from '@/enum/status-customer.enum'
import ModalPrint from '@/components/Modal/ModalPrint'
import {BDropdown, BDropdownItem, BTooltip} from 'bootstrap-vue'
import ModalNotification from '@/components/Modal/ModalNotification'
import ModalNotificationCustomer from '@/components/Modal/ModalNotificationCustomer'
import ModalSearchCustomer from '../../components/Modal/ModalSearchCustomer'
import ModalViewImageList from '../../components/Modal/ModalViewImageList'

Vue.filter('formatDate', function (value) {
	if (value) {
		return moment(String(value)).format('DD/MM/YYYY')
	}
})
Vue.component('downloadExcel', JsonExcel)
export default {
	name: 'index',
	components: {
		Search,
		ModalSearchAdvanced,
		ModalNotification,
		ModalViewImage,
		ModalPrint,
		ModalNotificationCustomer,
		ModalSearchCustomer,
		ModalViewImageList,
		'b-dropdown': BDropdown,
		'b-dropdown-item': BDropdownItem,
		'b-tooltip': BTooltip
	},
	data () {
		return {
			printDetail: '',
			cancelSearch: false,
			modalNotification: false,
			isSubmit: false,
			json_fields: {
				'Ma_khach_hang': 'id',
				'Ten_khach_hang': 'name',
				'SDT': 'phone',
				'Dia_chi': 'address',
				'Ngay_tao (VND)': 'created_date',
				'Nguoi_tao': 'created_by',
				'Trang_thai': 'status'
			},
			json_meta: [
				[
					{
						key: 'charset',
						value: 'utf-8'
					}
				]
			],
			arr: [],
			status: {
				status: STATUS.INACTIVE

			},
			enable: {
				status: STATUS.ACTIVE
			},
			modalPrint: false,
			activeStatus: false,
			showModalSearch: false,
			showModalImage: false,
			selectedRowKeys: [],
			isLoading: false,
			provinces: [],
			listCustomer: [],
			filter: {},
			pagination: {},
			perPage: '',
			openModal: false,
			form: {
				id: ''
			},
			picList: {
				images: [],
				address: [],
				id: []
			},
			modalNotificationCustomer: false,
			active: false,
			disable: false,
			totalRecord: 0,
			add: false,
			edit: false,
			delete: false
		}
	},
	created () {
		const permission = this.$store.getters.currentPermissions
		permission.forEach(value => {
			if (value === 'ADD_CUSTOMER') {
				this.add = true
			}
			if (value === 'EDIT_CUSTOMER') {
				this.edit = true
			}
			if (value === 'DELETE_CUSTOMER') {
				this.deleted = true
			}
		})
	},
	computed: {
		columns () {
			return [
				{
					title: 'Mã',
					align: 'center',
					scopedSlots: {customRender: 'id'},
					dataIndex: 'id',
					sorter: true,
					width: '180px',
					sortDirections: ['descend', 'ascend']
				},
				{
					title: 'Tên đối tác',
					align: 'center',
					dataIndex: 'name',
					sorter: true,
					sortDirections: ['descend', 'ascend']
				},
				{
					title: 'Số điện thoại',
					align: 'center',
					scopedSlots: {customRender: 'phone'},
					sorter: true,
					dataIndex: 'phone'
				},
				{
					title: 'Địa chỉ',
					align: 'center',
					scopedSlots: {customRender: 'address'},
					dataIndex: 'address',
					sorter: true,
					sortDirections: ['descend', 'ascend']
				},
				{
					title: 'Ngày tạo',
					align: 'center',
					dataIndex: 'created_at',
					scopedSlots: {customRender: 'created_at'},
					sorter: true,
					sortDirections: ['descend', 'ascend']
				},
				// {
				// 	title: 'Người tạo',
				// 	align: 'center',
				// 	dataIndex: 'created_by',
				// 	// scopedSlots: {customRender: 'created_by'},
				// 	scopedSlots: {
				// 		// filterDropdown: 'filterDropdown',
				// 		// filterIcon: 'filterIcon',
				// 		customRender: 'created_by'
				// 	},
				// 	onFilter: (value, record) =>
				// 		record.created_by
				// 			.toString()
				// 			.toLowerCase()
				// 			.includes(value.toLowerCase()),
				// 	onFilterDropdownVisibleChange: visible => {
				// 		if (visible) {
				// 			// eslint-disable-next-line vue/no-async-in-computed-properties
				// 			setTimeout(() => {
				// 				this.searchInput.focus()
				// 			}, 0)
				// 		}
				// 	},
				// 	width: '100px',
				// 	sorter: true,
				// 	sortDirections: ['descend', 'ascend']
				// },
				{
					title: 'Trạng thái',
					align: 'center',
					scopedSlots: {customRender: 'status'},
					dataIndex: 'status',
					sorter: true,
					sortDirections: ['descend', 'ascend']
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
							parseInt(row.id)
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
		formatDate (value) {
			return moment(String(value)).format('DD/MM/YYYY')
		},
		async openPrint (id) {
			this.isSubmit = true
			await Customer.getPrint(id).then((resp) => {
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
		async openPrintPdf (id) {
			this.isSubmit = true
			await this.getPrint(id)
			this.modalPrint = true
			this.isSubmit = false
		},
		async getPrint (id) {
			const resp = await Customer.getPrintPdf(id)
			this.printDetail = resp.data.url
		},
		async disableClick (index) {
			const query = {
				query: {
					page: 1,
					limit: this.limit || 20
				}
			}
			this.isSubmit = true
			await Customer.updateStatus('[' + index + ']', this.status)
			await this.getCustomers(query)
			this.isSubmit = false
			this.message = 'Vô hiệu hóa thành công.'
			this.modalNotification = true
		},
		async enableClick (index) {
			const query = {
				query: {
					page: 1,
					limit: this.limit || 20
				}
			}
			this.isSubmit = true
			await Customer.updateStatus('[' + index + ']', this.enable)
			await this.getCustomers(query)
			this.isSubmit = false
			this.message = 'Hủy vô hiệu hóa  ' + '<b>' + 'TSS_' + index + '</b>' + ' thành công.'
			this.modalNotification = true
		},
		handleSearches (selectedKeys, confirm, dataIndex) {
			confirm()
			this.searchText = selectedKeys[0]
			this.searchedColumn = dataIndex
		},
		async getProfiles () {
			const profile = this.$store.getters.profile
			if (profile.data.user.roles[0].name === 'ADMIN') {
				this.activeStatus = true
			}
		},
		async handleDisable () {
			this.messageNotification = 'Bạn có chắc muốn vô hiệu hóa với ' + '<b>' + this.arr.length + '</b>' + ' đối tác đã chọn không?'
			this.disable = true
			this.active = false
			this.modalNotificationCustomer = true
		},
		async actionDisable () {
			const query = {
				query: {
					page: 1,
					limit: this.limit || 20
				}
			}
			// eslint-disable-next-line no-undef
			this.isSubmit = true
			if (this.disable) {
				await Customer.updateStatus(STATUS_CUSTOMER.INACTIVE, this.arr)
				this.message = 'Vô hiệu hóa với ' + '<b>' + this.arr.length + '</b>' + ' đối tác đã chọn thành công.'
			}
			if (this.active) {
				await Customer.updateStatus(STATUS_CUSTOMER.ACTIVE, this.arr)
				this.message = 'Kích hoạt lại với ' + '<b>' + this.arr.length + '</b>' + ' đối tác đã chọn thành công.'
			}
			await this.getCustomers(query)
			this.isSubmit = false
			this.modalNotification = true
		},
		async handleActive () {
			this.messageNotification = 'Bạn có chắc muốn kích hoạt lại với ' + '<b>' + this.arr.length + '</b>' + ' đối tác đã chọn không?'
			this.disable = false
			this.active = true
			this.modalNotificationCustomer = true
		},
		formatArea (value) {
			let num = (value / 1).toFixed(0).replace(',', '.')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
		},
		// dateFormat: function(date) {
		//   return moment(date, 'YYYY-MM-DD').format('DD/MM/YYYY');
		// },
		handleSearch () {
			this.showModalSearch = true
		},
		handleCancelSearch () {
			this.filter = {}
			this.onFilterChange()
			this.cancelSearch = false
		},
		handleShowImage (inputId) {
			let picList = []
			this.listCustomer.filter(item => {
				if (item.id === inputId) {
					let imageList = []
					if (item.pic) {
						item.pic.map((item) => {
							imageList.push(item)
						})
					}
					// eslint-disable-next-line no-unused-vars
					picList = imageList
				}
			})
			if (picList && picList.length > 0) {
				// eslint-disable-next-line no-return-assign
				picList.map((item) => {
					this.picList.images.push(item)
					this.picList.id.push(inputId)
				})
			}
		},
		onShowSelectedImages () {
			this.picList = {images: [], id: []}
			this.selectedRowKeys.map((item) => {
				this.handleShowImage(item.id)
			})
			this.showModalImage = true
		},
		// onShowImages (id, address) {
		//   this.picList = {images: [], address: [], id: []}
		//   this.handleShowImage(id, address)
		//   this.showModalImage = true
		// },
		async onFilterChange ($event) {
			const query = {
				query: {
					page: 1,
					limit: this.limit || 20
				}
			}

			this.filter = {...$event}

			await this.getCustomers(query)
		},
		async onPageChange (pagination, filters, sorter) {
			this.perPage = pagination.pageSize
			const sortDesc = replace(sorter.order, 'end', '')

			const query = {
				page: pagination.current,
				limit: pagination.pageSize,
				sort: sorter.field,
				order: sortDesc
			}
			await this.getCustomers(query)
		},
		async getCustomers (query = {}) {
			this.isLoading = true
			const filter = {}

			for (let property in this.filter) {
				filter[`${property}`] = this.filter[property]
			}

			try {
				const resp = await Customer.paginate({
					query: {
						page: 1,
						limit: 20,
						...query,
						...filter
					}
				})

				this.listCustomer = [...resp.data.data]
				this.totalRecord = resp.data.total
				this.pagination = convertPagination(resp.data)
				this.isLoading = false
			} catch (e) {
				this.isLoading = false
			}
		},
		async updateStatus (id) {
			try {
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},
		async handleDetail (id) {
			// let routeData = this.$router.resolve({name: 'routeName', query: {data: "someData"}});
			const routeData = this.$router.resolve({
				name: 'customer.detail',
				query: {
					id: id
				}
			})
			window.open(routeData.href, '_blank')
		}
	},
	beforeRouteEnter: async (to, from, next) => {
		// eslint-disable-next-line no-sequences,standard/computed-property-even-spacing
		const category = await Customer.find(to.query['search', 'id', 'name', 'status', 'tax_code', 'created_by', 'created_date', 'address'])
		to.meta['detail'] = category.data
		return next()
	},
	beforeMount () {
		this.getCustomers()
		this.getProfiles()
	}
}
</script>

<style scoped lang="scss">
.table-detail {
  margin-top: 47px;
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

.status {
	text-transform: none;
  color: #2d9000;
  margin-bottom: 0;

  &.red {
    color: red;
  }
  &.orange {
    color: #FAA831;
  }
}

.full-address {
  width: 250px;
  white-space: nowrap;
  -webkit-line-clamp: 2 !important;
  overflow: hidden;
  text-overflow: ellipsis;
  margin-bottom: 0;
  margin-left: auto;
  margin-right: auto;
  text-transform: none;

  &:first-letter {
    text-transform: none;
  }
}

.create_by {
  text-transform: none;

  &:first-letter {
    text-transform: none;
  }

  margin-bottom: 0;
}

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

.btn-search-dropdown {
  background: #FAA831;
  color: #000000;
  font-size: 12px;
  border: none !important;
  border-radius: 5px;

  &:hover {
    color: #000000;
  }
}

.filter-container {
  padding: 0;
}

.input-filter-container {
  box-shadow: 0px 1px 4px rgba(0, 0, 0, 0.25);
  border-radius: 5px;
}

.container-filter {
  margin-top: 5px;
  padding: 0;
  background: #FFFFFF;
  box-shadow: 0px 1px 4px rgba(0, 0, 0, 0.25);
  border-radius: 5px;
}

.filter-controls {
  padding: 15px 10px;
}

.author-item {
  display: flex;
  justify-content: space-between;
  padding: 10px 10px 10px 15px;
  align-content: center;
  margin: 0;

  input {
    height: 14px;
    width: 14px;
  }
}

.search-box {
  margin-bottom: 5px;
  @media (max-width: 1023px) {
    padding: 0;
    margin: 0;
  }
}

.index-screen-button {
  img{
    max-width: 12px;
    height: auto;
  }
  @media (max-width: 1440px) {
    margin-right: 0;
    width: 135px;
  }
  @media (max-width: 1024px) {
    width: 100%;
    margin-right: 0;
    margin-bottom: 10px;
  }
  @media (max-width: 767px) {
    margin-bottom: 0;
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
.btn-container{
  display: flex;
  margin-bottom: 5px;
  @media (max-width: 1440px) {
    margin-top: 10px;
    width: 100%;
  }
  @media (max-width: 1024px) {
    display: block;
  }
}
</style>
