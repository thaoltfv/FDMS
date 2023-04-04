<template>
  <div id="CreateNews"
       class="create-news container-fluid">
    <Form/>
  </div>
</template>

<script>
import Form from '@/pages/category/dictionaries/Form'
export default {
	name: 'Create',
	components: {Form},
	methods: {
		beforeRouteEnter: async (to, from, next) => {
			this.basecount = to.params.basecount
			return next()
		}
	}
}
</script>
<style scoped lang="scss">
</style>
