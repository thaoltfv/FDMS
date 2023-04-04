<template>
  <div class="dashboard">
    <div class="card">
      <!--Header-->
      <div class="card-header d-flex align-items-center justify-content-between">
        <p class="font-weight-bold mb-0">Bản đồ</p>
<!--        <div class="total d-flex align-items-center">-->
<!--          <p class="font-weight-bold mb-0 mr-3">{{ $t('total_member') }} :</p>-->
<!--          <span class="font-weight-bold">{{ userTotal }}</span>-->
<!--        </div>-->
      </div>
      <!--Search-->
<!--      <div class="card-body">-->
<!--        <form role="search" @submit.prevent="search">-->
<!--          <div class="row">-->
<!--            <div class="col-12 col-sm-8 col-lg-5 mb-3 mb-lg-0">-->
<!--              <InputDatePickerRange-->
<!--                ref="DateRange"-->
<!--                format-date="YYYY-MM-DD HH:mm"-->
<!--                @startDate="filter.from = $event"-->
<!--                @endDate="filter.to = $event"/>-->
<!--            </div>-->
<!--            <div class="col-12 col-sm-4 col-lg-3 mb-3 mb-lg-0">-->
<!--              <a-select-->
<!--                style="width: 100%"-->
<!--                v-model="filter.display_type">-->
<!--                <a-select-option value="monthly">-->
<!--                  {{$t('monthly')}}-->
<!--                </a-select-option>-->
<!--                <a-select-option value="daily">-->
<!--                  {{$t('daily')}}-->
<!--                </a-select-option>-->
<!--              </a-select>-->
<!--            </div>-->

<!--            &lt;!&ndash;Search & Reset&ndash;&gt;-->
<!--            <div class="col-12 col-lg-4">-->
<!--              <div class="row">-->
<!--                <div class="col-6">-->
<!--                  <button type="button" @click="reset" class="text-nowrap btn btn-light btn-pill btn-block border-0">-->
<!--                    {{$t('btn_reset')}}-->
<!--                  </button>-->
<!--                </div>-->

<!--                <div class="col-6">-->
<!--                  <button type="submit" class="text-nowrap btn btn-primary btn-pill btn-block border-0">-->
<!--                    {{$t('btn_search')}}-->
<!--                  </button>-->
<!--                </div>-->
<!--              </div>-->
<!--            </div>-->
<!--          </div>-->
<!--        </form>-->
<!--      </div>-->

      <!--Chart-->
      <div class="card-footer">
        <!--Line chart-->
<!--        <ChartLine :chart-data="filterLineChartData" :options="lineChartOptions" style="height: 400px"/>-->
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.184775379563!2d106.6672518153342!3d10.797155861761864!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3175292ebba9ce6d%3A0xd8e5be84688dde40!2zOSwgMyDEkOG6t25nIFbEg24gTmfhu68sIFBoxrDhu51uZyAxMCAoUGjDuiBOaHXhuq1uKSwgUGjDuiBOaHXhuq1uLCBUaMOgbmggcGjhu5EgSOG7kyBDaMOtIE1pbmgsIFZp4buHdCBOYW0!5e0!3m2!1svi!2s!4v1623726649826!5m2!1svi!2s" class="w-100" style="border:0; height: 70vh" allowfullscreen="" loading="lazy"></iframe>
      </div>
    </div>
  </div>
</template>

<script>

import Dashboard from '@/models/Dashboard'
import store from '@/store'
import * as types from '@/store/mutation-types'
import Breadcrumb from '@/components/Breadcrumb'
import {keys, values} from 'lodash-es'
import locale from 'ant-design-vue/es/date-picker/locale/vi_VN'
import InputDatePickerRange from '@/components/Form/InputDatePickerRange'

export default {
	name: 'Home',

	components: {
		InputDatePickerRange,
		Breadcrumb
	},

	data () {
		return {
			locale,
			dataDashboard: {},
			dateRange: [],
			filter: {
				from: null,
				to: null,
				display_type: 'monthly'
			},
			lineChartData: {
				labels: [],
				datasets: [
					{
						label: this.$t('chart_title'),
						// Bezier curve tension of the line
						lineTension: 0.2,
						backgroundColor: 'transparent',
						borderColor: '#45aaf2',
						pointBackgroundColor: '#007cd4',
						pointBorderColor: '#f1f1f1',
						pointBorderWidth: 2,
						// set big & small dot circle
						pointRadius: 5,
						data: []
					}
				]
			},
			lineChartOptions: {
				maintainAspectRatio: false,
				responsive: true,
				events: ['mousemove', 'mouseout'],
				tooltips: {
					mode: 'point'
				},
				scales: {
					yAxes: [{
						ticks: {
							stepSize: 1
						}
					}],
					xAxes: [{
						ticks: {
							beginAtZero: true
						}
					}]
				}
			},
			keyChart: [],
			valueChart: []
		}
	},

	computed: {
		filterLineChartData () {
			let dataChart = store.getters.dataChart
			let lineChartData = {...this.lineChartData}
			let keyChart = keys(dataChart.statistics)
			let valueChart = values(dataChart.statistics)

			lineChartData.labels = [...keyChart]
			lineChartData.datasets[0].data = [...valueChart]
			return lineChartData
		},

		userTotal () {
			if ('total_user' in store.getters.dataChart) {
				return store.getters.dataChart.total_user
			}
		}
	},

	// async beforeRouteEnter (to, from, next) {
	//   // Get Dashboards
	//   const resp = await Dashboard.all()
	//
	//   store.commit(types.SET_CHART, resp.data)
	//
	//   return next()
	// },

	methods: {
		async search () {
			const resp = await Dashboard.all({
				query: {
					...this.filter
				}
			})

			store.commit(types.SET_CHART, resp.data)
		},

		reset () {
			this.$refs.DateRange.startValue = null
			this.$refs.DateRange.endValue = null

			for (let property in this.filter) {
				if (this.filter.hasOwnProperty(property)) {
					if (property === 'display_type') {
						this.filter[property] = 'monthly'
					} else {
						this.filter[property] = null
					}
				}
			}
			this.dateRange = []
			this.search()
		}
	}
}
</script>

<style lang="scss">
  .dashboard {
    padding: 1.25rem;
    .card {
      &-header {
        p {
          color: #333333;
        }
        span {
          font-size: 20px;
          color: #007CC3;
        }
      }

      &-body {
        .btn {
          white-space: nowrap;
        }
      }
    }
  }
</style>
