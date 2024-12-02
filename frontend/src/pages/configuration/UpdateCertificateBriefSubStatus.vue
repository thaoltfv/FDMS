<template>
</template>

<script>
import Vue from 'vue'
import CertificateBrief from '@/models/CertificationBrief'
const jsonConfig = require('../../../config/workflow.json')

export default {
	name: 'UpdateCertificateBriefSubStatus.vue',
	data () {
		return {
			jsonConfig: jsonConfig
		}
	},
	methods: {
		async updateSubStatusFromConfig () {
			const resp = await CertificateBrief.updateSubStatusFromConfig(this.jsonConfig)
			if (resp.data) {
				this.$toast.open({
					message: `${resp.data.message}`,
					type: 'success',
					position: 'top-right'
				})
			} else {
				this.$toast.open({
					message: `${resp.error.message}`,
					type: 'error',
					position: 'top-right'
				})
			}
		}
	},
	beforeMount () {
		this.updateSubStatusFromConfig()
	}
}
</script>
<style scoped>

</style>
