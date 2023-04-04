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
			const getDataStep = await CertificateAsset.getDataAllStep(to.params['id'])
			to.meta['step'] = getDataStep.data
		}
		return next()
	},
	created () {
		if ('id' in this.$route.params && this.$route.name === 'certification_asset.create') {
			this.open_select_type = false
		} else this.open_select_type = true
	}
}
</script>
<style></style>
