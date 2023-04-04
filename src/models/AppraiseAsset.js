//
import Model from './Model.js'

export default class AppraiseAsset extends Model {
	buildUrl (request) {
		const { params } = request
		return ['certification_brief/certificate-appraise-list', ...params]
	}
}
