<template>
  <div v-if="!isMobile()" class="checkbox-group">
    <a-checkbox-group :value="value" @change="handleChange">
      <a-checkbox
        v-for="(item, index) in options.data"
        :key="index"
        :value="item[options.value]"
        :class="item.class"
      >
        <span class="txt">{{ item[options.label] }}</span>
      </a-checkbox>
    </a-checkbox-group>
  </div>
  <div v-else>
    <!-- <div class="btn-filter">
			<button class="btn btn-orange btn-filter" @click="showDrawer"><img src="@/assets/icons/ic_log_history.svg" alt="history"> </button>
		</div> -->
		<div class="card" style="border: 0;">
      <div class="box_fixRight">
        <div class="btn-filter">
			<button class="btn btn-orange btn-filter" @click="showDrawer"><img src="@/assets/icons/ic_filter.svg" alt="history"> </button>
		</div>
      <div class="box_content">
        <a-checkbox-group :value="value" @change="handleChange">
          <div v-for="(item, index) in options.data"
            :key="index" style="padding-bottom: 5px;padding-top: 5px;">
          <a-checkbox

            :value="item[options.value]"

          >
            <span :class="item.class" style="color: white; font-weight: bold;">{{ item[options.label] }}</span>
          </a-checkbox>
          </div>
        </a-checkbox-group>
      </div>
    </div>
    </div>
  </div>
</template>

<script>
export default {
	name: 'ButtonCheckbox',

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
			type: Array,
			required: true,
			default: () => []
		}
	},
	data () {
		return {
			visible: false
		}
	},

	methods: {
		onClose () {
			this.visible = false
		},
		showDrawer () {
			this.visible = true
		},
		handleChange (value) {
			this.$emit('change', value)
		},
		isMobile () {
			if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
				return true
			} else {
				return false
			}
		}
	}
}
</script>

<style lang="scss" scoped>
.checkbox-group {
  /deep/ .ant-checkbox-group {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 6px;

    .ant-checkbox-wrapper {
      display: flex;
      flex-direction: row;
      justify-content: center;
      align-items: center;
      padding: 4px 10px;
      gap: 6px;
      isolation: isolate;
      border-radius: 3px;
      color: #FFFFFF;
      font-weight: 500;

      &__green {
        background: #69BD65;
      }
      &__orange {
        background: #FF963D;
      }
      &__cyan {
        background: #617F9E;
      }
      &__red {
        background: #de1616;
      }
      &__blue {
        background: #6e7582;
      }

      span {
        line-height: 2;
      }

      .ant-checkbox {
        .ant-checkbox-inner {
          width: 16px !important;
          height: 16px !important;
          top: 2px;
          border: 1px solid #FFFFFF !important;

          .ant-checkbox-input:focus+.ant-checkbox-inner {
            border-color: #FFFFFF !important;
          }
        }
      }

      .ant-checkbox-checked {
        .ant-checkbox-inner {
          background-color: #FFFFFF !important;
        }

        &::after {
          border: unset !important;
        }
      }

      @media (max-width: 1080px) {
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 2.295rem;

        .txt {
          display: none;
        }
      }

      /* style checkbox as radio */
      // .ant-checkbox {
      //   .ant-checkbox-inner {
      //     background-color: #fff;
      //     border-color: #d9d9d9;
      //     border-style: solid;
      //     border-width: 1px;
      //     border-radius: 100px !important;
      //     &::after {
      //       top: 4px;
      //       left: 4px;
      //       width: 10px;
      //       height: 10px;
      //       background-color: #1890ff;
      //       border: unset;
      //       border-top: 0;
      //       border-left: 0;
      //       border-radius: 8px;
      //       transform: scale(0);
      //       opacity: 0;
      //       transition: all 0.3s cubic-bezier(0.78, 0.14, 0.15, 0.86);
      //     }
      //   }
      // }
      // .ant-checkbox-checked {
      //   &::after {
      //     border: 1px solid #1890ff;
      //     border-radius: 50%;
      //     -webkit-animation: antRadioEffect 0.36s ease-in-out;
      //             animation: antRadioEffect 0.36s ease-in-out;
      //     -webkit-animation-fill-mode: both;
      //             animation-fill-mode: both;
      //   }
      //   .ant-checkbox-inner {
      //     background-color: #fff !important;
      //     &::after {
      //       transform: scale(1);
      //       opacity: 1;
      //       transition: all 0.3s cubic-bezier(0.78, 0.14, 0.15, 0.86);
      //     }
      //   }
      // }
    }

    @media (max-width: 767px) {
      align-items: unset;
      justify-content: space-between;
    }
  }
}

/deep/ .bg-info .ant-checkbox-checked .ant-checkbox-inner::after {
  border-color: #45aaf2 !important;
}

/deep/ .bg-primary .ant-checkbox-checked .ant-checkbox-inner::after {
  border-color: #0062AF !important;
}

/deep/ .bg-warning .ant-checkbox-checked .ant-checkbox-inner::after {
  border-color: #fab005 !important;
}

/deep/ .bg-success .ant-checkbox-checked .ant-checkbox-inner::after {
  border-color: #5eba00 !important;
}

/deep/ .bg-secondary .ant-checkbox-checked .ant-checkbox-inner::after {
  border-color: #6e7582 !important;
}

/deep/ .bg-control .ant-checkbox-checked .ant-checkbox-inner::after {
  border-color: #e8bc6b !important;
}

.btn {
    &-filter {
      position: fixed;
      right: 0;
      top: 130px;
      z-index: 100;
			border-radius: 5px 0 0 5px;
			padding: 0.5rem 0.3rem
    }
  }

  .box_fixRight {
  position: fixed;
  top: 170px;
  right: -100%;
  width: auto;
  transition: all 0.2s ease-in-out 0s;
  z-index: 100;
}
.box_fixRight .box_content {
  background: #fff;
  padding: 10px;
  box-shadow: 10px 0px 30px #888888;
  border-radius: 10px 0 0 10px;
}
.box_fixRight .box_content .item {
  display: block;
  padding: 13px 10px 13px 47px;
  color: #111;
}
.box_fixRight .box_content .item:hover {
  color: #db0000;
}
// .box_fixRight .box_content .item.item_3 {
//   background: url(../assets/img/blackberry-messenger.png) no-repeat left;
//   background-size: 35px 35px;
// }
// .box_fixRight .box_content .item.item_2 {
//   background: url(../assets/img/address-1.png) no-repeat left;
//   background-size: 35px 35px;
// }
// .box_fixRight .box_content .item.item_1 {
//   background: url(../assets/img/contacts-1.png) no-repeat left;
//   background-size: 35px 35px;
// }
.box_fixRight:hover {
  right: 0;
  transition: all 0.2s ease-in-out 0s;
}
</style>
