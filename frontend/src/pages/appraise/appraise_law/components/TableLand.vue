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
                                    <div class="input-table col-2">
                                      <InputText class="label-none input-error" v-model="form.document_type" vid="document_type" label="Loại văn bản" rules="required"/>
                                    </div>
                                    <div class="input-table col-2">
                                      <label>Số, Ngày</label>
                                      <InputTextarea class="label-none input-error" v-model="form.date" vid="date" label="Số, Ngày" rules="required"/>
                                    </div>
                                    <div class="input-table col-2">
                                      <label>Nội dung văn bản</label>
                                      <InputTextarea class="label-none input-error" v-model="form.content" vid="content" label="Nội dung văn bản" rules="required"/>
                                    </div>
                                    <div class="input-table col-2">
                                      <label>Vùng áp dụng</label>
                                      <InputCategoryData class="label-none input-error" v-model="form.provinces" vid="provinces" :options="optionsProvinces" label="Vùng áp dụng" rules="required"/>
                                    </div>
                                    <div class="input-table col-1">
                                      <label>Sắp xếp</label>
                                      <InputNumberFormat :min="0" class="label-none input-error" v-model="form.position" vid="position" label="Sắp xếp" rules="required" @change="changePosition($event)"/>
                                    </div>
                                    <div class="input-table col-1 justify-content-center">
                                      <label>Mặc định</label>
                                      <div>
                                        <InputSwitch class="label-none" v-model="form.is_defaults" vid="is_defaults" label="Chọn mặc định"/>
                                      </div>
                                    </div>
                                    <div class="input-table col-2 d-flex justify-content-end">
                                      <button class="btn btn-orange mr-3">Lưu</button>
                                      <button type="button" class="btn btn-white" @click="cancelItem">Hủy</button>
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
                            slot="provinces"
                            slot-scope="provinces,record"
                        >
                            <a-form-item
                                class="mb-0"
                                v-if="record.editable"
                            >
                                <a-select disabled v-decorator="['provinces', { rules: [] }]">
                                    <a-select-option
                                        v-for="(item) in listProvinces"
                                        :key="item.name"
                                        :value="item.name"
                                    >{{item.name}}</a-select-option>
                                </a-select>
                            </a-form-item>
                            <p
                                v-else
                                class="mb-0 description"
                            >
                                {{provinces}}
                            </p>
                        </template>

                        <template
                            slot="date"
                            slot-scope="date,record"
                        >
                            <a-form-item
                                class="mb-0"
                                v-if="record.editable"
                            >
                                <a-textarea
                                    class="text-right"
                                    v-decorator="['date', { rules: [{ required: true, message: 'Số, Ngày là bắt buộc' }] }]"
                                />
                            </a-form-item>
                            <p
                                v-else
                                class="mb-0 description"
                            >
                                {{date}}
                            </p>
                        </template>

                        <template
                            slot="content"
                            slot-scope="content,record"
                        >
                            <a-form-item
                                class="mb-0"
                                v-if="record.editable"
                            >
                                <a-textarea
                                    class="text-right"
                                    v-decorator="['content', { rules: [{ required: true, message: 'Nội dung văn bản là bắt buộc' }] }]"
                                />
                            </a-form-item>
                            <p
                                v-else
                                class="mb-0 description"
                            >
                                {{content}}
                            </p>
                        </template>

                        <template
                            slot="document_type"
                            slot-scope="document_type,record"
                        >
                            <a-form-item
                                class="mb-0"
                                v-if="record.editable"
                            >
                                <a-input
                                    class="text-right"
                                    v-decorator="['document_type', { rules: [{ required: true, message: 'Loại văn bản là bắt buộc' }] }]"
                                />
                            </a-form-item>
                            <p
                                v-else
                                class="mb-0 description"
                            >
                                {{document_type}}
                            </p>
                        </template>

                        <template
                            slot="position"
                            slot-scope="position,record"
                        >
                            <a-form-item
                                class="mb-0"
                                v-if="record.editable"
                            >
                                <a-input
                                    class="text-right"
                                    v-decorator="['position', { rules: [{ required: true, message: 'Sắp xếp là bắt buộc' }] }]"
                                />
                            </a-form-item>
                            <p
                                v-else
                                class="mb-0 description"
                            >
                                {{position}}
                            </p>
                        </template>

                        <template
                            slot="is_defaults"
                            slot-scope="is_defaults,record"
                        >
                            <a-form-item
                                class="mb-0"
                                v-if="record.editable"
                            >
                                <a-switch v-model="record.is_defaults" v-decorator="['is_defaults', { rules: [] }]" />
                            </a-form-item>
                            <p
                                v-else
                                class="mb-0 description"
                            >
                                {{is_defaults ? 'Có' : 'Không'}}
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
import AppraiseLaw from '@/models/AppraiseLaw'
import InputText from '@/components/Form/InputText'
import InputTextarea from '@/components/Form/InputTextarea'
import InputSwitch from '@/components/Form/InputSwitch'
import InputCategoryData from '@/components/Form/InputCategoryData'
import InputNumberFormat from '@/components/Form/InputNumber'
import WareHouse from '@/models/WareHouse'
import ModalDelete from '@/components/Modal/ModalDelete'
import ModalNotification from '@/components/Modal/ModalNotification'
import { Form, Input, Select } from 'ant-design-vue'
import ModalCancel from '@/components/Modal/ModalCancel'

export default {
	name: 'TableLand',
	props: ['listProvinces'],
	components: {
		ModalNotification,
		ModalDelete,
		ModalCancel,
		InputCategory,
		InputText,
		InputCategoryData,
		InputTextarea,
		InputNumberFormat,
		InputSwitch,
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
					title: 'Loại văn bản',
					align: 'left',
					width: '16%',
					scopedSlots: {customRender: 'document_type'},
					dataIndex: 'document_type'
				},
				{
					title: 'Số, Ngày',
					align: 'left',
					width: '16%',
					scopedSlots: {customRender: 'date'},
					dataIndex: 'date'
				},
				{
					title: 'Nội dung văn bản',
					align: 'left',
					width: '16%',
					scopedSlots: {customRender: 'content'},
					dataIndex: 'content'
				},
				{
					title: 'Vùng áp dụng (chọn 1 hoặc nhiều)',
					align: 'left',
					width: '16%',
					scopedSlots: {customRender: 'provinces'},
					dataIndex: 'provinces'
				},
				{
					title: 'Sắp xếp',
					align: 'right',
					width: '8%',
					scopedSlots: {customRender: 'position'},
					dataIndex: 'position'
				},
				{
					title: 'Chọn mặc định',
					align: 'left',
					width: '8%',
					scopedSlots: {customRender: 'is_defaults'},
					dataIndex: 'is_defaults'
				},
				{
					title: '',
					scopedSlots: {customRender: 'action'},
					align: 'center',
					width: '16%'
				}
			]
		},
		roles () {
			return {
				data: this.positions,
				id: 'id',
				key: 'description'
			}
		},
		optionsProvinces () {
			return {
				data: this.listProvinces,
				id: 'name',
				key: 'name'
			}
		}
	},
	data () {
		return {
			form: {
				date: '',
				content: '',
				provinces: 'Tất cả',
				position: '',
				document_type: '',
				is_defaults: true,
				type: 'DAT_DAI'
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
			activePending: '',
			dataProvinces: []
		}
	},
	created () {
		const permission = this.$store.getters.currentPermissions
		// fix_permission
		permission.forEach((value) => {
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
	methods: {
		changePosition (e) {
			if (e && parseInt(e) > 0) {
				this.form.position = parseInt(e)
			} else {
				this.form.position = ''
			}
		},
		addItem () {
			if (this.onValidateTable()) {
				this.openModalCancel = true
				this.activePending = 'add'
			} else {
				this.isAddNewItem = true
			}
		},
		cancelItem () {
			this.form = {
				date: '',
				content: '',
				provinces: 'Tất cả',
				position: '',
				document_type: '',
				is_defaults: true,
				type: 'DAT_DAI'
			}
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
				const resp = await AppraiseLaw.paginate({
					query: {
						page: 1,
						limit: 15,
						search: 'DAT_DAI',
						...query
					}
				})
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
			await AppraiseLaw.deleteAppraiser(this.id)
			await this.getAppraiserList()
			this.$toast.open({
				message: 'Xóa văn bản thẩm định thành công',
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
				const resp = await AppraiseLaw.create(data)
				if (resp) {
					if (resp.data) {
						this.isSubmit = false
						this.message = 'Tạo mới văn bản thẩm định thành công.'
						this.openNotification = true
						this.isAddNewItem = false
						this.form = {
							date: '',
							content: '',
							provinces: 'Tất cả',
							position: '',
							document_type: '',
							is_defaults: true,
							type: 'DAT_DAI'
						}
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
		async  handleUpdateTDV (data) {
			const resp = await AppraiseLaw.update(data)
			if (resp.data) {
				this.message = 'Cập nhật văn bản thẩm định thành công.'
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
    border-bottom: 1px solid #C5C5C5 !important;
    padding: 20px;
  }
  tr:nth-child(even) {
    background: rgba(194,201,209,0.2);
  }
}
.input-table {
  padding: 5px 15px;
}
.btn {
  &-white, &-orange {
    padding: 0.375rem 1rem;
    min-width: auto;
    height: 35px
  }
}
.description{
  text-transform: none !important;
  white-space: normal !important;
  margin-bottom: 0;
}
</style>
