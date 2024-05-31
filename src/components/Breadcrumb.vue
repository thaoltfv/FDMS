<template>
	<ol
		v-if="breadcrumbs.length"
		class="breadcrumb breadcrumb-arrows"
		aria-label="breadcrumbs"
	>
		<li
			class="breadcrumb-item"
			v-for="(item, index) in breadcrumbs"
			:key="index"
			:class="isActive(item.name)"
		>
			<router-link :to="{ name: item.name }" tag="a" class="d-flex">
				<template v-if="item.name === 'home'">
					<img src="@/assets/icons/ic_home.svg" :alt="item.title" />
				</template>
				<template v-else>{{ $t(`${item.title}`) }}</template>
			</router-link>
		</li>
	</ol>
</template>

<script>
import store from "@/store";
export default {
	name: "Breadcrumb",

	data() {
		return {
			breadcrumbs: []
		};
	},
	watch: {
		"$route.meta.breadcrumbs": {
			handler: function(newVal) {
				const isGuest = true;
				if (store.getters.profile !== null) {
					const profile = store.getters.profile.data.user;
					if (
						profile &&
						profile.roles &&
						(profile.roles[0].role_name.toUpperCase() === "KHÁCH" ||
							profile.roles[0].role_name.toUpperCase() === "KHÁCH HÀNG" ||
							profile.roles[0].role_name.toUpperCase() === "ĐỐI TÁC")
					) {
						this.breadcrumbs = [
							{ title: "Trang chủ", name: "home" },
							...newVal
						];
					}
				}
				if (!isGuest) {
					this.breadcrumbs.push({ title: "Trang chủ", name: "home" });
				}
				if (newVal) {
					this.breadcrumbs = [...newVal];
				} else {
					// this.breadcrumbs = [{ title: "Trang chủ", name: "home" }];
				}
			},
			deep: true,
			immediate: true
		}
	},
	methods: {
		isActive(name) {
			return {
				active: name === this.$route.name
			};
		}
	}
};
</script>

<style scoped lang="scss">
.breadcrumb {
	justify-content: flex-end;
	align-items: center;

	@media (max-width: 768px) {
		justify-content: flex-start;
		flex-wrap: nowrap;
		white-space: nowrap;
		overflow: auto;
	}

	.breadcrumb-item a {
		color: #617f9e !important;
		font-weight: 500 !important;
	}
}
</style>
