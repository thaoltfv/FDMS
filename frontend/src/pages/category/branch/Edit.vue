<template>
  <div id="UpdateNews" class="edit-category">
    <Form/>
  </div>
</template>

<script>
import Form from './Form'
import Branch from '@/models/Branch'
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
		const branch = await Branch.find(to.query['id'])
		to.meta['detail'] = branch.data
		return next()
	}
}
</script>
<style scoped lang="scss">
</style>
