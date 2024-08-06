<template>
	<div class="pannel card">
		<ValidationObserver
			tag="form"
			ref="observer"
			@submit.prevent="validateBeforeSubmit"
		>
			<InputCategory
				v-model="form.first_id"
				class="mb-3"
				vid="province"
				label="Phân cấp 1"
				rules="required"
				:options="optionsNewsCategory"
			/>

			<InputText
				v-model="form.name"
				placeholder=" Nhập Phân cấp 2"
				rules="required|max:200"
				:max-length="200"
				vid="news_title"
				label="Phân cấp 2"
			/>
			<div
				class="btn-footer d-md-flex d-block justify-content-end align-items-center"
			>
				<div class="d-md-flex d-block button-contain ">
					<button class="btn btn-white" @click="onCancel" type="button">
						<img
							class="img"
							src="../../../assets/icons/ic_cancel.svg"
							alt="cancel"
						/>
						Trở về
					</button>
					<button
						class="btn btn-white btn-orange text-nowrap"
						:class="{ 'btn-loading disabled': isSubmit }"
						type="submit"
					>
						<img
							class="img"
							src="../../../assets/icons/ic_save.svg"
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
import CustomerGroupSecond from "@/models/CustomerGroupSecond";
import CustomerGroupFirst from "@/models/CustomerGroupFirst";

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
			list: [],
			form: {
				name: "",
				first_id: ""
			},
			isSubmit: false,
			customer_group_second: null,
			province: null
		};
	},
	created() {
		if (
			"id" in this.$route.query &&
			this.$route.name === "customer_group_second.edit"
		) {
			this.form = Object.assign(this.form, {
				...this.$route.meta["detail"]
			});
			// this.province = this.$route.meta['detail'].province
			// if (this.province !== undefined && this.province !== null) {
			// 	this.list.push(this.province)
			// 	this.form.first_id = this.province.id
			// }
		}
	},
	computed: {
		optionsNewsCategory() {
			return {
				data: this.list,
				id: "id",
				key: "name"
			};
		}
	},
	methods: {
		async validateBeforeSubmit() {
			const isValid = await this.$refs.observer.validate();
			if (isValid) {
				this.handleSubmit();
			}
		},

		handleSubmit() {
			this.isSubmit = true;
			let data = this.form;
			if (this.$route.name === "customer_group_second.edit") {
				this.updateCustomerGroupSecond(data);
			} else {
				this.createCustomerGroupSecond(data);
			}
		},
		onCancel() {
			return this.$router.push({ name: "customer_group_second.index" });
		},

		async getCustomerGroupFirstList() {
			try {
				const resp = await CustomerGroupFirst.getCustomerGroupFirstList();
				this.list = [];
				this.list = [...resp.data];
			} catch (err) {
				this.isSubmit = false;
				throw err;
			}
		},

		async createCustomerGroupSecond(data) {
			try {
				const resp = await CustomerGroupSecond.create(data);

				if (resp && Object.keys(resp).length) {
					this.$router
						.push({ name: "customer_group_second.index" })
						.catch(_ => {});
				}
				if (resp.data) {
					this.$toast.open({
						message: "Thêm mới Phân cấp 2 thành công",
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

		async updateCustomerGroupSecond(data) {
			try {
				const resp = new CustomerGroupSecond(data);
				await resp.save();
				await this.$router
					.push({ name: "customer_group_second.index" })
					.catch(_ => {});
				if (resp.data) {
					this.isSubmit = false;
					this.$toast.open({
						message: "Cập nhật Phân cấp 2 thành công",
						type: "success",
						position: "top-right"
					});
				} else if (resp.error) {
					this.$toast.open({
						message: resp.error.message,
						type: "error",
						position: "top-right",
						duration: 3000
					});
					this.isSubmit = false;
				}
			} catch (err) {
				this.isSubmit = false;
			}
		}
	},
	beforeMount() {
		this.getCustomerGroupFirstList();
	}
};
</script>
<style scoped lang="scss">
.pannel {
	background: #ffffff;
	box-shadow: 1px 2px 0 #e5eaee;
	border-radius: 5px;
	padding: 25px;
	margin-bottom: 40px;
	&__table {
		padding: 25px 0;
		border-radius: 5px;
		//box-shadow: 0 0 5px rgba(0,0,0,.1);
	}
	&__input {
		p {
			color: #5a5386;
			font-weight: 600;
		}
	}
}
.form-control {
	width: 100%;
	margin-right: 5px;
	color: #555555;
	border-radius: 5px;

	@media (max-width: 1023px) {
		width: 100%;
	}
	&:focus {
		border-color: #cccccc;
		box-shadow: none;
	}
}
</style>
