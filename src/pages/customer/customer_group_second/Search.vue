<template>
	<!--Form-->
	<form
		role="search"
		@submit.prevent="search"
		class="search d-flex align-items-end justify-content-between w-100"
	>
		<InputCategory
			v-model="filter.first_id"
			vid="id"
			label="Phân cấp 1"
			class="mr-2 mb-0 input-search"
			:options="optionCustomerGroupFirst"
			@change="search"
			style="min-width: 179px"
		/>
		<div class="d-flex flex-row align-items-lg-end align-items-start input">
			<div class="position-relative" style="margin-right: 1.25rem">
				<InputText
					v-model="filter.search"
					class="input-flash"
					placeholder="Nhập tên phân cấp 2"
					label="Phân cấp 2"
				/>
				<button class="btn-img">
					<img
						class="img"
						src="../../../assets/icons/ic_search.svg"
						alt="search"
					/>
				</button>
			</div>
			<!-- <button style="height: 2.295rem;" class="btn btn-white btn-search text-nowrap index-screen-button"> <img src="../../../assets/icons/ic_search.svg" style="margin-right: 8px" alt="search">Tìm kiếm</button> -->
		</div>
	</form>
</template>

<script>
import CustomerGroupFirst from "@/models/CustomerGroupFirst";
import CustomerGroupSecond from "@/models/CustomerGroupSecond";
import InputCategory from "@/components/Form/InputCategory";
import InputText from "@/components/Form/InputText";
export default {
	name: "Search",

	components: {
		InputCategory,
		InputText
	},

	data() {
		return {
			customerGroupFirstLists: [],
			filter: {
				search: "",
				first_id: ""
			}
		};
	},

	created() {},
	computed: {
		optionCustomerGroupFirst() {
			return {
				data: this.customerGroupFirstLists,
				id: "id",
				key: "name"
			};
		}
	},
	methods: {
		search() {
			this.$emit("filter-changed", this.filter);
		},

		async getCustomerGroupFirstLists() {
			try {
				const resp = await CustomerGroupFirst.getCustomerGroupFirstList();
				this.customerGroupFirstLists = [...resp.data];
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
	beforeMount() {
		this.getCustomerGroupFirstLists();
	}
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
.input-category {
	margin-right: 10px;
	width: 20%;
	label {
		display: none;
	}
}
.search {
	margin-right: 15px;
	@media (max-width: 1020px) {
		margin-bottom: 1rem;
	}
	@media (max-width: 766px) {
		margin-right: 0;
		flex-direction: column;
		margin-bottom: 0;
	}
}
.btn-search {
	@media (max-width: 766px) {
		margin-top: 0;
		width: 50%;
		margin-bottom: 1rem;
	}
}
.input-search {
	@media (max-width: 766px) {
		width: 100%;
		margin-bottom: 1rem !important;
		margin-right: 0 !important;
	}
}
.input-select {
	@media (max-width: 766px) {
		width: 100%;
	}
}
// .btn-img {
//   right: 5px;
//   top: 45px;
// }
</style>
