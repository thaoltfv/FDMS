<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Repositories\EloquentCompareAssetGeneralRepository;
use App\Models\CompareAssetGeneral;
use App\Models\CompareProperty;
use App\Models\CertificateAssetComparisonFactor;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

/**
 * Class Province
 * @package App\Models
 */
class CertificateAsset extends Model
{
	use SoftDeletes;

	protected $table = 'certificate_assets';
	//protected $dateFormat = 'd-m-Y H:i:s';
	protected $casts = [
		'id' => 'integer',
	];
	protected $fillable = [
		'status',
		'asset_type_id',
		'province_id',
		'district_id',
		'ward_id',
		'street_id',
		'distance_id',
		'topographic_id',
		'land_no',
		'doc_no',
		'land_no_old',
		'doc_no_old',
		'coordinates',
		'appraise_asset',
		'appraise_basis_property_id',
		'document_description',
		'appraise_approach_id',
		'appraise_principle_id',
		'appraise_method_used_id',
		'created_by',
		'appraise_id',
		'real_estate_id',
		'full_address'
	];
	protected $guarded = [];

	public function realestate(): BelongsTo
	{
		return $this->belongsTo(RealEstate::class, 'id', 'appraise_id');
	}
	public function getRoundTotalAttribute()
	{
		$item = AppraisePrice::where('appraise_id', $this->appraise_id)->where('slug', 'round_total')->first();
		return isset($item->value) ? (int)$item->value : 0;
	}

	public function getRoundCompositeAttribute()
	{
		$item = AppraisePrice::where('appraise_id', $this->appraise_id)->where('slug', 'round_composite')->first();
		return isset($item->value) ? (int)$item->value : 0;
	}

	public function getRoundViolationCompositeAttribute()
	{
		$item = AppraisePrice::where('appraise_id', $this->appraise_id)->where('slug', 'round_violation_composite')->first();
		return isset($item->value) ? (int)$item->value : 0;
	}

	public function getRoundViolationFacilityAttribute()
	{
		$item = AppraisePrice::where('appraise_id', $this->appraise_id)->where('slug', 'round_violation_facility')->first();
		return isset($item->value) ? (int)$item->value : 0;
	}

	public function getRoundAppraiseTotalAttribute()
	{
		$item = AppraisePrice::where('appraise_id', $this->appraise_id)->where('slug', 'round_appraise_total')->first();
		return isset($item->value) ? (int)$item->value : 0;
	}


	public function getLayerCuttingPriceAttribute()
	{
		$item = AppraisePrice::where('appraise_id', $this->appraise_id)->where('slug', 'layer_cutting_price')->first();
		return isset($item->value) ? floatval($item->value) : null;
	}

	public function getLayerCuttingProcedureAttribute()
	{
		$item = AppraiseAppraisalMethods::where('appraise_id', $this->appraise_id)->where('slug', 'layer_cutting_procedure')->first();
		return isset($item->slug_value) ? boolval($item->slug_value) : false;
	}

	public function getUnifyIndicativePriceSlugAttribute()
	{
		$item = CertificateAssetAppraisalMethods::where('appraise_id', $this->id)->where('slug', 'thong_nhat_muc_gia_chi_dan')->first();
		if (!isset($item->slug_value)) {
			$item = CertificateAssetAppraisalMethods::create([
				'appraise_id' => $this->id,
				'slug' => "thong_nhat_muc_gia_chi_dan",
				'slug_value' => "trung-binh",
			]);
		}
		return isset($item->slug_value) ? $item->slug_value : '';
	}

	public function getCompositeLandRemaningSlugAttribute()
	{
		$item = CertificateAssetAppraisalMethods::where('appraise_id', $this->id)->where('slug', 'tinh_gia_dat_hon_hop_con_lai')->first();
		if (!isset($item->slug_value)) {
			$item = CertificateAssetAppraisalMethods::create([
				'appraise_id' => $this->id,
				'slug' => "tinh_gia_dat_hon_hop_con_lai",
				'slug_value' => "theo-chi-phi-chuyen-mdsd-dat",
			]);
		}
		return isset($item->slug_value) ? $item->slug_value : '';
	}

	public function getCompositeLandRemaningValueAttribute()
	{
		$item = CertificateAssetAppraisalMethods::where('appraise_id', $this->id)->where('slug', 'tinh_gia_dat_hon_hop_con_lai')->first();
		if (!isset($item->slug_value)) {
			$item = CertificateAssetAppraisalMethods::create([
				'appraise_id' => $this->id,
				'slug' => "tinh_gia_dat_hon_hop_con_lai",
				'slug_value' => "theo-chi-phi-chuyen-mdsd-dat",
			]);
		}
		return isset($item->value) ? $item->value : null;
	}

	public function getPlanningViolationPriceSlugAttribute()
	{
		$item = CertificateAssetAppraisalMethods::where('appraise_id', $this->id)->where('slug', 'tinh_gia_dat_vi_pham_quy_hoach')->first();
		if (!isset($item->slug_value)) {
			$item = CertificateAssetAppraisalMethods::create([
				'appraise_id' => $this->id,
				'slug' => "tinh_gia_dat_vi_pham_quy_hoach",
				'slug_value' => "theo-gia-dat-qd-ubnd",
			]);
		}
		return isset($item->slug_value) ? $item->slug_value : '';
	}

	public function getPlanningViolationPriceValueAttribute()
	{
		$item = CertificateAssetAppraisalMethods::where('appraise_id', $this->id)->where('slug', 'tinh_gia_dat_vi_pham_quy_hoach')->first();
		if (!isset($item->slug_value)) {
			$item = CertificateAssetAppraisalMethods::create([
				'appraise_id' => $this->id,
				'slug' => "tinh_gia_dat_vi_pham_quy_hoach",
				'slug_value' => "theo-gia-dat-qd-ubnd",
			]);
		}
		return isset($item->value) ? $item->value : null;
	}

	public function province(): BelongsTo
	{
		return $this->belongsTo(Province::class, 'province_id', 'id');
	}

	public function district(): BelongsTo
	{
		return $this->belongsTo(District::class, 'district_id', 'id');
	}

	public function ward(): BelongsTo
	{
		return $this->belongsTo(Ward::class, 'ward_id', 'id');
	}

	public function street(): BelongsTo
	{
		return $this->belongsTo(Street::class, 'street_id', 'id');
	}

	public function distance(): BelongsTo
	{
		return $this->belongsTo(Distance::class, 'distance_id', 'id');
	}

	public function assetType(): BelongsTo
	{
		return $this->belongsTo(Dictionary::class, 'asset_type_id', 'id');
	}

	public function topographic(): BelongsTo
	{
		return $this->belongsTo(Dictionary::class, 'topographic_id', 'id');
	}

	public function pic(): HasMany
	{
		return $this->hasMany(CertificateAssetPic::class, 'appraise_id');
	}

	public function properties(): HasMany
	{
		return $this->hasMany(CertificateAssetProperty::class, 'appraise_id');
	}

	public function tangibleAssets(): HasMany
	{
		return $this->hasMany(CertificateAssetTangibleAsset::class, 'appraise_id')->orderBy('id');
	}

	public function otherAssets(): HasMany
	{
		return $this->hasMany(CertificateAssetOtherAsset::class, 'appraise_id');
	}

	public function appraiseApproach(): BelongsTo
	{
		return $this->belongsTo(AppraiseOtherInformation::class, 'appraise_approach_id', 'id');
	}

	public function appraisePrinciple(): BelongsTo
	{
		return $this->belongsTo(AppraiseOtherInformation::class, 'appraise_principle_id', 'id');
	}

	public function appraiseMethodUsed(): BelongsTo
	{
		return $this->belongsTo(AppraiseOtherInformation::class, 'appraise_method_used_id', 'id');
	}

	public function appraiseBasisProperty(): BelongsTo
	{
		return $this->belongsTo(AppraiseOtherInformation::class, 'appraise_basis_property_id', 'id');
	}

	public function appraiseLaw(): HasMany
	{
		return $this->hasMany(AppraiseLaw::class, 'appraise_id', 'appraise_id')->orderBy('id', 'ASC');
	}
	public function certificateAppraiseLaw(): HasMany
	{
		return $this->hasMany(CertificateAssetLaw::class, 'appraise_id')->orderBy('id', 'ASC');
	}

	public function constructionCompany(): HasMany
	{
		//return $this->hasMany(ConstructionCompany::class, 'appraise_id');
		return $this->hasMany(CertificateAssetConstructionCompany::class, 'appraise_id');
	}

	public function certificateAssetConstructionCompany(): HasMany
	{
		return $this->hasMany(CertificateAssetConstructionCompany::class, 'appraise_id');
	}

	/* public function assetGeneral(): belongsToMany
    {
        return $this->belongsToMany(CompareAssetGeneral::class,'appraise_has_assets','appraise_id','asset_general_id','appraise_id')->orderBy('id', 'DESC');
    } */

	public function getAssetGeneralAttribute()
	{
		$result = [];
		if (isset($this->id) && !empty($this->id)) {
			$items = CertificateAssetHasAsset::where('appraise_id', $this->id)->orderBy('asset_general_id', 'DESC')->get();
			foreach ($items as $item) {
				$compareAssetGeneralRepository = new EloquentCompareAssetGeneralRepository(new CompareAssetGeneral());
				$item = $compareAssetGeneralRepository->findVersionById($item->asset_general_id, $item->version);
				//begin -group ELS
				if (isset($item['created_by'])) {
					$item['createdBy'] = $item['created_by'];
					//unset($item['created_by']);
				}
				if (isset($item['asset_type'])) {
					$item['assetType'] = $item['asset_type'];
					//unset($item['asset_type']);
				}
				if (isset($item['transaction_type'])) {
					$item['transactionType'] = $item['transaction_type'];
					//unset($item['transaction_type']);
				}
				if (isset($item['topographic_data'])) {
					$item['topographicData'] = $item['topographic_data'];
					//unset($item['topographic_data']);
				}

				if (isset($item['properties'])) {
					$itemTmp = [];
					foreach ($item['properties'] as $stt => $property) {
						$itemTmp[$stt] = $property;
						//unset($itemTmp[$stt]['property_detail']);
						$itemTmp[$stt]['propertyDetail'] = $item['properties'][$stt]['property_detail'];
						foreach ($property['property_detail'] as $stt1 => $propertyDetail) {
							if (isset($item['properties'][$stt]['property_detail'][$stt1]['land_type_purpose_data'])) {
								$itemTmp[$stt]['propertyDetail'][$stt1]['landTypePurposeData'] = $item['properties'][$stt]['property_detail'][$stt1]['land_type_purpose_data'];
							} else {
								$itemTmp[$stt]['propertyDetail'][$stt1]['landTypePurposeData'] = null;
							}
							//unset($itemTmp[$stt]['propertyDetail'][$stt1]['land_type_purpose_data']);

							if (isset($item['properties'][$stt]['property_detail'][$stt1]['position_type'])) {
								$itemTmp[$stt]['propertyDetail'][$stt1]['positionType'] = $item['properties'][$stt]['property_detail'][$stt1]['position_type'];
							} else {
								$itemTmp[$stt]['propertyDetail'][$stt1]['positionType'] = null;
							}
							//unset($itemTmp[$stt]['propertyDetail'][$stt1]['position_type']);

							if (isset($item['properties'][$stt]['property_detail'][$stt1]['land_type_purpose'])) {
								$itemTmp[$stt]['propertyDetail'][$stt1]['land_type_purpose_id'] = $item['properties'][$stt]['property_detail'][$stt1]['land_type_purpose'];
							} else {
								$itemTmp[$stt]['propertyDetail'][$stt1]['land_type_purpose_id'] = null;
							}
						}
						$itemTmp[$stt]['comparePropertyTurningTime'] = isset($item['properties'][$stt]['compare_property_turning_time']) ? $item['properties'][$stt]['compare_property_turning_time'] : null;
						$itemTmp[$stt]['comparePropertyDoc'] = isset($item['properties'][$stt]['compare_property_doc']) ? $item['properties'][$stt]['compare_property_doc'] : null;
						$itemTmp[$stt]['socialSecurity'] = isset($item['properties'][$stt]['social_security']) ? $item['properties'][$stt]['social_security'] : null;
						$itemTmp[$stt]['fengShui'] = isset($item['properties'][$stt]['feng_shui']) ? $item['properties'][$stt]['feng_shui'] : null;
						$itemTmp[$stt]['paymenMethod'] = isset($item['properties'][$stt]['paymen_method']) ? $item['properties'][$stt]['paymen_method'] : null;
						$itemTmp[$stt]['landShape'] = isset($item['properties'][$stt]['land_shape']) ? $item['properties'][$stt]['land_shape'] : null;
						//unset($itemTmp[$stt]['compare_property_turning_time']);
						//unset($itemTmp[$stt]['compare_property_doc']);
					}
					$item['properties'] = $itemTmp;
				}
				if (isset($item['tangible_assets'])) {
					$itemTmp = $item['tangible_assets'];
					foreach ($item['tangible_assets'] as $stt => $tangibleAssets) {
						if (isset($item['tangible_assets'][$stt]['building_type'])) {
							$itemTmp[$stt]['buildingType'] = $item['tangible_assets'][$stt]['building_type'];
						} else {
							$itemTmp[$stt]['buildingType'] = null;
						}
						//unset($itemTmp[$stt]['building_type']);

						if (isset($item['tangible_assets'][$stt]['building_category'])) {
							$itemTmp[$stt]['buildingCategory'] = $item['tangible_assets'][$stt]['building_category'];
						} else {
							$itemTmp[$stt]['buildingCategory'] = null;
						}
						//unset($itemTmp[$stt]['building_category']);
					}
					$item['tangibleAssets'] = $itemTmp;
					//unset($item['tangible_assets']);
				}

				if (isset($item['asset_type'])) {
					$item['assetType'] = $item['asset_type'];
					//unset($item['asset_type']);
				}
				if (isset($item['other_assets'])) {
					$item['otherAssets'] = $item['other_assets'];
					//unset($item['other_assets']);
				}
				if (isset($item['block_specification'])) {
					$item['blockSpecification'] = $item['block_specification'];
					//unset($item['block_specification']);
				}
				if (isset($item['room_details'])) {
					$item['roomDetails'] = $item['room_details'];
					//unset($item['room_details']);
				}
				//end -group ELS
				$item = json_decode(json_encode($item), FALSE);
				$result[] = $item;
			}
		}
		return $result;
	}

	public function createdBy(): BelongsTo
	{
		return $this->belongsTo(User::class, 'created_by', 'id');
	}

	public function version(): HasMany
	{
		return $this->hasMany(CertificateAssetVersion::class, 'appraise_id');
	}
	public function lastVersion(): HasOne
	{
		return $this->hasOne(CertificateAssetVersion::class, 'appraise_id')->orderBy('id', 'desc');
	}
	public function comparisonFactor(): HasMany
	{
		/* $comparisonFactors = $this->hasMany(CertificateAssetComparisonFactor::class, 'appraise_id');
		$checked = [];
		foreach($comparisonFactors->get() as $comparisonFactor) {
			if(!isset($checked[$comparisonFactor->appraise_id][$comparisonFactor->asset_general_id][$comparisonFactor->type])) {
				$checked[$comparisonFactor->appraise_id][$comparisonFactor->asset_general_id][$comparisonFactor->type] = 1;
			} else {
				CertificateAssetComparisonFactor::where('id', $comparisonFactor->id)->forceDelete();
			}
		} */
		return $this->hasMany(CertificateAssetComparisonFactor::class, 'appraise_id')->orderBy('asset_general_id', 'DESC')->orderBy('position');
	}

	public function appraiseAdapter(): HasMany
	{
		return $this->hasMany(CertificateAssetAdapter::class, 'appraise_id')->orderBy('asset_general_id', 'DESC');
	}

	public function appraiseHasAssets(): HasMany
	{
		return $this->hasMany(CertificateAssetHasAsset::class, 'appraise_id')->orderBy('asset_general_id', 'DESC');
	}

	public function comparisonTangibleFactor(): HasMany
	{
		return $this->hasMany(CertificateAssetCTangibleComparisonFactor::class, 'appraise_id');
	}

	public function assetUnitPrice(): HasMany
	{
		return $this->hasMany(CertificateAssetUnitPrice::class, 'appraise_id');
	}

	public function assetUnitArea(): HasMany
	{
		return $this->hasMany(CertificateAssetUnitArea::class, 'appraise_id');
	}

	public function assetPrice(): HasMany
	{
		return $this->hasMany(CertificateAssetPrice::class, 'appraise_id');
	}

	public function appraisalMethods(): HasMany
	{
		return $this->hasMany(CertificateAssetAppraisalMethods::class, 'appraise_id');
	}
	public function appraisal(): HasMany
	{
		return $this->hasMany(CertificateAssetAppraisalMethods::class, 'appraise_id');
	}
	public function hasAppraise(): HasOne
	{
		return $this->hasOne(certificateAssetConstructionCompany::class, 'appraise_id');
	}

	public function getAppraisalClclAttribute()
	{
		$item = CertificateAssetAppraisalMethods::where('appraise_id', $this->id)->where('slug', 'XAC_DINH_CHAT_LUONG_CON_LAI')->first();
		return $item;
	}
	public function getAppraisalDgxdAttribute()
	{
		$item = CertificateAssetAppraisalMethods::where('appraise_id', $this->id)->where('slug', 'XAC_DINH_DON_GIA_XAY_DUNG')->first();
		return $item;
	}
}
