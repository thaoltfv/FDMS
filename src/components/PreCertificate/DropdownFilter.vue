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
				<a-menu
					:default-selected-keys="['1']"
					mode="inline"
					style="width:320px"
				>
					<a-sub-menu key="ots">
						<span slot="title">
							<!-- <a-icon type="mail" /> -->
							<span>Trạng thái chuyển chính thức</span></span
						>
						<a-menu-item
							v-if="jsonConfig.filterOTS"
							v-for="(option, index) in jsonConfig.filterOTS"
							:key="index"
						>
							<a-checkbox
								v-model="filterKanban.selectedOfficialTransferStatus[index]"
								>{{ option.text }}</a-checkbox
							>
						</a-menu-item>
					</a-sub-menu>
					<a-sub-menu key="status">
						<span slot="title">
							<!-- <a-icon type="appstore" /> -->
							<span>Trạng thái YCSB</span></span
						>
						<a-menu-item
							v-if="lstFilterStatus"
							v-for="(option, index) in lstFilterStatus"
							:key="index"
						>
							<a-checkbox v-model="option.checked" :value="option.value">{{
								option.text
							}}</a-checkbox>
						</a-menu-item>
					</a-sub-menu>
					<a-sub-menu key="time" v-if="isMobile">
						<span slot="title"> <span>Thời gian</span></span>
						<a-menu-item>
							<div class="row" style="width:250px">
								<a-date-picker
									placeholder="Từ ngày"
									v-model="filterKanban.timeFilter.from"
									format="DD-MM-YYYY"
								></a-date-picker>
							</div> </a-menu-item
						><a-menu-item>
							<div class="row" style="width:250px">
								<a-date-picker
									placeholder="Đến ngày"
									v-model="filterKanban.timeFilter.to"
									:disabledDate="disabledToDate"
									format="DD-MM-YYYY"
								></a-date-picker>
							</div>
						</a-menu-item>
					</a-sub-menu>
				</a-menu>
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
import { usePreCertificateStore } from "@/store/preCertificate";
import moment from "moment";
import { BDropdown, BDropdownItem, BButton } from "bootstrap-vue";
export default {
	components: {
		"b-dropdown": BDropdown,
		"b-dropdown-item": BDropdownItem,
		"b-button": BButton
	},

	data() {
		return { isCloseable: false };
	},
	setup(props) {
		const checkMobile = () => {
			if (
				/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
					navigator.userAgent
				)
			) {
				return true;
			} else {
				return false;
			}
		};
		const isMobile = ref(checkMobile());
		const preCertificateStore = usePreCertificateStore();
		const { filter, jsonConfig, filterKanban } = storeToRefs(
			preCertificateStore
		);
		const lstFilterStatus = ref([]);
		const startSetup = async () => {
			if (!jsonConfig.value) {
				jsonConfig.value = await preCertificateStore.getConfig();
			}
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
		startSetup();
		const checkedValues = ref([]);

		return {
			filterKanban,
			checkedValues,
			jsonConfig,
			filter,
			preCertificateStore,
			lstFilterStatus,
			isMobile
		};
	},

	methods: {
		handleHide(bvEvent) {
			if (!this.isCloseable) {
				bvEvent.preventDefault();
			} else {
				this.$refs.dropdown.hide();
			}
		},
		// disabledToDate(current) {
		// 	// Disable dates before the "from" date
		// 	if (!this.filterKanban.timeFilter.from) return false;
		// 	let endOfDay = moment(this.filterKanban.timeFilter.from).endOf("day");
		// 	return current && current < endOfDay;
		// },
		async searchFunction() {
			this.filterKanban.selectedStatus = this.lstFilterStatus
				.filter(option => option.checked === true)
				.map(option => option.value);
			await this.preCertificateStore.getPreCertificateAll();
			this.$emit("notifi-kanban", this.filterKanban);
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
