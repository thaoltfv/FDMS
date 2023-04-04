<template>
  <div class="container__synthetic">
    <ValidationObserver tag="form"
                        ref="observer"
                        @submit.prevent="validateBeforeSubmit">
      <div class="container__estimate">
          <h2 class="estimate__title">THÔNG TIN TÀI SẢN ƯỚC TÍNH</h2>
          <div class="container container__detail">
            <div class="table">
              <div class="table__header">
                <p class="title">Thông tin căn hộ</p>
              </div>
              <div class="table__body">
                <div>
                  <div class="row land__input">
                    <div class="container__input col-6 position-relative">
                      <InputNumberFormat
                        label="Tầng"
                        v-model="form.floor"
                        vid="floor"
                        rules="required"
                        @change="changeFloor($event)"
                        :min="0"
                        :max="9999"
                        class="input--land-type input__apartment-estimate"
                        :formatter="valueFormat => `${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                      />
                      <p class="check-error" v-show="errorFloor === true">Vui lòng nhập số tầng hợp lệ</p>
                    </div>
                    <div class="container__input col-6">
                      <InputText
                        v-model="form.block"
                        label="Block (khu)"
                        vid="block"
                        class="input--land-type input__apartment-estimate"
                      />
                    </div>
                    <div class="position-relative container__input col-6">
                      <InputNumberFormat
                        v-model="form.area"
                        label="Diện tích"
                        rules="required"
                        vid="area"
                        class="input--land-type input__apartment-estimate"
                        :min="0"
                        :max="99999999"
                        @change="changeArea($event)"
                        :formatter="valueFormat => `${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                      />
                      <p class="check-error" v-show="errorArea === true">Vui lòng nhập giá trị diện tích hợp lệ</p>
                    </div>
                    <div class="container__input col-6 position-relative">
                      <InputNumberFormat
                        v-model="form.bedroom_num"
                        label="Số phòng ngủ"
                        rules="required"
                        vid="bedroom_num"
                        class="input--land-type input__apartment-estimate"
                        :min="0"
                        :max="10"
                        @change="changeBedroomNum($event)"
                        :formatter="valueFormat => `${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                      />
                      <p class="check-error" v-show="errorBedroom === true">Vui lòng nhập số phòng ngủ hợp lệ</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="table">
              <div class="table__header">
                <p class="title"> Mô tả tài sản</p>
              </div>
              <div class="table__body">
                <div>
                  <div class="container__description row">
                    <div class="col-12 col-lg-6 row">
                      <p class="title col-6">Chung cư:</p>
                      <p class="content col-6">{{apartment_name}}</p>
                    </div>
                  </div>
                  <div class="container__description row">
                    <div class="col-12 col-lg-6 row">
                      <p class="title col-6">Tầng:</p>
                      <p class="content col-6">{{format(form.floor)}}</p>
                    </div>
                    <div class="col-12 col-lg-6 row">
                      <p class="title col-6">Block (khu):</p>
                      <p class="content col-6">{{form.block}}</p>
                    </div>
                  </div>
                  <div class="container__description row">
                    <div class="col-12 col-lg-6 row">
                      <p class="title col-6">Diện tích:</p>
                      <p class="content col-6">{{ form.area ? formatArea(form.area) : ''}}m<sup>2</sup></p>
                    </div>
                    <div class="col-12 col-lg-6 row">
                      <p class="title col-6">Số phòng ngủ:</p>
                      <p class="content col-6">{{form.bedroom_num}}</p>
                    </div>
                  </div>
                  <div class="container__description row">
                    <div class="col-12 row">
                      <p class="title col-3">Địa chỉ:</p>
                      <p class="content content--full-address col-9">{{address.full_address}}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div>
          </div>
          <div class="d-flex justify-content-end">
            <button class="btn btn-orange btn-orange__result">Xem kết quả</button>
          </div>
        </div>
      </div>
    </ValidationObserver>
    <div id="result" v-if="result_estimate">
      <ResultEstimate :get_result="result" :landTypePurpose="landType" :buildingTypes="housingTypes" :user="user" :location="location"/>
    </div>
  </div>
</template>
<script>
import ResultEstimate from '@/pages/price_estimate/components/ResultEstimate'
import ModalTangibleEstimate from '@/components/Modal/ModalTangibleEstimate'
import InputCategory from '@/components/Form/InputCategory'
import InputNumberFormat from '@/components/Form/InputNumber'
import InputSwitch from '@/components/Form/InputSwitch'
import InputText from '@/components/Form/InputText'
import PriceEstimate from '@/models/PriceEstimate'
import WareHouse from '@/models/WareHouse'
export default {
	name: 'EstimateApartment',
	props: ['landType', 'buildingCategories', 'housingTypes', 'get_result', 'location', 'distance', 'address', 'user', 'apartment'],
	data () {
		return {
			errorFloor: false,
			errorArea: false,
			errorBedroom: false,
			item_print: {
				id: '',
				location: '',
				user_request: '',
				error_message: '',
				floor: '',
				block: '',
				area: '',
				bedroom_num: '',
				estimate_type: '',
				unit_price: ''
			},
			full_address: '',
			result_estimate: false,
			result: '',
			alley: true,
			show_estimate: false,
			openModalEstimate: false,
			provinces: [],
			districts: [],
			wards: [],
			streets: [],
			form: {
				floor: '',
				block: '',
				area: '',
				bedroom_num: ''
			},
			apartment_name: ''
		}
	},
	components: {
		InputCategory,
		InputText,
		InputNumberFormat,
		ModalTangibleEstimate,
		InputSwitch,
		ResultEstimate
	},
	mounted () {
		// this.getApartments()
	},
	computed: {
		optionLandTypePurpose () {
			return {
				data: this.landType,
				key: 'description',
				id: 'id'
			}
		},
		optionBuildingCategories () {
			return {
				data: this.buildingCategories,
				key: 'description',
				id: 'id'
			}
		},
		optionHousingTypes () {
			return {
				data: this.housingTypes,
				key: 'description',
				id: 'id'
			}
		},
		optionProvince () {
			return {
				data: this.provinces,
				key: 'name',
				id: 'id'
			}
		},
		optionDistrict () {
			return {
				data: this.districts,
				key: 'name',
				id: 'id'
			}
		},
		optionWard () {
			return {
				data: this.wards,
				key: 'name',
				id: 'id'
			}
		},
		optionStreet () {
			return {
				data: this.streets,
				key: 'name',
				id: 'id'
			}
		}
	},
	methods: {
		getItem () {
			this.item_print.estimate_type = this.address.estimate_type
			this.item_print.position = this.address.full_address
			this.item_print.apartment_id = this.address.apartment_id
			this.item_print.location = this.location
			this.item_print.floor = this.form.floor
			this.item_print.block = this.form.block
			this.item_print.area = this.form.area
			this.item_print.bedroom_num = this.form.bedroom_num
			this.item_print.id = this.result.id
			this.item_print.total_price = this.result.result.total_price
			this.item_print.error_message = this.result.error_message
			this.item_print.unit_price = this.result.result.unit_price
		},
		handleTangibleEstimate (tangible, index) {
			if (!this.isEditTangible) {
				this.form.building.push(tangible)
			} else {
				this.form.building[index] = tangible
			}
		},
		async getAddress () {
			this.form.province_id = this.address.province_id
			this.form.district_id = this.address.district_id
			this.form.ward_id = this.address.ward_id
			this.form.street_id = this.address.street_id
			await this.getProvinces()
			await this.getFullAddress()
		},
		async geocodeAddress (geocoder) {
			let center = {}
			const address = document.getElementById('full_address_estimate').value
			await geocoder.geocode({'address': address}, function (results, status) {
				if (status === 'OK') {
					const marker = {
						position: results[0].geometry.location
					}
					center = [parseFloat(marker.position.lat()), parseFloat(marker.position.lng())]
				} else {
				}
			})
			this.center = center
			this.markerLatLng = center
			this.circle.center = center
			await this.getAssetGenerals()
		},
		changeMainRoadLength (event) {
			this.form.main_road_length = event
		},
		async validateBeforeSubmit () {
			const isValid = await this.$refs.observer.validate()
			if (isValid) {
				if (this.errorArea === true) {
					this.$toast.open({
						message: 'Vui lòng nhập giá trị diện tích hợp lệ',
						type: 'error',
						position: 'top-right'
					})
				} else if (this.errorFloor === true) {
					this.$toast.open({
						message: 'Vui lòng nhập số tầng hợp lệ',
						type: 'error',
						position: 'top-right'
					})
				} else if (this.errorBedroom === true) {
					this.$toast.open({
						message: 'Vui lòng nhập số phòng ngủ hợp lệ',
						type: 'error',
						position: 'top-right'
					})
				} else {
					await this.handleResult()
				}
			} else {
				this.$toast.open({
					message: 'Vui lòng nhập đầy đủ các trường bắt buộc',
					type: 'error',
					position: 'top-right'
				})
			}
		},
		handleEstimateResult () {
			window.location = '#result'
		},
		format (value) {
			let num = (value / 1).toFixed(0).replace('.', ',')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.')
		},
		formatArea (value) {
			let num = (value / 1).toFixed(2).replace(',', '.')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
		},
		async changeArea (event) {
			if (event !== undefined && event !== null) {
				this.form.area = parseFloat(event).toFixed(2)
			} else {
				this.form.area = event
			}
			if (event === '' || event === undefined || event === null || this.form.area > 0) {
				this.errorArea = false
			} else {
				this.errorArea = true
			}
		},
		async changeBedroomNum (event) {
			if (event !== undefined && event !== null) {
				this.form.bedroom_num = parseInt(event)
			} else {
				this.form.bedroom_num = event
			}
			if (event === '' || event === undefined || event === null || this.form.bedroom_num > 0) {
				this.errorBedroom = false
			} else {
				this.errorBedroom = true
			}
		},
		async changeFloor (event) {
			if (event !== undefined && event !== null) {
				this.form.floor = parseInt(event)
			} else {
				this.form.floor = event
			}
			if (event === '' || event === undefined || event === null || this.form.floor > 0) {
				this.errorFloor = false
			} else {
				this.errorFloor = true
			}
		},
		getRecognizedArea (event, index) {
			for (let i = 0; i < this.form.recognized.length; i++) {
				if (i === index) {
					this.form.recognized[i].area = parseFloat(+event).toFixed(2)
				}
			}
		},
		changeQualityArea (event, index) {
			for (let i = 0; i < this.form.building.length; i++) {
				if (i === index) {
					this.form.building[i].remaining_quality = parseInt(+event)
				}
			}
		},
		changeBuildingArea (event, index) {
			for (let i = 0; i < this.form.building.length; i++) {
				if (i === index) {
					this.form.building[i].area = parseFloat(+event).toFixed(2)
				}
			}
		},
		async handleResult () {
			this.result_estimate = false
			await this.PostPriceEstimate()
			this.result_estimate = true
			await this.getItem()
			this.$emit('get_item', this.item_print)
			this.$emit('action')
			this.$emit('result', this.result)
		},
		handleAddBuilding () {
			this.form.building.push({
				building_category: '',
				remaining_quality: '',
				area: ''
			})
		},
		editEstimate (data, index) {
			this.isEditTangible = true
			this.openModalEstimate = true
			this.tangible = data
			this.tangible_index = index
		},
		handleDeleteBuilding (index) {
			this.form.building.splice(index, 1)
		},
		handleAddRecognized () {
			this.form.recognized.push({
				land_type_purpose: '',
				land_type_purpose_name: '',
				area: ''
			})
		},
		async getApartments () {
			try {
				const resp = await WareHouse.getApartment()
				this.apartments = [...resp.data]
			} catch (err) {
				this.isSubmit = false
				throw err
			}
			this.getApartmentName()
		},
		getApartmentName () {
			this.apartments.forEach(apartment => {
				if (apartment.id === this.address.apartment_id) {
					this.apartment_name = apartment.name
				}
			})
		},
		handleDeleteRecognized (index) {
			this.form.recognized.splice(index, 1)
			this.changeAreRecognized()
		},
		handleAddUnrecognized () {
			this.form.unrecognized.push({
				land_type_purpose: '',
				land_type_purpose_name: '',
				area: ''
			})
		},
		handleDeleteUnrecognized (index) {
			this.form.unrecognized.splice(index, 1)
			this.changeAreRecognized()
		},
		async PostPriceEstimate () {
			const location = this.location[0] + ',' + this.location[1]
			const distance = parseFloat(this.distance / 1000).toFixed(2)
			const estimate_type = this.address.estimate_type
			const apartment_id = this.address.apartment_id
			const apartment = this.form
			let district_id = ''
			if (this.apartment !== undefined && this.apartment !== null) {
				district_id = this.apartment.district_id
			} else {
				district_id = ''
			}
			const resp = await PriceEstimate.PostPriceEstimateApartment({location, distance, estimate_type, apartment_id, apartment, district_id})
			if (resp.data) {
				this.result = resp.data
			} else if (resp.error) {
				this.$toast.open({
					message: resp.error.message,
					type: 'error',
					position: 'top-right'
				})
			}
		}
	}
}
</script>

<style lang="scss" scoped>
.container {
  &__estimate {
    max-width: 1710px;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.25);
    border-radius: 5px;
    margin: 0 auto 20px;
    padding: 30px 30px 50px;
    @media (max-width: 767px) {
      padding: 20px;
    }
  }
  &__detail{
    max-width: 1100px;
    margin: auto;
  }
  &__cancel{
    padding: 10px 0;
    border-bottom: 1px solid #D0D0D0;
    margin-bottom: 30px;
  }
  &__synthetic{
    margin-bottom: 50px;
  }
  &__total{
    .title{
      margin-right: 15px;
      margin-bottom: 0;
      font-size: 1.125rem;
      color: #000000;
    }
    .input-total{
      height: 40px;
      width: 306px;
      background: #E5E5E5;
      border-radius: 5px;
      padding: 0 10px;
      display: flex;
      align-items: center;
      .total{
        margin-bottom: 0;
      }
    }
  }
  &__input{
    @media (max-width: 1440px) {
      width: auto;
      margin-right: 10px;
    }
  }
  &__description {
    padding: 8px 20px;
    border-bottom: 1px solid #D0D0D0;
    &:last-child {
      border-bottom: none;
    }
    .title, .content {
      padding: 0;
      color: #000000;
      margin-bottom: 0;
    }
    .title{
      font-weight: 600;

    }
    .content{
      white-space: nowrap;
      @media (max-width: 767px) {
        white-space: normal;
      }
      &--full-address{
        white-space: normal;
        margin-left: -5px;
      }
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
  &__edit {
    text-align: center;
    color: #F28C1C;
    cursor: pointer;
  }
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
    overflow-x: auto;
    overflow-y: hidden;
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
  &-orange{
    font-weight: 600;

    @media (max-width: 767px) {
      margin-bottom: 10px;
    }
  }
  &-cancel{
    color: #999999;
    box-shadow: none !important;
    padding: 0;
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
    }
  }
}
.table {
  margin-bottom: 35px;
  &__tangible{
    thead {
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
      tr {
        td{
          width: 23%;
          @media (max-width: 767px) {
            min-width: 200px;
          }
        }
      }
    }
  }
  &__description{
    thead{
      background: #F28C1C;
      text-align: left;
      tr {
        th{
          color: #FFFFFF;

          font-weight: 700;
          text-transform: none;
        }
      }
    }
    tbody{
      text-align: left;
      tr{
        td{
          &:first-child {
            width: 20%;
            font-weight: 700;
          }
          white-space: nowrap;
          color: #000000;
        }
      }
    }
  }
}
.tangible{
  margin-top: 50px;
  .title{
    font-weight: 700;
  }
}
.estimate{
  &__title{
    font-size: 30px;
    text-align: center;
    text-transform: uppercase;
    color: #000000;
    font-weight: 700;
    margin-bottom: 60px;
  }
}
.plot-num{
  margin-left: 30px;
  .name{
    margin-right: 30px;
    font-weight: 700;
  }
}
.input {
  &__num{
    @media (max-width: 767px) {
      min-width: 200px;
    }
  }
}
.check-error{
  position: absolute;
  bottom: 0;
  margin-bottom: 0;
  font-size: 12px;
  color: #cd201f;
  left: 123px;
}
</style>
