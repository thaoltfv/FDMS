<template>
	<!--Form-->
	<form
		role="search"
		@submit.prevent="search"
		class="search d-flex align-items-end"
	>
		<div class="d-flex flex-row align-items-lg-end align-items-start input">
			<div class="position-relative" style="margin-right: 1.25rem">
				<InputText
					v-model="filter.search"
					placeholder="Nhập tên Phân cấp 1"
					vid="id"
					class="mr-2 mb-0 input-flash"
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
import InputText from "@/components/Form/InputText";
export default {
	name: "Search",
	data() {
		return {
			filter: {
				search: ""
			}
		};
	},

	created() {},
	components: {
		InputText
	},
	methods: {
		search() {
			this.$emit("filter-changed", this.filter);
		},

		reset() {
			for (let property in this.filter) {
				if (this.filter.hasOwnProperty(property)) {
					this.filter[property] = "";
				}
			}
			this.$emit("filter-changed", this.filter);
		}
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
// .btn-img {
//   right: 5px;
//   top: 45px;
// }
</style>
