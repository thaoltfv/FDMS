<template>
  <div>
    <ValidationObserver tag="form"
                        ref="observer"
                        @submit.prevent="validateBeforeSubmit">
      <div
        class="modal-detail d-flex justify-content-center align-items-center"
        @click.self="handleCancel">
        <div class="card">
          <div class="btn--contain">
            <div class="btn--cancel" @click="handleCancel">
              <img src="../../assets/icons/ic_cancel-1.svg" alt="cancel">
            </div>
          </div>
          <div class="content-detail">
          <div class="input--search">
              <InputText
                v-model="filter.search"
                label="VC bất kỳ"
                class="contain-input contain-input__any"
              />
            <div class="input-grid">
              <InputCategory
                v-model="filter.asset_type_id"
                vid="info"
                label="Loại tài sản"
                class="contain-input contain-input__info"
                :options="optionsType"
              />
              <InputCategory
                v-model="filter.created_by"
                vid="info"
                label="Người tạo"
                class="contain-input contain-input__info"
                :options="optionCreateBy"
              />
              <InputText
                v-model="filter.status"
                vid="info"
                label="Trạng thái"
                v-if="roles === false"
                class="contain-input contain-input__info"
                :disabled-input="true"
              />
              <InputCategory
                v-model="filter.status"
                vid="info"
                label="Trạng thái"
                v-if="roles"
                class="contain-input contain-input__info"
                :options="opstionStatus"
              />
              <InputCategory
                v-model="filter.province_id"
                vid="province_id"
                label="Tỉnh/thành"
                class="contain-input contain-input__info"
                @change="changeProvince"
                :options="optionsProvince"
              />
              <InputCategory
                v-model="filter.district_id"
                vid="district_id"
                label="Quận/huyện"
                class="contain-input contain-input__info"
                @change="changeDistrict"
                :options="optionsDistrict"
              />
              <InputCategory
                v-model="filter.ward_id"
                vid="ward_id"
                label="Phường/xã"
                class="contain-input contain-input__info"
                :options="optionsWard"
              />
            </div>
            <div class="input-grid">
              <InputCategory
                v-model="filter.street_id"
                vid="street_id"
                label="Đường"
                class="contain-input contain-input__info"
                :options="optionsStreet"
              />
            </div>
          </div>
          <div class="search--advanced">
            <button class="btn btn--more" type="button" @click="showSearch = !showSearch"> Tìm kiếm nâng cao <a-icon class="icon" :class="showSearch? '': 'icon__hide'" type="up"/></button>
            <div class="search--advanced__show" v-if="showSearch">
              <div class="input-grid">
                <InputText
                  v-model="filter.id"
                  vid="province_id"
                  label="Mã tin đăng"
                  class="contain-input contain-input__info"
                />
                <InputText
                  v-model="filter.coordinates"
                  vid="province_id"
                  label="Tọa độ"
                  class="contain-input contain-input__info"
                />
                <InputCategory
                  v-model="filter.source_id"
                  vid="info"
                  label="Nguồn tin"
                  class="contain-input contain-input__info"
                  :options="optionsInfo"
                />
                <InputText
                  v-model="filter.created_at"
                  vid="province_id"
                  label="Ngày tạo"
                  type="date"
                  class="contain-input contain-input__info"
                />
                <InputText
                  v-model="public_date_from"
                  vid="public_date_from"
                  type="date"
                  label="Ngày đăng - từ ngày"
                  @change="changeDateStart(public_date_from)"
                  class="contain-input contain-input__info"
                />
                <InputText
                  v-model="public_date_to"
                  vid="public_date_to"
                  type="date"
                  label="đến ngày"
                  @change="changeDateEnd(public_date_to)"
                  class="contain-input contain-input__info"
                />
              <InputText
                v-model="filter.land_no"
                vid="province_id"
                label="Thửa đất số"
                class="contain-input contain-input__info"
              />
              <InputText
                v-model="filter.doc_no"
                vid="province_id"
                label="Bản đồ số"
                class="contain-input contain-input__info"
              />
			  <InputCategory
                  v-model="filter.transaction_type_id"
                  vid="info"
                  label="Loại giao dịch"
                  class="contain-input contain-input__info"
                  :options="optionsTransactionType"
                />
              </div>
                <div class="row row__total">
                  <div class="col-md-6 col-lg-3">
                    <h3 class="total-title">Tổng diện tích đất</h3>
                    <p>Chọn mức giá</p>
                    <div class="price-range range-slider d-flex align-items-center">
<!--                      <input type="number" class="input-price">-->
<!--                      <div class="line">-</div>-->
<!--                      <input type="number" class="input-price">-->
                      <input type="range" min="0" max="1000000" step="10" v-model="total_area_from">
<!--                      <input class="input-price" type="number" min="0" max="100000000000" step="1" v-model="total_area_from">-->
                      <InputNumberFormat
                        label=""
                        v-model="total_area_from"
                        vid="total_area_from"
                        @change="totalAreaFrom($event)"
                        :formatter="valueFormat => `${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                        :min="0"
                        :max="1000000"
                        :step="10"
                      />
                      <div class="line">-</div>
                      <input type="range" min="0" max="1000000" step="10" v-model="total_area_to">
<!--                      <input class="input-price" type="number" min="0" max="100000000000" step="1" v-model="total_area_to">-->
                      <InputNumberFormat
                        label=""
                        v-model="total_area_to"
                        vid="total_area_to"
                        @change="totalAreaTo($event)"
                        :formatter="valueFormat => `${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                        :min="0"
                        :max="1000000"
                        :step="10"
                      />
                    </div>
                  </div>
                  <div class="col-md-6 col-lg-3">
                    <h3 class="total-title">Tổng gíá trị xây dựng</h3>
                    <p>Chọn mức giá</p>
                    <div class="price-range range-slider d-flex align-items-center">
                      <input type="range" min="0" max="100000000000" step="10000000" v-model="total_construction_area_from">
<!--                      <input class="input-price" type="number" min="0" max="100000000000" step="1" v-model="total_construction_area_from">-->
                      <InputNumberFormat
                        label=""
                        v-model="total_construction_area_from"
                        vid="total_construction_area_from"
                        @change="totalConstructionAreaFrom($event)"
                        :formatter="valueFormat => `${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                        :min="0"
                        :max="100000000000"
                        :step="10000000"
                      />
                      <div class="line">-</div>
                      <input type="range" min="0" max="100000000000" step="10000000" v-model="total_construction_area_to">
<!--                      <input class="input-price" type="number" min="0" max="100000000000" step="1" v-model="total_construction_area_to">-->
                      <InputNumberFormat
                        label=""
                        v-model="total_construction_area_to"
                        vid="total_construction_area_to"
                        @change="totalConstructionAreaTo($event)"
                        :formatter="valueFormat => `${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                        :min="0"
                        :max="100000000000"
                        :step="10000000"
                      />
                    </div>
                  </div>
                  <div class="col-md-6 col-lg-3">
                    <h3 class="total-title">Tổng giá trị</h3>
                    <p>Chọn mức giá</p>
                    <div class="price-range range-slider d-flex align-items-center">
                      <input type="range" min="0" max="100000000000" step="10000000" v-model="total_amount_from ">
<!--                      <input class="input-price" type="number" min="0" max="100000000000" step="1" v-model="total_amount_from ">-->
                      <InputNumberFormat
                        label=""
                        v-model="total_amount_from"
                        vid="total_amount_from"
                        @change="totalAmountFrom($event)"
                        :min="0"
                        :max="100000000000"
                        :formatter="valueFormat => `${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                        :step="10000000"
                      />
                      <div class="line">-</div>
                      <input type="range" min="0" max="100000000000" step="10000000" v-model="total_amount_to">
<!--                      <input class="input-price" type="number" min="0" max="100000000000" step="1" v-model="total_amount_to">-->
                      <InputNumberFormat
                        label=""
                        v-model="total_amount_to"
                        vid="total_amount_to"
                        @change="totalAmountTo($event)"
                        :min="0"
                        :max="100000000000"
                        :formatter="valueFormat => `${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                        :step="10000000"
                      />
                    </div>
                  </div>
                  <div class="col-md-6 col-lg-3">
                    <h3 class="total-title">Đơn giá đất bình quân</h3>
                    <p>Chọn mức giá</p>
                    <div class="price-range range-slider d-flex align-items-center">
                      <input type="range" min="0" max="100000000000" step="10000000" v-model="average_land_unit_price_from">
<!--                      <input class="input-price" type="number" min="0" max="100000000000" step="1" v-model="average_land_unit_price_from">-->
                      <InputNumberFormat
                        label=""
                        v-model="average_land_unit_price_from"
                        vid="average_land_unit_price_from"
                        @change="averageLandUnitPriceFrom($event)"
                        :min="0"
                        :max="100000000000"
                        :formatter="valueFormat => `${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                        :step="10000000"
                      />
                      <div class="line">-</div>
                      <input type="range" min="0" max="100000000000" step="10000000" v-model="average_land_unit_price_to">
<!--                      <input class="input-price" type="number" min="0" max="100000000000" step="1" v-model=" average_land_unit_price_to">-->
                      <InputNumberFormat
                        label=""
                        v-model="average_land_unit_price_to"
                        vid="average_land_unit_price_to"
                        :min="0"
                        :max="100000000000"
                        :step="10000000"
                        :formatter="valueFormat => `${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                        @change="averageLandUnitPriceTo($event)"
                      />
                    </div>
                  </div>
                </div>
              <hr class="hr">
              <div class="input-grid input-grid__info">
                <InputText
                  v-model="filter.source_id"
                  vid="province_id"
                  label="Nguồn thông tin"
                  class="contain-input contain-input__info"
                />
                <InputText
                  v-model="filter.contact_person"
                  vid="province_id"
                  label="Người liên hệ"
                  class="contain-input contain-input__info"
                />
                <InputText
                  v-model="filter.contact_phone"
                  vid="province_id"
                  label="SĐT người liên hệ"
                  type="number"
                  :max-length="11"
                  class="contain-input contain-input__info"
                />
              </div>
            </div>
            </div>
          </div>
          <div>
            <button class="btn btn--search" @click.prevent="search(filter)">Tìm kiếm</button>
          </div>
        </div>
      </div>
    </ValidationObserver>
  </div>
</template>

<script>
import InputText from '@/components/Form/InputText'
import InputNumberFormat from '@/components/Form/InputNumber'
import InputCategory from '@/components/Form/InputCategory'
import InputDatePickerRange from '@/components/Form/InputDatePickerRange'
import WareHouse from '@/models/WareHouse'
import moment from 'moment'
export default {
	name: 'ModalSearchAdvanced',
	components: {
		InputCategory,
		InputNumberFormat,
		InputDatePickerRange,
		InputText
	},
	props: ['roles', 'filter_search'],
	data () {
		return {
			showSearch: false,
			propertyTypes: [],
			infoSources: [],
			transactionType: [],
			provinces: [],
			districts: [],
			wards: [],
			streets: [],
			users: [],
			status: [
				{
					id: 1,
					name: 'Đang kích hoạt'
				},
				{
					id: 2,
					name: 'Đang vô hiệu hóa'
				},
				{
					id: 3,
					name: 'Bản nháp'
				}
			],
			public_date_from: '',
			public_date_to: '',
			filter: {
				status: '',
				search: '',
				asset_type_id: '',
				created_at: '',
				created_by: '',
				coordinates: '',
				source_id: '',
				transaction_type_id: '',
				id: '',
				province_id: '',
				district_id: '',
				ward_id: '',
				street_id: '',
				land_no: '',
				doc_no: '',
				contact_person: '',
				contact_phone: '',
				public_date_from: '',
				total_area_from: 0,
				total_area_to: 0,
				total_construction_area_from: 0,
				total_construction_area_to: 0,
				total_amount_from: 0,
				total_amount_to: 0,
				average_land_unit_price_from: 0,
				average_land_unit_price_to: 0,
				public_date_to: ''
			}
		}
	},
	mounted () {
		if (this.filter_search !== undefined && this.filter_search !== null) {
			this.filter.search = this.filter_search.search
			this.filter.transaction_type_id = this.filter_search.transaction_type_id
			this.filter.created_by = this.filter_search.created_by
			this.filter.status = this.filter_search.status
			this.filter.asset_type_id = this.filter_search.asset_type_id
			this.filter.province_id = this.filter_search.province_id
			this.filter.district_id = this.filter_search.district_id
			this.filter.ward_id = this.filter_search.ward_id
			this.filter.street_id = this.filter_search.street_id
			this.filter.coordinates = this.filter_search.coordinates
			this.filter.id = this.filter_search.id
			this.filter.source_id = this.filter_search.source_id
			this.filter.created_at = this.filter_search.created_at
			this.filter.land_no = this.filter_search.land_no
			this.filter.doc_no = this.filter_search.doc_no
			this.filter.contact_person = this.filter_search.contact_person
			this.filter.contact_phone = this.filter_search.contact_phone
			if (this.filter_search.total_area_from !== undefined && this.filter_search.total_area_from !== null) {
				this.total_area_from = this.filter_search.total_area_from
			} else {
				this.total_area_from = 0
			}
			if (this.filter_search.total_area_to !== undefined && this.filter_search.total_area_to !== null) {
				this.total_area_to = this.filter_search.total_area_to
			} else {
				this.total_area_to = 0
			}
			if (this.filter_search.total_construction_area_from !== undefined && this.filter_search.total_construction_area_from !== null) {
				this.total_construction_area_from = this.filter_search.total_construction_area_from
			} else {
				this.total_construction_area_from = 0
			}
			if (this.filter_search.total_construction_area_to !== undefined && this.filter_search.total_construction_area_to !== null) {
				this.total_construction_area_to = this.filter_search.total_construction_area_to
			} else {
				this.total_construction_area_to = 0
			}
			if (this.filter_search.total_amount_from !== undefined && this.filter_search.total_amount_from !== null) {
				this.total_amount_from = this.filter_search.total_amount_from
			} else {
				this.total_amount_from = 0
			}
			if (this.filter_search.total_amount_to !== undefined && this.filter_search.total_amount_to !== null) {
				this.total_amount_to = this.filter_search.total_amount_to
			} else {
				this.total_amount_to = 0
			}
			if (this.filter_search.average_land_unit_price_from !== undefined && this.filter_search.average_land_unit_price_from !== null) {
				this.average_land_unit_price_from = this.filter_search.average_land_unit_price_from
			} else {
				this.average_land_unit_price_from = 0
			}
			if (this.filter_search.average_land_unit_price_to !== undefined && this.filter_search.average_land_unit_price_to !== null) {
				this.average_land_unit_price_to = this.filter_search.average_land_unit_price_to
			} else {
				this.average_land_unit_price_to = 0
			}
			if (this.filter_search.public_date_from !== '' && this.filter_search.public_date_from !== undefined && this.filter_search.public_date_from !== null) {
				this.public_date_from = moment(String(this.filter_search.public_date_from)).format('YYYY-DD-MM')
				this.filter.public_date_from = moment(String(this.filter_search.public_date_from)).format('DD-MM-YYYY')
			}
			if (this.filter_search.public_date_to !== '' && this.filter_search.public_date_to !== undefined && this.filter_search.public_date_to !== null) {
				this.public_date_to = moment(String(this.filter_search.public_date_to)).format('YYYY-DD-MM')
				this.filter.public_date_to = moment(String(this.filter_search.public_date_to)).format('DD-MM-YYYY')
			}
		}
	},
	computed: {
		total_area_from: {
			get: function () {
				return parseInt(this.filter.total_area_from)
			},
			set: function (val) {
				val = parseInt(val)
				if (val > this.filter.total_area_to) {
					this.filter.total_area_to = val
				}
				this.filter.total_area_from = val
			}
		},
		total_area_to: {
			get: function () {
				return parseInt(this.filter.total_area_to)
			},
			set: function (val) {
				val = parseInt(val)
				if (val < this.filter.total_area_from) {
					this.filter.total_area_from = val
				}
				this.filter.total_area_to = val
			}
		},
		total_construction_area_from: {
			get: function () {
				return parseInt(this.filter.total_construction_area_from)
			},
			set: function (val) {
				val = parseInt(val)
				if (val > this.filter.total_construction_area_to) {
					this.filter.total_construction_area_to = val
				}
				this.filter.total_construction_area_from = val
			}
		},
		total_construction_area_to: {
			get: function () {
				return parseInt(this.filter.total_construction_area_to)
			},
			set: function (val) {
				val = parseInt(val)
				if (val < this.filter.total_construction_area_from) {
					this.filter.total_construction_area_from = val
				}
				this.filter.total_construction_area_to = val
			}
		},
		total_amount_from: {
			get: function () {
				return parseInt(this.filter.total_amount_from)
			},
			set: function (val) {
				val = parseInt(val)
				if (val > this.filter.total_amount_to) {
					this.filter.total_amount_to = val
				}
				this.filter.total_amount_from = val
			}
		},
		total_amount_to: {
			get: function () {
				return parseInt(this.filter.total_amount_to)
			},
			set: function (val) {
				val = parseInt(val)
				if (val < this.filter.total_amount_from) {
					this.filter.total_amount_from = val
				}
				this.filter.total_amount_to = val
			}
		},
		average_land_unit_price_from: {
			get: function () {
				return parseInt(this.filter.average_land_unit_price_from)
			},
			set: function (val) {
				val = parseInt(val)
				if (val > this.filter.average_land_unit_price_to) {
					this.filter.average_land_unit_price_to = val
				}
				this.filter.average_land_unit_price_from = val
			}
		},
		average_land_unit_price_to: {
			get: function () {
				return parseInt(this.filter.average_land_unit_price_to)
			},
			set: function (val) {
				val = parseInt(val)
				if (val < this.filter.average_land_unit_price_from) {
					this.filter.average_land_unit_price_from = val
				}
				this.filter.average_land_unit_price_to = val
			}
		},
		optionCreateBy () {
			return {
				data: this.users,
				id: 'name',
				key: 'name'
			}
		},
		opstionStatus () {
			return {
				data: this.status,
				id: 'id',
				key: 'name'
			}
		},
		optionsType () {
			return {
				data: this.propertyTypes,
				id: 'id',
				key: 'description'
			}
		},
		optionsInfo () {
			return {
				data: this.infoSources,
				id: 'id',
				key: 'description'
			}
		},
		optionsTransactionType ()
		{
			return {
				data: this.transactionType,
				id: 'id',
				key: 'description'
			}
		},
		optionsProvince () {
			return {
				data: this.provinces,
				id: 'id',
				key: 'name'
			}
		},
		optionsDistrict () {
			return {
				data: this.districts,
				id: 'id',
				key: 'name'
			}
		},
		optionsWard () {
			return {
				data: this.wards,
				id: 'id',
				key: 'name'
			}
		},
		optionsStreet () {
			return {
				data: this.streets,
				id: 'id',
				key: 'name'
			}
		}
	},
	methods: {
		totalAreaFrom (event) {
			this.total_area_from = event
		},
		totalAreaTo (event) {
			this.total_area_to = event
		},
		totalConstructionAreaFrom (event) {
			this.total_construction_area_from = event
		},
		totalConstructionAreaTo (event) {
			this.total_construction_area_to = event
		},
		totalAmountFrom (event) {
			this.total_amount_from = event
		},
		totalAmountTo (event) {
			this.total_amount_to = event
		},
		averageLandUnitPriceFrom (event) {
			this.average_land_unit_price_from = event
		},
		averageLandUnitPriceTo (event) {
			this.average_land_unit_price_to = event
		},
		formatNumber (value) {
			let num = (value / 1).toFixed(0).replace('.', ',')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.')
		},
		search (filter) {
			console.log('vào hàm search')
			this.$emit('filter-changed', this.filter)
			this.$emit('cancel', event)
			this.$router.push({
				name: 'warehouse.index',
				query: {
					search: filter.search,
					asset_type_id: filter.asset_type_id,
					created_at: filter.created_at,
					created_by: filter.created_by,
					coordinates: filter.coordinates,
					source_id: filter.source_id,
					transaction_type_id: filter.transaction_type_id,
					id: filter.id,
					province_id: filter.province_id,
					district_id: filter.district_id,
					ward_id: filter.ward_id,
					street_id: filter.street_id,
					land_no: filter.land_no,
					doc_no: filter.doc_no,
					contact_person: filter.contact_person,
					contact_phone: filter.contact_phone,
					public_date_from: filter.public_date_from,
					public_date_to: filter.public_date_to,
					total_area_from: filter.total_area_from,
					total_area_to: filter.total_area_to,
					total_construction_area_from: filter.total_construction_area_from,
					total_construction_area_to: filter.total_construction_area_to,
					total_amount_from: filter.total_amount_from,
					total_amount_to: filter.total_amount_to,
					average_land_unit_price_from: filter.average_land_unit_price_from,
					average_land_unit_price_to: filter.average_land_unit_price_to
				}
			}).catch(_ => {})
			console.log('filter.transaction_type_id',filter.transaction_type_id)
		},
		handleCancel (event) {
			this.$emit('cancel', event)
		},
		async getPropertyType () {
			try {
				const resp = await WareHouse.getPropertyType()
				this.propertyTypes = [...resp.data]
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},
		async getInfoSource () {
			try {
				const resp = await WareHouse.getInfoSource()
				this.infoSources = [...resp.data]
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},
		async getTransactionType() {
			try {
				const resp = await WareHouse.getTransactionType()
				this.transactionType = [...resp.data]
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},
		async validateBeforeSubmit () {
			const isValid = await this.$refs.observer.validate()
			if (isValid) {
			}
		},
		changeDateStart (value) {
			this.filter.public_date_from = moment(String(value)).format('DD-MM-YYYY')
		},
		changeDateEnd (value) {
			this.filter.public_date_to = moment(String(value)).format('DD-MM-YYYY')
		},
		changeProvince (provinceId) {
			this.districts = []
			this.wards = []
			this.streets = []
			this.filter.district_id = ''
			this.filter.ward_id = ''
			this.filter.street_id = ''
			if (this.filter.province_id !== '' && this.filter.province_id !== undefined && this.filter.province_id !== null) {
				this.getDistrictsByProvinceId(+provinceId)
			}
		},
		changeDistrict (districtId) {
			this.wards = []
			this.streets = []
			this.filter.ward_id = ''
			this.filter.street_id = ''
			if (this.filter.district_id !== '' && this.filter.district_id !== undefined && this.filter.district_id !== null) {
				this.getWardsByDistrictId(+districtId)
				this.getStreetByDistrictId(+districtId)
			}
		},
		async getProvinces () {
			try {
				const resp = await WareHouse.getProvince()
				this.provinces = [...resp.data]
				if (this.filter.province_id !== '' && this.filter.province_id !== undefined && this.filter.province_id !== null) {
					await this.getDistrictsByProvinceId(this.filter.province_id)
				} else {
					this.districts = []
				}
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},

		async getDistrictsByProvinceId (id) {
			try {
				const resp = await WareHouse.getDistrict(id)
				this.districts = [...resp.data]
				if (this.filter.district_id !== '' && this.filter.district_id !== undefined && this.filter.district_id !== null) {
					await this.getWardsByDistrictId(this.filter.district_id)
					await this.getStreetByDistrictId(this.filter.district_id)
				} else {
					this.wards = []
					this.streets = []
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
		async getUser () {
			try {
				let arrayUser = []
				let users = []
				let getUsers = []
				const resp = await WareHouse.getUser()
				arrayUser = [...resp.data]
				arrayUser.forEach(user => {
					users.push(
						user.name
					)
				})
				getUsers = [...new Set(users)]
				getUsers.forEach(getUser => {
					this.users.push({
						name: getUser
					})
				})
				this.users.unshift(
					{
						name: 'Tất cả người tạo'
					}
				)
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},
		reset () {
			for (let property in this.filter) {
				if (this.filter.hasOwnProperty(property)) {
					this.filter[property] = ''
				}
			}
			this.$emit('filter-changed', this.filter)
		}
	},
	beforeMount () {
		this.getPropertyType()
		this.getProvinces()
		this.getInfoSource()
		this.getUser()
		this.getTransactionType()
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
  background: rgba(0,0,0,.6);
  @media (max-width: 787px) {
    padding: 20px;
  }
  .card {
    border-radius: 5px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
    max-width: 1100px;
    width: 100%;
    max-height: 90vh;
    margin-bottom: 0;
    padding: 20px 50px;
    @media (max-width: 787px) {
      padding: 20px 10px;
    }
    &-header {
      border-bottom: 1px solid #DDDDDD;
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
.content-detail{
  overflow-y: auto;
  overflow-x: hidden;
}
.btn {
  &--contain {
    display: flex;
    justify-content: flex-end;
    margin-bottom: 18px;
  }
  &--cancel{
    width: 13px;
    height: auto;
    cursor: pointer;
    img{
      width: 100%;
    }
  }
  &--more{
    padding: 9px;
    color: #FFFFFF;
    background: #FAA831;
    box-shadow: 0 1px 4px rgba(250, 168, 49, 0.25);
    border-radius: 5px;
    margin-top: 26px;
    font-weight: 600;
    font-size: 12px;
  }
  &--search {
    padding: 10px 48px;
    color: #FFFFFF;
    background: #FAA831;
    border-radius: 5px;
    margin-top: 40px;
    margin-bottom: 20px;
    font-weight: 700;
    font-size: 18px;
    float: right;
  }
  .icon{
    font-weight: bold;
    margin-left: 10px;
    transition: .3s;
    &__hide{
      transform: rotate(180deg) !important;
      transition: .3s;
    }
  }
}
.input{
  &-grid{
    margin-top: 28px;
    display: grid;
    grid-template-columns: 1fr 341px 1fr;
    grid-column-gap: 33px;
    grid-row-gap: 25px;
    &__info{
      white-space: nowrap !important;
      grid-template-columns: 1fr 1fr 1fr;
      margin-bottom: 27px;
    }
    @media (max-width: 768px) {
      grid-template-columns: 1fr;
    }
  }
}
.price-range{
  .line{
    margin: auto 11px;
  }
  .input-price{
    font-size: 12px;
    height: 25px;
    border-radius: 5px;
    border: 1px solid #555555;
    box-sizing: border-box;
    width: 48%;
    &:hover, &:active, &:focus{
      border: 1px solid #555555;
    }
  }
}
.hr {
  border: 1px solid #D9D9D9;
  background: #D9D9D9;
}
.slider{
  margin-top: 24px;
  position: relative;
  width: 80%;
  height: 5px;
  background: #D9D9D9;
  &-range{
    position: absolute;
    top: 0;
    height: 100%;
    width: 80%;
    background: #FAA831;
  }
  &-handle{
    height: 14px;
    width: 14px;
    top: -6px;
    border-radius: 2px;
    border: solid 2px #c5a37d;
    background: #c5a37d;
  }
}
.range-slider {
  width: 100%;
  margin: auto;
  text-align: center;
  position: relative;
}

.range-slider input[type=range] {
  position: absolute;
  left: 0;
  bottom: -24px;
  @media (max-width: 768px) {
    bottom: -55px;
  }
}

input[type=number] {
  border: 1px solid #ddd;
  text-align: center;
  font-size: 1.6em;
  -moz-appearance: textfield;
}

input[type=number]::-webkit-outer-spin-button,
input[type=number]::-webkit-inner-spin-button {
  -webkit-appearance: none;
}

input[type=number]:invalid,
input[type=number]:out-of-range {
  border: 2px solid #ff6347;
}

input[type=range] {
  -webkit-appearance: none;
  width: 100%;
}

input[type=range]:focus {
  outline: none;
}

input[type=range]:focus::-webkit-slider-runnable-track {
  background: #FAA831;
}

input[type=range]:focus::-ms-fill-lower {
  background: #FAA831;
}

input[type=range]:focus::-ms-fill-upper {
  background: #FAA831;
}

input[type=range]::-webkit-slider-runnable-track {
  width: 100%;
  height: 5px;
  cursor: pointer;
  animate: 0.2s;
  background: #FAA831;
  border-radius: 1px;
  box-shadow: none;
  border: 0;
}

input[type=range]::-webkit-slider-thumb {
  z-index: 2;
  position: relative;
  box-shadow: 0 0 0 #000;
  border: 1px solid #FAA831;
  height: 18px;
  width: 18px;
  border-radius: 25px;
  background: #f8bd6e;
  cursor: pointer;
  -webkit-appearance: none;
  margin-top: -7px;
}
.row__total{
  margin-top: 44px;
}
.total-title{
  color: #000000;
  font-size: 14px;
  font-weight: 600;
  @media (max-width: 768px) {
    padding: 20px 0;
  }
}
.row{
  margin-right: -26px;
  margin-left: -26px;
}
.row>*{
  padding-right: 26px;
  padding-left: 26px;
}
</style>
