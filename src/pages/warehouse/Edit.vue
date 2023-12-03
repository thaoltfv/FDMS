<template>
  <div class="container-fluid" :style="isMobile() ? {'margin':'0', 'padding': '0'} : {}">
    <Form/>
  </div>
</template>
<script>
import Form from './Form'
import WareHouse from '@/models/WareHouse'
export default {
	name: 'Edit',
	components: {
		Form
	},
	beforeRouteEnter: async (to, from, next) => {
		const warehouse = await WareHouse.find(to.query['id'])
		to.meta['detail'] = warehouse.data
		return next()
	},
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
