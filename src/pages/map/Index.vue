<template>
  <div :style="isMobile() ? {'margin':'0'} : {}">
    <Map/>
  </div>
</template>
<script>
import Map from '@/pages/map/components/Map'
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
