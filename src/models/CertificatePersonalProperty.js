//
import Model from './Model.js'

export default class CertificatePersonalProperty extends Model {
	buildUrl (request) {
		const { params } = request
		return ['personal-property/paging', ...params]
	}
}
