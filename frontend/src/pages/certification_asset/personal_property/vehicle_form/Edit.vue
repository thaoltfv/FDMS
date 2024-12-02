<template>
  <div class="container-fluid">
    <Form/>
  </div>
</template>
<script>
import Form from './Form'
import CertificateAsset from '@/models/CertificateAsset'
export default {
	name: 'Edit',
	components: {
		Form
	},
	beforeRouteEnter: async (to, from, next) => {
		const getDataStep = await CertificateAsset.getAllStepVehicle(to.query['id'], to.query['asset_type_id'])
		to.meta['step'] = getDataStep.data
		return next()
	}
}
</script>
<style></style>
