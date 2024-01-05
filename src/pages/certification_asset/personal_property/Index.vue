<template>
  <div v-if="!isMobile()" class="main-wrapper-new">
    <div class="container-button appraise-container">
      <div class="button__detail row mx-0 justify-content-between justify-content-lg-end align-items-center">
        <div class="col-12 col-md-6 col-xl-8">
          <button-checkbox :options="statusOptions" :value="selectedStatus" @change="onChangeStatus" />
        </div>
        <div class="search-block col-12 col-md-6 col-xl-4 d-flex justify-content-end align-items-center">
          <Search @filter-changed="onFilterQuickSearchChange($event)" />
          <!-- <router-link v-if="add" :to="{ name: 'certification_asset.create' }" class="btn text-nowrap index-screen-button ml-md-2">
            <img src="@/assets/icons/ic_new.svg" style="margin-right: 8px" alt="search">Tạo mới
          </router-link> -->
					<button @click="openChooseTypeCreate" class="btn text-nowrap index-screen-button ml-md-2">
						<img src="@/assets/icons/ic_new.svg" style="margin-right: 8px" alt="search">Tạo mới
					</button>
          <b-dropdown class="dropdown-container" no-caret v-if="this.export">
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
    <div class="container-fluid appraise-container mt-3">
      <TableAll
        :listCertificates="listAppraiseAll"
        :isLoading="isLoading"
        :pagination="paginationAll"
        @handleChange="onPageChange"
      />
    </div>
    <div>
        <ModalExportAdjust
        v-if="showAdjustModal"
        @cancel="showAdjustModal = false"
        :statusOptions="statusOptions"
        />
				<ModalSelectTypeProperty
					v-if="open_select_type"
					@cancel="open_select_type = false"
				/>
    </div>
  </div>
  <div v-else class="main-wrapper-new" style="margin: 0;padding-top:0;">
    <div class="container-button appraise-container">
      <div class="button__detail row mx-0 justify-content-between justify-content-lg-end align-items-center">
        <div class="col-12 col-md-6 col-xl-8">
          <button-checkbox :options="statusOptions" :value="selectedStatus" @change="onChangeStatus" />
        </div>
        <div class="search-block col-11 col-md-6 col-xl-4 d-flex justify-content-end align-items-center">
          <Search @filter-changed="onFilterQuickSearchChange($event)" />
		  </div>
          <!-- <router-link v-if="add" :to="{ name: 'certification_asset.create' }" class="btn text-nowrap index-screen-button ml-md-2">
            <img src="@/assets/icons/ic_new.svg" style="margin-right: 8px" alt="search">Tạo mới
          </router-link> -->
		  <!-- <div
					class="col-4"
					style="padding: 0;
    margin-top: 10px;"
				>
					<button @click="openChooseTypeCreate" class="btn text-nowrap index-screen-button ml-md-2">
						<img src="@/assets/icons/ic_new.svg" style="margin-right: 8px" alt="search">Tạo mới
					</button>
					</div> -->
					<div
					class="col-1"
					style="padding: 0;
    margin-top: 15px;"
				>
          <b-dropdown class="dropdown-container" no-caret v-if="this.export">
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
    <div class="container-fluid appraise-container mt-3">
      <TableAll
        :listCertificates="listAppraiseAll"
        :isLoading="isLoading"
        :pagination="paginationAll"
        @handleChange="onPageChange"
      />
    </div>
    <div>
        <ModalExportAdjust
        v-if="showAdjustModal"
        @cancel="showAdjustModal = false"
        :statusOptions="statusOptions"
        />
				<ModalSelectTypeProperty
					v-if="open_select_type"
					@cancel="open_select_type = false"
				/>
    </div>
  </div>
</template>

<script>
import { PERMISSIONS } from '@/enum/permissions.enum'
import CertificatePersonalProperty from '@/models/CertificatePersonalProperty'
import ModalSelectTypeProperty from './modals/ModalSelectTypeProperty'
import ButtonCheckbox from '@/components/Form/ButtonCheckbox'
import Search from '../personal_property/Search.vue'
import TableAll from './component/TableAll'
import ModalExportAdjust from './modals/ModalExportAdjust'
import { Tabs, TabItem } from 'vue-material-tabs'
import { convertPagination } from '@/utils/filters'
import CertificateAsset from '@/models/CertificateAsset'
import WareHouse from '@/models/WareHouse'
import Certificate from '@/models/Certificate'
import { BDropdown, BDropdownItem, BTooltip } from 'bootstrap-vue'
import moment from 'moment'
import store from '@/store'
import * as types from '@/store/mutation-types'
export default {
	name: 'Index',
	data () {
		return {
			theme: {
				navItem: '#000000',
				navActiveItem: '#FAA831',
				slider: '#FAA831',
				arrow: '#000000'
			},
			listAppraiseDraft: [],
			listAppraiseOpen: [],
			listAppraiseLock: [],
			listAppraiseClose: [],
			listAppraiseAll: [],
			list: [],
			paginationAll: {},
			paginationDraft: {},
			paginationOpen: {},
			paginationLock: {},
			paginationClose: {},
			filter: {},
			status: 0,
			activeStatus: false,
			showModalSearch: false,
			showAdjustModal: false,
			open_select_type: false,
			view: false,
			add: false,
			edit: false,
			deleted: false,
			accept: false,
			export: false,
			selectedStatus: [],
			statusOptions: {
				data: [
					// { label: 'Tiếp nhận hồ sơ', value: '1', class: 'bg-info' },
					// { label: 'Thẩm định', value: '2', class: 'bg-primary' },
					// { label: 'Kiểm soát', value: '6', class: 'bg-control' },
					// { label: 'Duyệt giá', value: '3', class: 'bg-warning' },
					// { label: 'Duyệt phát hành', value: '7', class: 'bg-warning' },
					// { label: 'In hồ sơ', value: '8', class: 'bg-warning' },
					// { label: 'Bàn giao khách hàng', value: '9', class: 'bg-warning' },
					// { label: 'Hoàn thành', value: '4', class: 'bg-success' },
					// { label: 'Hủy', value: '5', class: 'bg-secondary' }
				],
				value: 'value',
				label: 'label'
			},
			form: {
				createdBy: [],
				fromDate: '',
				toDate: '',
				status: []
			}
		}
	},
	components: {
		TabItem,
		Tabs,
		Search,
		TableAll,
		ButtonCheckbox,
		'b-dropdown': BDropdown,
		'b-dropdown-item': BDropdownItem,
		'b-tooltip': BTooltip,
		ModalExportAdjust,
		ModalSelectTypeProperty
	},
	created () {
		// fix_permission
		const permission = this.$store.getters.currentPermissions
		permission.forEach((value) => {
			if (value === PERMISSIONS.VIEW_CERTIFICATE_ASSET) {
				this.view = true
			}
			if (value === PERMISSIONS.ADD_CERTIFICATE_ASSET) {
				this.add = true
			}
			if (value === PERMISSIONS.EDIT_CERTIFICATE_ASSET) {
				this.edit = true
			}
			if (value === PERMISSIONS.DELETE_CERTIFICATE_ASSET) {
				this.deleted = true
			}
			if (value === PERMISSIONS.ACCEPT_CERTIFICATE_ASSET) {
				this.accept = true
			}
			if (value === PERMISSIONS.EXPORT_CERTIFICATE_ASSET) {
				this.export = true
			}
		})
	},
	methods: {
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
		openChooseTypeCreate () {
			this.open_select_type = true
		},
		handleChangeTab (event) {
			if (event === 0) {
				this.status = 1
			} else if (event === 1) {
				this.status = 2
			} else if (event === 2) {
				this.status = 3
			} else if (event === 3) {
				this.status = 4
			} else this.status = 0
		},
		async getProfiles () {
			const profile = this.$store.getters.profile
			if (profile && profile.data.user.roles[0].name.slice(-5) === 'ADMIN') {
				this.activeStatus = true
			}
		},
		async onFilterQuickSearchChange ($event) {
			this.filter = { ...$event }
			await this.getDataAll()
		},
		// async onFilterChange ($event) {
		//   const filter = { ...$event }
		//   await this.getDataAll(filter)
		// },
		handleSearch () {
			this.showModalSearch = true
		},
		async getDataDraft (params = {}) {
			this.isLoading = true
			const filter = {}
			try {
				const resp = await CertificatePersonalProperty.paginate({ query: { page: 1, limit: 20, ...params, ...filter, status: 1 } })
				this.listAppraiseDraft = [...resp.data.data]
				this.paginationDraft = convertPagination(resp.data)
				this.isLoading = false
			} catch (e) {
				this.isLoading = false
			}
		},
		async getDataOpen (params = {}) {
			this.isLoading = true
			const filter = {}
			try {
				const resp = await CertificatePersonalProperty.paginate({ query: { page: 1, limit: 20, ...params, ...filter, status: 2 } })
				this.listAppraiseOpen = [...resp.data.data]
				this.paginationOpen = convertPagination(resp.data)
				this.isLoading = false
			} catch (e) {
				this.isLoading = false
			}
		},
		async getDataLock (params = {}) {
			this.isLoading = true
			const filter = {}
			try {
				const resp = await CertificatePersonalProperty.paginate({ query: { page: 1, limit: 20, ...params, ...filter, status: 3 } })
				this.listAppraiseLock = [...resp.data.data]
				this.paginationLock = convertPagination(resp.data)
				this.isLoading = false
			} catch (e) {
				this.isLoading = false
			}
		},
		async getDataClose (params = {}) {
			this.isLoading = true
			const filter = {}
			try {
				const resp = await CertificatePersonalProperty.paginate({ query: { page: 1, limit: 20, ...params, ...filter, status: 4 } })
				this.listAppraiseClose = [...resp.data.data]
				this.paginationClose = convertPagination(resp.data)
				this.isLoading = false
			} catch (e) {
				this.isLoading = false
			}
		},
		async getDataAll (params = {}) {
			this.isLoading = true
			try {
				const resp = await CertificatePersonalProperty.paginate({ query: { page: 1, limit: 20, ...params, ...this.filter, status: this.selectedStatus } })
				this.listAppraiseAll = [...resp.data.data]
				this.paginationAll = convertPagination(resp.data)
				this.isLoading = false
			} catch (e) {
				this.isLoading = false
			}
		},
		async onPageChange (pagination, type, filter = {}, sorter = {}) {
			this.perPage = pagination.pageSize
			const params = {
				page: pagination.current,
				limit: pagination.pageSize,
				filters: filter,
				sortField: sorter.field,
				sortOrder: sorter.order
			}

			switch (type) {
			case 'Draft':
				await this.getDataDraft(params)
				break
			case 'Lock':
				await this.getDataLock(params)
				break
			case 'Close':
				await this.getDataClose(params)
				break
			case 'Open':
				await this.getDataOpen(params)
				break
			default:
				await this.getDataAll(params)
			}
		},
		onChangeStatus (value) {
			this.selectedStatus = value
			this.getDataAll()
		},
		async export30daysBefore () {
			this.form.fromDate = await moment(new Date(new Date().setDate(new Date().getDate() - 30))).format('DD/MM/YYYY')
			this.form.toDate = await moment(new Date()).format('DD/MM/YYYY')
			const res = await CertificateAsset.exportDataCertificationAsset(this.form)
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
		async exportMonthBefore () {
			let date = new Date()
			let datePrevious = new Date(date.setDate(0))
			let from_date = new Date(new Date(datePrevious).setDate(1))
			let to_date = new Date(datePrevious)
			this.form.fromDate = await moment(from_date).format('DD/MM/YYYY')
			this.form.toDate = await moment(to_date).format('DD/MM/YYYY')
			const res = await CertificateAsset.exportDataCertificationAsset(this.form)
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
		async exportQuarter () {
			let quarterAdjustment = (moment().month() % 3) + 1
			let lastQuarterEndDate = moment().subtract({ months: quarterAdjustment }).endOf('month')
			let lastQuarterStartDate = lastQuarterEndDate.clone().subtract({ months: 2 }).startOf('month')
			this.form.fromDate = await moment(lastQuarterStartDate).format('DD/MM/YYYY')
			this.form.toDate = await moment(lastQuarterEndDate).format('DD/MM/YYYY')
			const res = await CertificateAsset.exportDataCertificationAsset(this.form)
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
		}
	},
	beforeMount () {
		this.getDataAll()
		this.getProfiles()
	},
	async mounted () {
		if (this.$store.getters.dictionaries && this.$store.getters.dictionaries.length === 0) {
			const res = await WareHouse.getDictionaries()
			store.commit(types.SET_DICTIONARIES, {...res})
		}
		if (this.$store.getters.appraiseOther && this.$store.getters.appraiseOther.length === 0) {
			const response = await Certificate.getAppraiseOthers()
			store.commit(types.SET_APPRAISE_OTHER, {...response})
		}
	}
}
</script>

<style scoped lang="scss">
.main-wrapper-new {
  background: #FFFFFF;
  box-shadow: 0px 0px 20px rgba(0, 80, 124, 0.16);
  border-radius: 5px;
  margin: 12px;
  padding: 22px 12px;

  .index-screen-button {
    img {
      max-width: 12px;
      height: auto;
    }

    @media (max-width: 1440px) {
      width: 135px;
      margin-right: 0;
    }

    @media (max-width: 767px) {
      width: 100%;
      margin-bottom: 0;
    }
  }

  .btn {
    background: #E2F3FC;
    border: 1px solid #007EC6;
    border-radius: 3px;
    color: #007EC6;
    height: 35px;
  }

  .button__detail {
    @media (max-width: 1024px) {
      align-items: unset !important;
    }
  }

  .search-block {
    @media (max-width: 767px) {
      flex-direction: column;
      align-items: center;

      .search {
        margin: 10px 0;
      }
    }
  }
}
.container_image {
	width: 35px;
	height: 30px;
}
</style>
