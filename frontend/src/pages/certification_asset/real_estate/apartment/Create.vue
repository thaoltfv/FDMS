<template>
  <div class="container-fluid" :style="isMobile() ? {'margin':'0', 'padding': '0'} : {}">
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
	},
	methods: {
		isMobile () {
			if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
				return true
			} else {
				return false
			}
		}
	}
}
</script>
<style></style>
