<template>
	<!--Form-->
	<form
		role="search"
		@submit.prevent="search"
		class="search d-md-flex d-block align-items-end justify-content-between w-100"
	>
		<div
			class="d-flex align-items-end justify-content-md-start justify-content-between"
		>
			<InputCategory
				v-model="filter.first_id"
				label="Phân cấp 1"
				class="mb-0 input-select"
				:options="optionsFirstGroup"
				style="min-width: 179px; margin-right: 15px"
				vid="id"
				@change="changeFirstGroup($event)"
			/>
			<InputCategory
				v-model="filter.second_id"
				class="mr-2 mb-0 input-select"
				vid="id"
				label="Phân cấp 2"
				style="min-width: 179px"
				:options="optionsSecondGroup"
				@change="search"
			/>
		</div>
		<div class="d-flex flex-row align-items-lg-end align-items-start input">
			<div class="position-relative" style="margin-right: 1.25rem">
				<InputText
					v-model="filter.search"
					class="input-flash"
					placeholder="Nhập tên phân cấp 3"
					vid="id"
					label="Phân cấp 3"
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
			firstGroups: [],
			secondGroups: [],
			filter: {
				search: "",
				first_id: "",
				second_id: ""
			}
		};
	},

	created() {},
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
		}
	},
	methods: {
		changeFirstGroup(firstId) {
			this.secondGroups = [];
			this.filter.second_id = "";
			this.secondGroups = this.firstGroups.filter(
				e => e.id === +firstId
			)[0].second_groups;
			this.search();
		},
		search() {
			this.$emit("filter-changed", this.filter);
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
		this.getCustomerGroupFirstList();
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
	margin-bottom: 47px;
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
	@media (max-width: 767px) {
		margin-right: 0;
	}
}
.btn-search {
	@media (max-width: 1024px) {
		min-width: 50%;
	}
	@media (max-width: 767px) {
		min-width: 35%;
		width: 35%;
	}
}
.input {
	@media (max-width: 1024px) {
		flex-direction: row !important;
		margin-top: 1rem;
		.btn-search {
			min-width: 35%;
		}
	}
	@media (max-width: 767px) {
		flex-direction: row !important;
		margin-top: 1rem;
		justify-content: space-between;
		.btn-search {
			margin-top: 0;
		}
	}
	&-select {
		@media (max-width: 767px) {
			min-width: 45% !important;
			margin-right: 0 !important;
		}
	}
}
// .btn-img {
//   right: 5px;
//   top: 45px;
// }
</style>
