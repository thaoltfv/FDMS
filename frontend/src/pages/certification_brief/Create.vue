<template>
  <div class="container-fluid" :style="isMobile() ? {'margin':'0', 'padding': '0'} : {}">
    <Form/>
  </div>
</template>
<script>
import Form from './Form'
// import AppraiseData from '@/models/AppraiseData'
export default {
	name: 'Create',
	components: {
		Form
	},
	// beforeRouteEnter: async (to, from, next) => {
	//   if (to.params['id']) {
	//     const warehouse = await AppraiseData.find(to.params['id'])
	//     to.meta['detail'] = warehouse.data
	//     return next()
	//   } else return next()
	// }
	methods: {
		isMobile () {
			if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
				return true
			} else {
				return false
			}
		}
	}
}
</script>
<style></style>
