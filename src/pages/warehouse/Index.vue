<template>
  <div v-if="!isMobile()" id="wareHouse" class="container-fluid position-relative">
    <div class="loading" :class="{'loading__true': isSubmit}">
      <a-spin />
    </div>
    <div class="pannel">
      <div class="pannel__content row mx-0 justify-content-between justify-content-lg-end align-items-center">
        <Search @filter-changed="onFilterChange($event)" class="search-box"/>
        <div class="justify-content-between align-items-center btn-container">
          <button class="btn btn-white text-nowrap index-screen-button  d-none d-lg-inline-flex" @click.prevent="handleSearch()">
            <img src="@/assets/icons/ic_search.svg" style="margin-right: 8px" alt="search">Tìm kiếm
          </button>
          <button class="btn btn-white text-nowrap index-screen-button ml-2" @click.prevent="handleCancelSearch()" v-if="cancelSearch">
            <img src="@/assets/icons/ic_search.svg" style="margin-right: 8px" alt="search">Hủy tìm kiếm
          </button>
          <!-- <router-link :to="{name: 'warehouse.create'}" class="btn btn-white text-nowrap index-screen-button" v-if="add"><img
            src="@/assets/icons/ic_add.svg" style="margin-right: 8px" alt="search">Tạo mới
          </router-link> -->
					<button class="btn mr-0 btn-white text-nowrap index-screen-button ml-2" @click="openSelectType" v-if="add">
						<img src="@/assets/icons/ic_add.svg" style="margin-right: 8px" alt="search">Tạo mới
					</button>
          <b-dropdown class="dropdown_btn d-none d-lg-inline-flex" no-caret>
            <template #button-content>
							<div class="container_image">
								<img src="@/assets/icons/ic_more.svg" alt="">
							</div>
            </template>
            <!-- <b-dropdown-item @click.prevent="export30daysBefore">Xuất dữ liệu 30 ngày trước</b-dropdown-item>
            <b-dropdown-item @click.prevent="exportMonthBefore">Xuất dữ liệu tháng trước</b-dropdown-item>
            <b-dropdown-item @click.prevent="exportQuarter">Xuất dữ liệu quý trước</b-dropdown-item> -->
            <b-dropdown-item @click.prevent="exportAdjust">Xuất dữ liệu tùy chỉnh</b-dropdown-item>
          </b-dropdown>
          <!-- <download-excel
						:data="selectedRowKeys"
						:fields="json_fields"
						:class="selectedRowKeys.length === 0 ? 'disabled' : ''"
						worksheet="document"
						name="Donava.xls"
						class="btn btn-white text-nowrap index-screen-button"
						v-if="exportFile"
          >
					<img src="@/assets/icons/ic_export.svg" style="margin-right: 8px" alt="search"> Xuất file excel
          </download-excel> -->
        </div>
      </div>
    </div>
    <div class="table-detail position-relative mt-3" :class="totalRecord === 0 ? 'empty-data' : ''">
      <a-table bordered
               :columns="columns"
               :data-source="listWarehouses"
               :loading="isLoading"
               class="table-property"
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
            <button @click.prevent="handleDetail(id, property)" class="link-detail">{{property.migrate_status}}_{{ id }}</button>
            <b-dropdown class="dropdown-container" no-caret>
              <template #button-content>
                <img src="@/assets/icons/ic_more.svg" alt="">
              </template>
              <b-dropdown-item @click="onShowImages(id, property.full_address)">
                <div class="dropdown-item-container"><img
                  src="@/assets/icons/ic_img.svg" alt="img">Xem hình ảnh
                </div>
              </b-dropdown-item>
              <b-dropdown-item @click.prevent="openPrint(id)">
                <div class="dropdown-item-container"><img src="@/assets/icons/ic_download.svg" alt="print">Tải xuống</div>
              </b-dropdown-item>
              <b-dropdown-item @click.prevent="openPrintPdf(id)">
                <div class="dropdown-item-container"><img src="@/assets/icons/ic_printer.svg" alt="print">In</div>
              </b-dropdown-item>
              <b-dropdown-item @click.prevent="disableClick(id)">
                <div class="dropdown-item-container"><img src="@/assets/icons/ic_lock.svg" alt="">Vô hiệu hóa</div>
              </b-dropdown-item>
              <b-dropdown-item @click.prevent="enableClick(id)">
                <div class="dropdown-item-container" v-if="activeStatus"><img src="@/assets/icons/ic_unlock.svg"
                                                                              alt="">Hủy vô hiệu hóa
                </div>
              </b-dropdown-item>
            </b-dropdown>
          </div>
        </template>
        <template slot="full_address" slot-scope="full_address, id">
          <p :id="id.id" class="full-address text-left">{{ full_address }}</p>
          <b-tooltip :target="(id.id).toString()">{{ full_address }}</b-tooltip>
        </template>
        <template slot="status" slot-scope="status">
          <p class="status" :class="status === 2 ? 'red' : status === 3 ? 'orange' : ''">
            {{ status === 1 ? 'Đang kích hoạt' : status === 2 ? 'Đang vô hiệu hóa' : 'Bản nháp' }}</p>
        </template>
        <template slot="area_total" slot-scope="area_total">
          <p class="total_rea mb-0">{{ formatNumber(area_total) }} m<sup>2</sup></p>
        </template>
        <template slot="total_construction_area" slot-scope="total_construction_area">
          <p class="total_construction_area mb-0">{{ formatNumber(total_construction_area) }} m<sup>2</sup></p>
        </template>
        <template slot="total_amount" slot-scope="total_amount">
          <p class="total_amount mb-0">{{ formatPrice(total_amount) }}</p>
        </template>
        <template slot="average_land_unit_price" slot-scope="average_land_unit_price">
          <p class="average_land_unit_price mb-0">{{ formatPrice(average_land_unit_price) }}</p>
        </template>
        <template slot="transaction_type" slot-scope="transaction_type">
          <p class="transaction_type mb-0">{{ transaction_type.description }}</p>
        </template>
        <template slot="created_by" slot-scope="created_by">
          <p class="create_by mb-0">{{ created_by }}</p>
        </template>
        <template slot="public_date" slot-scope="public_date">
          <p class="public_date mb-0">{{ formatDate(public_date) }}</p>
        </template>
		<template slot="updated_at" slot-scope="updated_at">
          <p class="updated_at mb-0">{{ formatDate(updated_at) }}</p>
        </template>
        <template slot="created_at" slot-scope="created_at">
          <p class="public_date mb-0">{{ formatDate(created_at) }}</p>
        </template>
      </a-table>
      <div class="total position-absolute" v-if="totalRecord > 0">
        Tổng cộng: {{ totalRecord }} tin đăng
      </div>
    </div>
    <ModalSearchAdvanced
      v-if="showModalSearch"
      @cancel="showModalSearch = false"
      @filter-changed="onFilterChange($event)"
      v-bind:roles="this.activeStatus"
      :filter_search="this.filter"
    />
    <ModalViewImage
      v-if="showModalImage"
      @cancel="showModalImage = false"
      v-bind:pics="this.picList"
    />
    <ModalPrint
      v-if="modalPrint"
      @cancel="modalPrint = false"
      v-bind:print_detail="this.printDetail"
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
    <ModalNotificationProperty
      v-if="modalNotificationProperty"
      @cancel="modalNotificationProperty = false"
      v-bind:notification="this.messageNotificationProperty"
      v-bind:id="this.indexProperty"
      @action="actionDisableProperty"
    />
		<ModalSelectTypeAsset
			v-if="open_select_type"
			@cancel="open_select_type = false"
		/>
		<ModalExportAdjust
			v-if="showAdjustModal"
			@cancel="showAdjustModal = false"
		/>
  </div>
  <div v-else id="wareHouse" class="container-fluid position-relative">
    <div class="loading" :class="{'loading__true': isSubmit}">
      <a-spin />
    </div>
    <div class="pannel">
      <div class="pannel__content row mx-0 justify-content-between justify-content-lg-end align-items-center">
        <div
					class="search-block col-6 col-md-6 col-xl-4 d-flex justify-content-end align-items-center" style="padding: 0;"
				>
		<Search @filter-changed="onFilterChange($event)" class="search-box"/>
		</div>
		<div
					class="col-4"
					style="padding: 0;
    margin-top: -10px;"
				>
					<button class="btn mr-0 btn-white text-nowrap index-screen-button ml-2" @click="openSelectType" v-if="add">
						<img src="@/assets/icons/ic_add.svg" style="margin-right: 8px" alt="search">Tạo mới
					</button>
					</div>
					<div
					class="col-1"
					style="padding: 0;"
				>
          <b-dropdown class="dropdown_btn d-lg-inline-flex" no-caret>
            <template #button-content>
							<div class="container_image">
								<img src="@/assets/icons/ic_more.svg" alt="">
							</div>
            </template>
            <!-- <b-dropdown-item @click.prevent="export30daysBefore">Xuất dữ liệu 30 ngày trước</b-dropdown-item>
            <b-dropdown-item @click.prevent="exportMonthBefore">Xuất dữ liệu tháng trước</b-dropdown-item>
            <b-dropdown-item @click.prevent="exportQuarter">Xuất dữ liệu quý trước</b-dropdown-item> -->
            <b-dropdown-item @click.prevent="exportAdjust">Xuất dữ liệu tùy chỉnh</b-dropdown-item>
          </b-dropdown>
		  </div>
      </div>
    </div>
    <div class="table-detail position-relative mt-3" :class="totalRecord === 0 ? 'empty-data' : ''" style="overflow: scroll;max-height: 76vh;">
		<b-card :class="{['border-' + configColor(element)]: true}" class="card_container mb-3" v-for="element in listWarehouses" :key="element.id+'_'+element.status">
            <div class="col-12 d-flex mb-2 justify-content-between">
              <span @click="handleDetail(element.id, element)" class="content_id" :class="`bg-${configColor(element)}-15 text-${configColor(element)}`">TSS_{{element.id}}</span>
            </div>
			<div class="property-content mb-2 d-flex color_content">
              <div class="label_container d-flex">
                <div class="d-flex">
                <span style="font-weight: 500"><strong class="d_inline mr-1">Địa chỉ:</strong><span :id="element.id + 'all'" class="text-left">{{ element.full_address.substring(30,0)+'...'}}</span></span>
				<b-tooltip :target="(element.id + 'all').toString()">{{ element.full_address }}</b-tooltip>
                </div>
              </div>
            </div>
			<div class="property-content mb-2 d-flex color_content" >
				<div class="label_container d-flex">
					<div class="d-flex">
					<span style="font-weight: 500"><strong class="d_inline mr-1">Loại tài sản:</strong><span class="text-capitalize">{{element.asset_type.description.toLowerCase()}}</span></span>
					</div>
				</div>
			</div>
			<div class="property-content mb-2 d-flex color_content" >
				<div class="label_container d-flex">
					<div class="d-flex">
					<span style="font-weight: 500"><strong class="d_inline mr-1">Loại giao dịch:</strong><span class="text-capitalize">{{element.transaction_type.description.toLowerCase()}}</span></span>
					</div>
				</div>
			</div>
			<div class="property-content mb-2 d-flex color_content">
				<div class="label_container d-flex">
					<div class="d-flex">
					<span style="font-weight: 500"><strong class="d_inline mr-1">Tổng diện tích:</strong><span class="text-none">{{ element.total_area ? formatNumber(element.total_area) : 0 }} m<sup>2</sup></span></span>
					</div>
				</div>
			</div>
			<div class="property-content mb-2 d-flex color_content">
				<div class="label_container d-flex">
					<div class="d-flex">
					<span style="font-weight: 500"><strong class="d_inline mr-1">Đơn giá bình quân(VNĐ):</strong><span class="text-none">{{element.average_land_unit_price ? formatPrice(element.average_land_unit_price) : '-' }}</span></span>
					</div>
				</div>
			</div>
			<div class="property-content mb-2 d-flex color_content">
				<div class="label_container d-flex">
					<div class="d-flex">
					<span style="font-weight: 500"><strong class="d_inline mr-1">Tổng giá trị(VNĐ):</strong><span class="text-none">{{element.total_amount ? formatPrice(element.total_amount) : '-' }}</span></span>
					</div>
				</div>
			</div>
			<div class="property-content mb-2 d-flex color_content">
				<div class="label_container d-flex">
					<div class="d-flex">
					<span style="font-weight: 500"><strong class="d_inline mr-1">Thời điểm đăng:</strong><span class="public_date">{{ formatDate(element.public_date) }}</span></span>
					</div>
				</div>
			</div>
			<div class="property-content mb-2 d-flex color_content">
				<div class="label_container d-flex">
					<div class="d-flex">
					<span style="font-weight: 500"><strong class="d_inline mr-1">Ngày cập nhật:</strong><span class="public_date">{{ formatDate(element.updated_at) }}</span></span>
					</div>
				</div>
			</div>
			<div class="property-content mb-2 d-flex color_content">
				<div class="label_container d-flex">
					<div class="d-flex">
					<span style="font-weight: 500"><strong class="d_inline mr-1">Người tạo:</strong><span class="text-capitalize">{{element.created_by.name}}</span></span>
					</div>
				</div>
			</div>
          </b-card>
    </div>
	<div class="pagination-wrapper" style="margin-bottom: 20px;">
			<div class="page-size">
			Hiển thị
			<a-select ref="select" :value="Number(pagination.pageSize)" style="width: 71px" :options="pageSizeOptions"
				@change="onSizeChange" />
			hàng
			</div>
			<a-pagination :current="Number(pagination.current)" :page-size="Number(pagination.pageSize)"
			:total="Number(pagination.total)"
			:show-total="(total, range) => `Kết quả hiển thị ${range[0]} - ${range[1]} của ${pagination.total} tài sản`"
			@change="onPaginationChange">
			</a-pagination>
      	</div>
    <ModalSearchAdvanced
      v-if="showModalSearch"
      @cancel="showModalSearch = false"
      @filter-changed="onFilterChange($event)"
      v-bind:roles="this.activeStatus"
      :filter_search="this.filter"
    />
    <ModalViewImage
      v-if="showModalImage"
      @cancel="showModalImage = false"
      v-bind:pics="this.picList"
    />
    <ModalPrint
      v-if="modalPrint"
      @cancel="modalPrint = false"
      v-bind:print_detail="this.printDetail"
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
    <ModalNotificationProperty
      v-if="modalNotificationProperty"
      @cancel="modalNotificationProperty = false"
      v-bind:notification="this.messageNotificationProperty"
      v-bind:id="this.indexProperty"
      @action="actionDisableProperty"
    />
		<ModalSelectTypeAsset
			v-if="open_select_type"
			@cancel="open_select_type = false"
		/>
		<ModalExportAdjust
			v-if="showAdjustModal"
			@cancel="showAdjustModal = false"
		/>
  </div>
</template>
<script>
import Search from '@/pages/warehouse/Search'
import {replace} from 'lodash-es'
import WareHouse from '@/models/WareHouse'
import {convertPagination} from '@/utils/filters'
import ModalSearchAdvanced from '@/components/Modal/ModalSearchAdvanced'
import ModalViewImage from '@/components/Modal/ModalViewImage'
import moment from 'moment'
import JsonExcel from 'vue-json-excel'
import Vue from 'vue'
import {STATUS} from '@/enum/status.enum'
import ModalPrint from '@/components/Modal/ModalPrint'
import {BDropdown, BDropdownItem, BTooltip,
	BCard,
	BRow,
	BCol,
	BFormGroup,
	BFormInput } from 'bootstrap-vue'
import ModalExportAdjust from './modals/ModalExportAdjust'
import ModalNotification from '@/components/Modal/ModalNotification'
import ModalNotificationCustomer from '@/components/Modal/ModalNotificationCustomer'
import ModalNotificationProperty from '@/components/Modal/ModalNotificationProperty'
import ModalSelectTypeAsset from './modals/ModalSelectTypeAsset'
import {LTooltip} from 'vue2-leaflet'
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
		'b-dropdown': BDropdown,
		'b-dropdown-item': BDropdownItem,
		'b-tooltip': BTooltip,
		ModalNotificationCustomer,
		ModalNotificationProperty,
		ModalSelectTypeAsset,
		ModalExportAdjust,
		LTooltip,
		BCard,
		BRow,
		BCol,
		BFormGroup,
		BFormInput
	},
	data () {
		return {
			pageSizeOptions: [
				{ value: '10', label: '10' },
				{ value: '20', label: '20' },
				{ value: '30', label: '30' }
			],
			modalNotificationProperty: false,
			modalNotificationCustomer: false,
			open_select_type: false,
			messageNotificationProperty: '',
			messageNotification: '',
			indexProperty: '',
			printDetail: '',
			disable: false,
			active: false,
			cancelSearch: false,
			modalNotification: false,
			isSubmit: false,
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
			showAdjustModal: false,
			selectedRowKeys: [],
			isLoading: false,
			provinces: [],
			listWarehouses: [],
			filter: {},
			pagination: {},
			perPage: '',
			openModal: false,
			form: {
				createdBy: [],
				fromDate: '',
				toDate: ''
			},
			picList: {
				images: [],
				address: [],
				id: []
			},
			totalRecord: 0,
			add: false,
			edit: false,
			delete: false,
			exportFile: false
		}
	},
	created () {
		const permission = this.$store.getters.currentPermissions
		permission.forEach(value => {
			if (value === 'ADD_PRICE') {
				this.add = true
			}
			if (value === 'EDIT_PRICE') {
				this.edit = true
			}
			if (value === 'DELETE_PRICE') {
				this.deleted = true
			}
			if (value === 'EXPORT_PRICE') {
				this.exportFile = true
			}
		})
	},
	computed: {
		columns () {
			return [
				{
					title: 'Mã tin đăng',
					align: 'center',
					scopedSlots: {customRender: 'id'},
					dataIndex: 'id',
					sorter: true,
					sortDirections: ['descend', 'ascend']
				},
				{
					title: 'Loại tài sản',
					align: 'center',
					dataIndex: 'asset_type.description',
					// scopedSlots: {customRender: 'transaction_type'},
					sorter: true,
					sortDirections: ['descend', 'ascend']
				},
				{
					title: 'Địa chỉ đầy đủ',
					class: 'optional-data',
					align: 'center',
					scopedSlots: {customRender: 'full_address'},
					dataIndex: 'full_address'
				},
				{
					title: 'Tổng diện tích',
					align: 'center',
					scopedSlots: {customRender: 'area_total'},
					dataIndex: 'area_total',
					sorter: false,
					sortDirections: ['descend', 'ascend']
				},
				{
					title: 'Đơn giá bình quân',
					class: 'optional-data',
					align: 'center',
					scopedSlots: {customRender: 'average_land_unit_price'},
					dataIndex: 'average_land_unit_price',
					sorter: true,
					sortDirections: ['descend', 'ascend']
				},
				{
					title: 'Tổng giá trị',
					align: 'center',
					scopedSlots: {customRender: 'total_amount'},
					dataIndex: 'total_amount',
					sorter: true,
					sortDirections: ['descend', 'ascend']
				},
				{
					title: 'Thời điểm đăng',
					align: 'center',
					dataIndex: 'public_date',
					scopedSlots: {customRender: 'public_date'},
					sorter: true,
					sortDirections: ['descend', 'ascend']
				},
				{
					title: 'Ngày cập nhật',
					align: 'center',
					dataIndex: 'updated_at',
					scopedSlots: {customRender: 'updated_at'},
					sorter: true,
					sortDirections: ['descend', 'ascend']
				},
				{
					title: 'Ngày tạo',
					class: 'optional-data',
					align: 'center',
					dataIndex: 'created_at',
					scopedSlots: {customRender: 'created_at'},
					sorter: true,
					sortDirections: ['descend', 'ascend']
				},
				{
					title: 'Người tạo',
					align: 'center',
					dataIndex: 'created_by.name',
					// scopedSlots: {customRender: 'created_by'},
					scopedSlots: {
						// filterDropdown: 'filterDropdown',
						// filterIcon: 'filterIcon',
						customRender: 'created_by'
					},
					onFilter: (value, record) =>
						record.created_by
							.toString()
							.toLowerCase()
							.includes(value.toLowerCase()),
					onFilterDropdownVisibleChange: visible => {
						if (visible) {
							// eslint-disable-next-line vue/no-async-in-computed-properties
							setTimeout(() => {
								this.searchInput.focus()
							}, 0)
						}
					},
					width: '100px'
				},
				{
					title: 'Loại giao dịch',
					class: 'optional-data',
					align: 'center',
					dataIndex: 'transaction_type.description',
					// scopedSlots: {customRender: 'transaction_type'},
					sorter: true,
					sortDirections: ['descend', 'ascend']
				}
				// {
				// 	title: 'Trạng thái',
				// 	class: 'optional-data',
				// 	align: 'center',
				// 	scopedSlots: {customRender: 'status'},
				// 	dataIndex: 'status',
				// 	sorter: true,
				// 	sortDirections: ['descend', 'ascend']
				// }
			]
		},
		rowSelection () {
			// eslint-disable-next-line no-unused-vars
			const {selectedRowKeys} = this
			return {
				onChange: (selectedRowKeys, selectedRows) => {
					// eslint-disable-next-line vue/no-side-effects-in-computed-properties
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
		onSizeChange (pageSize) {
			const pagination = { ...this.pagination, pageSize: Number(pageSize) }
			this.onPageChange(pagination)
		},
		onPaginationChange (current) {
			const pagination = { ...this.pagination, current: Number(current) }
			this.onPageChange(pagination)
		},
		configColor (element) {
			if (element.status == 1) {
				return 'info'
			}
			if (element.status == 2) {
				return 'primary'
			}
			if (element.status == 3) {
				return 'warning'
			}
			if (element.status == 4) {
				return 'success'
			}
			if (element.status == 5) {
				return 'secondary'
			}
			if (element.status == 6) {
				return 'control'
			}
			return 'red'
		},
		isMobile () {
			if (
				/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
					navigator.userAgent
				)
			) {
				return true
			} else {
				return false
			}
		},
		openSelectType () {
			this.open_select_type = true
		},
		onSelectChange (selectedRowKeys, selectedRows) {
			this.selectedRowKeys = [...selectedRows]
			// eslint-disable-next-line vue/no-side-effects-in-computed-properties
			this.arr = selectedRowKeys
			const rows = this.selectedRowKeys
			const column = []
			rows.forEach(row => {
				column.push(
					row.id
				)
				return column
			})
		},
		async actionDisable () {
			// eslint-disable-next-line no-undef
			this.isSubmit = true
			if (this.active) {
				await WareHouse.updateStatus('[' + this.arr + ']', this.enable)
			} else if (this.disable) {
				await WareHouse.updateStatus('[' + this.arr + ']', this.status)
			}
			await this.getWarehouses()
			this.isSubmit = false
			if (this.active) {
				this.message = 'Kích hoạt lại với ' + '<b>' + this.arr.length + '</b>' + ' TSSS đã chọn thành công.'
			} else if (this.disable) {
				this.message = 'Vô hiệu hóa với ' + '<b>' + this.arr.length + '</b>' + ' TSSS đã chọn thành công.'
			}
			this.selectedRowKeys = []
			this.arr = []
			this.modalNotification = true
		},
		formatDate (value) {
			return moment(String(value)).format('DD/MM/YYYY')
		},
		async openPrint (id) {
			this.isSubmit = true
			await WareHouse.getPrint(id).then((resp) => {
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
			const resp = await WareHouse.getPrintPdf(id)
			this.printDetail = resp.data.url
		},
		async disableClick (index) {
			this.indexProperty = index
			this.messageNotificationProperty = 'Bạn có chắc muốn vô hiệu hóa  ' + '<b>' + 'TSS_' + index + '</b>' + ' không?'
			this.modalNotificationProperty = true
			this.disable = true
			this.active = false
		},
		async enableClick (index) {
			this.indexProperty = index
			this.messageNotificationProperty = 'Bạn có chắc muốn hủy vô hiệu hóa  ' + '<b>' + 'TSS_' + index + '</b>' + ' không?'
			this.modalNotificationProperty = true
			this.disable = false
			this.active = true
		},
		async actionDisableProperty () {
			this.isSubmit = true
			if (this.active) {
				await WareHouse.updateStatus('[' + this.indexProperty + ']', this.enable)
			} else if (this.disable) {
				await WareHouse.updateStatus('[' + this.indexProperty + ']', this.status)
			}
			await this.getWarehouses()
			this.isSubmit = false
			if (this.active) {
				this.message = 'Hủy vô hiệu hóa ' + '<b>' + 'TSS_' + this.indexProperty + '</b>' + ' thành công.'
			} else if (this.disable) {
				this.message = 'Vô hiệu hóa ' + '<b>' + 'TSS_' + this.indexProperty + '</b>' + ' thành công.'
			}
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
			this.disable = true
			this.active = false
			this.messageNotification = 'Bạn có chắc muốn vô hiệu hóa với ' + '<b>' + this.arr.length + '</b>' + ' TSSS đã chọn không?'
			this.modalNotificationCustomer = true
		},
		async handleActive () {
			this.disable = false
			this.active = true
			this.messageNotification = 'Bạn có chắc muốn kích hoạt lại với ' + '<b>' + this.arr.length + '</b>' + ' TSSS đã chọn không?'
			this.modalNotificationCustomer = true
		},
		async handlePrints () {
			this.isSubmit = true
			if (this.arr.length > 3) {
				this.$toast.open({
					message: 'Chỉ được in tối đa 3 phiếu/lần ',
					type: 'error',
					position: 'top-right',
					duration: 3000
				})
				this.isSubmit = false
			} else {
				await WareHouse.getPrint(this.arr).then((resp) => {
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
				})
			}
		},
		formatPrice (value) {
			let num = parseFloat(value / 1).toFixed(0).replace('.', ',')
			if (num.length > 3 && num.length <= 6) {
				return parseFloat(num / 1000).toFixed(1).replace('.', ',') + ' Nghìn'
			} else if (num.length > 6 && num.length <= 9) {
				return parseFloat(num / 1000000).toFixed(1).replace('.', ',') + ' Triệu'
			} else if (num.length > 9) {
				return parseFloat(num / 1000000000).toFixed(1).replace('.', ',') + ' Tỷ'
			} else if (num < 900) {
				return num + ' đ' // if value < 1000, nothing to do
			}
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.')
		},
		formatNumber (value) {
			if (value) {
				let formatedNum = parseFloat(value).toString().replace('.', ',')
				return formatedNum.toString().replace(/^[+-]?\d+/, function (int) {
					return int.replace(/(\d)(?=(\d{3})+$)/g, '$1.')
				})
			}
		},
		handleSearch () {
			this.showModalSearch = true
		},
		handleCancelSearch () {
			this.filter = {}
			this.onFilterChange()
			this.cancelSearch = false
		},
		handleShowImage (inputId, address) {
			let picList = []
			this.listWarehouses.filter(item => {
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
					if (address) {
						this.picList.address.push(address)
					}
					this.picList.id.push(inputId)
				})
			}
		},
		onShowSelectedImages () {
			this.picList = {images: [], address: [], id: []}
			this.selectedRowKeys.map((item) => {
				this.handleShowImage(item.id, item.full_address)
			})
			this.showModalImage = true
		},
		onShowImages (id, address) {
			this.picList = {images: [], address: [], id: []}
			this.handleShowImage(id, address)
			this.showModalImage = true
		},
		async onFilterChange ($event) {
			const query = {
				query: {
					page: 1,
					limit: this.limit || 20
				}
			}

			this.filter = {...$event}

			await this.getWarehouses(query)
		},
		async onPageChange (pagination, filter = {}, sorter = {}) {
			this.perPage = pagination.pageSize
			const sortDesc = replace(sorter.order, 'end', '')

			const query = {
				page: pagination.current,
				limit: pagination.pageSize,
				sort: sorter.field,
				order: sortDesc
			}
			await this.getWarehouses(query)
		},
		async getWarehouses (query = {}) {
			this.isLoading = true
			const filter = {}

			for (let property in this.filter) {
				filter[`${property}`] = this.filter[property]
			}

			try {
				const resp = await WareHouse.paginate({
					query: {
						page: 1,
						limit: 20,
						...query,
						...filter
					}
				})
				this.listWarehouses = [...resp.data.data]
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
		async export30daysBefore () {
			this.form.fromDate = await moment(new Date(new Date().setDate(new Date().getDate() - 30))).format('DD/MM/YYYY')
			this.form.toDate = await moment(new Date()).format('DD/MM/YYYY')
			this.exportData()
		},
		async exportMonthBefore () {
			let date = new Date()
			let datePrevious = new Date(date.setDate(0))
			let from_date = new Date(new Date(datePrevious).setDate(1))
			let to_date = new Date(datePrevious)
			this.form.fromDate = await moment(from_date).format('DD/MM/YYYY')
			this.form.toDate = await moment(to_date).format('DD/MM/YYYY')
			this.exportData()
		},
		async exportQuarter () {
			let quarterAdjustment = (moment().month() % 3) + 1
			let lastQuarterEndDate = moment().subtract({ months: quarterAdjustment }).endOf('month')
			let lastQuarterStartDate = lastQuarterEndDate.clone().subtract({ months: 2 }).startOf('month')
			this.form.fromDate = await moment(lastQuarterStartDate).format('DD/MM/YYYY')
			this.form.toDate = await moment(lastQuarterEndDate).format('DD/MM/YYYY')
			this.exportData()
		},
		async exportData () {
			const res = await WareHouse.exportDataComparisionAsset(this.form)
			if (res.data) {
				const fileLink = document.createElement('a')
				fileLink.href = res.data.url
				fileLink.setAttribute('download', res.data.file_name)
				document.body.appendChild(fileLink)
				fileLink.click()
				fileLink.remove()
				window.URL.revokeObjectURL(fileLink)
				this.$toast.open({
					message: 'Xuất dữ liệu thành công',
					type: 'success',
					duration: 3000,
					position: 'top-right'
				})
			} else if (res.error) {
				this.$toast.open({
					message: res.error.message,
					type: 'error',
					duration: 3000,
					position: 'top-right'
				})
			}
		},
		exportAdjust () {
			this.showAdjustModal = true
		},
		async handleDetail (id, property) {
			// let routeData = this.$router.resolve({name: 'routeName', query: {data: "someData"}});
			if (this.isMobile()) {
				this.$router.push({
					name: 'warehouse.detail',
					query: { id: id }
				}).catch(_ => {})
			} else {
				const routeData = this.$router.resolve({
					name: 'warehouse.detail',
					query: {
						id: id,
						version: property.version
					}
				})
				window.open(routeData.href, '_blank')
			}
		}
	},
	beforeRouteEnter: async (to, from, next) => {
		// eslint-disable-next-line no-sequences,standard/computed-property-even-spacing
		const category = await WareHouse.find(to.query['search', 'asset_type_id', 'created_at', 'created_by', 'coordinates', 'source_id', 'transaction_type_id', 'id', 'province_id', 'district_id', 'ward_id', 'street_id', 'land_no', 'doc_no', 'contact_person', 'contact_phone', 'contact_phone', 'public_date_from', 'public_date_to', 'updated_at'])
		to.meta['detail'] = category.data
		return next()
	},
	beforeMount () {
		this.getWarehouses()
		this.getProfiles()
	}
}
</script>

<style scoped lang="scss">

 /deep/
.optional-data {
	@media (max-width: 1024px) {
		display: none;
	}
}

.link-detail {
  white-space: nowrap;
  color: #FAA831;
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
		margin-left: 0 !important;
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
  // @media (max-width: 1440px) {
  //   margin-top: 10px;
  //   width: 100%;
	// 	display: block;
  // }
  @media (max-width: 1024px) {
    display: block;
		width: 100%;
		margin-top: 10px;
  }
}
.dropdown_btn {
	@media (max-width: 1024px) {
		margin-top: 10px;
    width: 100%;
		margin-left: unset
	}
}
.tooltip {
  position: relative;
  display: inline-block;
  border-bottom: 1px dotted black;
}

.tooltip .tooltiptext {
  visibility: hidden;
  width: 120px;
  background-color: black;
  color: #fff;
  text-align: center;
  border-radius: 6px;
  padding: 5px 0;

  /* Position the tooltip */
  position: absolute;
  z-index: 1;
}

.tooltip:hover .tooltiptext {
  visibility: visible;
}
.container_image {
	width: 35px;
	height: 30px;
}
.pagination-wrapper {
    margin-top: 18px;
    display: flex;
    justify-content: space-between;
    align-items: center;

    .ant-select {
      margin-left: 11px;
      margin-right: 11px;
    }

    .page-size {
      display: flex;
      align-items: center;
      margin-right: 20px;
			@media (max-width: 1024px) {
				display: none;
			}
    }

    .ant-pagination {
      flex-grow: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-wrap: wrap;
      row-gap: 10px;

      /deep/ .ant-pagination-total-text {
        height: unset;
        flex-grow: 1;
        @media (max-width: 1024px) {
					display: none;
        }
      }

      /deep/ .ant-pagination-item-active {
        background: #007EC6;

        a {
          color: #FFFFFF;
        }
      }

      /deep/ .ant-pagination-prev,
      /deep/ .ant-pagination-next {
        border: 1px solid #d9d9d9;

        &:hover {
          border-color: #1890ff;
          transition: all 0.3s;
        }

        a:hover {
          i {
            color: #1890ff;
          }
        }
      }
    }

    @media (max-width: 1024px) {
      flex-direction: column;
      gap: 20px;
    }
  }
</style>
