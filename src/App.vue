
<template>
  <div id="app">
    <vue-progress-bar/>
      <component class="d-flex " :is="layout" v-if="layout"/>
  </div>
</template>

<script>
export default {
	components: {
		'auth': () => import(/* webpackChunkName: "auth" */ '@/layouts/Auth.vue'),
		'default': () => import(/* webpackChunkName: "default" */ '@/layouts/Default.vue')
	},
	computed: {
		layout () {
			return this.$store.state.layout || 'default'
		}
	}
}
</script>
<style lang="scss" scoped>
  #app {
    min-height: 100%;
    // user-select: none;
  }
  @media (max-width: 1023px) {
    .d-flex{
      display: block!important;
    }
  }
  .nav{
    @media (max-width: 1024px) {
      position: fixed;
    }
  }
</style>
