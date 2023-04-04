<template>
  <div>
      <div class="card">
        <div class="card-title">
          <div class="d-flex justify-content-between align-items-center">
            <h3 class="title"> {{ name }} </h3>
          </div>
        </div>
        <div class="card-body card-info" >
          <div class="row">
            <div class="col-12">
              <DoughnutChart :key="render_doughnut_chart" ref="chart_doughnut" text_center="" :data="chartDoughnut" :options="chartDoughnutOptions"  />
            </div>
          </div>
        </div>
      </div>
    </div>
</template>
<script>
import Report from '@/models/Report'
import DoughnutChart from '@/components/charts/DoughnutChart'
export default {
	components: {
		DoughnutChart
	},
	props: {
		name: {
			type: String,
			default: 'Doanh thu'
		},
		text_center: {
			typeof: String || Number,
			default: ''
		}
	},
	data () {
		return {
			render_doughnut_chart: 1302,
			chartDoughnut: {
				labels: [],
				datasets: [{
					borderWidth: 0,
					backgroundColor: [
						'#00508F',
						'#fab005',
						'#EF3D39',
						'#FF7600',
						'#3297E1',
						'#6E7582'
					],
					data: []
				}]
			},
			chartDoughnutOptions: {
				responsive: true,
				maintainAspectRatio: false,
				tooltips: {
					callbacks: {
						label: (tooltipItem, data) => {
							let dataset = data.datasets[tooltipItem.datasetIndex]
							return data.labels[tooltipItem.index] + ': ' + this.formatNumber(dataset.data[tooltipItem.index]) + ' đ'
						}
					}
				},
				legend: {
					onClick: (e) => e.stopPropagation(),
					position: 'right'
				},
				position: 'center',
				showAllTooltips: false,
				plugins: {
					datalabels: false
				}
			}
		}
	},
	async mounted () {
		await this.getDataBranchRevenue()
	},
	methods: {
		formatNumber (num) {
			// convert number to dot format
			return num.toString().replace(/^[+-]?\d+/, function (int) {
				return int.replace(/(\d)(?=(\d{3})+$)/g, '$1.')
			})
		},
		async getDataBranchRevenue () {
			const res = await Report.getReportBranchRevenue()
			if (res.data && res.data.branch_id && res.data.branch_id.length > 0) {
				this.chartDoughnut.datasets[0].data = res.data.data
				this.chartDoughnut.labels = res.data.label
			} else {
				await this.$toast.open({
					message: 'Hiện không có dữ liệu báo cáo',
					type: 'error',
					position: 'top-right',
					duration: 4000
				})
				this.chartDoughnut.datasets[0].data = []

				this.chartDoughnut.labels = []
			}
			this.render_doughnut_chart += 1
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
