import Model from "./Model.js";

export default class PreCertificateConfig extends Model {
	buildUrl(request) {
		const { params } = request;
		return ["pre-certificate-config", ...params];
	}
}
