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
					<img src="@/assets/icons/ic_home.svg" :alt="item.title">
				</template>
				<template v-else>{{ $t(`${item.title}`) }}</template>
			</router-link>
		</li>
	</ol>
</template>

<script>
export default {
	name: 'Breadcrumb',

	data () {
		return {
			breadcrumbs: []
		}
	},
	watch: {
		'$route.meta.breadcrumbs': {
			handler: function (newVal) {
				if (newVal) {
					this.breadcrumbs = [
						{ title: 'Trang chủ', name: 'home' },
						...newVal
					]
				} else {
					this.breadcrumbs = [
						{ title: 'Trang chủ', name: 'home' }
					]
				}
			},
			deep: true,
			immediate: true
		}
	},
	methods: {
		isActive (name) {
			return {
				active: name === this.$route.name
			}
		}
	}
}
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
		color: #617F9E !important;
		font-weight: 500 !important;
	}
}

</style>
