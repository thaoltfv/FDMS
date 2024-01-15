<template>
	<ValidationProvider
		tag="div"
		:name="label ? label : nonLabel"
		:vid="vid"
		:rules="rules"
		v-slot="{ errors }"
	>
		<label
			v-if="label"
			class="form-label font-weight-bold d-flex justify-content-between align-items-center color_content"
		>
			<div class="d-flex align-items-start">
				{{ $t(label) }} <b v-if="requiredIcon" class="ml-1 text-red">*</b>
				<span v-if="required" class="required">{{ $t("required") }}</span>
			</div>
			<span v-if="rules.includes('max:')" class="character-count">
				{{ value.length }}/{{ maxLength }}
			</span>
		</label>

		<div class="container_area">
			<input
				class="input_area ant-input color_content"
				:class="{
					inputError: errors[0] || errorMessage,
					input_center: text_center
				}"
				type="text"
				:disabled="disabled"
				v-model="valueMutator"
				@input="debounceInput"
				@change="onChange"
			/>
			<span v-if="sufix" class="suffix color_content">đ</span>
			<!--Message Error-->
			<span v-if="errors[0] || errorMessage" class="errors">{{
				errors[0] || errorMessage
			}}</span>
		</div>
	</ValidationProvider>
</template>

<script>
import { debounce } from "lodash-es";

export default {
	name: "InputCurrency",
	data() {
		return {
			valueMutator:
				this.value || this.value === 0 ? this.formatNumber(this.value) : 0,
			errorMessage: ""
		};
	},
	model: {
		prop: "value",
		event: "change"
	},
	components: {},
	props: {
		requiredIcon: {
			type: Boolean,
			default: false
		},
		disabled: {
			type: Boolean,
			default: false
		},
		v_model: {
			type: [Number, String],
			default: 1
		},
		label: {
			type: String,
			default: ""
		},
		nonLabel: {
			type: String,
			default: ""
		},
		rules: {
			type: String,
			default: ""
		},

		vid: {
			type: String,
			default: ""
		},
		text_center: {
			type: Boolean,
			default: false
		},
		placeholder: {
			type: String,
			default: ""
		},
		max: {
			type: [String, Number],
			default: 1000000000000
		},
		min: {
			type: [String, Number],
			default: ""
		},
		step: {
			type: [String, Number]
		},
		value: {
			type: [String, Number],
			default: ""
		},
		autocomplete: {
			type: String,
			default: ""
		},
		sufix: {
			type: Boolean,
			default: true
		},
		icon: {
			type: String,
			default: ""
		},
		addon: {
			type: String,
			default: ""
		},
		required: {
			type: Boolean,
			default: false
		},
		defaultValue: {
			type: [String, Number],
			default: "0"
		},
		formatter: {
			type: Function
		}
	},
	methods: {
		debounceInput: debounce(function(e) {
			this.onChange(e);
		}, 400),
		onChange(event) {
			if (event.target.value) {
				// if (event.target.value.match(/^(\d)+(\.\d+)*?$/g)) {
				if (
					event.target.value.match(
						/^(\d)+(\.\d+)*?$|^(\d)+(,\d+)*?$|^(\d)+(\s\d+)*?$/g
					)
				) {
					let valueChecked = event.target.value.match(
						/^(\d)+(\.\d+)*?$|^(\d)+(,\d+)*?$|^(\d)+(\s\d+)*?$/g
					)[0];
					let formatValue = this.supportClientAction(valueChecked);
					if (this.validateInput(formatValue)) {
						this.$emit("change", +formatValue);
						this.valueMutator = formatValue.replace(
							/\B(?=(\d{3})+(?!\d))/g,
							"."
						);
						this.errorMessage = "";
					}
				} else {
					this.errorMessage = "Vui lòng kiểm tra lại nhập không hợp lệ";
					this.$emit("error", event.target.value);
				}
			} else if (this.required || this.rules) {
				this.$emit("change", "");
			}
		},
		supportClientAction(value) {
			let formatValue = value;
			// Remove first character is zero
			if (value.match(/(^0+)([\d])/g)) {
				formatValue = value.replace(/(^0+)([\d])/g, "$2");
			}
			if (value.includes(" ")) {
				formatValue = value.replace(/\s*/g, "");
			} else if (value.includes(".")) {
				formatValue = value.replace(/\.*/g, "");
			} else if (value.includes(",")) {
				formatValue = value.replace(/,*/g, "");
			}
			// Remove dot group when copy from another place
			formatValue = formatValue.replace(/(\.+)/g, "");
			return formatValue;
		},
		validateInput(value) {
			if (value < this.min) {
				this.errorMessage = `Đơn giá phải lớn hơn ${this.formatNumber(
					this.min
				)} đ`;
				return false;
			}
			if (value > this.max) {
				this.errorMessage = `Đơn giá phải nhỏ ${this.formatNumber(this.max)} đ`;
				return false;
			}
			return true;
		},
		formatNumber(value) {
			if (value !== "") {
				// let num = (value / 1).toFixed(0).replace('.', ',')
				return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
			} else return value;
		}
	}
};
</script>

<style lang="scss" scoped>
.inputError {
	border: 1px solid red !important;
}
.errors {
	color: #cd201f;
	padding: 5px 0 0;
	display: block;
	font-size: 12px;
}
.container_area {
	position: relative;
}
.suffix {
	position: absolute;
	right: 1rem;
	top: 6px;
}
.input_area {
	text-align: end;
	width: 100%;
	height: 2.295rem;
	padding-right: 2rem;
	border: 1px solid #d9d9d9;
	border-radius: 5px;
}
.input_center {
	text-align: center !important;
	padding-right: unset !important;
	padding-left: unset !important;
}
.character-count {
	font-size: 12px;
	font-weight: normal;
	color: #999999;
}
</style>
