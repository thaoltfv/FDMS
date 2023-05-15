<template>
  <!--Form-->
  <form role="search" @submit.prevent="search" class="search d-md-flex d-block align-items-end justify-content-between w-100">
    <div class="d-flex align-items-end justify-content-md-start justify-content-between">
      <InputCategory
        v-model="filter.province_id"
        label="Tỉnh/Thành"
        class="mb-0 input-select"
        :options= optionsProvince
        style="min-width: 179px; margin-right: 15px"
        vid="province"
        @change="changeProvince($event)"
      />
      <InputCategory
        v-model="filter.district_id"
        class="mr-2 mb-0 input-select"
        vid="district"
        label="Quận/Huyện"
        style="min-width: 179px"
        :options= optionsDistrict />
    </div>
    <div class="d-flex flex-row align-items-lg-end align-items-start input">
      <div class="position-relative" style="margin-right: 1.25rem">
        <InputText
          v-model="filter.search"
          label="Phường/Xã"
          class="input-flash"
          vid="ward"
          placeholder="Nhập tên Phường/Xã"
        />
        <button class="btn-img">
          <img class="img" src="../../../assets/icons/ic_search.svg" alt="search">
        </button>
      </div>
      <!-- <button style="height: 2.295rem;" class="btn btn-white btn-search text-nowrap index-screen-button"> <img src="../../../assets/icons/ic_search.svg" style="margin-right: 8px" alt="search">Tìm kiếm</button> -->
    </div>
  </form>
</template>

<script>
import District from '@/models/District'
import InputCategory from '@/components/Form/InputCategory'
import InputText from '@/components/Form/InputText'
import Ward from '@/models/Ward'
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
			filter: {
				search: '',
				province_id: '',
				district_id: ''
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
		}
	},
	methods: {
		changeProvince (provinceId) {
			this.districts = []
			this.filter.district_id = ''
			this.getDistrictsByProvinceId(+provinceId)
		},
		search () {
			this.$emit('filter-changed', this.filter)
		},

		async getProvinces () {
			try {
				const resp = await District.getProvince()
				this.provinces = [...resp.data]
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},
		async getDistrictsByProvinceId (id) {
			try {
				const resp = await Ward.getDistrict(id)
				this.districts = [...resp.data]
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
		this.getProvinces()
	}
}
</script>

<style lang="scss" scoped>
.input{
  @media (max-width: 1024px) {
    flex-direction: row !important;
    margin-top: 1rem;
    .btn-search{
      min-width: 40%;
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
  &-select{
    @media (max-width: 767px) {
      min-width: 45% !important;
      margin-right: 0 !important;
    }
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
.search{
  margin-right: 15px;
  @media (max-width: 767px) {
    margin-right: 0;
  }
}
.btn-search{
  @media (max-width: 1024px) {
    min-width: 50%;
  }
  @media (max-width: 767px) {
    min-width: 35%;
    width:35%;
  }
}

.pannel{
  background: #FFFFFF;
  box-shadow: 1px 2px 0 #e5eaee;
  border-radius: 5px;
  padding: 25px;
  margin-bottom: 40px;
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
// .btn-img {
//   right: 5px;
//   top: 45px;
// }
</style>
