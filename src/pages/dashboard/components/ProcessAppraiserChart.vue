<template>
  <div>
      <div class="card">
        <div class="card-title">
          <div class="d-flex justify-content-between align-items-center">
            <h3 class="title">{{ name }} </h3>
            <div class="d-flex">
              <InputDatePickerRange
                :key="render_expired_date"
                vid="search"
                format-date="DD/MM/YYYY"
                :startDateValue="form.fromDate"
                :endDateValue="form.toDate"
                @startDate="form.fromDate = $event"
                @endDate="form.toDate = $event"
              />
              <button class="btn ml-2" @click="getDataReportExpired">
                <img src="@/assets/icons/ic_search.svg" alt="search">
              </button>
            </div>
          </div>
        </div>
        <div class="card-body card-info" >
          <div class="row">
            <div class="col-12">
              <DoughnutChart :key="render_expired_date" ref="chart_doughnut" text_center="" :data="chartDoughnut" :options="chartDoughnutOptions"  />
            </div>
          </div>
        </div>
      </div>
    </div>
</template>
<script>
import Report from '@/models/Report'
import DoughnutChart from '@/components/charts/DoughnutChart'
import InputDatePickerRange from '@/components/Form/InputDatePickerRange'
export default {
	components: {
		DoughnutChart,
		InputDatePickerRange
	},
	props: {
		name: {
			type: String,
			default: 'Tiến độ thẩm định'
		},
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
			form: {
				fromDate: this.fromDate ? this.fromDate : null,
				toDate: this.toDate ? this.toDate : null
			},
			render_expired_date: 1302,
			chartDoughnut: {
				labels: [],
				datasets: [{
					backgroundColor: [],
					data: []
				}],
				total: ''
			},
			chartDoughnutOptions: {
				responsive: true,
				maintainAspectRatio: false,
				legend: {
					onClick: (e) => e.stopPropagation(),
					position: 'right'
				},
				position: 'center',
				showAllTooltips: false,
				plugins: {
					datalabels: {
						display: true,
						color: 'white',
						align: 'center',
						anchor: 'center',
						borderRadius: 3,
						formatter: (value, ctx) => {
							let sum = 0
							let dataArr = ctx.chart.data.datasets[0].data
							dataArr.map(data => {
								sum += data
							})
							let percentage = (value * 100 / sum) + '%'
							return percentage
						},
						font: { size: 16, weight: 'bold' }
					}
				}
			}
		}
	},
	async mounted () {
		await this.getDataReportExpired()
	},
	methods: {
		async getDataReportExpired () {
			const res = await Report.getReportExpired(this.form)
			if (res.data && res.data.label && res.data.label.length > 0) {
				this.chartDoughnut.datasets[0].data = res.data.data
				this.chartDoughnut.labels = []
				this.chartDoughnut.datasets[0].backgroundColor = []
				res.data.label.forEach(item => {
					if (item === 'none') {
						this.chartDoughnut.datasets[0].backgroundColor.push('#0062AF')
						this.chartDoughnut.labels.push('Trong thời hạn')
					} else {
						this.chartDoughnut.datasets[0].backgroundColor.push('#FF9900')
						this.chartDoughnut.labels.push('Trễ hạn')
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
			}
			this.render_expired_date += 1
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
