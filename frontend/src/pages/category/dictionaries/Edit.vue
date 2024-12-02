<template>
  <div id="UpdateNews" class="edit-category">
    <Form/>
  </div>
</template>

<script>
import Form from '@/pages/category/dictionaries/Form'
import Dictionary from '@/models/Dictionary'
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
		const apartment = await Dictionary.find(to.query['id'])
		to.meta['detail'] = apartment.data
		return next()
	}
}
</script>
<style scoped lang="scss">
</style>
