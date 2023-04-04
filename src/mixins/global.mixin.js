import Vue from 'vue'
import store from '@/store'
import { PERMISSIONS } from '@/enum/permissions.enum'
import { ADMIN_TYPE } from '@/enum/admin-type.enum'

Vue.mixin({
	data () {
		return {PERMISSIONS, ADMIN_TYPE}
	},
	methods: {
		hasPermissionAction (permissionName) {
			const permission = store.getters.currentPermissions.find(permission => permission.trim() === permissionName)
			return permission && 'id' in permission
		}
	}
})
