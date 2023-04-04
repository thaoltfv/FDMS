<template>
  <div id="UpdateRole" class="container-fluid">
    <Form/>
  </div>
</template>

<script>
import Role from '@/models/Role'
import Form from './Form'

export default {
	name: 'Edit',

	components: {
		Form
	},

	beforeRouteEnter: async (to, from, next) => {
		// Get Detail
		const respDetail = await Role.find(to.query['id'])
		to.meta['detail'] = respDetail.data
		return next()
	}
}
</script>

<style scoped>

</style>
