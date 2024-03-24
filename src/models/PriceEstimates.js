import { isEmpty } from "lodash-es";
import Model from "./Model.js";

export default class priceEstimateModel extends Model {
	buildUrl(request) {
		const { params } = request;
		return ["price_estimates", ...params];
	}

	static async getDataAllStep(id = "") {
		return new this().request({
			method: "GET",
			url: `price_estimates/price-estimate-all-step/${id}`,
			isStatic: true
		});
	}
	static async submitStep1(data, id = "") {
		return new this().request({
			method: "POST",
			url: `price_estimates/step1-general-infomation/${id}`,
			data: data,
			isStatic: true
		});
	}
	static async submitStep2(data, id = "") {
		return new this().request({
			method: "POST",
			url: `price_estimates/step2-assets-infomation/${id}`,
			data: data,
			isStatic: true
		});
	}
	static async submitStep3(data, id = "") {
		return new this().request({
			method: "POST",
			url: `price_estimates/step3-final/${id}`,
			data: data,
			isStatic: true
		});
	}
	static async moveToAppraise(id = "") {
		return new this().request({
			method: "POST",
			url: `price_estimates/move-to-appraise/${id}`,
			isStatic: true
		});
	}
}
