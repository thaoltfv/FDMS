import store from '@/store'

export const LoginGuard = (to, from, next) => {
	if (store.getters.profile) {
		next({ name: 'home' })
		return
	}
	next()
}
