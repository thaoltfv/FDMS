<template>
	<ValidationObserver
		tag="form"
		ref="observer"
		@submit.prevent="validateBeforeSubmit"
	>
		<div class="card">
			<div class="card-title">
				<div class="d-flex justify-content-between align-items-center">
					<h3 class="title">Thông tin chung</h3>
				</div>
			</div>
			<div class="card-body card-info">
				<div class="container-fluid color_content">
					<div class="row">
						<InputText
							v-model="form.name"
							placeholder="Nhập họ tên"
							rules="required|max:200"
							label="Họ tên"
							:max-length="200"
							class="col-12 col-lg-6 input-content"
						/>
						<InputText
							v-model="form.email"
							placeholder="Nhập email"
							rules="required|max:200"
							type="email"
							label="Email"
							:disabled-input="true"
							autocomplete="off"
							class="col-12 col-lg-6 input-content"
							:class="this.$route.name === 'staff.edit' ? '' : 'd-none'"
						/>
						<InputText
							v-model="form.email"
							placeholder="Nhập email"
							rules="required|max:200"
							type="email"
							label="Email"
							autocomplete="off"
							class="col-12 col-lg-6 input-content"
							:class="this.$route.name === 'staff.edit' ? 'd-none' : ''"
						/>
						<InputText
							v-model="form.phone"
							placeholder="Nhập số điện thoại"
							rules="required|max:11"
							type="number"
							label="Số điện thoại"
							autocomplete="off"
							:max-length="11"
							class="col-12 col-lg-6 input-content"
						/>
						<!-- <InputText
							v-model="form.password"
							placeholder="Nhập password"
							:rules="this.$route.name === 'staff.edit'? '' : 'required|max:200'"
							type="password"
							label="Password"
							:max-length="200"
							autocomplete="off"
							class="col-12 col-lg-6 input-content"
							:class="this.$route.name === 'staff.edit' ? 'd-none' : ''"
						/> -->
						<InputCategory
							v-show="!form.is_guest"
							v-model="form.role_id"
							vid="role"
							label="Phân quyền"
							rules="required"
							:options="optionsRole"
							:disabled="form.is_guest"
							class="col-12 col-lg-6 input-content"
						/>
						<InputText
							v-model="form.address"
							placeholder="Nhập địa chỉ"
							rules="max:200"
							label="Địa chỉ"
							:max-length="300"
							class="col-12 col-lg-6 input-content"
						/>
						<!-- <InputCategory
							v-model="form.customer_group_id"
							class="col-12 col-lg-6 input-content"
							vid="customer_group_id"
							label="Nhóm đối tác"
							:options="optionsCustomerGroup"
						/> -->
					</div>
				</div>
			</div>
		</div>
		<div class="card" v-if="!form.is_guest">
			<div class="card-title">
				<div class="d-flex justify-content-between align-items-center">
					<h3 class="title">Thông tin nhân viên</h3>
				</div>
			</div>
			<div class="card-body card-info">
				<div class="container-fluid color_content">
					<div class="row">
						<InputCategory
							v-if="form.appraiser"
							class="col-12 col-lg-6 input-content"
							v-model="form.appraiser.appraise_position_id"
							:options="optionRoles"
							label="Chức vụ"
							rules="required"
						/>
						<InputText
							v-if="form.appraiser"
							v-model="form.appraiser.appraiser_number"
							placeholder="Nhập số thẩm định viên"
							rules="max:200"
							label="Số thẩm định viên"
							:max-length="200"
							class="col-12 col-lg-6 input-content"
						/>
						<InputCategory
							v-if="form.appraiser"
							v-model="form.appraiser.branch_id"
							class="col-12 col-lg-6 input-content"
							vid="branch"
							label="Chi nhánh"
							rules="required"
							:options="optionsBranch"
						/>
						<InputText
							v-model="form.mailing_address"
							placeholder="Nhập email thông báo"
							rules="max:200"
							type="email"
							label="Email thông báo"
							autocomplete="off"
							:max-length="200"
							class="col-12 col-lg-6 input-content"
						/>
					</div>
				</div>
			</div>
		</div>
		<div class="card">
			<div class="card-title">
				<div class="d-flex justify-content-between align-items-center">
					<h3 class="title">Thông tin khách hàng</h3>
				</div>
			</div>
			<div class="card-body card-info">
				<div class="container-fluid color_content">
					<div class="row">
						<InputCategory
							class="col-12 col-lg-6 input-content"
							v-model="name_lv_1"
							:options="optionsLV1"
							label="Phân cấp 1"
							rules="required"
						/>
						<InputCategory
							class="col-12 col-lg-6 input-content"
							v-model="name_lv_2"
							:options="optionsLV2"
							label="Phân cấp 2"
						/>
						<InputCategory
							class="col-12 col-lg-6 input-content"
							v-model="name_lv_3"
							:options="optionsLV3"
							label="Phân cấp 3"
						/>
						<InputCategory
							class="col-12 col-lg-6 input-content"
							v-model="name_lv_4"
							:options="optionsLV4"
							label="Phân cấp 4"
						/>
					</div>
				</div>
			</div>
		</div>
		<div
			class="btn-footer d-md-flex d-block justify-content-end align-items-center"
		>
			<div class="d-md-flex d-block button-contain ">
				<button class="btn btn-white" @click="onCancel" type="button">
					<img
						class="img"
						src="../../assets/icons/ic_cancel.svg"
						alt="cancel"
					/>
					Trở về
				</button>
				<button
					class="btn btn-white btn-orange text-nowrap"
					:class="{ 'btn-loading disabled': isSubmit }"
					type="submit"
				>
					<img class="img" src="../../assets/icons/ic_save.svg" alt="save" />
					Lưu
				</button>
			</div>
		</div>
	</ValidationObserver>
</template>
<script>
import InputCategory from "@/components/Form/InputCategory";
import InputText from "@/components/Form/InputText";
import User from "@/models/User";
import WareHouse from "@/models/WareHouse";
import { capitalize } from "lodash-es";
import AppraiserCompany from "@/models/AppraiserCompany";

export default {
	props: {
		detail: {
			type: Object,
			default: () => {}
		}
	},
	name: "Form",
	components: {
		InputText,
		InputCategory
	},

	data() {
		return {
			list_total1: [],
			total_account: null,
			customerGroups: [],
			roles: [],
			branches: [],
			positions: [],
			listLV1: [],
			listLV2: [],
			listLV3: [],
			listLV4: [],
			name_lv_1: "",
			name_lv_2: "",
			name_lv_3: "",
			name_lv_4: "",
			form: {
				is_guest: false,
				appraiser: {
					appraise_position_id: "",
					appraiser_number: "",
					branch_id: ""
				},
				role_id: "",
				branch_id: "",
				customer_group_id: "",
				phone: "",
				name: "",
				email: "",
				password: "",
				mailing_address: "",
				address: ""
			},
			isSubmit: false,
			branch: null,
			role_id: ""
		};
	},
	// async mounted() {
	// 	const appraiserCompany = await AppraiserCompany.detail()
	// 	// // console.log(';dsdsad',appraiserCompany )
	// 	this.total_account = appraiserCompany.data.data[0].total_account
	// 	// // console.log('total 1', this.total_account)
	// 	// // console.log('total 1', this.total_account, this.list_total1.length)
	// 	// if (this.total_account && this.list_total1.length){
	// 	// 	// console.log('total 1', this.total_account, this.list.length)
	// 	// }
	// },
	created() {
		// console.log('dsadsađâs',this.$route.meta['detail'])
		if ("is_guest" in this.$route.query) {
			if (this.$route.query.is_guest === "true") {
				this.form.is_guest = true;
			} else {
				this.form.is_guest = false;
			}
		} else {
		}
		if ("id" in this.$route.query && this.$route.name === "staff.edit") {
			this.form = Object.assign(this.form, {
				...this.$route.meta["detail"]
			});
			this.name_lv_1 = this.$route.meta["detail"].name_lv_1;
			this.name_lv_2 = this.$route.meta["detail"].name_lv_2;
			this.name_lv_3 = this.$route.meta["detail"].name_lv_3;
			this.name_lv_4 = this.$route.meta["detail"].name_lv_4;
			this.branch = this.$route.meta["detail"].branch;
			this.role_id = this.$route.meta["detail"].roles[0].id;
		} else {
			let year = new Date().getFullYear();
			this.form.password =
				"ThamDinh" + capitalize(process.env.CLIENT_ENV) + "@" + year;
		}
	},
	computed: {
		optionsLV1() {
			return {
				data: this.listLV1,
				id: "description",
				key: "description"
			};
		},
		optionsLV2() {
			return {
				data: this.listLV2,
				id: "description",
				key: "description"
			};
		},
		optionsLV3() {
			return {
				data: this.listLV3,
				id: "description",
				key: "description"
			};
		},
		optionsLV4() {
			return {
				data: this.listLV4,
				id: "description",
				key: "description"
			};
		},
		optionsCustomerGroup() {
			return {
				data: this.customerGroups,
				id: "id",
				key: "description"
			};
		},
		optionsRole() {
			return {
				data: this.roles,
				id: "id",
				key: "role_name"
			};
		},
		optionsBranch() {
			return {
				data: this.branches,
				id: "id",
				key: "name"
			};
		},
		optionRoles() {
			return {
				data: this.positions,
				id: "id",
				key: "description"
			};
		}
	},
	methods: {
		async getDictionary() {
			try {
				const reps = await WareHouse.getDictionaries();
				this.positions = [...reps.data.chuc_vu];
				this.customerGroups = reps.data.nhom_doi_tac
					? [...reps.data.nhom_doi_tac]
					: [];

				if (this.customerGroups.length > 0) {
					for (let index = 0; index < this.customerGroups.length; index++) {
						const element = this.customerGroups[index];
						console.log(element);
						if (element.name_lv_1) {
							if (this.listLV1.length === 0) {
								this.listLV1.push({ description: element.name_lv_1 });
							} else {
								const temp = this.listLV1.filter(
									e =>
										e.name_lv_1 &&
										e.name_lv_1.toUpperCase() ===
											element.name_lv_1.toUpperCase()
								);
								if (temp.length === 0) {
									this.listLV1.push({ description: element.name_lv_1 });
								}
							}
						}
						if (element.name_lv_1) {
							if (this.listLV2.length === 0) {
								this.listLV2.push({ description: element.name_lv_2 });
							} else {
								const temp = this.listLV2.filter(
									e =>
										e.name_lv_2 &&
										e.name_lv_2.toUpperCase() ===
											element.name_lv_2.toUpperCase()
								);
								if (temp.length === 0) {
									this.listLV2.push({ description: element.name_lv_2 });
								}
							}
						}
						if (element.name_lv_1) {
							if (this.listLV3.length === 0) {
								this.listLV3.push({ description: element.name_lv_3 });
							} else {
								const temp = this.listLV3.filter(
									e =>
										e.name_lv_3 &&
										e.name_lv_3.toUpperCase() ===
											element.name_lv_3.toUpperCase()
								);
								if (temp.length === 0) {
									this.listLV3.push({ description: element.name_lv_3 });
								}
							}
						}
						if (element.name_lv_4) {
							if (this.listLV4.length === 0) {
								this.listLV4.push({ description: element.name_lv_4 });
							} else {
								const temp = this.listLV4.filter(
									e =>
										e.name_lv_4 &&
										e.name_lv_4.toUpperCase() ===
											element.name_lv_4.toUpperCase()
								);
								if (temp.length === 0) {
									this.listLV4.push({ description: element.name_lv_4 });
								}
							}
						}
					}
				}
			} catch (err) {
				this.isSubmit = false;
				throw err;
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
			const role = this.roles.find(role => role.id === data.role_id);
			data.role = role.name;
			data.role = role.role_name;
			if (data.is_guest) {
				data.name_lv_1 = this.name_lv_1;
				data.name_lv_2 = this.name_lv_2;
				data.name_lv_3 = this.name_lv_3;
				data.name_lv_4 = this.name_lv_4;
			}
			// console.log(role);
			if (this.$route.name === "staff.edit") {
				this.updateStaff(data);
			} else {
				this.createStaff(data);
			}
		},
		onCancel() {
			return this.$router.push({ name: "staff.index" });
		},

		async getRoles() {
			try {
				const resp = await User.getRoles();
				this.roles = [...resp.data.data];
				this.form.role_id = this.role_id;
				if (this.form.is_guest) {
					const temp = this.roles.find(
						e => e.role_name.toUpperCase() === "KHÁCH"
					);
					if (temp) {
						this.form.role_id = temp.id;
					}
				}
			} catch (err) {
				this.isSubmit = false;
				throw err;
			}
		},
		async getBranches() {
			try {
				const resp = await User.getBranches();
				this.branches = [...resp.data];
				if (this.branch !== undefined && this.branch !== null) {
					this.form.branch_id = this.branch.id;
				}
			} catch (err) {
				this.isSubmit = false;
				throw err;
			}
		},

		async createStaff(data) {
			try {
				const resp = await User.create(data);

				if (resp && Object.keys(resp).length) {
					this.$router.push({ name: "staff.index" }).catch(_ => {});
				}
				if (resp.data) {
					this.$toast.open({
						message: "Thêm mới nhân viên thành công",
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
				this.$toast.open({
					message: "Email đã tồn tại",
					type: "error",
					position: "top-right"
				});
				throw err;
			}
		},

		async updateStaff(data) {
			try {
				const resp = new User(data);
				await resp.save();
				await this.$router.push({ name: "staff.index" }).catch(_ => {});
				if (resp.data) {
					this.$toast.open({
						message: "Cập nhật nhân viên thành công",
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
				throw err;
			}
		},
		async getStaffsFull(params = {}) {
			this.isLoading = true;
			const filter = {};

			try {
				const resp = await User.paginate({
					query: {
						page: 1,
						limit: 2000000,
						...params,
						...filter
					}
				});

				this.list_total1 = [...resp.data.data];
				const appraiserCompany = await AppraiserCompany.detail();
				// // console.log(';dsdsad',appraiserCompany )
				this.total_account = appraiserCompany.data.data[0].total_account;
				if (
					this.list_total1.filter(function(item) {
						return item.status_user == "active";
					}).length >= this.total_account
				) {
					this.$toast.open({
						message: "Số lượng tại khoản sử dụng đã đạt giới hạn",
						type: "error",
						position: "top-right",
						duration: 3000
					});
					this.$router.push({ name: "staff.index" });
				}
				this.isLoading = false;
			} catch (e) {
				this.isLoading = false;
			}
		}
	},
	beforeMount() {
		this.getRoles();
		this.getBranches();
		this.getDictionary();
		// console.log('this.$route.name',this.$route.name)
		if (this.$route.name === "staff.create") {
			this.getStaffsFull();
		}
	},
	mounted() {
		// console.log(this.form.password)
	}
};
</script>
<style scoped lang="scss">
.input-content {
	margin-bottom: 10px;
}
.card {
	border-radius: 5px;
	box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
	background: #ffffff;
	margin-bottom: 1rem;

	&-footer {
		padding: 15px 24px;
	}

	&-title {
		padding: 15px;
		margin-bottom: 0;
		color: #e8e8e8;
		border-bottom: 2px solid;
		&__img {
			padding: 8px 20px;
		}
		h3 {
			color: #007ec6;
		}
		@media (max-width: 768px) {
			padding: 12px;
		}

		.title {
			font-size: 1.125rem;
			font-weight: 600;
			margin-bottom: 0;
		}
	}
}
form {
	padding-bottom: 35px;
}
</style>
