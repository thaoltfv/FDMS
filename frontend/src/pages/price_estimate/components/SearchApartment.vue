<template>
  <div class="row justify-content-between">
    <div class="filter-timer col-12 col-lg-3 ">
      <InputText
        v-model="address.province"
        vid="province_id"
        label=""
        placeholder="Tỉnh/Thành"
        class="label-none form-group-container form-disabled"
        disabled-input
      />
    </div>
    <div class="filter-timer col-12 col-lg-3">
      <InputText
        v-model="address.district"
        vid="district_id"
        label=""
        placeholder="Quận/Huyện"
        class="label-none form-group-container form-disabled"
        disabled-input
      />
    </div>
    <div class="filter-timer col-12 col-lg-3">
      <InputText
        v-model="address.ward"
        vid="ward_id"
        label=""
        placeholder="Phường/Xã"
        class="label-none form-group-container form-disabled"
        disabled-input
      />
    </div>
    <div class="filter-timer col-12 col-lg-3">
      <InputText
        v-model="address.street"
        vid="street_id"
        label=""
        placeholder="Đường"
        class="label-none form-group-container form-disabled"
        disabled-input
      />
    </div>
  </div>
</template>

<script>
import InputText from '@/components/Form/InputText'
export default {
	name: 'SearchApartment',
	props: ['address'],
	components: {
		InputText
	},
	data () {
		return {
			front_side: false,
			provinces: [],
			districts: [],
			wards: [],
			streets: [],
			search: {
				front_side: 0,
				province_id: '',
				district_id: '',
				ward_id: '',
				street_id: ''
			}
		}
	},
	methods: {
	}
}
</script>
<style scoped lang="scss">
  .filter-timer{
    @media (max-width: 1023px) {
      margin-bottom: 10px;
    }
  }
  .search-container{
    margin-top: 15px;
  }
</style>
