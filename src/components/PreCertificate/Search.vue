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
			<b-tooltip
				v-if="!isMobile()"
				placement="rightbottom"
				target="search_input"
			>
				"@" - Tìm theo tên Người tạo <br />
				"$" - Tìm theo tổng giá trị<br />
				"%" - Tìm theo tên Khách hàng
			</b-tooltip>
			<button class="btn-img btn-img__search">
				<img class="img" src="../../assets/icons/ic_search.svg" alt="search" />
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
		isMobile() {
			if (
				/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
					navigator.userAgent
				)
			) {
				return true;
			} else {
				return false;
			}
		},
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
