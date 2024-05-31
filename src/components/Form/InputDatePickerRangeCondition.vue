<template>
	<div class="date-picker-range">
		<!--label-->
		<label
			v-if="label"
			class="form-label font-weight-bold d-flex align-items-start"
		>
			{{ $t(label) }}
			<span v-if="required" class="required">
				{{ $t("required") }}
			</span>
		</label>

		<div class="d-flex justify-content-between align-items-start">
			<ValidationProvider
				tag="div"
				class="w-100"
				:name="nameStart"
				:vid="vidStartDatetime"
				:rules="rulesStart"
				v-slot="{ errors }"
			>
				<!--datepicker from - to-->
				<div :class="{ has__error: errors[0] }">
					<a-date-picker
						show-time
						v-model="startValue"
						:format="formatDate"
						:disabled-date="disabledStartDate"
						:locale="locale"
						placeholder="Từ ngày"
						@openChange="handleStartOpenChange"
						@change="chooseStartDate"
					/>

					<!--Message Error-->
					<span v-if="errors[0]" class="errors">
						{{ errors[0] }}
					</span>
				</div>
			</ValidationProvider>

			<span class="tilde mt-2">-</span>

			<ValidationProvider
				tag="div"
				class="w-100"
				:name="nameEnd"
				:vid="vidEndDatetime"
				:rules="rulesEnd"
				v-slot="{ errors }"
			>
				<!--datepicker from - to-->
				<div :class="{ has__error: errors[0] }">
					<a-date-picker
						show-time
						v-model="endValue"
						:format="formatDate"
						:disabled-date="disabledEndDate"
						:locale="locale"
						placeholder="Đến ngày"
						:open="endOpen"
						@openChange="handleEndOpenChange"
						@change="chooseEndDate"
					/>

					<!--Message Error-->
					<span v-if="errors[0]" class="errors">
						{{ errors[0] }}
					</span>
				</div>
			</ValidationProvider>
		</div>
	</div>
</template>

<script>
import locale from "ant-design-vue/es/date-picker/locale/vi_VN";
import moment from "moment";

export default {
	name: "InputDatePickerRangeCondition",

	data() {
		return {
			locale,
			startValue: null,
			endValue: null,
			endOpen: false
		};
	},

	model: {
		prop: "value",
		event: "change"
	},

	props: {
		formatDate: {
			type: String,
			default: "DD-MM-YYYY"
		},

		label: {
			type: String,
			default: ""
		},

		nameStart: {
			type: String,
			default: ""
		},

		nameEnd: {
			type: String,
			default: ""
		},

		vidStartDatetime: {
			type: String,
			default: ""
		},

		vidEndDatetime: {
			type: String,
			default: ""
		},

		rulesStart: {
			type: String,
			default: ""
		},

		rulesEnd: {
			type: String,
			default: ""
		},

		required: {
			type: Boolean,
			default: false
		},

		placeholder: {
			type: String,
			default: ""
		},

		startDateValue: {
			type: String,
			default: null
		},

		endDateValue: {
			type: String,
			default: null
		}
	},

	created() {
		if (this.startDateValue && this.endDateValue) {
			this.startValue = moment(this.startDateValue, "DD-MM-YYYY");
			this.endValue = moment(this.endDateValue, "DD-MM-YYYY");
		}
	},

	methods: {
		disabledStartDate(startValue) {
			const endValue = this.endValue;
			if (!startValue || !endValue) {
				return false;
			}
			return startValue.valueOf() > endValue.valueOf();
		},

		disabledEndDate(endValue) {
			const startValue = this.startValue;
			let startValueTemp = new Date(
				new Date(startValue).setMonth(new Date(startValue).getMonth() + 6)
			);
			if (!endValue || !startValue) {
				return false;
			} else if (endValue && endValue.valueOf() > startValueTemp) {
				return true;
			} else return startValue.valueOf() >= endValue.valueOf();
		},

		handleStartOpenChange(open) {
			if (!open) {
				this.endOpen = true;
			}
		},

		handleEndOpenChange(open) {
			this.endOpen = open;
		},

		chooseStartDate(date, dateString) {
			this.$emit("startDate", dateString);
		},

		chooseEndDate(date, dateString) {
			this.$emit("endDate", dateString);
		}
	}
};
</script>

<style lang="scss" scoped>
/deep/.ant-calendar-picker {
	.anticon-close-circle {
		opacity: 1;
		color: #c7c6c6;
		right: 10px;
		font-size: 15px;
	}
}
</style>
