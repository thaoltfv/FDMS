<template>
    <div>
        <div class="appraiser-container">
            <div class="table-detail">
                <a-form :form="formANTD">
                    <a-table bordered :columns="columns" :data-source="appraiserList" :loading="isLoading" class="table__import"
                        :rowKey="record => record.id"
                        :pagination="{...pagination }"
                        @change="onPageChange">
                        <!--Custom type table-->
                        <template slot="footer" slot-scope="currentPageData" v-if="add">
                            <ValidationObserver tag="form" ref="observer" @submit.prevent="validateBeforeSubmit">
                                <div class="row table-footer" v-if="isAddNewItem">
                                    <div class="input-table col-3">
                                        <InputText
                                            class="label-none input-error"
                                            v-model="form.name"
                                            vid="addName"
                                            label="Thẩm định viên"
                                            rules="required"/>
                                    </div>
                                    <div class="input-table col-3">
                                        <InputText
                                            class="label-none input-error"
                                            v-model="form.appraiser_number"
                                            vid="addId"
                                            label="Số thẻ TĐV về giá của thẩm định viên"
                                            />
                                    </div>
                                    <div class="input-table col-3">
                                      <label>Chức vụ</label>
                                        <InputCategory
                                            class="label-none input-error"
                                            v-model="form.appraise_position_id"
                                            :options="roles"
                                            label="Chức vụ"
                                            rules="required"/>
                                    </div>
                                    <div class="input-table col-3 d-flex justify-content-end">
                                        <button class="btn btn-orange mr-3">Lưu</button>
                                        <button
                                            type="button"
                                            class="btn btn-white"
                                            @click="cancelItem"
                                        >Hủy</button>
                                    </div>
                                </div>
                                <button
                                    v-if="!isAddNewItem"
                                    class="btn btn-white"
                                    type="button"
                                    @click="addItem"
                                >Thêm mới</button>
                            </ValidationObserver>
                        </template>

                        <template
                            slot="appraise_position"
                            slot-scope="appraise_position,record"
                        >
                            <a-form-item
                                class="mb-0"
                                v-if="record.editable"
                            >
                                <a-select v-decorator="['appraise_position_id', { rules: [{ required: true, message: 'Chức vụ là bắt buộc' }] }]">
                                    <a-select-option :value="0">Bỏ chọn</a-select-option>
                                    <a-select-option
                                        v-for="(item) in positions"
                                        :key="item.id"
                                        :value="item.id"
                                    >{{item.description}}</a-select-option>
                                </a-select>
                            </a-form-item>
                            <p
                                v-else
                                class="mb-0 appraiserName"
                            >
                                {{appraise_position}}
                            </p>
                        </template>
                        <template
                            slot="name"
                            slot-scope="name,record"
                        >
                            <a-form-item class="mb-0" v-if="record.editable">
                                <a-input v-decorator="['name', { rules: [{ required: true, message: 'Thẩm định viên là bắt buộc' }] }]" />
                            </a-form-item>
                            <p v-else class="mb-0 appraiserName"> {{name}} </p>
                        </template>
                        <template
                            slot="appraiser_number"
                            slot-scope="appraiser_number,record"
                        >
                            <a-form-item
                                class="mb-0"
                                v-if="record.editable"
                            >
                                <a-input
                                    class="text-right"
                                    v-decorator="['appraiser_number']"
                                />
                            </a-form-item>
                            <p
                                v-else
                                class="mb-0 appraiserName"
                            >
                                {{appraiser_number}}
                            </p>
                        </template>
                        <template slot="action" slot-scope="action_delete,record">
                            <div class="d-flex justify-content-end" v-if="record.editable">
                                <button class="btn btn-orange mr-2" @click.prevent="handleSubmitForm">Lưu</button>
                                <button @click.prevent="() => openModalCancel=true" class="btn btn-white">Hủy</button>
                            </div>
                            <div v-else class="d-flex justify-content-end">
                                <button
                                    v-if="edit"
                                    :disabled="editingKey !== ''"
                                    class="btn btn-white"
                                    type="button"
                                    @click.prevent="() => editTable(record.id)">Sửa</button>
                                <button
                                    v-if="deleted"
                                    :disabled="editingKey !== ''"
                                    class="btn btn-white"
                                    type="button"
                                    @click.prevent="handleOpenModal(record.id)"
                                >Xóa</button>
                            </div>
                        </template>
                    </a-table>
                </a-form>
            </div>
        </div>
        <ModalDelete
            v-if="openModal"
            @cancel="openModal = false"
            @action="handleDelete"
        />
        <ModalNotification
            v-if="openNotification"
            v-bind:notification="this.message"
            @cancel="openNotification = false"
        />
        <ModalCancel
            v-if="openModalCancel"
            @cancel="openModalCancel = false, activePending=''"
            @action="handleCancel"
        />
    </div>
</template>

<script>
import { convertPagination } from '@/utils/filters'
import { replace } from 'lodash-es'
import InputCategory from '@/components/Form/InputCategory'
import Appraiser from '@/models/Appraiser'
import InputText from '@/components/Form/InputText'
import WareHouse from '@/models/WareHouse'
import ModalDelete from '@/components/Modal/ModalDelete'
import ModalNotification from '@/components/Modal/ModalNotification'
import { Form, Input, Select } from 'ant-design-vue'
import ModalCancel from '@/components/Modal/ModalCancel'

export default {
	name: 'Index',
	components: {
		ModalNotification,
		ModalDelete,
		ModalCancel,
		InputCategory,
		InputText,
		'a-form': Form,
		'a-form-item': Form.Item,
		'a-input': Input,
		'a-select': Select,
		'a-select-option': Select.Option
	},
	computed: {
		columns () {
			return [
				{
					title: 'Thẩm định viên',
					align: 'left',
					width: '25%',
					scopedSlots: { customRender: 'name' },
					dataIndex: 'name'
				},
				{
					title: 'Số thẻ TĐV về giá của thẩm định viên',
					align: 'right',
					width: '25%',
					scopedSlots: { customRender: 'appraiser_number' },
					dataIndex: 'appraiser_number'
				},
				{
					title: 'Chức vụ',
					align: 'left',
					width: '25%',
					scopedSlots: { customRender: 'appraise_position' },
					dataIndex: 'appraise_position.description'
				},
				{
					title: '',
					scopedSlots: { customRender: 'action' },
					align: 'center',
					width: '25%'
				}
			]
		},
		roles () {
			return {
				data: this.positions,
				id: 'id',
				key: 'description'
			}
		}
	},
	data () {
		return {
			form: {
				name: '',
				appraiser_number: '',
				appraise_position_id: ''
			},
			formANTD: this.$form.createForm(this, {}),
			pagination: {},
			message: '',
			openNotification: false,
			openModal: false,
			position: [],
			isLoading: false,
			appraiserList: [],
			isAddNewItem: false,
			add: false,
			edit: false,
			deleted: false,
			openModalCancel: false,
			editingKey: '',
			activePending: ''
		}
	},
	created () {
		const permission = this.$store.getters.currentPermissions
		// fix_permission
		permission.forEach((value) => {
			if (value === 'ADD_ROLE') {
				this.add = true
			}
			if (value === 'EDIT_ROLE') {
				this.edit = true
			}
			if (value === 'DELETE_ROLE') {
				this.deleted = true
			}
		})
	},
	methods: {
		addItem () {
			if (this.onValidateTable()) {
				this.openModalCancel = true
				this.activePending = 'add'
			} else {
				this.isAddNewItem = true
			}
		},
		cancelItem () {
			this.form.name = ''
			this.form.appraiser_number = ''
			this.form.appraise_position_id = ''
			this.isAddNewItem = false
		},
		removeItem (index) {
			this.appraiserList.splice(index, 1)
		},
		async getAppraiserList (query = {}) {
			this.isLoading = true
			const filter = {}
			for (let property in this.filter) {
				filter[`${property}`] = this.filter[property]
			}

			try {
				const resp = await Appraiser.paginate({ query: { page: 1, limit: 15, ...query } })
				this.appraiserList = [...resp.data.data]
				this.pagination = convertPagination(resp.data)
				this.totalRecord = resp.data.total
				this.isLoading = false
			} catch (e) {
				this.isLoading = false
			}
		},
		async onPageChange (pagination, filters, sorter = {}) {
			this.perPage = pagination.pageSize
			const sortDesc = replace(sorter.order, 'end', '')

			const query = {
				page: pagination.current,
				limit: pagination.pageSize,
				order: sortDesc
			}
			this.getAppraiserList(query)
		},
		async getDictionary () {
			try {
				const reps = await WareHouse.getDictionaries()
				this.positions = [...reps.data.chuc_vu]
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},
		handleOpenModal (id) {
			this.openModal = true
			this.id = id
		},
		async handleDelete () {
			await Appraiser.deleteAppraiser(this.id)
			await this.getAppraiserList()
			this.$toast.open({
				message: 'Xóa TDV thành công',
				type: 'success',
				position: 'top-right'
			})
		},
		async validateBeforeSubmit () {
			const isValid = await this.$refs.observer.validate()
			if (!isValid) {
				this.$toast.open({
					message: 'Vui lòng nhập đầy đủ các trường bắt buộc',
					type: 'error',
					position: 'top-right'
				})
			} else {
				this.handleSubmit()
			}
		},
		handleSubmit () {
			this.isSubmit = true
			let data = this.form
			this.createAppraiser(data)
		},
		async createAppraiser (data) {
			try {
				const resp = await Appraiser.create(data)
				if (resp) {
					if (resp.data) {
						this.isSubmit = false
						this.message = 'Tạo mới TDV thành công.'
						this.openNotification = true
						this.isAddNewItem = false
						this.form.name = ''
						this.form.appraiser_number = ''
						this.form.appraise_position_id = ''
						await this.getAppraiserList()
					} else if (resp.error) {
						this.$toast.open({
							message: resp.error.message,
							type: 'error',
							position: 'top-right',
							duration: 3000
						})
						this.isSubmit = false
					}
				}
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},
		setFieldsValue (target) {
			setTimeout(() => {
				this.formANTD.setFieldsValue(target)
			}, 200)
		},
		editTable (key) {
			if (this.onValidateTable()) {
				this.id = key
				this.openModalCancel = true
				this.activePending = 'edit'
			} else {
				const newData = [...this.appraiserList]
				const target = newData.filter((item) => key === item.id)[0]
				this.editingKey = key
				if (target) {
					target.editable = true
					this.appraiserList = newData
					this.setFieldsValue(target)
				}
			}
		},
		cancel (id) {
			const newData = [...this.appraiserList]
			this.editingKey = ''
			if (id) {
				const target = newData.filter((item) => id === item.id)[0]
				if (target) {
					target.editable = false
					this.appraiserList = newData
				}
			} else {
				newData.splice(newData.length - 1, 1)
				this.isAddNewItem = false
				this.appraiserList = newData
			}
		},
		handleSubmitForm (event) {
			event.preventDefault()
			this.formANTD.validateFields((err) => {
				if (!err) {
					const newData = [...this.appraiserList]
					const target = newData.find((item) => item.editable)
					if (target) {
						let params = this.formANTD.getFieldsValue()
						if (target.id && params) {
							params.id = target.id
							this.handleUpdateTDV(params)
						}
					}
				}
			})
		},
		async  handleUpdateTDV (data) {
			const resp = await Appraiser.update(data)
			if (resp.data) {
				this.message = 'Cập nhật TDV thành công.'
				this.openNotification = true
				this.editingKey = ''
				this.isAddNewItem = false
				this.onPageChange(1)
			} else if (resp.error) {
				this.$toast.open({
					message: resp.error.message,
					type: 'error',
					position: 'top-right'
				})
			}
		},
		handleCancel () {
			const newData = [...this.appraiserList]
			switch (this.activePending) {
			case 'add':
				this.isAddNewItem = true
				const targetAdd = newData.find((item) => item.editable)
				if (targetAdd) {
					this.editingKey = ''
					targetAdd.editable = false
					this.appraiserList = newData
				}
				break
			case 'edit':
				this.isAddNewItem = false
				const targetEdit = newData.find((item) => item.id === this.id)
				if (targetEdit) {
					this.editingKey = ''
					targetEdit.editable = true
					this.appraiserList = newData
					this.setFieldsValue(targetEdit)
				}
				break
			default :
				const target = newData.find((item) => item.editable)
				if (target) {
					this.editingKey = ''
					target.editable = false
					this.appraiserList = newData
				}
				break
			}
		},

		onValidateTable () {
			let newData = [...this.appraiserList]
			let target = newData.find((item) => item.editable)
			return !!target || this.isAddNewItem
		}
	},
	beforeMount () {
		this.getAppraiserList()
		this.getDictionary()
	}
}
</script>

<style scoped lang="scss">
.appraiser-container {
    padding: 0 1.25rem;

    .table__tangible {
        border-radius: 5px !important;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.4);
    }

    th {
        text-align: center;
        background: #f3f2f7;
        color: #000000;
        font-weight: 700;
        padding: 20px 15px;

        border: none;
    }

    td {
        border-right: none;
        color: #333333;
        word-break: break-word;
        border-bottom: 1px solid #c5c5c5 !important;
        padding: 20px;
    }
    tr:nth-child(even) {
        background: rgba(194, 201, 209, 0.2);
    }
}
.input-table {
    padding: 5px 15px;
}
.btn {
    &-white,
    &-orange {
        padding: 0.375rem 1rem;
        min-width: auto;
        height: 35px
    }
}
.acronym,
.appraiserName {
    text-transform: none !important;
}
</style>
<style lang="scss">
.ant-select-selection {
    .ant-select-selection-selected-value {
        text-transform: uppercase;
    }
}
</style>
