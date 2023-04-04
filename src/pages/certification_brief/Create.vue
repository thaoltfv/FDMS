<template>
  <div class="container-fluid">
    <Form/>
  </div>
</template>
<script>
import Form from './Form'
// import AppraiseData from '@/models/AppraiseData'
export default {
	name: 'Create',
	components: {
		Form
	}
	// beforeRouteEnter: async (to, from, next) => {
	//   if (to.params['id']) {
	//     const warehouse = await AppraiseData.find(to.params['id'])
	//     to.meta['detail'] = warehouse.data
	//     return next()
	//   } else return next()
	// }
}
</script>
<style></style>
