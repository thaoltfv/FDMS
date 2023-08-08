<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Services\CommonService;
use App\Repositories\EloquentCompareAssetGeneralRepository;
use App\Models\CompareAssetGeneral;
use App\Models\CompareProperty;
use App\Models\AppraisePrice;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

/**
 * Class Province
 * @package App\Models
 */
class Appraise extends Model
{
    use SoftDeletes;

    protected $table = 'appraises';
    //protected $dateFormat = 'd-m-Y H:i:s';
    protected $casts = [
        'id' => 'integer',
    ];
    protected $appends = [
        'total_construction_base'
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
        'is_check_frontside',
        'created_by',
        'updated_at',
        'step',
        'sub_status',
        'certificate_id',
        'full_address',
        'filter_year'
    ];
    protected $guarded = [];

	//protected $appends = array('status_text');

	public function getStatusTextAttribute()
    {
		$status = $this->status;
		$statusText = "";
		switch ($status) {
			case 1:
				$statusText = "Mới";
			break;
			case 2:
				$statusText = "Đang Thực Hiện";
			break;
			case 3:
				$statusText = "Đang Duyệt";
			break;
			case 4:
				$statusText = "Hoàn Thành";
            break;
            default:
                $statusText = "Hủy";
			break;
		}
        return $statusText;
    }

    public function getTotalDesicionAverageAttribute()
    {
		$item = AppraisePrice::where('appraise_id', $this->id)->where('slug', 'total_desicion_average')->first();
		return isset($item->value) ? (int)$item->value : 0;
    }

	public function getRoundTotalAttribute()
    {
		$item = AppraisePrice::where('appraise_id', $this->id)->where('slug', 'round_total')->first();
		return isset($item->value) ? (int)$item->value : 0;
    }

    public function getRoundCompositeAttribute()
    {
		$item = AppraisePrice::where('appraise_id', $this->id)->where('slug', 'round_composite')->first();
		return isset($item->value) ? (int)$item->value : 0;
    }

    public function getRoundViolationCompositeAttribute()
    {
		$item = AppraisePrice::where('appraise_id', $this->id)->where('slug', 'round_violation_composite')->first();
		return isset($item->value) ? (int)$item->value : 0;
    }

    public function getRoundViolationFacilityAttribute()
    {
		$item = AppraisePrice::where('appraise_id', $this->id)->where('slug', 'round_violation_facility')->first();
		return isset($item->value) ? (int)$item->value : 0;
    }

	public function getRoundAppraiseTotalAttribute()
    {
		$item = AppraisePrice::where('appraise_id', $this->id)->where('slug', 'round_appraise_total')->first();
		return isset($item->value) ? (int)$item->value : 0;
    }

	public function getPriceLandAssetAttribute()
    {
		$item = AppraisePrice::where('appraise_id', $this->id)->where('slug', 'land_asset_price')->first();
		return isset($item->value) ? $item->value : 0;
    }

	public function getPriceTangibleAssetAttribute()
    {
		$item = AppraisePrice::where('appraise_id', $this->id)->where('slug', 'tangible_asset_price')->first();
		return isset($item->value) ? $item->value : 0;
    }

	public function getPriceOtherAssetAttribute()
    {
		$item = AppraisePrice::where('appraise_id', $this->id)->where('slug', 'other_asset_price')->first();
		return isset($item->value) ? $item->value : 0;
    }

	public function getPriceTotalAssetAttribute()
    {
		$item = AppraisePrice::where('appraise_id', $this->id)->where('slug', 'total_asset_price')->first();
		return isset($item->value) ?  $item->value : 0;
    }

	/* public function getTotalAssetPriceAttribute()
    {
		$item = AppraisePrice::where('appraise_id', $this->id)->where('slug', 'total_asset_price')->first();
		return isset($item->value) ? $item->value : 0;
    } */

	public function getTotalAssetPriceAttribute()
    {
		$item = AppraisePrice::where('appraise_id', $this->id)->where('slug', 'total_asset_price')->first();
		$value = isset($item->value) ? $item->value : 0;
		$asset = new \stdClass();
		$asset->round_appraise_total = $this->getRoundAppraiseTotalAttribute();

		return CommonService::roundTotalAssetsPrice($asset, $value);
    }

    public function getLayerCuttingPriceAttribute()
    {
		$item = AppraisePrice::where('appraise_id', $this->id)->where('slug', 'layer_cutting_price')->first();
		return isset($item->value) ? floatval($item->value) : null;
    }

    public function getLayerCuttingProcedureAttribute()
    {
		$item = AppraiseAppraisalMethods::where('appraise_id', $this->id)->where('slug', 'layer_cutting_procedure')->first();
		return isset($item->slug_value) ? boolval($item->slug_value) : false;
    }

	public function getUnifyIndicativePriceSlugAttribute()
    {
		$item = AppraiseAppraisalMethods::where('appraise_id', $this->id)->where('slug', 'thong_nhat_muc_gia_chi_dan')->first();
		if(!isset($item->slug_value)) {
			$item = AppraiseAppraisalMethods::create([
				'appraise_id' => $this->id,
				'slug' => "thong_nhat_muc_gia_chi_dan",
				'slug_value' => "trung-binh",
			]);
		}
		return isset($item->slug_value) ? $item->slug_value : '';
    }

	public function getCompositeLandRemaningSlugAttribute()
    {
		$item = AppraiseAppraisalMethods::where('appraise_id', $this->id)->where('slug', 'tinh_gia_dat_hon_hop_con_lai')->first();
		if(!isset($item->slug_value)) {
			$item = AppraiseAppraisalMethods::create([
				'appraise_id' => $this->id,
				'slug' => "tinh_gia_dat_hon_hop_con_lai",
				'slug_value' => "theo-chi-phi-chuyen-mdsd-dat",
			]);
		}
		return isset($item->slug_value) ? $item->slug_value : '';
    }

	public function getCompositeLandRemaningValueAttribute()
    {
		$item = AppraiseAppraisalMethods::where('appraise_id', $this->id)->where('slug', 'tinh_gia_dat_hon_hop_con_lai')->first();
		if(!isset($item->slug_value)) {
			$item = AppraiseAppraisalMethods::create([
				'appraise_id' => $this->id,
				'slug' => "tinh_gia_dat_hon_hop_con_lai",
				'slug_value' => "theo-chi-phi-chuyen-mdsd-dat",
			]);
		}
		return isset($item->value) ? $item->value : null;
    }

	public function getPlanningViolationPriceSlugAttribute()
    {
		$item = AppraiseAppraisalMethods::where('appraise_id', $this->id)->where('slug', 'tinh_gia_dat_vi_pham_quy_hoach')->first();
		if(!isset($item->slug_value)) {
			$item = AppraiseAppraisalMethods::create([
				'appraise_id' => $this->id,
				'slug' => "tinh_gia_dat_vi_pham_quy_hoach",
				'slug_value' => "theo-gia-dat-qd-ubnd",
			]);
		}
		return isset($item->slug_value) ? $item->slug_value : '';
    }

	public function getPlanningViolationPriceValueAttribute()
    {
		$item = AppraiseAppraisalMethods::where('appraise_id', $this->id)->where('slug', 'tinh_gia_dat_vi_pham_quy_hoach')->first();
		if(!isset($item->slug_value)) {
			$item = AppraiseAppraisalMethods::create([
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
        return $this->hasMany(AppraisePic::class, 'appraise_id');
    }

    public function properties(): HasMany
    {
        return $this->hasMany(AppraiseProperty::class, 'appraise_id');
    }

    public function tangibleAssets(): HasMany
    {
        return $this->hasMany(AppraiseTangibleAsset::class, 'appraise_id')->orderBy('id');
    }

    public function otherAssets(): HasMany
    {
        return $this->hasMany(AppraiseOtherAsset::class, 'appraise_id');
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
        return $this->hasMany(AppraiseLaw::class, 'appraise_id')->orderBy('id', 'ASC');
    }

    public function constructionCompany(): HasMany
    {
        return $this->hasMany(ConstructionCompany::class, 'appraise_id');
    }

    /* public function assetGeneral(): belongsToMany
    {
        return $this->belongsToMany(CompareAssetGeneral::class,'appraise_has_assets','appraise_id','asset_general_id')->orderBy('id', 'DESC');
    } */

	/* public function getAssetGeneralAttribute()
    {
		$result = [];
        if (isset($this->id)&&!empty($this->id)) {
			$items = AppraiseHasAsset::where('appraise_id', $this->id)->get();
			foreach($items as $item) {
				$compareAssetGeneralRepository = new EloquentCompareAssetGeneralRepository(new CompareAssetGeneral());
				$item = $compareAssetGeneralRepository->findVersionById($item->asset_general_id, $item->version);
				$item = json_decode (json_encode ($item), FALSE);
				$result[] = $item;
			}
		}

		return $result;
    } */
	public function getAssetGeneralAttribute()
    {
		$result = [];
        if (isset($this->id)&&!empty($this->id)) {
			$items = AppraiseHasAsset::where('appraise_id', $this->id)->get();
			foreach($items as $item) {
				$compareAssetGeneralRepository = new EloquentCompareAssetGeneralRepository(new CompareAssetGeneral());
				$item = $compareAssetGeneralRepository->findVersionById($item->asset_general_id, $item->version);
				//begin -group ELS
				if(isset($item['created_by'])) {
					$item['createdBy'] = $item['created_by'];
					//unset($item['created_by']);
				}
				if(isset($item['asset_type'])) {
					$item['assetType'] = $item['asset_type'];
					//unset($item['asset_type']);
				}
				if(isset($item['transaction_type'])) {
					$item['transactionType'] = $item['transaction_type'];
					//unset($item['transaction_type']);
				}
				if(isset($item['topographic_data'])) {
					$item['topographicData'] = $item['topographic_data'];
					//unset($item['topographic_data']);
				}

				if(isset($item['properties'])) {
					$itemTmp = [];
					foreach($item['properties'] as $stt => $property) {
						$itemTmp[$stt] = $property;
						//unset($itemTmp[$stt]['property_detail']);
						$itemTmp[$stt]['propertyDetail'] = $item['properties'][$stt]['property_detail'];
						foreach($property['property_detail'] as $stt1 => $propertyDetail) {
							if(isset($item['properties'][$stt]['property_detail'][$stt1]['land_type_purpose_data'])) {
								$itemTmp[$stt]['propertyDetail'][$stt1]['landTypePurposeData'] = $item['properties'][$stt]['property_detail'][$stt1]['land_type_purpose_data'];
							} else {
								$itemTmp[$stt]['propertyDetail'][$stt1]['landTypePurposeData'] = null;
							}
							//unset($itemTmp[$stt]['propertyDetail'][$stt1]['land_type_purpose_data']);

							if(isset($item['properties'][$stt]['property_detail'][$stt1]['position_type'])) {
								$itemTmp[$stt]['propertyDetail'][$stt1]['positionType'] = $item['properties'][$stt]['property_detail'][$stt1]['position_type'];
							} else {
								$itemTmp[$stt]['propertyDetail'][$stt1]['positionType'] = null;
							}
							//unset($itemTmp[$stt]['propertyDetail'][$stt1]['position_type']);

							if(isset($item['properties'][$stt]['property_detail'][$stt1]['land_type_purpose'])) {
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
				if(isset($item['tangible_assets'])) {
					$itemTmp = $item['tangible_assets'];
					foreach($item['tangible_assets'] as $stt => $tangibleAssets) {
						if(isset($item['tangible_assets'][$stt]['building_type'])) {
							$itemTmp[$stt]['buildingType'] = $item['tangible_assets'][$stt]['building_type'];
						} else {
							$itemTmp[$stt]['buildingType'] = null;
						}
						//unset($itemTmp[$stt]['building_type']);

						if(isset($item['tangible_assets'][$stt]['building_category'])) {
							$itemTmp[$stt]['buildingCategory'] = $item['tangible_assets'][$stt]['building_category'];
						} else {
							$itemTmp[$stt]['buildingCategory'] = null;
						}
						//unset($itemTmp[$stt]['building_category']);
					}
					$item['tangibleAssets'] = $itemTmp;
					//unset($item['tangible_assets']);
				}

				if(isset($item['asset_type'])) {
					$item['assetType'] = $item['asset_type'];
					//unset($item['asset_type']);
				}
				if(isset($item['other_assets'])) {
					$item['otherAssets'] = $item['other_assets'];
					//unset($item['other_assets']);
				}
				if(isset($item['block_specification'])) {
					$item['blockSpecification'] = $item['block_specification'];
					//unset($item['block_specification']);
				}
				if(isset($item['room_details'])) {
					$item['roomDetails'] = $item['room_details'];
					//unset($item['room_details']);
				}
				//end -group ELS
				$item = json_decode (json_encode ($item), FALSE);
				$result[] = $item;
			}
		}
		usort($result, function($a, $b) {
			$aId = (isset($a->id)) ? $a->id : 0;
			$bId = (isset($b->id)) ? $b->id : 0;
			return $aId < $bId;
		});

		return $result;
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function version(): HasMany
    {
        return $this->hasMany(AppraiseVersion::class, 'appraise_id');
    }
    public function lastVersion (): HasOne
    {
        return $this->hasOne(AppraiseVersion::class, 'appraise_id')->latest();
    }
    public function comparisonFactor(): HasMany
    {
        return $this->hasMany(AppraiseComparisonFactor::class, 'appraise_id')->orderBy('asset_general_id', 'DESC')->orderBy('position');
    }

	public function appraiseAdapter(): HasMany
    {
        return $this->hasMany(AppraiseAdapter::class, 'appraise_id')->orderBy('asset_general_id', 'DESC');
    }

    public function appraiseHasAssets(): HasMany
    {
        return $this->hasMany(AppraiseHasAsset::class,'appraise_id')->orderBy('asset_general_id', 'DESC');
    }

    public function comparisonTangibleFactor(): HasMany
    {
        return $this->hasMany(AppraiseTangibleComparisonFactor::class, 'appraise_id');
    }

	public function assetUnitPrice(): HasMany
    {
        return $this->hasMany(AppraiseUnitPrice::class, 'appraise_id')->orderBy('land_type_id', 'ASC');;
    }

	public function assetUnitArea(): HasMany
    {
        return $this->hasMany(AppraiseUnitArea::class, 'appraise_id')->orderBy('land_type_id', 'ASC');;
    }

	public function assetPrice(): HasMany
    {
        return $this->hasMany(AppraisePrice::class, 'appraise_id');
    }

	public function appraisalMethods(): HasMany
    {
        return $this->hasMany(AppraiseAppraisalMethods::class, 'appraise_id');
    }

    public function appraisal(): HasMany
    {
        return $this->hasMany(AppraiseAppraisalMethods::class, 'appraise_id');
    }
	public function getDescriptionAttribute()
    {
		$item = AppraiseProperty::where('appraise_id', $this->id)->first();
		return isset($item['description']) ? $item['description'] : '';
    }

    public function getLandDetailsAttribute()
    {
        $select = ['id','appraise_id','front_side_width','insight_width','land_shape_id','appraise_land_sum_area','coordinates','legal_id'];

        $with=['landShape:id,type,description',
                'legal:id,type,description',
            ];

		$item = AppraiseProperty::with($with)->where('appraise_id', $this->id)->whereNotNull(['insight_width'])->select($select)->first();
        if(isset($item))
            $item->append('topographic');
		return $item;
    }
    public function getTotalAreaAttribute()
    {
        $select = ['id','appraise_property_id','land_type_purpose_id','total_area','is_transfer_facility'];
        $with=['landTypePurpose:id,type,description'];
		$propertieId = AppraiseProperty::where('appraise_id', $this->id)->first()->id;

        $result = AppraisePropertyDetail::with($with)
            ->select($select)
            ->where(['appraise_property_id'=>$propertieId])
            ->where('total_area','>',0)
            ->get();
        return $result;

    }

    public function getPlanningAreaAttribute()
    {
        $select = ['id','appraise_property_id','land_type_purpose_id','planning_area','type_zoning'];
        $with=['landTypePurpose:id,type,description'];
		$propertieId = AppraiseProperty::where('appraise_id', $this->id)->first()->id;

        $result = AppraisePropertyDetail::with($with)
            ->select($select)
            ->where(['appraise_property_id'=>$propertieId])
            ->where(['is_zoning'=>true])
            ->get();
        return $result;

    }
    public function getUbndPriceAttribute()
    {
        $select = ['id','appraise_property_id','land_type_purpose_id','circular_unit_price','position_type_id'];
        $with=['positionType:id,type,description','landTypePurpose:id,type,description'];
		$propertieId = AppraiseProperty::where('appraise_id', $this->id)->first()->id;


        $result = AppraisePropertyDetail::with($with)
            ->select($select)
            ->where(['appraise_property_id'=>$propertieId])
            ->get();
        return $result;
    }
    public function getTrafficInfomationAttribute()
    {
        $select =['id','appraise_id','front_side','individual_road','main_road_length','material_id','two_sides_land','description'];
        $with=[
                'material:id,type,description',
                'propertyTurningTime:id,appraise_property_id,main_road_length,is_alley_with_connection,turning,material_id,main_road_distance',
                'propertyTurningTime.material:id,type,description'
            ];
        $result = AppraiseProperty::with($with)
            ->select($select)
            ->where(['appraise_id'=>$this->id])
            ->get()->first();
        return $result;
    }
    public function getEconomicInfomationAttribute()
    {
        $select =['id','appraise_id','business_id','social_security_id','feng_shui_id','zoning_id','condition_id'];
        $with=[
                'business:id,type,description',
                'socialSecurity:id,type,description',
                'fengShui:id,type,description',
                'conditions:id,type,description',
                'zoning:id,type,description',
            ];
        $result = AppraiseProperty::with($with)
            ->select($select)
            ->where(['appraise_id'=>$this->id])
            ->get()->first();
        return $result;
    }
    public function getGeneralInfomationAttribute()
    {
        $select =['id','status','asset_type_id','province_id','district_id','ward_id','street_id','distance_id','topographic_id','coordinates','appraise_asset', 'full_address'];
        $with=[
                'createdBy:id,name',
                'topographic:id,description',
                'assetType:id,description',
                'distance:id,name,street_id',
                'province:id,name',
                'district:id,name',
                'ward:id,name',
                'street:id,name'
            ];
        $result = Appraise::with($with)
            ->select($select)
            ->where(['id'=>$this->id])
            ->get()->first();

        return $result;
    }
    public function getValueBaseAndApproachAttribute()
    {
        $select = ['id', 'appraise_basis_property_id', 'appraise_principle_id',
        'document_description', 'appraise_approach_id', 'appraise_method_used_id',
       ];
        $with= [
            'appraiseApproach:id,name,description,type',
            'appraisePrinciple:id,name,description,type',
            'appraiseMethodUsed:id,name,description,type',
            'appraiseBasisProperty:id,name,description,type',
            ];
        $result = Appraise::with($with)
        ->select($select)
        ->where('id', $this->id)
        ->whereNotNull('appraise_approach_id')
        ->first();
        // add default value
        if(! isset($result)){
            $result = Appraise::where('id', '=', $this->id)->get('id')->first();
            $result->append('appraise_approach_id');
            $result->append('appraise_basis_property_id');
            $result->append('appraise_method_used_id');
            $result->append('appraise_principle_id');
            $result->append('document_description');
        }
        return $result;
    }
    public function getAppraisalMethodsAttribute()
    {
        $result = [];
        if(AppraiseAppraisalMethods::where('appraise_id',$this->id)->exists())
        {
            $select = [ 'slug_value','value',
                        ];
                $with= [
                        ];
            $result1 = AppraiseAppraisalMethods::with($with)
                ->select($select)
                ->where(['appraise_id'=>$this->id])
                ->where('slug',['thong_nhat_muc_gia_chi_dan'])
                ->first();

            $result2 = AppraiseAppraisalMethods::with($with)
                ->select($select)
                ->where(['appraise_id'=>$this->id])
                ->where('slug',['tinh_gia_dat_hon_hop_con_lai'])
                ->first();
            $result3 = AppraiseAppraisalMethods::with($with)
                ->select($select)
                ->where(['appraise_id'=>$this->id])
                ->where('slug',['tinh_gia_dat_vi_pham_quy_hoach'])
                ->first();

            $result = array_merge(['thong_nhat_muc_gia_chi_dan' => $result1],
                            ['tinh_gia_dat_hon_hop_con_lai' => $result2],
                            ['tinh_gia_dat_vi_pham_quy_hoach' => $result3]
                        );
        }
        else
        {
            $result1=[
                            'slug_value' => 'trung-binh',
                            'value' => null,
                        ];
            $result2=[
                            'slug_value' => 'theo-chi-phi-chuyen-mdsd-dat',
                            'value' => null,
                        ];
            $result3=[
                            'slug_value' => 'theo-gia-dat-qd-ubnd',
                            'value' => null,
                        ];
            $result = array_merge(['thong_nhat_muc_gia_chi_dan' => $result1],
                                ['tinh_gia_dat_hon_hop_con_lai' => $result2],
                                ['tinh_gia_dat_vi_pham_quy_hoach' => $result3]
                    );
        }

        return $result;
    }

    public function getMapImgAttribute()
    {
        $select = ['id', 'appraise_id','link','type_id'
                ];
        $with= [
        ];
        $result = AppraisePic::with($with)
        ->select($select)
        ->where(['appraise_id'=>$this->id])
        ->where('type_id',153)
        ->get()
        ->first();

        return isset($result['link']) ? $result['link'] : '';
        // return $result;
    }
    public function getAssetsGeneralAttribute()
    {
		$result = [];
        if (isset($this->id)&&!empty($this->id)) {
			$items = AppraiseHasAsset::where('appraise_id', $this->id)->get();
            $stt =0;
			foreach($items as $item) {
				$compareAssetGeneralRepository = new EloquentCompareAssetGeneralRepository(new CompareAssetGeneral());
				$result[$stt] = $compareAssetGeneralRepository->findVersionById_v2($item->asset_general_id, $item->version);
                $result[$stt]['version'] = $item->version;
                $stt++;
            }
        }
        return $result;
    }
    public function getDistanceMaxAttribute()
    {
        if (isset($this->id)&&!empty($this->id)) {
			$items = AppraiseHasAsset::where('appraise_id', $this->id)->get();
            if(isset($items) && count($items) ==3 ){
                $appraiseLocation = $this->coordinates;
                $distanceMax = 0;
                $stt =0;
                foreach($items as $item) {

                    $compareAssetGeneralRepository = new EloquentCompareAssetGeneralRepository(new CompareAssetGeneral());
                    $result[$stt] = $compareAssetGeneralRepository->findVersionById_v2($item->asset_general_id, $item->version);

                    $assetLocation =  isset($result[$stt]['coordinates']) ? $result[$stt]['coordinates'] : $appraiseLocation;
                    // dump($assetLocation);
                    $calDistance =  CommonService::calAppraiseAssetDistance($appraiseLocation,$assetLocation);

                    if($calDistance > $distanceMax){
                        $distanceMax = $calDistance;
                    }
                    // dump($distanceMax);
                    $stt++;
                }
                $distance = ($distanceMax ==0) ? 2 : $distanceMax;
            }else
                $distance = 2;
        }
        // dd($distance);
        return CommonService::roundDistance($distance);
    }

    public function getConstructionAttribute()
    {
        $result =[];
        if (AppraiseTangibleAsset::where('appraise_id', $this->id)->exists()) {
            $select = ['id', 'appraise_id', 'building_type_id', 'building_category_id',
                        'floor', 'remaining_quality', 'total_construction_base',
                        'total_construction_area', 'start_using_year', 'duration',
                        'description', 'other_building', 'rate_id', 'structure_id',
                        'crane_id', 'aperture_id', 'factory_type_id', 'contruction_description',
                        'gpxd','created_at','tangible_name'
                    ];
            $with= [
                    'buildingType:id,type,description',
                    'buildingCategory:id,type,description',
                    'rate:id,type,description',
                    'structure:id,type,description',
                    'crane:id,type,description',
                    'aperture:id,type,description',
                    'factoryType:id,type,description',
                    ];
            $result = AppraiseTangibleAsset::with($with)
                ->select($select)
                ->where(['appraise_id'=>$this->id])
                ->orderBy('id')
                ->get();
        }
        return  $result;
    }

    public function getTotalConstructionAreaAttribute(){
        return floatval($this->tangibleAssets()->sum('total_construction_area')) ;
    }
    public function getTotalConstructionBaseAttribute(){
        return floatval($this->tangibleAssets()->sum('total_construction_base')) ;
    }
    public function getAppraiseLandSumAreaAttribute(){
        return floatval($this->properties()->sum('appraise_land_sum_area')) ;
    }

    public function getAppraiseApproachIdAttribute($value)
    {
        if( isset($value))
            return $value;
        $select = ['id'];
        $where = [
            'is_defaults' => true,
            'type' => 'CACH_TIEP_CAN_CHI_PHI',
        ];
		$item = AppraiseOtherInformation::where($where)->select($select)->first();
		return $item->id;
    }
    public function getAppraisePrincipleIdAttribute($value)
    {
        if( isset($value))
            return $value;
        $select = ['id'];
        $where = [
            'is_defaults' => true,
            'type' => 'NGUYEN_TAC_THAM_DINH',
        ];
		$item = AppraiseOtherInformation::where($where)->select($select)->first();
		return $item->id;
    }
    public function getAppraiseMethodUsedIdAttribute($value)
    {
        if( isset($value))
            return $value;
        $select = ['id'];
        $where = [
            'is_defaults' => true,
            'type' => 'PHUONG_PHAP_THAM_DINH_SU_DUNG',
        ];
		$item = AppraiseOtherInformation::where($where)->select($select)->first();
		return $item->id;
    }
    public function getAppraiseBasisPropertyIdAttribute($value)
    {
        if( isset($value))
            return $value;
        $select = ['id'];
        $where = [
            'is_defaults' => true,
            'type' => 'CO_SO_THAM_DINH',
        ];
		$item = AppraiseOtherInformation::where($where)->select($select)->first();
		return $item->id;
    }
    // public function getDocumentDescriptionAttribute()
    // {
    //     return 'Các hồ sơ, tài liệu về tài sản do khách hàng cung cấp là đầy đủ và tin cậy';
    // }

    public function getDocumentDescriptionAttribute()
    {
        return `
        + Giả thiết:
        + Giả thiết đặc biệt:`;
    }

    // protected function getDescriptionCapitalizeAttribute()
    // {
    //     $dictionaryId = Dictionary::where('id',$this->compari)
    //     return ucfirst($this->description);
    // }

    public function realEstate():BelongsTo
    {
        return $this->belongsTo(RealEstate::class, 'real_estate_id', 'id');
    }
    public function certificate():BelongsTo
    {
        return $this->belongsTo(Certificate::class, 'certificate_id', 'id');
    }
    public function getAppraisalClclAttribute()
    {
		$item = AppraiseAppraisalMethods::where('appraise_id', $this->id)->where('slug', 'XAC_DINH_CHAT_LUONG_CON_LAI')->first();
		return $item;
    }
	public function getAppraisalDgxdAttribute()
    {
		$item = AppraiseAppraisalMethods::where('appraise_id', $this->id)->where('slug', 'XAC_DINH_DON_GIA_XAY_DUNG')->first();
		return $item;
    }

}
