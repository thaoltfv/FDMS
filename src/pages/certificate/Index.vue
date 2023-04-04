<template>
 <div>
   <div class="container-button appraise-container">
      <div class="button__detail row mx-0 justify-content-between justify-content-lg-end align-items-center">
        <div class="col-9">
          <Search @filter-changed="onFilterQuickSearchChange($event)"/>
        </div>
        <div class="col-3 d-flex justify-content-end">
          <button class="btn btn-white text-nowrap index-screen-button" @click.prevent="handleSearch()">
            <img src="../../assets/icons/ic_search.svg" style="margin-right: 8px" alt="search">Tìm kiếm
          </button>
          <router-link v-if="add" :to="{name: 'certificate.create'}" class="btn btn-white text-nowrap index-screen-button"><img
            src="../../assets/icons/ic_add.svg" style="margin-right: 8px" alt="search">Tạo mới
          </router-link>
        </div>
      </div>
   </div>
   <div class="container-fluid appraise-container mt-4">
      <TableAll :listCertificates="listAppraiseAll" :isLoading="isLoading" :pagination="paginationAll" @handleChange="onPageChange"/>
   </div>
    <ModalSearchAppraise
      v-if="showModalSearch"
      @cancel="showModalSearch = false"
      @filter-changed="onFilterChange($event)"
      :roles="this.activeStatus"
      :status="status"
      :filter_search="this.filter"
    />
 </div>
</template>

<script>

import {Tabs, TabItem} from 'vue-material-tabs'
import Search from '@/pages/certificate/Search'
import ModalSearchAppraise from './components/modals/ModalSearchAppraise.vue'
import Tables from './components/Tables'
import TableClose from './components/TableClose'
import TableLock from './components/TableLock'
import TableOpen from './components/TableOpen'
import TableAll from './components/TableAll'
import JsonExcel from 'vue-json-excel'
import Vue from 'vue'

import AppraiseData from '../../models/AppraiseData'
import { convertPagination } from '@/utils/filters'

Vue.component('downloadExcel', JsonExcel)
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
			view: false,
			add: false,
			edit: false,
			deleted: false,
			accept: false
		}
	},
	components: {
		TabItem,
		Tabs,
		Search,
		Tables,
		TableOpen,
		TableLock,
		TableClose,
		TableAll,
		ModalSearchAppraise
	},
	created () {
		// fix_permission
		const permission = this.$store.getters.currentPermissions
		permission.forEach(value => {
			if (value === 'VIEW_PRICE') {
				this.view = true
			}
			if (value === 'ADD_PRICE') {
				this.add = true
			}
			if (value === 'EDIT_PRICE') {
				this.edit = true
			}
			if (value === 'DELETE_PRICE') {
				this.deleted = true
			}
			if (value === 'ACCEPT_PRICE') {
				this.accept = true
			}
		})
	},
	methods: {
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
			const filter = {...$event}
			const query = {
				query: {
					...filter
				}
			}
			await this.getDataAll(query)
		},
		async onFilterChange ($event) {
			const filter = {...$event}
			const query = {
				query: {
					...filter
				}
			}
			await this.getDataAll(query)
		},
		handleSearch () {
			this.showModalSearch = true
		},
		async getDataDraft (params = {}) {
			this.isLoading = true
			const filter = {}
			try {
				const resp = await AppraiseData.paginate({query: { page: 1, limit: 20, ...params, ...filter, status: 1 }})
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
				const resp = await AppraiseData.paginate({query: { page: 1, limit: 20, ...params, ...filter, status: 2 }})
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
				const resp = await AppraiseData.paginate({query: { page: 1, limit: 20, ...params, ...filter, status: 3 }})
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
				const resp = await AppraiseData.paginate({query: { page: 1, limit: 20, ...params, ...filter, status: 4 }})
				this.listAppraiseClose = [...resp.data.data]
				this.paginationClose = convertPagination(resp.data)
				this.isLoading = false
			} catch (e) {
				this.isLoading = false
			}
		},
		async getDataAll (params = {}) {
			this.isLoading = true
			const filter = {}
			try {
				const resp = await AppraiseData.paginate({query: { page: 1, limit: 20, ...params, ...filter }})
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
		}
	},
	beforeMount () {
		// this.getDataDraft()
		// this.getDataOpen()
		// this.getDataLock()
		// this.getDataClose()
		this.getDataAll()
		this.getProfiles()
	}
}
</script>

<style scoped lang="scss">
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
  .appraise-container {
    padding: 0 1.25rem;
  }
</style>
