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
              <DoughnutChart :key="render_doughnut_chart" ref="chart_doughnut" :text_center="chartDoughnut.total" :data="chartDoughnut" :options="chartDoughnutOptions"  />
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
			default: 'Tiến độ xử lý'
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
						font: { size: 16, weight: 'bold' }
					}
				}
			}
		}
	},
	async mounted () {
		await this.getDataProcessProgress()
	},
	methods: {
		async getDataProcessProgress () {
			const res = await Report.getReportProcessProgress(this.form)
			console.log('data process', res)
			if (res.data && res.data.status && res.data.status.length > 0) {
				this.chartDoughnut.datasets[0].data = res.data.data
				this.chartDoughnut.datasets[0].backgroundColor = []
				this.chartDoughnut.total = res.data.data.reduce((a, b) => a + b, 0)
				res.data.status.forEach(item => {
					if (item === 1) {
						this.chartDoughnut.datasets[0].backgroundColor.push('#45AAF2')
						this.chartDoughnut.labels.push('Tiếp nhận')
					} else if (item === 2) {
						this.chartDoughnut.datasets[0].backgroundColor.push('#0062AF')
						this.chartDoughnut.labels.push('Thẩm định')
					} else if (item === 6) {
						this.chartDoughnut.datasets[0].backgroundColor.push('#e8bc6b')
						this.chartDoughnut.labels.push('Kiểm soát')
					} else if (item === 3) {
						this.chartDoughnut.datasets[0].backgroundColor.push('#fab005')
						this.chartDoughnut.labels.push('Phê duyệt')
					}
				})
			} else {
				await this.$toast.open({
					message: 'Hiện không có dữ liệu báo cáo',
					type: 'error',
					position: 'top-right',
					duration: 4000
				})
				this.chartDoughnut.datasets[0].data = []
				this.chartDoughnut.labels = []
				this.chartDoughnut.datasets[0].backgroundColor = []
				this.chartDoughnut.total = ''
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
