<template>
	<div class="pannel card">
		<ValidationObserver
			tag="form"
			ref="observer"
			@submit.prevent="validateBeforeSubmit"
		>
			<InputText
				v-model="form.name"
				placeholder="Nhâp tên Phân cấp 1"
				rules="required|max:200"
				:max-length="200"
				vid="news_title"
				label="Phân cấp 1"
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
import CustomerGroupFirst from "@/models/CustomerGroupFirst";

export default {
	props: {
		detail: {
			type: Object,
			default: () => {}
		}
	},
	name: "Form",
	components: { InputText },
	data() {
		return {
			form: {
				name: ""
			},
			isSubmit: false
		};
	},
	created() {
		if (
			"id" in this.$route.query &&
			this.$route.name === "customer_group_first.edit"
		) {
			this.form = Object.assign(this.form, {
				...this.$route.meta["detail"]
			});
		} else {
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

			if (this.$route.name === "customer_group_first.edit") {
				this.updateCustomerGroup1(data);
			} else {
				this.createCustomerGroup1(data);
			}
		},

		onCancel() {
			return this.$router.push({ name: "customer_group_first.index" });
		},

		async createCustomerGroup1(data) {
			try {
				const resp = await CustomerGroupFirst.create(data);

				if (resp && Object.keys(resp).length) {
					this.$router
						.push({ name: "customer_group_first.index" })
						.catch(_ => {});
				}
				if (resp.data) {
					this.$toast.open({
						message: "Thêm mới Phân cấp 1 thành công",
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

		async updateCustomerGroup1(data) {
			try {
				const resp = new CustomerGroupFirst(data);
				await resp.save();
				await this.$router
					.push({ name: "customer_group_first.index" })
					.catch(_ => {});
				if (resp.data) {
					this.isSubmit = false;
					this.$toast.open({
						message: "Cập nhật Phân cấp 1 thành công",
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
				throw err;
			}
		}
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
