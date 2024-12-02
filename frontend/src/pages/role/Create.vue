<template>
  <div id="CreateRole" class="container-fluid">
    <Form/>
  </div>
</template>

<script>

import Form from '@/pages/role/Form'
export default {
	name: 'role.create',
	components: {Form},
	method: {
		handleSubmit: function () {
		}
	}
}
</script>

<style scoped>

</style>
