<template>
  <div class="container__result">
    <div class="container container__detail">
      <div class="table" v-if="((result.recognized && result.recognized.length > 0 ) || (result.unrecognized && result.unrecognized.length > 0)) ">
        <table class="w-100 table__tangible">
          <thead>
          <tr>
            <th>Quyền sử dụng đất</th>
            <th>Diện tích</th>
            <th>Đơn giá</th>
            <th>Thành tiền</th>
          </tr>
          </thead>
          <tbody>
          <tr v-if="result.recognized && result.recognized.length > 0" v-for="(recognize, index) in result.recognized" :key="'recognized'+index">
            <td>
              Phần đất vi phạm quy hoạch
            </td>
            <td>
              {{recognize.area}}m<sup>2</sup>
            </td>
            <td>
              {{format(recognize.average_land_unit_price)+ 'đ'}}
            </td>
            <td>
              {{format(recognize.estimate_price)}} đ
            </td>
          </tr>
          <tr v-if="result.unrecognized && result.unrecognized.length > 0" v-for="(unrecognized, index) in result.unrecognized" :key="'unrecognized'+index">
            <td>
              Phần đất phù hợp quy hoạch
            </td>
            <td>
              {{ unrecognized.area }}m<sup>2</sup>
            </td>
            <td>
              {{format(unrecognized.average_land_unit_price) + ' đ'}}
            </td>
            <td>
              {{ format(unrecognized.estimate_price) }} đ
            </td>
          </tr>
          </tbody>
        </table>
      </div>
      <div class="table tangible" v-if="result.building && result.building.length > 0">
        <table class="w-100 table__tangible">
          <thead>
          <tr>
            <th>Loại</th>
            <th>Diện tích sàn xây dựng</th>
            <th>% Chất lượng còn lại</th>
            <th>Đơn giá</th>
            <th>Thành tiền</th>
          </tr>
          </thead>
          <tbody>
          <tr  v-for="(build, index) in result.building" :key="'building'+index">
            <td>
              <div v-if="buildingTangible[index] !== undefined && buildingTangible[index] !== null">
                {{buildingTangible[index].description}}
              </div>
            </td>
            <td>
              {{formatFloat(build.area)}}m<sup>2</sup>
            </td>
            <td>
              {{build.remaining_quality}} %
            </td>
            <td>
              {{format(build.average_building_unit_price) + ' đ'}}
            </td>
            <td>
              {{format(build.estimate_price)}} đ
            </td>
          </tr>
          </tbody>
        </table>
      </div>
      <div class="d-flex justify-content-end align-items-center w-100" v-if="result.result.error_message === '' || result.result.error_message === undefined || result.result.error_message === null">
        <div class="container__total">
          <div class="total__estimate">
            <p class="title">Tổng giá trị tài sản</p>
            <p class="price">{{format(result.result.total_price)}} VND</p>
          </div>
          <div class="vertical__line" v-if="result.assets && result.assets.length > 0" />
          <div class="reliability" v-if="result.assets && result.assets.length > 0" :class="result.reliability === 1 ? '' : result.reliability === 2 ? 'reliability--orange' : 'reliability--red'">
            <button class="btn btn-reliability" :class="result.reliability === 1 ? '' : result.reliability === 2 ? 'btn-reliability--orange' : 'btn-reliability--red'" @click="handlePropertyEstimate"> Độ tin cậy
              {{result.reliability === 1 ? ' Cao' : result.reliability === 2 ? ' Trung bình' : ' Thấp'}}</button>
          </div>
        </div>
      </div>
      <div class="container__total justify-content-center" v-if="result.result.error_message !== '' && result.result.error_message !== undefined && result.result.error_message !== null">
        <div class="total__estimate">
          <p class="price">{{result.result.error_message}}</p>
        </div>
      </div>
    </div>
    <ModalPropertyEstimate
      v-if="openModalPropertyEstimate"
      :assets="this.result.assets"
      :landTypePurpose="landTypePurpose"
      @cancel="openModalPropertyEstimate = false"
    />
  </div>
</template>

<script>
import InputCategory from '@/components/Form/InputCategory'
import InputText from '@/components/Form/InputText'
import InputNumberFormat from '@/components/Form/InputNumber'
import ModalPropertyEstimate from '@/components/Modal/ModalPropertyEstimate'
import PriceEstimate from '@/models/PriceEstimate'
export default {
	name: 'Result',
	props: ['result', 'buildingTypes', 'landTypePurpose', 'buildingTangible'],
	data () {
		return {
			old_result: null,
			new_result: {
				assets: '',
				error_message: '',
				unrecognized: '',
				recognized: '',
				building: '',
				reliability: '',
				status: '',
				result: {
					total_price: '',
					error_message: ''
				}
			},
			date: '',
			errormessage: '',
			assets: [],
			reliability: null,
			openModalPropertyEstimate: false,
			landTypeRecognized: [
				{
					description: ''
				}
			],
			landTypeUnrecognized: [
				{
					description: ''
				}
			],
			total_price: '',
			recognized: [],
			unrecognized: [],
			building: [],
			total: {
				area: 0,
				price: 0
			},
			total_building: {
				area: 0,
				price: 0
			}
		}
	},
	created () {
	},
	mounted () {
		this.getNow()
		this.handleResult()
		// this.findBuilding()
		// this.findLandTypeRecognized()
		// this.findLandTypeUnrecognized()
	},
	components: {
		ModalPropertyEstimate,
		InputCategory,
		InputNumberFormat,
		InputText
	},
	computed: {
	},
	methods: {
		getNow () {
			const today = new Date()
			const date = today.getDate() + '-' + (today.getMonth() + 1) + '-' + today.getFullYear()
			this.date = date
		},
		onScrollToTop () {
			window.scrollTo(0, 0)
		},
		handlePropertyEstimate () {
			this.openModalPropertyEstimate = true
		},
		handleResult () {
			let total_area_recognized = 0
			let total_price_recognized = 0
			let total_area_unrecognized = 0
			let total_price_unrecognized = 0
			let total_area_building = 0
			let total_price_building = 0
			if (this.get_result !== undefined && this.get_result !== null) {
				if (this.get_result.recognized !== undefined && this.get_result.recognized !== null) {
					this.recognized = this.get_result.recognized
				}
				if (this.get_result.unrecognized !== undefined && this.get_result.unrecognized !== null) {
					this.unrecognized = this.get_result.unrecognized
					this.reliability = this.get_result.reliability
					this.assets = this.get_result.assets
				}
				if (this.get_result.building !== undefined && this.get_result.building !== null) {
					this.building = this.get_result.building
				}
				if (this.get_result.result.total_price !== undefined && this.get_result.result.total_price !== null) {
					this.total_price = this.get_result.result.total_price
				} else {
					this.total_price = 0
				}
				if (this.get_result.result.status === 0) {
					this.errormessage = this.get_result.result.error_message
				} else {
					this.errormessage = ''
				}
			}
			if (this.recognized !== undefined && this.recognized !== null) {
				this.recognized.forEach(recognize => {
					if (recognize.area !== '' && recognize.area !== undefined && recognize.area !== null) {
						total_area_recognized = total_area_recognized + parseInt(recognize.area)
					}
					total_price_recognized = total_price_recognized + parseInt(recognize.estimate_price)
				})
			}
			if (this.unrecognized !== undefined && this.unrecognized !== null) {
				this.unrecognized.forEach(unrecognized => {
					if (unrecognized.area !== '' && unrecognized.area !== undefined && unrecognized.area !== null) {
						total_area_unrecognized = total_area_unrecognized + parseInt(unrecognized.area)
					}
					total_price_unrecognized = total_price_unrecognized + parseInt(unrecognized.estimate_price)
				})
			}
			if (this.result.building !== undefined && this.result.building !== null) {
				this.building.forEach(building => {
					if (building.area !== '' && building.area !== undefined && building.area !== null) {
						total_area_building = total_area_building + parseInt(building.area)
					}
					total_price_building = total_price_building + parseInt(building.estimate_price)
				})
			}
			this.total_building.area = total_area_building
			this.total_building.price = total_price_building
			this.total.area = total_area_recognized + total_area_unrecognized
			this.total.price = total_price_recognized + total_price_unrecognized
		},
		findLandTypeRecognized () {
			this.landTypeRecognized = []
			this.result.recognized.forEach(recognized => {
				this.landTypePurpose.forEach(landType => {
					if (recognized.land_type_purpose === landType.id) {
						this.landTypeRecognized.push({
							description: landType.acronym
						})
					}
				})
			})
		},
		findLandTypeUnrecognized () {
			this.landTypeUnrecognized = []
			this.result.unrecognized.forEach(unrecognized => {
				this.landTypePurpose.forEach(landType => {
					if (unrecognized.land_type_purpose === landType.id) {
						this.landTypeUnrecognized.push({
							description: landType.acronym
						})
					}
				})
			})
		},
		findBuilding () {
			this.buildingTangible = []
			this.result.building.forEach(build => {
				this.buildingTypes.forEach(buildingType => {
					if (build.building_category === buildingType.id) {
						this.buildingTangible.push({
							description: buildingType.description
						})
					}
				})
			})
		},
		changeUnitPrice (event, index) {
			for (let i = 0; i < this.result.recognized.length; i++) {
				if (i === index) {
					this.result.recognized[i].average_land_unit_price = +event
					this.result.recognized[i].estimate_price = +event * this.recognized[i].area
				}
			}
		},
		changeUnitPriceUnrecognized (event, index) {
			for (let i = 0; i < this.result.unrecognized.length; i++) {
				if (i === index) {
					this.result.unrecognized[i].average_land_unit_price = +event
					this.result.unrecognized[i].estimate_price = +event * this.unrecognized[i].area
				}
			}
		},
		changeUnitPriceBuilding (event, index) {
			for (let i = 0; i < this.result.building.length; i++) {
				if (i === index) {
					this.result.building[i].average_building_unit_price = +event
					this.result.building[i].estimate_price = +event * this.building[i].area * (this.building[i].remaining_quality / 100)
				}
			}
		},
		totalPriceLand () {
			let total_price_recognized = 0
			let total_price_unrecognized = 0
			if (this.result.recognized !== undefined && this.result.recognized !== null) {
				this.result.recognized.forEach(recognize => {
					total_price_recognized = total_price_recognized + parseInt(recognize.estimate_price)
				})
			}
			if (this.result.unrecognized !== undefined && this.result.unrecognized !== null) {
				this.result.unrecognized.forEach(unrecognized => {
					total_price_unrecognized = total_price_unrecognized + parseInt(unrecognized.estimate_price)
				})
			}
			this.total.price = total_price_recognized + total_price_unrecognized
		},
		totalPriceBuilding () {
			let total_price_building = 0
			if (this.building !== undefined && this.building !== null) {
				this.building.forEach(building => {
					total_price_building = total_price_building + parseInt(building.estimate_price)
				})
			}
			this.total_building.price = total_price_building
		},
		totalUnitPrice () {
			this.total_price = this.total_building.price + this.total.price
		},
		async createEstimateLog () {
			const input = this.input
			const output = this.new_result
			await PriceEstimate.LogPriceEstimate({input, output})
		},
		format (value) {
			let num = (value / 1).toFixed(0).replace(',', '.')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
		},
		formatFloat (value) {
			let num = (value / 1).toFixed(2).replace(',', '.')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
		}
	},
	beforeMount () {
		this.handleResult()
	}
}
</script>

<style lang="scss" scoped>
.container {
  &__result {
    max-width: 1545px;
    border-radius: 5px;
    margin: 40px auto 0;
    padding: 30px 30px 50px;
    position: relative;
    .title{
      text-transform: uppercase;
      text-align: center;
      font-weight: 700;
      font-size: 30px;
      color: #000000;
      margin-bottom: 60px;
    }
    @media (max-width: 767px) {
      padding: 20px;
    }
  }
  &__time{
    color: #000000;
    font-weight: 700;
    font-size: 20px;
    .user{
      margin-bottom: 10px;
    }
    .date{
      margin-bottom: 0;
    }
  }
  &__total{
    .input-total{
      height: 40px;
      width: 306px;
      background: #E5E5E5;
      border-radius: 5px;
    }
  }
}
.table{
  width: 100%;
  border: 1px solid #D0D0D0;
  box-sizing: border-box;
  border-radius: 5px;
  overflow-x: auto;
  overflow-y: hidden;
  &__header{
    padding: 7px 20px;
    background: #F28C1C;
    color: #FFFFFF;
    border-radius: 5px 5px 0 0;
    box-sizing: border-box;
    .title{
      margin-bottom: 0;
      font-weight: 700;
      font-size: 1.125rem;
    }
  }
  &__body{
    padding: 0 20px;
  }
  &--margin {
    margin-bottom: 54px;
  }
}
.btn{
  &__add {
    box-shadow: none !important;
    color: #000000;
    font-size: 1.125rem;
    padding-left: 0;
    img{
      margin-right: 5px;
    }
  }
}
.land__input{
  margin-top: 18px;
}
.ic{
  &__delete{
    cursor: pointer;
  }
}
.btn{
  &-orange{
    &__result{
      width: 204px;
      margin-bottom: 20px;
    }
  }
}
.table__tangible{
  thead{
    background: #F28C1C;
    text-align: center;
    tr {
      th{
        color: #FFFFFF;

        font-weight: 700;
        text-transform: none;
      }
    }
  }
  tbody{
    text-align: center;
    tr{
      td{
        white-space: nowrap;
        color: #000000;
      }
    }
  }
}
.tangible{
  margin-top: 50px;
}
.container__total {
  display: flex;
  justify-content: flex-end;
  align-items: center;
  @media (max-width: 767px) {
    display: block;
  }
}
.total {
  &__estimate {
    text-align: right;
    @media (max-width: 767px) {
      text-align: center;
    }
    .title {
      text-align: right;
      margin-bottom: 0;
      font-size: 1.125rem;
      color: #F28C1C;
      @media (max-width: 767px) {
        font-size: 26px;
        margin-right: 0;
      }
    }
    .price{
      font-size: 40px;
      color: #F28C1C;
      font-weight: 700;
      margin-bottom: 0;
      @media (max-width: 767px) {
        font-size: 30px;
      }
    }
  }
}
.vertical{
  &__line{
    border-left: 1px solid #D0D0D0;
    margin: 0 40px;
    height: 100%;
    @media (max-width: 767px) {
      border-left: none;
      border-bottom: 1px solid #D0D0D0;
      margin: 20px 10px;
    }
  }
}
.reliability {
  text-align: center;
  font-size: 1.125rem;
  color: #1F8B24;
  &--orange{
    color: #FAA831;
  }
  &--red {
    color: #EF3039;
  }
  &__title, &__detail {
    margin-bottom: 0;
  }
  &__detail {
    text-decoration: underline;
    cursor: pointer;
  }
}
.estimate__empty {
  display: flex;
  width: 100%;
  justify-items: center;
  align-items: center;
  text-align: center;
  font-size: 24px;
  height: 35vh;
  p {
    width: 100%;
    text-align: center;
  }
}
.btn {
  &-reliability {
    margin-top: 20px;
    background: #1F8B24;
    color: #FFFFFF;
    &--orange{
      background: #FAA831;
    }
    &--red {
      background: #EF3039;
    }
  }
}
.scroll-to-top {
  position: absolute;
  bottom: 30px;
  right: 30px;
  cursor: pointer;
  background: #faa831;
  border-radius: 50%;
  height: 50px;
  width: 50px;
  display: flex;
  align-items: center;
  justify-content: center;
  img {
    height: 30px;
  }
}
</style>
