<template>
  <div class="container-fluid" :style="isMobile() ? {'margin':'0', 'padding': '0'} : {}">
    <Form/>
  </div>
</template>
<script>
import Form from './Form'
export default {
	name: 'Create',
	components: {
		Form
	},
	methods: {
		isMobile() {
			if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
				return true
			} else {
				return false
			}
		}
	}
}
</script>
<style></style>
