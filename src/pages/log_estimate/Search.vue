<template>
    <form class="search" role="search" @submit.prevent="search">
      <div class="d-flex align-items-center">
        <div class="position-relative">
          <InputDatePickerRange
            class="input-flash"
            placeholder="Tìm kiếm nhanh"
            vid="search"
            format-date="DD-MM-yyyy"
            @startDate="filter.date_from = $event"
            @endDate="filter.date_to = $event"
            label="Tìm kiếm" />
        </div>
        <button class="btn btn-white" style="margin-left: 20px">
          <img src="../../assets/icons/ic_search.svg" style="margin-right: 8px" alt="search"> Trích xuất dữ liệu
        </button>
      </div>
    </form>

</template>

<script>
import InputCategory from '@/components/Form/InputCategory'
import InputText from '@/components/Form/InputText'
import InputDatePickerRange from '@/components/Form/InputDatePickerRange'
export default {
	name: 'search',
	components: {
		InputCategory,
		InputDatePickerRange,
		InputText
	},
	data () {
		return {
			filter: {
				date_from: '',
				date_to: ''
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
.btn-img{
  &__search{
    right: 5px;
    top: 45px
  }
}
</style>
