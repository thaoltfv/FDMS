<template>
	<ValidationProvider tag="div" :name="label" :vid="vid" :rules="rules">
		<label
			class="form-label font-weight-bold position-relative d-flex align-items-start color_content"
		>
			{{ $t(label) }}
		</label>

		<div class="d-flex align-items-center">
			<InputNumber
				:placeholder="placeholder"
				@keypress="type === 'number' ? preventE($event) : ''"
				:parser="valueFormat => valueFormat.replace(/\$\s?|(,*)/g, '')"
				:max="max"
				:min="min"
				:default-value="defaultValue"
				:addon-after="addon"
				:step="0.25"
				:v-model="value"
				@change="onChange"
			>
			</InputNumber>
			<div class="ml-1">
				<span>Giờ</span>
			</div>
		</div>
	</ValidationProvider>
</template>

<script>
import { InputNumber } from "ant-design-vue";
export default {
	name: "InputNumberMinute",

	model: {
		prop: "value",
		event: "changed"
	},
	components: {
		InputNumber
	},
	props: {
		v_model: {
			type: [Number, String],
			default: 1
		},
		label: {
			type: String,
			default: ""
		},
		text_center: {
			type: Boolean,
			default: false
		},

		rules: {
			type: String,
			default: ""
		},

		vid: {
			type: String,
			default: ""
		},

		placeholder: {
			type: String,
			default: ""
		},
		max: {
			type: [String, Number]
		},
		min: {
			type: [String, Number]
		},
		step: {
			type: [String, Number]
		},
		// value: {
		// 	type: [String, Number],
		// 	default: 1
		// },

		required: {
			type: Boolean,
			default: false
		},
		defaultValue: {
			type: [String, Number],
			default: 0
		},
		formatter: {
			type: Function
		},
		requiredIcon: {
			type: Boolean,
			default: false
		},
		addon: {
			type: String,
			default: "phút"
		}
	},
	data() {
		return {
			value: 0
		};
	},
	methods: {
		onChange(value) {
			if (value && value !== 0 && value !== "") {
				const convertToMiliseconds = value * 60 * 60000;
				this.$emit("changeHour", convertToMiliseconds);
			} else {
				this.value = 0;
				this.$emit("changeHour", 0);
			}
		},
		formatNumber(value) {
			let num = (value / 1).toFixed(0).replace(".", ",");
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
		},
		preventE(evt) {
			if (
				evt.which !== 8 &&
				evt.which !== 0 &&
				(evt.which < 48 || evt.which > 57)
			) {
				evt.preventDefault();
			}
		},
		handleType($event) {
			let value = $event.target.value;
			let one = "1";
			let valueLength = one.padEnd(this.maxLength + 1, 0);
		}
	}
};
</script>

<style lang="scss" scoped>
.character-count {
	font-size: 12px;
	font-weight: normal;
	color: #999999;
}
.text_center {
	/deep/ .ant-input-number-input {
		text-align: center !important;
	}
}
</style>
