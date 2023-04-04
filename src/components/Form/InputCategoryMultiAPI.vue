<template>
  <ValidationProvider tag="div"
                      :name="label"
                      :vid="vid"
                      :rules="rules"
                      v-slot="{ errors }">
    <label v-if="label"
           class="form-label font-weight-bold">
      {{ $t(label) }}
      <span v-if="required"
            class="required"
            v-text="$t('required')"/>
    </label>

    <div :class="{'has__error': errors[0]}">
      <div class="custom-arrow">
        <a-select
          mode="multiple"
          style="width: 100%"
          label-in-value
          option-filter-prop="children"
          :value="Object.keys(inputValue).length ? inputValue : undefined"
          :placeholder="$t('select_option_empty')"
          :filter-option="filterOption"
          :not-found-content="fetching ? undefined : null"
          @focus="fetchData"
          @search="fetchData"
          @change="handleChange">
          <a-spin v-if="fetching"
                  slot="notFoundContent"
                  size="small"/>

          <a-select-option v-for="(item, index) in data"
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
import { debounce, trim } from 'lodash-es'
import Model from '@/models/Model'

export default {
	name: 'InputCategoryMultiAPI',

	model: {
		prop: 'value',
		event: 'change'
	},

	props: {
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
			type: Array
		},

		options: {
			type: Object,
			required: true
		},

		required: {
			type: Boolean,
			default: false
		},

		modelJavel: {
			type: [Model, Function],
			required: true
		}
	},

	data () {
		this.lastFetchId = 0
		this.fetchData = debounce(this.fetchData, 800)

		return {
			data: [],
			fetching: false
		}
	},

	computed: {
		inputValue () {
			let newArr = this.value.map(item => {
				return Object.assign({
					key: trim(item.id),
					label: trim(item.name)
				})
			})
			return newArr
		}
	},

	methods: {
		handleChange (value) {
			let newArr = value.map(item => {
				return Object.assign({
					id: trim(item.key),
					name: trim(item.label)
				})
			})
			this.$emit('change', newArr)
		},

		async fetchData (value) {
			this.lastFetchId += 1
			this.data = []
			this.fetching = true
			const fetchId = this.lastFetchId

			const response = await this.modelJavel.paginate({
				query: {
					page: 1,
					per_page: value ? 0 : 20,
					'filters[name]': value || '',
					'filters[is_active]': 1,
					'sortBy[updated_at]': 'desc'
				}
			})

			if (fetchId !== this.lastFetchId) {
				// for fetch callback order
				return
			}

			const data = response.data.map(item => ({
				id: item.id,
				name: item.name
			}))

			this.data = data.length ? data : []
			this.fetching = false
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
