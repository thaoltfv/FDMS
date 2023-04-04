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
              <img src="../../../../assets/icons/ic_cancel-1.svg" alt="cancel">
            </div>
          </div>
          <div class="content-detail">
          <div class="input--search">
            <div class="row justify-content-between">
              <InputText
                v-model="filter.petitioner_name"
                vid="base"
                label="Tên KH yêu cầu (trên Chứng thư)"
                :max="9999999"
                class="col-12 col-md-6 col-lg-6 form-group-container marginTop"
              />
              <InputText
                v-model="filter.id"
                vid="id"
                label="Mã HSTD"
                class="col-12 col-md-6 col-lg-6 form-group-container marginTop"
              />
              <InputText
                v-model="filter.document_num"
                vid="document_num"
                label="Số HĐ"
                class="col-12 col-md-6 col-lg-6 form-group-container marginTop"
              />
              <InputText
                v-model="filter.document_date"
                vid="document_date"
                type="date"
                label="Ngày HĐ"
                @change="changeDocumentDate(public_date_to)"
                class="col-12 col-md-6 col-lg-6 form-group-container marginTop"
              />
              <InputText
                v-model="filter.certificate_num"
                vid="certificate_num"
                label="Số chứng thư"
                class="col-12 col-md-6 col-lg-6 form-group-container marginTop"
              />
              <InputText
                v-model="filter.certificate_date"
                vid="certificate_date"
                type="date"
                label="Ngày chứng thư"
                @change="changeCertificateDate(public_date_to)"
                class="col-12 col-md-6 col-lg-6 form-group-container marginTop"
              />
              <InputCategory
                v-if="activeStatus"
                v-model="filter.created_by"
                vid="info"
                label="Người tạo"
                :options="optionCreateBy"
                class="col-12 col-md-4 col-lg-4 form-group-container marginTop"
              />
              <InputText
                v-model="public_date_from"
                vid="public_date_from"
                type="date"
                label="Ngày tạo - từ ngày"
                @change="changeDateStart(public_date_from)"
                :class="activeStatus ? `${col_4} form-group-container marginTop` : `${col_6} form-group-container marginTop`"
              />
              <InputText
                v-model="public_date_to"
                vid="public_date_to"
                type="date"
                label="Ngày tạo - đến ngày"
                @change="changeDateEnd(public_date_to)"
                :class="activeStatus ? `${col_4} form-group-container marginTop` : `${col_6} form-group-container marginTop`"
              />
              <div>
                  <div class="col-12 marginTop ">
                    <h3 class="total-title">Tổng giá trị thẩm định</h3>
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
import Certificate from '@/models/Certificate'
import moment from 'moment'
export default {
	name: 'ModalSearchCertificate',
	components: {
		InputCategory,
		InputNumberFormat,
		InputDatePickerRange,
		InputText
	},
	props: ['roles', 'filter_search', 'status'],
	data () {
		return {
			col_6: 'col-12 col-md-6 col-lg-6',
			col_4: 'col-12 col-md-4 col-lg-4',
			activeStatus: false,
			showSearch: false,
			propertyTypes: [],
			infoSources: [],
			provinces: [],
			districts: [],
			wards: [],
			streets: [],
			users: [],
			appraisalPurposes: [],
			statusArray: [
				{
					id: 1,
					name: 'Mới'
				},
				{
					id: 2,
					name: 'Đang duyệt'
				},
				{
					id: 3,
					name: 'Đã duyệt'
				}
			],
			public_date_from: '',
			public_date_to: '',
			filter: {
				status: '',
				search: '',
				created_by: '',
				id: '',
				public_date_from: '',
				total_amount_from: 0,
				total_amount_to: 0,
				document_date: '',
				document_num: '',
				certificate_date: '',
				certificate_num: '',
				ticket_num: '',
				petitioner_name: ''
			}
		}
	},
	mounted () {
		let statusFilter = this.statusArray.filter(item => item.id === this.status)
		if (statusFilter && statusFilter.length > 0) {
			this.statusArray = statusFilter
		}
		if (this.filter_search !== undefined && this.filter_search !== null) {
			this.filter.created_by = this.filter_search.created_by
			this.filter.status = this.filter_search.status
			this.filter.id = this.filter_search.id
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
				data: this.statusArray,
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
		},
		optionsAppraisalPurposes () {
			return {
				data: this.appraisalPurposes,
				id: 'id',
				key: 'name'
			}
		}
	},

	methods: {
		async getProfiles () {
			const profile = this.$store.getters.profile
			if (profile && profile.data.user.roles[0].name.slice(-5) === 'ADMIN') {
				this.activeStatus = true
			}
		},
		async getAppraiseOthers () {
			const resp = await Certificate.getAppraiseOthers()
			this.appraisalPurposes = await [...resp.data.muc_dich_tham_dinh_gia]
		},
		changeBase (e) {
			if (e !== undefined && e !== null) {
				this.filter.ticket_num = parseFloat(e).toFixed(0)
			} else {
				this.filter.ticket_num = ''
			}
		},
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
			this.$emit('filter-changed', this.filter)
			this.$emit('cancel', event)
		},
		handleCancel (event) {
			this.$emit('cancel', event)
		},
		async getPropertyType () {
			try {
				const resp = await WareHouse.getPropertyType()
				let propertyTypeAll = [...resp.data]
				propertyTypeAll.forEach((propertyType) => {
					if (propertyType.description !== 'CHUNG CƯ') {
						this.propertyTypes.push(propertyType)
					}
				})
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
		changeCertificateDate (value) {
			this.filter.public_date_to = moment(String(value)).format('DD-MM-YYYY')
		},
		changeDocumentDate (value) {
			this.filter.public_date_to = moment(String(value)).format('DD-MM-YYYY')
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
		this.getProfiles()
		this.getPropertyType()
		this.getInfoSource()
		this.getUser()
		this.getAppraiseOthers()
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
    height: 60vh;
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
    font-size: 1.2rem;
    float: right;
  }
  .icon{
    font-weight: 700;
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
  animation: 0.2s;
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
.marginTop {
  margin-top: 10px;
}
</style>
