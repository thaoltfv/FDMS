<template>
  <!--Form-->
    <form role="search" @submit.prevent="search" class="search d-flex align-items-end justify-content-between w-100">
      <InputCategory
        v-model="filter.province_id"
        vid="id"
        label="Tỉnh/Thành"
        class="mr-2 mb-0 input-search"
        :options= optionsProvince
        @change="changeProvinces"
        style="min-width: 179px"
      />
    </form>
</template>

<script>
import District from '@/models/District'
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
		changeProvinces () {
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
  .search{
    margin-right: 15px;
    @media (max-width: 1020px) {
      margin-bottom: 1rem;
    }
    @media (max-width: 766px) {
      margin-right: 0;
      flex-direction: column;
      margin-bottom: 0;
    }
  }
  .btn-search{
    @media (max-width: 766px) {
      margin-top: 0;
      width: 50%;
      margin-bottom: 1rem;
    }
  }
  .input-search{
    @media (max-width: 766px) {
      width: 100%;
      margin-bottom: 1rem !important;
      margin-right: 0 !important;
    }
  }
  .input-select{
    @media (max-width: 766px) {
      width: 100%;
    }
  }
  // .btn-img {
  //   right: 5px;
  //   top: 45px;
  // }
  </style>
