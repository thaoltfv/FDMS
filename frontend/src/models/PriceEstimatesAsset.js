//
import Model from "./Model.js";

export default class PriceEstimatesAsset extends Model {
	buildUrl(request) {
		const { params } = request;
		return ["price_estimates_for_pre_certificate", ...params];
	}
}
