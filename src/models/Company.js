import Model from './Model.js'

export default class Company extends Model {
	buildUrl (request) {
		const { params } = request
		return ['companies', ...params]
	}

	static async delete (id) {
		return (new this()).request({ method: 'DELETE', url: `companies/${id}`, isStatic: true })
	}
}
