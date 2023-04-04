<template>
  <div class="container-fluid">
    <Form/>
  </div>
</template>
<script>
import Form from './Form'
import CertificateAsset from '@/models/CertificateAsset'
export default {
	name: 'Create',
	components: {
		Form
	},
	beforeRouteEnter: async (to, from, next) => {
		if (to.params['id']) {
			const getDataStep = await CertificateAsset.getAllStepApartment(to.params['id'])
			to.meta['step'] = getDataStep.data
		}
		return next()
	}
}
</script>
<style></style>
