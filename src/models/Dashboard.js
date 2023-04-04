import Model from './Model.js'

export default class Dashboard extends Model {
	buildUrl (request) {
		const { params } = request
		return ['dashboards', ...params]
	}
}
