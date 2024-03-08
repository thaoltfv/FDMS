<?php

namespace App\Services;

use App\Models\AppraisePrice;
use App\Models\CertificateAssetPrice;
use App\Models\CertificatePrice;
use App\Models\User;
use App\Models\Appraise;

use App\Enum\EstimateAssetDefault;
use App\Enum\ValueDefault;
use App\Models\ApartmentAsset;
use App\Models\AppraiseAdapter;
use App\Models\AppraiseAppraisalMethods;
use App\Models\AppraiseComparisonFactor;
use App\Models\AppraiseHasAsset;
use App\Models\AppraiseProperty;
use App\Models\Appraiser;
use App\Models\AppraiseUnitArea;
use App\Models\AppraiseUnitPrice;
use App\Models\Certificate;
use App\Models\CertificateHasAppraise;
use App\Models\CompareAssetGeneral;
use App\Models\Dictionary;
use App\Models\RealEstate;
use App\Notifications\BroadcastNotification;
use App\Repositories\EloquentCompareAssetGeneralRepository;
use App\Repositories\EloquentUserRepository;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Kreait\Firebase\RemoteConfig\DefaultValue;
use Log;
use PHPViet\NumberToWords\Transformer;
use Spatie\QueryBuilder\QueryBuilder;
use Ramsey\Uuid\Type\Decimal;
use Tymon\JWTAuth\Facades\JWTAuth;
use Rap2hpoutre\FastExcel\FastExcel;
use Spatie\Activitylog\Models\Activity;


class CommonService
{


	public function __construct()
	{
	}

	public static function getUserReport()
	{
		$jwt = JWTAuth::getToken();
		$eloquentUserRepository = new EloquentUserRepository(new User());
		$user = $eloquentUserRepository->validateToken($jwt);
		$str = $user->name;
		$str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
		$str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
		$str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
		$str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
		$str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
		$str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
		$str = preg_replace("/(đ)/", 'd', $str);
		$str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
		$str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
		$str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
		$str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
		$str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
		$str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
		$str = preg_replace("/(Đ)/", 'D', $str);
		$str = str_replace(' ', '', $str);

		return $str;
	}

	public static function isJSON($string)
	{
		return is_string($string) && is_array(json_decode($string, true)) ? true : false;
	}

	public static function convertNumberToWords($number)
	{
		$result = '';
		$transformer = new Transformer();
		if (is_numeric($number)) {
			$result = $transformer->toWords($number);
			$result = str_replace('  ', ' ', $result);
		}

		return self::mbUcfirst($result);
	}

	public static function roundPrice($total, $round = 0)
	{

		// if ($round > 0) {
		// 	$round = pow(10, $round);
		// 	$result = ceil($total / $round) * $round;
		// } elseif ($round == 0) {
		// 	$result = round($total, 0);
		// } else {
		// 	$round = pow(10, abs($round));
		// 	$result = floor($total / $round) * $round;
		// }
		$total = intval($total);
		$round = intval($round);
		if ($round > 0) {
			$round = pow(10, $round);
			$check_var = 5 * $round / 10;
			$devide_value = (float) $total / (float) $round;
			$check_part = ($devide_value - floor($devide_value)) * $round;
			if ($check_part > $check_var) {
				$result = ceil($total / $round) * $round;
			} else if ($check_part < $check_var) {
				$result = floor($total / $round) * $round;
			} else $result = round($total, 0);
			// dd($total,$round,$check_var,$devide_value,$check_part,$result);
		} else {
			$result = round($total, 0);
		}
		return $result;
	}

	public static function roundAssetPrice($asset, $number)
	{
		if (isset($asset->round_total) && ($asset->round_total <= 7 && $asset->round_total >= -7)) {
			return self::roundPrice($number, $asset->round_total);
		}
	}

	public static function roundCompositeAssetPrice($asset, $number)
	{
		if (isset($asset->round_composite) && ($asset->round_composite <= 7 && $asset->round_composite >= -7)) {
			return self::roundPrice($number, $asset->round_composite);
		}
	}

	public static function roundViolationAssetPrice($asset, $number)
	{
		if (isset($asset->round_violation_facility) && ($asset->round_violation_facility <= 7 && $asset->round_violation_facility >= -7)) {
			return self::roundPrice($number, $asset->round_violation_facility);
		}
	}

	public static function roundViolationCompositeAssetPrice($asset, $number)
	{
		if (isset($asset->round_violation_composite) && ($asset->round_violation_composite <= 7 && $asset->round_violation_composite >= -7)) {
			return self::roundPrice($number, $asset->round_violation_composite);
		}
	}

	public static function roundTotalAssetsPrice($asset, $number)
	{
		if (isset($asset->round_appraise_total) && ($asset->round_appraise_total <= 7 && $asset->round_appraise_total >= -7)) {
			return self::roundPrice($number, $asset->round_appraise_total);
		}
	}

	public static function roundCertificatePrice($certificate, $number)
	{
		if (isset($certificate->round_certificate_total) && ($certificate->round_certificate_total <= 7 && $certificate->round_certificate_total >= -7)) {
			if ($certificate->round_certificate_total > 0) {
				$round = pow(10, $certificate->round_certificate_total);
				$result = ceil($number / $round) * $round;
			} else if ($certificate->round_certificate_total == 0) {
				$result = round($number, 0);
			} else {
				$round = pow(10, abs($certificate->round_certificate_total));
				$result = floor($number / $round) * $round;
			}
		} else {
			$result = $number;
		}

		return $result;
	}

	public static function nl2br($string)
	{
		$result = str_replace("\n", '<w:br/>', $string);
		return $result;
	}

	public static function mbUcfirst($string, $encoding = "utf8")
	{
		if (!empty($string)) {
			$string = mb_strtolower($string);
			$firstChar = mb_substr($string, 0, 1, $encoding);
			$then = mb_substr($string, 1, null, $encoding);
			return mb_strtoupper($firstChar, $encoding) . $then;
		}
		return '';
	}

	public static function mbLcfirst($string, $encoding = "utf8")
	{
		$string = self::mbCaseTitle($string);
		$firstChar = mb_substr($string, 0, 1, $encoding);
		$then = mb_substr($string, 1, null, $encoding);
		return mb_strtolower($firstChar, $encoding) . $then;
	}

	public static function mbCaseTitle($string, $encoding = "utf8")
	{
		if (!empty($string)) {
			$result = mb_convert_case($string, MB_CASE_TITLE, $encoding);
			return $result;
		}
		return '';
	}

	public static function formatCompanyName($company) // format company name
	{
		$result = mb_strtoupper($company->name);
		$length = strlen($result);
		if ($length > 0) {
			// $result = substr_replace($result, " <w:br/>", $company->down_line, 0);
			$strCurrent = $result;
			$strToArray = (explode(" ", $strCurrent));
			$downLine = " <w:br/>";
			$countDowline = $company->down_line;
			$storeNumberCount = $countDowline;

			if (isset($strToArray) && isset($countDowline) && $length >= $countDowline) {
				// for( ; $storeNumberCount < count($strToArray); $storeNumberCount = $storeNumberCount + $countDowline + 1 )
				// {
				array_splice($strToArray, $storeNumberCount, 0, $downLine);
				// }
				$result =  implode(" ", $strToArray);
			}
		}
		return $result;
	}
	public static function downLineCompanyName($companyName, $countDowline) // format company name
	{
		$result = mb_strtoupper($companyName);
		$length = strlen($result);
		if ($length > 0) {
			$strCurrent = $result;
			$strToArray = (explode(" ", $strCurrent));
			$downLine = " <w:br/>";
			if (isset($strToArray) && isset($countDowline) && $length >= $countDowline) {
				array_splice($strToArray, $countDowline, 0, $downLine);
				$result =  implode(" ", $strToArray);
			}
		}
		return $result;
	}

	public static function getCertificateAssetPriceTotal($certificate) // don gia tong
	{
		$landAssetPriceTotal = 0;
		$tangibleAssetPriceTotal = 0;
		$otherAssetPriceTotal = 0;
		foreach ($certificate->appraises as $stt => $appraise) {
			$value1 = self::getLandAssetPriceTotal($appraise, true);
			$landAssetPriceTotal += $value1;
			self::setCertificateAssetPrice($appraise, $value1, 'land_asset_price');
			$value2 = self::getTangibleAssetPriceTotal($appraise);
			$tangibleAssetPriceTotal += $value2;
			self::setCertificateAssetPrice($appraise, $value2, 'tangible_asset_price');
			$value3 = self::getOtherAssetPriceTotal($appraise);
			$otherAssetPriceTotal += $value3;
			self::setCertificateAssetPrice($appraise, $value3, 'other_asset_price');
			self::setCertificateAssetPrice($appraise, $value1 + $value2 + $value3, 'total_asset_price');
		}

		self::setCertificatePrice($certificate, $landAssetPriceTotal, 'land_asset_price');
		self::setCertificatePrice($certificate, $tangibleAssetPriceTotal, 'tangible_asset_price');
		self::setCertificatePrice($certificate, $otherAssetPriceTotal, 'other_asset_price');
		self::setCertificatePrice($certificate, $landAssetPriceTotal + $tangibleAssetPriceTotal + $otherAssetPriceTotal, 'total_asset_price');
	}

	public static function setCertificatePrice($certificate, $value, $type) // don gia tong
	{
		$item = [
			'certificate_id' => $certificate->id,
			'slug' => $type,
			'value' => $value,
			'description' => '',
		];

		$appraisePriceId = null;
		$appraisePrice = CertificatePrice::where('certificate_id', $certificate->id)->where('slug', $type)->first();
		if (isset($appraisePrice)) {
			$appraisePriceId = $appraisePrice->id;
			$appraisePrice = new CertificatePrice($item);
			$appraisePriceId = QueryBuilder::for($appraisePrice)
				->updateOrInsert(['id' => $appraisePriceId], $appraisePrice->attributesToArray());
		} else {
			$appraisePrice = new CertificatePrice($item);
			$appraisePriceId = QueryBuilder::for(CertificatePrice::class)
				->insertGetId($appraisePrice->attributesToArray());
		}
	}

	public static function getCertificatePrice($certificate, $type)
	{
		$result = CertificatePrice::where('certificate_id', $certificate->id)->where('slug', $type)->first();
		return isset($result->value) ? round($result->value) : 0;
	}

	public static function setCertificateAssetPrice($appraise, $value, $type) // don gia tong
	{
		$item = [
			'appraise_id' => $appraise->id,
			'slug' => $type,
			'value' => $value,
			'description' => '',
		];
		$appraisePriceId = null;
		$appraisePrice = CertificateAssetPrice::where('appraise_id', $appraise->id)->where('slug', $type)->first();
		if (isset($appraisePrice)) {
			$appraisePriceId = $appraisePrice->id;
			$appraisePrice = new CertificateAssetPrice($item);
			$appraisePriceId = QueryBuilder::for($appraisePrice)
				->updateOrInsert(['id' => $appraisePriceId], $appraisePrice->attributesToArray());
		} else {
			$appraisePrice = new CertificateAssetPrice($item);
			$appraisePriceId = QueryBuilder::for(CertificateAssetPrice::class)
				->insertGetId($appraisePrice->attributesToArray());
		}
	}

	public static function getCertificateAssetPrice($appraise, $type)
	{
		$result = CertificateAssetPrice::where('appraise_id', $appraise->id)->where('slug', $type)->first();
		return isset($result->value) ? $result->value : 0;
	}

	public static function getAssetPriceTotal($appraise) // don gia tong
	{
		//tính giá đất
		$landAssetPriceTotal = self::getLandAssetPriceTotal($appraise);
		// cập nhật giá vào bảng Appraise_prices với slug = 'land_asset_price'
		self::setAppraisePrice($appraise, $landAssetPriceTotal, 'land_asset_price');
		//tính giá nhà
		$tangibleAssetPriceTotal = self::getTangibleAssetPriceTotal($appraise);
		// cập nhật giá nhà vào bảng appraise_prices với slug ='tangible_asset_price'
		self::setAppraisePrice($appraise, $tangibleAssetPriceTotal, 'tangible_asset_price');
		// tính giá TS khác
		$otherAssetPriceTotal = self::getOtherAssetPriceTotal($appraise);
		// cập nhật giá TS khác vào bảng appraise_prices với slug ='other_asset_price'
		self::setAppraisePrice($appraise, $otherAssetPriceTotal, 'other_asset_price');
		//tính Tổng giá
		$assetPriceTotal = $landAssetPriceTotal + $tangibleAssetPriceTotal + $otherAssetPriceTotal;
		//cập nhật giá tổng vào bảng appraise_prices với slug ='total_asset_price'
		self::setAppraisePrice($appraise, $assetPriceTotal, 'total_asset_price');
	}

	public static function setAppraisePrice($appraise, $value, $type) // don gia tong
	{
		$item = [
			'appraise_id' => $appraise->id,
			'slug' => $type,
			'value' => $value,
			'description' => '',
		];
		$appraisePriceId = null;
		$appraisePrice = AppraisePrice::where('appraise_id', $appraise->id)->where('slug', $type)->first();
		if (isset($appraisePrice)) {
			$appraisePriceId = $appraisePrice->id;
			$appraisePrice = new AppraisePrice($item);
			$appraisePriceId = QueryBuilder::for($appraisePrice)
				->updateOrInsert(['id' => $appraisePriceId], $appraisePrice->attributesToArray());
		} else {
			$appraisePrice = new AppraisePrice($item);
			$appraisePriceId = QueryBuilder::for(AppraisePrice::class)
				->insertGetId($appraisePrice->attributesToArray());
		}
	}

	public static function getAppraisePrice($appraise, $type)
	{
		$result = AppraisePrice::where('appraise_id', $appraise->id)->where('slug', $type)->first();
		return isset($result->value) ? $result->value : 0;
	}

	public static function getOtherAssetPriceTotal($appraise) //don gia tài sản khác
	{
		$otherAssetTotal = 0;
		if (isset($appraise->otherAssets) && count($appraise->otherAssets)) {
			foreach ($appraise->otherAssets as $index => $otherAsset) {
				$otherAssetTotal += $otherAsset->total_price;
			}
		}

		return $otherAssetTotal;
	}
	public static function getClclChoosed($tangibleAsset, $appraisalCLCL)
	{
		if (!empty($appraisalCLCL)) {
			if ($appraisalCLCL->slug_value == 'trung-binh-cong') {
				$clcl =  self::getClclV2($tangibleAsset);
			} elseif ($appraisalCLCL->slug_value == 'chuyen-gia') {
				$clcl =  self::getClcl2($tangibleAsset);
			} else {
				$clcl =  $tangibleAsset->remaining_quality ?? 0;
			}
		} else {
			$clcl = self::getClclV2($tangibleAsset);
		}
		return $clcl;
	}
	public static function getDgxdChoosed($tangibleAsset, $appraisalDgxd)
	{
		$unitPrice = 0;
		if (!empty($appraisalDgxd) && $appraisalDgxd->slug_value == 'dg-quyet-dinh')
			$unitPrice = $tangibleAsset->total_desicion_average;
		else
			$unitPrice =  self::getTangibleAssetPriceV2($tangibleAsset);
		return $unitPrice;
	}
	public static function getTangibleAssetPriceTotal($appraise) //tổng đơn giá nhà
	{
		$tangibleAssetTotal = 0;
		if (isset($appraise->tangibleAssets) && count($appraise->tangibleAssets)) {
			$appraisal = AppraiseAppraisalMethods::query()->where('appraise_id', $appraise->id)->whereIn('slug', ['XAC_DINH_DON_GIA_XAY_DUNG', 'XAC_DINH_CHAT_LUONG_CON_LAI'])->get();

			foreach ($appraise->tangibleAssets as $index => $tangibleAsset) {
				if (!empty($appraisal) && count($appraisal) > 0) {
					$appraisalDGXD = $appraisal->where('slug', 'XAC_DINH_DON_GIA_XAY_DUNG')->first();
					$appraisalCLCL = $appraisal->where('slug', 'XAC_DINH_CHAT_LUONG_CON_LAI')->first();
					if (!empty($appraisalDGXD) && $appraisalDGXD->slug_value == 'dg-quyet-dinh')
						$unitPrice = $tangibleAsset->total_desicion_average;
					else
						$unitPrice =  self::getTangibleAssetPriceV2($tangibleAsset);

					if (!empty($appraisalCLCL) && $appraisalCLCL->slug_value == 'trung-binh-cong') {
						$clcl =  self::getClclV2($tangibleAsset);
					} elseif (!empty($appraisalCLCL) && $appraisalCLCL->slug_value == 'chuyen-gia') {
						$clcl =  self::getClcl2($tangibleAsset);
					} else {
						$clcl =  $tangibleAsset->remaining_quality ?? 0;
					}
				} else {
					$unitPrice =  self::getTangibleAssetPriceV2($tangibleAsset);
					$clcl =  self::getClclV2($tangibleAsset);
					$building_type = (isset($tangibleAsset->buildingType) && isset($tangibleAsset->buildingType->description)) ? $tangibleAsset->buildingType->description : '';
				}
				$total = (round($tangibleAsset->total_construction_base * $clcl / 100 * $unitPrice));
				$tangibleAssetTotal += $total;
			}
		}

		return $tangibleAssetTotal;
	}

	public static function getTangibleAssetPrice($appraise) //đơn giá nhà
	{
		$constructionCompany1 = $appraise->constructionCompany[0] ?? null;
		$constructionCompany2 = $appraise->constructionCompany[1] ?? null;
		$constructionCompany3 = $appraise->constructionCompany[2] ?? null;

		$unitPrice1 = isset($constructionCompany1) ? $constructionCompany1->unit_price_m2 : 0;
		$unitPrice2 = isset($constructionCompany2) ? $constructionCompany2->unit_price_m2 : 0;
		$unitPrice3 = isset($constructionCompany3) ? $constructionCompany3->unit_price_m2 : 0;

		// return round((($unitPrice1 + $unitPrice2 + $unitPrice3) / 3), -5, PHP_ROUND_HALF_DOWN);
		return self::roundPrice((($unitPrice1 + $unitPrice2 + $unitPrice3) / 3));
	}

	public static function getTangibleAssetPriceV2($tangibleAsset) //đơn giá nhà
	{
		$constructionCompany1 = $tangibleAsset->constructionCompany[0] ?? null;
		$constructionCompany2 = $tangibleAsset->constructionCompany[1] ?? null;
		$constructionCompany3 = $tangibleAsset->constructionCompany[2] ?? null;

		$unitPrice1 = isset($constructionCompany1) ? $constructionCompany1->unit_price_m2 : 0;
		$unitPrice2 = isset($constructionCompany2) ? $constructionCompany2->unit_price_m2 : 0;
		$unitPrice3 = isset($constructionCompany3) ? $constructionCompany3->unit_price_m2 : 0;

		// return round((($unitPrice1 + $unitPrice2 + $unitPrice3) / 3), -5, PHP_ROUND_HALF_DOWN);
		return self::roundPrice((($unitPrice1 + $unitPrice2 + $unitPrice3) / 3));
	}

	public static function getSelectedTangibleAssetPrice($tangibleAsset, $method) //đơn giá nhà
	{
		$unitPrice = 0;
		if ($method == 'dg-quyet-dinh')
			$unitPrice = $tangibleAsset->total_desicion_average;
		else
			$unitPrice =  self::getTangibleAssetPriceV2($tangibleAsset);

		return $unitPrice;
	}
	public static function getSelectedRemain($tangibleAsset, $method)
	{
		$clcl =  $tangibleAsset->remaining_quality ?? 0;
		if ($method == 'trung-binh-cong') {
			$clcl =  self::getClclV2($tangibleAsset);
		} elseif ($method == 'chuyen-gia') {
			$clcl =  self::getClcl2($tangibleAsset);
		}
		return $clcl;
	}

	public static function getClcl($appraise)
	{
		$comparisonTangibleFactor = $appraise->comparisonTangibleFactor[0] ?? null;
		$p1 = $comparisonTangibleFactor->p1 ?? 0;
		$h1 = $comparisonTangibleFactor->h1 ?? 0;
		$p2 = $comparisonTangibleFactor->p2 ?? 0;
		$h2 = $comparisonTangibleFactor->h2 ?? 0;
		$p3 = $comparisonTangibleFactor->p3 ?? 0;
		$h3 = $comparisonTangibleFactor->h3 ?? 0;
		$d4 = $comparisonTangibleFactor->d4 ?? 0;
		$h4 = $comparisonTangibleFactor->h4 ?? 0;
		$p5 = $comparisonTangibleFactor->p5 ?? 0;
		$h5 = $comparisonTangibleFactor->h5 ?? 0;

		$clcl1 = $appraise->tangibleAssets[0]->remaining_quality ?? 0;
		$clcl2 = ($p1 + $p2 + $p3 + $d4 + $p5) != 0 ? round(($p1 * $h1 + $p2 * $h2 + $p3 * $h3 + $d4 * $h4 + $p5 * $h5) / ($p1 + $p2 + $p3 + $d4 + $p5), 0) : 0;

		return round(($clcl1 + $clcl2) / 2);
	}

	public static function getClcl2($tangibleAsset)
	{
		$comparisonTangibleFactor = $tangibleAsset->comparisonTangibleFactor ?? null;
		$p1 = $comparisonTangibleFactor->p1 ?? 0;
		$h1 = $comparisonTangibleFactor->h1 ?? 0;
		$p2 = $comparisonTangibleFactor->p2 ?? 0;
		$h2 = $comparisonTangibleFactor->h2 ?? 0;
		$p3 = $comparisonTangibleFactor->p3 ?? 0;
		$h3 = $comparisonTangibleFactor->h3 ?? 0;
		$d4 = $comparisonTangibleFactor->d4 ?? 0;
		$h4 = $comparisonTangibleFactor->h4 ?? 0;
		$p5 = $comparisonTangibleFactor->p5 ?? 0;
		$h5 = $comparisonTangibleFactor->h5 ?? 0;

		$clcl2 = ($p1 + $p2 + $p3 + $d4 + $p5) != 0 ? round(($p1 * $h1 + $p2 * $h2 + $p3 * $h3 + $d4 * $h4 + $p5 * $h5) / ($p1 + $p2 + $p3 + $d4 + $p5), 0) : 0;
		return $clcl2;
	}
	public static function getClclV2($tangibleAsset)
	{
		$comparisonTangibleFactor = $tangibleAsset->comparisonTangibleFactor ?? null;
		$p1 = $comparisonTangibleFactor->p1 ?? 0;
		$h1 = $comparisonTangibleFactor->h1 ?? 0;
		$p2 = $comparisonTangibleFactor->p2 ?? 0;
		$h2 = $comparisonTangibleFactor->h2 ?? 0;
		$p3 = $comparisonTangibleFactor->p3 ?? 0;
		$h3 = $comparisonTangibleFactor->h3 ?? 0;
		$d4 = $comparisonTangibleFactor->d4 ?? 0;
		$h4 = $comparisonTangibleFactor->h4 ?? 0;
		$p5 = $comparisonTangibleFactor->p5 ?? 0;
		$h5 = $comparisonTangibleFactor->h5 ?? 0;

		$clcl1 = $tangibleAsset->remaining_quality ?? 0;
		$clcl2 = ($p1 + $p2 + $p3 + $d4 + $p5) != 0 ? round(($p1 * $h1 + $p2 * $h2 + $p3 * $h3 + $d4 * $h4 + $p5 * $h5) / ($p1 + $p2 + $p3 + $d4 + $p5), 0) : 0;

		return round(($clcl1 + $clcl2) / 2);
	}
	public static function getLandAssetPriceTotal($appraise, $isCertificateAsset = false) //don gia dat
	{
		$propertyDetailTotal = 0;
		$totalArea = 0;
		//echo '<pre>';
		foreach ($appraise->properties as $property) {
			foreach ($property->propertyDetail as $item) {

				$totalArea += $item->total_area;
				$landTypePurpose = (isset($item->landTypePurpose) && isset($item->landTypePurpose->acronym)) ? $item->landTypePurpose->acronym : '';
				if ($item->is_zoning) {

					$round = $appraise->assetPrice->where('slug', 'land_asset_purpose_' . $landTypePurpose . '_violation_round')->first();
					$round = isset($round->value) ? $round->value : 0;

					$donGiaDat = self::getPlanViolationAssetPrice($appraise, $item, $isCertificateAsset);
					if ($isCertificateAsset) {
						if ($item->is_transfer_facility || !isset($appraise->composite_land_remaning_slug) || ($appraise->composite_land_remaning_slug != 'theo-phuong-phap-doc-lap')) {
							self::setCertificateAssetPrice($appraise, $donGiaDat, 'land_asset_purpose_' . $landTypePurpose . '_violation_price');
							self::setCertificateAssetPrice($appraise, $item->planning_area, 'land_asset_purpose_' . $landTypePurpose . '_violation_area');
							self::setCertificateAssetPrice($appraise, $round, 'land_asset_purpose_' . $landTypePurpose . '_violation_round');
						}
					} else {
						self::setAppraisePrice($appraise, $donGiaDat, 'land_asset_purpose_' . $landTypePurpose . '_violation_price');
						self::setAppraisePrice($appraise, $item->planning_area, 'land_asset_purpose_' . $landTypePurpose . '_violation_area');
						self::setAppraisePrice($appraise, $round, 'land_asset_purpose_' . $landTypePurpose . '_violation_round');
					}

					$donGiaDatRound = self::roundPrice($donGiaDat, $round);
					$total = self::roundPrice($item->planning_area * $donGiaDatRound);

					$propertyDetailTotal += $total;
				}

				if (!$item->is_zoning || $item->total_area -  $item->planning_area >= 0) {
					$round = $appraise->assetPrice->where('slug', 'land_asset_purpose_' . $landTypePurpose . '_round')->first();
					$round = isset($round->value) ? $round->value : 0;

					$donGiaDat = self::getLandAssetPrice($appraise, $item, $isCertificateAsset);
					if ($isCertificateAsset) {
						if ($item->is_transfer_facility || !isset($appraise->composite_land_remaning_slug) || ($appraise->composite_land_remaning_slug != 'theo-phuong-phap-doc-lap')) {
							self::setCertificateAssetPrice($appraise, $donGiaDat, 'land_asset_purpose_' . $landTypePurpose . '_price');
							self::setCertificateAssetPrice($appraise, ($item->total_area -  $item->planning_area), 'land_asset_purpose_' . $landTypePurpose . '_area');
							self::setCertificateAssetPrice($appraise, $round, 'land_asset_purpose_' . $landTypePurpose . '_round');
						}
					} else {
						self::setAppraisePrice($appraise, $donGiaDat, 'land_asset_purpose_' . $landTypePurpose . '_price');
						self::setAppraisePrice($appraise, ($item->total_area -  $item->planning_area), 'land_asset_purpose_' . $landTypePurpose . '_area');
						self::setAppraisePrice($appraise, $round, 'land_asset_purpose_' . $landTypePurpose . '_round');
					}

					$donGiaDatRound = self::roundPrice($donGiaDat, $round);
					$total = self::roundPrice(($item->total_area -  $item->planning_area) * $donGiaDatRound);
					$propertyDetailTotal += $total;
				}
			}
		}

		if ($isCertificateAsset) {
			self::setCertificateAssetPrice($appraise, $totalArea, 'total_asset_area');
		} else {
			self::setAppraisePrice($appraise, $totalArea, 'total_asset_area');
		}

		return $propertyDetailTotal;
	}

	public static function getCPCDMDSD($assetId, $assetGeneralId) //don gia dat
	{
		$result = Appraise::where('id', '=', $assetId)
			->with('appraiseHasAssets')
			->with('assetUnitPrice')
			->first();
		$result->append('asset_general');
		$asset = $result;
		$asset->assetGeneral = $asset->asset_general;

		if (!empty($asset->assetGeneral)) {
			$assetGeneral =  null;
			$assetGeneralDetail =  null;
			foreach ($asset->assetGeneral as $item) {
				if (isset($item->id) && ($item->id == $assetGeneralId)) {
					$assetGeneral = $item;
				}
			}
		}

		if (isset($assetGeneral->id)) {
			foreach ($asset->appraiseHasAssets as $appraiseHasAssets) {
				if ($appraiseHasAssets->asset_general_id == $assetGeneral->id) {
					foreach ($assetGeneral->properties as $properties) {
						if ($properties->id == $appraiseHasAssets->asset_property_detail_id) {
							$assetGeneralDetail = $properties;
						}
					}
				}
			}


			$baseAcronym = '';
			$baseUnitPrice = 0;
			$baseAcronymId = '';
			$basePositionTypeId = '';
			if ((isset($asset->properties[0])) && (count($asset->properties[0]->propertyDetail) == 1)) {
				$baseUnitPrice = $asset->properties[0]->propertyDetail[0]->circular_unit_price;
				$baseAcronym = $asset->properties[0]->propertyDetail[0]->landTypePurpose->acronym;
				$baseAcronymId = $asset->properties[0]->propertyDetail[0]->land_type_purpose_id;
				$basePositionTypeId = $asset->properties[0]->propertyDetail[0]->position_type_id;
			} else {
				foreach ($asset->properties[0]->propertyDetail as $index => $item) {
					if ($item->is_transfer_facility || !$index) {
						$baseUnitPrice = $item->circular_unit_price;
						$baseAcronym = $item->landTypePurpose->acronym;
						$baseAcronymId = $item->land_type_purpose_id;
						$basePositionTypeId = $item->position_type_id;
					}
				}
			}

			$baseUnitPrice = floatval($baseUnitPrice);
			$assetUnitPrices = [];
			$assetViolateAreas = [];
			$assetViolateAreasTotal = [];
			if (isset($asset->assetUnitPrice)) {
				foreach ($asset->assetUnitPrice as $item) {
					if ($item->update == 2) {
						$assetUnitPrices[$item->appraise_id][$item->asset_general_id][$item->land_type_id] = $item->update_value;
					} else {
						$assetUnitPrices[$item->appraise_id][$item->asset_general_id][$item->land_type_id] = $item->original_value;
					}
				}
			}
			if (isset($asset->assetUnitArea)) {
				foreach ($asset->assetUnitArea as $item) {
					$assetViolateAreas[$item->appraise_id][$item->asset_general_id][$item->land_type_id][$item->position_type_id] = $item->violation_asset_area;
					if (isset($assetViolateAreasTotal[$item->appraise_id][$item->asset_general_id])) {
						$assetViolateAreasTotal[$item->appraise_id][$item->asset_general_id] += $item->violation_asset_area;
					} else {
						$assetViolateAreasTotal[$item->appraise_id][$item->asset_general_id] = $item->violation_asset_area;
					}
				}
			}

			$cpcdmdsd = 0;
			if (isset($assetGeneralDetail->propertyDetail)) {
				foreach ($assetGeneralDetail->propertyDetail as $item) {
					if (isset($item->landTypePurposeData->acronym) && $item->landTypePurposeData->acronym != $baseAcronym) {
						$circularUnitPrice = (isset($assetUnitPrices[$asset->id][$assetGeneralDetail->asset_general_id][$item->land_type_purpose])) ? $assetUnitPrices[$asset->id][$assetGeneralDetail->asset_general_id][$item->land_type_purpose] : $item->circular_unit_price;
						$circularBaseUnitPrice = (isset($assetUnitPrices[$asset->id][$assetGeneralDetail->asset_general_id][$baseAcronymId])) ? $assetUnitPrices[$asset->id][$assetGeneralDetail->asset_general_id][$baseAcronymId] : $baseUnitPrice;

						$unitPrice = floatval($circularBaseUnitPrice) - floatval($circularUnitPrice);

						$area = $item->total_area;
						$areaViolate = (isset($assetViolateAreas[$asset->id][$assetGeneralDetail->asset_general_id][$item->land_type_purpose][$item->position_type_id])) ? $assetViolateAreas[$asset->id][$assetGeneralDetail->asset_general_id][$item->land_type_purpose][$item->position_type_id] : 0;
						$area -= $areaViolate;

						$cpcdmdsd += $unitPrice * floatval($area);
					}
				}
			}

			return $cpcdmdsd;
		}

		return 0;
	}

	public static function getLandAssetPrice($asset, $curPropertyDetail, $isCertificateAsset = false) //don gia dat
	{
		$asset1 = $asset->assetGeneral[0] ?? null;
		$asset2 = $asset->assetGeneral[1] ?? null;
		$asset3 = $asset->assetGeneral[2] ?? null;

		$detail1 = $asset1->properties[0] ?? null;
		$detail2 = $asset2->properties[0] ?? null;
		$detail3 = $asset3->properties[0] ?? null;

		$asset1TotalViolateAmount = 0;
		$asset2TotalViolateAmount = 0;
		$asset3TotalViolateAmount = 0;

		foreach ($asset->appraiseHasAssets as $appraiseHasAssets) {
			if ($appraiseHasAssets->asset_general_id == $asset1->id) {
				foreach ($asset1->properties as $properties) {
					if ($properties->id == $appraiseHasAssets->asset_property_detail_id) {
						$detail1 = $properties;
					}
				}
			}
			if ($appraiseHasAssets->asset_general_id == $asset2->id) {
				foreach ($asset2->properties as $properties) {
					if ($properties->id == $appraiseHasAssets->asset_property_detail_id) {
						$detail2 = $properties;
					}
				}
			}
			if ($appraiseHasAssets->asset_general_id == $asset3->id) {
				foreach ($asset3->properties as $properties) {
					if ($properties->id == $appraiseHasAssets->asset_property_detail_id) {
						$detail3 = $properties;
					}
				}
			}
		}

		$dtsxd1 = $asset1->tangibleAssets[0]->total_construction_base ?? 0;
		$dtsxd2 = $asset2->tangibleAssets[0]->total_construction_base ?? 0;
		$dtsxd3 = $asset3->tangibleAssets[0]->total_construction_base ?? 0;

		$dgxd1 = isset($asset1->tangibleAssets[0]) ? $asset1->tangibleAssets[0]->unit_price_m2 : 0;
		$dgxd2 = isset($asset2->tangibleAssets[0]) ? $asset2->tangibleAssets[0]->unit_price_m2 : 0;
		$dgxd3 = isset($asset3->tangibleAssets[0]) ? $asset3->tangibleAssets[0]->unit_price_m2 : 0;

		$clcl1 = $asset1->tangibleAssets[0]->remaining_quality ?? 0;
		$clcl2 = $asset2->tangibleAssets[0]->remaining_quality ?? 0;
		$clcl3 = $asset3->tangibleAssets[0]->remaining_quality ?? 0;

		$otherAssetPrice1 = $asset1->total_order_amount ?? 0;
		$otherAssetPrice2 = $asset2->total_order_amount ?? 0;
		$otherAssetPrice3 = $asset3->total_order_amount ?? 0;

		$baseAcronym = '';
		$baseUnitPrice = 0;
		$baseAcronymId = '';
		$basePositionTypeId = '';
		if ((isset($asset->properties[0])) && (count($asset->properties[0]->propertyDetail) == 1)) {
			$baseUnitPrice = $asset->properties[0]->propertyDetail[0]->circular_unit_price;
			$baseAcronym = $asset->properties[0]->propertyDetail[0]->landTypePurpose->acronym;
			$baseAcronymId = $asset->properties[0]->propertyDetail[0]->land_type_purpose_id;
			$basePositionTypeId = $asset->properties[0]->propertyDetail[0]->position_type_id;
		} else {
			foreach ($asset->properties[0]->propertyDetail as $index => $item) {
				if ($item->is_transfer_facility || !$index) {
					$baseUnitPrice = $item->circular_unit_price;
					$baseAcronym = $item->landTypePurpose->acronym;
					$baseAcronymId = $item->land_type_purpose_id;
					$basePositionTypeId = $item->position_type_id;
				}
			}
		}
		$baseUnitPrice = floatval($baseUnitPrice);
		$assetUnitPrices = [];
		$assetViolateAreas = [];
		$assetViolateAreasTotal = [];
		if (isset($asset->assetUnitPrice)) {
			foreach ($asset->assetUnitPrice as $item) {
				if ($item->update == 2) {
					$assetUnitPrices[$item->appraise_id][$item->asset_general_id][$item->land_type_id] = $item->update_value;
				} else {
					$assetUnitPrices[$item->appraise_id][$item->asset_general_id][$item->land_type_id] = $item->original_value;
				}
			}
		}
		if (isset($asset->assetUnitArea)) {
			foreach ($asset->assetUnitArea as $item) {
				$assetViolateAreas[$item->appraise_id][$item->asset_general_id][$item->land_type_id][$item->position_type_id] = $item->violation_asset_area;
				if (isset($assetViolateAreasTotal[$item->appraise_id][$item->asset_general_id])) {
					$assetViolateAreasTotal[$item->appraise_id][$item->asset_general_id] += $item->violation_asset_area;
				} else {
					$assetViolateAreasTotal[$item->appraise_id][$item->asset_general_id] = $item->violation_asset_area;
				}
			}
		}

		if (isset($detail1->propertyDetail)) {
			foreach ($detail1->propertyDetail as $item) {
				$circularUnitPrice = (isset($assetUnitPrices[$asset->id][$detail1->asset_general_id][$item->land_type_purpose][$item->position_type_id])) ? $assetUnitPrices[$asset->id][$detail1->asset_general_id][$item->land_type_purpose][$item->position_type_id] : $item->circular_unit_price;

				$areaViolate = (isset($assetViolateAreas[$asset->id][$detail1->asset_general_id][$item->land_type_purpose][$item->position_type_id])) ? $assetViolateAreas[$asset->id][$detail1->asset_general_id][$item->land_type_purpose][$item->position_type_id] : 0;

				// $asset1TotalViolateAmount += (floatval($circularUnitPrice)*$areaViolate);
			}
		}
		if (isset($detail2->propertyDetail)) {
			foreach ($detail2->propertyDetail as $item) {
				$circularUnitPrice = (isset($assetUnitPrices[$asset->id][$detail2->asset_general_id][$item->land_type_purpose][$item->position_type_id])) ? $assetUnitPrices[$asset->id][$detail2->asset_general_id][$item->land_type_purpose][$item->position_type_id] : $item->circular_unit_price;

				$areaViolate = (isset($assetViolateAreas[$asset->id][$detail2->asset_general_id][$item->land_type_purpose][$item->position_type_id])) ? $assetViolateAreas[$asset->id][$detail2->asset_general_id][$item->land_type_purpose][$item->position_type_id] : 0;

				// $asset2TotalViolateAmount += (floatval($circularUnitPrice)*$areaViolate);
			}
		}
		if (isset($detail3->propertyDetail)) {
			foreach ($detail3->propertyDetail as $item) {
				$circularUnitPrice = (isset($assetUnitPrices[$asset->id][$detail3->asset_general_id][$item->land_type_purpose][$item->position_type_id])) ? $assetUnitPrices[$asset->id][$detail3->asset_general_id][$item->land_type_purpose][$item->position_type_id] : $item->circular_unit_price;

				$areaViolate = (isset($assetViolateAreas[$asset->id][$detail3->asset_general_id][$item->land_type_purpose][$item->position_type_id])) ? $assetViolateAreas[$asset->id][$detail3->asset_general_id][$item->land_type_purpose][$item->position_type_id] : 0;

				// $asset3TotalViolateAmount += (floatval($circularUnitPrice)*$areaViolate);
			}
		}

		$asset1AdjustPercent = 0;
		$asset2AdjustPercent = 0;
		$asset3AdjustPercent = 0;

		$asset1TotalEstimateAmount = 0;
		$asset2TotalEstimateAmount = 0;
		$asset3TotalEstimateAmount = 0;

		$changePurposePrice1 = null;
		$changePurposePrice2 = null;
		$changePurposePrice3 = null;

		$assetNotViolateAreaTotal1 = isset($detail1->asset_general_land_sum_area) ? $detail1->asset_general_land_sum_area : 0;
		$assetNotViolateAreaTotal1 -= isset($assetViolateAreasTotal[$asset->id][$detail1->asset_general_id]) ? $assetViolateAreasTotal[$asset->id][$detail1->asset_general_id] : 0;
		$assetNotViolateAreaTotal2 = isset($detail2->asset_general_land_sum_area) ? $detail2->asset_general_land_sum_area : 0;
		$assetNotViolateAreaTotal2 -= isset($assetViolateAreasTotal[$asset->id][$detail2->asset_general_id]) ? $assetViolateAreasTotal[$asset->id][$detail2->asset_general_id] : 0;
		$assetNotViolateAreaTotal3 = isset($detail3->asset_general_land_sum_area) ? $detail3->asset_general_land_sum_area : 0;
		$assetNotViolateAreaTotal3 -= isset($assetViolateAreasTotal[$asset->id][$detail3->asset_general_id]) ? $assetViolateAreasTotal[$asset->id][$detail3->asset_general_id] : 0;


		foreach ($asset->appraiseAdapter as $item) {
			if ($item['asset_general_id'] == $asset1->id) {
				$asset1AdjustPercent = $item['percent'];
				$changePurposePrice1 = isset($item['change_purpose_price']) ? $item['change_purpose_price'] : null;
				$asset1TotalViolateAmount = isset($item['change_violate_price']) ? $item['change_violate_price'] : 0;
				$asset1TotalEstimateAmount = $asset1->total_amount * $asset1AdjustPercent / 100;
			}
			if ($item['asset_general_id'] == $asset2->id) {
				$asset2AdjustPercent = $item['percent'];
				$changePurposePrice2 = isset($item['change_purpose_price']) ? $item['change_purpose_price'] : null;
				$asset2TotalViolateAmount = isset($item['change_violate_price']) ? $item['change_violate_price'] : 0;
				$asset2TotalEstimateAmount = $asset2->total_amount * $asset2AdjustPercent / 100;
			}
			if ($item['asset_general_id'] == $asset3->id) {
				$asset3AdjustPercent = $item['percent'];
				$changePurposePrice3 = isset($item['change_purpose_price']) ? $item['change_purpose_price'] : null;
				$asset3TotalViolateAmount = isset($item['change_violate_price']) ? $item['change_violate_price'] : 0;
				$asset3TotalEstimateAmount = $asset3->total_amount * $asset3AdjustPercent / 100;
			}
		}

		$price1 = 0;
		if (isset($changePurposePrice1)) {
			$price1 = $changePurposePrice1;
		} else if (isset($detail1->propertyDetail)) {
			foreach ($detail1->propertyDetail as $item) {
				if (isset($item->landTypePurposeData->acronym) && $item->landTypePurposeData->acronym != $baseAcronym) {
					$circularUnitPrice = (isset($assetUnitPrices[$asset->id][$detail1->asset_general_id][$item->land_type_purpose][$item->position_type_id])) ? $assetUnitPrices[$asset->id][$detail1->asset_general_id][$item->land_type_purpose][$item->position_type_id] : $item->circular_unit_price;
					$circularBaseUnitPrice = (isset($assetUnitPrices[$asset->id][$detail1->asset_general_id][$baseAcronymId][$basePositionTypeId])) ? $assetUnitPrices[$asset->id][$detail1->asset_general_id][$baseAcronymId][$basePositionTypeId] : $baseUnitPrice;

					$unitPrice = floatval($circularBaseUnitPrice) - floatval($circularUnitPrice);

					$area = $item->total_area;
					$areaViolate = (isset($assetViolateAreas[$asset->id][$detail1->asset_general_id][$item->land_type_purpose][$item->position_type_id])) ? $assetViolateAreas[$asset->id][$detail1->asset_general_id][$item->land_type_purpose][$item->position_type_id] : 0;
					$area -= $areaViolate;

					$price1 += $unitPrice * floatval($area);
				}
			}
		}
		$price2 = 0;
		if (isset($changePurposePrice2)) {
			$price2 = $changePurposePrice2;
		} else if (isset($detail2->propertyDetail)) {
			foreach ($detail2->propertyDetail as $item) {
				if (isset($item->landTypePurposeData->acronym) && $item->landTypePurposeData->acronym != $baseAcronym) {
					$circularUnitPrice = (isset($assetUnitPrices[$asset->id][$detail2->asset_general_id][$item->land_type_purpose][$item->position_type_id])) ? $assetUnitPrices[$asset->id][$detail2->asset_general_id][$item->land_type_purpose][$item->position_type_id] : $item->circular_unit_price;
					$circularBaseUnitPrice = (isset($assetUnitPrices[$asset->id][$detail2->asset_general_id][$baseAcronymId][$basePositionTypeId])) ? $assetUnitPrices[$asset->id][$detail2->asset_general_id][$baseAcronymId][$basePositionTypeId] : $baseUnitPrice;

					$unitPrice = floatval($circularBaseUnitPrice) - floatval($circularUnitPrice);

					$area = $item->total_area;
					$areaViolate = (isset($assetViolateAreas[$asset->id][$detail2->asset_general_id][$item->land_type_purpose][$item->position_type_id])) ? $assetViolateAreas[$asset->id][$detail2->asset_general_id][$item->land_type_purpose][$item->position_type_id] : 0;
					$area -= $areaViolate;

					$price2 += $unitPrice * floatval($area);
				}
			}
		}
		$price3 = 0;
		if (isset($changePurposePrice3)) {
			$price3 = $changePurposePrice3;
		} else if (isset($detail3->propertyDetail)) {
			foreach ($detail3->propertyDetail as $item) {
				if (isset($item->landTypePurposeData->acronym) && $item->landTypePurposeData->acronym != $baseAcronym) {
					$circularUnitPrice = (isset($assetUnitPrices[$asset->id][$detail3->asset_general_id][$item->land_type_purpose][$item->position_type_id])) ? $assetUnitPrices[$asset->id][$detail3->asset_general_id][$item->land_type_purpose][$item->position_type_id] : $item->circular_unit_price;
					$circularBaseUnitPrice = (isset($assetUnitPrices[$asset->id][$detail3->asset_general_id][$baseAcronymId][$basePositionTypeId])) ? $assetUnitPrices[$asset->id][$detail3->asset_general_id][$baseAcronymId][$basePositionTypeId] : $baseUnitPrice;

					$unitPrice = floatval($circularBaseUnitPrice) - floatval($circularUnitPrice);

					$area = $item->total_area;
					$areaViolate = (isset($assetViolateAreas[$asset->id][$detail3->asset_general_id][$item->land_type_purpose][$item->position_type_id])) ? $assetViolateAreas[$asset->id][$detail3->asset_general_id][$item->land_type_purpose][$item->position_type_id] : 0;
					$area -= $areaViolate;

					$price3 += $unitPrice * floatval($area);
				}
			}
		}


		$price1 = 0;
		if (isset($changePurposePrice1)) {
			$price1 = $changePurposePrice1;
		} else if (isset($detail1->propertyDetail)) {
			foreach ($detail1->propertyDetail as $item) {
				if (isset($item->landTypePurposeData->acronym) && $item->landTypePurposeData->acronym != $baseAcronym) {
					$circularUnitPrice = (isset($assetUnitPrices[$asset->id][$detail1->asset_general_id][$item->land_type_purpose])) ? $assetUnitPrices[$asset->id][$detail1->asset_general_id][$item->land_type_purpose] : $item->circular_unit_price;
					$circularBaseUnitPrice = (isset($assetUnitPrices[$asset->id][$detail1->asset_general_id][$baseAcronymId])) ? $assetUnitPrices[$asset->id][$detail1->asset_general_id][$baseAcronymId] : $baseUnitPrice;

					$unitPrice = floatval($circularBaseUnitPrice) - floatval($circularUnitPrice);

					$area = $item->total_area;
					$areaViolate = (isset($assetViolateAreas[$asset->id][$detail1->asset_general_id][$item->land_type_purpose][$item->position_type_id])) ? $assetViolateAreas[$asset->id][$detail1->asset_general_id][$item->land_type_purpose][$item->position_type_id] : 0;
					$area -= $areaViolate;

					$price1 += $unitPrice * floatval($area);
				}
			}
		}
		$price2 = 0;
		if (isset($changePurposePrice2)) {
			$price2 = $changePurposePrice2;
		} else if (isset($detail2->propertyDetail)) {
			foreach ($detail2->propertyDetail as $item) {
				if (isset($item->landTypePurposeData->acronym) && $item->landTypePurposeData->acronym != $baseAcronym) {
					$circularUnitPrice = (isset($assetUnitPrices[$asset->id][$detail2->asset_general_id][$item->land_type_purpose])) ? $assetUnitPrices[$asset->id][$detail2->asset_general_id][$item->land_type_purpose] : $item->circular_unit_price;
					$circularBaseUnitPrice = (isset($assetUnitPrices[$asset->id][$detail2->asset_general_id][$baseAcronymId])) ? $assetUnitPrices[$asset->id][$detail2->asset_general_id][$baseAcronymId] : $baseUnitPrice;

					$unitPrice = floatval($circularBaseUnitPrice) - floatval($circularUnitPrice);

					$area = $item->total_area;
					$areaViolate = (isset($assetViolateAreas[$asset->id][$detail2->asset_general_id][$item->land_type_purpose][$item->position_type_id])) ? $assetViolateAreas[$asset->id][$detail2->asset_general_id][$item->land_type_purpose][$item->position_type_id] : 0;
					$area -= $areaViolate;

					$price2 += $unitPrice * floatval($area);
				}
			}
		}
		$price3 = 0;
		if (isset($changePurposePrice3)) {
			$price3 = $changePurposePrice3;
		} else if (isset($detail3->propertyDetail)) {
			foreach ($detail3->propertyDetail as $item) {
				if (isset($item->landTypePurposeData->acronym) && $item->landTypePurposeData->acronym != $baseAcronym) {
					$circularUnitPrice = (isset($assetUnitPrices[$asset->id][$detail3->asset_general_id][$item->land_type_purpose])) ? $assetUnitPrices[$asset->id][$detail3->asset_general_id][$item->land_type_purpose] : $item->circular_unit_price;
					$circularBaseUnitPrice = (isset($assetUnitPrices[$asset->id][$detail3->asset_general_id][$baseAcronymId])) ? $assetUnitPrices[$asset->id][$detail3->asset_general_id][$baseAcronymId] : $baseUnitPrice;

					$unitPrice = floatval($circularBaseUnitPrice) - floatval($circularUnitPrice);

					$area = $item->total_area;
					$areaViolate = (isset($assetViolateAreas[$asset->id][$detail3->asset_general_id][$item->land_type_purpose][$item->position_type_id])) ? $assetViolateAreas[$asset->id][$detail3->asset_general_id][$item->land_type_purpose][$item->position_type_id] : 0;
					$area -= $areaViolate;

					$price3 += $unitPrice * floatval($area);
				}
			}
		}


		$totalPrice1 = ($asset1TotalEstimateAmount ?? 0) + $price1 - ($dtsxd1 * $dgxd1 * $clcl1 / 100) - $otherAssetPrice1 - $asset1TotalViolateAmount;
		$totalPrice2 = ($asset2TotalEstimateAmount ?? 0) + $price2 - ($dtsxd2 * $dgxd2 * $clcl2 / 100) - $otherAssetPrice2 - $asset2TotalViolateAmount;
		$totalPrice3 = ($asset3TotalEstimateAmount ?? 0) + $price3 - ($dtsxd3 * $dgxd3 * $clcl3 / 100) - $otherAssetPrice3 - $asset3TotalViolateAmount;
		$dgd1 = (isset($assetNotViolateAreaTotal1) && $assetNotViolateAreaTotal1) ? ($totalPrice1 / $assetNotViolateAreaTotal1) : 0;
		$dgd2 = (isset($assetNotViolateAreaTotal2) && $assetNotViolateAreaTotal2) ? ($totalPrice2 / $assetNotViolateAreaTotal2) : 0;
		$dgd3 = (isset($assetNotViolateAreaTotal3) && $assetNotViolateAreaTotal3) ? ($totalPrice3 / $assetNotViolateAreaTotal3) : 0;

		$comparisonFactor1 = [];
		$comparisonFactor2 = [];
		$comparisonFactor3 = [];
		$otherFactor1 = [];
		$otherFactor2 = [];
		$otherFactor3 = [];

		$totalPricePL1 = 0;
		$totalPricePL2 = 0;
		$totalPricePL3 = 0;

		foreach ($asset->comparisonFactor as $comparisonFactor) {
			if ($comparisonFactor->type != 'yeu_to_khac') {
				if ($asset1 && ($comparisonFactor->asset_general_id == $asset1->id)) {
					$comparisonFactor1[$comparisonFactor->type] = $comparisonFactor;
				}
				if ($asset2 && ($comparisonFactor->asset_general_id == $asset2->id)) {
					$comparisonFactor2[$comparisonFactor->type] = $comparisonFactor;
				}
				if ($asset3 && ($comparisonFactor->asset_general_id == $asset3->id)) {
					$comparisonFactor3[$comparisonFactor->type] = $comparisonFactor;
				}
			} else {
				if ($asset1 && ($comparisonFactor->asset_general_id == $asset1->id)) {
					$otherFactor1[] = $comparisonFactor;
				}
				if ($asset2 && ($comparisonFactor->asset_general_id == $asset2->id)) {
					$otherFactor2[] = $comparisonFactor;
				}
				if ($asset3 && ($comparisonFactor->asset_general_id == $asset3->id)) {
					$otherFactor3[] = $comparisonFactor;
				}
			}
		}

		if (isset($comparisonFactor1['phap_ly']) && $comparisonFactor1['phap_ly']->status == 1) {

			$percentPl1 = $comparisonFactor1['phap_ly']->adjust_percent ?? 0;
			$percentPl2 = $comparisonFactor2['phap_ly']->adjust_percent ?? 0;
			$percentPl3 = $comparisonFactor3['phap_ly']->adjust_percent ?? 0;

			$totalPricePL1 = $dgd1 * (1 + $percentPl1 / 100);
			$totalPricePL2 = $dgd2 * (1 + $percentPl2 / 100);
			$totalPricePL3 = $dgd3 * (1 + $percentPl3 / 100);
		}

		$mgcd1 = $totalPricePL1;
		$mgcd2 = $totalPricePL2;
		$mgcd3 = $totalPricePL3;

		$factors = EstimateAssetDefault::COMPARATION_FACTORS;
		unset($factors['yeu_to_khac']);
		foreach ($factors as $factor) {
			if ($factor !== 'phap_ly' && isset($comparisonFactor1[$factor]) && $comparisonFactor1[$factor]->status == 1) {
				$percent1 = $comparisonFactor1[$factor]->adjust_percent ?? 0;
				$percent2 = $comparisonFactor2[$factor]->adjust_percent ?? 0;
				$percent3 = $comparisonFactor3[$factor]->adjust_percent ?? 0;

				$mgcd1 += $percent1 * $totalPricePL1 / 100;
				$mgcd2 += $percent2 * $totalPricePL2 / 100;
				$mgcd3 += $percent3 * $totalPricePL3 / 100;
			}
		}
		foreach ($otherFactor1 as $factor => $item) {
			if (isset($otherFactor1[$factor]) && $otherFactor1[$factor]->status == 1) {
				$percent1 = $otherFactor1[$factor]->adjust_percent ?? 0;
				$percent2 = $otherFactor2[$factor]->adjust_percent ?? 0;
				$percent3 = $otherFactor3[$factor]->adjust_percent ?? 0;

				$mgcd1 += $percent1 * $totalPricePL1 / 100;
				$mgcd2 += $percent2 * $totalPricePL2 / 100;
				$mgcd3 += $percent3 * $totalPricePL3 / 100;
			}
		}

		$mgtb = count($asset->assetGeneral) > 0 ? (($mgcd1 + $mgcd2 + $mgcd3) / count($asset->assetGeneral)) : 0;
		if (isset($asset->unify_indicative_price_slug) && ($asset->unify_indicative_price_slug == 'thap-nhat')) {
			$mgtb = min($mgcd1, $mgcd2, $mgcd3);
		}
		if (isset($asset->unify_indicative_price_slug) && ($asset->unify_indicative_price_slug == 'cao-nhat')) {
			$mgtb = max($mgcd1, $mgcd2, $mgcd3);
		}

		if ($asset->layer_cutting_procedure) {
			$mgtbr = self::roundAssetPrice($asset, $asset->layer_cutting_price);
		} else {
			$mgtbr = self::roundAssetPrice($asset, $mgtb);
		}

		if ($curPropertyDetail->is_transfer_facility) {
			return $mgtb;
		} else {
			if (!isset($asset->composite_land_remaning_slug) || ($asset->composite_land_remaning_slug == 'theo-chi-phi-chuyen-mdsd-dat')) {
				return ($mgtbr - ($baseUnitPrice - floatval($curPropertyDetail->circular_unit_price)));
			}
			if (isset($asset->composite_land_remaning_slug) && ($asset->composite_land_remaning_slug == 'theo-ty-le-gia-dat-co-so-chinh')) {
				return ($mgtbr * $asset->composite_land_remaning_value / 100);
			}
			if (isset($asset->composite_land_remaning_slug) && ($asset->composite_land_remaning_slug == 'theo-phuong-phap-doc-lap')) {
				$landTypePurpose = (isset($curPropertyDetail->landTypePurpose) && isset($curPropertyDetail->landTypePurpose->acronym)) ? $curPropertyDetail->landTypePurpose->acronym : '';
				if ($isCertificateAsset) {
					return self::getCertificateAssetPrice($asset, 'land_asset_purpose_' . $landTypePurpose . '_price');
				} else {
					return self::getAppraisePrice($asset, 'land_asset_purpose_' . $landTypePurpose . '_price');
				}
			}
		}
	}

	public static function getPlanViolationAssetPrice($asset, $curPropertyDetail, $isCertificateAsset = false) //don gia dat
	{
		if (!isset($asset->planning_violation_price_slug) || ($asset->planning_violation_price_slug == 'theo-gia-dat-qd-ubnd')) {
			return $curPropertyDetail->circular_unit_price ?? 0;
		}

		if (isset($asset->planning_violation_price_slug) && ($asset->planning_violation_price_slug == 'theo-ty-le-gia-dat-thi-truong')) {
			if ($asset->layer_cutting_procedure && $curPropertyDetail->is_transfer_facility) {
				$donGiaDat = $asset->layer_cutting_price;
			} else {
				$donGiaDat = self::getLandAssetPrice($asset, $curPropertyDetail, $isCertificateAsset);
			}
			if (!$curPropertyDetail->is_transfer_facility) {
				$donGiaDatRound = self::roundCompositeAssetPrice($asset, $donGiaDat);
			} else {
				$donGiaDatRound = self::roundAssetPrice($asset, $donGiaDat);
			}

			return $donGiaDatRound * $asset->planning_violation_price_value / 100;
		}
	}

	public static function withoutAccents($str = null)
	{
		$str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
		$str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
		$str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
		$str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
		$str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
		$str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
		$str = preg_replace("/(đ)/", 'd', $str);
		$str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
		$str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
		$str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
		$str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
		$str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
		$str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
		$str = preg_replace("/(Đ)/", 'D', $str);
		$str = preg_replace("/ /", '', $str);
		return $str;
	}

	public static function calTotal($Total, $round)
	{
		if ($round > 0) {
			$round = pow(10, $round);
			$result = ceil($Total / $round) * $round;
		} else {
			$round = pow(10, abs($round));
			$result = floor($Total / $round) * $round;
		}
		return $result;
	}

	public static function calAssetCPCMDSD(int $priceUBNDBase, int $priceUBND, float $mainArea)
	{
		$result = 0;
		$result = ($priceUBNDBase - $priceUBND) * $mainArea;
		return $result;
	}

	public static function getCertificateAssetPriceTotal_v2($certificateId)
	{
		//// Do hiện tại chỉ clone dữ liệu từ bảng appraise nên không nên tính lại mà chỉ cần lấy dữ liệu từ
		$certificateAppraises = CertificateHasAppraise::where('certificate_id', $certificateId)->get();
		if (isset($certificateAppraises)) {
			$landAssetPriceTotal = 0;
			$tangibleAssetPriceTotal = 0;
			$otherAssetPriceTotal = 0;
			$assetPriceTotal = 0;
			$roundTotal = 0;
			$slug = ['land_asset_price', 'tangible_asset_price', 'other_asset_price', 'total_asset_price', 'round_appraise_total'];
			$stt = 0;
			foreach ($certificateAppraises as $appraise) {
				$priceList = CertificateAssetPrice::where('appraise_id', $appraise['appraise_id'])->whereIn('slug', $slug)->get(['slug', 'value']);
				foreach ($priceList as $price) {
					if ($price['slug'] == 'land_asset_price') {
						$landAssetPriceTotal += $price['value'];
					} elseif ($price['slug'] == 'tangible_asset_price') {
						$tangibleAssetPriceTotal += $price['value'];
					} elseif ($price['slug'] == 'other_asset_price') {
						$otherAssetPriceTotal += $price['value'];
					} elseif ($price['slug'] == 'total_asset_price') {
						$assetPriceTotal += $price['value'];
					} elseif ($price['slug'] == 'round_appraise_total') {
						$round = $price['value'];
						if ($stt == 0) {
							$roundTotal = $round;
						} elseif (($roundTotal < 0) || ($round < 0)) {
							if (abs($roundTotal) > abs($round))
								$roundTotal = -abs($round);
							else
								$roundTotal = -abs($round);
						} else {
							if ($roundTotal > $round)
								$roundTotal = $round;
						}
					}
				}
				$stt += 1;
			}

			$assetPriceTotal = self::roundPrice($assetPriceTotal, $roundTotal);
			CertificatePrice::where('certificate_id', $certificateId)->forceDelete();

			foreach ($slug as $item) {
				$data = [];
				$data['certificate_id'] = $certificateId;
				$data['slug'] = $item;
				if ($item == 'land_asset_price') {
					$data['value'] = $landAssetPriceTotal;
				} elseif ($item == 'tangible_asset_price') {
					$data['value'] = $tangibleAssetPriceTotal;
				} elseif ($item == 'other_asset_price') {
					$data['value'] = $otherAssetPriceTotal;
				} elseif ($item == 'total_asset_price') {
					$data['value'] = $assetPriceTotal;
				} elseif ($item == 'round_total') {
					$data['value'] = $roundTotal;
				}
				$data['description'] = '';
				$assetPrice = new CertificatePrice($data);
				QueryBuilder::for($assetPrice)
					->insert($assetPrice->attributesToArray());
			}
		}
	}

	public static function roundDistance($distane)
	{
		$result = 0;
		$distaneArray = explode('.', $distane);
		if (count($distaneArray) > 1) {
			$bonus = substr($distaneArray[1], 0, 1);
			if ($bonus < 5) {
				$result = $distaneArray[0] + 0.5;
			} else {
				$result = $distaneArray[0] + 1;
			}
		} else {
			$result = $distane;
		}
		return $result;
	}

	public static function calAppraiseAssetDistance($appraiseLocation, $assetLocation)
	{
		try {
			$result = 2;
			$location1 = explode(',', $appraiseLocation);
			$location2 = explode(',', $assetLocation);
			$lat1 = (float)$location1[0] ?? null;
			$lon1 = (float)$location1[1] ?? null;
			$lat2 = (float)$location2[0] ?? null;
			$lon2 = (float)$location2[1] ?? null;
			$result = self::getDistanceFromLatLonInKm($lat1, $lon1, $lat2, $lon2);
		} catch (Exception $ex) {
			$result = 2;
		}

		return $result;
	}

	//// COPYRIGHT ON stackoverflow.com
	public static function getDistanceFromLatLonInKm($lat1, $lon1, $lat2, $lon2, $earthRadius = 6371)
	{
		// $earthRadius = 6371; // Radius of the earth in km
		// convert from degrees to radians
		$latFrom = deg2rad($lat1);
		$lonFrom = deg2rad($lon1);
		$latTo = deg2rad($lat2);
		$lonTo = deg2rad($lon2);
		$latDelta = $latTo - $latFrom;
		$lonDelta = $lonTo - $lonFrom;

		$angle = 2 * asin(sqrt(
			pow(sin($latDelta / 2), 2) +
				cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)
		));
		return $angle * $earthRadius;
	}

	public static function getUser()
	{
		$jwt = JWTAuth::getToken();
		$eloquentUserRepository = new EloquentUserRepository(new User());
		$user = $eloquentUserRepository->validateToken($jwt);

		return $user;
	}

	public static function checkUserPermission(array $permissions)
	{
		$jwt = JWTAuth::getToken();
		if (empty($jwt)) {
			return false;
		}
		$eloquentUserRepository = new EloquentUserRepository(new User());
		$user = $eloquentUserRepository->validateToken($jwt);

		if ($user->hasRole('ROOT_ADMIN')) {
			return true;
		} else {
			foreach ($permissions as $permission) {
				if ($user->can($permission)) {
					return true;
				}
			}
		}
		return false;
	}

	public static function calAssetGTDTQH(int $priceUBND, float $violetArea)
	{
		return self::roundPrice($priceUBND * $violetArea, 0);
	}

	public static function checkExistsAppraiseLaw(int $realEstateID)
	{
		$result = null;
		$realEstate = RealEstate::find($realEstateID);
		if ($realEstate->appraises && count($realEstate->appraises->appraiseLaw) > 0) {
			return $result;
		} else  if ($realEstate->apartment && count($realEstate->apartment->law) > 0) {
			return $result;
		}
		$result = ['message' => 'TSTĐ_' . $realEstateID . ' - chưa có thông tin pháp lý', 'exception' => ''];
		return $result;
	}

	public static function checkValidAppraise(int $realEstateID)
	{
		$result = null;
		$realEstate = RealEstate::find($realEstateID);
		if ($realEstate->appraises) {
			$price = self::checkAppraisePrice($realEstate->appraises);
			Log::info('checkValidAppraise: ' . print_r($price, true));

			if (isset($price)) {
				$message = $price;
			}
			$comparison = self::checkAppraiseAdjust($realEstate->appraises->id);
			if (isset($comparison)) {
				Log::info('checkValidAppraise: ' . print_r($comparison, true));
				Log::info('checkAdjustRate: ' . print_r($comparison['checkAdjustRate'], true));
				Log::info('checkMaxAvg: ' . print_r($comparison['checkMaxAvg'], true));
				if ($comparison['checkAdjustRate'] == true) {
					$message[] = 'Mức độ chênh lệch với mức giá trung bình của các mức giá chỉ dẫn lớn hơn ' . ValueDefault::MAXIMUM_AVERAGE_RATE . '%';
				}
				if ($comparison['checkMaxAvg'] == true) {
					$message[] = 'Mức độ chênh lệch của mức giá sau điều chỉnh với mức giá chỉ dẫn lớn hơn ' . ValueDefault::TOTAL_ADJUST_RATE . '%';
				}
			}
			Log::info('checkValidAppraise: ' . print_r($message, true));
			if (!empty($message)) {
				$result['message'] = 'TSTĐ_' . $realEstateID . ': ' . implode(', ', $message);
				$result['exception'] = '';
			}
		} else if ($realEstate->apartment) {
			$price = self::checkApartmentPrice($realEstate->apartment);
			Log::info('checkValidAppraise: ' . print_r($price, true));

			if (isset($price)) {
				$message = $price;
			}

			$comparison = self::checkApartmentAdjust($realEstate->apartment->id);

			if (isset($comparison)) {
				if ($comparison['checkAdjustRate'] == true) {
					$message[] = 'Mức độ chênh lệch với mức giá trung bình của các mức giá chỉ dẫn lớn hơn ' . ValueDefault::MAXIMUM_AVERAGE_RATE . '%';
				}
				if ($comparison['checkMaxAvg'] == true) {
					$message[] = 'Mức độ chênh lệch của mức giá sau điều chỉnh với mức giá chỉ dẫn lớn hơn ' . ValueDefault::TOTAL_ADJUST_RATE . '%';
				}
				Log::info('checkValidAppraise: ' . print_r($comparison, true));
				Log::info('checkAdjustRate: ' . print_r($comparison['checkAdjustRate'], true));
				Log::info('checkMaxAvg: ' . print_r($comparison['checkMaxAvg'], true));
			}
			Log::info(
				'checkValidAppraise: ' . print_r($message, true)
			);
			if (!empty($message)) {
				$result['message'] = 'TSTĐ_' . $realEstateID . ': ' . implode(', ', $message);
				$result['exception'] = '';
			}
		}
		return $result;
	}
	public static function checkApartmentPrice($apartment)
	{
		$message = [];
		$appraisePrice = $apartment->price;
		if (isset($appraisePrice)) {
			foreach ($appraisePrice as $price) {
				if (strpos(strval($price->slug), 'price') !== false) {
					switch ($price->slug) {
						case 'apartment_asset_price':
							if (floatval($price->value) <= 0) $message[] = 'Đơn giá căn hộ phải lớn hơn 0';
							break;
						case 'apartment_area':
							if (floatval($price->value) <= 0) $message[] = 'Diện tích căn hộ phải lớn hơn 0';
							break;
						case 'apartment_total_price':
							if (floatval($price->value) <= 0) $message[] = 'Giá trị căn hộ phải lớn hơn 0';
							break;
						case 'total_price':
							if (floatval($price->value) <= 0) $message[] = 'Tổng giá trị căn hộ phải lớn hơn 0';
							break;
						default:
							if (floatval($price->value) < 0)  $message[] = $price->slug . ' nhỏ hơn 0';
							break;
					}
				}
			}
		}
		return $message;
	}
	public static function checkAppraisePrice($appraise)
	{
		$message = [];
		$appraisePrice = $appraise->assetPrice;
		if (isset($appraisePrice)) {
			foreach ($appraisePrice as $price) {
				if (strpos(strval($price->slug), 'price') !== false) {
					switch ($price->slug) {
						case 'layer_cutting_price':
							if (floatval($price->value) <= 0) $message[] = 'Đơn giá sau cắt lớp phải lớn hơn 0';
							break;
						case 'land_asset_price':
							if (floatval($price->value) <= 0) $message[] = 'Giá trị quyền sử dụng đất phải lớn hơn 0';
							break;
						case 'total_asset_price':
							if (floatval($price->value) <= 0) $message[] = 'Tổng giá trị tài sản phải lớn hơn 0';
							break;
						default:
							if (floatval($price->value) < 0)  $message[] = $price->slug . ' nhỏ hơn 0';
							break;
					}
				}
			}
		}
		return $message;
	}

	public static function checkAppraiseAdjust(int $appraiseId)
	{
		$with = [
			'appraiseHasAssets:appraise_id,asset_general_id,version',
			'appraiseAdapter:appraise_id,asset_general_id,change_purpose_price,change_violate_price,percent',
			'assetUnitArea',
			'assetUnitPrice',
			'comparisonFactor' => function ($q) {
				$q->where('status', '=', 1)->where('adjust_percent', '<>', '0');
			},
		];
		$select = [
			'id'
		];
		$appraise = Appraise::with($with)->where('id', $appraiseId)->select($select)->first();
		$appraise->checkMaxAvg = false;
		$appraise->checkAdjustRate = false;

		$assets = $appraise->appraiseHasAssets;
		$compareAssetGeneralRepository = new EloquentCompareAssetGeneralRepository(new CompareAssetGeneral());
		$sumPrice = 0;
		foreach ($assets as $asset) {
			$asset_general_id = $asset->asset_general_id;
			$data[$asset_general_id]['asset_general_id'] = $asset_general_id;
			$asset_general = $compareAssetGeneralRepository->findVersionById_v2($asset_general_id, $asset->version);

			$adapter =  $appraise->appraiseAdapter->where('asset_general_id', $asset_general_id)->first();
			$unitAreas =  $appraise->assetUnitArea->where('asset_general_id', $asset_general_id);
			$comparisonFactor =  $appraise->comparisonFactor->where('asset_general_id', $asset_general_id);
			$totalArea = floatval($asset_general['properties'][0]['asset_general_land_sum_area']);
			$violateArea = floatval(0);
			$purposeArea = floatval(0);
			$data[$asset_general_id]['total_area'] = $totalArea;
			foreach ($unitAreas as $unitArea) {

				$violateArea += $unitArea->violation_asset_area;
			}
			$total_amount = $asset_general['total_amount'];
			$percent = $adapter->percent ?? 0;
			$estimate_amount =  ($percent *  $total_amount) / 100;
			$construction_amount = $asset_general['total_construction_amount'];
			$violatePrice = isset($adapter->change_violate_price) ? $adapter->change_violate_price : 0;
			$purposePrice = isset($adapter->change_purpose_price) ? $adapter->change_purpose_price : 0;

			$calculate_price = $estimate_amount - $violatePrice - $construction_amount + $purposePrice;
			$purposeArea = $totalArea - $violateArea;
			$average_price = round($calculate_price / $purposeArea, 0);

			$legalRate = 0;
			$diffRateTotal = floatval(0);
			foreach ($comparisonFactor as $factor) {
				if ($factor->type == 'phap_ly') {
					$legalRate = $factor->adjust_percent ?? 0;
				} else {
					if ($factor->adjust_percent != 0) {
						$diffRateTotal = $diffRateTotal + $factor->adjust_percent ?? 0;
					}
				}
			}
			//Price after ajust by legal factor
			$legalAmount = round($average_price *  $legalRate / 100, 0);
			$averate_price_legal = $average_price + $legalAmount;
			Log::info('calculate_price: ' . $average_price);
			Log::info('violateArea: ' . $violateArea);
			Log::info('totalArea: ' . $totalArea);
			Log::info('average_price: ' . $average_price);
			Log::info('legalRate: ' . $legalRate);
			Log::info('averate_price_legal: ' . $averate_price_legal);
			Log::info('legalAmount: ' . $legalAmount);
			//Price after ajust by other factor
			$diffRateAmount = round($averate_price_legal * $diffRateTotal / 100, 0);
			$indicative_amount = $averate_price_legal + $diffRateAmount;
			Log::info('diffRateAmount]: ' . $diffRateAmount);
			Log::info('diffRateTotal]: ' . $diffRateTotal);
			Log::info('indicative_amount]: ' . $indicative_amount);
			$data[$asset_general_id]['indicative_price'] = $indicative_amount;

			if ($diffRateTotal + $legalRate > ValueDefault::TOTAL_ADJUST_RATE) {
				$appraise->checkMaxAvg = true;
			}

			$sumPrice += $indicative_amount;
		}

		$avg_adjust_price = round($sumPrice / 3, 0);
		foreach ($data as $item) {
			$diff = round(($item['indicative_price'] - $avg_adjust_price) / $avg_adjust_price * 100, 0);

			Log::info('item[indicative_price]: ' . $item['indicative_price']);
			Log::info('avg_adjust_price: ' . $avg_adjust_price);
			Log::info('diff: ' . $diff);
			if (abs($diff) > ValueDefault::MAXIMUM_AVERAGE_RATE) {
				$appraise->checkAdjustRate = true;
			}
		}
		return $appraise;
	}

	public static function checkApartmentAdjust(int $apartmentId)
	{
		$with = [
			'apartmentHasAsset:apartment_asset_id,asset_general_id',
			'apartmentAdapter:apartment_asset_id,asset_general_id,percent',
			'comparisonFactor' => function ($q) {
				$q->where('status', '=', 1)->where('adjust_percent', '<>', '0');
			},
		];
		$select = [
			'id'
		];
		$appraise = ApartmentAsset::with($with)->where('id', $apartmentId)->select($select)->first();
		$appraise->append('assets_general');
		$appraise->checkMaxAvg = false;
		$appraise->checkAdjustRate = false;
		$assets = $appraise->assets_general;
		$adapters = $appraise->apartmentAdapter;
		$comparisons = $appraise->comparisonFactor;
		$sumPrice = 0;
		foreach ($assets as $asset) {
			$adapter = $adapters->where('asset_general_id', $asset->id)->first();
			$comparison = $comparisons->where('asset_general_id', $asset->id);
			$legal = $comparison->where('type', 'phap_ly')->first();
			$notLegal = $comparison->whereNotIn('type', ['phap_ly']);
			$totalAmount = $asset->total_amount;
			$percent = $adapter->percent;
			$amount = $totalAmount * $percent / 100;
			$area = floatval($asset->room_details[0]['area'] ?? 0);
			$unitPrice = $amount / $area;
			$legalRate = $legal->adjust_percent ?? 0;
			$legalUnitPrice = $unitPrice + $unitPrice * $legalRate / 100;
			$notLegalRate = $notLegal->sum('adjust_percent');
			$price = $legalUnitPrice + $legalUnitPrice * $notLegalRate / 100;
			$sumPrice += $price;

			$data[$asset->id]['indicative_price'] = $price;
			if ($legalRate + $notLegalRate > ValueDefault::TOTAL_ADJUST_RATE) {
				$appraise->checkMaxAvg = true;
			}
		}
		$avg_adjust_price = round($sumPrice / 3, 0);
		foreach ($data as $item) {
			$diff = round(($item['indicative_price'] - $avg_adjust_price) / $avg_adjust_price * 100, 0);

			if (abs($diff) > ValueDefault::MAXIMUM_AVERAGE_RATE) {
				$appraise->checkAdjustRate = true;
			}
		}
		return $appraise;
	}

	public static function getComparisonAppraise(array $ids)
	{
		// $ids = [1013,1051];
		$with = [
			'properties.material:id,description',
			'assetType:id,description',
			'properties:id,appraise_id,front_side,front_side_width,insight_width,land_shape_id,appraise_land_sum_area,main_road_length,material_id',
			'properties.landShape:id,description',
			'properties.propertyDetail:appraise_property_id,land_type_purpose_id,is_transfer_facility',
			'properties.propertyDetail.landTypePurpose:id,description,acronym',
			'properties.propertyTurningTime:appraise_property_id,main_road_length,material_id',
			'properties.propertyTurningTime.material:id,description',
			'createdBy:id,name',
			'assetPrice:appraise_id,slug,value',
			'tangibleAssets:appraise_id,building_type_id,total_construction_area,total_construction_base',
			'tangibleAssets.buildingType:id,description',
		];
		$select = [
			'appraises.id', 'asset_type_id', 'appraises.created_by', 'appraises.updated_at', 'appraises.created_at',
			DB::raw("SPLIT_PART(appraises.coordinates , ',', 1)::float as lat1"),
			DB::raw("SPLIT_PART(appraises.coordinates , ',', 2)::float as lon1"),
		];
		$appraises = Appraise::with($with)
			->whereIn('id', $ids)
			->select($select)
			->get();

		$tbDistanceString = '"tbDistance"';

		$distance = ValueDefault::RADIUS_SCAN;
		// $distance= 100;
		$earthRadius = 6371;
		foreach ($appraises as $appraise) {
			$lat1 = floatval($appraise->lat1);
			$lon1 = floatval($appraise->lon1);
			$front_side = $appraise->properties[0]->front_side;
			$land_type_purpose = $appraise->properties[0]->propertyDetail->where('is_transfer_facility', '=', true)->first();
			$land_type_purpose_id = $land_type_purpose->land_type_purpose_id;
			$land_type_acronym = $land_type_purpose->landTypePurpose->acronym;
			DB::enableQueryLog();
			$slug = 'land_asset_purpose_' . $land_type_acronym . '_price';

			$select1 = [
				'appraises.id', 'asset_type_id', 'appraises.created_by', 'appraises.updated_at', 'appraises.created_at',
				DB::raw('cast("tbDistance"."distance" as decimal(10,3)) as distance'),
				'appraise_prices.value'
			];
			$appraiseList = Appraise::with($with)
				->join('appraise_properties as t1', function ($join) use ($front_side, $land_type_purpose_id) {
					$join->on('t1.appraise_id', '=', 'appraises.id')
						->where('t1.front_side', $front_side)
						->whereNull('t1.deleted_at')
						->join('appraise_property_details as t2', function ($join1) use ($land_type_purpose_id) {
							$join1->on('t1.id', '=', 't2.appraise_property_id')
								->whereNull('t2.deleted_at')
								->where('t2.land_type_purpose_id', '=', $land_type_purpose_id)
								->where('t2.is_transfer_facility', '=', true);
						});
				})
				->join(DB::raw("(select $earthRadius  * 2 * asin(sqrt(power(SIN((pi()/180) * ( SPLIT_PART(coordinates , ',',1)::float -  $lat1 ) /2),2)
                    + cos((pi()/180) * SPLIT_PART(coordinates , ',',1)::float)
                    * cos((pi()/180) *  $lat1)
                    * POWER(sin( (pi()/180) * (SPLIT_PART(coordinates , ',',2)::float - $lon1 ) /2),2))) as distance , id
                    from appraises
                    where deleted_at is null) as " . $tbDistanceString), function ($join1) {
					$join1->on('tbDistance.id', '=', 'appraises.id');
				})
				->join('appraise_prices', function ($join2) use ($slug) {
					$join2->on('appraise_prices.appraise_id', '=', 'appraises.id')
						->where('appraise_prices.slug', '=', $slug);
				})
				->select($select1)
				->whereIn('appraises.status', [3, 4])
				->where('tbDistance.distance', '<', $distance)
				->where('appraises.id', '<>', $appraise->id)
				->orderBy('tbDistance.distance')
				->limit(3)
				->get();

			$price = $appraise->assetPrice->where('slug', $slug)->first();
			if (isset($price))
				$value = $price->value;
			else
				$value = 0;
			$appraise->purposeSlug = $land_type_acronym;
			$appraise->purposePrice = $value;

			if (isset($appraiseList) && count($appraiseList) > 0) {
				foreach ($appraiseList as $appraiseSS) {
					$priceSS = $appraiseSS->assetPrice->where('slug', $slug)->first();

					$appraiseSS->purposeSlug = $land_type_acronym;
					$appraiseSS->purposePrice = 0;
					if (isset($priceSS)) {
						$valueSS = $priceSS->value;
						$appraiseSS->purposePrice = $valueSS;
						if ($valueSS != 0) {
							$risk = (($value - $valueSS) / $valueSS) * 100;
							$appraiseSS->risk = $risk;
							if (abs($risk) < ValueDefault::CERTIFICATION_ASSET_MIN_RISK) {
								$appraiseSS->message = 'Độ rủi ro thấp';
								$appraiseSS->priority = 0;
							} elseif (abs($risk) > ValueDefault::CERTIFICATION_ASSET_MIN_RISK) {
								$appraiseSS->message = 'Độ rủi ro cao';
								$appraiseSS->priority = 2;
							} else {
								$appraiseSS->message = 'Độ rủi ro trung bình';
								$appraiseSS->priority = 1;
							}
						}
					} else {
						$appraiseSS->message = 'Không tính được độ rủi ro.';
						$appraiseSS->priority = 2;
					}
				}
				$appraise->message = '';
			} else
				$appraise->message = 'Chưa có TSTĐ tương tự để kiểm soát chéo';
			$appraise->appraiseList = $appraiseList;
		}
		return $appraises;
	}

	public static function callNotification($users, $data)
	{
		register_shutdown_function([self::class, 'registerShutdowncallNotification'], $users, $data);
		return;
	}
	public static function registerShutdowncallNotification($users, $data)
	{
		$broadcast = new BroadcastNotification((object)$data);
		Notification::send($users, $broadcast);
	}
	public static function convertStatusText($status)
	{
		switch ($status) {
			case 2:
				$data = 'Đang Thẩm Định';
				break;
			case 3:
				$data = 'Đang Duyệt';
				break;
			case 4:
				$data = 'Hoàn Thành';
				break;
			case 5:
				$data = 'Đã Hủy';
				break;
			case 6:
				$data = 'Đang Kiểm Soát';
				break;
			default:
				$data = 'Mới';
		}

		return $data;
	}
	public static function getUtilities()
	{
		$data = Dictionary::query()->where(['type' => 'TIEN_ICH_CO_BAN', 'status' => 1])->get();
		return $data;
	}

	public static function getViTri($id)
	{
		$data = Dictionary::query()->where(['type' => 'VI_TRI_DAT', 'id' => $id])->first();
		return $data->description;
	}
	public static function getTotalRealEstatePrice($realEstates)
	{
		$value = 0;
		foreach ($realEstates as $item) {
			$value += self::roundPrice($item->total_price, $item->round_total);
		}
		return $value;
	}

	public static function getTotalRealEstatePriceNotRound($realEstates)
	{
		$value = 0;
		foreach ($realEstates as $item) {
			$value += $item->total_price;
		}
		return $value;
	}
	public static function getUtilitiesDescription($utilities)
	{
		$descriptionArr = [];
		if (!empty($utilities)) {
			$utiMaster = self::getUtilities();
			foreach ($utilities as $uti) {
				$find = $utiMaster->where('acronym', $uti)->first();
				if (!empty($find)) {
					$descriptionArr[] = self::mbCaseTitle($find->description);
				}
			}
		}
		return $descriptionArr;
	}
	public static function isnull($str, $return)
	{
		try {
			if (!empty($str)) {
				return $str;
			}
			return $return;
		} catch (Exception $ex) {
			Log::error($ex);
			return $return;
		}
	}

	public static function getCompareWithId($id)
	{
		$dataAppraise = Activity::where('subject_type', 'App\Models\CompareAssetGeneral')->where('subject_id',  $id)->with('causer')->orderBy('id', 'desc')->first();
		return $dataAppraise->updated_at;
	}

	public static function getCompareWithIdNote($id)
	{
		$dataAppraise = CompareAssetGeneral::where('id', $id)->orderBy('id', 'desc')->first();
		return $dataAppraise->note;
	}

	public static function getPlaningInfo($id)
	{
		$dataAppraise = RealEstate::where('id', $id)->first();
		return $dataAppraise->planning_info;
	}
}
