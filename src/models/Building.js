import Model from './Model.js'

export default class Building extends Model {
	buildUrl (request) {
		const { params } = request
		return ['building-price', ...params]
	}
	static async getDictionaries () {
		return (new this()).request({ method: 'GET', url: `dictionaries`, isStatic: true })
	}
	static async deleteBuilding (id) {
		return (new this()).request({ method: 'DELETE', url: `building-price/${id}`, isStatic: true })
	}
}
