<template>
      <!--Form-->
    <form role="search" @submit.prevent="search" class="search d-md-flex d-block align-items-end justify-content-end w-100">
      <div class="d-flex flex-row align-items-lg-end align-items-start input">
        <div class="position-relative" style="margin-right: 1.25rem">
          <InputText
            v-model="filter.search"
            placeholder="Nhập tên chung cư"
            vid="id"
            label="Tên chung cư"
            class="mr-2 mb-0 input-flash"
          />
          <button class="btn-img">
            <img class="img" src="@/assets/icons/ic_search.svg" alt="search">
          </button>
        </div>
        <!-- <button style="height: 2.295rem;" class="btn btn-white btn-search text-nowrap index-screen-button">
					<img src="@/assets/icons/ic_search.svg" style="margin-right: 8px" alt="search">Tìm kiếm</button> -->
      </div>
    </form>

</template>

<script>
import InputText from '@/components/Form/InputText'
import InputCategory from '@/components/Form/InputCategory'
import Apartment from '@/models/Apartment'
export default {
	name: 'Search',
	data () {
		return {
			provinces: [],
			districts: [],
			filter: {
				search: ''
			}
		}
	},

	created () {
	},
	components: {
		InputText,
		InputCategory
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
				const resp = await Apartment.getProvince()
				this.provinces = [...resp.data]
				this.filter.province_id = 45
				await this.getDistrictsByProvinceId(this.filter.province_id)
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},
		async getDistrictsByProvinceId (id) {
			try {
				const resp = await Apartment.getDistrict(id)
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
.search{
  margin-right: 15px;
  @media (max-width: 766px) {
    margin-right: 0;
  }
}
.btn-search{
  @media (max-width: 766px) {
    margin-top: 0 !important;
    width: 50%;
  }
}
// .btn-img {
//   right: 10px;
//   top: 1.2rem;
// }
</style>
