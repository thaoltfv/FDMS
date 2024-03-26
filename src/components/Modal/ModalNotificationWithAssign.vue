<template>
	<div class="modal-delete d-flex justify-content-center align-items-center">
		<div class="card">
			<div
				class="card-header d-flex justify-content-between align-items-center py-3"
			>
				<h3 class="font-weight-bold mb-0">
					<!--          {{$t('btn_delete')}}-->
					Thông báo
				</h3>

				<img
					@click="handleCancel"
					src="../../assets/images/icon-btn-back.svg"
					alt="icon"
				/>
			</div>

			<div class="card-body">
				<h5
					style="font-size: 18px"
					v-html="
						`${notification}${
							status_text &&
							status_text != 'Khôi phục' &&
							status_text != 'Hủy' &&
							status_text != 'Từ chối'
								? '<br>&quot;' + status_text + '&quot;'
								: ''
						}`
					"
					class="padding-bottom : 5px"
				></h5>
				<div>
					<InputCategory
						v-if="appraiser"
						v-model="chosenAppraiser"
						vid="chosenAppraiser"
						:label="labelAppraiser"
						rules="required"
						:requiredIcon="true"
						class="form-group-container col-12"
						:options="optionsAppraisers"
					/>

					<div
						v-if="
							appraiser &&
							status_text &&
							status_text != 'Khôi phục' &&
							status_text != 'Hủy' &&
							status_text != 'Từ chối'
						"
						class="form-group-container row"
					>
						<InputDatePickerV2
							v-model="estimateCompleteDate"
							vid="estimateCompleteDate"
							label="Thời gian hoàn thành dự kiến"
							rules="required"
							:requiredIcon="true"
							class="col-6"
						/>
						<InputNumberMinute
							@changeHour="changeEstimateComplete"
							v-model="estimateCompleteTime"
							vid="estimateCompleteTime"
							label="Thời gian gia hạn"
							rules="required"
							:requiredIcon="true"
							class="col-6"
						/>
					</div>

					<InputCategory
						v-if="notification == `Bạn có muốn 'Từ chối' hồ sơ này?`"
						v-model="reason_id"
						vid="reason_id"
						label="Lí do"
						class="mb-3"
						:options="optionsReasons"
					/>
					<InputCategory
						v-if="notification == `Bạn có muốn 'Hủy' hồ sơ này?`"
						v-model="reason_id"
						vid="reason_id"
						label="Lí do hủy sơ bộ"
						class="mb-3"
						:options="optionsReasonsCancelPC"
					/>

					<InputTextarea
						:rows="rows"
						:autosize="false"
						:maxLength="1000"
						v-model="note"
						label="Ghi chú"
						class="form-group-container mb-3"
					/>
				</div>
				<div v-if="!isMobile()" class="btn__group">
					<button
						class="btn btn-white font-weight-normal font-weight-bold"
						@click.prevent="handleCancel"
						v-text="$t('popup_btn_no')"
					/>
					<button
						class="btn btn-white btn-orange font-weight-bold mt-md-0 mt-2"
						@click.prevent="handleAction(note, reason_id)"
						v-text="$t('popup_btn_yes')"
					/>
				</div>
				<div v-else class="btn__group row" style="padding: 0">
					<div class="col-6" style="padding: 0; margin-top: 8px">
						<button
							class="btn btn-white font-weight-normal font-weight-bold"
							@click.prevent="handleCancel"
							v-text="$t('popup_btn_no')"
						/>
					</div>
					<div class="col-6" style="padding: 0">
						<button
							class="btn btn-white btn-orange font-weight-bold mt-md-0 mt-2"
							@click.prevent="handleAction(note, reason_id)"
							v-text="$t('popup_btn_yes')"
						/>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import { ref } from "vue";
import { storeToRefs } from "pinia";
import { usePreCertificateStore } from "@/store/preCertificate";
import { useWorkFlowConfig } from "@/store/workFlowConfig";
import InputTextarea from "@/components/Form/InputTextarea";
import InputCategory from "@/components/Form/InputCategory";
import WareHouse from "@/models/WareHouse";
import InputDatePickerV2 from "../Form/InputDatePickerV2.vue";
import InputNumberMinute from "../Form/InputNumberMinute.vue";
export default {
	components: {
		InputTextarea,
		InputCategory,
		InputDatePickerV2,
		InputNumberMinute,
	},
	name: "ModalNotificationPreCertificateNote",
	data() {
		return {
			note: "",
			rows: 3,
			reason_id: null,
			reasons: [],
			reasonCancelPC: [],
		};
	},
	props: [
		"notification",
		"appraiser",
		"status_text",
		"workflowName",
		"status_next",
		"dataHSTD",
		"status_next",
	],
	setup(props) {
		const preCertificateStore = usePreCertificateStore();
		const { lstDataConfig } = storeToRefs(preCertificateStore);
		const configStore = useWorkFlowConfig();
		const { configs } = storeToRefs(configStore);
		const chosenAppraiser = ref(null);
		const tempEstimate = ref(null);
		const estimateCompleteDate = ref(null);
		const estimateCompleteTime = ref(null);
		const chosenAppraiserOriginal = ref(null);
		const labelAppraiser = ref(null);
		const getExpireStatusDate = (config) => {
			const configTemp = config ? config : null;
			let dateConvert = new Date();
			let minutes = configTemp.process_time ? configTemp.process_time : 1440;
			let dateConverted = dateConvert.getTime() + minutes * 60000;

			let status_expired_at = moment(dateConverted).format("DD-MM-YYYY HH:mm");

			return status_expired_at;
		};

		const getStart = async () => {
			chosenAppraiser.value = props.appraiser.id;
			chosenAppraiserOriginal.value = props.appraiser.id;
			if (!lstDataConfig.value.appraiser_sales) {
				await preCertificateStore.getStartData(
					true,
					false,
					false,
					false,
					false
				);
			}
			if (!configs.value[props.workflowName]) {
				await configStore.getConfig();
			}
			labelAppraiser.value =
				configs.value[props.workflowName].appraiser[props.appraiser.type];
			const config = configs.value[props.workflowName].principle.find(
				(item) => item.status === props.status_next && item.isActive === 1
			);

			let status_expired_at_temp = config.process_time
				? getExpireStatusDate(config)
				: null;

			estimateCompleteDate.value = status_expired_at_temp;
			let dateConvert = new Date();
			let minutes = config.process_time ? config.process_time : 1440;
			let dateConverted = dateConvert.getTime() + minutes * 60000;
			tempEstimate.value = dateConverted;
		};

		if (props.appraiser) getStart();
		return {
			chosenAppraiser,
			chosenAppraiserOriginal,
			tempEstimate,
			estimateCompleteDate,
			estimateCompleteTime,
			labelAppraiser,
			lstDataConfig,
			preCertificateStore,
		};
	},
	computed: {
		optionsReasons() {
			return {
				data: this.reasons,
				id: "id",
				key: "description",
			};
		},
		optionsReasonsCancelPC() {
			return {
				data: this.reasonCancelPC,
				id: "id",
				key: "description",
			};
		},

		optionsAppraisers() {
			return {
				data: this.lstDataConfig.appraiser_sales,
				id: "id",
				key: "name",
			};
		},
	},
	mounted() {
		this.getDictionary();
	},
	methods: {
		changeEstimateComplete(value) {
			let dateConverted = moment(this.tempEstimate).valueOf() + value;
			let status_expired_at = moment(dateConverted).format("DD-MM-YYYY HH:mm");
			this.estimateCompleteDate = status_expired_at;
		},
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
		},
		handleCancel(event) {
			this.$emit("cancel", event);
		},

		handleAction(note, reason_id) {
			if (
				this.notification == `Bạn có muốn 'Hủy' hồ sơ này?` &&
				!this.reason_id
			) {
				this.$toast.open({
					message: "Vui lòng chọn lý do hủy sơ bộ",
					type: "error",
					position: "top-right",
					duration: 3000,
				});
				return;
			}
			if (this.appraiser && !this.chosenAppraiser) {
				this.$toast.open({
					message: "Vui lòng chọn " + this.labelAppraiser,
					type: "error",
					position: "top-right",
					duration: 3000,
				});
				return;
			}
			const tempAppraiser =
				this.chosenAppraiser == this.chosenAppraiserOriginal
					? null
					: {
							id: this.chosenAppraiser,
							type: this.appraiser.type,
					  };
			this.$emit(
				"action",
				note,
				reason_id,
				tempAppraiser,
				this.estimateCompleteDate
			);
			this.$emit("cancel", note);
		},
		async getDictionary() {
			if (
				this.notification == `Bạn có muốn 'Từ chối' hồ sơ này?` ||
				this.notification == `Bạn có muốn 'Hủy' hồ sơ này?`
			) {
				const resp = await WareHouse.getDictionaries();
				this.reasons = resp.data.li_do;
				this.reasonCancelPC = resp.data.li_do_huy_so_bo;
			}
		},
		getExpireStatusDate(config) {
			const configTemp = config ? config : this.config;
			let dateConvert = new Date();
			let minutes = configTemp.process_time ? configTemp.process_time : 1440;
			let dateConverted = new Date(dateConvert.getTime() + minutes * 60000);
			let status_expired_at = moment(dateConverted).format("DD-MM-YYYY HH:mm");
			return status_expired_at;
		},
	},
};
</script>

<style lang="scss" scoped>
.modal-delete {
	position: fixed;
	z-index: 1002;
	left: 0;
	top: 0;
	width: 100%;
	height: 100%;
	background: rgba(0, 0, 0, 0.6);

	@media (max-width: 787px) {
		padding: 20px;
	}
	.card {
		max-width: 600px;
		width: 100%;
		margin-bottom: 0;
		&-header {
			border-bottom: 1px solid #dddddd;
			h3 {
				color: #333333;
			}
			img {
				cursor: pointer;
			}
		}
		&-body {
			text-align: center;
			padding: 40px;
			p {
				color: #333333;
				margin-bottom: 40px;
			}

			.btn__group {
				.btn {
					max-width: 150px;
					width: 100%;
					margin: 0 10px;
				}
			}
		}
	}
}
.btn {
	&-orange {
		background: #faa831;
		color: #ffffff;
		font-weight: bold !important;
	}
}
</style>
