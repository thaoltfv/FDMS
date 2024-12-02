<template>
	<!--Form-->
	<form
		role="search"
		@submit.prevent="search"
		class="search d-md-flex d-block align-items-end justify-content-end w-100"
	>
		<div class="d-flex flex-row align-items-lg-end align-items-start input">
			<div class="position-relative" style="margin-right: 1.25rem">
				<InputCategory
					v-model="filter.search"
					vid="search"
					label="Tên"
					placeholder="Nhập loại"
					class="mr-2 mb-0 input-flash"
					:options="optionsType"
					@change="search($event)"
				/>
			</div>
		</div>
	</form>
</template>

<script>
import InputText from "@/components/Form/InputText";
import InputCategory from "@/components/Form/InputCategory";
import { DICTIONARIES } from "@/enum/dictionaries.enum";
import Dictionary from "@/models/Dictionary";
export default {
	name: "Search",
	props: ["filter_search"],
	data() {
		return {
			types: DICTIONARIES.filter(e => e.TYPE !== "NHOM_DOI_TAC"),
			filter: {
				search: ""
			}
		};
	},
	mounted() {
		if (
			this.filter_search !== "" &&
			this.filter_search !== undefined &&
			this.filter_search !== null
		) {
			this.filter.search = this.filter_search;
			this.search(this.filter_search);
		}
	},
	created() {},
	components: {
		InputText,
		InputCategory
	},
	computed: {
		optionsType() {
			return {
				data: this.types,
				id: "TYPE",
				key: "NAME"
			};
		}
	},
	methods: {
		changeProvince(provinceId) {
			this.districts = [];
			this.filter.district_id = "";
			this.getDistrictsByProvinceId(+provinceId);
		},
		search(event) {
			this.$emit("filter-changed", this.filter);
		},
		async getProvinces() {
			try {
				const resp = await Dictionary.getProvince();
				this.provinces = [...resp.data];
				this.filter.province_id = 45;
				await this.getDistrictsByProvinceId(this.filter.province_id);
			} catch (err) {
				this.isSubmit = false;
				throw err;
			}
		},
		async getDistrictsByProvinceId(id) {
			try {
				const resp = await Dictionary.getDistrict(id);
				this.districts = [...resp.data];
			} catch (err) {
				this.isSubmit = false;
				throw err;
			}
		},
		reset() {
			for (let property in this.filter) {
				if (this.filter.hasOwnProperty(property)) {
					this.filter[property] = "";
				}
			}
			this.$emit("filter-changed", this.filter);
		}
	},
	beforeMount() {}
};
</script>

<style lang="scss" scoped>
.input {
	@media (max-width: 1023px) {
		width: 100%;
	}
	.input-category {
		@media (max-width: 1023px) {
			margin-bottom: 10px;
			width: 100%;
		}
	}
	.btn-gray {
		@media (max-width: 1023px) {
			width: 100%;
		}
	}
}
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
	margin-right: 5px;
	width: auto;
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
.search {
	margin-right: 15px;
	@media (max-width: 766px) {
		margin-right: 0;
	}
}
.btn-search {
	@media (max-width: 766px) {
		margin-top: 0 !important;
		width: 50%;
	}
}
</style>
