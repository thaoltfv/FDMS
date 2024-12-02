<template>
  <div class="form-radio">
    <label v-if="label"
           class="form-label font-weight-bold">
      {{ $t(label) }}
    </label>

    <a-radio-group name="radioGroup"
                   :value="value"
                   @change="handleChange">
      <a-radio v-for="(item, index) in options.data"
               :key="index"
               :value="item[options.value]">
        {{item[options.key]}}
      </a-radio>
    </a-radio-group>
  </div>
</template>

<script>
export default {
	name: 'InputRadio',

	model: {
		prop: 'value',
		event: 'change'
	},

	props: {
		label: {
			type: String,
			default: ''
		},

		options: {
			type: Object,
			required: true
		},

		value: {
			type: String,
			required: true,
			default: ''
		}
	},

	methods: {
		handleChange (e) {
			this.$emit('change', e.target.value)
		}
	}
}
</script>

<style lang="scss" scoped>
  .form-radio {
    /deep/ .ant-radio-group {
      display: flex;
      align-items: center;
      .ant-radio-wrapper {
        display: flex;
        align-items: center;
        margin-right: 26px;
        .ant-radio {
          &-checked {
            .ant-radio-inner {
              border-color: #0062AF;
              &:after {
                border-radius: unset;
                background-color: #0062AF;
              }
            }
            &:after {
              border: 1px solid #0062AF;
              border-radius: unset;
            }
          }
          &-inner {
            border-radius: unset;
          }
        }
        span {
          line-height: 2;
        }
      }
    }
  }
</style>
