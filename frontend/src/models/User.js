import Model from './Model.js'

export default class User extends Model {
	buildUrl (request) {
		const { params } = request
		return ['user', ...params]
	}

	static async deleteUser (id) {
		return (new this()).request({ method: 'DELETE', url: `user/${id}`, isStatic: true })
	}
	static async deActiveUser (id) {
		const data = {id: id}
		return (new this()).request({ method: 'POST', url: `users/deactive-user/${id}`, data, isStatic: true })
	}
	static async activeUser (id) {
		const data = {id: id}
		return (new this()).request({ method: 'POST', url: `users/active-user/${id}`, data, isStatic: true })
	}
	static async IsntLegalUser (id) {
		const data = {id: id}
		return (new this()).request({ method: 'POST', url: `users/isnt-legal-user/${id}`, data, isStatic: true })
	}
	static async IsLegalUser (id) {
		const data = {id: id}
		return (new this()).request({ method: 'POST', url: `users/is-legal-user/${id}`, data, isStatic: true })
	}
	static async resetUser (id) {
		const data = {id: id}
		return (new this()).request({ method: 'POST', url: `users/reset-password/${id}`, data, isStatic: true })
	}
	static async changePassword (password) {
		const data = {new_password: password.new_password, confirm_new_password: password.confirm_new_password}
		return (new this()).request({ method: 'POST', url: `users/change-password`, data, isStatic: true })
	}
	static async getRoles () {
		return (new this()).request({method: 'GET', url: `role`, isStatic: true})
	}
	static async getAllRoles () {
		return (new this()).request({method: 'GET', url: `roles`, isStatic: true})
	}
	static async getBranches () {
		return (new this()).request({method: 'GET', url: `branches`, isStatic: true})
	}
	static async updateUser (id, data) {
		return (new this()).request({ method: 'PUT', url: `user/${id}`, data, isStatic: true })
	}
}
