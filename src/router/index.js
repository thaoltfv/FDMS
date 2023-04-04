import Vue from 'vue'
import ToastPlugin from 'vue-toast-notification'
import Router from 'vue-router'
import { routes } from '@/router/routes'
import store from '@/store'
import head from 'lodash-es/head'
import * as types from '@/store/mutation-types'

Vue.use(Router)
Vue.use(ToastPlugin)

const router = new Router({
	mode: 'history',
	/* base: '/cms2/', */
	routes
})

/**
 * Global Before Guards
 *
 * @param to
 * @param from
 * @param next
 * @returns {Promise<void>}
 */
const beforeEach = async (to, from, next) => {
	// Check if this feature is block from current client env
	if (to.meta && to.meta.denied && to.meta.denied.includes(process.env.CLIENT_ENV)) {
		Vue.$toast.open({
			message: 'Hiện tại chức năng này chưa được mở ở phiên bản hiện tại, vui lòng liên hệ đội ngũ kỹ thuật',
			type: 'error',
			position: 'top-right',
			duration: 3000
		})
		return false
	}
	const currentRoute = head(to.matched)
	store.commit(types.SET_LAYOUT, { layout: currentRoute.meta.layout })
	router.app.$Progress.start()
	next()
}

/**
 * Global After Hooks
 *
 * @returns {Promise<void>}
 */
const afterEach = async () => {
	await router.app.$nextTick()
	store.commit(types.END_LOADING)
	router.app.$Progress.finish()
}
router.beforeEach(beforeEach)
router.afterEach(afterEach)
export default router
