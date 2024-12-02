<template>
  <div id="UpdateShop">
    <Form/>
  </div>
</template>

<script>
import User from '@/models/User'
import Prefecture from '@/models/Prefecture'
import Company from '@/models/Company'
import store from '@/store'
import * as types from '@/store/mutation-types'
import Form from './Form'

export default {
	name: 'Edit',

	data () {
		return {
			detail: null
		}
	},

	components: {
		Form
	},

	beforeRouteEnter: async (to, from, next) => {
		// Get Companies
		const respCompanies = await Company.all()

		// Get Prefecture
		let respPrefecture = store.getters.prefectures

		if (!respPrefecture) {
			respPrefecture = await Prefecture.all()

			store.commit(types.SET_PREFECTURES, respPrefecture)
		}

		// Get Detail
		const resp = await User.find(to.params['id'], {
			query: {
				'include': 'company,prefecture'
			}
		})

		to.meta['companies'] = respCompanies.data
		to.meta['prefectures'] = respPrefecture.data
		to.meta['detail'] = resp.data

		return next()
	}
}
</script>

<style scoped>

</style>
