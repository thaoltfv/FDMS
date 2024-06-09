<template>
	<div class="dropdown-box">
		<b-dropdown
			id="dropdown-right"
			text="Filter"
			right
			variant="outline-primary"
			ref="dropdown"
			@hide="handleHide($event)"
			@hidden="isCloseable = false"
			class="dropdown m-2"
			v-model="isCloseable"
			no-flip
		>
			<template v-slot:default>
				<a-menu mode="inline" style="width:320px">
					<a-sub-menu key="status">
						<span slot="title"> <span>Trạng thái HSTĐ</span></span>
						<a-menu-item
							v-if="lstFilterStatus && lstFilterStatus.length > 0"
							v-for="(option, index) in lstFilterStatus"
							:key="index"
						>
							<a-checkbox v-model="option.checked" :value="option.value">{{
								option.text
							}}</a-checkbox>
						</a-menu-item>
					</a-sub-menu>
					<a-sub-menu key="time">
						<span slot="title"> <span>Thời gian</span></span>
						<a-menu-item>
							<div class="row" style="width:250px">
								<a-date-picker
									placeholder="Từ ngày"
									v-model="timeFilter.from"
									format="DD-MM-YYYY"
								></a-date-picker>
							</div> </a-menu-item
						><a-menu-item>
							<div class="row" style="width:250px">
								<a-date-picker
									placeholder="Đến ngày"
									v-model="timeFilter.to"
									:disabledDate="disabledToDate"
									format="DD-MM-YYYY"
								></a-date-picker>
							</div>
						</a-menu-item>
					</a-sub-menu>
				</a-menu>
				<!-- <a-menu
					:default-selected-keys="['1']"
					:default-open-keys="['status']"
					mode="inline"
					style="width:320px"
				>
					<a-sub-menu key="status">
						<span slot="title">
						
							<span>{{ title || "" }}</span></span
						>
						<a-menu-item
							v-if="lstFilterStatus && lstFilterStatus.length > 0"
							v-for="(option, index) in lstFilterStatus"
							:key="index"
						>
							<a-checkbox v-model="option.checked" :value="option.value">{{
								option.text
							}}</a-checkbox>
						</a-menu-item>
					</a-sub-menu>
				</a-menu> -->
				<div
					class="row"
					style="width:100%;margin-top:10px; display: flex; justify-content: space-between;"
				>
					<b-button
						variant="outline-danger"
						style=" margin-left:35px;width:50px"
						@click="closeMe"
						>Thoát</b-button
					>
					<b-button
						style=" width:50px"
						variant="outline-primary"
						@click="searchFunction"
						>Tìm</b-button
					>
				</div>
			</template>
		</b-dropdown>
	</div>
</template>
<script>
import { ref } from "vue";
import { storeToRefs } from "pinia";
import { useWorkFlowConfig } from "@/store/workFlowConfig";
import { BDropdown, BDropdownItem, BButton } from "bootstrap-vue";
import moment from "moment";
export default {
	components: {
		"b-dropdown": BDropdown,
		"b-dropdown-item": BDropdownItem,
		"b-button": BButton
	},
	props: {
		title: String
	},
	data() {
		return { isCloseable: false, timeFilter: { from: "", to: "" } };
	},
	setup() {
		const workFlowConfigStore = useWorkFlowConfig();
		const { configs } = storeToRefs(workFlowConfigStore);
		const jsonConfig = ref({});
		const filter = ref([]);
		const lstFilterStatus = ref([]);
		const lstStatusChosen = ref([]);
		const startFunction = async () => {
			if (!configs.value.hstdConfig)
				await workFlowConfigStore.getConfigByName("workflowHSTD");
			jsonConfig.value = configs.value.hstdConfig;
			if (jsonConfig.value && jsonConfig.value.principle) {
				lstFilterStatus.value = jsonConfig.value.principle
					.filter(element => element.isActive === 1)
					.map(element => ({
						...element,
						value: element.status,
						text: element.description
					}));
			}
		};
		startFunction();

		const checkedValues = ref([]);

		return {
			checkedValues,
			jsonConfig,
			filter,
			lstFilterStatus,
			lstStatusChosen
		};
	},

	methods: {
		disabledToDate(current) {
			// Disable dates before the "from" date
			if (!this.timeFilter.from) return false;
			let endOfDay = moment(this.timeFilter.from).endOf("day");
			return current && current < endOfDay;
		},
		handleHide(bvEvent) {
			if (!this.isCloseable) {
				bvEvent.preventDefault();
			} else {
				this.$refs.dropdown.hide();
			}
		},
		async searchFunction() {
			let result = this.lstFilterStatus
				.filter(option => option.checked === true)
				.map(option => String(option.value));

			this.$emit("search", result, this.timeFilter.from, this.timeFilter.to);
		},
		closeMe() {
			this.isCloseable = true;
			this.$refs.dropdown.hide();
		}
	}
};
</script>
<style>
.drop-downn {
	background: #e2f3fc;
	border: 1px solid #007ec6;
	border-radius: 3px;
	color: #007ec6 !important;
	height: 35px;
	padding: 0.375rem 1rem;
	white-space: nowrap;
}
.ant-menu-inline > .ant-menu-submenu > .ant-menu-submenu-title {
	height: auto;
	line-height: normal;
	white-space: normal;
}
.checkbox-group {
	margin-left: 20px; /* Adjust as needed */
	margin-right: 20px; /* Adjust as needed */
}
.dropdown-box {
	div,
	button {
		width: 110%;
	}
}
.dropdown .dropdown-menu {
	max-height: 90dvh; /* Adjust as needed */
	overflow-y: auto;
}
.custom-datepicker .ant-calendar-picker {
	width: 100px !important;
}
</style>
