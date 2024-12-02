<template>
  <!--Form-->
  <form role="search" @submit.prevent="search" class="search d-flex align-items-end">
    <div class="position-relative" style="margin-right: 1.25rem">
      <InputText
        v-model="filter.search"
        placeholder="Nhập tên chi nhánh"
        :max-length="200"
        vid="id"
        class="mr-2 mb-0 input-flash"
      />
      <button class="btn-img">
        <img class="img" src="../../../assets/icons/ic_search.svg" alt="search">
      </button>
    </div>
    <button style="height: 2.295rem;" class="btn btn-white btn-search text-nowrap index-screen-button m-lg-0 mt-2"> <img src="../../../assets/icons/ic_search.svg" style="margin-right: 8px" alt="search">Tìm kiếm</button>
  </form>
</template>

<script>
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
			filter: {
				search: '',
				province_id: ''
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
		}
	},
	methods: {
		search () {
			this.$emit('filter-changed', this.filter)
		},

		reset () {
			for (let property in this.filter) {
				if (this.filter.hasOwnProperty(property)) {
					this.filter[property] = ''
				}
			}
			this.$emit('filter-changed', this.filter)
		}
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
  //margin-right: 10px;
  width: 20%;
  label{
    display: none;
  }
}
.search{
  margin-right: 15px;
  @media (max-width: 767px) {
    margin-right: 0;
  }
}
.btn-search{
  @media (max-width: 767px) {
    min-width: 35%;
  }
}
// .btn-img {
//   right: 5px;
//   top: 45px;
// }
</style>
