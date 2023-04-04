<template>
  <div class="container__result">
    <div class="scroll-to-top" @click="onScrollToTop">
      <img src="../../../assets/icons/top-alignment.svg" alt="">
    </div>
    <div class="container container__detail">
      <h3 class="title">Kết quả ước tính giá</h3>
      <div class="d-flex justify-content-end pb-2">
        <button class="btn btn-orange" @click="handleCalculate">Cập nhật</button>
      </div>
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
            <td>
              Phần đất phù hợp quy hoạch
            </td>
            <td v-if="landTypeUnrecognized[index] !== undefined && landTypeUnrecognized[index] !== null">
              {{ landTypeUnrecognized[index].description }}
            </td>
            <td>
              {{ formatFloat(unrecognize.area) }}m<sup>2</sup>
            </td>
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
            <td>
              {{ format(unrecognize.estimate_price) }} đ
            </td>
          </tr>
          <tr v-if="recognized.length > 0" v-for="(recognize, index) in recognized" :key="'recognized'+index">
            <td>
              Phần đất vi phạm quy hoạch
            </td>
            <td>
              <div v-if="landTypeRecognized[index] !== undefined && landTypeRecognized[index] !== null">
                {{ landTypeRecognized[index].description }}
              </div>
            </td>
            <td>
              {{formatFloat(recognize.area)}}m<sup>2</sup>
            </td>
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
            <td>
              {{format(recognize.estimate_price)}} đ
            </td>
          </tr>
          <tr>
            <td>
              Tổng cộng:
            </td>
            <td>
            </td>
            <td>
              {{ formatFloat(total.area) }}m<sup>2</sup>
            </td>
            <td>
            </td>
            <td>
              {{ format(total.price) }} đ
            </td>
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
          <tr  v-for="(build, index) in building" :key="'building'+index">
            <td v-if="buildingTangible[index] !== undefined && buildingTangible[index] !== null">
              {{buildingTangible[index].description}}
            </td>
            <td>
              {{formatFloat(build.area)}}m<sup>2</sup>
            </td>
            <td>
              {{build.remaining_quality}} %
            </td>
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
            <td>
              {{format(build.estimate_price)}} đ
            </td>
          </tr>
          <tr v-if="building.length > 0">
            <td>
              Tổng cộng:
            </td>
            <td>
              {{ formatFloat(total_building.area)}}m<sup>2</sup>
            </td>
            <td>
            </td>
            <td>
            </td>
            <td>
              {{ format(total_building.price)}} đ
            </td>
          </tr>
          </tbody>
        </table>
      </div>
      <div class="text-danger" v-show="waringUpdate">
        *Đơn giá ước tính đã bị thay đổi. Bạn cần cập nhật để hệ thống tính lại tổng giá trị tài sản.
      </div>
      <div class="d-flex justify-content-between align-items-center w-100" v-if="total_price > 0">
        <div class="container__time">
          <p class="user">Tạo bởi: {{user}}</p>
          <p class="date">Ngày tạo: {{date}}</p>
        </div>

        <div class="container__total">
          <div class="total__estimate">
            <p class="title">Tổng giá trị tài sản</p>
            <p class="price">{{format(total_price)}} VND</p>
          </div>
          <div class="vertical__line" v-if="get_result.assets && get_result.assets.length > 0" />
          <div class="reliability" v-if="get_result.assets && get_result.assets.length > 0" :class="reliability === 1 ? '' : reliability === 2 ? 'reliability--orange' : 'reliability--red'">
            <button class="btn btn-reliability" :class="reliability === 1 ? '' : reliability === 2 ? 'btn-reliability--orange' : 'btn-reliability--red'" @click="handlePropertyEstimate"> Độ tin cậy
              {{reliability === 1 ? ' Cao' : reliability === 2 ? ' Trung bình' : ' Thấp'}}</button>
          </div>
        </div>
      </div>
      <div class="container__total justify-content-end" v-if="errormessage !== '' && errormessage !== undefined && errormessage !== null">
        <div class="total__estimate">
          <p class="price price--error" v-html="errormessage.replace('.', `. <br/>`)"></p>
        </div>
      </div>
      <ModalPropertyEstimate
        v-if="openModalPropertyEstimate"
        :assets="assets"
        :landTypePurpose="landTypePurpose"
        :location="location"
        @cancel="openModalPropertyEstimate = false"
      />
      <ModalNotificationEstimate
        v-if="alertChangePrice"
        :notification ="messageAlert"
        @cancel="handleCancelNotify"
        @action="handleCalculate"
      />
      <!-- <ModalUpdateEstimate
        v-if="showUpdateBuildingPrice"
        :unrecognized="unrecognized"
        :building="building"
        :buildingTangible="buildingTangible"
        @cancel="showUpdateBuildingPrice = false"
        /> -->
    </div>
  </div>
</template>

<script>
import InputCategory from '@/components/Form/InputCategory'
import InputText from '@/components/Form/InputText'
import InputNumberFormat from '@/components/Form/InputNumber'
import ModalPropertyEstimate from '@/components/Modal/ModalPropertyEstimate'
import PriceEstimate from '@/models/PriceEstimate'
import ModalNotificationEstimate from '@/components/Modal/ModalNotificationEstimate'
import ModalUpdateEstimate from '@/components/Modal/ModalUpdateEstimate'
export default {
	name: 'Result',
	props: ['get_result', 'landTypePurpose', 'buildingTypes', 'user', 'input', 'address', 'location', 'user_request'],
	data () {
		return {
			formReport: {
				id: '',
				report: {
					estimate_type: '',
					province: '',
					district: '',
					ward: '',
					street: '',
					location: '',
					front_side: '',
					main_road_length: null,
					land_no: '',
					doc_no: '',
					create_by: '',
					create_date: '',
					user_request: '',
					result: '',
					is_print: false,
					total_price: null,
					total_price_update: null,
					total_building_price_update: null,
					total_land_price_update: null,
					note: ''
				},
				report_detail: {
					land: [],
					building: [],
					assets: []
				}
			},
			print_item: {
				id: '',
				building: null,
				recognized: null,
				unrecognized: null,
				total_price: 0,
				total_area_building: 0,
				total_area_land: 0,
				total_land: 0,
				total_building: 0,
				landTypeRecognized: [],
				landTypeUnrecognized: [],
				buildingTangible: [],
				error_message: ''
			},
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
			dateNow: '',
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
			buildingTangible: [
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
			},
			waringUpdate: false,
			alertChangePrice: false,
			messageAlert: '"Bạn có muốn cập nhập giá trị mới không?"',
			temTime: null,
			showUpdateBuildingPrice: false
		}
	},
	created () {
		this.old_result = JSON.parse(JSON.stringify(this.get_result))
	},
	async mounted () {
		await this.getNow()
		await this.getDateNow()
		await this.handleResult()
		await this.findLandTypeRecognized()
		await this.findLandTypeUnrecognized()
		await this.findBuilding()
		await this.getLogEstimate()
		this.createEstimateLog()
	},
	components: {
		ModalPropertyEstimate,
		InputCategory,
		InputNumberFormat,
		InputText,
		ModalNotificationEstimate,
		ModalUpdateEstimate
	},
	computed: {
	},
	methods: {
		getPrint () {
			this.formReport.report.is_print = true
			this.createEstimateLog()
		},
		getLogEstimate () {
			this.formReport.id = this.old_result.id
			if (this.input.building.length > 0) {
				this.formReport.report.estimate_type = 'Đất có nhà'
			} else {
				this.formReport.report.estimate_type = 'Đất'
			}
			this.formReport.report.province = this.address.province
			this.formReport.report.district = this.address.district
			this.formReport.report.ward = this.address.ward
			this.formReport.report.street = this.address.street
			this.formReport.report.location = this.location[0] + ', ' + this.location[1]
			if (this.input.front_side === 1) {
				this.formReport.report.front_side = 'Mặt tiền'
			} else {
				this.formReport.report.front_side = 'Hẻm'
			}
			this.formReport.report.main_road_length = this.input.main_road_length
			this.formReport.report.doc_no = this.input.doc_num
			this.formReport.report.land_no = this.input.plot_num
			this.formReport.report.note = this.input.note
			this.formReport.report.create_by = this.user
			this.formReport.report.create_date = this.dateNow
			this.formReport.report.user_request = this.user_request
			this.formReport.report.total_building_price_update = this.total_building.price
			this.formReport.report.total_land_price_update = this.total.price
			if (this.old_result.status === 0) {
				this.formReport.report.result = 'Không có kết quả'
				this.formReport.report.total_price = null
			} else if (this.old_result.status === 1) {
				this.formReport.report.result = 'Thành công'
				this.formReport.report.total_price = this.old_result.result.total_price
			}
			if (this.input.unrecognized.length > 0 || this.input.recognized.length > 0) {
				this.getLand()
			}
			if (this.input.building.length > 0) {
				this.getBuilding()
			}
			this.getAssets()
		},
		getLand () {
			let landArray = []
			if (this.old_result.unrecognized && this.old_result.unrecognized.length > 0) {
				this.old_result.unrecognized.forEach(unrecognized => {
					const landName = this.landTypePurpose.find(landType => unrecognized.land_type_purpose === landType.id)
					landArray.push(
						{
							land_type_purpose_name: landName.description,
							area: unrecognized.area,
							type: 'Đất phù hợp quy hoạch',
							estimate_price: unrecognized.estimate_price,
							average_unit_price: unrecognized.average_land_unit_price,
							estimate_price_update: null,
							average_unit_price_update: null
						}
					)
				})
			}
			if (this.old_result.recognized && this.old_result.recognized.length > 0) {
				this.old_result.recognized.forEach(recognized => {
					const landName = this.landTypePurpose.find(landType => recognized.land_type_purpose === landType.id)
					landArray.push(
						{
							land_type_purpose_name: landName.description,
							area: recognized.area,
							type: 'Đất vi phạm quy hoạch',
							estimate_price: recognized.estimate_price,
							average_unit_price: recognized.average_land_unit_price,
							estimate_price_update: null,
							average_unit_price_update: null
						}
					)
				})
			}
			this.formReport.report_detail.land = landArray
		},
		getBuilding () {
			let buildingArray = []
			if (this.old_result.building && this.old_result.building.length > 0) {
				this.old_result.building.forEach(building => {
					const buildingName = this.buildingTypes.find(buildingType => building.building_category === buildingType.id)
					buildingArray.push(
						{
							building_category: buildingName.description,
							remaining_quality: building.remaining_quality,
							area: building.area,
							estimate_price: building.estimate_price,
							average_unit_price: building.average_building_unit_price,
							estimate_price_update: null,
							average_unit_price_update: null
						}
					)
				})
				this.formReport.report_detail.building = buildingArray
			}
		},
		getAssets () {
			let assetArray = []
			let reliability = ''
			if (this.old_result.status === 1 && this.old_result.assets.length > 0) {
				if (this.old_result.reliability === 1) {
					reliability = 'Độ tin cậy cao'
				} else if (this.old_result.reliability === 2) {
					reliability = 'Độ tin cậy trung bình'
				} else if (this.old_result.reliability === 3) {
					reliability = 'Độ tin cậy thấp'
				}
				this.old_result.assets.forEach(asset => {
					assetArray.push(
						{
							id: asset.id,
							reliability: reliability,
							version: asset.version
						}
					)
				})
				this.formReport.report_detail.assets = assetArray
			} else {
				this.formReport.report_detail.assets = []
			}
		},
		getNow () {
			const today = new Date()
			const date = `${today.getHours() < 10 ? '0' + today.getHours() : today.getHours()}` + ':' + `${today.getMinutes() < 10 ? '0' + today.getMinutes() : today.getMinutes()}` + ' ' + `${today.getDate() < 10 ? '0' + today.getDate() : today.getDate()}` + '/' + `${(today.getMonth() + 1) < 10 ? '0' + (today.getMonth() + 1) : (today.getMonth() + 1)}` + '/' + today.getFullYear()
			this.date = date
		},
		getDateNow () {
			const today = new Date()
			const date = `${today.getDate() < 10 ? '0' + today.getDate() : today.getDate()}` + '-' + `${(today.getMonth() + 1) < 10 ? '0' + (today.getMonth() + 1) : (today.getMonth() + 1)}` + '-' + today.getFullYear()
			this.dateNow = date
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
					this.print_item.recognized = this.get_result.recognized
				}
				if (this.get_result.unrecognized !== undefined && this.get_result.unrecognized !== null) {
					this.unrecognized = this.get_result.unrecognized
					this.print_item.unrecognized = this.get_result.unrecognized
					this.reliability = this.get_result.reliability
					this.assets = this.get_result.assets
				}
				if (this.get_result.building !== undefined && this.get_result.building !== null) {
					this.building = this.get_result.building
					this.print_item.building = this.get_result.building
				}
				if (this.get_result.result.total_price !== undefined && this.get_result.result.total_price !== null) {
					this.total_price = this.get_result.result.total_price
					this.print_item.total_price = this.get_result.result.total_price
				} else {
					this.total_price = 0
					this.print_item.total_price = 0
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
						total_area_recognized = total_area_recognized + parseFloat(recognize.area)
					}
					total_price_recognized = total_price_recognized + parseInt(recognize.estimate_price)
				})
			}
			if (this.unrecognized !== undefined && this.unrecognized !== null) {
				this.unrecognized.forEach(unrecognized => {
					if (unrecognized.area !== '' && unrecognized.area !== undefined && unrecognized.area !== null) {
						total_area_unrecognized = total_area_unrecognized + parseFloat(unrecognized.area)
					}
					total_price_unrecognized = total_price_unrecognized + parseInt(unrecognized.estimate_price)
				})
			}
			if (this.building !== undefined && this.building !== null) {
				this.building.forEach(building => {
					if (building.area !== '' && building.area !== undefined && building.area !== null) {
						total_area_building = total_area_building + parseFloat(building.area)
					}
					total_price_building = total_price_building + parseInt(building.estimate_price)
				})
			}
			this.total_building.area = total_area_building
			this.total_building.price = total_price_building
			this.total.area = total_area_recognized + total_area_unrecognized
			this.total.price = total_price_recognized + total_price_unrecognized
			this.print_item.total_area_building = total_area_building
			this.print_item.total_area_land = total_area_recognized + total_area_unrecognized
			this.print_item.total_land = total_price_recognized + total_price_unrecognized
			this.print_item.total_building = total_price_building
			this.print_item.error_message = this.get_result.result.error_message
			this.print_item.id = this.get_result.id
			this.$emit('get_item', this.print_item)
		},
		findLandTypeRecognized () {
			this.landTypeRecognized = []
			this.recognized.forEach(recognized => {
				this.landTypePurpose.forEach(landType => {
					if (recognized.land_type_purpose === landType.id) {
						this.landTypeRecognized.push({
							description: landType.acronym
						})
					}
				})
			})
			this.print_item.landTypeRecognized = this.landTypeRecognized
			this.$emit('get_item', this.print_item)
		},
		findLandTypeUnrecognized () {
			this.landTypeUnrecognized = []
			this.unrecognized.forEach(unrecognized => {
				this.landTypePurpose.forEach(landType => {
					if (unrecognized.land_type_purpose === landType.id) {
						this.landTypeUnrecognized.push({
							description: landType.acronym
						})
					}
				})
			})
			this.print_item.landTypeUnrecognized = this.landTypeUnrecognized
			this.$emit('get_item', this.print_item)
		},
		findBuilding () {
			this.buildingTangible = []
			this.building.forEach(build => {
				this.buildingTypes.forEach(buildingType => {
					if (build.building_category === buildingType.id) {
						this.buildingTangible.push({
							description: buildingType.description
						})
					}
				})
			})
			this.print_item.buildingTangible = this.buildingTangible
			this.$emit('get_item', this.print_item)
		},
		afterActionChange () {
			this.alertChangePrice = true
			this.waringUpdate = true
			this.$emit('warning', this.waringUpdate)
		},
		changeUnitPrice (event, index) {
			for (let i = 0; i < this.recognized.length; i++) {
				if (i === index) {
					this.recognized[i].average_land_unit_price = +event
					this.recognized[i].estimate_price = +event * this.recognized[i].area
				}
			}
			// this.waringUpdate = true
			// this.$emit('warning', this.waringUpdate)
			// this.totalPriceLand()
			if (this.temTime) { clearTimeout(this.temTime) }
			this.temTime = setTimeout(this.afterActionChange, 1500)
		},
		changeUnitPriceUnrecognized (event, index) {
			for (let i = 0; i < this.unrecognized.length; i++) {
				if (i === index) {
					this.unrecognized[i].average_land_unit_price = +event
					this.unrecognized[i].estimate_price = +event * this.unrecognized[i].area
				}
			}
			// this.totalPriceLand()
			// this.waringUpdate = true
			// this.$emit('warning', this.waringUpdate)
			if (this.temTime) { clearTimeout(this.temTime) }
			this.temTime = setTimeout(this.afterActionChange, 1500)
		},

		changeUnitPriceBuilding (event, index) {
			for (let i = 0; i < this.building.length; i++) {
				if (i === index) {
					this.building[i].average_building_unit_price = +event
					this.building[i].estimate_price = +event * this.building[i].area * (this.building[i].remaining_quality / 100)
				}
			}
			this.formReport.report.is_update_building_value = true
			// this.totalPriceBuilding()
			// this.waringUpdate = true
			// this.$emit('warning', this.waringUpdate)
			if (this.temTime) { clearTimeout(this.temTime) }
			this.temTime = setTimeout(this.afterActionChange, 1500)
		},
		handleCancelNotify () {
			this.alertChangePrice = false
		},
		totalPriceLand () {
			let total_price_recognized = 0
			let total_price_unrecognized = 0
			if (this.recognized !== undefined && this.recognized !== null) {
				this.recognized.forEach(recognize => {
					total_price_recognized = total_price_recognized + recognize.estimate_price
				})
			}
			if (this.unrecognized !== undefined && this.unrecognized !== null) {
				this.unrecognized.forEach(unrecognize => {
					total_price_unrecognized = total_price_unrecognized + unrecognize.estimate_price
				})
			}
			this.total.price = total_price_recognized + total_price_unrecognized
			this.print_item.total_land = total_price_recognized + total_price_unrecognized
			// this.totalUnitPrice()
		},
		totalPriceBuilding () {
			let total_price_building = 0
			if (this.building !== undefined && this.building !== null) {
				this.building.forEach(building => {
					total_price_building = total_price_building + building.estimate_price
				})
			}
			this.total_building.price = total_price_building
			this.print_item.total_building = total_price_building
		},
		totalUnitPrice () {
			this.total_price = this.total_building.price + this.total.price
			this.formReport.report.total_price_update = this.total_building.price + this.total.price
			this.print_item.total_price = this.total_price
			this.print_item.error_message = ''
		},
		handleDialogUpdatePrice () {

		},
		async handleCalculate () {
			// this.showUpdateBuildingPrice = true
			this.alertChangePrice = false
			this.waringUpdate = false
			this.$emit('warning', this.waringUpdate)
			this.errormessage = ''
			await this.totalPriceLand()
			await this.totalPriceBuilding()
			await this.totalUnitPrice()
			await this.getNewResult()
			await this.getLandUpdate()
			await this.getBuildingUpdate()
			if (this.old_result.result.total_price !== this.total_price) {
				await this.createEstimateLog()
			}
			this.$emit('get_item', this.print_item)
		},
		getLandUpdate () {
			let landArray = []
			if (this.old_result.unrecognized && this.old_result.unrecognized.length > 0) {
				this.old_result.unrecognized.forEach((unrecognized, index) => {
					const landName = this.landTypePurpose.find(landType => unrecognized.land_type_purpose === landType.id)
					landArray.push(
						{
							land_type_purpose_name: landName.description,
							area: unrecognized.area,
							type: 'Đất phù hợp quy hoạch',
							estimate_price: unrecognized.estimate_price,
							average_unit_price: unrecognized.average_land_unit_price,
							estimate_price_update: this.unrecognized[index].estimate_price,
							average_unit_price_update: this.unrecognized[index].average_land_unit_price
						}
					)
				})
			}
			if (this.old_result.recognized && this.old_result.recognized.length > 0) {
				this.old_result.recognized.forEach((recognized, index) => {
					const landName = this.landTypePurpose.find(landType => recognized.land_type_purpose === landType.id)
					landArray.push(
						{
							land_type_purpose_name: landName.description,
							area: recognized.area,
							type: 'Đất vi phạm quy hoạch',
							estimate_price: recognized.estimate_price,
							average_unit_price: recognized.average_land_unit_price,
							estimate_price_update: this.recognized[index].estimate_price,
							average_unit_price_update: this.recognized[index].average_land_unit_price
						}
					)
				})
			}
			this.formReport.report_detail.land = landArray
		},
		getBuildingUpdate () {
			let buildingArray = []
			if (this.old_result.building && this.old_result.building.length > 0) {
				this.old_result.building.forEach((building, index) => {
					const buildingName = this.buildingTypes.find(buildingType => building.building_category === buildingType.id)
					buildingArray.push(
						{
							building_category: buildingName.description,
							remaining_quality: building.remaining_quality,
							area: building.area,
							estimate_price: building.estimate_price,
							average_unit_price: building.average_building_unit_price,
							estimate_price_update: this.building[index].estimate_price,
							average_unit_price_update: this.building[index].average_building_unit_price
						}
					)
				})
				this.formReport.report_detail.building = buildingArray
			}
		},
		getNewResult () {
			// result
			this.new_result.assets = this.get_result.assets
			this.new_result.error_message = this.get_result.error_message
			this.new_result.unrecognized = this.unrecognized
			this.new_result.reliability = this.get_result.reliability
			this.new_result.recognized = this.recognized
			this.new_result.building = this.building
			this.new_result.status = this.get_result.status
			this.new_result.result.total_price = this.total_price
			this.new_result.result.error_message = this.get_result.result.error_message
			this.new_result.result.status = this.get_result.result.status
			// print
			this.print_item.recognized = this.recognized
			this.print_item.building = this.building
			this.print_item.unrecognized = this.unrecognized
		},
		async createEstimateLog () {
			this.formReport.report.total_building_price_update = this.total_building.price
			this.formReport.report.total_land_price_update = this.total.price
			const input = this.formReport
			await PriceEstimate.LogPriceEstimate({input})
		},
		format (value) {
			let num = (value / 1).toFixed(0).replace(',', '.')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
		},
		formatFloat (value) {
			let num = (value / 1).toFixed(2).replace(',', '.')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
		}
	}
	// beforeMount () {
	//   this.handleResult()
	// }
}
</script>

<style lang="scss" scoped>
.container {
  &__result {
    //min-height: calc(100vh - 68px);
    max-width: 1710px;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.25);
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
  &__detail{
    max-width: 1100px;
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
      &:last-child{
        background: rgba(242, 140, 28, 0.1);
        td {
          font-weight: 700;
        }
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
      &--error {
        margin-top: 10px;
        color: #EF3039;
        font-weight: 500;
        font-size: 23px;
      }
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
