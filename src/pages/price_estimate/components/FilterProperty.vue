<template>
  <div class="container__filter">
    <div class="loading" :class="{'loading__true': isSubmit}">
      <a-spin />
    </div>
    <div class="filter__detail" :class="search.estimate_type === 'CHUNG_CU' ? 'filter__detail--apartment' : ''">
      <div class="filter__detail--container">
        <div class="container__btn--search">
          <div class="d-block d-md-flex justify-content-end btn-container">
            <button class="btn btn-orange btn-search" type="button" @click="handleSearchAdvanced" v-if="search.estimate_type !== 'CHUNG_CU'">Tìm kiếm nâng cao</button>
          </div>
        </div>
        <div class="container-filter">
          <div class="container-input d-flex">
            <div class="container-input__detail w-100 align-items-center justify-content-between">
              <InputCategory
                v-model="search.estimate_type"
                vid="transaction_type"
                label=""
                placeholder="Loại tài sản"
                :options="optionsTransactionType"
                class="lable-none input-search-map input-search-map--estimate"
                @change="changeAssetType"
              />
              <div class="d-flex input-coordinate" v-if="search.estimate_type !== 'CHUNG_CU'">
                <gmap-autocomplete
                  :value="search.full_address"
                  placeholder="Tìm kiếm theo địa chỉ"
              		@place_changed="setPlace"
                  @change="checkPlace"
                  @keyup.enter="handleSearch"
                  class="input-text-coordinate"
                  :options="{
										fields: ['geometry', 'address_components', 'formatted_address'],
										componentRestrictions:{country: 'vn'}
									}"
                >
                </gmap-autocomplete>

              </div>
              <InputCategory
                v-model="search.apartment_id"
                vid="apartment_id"
                label=""
                placeholder="Tên chung cư"
                :options="optionsApartment"
                class="lable-none input__apartment"
                v-if="search.estimate_type === 'CHUNG_CU'"
                @change="handleApartment"
              />
              <button class="btn btn-orange btn-orange--search" id="coordinateFilter" @click="handleSearch" >
                Tìm kiếm
              </button>
            </div>
          </div>
        </div>
        <div class="input-address" v-if="search_advanced">
          <div class="row justify-content-between">
            <InputCategory
              v-model="search.province_id"
              vid="province_id"
              label=""
              placeholder="Tỉnh/Thành phố"
              :options="optionsProvince"
              @change="changeProvince($event)"
              class="lable-none col-4 input-search-map input-search-map__advanced"
            />
            <InputCategory
              v-model="search.district_id"
              vid="district_id"
              label=""
              placeholder="Quận/Huyện"
              :options="optionsDistrict"
              @change="changeDistrict($event)"
              class="lable-none col-4 input-search-map input-search-map__advanced"
            />
            <InputCategory
              v-model="search.ward_id"
              vid="ward_id"
              label=""
              placeholder="Phường/Xã"
              :options="optionsWard"
              @change="changeWard()"
              class="lable-none col-4 input-search-map input-search-map__advanced"
            />
            <InputCategory
              v-model="search.street_id"
              vid="street_id"
              label="street_id"
              placeholder="Đường"
              :options="optionsStreet"
              @change="changeStreet()"
              class="lable-none col-4 input-search-map input-search-map__advanced"
            />
          </div>
          <div class="container__btn d-flex justify-content-end">
            <button class="btn btn-orange btn-orange--search" id="coordinateFilterAdvanced" @click="searchAdvanced" >
              Áp dụng
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import InputCategory from '@/components/Form/InputCategory'
import InputText from '@/components/Form/InputText'
import WareHouse from '@/models/WareHouse'
import {ASSET_TYPE} from '@/enum/asset-type.enum'
import PriceEstimate from '@/models/PriceEstimate'
import streetJson from '../../../assets/json/street_compare'
export default {
	name: 'search',
	props: ['address', 'property_types'],
	components: {
		InputCategory,
		InputText
	},
	data () {
		return {
			name: '',
			isSubmit: false,
			address_ids: null,
			address_ids_street: null,
			transactionTypes: [],
			apartments: [],
			search_advanced: false,
			openMap: false,
			transaction_types: ASSET_TYPE,
			provinces: [],
			districts: [],
			wards: [],
			streets: [],
			search: {
				estimate_type: 'DAT',
				apartment_id: 1,
				province_id: '',
				district_id: '',
				ward_id: '',
				street_id: '',
				asset_type_ids: [],
				transaction: [],
				coordinates: '',
				full_address: ''
			},
			apartment: {
				full_address: '',
				province: '',
				province_id: '',
				district: '',
				district_id: '',
				ward: '',
				ward_id: '',
				street: '',
				street_id: ''
			},
			address_components: {
				province: '',
				district: '',
				street: '',
				wards: []
			}
		}
	},
	created () {
	},
	computed: {
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
		optionsTransactionType () {
			return {
				data: ASSET_TYPE,
				id: 'TITLE',
				key: 'NAME'
			}
		},
		optionsApartment () {
			return {
				data: this.apartments,
				id: 'id',
				key: 'name'
			}
		}
	},
	methods: {
		setPlace (place) {
			if (place.geometry && place.geometry.location) {
				this.search.full_address = place.formatted_address
			} else {
				if (place.name) {
					this.search.full_address = place.name
				}
			}
			if (this.search.full_address) { this.handleSearch() }
		},
		checkPlace (e) {
			this.search.full_address = e.target.value
		},
		convertString (address) {
			const arrayAddress = address.split(', ')
			if (arrayAddress.length > 2) {
				this.address_components.wards = [arrayAddress[1], arrayAddress[2]]
			} else if (arrayAddress.length > 1) {
				this.address_components.wards = [arrayAddress[1]]
			} else {
				this.address_components.wards = []
			}
		},
		async initMap () {
			// eslint-disable-next-line no-undef
			const geocoder = new google.maps.Geocoder()
			const address = this.search.full_address
			if (address) {
				if (address.split(',') && address.split(',').length === 2 && parseFloat(address.split(',')[0]) && parseFloat(address.split(',')[1])) {
					await this.geocodeAddress(geocoder, address, 'location')
				} else { await this.geocodeAddress(geocoder, address, 'address') }
			}
		},
		async geocodeAddress (geocoder, address, type) {
			let center = {}
			let address_components = []
			// let addressLog = []
			let formatted_address = ''
			console.log(address, type)
			await geocoder.geocode({'address': address}, function (results, status) {
				if (status === 'OK') {
					// addressLog = results[0]
					address_components = results[0].address_components
					formatted_address = results[0].formatted_address
					const marker = {
						position: results[0].geometry.location
					}
					if (type === 'address') {
						center = [parseFloat(marker.position.lat()), parseFloat(marker.position.lng())]
					}
				}
			})
			if (type === 'location') { center = [parseFloat(address.split(',')[0]), parseFloat(address.split(',')[1])] }
			this.search.coordinates = center[0] + ',' + center[1]
			// await this.getAddressLog(addressLog)
			await this.convertString(formatted_address)
			await this.getAddressDetail(address_components)
		},
		async getAddressLog (input) {
			await PriceEstimate.logAddressEstimates(JSON.stringify(input))
		},
		async getAddressDetail (address_components) {
			let address_province = address_components.find(address_component_province => address_component_province.types[0] === 'administrative_area_level_1')
			let address_district = address_components.find(address_component_district => address_component_district.types[0] === 'locality' || address_component_district.types[0] === 'administrative_area_level_2')
			let address_street = address_components.find(address_component => address_component.types[0] === 'route')
			if (address_province) {
				this.address_components.province = address_province.long_name.normalize('NFC')
			} else {
				this.address_components.province = 'UnNamedProvince'
			}
			if (address_district) {
				this.address_components.district = address_district.long_name.normalize('NFC')
			} else {
				this.address_components.district = 'UnNamedRoad'
			}
			if (address_street) {
				this.address_components.street = address_street.long_name.normalize('NFC')
			} else {
				this.address_components.street = ''
			}
			await this.getAddressLocation()
			await this.getLocationStreet(address_street)
			await this.getAddress()
		},
		async getAddressLocation () {
			const province = this.address_components.province
			const district = this.address_components.district
			const wards = this.address_components.wards
			const resp = await PriceEstimate.getAddress({province, district, wards})
			this.address_ids = resp.data
		},
		async getLocationStreet (address_street) {
			if (this.address_ids.district && this.address_ids.district.id) {
				const district_id = this.address_ids.district.id
				let street = this.address_components.street
				const resp = await PriceEstimate.getStreet({district_id, street})
				if (resp.data.street) {
					this.address_ids_street = resp.data
				} else {
					streetJson.data.forEach(streetDistrict => {
						if (address_street && streetDistrict.district.toLowerCase() === this.address_ids.district.name.toLowerCase() && streetDistrict.street === address_street.long_name.normalize('NFC')) {
							street = streetDistrict.street_compare
						}
					})
					const respAgain = await PriceEstimate.getStreet({district_id, street})
					this.address_ids_street = respAgain.data
				}
			}
		},
		getAddress () {
			if (this.address_ids.province !== undefined && this.address_ids.province !== null) {
				this.search.province_id = this.address_ids.province.id
				if (this.search.estimate_type === 'CHUNG_CU') {
					this.apartment.province = this.address_ids.province.name
					this.apartment.province_id = this.address_ids.province.id
				}
			} else {
				this.search.province_id = ''
				if (this.search.estimate_type === 'CHUNG_CU') {
					this.apartment.province = ''
					this.apartment.province_id = ''
				}
			}
			if (this.address_ids.district !== undefined && this.address_ids.district !== null) {
				this.search.district_id = this.address_ids.district.id
				if (this.search.estimate_type === 'CHUNG_CU') {
					this.apartment.district = this.address_ids.district.name
					this.apartment.district_id = this.address_ids.district.id
				}
			} else {
				this.search.district_id = ''
				if (this.search.estimate_type === 'CHUNG_CU') {
					this.apartment.district = ''
					this.apartment.district_id = ''
				}
			}
			if (this.address_ids_street && this.address_ids_street.street !== undefined && this.address_ids_street.street !== null) {
				this.search.street_id = this.address_ids_street.street.id
				if (this.search.estimate_type === 'CHUNG_CU') {
					this.apartment.street = this.address_ids_street.street.name
					this.apartment.street_id = this.address_ids_street.street.id
				}
			} else {
				this.search.street_id = ''
				if (this.search.estimate_type === 'CHUNG_CU') {
					this.apartment.street = ''
					this.apartment.street_id = ''
				}
			}
			if (this.address_ids.ward !== undefined && this.address_ids.ward !== null) {
				this.search.ward_id = this.address_ids.ward.id
				if (this.search.estimate_type === 'CHUNG_CU') {
					this.apartment.ward = this.address_ids.ward.name
					this.apartment.ward_id = this.address_ids.ward.id
				}
			} else {
				this.search.ward_id = ''
				if (this.search.estimate_type === 'CHUNG_CU') {
					this.apartment.ward = ''
					this.apartment.ward_id = ''
				}
			}
		},
		reset () {
			for (let property in this.filter) {
				if (this.filter.hasOwnProperty(property)) {
					this.filter[property] = ''
				}
			}
		},
		changeAssetType () {
			this.search_advanced = false
			this.search.province_id = ''
			this.search.district_id = ''
			this.search.ward_id = ''
			this.search.street_id = ''
			this.search.full_address = ''
			this.apartment.province = ''
			this.apartment.province_id = ''
			this.apartment.district = ''
			this.apartment.district_id = ''
			this.apartment.ward = ''
			this.apartment.ward_id = ''
			this.apartment.street = ''
			this.apartment.street_id = ''
			this.search.coordinates = ''
			if (this.search.estimate_type !== 'CHUNG_CU') {
				this.search.apartment_id = ''
			} else {
				this.search.apartment_id = 1
				let name = ''
				let apartmentAddress = ''
				this.apartments.forEach(apartment => {
					if (apartment.id === this.search.apartment_id) {
						if (apartment.coordinates !== undefined && apartment.coordinates !== null) {
							this.search.coordinates = apartment.coordinates
						} else {
							this.search.coordinates = ''
						}
						if ((apartment.coordinates === '' || apartment.coordinates === undefined || apartment.coordinates === null) && apartment.name !== undefined && apartment.name !== null) {
							name = apartment.name
						} else {
							name = ''
						}
						if (apartment.address !== undefined && apartment.address !== null) {
							apartmentAddress = apartment.address
						} else {
							apartmentAddress = ''
						}
					}
				})
				if (this.search.coordinates === '') {
					this.search.full_address = name + ', ' + apartmentAddress
				} else {
					this.search.full_address = apartmentAddress
				}
				this.name = name
				this.getApartmentAddress()
			}
			this.search.asset_type_ids = []
			if (this.search.estimate_type === '') {
				this.search.estimate_type = 'DAT'
			}
			this.assetTypes.forEach(assetType => {
				if ((this.search.estimate_type === 'DAT' && assetType.description === 'ĐẤT CÓ NHÀ') || (this.search.estimate_type === 'DAT' && assetType.description === 'ĐẤT TRỐNG')) {
					this.search.asset_type_ids.push(assetType.id)
				} else if (this.search.estimate_type === 'CHUNG_CU' && assetType.description === 'CHUNG CƯ') {
					this.search.asset_type_ids.push(assetType.id)
				}
			})
		},
		async getAssetTypes () {
			await this.getDictionary()
			this.assetTypes.forEach(assetType => {
				if ((this.search.estimate_type === 'DAT' && assetType.description === 'ĐẤT CÓ NHÀ') || (this.search.estimate_type === 'DAT' && assetType.description === 'ĐẤT TRỐNG')) {
					this.search.asset_type_ids.push(assetType.id)
				} else if (this.search.estimate_type === 'CHUNG_CU' && assetType.description === 'CHUNG CƯ') {
					this.search.asset_type_ids.push(assetType.id)
				}
			})
			this.$emit('getAsset', this.search.asset_type_ids)
		},
		async getApartmentAddress () {
			this.apartments.forEach(apartment => {
				if (apartment.id === this.search.apartment_id) {
					if (apartment.province !== undefined && apartment.province !== null) {
						if (this.apartment.province === '' || this.apartment.province === undefined || this.apartment.province === null) {
							this.apartment.province = apartment.province.name
						}
						if (this.apartment.province_id === '' || this.apartment.province_id === undefined || this.apartment.province_id === null) {
							this.apartment.province_id = apartment.province.id
						}
					} else {
						this.apartment.province = ''
						this.apartment.province_id = ''
					}
					if (apartment.district !== undefined && apartment.district !== null) {
						if (this.apartment.district === '' || this.apartment.district === undefined || this.apartment.district === null) {
							this.apartment.district = apartment.district.name
						}
						if (this.apartment.district_id === '' || this.apartment.district_id === undefined || this.apartment.district_id === null) {
							this.apartment.district_id = apartment.district.id
						}
					} else {
						this.apartment.district = ''
						this.apartment.district_id = ''
					}
					if (apartment.ward !== undefined && apartment.ward !== null) {
						if (this.apartment.ward === '' || this.apartment.ward === undefined || this.apartment.ward === null) {
							this.apartment.ward = apartment.ward.name
						}
						if (this.apartment.ward_id === '' || this.apartment.ward_id === undefined || this.apartment.ward_id === null) {
							this.apartment.ward_id = apartment.ward.id
						}
					} else {
						this.apartment.ward = ''
						this.apartment.ward_id = ''
					}
					if (apartment.street !== undefined && apartment.street !== null) {
						if (this.apartment.street === '' || this.apartment.street === undefined || this.apartment.street === null) {
							this.apartment.street = apartment.street.name
						}
						if (this.apartment.street_id === '' || this.apartment.street_id === undefined || this.apartment.street_id === null) {
							this.apartment.street_id = apartment.street.id
						}
					} else {
						this.apartment.street = ''
						this.apartment.street_id = ''
					}
				}
			})
			await this.getRadius()
			if (this.name !== '' && this.name !== null && this.name !== undefined) {
				this.search.full_address = `${this.name ? this.name : ''}` + `${this.apartment.street ? ', ' + this.apartment.street : ''}` + `${this.apartment.ward ? ', ' + this.apartment.ward : ''}` + `${this.apartment.district ? ', ' + this.apartment.district : ''}` + `${this.apartment.province ? ', ' + this.apartment.province : ''}`
			} else if (this.apartment.street !== '' && this.apartment.street !== undefined && this.apartment.street !== null) {
				this.search.full_address = `${this.apartment.street ? this.apartment.street : ''}` + `${this.apartment.ward ? ', ' + this.apartment.ward : ''}` + `${this.apartment.district ? ', ' + this.apartment.district : ''}` + `${this.apartment.province ? ', ' + this.apartment.province : ''}`
			} else if (this.apartment.ward !== '' && this.apartment.ward !== undefined && this.apartment.ward !== null) {
				this.search.full_address = this.apartment.ward + `${this.apartment.district ? ', ' + this.apartment.district : ''}` + `${this.apartment.province ? ', ' + this.apartment.province : ''}`
			} else {
				this.search.full_address = `${this.apartment.district ? this.apartment.district : ''}` + `${this.apartment.province ? ', ' + this.apartment.province : ''}`
			}
		},
		getRadius () {
			let radius = ''
			if (this.apartment.district !== 'Thành phố Biên Hòa' && this.apartment.district !== 'Thành phố biên hòa' && this.apartment.dibstrict !== 'Thành phố Long Khánh') {
				radius = 2000
			} else {
				radius = 1000
			}
			this.$emit('get-radius', radius)
		},
		handleMap () {
			this.openMap = true
		},
		async handleSearch () {
			this.$gmapApiPromiseLazy().then(
				await this.initMap()
			)
			if (this.search.estimate_type === 'CHUNG_CU' && this.search.coordinates === '') {
				await this.getApartmentAddress()
				this.$toast.open({
					message: 'Chung cư ' + this.name + ' chưa cập nhật tọa độ',
					type: 'warning',
					position: 'top-right'
				})
			}
			const map = true
			this.$emit('map', map)
			this.$emit('filter-changed', this.search)
			this.$emit('filter-apartment', this.apartment)
		},
		searchAdvanced () {
			const map = true
			this.$emit('map', map)
			this.$emit('filter-changed', this.search)
		},
		handleSearchAdvanced () {
			this.search.province_id = ''
			this.search.district_id = ''
			this.search.ward_id = ''
			this.search.street_id = ''
			this.search_advanced = !this.search_advanced
		},
		changeProvince (provinceId) {
			this.districts = []
			this.wards = []
			this.streets = []
			this.search.district_id = ''
			this.search.ward_id = ''
			this.search.street_id = ''
			this.getDistrictsByProvinceId(+provinceId)
			const data = this.search
			let provinceName = ''
			this.provinces.forEach(province => {
				if (province.id === data.province_id) {
					provinceName = province.name
				}
			})
			this.search.full_address = provinceName
		},
		changeDistrict (districtId) {
			this.wards = []
			this.streets = []
			this.search.ward_id = ''
			this.search.street_id = ''
			this.getWardsByDistrictId(+districtId)
			this.getStreetByDistrictId(+districtId)
			const data = this.search
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
			this.search.full_address = districtName + ',' + provinceName
		},
		changeWard () {
			const data = this.search
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
				this.search.full_address = streetName + ', ' + districtName + ', ' + provinceName
			} else if (streetName === '') {
				this.search.full_address = wardName + ', ' + districtName + ', ' + provinceName
			} else {
				this.search.full_address = streetName + ', ' + wardName + ', ' + districtName + ', ' + provinceName
			}
		},
		changeStreet () {
			const data = this.search
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
				this.search.full_address = streetName + ', ' + districtName + ', ' + provinceName
			} else if (streetName === '') {
				this.search.full_address = wardName + ', ' + districtName + ', ' + provinceName
			} else {
				this.search.full_address = streetName + ', ' + wardName + ', ' + districtName + ', ' + provinceName
			}
		},
		handleApartment () {
			this.apartment.province = ''
			this.apartment.province_id = ''
			this.apartment.district = ''
			this.apartment.district_id = ''
			this.apartment.ward = ''
			this.apartment.ward_id = ''
			this.apartment.street = ''
			this.apartment.street_id = ''
			let name = ''
			let apartmentAddress = ''
			this.apartments.forEach(apartment => {
				if (apartment.id === this.search.apartment_id) {
					if (apartment.coordinates !== undefined && apartment.coordinates !== null) {
						this.search.coordinates = apartment.coordinates
					} else {
						this.search.coordinates = ''
					}
					if ((apartment.coordinates === '' || apartment.coordinates === undefined || apartment.coordinates === null) && apartment.name !== undefined && apartment.name !== null) {
						name = apartment.name
					} else {
						name = ''
					}
					if (apartment.address !== undefined && apartment.address !== null) {
						apartmentAddress = apartment.address
					} else {
						apartmentAddress = ''
					}
				}
			})
			if (this.search.coordinates === '' || this.search.coordinates === undefined || this.search.coordinates === null) {
				this.search.full_address = name + ', ' + apartmentAddress
			} else {
				this.search.full_address = apartmentAddress
			}
			this.name = name
			this.getApartmentAddress()
		},
		async getProvinces () {
			try {
				const resp = await WareHouse.getProvince()
				this.provinces = [...resp.data]
				await this.getDistrictsByProvinceId(this.search.province_id)
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},

		async getDistrictsByProvinceId (id) {
			try {
				const resp = await WareHouse.getDistrictsByProvinceId(id)
				this.districts = [...resp.data]
				if (this.search.district_id !== '') {
					await this.getWardsByDistrictId(this.search.district_id)
					await this.getStreetByDistrictId(this.search.district_id)
				}
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},
		async getWardsByDistrictId (id) {
			this.wards = this.districts.find(item => item.id === id).wards
			this.wards.forEach(item => {
				item.name = this.formatCapitalize(item.name)
			})
		},
		async getStreetByDistrictId (id) {
			this.streets = this.districts.find(item => item.id === id).streets
			this.streets.forEach(item => {
				item.name = this.formatCapitalize(item.name)
			})
		},
		formatCapitalize (word) {
			return word.replace(/(?:^|\s|[-"'([{])+\S/g, function (x) { return x.toUpperCase() })
		},
		async getApartments () {
			try {
				const resp = await WareHouse.getApartment()
				this.apartments = [...resp.data]
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},
		async getDictionary () {
			try {
				const reps = await WareHouse.getDictionaries()
				this.assetTypes = [...reps.data.loai_tai_san]
				this.transactionTypes = [...reps.data.loai_giao_dich]
				await this.getTransactionType()
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},
		getTransactionType () {
			this.transactionTypes.forEach(transaction => {
				if (transaction.description === 'ĐÃ BÁN' || transaction.description === 'ĐANG RAO BÁN') {
					this.search.transaction.push(transaction.id)
				}
			})
		},
		handleCoordinate (coordinate) {
			this.search.coordinates = coordinate
		},
		handleGetAddress (address, full_address) {
			this.search.full_address = full_address
			this.address_components = address
		}
	},
	async beforeMount () {
		this.isSubmit = true
		await this.getDictionary()
		await this.changeAssetType()
		this.isSubmit = false
		// await this.getApartments()
		await this.getProvinces()
	}
}
</script>
<style scoped lang="scss">
.container-filter{
  margin: auto;
  padding: 30px;
  background: #FFFFFF;
  box-shadow: 0 0 15px rgba(0, 0, 0, 0.25);
  border-radius: 5px;
  box-sizing: border-box;
}
.btn{
  font-size: 1.125rem;
  &-container{
    margin-bottom: 10px;
  }
  &-coordinate{
    border: none;
    height: 2.295rem;
    padding: 0 38px;
    font-size: 1.125rem;
    @media (max-width: 767px) {
      padding: 0 16px;
    }
  }
  &-white{
    background: #FFFFFF;
    color: #000000;
    border-color: transparent;
    padding: 12px 0;
    min-width: 140px;
    margin-right: 5px;
    &.active {
      background: #FAA831;
      color: #FFFFFF;
    }
    &.disabled{
      cursor: not-allowed;
    }
  }
  &-orange{
    background: #FAA831;
    color: #FFFFFF;
  }
  &-outline{
    &-white{
      height: 2.295rem;
      width: 155px;
      border: 1px solid #000000;
      background: #ffffff;
      color: #000000;
      &:hover{
        border: 1px solid #000000 !important;
        background: #ffffff !important;
        color: #000000 !important;
      }
      @media (max-width: 1023px) {
        width: 100%;
        margin-top: 10px;
      }
    }
  }
}
.input-coordinate{
  max-width: 70%;
  width: 826px;
  margin: auto 10px;
  @media (max-width: 1620px) {
    max-width: 60%;
  }
  @media (max-width: 1200px) {
    max-width: 55%;
  }
  @media (max-width: 1024px) {
    margin: 10px auto auto;
    max-width: 100%;
  }
}
.img-locate{
}
.container-input{
  box-sizing: border-box;
  background: #FFFFFF;
  &__detail {
    display: flex;
    @media (max-width: 1023px) {
      display: block;
    }
  }
}
.input-address{
  border-radius: 0 0 5px 5px;
  padding: 30px;
  background: rgba(255, 255, 255, 0.9);
  border: 1px solid #D0D0D0;
  z-index: -1;
  box-sizing: border-box;
}
.container {
  &__filter {
    max-width: 1377px;
    margin: auto;
    height: calc(100% - 80px);
    max-height: calc(100% - 80px);
    .filter{
      &__detail {
        height: 100%;
        width: 100%;
        display: flex;
        justify-content: center;
        padding-top: 275px;
        @media (max-width: 1023px) {
          padding-top: 0;
          align-items: center;
        }
        &--container{
          width: 100%;
        }
        &--apartment{
          padding-top: 300px;
        }
      }
    }
  }
  &__btn{
    margin-top: 60px;
  }
}
.btn-search{
  @media (max-width: 767px) {
    margin-top: 10px;
    width: 100%;
  }
}
.btn-orange--search {
  @media (max-width: 1023px) {
    margin-top: 10px;
    margin-bottom: 0;
  }
}
.input-coordinate {
  border: 1px solid #818181;
  display: flex;
  align-items: center;
  border-radius: 3px;
  padding: 0 0 0 11px;
  height: 2.295rem;
  box-sizing: border-box;
  .input-text-coordinate {
    height: 2.295rem;
    width: 100%;
    border: none;
    background: transparent;
    &:focus-visible {
      outline: none;
    }
  }
  .btn-coordinate {
    height: 26px;
    box-shadow: none;
  }
}
.loading{
  display: none;
  &__true{
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
    &.btn-loading{
      &:after{
        width: 2rem !important;
        height: 2rem !important;
      }
    }
  }
}
</style>
