<template>
    <ValidationObserver tag="form" ref="observer">
        <div class="modal-detail d-flex justify-content-center align-items-center">
            <div class="card">
                <div class="container-title">
                    <div class="d-lg-flex d-block shadow-bottom">
                        <h2 class="title">Cập nhập đơn giá</h2>
                    </div>
                </div>
                <div class="contain-detail">
                  <div class="table" v-if="((unrecognized.length > 0 || recognized.length > 0)) ">
                    <table class="w-100 table__tangible">
                      <thead>
                      <tr>
                        <th>Quyền sử dụng đất</th>
                        <th>Loại đất</th>
                        <th>Diện tích</th>
                        <th>Đơn giá</th>
                        <th>Thành tiền</th>
                      </tr>
                      </thead>
                      <tbody>
                        <tr v-if="unrecognized.length > 0" v-for="(unrecognize, index) in unrecognized" :key="'unrecognized'+index">
                          <td>Phần đất phù hợp quy hoạch</td>
                          <td v-if="landTypeUnrecognized[index] !== undefined && landTypeUnrecognized[index] !== null">{{ landTypeUnrecognized[index].description }}</td>
                          <td>{{ formatFloat(unrecognize.area) }}m<sup>2</sup></td>
                          <td>
                            <InputNumberFormat
                              label=""
                              v-model="unrecognize.average_land_unit_price"
                              vid="unrecognize_unit_price"
                              @change="changeUnitPriceUnrecognized($event , index)"
                              :max="9999999999"
                              :min="0"
                              :formatter="valueFormat => `${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                              class="input__unit-price"
                            />
                          </td>
                          <td>{{ format(unrecognize.estimate_price) }} đ</td>
                        </tr>
                        <tr v-if="recognized.length > 0" v-for="(recognize, index) in recognized" :key="'recognized'+index">
                          <td>Phần đất vi phạm quy hoạch</td>
                          <td>
                            <div v-if="landTypeRecognized[index] !== undefined && landTypeRecognized[index] !== null">
                              {{ landTypeRecognized[index].description }}
                            </div>
                          </td>
                          <td>{{formatFloat(recognize.area)}}m<sup>2</sup></td>
                          <td>
                            <InputNumberFormat
                              label=""
                              v-model="recognize.average_land_unit_price"
                              vid="unit_price"
                              @change="changeUnitPrice($event , index)"
                              :max="9999999999"
                              :min="0"
                              :formatter="valueFormat => `${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                              class="input__unit-price"
                            />
                          </td>
                          <td>{{format(recognize.estimate_price)}} đ</td>
                        </tr>
                        <tr>
                          <td>Tổng cộng:</td>
                          <td></td>
                          <td>{{ formatFloat(total.area) }}m<sup>2</sup></td>
                          <td></td>
                          <td>{{ format(total.price) }} đ</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  <div class="table tangible" v-if="building.length > 0">
                    <table class="w-100 table__tangible">
                      <thead>
                        <tr>
                          <th>Loại công trình</th>
                          <th>Diện tích sàn xây dựng</th>
                          <th>% Chất lượng còn lại</th>
                          <th>Đơn giá</th>
                          <th>Thành tiền</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="(build, index) in building" :key="'building'+index">
                          <td v-if="buildingTangible[index] !== undefined && buildingTangible[index] !== null">{{buildingTangible[index].description}}</td>
                          <td>{{formatFloat(build.area)}}m<sup>2</sup></td>
                          <td>{{build.remaining_quality}} %</td>
                          <td>
                            <InputNumberFormat
                              label=""
                              v-model="build.average_building_unit_price"
                              vid="building_unit_price"
                              @change="changeUnitPriceBuilding($event , index)"
                              :max="9999999999"
                              :min="0"
                              :formatter="valueFormat => `${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                              class="input__unit-price"
                            />
                          </td>
                          <td>{{format(build.estimate_price)}} đ</td>
                        </tr>
                        <tr v-if="building.length > 0">
                          <td>Tổng cộng:</td>
                          <td>{{ formatFloat(total_building.area)}}m<sup>2</sup></td>
                          <td></td>
                          <td></td>
                          <td>{{ format(total_building.price)}} đ</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
                <!-- footer -->
                <div class="container-title container-title__footer">
                        <div class="d-flex justify-content-between justify-content-lg-end">
                            <button class="btn btn-orange" type="button" @click="saveData">
                              <img src="../../assets/icons/ic_save.svg" style="margin-right: 12px" alt="save"/>Lưu</button>
                            <button class="btn btn-white btn-action-modal" type="button" @click="handleCancel">
                              <img src="../../assets/icons/ic_cancel.svg" style="margin-right: 12px" alt="save"/>Trở lại</button>
                        </div>
                </div>
            </div>
        </div>
    </ValidationObserver>
</template>

<script>
import InputCategory from '@/components/Form/InputCategory'
import InputText from '@/components/Form/InputText'
import InputNumberFormat from '@/components/Form/InputNumber'
import PriceEstimate from '@/models/PriceEstimate'
export default {
	name: 'Result',
	props: ['unrecognized', 'recognized', 'building', 'result', 'buildingTypes', 'landTypePurpose', 'buildingTangible', 'total_building', 'total'],
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
			total_price: ''
		}
	},
	created () {
	},
	mounted () {
		// this.getNow()
		// this.handleResult()
		// this.findBuilding()
		// this.findLandTypeRecognized()
		// this.findLandTypeUnrecognized()
	},
	components: {
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
			this.$emit('changeUnitPrice', event, index)
		},
		changeUnitPriceUnrecognized (event, index) {
			this.$emit('changeUnitPriceUnrecognized', event, index)
		},
		changeUnitPriceBuilding (event, index) {
			this.$emit('changeUnitPriceBuilding', event, index)
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
		},
		handleCancel (event) {
			this.$emit('cancel', event)
		},
		handleAction (event) {
			this.$emit('cancel', event)
			this.$emit('action', this.selectedRowKeys)
		},
		saveData () {

		}
	},
	beforeMount () {
		// this.handleResult()
	}
}
</script>

<style lang="scss" scoped>
.modal-detail {
  position: fixed;
  z-index: 1031;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.6);
  @media (max-width: 768px) {
    padding: 20px;
  }
  .card {
    border-radius: 5px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
    max-width: 1400px;
    width: 100%;
    max-height: 90vh;
    margin-bottom: 0;
    padding: 20px 30px;
    @media (max-width: 768px) {
      padding: 20px 10px;
    }
    &-header {
      border-bottom: 1px solid #dddddd;
      h3 {
        color: #333333;
      }
      img {
        cursor: pointer;
      }
    }
    &-body {
      text-align: center;
      p {
        color: #333333;
        margin-bottom: 40px;
      }

      .btn__group {
        .btn {
          max-width: 150px;
          width: 100%;
          margin: 0 10px;
        }
      }
    }
  }
}
.title-property {
  font-weight: 700;
  font-size: 18px;
  margin-bottom: 18px;
}
.input-contain {
  margin-bottom: 25px;
}
.card-table {
  border-radius: 5px;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
  background: #ffffff;
  max-width: 99%;
  margin: 50px auto 75px;
}
.card-table tbody tr:last-child td,
.card-table tbody tr:last-child th {
  border-bottom: 1px solid #e5e5e5;
}
.card {
  .contain-detail {
    overflow-y: auto;
    overflow-x: hidden;
    margin-top: 20px;
    margin-bottom: 20px;
    &::-webkit-scrollbar {
      width: 2px;
    }
  }
  &-title {
    background: #f3f2f7;
    padding: 16px 20px;
    margin-bottom: 0;
    &__img {
      padding: 8px 20px;
    }
    .title {
      font-weight: 600;
      margin-bottom: 0;
    }
  }
  &-body {
    padding: 35px 30px 40px;
  }
  &-info {
    .title {
      font-weight: 700;
      margin-top: 28px;
    }
  }
  &-land {
    position: relative;
    padding: 0;
  }
}
.table-property {
  width: 100%;
  font-weight: 400;
  color: #000000;
  text-align: center;
  thead {
    th {
      padding: 12px;
      font-weight: 400;
    }
  }
  tbody {
    td {
      border: 1px solid #e5e5e5;
      &:first-child {
        border-left: none;
      }
      &:last-child {
        border-right: none;
      }
      box-sizing: border-box;
      padding: 20px 14px;
    }
  }
}
.btn-delete {
  cursor: pointer;
  display: flex;
  align-items: center;
  background: #ffffff;
  border: 0.777778px solid #000000;
  border-radius: 5.88235px;
  padding: 10px;
  margin: auto;
  width: 36px;
  height: 36px;
  img {
    width: 100%;
    height: auto;
    min-width: 0.75rem;
  }
}
.contain-table {
  overflow-x: auto;
  @media (max-width: 1024px) {
    overflow-y: hidden;
    overflow-x: auto;
  }
  .table-property {
    width: 100%;
  }
}
.contain-file {
  display: flex;
  align-items: center;
  h3 {
    margin-top: 8px;
    margin-bottom: 0;
  }
}
.btn-upload {
  background: #ffffff;
  white-space: nowrap;
  border: 1px solid #555555;
  box-sizing: border-box;
  border-radius: 5px;
  padding: 5px 19px;
  font-size: 10px;
}
.btn-property {
  padding: 10px;
}
.img-dropdown {
  cursor: pointer;
  width: 18px;
  &__hide {
    transform: rotate(90deg);
    transition: 0.3s;
  }
}
.img-upload {
  margin-left: 20px;
  position: relative;
  width: 123px;
  height: 35px;
  color: #fff;
  background: #faa831;
  font-size: 14px;
  border-radius: 5px;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
  display: flex;
  justify-content: center;
  align-items: center;
  box-sizing: border-box;
  cursor: pointer;
  input {
    cursor: pointer !important;
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    width: 100%;
    opacity: 0;
  }
}
.contain-img {
  height: auto;
  position: relative;
  .img {
    width: 100%;
  }
  .delete {
    position: absolute;
    top: 0;
    right: 0;
    background: #000000;
    color: #ffffff;
    width: 20px;
    height: 20px;
    text-align: center;
    line-height: 1.5;
    cursor: pointer;
    font-weight: bold;
    border-radius: 5px;
  }
}
.contain-total {
  &__left {
    color: #000000;
    .num {
      padding: 0 11px 0 24px;
      width: 340px;
      height: 35px;
      line-height: 1.5;
      border-radius: 5px;
      border: 1px solid #555555;
      display: flex;
      align-items: center;
      justify-content: flex-end;
      background: #f1f1f1 !important;
      cursor: not-allowed;
      user-select: none;
      p {
        margin-bottom: 0;
      }
    }
    .name {
      min-width: 175px;
      margin-bottom: 0;
      margin-right: 20px;
    }
  }
}
.img-locate {
  cursor: pointer;
  position: absolute;
  right: 15px;
  top: 35px;
}
.form-control {
  width: 100%;
}
.btn-orange {
  background: #faa831;
  text-align: center;
  border-radius: 5px;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25) !important;
  height: 35px;
  width: 146px;
  color: #fff;
  margin-right: 15px;
  box-sizing: border-box;
  &:hover {
    border-color: #dc8300;
  }
}
.container-title {
  margin: -20px -30px auto;
  padding: 25px 30px 0;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
  @media (max-width: 767px) {
    margin: -20px -10px auto;
    padding: 20px 10px 0;
  }
  .title {
    font-size: 1.125rem;
    margin-bottom: 25px;
    font-weight: bold;
    @media (max-width: 767px) {
      font-size: 16px;
    }
  }
  &__footer {
    margin: auto -30px -20px;
    padding: 20px 30px 20px;
    @media (max-width: 767px) {
      margin: auto -10px -20px;
      padding: 20px 10px 0;
      .btn-white {
        margin-bottom: 20px;
      }
    }
  }
}
.contain-img {
  aspect-ratio: 1/1;
  overflow: hidden;
  height: auto;
  position: relative;
  text-align: center;
  margin-bottom: 10px;
  .img {
    object-fit: cover;
    margin-right: 0;
    width: 100%;
    height: 100%;
    cursor: pointer;
    &-table {
      margin: auto;
      min-width: 50px;
      min-height: 50px;
      width: 50px;
      height: 50px;
      object-fit: cover;
    }
  }
  &__table {
    width: auto;
  }
  .delete {
    position: absolute;
    top: 0;
    right: 0.75rem;
    background: #000000;
    color: #ffffff;
    width: 20px;
    height: 20px;
    text-align: center;
    line-height: 1.5;
    cursor: pointer;
    font-weight: bold;
    border-radius: 5px;
  }
}
.container-img {
  padding: 0.75rem 0;
  border: 1px solid #0b0d10;
}
.loading {
  display: none;
  &__true {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 100vh;
    background: rgba(0, 0, 0, 0.62);
    z-index: 100000;
    display: flex;
    align-items: center;
    justify-content: center;
    &.btn-loading {
      &:after {
        width: 2rem !important;
        height: 2rem !important;
      }
    }
  }
}
.input-disabled {
  min-height: 30px;
  height: 33px;
}
.text-none {
  text-transform: none;
}
.table-property {
  width: 100%;
  font-weight: 400;
  color: #000000;
  text-align: center;
  thead {
    tr {
      border-radius: 0 5px 5px 0;
    }
    th {
      padding: 12px 0;
      font-weight: 700;
      background-color: #f29003;
      color: #ffffff;
      @media (max-width: 787px) {
        padding: 12px;
      }
    }
  }
  tbody {
    td {
      border: 1px solid #e5e5e5;
      &:first-child {
        border-left: none;
      }
      padding: 20px 14px;
    }
  }
  &__order {
    tbody {
      td {
        &:first-child {
          width: 40%;
        }
        &:last-child {
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
.container-table {
  border-radius: 5px;
  border: 1px solid #f3f2f7;
}
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
      font-size: 16px;
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
    font-size: 16px;
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
        font-size: 14px;
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
  font-size: 16px;
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
