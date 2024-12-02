<template>
  <div class="container-fluid">
    <Form/>
  </div>
</template>
<script>
import Form from './Form'
import CertificationBrief from '@/models/CertificationBrief'
export default {
	name: 'Create',
	components: {
		Form
	},
	beforeRouteEnter: async (to, from, next) => {
		const getDataCertificate = await CertificationBrief.getDetailCertificateBrief(to.query['id'])
		to.meta['data'] = getDataCertificate.data
		return next()
	}
}
</script>
<style></style>
