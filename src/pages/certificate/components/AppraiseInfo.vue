<template>
  <div class="card">
    <div class="card-title">
      <div class="d-flex justify-content-between align-items-center">
        <h3 class="title">Cơ sở giá trị, cách tiếp cận và phương pháp thẩm định</h3>
        <img class="img-dropdown" :class="!showCard ? 'img-dropdown__hide' : ''" src="../../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showCard = !showCard">
      </div>
    </div>
    <div class="card-body card-info" v-show="showCard">
      <Tabs :theme="theme" :navAuto="true">
          <TabItem name="Phương pháp tính toán">
            <div class="container-fluid">
              <div class="row">
                  <InputCategory
                    v-model="data.unify_indicative_price_slug"
                    vid="unify_indicative_price_slug"
                    label="Thống nhất mức giá chỉ dẫn"
                    rules="required"
                    @change="changeUnifyIndicativePrice"
                    class="col-12 col-md-6 form-group-container"
                    :options="optionsUnifyIndicativePrice"
                  />
              </div>
              <div class="row">
                  <InputCategory
                    v-model="data.composite_land_remaning_slug"
                    vid="composite_land_remaning_slug"
                    label="Tính giá đất hỗn hợp còn lại"
                    rules="required"
                    @change="changeCompositeLandRemaning"
                    class="col-12 col-md-6 form-group-container"
                    :options="optionsCompositeLandRemaning"
                  />
                  <div>
                    <div v-if="data.composite_land_remaning_slug === 'theo-ty-le-gia-dat-co-so-chinh'" class="container_percent col-12 col-md-2">
                      <InputNumberFormat
                        vid="test"
                        :max="99999999"
                        :min="-99999999"
                        rules="required"
                        :step="0"
                        @change="changePercentRemain($event)"
                        v-model="data.composite_land_remaning_value"
                      />
                      <div class="percent">%</div>
                    </div>
                    <span class="text-error" v-if="data.composite_land_remaning_value < 0">Giá trị nhập phải lớn hơn 0</span>
                  </div>

              </div>
              <div class="row">
                  <InputCategory
                    v-model="data.planning_violation_price_slug"
                    vid="planning_violation_price_slug"
                    label="Tính giá đất vi phạm quy hoạch"
                    rules="required"
                    @change="changePlanningViolationPrice"
                    class="col-12 col-md-6 form-group-container"
                    :options="optionsPlanningViolationPrice"
                  />
                  <div>
                    <div v-if="data.planning_violation_price_slug === 'theo-ty-le-gia-dat-thi-truong'"  class="container_percent">
                      <InputNumberFormat
                        vid="test"
                        :max="99999999"
                        :min="-99999999"
                        :step="0"
                        rules="required"
                        @change="changePercentZoning($event)"
                        v-model="data.planning_violation_price_value"
                      />
                      <div class="percent">%</div>
                    </div>
                    <span class="text-error" v-if="data.planning_violation_price_value < 0 || data.planning_violation_price_value > 100">Giá trị nhập trong khoảng 0-100</span>
                  </div>
              </div>
            </div>
          </TabItem>
          <TabItem name="Cơ sở giá trị và cách tiếp cận">
            <div class="container-fluid">
              <div class="row justify-content-between">
                <InputCategory
                  v-model="data.appraise_basis_property_id"
                  vid="appraise_basis_property_id"
                  rules="required"
                  label="Cơ sở giá trị của tài sản thẩm định giá"
                  class="col-12 col-md-6 form-group-container"
                  :options="optionsAppraisalFacility"
                />
                <InputCategory
                  v-model="data.appraise_principle_id"
                  vid="staff"
                  label="Nguyên tắc thẩm định"
                  class="col-12 col-md-6 form-group-container"
                  :options="optionsAppraisalPrinciples"
                />
                <InputCategory
                  v-model="data.appraise_approach_id"
                  vid="appraise_approach_id"
                  rules="required"
                  label="Cách tiếp cận"
                  class="col-12 col-md-6 form-group-container"
                  :options="optionsApproach"
                />
                <InputCategory
                  v-model="data.appraise_method_used_id"
                  vid="appraise_method_used_id"
                  label="Phương pháp sử dụng"
                  rules="required"
                  class="col-12 col-md-6 form-group-container"
                  :options="optionsMethodsUsed"
                />
                <InputTextarea
                  label="Giả thiết và giả thiết đặc biệt"
                  v-model="data.document_description"
                  vid="document_description"
                  :rows="2"
                  class="col-12 form-group-container"
                />
              </div>
            </div>
          </TabItem>
      </Tabs>
    </div>
  </div>
</template>

<script>

import InputCategory from '@/components/Form/InputCategory'
import InputText from '@/components/Form/InputText'
import InputDatePicker from '@/components/Form/InputDatePicker'
import InputTextarea from '@/components/Form/InputTextarea'
import {Tabs, TabItem} from 'vue-material-tabs'
import InputNumberFormat from '@/components/Form/InputNumber'
export default {
	name: 'AppraiseInfo',
	props: ['data', 'appraisalFacility', 'approach', 'methodsUsed', 'appraisalPrinciples', 'unifyIndicativePrice', 'compositeLandRemaning', 'planningViolationPrice'],
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
	components: {
		InputCategory,
		InputText,
		InputDatePicker,
		InputTextarea,
		Tabs,
		TabItem,
		InputNumberFormat
	},
	data () {
		return {
			theme: {
				navItem: '#000000',
				// navActiveItem: '#FAA831',
				navActiveItem: '#FF9900',
				slider: '#FAA831',
				arrow: '#000000'
			},
			showCard: true,
			directions: [],
			form: {
				appraise_date: '',
				appraisal_time: '',
				appraiser: '',
				manager: '',
				staff: '',
				note: '- Giả thiết:\n- Giả thiết đặc biệt:'
			}
		}
	},
	methods: {
		changeUnifyIndicativePrice (event) {
			this.$emit('changeUnifyIndicativePrice', event)
		},
		changeCompositeLandRemaning (event) {
			this.$emit('changeCompositeLandRemaning', event)
		},
		changePlanningViolationPrice (event) {
			this.$emit('changePlanningViolationPrice', event)
		},
		changePercentRemain (event) {
			this.$emit('changePercentRemain', event)
		},
		changePercentZoning (event) {
			this.$emit('changePercentZoning', event)
		}
	}
}
</script>

<style scoped lang="scss">
  .text-error{
    left: 14px;
    color: #cd201f;
    font-size: 12px;
  }
  .container_percent {
    display: flex;
    align-items: center;
    padding-top: 45px;
    width: 100%;
  }
  .percent {
    margin-left: 10px;
    margin-top: 5px;
    font-size: 1.125rem;
    font-weight: 700;
  }
  .card{
    border-radius: 5px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
    background: #FFFFFF;
    margin-bottom: 25px;
    @media (max-width: 768px) {
      margin-bottom: 20px;
    }
    @media (max-width: 418px) {
      margin-bottom: 20px;
    }
    &-title{
      background: #F3F2F7;
      padding: 16px 20px;
      margin-bottom: 0;
      &__img{
        padding: 8px 20px;
      }
      @media (max-width: 768px) {
        padding: 12px;
      }
      .title{
        font-size: 1.125rem;
        font-weight: 600;
        margin-bottom: 0;
      }
    }
    &-body{
      padding: 35px 30px 40px;
      @media (max-width: 787px) {
        padding: 15px;
      }
    }
    &-info{
      .title{
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
    &-land{
      position: relative;
      padding: 0;
    }
  }
  .form-group-container {
    margin-top: 15px;
  }
  .color-black{
    color: #333333;
  }
  .btn-delete{
    cursor: pointer;
    display: flex;
    align-items: center;
    background: #FFFFFF;
    border: 0.777778px solid #000000;
    border-radius: 5.88235px;
    padding: 10px;
    margin: auto;
    width: 36px;
    height: 36px;
    img{
      width: 100%;
      height: auto;
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
      &:hover{
        border-color: #dc8300;
      }
    }
  }
  .img-dropdown{
    cursor: pointer;
    width: 18px;
    &__hide{
      transform: rotate(90deg);
      transition: .3s;
    }
  }
</style>
