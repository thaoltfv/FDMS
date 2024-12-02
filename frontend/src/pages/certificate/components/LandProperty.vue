<template>
  <div>
    <div class="card" >
      <div class="card-title">
        <div class="d-flex justify-content-between align-items-center">
          <h3 class="title">Thông tin về quyền sử dụng đất</h3>
          <img class="img-dropdown" :class="!showCard ? 'img-dropdown__hide' : ''" src="../../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showCard = !showCard">
        </div>
      </div>
      <div class="card-body card-info card-land" v-show="showCard">
        <div class="contain-table">
          <table class="table-property" v-if="properties_clone.length > 0">
            <thead>
              <tr>
                <th>Mã tài sản đất</th>
                <!-- <th>Số tờ</th>
                <th>Số thửa</th> -->
                <th>Tọa độ</th>
                <th>Loại đất</th>
                <th>Tổng diện tích thẩm định (m<sup>2</sup>)</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
            <tr v-for="(property, index) in properties_clone" :key="'property' + index">
              <td>
                <div class="d-flex align-items-center  contain-total contain-total__table">
                  <div class="num num-id d-flex justify-content-center text-center" @click="openModalProperty(property, index)" >Chỉnh sửa</div>
                </div>
              </td>
              <!-- <td>
                <div class="d-flex align-items-center contain-total contain-total__table">
                  <div class="num"><p>{{property.doc_no}}</p></div>
                </div>
              </td>
              <td>
                <div class="d-flex align-items-center contain-total contain-total__table">
                  <div class="num"><p>{{property.land_no}}</p></div>
                </div>
              </td> -->
              <td>
                <div class="d-flex align-items-center coordinate">
                  <div class="num"><p>{{property.coordinates}}</p></div>
                </div>
              </td>
              <td>
                <div class="d-flex align-items-center contain-total contain-total__table">
                  <div class="num"><p>{{property.land_type ? property.land_type.description : ''}}</p></div>
                </div>
              </td>
              <td>
                <div class="d-flex align-items-center contain-total contain-total__table">
                  <div class="num"><p v-if="property.appraise_land_sum_area">{{parseFloat(property.appraise_land_sum_area).toFixed(2)}}</p></div>
                </div>
              </td>
              <td>
                <div class="btn-delete" @click="removeProperty(index)">
                  <img src="../../../assets/icons/ic_delete.svg" alt="delete">
                </div>
              </td>
            </tr>
            </tbody>
          </table>
        </div>
        <div class="btn-property" v-if="properties_clone.length < 1">
          <button class="btn btn-white btn-orange btn-add" @click="handleModalLandProperty" type="button">
            <img src="../../../assets/icons/ic_add-white.svg" alt="add">
            Thêm
          </button>
        </div>
      </div>
    </div>
    <ModalLandProperty
      v-if="openModalLandProperty"
      @cancel="cancelProperty"
      :frontSideOptions="frontSideOptions"
      :twoSidesLandOptions="twoSidesLandOptions"
      :individualRoadOptions="individualRoadOptions"
      :property="property"
      :full_address="full_address"
      :coordinates="coordinates"
      :unit_price="unit_price"
      :land_no="land_no"
      :doc_no="doc_no"
      @action="handleSaveProperty"
    />
    <ModalWarning
      v-if="openModalWarning"
      @cancel="openModalWarning = false"
      @action="handleActionWarning"
    />
  </div>
</template>

<script>

import InputText from '@/components/Form/InputText'
import InputNumberFormat from '@/components/Form/InputNumber'
import ModalLandProperty from './modals/ModalLandProperty'
import ModalWarning from '@/components/Modal/ModalWarning'
export default {
	name: 'LandProperty',
	props: ['properties', 'coordinates', 'full_address', 'unit_price', 'doc_no', 'land_no', 'landType'],
	components: {
		InputText,
		InputNumberFormat,
		ModalLandProperty,
		ModalWarning
	},
	data () {
		return {
			showCard: true,
			openModalWarning: false,
			frontSideOptions: {
				items: {
					preSelected: '',
					labels: [
						{ name: 'Yes', color: 'white' },
						{ name: '', color: 'white' },
						{ name: 'No', color: 'white' }
					]
				}
			},
			twoSidesLandOptions: {
				items: {
					preSelected: '',
					labels: [
						{ name: 'Yes', color: 'white' },
						{ name: '', color: 'white' },
						{ name: 'No', color: 'white' }
					]
				}
			},
			individualRoadOptions: {
				items: {
					preSelected: '',
					labels: [
						{ name: 'Yes', color: 'white' },
						{ name: '', color: 'white' },
						{ name: 'No', color: 'white' }
					]
				}
			},
			openModalLandProperty: false,
			propertyBeforeEdit: null,
			properties_clone: JSON.parse(JSON.stringify(this.properties)),
			property: null,
			isEditProperty: false,
			compare_assets: [],
			landTypes: []
		}
	},
	created () {
		this.propertyBeforeEdit = JSON.parse(JSON.stringify(this.properties_clone))
	},
	methods: {
		handleActionWarning () {
			document.getElementsByTagName('BODY')[0].style.overflow = 'hidden'
			this.openModalLandProperty = true
		},
		openModalProperty (data, index) {
			this.openModalLandProperty = true
			this.property = data
			this.indexProperty = index
			this.isEditProperty = true
			this.propertyBeforeEdit = JSON.parse(JSON.stringify(this.properties_clone))
			if (data.front_side === 1) {
				this.frontSideOptions.items.preSelected = 'Yes'
				data.front_side_switch = true
			} else if (data.front_side === 0) {
				data.front_side_switch = false
				this.frontSideOptions.items.preSelected = 'No'
			} else {
				this.frontSideOptions.items.preSelected = ''
			}
			if (data.two_sides_land === true) {
				this.twoSidesLandOptions.items.preSelected = 'Yes'
			} else if (data.two_sides_land === false) {
				this.twoSidesLandOptions.items.preSelected = 'No'
			} else {
				this.twoSidesLandOptions.items.preSelected = ''
			}
			if (data.individual_road === 1) {
				this.individualRoadOptions.items.preSelected = 'Yes'
			} else if (data.individual_road === 0) {
				this.individualRoadOptions.items.preSelected = 'No'
			} else {
				this.individualRoadOptions.items.preSelected = ''
			}
			document.getElementsByTagName('BODY')[0].style.overflow = 'hidden'
		},
		removeProperty (index) {
			this.properties_clone.splice(index, 1)
			this.landTypes.splice(index, 1)
			this.compare_assets = []
			this.$emit('compare_assets', this.compare_assets)
		},
		handleSaveProperty (property) {
			if (this.isEditProperty === false) {
				this.properties_clone.push(property)
				this.landTypes.push({land: this.landType.find(land => property.land_type_id === land.id).description})
			} else {
				this.properties_clone[this.indexProperty] = property
				this.landTypes[this.indexProperty] = {land: this.landType.find(land => property.land_type_id === land.id).description}
			}
			if (this.properties_clone[0].land_no && this.properties_clone[0].land_no !== '') {
				this.compare_assets = [{
					id: this.properties_clone[0].land_no,
					plot_num: 'Số tờ: ' + this.properties_clone[0].doc_no + ', Số thửa: ' + this.properties_clone[0].land_no
				}]
			} else {
				this.compare_assets = []
			}
			this.$emit('compare_assets', this.compare_assets, this.properties_clone)
		},
		addLand () {
			this.landTypes.push({land: this.properties_clone[0] ? this.landType.find(land => this.properties_clone[0].land_type_id === land.id).description : ''})
		},
		handleModalLandProperty () {
			this.isEditProperty = false
			this.indexProperty = ''
			this.property = null
			if (this.unit_price.province && this.unit_price.district && this.unit_price.ward && this.unit_price.street) {
				document.getElementsByTagName('BODY')[0].style.overflow = 'hidden'
				this.openModalLandProperty = true
			} else {
				this.openModalWarning = true
			}
		},
		cancelProperty () {
			if (this.isEditProperty === true) {
				this.properties_clone = JSON.parse(JSON.stringify(this.propertyBeforeEdit))
			}
			this.$emit('cancelProperty', this.properties_clone)
			document.getElementsByTagName('BODY')[0].style.overflow = 'auto'
			this.openModalLandProperty = false
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
  .img-dropdown{
    cursor: pointer;
    width: 18px;
    &__hide{
      transform: rotate(90deg);
      transition: .3s;
    }
  }
</style>
