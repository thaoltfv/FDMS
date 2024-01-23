<template>
	<div id="wareHouse" class="w-100 position-relative">
		<div class="loading" :class="{ loading__true: isSubmit }">
			<a-spin />
		</div>
		<div
			class="table-detail container__table--detail position-relative"
			:class="localLstConfig.length === 0 ? 'empty-data' : ''"
		>
			<a-form :form="formConfig">
				<a-table
					bordered
					:columns="columns"
					:data-source="localLstConfig"
					:loading="isLoading"
					class="table__import"
					:rowKey="record => record.id"
					:pagination="{
						...pagination
					}"
				>
					<template slot="process_time" slot-scope="text, record, index">
						<a-form-item class="mb-0" v-if="record.editable">
							<div class="row">
								<InputCurrency
									v-model="record.day_process"
									:vid="'day_process' + record.id"
									:max="99999999999999"
									label=""
									:showLabel="false"
									class="col-3"
									:sufix="false"
									@change="record.day_process = $event"
								/>
								Ngày

								<InputCurrency
									v-model="record.hour_process"
									:vid="'hour_process' + record.id"
									:max="99999999999999"
									label=""
									:showLabel="false"
									class="col-3"
									:sufix="false"
									@change="record.hour_process = $event"
								/>
								Giờ

								<InputCurrency
									v-model="record.minute_process"
									:vid="'minute_process' + record.id"
									:max="99999999999999"
									label=""
									:showLabel="false"
									class="col-3"
									:sufix="false"
									@change="record.minute_process = $event"
								/>
								Phút
							</div>
						</a-form-item>
						<p v-else class="mb-0">
							{{
								record.process_time
									? `${record.day_process +
											" ngày " +
											record.hour_process +
											" giờ " +
											record.minute_process +
											" phút"}`
									: ""
							}}
						</p>
					</template>

					<template slot="expire_in" slot-scope="text, record, index">
						<a-form-item class="mb-0" v-if="record.editable">
							<div class="row">
								<InputCurrency
									v-model="record.day_expire"
									:vid="'day_expire' + record.id"
									:max="99999999999999"
									label=""
									:showLabel="false"
									class="col-3"
									:sufix="false"
									@change="record.day_expire = $event"
								/>
								Ngày

								<InputCurrency
									v-model="record.hour_expire"
									:vid="'hour_expire' + record.id"
									:max="99999999999999"
									label=""
									:showLabel="false"
									class="col-3"
									:sufix="false"
									@change="record.hour_expire = $event"
								/>
								Giờ

								<InputCurrency
									v-model="record.minute_expire"
									:vid="'minute_expire' + record.id"
									:max="99999999999999"
									label=""
									:showLabel="false"
									class="col-3"
									:sufix="false"
									@change="record.minute_expire = $event"
								/>
								Phút
							</div>
						</a-form-item>
						<p v-else class="mb-0">
							{{
								record.expire_in
									? `${record.day_expire +
											" ngày " +
											record.hour_expire +
											" giờ " +
											record.minute_expire +
											" phút"}`
									: ""
							}}
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
						</div>
					</template>
				</a-table>
			</a-form>
		</div>

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
import { useWorkFlowConfig } from "@/store/workFlowConfig";
import InputCurrency from "@/components/Form/InputCurrency.vue";
export default {
	name: "Table",
	props: ["lstConfig", "type"],
	components: {
		ModalNotification,
		ModalDelete,
		ModalCancel,
		InputText,
		"a-form": Form,
		"a-form-item": Form.Item,
		"a-input": Input,
		"a-select": Select,
		"a-select-option": Select.Option,
		InputCurrency
	},
	data() {
		return {
			localLstConfig: [],
			isSubmit: false,
			message: "",
			openNotification: false,
			position: [],
			isLoading: false,
			totalRecord: 0,
			pagination: {
				pageSize: 1000
			},
			formConfig: this.$form.createForm(this, {}),
			form: {
				id: "",
				name: "",
				process_time: 0,
				expire_in: 0,
				day_process: 0,
				hour_process: 0,
				minute_process: 0,
				day_expire: 0,
				hour_expire: 0,
				minute_expire: 0
			},
			add: false,
			edit: false,
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
					title: "Tên",
					align: "left",
					scopedSlots: { customRender: "description" },
					dataIndex: "description",
					width: "28%"
				},
				{
					title: "Thời gian thực hiện",
					align: "left",
					scopedSlots: { customRender: "process_time" },
					dataIndex: "process_time",
					width: "30%"
				},

				{
					title: "Thời gian trước khi warning",
					align: "left",
					scopedSlots: { customRender: "expire_in" },
					dataIndex: "expire_in",
					width: "30%"
				},
				{
					title: "",
					scopedSlots: { customRender: "action" },
					align: "center",
					width: "5%"
				}
			];
		}
	},
	created() {
		this.localLstConfig = this.lstConfig;
		const permission = this.$store.getters.currentPermissions;
		// fix_permission
		permission.forEach(value => {
			if (value === "EDIT_ROLE") {
				this.edit = true;
			}
		});
	},
	setup() {
		const workFlowConfigStore = useWorkFlowConfig();
		return { workFlowConfigStore };
	},
	methods: {
		editTable(key) {
			if (this.onValidateTable()) {
				this.add = false;
				this.id = key;
				this.openModalCancel = true;
				this.activePending = "edit";
			} else {
				const newData = [...this.localLstConfig];
				const target = newData.filter(item => key === item.id)[0];
				this.editingKey = key;
				if (target) {
					target.editable = true;
					this.localLstConfig = newData;
					this.setFieldsValue(target);
				}
			}
		},
		setFieldsValue(target) {
			setTimeout(() => {
				this.formConfig.setFieldsValue(target);
			}, 200);
		},
		onValidateTable() {
			let newData = [...this.localLstConfig];
			let target = newData.find(item => item.editable);
			return !!target;
		},
		async handleSubmitForm(event) {
			event.preventDefault();
			let newData = [...this.localLstConfig];
			const lstString = [
				"day_process",
				"hour_process",
				"minute_process",
				"day_expire",
				"hour_expire",
				"minute_expire"
			];
			for (let index = 0; index < newData.length; index++) {
				const item = newData[index];
				lstString.forEach(itemLstString => {
					if (!(item[itemLstString] > 0)) {
						item[itemLstString] = 0;
					}
				});
				const totalMinutes =
					item.day_process * 24 * 60 +
					item.hour_process * 60 +
					item.minute_process * 1;
				item.process_time = totalMinutes;

				const totalMinutes2 =
					item.day_expire * 24 * 60 +
					item.hour_expire * 60 +
					item.minute_expire * 1;
				item.expire_in = totalMinutes2;

				if (item.expire_in > item.process_time) {
					this.$toast.open({
						message:
							"Thời gian trước khi warning phải nhỏ hơn thời gian thực hiện",
						type: "error",
						position: "top-right"
					});
					return;
				}
			}
			const resp = await this.workFlowConfigStore.updateConfig(
				this.type,
				newData,
				this.$toast
			);
			if (resp.data) {
				this.message = "Cập nhật workflow thành công.";
				await this.$emit("resfreshData");
				this.openNotification = true;
				this.editingKey = "";
			} else if (resp.error) {
				this.$toast.open({
					message: resp.error.message,
					type: "error",
					position: "top-right"
				});
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

		handleCancel() {
			const newData = [...this.localLstConfig];
			switch (this.activePending) {
				case "edit":
					this.isAddNewItem = false;
					const targetEdit = newData.find(item => item.id === this.id);
					console.log("handleCanceledit");
					if (targetEdit) {
						this.editingKey = "";
						targetEdit.editable = true;
						this.localLstConfig = newData;
						this.setFieldsValue(targetEdit);
					}
					break;
				default:
					const target = newData.find(item => item.editable);
					console.log("handleCanceldefault", target);
					if (target) {
						target.day_process = target.day_process_original;
						target.hour_process = target.hour_process_original;
						target.minute_process = target.minute_process_original;
						target.day_expire = target.day_expire_original;
						target.hour_expire = target.hour_expire_original;
						target.minute_expire = target.minute_expire_original;
						this.editingKey = "";
						target.editable = false;
						this.localLstConfig = newData;
					}
					break;
			}
		}
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
