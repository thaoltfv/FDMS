import Model from './Model.js'

export default class Notification extends Model {
	buildUrl (request) {
		const { params } = request
		return ['notification', ...params]
	}
	static async deleteNotification (id) {
		return new this().request({
			method: 'DELETE',
			url: `notification/${id}`,
			isStatic: true
		})
	}
	static async getAll (userId) {
		return new this().request({
			method: 'GET',
			url: `notifications/${userId}`,
			isStatic: true
		})
	}
	static async markAsRead (data) {
		return new this().request({
			method: 'POST',
			url: `notification`,
			data,
			isStatic: true
		})
	}
	static async markAllAsRead () {
		return new this().request({
			method: 'POST',
			url: `notification/all`,
			isStatic: true
		})
	}
}
