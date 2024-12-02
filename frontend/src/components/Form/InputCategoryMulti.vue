<template>
  <ValidationProvider tag="div"
                      :name="label"
                      :vid="vid"
                      :rules="rules"
                      v-slot="{ errors }">
    <label class="form-label font-weight-bold">
      {{ $t(label) }}
      <span v-if="required" class="required">{{$t('required')}}</span>
    </label>

    <div :class="{'has__error': errors[0]}">
      <div class="custom-arrow">
        <a-select
          show-search
          mode="multiple"
          style="width: 100%"
          :value="value === '' ? undefined : value"
          :placeholder="$t('select_option_empty')"
          :filter-option="filterOption"
          :maxTagCount="maxTagCount"
          option-filter-prop="children"
          @change="handleChange">
          <a-select-option v-for="(item, index) in options.data"
                           :key="index"
                           :value="item[options.id]">
            {{ item[options.key] }}
          </a-select-option>
        </a-select>
        <a-icon type="down" :style="{ fontSize: '12px'}"/>
      </div>
      <!--Message Error-->
      <span v-if="errors[0]" class="errors">{{ errors[0] }}</span>
    </div>
  </ValidationProvider>
</template>

<script>
export default {
	name: 'InputCategoryMulti',

	model: {
		prop: 'value',
		event: 'change'
	},

	props: {
		label: {
			type: String,
			required: true,
			default: ''
		},
		maxTagCount: {
			type: Number,
			default: 1
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
			type: Array
		},

		options: {
			type: Object,
			required: true
		},

		required: {
			type: Boolean,
			default: false
		}
	},

	methods: {
		handleChange (value) {
			this.$emit('change', value)
		},

		filterOption (input, option) {
			return (
				option.componentOptions.children[0].text.toLowerCase().indexOf(input.toLowerCase().trim()) >= 0
			)
		}
	}
}
</script>

<style scoped>

</style>
