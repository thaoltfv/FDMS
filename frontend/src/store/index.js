import Vue from 'vue'
import Vuex from 'vuex'
import * as types from './mutation-types'

Vue.use(Vuex)

export default new Vuex.Store({
	state () {
		return {
			layout: 'default',
			profile: null,
			currentPermissions: [],
			token: localStorage.getItem('token'),
			userId: localStorage.getItem('userId'),
			loadingOverlay: false,
			prefectures: null,
			dataChart: {},
			mapInit: false,
			previewUpdateTime: '',
			productionUpdateTime: '',
			isResponseError: false,
			unreadNotification: 0,
			provinces: [],
			apartments: [],
			dictionaries: [],
			appraiseOther: [],
			mapLocation: [],
			mapFilter: [],
			navExp: true
		}
	},
	getters: {
		apartments: state => state.apartments,
		profile: state => state.profile,
		provinces: state => state.provinces,
		dictionaries: state => state.dictionaries,
		appraiseOther: state => state.appraiseOther,
		hasToken: state => state.token,
		userId: state => state.userId,
		layout: state => state.layout || 'default',
		prefectures: state => state.prefectures,
		dataChart: state => state.dataChart,
		currentPermissions: state => state.currentPermissions,
		mapInit: state => state.mapInit,
		previewUpdateTime: state => state.previewUpdateTime,
		productionUpdateTime: state => state.productionUpdateTime,
		isResponseError: state => state.isResponseError,
		unreadNotification: state => state.unreadNotification,
		mapLocation: state => state.mapLocation,
		mapFilter: state => state.mapFilter,
		navExp: state => state.navExp
	},
	mutations: {
		setIsResponseError (state, payload) {
			state.isResponseError = payload
		},
		[types.SET_PREFECTURES] (state, payload) {
			state.prefectures = payload
		},
		[types.SET_CHART] (state, payload) {
			state.dataChart = payload
		},
		[types.START_LOADING] (state) {
			state.loadingOverlay = true
			document.getElementById('loading').style.opacity = 'block'
		},
		[types.END_LOADING] (state) {
			state.loadingOverlay = false
			document.getElementById('loading').style.display = 'none'
		},
		[types.SET_LAYOUT] (state, { layout }) {
			state.layout = layout
		},
		[types.SET_PROFILE] (state, { profile }) {
			state.profile = profile
			const unreadNotification = profile.data.unreadNotifications || 0
			state.unreadNotification = unreadNotification
			localStorage.setItem('email', profile.data.user.email)
		},
		[types.SET_TOKEN] (state, data) {
			state.token = data.token
			localStorage.setItem('token', data.token)
			localStorage.setItem('refresh-token', data.refreshToken)
		},
		[types.SET_USER_ID] (state, userId) {
			state.userId = userId
			localStorage.setItem('userId', userId)
		},
		[types.SET_PERMISSION] (state, permissions) {
			state.currentPermissions = permissions
		},
		[types.LOG_OUT] (state) {
			state.token = null
			state.profile = null
			localStorage.removeItem('token')
			localStorage.removeItem('userId')
			localStorage.removeItem('refresh-token')
		},
		setMapInit (state, payload) {
			state.mapInit = payload
		},
		[types.SET_APP_UPDATE_TIME] (state, data) {
			state.previewUpdateTime = data.preview
			state.productionUpdateTime = data.production
		},
		[types.SET_UNREAD_NOTIFICATION] (state, number) {
			state.unreadNotification = number
		},
		[types.SET_PROVINCE] (state, data) {
			state.provinces = data
		},
		[types.SET_DICTIONARIES] (state, data) {
			state.dictionaries = data
		},
		[types.SET_APPRAISE_OTHER] (state, data) {
			state.appraiseOther = data
		},
		[types.SET_APARTMENT] (state, data) {
			state.apartments = data
		},
		[types.SET_MAP_LOCATION] (state, mapLocation) {
			state.mapLocation = mapLocation
		},
		[types.SET_MAP_FILTER] (state, mapFilter) {
			state.mapFilter = mapFilter
		},
		[types.SET_NAV_EXP] (state, data) {
			state.navExp = data
		}
	}
})
