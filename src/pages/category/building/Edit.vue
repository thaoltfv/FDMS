<template>
  <div id="UpdateNews" class="edit-category">
    <Form/>
  </div>
</template>

<script>
import Form from '@/pages/category/building/Form'
import Building from '@/models/Building'
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
		const building = await Building.find(to.query['id'])
		to.meta['detail'] = building.data
		return next()
	}
}
</script>
<style scoped lang="scss">
</style>
