<template>
  <div>
    <div class="container-fluid appraise-container">
      <Tabs :theme="theme" :navAuto="true">
        <TabItem name="Văn bản pháp luật về thẩm định giá">
          <TableValuation :listProvinces="listProvinces"/>
        </TabItem>
        <TabItem name="Văn bản pháp luật về đất đai">
          <TableLand :listProvinces="listProvinces"/>
        </TabItem>
        <TabItem name="Văn bản pháp luật về xây dựng">
          <TableConstruct :listProvinces="listProvinces"/>
        </TabItem>
        <TabItem name="Văn bản pháp luật của địa phương">
          <TableLocal :listProvinces="listProvinces"/>
        </TabItem>
        <TabItem name="Pháp lý tài sản thẩm định">
          <TableJuridical :listProvinces="listProvinces"/>
        </TabItem>
      </Tabs>
    </div>
  </div>
</template>

<script>
import {Tabs, TabItem} from 'vue-material-tabs'
import TableValuation from './components/TableValuation'
import TableLand from './components/TableLand'
import TableConstruct from './components/TableConstruct'
import TableLocal from './components/TableLocal'
import TableJuridical from './components/TableJuridical'
import District from '../../../models/District'

export default {
	name: 'Index',
	components: {
		Tabs,
		TabItem,
		TableValuation,
		TableLand,
		TableConstruct,
		TableLocal,
		TableJuridical
	},
	data: () => ({
		theme: {
			navItem: '#000000',
			navActiveItem: '#FAA831',
			slider: '#FAA831',
			arrow: '#000000'
		},
		listProvinces: []
	}),
	methods: {
		async getProvinces () {
			try {
				const resp = await District.getProvince()
				this.listProvinces = [...resp.data]
				this.listProvinces.unshift(
					{
						name: 'Tất cả'
					}
				)
			} catch (err) {
				// console.log(err)
				this.isSubmit = false
				throw err
			}
		}
	},
	beforeMount () {
		this.getProvinces()
	}
}
</script>

<style scoped>
.appraise-container {
  padding: 0 1.25rem;
}
</style>
