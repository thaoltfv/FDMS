<template>
	<div class="row">
		<div v-for="item in reportTypes" :key="item.id" :class="item.class">
				<StatusChart v-if="item.slug === 'status_report'" :fromDate="fromDate" :toDate="toDate"/>
				<ProcessAppraiserChart v-if="item.slug === 'process_appraiser_report'" :fromDate="fromDate" :toDate="toDate"/>
				<CertificationBriefChart v-if="item.slug === 'certification_brief_report'" :fromDate="fromDateMonth" :toDate="toDateMonth"/>
				<!-- <ComponentTestPercent v-if="item.slug === 'user_report'" :fromDate="fromDate" :toDate="toDate"/> -->
		</div>
	</div>
</template>

<script>
import moment from 'moment'
import StatusChart from './components/StatusChart'
import { ReportTypes } from '@/enum/report-type.enum'
import ComponentTestPercent from './components/ComponentTestPercent'
import ProcessAppraiserChart from './components/ProcessAppraiserChart'
import CertificationBriefChart from './components/CertificationBriefChart'
export default {
	name: 'Report.Index',
	components: {
		StatusChart,
		ProcessAppraiserChart,
		CertificationBriefChart,
		ComponentTestPercent
	},
	data () {
		return {
			fromDate: moment(new Date(new Date().setDate(new Date().getDate() - 30))).format('DD/MM/YYYY'),
			toDate: moment(new Date()).format('DD/MM/YYYY'),
			fromDateMonth: moment(new Date(new Date().setFullYear(new Date().getFullYear() - 1))).format('DD/MM/YYYY'),
			toDateMonth: moment(new Date()).format('DD/MM/YYYY'),
			reportTypes: ReportTypes
		}
	},
	async mounted () {
	},
	methods: {
	}
}
</script>
<style lang="scss" scoped>
.card {
  border-radius: 5px;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
  background: #FFFFFF;
  margin-bottom: 1rem;

  &-footer {
    padding: 15px 24px;
  }

  &-title {
    padding: 15px;
    margin-bottom: 0;
    color: #E8E8E8;
    border-bottom: 2px solid;
    &__img {
      padding: 8px 20px;
    }
    h3 {
      color: #007EC6;
    }
    @media (max-width: 768px) {
      padding: 12px;
    }

    .title {
      font-size: 1.125rem;
      font-weight: 600;
      margin-bottom: 0;
    }
  }

  &-body {

    @media (max-width: 787px) {
      padding: 15px;
    }
  }

  &-info {
    .title {
      font-size: 1.125rem;
      font-weight: 700;
      margin-top: 28px;

      &-highlight {
        background: rgba(252, 194, 114, 0.53);
        text-align: center;
        padding: 10px 0;
        border-radius: 2px;
      }
    }
  }

  &-land {
    position: relative;
    padding: 0;
  }
}
</style>
