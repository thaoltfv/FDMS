<template>
<div class="container-fluid">
		<div class="pannel">
			<div class="pannel__content d-md-flex d-block justify-content-end align-items-end">
				<Search @filter-changed="onFilterChange($event)" />
				<router-link :to="{name: 'apartment.create'}" class="btn btn-create btn-white text-nowrap index-screen-button mb-0 px-3" tag="button" v-if="add"><img
					src="@/assets/icons/ic_add.svg" style="margin-right: 8px" alt="icon add">
					Tạo chung cư
				</router-link>
			</div>
		</div>
	<div :class="totalRecord === 0 ? 'empty-data' : ''">
		<a-table bordered
				:columns="columns"
				:data-source="list_apartments"
				:loading="isLoading"
				:rowKey="record => record.id"
				table-layout="top"
				:pagination="{
					...pagination,
				}"
				@change="onPageChange">
			<!--Custom type table-->
			<template slot="action" slot-scope="action_edit , action_delete">
					<div class="d-flex justify-content-end">
					<a-tooltip placement="bottom" :title="$t('tooltip_edit')" v-if="edit" class="mr-2">
							<a @click.prevent="handleEdit(action_edit.id)" href="#" class="text-decoration-none action">
								<img class="icon-action" src="@/assets/images/icon-edit.svg" alt="icon">
							</a>
					</a-tooltip>
					<a-tooltip placement="bottom" :title="$t('tooltip_delete')" v-if="deleted">
							<a href="#" @click.prevent="handleOpenModal(action_delete.id)" class="text-decoration-none action">
								<img class="icon-action" src="@/assets/images/icon-delete.svg" alt="icon">
							</a>
					</a-tooltip>
					</div>
			</template>
			<template slot="name" slot-scope="name">
				<p class="text-none mb-0">{{name}}</p>
			</template>
			<template slot="created_at" slot-scope="created_at">
				<p class="created_at mb-0">{{created_at | formatDate}}</p>
			</template>
		</a-table>
	</div>
	<ModalDelete
		v-if="openModal"
		@cancel="openModal = false"
		@action="handleDelete"/>
</div>
</template>

<script>
import Apartment from '@/models/Apartment'
import { convertPagination } from '@/utils/filters'
import Search from './Search'
import {replace} from 'lodash-es'
import ModalDelete from '@/components/Modal/ModalDelete'
import Vue from 'vue'
import moment from 'moment'
import File from '@/models/File'
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
			totalRecord: 0,
			isLoading: false,
			list_apartments: [],
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
		this.list_apartments = this.$route.query['list']
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
					title: 'Tên chung cư',
					align: 'left',
					scopedSlots: {customRender: 'name'},
					dataIndex: 'name'
				},
				{
					title: 'Loại',
					align: 'left',
					dataIndex: 'rank'
				},
				{
					title: 'Số block',
					align: 'right',
					dataIndex: 'total_blocks'
				},
				{
					title: 'Số căn hộ',
					align: 'right',
					dataIndex: 'total_apartments'
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
			return File.uploadExcelApartment({data: formData}).then(response => {
				if (response && response.data && response.data.data) {
					this.isLoading = false
					this.getApartments()
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
			await this.getApartments(params)
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
			await this.getApartments(params)
		},

		async handleEdit (id) {
			this.$router.push({
				name: 'apartment.edit',
				query: {
					id: id
				}
			}).catch(_ => {})
		},

		async handleDelete () {
			await Apartment.deleteApartment(this.id)
			await this.getApartments()
			this.$toast.open({
				message: 'Xóa chung cư thành công',
				type: 'success',
				position: 'top-right'
			})
		},

		async getApartments (params = {}) {
			this.isLoading = true
			const filter = {}

			for (let property in this.filter) {
				filter[`${property}`] = this.filter[property]
			}

			try {
				const resp = await Apartment.paginate({
					query: {
						page: 1,
						limit: 20,
						...params,
						...filter
					}
				})

				this.list_apartments = [...resp.data.data]
				this.totalRecord = resp.data.total
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
		this.getApartments()
	},

	async handleDelete () {
		try {
			await Apartment.deleteApartment(this.id)
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
</style>
