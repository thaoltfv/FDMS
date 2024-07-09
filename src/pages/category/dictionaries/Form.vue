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
				vid="first_id"
				label="Phân cấp 1"
				rules="required"
				:options="optionsFirstGroup"
				@change="changeFirstGroup($event)"
			/>
			<InputCategory
				v-model="form.second_id"
				class="mb-3"
				vid="first_id"
				label="Phân cấp 2"
				rules="required"
				:options="optionsSecondGroup"
				@change="changeSecondGroup($event)"
			/>
			<InputCategory
				v-model="form.third_id"
				class="mb-3"
				vid="third_id"
				label="Phân cấp 3"
				rules="required"
				:options="optionsThirdGroup"
				@change="changeThirdGroup($event)"
			/>
			<InputCategory
				v-model="form.fourth_id"
				class="mb-3"
				vid="first_id"
				label="Phân cấp 4"
				rules="required"
				:options="optionsFourthGroup"
				@change="changeFourthGroup($event)"
			/>
			<InputText
				:key="keyRender"
				v-model="form.description"
				placeholder="Nhập tên chi tiết"
				rules="required"
				class="mb-3"
				vid="description"
				:disabledInput="form.type == 'NHOM_DOI_TAC'"
				label="Tên chi tiết"
			/>
			<!-- <InputText
				v-if="form.type == 'NHOM_DOI_TAC'"
				v-model="form.name_lv_1"
				placeholder="Nhập phân cấp 1"
				rules="required|max:200"
				:max-length="200"
				class="mb-3"
				vid="description"
				label="Phân cấp 1"
				@change="getFullCustomerGroupName"
			/>
			<InputText
				v-if="form.type == 'NHOM_DOI_TAC'"
				v-model="form.name_lv_2"
				placeholder="Nhập phân cấp 2"
				rules="max:200"
				:max-length="200"
				class="mb-3"
				vid="description"
				label="Phân cấp 2"
				@change="getFullCustomerGroupName"
			/>
			<InputText
				v-if="form.type == 'NHOM_DOI_TAC'"
				v-model="form.name_lv_3"
				placeholder="Nhập phân cấp 3"
				rules="max:200"
				:max-length="200"
				class="mb-3"
				vid="description"
				label="Phân cấp 3"
				@change="getFullCustomerGroupName"
			/>
			<InputText
				v-if="form.type == 'NHOM_DOI_TAC'"
				v-model="form.name_lv_4"
				placeholder="Nhập phân cấp 4"
				rules="max:200"
				:max-length="200"
				class="mb-3"
				vid="description"
				label="Phân cấp 4"
				@change="getFullCustomerGroupName"
			/>
			<InputText
				v-model="form.description"
				placeholder="Nhập tên chi tiết"
				rules="required"
				class="mb-3"
				vid="description"
				:disabledInput="form.type == 'NHOM_DOI_TAC'"
				label="Tên chi tiết"
			/> -->
			<InputText
				v-if="form.type == 'LOAI_DAT_CHI_TIET' || form.type == 'CHUC_VU'"
				v-model="form.acronym"
				placeholder="Nhập tên viết tắt"
				rules="required|max:200"
				:max-length="200"
				class="mb-3"
				vid="description"
				label="Tên viết tắt"
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
import Dictionary from "@/models/Dictionary";
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
	computed: {
		optionsFirstGroup() {
			return {
				data: this.firstGroups,
				id: "id",
				key: "name"
			};
		},
		optionsSecondGroup() {
			return {
				data: this.secondGroups,
				id: "id",
				key: "name"
			};
		},
		optionsThirdGroup() {
			return {
				data: this.thirdGroups,
				id: "id",
				key: "name"
			};
		},
		optionsFourthGroup() {
			return {
				data: this.fourthGroups,
				id: "id",
				key: "name"
			};
		}
	},
	data() {
		return {
			firstGroups: [],
			secondGroups: [],
			thirdGroups: [],
			fourthGroups: [],
			form: {
				description: "",
				first_id: "",
				second_id: "",
				third_id: "",
				fourth_id: "",
				type: ""
			},
			isSubmit: false,
			province: null,
			district: null,
			ward: null,
			street: null,
			keyRender: 1000
		};
	},
	created() {
		this.form.type = this.$route.query.type;
		if (this.form.type === "NHOM_DOI_TAC") {
			this.getCustomerGroupFirstList();
		}
	},
	methods: {
		changeFirstGroup(firstId) {
			this.form.second_id = "";
			this.form.third_id = "";
			this.form.fourth_id = "";
			this.getFullCustomerGroupName();
			this.secondGroups = [];
			this.thirdGroups = [];
			this.fourthGroups = [];
			this.secondGroups = this.firstGroups.filter(
				e => e.id === +firstId
			)[0].second_groups;
		},
		changeSecondGroup(secondId) {
			this.form.third_id = "";
			this.form.fourth_id = "";
			this.getFullCustomerGroupName();
			this.thirdGroups = [];
			this.fourthGroups = [];
			this.thirdGroups = this.secondGroups.filter(
				e => e.id === +secondId
			)[0].third_groups;
		},
		changeThirdGroup(thirdId) {
			this.form.fourth_id = "";
			this.getFullCustomerGroupName();
			this.fourthGroups = [];
			this.fourthGroups = this.thirdGroups.filter(
				e => e.id === +thirdId
			)[0].fourth_groups;
		},
		changeFourthGroup(fourthId) {
			this.getFullCustomerGroupName();
		},
		async validateBeforeSubmit() {
			const isValid = await this.$refs.observer.validate();
			if (isValid) {
				this.handleSubmit();
			}
		},
		async getCustomerGroupFirstList() {
			try {
				const resp = await CustomerGroupFirst.getCustomerGroupFirstList();
				this.firstGroups = [...resp.data];
			} catch (err) {
				this.isSubmit = false;
				throw err;
			}
		},
		handleSubmit() {
			this.isSubmit = true;
			let data = this.form;
			this.createDictionary(data);
		},
		getFullCustomerGroupName() {
			this.form.description = "";
			let tempName = "";
			if (this.form.first_id && this.firstGroups.length > 0) {
				const temp = this.firstGroups.filter(e => e.id === this.form.first_id);
				if (temp.length > 0) {
					tempName = temp[0].name;
				}
			}
			if (this.form.second_id && this.secondGroups.length > 0) {
				const temp = this.secondGroups.filter(
					e => e.id === this.form.second_id
				);
				if (temp.length > 0) {
					tempName += "_" + temp[0].name;
				}
			}
			if (this.form.third_id && this.thirdGroups.length > 0) {
				const temp = this.thirdGroups.filter(e => e.id === this.form.third_id);
				if (temp.length > 0) {
					tempName += "_" + temp[0].name;
				}
			}
			if (this.form.fourth_id && this.fourthGroups.length > 0) {
				const temp = this.fourthGroups.filter(
					e => e.id === this.form.fourth_id
				);
				if (temp.length > 0) {
					tempName += "_" + temp[0].name;
				}
			}
			this.form.description = tempName;
		},
		onCancel() {
			window.history.go(-1);
		},

		async createDictionary(data) {
			try {
				const resp = await Dictionary.create(data);

				if (resp && Object.keys(resp).length) {
					window.history.go(-1);
				}
				this.$toast.open({
					message: "Thêm mới thành công",
					type: "success",
					position: "top-right"
				});
			} catch (err) {
				this.isSubmit = false;
				throw err;
			}
		}
	},
	beforeMount() {}
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
.coordinate-img {
	cursor: pointer;
	position: absolute;
	right: 10px;
	top: 30px;
	background: #ffffff;
	height: 2.295rem;
	width: 32px;
	display: grid;
	place-items: center;
	img {
		height: 60%;
	}
}
</style>
