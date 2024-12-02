<template>
  <div class="card mb-3">
    <div class="card-body">
      <!--Form-->
      <form role="search" @submit.prevent="search">
        <div class="row">

          <!--User name-->
          <div class="col-12 col-sm-6 col-md-4 mb-3">
            <label class="form-label font-weight-bold">{{$t('user_name')}}</label>

            <a-input v-model="filter.nickname"/>
          </div>

          <!--Login ID-->
          <div class="col-12 col-sm-6 col-md-4 mb-3">
            <label class="form-label font-weight-bold">{{$t('login_id')}}</label>

            <a-input v-model="filter.login_id"/>
          </div>

          <!--Prefecture-->
          <div class="col-12 col-sm-6 col-md-4 mb-3">
            <label class="form-label font-weight-bold">{{$t('prefecture')}}</label>

            <div class="custom-arrow">
              <a-select
                mode="multiple"
                style="width: 100%"
                :filter-option="filterOption"
                option-filter-prop="children"
                v-model="filter.prefecture_id"
                :placeholder="$t('placeholder_prefecture')">
                <a-select-option v-for="item in list_prefectures" :key="item.id">
                  {{ item.prefecture_name }}
                </a-select-option>
              </a-select>
              <a-icon type="down" :style="{ fontSize: '12px'}"/>
            </div>
          </div>

          <!--Active-->
          <div class="col-12 col-sm-6 col-md-4 mb-3 mb-sm-0">
            <label class="form-label font-weight-bold">{{$t('status')}}</label>

            <a-select
              show-search
              style="width: 100%"
              v-model="filter.is_active">
              <a-select-option value="">
                {{$t('select_option_empty')}}
              </a-select-option>
              <a-select-option value="1">
                {{$t('select_option_active')}}
              </a-select-option>
              <a-select-option value="0">
                {{$t('select_option_inactive')}}
              </a-select-option>
            </a-select>
          </div>

          <!--User number-->
          <div class="col-12 col-sm-6 col-md-4 mb-3 mb-sm-0">
            <InputText v-model="filter.user_number"
                       rules="max:50"
                       :max-length="50"
                       label="user_number"/>
          </div>

          <!--Search & Reset-->
          <div class="col-12 col-sm-6 col-md-4 mb-3 mb-sm-0">
            <label class="form-label" v-html="'&nbsp;'"/>

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
import { RefreshCwIcon, SearchIcon } from 'vue-feather-icons'
import InputText from '@/components/Form/InputText'

export default {
	name: 'Search',
	data () {
		return {
			list_prefectures: [],
			filter: {
				nickname: '',
				login_id: '',
				prefecture_id: [],
				is_active: '',
				user_number: ''
			}
		}
	},

	components: {
		// Icon
		SearchIcon,
		RefreshCwIcon,
		InputText
	},

	created () {
		this.list_prefectures = this.$route.meta['prefectures']
	},

	methods: {
		search () {
			this.$emit('filter-changed', this.filter)
		},

		reset () {
			for (let property in this.filter) {
				if (this.filter.hasOwnProperty(property)) {
					if (property === 'prefecture_id') {
						this.filter[property] = []
					} else {
						this.filter[property] = ''
					}
				}
			}

			this.$emit('filter-changed', this.filter)
		},

		filterOption (input, option) {
			return (
				option.componentOptions.children[0].text.toLowerCase().indexOf(input.toLowerCase().trim()) >= 0
			)
		}
	}
}
</script>

<style lang="postcss" scoped>

</style>
