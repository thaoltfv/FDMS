<template>
	<form class="search" role="search" @submit.prevent="search">
		<div class="position-relative">
			<InputText
				id="search_input"
				v-model="filter.search"
				class="input-flash"
				placeholder="Tìm kiếm nhanh"
				vid="search"
			/>
			<b-tooltip target="search_input">
				"!" - Tìm trên tổng diện tích <br />
				"@" - Tìm người tạo <br />
				"&" - Tìm theo số tờ, thửa <br />
				"$" - tổng giá trị <br />
				"^" - Tìm theo địa chỉ tài sản
			</b-tooltip>
			<button class="btn-img btn-img__search">
				<img class="img" src="@/assets/icons/ic_search.svg" alt="search" />
			</button>
		</div>
	</form>
</template>

<script>
import InputText from "@/components/Form/InputText";
import { BTooltip } from "bootstrap-vue";
export default {
	name: "search",
	components: {
		InputText,
		"b-tooltip": BTooltip
	},
	data() {
		return {
			filter: {
				search: ""
			},
			message: ""
		};
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
	},
	beforeMount() {}
};
</script>
<style scoped lang="scss">
.search {
	@media (max-width: 1440px) {
		width: 100%;
	}
}
// .btn-img{
//   &__search{
//     right: 5px;
//     top: 45px;
//   }
// }
</style>
