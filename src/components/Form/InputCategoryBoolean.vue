<template>
  <ValidationProvider tag="div"
                      :name="label ? label : nonLabel"
                      :vid="vid"
                      :rules="rules+'|not_emoji'"
                      v-slot="{ errors }">
    <label v-if="label" class="form-label font-weight-bold position-relative d-flex align-items-start color_content">
      {{ $t(label)  }}
      <span v-if="required" class="required">{{$t('required')}}</span>

      <a-popconfirm
        v-if="popover"
        :title="$t('popover_select_area')"
        :ok-text="$t('yes')"
        :cancel-text="$t('no')"
        placement="topRight"
        @confirm="handleConfirm"
        @cancel="handleCancel">
        <p ref="btnPopover" class="text-popover">Trigger</p>
      </a-popconfirm>
    </label>

    <div :class="{'has__error': errors[0] || showError}">
      <a-select
        class="color_content"
        show-search
        style="width: 100%"
        :value="value === null ? undefined : (+value === 1 ? 1 : 0)"
        :filter-option="filterOption"
        :disabled="disabled"
        @popupScroll="scrollOption($event)"
        :placeholder="placeholder"
        option-filter-prop="children"
        @change="handleChange">
        <!-- <a-select-option :value="null" :disabled="false" >
          {{$t('select_option_empty')}}
        </a-select-option> -->
        <a-select-option
          class="color_content"
          v-for="(item, index) in options.data"
          :key="index"
          :value="item[options.id]"
          :disabled="item[options.disabled]? item[options.disabled] : false"
        >
          {{ item[options.code] ? item[options.code] + '：' : '' }}{{ item[options.key] }}
        </a-select-option>
      </a-select>

      <!--Message Error-->
      <span v-if="errors[0]" class="errors">{{ errors[0] }}</span>
    </div>
  </ValidationProvider>
</template>

<script>
export default {
	name: 'InputCategory',

	model: {
		prop: 'value',
		event: 'change'
	},

	props: {
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

		value: {
			type: [Boolean],
			default: null
		},

		options: {
			type: Object,
			required: true
		},

		required: {
			type: Boolean,
			default: false
		},

		disabled: {
			type: Boolean,
			default: false
		},
		filterRole: {
			type: Boolean,
			default: false
		},

		popover: {
			type: Boolean,
			default: false
		},
		showError: {
			type: Boolean,
			default: false
		},
		placeholder: {
			type: String,
			default: ''
		}
	},

	methods: {
		handleChange (value) {
			this.$emit('change', value !== 0)

			if (this.filterRole) {
				this.$emit('handle-filter-role')
			}

			if (this.popover) {
				setTimeout(() => {
					this.$refs.btnPopover.click()
				}, 500)
			}
		},

		filterOption (input, option) {
			return (
				option.componentOptions.children[0].text.toLowerCase().indexOf(input.toLowerCase().trim()) >= 0
			)
		},

		scrollOption (e) {
			this.$emit('scrollChange', e)
		},

		handleConfirm (e) {
			this.$emit('popoverConfirm', e)
		},

		handleCancel (e) {
			this.$emit('popoverCancel', e)
		}
	}
}
</script>

<style lang="scss" scoped>
  .text-popover {
    opacity: 0;
    padding: 0;
    position: absolute;
    right: 0;
    top: 0;
    border: none;
    background: none;
    color: transparent;
    user-select: none;
    margin: 0;
    width: 0;
    height: 0;
  }
</style>
