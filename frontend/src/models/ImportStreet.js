import Model from './Model.js'

export default class ImportStreet extends Model {
	buildUrl (request) {
		const { params } = request
		return ['import/list', ...params]
	}
	static async getStreet (index, size) {
		return (new this()).request({ method: 'GET', url: `import/street?index=${index}&size=${size}`, isStatic: true })
	}
	static async getDistance () {
		return (new this()).request({ method: 'GET', url: `import/distance`, isStatic: true })
	}
	static async getUnitPrice () {
		return (new this()).request({ method: 'GET', url: `import/unit-price`, isStatic: true })
	}
}
