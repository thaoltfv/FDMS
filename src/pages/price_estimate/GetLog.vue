<template>
  <div class="h-100">
    <Map/>
  </div>
</template>
<script>

import getEstimateLog from '@/models/getEstimateLog'
import Map from '@/pages/price_estimate/components/Map'

export default {
	name: 'GetLog',
	components: {
		Map
	},
	data () {
		return {
			years: []
		}
	},
	beforeRouteEnter: async (to, from, next) => {
		const estimate = await getEstimateLog.find(to.query['id'])
		to.meta['detail'] = estimate.data
		return next()
	},
	beforeMount () {
	}
}
</script>
<style lang="scss" scoped>
.title {
  text-align: center;
  font-size: 36px;
  font-weight: 600;
  text-transform: uppercase;
  color: #000000;
  margin-bottom: 40px;
}
</style>
