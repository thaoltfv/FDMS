<template>
  <div>
    <Nav/>
    <div class="page">
      <Header/>
      <Child/>
    </div>
  </div>
</template>

<script>
import '../../node_modules/leaflet/dist/leaflet.js'
import '../../node_modules/vue2-leaflet'
import Nav from '@/components/Nav'
import Header from '@/components/Header'
import Child from '@/components/Child'

export default {
	name: 'Default',

	components: {
		Nav,
		Header,
		Child
	}
}
</script>
