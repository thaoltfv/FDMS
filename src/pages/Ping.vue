<template>
  <div></div>
</template>

<script>
import axios from '@/plugins/axios'
export default {
	methods: {
		async getPing () {
			await axios.get(`${process.env.API_URL}/api/ping`)
		}
	},
	mounted () {
		this.getPing()
	}
}
</script>

<style>

</style>
