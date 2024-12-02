<template>
  <ValidationProvider tag="div"
                      :name="label"
                      :vid="vid"
                      :rules="rules+'|not_emoji'"
                      v-slot="{ errors }">
    <label v-if="label"
           class="form-label font-weight-bold d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-start">
        {{ $t(label) }}
        <span v-if="required" class="required">{{$t('required')}}</span>
      </div>
      <span v-if="rules.includes('max:')" class="character-count">
          {{value.length}}/{{maxLength}}
        </span>
    </label>

    <div :class="{'has__error': errors[0]}">
      <InputNumber
        :placeholder="placeholder"
        @keypress="type === 'number' ? preventE($event) : ''"
        :default-value="defaultValue"
        :formatter="formatter"
        :parser="valueFormat => valueFormat.replace(/\$\s?|(,*)/g, '')"
        :value="value"
        :disabled="disabledInput"
        :max="max"
        :min="min"
        :step="step"
        :addon-after="addon"
        @change="onChange"
        @input="handleType($event)"
      >
      <a-icon v-if="icon" slot="prefix" :type="icon"/>
      </InputNumber>
      <!--Message Error-->
      <span v-if="errors[0]" class="errors">{{ errors[0] }}</span>
    </div>
  </ValidationProvider>
</template>

<script>
import {InputNumber} from 'ant-design-vue'
export default {
	name: 'InputNumberValueCustom',

	model: {
		prop: 'value',
		event: 'changed'
	},
	components: {
		InputNumber
	},
	props: {
		disabledInput: {
			type: Boolean,
			default: false
		},
		v_model: {
			type: [Number, String],
			default: ''
		},
		label: {
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

		placeholder: {
			type: String,
			default: ''
		},
		max: {
			type: [String, Number]
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
		}
	},

	methods: {
		onChange (value) {
			this.$emit('change', value)
		},
		formatNumber (value) {
			let num = (value / 1).toFixed(0).replace('.', ',')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
		},
		preventE (evt) {
			if ((evt.which !== 8 && evt.which !== 0) && (evt.which < 48 || evt.which > 57)) {
				evt.preventDefault()
			}
		},
		handleType ($event) {
			this.$emit('change', $event.target.value)
			let value = $event.target.value
			let one = '1'
			let valueLength = one.padEnd(this.maxLength + 1, 0)

			if (value < parseInt(valueLength)) {
				this.$emit('change', $event.target.value)
			}
		}
	}
}
</script>

<style lang="scss" scoped>
.character-count {
  font-size: 12px;
  font-weight: normal;
  color: #999999;
}
</style>
