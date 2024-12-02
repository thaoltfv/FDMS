import axios from 'axios'
import { isEmpty } from 'lodash-es'
import router from '@/router'
import * as types from '@/store/mutation-types'
import store from '@/store'
import firebase from 'firebase/app'
import 'firebase/auth'

const instance = axios.create()
instance.defaults.baseURL = process.env.API_URL
instance.defaults.headers.common.Accept = '*/*'

// Interceptors
instance.interceptors.request.use(
	(config) => {
		if (localStorage.getItem('token')) {
			config.headers.Authorization = `Bearer ${localStorage.getItem('token')}`
		}
		return config
	}
)
instance.interceptors.response.use(response => response,
	async (error) => {
		// Do something with response error
		try {
			const status = error.response.status
			const originalRequest = error.config
			const getLogin = async (idToken, user) => {
				const data = await axios.post(originalRequest.baseURL + '/api/auth/refresh', {
					email: localStorage.getItem('email'),
					token: idToken
				})
				if (data !== undefined && data.status === 200) {
					localStorage.setItem('token', idToken)
					return new Promise(resolve => {
						originalRequest.headers.Authorization = `Bearer ${idToken}`
						resolve(axios(originalRequest))
					})
				} else {
					store.commit(types.LOG_OUT)
					if (store.state.layout !== 'auth') await router.push({ name: 'login' })
				}
			}
			if (status === 401) {
				if (!isEmpty(localStorage.getItem('email'))) {
					firebase.auth().onIdTokenChanged(user =>
						user.getIdToken(true).then((idToken) => {
							getLogin(idToken, user)
						})
					)
				} else {
					store.commit(types.LOG_OUT)
					if (store.state.layout !== 'auth') await router.push({ name: 'login' })
				}
			}
		} catch (e) {}
		return Promise.reject(error)
	}
)
export default instance
