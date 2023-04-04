<template>
  <div class="container-fluid">
    <Form />
  </div>
</template>
<script>
import Form from './Form'
// import CertificateAsset from '@/models/CertificateAsset'
// import WareHouse from '@/models/WareHouse'
export default {
	name: 'Create-OtherPurpose',
	components: {
		Form
	},
	data () {
		return {

		}
	},
	beforeRouteEnter: async (to, from, next) => {
		// if (to.params['id']) {
		// 	const getDataStep = await CertificateAsset.getDataAllStep(to.params['id'])
		// 	to.meta['step'] = getDataStep.data
		// 	const resp = await WareHouse.getProvince()
		// 	to.meta['provinces'] = [...resp.data]
		// }
		return next()
	}

}
</script>
<style></style>
