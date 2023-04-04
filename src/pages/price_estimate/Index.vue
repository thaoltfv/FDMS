<template>
  <div class="h-100">
    <Map/>
  </div>
</template>
<script>

import Map from '@/pages/price_estimate/components/Map'

export default {
	name: 'index',
	components: {
		Map
	},
	data () {
		return {
			years: []
		}
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
