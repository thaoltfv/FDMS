<template>
  <div class="row">
    <div class="filter-timer col-12 col-lg-3 ">
      <InputCategory
        v-model="address.province_id"
        vid="province_id"
        label=""
        type="date"
        :options="optionsProvince"
        placeholder="Tỉnh/Thành"
        @change="changeProvince($event)"
        class="label-none form-group-container"
      />
    </div>
    <div class="filter-timer col-12 col-lg-3">
      <InputCategory
        v-model="address.district_id"
        vid="district_id"
        label=""
        type="date"
        :options="optionsDistrict"
        @change="changeDistrict($event)"
        placeholder="Quận/Huyện"
        class="label-none form-group-container"
      />
    </div>
    <div class="filter-timer col-12 col-lg-3">
      <InputCategory
        v-model="address.ward_id"
        vid="ward_id"
        label=""
        type="date"
        :options="optionsWard"
        placeholder="Phường/Xã"
        @change="changeWard($event)"
        class="label-none form-group-container"
      />
    </div>
    <div class="filter-timer col-12 col-lg-3">
      <InputCategory
        v-model="address.street_id"
        vid="street_id"
        label=""
        type="date"
        :options="optionsStreet"
        placeholder="Đường"
        @change="changeStreet"
        class="label-none form-group-container"
      />
    </div>
  </div>
</template>

<script>
import InputCategory from '@/components/Form/InputCategory'
import InputText from '@/components/Form/InputText'
import WareHouse from '@/models/WareHouse'
export default {
	name: 'search',
	props: ['address'],
	components: {
		InputCategory,
		InputText
	},
	data () {
		return {
			provinces: [],
			districts: [],
			wards: [],
			streets: [],
			search: {
				province_id: '',
				district_id: '',
				ward_id: '',
				street_id: ''
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
		}
	},
	methods: {
		reset () {
			for (let property in this.filter) {
				if (this.filter.hasOwnProperty(property)) {
					this.filter[property] = ''
				}
			}
			this.$emit('filter-changed', this.filter)
		},
		changeProvince (provinceId) {
			this.districts = []
			this.wards = []
			this.streets = []
			this.address.district_id = ''
			this.address.ward_id = ''
			this.address.street_id = ''
			this.getDistrictsByProvinceId(+provinceId)
			this.getWardsByDistrictId(+provinceId)
			this.getStreetByDistrictId(+provinceId)
			const data = this.address
			let provinceName = ''
			this.provinces.forEach(province => {
				if (province.id === data.province_id) {
					provinceName = province.name
				}
			})
			this.address.full_address = provinceName
		},
		changeDistrict (districtId) {
			this.wards = []
			this.streets = []
			this.address.ward_id = ''
			this.address.street_id = ''
			this.getWardsByDistrictId(+districtId)
			this.getStreetByDistrictId(+districtId)
			const data = this.address
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
			this.address.full_address = districtName + ',' + provinceName
		},
		changeWard () {
			const data = this.address
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
				this.address.full_address = streetName + ', ' + districtName + ', ' + provinceName
			} else if (streetName === '') {
				this.address.full_address = wardName + ', ' + districtName + ', ' + provinceName
			} else {
				this.address.full_address = streetName + ', ' + wardName + ', ' + districtName + ', ' + provinceName
			}
		},
		changeStreet () {
			const data = this.address
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
				this.address.full_address = streetName + ', ' + districtName + ', ' + provinceName
			} else if (streetName === '') {
				this.address.full_address = wardName + ', ' + districtName + ', ' + provinceName
			} else {
				this.address.full_address = streetName + ', ' + wardName + ', ' + districtName + ', ' + provinceName
			}
		},
		async getProvinces () {
			try {
				const resp = await WareHouse.getProvince()
				this.provinces = [...resp.data]
				if (this.address.province_id === '') {
					this.address.province_id = 34
				}
				await this.getDistrictsByProvinceId(this.address.province_id)
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},

		async getDistrictsByProvinceId (id) {
			await WareHouse.getDistrictsByProvinceId(id)
				.then((resp) => {
					this.districts = resp.data
					if (this.address.district_id !== '') {
						this.getWardsByDistrictId(this.address.district_id)
						this.getStreetByDistrictId(this.address.district_id)
					}
				})
				.catch((err) => {
					this.isSubmit = false
					throw err
				})
		},
		getWardsByDistrictId (id) {
			let wards = this.districts.filter(item => item.id === id)
			this.wards = wards[0].wards
		},
		getStreetByDistrictId (id) {
			let streets = this.districts.filter(item => item.id === id)
			this.streets = streets[0].streets
		},
		async beforeMount () {
			await this.getProvinces()
		}
	}
}
</script>
<style scoped lang="scss">
.filter-timer{
  @media (max-width: 1023px) {
    margin-bottom: 10px;
  }
}
</style>
