
<template>
  <div id="app">
    <vue-progress-bar/>
      <component class="d-flex " :is="layout" v-if="layout"/>
  </div>
</template>

<script>
// import gtm from '@/plugins/gtm'
import $ from 'jquery'
export default {
	components: {
		'auth': () => import(/* webpackChunkName: "auth" */ '@/layouts/Auth.vue'),
		'default': () => import(/* webpackChunkName: "default" */ '@/layouts/Default.vue')
	},
	computed: {
		layout () {
			return this.$store.state.layout || 'default'
		}
	},
  created() {
    // // console.log('gtm', gtm)
    // gtm.enable(true)
  },
  mounted () {
    $('head').append(`<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-P5Q3F6R6');<\/script>`)
  $('body').append(`<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-P5Q3F6R6"
height="0" width="0" style="display:none;visibility:hidden"><\/iframe><\/noscript>`)
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
