<template>
  <div id="UpdateNews" class="edit-category">
    <Form/>
  </div>
</template>

<script>
import Form from '@/pages/category/province/Form'
import Category from '@/models/Category'
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
		const category = await Category.find(to.query['id'])
		to.meta['detail'] = category.data
		return next()
	}
}
</script>
<style scoped lang="scss">
</style>
