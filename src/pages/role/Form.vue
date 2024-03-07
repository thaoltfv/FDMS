<template>
	<div class="container__role">
		<ValidationObserver
			tag="form"
			ref="observer"
			class="role-section"
			@submit.prevent="validateBeforeSubmit"
		>
			<!--Card Field-->
			<div class="card mb-3">
				<div class="card-body">
					<div class="row">
						<!--Display name role-->
						<InputText
							v-model="form.role_name"
							class="col-md-6 mb-3 mb-sm-0"
							rules="required|max:100"
							:max-length="100"
							vid="role_name"
							label="Tên hiển thị"
							placeholder="Nhập tên hiển thị"
						/>
					</div>
					<p class="mt-2 text-warning">
						! Thay đổi phân quyền có thể ảnh hưởng đến các chức năng khác
					</p>
				</div>
			</div>
			<div class="errors" style="color: red" v-if="error">
				{{ error }}
			</div>
			<div class="card mb-3">
				<table class="table permission-table" ref="table">
					<thead>
						<tr>
							<th>Danh sách màn hình</th>
							<th>
								<div class="d-flex justify-content-start align-items-center">
									<label class="input-checkbox m-0 mr-2">
										<input
											type="checkbox"
											@click="selectAll('VIEW')"
											v-model="viewSelected"
										/>
										<span class="check-mark"></span>
									</label>
									<p class="mb-0">Xem</p>
								</div>
							</th>
							<th>
								<div class="d-flex justify-content-start align-items-center">
									<label class="input-checkbox m-0 mr-2">
										<input
											type="checkbox"
											@click="selectAll('ADD')"
											v-model="addSelected"
										/>
										<span class="check-mark"></span>
									</label>
									<p class="mb-0">Thêm</p>
								</div>
							</th>
							<th class="text-center">
								<div class="d-flex justify-content-start align-items-center">
									<label class="input-checkbox m-0 mr-2">
										<input
											type="checkbox"
											@click="selectAll('EDIT')"
											v-model="editSelected"
										/>
										<span class="check-mark"></span>
									</label>
									<p class="mb-0">Sửa</p>
								</div>
							</th>
							<th class="text-center">
								<div class="d-flex justify-content-start align-items-center">
									<label class="input-checkbox m-0 mr-2">
										<input
											type="checkbox"
											@click="selectAll('DELETE')"
											v-model="deleteSelected"
										/>
										<span class="check-mark"></span>
									</label>
									<p class="mb-0">Xóa</p>
								</div>
							</th>
							<th class="text-center">
								<div class="d-flex justify-content-start align-items-center">
									<label class="input-checkbox m-0 mr-2">
										<input
											type="checkbox"
											@click="selectAll('ACCEPT')"
											v-model="acceptSelected"
										/>
										<span class="check-mark"></span>
									</label>
									<p class="mb-0">Duyệt</p>
								</div>
							</th>
							<th class="text-center">
								<div class="d-flex justify-content-start align-items-center">
									<label class="input-checkbox m-0 mr-2">
										<input
											type="checkbox"
											@click="selectAll('EXPORT')"
											v-model="exportSelected"
										/>
										<span class="check-mark"></span>
									</label>
									<p class="mb-0">Xuất excel</p>
								</div>
							</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="item of screens" :key="item.name">
							<td>{{ item.name_vietsub }}</td>
							<td>
								<label class="input-checkbox">
									<input
										class="cursor-pointer"
										v-model="viewPermissions"
										checked
										@click="select('VIEW')"
										:value="`VIEW_${item.name}`"
										@change="change('VIEW')"
										type="checkbox"
									/>
									<span class="check-mark"></span>
								</label>
							</td>
							<td>
								<label class="input-checkbox">
									<input
										class="cursor-pointer"
										v-model="addPermissions"
										@click="select('ADD')"
										:value="`ADD_${item.name}`"
										@change="change('ADD')"
										type="checkbox"
									/>
									<span class="check-mark"></span>
								</label>
							</td>
							<td>
								<label class="input-checkbox">
									<input
										class="cursor-pointer"
										v-model="editPermissions"
										@click="select('EDIT')"
										:value="`EDIT_${item.name}`"
										@change="change('EDIT')"
										type="checkbox"
									/>
									<span class="check-mark"></span>
								</label>
							</td>
							<td>
								<label class="input-checkbox">
									<input
										class="cursor-pointer"
										v-model="deletePermissions"
										@click="select('DELETE')"
										:value="`DELETE_${item.name}`"
										@change="change('DELETE')"
										type="checkbox"
									/>
									<span class="check-mark"></span>
								</label>
							</td>
							<td>
								<label class="input-checkbox">
									<input
										class="cursor-pointer"
										v-model="acceptPermissions"
										@click="select('ACCEPT')"
										:value="`ACCEPT_${item.name}`"
										@change="change('ACCEPT')"
										type="checkbox"
									/>
									<span class="check-mark"></span>
								</label>
							</td>
							<td>
								<label class="input-checkbox">
									<input
										class="cursor-pointer"
										v-model="exportPermissions"
										@click="select('EXPORT')"
										:value="`EXPORT_${item.name}`"
										@change="change('EXPORT')"
										type="checkbox"
									/>
									<span class="check-mark"></span>
								</label>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div
				class="btn-footer d-md-flex d-block justify-content-end align-items-center"
			>
				<div class="d-lg-flex d-block button-contain">
					<button
						@click="onCancel"
						type="button"
						class="btn btn-white text-nowrap"
						:class="{ disabled: isSubmit }"
					>
						<img
							src="../../assets/icons/ic_cancel.svg"
							style="margin-right: 12px"
							alt="save"
						/>
						Trở lại
					</button>
					<button
						class="btn btn-white btn-orange text-nowrap"
						:class="{ 'btn-loading disabled': isSubmit }"
						type="submit"
					>
						<img
							src="../../assets/icons/ic_save.svg"
							:class="{ 'd-none': isSubmit }"
							style="margin-right: 12px"
							alt="save"
						/>
						Lưu
					</button>
				</div>
			</div>
		</ValidationObserver>
	</div>
</template>

<script>
import InputText from "@/components/Form/InputText";
import InputCategory from "@/components/Form/InputCategory";
import InputTextarea from "@/components/Form/InputTextarea";
import Role from "@/models/Role";
import InputSwitch from "@/components/Form/InputSwitch";
import Permission from "@/models/Permission";
export default {
	name: "Form",

	components: {
		InputSwitch,
		InputTextarea,
		InputCategory,
		InputText
	},

	data() {
		return {
			isLoading: false,
			screens: [],
			permissions: [],
			form: {
				name: "",
				role_name: ""
			},
			error: null,
			isSubmit: false,
			selected: [],
			viewSelected: false,
			addSelected: false,
			editSelected: false,
			deleteSelected: false,
			acceptSelected: false,
			exportSelected: false,
			viewPermissions: [],
			addPermissions: [],
			editPermissions: [],
			deletePermissions: [],
			acceptPermissions: [],
			exportPermissions: []
		};
	},
	created() {
		if ("id" in this.$route.query && this.$route.name === "role.edit") {
			this.form = Object.assign(this.form, {
				...this.$route.meta["detail"]
			});
			this.permissions = this.$route.meta["detail"].permissions;
		} else {
		}
	},
	mounted() {
		if (this.$route.name === "role.create") {
			this.form.name = this.randomRoleName(6);
		}
	},
	methods: {
		randomRoleName(name) {
			let text = " ";
			let chars = "abcdefghijklmnopqrstuvwxyz";

			for (let i = 0; i < name; i++) {
				text += chars.charAt(Math.floor(Math.random() * chars.length));
			}

			return text;
		},
		selectAll(permission) {
			switch (permission) {
				case "VIEW":
					this.viewPermissions = [];
					if (!this.viewSelected) {
						this.screens.forEach(screen => {
							this.viewPermissions.push(permission + "_" + screen.name);
						});
					}
					break;
				case "ADD":
					this.addPermissions = [];
					if (!this.addSelected) {
						this.screens.forEach(screen => {
							this.addPermissions.push(permission + "_" + screen.name);
						});
					}
					if (this.addPermissions.length === this.screens.length) {
						this.addSelected = true;
					}
					break;
				case "EDIT":
					this.editPermissions = [];
					if (!this.editSelected) {
						this.screens.forEach(screen => {
							this.editPermissions.push(permission + "_" + screen.name);
						});
					}
					if (this.editPermissions.length === this.screens.length) {
						this.editSelected = true;
					}
					break;
				case "DELETE":
					this.deletePermissions = [];
					if (!this.deleteSelected) {
						this.screens.forEach(screen => {
							this.deletePermissions.push(permission + "_" + screen.name);
						});
					}
					if (this.deletePermissions.length === this.screens.length) {
						this.deleteSelected = true;
					}
					break;
				case "ACCEPT":
					this.acceptPermissions = [];
					if (!this.acceptSelected) {
						this.screens.forEach(screen => {
							this.acceptPermissions.push(permission + "_" + screen.name);
						});
					}
					if (this.acceptPermissions.length === this.screens.length) {
						this.acceptSelected = true;
					}
					break;
				case "EXPORT":
					this.exportPermissions = [];
					if (!this.exportSelected) {
						this.screens.forEach(screen => {
							this.exportPermissions.push(permission + "_" + screen.name);
						});
					}
					if (this.exportPermissions.length === this.screens.length) {
						this.exportSelected = true;
					}
					break;
			}
		},
		select(permission) {
			switch (permission) {
				case "VIEW":
					this.viewSelected = false;
					break;
				case "ADD":
					this.addSelected = false;
					break;
				case "EDIT":
					this.editSelected = false;
					break;
				case "DELETE":
					this.deleteSelected = false;
					break;
				case "ACCEPT":
					this.acceptSelected = false;
					break;
				case "EXPORT":
					this.exportSelected = false;
					break;
			}
		},
		change(permission) {
			switch (permission) {
				case "VIEW":
					if (this.viewPermissions.length === this.screens.length) {
						this.viewSelected = true;
					}
					break;
				case "ADD":
					if (this.addPermissions.length === this.screens.length) {
						this.addSelected = true;
					}
					break;
				case "EDIT":
					if (this.editPermissions.length === this.screens.length) {
						this.editSelected = true;
					}
					break;
				case "DELETE":
					if (this.deletePermissions.length === this.screens.length) {
						this.deleteSelected = true;
					}
					break;
				case "ACCEPT":
					if (this.acceptPermissions.length === this.screens.length) {
						this.accpetSelected = true;
					}
					break;
				case "EXPORT":
					if (this.exportPermissions.length === this.screens.length) {
						this.exportSelected = true;
					}
					break;
			}
		},
		async validateBeforeSubmit() {
			const isValid = await this.$refs.observer.validate();
			if (isValid) {
				this.handleSubmit();
			}
		},

		handleSubmit() {
			this.isSubmit = true;
			let data = this.form;
			let permissions = [];
			if (this.viewPermissions.length > 0) {
				this.viewPermissions.forEach(value => {
					permissions.push(value);
				});
			}
			if (this.addPermissions.length > 0) {
				this.addPermissions.forEach(value => {
					permissions.push(value);
				});
			}
			if (this.editPermissions.length > 0) {
				this.editPermissions.forEach(value => {
					permissions.push(value);
				});
			}
			if (this.deletePermissions.length > 0) {
				this.deletePermissions.forEach(value => {
					permissions.push(value);
				});
			}
			if (this.acceptPermissions.length > 0) {
				this.acceptPermissions.forEach(value => {
					permissions.push(value);
				});
			}
			if (this.exportPermissions.length > 0) {
				this.exportPermissions.forEach(value => {
					permissions.push(value);
				});
			}
			data.permissions = permissions;
			if (this.$route.name === "role.edit") {
				this.updateRole(data);
			} else {
				this.createRole(data);
			}
		},

		async createRole(data) {
			try {
				const resp = await Role.create(data);
				if (resp && Object.keys(resp).length) {
					this.$router.push({ name: "role.index" }).catch(_ => {});
				}
				if (resp.data) {
					this.$toast.open({
						message: "Thêm mới phân quyên thành công",
						type: "success",
						position: "top-right"
					});
				} else if (resp.error) {
					this.$toast.open({
						message: resp.error.message,
						type: "error",
						position: "top-right"
					});
				}
			} catch (err) {
				this.isSubmit = false;
				this.error = "Vui lòng chọn phân quyền";
				throw err;
			}
		},

		async updateRole(data) {
			try {
				const resp = new Role(data);
				await resp.save();
				await this.$router.push({ name: "role.index" }).catch(_ => {});
				if (resp.data) {
					this.$toast.open({
						message: "Cập nhật phân quyền thành công",
						type: "success",
						position: "top-right"
					});
				} else if (resp.data.error) {
					this.$toast.open({
						message: resp.error.message,
						type: "error",
						position: "top-right",
						duration: 3000
					});
				}
			} catch (err) {
				this.isSubmit = false;
			}
		},
		async getScreens() {
			try {
				const resp = await Permission.getPermissionScreen();
				const response = [...resp.data];
				this.screens = [];
				response.forEach(response => {
					const screen = [];
					screen.name = response;
					if (screen.name == "DASHBOARD") {
						screen.name_vietsub = "BÁO CÁO QUẢN TRỊ";
					}
					if (screen.name == "USER") {
						screen.name_vietsub = "NHÂN VIÊN";
					}
					if (screen.name == "ROLE") {
						screen.name_vietsub = "HRM";
					}
					if (screen.name == "MAP") {
						screen.name_vietsub = "BẢN ĐỒ";
					}
					if (screen.name == "PRICE") {
						screen.name_vietsub = "KHO GIÁ";
					}
					if (screen.name == "CATEGORY") {
						screen.name_vietsub = "DANH MỤC DỮ LIỆU";
					}
					if (screen.name == "CUSTOMER") {
						screen.name_vietsub = "QUẢN LÝ ĐỐI TÁC";
					}
					if (screen.name == "CERTIFICATE_ASSET") {
						screen.name_vietsub = "TÀI SẢN THẨM ĐỊNH";
					}
					if (screen.name == "CERTIFICATE_BRIEF") {
						screen.name_vietsub = "HỒ SƠ THẨM ĐỊNH";
					}
					if (screen.name == "PRE_CERTIFICATE") {
						screen.name_vietsub = "YÊU CẦU SƠ BỘ";
					}
					if (screen.name == "ACCOUNTING") {
						screen.name_vietsub = "THANH TOÁN";
					}
					if (this.permissions.length > 0) {
						this.permissions.forEach(permission => {
							const perScreen = permission.name.split(/^([A-Z]+)/g);
							if (perScreen[2].split(/^_/g)[1] === response) {
								switch (perScreen[1]) {
									case "VIEW":
										this.viewPermissions.push(permission.name);
										break;
									case "ADD":
										this.addPermissions.push(permission.name);
										break;
									case "EDIT":
										this.editPermissions.push(permission.name);
										break;
									case "DELETE":
										this.deletePermissions.push(permission.name);
										break;
									case "ACCEPT":
										this.acceptPermissions.push(permission.name);
										break;
									case "EXPORT":
										this.exportPermissions.push(permission.name);
										break;
								}
							}
						});
					}
					this.screens.push(screen);
				});
				// console.log('this.screens',this.screens)
				if (this.viewPermissions.length === this.screens.length) {
					this.viewSelected = true;
				}
				if (this.addPermissions.length === this.screens.length) {
					this.addSelected = true;
				}
				if (this.editPermissions.length === this.screens.length) {
					this.editSelected = true;
				}
				if (this.deletePermissions.length === this.screens.length) {
					this.deleteSelected = true;
				}
				if (this.acceptPermissions.length === this.screens.length) {
					this.acceptSelected = true;
				}
				if (this.exportPermissions.length === this.screens.length) {
					this.exportSelected = true;
				}
			} catch (err) {
				this.isSubmit = false;
				throw err;
			}
		},
		onCancel() {
			return this.$router.push({ name: "role.index" });
		}
	},
	beforeMount() {
		this.getScreens();
	}
};
</script>

<style lang="scss" scoped>
.container__role {
	margin-bottom: 80px;
	@media (max-width: 767px) {
		margin-bottom: 130px;
	}
}
.card {
	overflow-x: auto;
	overflow-y: hidden;
}
.permission-table {
	margin-bottom: 0;
	td,
	th {
		padding: 1rem 2rem;
	}

	tbody {
		tr {
			border-bottom: 1px solid #c4c4c4;

			&:nth-last-child(1) {
				border-bottom: none;
			}
		}
		td {
			border-bottom: none;
		}
	}
}
.input-checkbox {
	position: relative;
	display: flex;
	align-items: center;
	justify-content: center;
	width: 20px;
	height: 20px;
	margin-bottom: 0;
	input {
		width: 20px;
		height: 20px;
		position: absolute;
		cursor: pointer;
		opacity: 0;
		&:checked {
			& ~ .check-mark {
				background-color: #faa831;
				&:after {
					display: block;
				}
			}
		}
	}
	.check-mark {
		position: absolute;
		top: 0;
		left: 0;
		cursor: pointer;
		width: 20px;
		height: 20px;
		background-color: #ffffff;
		border: 1px solid #faa831;
		border-radius: 4px;
		&:after {
			content: "";
			position: absolute;
			display: none;
			left: 50%;
			top: 50%;
			width: 5px;
			height: 10px;
			border: solid #ffffff;
			border-width: 0 3px 3px 0;
			-webkit-transform: rotate(45deg) translate(-125%, -25%);
			-ms-transform: rotate(45deg) translate(-125%, -25%);
			transform: rotate(45deg) translate(-125%, -25%);
		}
	}
}
</style>
