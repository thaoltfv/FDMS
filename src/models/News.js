import Model from './Model.js'

export default class News extends Model {
	buildUrl (request) {
		const { params } = request
		return ['news', ...params]
	}

	static async news_categories () {
		return (new this()).request({ method: 'GET', url: `news_categories`, isStatic: true })
	}

	static async delete (id) {
		return (new this()).request({ method: 'DELETE', url: `news/${id}`, isStatic: true })
	}
}
