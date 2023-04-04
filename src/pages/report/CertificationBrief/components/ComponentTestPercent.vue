<template>
  <div>
      <div class="card">
        <div class="card-title">
          <div class="d-flex justify-content-between align-items-center">
            <h3 class="title">Trạng thái</h3>
            <div class="d-flex">
              <InputDatePickerRange
                :key="render_doughnut_key"
                vid="search"
                format-date="DD/MM/YYYY"
                :startDateValue="form.fromDate"
                :endDateValue="form.toDate"
                @startDate="form.fromDate = $event"
                @endDate="form.toDate = $event"
              />
              <button class="btn ml-2" @click="getDataReport">
                <img src="@/assets/icons/ic_search.svg" alt="search">
              </button>
            </div>
          </div>
        </div>
        <div class="card-body card-info" >
          <div class="row">
            <div class="col-12">
              <CirclePercentChart :key="render_doughnut_key" ref="chart_doughnut" :text_center="chartDoughnut.total" :data="chartDoughnut" :options="chartDoughnutOptions"  />
            </div>
          </div>
        </div>
      </div>
    </div>
</template>
<script>
import Report from '@/models/Report'
import CirclePercentChart from '@/components/charts/CirclePercentChart'
import InputDatePickerRange from '@/components/Form/InputDatePickerRange'
export default {
	components: {
		CirclePercentChart,
		InputDatePickerRange
	},
	props: {
		fromDate: {
			type: String,
			default: null
		},
		toDate: {
			type: String,
			default: null
		},
		text_center: {
			typeof: String || Number,
			default: ''
		}
	},
	data () {
		return {
			render_doughnut_key: 1021,
			form: {
				fromDate: this.fromDate ? this.fromDate : null,
				toDate: this.toDate ? this.toDate : null
			},

			chartDoughnut: {
				label: 'Africa / Population (millions)',
				percent: 68,
				backgroundColor: ['#5283ff'],
				total: '68%'
			},
			chartDoughnutOptions: {
				responsive: true,
				maintainAspectRatio: false,
				legend: {
					onClick: (e) => e.stopPropagation()
				},
				position: 'center',
				showAllTooltips: false,
				plugins: [
					{
						beforeInit: (chart) => {
							const dataset = chart.data.datasets[0]
							// chart.data.labels = [dataset.label];
							dataset.data = [dataset.percent, 100 - dataset.percent]
						}
					},
					{
						datalabels: {
							display: true,
							color: 'white',
							align: 'center',
							anchor: 'center',
							borderRadius: 3,
							font: { size: 16, weight: 'bold' }
						}
					}
				]
			}
		}
	},
	async mounted () {
		// await this.getDataReport()
	},
	methods: {
		async getDataReport () {
			const res = await Report.getReportCertificationBrief(this.form)
			if (res.data && res.data.status && res.data.status.length > 0) {
				this.chartDoughnut.datasets[0].data = res.data.data
				this.chartDoughnut.labels = res.data.label
				this.chartDoughnut.datasets[0].backgroundColor = []
				this.chartDoughnut.total = res.data.data.reduce((a, b) => a + b, 0)
				res.data.status.forEach(item => {
					if (item === 1) {
						this.chartDoughnut.datasets[0].backgroundColor.push('#45aaf2')
					} else if (item === 2) {
						this.chartDoughnut.datasets[0].backgroundColor.push('#0062AF')
					} else if (item === 3) {
						this.chartDoughnut.datasets[0].backgroundColor.push('#fab005')
					} else if (item === 4) {
						this.chartDoughnut.datasets[0].backgroundColor.push('#26BF7F')
					} else if (item === 5) {
						this.chartDoughnut.datasets[0].backgroundColor.push('#6e7582')
					}
				})
			} else {
				await this.$toast.open({
					message: 'Hiện không có dữ liệu trong khoảng thời gian này',
					type: 'error',
					position: 'top-right',
					duration: 4000
				})
				this.chartDoughnut.datasets[0].data = []
				this.chartDoughnut.labels = []
				this.chartDoughnut.datasets[0].backgroundColor = []
				this.chartDoughnut.total = ''
			}
			this.render_doughnut_key += 1
		}
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
