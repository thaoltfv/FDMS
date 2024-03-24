<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Services\CommonService;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

use App\Repositories\EloquentCompareAssetGeneralRepository;
use App\Models\CompareAssetGeneral;

/**
 * Class Province
 * @package App\Models
 */
class PriceEstimate extends Model
{
    use SoftDeletes;

    protected $table = 'price_estimates';
    //protected $dateFormat = 'd-m-Y H:i:s';
    protected $casts = [
        'id' => 'integer',
    ];
    protected $appends = [
        // 'total_construction_base'
    ];
    protected $fillable = [
        'status',
        'asset_type_id',
        'province_id',
        'district_id',
        'ward_id',
        'street_id',
        'distance_id',
        'land_no',
        'doc_no',
        'coordinates',
        'address_number',
        'price_estimate_asset',
        'created_by',
        'updated_at',
        'step',
        'appraise_asset',
        'full_address',
        'full_address_street',
        'appraise_id',
        'project_id',
        'apartment_asset_id'
    ];
    protected $guarded = [];

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

    public function properties(): HasMany
    {
        return $this->hasMany(PriceEstimateProperty::class, 'price_estimate_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function version(): HasMany
    {
        return $this->hasMany(PriceEstimateVersion::class, 'price_estimate_id');
    }

    public function lastVersion(): HasOne
    {
        return $this->hasOne(PriceEstimateVersion::class, 'price_estimate_id')->latest();
    }

    // public function finalEstimate(): HasMany
    // {
    //     return $this->hasMany(PriceEstimateFinal::class, 'price_estimate_id');
    // }

    public function getFinalEstimateAttribute()
    {
        $with = [
            'planningArea',
            'planningArea.landTypePurpose:id,description,acronym',
            'totalArea',
            'totalArea.landTypePurpose:id,description,acronym',
            'tangibleAssets',
            'tangibleAssets.buildingType:id,description',
            'appraisePurpose',
            'assetType',
        ];
        return PriceEstimateFinal::with($with)
            ->where('price_estimate_id', $this->id)
            ->first();
    }

    public function getLandDetailsAttribute()
    {
        $select = ['id', 'price_estimate_id', 'front_side', 'main_road_length', 'material_id', 'description', 'appraise_land_sum_area'];

        $with = [
            // 'landShape:id,type,description',
            // 'legal:id,type,description',
        ];

        $item = PriceEstimateProperty::with($with)->where('price_estimate_id', $this->id)->select($select)->first();
        // if (isset($item))
        //     $item->append('topographic');
        return $item;
    }
    public function getTotalAreaAttribute()
    {
        $select = ['id', 'price_estimate_property_id', 'land_type_purpose_id', 'total_area', 'is_transfer_facility', 'main_area'];
        $with = ['landTypePurpose:id,type,description'];
        $propertieId = PriceEstimateProperty::where('price_estimate_id', $this->id)->first()->id;

        $result = PriceEstimatePropertyDetail::with($with)
            ->select($select)
            ->where(['price_estimate_property_id' => $propertieId])
            ->where('total_area', '>', 0)
            ->get();
        return $result;
    }

    public function getPlanningAreaAttribute()
    {
        $select = ['id', 'price_estimate_property_id', 'land_type_purpose_id', 'type_zoning', 'planning_area'];
        $with = ['landTypePurpose:id,type,description'];
        $propertieId = PriceEstimateProperty::where('price_estimate_id', $this->id)->first()->id;

        $result = PriceEstimatePropertyDetail::with($with)
            ->select($select)
            ->where(['price_estimate_property_id' => $propertieId])
            ->where('planning_area', '>', 0)
            // ->whereNotNull(['extra_planning'])
            ->get();
        return $result;
    }

    public function getTrafficInfomationAttribute()
    {
        $select = ['id', 'price_estimate_id', 'front_side', 'main_road_length', 'material_id', 'description', 'appraise_land_sum_area'];
        $with = [
            'material:id,type,description',
            'propertyTurningTime:id,price_estimate_property_id,main_road_length,turning,material_id,main_road_distance',
            'propertyTurningTime.material:id,type,description'
        ];
        $result = PriceEstimateProperty::with($with)
            ->select($select)
            ->where(['price_estimate_id' => $this->id])
            ->get()->first();
        return $result;
    }
    public function getGeneralInfomationAttribute()
    {
        // $select = ['id', 'status', 'asset_type_id', 'province_id', 'district_id', 'ward_id', 'street_id', 'distance_id', 'topographic_id', 'coordinates', 'price_estimate_asset', 'full_address', 'apartment_number', 'doc_no', 'plot_num'];
        $with = [
            'createdBy:id,name',
            'assetType:id,description',
            'distance:id,name,street_id',
            'province:id,name',
            'district:id,name',
            'ward:id,name',
            'street:id,name'
        ];
        $result = PriceEstimate::with($with)
            // ->select($select)
            ->where(['id' => $this->id])
            ->get()->first();

        return $result;
    }

    public function getAssetGeneralAttribute()
    {
        $result = [];
        if (isset($this->id) && !empty($this->id)) {
            $items = PriceEstimateHasAsset::where('price_estimate_id', $this->id)->get();
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
        usort($result, function ($a, $b) {
            $aId = (isset($a->id)) ? $a->id : 0;
            $bId = (isset($b->id)) ? $b->id : 0;
            return $aId < $bId;
        });

        return $result;
    }
    public function getDistanceMaxAttribute()
    {
        if (isset($this->id) && !empty($this->id)) {
            $items = PriceEstimateHasAsset::where('price_estimate_id', $this->id)->get();
            if (isset($items) && count($items) > 1 && count($items) <= 3) {
                $appraiseLocation = $this->coordinates;
                $distanceMax = 0;
                $stt = 0;
                foreach ($items as $item) {

                    $compareAssetGeneralRepository = new EloquentCompareAssetGeneralRepository(new CompareAssetGeneral());
                    $result[$stt] = $compareAssetGeneralRepository->findVersionById_v2($item->asset_general_id, $item->version);

                    $assetLocation =  isset($result[$stt]['coordinates']) ? $result[$stt]['coordinates'] : $appraiseLocation;
                    // dump($assetLocation);
                    $calDistance =  CommonService::calAppraiseAssetDistance($appraiseLocation, $assetLocation);

                    if ($calDistance > $distanceMax) {
                        $distanceMax = $calDistance;
                    }
                    // dump($distanceMax);
                    $stt++;
                }
                $distance = ($distanceMax == 0) ? 2 : $distanceMax;
            } else
                $distance = 2;
        }
        // dd($distance);
        return CommonService::roundDistance($distance);
    }
    public function getAssetsGeneralAttribute()
    {
        $result = [];
        if (isset($this->id) && !empty($this->id)) {
            $items = PriceEstimateHasAsset::where('price_estimate_id', $this->id)->get();
            $stt = 0;
            foreach ($items as $item) {
                $compareAssetGeneralRepository = new EloquentCompareAssetGeneralRepository(new CompareAssetGeneral());
                $result[$stt] = $compareAssetGeneralRepository->findVersionById_v2($item->asset_general_id, $item->version);
                $result[$stt]['version'] = $item->version;
                $stt++;
            }
        }
        return $result;
    }
    public function getMapImgAttribute()
    {
        $select = [
            'id', 'price_estimate_id', 'link', 'type_id'
        ];
        $with = [];
        $result = PriceEstimatePic::with($with)
            ->select($select)
            ->where(['price_estimate_id' => $this->id])
            ->where('type_id', 153)
            ->get()
            ->first();

        return isset($result['link']) ? $result['link'] : '';
        // return $result;
    }
}
