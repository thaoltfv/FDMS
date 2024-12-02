<template>
	<div id="wareHouse" class="w-100 position-relative">
		<div class="loading" :class="{ loading__true: isSubmit }">
			<a-spin />
		</div>
		<div
			class="table-detail container__table--detail position-relative"
			:class="listAppraise.length === 0 ? 'empty-data' : ''"
		>
			<a-form :form="formYTSS">
				<a-table
					bordered
					:columns="columns"
					:data-source="listAppraise"
					:loading="isLoading"
					class="table__import"
					:rowKey="record => record.id"
					:pagination="{
						...pagination
					}"
				>
					<!-- thêm mới footer -->
					<template
						slot="footer"
						slot-scope="currentPageData"
						v-if="add"
						class="footer"
					>
						<ValidationObserver
							tag="form"
							ref="observer"
							@submit.prevent="validateBeforeSubmit"
						>
							<div
								class="row table-footer align-items-center"
								v-if="isAddNewItem"
							>
								<!-- <div class="input-table col-1">
                        <InputText class="label-none input-error" :max-length="3" type="number" v-model="form.appraise_point" label="TT"/>
                      </div> -->
								<div class="input-table col-3">
									<InputText
										class="label-none input-error"
										v-model="form.appraise_title"
										vid="addName"
										label="TSTD"
										rules="required"
									/>
								</div>
								<div class="input-table col-1">
									<InputText
										class="label-none input-error"
										:max-length="3"
										type="number"
										v-model="form.appraise_point"
										label="Điểm"
										rules="required"
									/>
								</div>
								<div class="input-table col-2">
									<InputText
										class="label-none input-error"
										v-model="form.asset_title"
										vid="addId"
										label="TSSS"
										rules="required"
									/>
								</div>

								<div class="input-table col-1">
									<InputText
										class="label-none input-error"
										:max-length="3"
										type="number"
										v-model="form.asset_point"
										label="Điểm"
										rules="required"
									/>
								</div>
								<div class="input-table col-2">
									<InputText
										class="label-none input-error"
										v-model="form.description"
										vid="addId"
										label="Đánh giá"
										rules="required"
									/>
								</div>
								<div class="input-table col-1">
									<label><strong>TLĐC</strong></label>
									<InputNumberFormat
										v-model="form.adjust_percent"
										@change="changePercent($event)"
										:min="-100"
										class="label-none input-error"
										label="Tỉ lệ đối chiếu"
										rules="required"
									/>
								</div>
								<div class="input-table col-2 d-flex justify-content-end">
									<button class="btn btn-orange mr-3">Lưu</button>
									<button
										type="button"
										class="btn btn-white"
										@click="cancelItem"
									>
										Hủy
									</button>
								</div>
							</div>
							<button
								v-if="!isAddNewItem && add"
								class="btn btn-white"
								type="button"
								@click="addItem"
							>
								Thêm mới
							</button>
						</ValidationObserver>
					</template>

					<!--Custom type table edit-->
					<template slot="appraise_title" slot-scope="appraise_title, record">
						<a-form-item class="mb-0" v-if="record.editable">
							<a-input
								v-decorator="[
									'appraise_title',
									{ rules: [{ required: true, message: 'TSTĐ là bắt buộc' }] }
								]"
							/>
						</a-form-item>
						<p v-else class="mb-0">{{ appraise_title }}</p>
					</template>

					<template slot="appraise_point" slot-scope="appraise_point, record">
						<a-form-item class="mb-0" v-if="record.editable">
							<InputText
								v-decorator="['appraise_point']"
								class="label-none input-error"
								:max-length="3"
								type="number"
								v-model="form.appraise_point"
								rules="required"
							/>
						</a-form-item>
						<p v-else class="mb-0">{{ appraise_point }}</p>
					</template>

					<template slot="asset_title" slot-scope="asset_title, record">
						<a-form-item class="mb-0" v-if="record.editable">
							<a-input
								v-decorator="[
									'asset_title',
									{ rules: [{ required: true, message: 'TSSS là bắt buộc' }] }
								]"
							/>
						</a-form-item>
						<p v-else class="mb-0">{{ asset_title }}</p>
					</template>

					<template slot="asset_point" slot-scope="asset_point, record">
						<a-form-item class="mb-0" v-if="record.editable">
							<InputText
								v-decorator="['asset_point']"
								v-model="form.asset_point"
								class="label-none input-error"
								:max-length="3"
								type="number"
								rules="required"
							/>
						</a-form-item>
						<p v-else class="mb-0">{{ asset_point }}</p>
					</template>

					<template slot="description" slot-scope="description, record">
						<a-form-item class="mb-0" v-if="record.editable">
							<a-input
								v-decorator="[
									'description',
									{
										rules: [{ required: true, message: 'Đánh giá là bắt buộc' }]
									}
								]"
							/>
						</a-form-item>
						<p v-else class="mb-0">{{ description }}</p>
					</template>

					<template slot="adjust_percent" slot-scope="adjust_percent, record">
						<a-form-item class="mb-0" v-if="record.editable">
							<a-input
								v-decorator="[
									'adjust_percent',
									{ rules: [{ required: true, message: 'TLĐC là bắt buộc' }] }
								]"
							/>
						</a-form-item>
						<p v-else class="mb-0">
							{{ adjust_percent ? adjust_percent + "%" : "0%" }}
						</p>
					</template>

					<!-- action -->
					<template slot="action" slot-scope="action_delete, record">
						<div class="d-flex justify-content-end" v-if="record.editable">
							<button
								class="btn btn-orange mr-1"
								@click.prevent="handleSubmitForm"
							>
								Lưu
							</button>
							<button
								@click.prevent="() => (openModalCancel = true)"
								class="btn btn-white"
							>
								Hủy
							</button>
						</div>
						<div class="d-flex justify-content-end" v-else>
							<button
								v-if="edit"
								:disabled="editingKey !== ''"
								class="btn btn-white"
								type="button"
								@click.prevent="() => editTable(action_delete.id)"
							>
								Sửa
							</button>
							<button
								v-if="deleted"
								class="btn btn-white"
								type="button"
								@click.prevent="handleOpenModal(action_delete.id)"
							>
								Xóa
							</button>
						</div>
					</template>
				</a-table>
			</a-form>
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
			@cancel="(openModalCancel = false), (activePending = ''), (add = true)"
			@action="handleCancel"
		/>
	</div>
</template>

<script>
import InputText from "@/components/Form/InputText";
import { Form, Input, Select } from "ant-design-vue";
import ModalCancel from "@/components/Modal/ModalCancel";
import ModalNotification from "@/components/Modal/ModalNotification";
import ModalDelete from "@/components/Modal/ModalDelete";
import InputNumberFormat from "@/components/Form/InputNumber";
import Appraise from "@/models/Appraise";
export default {
	name: "Table",
	props: ["listAppraise", "type"],
	components: {
		ModalNotification,
		ModalDelete,
		ModalCancel,
		InputText,
		InputNumberFormat,
		"a-form": Form,
		"a-form-item": Form.Item,
		"a-input": Input,
		"a-select": Select,
		"a-select-option": Select.Option
	},
	data() {
		return {
			isSubmit: false,
			message: "",
			openNotification: false,
			openModal: false,
			position: [],
			isLoading: false,
			isAddNewItem: false,
			totalRecord: 0,
			pagination: {
				pageSize: 1000
			},
			formYTSS: this.$form.createForm(this, {}),
			form: {
				appraise_title: "",
				appraise_point: "",
				asset_title: "",
				asset_point: "",
				description: "",
				adjust_percent: 0,
				type: this.type || "",
				difference_point: 0,
				asset_percent: 0,
				appraise_percent: 0
			},
			add: false,
			edit: false,
			deleted: false,
			editingKey: "",
			activePending: "",
			openModalCancel: false
		};
	},
	computed: {
		columns() {
			return [
				{
					title: "TT",
					align: "left",
					dataIndex: "id",
					width: "7%"
				},
				{
					title: "TSTĐ",
					align: "left",
					scopedSlots: { customRender: "appraise_title" },
					dataIndex: "appraise_title",
					width: "20%"
				},
				{
					title: "ĐIỂM",
					align: "right",
					scopedSlots: { customRender: "appraise_point" },
					dataIndex: "appraise_point",
					width: "7%"
				},
				{
					title: "TSSS",
					align: "left",
					scopedSlots: { customRender: "asset_title" },
					dataIndex: "asset_title",
					width: "20%"
				},
				{
					title: "ĐIỂM",
					align: "right",
					scopedSlots: { customRender: "asset_point" },
					dataIndex: "asset_point",
					width: "7%"
				},
				{
					title: "ĐÁNH GIÁ",
					align: "left",
					scopedSlots: { customRender: "description" },
					dataIndex: "description",
					width: "20%"
				},
				// {
				//   title: 'CHÊNH LỆCH',
				//   align: 'right',
				//   dataIndex: 'difference_point'
				// },
				// {
				//   title: 'TỶ LỆ TSTĐ',
				//   align: 'right',
				//   dataIndex: 'appraise_percent'
				// },
				// {
				//   title: 'TỶ LỆ TSSS',
				//   align: 'right',
				//   dataIndex: 'asset_percent'
				// },
				{
					title: "TLĐC",
					align: "right",
					scopedSlots: { customRender: "adjust_percent" },
					dataIndex: "adjust_percent",
					width: "7%"
				},
				{
					title: "",
					scopedSlots: { customRender: "action" },
					align: "center",
					width: "5%"
				}
			];
		},
		roles() {
			return {
				data: this.positions,
				id: "id",
				key: "description"
			};
		}
	},
	created() {
		const permission = this.$store.getters.currentPermissions;
		// fix_permission
		permission.forEach(value => {
			if (value === "ADD_CATEGORY") {
				this.add = true;
			}
			if (value === "EDIT_CATEGORY") {
				this.edit = true;
			}
			if (value === "DELETE_CATEGORY") {
				this.deleted = true;
			}
		});
	},
	methods: {
		// async onPageChange (pagination, filters, sorter) {
		//   this.perPage = pagination.pageSize
		//   const sortDesc = replace(sorter.order, 'end', '')

		//   const query = {
		//     page: pagination.current,
		//     limit: pagination.pageSize,
		//     order: sortDesc
		//   }
		// },
		editTable(key) {
			if (this.onValidateTable()) {
				this.add = false;
				this.id = key;
				this.openModalCancel = true;
				this.activePending = "edit";
			} else {
				const newData = [...this.listAppraise];
				const target = newData.filter(item => key === item.id)[0];
				this.editingKey = key;
				if (target) {
					target.editable = true;
					this.listAppraise = newData;
					this.setFieldsValue(target);
				}
			}
		},
		setFieldsValue(target) {
			setTimeout(() => {
				this.formYTSS.setFieldsValue(target);
			}, 200);
		},
		onValidateTable() {
			let newData = [...this.listAppraise];
			let target = newData.find(item => item.editable);
			return !!target || this.isAddNewItem;
		},
		handleSubmitForm(event) {
			event.preventDefault();
			this.formYTSS.validateFields(err => {
				if (!err) {
					const newData = [...this.listAppraise];
					const target = newData.find(item => item.editable);
					if (target) {
						let params = this.formYTSS.getFieldsValue();
						if (target.id && params) {
							params.id = target.id;
							params["type"] = this.form.type;
							this.handleUpdateYTSS(params);
						}
					}
				}
			});
		},
		async handleUpdateYTSS(data) {
			const resp = await Appraise.updateDictionary(data);
			if (resp.data) {
				this.message = "Cập nhật YTSS thành công.";
				await this.$emit("resfreshData");
				this.openNotification = true;
				this.editingKey = "";
				this.isAddNewItem = false;
			} else if (resp.error) {
				this.$toast.open({
					message: resp.error.message,
					type: "error",
					position: "top-right"
				});
			}
		},
		addItem() {
			if (this.onValidateTable()) {
				this.openModalCancel = true;
				this.activePending = "add";
			} else {
				this.isAddNewItem = true;
			}
		},
		cancelItem() {
			this.form.appraise_title = "";
			this.form.appraise_point = "";
			this.form.asset_title = "";
			this.form.asset_point = "";
			this.form.description = "";
			this.form.adjust_percent = 0;
			this.form.difference_point = 0;
			this.form.asset_percent = 0;
			this.form.appraise_percent = 0;
			this.isAddNewItem = false;
		},
		handleOpenModal(id) {
			this.openModal = true;
			this.id = id;
		},
		changePercent(e) {
			if (e) {
				this.form.adjust_percent = e;
			} else {
				this.form.adjust_percent = "";
			}
		},
		async validateBeforeSubmit() {
			const isValid = await this.$refs.observer.validate();
			if (!isValid) {
				this.$toast.open({
					message: "Vui lòng nhập đầy đủ các trường bắt buộc",
					type: "error",
					position: "top-right"
				});
			} else {
				this.handleSubmit();
			}
		},
		handleSubmit() {
			let data = this.form;
			this.createYTSS(data);
		},
		async createYTSS(data) {
			try {
				this.isSubmit = true;
				const resp = await Appraise.create(data);
				if (resp) {
					if (resp.data) {
						this.isSubmit = false;
						this.message = "Tạo mới YTSS thẩm định thành công.";
						this.openNotification = true;
						this.isAddNewItem = false;
						this.form.appraise_title = "";
						this.form.appraise_point = "";
						this.form.asset_title = "";
						this.form.asset_point = "";
						this.form.description = "";
						this.form.adjust_percent = 0;
						this.form.difference_point = 0;
						this.form.asset_percent = 0;
						this.form.appraise_percent = 0;
						await this.$emit("resfreshData");
					} else if (resp.error) {
						this.$toast.open({
							message: resp.error.message,
							type: "error",
							position: "top-right",
							duration: 3000
						});
						this.isSubmit = false;
					}
				}
			} catch (err) {
				this.isSubmit = false;
				throw err;
			}
		},
		handleCancel() {
			const newData = [...this.listAppraise];
			switch (this.activePending) {
				case "add":
					this.isAddNewItem = true;
					const targetAdd = newData.find(item => item.editable);
					if (targetAdd) {
						this.editingKey = "";
						targetAdd.editable = false;
						this.listAppraise = newData;
					}
					break;
				case "edit":
					this.isAddNewItem = false;
					const targetEdit = newData.find(item => item.id === this.id);
					if (targetEdit) {
						this.editingKey = "";
						targetEdit.editable = true;
						this.listAppraise = newData;
						this.setFieldsValue(targetEdit);
					}
					break;
				default:
					const target = newData.find(item => item.editable);
					if (target) {
						this.editingKey = "";
						target.editable = false;
						this.listAppraise = newData;
					}
					break;
			}
		},
		async handleDelete() {
			await Appraise.deleteDictionary(this.id);
			await this.$emit("resfreshData");
			this.$toast.open({
				message: "Xóa YTSS thẩm định thành công",
				type: "success",
				position: "top-right"
			});
		}
		// async getDictionary () {
		//   try {
		//     const reps = await WareHouse.getDictionaries()
		//     this.positions = [...reps.data.chuc_vu]
		//   } catch (err) {
		//     this.isSubmit = false
		//     throw err
		//   }
		// }
	}
};
</script>

<style scoped lang="scss">
.ant-table-footer {
	height: 80px !important;
	margin-bottom: 20px !important;
}
.loading {
	display: none;
	&__true {
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
		&.btn-loading {
			&:after {
				width: 2rem !important;
				height: 2rem !important;
			}
		}
	}
}
.btn {
	&-white,
	&-orange {
		padding: 0.375rem 1rem;
		min-width: auto;
	}
}
</style>
