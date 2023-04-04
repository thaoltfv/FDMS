<template>
  <div id="CreateNews">
    <Form/>
  </div>
</template>

<script>
import Prefecture from '@/models/Prefecture'
import Company from '@/models/Company'
import store from '@/store'
import * as types from '@/store/mutation-types'
import Form from './Form'

export default {
	name: 'Create',

	components: { Form },

	async beforeRouteEnter (to, from, next) {
		// Get Companies
		const respCompanies = await Company.all()

		// Get Prefecture
		let respPrefecture = store.getters.prefectures

		if (!respPrefecture) {
			respPrefecture = await Prefecture.all()

			store.commit(types.SET_PREFECTURES, respPrefecture)
		}

		to.meta['companies'] = respCompanies.data
		to.meta['prefectures'] = respPrefecture.data

		return next()
	}
}
</script>

<style scoped>

</style>
