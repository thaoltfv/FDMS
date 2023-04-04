<template>
  <div>
    <div class="container-fluid appraise-container">
      <Tabs :theme="theme" :navAuto="true">
        <!-- <TabItem name="Loại tài sản">
          <TableAssetType/>
        </TabItem> -->
        <TabItem name="Cơ sở thẩm định">
          <TableAppraisalFacility/>
        </TabItem>
        <TabItem name="Nguyên tắc thẩm định">
          <TableRule/>
        </TabItem>
        <TabItem name="Cách tiếp cận">
          <TableCostApproach/>
        </TabItem>
        <TabItem name="Phương pháp thẩm định sử dụng">
          <TableAppraisalMethodUsed/>
        </TabItem>
        <TabItem name="Mục đích thẩm định giá">
          <TableValuationPurposes/>
        </TabItem>
      </Tabs>
    </div>
  </div>
</template>

<script>
import {Tabs, TabItem} from 'vue-material-tabs'
import TableAssetType from './components/TableAssetType'
import TableAppraisalFacility from './components/TableAppraisalFacility'
import TableRule from './components/TableRule'
import TableCostApproach from './components/TableCostApproach'
import TableAppraisalMethodUsed from './components/TableAppraisalMethodUsed'
import TableValuationPurposes from './components/TableValuationPurposes'

export default {
	name: 'Index',
	components: {
		Tabs,
		TabItem,
		TableAssetType,
		TableAppraisalFacility,
		TableRule,
		TableCostApproach,
		TableAppraisalMethodUsed,
		TableValuationPurposes
	},
	data: () => ({
		theme: {
			navItem: '#000000',
			navActiveItem: '#FAA831',
			slider: '#FAA831',
			arrow: '#000000'
		}
	}),
	methods: {
	},
	beforeMount () {
	}
}
</script>

<style scoped>
.appraise-container {
  padding: 0 1.25rem;
}
</style>
