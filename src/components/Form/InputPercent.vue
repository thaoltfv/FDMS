<template>
  <ValidationProvider tag="div"
                      :name="label ? label : nonLabel"
                      :vid="vid"
                      :rules="rules+'|not_emoji'"
                      v-slot="{ errors }">
    <label v-if="label"
           class="form-label font-weight-bold d-flex justify-content-between align-items-center color_content">
      <div class="d-flex align-items-start">
        {{ $t(label) }}
        <!-- <span v-if="required" class="required">{{$t('required')}}</span> -->
      </div>
      <!-- <span v-if="rules.includes('max:')" class="character-count">
          {{value.length}}/{{maxLength}}
        </span> -->
    </label>

    <div class="container_area">
      <input
        class="input_area ant-input color_content"
        :class="{'inputError':errors[0] || errorMessage, 'input_center': text_center}"
        type="text"
        :disabled="disabled"
        v-model="valueArea"
        @input="debounceInput"
      />
      <span class="suffix color_content">%</span>
      <!--Message Error-->
      <span v-if="errors[0] || errorMessage" class="errors">{{ errors[0] || errorMessage }}</span>
    </div>
  </ValidationProvider>
</template>

<script>
import { debounce } from 'lodash-es'
export default {
	name: 'InputArea',
	data () {
		return {
			valueArea: this.value ? this.formatNumber(this.value) : 0,
			errorMessage: ''
		}
	},
	model: {
		prop: 'value',
		event: 'change'
	},
	components: {

	},
	props: {
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
			default: ''
		},
		nonLabel: {
			type: String,
			default: ''
		},

		rules: {
			type: String,
			default: ''
		},

		vid: {
			type: String,
			default: ''
		},
		text_center: {
			type: Boolean,
			default: false
		},
		placeholder: {
			type: String,
			default: ''
		},
		max: {
			type: [String, Number],
			default: 9999999999999
		},
		min: {
			type: [String, Number],
			default: 0
		},
		step: {
			type: [String, Number]
		},
		value: {
			type: [String, Number],
			default: ''
		},
		autocomplete: {
			type: String,
			default: ''
		},

		icon: {
			type: String,
			default: ''
		},
		addon: {
			type: String,
			default: ''
		},
		required: {
			type: Boolean,
			default: false
		},
		defaultValue: {
			type: [String, Number],
			default: ''
		},
		formatter: {
			type: Function
		},
		decimal: {
			type: [String, Number],
			default: 3
		}
	},

	methods: {
		debounceInput: debounce(function (e) {
			this.onChange(e)
		}, 500),
		async onChange (event) {
			if (event.target.value) {
				if (event.target.value.match(/^\d+(\.\d+)*(,\d+)?$|^\d+(,\d+)*(\.\d+)?$/g)) {
					let valueChecked = event.target.value.match(/^\d+(\.\d+)*(,\d+)?$|^\d+(,\d+)*(\.\d+)?$/g)[0]
					let formatValue = this.supportClientAction(valueChecked)
					if (this.validateInput(formatValue)) {
						let formatNumberDecimal = +parseFloat(formatValue).toFixed(this.decimal)
						this.$emit('change', formatNumberDecimal)
						let convertedValue = formatNumberDecimal.toString().replace('.', ',')
						// change value number to dot format
						this.valueArea = this.formatNumber(convertedValue)
						this.errorMessage = ''
					}
				} else {
					// this.rules = 'required'
					this.errorMessage = 'Vui lòng kiểm tra lại nhập không hợp lệ'
				}
			} else if (this.required || this.rules) {
				this.$emit('change', '')
			}
		},
		supportClientAction (value) {
			// Remove first character is zero
			let formatValue = value
			// Remove dot group when copy from another place
			if (value.match(/^\d+(\.\d+)*(,\d+)?$/g)) {
				if (value.match(/(,+)/g)) {
					formatValue = formatValue.replace(/(\.+)/g, '')
					formatValue = formatValue.replace(/(,+)/g, '.')
				}
				return formatValue
			} else {
				formatValue = formatValue.replace(/(,+)/g, '')
				return formatValue
			}
		},
		validateInput (value) {
			if (value <= this.min) {
				this.errorMessage = `${this.label} phải lớn hơn ${this.min}`
				return false
			}
			if (value > this.max) {
				this.errorMessage = `${this.label} phải nhỏ ${this.max}`
				return false
			}
			return true
		},
		formatNumber (num) {
			// convert number to dot format
			return num.toString().replace(/^[+-]?\d+/, function (int) {
				return int.replace(/(\d)(?=(\d{3})+$)/g, '$1.')
			})
		}
	}
}
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
  padding-right: 2.5rem;
  border: 1px solid #d9d9d9;
  border-radius: 5px
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
