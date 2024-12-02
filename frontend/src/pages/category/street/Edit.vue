<template>
  <div id="UpdateNews" class="edit-category">
    <Form/>
  </div>
</template>

<script>
import Form from './Form'
import Street from '@/models/Street'
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
		const category = await Street.find(to.query['id'])
		to.meta['detail'] = category.data
		return next()
	}
}
</script>
<style scoped lang="scss">
</style>
