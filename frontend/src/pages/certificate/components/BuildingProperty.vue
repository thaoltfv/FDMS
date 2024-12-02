<template>
  <div>
    <div class="card">
      <div class="card-title">
        <div class="d-flex justify-content-between align-items-center">
          <h3 class="title">Thông tin về công trình xây dựng</h3>
          <img class="img-dropdown" :class="!showCard ? 'img-dropdown__hide' : ''" src="../../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showCard = !showCard">
        </div>
      </div>
      <div class="card-body card-info card-land" v-show="showCard">
        <div class="contain-table contain-table__tangible">
          <table class="table-property">
            <thead v-if="tangible_assets_clone.length > 0">
            <tr>
              <th>Mã số</th>
              <th>Loại</th>
              <th>Cấp nhà</th>
              <th>Chất lượng còn lại (%)</th>
              <th>Diện tích sàn (m <sup>2</sup>)</th>
              <th></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(tangible, index) in tangible_assets_clone" :key="'tangible' + index">
              <td>
                <div class="d-flex justify-content-center align-items-center contain-total contain-total__table">
                  <div class="num num-id text-nowrap d-flex justify-content-center text-center" @click="openModalProperty(tangible, index)"><a>Chỉnh sửa</a></div>
                </div>
              </td>
              <td>
                <InputCategory
                  v-model="tangible.building_type_id"
                  vid="building_type_id"
                  label="Loại"
                  disabled
                  rules="required"
                  class="contain-input contain-input__info contain-input__property"
                  :options="optionsHousingType"
                />
              </td>
              <td>
                <InputCategory
                  v-model="tangible.building_category_id"
                  vid="building_category_id"
                  label="Cấp nhà"
                  disabled
                  :options="optionsBuildingCategories"
                  class="contain-input contain-input__info contain-input__property"
                  hidden
                />
              </td>
              <td>
                <div class="d-flex align-items-center justify-content-center position-relative">
                  <InputNumberFormat
                    v-model="tangible.remaining_quality"
                    vid="remaining_quality"
                    label="Chất lượng còn lại"
                    :max="100"
                    :min="0"
                    disabled-input
                    rules="required"
                    class="contain-input contain-input__info contain-input__property"
                  />
<!--                  <div class="percent">%</div>-->
                </div>
              </td>
              <td>
                <InputNumberFormat
                  v-model="tangible.total_construction_base"
                  vid="total_construction_base"
                  label="Diện tích sàn"
                  :max="99999999"
                  :min="0"
                  disabled-input
                  rules="required"
                  class="contain-input contain-input__info contain-input__property"
                />
              </td>
              <td>
                <div class="btn-delete" @click="removeBuilding(index)">
                  <img src="../../../assets/icons/ic_delete.svg" alt="delete">
                </div>
              </td>
            </tr>
            </tbody>
          </table>
        </div>
        <div class="btn-property" v-if="tangible_assets_clone.length < 1">
          <button class="btn btn-white btn-orange btn-add" type="button" @click="handleModalBuildingProperty">
            <img src="../../../assets/icons/ic_add-white.svg" alt="add">
            Thêm
          </button>
        </div>
      </div>
    </div>
    <ModalBuildingProperty
      v-if="openModalBuildingProperty"
      @cancel="cancelBuilding"
      :buildingEdit="building"
      :compare_properties="compare_assets"
      @action="handleSaveBuilding"
    />
  </div>
</template>

<script>

import InputText from '@/components/Form/InputText'
import InputNumberFormat from '@/components/Form/InputNumber'
import InputCategory from '@/components/Form/InputCategory'
import InputSwitch from '@/components/Form/InputSwitch'
import ModalBuildingProperty from './modals/ModalBuildingProperty'
export default {
	name: 'BuildingProperty',
	props: ['housingTypes', 'buildingCategories', 'tangible_assets', 'compare_assets'],
	computed: {
		optionsHousingType () {
			return {
				data: this.housingTypes,
				id: 'id',
				key: 'description'
			}
		},
		optionsBuildingCategories () {
			return {
				data: this.buildingCategories,
				id: 'id',
				key: 'description'
			}
		},
		optionYearBuild () {
			return {
				data: this.built_years,
				id: 'year',
				key: 'year'
			}
		}
	},
	components: {
		InputText,
		InputNumberFormat,
		InputCategory,
		InputSwitch,
		ModalBuildingProperty
	},
	data () {
		return {
			showCard: true,
			openModalBuildingProperty: false,
			built_years: [],
			building: null,
			indexBuilding: null,
			isEditBuilding: false,
			tangible_assets_clone: JSON.parse(JSON.stringify(this.tangible_assets)),
			buildingBeforeEdit: null
		}
	},
	mounted () {
		this.handleBuiltYear()
	},
	created () {
		this.buildingBeforeEdit = JSON.parse(JSON.stringify(this.tangible_assets_clone))
	},
	methods: {
		openModalProperty (data, index) {
			this.openModalBuildingProperty = true
			this.building = data
			this.indexBuilding = index
			this.isEditBuilding = true
			this.buildingBeforeEdit = JSON.parse(JSON.stringify(this.tangible_assets_clone))
			document.getElementsByTagName('BODY')[0].style.overflow = 'hidden'
		},
		removeBuilding (index) {
			this.tangible_assets_clone.splice(index, 1)
		},
		handleSaveBuilding (data) {
			if (this.isEditBuilding === false) {
				this.tangible_assets_clone.push(data)
			} else {
				this.tangible_assets_clone[this.indexBuilding] = data
			}
			this.openModalBuildingProperty = false
			document.getElementsByTagName('BODY')[0].style.overflow = 'auto'
			this.$emit('saveBuilding', this.tangible_assets_clone)
		},
		cancelBuilding () {
			this.openModalBuildingProperty = false
			if (this.isEditBuilding) {
				this.tangible_assets_clone = JSON.parse(JSON.stringify(this.buildingBeforeEdit))
			}
			document.getElementsByTagName('BODY')[0].style.overflow = 'auto'
			this.$emit('cancelBuilding', this.tangible_assets_clone)
		},
		handleModalBuildingProperty () {
			this.building = null
			this.indexBuilding = ''
			this.isEditBuilding = false
			this.openModalBuildingProperty = true
			document.getElementsByTagName('BODY')[0].style.overflow = 'hidden'
		},
		remainingQuality (event) {
			this.tangible.remaining_quality = event
			this.tangible.estimation_value = this.tangible.total_construction_base * this.tangible.unit_price_m2 * (this.tangible.remaining_quality / 100)
		},
		changeFloor (event) {
			if (event !== undefined && event !== null) {
				this.tangible.floor = parseInt(event)
			} else {
				this.tangible.floor = 0
			}
		},
		handleBuiltYear () {
			const year = new Date().getFullYear()
			for (let i = 1970; i <= year; i++) {
				this.built_years.push(
					{
						year: i
					}
				)
			}
			function compare (a, b) {
				if (a.year > b.year) { return -1 }
				if (a.year < b.year) { return 1 }
				return 0
			}
			return this.built_years.sort(compare)
		}
	}
}
</script>

<style scoped lang="scss">
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
      }
    }
    &-land{
      position: relative;
      padding: 0;
    }
  }
  .contain-table{
    &__tangible{
      overflow-y: hidden;
      overflow-x: auto;
    }
    @media (max-width: 1440px) {
      overflow-y: hidden;
      overflow-x: auto;
    }
    .table-property{
      width: 100%;
    }
  }
  .table-property{
    width: 100%;
    font-weight: 500;
    color: #000000;
    text-align: center;
    thead{
      th{
        padding: 12px 0;
        font-weight: 500;
        @media (max-width: 787px) {
          padding: 12px;
        }
      }
    }
    tbody{
      td{
        border: 1px solid #E5E5E5;
        &:first-child{
          border-left: none;
          border-right: none;
        }
        padding: 20px 14px;
      }
    }
    &__order {
      tbody{
        td{
          &:first-child{
            width: 40%;
          }
          &:last-child{
            width: 70px;
          }
          padding: 20px 70px;
          @media (max-width: 1023px) {
            padding: 20px 30px;
          }
        }
      }
    }
  }
  .contain-total{
    display: grid !important;
    margin-right: 0;
    grid-template-columns: 1fr 1fr;
    color: #333333;
    @media (max-width: 1440px) {
      display: block !important;
    }
    .num{
      padding: 0 11px 0 24px;
      height: 35px;
      line-height: 1.5;
      width: 180px;
      border-radius: 5px;
      border: 1px solid #555555;
      display: flex;
      align-items: center;
      justify-content: flex-end;
      background: #f1f1f1 !important;
      cursor: not-allowed;
      user-select: none;
      @media (max-width: 787px) {
        width: 100% !important;
      }
      p{
        margin-bottom: 0;
      }
      &-id{
        color: #FAA831;
        text-align: center !important;
        background: #FFFFFF !important;
        border: none;
        width: 100%;
        padding: 0;
      }
    }
    .name{
      margin-bottom: 0;
      font-size: 1.125rem;
      font-weight: 500;
      margin-right: 20px;
      @media (max-width: 1440px) {
        margin-bottom: 10px;
        font-weight: 700;
      }
    }
    &__last{
      .num{
        width: 315px;
        @media (max-width: 767px){
          width: calc(100vw - 120px) ;
        }
      }
    }
    &__table{
      grid-template-columns: 1fr;
      .num{
        text-align: left;
        margin: auto;
        &-id{
          cursor: pointer;
          color: #FAA831;
          text-align: center !important;
        }
      }
    }
  }
  .coordinate{
    color: #000000;
    background: #f1f1f1;
    padding: 0 11px 0 24px;
    display: flex;
    align-items: center;
    height: 35px;
    border-radius: 5px;
    border: 1px solid #555555;
    .num{
      p{
        margin-bottom: 0;
      }
    }
  }
  .btn{
    &-white{
      max-height: none;

      line-height: 19.07px;
      min-width: 153px;
      margin-right: 15px;
      &:last-child{
        margin-right: 0;
      }
    }
    &-orange {
      max-height: none;

      line-height: 19.07px;
      min-width: 153px;
      margin-right: 15px;
      color: #FFFFFF;
      background: #FAA831;
    }
    &-contain{
      margin-bottom: 55px;
      @media (max-width: 768px) {
        margin-bottom: 30px;
      }
    }
  }
  .btn-delete{
    display: flex;
    align-items: center;
    cursor: pointer;
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
  .btn-property{
    padding: 10px;
  }
  .percent{
    top: 0;
    right: 0;
    bottom: 0;
    position: absolute;
    background: #f1f1f1;
    border: 1px solid #000000;
    border-left: none;
    height: 100%;
    line-height: 1.5;
    border-bottom-right-radius: 5px;
    border-top-right-radius: 5px;
    padding: 5.5px;
    box-sizing: border-box;
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
