import Model from './Model.js'

export default class Banner extends Model {
	buildUrl (request) {
		const { params } = request
		return ['banner', ...params]
	}

	static async delete (id) {
		return (new this()).request({ method: 'DELETE', url: `banner/${id}`, isStatic: true })
	}
}
