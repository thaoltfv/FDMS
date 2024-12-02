import Model from './Model.js'

export default class WorkFlow extends Model {
	buildUrl (request) {
		const { params } = request
		return ['workflow', ...params]
	}
	static async getWorkFlow () {
		return (new this()).request({ method: 'GET', url: `workflow/getworkflow`, isStatic: true })
	}
}
