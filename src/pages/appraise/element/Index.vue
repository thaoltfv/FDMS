<template>
  <div>
    <div class="container-fluid appraise-container">
      <Tabs :theme="theme" :navAuto="true">
        <TabItem name="PHÁP LÝ">
          <Table :listAppraise="listAppraiseJuridical" type="PHAP_LY" @resfreshData="refreshData" />
        </TabItem>
        <TabItem name="HÌNH DÁNG ĐẤT">
          <Table :listAppraise="listAppraiseEarthShape" type="HINH_DANG_DAT"  @resfreshData="refreshData"/>
        </TabItem>
        <TabItem name="GIAO THÔNG">
          <Table :listAppraise="listAppraiseTraffic" type="GIAO_THONG" @resfreshData="refreshData"/>
        </TabItem>
        <TabItem name="KINH DOANH">
          <Table :listAppraise="listAppraiseBusiness" type="KINH_DOANH" @resfreshData="refreshData"/>
        </TabItem>
        <TabItem name="ĐIỀU KIỆN HẠ TẦNG">
          <Table :listAppraise="listAppraiseInfrastructure" type="DIEU_KIEN_HA_TANG" @resfreshData="refreshData"/>
        </TabItem>
        <TabItem name="AN NINH, MÔI TRƯỜNG SỐNG">
          <Table :listAppraise="listAppraiseHabitat" type="AN_NINH_MOI_TRUONG_SONG" @resfreshData="refreshData"/>
        </TabItem>
        <TabItem name="PHONG THUỶ">
          <Table :listAppraise="listAppraiseFengShui" type="PHONG_THUY" @resfreshData="refreshData"/>
        </TabItem>
        <TabItem name="ĐIỀU KIỆN THANH TOÁN">
          <Table :listAppraise="listAppraiseCondition" type="DIEU_KIEN_THANH_TOAN" @resfreshData="refreshData"/>
        </TabItem>
      </Tabs>
    </div>
  </div>
</template>

<script>
import {Tabs, TabItem} from 'vue-material-tabs'
import Table from './components/Table'
import Appraise from '../../../models/Appraise'
export default {
	name: 'IndexElement',
	components: {
		Tabs,
		TabItem,
		Table
	},
	data: () => ({
		theme: {
			navItem: '#000000',
			navActiveItem: '#FAA831',
			slider: '#FAA831',
			arrow: '#000000'
		},
		listAppraiseJuridical: [],
		listAppraiseEarthShape: [],
		listAppraiseBusiness: [],
		listAppraiseTraffic: [],
		listAppraiseInfrastructure: [],
		listAppraiseHabitat: [],
		listAppraiseFengShui: [],
		listAppraiseCondition: [],
		listAppraise: []
	}),
	methods: {
		async getAppraiseJuridical () {
			this.isLoading = true
			try {
				const data = 'PHAP_LY'
				const resp = await Appraise.getDictionary(data)
				this.listAppraiseJuridical = [...resp.data]
				this.isLoading = false
			} catch (e) {
				this.isLoading = false
			}
		},
		async getAppraiseEarthShape () {
			this.isLoading = true
			try {
				const data = 'HINH_DANG_DAT'
				const resp = await Appraise.getDictionary(data)
				this.listAppraiseEarthShape = [...resp.data]
				this.isLoading = false
			} catch (e) {
				this.isLoading = false
			}
		},
		async getAppraiseTraffic () {
			this.isLoading = true
			try {
				const data = 'GIAO_THONG'
				const resp = await Appraise.getDictionary(data)
				this.listAppraiseTraffic = [...resp.data]
				this.isLoading = false
			} catch (e) {
				this.isLoading = false
			}
		},
		async getAppraiseBusiness () {
			this.isLoading = true
			try {
				const data = 'KINH_DOANH'
				const resp = await Appraise.getDictionary(data)
				this.listAppraiseBusiness = [...resp.data]
				this.isLoading = false
			} catch (e) {
				this.isLoading = false
			}
		},
		async getAppraiseInfrastructure () {
			this.isLoading = true
			try {
				const data = 'DIEU_KIEN_HA_TANG'
				const resp = await Appraise.getDictionary(data)
				this.listAppraiseInfrastructure = [...resp.data]
				this.isLoading = false
			} catch (e) {
				this.isLoading = false
			}
		},
		async getAppraiseHabitat () {
			this.isLoading = true
			try {
				const data = 'AN_NINH_MOI_TRUONG_SONG'
				const resp = await Appraise.getDictionary(data)
				this.listAppraiseHabitat = [...resp.data]
				this.isLoading = false
			} catch (e) {
				this.isLoading = false
			}
		},
		async getAppraiseFengShui () {
			this.isLoading = true
			try {
				const data = 'PHONG_THUY'
				const resp = await Appraise.getDictionary(data)
				this.listAppraiseFengShui = [...resp.data]
				this.isLoading = false
			} catch (e) {
				this.isLoading = false
			}
		},
		async getAppraiseCondition () {
			this.isLoading = true
			try {
				const data = 'DIEU_KIEN_THANH_TOAN'
				const resp = await Appraise.getDictionary(data)
				this.listAppraiseCondition = [...resp.data]
				this.isLoading = false
			} catch (e) {
				this.isLoading = false
			}
		},
		refreshData () {
			this.getAppraiseJuridical()
			this.getAppraiseEarthShape()
			this.getAppraiseTraffic()
			this.getAppraiseBusiness()
			this.getAppraiseInfrastructure()
			this.getAppraiseHabitat()
			this.getAppraiseFengShui()
			this.getAppraiseCondition()
		}
	},

	beforeMount () {
		this.refreshData()
		// this.getAppraiseJuridical()
		// this.getAppraiseEarthShape()
		// this.getAppraiseTraffic()
		// this.getAppraiseBusiness()
		// this.getAppraiseInfrastructure()
		// this.getAppraiseHabitat()
		// this.getAppraiseFengShui()
		// this.getAppraiseCondition()
	}
}
</script>

<style scoped>
.appraise-container {
  padding: 0 1.25rem;
  height: 100%;
}
</style>
