//
import Model from './Model.js'

export default class CertificatePersonalProperty extends Model {
	buildUrl (request) {
		const { params } = request
		return ['personal-property/paging', ...params]
	}
	static async exportDataCertificatePersonalProperty (data) {
		// if (process.env.CLIENT_ENV === 'trial') {
		// 	return {
		// 		error: {message: 'Hiện tại chức năng này chưa được mở ở phiên bản dùng thử'}
		// 	}
		// }
		const { fromDate, toDate, createdBy, status } = data
		return (new this()).request({ method: 'GET', url: `personal-property/adjust-export?fromDate=${fromDate}&toDate=${toDate}&status=${status}&created_by=${createdBy}`, isStatic: true })
	}
}
