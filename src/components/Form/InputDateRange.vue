<template>
  <ValidationProvider tag="div"
                      :name="label"
                      :vid="vid"
                      :rules="rules"
                      v-slot="{ errors }">
    <label class="form-label">
      {{ $t(label) }}
      <span v-if="required" class="required">{{$t('required')}}</span>
    </label>

    <div :class="{'has__error': errors[0]}">
      <a-range-picker
        style="width: 100%;"
        :show-time="{ format: 'HH:mm' }"
        :locale="locale"
        :format="formatDate"
        :value="getDate || null"
        :placeholder="[$t('start_time'), $t('end_time')]"
        @change="onChange">
        <a-icon slot="suffixIcon" type="calendar" />
      </a-range-picker>

      <!--Message Error-->
      <span v-if="errors[0]" class="errors">{{ errors[0] }}</span>
    </div>
  </ValidationProvider>
</template>

<script>
import moment from 'moment'
import locale from 'ant-design-vue/es/date-picker/locale/ja_JP'

export default {
	name: 'InputDateRange',

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
			default: 'YYYY-MM-DD'
		},

		label: {
			type: String,
			required: true,
			default: ''
		},

		vid: {
			type: String,
			default: ''
		},

		rules: {
			type: String,
			default: ''
		},

		value: {
			type: Array,
			required: true
		},

		required: {
			type: Boolean,
			default: false
		}
	},

	computed: {
		getDate () {
			if (this.value && this.value.length && this.value[0] && this.value[1]) {
				return [moment(this.value[0]), moment(this.value[1])]
			}
		}
	},

	methods: {
		onChange (date, dateString) {
			return this.$emit('change', dateString)
		}
	}
}
</script>

<style scoped>

</style>
