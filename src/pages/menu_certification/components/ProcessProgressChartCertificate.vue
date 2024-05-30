<template>
	<div>
		<div class="card">
			<div class="card-title">
				<div class="d-flex justify-content-between align-items-center">
					<h3 class="title mt-2 text-nowrap">{{ name }}</h3>
					<div class="row d-flex mr-1 justify-content-end">
						<InputDatePickerRangeCondition
							class="col-8 col-md-8 col-lg-8 form-group-container"
							vid="search"
							format-date="DD/MM/YYYY"
							:startDateValue="form.fromDate"
							:endDateValue="form.toDate"
							@startDate="form.fromDate = $event"
							@endDate="form.toDate = $event"
							label="Từ ngày - đến ngày"
						/>
						<button
							style="height:35px; margin-top: 28px;"
							class="btn btn-orange btn-action-modal"
							type="button"
							@click="handleSearch"
						>
							Tìm kiếm
						</button>
					</div>
				</div>
			</div>
			<div class="card-body card-info">
				<div class="row">
					<div class="col-12">
						<DoughnutChart
							:key="render_doughnut_chart"
							ref="chart_doughnut"
							:text_center="chartDoughnut.total"
							:data="chartDoughnut"
							:options="chartDoughnutOptions"
						/>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>
<script>
import Report from "@/models/Report";
import DoughnutChart from "@/components/charts/DoughnutChart";
import InputDatePickerRangeCondition from "@/components/Form/InputDatePickerRangeCondition";
export default {
	components: {
		DoughnutChart,
		InputDatePickerRangeCondition
	},
	props: {
		name: {
			type: String,
			default: "Hồ sơ thẩm định"
		},
		text_center: {
			typeof: String || Number,
			default: ""
		},
		fromDate: {
			type: String,
			default: null
		},
		toDate: {
			type: String,
			default: null
		}
	},
	data() {
		return {
			form: {
				fromDate: this.fromDate ? this.fromDate : null,
				toDate: this.toDate ? this.toDate : null
			},
			render_doughnut_chart: 1302,
			chartDoughnut: {
				labels: [],
				datasets: [
					{
						backgroundColor: [],
						data: []
					}
				],
				total: ""
			},
			chartDoughnutOptions: {
				responsive: true,
				maintainAspectRatio: false,
				legend: {
					onClick: e => e.stopPropagation(),
					position: "right"
				},
				position: "center",
				showAllTooltips: false,
				plugins: {
					datalabels: {
						display: true,
						color: "white",
						align: "center",
						anchor: "center",
						borderRadius: 3,
						font: { size: 16, weight: "bold" }
					}
				}
			}
		};
	},
	async mounted() {
		await this.getDataProcessProgress(this.form.fromDate, this.form.toDate);
	},
	methods: {
		async getDataProcessProgress(fromDate, toDate) {
			this.form = {
				fromDate: fromDate,
				toDate: toDate
			};
			const res = await Report.getReportProcessProgressCertificate(this.form);
			// console.log('data process', res)
			if (res.data && res.data.status && res.data.status.length > 0) {
				this.chartDoughnut.datasets[0].data = res.data.data;
				this.chartDoughnut.datasets[0].backgroundColor = [];
				this.chartDoughnut.labels = [];
				this.chartDoughnut.total = res.data.data.reduce((a, b) => a + b, 0);
				res.data.status.forEach(item => {
					if (item === 1) {
						this.chartDoughnut.datasets[0].backgroundColor.push("#45AAF2");
						this.chartDoughnut.labels.push("Tiếp nhận hồ sơ");
					} else if (item === 2) {
						this.chartDoughnut.datasets[0].backgroundColor.push("#FAB005");
						this.chartDoughnut.labels.push("Đang thực hiện");
						// } else if (item === 10) {
						// 	this.chartDoughnut.datasets[0].backgroundColor.push("#45AAF4");
						// 	this.chartDoughnut.labels.push("Phân hồ sơ");
						// } else if (item === 2) {
						// 	this.chartDoughnut.datasets[0].backgroundColor.push("#0062AF");
						// 	this.chartDoughnut.labels.push("Thẩm định");
						// } else if (item === 3) {
						// 	this.chartDoughnut.datasets[0].backgroundColor.push("#EBC784");
						// 	this.chartDoughnut.labels.push("Duyệt giá");
						// } else if (item === 7) {
						// 	this.chartDoughnut.datasets[0].backgroundColor.push("#FAB005");
						// 	this.chartDoughnut.labels.push("Duyệt phát hành");
						// } else if (item === 8) {
						// 	this.chartDoughnut.datasets[0].backgroundColor.push("#FAB009");
						// 	this.chartDoughnut.labels.push("In hồ sơ");
						// } else if (item === 9) {
						// 	this.chartDoughnut.datasets[0].backgroundColor.push("#FAB010");
						// 	this.chartDoughnut.labels.push("Bàn giao khách hàng");
					} else if (item === 4) {
						this.chartDoughnut.datasets[0].backgroundColor.push("#26BF7F");
						this.chartDoughnut.labels.push("Hoàn thành");
					} else if (item === 5) {
						this.chartDoughnut.datasets[0].backgroundColor.push("#6E7582");
						this.chartDoughnut.labels.push("Hủy");
					}
				});
			} else {
				await this.$toast.open({
					message: "Hiện không có dữ liệu báo cáo",
					type: "error",
					position: "top-right",
					duration: 4000
				});
				this.chartDoughnut.datasets[0].data = [];
				this.chartDoughnut.labels = [];
				this.chartDoughnut.datasets[0].backgroundColor = [];
				this.chartDoughnut.total = "";
			}
			this.render_doughnut_chart += 1;
		},
		handleSearch() {
			if (
				this.form.fromDate &&
				this.form.fromDate.trim() !== "" &&
				this.form.toDate &&
				this.form.toDate.trim() !== ""
			) {
				this.getDataProcessProgress(this.form.fromDate, this.form.toDate);
			} else {
				this.$toast.open({
					message: "Vui lòng nhập đầy đủ 2 trường từ ngày - đến ngày",
					type: "error",
					position: "top-right",
					duration: 3000
				});
			}
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
