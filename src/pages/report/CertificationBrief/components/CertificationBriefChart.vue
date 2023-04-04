<template>
  <div>
      <div class="card">
        <div class="card-title">
          <div class="d-flex justify-content-between align-items-center">
            <h3 class="title">Báo cáo hồ sơ thẩm định</h3>
            <div class="d-flex">
              <InputMonthPickerRange
                :key="render_bar_chart"
                vid="search"
                format-date="MM/YYYY"
                :startDateValue="form.fromDate"
                :endDateValue="form.toDate"
                @startDate="handleChangeStartDate"
                @endDate="handleChangeEndDate"
              />
              <button class="btn ml-2" @click="getDataCertificationBrief">
                <img src="@/assets/icons/ic_search.svg" alt="search">
              </button>
            </div>
          </div>
        </div>
        <div class="card-body card-info" >
          <div class="row">
            <div class="col-12">
              <BarChart :key="render_bar_chart" :chart-data="chartBar" :options="chartBarOption" />
            </div>
          </div>
        </div>
      </div>
    </div>
</template>
<script>
import moment from 'moment'
import Report from '@/models/Report'
import BarChart from '@/components/charts/BarChart'
import InputMonthPickerRange from '@/components/Form/InputMonthPickerRange'
export default {
	components: {
		BarChart,
		InputMonthPickerRange
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
			form: {
				fromDate: this.fromDate ? this.fromDate : null,
				toDate: this.toDate ? this.toDate : null,
				status: [1, 4]
			},
			render_bar_chart: 1302,
			chartBar: {
				labels: [],
				datasets: []
			},
			chartBarOption: {
				responsive: true,
				maintainAspectRatio: false,
				legend: {
					onClick: (e) => e.stopPropagation(),
					position: 'bottom'
				},
				plugins: {
					datalabels: {
						backgroundColor: function (context) {
							return context.dataset.borderColor
						},
						display: false,
						borderRadius: 4,
						color: 'white',
						font: {
							weight: 'bold'
						},
						formatter: Math.round,
						padding: 6,
						align: 'end',
						anchor: 'end'
					}
				}
			}
		}
	},
	async mounted () {
		await this.getDataCertificationBrief()
	},
	methods: {
		handleChangeStartDate (event) {
			this.form.fromDate = moment(event, 'MM/YYYY').format('DD/MM/YYYY')
		},
		handleChangeEndDate (event) {
			this.form.toDate = moment(event, 'MM/YYYY').format('DD/MM/YYYY')
		},
		async getDataCertificationBrief () {
			let dataset = []
			this.chartBar.datasets = []
			this.chartBar.labels = []
			const res = await Report.getReportCertificationBrief(this.form)
			if (res.data && res.data.data) {
				await res.data.data.forEach(item => {
					if (item.status === '1') {
						dataset.push({
							data: item.count,
							label: item.label,
							status: 1,
							borderColor: '#45aaf2',
							backgroundColor: '#45aaf2',
							fill: false
						})
					} else if (item.status === '2') {
						dataset.push({
							data: item.count,
							label: item.label,
							status: 2,
							borderColor: '#0062AF',
							backgroundColor: '#0062AF',
							fill: false
						})
					} else if (item.status === '3') {
						dataset.push({
							data: item.count,
							label: item.label,
							status: 3,
							borderColor: '#fab005',
							backgroundColor: '#fab005',
							fill: false
						})
					} else if (item.status === '4') {
						dataset.push({
							data: item.count,
							label: item.label,
							status: 4,
							borderColor: '#26BF7F',
							backgroundColor: '#26BF7F',
							fill: false
						})
					} else {
						dataset.push({
							data: item.count,
							label: item.label,
							status: 5,
							borderColor: '#6e7582',
							backgroundColor: '#6e7582',
							fill: false
						})
					}
				})
				this.chartBar.labels = res.data.label
				this.chartBar.datasets = dataset
			} else {
				await this.$toast.open({
					message: 'Hiện không có dữ liệu trong khoảng thời gian này',
					type: 'error',
					position: 'top-right',
					duration: 4000
				})
				this.chartBar.datasets = []
				this.chartBar.labels = []
			}
			this.render_bar_chart += 1
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
