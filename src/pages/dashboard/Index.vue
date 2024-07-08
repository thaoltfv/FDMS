<template>
	<div class="row">
		<div v-for="item in dashboard" :key="item.id" :class="item.class">
			<CertificationBacklogChart
				:name="item.name"
				v-if="item.slug === 'backlog_report' && !isMobile"
			/>
			<ProcessProgressChart
				:name="item.name"
				v-if="item.slug === 'process_progress_report' && !isMobile"
			/>
			<FinishByQuarterChart
				:name="item.name"
				v-if="item.slug === 'finish_byquarter_report' && !isMobile"
			/>
			<CancelByQuarterChart
				:name="item.name"
				v-if="item.slug === 'cancel_byquarter_report' && !isMobile"
			/>
			<FinishByMonthChart
				:name="item.name"
				v-if="item.slug === 'finish_bymonth_report'"
			/>
			<CancelByMonthChart
				:name="item.name"
				v-if="item.slug === 'cancel_bymonth_report'"
			/>
			<BranchRevenueChart
				:name="item.name"
				v-if="item.slug === 'branch_revenue_report' && !isMobile"
			/>
			<BranchDeptChart
				:name="item.name"
				v-if="item.slug === 'branch_debt_report' && !isMobile"
			/>
			<CountCompareAssetChart
				:name="item.name"
				v-if="item.slug === 'compare_asset_report' && !isMobile"
			/>
			<CountAppraiseAssetChart
				:name="item.name"
				v-if="item.slug === 'appraise_asset_report' && !isMobile"
			/>
			<StatusChart
				:name="item.name"
				v-if="item.slug === 'status_report' && !isMobile"
				:fromDate="fromDate"
				:toDate="toDate"
			/>
			<ProcessAppraiserChart
				:name="item.name"
				v-if="item.slug === 'process_appraiser_report' && !isMobile"
				:fromDate="fromDate"
				:toDate="toDate"
			/>
			<CertificationBriefChart
				:name="item.name"
				v-if="item.slug === 'certification_brief_report' && !isMobile"
				:fromDate="fromDateMonth"
				:toDate="toDateMonth"
			/>
		</div>
	</div>
</template>
<script>
import moment from "moment";
import ProcessProgressChart from "./components/ProcessProgressChart";
import CertificationBacklogChart from "./components/CertificationBacklogChart";
import FinishByQuarterChart from "./components/FinishByQuarterChart";
import CancelByQuarterChart from "./components/CancelByQuarterChart";
import FinishByMonthChart from "./components/FinishByMonthChart";
import CancelByMonthChart from "./components/CancelByMonthChart";
import BranchRevenueChart from "./components/BranchRevenueChart";
import BranchDeptChart from "./components/BranchDeptChart";
import CountCompareAssetChart from "./components/CountCompareAssetChart";
import CountAppraiseAssetChart from "./components/CountAppraiseAssetChart";
import StatusChart from "./components/StatusChart";
import ProcessAppraiserChart from "./components/ProcessAppraiserChart";
import CertificationBriefChart from "./components/CertificationBriefChart";
const dashboardConfig = require("../../../config/dashboard.json");

export default {
	components: {
		ProcessProgressChart,
		CertificationBacklogChart,
		FinishByQuarterChart,
		CancelByQuarterChart,
		FinishByMonthChart,
		CancelByMonthChart,
		BranchRevenueChart,
		BranchDeptChart,
		CountCompareAssetChart,
		CountAppraiseAssetChart,
		StatusChart,
		ProcessAppraiserChart,
		CertificationBriefChart
	},
	data() {
		return {
			fromDate: moment(
				new Date(new Date().setDate(new Date().getDate() - 30))
			).format("DD/MM/YYYY"),
			toDate: moment(new Date()).format("DD/MM/YYYY"),
			fromDateMonth: moment(
				new Date(new Date().setFullYear(new Date().getFullYear() - 1))
			).format("DD/MM/YYYY"),
			toDateMonth: moment(new Date()).format("DD/MM/YYYY"),
			dashboard: dashboardConfig.dashboard.filter(i => i.active === true)
		};
	},
	methods: {
		isMobile() {
			if (
				/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
					navigator.userAgent
				)
			) {
				return true;
			} else {
				return false;
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
