<template>
  <div class="switch-active">
    <label
           class="form-label font-weight-bold">
      Tìm theo tờ, thửa
    </label>

    <a-switch v-model="switchValue" :disabled="disabled"/>
  </div>
</template>

<script>
export default {
	name: 'InputSwitch',

	props: {
		label: {
			type: String,
			default: ''
		},

		disabled: {
			type: Boolean,
			default: false
		},

		value: {
			type: Boolean,
			required: false,
			default: false
		}
	},

	computed: {
		switchValue: {
			get () {
				return this.value
			},
			set (val) {
				this.$emit('input', val)
			}
		}
	}
}
</script>

<style lang="scss" scoped>
  /deep/.ant-switch {
    min-width: 80px;
    height: 23px;
    background: #DDDDDD;
    &:before {
      content: 'Tắt';
      font-size: 10px;
      color: #ffffff;
      position: absolute;
      top: 0;
      left: 27px;
    }
    &:after {
      top: 50%;
      transform: translateY(-50%);
      left: -2px;
      width: 27px;
      height: 27px;
      border: 1px solid #DDDDDD;
      box-shadow: 0 3px 6px rgba(0,0,0,.16);
    }
    &:active {
      &::before {
        width: unset;
      }
      &::after {
        //width: 20px;
      }
    }
    &-checked {
      background: #FAA831;
      transform: unset;
      &:before {
        content: 'Bật';
        font-size: 12px;
        color: #ffffff;
        position: absolute;
        top: 0;
        left: 10px;
      }
      &:after {
        left: 52px;
        //transform: translate(0,-50%);
        margin-left: 1px;
      }
      &:active {
        &::before {
          //width: unset;
        }
      }
    }
  }
</style>
