<template>
  <div id="UpdateNews" class="edit-category container-fluid">
    <Form/>
  </div>
</template>

<script>
import Form from './Form'
import User from '@/models/User'
export default {
	name: 'Edit',
	data () {
		return {
			detail: null
		}
	},
	components: {
		Form
	},
	beforeRouteEnter: async (to, from, next) => {
		const staff = await User.find(to.query['id'])
		to.meta['detail'] = staff.data
		return next()
	}
}
</script>
<style scoped lang="scss">
</style>
