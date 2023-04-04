<template>
  <!--Form-->
  <form role="search" @submit.prevent="search" class="search d-flex align-items-end">
    <div class="d-flex flex-row align-items-lg-end align-items-start input">
      <div class="position-relative" style="margin-right: 1.25rem">
        <InputText
          v-model="filter.search"
          placeholder="Nhập tên nhân viên"
          :max-length="200"
          vid="id"
          class="mr-2 mb-0 input-flash"
        />
        <button class="btn-img">
          <img class="img" src="../../assets/icons/ic_search.svg" alt="search">
        </button>
      </div>
      <button style="height: 2.295rem;" class="btn btn-white btn-search text-nowrap index-screen-button"> <img src="../../assets/icons/ic_search.svg" style="margin-right: 8px" alt="search">Tìm kiếm</button>
    </div>
  </form>
</template>

<script>
import User from '@/models/User'
import InputText from '@/components/Form/InputText'
import InputCategory from '@/components/Form/InputCategory'
export default {
	name: 'Search',

	components: {
		InputText,
		InputCategory
	},

	data () {
		return {
			roles: [],
			branches: [],
			filter: {
				search: '',
				staff_id: '',
				branch_id: '',
				role_id: ''
			}
		}
	},

	created () {
	},
	computed: {
		optionsRole () {
			return {
				data: this.roles,
				id: 'id',
				key: 'name'
			}
		},
		optionsBranch () {
			return {
				data: this.branches,
				id: 'id',
				key: 'name'
			}
		}
	},
	methods: {
		search () {
			this.$emit('filter-changed', this.filter)
		},

		async getRoles () {
			try {
				const resp = await User.getRoles()
				this.roles = [...resp.data.data]
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},

		async getBranches () {
			try {
				const resp = await User.getBranches()
				this.branches = [...resp.data]
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
		this.getRoles()
		this.getBranches()
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
  @media (max-width: 766px) {
    margin-right: 0;
    justify-content: space-between;
    .btn-search{
      margin-top: 0 !important;
      min-width: 35%;
    }
  }
}
// .btn-img {
//   right: 5px;
//   top: 45px;
// }
</style>
