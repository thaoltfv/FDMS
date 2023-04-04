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
          <span v-if="required" class="required">{{$t('required')}}</span>
        </div>
        <span v-if="rules.includes('max:')" class="character-count">
          {{value.length}}/{{maxLength}}
        </span>
      </label>

      <div :style="styleInputContainer"  :class="{'has__error': errors[0]}">
        <a-input class="color_content"
                :placeholder="placeholder"
				:id="id"
                 :tabindex="tabindex"
                 :value="value"
                 :max-length="maxLength"
                 :max= "max"
                 :min="min"
                 :checked="checked"
                 :disabled="disabledInput"
                 :type="type"
                 :name = "name"
                 :multiple= "multiple"
                 :addon-after="addon_after"
                 :suffix="suffix"
                 :autocomplete="autocomplete"
                 :style="styleInput"
                 @keypress="type === 'number' ? preventE($event) : ''"
                 @input="handleType($event)"
        >
          <a-icon v-if="icon" slot="prefix" :type="icon"/>
        </a-input>

        <!--Message Error-->
        <span v-if="errors[0]" class="errors">{{ errors[0] }}</span>
      </div>
  </ValidationProvider>
</template>

<script>
export default {
	name: 'InputText',

	model: {
		prop: 'value',
		event: 'change'
	},

	props: {
		id: {
			type: String,
			default: ''
		},
		label: {
			type: String,
			default: ''
		},
		nonLabel: {
			type: String,
			default: ''
		},
		styleLabel: {
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

		maxLength: {
			type: [String, Number]
		},
		max: {
			type: [String, Number, Object]
		},
		min: {
			type: [String, Number, Object]
		},
		checked: {
			type: Boolean,
			default: false
		},
		name: {
			type: String,
			default: ''
		},
		styleInput: {
			type: String,
			default: ''
		},
		styleInputContainer: {
			type: String,
			default: ''
		},
		multiple: {
			type: Boolean,
			default: false
		},
		value: {
			type: [String, Number, Object],
			default: ''
		},

		disabledInput: {
			type: Boolean,
			default: false
		},

		type: {
			type: String,
			default: 'text'
		},

		autocomplete: {
			type: String,
			default: ''
		},

		icon: {
			type: String,
			default: ''
		},

		column: {
			type: String,
			default: ''
		},

		required: {
			type: Boolean,
			default: false
		},

		tabindex: {
			type: String
		},
		addon_after: {
			type: String
		},
		suffix: {
			type: String
		}
	},
	methods: {
		preventE (evt) {
			if ((evt.which !== 8 && evt.which !== 0) && (evt.which < 48 || evt.which > 57)) {
				evt.preventDefault()
			}
		},
		handleType ($event) {
			if (this.type !== 'number') {
				this.$emit('change', $event.target.value)
				return
			}
			let value = $event.target.value
			let one = '1'
			let valueLength = one.padEnd(this.maxLength + 1, 0)

			if (value < parseInt(valueLength)) {
				this.$emit('change', $event.target.value)
			}
		},
		nameKeydown (e) {
			const keyCode = e.which
			if (!((keyCode >= 48 && keyCode <= 57) || (keyCode >= 65 && keyCode <= 90) || (keyCode >= 97 && keyCode <= 122)) && keyCode !== 188 && keyCode !== 8 && keyCode !== 32) {
				e.preventDefault()
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
.color_content{
	@media print{
		border: none !important;
		outline: none !important;
		color: black !important;
		padding-left: 0 !important;
	}
}
</style>
