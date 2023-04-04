<template>
  <!--Form-->
  <form role="search" @submit.prevent="search" class="search w-100">
    <div class="row container-input">
      <InputCategory
        v-model="province_id"
        label="Tỉnh/Thành"
        class="col-12 col-lg-4 input-select"
        :options="optionsProvince"
        vid="province_id"
        @change="changeProvince($event)"
      />
      <InputCategory
        v-model="district_id"
        class="col-12 col-lg-4 input-select"
        vid="district_id"
        label="Quận/Huyện"
        :options= "optionsDistrict"
        @change="changeDistrict($event)"
      />
      <InputCategory
        v-model="ward_id"
        class="col-12 col-lg-4 input-select"
        vid="ward_id"
        label="Phường/Xã"
        :options= "optionsWard"
        @change="changeWard($event)"
      />
      <InputCategory
        v-model="street_id"
        class="col-12 col-lg-6 input-select input-select__unit"
        vid="street_id"
        label="Đường"
        :options= "optionsStreet"
        @change="changeStreet($event)"
      />
      <InputCategory
        v-model="distance_id"
        class="col-12 col-lg-6 input-select input-select__unit"
        vid="distance_id"
        label="Đoạn"
        @change="changeDistance"
        :options= optionsDistance
      />
    </div>
    <div class=" container__btn d-flex flex-row align-items-lg-end align-items-start input">
      <button style="height: 2.295rem;" class="btn btn-white btn-search text-nowrap index-screen-button"> <img src="../../../assets/icons/ic_search.svg" style="margin-right: 8px" alt="search">Tìm kiếm</button>
    </div>
  </form>
</template>

<script>
import District from '@/models/District'
import WareHouse from '@/models/WareHouse'
import InputCategory from '@/components/Form/InputCategory'
import InputText from '@/components/Form/InputText'
export default {
	name: 'Search',

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
			distances: [],
			province_id: '',
			district_id: '',
			street_id: '',
			ward_id: '',
			distance_id: '',
			filter: {
				province: '',
				district: '',
				street: '',
				ward: '',
				distance: ''
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
		optionsDistance () {
			return {
				data: this.distances,
				id: 'id',
				key: 'name'
			}
		}
	},
	methods: {
		search () {
			this.$emit('filter-changed', this.filter)
		},

		async getProvinces () {
			try {
				const resp = await District.getProvince()
				this.provinces = [...resp.data]
				if (this.province_id === 0 && this.province_id !== '' && this.province_id !== undefined && this.province_id !== null) {
					await this.getDistrictsByProvinceId(this.province_id)
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
				if (this.district_id === 0 && this.district_id !== '' && this.district_id !== undefined && this.district_id !== null) {
					await this.getWardsByDistrictId(this.district_id)
					await this.getStreetByDistrictId(this.district_id)
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
				if (this.street_id === 0 && this.street_id !== '' && this.street_id !== undefined && this.street_id !== null) {
					await this.getDistanceByStreetId(this.street_id)
				}
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},
		async getDistanceByStreetId (id) {
			const resp = await WareHouse.getDistance(id)
			this.distances = [...resp.data]
		},
		changeProvince (provinceId) {
			this.districts = []
			this.wards = []
			this.streets = []
			this.distances = []
			this.district_id = ''
			this.ward_id = ''
			this.street_id = ''
			this.distance_id = ''
			let provinceName = ''
			this.filter.province = ''
			this.filter.district = ''
			this.filter.street = ''
			this.filter.ward = ''
			this.filter.distance = ''
			this.getDistrictsByProvinceId(+provinceId)
			this.provinces.forEach(province => {
				if (province.id === this.province_id) {
					provinceName = province.name
				}
			})
			this.filter.province = provinceName
		},
		changeDistrict (districtId) {
			this.wards = []
			this.streets = []
			this.distances = []
			this.ward_id = ''
			this.street_id = ''
			this.distance_id = ''
			let districtName = ''
			this.filter.district = ''
			this.filter.street = ''
			this.filter.ward = ''
			this.filter.distance = ''
			this.getWardsByDistrictId(+districtId)
			this.getStreetByDistrictId(+districtId)
			this.districts.forEach(district => {
				if (district.id === this.district_id) {
					districtName = district.name
				}
			})
			this.filter.district = districtName
		},
		changeWard () {
			let wardName = ''
			this.filter.ward = ''
			this.wards.forEach(ward => {
				if (ward.id === this.ward_id) {
					wardName = ward.name
				}
			})
			this.filter.ward = wardName
		},
		changeStreet (streetId) {
			let streetName = ''
			this.distances = []
			this.distance_id = ''
			this.filter.street = ''
			this.filter.distance = ''
			this.getDistanceByStreetId(+streetId)
			this.streets.forEach(street => {
				if (street.id === this.street_id) {
					streetName = street.name
				}
			})
			this.filter.street = streetName
		},
		changeDistance () {
			let distanceName = ''
			this.filter.distance = ''
			this.distances.forEach(distance => {
				if (distance.id === this.distance_id) {
					distanceName = distance.name
				}
			})
			this.filter.distance = distanceName
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
		this.getProvinces()
	}
}
</script>

<style lang="scss" scoped>
.input{
  @media (max-width: 1023px) {
    width: 100%;
  }
  .input-category{
    @media (max-width: 1023px) {
      margin-bottom: 10px;
      width: 100%;
    }
  }
  .btn-gray{
    @media (max-width: 1023px) {
      width: 100%;
    }
  }
}
.pannel{
  background: #FFFFFF;
  box-shadow: 1px 2px 0 #e5eaee;
  border-radius: 5px;
  margin-bottom: 47px;
  &__table{
    padding: 25px 0;
    border-radius: 5px;
  }
  &__input{
    p{
      color: #5a5386;
      font-weight: 600;
    }
  }
}
.form-control{
  margin-right: 5px;
  width: auto;
  color: #555555;
  border-radius: 5px;

  @media (max-width: 1023px) {
    width: 100%;
  }
  &:focus{
    border-color: #CCCCCC;
    box-shadow: none;
  }
}
.input-category{
  margin-right: 10px;
  width: 20%;
  label{
    display: none;
  }
}
.search{
  margin-right: 15px;
  display: block;
  align-items: end;
  justify-content: space-between;
  @media (max-width: 1600px) {
    margin-top: 10px;
    margin-right: 0;
    display: block;
  }
}
.container {
  &__btn {
      margin-top: 10px;
      display: flex;
      justify-content: flex-end;
    .btn-search{
      @media (max-width: 1024px) {
        min-width: 50%;
      }
      @media (max-width: 767px) {
        min-width: 35%;
        width:35%;
      }
    }
  }
}
.input{
  @media (max-width: 1024px) {
    flex-direction: row !important;
    margin-top: 1rem;
    .btn-search{
      min-width: 35%;
    }
  }
  @media (max-width: 767px) {
    flex-direction: row !important;
    margin-top: 1rem;
    justify-content: space-between;
    .btn-search{
      margin-top: 0;
    }
  }
  /*&-select{*/
  /*  width: 200px;*/
  /*  &__unit {*/
  /*    min-width: 240px !important;*/
  /*    width: 240px;*/
  /*  }*/
  /*  @media (max-width: 767px) {*/
  /*    min-width: 45% !important;*/
  /*    margin-right: 0 !important;*/
  /*  }*/
  /*}*/
}
</style>
