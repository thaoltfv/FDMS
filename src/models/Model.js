import { Model as BaseModel } from 'javel'
import axios from '@/plugins/axios'

export default class Model extends BaseModel {
	baseUrl () {
		return '/api/'
	}

	makeRequest ({ method, url, data, query, onUploadProgress }) {
		return axios({ method, url, data, params: query, onUploadProgress })
	}
}
