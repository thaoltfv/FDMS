<template>
  <div id="UpdateNews" class="edit-category">
    <Form/>
  </div>
</template>

<script>
import Form from '@/pages/category/district/Form'
import District from '@/models/District'
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
		const category = await District.find(to.query['id'])
		to.meta['detail'] = category.data
		return next()
	}
}
</script>
<style scoped lang="scss">
</style>
