<template>
  <div>
    <div class="card">
      <div class="card-title">
        <div class="d-flex justify-content-between align-items-center">
          <h3 class="title">Cơ sở giá trị và cách tiếp cận</h3>
            <img class="img-dropdown" :class="!showAddLegal ? 'img-dropdown__hide' : ''"
              src="@/assets/images/icon-btn-down.svg" alt="dropdown" @click="() => { showAddLegal = !showAddLegal;  }">
        </div>
      </div>
      <div class="card-body card-info" v-show="showAddLegal">
        <div class="container-fluid">
          <div class="row container_content pb-2 ">
            <div class="col-12 col-lg-4 color_content form-label font-weight-bold form-group-container">Cơ sở giá trị của tài sản thẩm định giá</div>
            <div class="row col-lg-8">
              <div class="col-12 col-lg-6 pl-4" v-for="(itemFacility, indexFacility) in appraisalFacility" :key="indexFacility">
                <div class="d-flex form-group-container">
                  <input disabled type="radio" :name="itemFacility.type" :id="itemFacility.id" :value="itemFacility.id" v-model="data.value_base_and_approach.appraise_basis_property_id">
                  <div style="margin-left: 0.5rem" class="color_content"><label style="margin-bottom: unset !important" :for="itemFacility.id">{{itemFacility.name}}</label></div>
                </div>
              </div>
            </div>
          </div>
          <div class="row container_content pb-2">
            <div class="col-12 col-lg-4 color_content form-label font-weight-bold form-group-container">Nguyên tắc thẩm định</div>
            <div class="row col-lg-8">
              <div class="col-12 col-lg-6 pl-4" v-for="(itemPrinciple, indexPrinciple) in appraisalPrinciples" :key="indexPrinciple">
                <div class="d-flex form-group-container">
                  <input disabled type="radio" :name="itemPrinciple.type" :id="itemPrinciple.id" :value="itemPrinciple.id" v-model="data.value_base_and_approach.appraise_principle_id">
                  <div style="margin-left: 0.5rem" class="color_content"><label style="margin-bottom: unset !important" :for="itemPrinciple.id">{{itemPrinciple.name}}</label></div>
                </div>
              </div>
            </div>
          </div>

          <div class="row container_content pb-2">
            <div class="col-12 col-lg-4 color_content form-label font-weight-bold form-group-container">Cách tiếp cận</div>
            <div class="row col-lg-8">
              <div class="col-12 col-lg-6 pl-4" v-for="(itemApproach, indexApproach) in approach" :key="indexApproach">
                <div class="d-flex form-group-container">
                  <input disabled type="radio" :name="itemApproach.type" :id="itemApproach.id" :value="itemApproach.id" v-model="data.value_base_and_approach.appraise_approach_id">
                  <div style="margin-left: 0.5rem" class="color_content"><label style="margin-bottom: unset !important" :for="itemApproach.id">{{itemApproach.name}}</label></div>
                </div>
              </div>
            </div>
          </div>

          <div class="row container_content pb-2">
            <div class="col-12 col-lg-4 color_content form-label font-weight-bold form-group-container">Phương pháp sử dụng</div>
            <div class="row col-lg-8">
              <div class="col-12 col-lg-6 pl-4" v-for="(itemUsedMethod, indexUsedMethod) in methodsUsed" :key="indexUsedMethod">
                <div class="d-flex form-group-container">
                  <input disabled type="radio" :name="itemUsedMethod.type" :id="itemUsedMethod.id" :value="itemUsedMethod.id" v-model="data.value_base_and_approach.appraise_method_used_id">
                  <div style="margin-left: 0.5rem" class="color_content"><label style="margin-bottom: unset !important" :for="itemUsedMethod.id">{{itemUsedMethod.name}}</label></div>
                </div>
              </div>
            </div>
          </div>

          <div class="row align-items-center container_content pb-2">
            <div class="d-flex col-12 col-lg-4 color_content form-label font-weight-bold form-group-container">Giả thiết và giả thiết đặc biệt</div>
              <div class="col-12 col-lg-8">
                <InputTextarea
                  nonLabel="Giả thiết và giả thiết đặc biệt"
                  v-model="data.value_base_and_approach.document_description"
                  rules="required"
                  vid="document_description"
                  :rows="3"
                  :disableInput="true"
                  
                  :autosize="true"
                  class="col-12"
                />
              </div>
            </div>
          </div>

        </div>
      </div>
      <div class="card">
        <div class="card-title">
          <div class="d-flex justify-content-between align-items-center">
            <h3 class="title">Phương pháp tính toán</h3>
              <img class="img-dropdown" :class="!showAddLegal ? 'img-dropdown__hide' : ''"
                src="@/assets/images/icon-btn-down.svg" alt="dropdown" @click="() => { showAddLegal = !showAddLegal;  }">
          </div>
        </div>
        <div class="card-body card-info" v-show="showAddLegal">
          <div class="container-fluid">
            <div class="row container_content pb-2 form-group-container" >
              <div class="col-12 col-lg-4 color_content form-label font-weight-bold">Thống nhất mức giá chỉ dẫn</div>
              <div class="col-12 col-lg-8" >
                <div class="row align-items-center">
                  <div class="col-12 col-lg-4 pl-4 align-items-center" v-for="(itemIndicative, indexIndicative) in unifyIndicativePrice" :key="indexIndicative">
                    <div class="d-flex">
                      <input disabled type="radio" :name="itemIndicative.slug" :id="itemIndicative.slug" :value="itemIndicative.slug" v-model="data.appraisal_methods.thong_nhat_muc_gia_chi_dan.slug_value">
                      <div style="margin-left: 0.5rem" class="color_content"><label style="margin-bottom: unset !important" :for="itemIndicative.slug">{{itemIndicative.name}}</label></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row container_content pb-2" >
              <div class="col-12 col-lg-4 color_content form-label font-weight-bold form-group-container" >Tính giá đất hỗn hợp còn lại</div>
              <div class="col-12 col-lg-8">
                <div class="row align-items-center pl-4" v-for="(itemLandRemaning, indexLandRemaning) in compositeLandRemaning" :key="indexLandRemaning">
                  <div class="col-7 col-lg-6 pl-0">
                    <div class="d-flex form-group-container" >
                      <input disabled type="radio" :name="itemLandRemaning.slug" @change="handleChangeLandRemaining" :id="itemLandRemaning.slug" :value="itemLandRemaning.slug" v-model="data.appraisal_methods.tinh_gia_dat_hon_hop_con_lai.slug_value">
                      <div style="margin-left: 0.5rem" class="color_content"><label style="margin-bottom: unset !important" :for="itemLandRemaning.slug">{{itemLandRemaning.name}}</label></div>
                    </div>
                  </div>
                  <div class="col-2" v-if="itemLandRemaning.slug === 'theo-ty-le-gia-dat-co-so-chinh'">
                    <InputPercent
                      v-model="data.appraisal_methods.tinh_gia_dat_hon_hop_con_lai.value"
                      nonLabel="Tỷ lệ"
                      vid="test"
                      :max="100"
                      :decimal="0"
                      :disabled="true"
                      rules="required"
                      class="form-group-container"
                      @change="handleChangePercentRemain"
                    />
                  </div>
                </div>
              </div>
            </div>
            <div class="row container_content pb-2" >
              <div class="col-12 col-lg-4 color_content form-label font-weight-bold form-group-container">Tính giá đất vi phạm quy hoạch</div>
              <div class="col-12 col-lg-8" >
                <div class="row align-items-center pl-4" v-for="(itemVialationPrice, indexVialationPrice) in planningViolationPrice" :key="indexVialationPrice">
                  <div class="col-7 col-lg-6 pl-0">
                    <div class="d-flex form-group-container" >
                      <input disabled type="radio" :name="itemVialationPrice.slug" :id="itemVialationPrice.slug" @change="handleChangeViolationPrice" :value="itemVialationPrice.slug" v-model="data.appraisal_methods.tinh_gia_dat_vi_pham_quy_hoach.slug_value">
                      <div style="margin-left: 0.5rem" class="color_content"><label style="margin-bottom: unset !important" :for="itemVialationPrice.slug">{{itemVialationPrice.name}}</label></div>
                    </div>
                  </div>
                  <div class="col-2" v-if="itemVialationPrice.slug === 'theo-ty-le-gia-dat-thi-truong'">
                    <InputPercent
                      v-model="data.appraisal_methods.tinh_gia_dat_vi_pham_quy_hoach.value"
                      nonLabel="Tỷ lệ"
                      vid="test"
                      :max="100"
                      :decimal="0"
                      :disabled="true"
                      rules="required"
                      class="form-group-container"
                      @change="handleChangePercentVio"
                    />
                  </div>
                </div>
              </div>
            </div>
        </div>
        </div>
      </div>
  </div>

</template>

<script>
import InputNumberNoneFormat from '@/components/Form/InputNumberNoneFormat'
import InputPercent from '@/components/Form/InputPercent'
import InputNumberNew from '@/components/Form/InputNumberNew'
import InputCurrency from '@/components/Form/InputCurrency'
import InputCategoryBoolean from '@/components/Form/InputCategoryBoolean'
import InputArea from '@/components/Form/InputArea'
import InputDatePicker from '@/components/Form/InputDatePicker'
import InputCategory from '@/components/Form/InputCategory'
import InputText from '@/components/Form/InputText'
import InputTextarea from '@/components/Form/InputTextarea'
import InputSwitch from '@/components/Form/InputSwitch'
import InputLengthArea from '@/components/Form/InputLengthArea'
import { BAlert } from 'bootstrap-vue'
import Vue from 'vue'
import Icon from 'buefy'
Vue.use(Icon)
export default {
	name: 'Step5',
	props: ['data', 'appraisalFacility', 'approach', 'methodsUsed', 'appraisalPrinciples', 'unifyIndicativePrice', 'compositeLandRemaning', 'planningViolationPrice'],
	components: {
		InputCategory,
		InputText,
		InputTextarea,
		InputSwitch,
		InputLengthArea,
		InputArea,
		InputDatePicker,
		InputCategoryBoolean,
		InputCurrency,
		InputNumberNew,
		InputPercent,
		BAlert,
		InputNumberNoneFormat
	},
	computed: {
		optionsAppraisalFacility () {
			return {
				data: this.appraisalFacility,
				id: 'id',
				key: 'name'
			}
		},
		optionsApproach () {
			return {
				data: this.approach,
				id: 'id',
				key: 'name'
			}
		},
		optionsMethodsUsed () {
			return {
				data: this.methodsUsed,
				id: 'id',
				key: 'name'
			}
		},
		optionsAppraisalPrinciples () {
			return {
				data: this.appraisalPrinciples,
				id: 'id',
				key: 'name'
			}
		},
		optionsUnifyIndicativePrice () {
			return {
				data: this.unifyIndicativePrice,
				id: 'slug',
				key: 'name'
			}
		},
		optionsCompositeLandRemaning () {
			return {
				data: this.compositeLandRemaning,
				id: 'slug',
				key: 'name'
			}
		},
		optionsPlanningViolationPrice () {
			return {
				data: this.planningViolationPrice,
				id: 'slug',
				key: 'name'
			}
		}
	},
	data () {
		return {
			showDetailLegal: true,
			showAddLegal: true,
			isAddLegal: false,
			isEditLegal: false
		}
	},
	mounted () {
    // console.log('--------------------',this.data)
	},
	beforeUpdate () {
	},
	methods: {
		handleChangeLandRemaining (event) {
			this.$emit('changeLandRemaing', event.target.value)
		},
		handleChangeViolationPrice (event) {
			this.$emit('changeViolationPrice', event.target.value)
		},
		handleChangePercentRemain (event) {
			this.$emit('changePercentRemain', event)
		},
		handleChangePercentVio (event) {
			this.$emit('changePercentVio', event)
		}
	}

}
</script>
<style scoped lang="scss">

.card {
  border-radius: 5px;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
  background: #FFFFFF;
  margin-bottom: 1rem;

  &-footer {
    padding: 15px 24px;
  }

  &-title {
    padding: 15px;
    margin-bottom: 0;
    color: #E8E8E8;
    border-bottom: 2px solid;
    &__img {
      padding: 8px 20px;
    }
    h3 {
      color: #007EC6;
    }
    @media (max-width: 768px) {
      padding: 12px;
    }

    .title {
      font-size: 1.125rem;
      font-weight: 600;
      margin-bottom: 0;
    }
  }

  &-body {

    @media (max-width: 787px) {
      padding: 15px;
    }
  }

  &-sub_header_title {
    padding: 15px 24px;
  }

  &-info {
    .title {
      font-size: 1.125rem;
      font-weight: 700;
      margin-top: 28px;

      &-highlight {
        background: rgba(252, 194, 114, 0.53);
        text-align: center;
        padding: 10px 0;
        border-radius: 2px;
      }
    }
  }

  &-land {
    position: relative;
    padding: 0;
  }
}

.form-group-container {
  margin-top: 10px;
}

.color-black {
  color: #333333;
}

.btn-delete {
  cursor: pointer;
  display: flex;
  align-items: center;
  background: #FFFFFF;
  padding: 10px;
  border: none;

  // margin: auto;
  // width: 36px;
  // height: 36px;

  img {
    width: 100%;
    height: auto;
    min-width: 0.75rem;
  }
}

.btn {
  &-orange {
    background: #FAA831;
    text-align: center;
    border-radius: 5px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25) !important;
    height: 35px;
    width: 100px;
    color: #fff;
    margin: 15px 0 0;
    box-sizing: border-box;

    &:hover {
      border-color: #dc8300;
    }
  }
}

.img-dropdown {
  cursor: pointer;
  width: 18px;

  &__hide {
    transform: rotate(90deg);
    transition: .3s;
  }
}

.img-locate {
  cursor: pointer;
  position: absolute;
  right: 14px;
  top: 2.1rem;
  background: #FFFFFF;
  height: 2.1rem;
  width: 32px;
  display: grid;
  place-items: center;

  img {
    height: 60%;
  }
}

.text-error {
  color: #cd201f;

}

.select-group {
  background-color: #F6F7FB;
  border: 1px solid #E8E8E8;
  border-radius: 3px;
  padding: 16px 22px;

  .select-title {
    color: #FAA831;
    font-weight: 700;
    white-space: nowrap;
  }
}
  .img_add {
    width: 100%;
    height: 100% !important;
    cursor: pointer;
  }
  .container_input {
    border-radius: 10px;
    border: 2px solid #FAA831;
    width: 100%;
    height: 100%;
    position: relative;
  }
  .input_file_4 {
    left: 0;
    opacity: 0;
    height: 100%;
    width: 100%;
    cursor: pointer;
    position: absolute;
  }
  .sub_header_title {
    background-color: #F6F7FB;
    border: 1px solid #E8E8E8;
    border-radius: 3px;
    padding: 0.5rem 2rem;
    position: relative;
    color: #00507C;
    font-weight: 700;
    font-size: 1.125rem;
    .label {
      margin-right: 15px;
    }
    label {
      margin: 0;
    }
    &::before {
      content: '';
      position: absolute;
      height: calc(100% - 16px);
      width: 3px;
      background-color: #99D161;
      border-radius: 3px;
      top: 50%;
      left: 0;
      transform: translateY(-50%);
    }
  }
  .sub_header_title-rows {
    padding-top: 10px;
  }
  .footer-title {
    font-size: 1.2rem;
    font-weight: 700;
    color: #00507C;
  }
  /deep/ {
    .form-group-container.disabled {
      background-color: rgba(222, 230, 238, 0.3);
      .ant-input {
        background-color: rgba(222, 230, 238, 0.3) !important;
      }
    }
  }
 .color_content {
  color: #00507C
 }
.result_total_appraise {
  text-align: center;
  background: #EEF9FF;
  border: 1px solid #007EC6;
  border-radius: 3px;
  padding-top: 0.5rem;
  padding-bottom: 0.4rem;
}
.table_legal {
    width: 100%;
    font-weight: 500;
    color: #000000;
    text-align: center;
    thead{
      th{
        padding: 12px 0;
        font-weight: 700;
        background-color: #f29003;
        color: #FFFFFF;
      }
    }
    tbody{
      td{
        border: 1px solid #E5E5E5;
        &:first-child{
          width: 7%;
        }
        &:nth-child(2) {
          width: 15%;
        }
        &:last-child{
          width: 10%;
        }
        box-sizing: border-box;
        padding: 10px 14px;
      }
    }
  }
  .container_content {
    border-bottom: 1px solid #E8E8E8;
  }
</style>
