<template>
  <div>
    <div class="card" >
      <div class="card-title">
        <div class="d-flex justify-content-between align-items-center">
          <h3 class="title">Thông tin về tài sản khác</h3>
          <img class="img-dropdown" :class="!showCard ? 'img-dropdown__hide' : ''" src="../../../assets/images/icon-btn-down.svg" alt="dropdown" @click="showCard = !showCard">
        </div>
      </div>
      <div class="card-body card-info card-land" v-show="showCard">
        <div class="contain-table">
          <table class="table-property table-property__order">
            <thead>
            <tr v-if="other_assets.length > 0 ">
              <th>Tên tài sản</th>
              <th>Đặc điểm</th>
              <th>Số lượng</th>
              <th>ĐVT</th>
              <th>Đơn giá</th>
              <th>Thành tiền</th>
              <th></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(other, index) in other_assets" :key="other.id">
              <td>
                <InputText
                  v-model="other.name"
                  :vid="'namePropertyOther' + index"
                  class="contain-input contain-input__info contain-input__property"
                  rules="required"
                />
              </td>
              <td>
                  <InputText
                    v-model="other.description"
                    :vid="'typePropertyOther' + index"
                    class="contain-input contain-input__scale-end"
                    :max-length="200"
                    styleInputContainer="width:100%"
                    rules="required"
                  />
              </td>
              <td>
                <InputNumberFormat
                  v-model="other.total"
                  :vid="'total_amount_other'+ index"
                  label="Số lượng"
                  :max="99999999999999"
                  :min="0"
                  rules="required"
                  @change="changeNumber($event, index)"
                  class="contain-input contain-input__info contain-input__property"/>
              </td>
              <td>
                <InputText
                  v-model="other.dvt"
                  :vid="'typePropertyOther' + index"
                  class="contain-input contain-input__info contain-input__property"
                  :max-length="200"
                  rules="required"
                />
              </td>
              <td>
                <InputPriceNumber
                  v-model="other.unit_price"
                  :vid=" 'unit_price' + index"
                  :formatter="valueFormat => `${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                  @change="changeUnitPrice($event,index)"
                  rules="required"
                  class="contain-input contain-input__info contain-input__property"
                />
              </td>
              <td>
                <InputPriceNumber
                  v-model="other.total_price"
                  :vid="'unit_price' + index"
                  :disabledInput="true"
                  :formatter="valueFormat => `${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                  class="contain-input contain-input__info contain-input__property"
                />
              </td>
              <td>
                <div class="btn-delete" @click="removeOther(index)">
                  <img src="../../../assets/icons/ic_delete.svg" alt="delete">
                </div>
              </td>
            </tr>
            </tbody>
          </table>
        </div>
        <div class="btn-property">
          <button class="btn btn-white btn-orange btn-add" type="button" @click="addOtherAsset">
            <img src="../../../assets/icons/ic_add-white.svg" alt="add">
            Thêm
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>

import InputText from '@/components/Form/InputText'
import InputNumberFormat from '@/components/Form/InputNumber'
import InputPriceNumber from '@/components/Form/InputNumberFormat'
import InputCategory from '@/components/Form/InputCategory'
import InputSwitch from '@/components/Form/InputSwitch'
export default {
	name: 'OtherProperty',
	props: ['housingTypes', 'other_assets'],
	computed: {
		optionsHousingType () {
			return {
				data: this.housingTypes,
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
		InputPriceNumber
	},
	data () {
		return {
			showCard: true,
			openModalLandProperty: false,
			built_years: [],
			form: {
				other_assets: []
			}
		}
	},
	methods: {
		changeUnitPrice (e, index) {
			if (e && e > 0) {
				this.other_assets[index].unit_price = e
				this.other_assets[index].total_price = e * (this.other_assets[index].total ? this.other_assets[index].total : 0)
			} else {
				this.other_assets[index].unit_price = ''
				this.other_assets[index].total_price = ''
			}
		},
		changeTotalPrice () {
		},
		changeNumber (e, index) {
			if (e && e > 0) {
				this.other_assets[index].total = e
			} else {
				this.other_assets[index].total = ''
			}
		},
		addOtherAsset () {
			this.other_assets.push({
				name: '',
				description: '',
				total: '',
				dvt: '',
				unit_price: '',
				total_price: ''
			})
		},
		removeOther (index) {
			this.other_assets.splice(index, 1)
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
    overflow-y: hidden;
    overflow-x: hidden;
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
        padding: 20px 5px;
      }
    }
    &__order {
      tbody{
        td{
          &:first-child{
            width: 15%;
          }
          &:nth-child(2) {
            width: 25%;
          }
          &:last-child{
            width: 70px;
          }
          padding: 20px 10px;
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
</style>
