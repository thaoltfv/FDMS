import Model from './Model.js'

export default class getEstimateLog extends Model {
	buildUrl (request) {
		const { params } = request
		return ['get-estimate-log', ...params]
	}
}
