<template>
  <div class="container-fluid">
    <Form/>
  </div>
</template>
<script>
import Form from './Form'
import Certificate from '@/models/Certificate'
export default {
	name: 'Edit',
	components: {
		Form
	},
	beforeRouteEnter: async (to, from, next) => {
		const warehouse = await Certificate.find(to.query['id'])
		to.meta['detail'] = warehouse.data
		return next()
	}
}
</script>
<style></style>
