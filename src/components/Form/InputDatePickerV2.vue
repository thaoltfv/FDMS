<template>
  <ValidationProvider tag="div"
                      :name="label"
                      :vid="vid"
                      :rules="rules"
                      v-slot="{ errors }">
    <label class="form-label font-weight-bold">
      {{ $t(label) }}
      <span v-if="required" class="required">{{ $t('required') }}</span>
    </label>

    <div :class="{'has__error': errors[0]}">
      <a-date-picker
        style="width: 100%;"
        :show-time="showTime ? { format: 'HH:mm' } : false"
        :placeholder="placeholder"
        :value="getDate"
        :format="formatDate"
        :disabled="disabled"
        :locale="locale"
        @change="onChange"/>
        <!-- v-bind:disabledDate="date ? date : disabledDate" -->

      <!--Message Error-->
      <span v-if="errors[0]" class="errors">{{ errors[0] }}</span>
    </div>
  </ValidationProvider>
</template>

<script>
import moment from 'moment'
import locale from 'ant-design-vue/es/date-picker/locale/vi_VN'

export default {
	name: 'InputDatePicker',
	// setup () {
	//   const disabledDate = current => {
	//     return current && current < moment().endOf('day')
	//   }
	//   return {
	//     moment,
	//     disabledDate
	//   }
	// },
	data () {
		return {
			locale
		}
	},

	model: {
		prop: 'value',
		event: 'change'
	},

	props: {
		formatDate: {
			type: String,
			default: 'DD-MM-YYYY HH:mm'
		},

		placeholder: {
			type: String,
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

		value: {
			type: String,
			default: ''
		},

		required: {
			type: Boolean,
			default: false
		},

		showTime: {
			type: Boolean,
			default: false
		},
		picker: {
			type: String,
			default: ''
		},
		date: {
			type: Function,
			default: null
		},
		disabled: {
			type: Boolean,
			default: false
		}
	},
	computed: {
		getDate () {
			return this.value ? moment(this.value, 'DD-MM-YYYY HH:mm') : null
		}
	},
	methods: {
		onChange (date, dateString) {
			return this.$emit('change', dateString)
		},
		disabledDate (current) {
			return current && current > moment().endOf('day')
		}
	}
}

</script>
<style lang="scss" scoped>
/deep/ .ant-calendar-picker {
  .anticon-close-circle {
    opacity: 1;
    color: #c7c6c6;
    right: 10px;
    font-size: 15px;
  }
}
</style>
