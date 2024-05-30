<template>
	<div>
		<div class="card">
			<div class="card-title" style="height: 96px;">
				<div class="d-flex justify-content-between align-items-center">
					<h3 class="title mt-4">{{ name }}</h3>
				</div>
			</div>
			<div class="card-body card-info">
				<div class="row">
					<div class="col-12">
						<BarChart
							:key="render_bar_chart"
							:chart-data="chartBar"
							:options="chartBarOption"
						/>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>
<script>
import Report from "@/models/Report";
import BarChart from "@/components/charts/BarChart";
export default {
	components: {
		BarChart
	},
	props: {
		name: {
			type: String,
			default: "Hồ sơ hủy theo tháng"
		}
	},
	data() {
		return {
			render_bar_chart: 1302,
			chartBar: {
				labels: [],
				datasets: []
			},
			chartBarOption: {
				responsive: true,
				maintainAspectRatio: false,
				legend: {
					onClick: e => e.stopPropagation(),
					position: "top"
				},
				scales: {
					yAxes: [
						{
							ticks: {
								beginAtZero: true,
								precision: 0
							}
						}
					]
				},
				plugins: {
					datalabels: {
						backgroundColor: function(context) {
							return context.dataset.borderColor;
						},
						display: false,
						borderRadius: 4,
						color: "white",
						font: {
							weight: "bold"
						},
						formatter: Math.round,
						padding: 6,
						align: "end",
						anchor: "end"
					}
				}
			}
		};
	},
	async mounted() {
		await this.getReportCertificationConversionRateByMonth();
	},
	methods: {
		async getReportCertificationConversionRateByMonth() {
			let dataset = [];
			this.chartBar.datasets = [];
			this.chartBar.labels = [];
			const res = await Report.getReportCertificationConversionRateByMonth();
			if (res.data && res.data.data) {
				await res.data.data.forEach(item => {
					if (item.label === "DaChuyenDoi") {
						dataset.push({
							data: item.count,
							label: "Đã chuyển dổi",
							borderColor: "#33a853",
							backgroundColor: "#33a853",
							fill: false
						});
					} else {
						dataset.push({
							data: item.count,
							label: "Chưa chuyển đổi",
							borderColor: "#b6d7a8",
							backgroundColor: "#b6d7a8",
							fill: false
						});
					}
				});
				this.chartBar.labels = res.data.label;
				this.chartBar.datasets = dataset;
			} else {
				await this.$toast.open({
					message: "Hiện không có dữ liệu báo cáo",
					type: "error",
					position: "top-right",
					duration: 4000
				});
				this.chartBar.datasets = [];
				this.chartBar.labels = [];
			}
			this.render_bar_chart += 1;
		}
	}
};
</script>
<style lang="scss" scoped>
.card {
	border-radius: 5px;
	box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
	background: #ffffff;
	margin-bottom: 1rem;

	&-footer {
		padding: 15px 24px;
	}

	&-title {
		padding: 15px;
		margin-bottom: 0;
		color: #e8e8e8;
		border-bottom: 2px solid;
		&__img {
			padding: 8px 20px;
		}
		h3 {
			color: #007ec6;
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
