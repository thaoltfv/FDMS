import Admin from '@/models/Admin'
import store from '@/store'
import * as types from '@/store/mutation-types'
import isEmpty from 'lodash-es/isEmpty'
// import { PERMISSIONS } from '@/enum/permissions.enum'

export const AuthGuard = async (to, from, next) => {
	if (store.getters.profile) {
		PermissionGuard(to, from, next)
		return
	}

	if (!store.getters.hasToken) {
		next({ name: 'login' })
		return
	}

	try {
		const profile = await Admin.profile(store.getters.userId)

		if (isEmpty(profile)) {
			next({ name: 'login' })
			return
		}
		store.commit(types.SET_PROFILE, { profile })
		store.commit(types.SET_PERMISSION, profile.data.permissions)
		PermissionGuard(to, from, next)
	} catch (e) {
		store.commit(types.LOG_OUT)
		next({ name: 'login' })
		throw e
	}
}

export const PermissionGuard = (to, from, next) => {
	// if (to.meta.permissions === PERMISSIONS.ALL) {
	//   next()
	// } else {
	const permissions = store.getters.currentPermissions
	const permissionList = to.meta.permissions
	if (permissionList && permissions.find(permission => permissionList.includes(permission))) {
		next()
	} else {
		next({ name: 'error.403' })
	}
	// }
}
