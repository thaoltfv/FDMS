<template>
    <form class="search" role="search" @submit.prevent="search">
      <div class="position-relative">
        <InputText
          v-model="filter.search"
          class="input-flash"
          placeholder="Tìm kiếm nhanh"
          vid="search"
          />
        <button class="btn-img btn-img__search">
          <img class="img" src="../../assets/icons/ic_search.svg" alt="search">
        </button>
      </div>
    </form>
</template>

<script>
import InputCategory from '@/components/Form/InputCategory'
import InputText from '@/components/Form/InputText'
export default {
	name: 'search',
	components: {
		InputCategory,
		InputText
	},
	data () {
		return {
			filter: {
				search: ''
			}
		}
	},
	created () {
	},
	computed: {
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
	},
	beforeMount () {
	}
}
</script>
<style scoped lang="scss">
.search{
  @media (max-width: 1440px) {
    width: 100%;
  }
}
// .btn-img{
//   &__search{
//     right: 5px;
//     top: 45px;
//   }
// }
</style>
