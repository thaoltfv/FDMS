<template>
	<div>
		<div class="appraiser-container">
			<div class="table-detail">
				<a-form :form="formCTXD">
					<a-table
						bordered
						:columns="columns"
						:data-source="appraiserList"
						:loading="isLoading"
						class="table__import"
						:rowKey="record => record.id"
						:pagination="{
							...pagination
						}"
						@change="onPageChange"
					>
						<!--Custom type table-->
						<template slot="footer" slot-scope="currentPageData" v-if="add">
							<ValidationObserver
								tag="form"
								ref="observer"
								@submit.prevent="validateBeforeSubmit"
							>
								<div
									class="row table-footer align-items-center"
									v-if="isAddNewItem"
								>
									<div class="input-table col-2">
										<InputText
											class="label-none input-error"
											v-model="form.name"
											vid="addName"
											label="Tên"
											rules="required"
										/>
									</div>
									<div class="input-table col-2">
										<InputText
											class="label-none input-error"
											v-model="form.address"
											vid="addId"
											label="Địa chỉ"
											rules="required"
										/>
									</div>
									<div class="input-table col-1">
										<InputText
											class="label-none input-error"
											:max-length="11"
											type="number"
											v-model="form.phone_number"
											label="Điện thoại"
											rules="required"
										/>
									</div>
									<div class="input-table col-2">
										<InputText
											class="label-none input-error"
											v-model="form.manager_name"
											label="Giám đốc"
											rules="required"
										/>
									</div>
									<div class="input-table col-2">
										<label>Đơn giá xây dựng</label>
										<InputNumberFormat
											@change="changeUnitPrice($event)"
											:formatter="
												valueFormat =>
													`${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')
											"
											:max="99999999999999"
											:min="-99999999999999"
											class="label-none input-error"
											v-model="form.unit_price_m2"
											label="Đơn giá xây dựng"
											rules="required"
										/>
									</div>
									<div class="input-table col-1 justify-content-center">
										<label>Mặc định</label>
										<InputSwitch
											v-model="form.is_defaults"
											vid="is_defaults"
											rules="required"
											label="Chọn mặc định"
											class="label-none"
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

						<template slot="address" slot-scope="address, record">
							<a-form-item class="mb-0" v-if="record.editable">
								<a-input
									v-decorator="[
										'address',
										{
											rules: [
												{ required: true, message: 'Địa chỉ là bắt buộc' }
											]
										}
									]"
								/>
							</a-form-item>
							<p v-else class="mb-0 address">{{ address }}</p>
						</template>
						<template slot="name" slot-scope="name, record">
							<a-form-item class="mb-0" v-if="record.editable">
								<a-input
									v-decorator="[
										'name',
										{ rules: [{ required: true, message: 'Tên là bắt buộc' }] }
									]"
								/>
							</a-form-item>
							<p v-else class="mb-0 address">{{ name }}</p>
						</template>
						<template slot="manager_name" slot-scope="manager_name, record">
							<a-form-item class="mb-0" v-if="record.editable">
								<a-input
									v-decorator="[
										'manager_name',
										{
											rules: [
												{ required: true, message: 'Giám đốc là bắt buộc' }
											]
										}
									]"
								/>
							</a-form-item>
							<p v-else class="mb-0 address">{{ manager_name }}</p>
						</template>
						<template slot="unit_price_m2" slot-scope="unit_price_m2, record">
							<a-form-item class="mb-0" v-if="record.editable">
								<InputNumberFormat
									v-decorator="['unit_price_m2']"
									v-model="form.unit_price_m2"
									@change="changeUnitPrice($event)"
									:formatter="
										valueFormat =>
											`${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')
									"
									:max="99999999999999"
									:min="-99999999999999"
									class="label-none input-error"
									label="Đơn giá xây dựng"
									rules="required"
								/>
							</a-form-item>
							<p v-else class="mb-0">
								{{ unit_price_m2 ? formatNumber(unit_price_m2) : 0 }} đ
							</p>
						</template>

						<template slot="phone_number" slot-scope="phone_number, record">
							<a-form-item class="mb-0" v-if="record.editable">
								<InputText
									v-decorator="['phone_number']"
									v-model="form.phone_number"
									class="label-none input-error"
									:max-length="11"
									type="number"
									rules="required"
								/>
							</a-form-item>
							<p v-else class="mb-0 address">{{ phone_number }}</p>
						</template>

						<template slot="is_defaults" slot-scope="is_defaults, record">
							<a-form-item class="mb-0" v-if="record.editable">
								<InputSwitchZone
									v-model="form.is_defaults"
									vid="is_defaults"
									rules="required"
									label="Chọn mặc định"
									class="label-none"
									v-decorator="['is_defaults']"
									@handleChange="changeSwitchBox"
								/>
							</a-form-item>
							<p v-else class="mb-0">
								{{ is_defaults === true ? "Có" : "Không" }}
							</p>
						</template>
						<template slot="action" slot-scope="action_delete, record">
							<div class="d-flex justify-content-end" v-if="record.editable">
								<button
									class="btn btn-orange mr-2"
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
									class="btn btn-white"
									v-if="deleted"
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
import { convertPagination } from "@/utils/filters";
import { replace } from "lodash-es";
import InputCategory from "@/components/Form/InputCategory";
import AppraisalConstruction from "@/models/AppraisalConstruction";
import InputText from "@/components/Form/InputText";
import WareHouse from "@/models/WareHouse";
import ModalDelete from "@/components/Modal/ModalDelete";
import ModalNotification from "@/components/Modal/ModalNotification";
import InputSwitch from "@/components/Form/InputSwitch";
import InputSwitchZone from "@/components/Form/InputSwitchZone";
import InputNumberFormat from "@/components/Form/InputNumber";
import Certificate from "@/models/Certificate";
import { Form, Input, Select } from "ant-design-vue";
import ModalCancel from "@/components/Modal/ModalCancel";

export default {
	name: "Index",
	components: {
		ModalNotification,
		ModalDelete,
		InputCategory,
		InputSwitch,
		InputNumberFormat,
		ModalCancel,
		InputSwitchZone,
		InputText,
		"a-form": Form,
		"a-form-item": Form.Item,
		"a-input": Input,
		"a-select": Select,
		"a-select-option": Select.Option
	},
	computed: {
		columns() {
			return [
				{
					title: "Tên",
					align: "left",
					width: "16%",
					scopedSlots: { customRender: "name" },
					dataIndex: "name"
				},
				{
					title: "Địa chỉ",
					align: "center",
					width: "16%",
					scopedSlots: { customRender: "address" },
					dataIndex: "address"
				},
				{
					title: "Điện thoại",
					align: "right",
					width: "8%",
					scopedSlots: { customRender: "phone_number" },
					dataIndex: "phone_number"
				},
				{
					title: "Giám đốc",
					align: "left",
					width: "16%",
					scopedSlots: { customRender: "manager_name" },
					dataIndex: "manager_name"
				},
				{
					title: "Đơn giá xây dựng",
					align: "right",
					width: "16%",
					scopedSlots: { customRender: "unit_price_m2" },
					dataIndex: "unit_price_m2"
				},
				{
					title: "Chọn mặc định",
					align: "center",
					width: "8%",
					scopedSlots: { customRender: "is_defaults" },
					dataIndex: "is_defaults"
				},
				{
					title: "",
					scopedSlots: { customRender: "action" },
					align: "center",
					width: "16%"
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
	data() {
		return {
			pagination: {},
			formCTXD: this.$form.createForm(this, {}),
			form: {
				name: "",
				address: "",
				phone_number: "",
				manager_name: "",
				unit_price_m2: "",
				is_defaults: true
			},
			message: "",
			openNotification: false,
			openModal: false,
			position: [],
			isLoading: false,
			appraiserList: [],
			isAddNewItem: false,
			add: false,
			edit: false,
			deleted: false,
			constructions: [],
			constructionsTrue: [],
			editingKey: "",
			activePending: "",
			openModalCancel: false,
			showAdd: true
		};
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
		editTable(key) {
			if (this.onValidateTable()) {
				this.add = false;
				this.id = key;
				this.openModalCancel = true;
				this.activePending = "edit";
			} else {
				const newData = [...this.appraiserList];
				const target = newData.filter(item => key === item.id)[0];
				this.editingKey = key;
				if (target) {
					target.editable = true;
					this.appraiserList = newData;
					this.setFieldsValue(target);
				}
			}
		},
		setFieldsValue(target) {
			setTimeout(() => {
				this.formCTXD.setFieldsValue(target);
			}, 200);
		},
		onValidateTable() {
			let newData = [...this.appraiserList];
			let target = newData.find(item => item.editable);
			return !!target || this.isAddNewItem;
		},
		async handleSubmitForm(event) {
			event.preventDefault();
			this.formCTXD.validateFields(err => {
				if (!err) {
					const newData = [...this.appraiserList];
					const target = newData.find(item => item.editable);
					if (target) {
						let params = this.formCTXD.getFieldsValue();
						if (target.id && params) {
							params.id = target.id;
							if (this.getConstructions() && params.is_defaults) {
								this.$toast.open({
									message:
										"Đã có 3 đơn vị Xây dựng được chọn mặc định vui lòng đưa trường mặc định về không để lưu",
									type: "error",
									position: "top-right"
								});
							} else this.handleUpdateCTDX(params);
						}
					}
				}
			});
		},

		handleCancel() {
			const newData = [...this.appraiserList];
			switch (this.activePending) {
				case "add":
					this.isAddNewItem = true;
					const targetAdd = newData.find(item => item.editable);
					if (targetAdd) {
						this.editingKey = "";
						targetAdd.editable = false;
						this.appraiserList = newData;
					}
					break;
				case "edit":
					this.isAddNewItem = false;
					const targetEdit = newData.find(item => item.id === this.id);
					if (targetEdit) {
						this.editingKey = "";
						targetEdit.editable = true;
						this.appraiserList = newData;
						this.setFieldsValue(targetEdit);
					}
					break;
				default:
					const target = newData.find(item => item.editable);
					if (target) {
						this.editingKey = "";
						target.editable = false;
						this.appraiserList = newData;
					}
					break;
			}
		},
		formatNumber(value) {
			let num = (value / 1).toFixed(0).replace(",", ".");
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
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
			this.form.name = "";
			this.form.address = "";
			this.form.phone_number = "";
			this.form.manager_name = "";
			this.form.unit_price_m2 = "";
			this.form.is_defaults = true;
			this.isAddNewItem = false;
		},
		removeItem(index) {
			this.appraiserList.splice(index, 1);
		},
		changeUnitPrice(e) {
			if (e) {
				this.form.unit_price_m2 = e;
			} else {
				this.form.unit_price_m2 = "";
			}
		},
		changeSwitchBox(e) {
			if (e) {
				this.form.is_defaults = e;
			} else {
				this.form.is_defaults = false;
			}
		},
		async getAppraiserList(query = {}) {
			this.isLoading = true;
			const filter = {};

			for (let property in this.filter) {
				filter[`${property}`] = this.filter[property];
			}

			try {
				const resp = await AppraisalConstruction.paginate({
					query: {
						page: 1,
						limit: 15,
						...query
					}
				});

				this.appraiserList = [...resp.data.data];
				this.pagination = convertPagination(resp.data);
				this.totalRecord = resp.data.total;
				this.isLoading = false;
			} catch (e) {
				this.isLoading = false;
			}
		},
		async onPageChange(pagination, filters, sorter) {
			this.perPage = pagination.pageSize;
			const sortDesc = replace(sorter.order, "end", "");

			const query = {
				page: pagination.current,
				limit: pagination.pageSize,
				order: sortDesc
			};
			await this.getAppraiserList(query);
		},
		async getDictionary() {
			try {
				const reps = await WareHouse.getDictionaries();
				this.positions = [...reps.data.chuc_vu];
			} catch (err) {
				this.isSubmit = false;
				throw err;
			}
		},
		handleOpenModal(id) {
			this.openModal = true;
			this.id = id;
		},
		async handleDelete() {
			await AppraisalConstruction.deleteAppraiser(this.id);
			await this.getAppraiserList();
			this.$toast.open({
				message: "Xóa CTXD thẩm định thành công",
				type: "success",
				position: "top-right"
			});
		},
		async validateBeforeSubmit() {
			const isValid = await this.$refs.observer.validate();
			if (!isValid) {
				this.$toast.open({
					message: "Vui lòng nhập đầy đủ các trường bắt buộc",
					type: "error",
					position: "top-right"
				});
			} else if (this.form.unit_price_m2 < 0) {
				this.$toast.open({
					message: "Vui lòng không nhập giá trị âm cho đơn giá",
					type: "error",
					position: "top-right"
				});
			} else if (this.getConstructions() && this.form.is_defaults) {
				this.$toast.open({
					message:
						"Đã có 3 đơn vị Xây dựng được chọn mặc định vui lòng đưa trường mặc định về không để lưu",
					type: "error",
					position: "top-right"
				});
			} else {
				this.handleSubmit();
			}
		},
		handleSubmit() {
			this.isSubmit = true;
			let data = this.form;
			this.createAppraiser(data);
		},
		async createAppraiser(data) {
			try {
				const resp = await AppraisalConstruction.create(data);
				if (resp) {
					if (resp.data) {
						this.isSubmit = false;
						this.message = "Tạo mới CTXD thẩm định thành công.";
						this.openNotification = true;
						this.isAddNewItem = false;
						this.form.name = "";
						this.form.address = "";
						this.form.phone_number = "";
						this.form.manager_name = "";
						this.form.unit_price_m2 = "";
						this.form.is_defaults = true;
						await this.getAppraiserList();
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
		async handleUpdateCTDX(data) {
			const resp = await AppraisalConstruction.update(data);
			if (resp.data) {
				this.message = "Cập nhật CTDX thành công.";
				this.openNotification = true;
				this.editingKey = "";
				this.isAddNewItem = false;
				// this.onPageChange(1)
				await this.getAppraiserList();
			} else if (resp.error) {
				this.$toast.open({
					message: resp.error.message,
					type: "error",
					position: "top-right"
				});
			}
		},
		async getAppraiseConstructions() {
			const resp = await Certificate.getAppraiseConstructions();
			this.constructions = [...resp.data];
			this.getConstructions();
		},
		getConstructions() {
			let checkArray = this.appraiserList.filter(item => item.is_defaults);
			if (checkArray && checkArray.length >= 3) {
				return true;
			} else return false;
		}
	},
	beforeMount() {
		this.getAppraiserList();
		this.getDictionary();
		this.getAppraiseConstructions();
	}
};
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
	}
}
.address {
	white-space: normal !important;
	max-width: 100% !important;
	text-transform: none;
}
</style>
