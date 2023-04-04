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
		const getDataStep = await CertificateAsset.getDataAllStep(to.query['id'])
		to.meta['step'] = getDataStep.data
		if (getDataStep.data.step >= 6) {
			const getDataStep7 = await CertificateAsset.getDataStep7(to.query['id'])
			to.meta['step7'] = getDataStep7.data
		}
		return next()
	}
}
</script>
<style></style>
