<template>
  <div class="container__synthetic">
    <ValidationObserver tag="form"
                        ref="observer"
                        @submit.prevent="validateBeforeSubmit">
      <div class="container__estimate">
        <h2 class="estimate__title">THÔNG TIN TÀI SẢN ƯỚC TÍNH</h2>
        <div class="container container__detail">
          <div class="d-flex align-items-center justify-content-between" style="margin-bottom: 35px">
            <div class="row align-items-center">
              <div class="d-flex align-items-center">
                <p class="front-side">Mặt tiền</p>
                <div class="d-flex align-items-center">
                  <p class="mb-0 mr-2">Có</p>
                  <ToggleSwitch group="a" :options="myOptions" @change="changeSwitchFrontSide($event)"/>
                  <p class="mb-0 ml-2">Không</p>
                </div>
              </div>
              <div>
                <InputNumberFormat
                  v-model="form.main_road_length"
                  label="Bề rộng hẻm"
                  vid="individual_road"
                  rules="required"
                  class="input-text__estimate"
                  :max="99999999"
                  :min="0"
                  v-if="alley"
                  :formatter="valueFormat => `${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                  @change="changeMainRoadLength($event)"
                />
              </div>
            </div>
            <div>
              <InputSwitch
                v-model="property_type"
                vid="property_type"
                label="Công trình xây dựng"
                class="contain-input contain-input__tangible"
                @input="changePropertyType"
              />
            </div>
          </div>
          <div class="table table--margin">
            <div class="table__header">
              <p class="title">Phần đất phù hợp quy hoạch</p>
            </div>
            <div class="table__body">
              <div v-for="(unrecognized, index) in form.unrecognized" :key="index">
                <div class="d-flex land__input">
                  <div class="container__input col-5">
                    <InputCategory
                      label="Loại đất"
                      rules="required"
                      v-model="unrecognized.land_type_purpose"
                      :vid="'recognized_land_type_purpose' + index"
                      :options="optionLandTypePurpose"
                      @change="changeUnrecognized(index, form.unrecognized)"
                      class="input--land-type"
                    />
<!--                    <span class="errors-messages" v-if="error_duplicate_unrecognized[index] !== ''" >{{error_duplicate_unrecognized[index]}}</span>-->
                  </div>
                  <div class="position-relative container__input col-5">
                    <InputNumberFormat
                      v-model="unrecognized.area"
                      label="Diện tích"
                      rules="required"
                      :vid="'recognized_area' + index"
                      class="input--land-type"
                      :min="0"
                      :max="99999999"
                      :formatter="valueFormat => `${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                      @change="changeAreUnrecognized($event, index)"
                    />
                    <p class="check-error check-error__land" v-show="checkUnrecognized[index] && checkUnrecognized[index].error">Vui lòng nhập giá trị diện tích hợp lệ</p>
                  </div>
                  <div class="container__input col-2 d-flex align-items-center">
                    <img class="ic__delete" src="../../../assets/icons/ic_delete.svg" alt="" @click="handleDeleteUnrecognized(index, form.unrecognized)">
                  </div>
                </div>
              </div>
              <p v-show="duplicateUnrecognized" class="text-danger mt-3">Trùng mục đích</p>
              <div>
                <button type="button" class="btn btn__add" @click="handleAddUnrecognized(form.unrecognized)">
                  <img src="../../../assets/icons/ic_add.svg" alt="">Thêm
                </button>
              </div>
            </div>
          </div>
          <div class="table">
            <div class="table__header">
              <p class="title">Phần đất vi phạm quy hoạch</p>
            </div>
            <div class="table__body">
              <div v-for="(recognized, index) in form.recognized" :key="index">
                <div class="d-flex land__input">
                  <div class="container__input col-5">
                    <InputCategory
                      label="Loại đất"
                      v-model="recognized.land_type_purpose"
                      :vid="'unrecognized_land_type_purpose' + index"
                      rules="required"
                      :options="optionLandTypeRecognized"
                      @change="changeRecognized(index, form.recognized)"
                      class="input--land-type"
                    />
<!--                    <span class="errors-messages" v-if="error_duplicate_recognized[index] !== ''" >{{error_duplicate_recognized[index]}}</span>-->
                  </div>
                  <div class="position-relative container__input col-5">
                    <InputNumberFormat
                      v-model="recognized.area"
                      label="Diện tích"
                      rules="required"
                      :vid="'unrecognized_area' + index"
                      class="input--land-type"
                      :min="0"
                      @change="changeAreRecognized($event, index)"
                      :formatter="valueFormat => `${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                    />
                    <p class="check-error check-error__land" v-show="checkRecognized[index] && checkRecognized[index].error">Vui lòng nhập giá trị diện tích hợp lệ</p>
                  </div>
                  <div class="container__input col-2 d-flex align-items-center">
                    <img class="ic__delete" src="../../../assets/icons/ic_delete.svg" alt="" @click="handleDeleteRecognized(index, form.recognized)">
                  </div>
                </div>
              </div>
              <p v-show="duplicateRecognized" class="text-danger mt-3">Trùng mục đích</p>
              <div>
                <button type="button" class="btn btn__add" @click="handleAddRecognized(form.recognized)">
                  <img src="../../../assets/icons/ic_add.svg" alt="">Thêm
                </button>
              </div>
            </div>
          </div>
          <div class="d-flex align-items-center justify-content-end container__total mb-3">
            <p class="title"> Tổng diện tích </p>
            <div class="input-total"><p class="total">{{formatFloat(form.total_area) }}</p></div>
          </div>
          <div class="tangible" v-if="property_type">
            <h3 class="title">Công trình xây dựng</h3>
            <div class="table">
              <table class="w-100 table__tangible">
                <thead>
                <tr>
                  <th>Loại</th>
                  <th>Diện tích sàn</th>
                  <th>% Chất lượng còn lại</th>
                  <th></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(build, index) in form.building" :key="'building'+index">
                  <td v-if="form.building.length > 0">
                    <InputCategory
                      v-model="build.building_category"
                      rules="required"
                      :vid="'building_type' + index"
                      label="Loại nhà"
                      :options="optionHousingTypes"
                      class="input__table"
                    />
                  </td>
                  <td>
                    <InputNumberFormat
                      v-model="build.area"
                      rules="required"
                      :vid="'build_area' + index"
                      label="Diện tích"
                      :max="99999999"
                      :min="0"
                      :formatter="valueFormat => `${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                      @change="changeBuildingArea($event, index)"
                      class="input__table"
                    />
                    <p class="check-error" v-show="checkBuilding[index] && checkBuilding[index].error">Vui lòng nhập giá trị diện tích hợp lệ</p>
                  </td>
                  <td>
                    <InputNumberFormat
                      v-model="build.remaining_quality"
                      :vid="'build_remaining_quality' + index"
                      rules="required"
                      label="Chất lượng"
                      :max="100"
                      @change="changeQualityArea($event, index)"
                      class="input__table"
                    />
                  </td>
                  <td>
                    <div>
                      <img class="ic__delete" src="../../../assets/icons/ic_delete.svg" alt="" @click="handleDeleteBuilding(index)">
                    </div>
                  </td>
                </tr>
                </tbody>
              </table>
              <p v-show="duplicateBuilding" class="text-danger ml-2 mb-0">Loại công trình xây dựng không được trùng nhau</p>
              <div class="table__body">
                <button type="button" class="btn btn__add" @click="handleAddBuilding(form.building)"><img src="../../../assets/icons/ic_add.svg" alt="">Thêm</button>
              </div>
            </div>
          </div>
          <div>
            <div class="table">
              <table class="w-100 table__description">
                <thead>
                <tr>
                  <th>Mô tả tài sản</th>
                  <th></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                  <td>
                    Loại tài sản:
                  </td>
                  <td>
                    {{ form.building.length > 0 ? 'Đất có nhà' : 'Đất' }}
                  </td>
                </tr>
                <tr>
                  <td>
                    Vị trí:
                  </td>
                  <td>
                    {{position}}
                  </td>
                </tr>
                <tr>
                  <td>
                    Tọa độ:
                  </td>
                  <td>
                    {{coordinates[0] + ', ' + coordinates[1]}}
                  </td>
                </tr>
                <tr>
                  <td>
                    Số tờ:
                  </td>
                  <td>
                    <div class="d-flex align-items-center">
                      <InputText
                        label=""
                        vid="doc_num"
                        v-model="form.doc_num"
                        class="input__num"
                        @change="changeDoc"
                      />
                      <div class="plot-num d-flex align-items-center">
                        <div class="name">Số thửa: </div>
                        <InputText
                          label=""
                          vid="plot_num"
                          v-model="form.plot_num"
                          class="input__num"
                          @change="changePlot"
                        />
                      </div>
                    </div>
                  </td>
                </tr>
                 <tr>
                  <td class="name"><div class="note_class">Ghi chú:</div></td>
                  <td>
                    <InputTextarea
                      label=""
                      vid="note"
                      v-model="form.note"
                      class="input-contain"
                      @change="getNoteRequest"
                    />
                  </td>
                </tr>
                <tr>
                  <td class="name">Người yêu cầu:</td>
                  <td>
                    <InputText
                      label=""
                      vid="request"
                      v-model="request"
                      @change="getUserRequest"
                    />
                  </td>
                </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="d-flex justify-content-end">
            <button class="btn btn-orange btn-orange__result">Xem kết quả</button>
          </div>
        </div>
      </div>
    </ValidationObserver>
    <div id="result" v-if="result_estimate">
      <Result ref="result" :user_request="request" :get_result="result" :landTypePurpose="landType" :buildingTypes="housingTypes" :location="this.coordinates" :address="this.address" :user="user" :input="form" @get_item="getItem" @warning="getWarning"/>
    </div>
    <ModalTangibleEstimate
      v-if="openModalEstimate"
      @cancel="openModalEstimate = false"
      :tangible ="this.tangible"
      :tangible_index = "this.tangible_index"
      @action="handleTangibleEstimate"
    />
    <ModalCancelCustomerTangible
      v-if="openModalTangibleCancel"
      @cancel="cancelTangible"
      :message="'Chọn về không sẽ không có dữ liệu công trình xây dựng. Bạn có muốn thay đổi không?'"
      @action="acceptTangible"
    />
  </div>
</template>
<script>
import Result from '@/pages/price_estimate/components/Result'
import ModalTangibleEstimate from '@/components/Modal/ModalTangibleEstimate'
import InputCategory from '@/components/Form/InputCategory'
import InputNumberFormat from '@/components/Form/InputNumber'
import InputSwitch from '@/components/Form/InputSwitch'
import InputText from '@/components/Form/InputText'
import InputTextarea from '@/components/Form/InputTextarea'
import PriceEstimate from '@/models/PriceEstimate'
import {STATUS} from '@/enum/status.enum'
import WareHouse from '@/models/WareHouse'
import ToggleSwitch from '@/components/Form/ToggleSwitch'
import ModalCancelCustomerTangible from '../../../components/Modal/ModalCancelCustomerTangible'

export default {
	name: 'Estimate',
	props: ['myOptions', 'landType', 'buildingCategories', 'housingTypes', 'get_result', 'location', 'distance', 'address', 'user', 'transaction_type_ids'],
	data () {
		return {
			openModalTangibleCancel: false,
			property_type: false,
			landUnrecognized: [],
			landRecognized: [],
			full_address_position: '',
			coordinates: '',
			item_print: {
				id: '',
				position: '',
				asset_type: 'Đất',
				location: '',
				user_request: '',
				doc_num: '',
				plot_num: '',
				estimate_type: '',
				building: null,
				recognized: null,
				unrecognized: null,
				total_area_building: 0,
				total_area_land: 0,
				total_land: 0,
				total_building: 0,
				landTypeRecognized: [],
				landTypeUnrecognized: [],
				buildingTangible: [],
				error_message: ''
			},
			request: '',
			full_address: '',
			front_side_switch: false,
			result_estimate: false,
			result: '',
			alley: false,
			show_estimate: false,
			openModalEstimate: false,
			provinces: [],
			districts: [],
			wards: [],
			streets: [],
			position: '',
			form: {
				note: '',
				main_road_length: '',
				province_id: '',
				province: '',
				district_id: '',
				district: '',
				ward_id: '',
				ward: '',
				street_id: '',
				street: '',
				front_side: '',
				doc_num: '',
				plot_num: '',
				type: '',
				total_area: '',
				result: '',
				asset_types: [],
				transaction_type_ids: [],
				recognized: [
				],
				unrecognized: [
				],
				building: [
				]
			},
			checkUnrecognized: [],
			checkRecognized: [],
			checkBuilding: [],
			error_duplicate_unrecognized: [],
			error_duplicate_recognized: [],
			duplicateUnrecognized: false,
			duplicateRecognized: false,
			duplicateBuilding: false
		}
	},
	components: {
		InputCategory,
		InputText,
		InputNumberFormat,
		ModalTangibleEstimate,
		InputSwitch,
		Result,
		ToggleSwitch,
		ModalCancelCustomerTangible,
		InputTextarea
	},
	computed: {
		optionLandTypePurpose () {
			return {
				data: this.landUnrecognized,
				key: 'description',
				id: 'id',
				disabled: 'disabled'
			}
		},
		optionLandTypeRecognized () {
			return {
				data: this.landRecognized,
				key: 'description',
				id: 'id',
				disabled: 'disabled'
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
	mounted () {
		this.landType.forEach(land => {
			this.landUnrecognized.push(
				{
					id: land.id,
					description: land.description,
					disabled: false
				}
			)
			this.landRecognized.push(
				{
					id: land.id,
					description: land.description,
					disabled: false
				}
			)
		})
	},
	methods: {
		getWarning (e) {
			this.$emit('warning', e)
		},
		changePropertyType () {
			if (this.form.building.length > 0 && this.property_type === false) {
				this.openModalTangibleCancel = true
			}
		},
		acceptTangible () {
			this.form.building = []
			this.checkBuilding = []
			this.openModalTangibleCancel = false
		},
		cancelTangible () {
			this.property_type = true
			this.openModalTangibleCancel = false
		},
		getIsPrint () {
			this.$refs.result.getPrint()
		},
		getAddressEstimate () {
			this.full_address_position = this.address.full_address
			this.coordinates = this.location
			if (this.form.front_side === 1) {
				this.position = 'Tiếp giáp mặt tiền ' + this.address.full_address
			} else if (this.form.front_side === 0) {
				this.position = 'Tiếp giáp hẻm rộng khoảng ' + this.format(this.form.main_road_length) + ' m gần ' + this.address.full_address
			} else {
				this.position = this.address.full_address
			}
		},
		getItem (item) {
			this.item_print.position = this.position
			this.item_print.location = this.coordinates
			this.item_print.user = this.user
			this.item_print.estimate_type = this.address.estimate_type
			this.item_print.building = item.building
			this.item_print.recognized = item.recognized
			this.item_print.unrecognized = item.unrecognized
			this.item_print.total_price = item.total_price
			this.item_print.total_area_building = item.total_area_building
			this.item_print.total_area_land = item.total_area_land
			this.item_print.total_land = item.total_land
			this.item_print.total_building = item.total_building
			this.item_print.landTypeRecognized = item.landTypeRecognized
			this.item_print.landTypeUnrecognized = item.landTypeUnrecognized
			this.item_print.buildingTangible = item.buildingTangible
			this.item_print.error_message = item.error_message
			this.item_print.id = item.id
			this.$emit('get_item', this.item_print)
		},
		getNoteRequest () {
			this.item_print.note = this.form.note
		},
		getUserRequest () {
			this.item_print.user_request = this.request
		},
		changeDoc () {
			this.item_print.doc_num = this.form.doc_num
		},
		changePlot () {
			this.item_print.plot_num = this.form.plot_num
		},
		changeSwitchFrontSide (event) {
			if (event.value === 'Tất cả') {
				this.form.front_side = ''
			} else if (event.value === 'Có') {
				this.form.front_side = 1
				this.form.main_road_length = ''
				this.position = 'Tiếp giáp mặt tiền ' + this.full_address_position
			} else if (event.value === 'Không') {
				this.form.front_side = 0
				this.position = 'Tiếp giáp hẻm rộng khoảng ' + this.format(this.form.main_road_length) + ' m gần ' + this.full_address_position
			}
			this.alley = this.form.front_side === 0
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
			await this.getFullAddress()
		},
		async geocodeAddress (geocoder) {
			let center = {}
			const address = document.getElementById('full_address_estimate').value
			await geocoder.geocode({'address': address}, function (results, status) {
				if (status === 'OK') {
					const marker = {
						position: results[0].geometry.coordinates
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
		changeProvince (provinceId) {
			this.districts = []
			this.wards = []
			this.streets = []
			this.form.district_id = ''
			this.form.ward_id = ''
			this.form.street_id = ''
			this.getDistrictsByProvinceId(+provinceId)
			const data = this.form
			let provinceName = ''
			this.provinces.forEach(province => {
				if (province.id === data.province_id) {
					provinceName = province.name
				}
			})
			this.form.province = provinceName
			this.full_address = provinceName
		},
		changeDistrict (districtId) {
			this.wards = []
			this.streets = []
			this.form.ward_id = ''
			this.form.street_id = ''
			this.getWardsByDistrictId(+districtId)
			this.getStreetByDistrictId(+districtId)
			const data = this.form
			let provinceName = ''
			let districtName = ''
			this.provinces.forEach(province => {
				if (province.id === data.province_id) {
					provinceName = province.name
					this.districts.forEach(district => {
						if (district.id === data.district_id) {
							districtName = district.name
						}
					})
				}
			})
			this.full_address = districtName + ', ' + provinceName
			this.form.district = districtName
		},
		changeWard () {
			const data = this.form
			let provinceName = ''
			let districtName = ''
			let wardName = ''
			let streetName = ''
			this.provinces.forEach(province => {
				if (province.id === data.province_id) {
					provinceName = province.name
					this.districts.forEach(district => {
						if (district.id === data.district_id) {
							districtName = district.name
							this.wards.forEach(ward => {
								if (ward.id === data.ward_id) {
									wardName = ward.name
								}
							})
							this.streets.forEach(street => {
								if (street.id === data.street_id) {
									streetName = street.name
								}
							})
						}
					})
				}
			})
			if (wardName === '') {
				this.full_address = streetName + ', ' + districtName + ', ' + provinceName
			} else if (streetName === '') {
				this.full_address = wardName + ', ' + districtName + ', ' + provinceName
			} else {
				this.full_address = streetName + ', ' + wardName + ', ' + districtName + ', ' + provinceName
			}
			this.form.ward = wardName
		},
		changeStreet () {
			const data = this.form
			let provinceName = ''
			let districtName = ''
			let wardName = ''
			let streetName = ''
			this.provinces.forEach(province => {
				if (province.id === data.province_id) {
					provinceName = province.name
					this.districts.forEach(district => {
						if (district.id === data.district_id) {
							districtName = district.name
							this.wards.forEach(ward => {
								if (ward.id === data.ward_id) {
									wardName = ward.name
								}
							})
							this.streets.forEach(street => {
								if (street.id === data.street_id) {
									streetName = street.name
								}
							})
						}
					})
				}
			})
			if (wardName === '') {
				this.full_address = streetName + ', ' + districtName + ', ' + provinceName
			} else if (streetName === '') {
				this.full_address = wardName + ', ' + districtName + ', ' + provinceName
			} else {
				this.full_address = streetName + ', ' + wardName + ', ' + districtName + ', ' + provinceName
			}
			this.form.street = streetName
		},
		getFullAddress () {
			const data = this.form
			let provinceName = ''
			let districtName = ''
			let wardName = ''
			let streetName = ''
			this.provinces.forEach(province => {
				if (province.id === data.province_id) {
					provinceName = province.name
					this.districts.forEach(district => {
						if (district.id === data.district_id) {
							districtName = district.name
							this.wards.forEach(ward => {
								if (ward.id === data.ward_id) {
									wardName = ward.name
								}
							})
							this.streets.forEach(street => {
								if (street.id === data.street_id) {
									streetName = street.name
								}
							})
						}
					})
				}
			})
			if (wardName === '') {
				this.full_address = streetName + ', ' + districtName + ', ' + provinceName
			} else if (streetName === '') {
				this.full_address = wardName + ', ' + districtName + ', ' + provinceName
			} else {
				this.full_address = streetName + ', ' + wardName + ', ' + districtName + ', ' + provinceName
			}
			this.form.province = provinceName
			this.form.district = districtName
			this.form.ward = wardName
			this.form.street = streetName
		},
		changeMainRoadLength (event) {
			if (event !== undefined && event !== null) {
				this.form.main_road_length = parseFloat(event).toFixed(2)
			} else {
				this.form.main_road_length = 0
			}
			if (this.form.front_side === 0) {
				this.position = 'Tiếp giáp hẻm rộng khoảng ' + this.formatFloat(this.form.main_road_length) + ' m, gần ' + this.full_address_position
			}
		},
		async validateBeforeSubmit () {
			let isValid = await this.$refs.observer.validate()
			let checkUnrecognized = false
			let checkRecognized = false
			let checkBuilding = false
			this.checkUnrecognized.forEach(checkError => {
				if (checkError.error === true) {
					checkUnrecognized = true
				}
			})
			this.checkRecognized.forEach(checkError => {
				if (checkError.error === true) {
					checkRecognized = true
				}
			})
			this.checkBuilding.forEach(checkError => {
				if (checkError.error === true) {
					checkBuilding = true
				}
			})
			if (isValid) {
				this.form.status = STATUS.ACTIVE
				if (this.checkDuplicateRecognized(this.form.recognized)) {
					this.duplicateRecognized = true
					this.$toast.open({
						message: 'Vui lòng không chọn trùng loại đất của phần đất vi phạm quy hoạch',
						type: 'error',
						position: 'top-right'
					})
				} else if (this.checkDuplicateUnrecognized(this.form.unrecognized)) {
					this.duplicateUnrecognized = true
					this.$toast.open({
						message: 'Vui lòng không chọn trùng loại đất của phần đất phù hợp quy hoạch',
						type: 'error',
						position: 'top-right'
					})
				} else if (this.form.front_side === '' || this.form.front_side === undefined || this.form.front_side === null) {
					this.$toast.open({
						message: 'Vui lòng chọn mặt tiền',
						type: 'error',
						position: 'top-right'
					})
				} else if (checkUnrecognized === true || checkRecognized === true || checkBuilding) {
					this.$toast.open({
						message: 'Vui lòng nhập giá trị diện tích hợp lệ',
						type: 'error',
						position: 'top-right'
					})
				} else if ((this.property_type && ((this.form.recognized.length > 0 || this.form.unrecognized.length > 0) && this.form.building.length > 0)) || (this.property_type === false && (this.form.recognized.length > 0 || this.form.unrecognized.length > 0))) {
					await this.handleResult()
				} else if (this.form.recognized.length === 0 && this.form.unrecognized.length === 0) {
					this.$toast.open({
						message: 'Vui lòng nhập thông tin đất cần ước tính giá',
						type: 'error',
						position: 'top-right'
					})
				} else if (this.property_type && this.form.building.length === 0) {
					this.$toast.open({
						message: 'Vui lòng nhập thông tin công trình xây dựng cần ước tính giá',
						type: 'error',
						position: 'top-right'
					})
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
		formatFloat (value) {
			let num = (value / 1).toFixed(2).replace(',', '.')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
		},
		arrayRecognized () {
			let total_area_recognized = 0
			this.form.recognized.forEach(recognized => {
				if (recognized.area !== '' && recognized.area !== undefined && recognized.area !== null) {
					total_area_recognized = total_area_recognized + parseFloat(recognized.area)
				}
			})
			return total_area_recognized
		},
		arrayUnrecognized () {
			let total_area_unrecognized = 0
			this.form.unrecognized.forEach(unrecognized => {
				if (unrecognized.area !== '' && unrecognized.area !== undefined && unrecognized.area !== null) {
					total_area_unrecognized = total_area_unrecognized + parseFloat(unrecognized.area)
				}
			})
			return total_area_unrecognized
		},
		async changeAreRecognized (event, index) {
			await this.getRecognizedArea(event, index)
			this.form.total_area = this.arrayRecognized() + this.arrayUnrecognized()
		},
		getRecognizedArea (event, index) {
			for (let i = 0; i < this.form.recognized.length; i++) {
				if (i === index) {
					this.form.recognized[i].area = parseFloat(+event).toFixed(2)
					if (this.form.recognized[i].area > 0) {
						this.checkRecognized[i].error = false
					} else {
						this.checkRecognized[i].error = true
					}
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
					if (this.form.building[i].area > 0) {
						this.checkBuilding[i].error = false
					} else {
						this.checkBuilding[i].error = true
					}
				}
			}
		},
		async changeAreUnrecognized (event, index) {
			await this.getUnrecognizedArea(event, index)
			this.form.total_area = this.arrayRecognized() + this.arrayUnrecognized()
		},
		changeRecognized (index, array) {
			this.landType.forEach(land => {
				if (land.id === this.form.recognized[index].land_type_purpose) {
					this.form.recognized[index].land_type_purpose_name = land.description
				}
			})
			this.duplicateRecognized = this.checkDuplicateRecognized(array)
		},
		async changeUnrecognized (index, array) {
			this.landType.forEach(land => {
				if (land.id === this.form.unrecognized[index].land_type_purpose) {
					this.form.unrecognized[index].land_type_purpose_name = land.description
				}
			})
			let isDuplicate = this.checkDuplicateUnrecognized(array)
			this.duplicateUnrecognized = !!isDuplicate
		},
		checkDuplicate (index) {
			this.landUnrecognized.find(landUnrecognize => landUnrecognize.id === this.form.unrecognized[index].land_type_purpose).disabled = true
		},
		getUnrecognizedArea (event, index) {
			for (let i = 0; i < this.form.unrecognized.length; i++) {
				if (i === index) {
					this.form.unrecognized[i].area = parseFloat(+event).toFixed(2)
					if (this.form.unrecognized[i].area > 0) {
						this.checkUnrecognized[i].error = false
					} else {
						this.checkUnrecognized[i].error = true
					}
				}
			}
		},
		async handleResult () {
			this.result_estimate = false
			await this.PostPriceEstimate()
			this.result_estimate = true
			this.$emit('result', this.result)
		},
		checkDuplicateBuilding (array) {
			this.duplicateBuilding = false
			const valueArr = array.map((item) => { return item.building_category })
			return valueArr.some((item, idx) => {
				return valueArr.indexOf(item) !== idx
			})
		},
		handleAddBuilding () {
			this.form.building.push({
				building_category: '',
				remaining_quality: '',
				area: ''
			})
			this.checkBuilding.push(
				{
					error: false
				}
			)
			if (this.form.building.length > 0) {
				this.item_print.asset_type = 'Đất có nhà'
			} else {
				this.item_print.asset_type = 'Đất'
			}
		},
		editEstimate (data, index) {
			this.isEditTangible = true
			this.openModalEstimate = true
			this.tangible = data
			this.tangible_index = index
		},
		handleDeleteBuilding (index) {
			this.form.building.splice(index, 1)
			this.checkBuilding.splice(index, 1)
		},
		checkDuplicateRecognized (array) {
			this.duplicateRecognized = false
			const valueArr = array.map((item) => { return item.land_type_purpose })
			return valueArr.some((item, idx) => {
				return valueArr.indexOf(item) !== idx
			})
		},
		handleAddRecognized (array) {
			let isDuplicate = this.checkDuplicateRecognized(array)
			if (!isDuplicate) {
				this.duplicateRecognized = false
				this.form.recognized.push({
					land_type_purpose: '',
					land_type_purpose_name: '',
					area: ''
				})
				this.checkRecognized.push({
					error: false
				})
			} else {
				this.duplicateRecognized = true
			}
		},
		handleDeleteRecognized (index, array) {
			this.form.recognized.splice(index, 1)
			this.checkRecognized.splice(index, 1)
			this.changeAreRecognized()
			this.duplicateRecognized = this.checkDuplicateRecognized(array)
		},
		checkDuplicateUnrecognized (array) {
			this.duplicateUnrecognized = false
			const valueArr = array.map((item) => { return item.land_type_purpose })
			return valueArr.some((item, idx) => {
				return valueArr.indexOf(item) !== idx
			})
		},
		handleAddUnrecognized (array) {
			let isDuplicate = this.checkDuplicateUnrecognized(array)
			if (!isDuplicate) {
				this.duplicateUnrecognized = false
				this.form.unrecognized.push({
					land_type_purpose: '',
					land_type_purpose_name: '',
					area: ''
				})
				this.checkUnrecognized.push(
					{
						error: false
					}
				)
			} else {
				this.duplicateUnrecognized = true
			}
		},
		handleDeleteUnrecognized (index, array) {
			this.form.unrecognized.splice(index, 1)
			this.checkUnrecognized.splice(index, 1)
			this.changeAreRecognized()
			let isDuplicate = this.checkDuplicateUnrecognized(array)
			this.duplicateUnrecognized = !!isDuplicate
		},
		async getProvinces () {
			try {
				const resp = await WareHouse.getProvince()
				this.provinces = [...resp.data]
				await this.getDistrictsByProvinceId(this.form.province_id)
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},

		async getDistrictsByProvinceId (id) {
			try {
				const resp = await WareHouse.getDistrictsByProvinceId(id)
				this.districts = [...resp.data]
				if (this.form.district_id !== '') {
					await this.getWardsByDistrictId(this.form.district_id)
					await this.getStreetByDistrictId(this.form.district_id)
				}
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},
		async getWardsByDistrictId (id) {
			try {
				const resp = await WareHouse.getWard(id)
				this.wards = [...resp.data]
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},
		async getStreetByDistrictId (id) {
			try {
				const resp = await WareHouse.getStreet(id)
				this.streets = [...resp.data]
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},
		async PostPriceEstimate () {
			const front_side = this.form.front_side
			const main_road_length = this.form.main_road_length
			const province_id = this.address.province_id
			const province = this.address.province
			const district_id = this.address.district_id
			const district = this.address.district
			const ward_id = this.address.ward_id
			const ward = this.address.ward
			const street_id = this.address.street_id
			const street = this.address.street
			const location = this.coordinates[0] + ',' + this.coordinates[1]
			const distance = parseFloat(this.distance / 1000).toFixed(2)
			const recognized = this.form.recognized
			const unrecognized = this.form.unrecognized
			const building = this.form.building
			const estimate_type = this.address.estimate_type
			const transaction_type_ids = this.transaction_type_ids
			const doc_num = this.form.doc_num
			const plot_num = this.form.plot_num
			const user_request = this.request
			const note = this.form.note
			const resp = await PriceEstimate.PostPriceEstimate({province_id, province, district_id, district, ward_id, ward, street_id, street, location, distance, front_side, main_road_length, recognized, unrecognized, building, estimate_type, transaction_type_ids, doc_num, plot_num, user_request, note})
			if (resp.data) {
				this.result = resp.data
			} else if (resp.error) {
				this.$toast.open({
					message: resp.error.message,
					type: 'error',
					position: 'top-right'
				})
			}
		},
		getDataLog (data) {
			this.form.unrecognized = data.unrecognized
			this.form.note = data.note
			this.form.recognized = data.recognized
			this.form.building = data.building
			this.request = data.user_request
			this.item_print.user_request = data.user_request
			data.unrecognized.forEach(() => {
				this.checkUnrecognized.push(
					{
						error: false
					}
				)
			})
			data.recognized.forEach(() => {
				this.checkRecognized.push(
					{
						error: false
					}
				)
			})
			data.building.forEach(() => {
				this.checkBuilding.push(
					{
						error: false
					}
				)
			})
			if (data.plot_num) {
				this.form.plot_num = data.plot_num
				this.item_print.plot_num = this.form.plot_num
			}
			if (data.doc_num) {
				this.form.doc_num = data.doc_num
				this.item_print.doc_num = this.form.doc_num
			}
			this.property_type = data.building.length > 0
			this.form.total_area = this.arrayRecognized() + this.arrayUnrecognized()
			this.form.front_side = data.front_side
			if (data.front_side === 0) {
				this.form.main_road_length = data.main_road_length
				this.alley = true
			}
		},
		getLandTypePurpose (landType) {
			landType.forEach(land => {
				this.landUnrecognized.push(
					{
						id: land.id,
						description: land.description,
						disabled: false
					}
				)
				this.landRecognized.push(
					{
						id: land.id,
						description: land.description,
						disabled: false
					}
				)
			})
		}
	},
	beforeMount () {
		this.getProvinces()
	}
}
</script>

<style lang="scss" scoped>
.container {
  &__estimate {
    max-width: 1710px;;
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
          padding: 1.3rem 0.5rem;
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
.front-side {
  margin-bottom: 0;
  margin-right: 10px;
  color: #333333;
  font-weight: 700;
}
.check-error{
  position: absolute;
  margin-bottom: 0;
  font-size: 12px;
  color: #cd201f;
  &__land {
    bottom: -16px;
    left: 72px;
  }
}
.note_class {
  position: absolute;
}
</style>
