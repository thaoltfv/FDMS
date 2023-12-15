import Model from "./Model.js";

export default class PreCertificateConfig extends Model {
	buildUrl(request) {
		const { params } = request;
		return ["pre-certificate-config", ...params];
	}
	static async getConfig() {
		let resp = await new this().request({
			method: "GET",
			url: `pre-certificate-configs`,
			isStatic: true
		});
		return resp;
	}
}
