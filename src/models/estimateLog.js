import Model from './Model.js'

export default class EstimateLog extends Model {
	buildUrl (request) {
		const { params } = request
		return ['estimate-log', ...params]
	}
	static async getEstimateLogs (ids) {
		const data = '[' + ids + ']'
		return (new this()).request({ method: 'GET', url: `estimate-logs?ids=${data}`, isStatic: true })
	}
}
