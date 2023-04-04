<template>
  <div class="container-fluid">
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
	}
}
</script>
<style></style>
