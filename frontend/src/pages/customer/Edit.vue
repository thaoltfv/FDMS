<template>
  <div class="container-fluid">
    <Form/>
  </div>
</template>
<script>
import Form from './Form'
import Customer from '@/models/Customer'
export default {
	name: 'Edit',
	components: {
		Form
	},
	beforeRouteEnter: async (to, from, next) => {
		const customer = await Customer.find(to.query['id'])
		to.meta['detail'] = customer.data
		return next()
	}
}
</script>
<style></style>
