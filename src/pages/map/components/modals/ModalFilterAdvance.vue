<template>
  <div>
      <div class="modal-delete d-flex justify-content-center align-items-center" >
          <div class="card">
          <div class="container-title">
              <div class="d-lg-flex d-block shadow-bottom">
              <h2 class="title">Tìm kiếm nâng cao</h2>
              </div>
          </div>
          <div class="card-body">
            <div class="ml-2 row">
							<InputCategory
								v-model="form.property_type"
								vid="property_type"
								label="Loại tài sản"
								:options="optionsPropertyType"
								placeholder="Loại tài sản"
								class="label-none form-group-container col-12 col-lg-6"
							/>
							<InputCategory
								v-model="form.year"
								vid="year"
								label="Năm"
								:options="optionsYears"
								placeholder="Năm"
								class="label-none form-group-container col-12 col-lg-6"
							/>
						</div>
							<div class="seperator  mt-3 col-12">
								<h3><span>Vị Trí</span></h3>
							</div>
							<div class="ml-2 row">
								<div class="form-group-container col-12 col-lg-8">
									<gmap-autocomplete
										:value="form.search_address"
										placeholder='Nhập địa điểm, vị trí hoặc tọa độ'
										@place_changed="setPlace"
										@change="changePlace"
										@keyup.enter="changePlace"
										class="col-12 col-lg-8 input-map"
										:options="{fields: ['geometry', 'address_components', 'formatted_address']}"
									/>
								</div>
								<InputCategory
									v-model="form.front_side"
									vid="front_side"
									label="Mặt tiền"
									:options="optionsFrontSide"
									placeholder="Mặt tiền"
									@change="changeFrontSide($event)"
									class="label-none form-group-container col-12 col-lg-4"
								/>
							</div>
							<div class="seperator mt-3 col-12">
								<h3><span>Khác</span></h3>
							</div>
            <div class="ml-2 row">
								<div class="col-12 col-lg-4 d-flex justify-content-between align-items-center input-number">
								<div class="col-5">
									<InputNumberFormat
										v-model="form.total_area_from"
										vid="total_area_from"
										label=""
										placeholder="Diện tích"
										:max="999999"
										:formatter="valueFormat => `${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
										@change="totalAreaFrom($event)"
										class="label-none form-group-container"
									/>
								</div>~
								<div class="col-5">
									<InputNumberFormat
										v-model="form.total_area_to"
										vid="total_area_from"
										label=""
										placeholder="Diện tích"
										@change="totalAreaTo"
										:max="999999"
										:formatter="valueFormat => `${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
										class="label-none form-group-container"
									/>
								</div>
							</div>
							<div class="vetical-line"> | </div>
							<div class="col-12 col-lg d-flex justify-content-between align-items-center input-number">
								<div class="col-5">
									<InputNumberFormat
										v-model="form.total_amount_from"
										vid="total_area_from"
										label=""
										placeholder="Giá tiền"
										@change="totalAmountFrom($event)"
										:max="99999999999999"
										:formatter="valueFormat => `${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
										class="label-none form-group-container"
									/>
								</div>
								~
								<div class="col-5">
									<InputNumberFormat
										v-model="form.total_amount_to"
										vid="total_area_from"
										placeholder="Giá tiền"
										label=""
										@change="totalAmountTo($event)"
										:max="99999999999999"
										:formatter="valueFormat => `${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
										class="label-none form-group-container"
									/>
								</div>
							</div>
            </div>
            <div class="btn__group mt-2">
              <button class="btn btn-white" type="button" @click="handleCancel"><img src="@/assets/icons/ic_cancel.svg" style="margin-right: 5px" alt="cancel"> Trở lại</button>
              <button class="btn btn-white ml-3 btn-orange text-nowrap" type="button" @click.prevent="handleFilter">Tìm kiếm</button>
            </div>
          </div>

          </div>
      </div>
  </div>
</template>

<script>
import InputText from '@/components/Form/InputText'
import InputTextarea from '@/components/Form/InputTextarea'
import InputDatePicker from '@/components/Form/InputDatePicker'
import InputCategory from '@/components/Form/InputCategory'
import InputNumberFormat from '@/components/Form/InputNumber'
import moment from 'moment'
import store from '@/store'
import * as types from '@/store/mutation-types'
import { isEmpty } from 'lodash-es'

export default {
	name: 'ModalFilterAss',
	components: {
		InputText,
		InputTextarea,
		InputDatePicker,
		InputCategory,
		InputNumberFormat
	},
	data () {
		return {
			// form: this.data ? JSON.parse(JSON.stringify(this.data)) : {},
			form: {
				total_area_from: '',
				total_area_to: '',
				total_amount_from: '',
				total_amount_to: '',
				front_side: '',
				property_type: 0,
				search_address: '',
				coordinate:[],
				year: moment(new Date(new Date().setFullYear(new Date().getFullYear() - 1))).format('YYYY-MM-DD')
			},
			districts: [],
			wards: [],
			streets: [],
			front_sides: [
				{
					name: 'Mặt tiền',
					id: 1
				},
				{
					name: 'Hẻm',
					id: 0
				}
			],
			years: [
				{
					name: '1 năm trước',
					id: moment(new Date(new Date().setFullYear(new Date().getFullYear() - 1))).format('YYYY-MM-DD')
				},
				{
					name: '2 năm trước',
					id: moment(new Date(new Date().setFullYear(new Date().getFullYear() - 2))).format('YYYY-MM-DD')
				},
				{
					name: '3 năm trước',
					id: moment(new Date(new Date().setFullYear(new Date().getFullYear() - 3))).format('YYYY-MM-DD')
				},
				{
					name: '4 năm trước',
					id: moment(new Date(new Date().setFullYear(new Date().getFullYear() - 4))).format('YYYY-MM-DD')
				},
				{
					name: '5 năm trước',
					id: moment(new Date(new Date().setFullYear(new Date().getFullYear() - 5))).format('YYYY-MM-DD')
				}
			],
			propertyTypes: [
				{
					name: 'Nhà đất',
					id: 0
				},
				{
					name: 'Chung cư',
					id: 1
				}
			]

		}
	},
	computed: {
		optionsPropertyType () {
			return {
				data: this.propertyTypes,
				id: 'id',
				key: 'name'
			}
		},
		optionsYears () {
			return {
				data: this.years,
				id: 'id',
				key: 'name'
			}
		},
		optionsFrontSide () {
			return {
				data: this.front_sides,
				id: 'id',
				key: 'name'
			}
		},
	},
	async mounted () {
		const data = await this.getCacheFilterData()
		if (!isEmpty(data)) {
			this.form = data
		}
		this.form.coordinate = this.getDefaultLocation()
	},
	methods: {
		getCacheFilterData () {
			let data = store.getters.mapFilter
			if (isEmpty(data)) {
				let local = localStorage.getItem('mapFilter')
				if (!isEmpty(local)) {
					data = JSON.parse(local)
					store.commit(types.SET_MAP_FILTER, data)
				}
			}
			return data
		},
		getDefaultLocation() {
			let mapLocation = store.getters.mapLocation
			if (isEmpty(mapLocation)) {
				let local = localStorage.getItem('mapLocation')
				if (!isEmpty(local)) {
					store.commit(types.SET_MAP_LOCATION, mapLocation)
					localStorage.setItem('mapLocation', JSON.stringify(mapLocation))
				}
			}
			return mapLocation
		},
		changePlace (event) {
			this.form.search_address = event.target.value
		},
		setPlace (place) {
			if (place.geometry && place.geometry.location) {
				this.form.coordinate = [place.geometry.location.lat(), place.geometry.location.lng()]
				this.form.search_address = place.formatted_address
			} else {
				if (place.name) {
					let location = place.name
					this.form.search_address = place.name
					if (location.split(',') && location.split(',').length === 2 && parseFloat(location.split(',')[0]) && parseFloat(location.split(',')[1])) {
						let lat = parseFloat(location.split(',')[0])
						let lng = parseFloat(location.split(',')[1])
						this.form.coordinate = [lat, lng]
					}
				} else {
					this.form.coordinate = []
				}
			}
		},
		totalAreaFrom (event) {
			if (event) {
				this.form.total_area_from = event
			} else this.form.total_area_from = ''
		},
		totalAreaTo (event) {
			if (event) {
				this.form.total_area_to = event
			} else this.form.total_area_to = ''
		},
		totalAmountFrom (event) {
			if (event) {
				this.form.total_amount_from = event
			} else this.form.total_amount_from = ''
		},
		totalAmountTo (event) {
			if (event) {
				this.form.total_amount_to = event
			} else this.form.total_amount_to = ''
		},
		changeFrontSide (event) {
			if (event || event === 0) {
				this.form.front_side = event
			} else this.form.front_side = ''
		},
		handleCancel (event) {
			this.$emit('cancel', event)
		},
		handleFilter () {
			this.$emit('action', this.form)
		}
	},

}
</script>

<style lang="scss" scoped>
.modal-delete {
  position: fixed;
  z-index: 300;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,.6);

  @media (max-width: 787px) {
    padding: 20px;
  }
  .card {
    max-width: 800px;
    width: 100%;
    margin-bottom: 0;
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
      padding: 0px 15px 15px;
      border-top: 1px solid #E8E8E8;
      .btn__group {
        text-align: right;
        .btn {
          max-width: fit-content;
          width: 100%;
          margin: 14px 0 0;
        }
      }
    }
  }
}
.container-title{
// margin: -35px -95px auto;
// padding: 35px 95px 0;
// padding: 15px 50px 10px 95px;
// box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
padding-left: 1rem;

.title{
  color:#007EC6;
  font-weight: 600;
  margin-top:20px;
  margin-bottom: 15px;
  font-size: 1.2rem;
  @media (max-width: 767px) {
    font-size: 1.125rem;
  }
}
}
.title{
font-weight: 500;
font-size: 1.125rem;
text-align: left;
margin-bottom: 7px;
}
.btn{
&-orange {
  background: #FAA831;
  color: #FFFFFF;
  font-weight: 700 !important;
}
&-white{
  min-width: auto;
}
}
.form-group-container {
margin-top: 10px;
}
.property_content {
font-weight: 600
}
.input_checkbox {
margin-right: 10px;
}
.color-black {
color: #333333;

}
.border_disable {
border-color: #d9d9d9 !important;
}
.form-group-container {
margin-top: 10px;
}
.line{
  border: 1px solid #999999;
  border-radius: 18px;
  width: 12px;
}
.vetical-line{
	display: flex;
    align-items: center;
    justify-content: center;
		width: 77px;
		padding: 0;
		color: #999999;
		@media (max-width: 1023px) {
    display: none;
  }
}
.seperator h3 {
  display: flex;
  justify-content: center;
  align-items: center;
}

.seperator h3::after {
  content: "";
  display: block;
  flex-grow: 1;
  height: 1px;
  background: #ccc;
}

.seperator h3 span {
  padding-right: 0.5em;
}

.input-map {
	margin-left: unset;
}
</style>
