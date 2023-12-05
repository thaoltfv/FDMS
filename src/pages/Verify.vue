<template>
  <div class="login" v-if="layout === 'auth'">
  </div>
</template>

<script>
import * as types from '@/store/mutation-types'
import store from '@/store'
import Admin from '@/models/Admin'
import firebase from 'firebase/app'
import 'firebase/auth'
export default {
	name: 'Verify',
	data () {
		return {
			hidePassword: true,
			isSubmit: false,
			submitted: false,
			user: null,
			test: false,
			form: {
				login_id: '',
				token: ''
			},
			error: null
		}
	},
	created () {
	},
	async mounted () {
		await this.getToken()
	},
	computed: {
		layout () {
			return this.$store.state.layout
		}
	},

	methods: {
		getToken () {
			if (store.getters.profile === undefined || store.getters.profile === null) {
				firebase.auth().onAuthStateChanged(user =>
					this.getUser(user)
				)
			}
		},
		async getUser (user) {
			if (firebase.auth().currentUser) {
				user.getIdToken(false).then((idToken) => {
					this.form.token = idToken
					this.form.login_id = user.email
					this.login()
				})
			}
		},
		async login () {
			this.isSubmit = true
			try {
				// clear storage
				await localStorage.clear()
				const response = await Admin.login(this.form.login_id, this.form.token)
				if (response.data && response.data.user_id) {
					const userId = response.data.user_id
					store.commit(types.SET_TOKEN, response.data)
					store.commit(types.SET_USER_ID, userId)
					const profile = await Admin.profile(userId)
					if (profile) {
						store.commit(types.SET_PROFILE, { profile })
						store.commit(types.SET_PERMISSION, profile.data.permissions)
						if (this.isMobile()) {
							return await this.$router.push({ name: 'certification_brief.index' })
						} else {
							return await this.$router.push({ name: 'certification_brief.index' })
						}
					}
				}
			} catch (error) {
				this.isSubmit = false
				await this.$toast.open({
					message: 'Tài khoản đã bị khóa',
					type: 'error',
					position: 'top-right'
				})
				await this.$router.push({ name: 'login' })
			}
		},
		isMobile () {
			if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
				return true
			} else {
				return false
			}
		}
	}
}
</script>

<style scoped>

</style>
