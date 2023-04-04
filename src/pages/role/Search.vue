<template>
  <div class="card">
    <div class="card-body">
      <!--Form-->
      <form role="search" @submit.prevent="search">
        <div class="row">

          <!--Display Name-->
          <div class="col-12 col-sm-6 col-md-4 mb-3 mb-sm-0">
            <label class="form-label font-weight-bold">{{$t('display_name_role')}}</label>

            <a-input v-model="filter.display_name"/>
          </div>

          <!--Search & Reset-->
          <div class="col-12 col-sm-6 col-md-4 ml-auto">
            <label class="form-label font-weight-bold" v-html="'&nbsp;'"/>

            <div class="row">
              <div class="col-6">
                <button type="button" @click="reset" class="text-nowrap btn btn-pill btn-light btn-block border-0">
                  {{$t('btn_reset')}}
                </button>
              </div>

              <div class="col-6">
                <button type="submit" class="text-nowrap btn btn-pill btn-primary btn-block border-0">
                  {{$t('btn_search')}}
                </button>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script>

export default {
	name: 'Search',
	data () {
		return {
			filter: {
				display_name: '',
				name: ''
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

<style lang="postcss" scoped>

</style>
