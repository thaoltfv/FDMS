<?php

namespace App\Repositories;

use App\Contracts\EstimatePriceRepository;
use App\Enum\EstimateAssetDefault;
use App\Enum\ValueDefault;
use App\Models\CompareAssetGeneral;
use App\Models\Dictionary;
use App\Models\Distance;
use App\Models\UnitPrice;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use const App\Enum\ERROR_MESSAGE;

class EloquentEstimatePriceRepository extends EloquentRepository implements EstimatePriceRepository
{

    /**
     * @param $id
     * @return Builder[]|Collection
     */
    public function findByIds($id)
    {
        $ids = json_decode($id);
        return $this->model->query()
            ->whereIn('id', $ids, 'or', false)
            ->with('createdBy')
            ->with('province')
            ->with('district')
            ->with('ward')
            ->with('street')
            ->with('distance')
            ->with('assetType')
            ->with('source')
            ->with('apartment')
            ->with('pic')
            ->with('topographicData')
            ->with('properties.propertyDetail')
            ->with('properties.comparePropertyTurningTime')
            ->with('properties.comparePropertyTurningTime.material')
            ->with('properties.propertyDetail.landTypePurposeData')
            ->with('properties.propertyDetail.positionType')
            ->with('properties.comparePropertyDoc')
            ->with('properties.pic')
            ->with('properties.legal')
            ->with('properties.zoning')
            ->with('properties.landType')
            ->with('properties.landShape')
            ->with('properties.business')
            ->with('properties.electricWater')
            ->with('properties.socialSecurity')
            ->with('properties.fengShui')
            ->with('properties.paymenMethod')
            ->with('properties.conditions')
            ->with('properties.material')
            ->with('tangibleAssets.buildingType')
            ->with('tangibleAssets.buildingCategory')
            ->with('tangibleAssets.compareProperty')
            ->with('tangibleAssets.pic')
            ->with('tangibleAssets.rate')
            ->with('tangibleAssets.structure')
            ->with('tangibleAssets.crane')
            ->with('tangibleAssets.aperture')
            ->with('tangibleAssets.factoryType')
            ->with('otherAssets.otherTypeAsset')
            ->with('otherAssets.pic')
            ->with('blockSpecification')
            ->with('blockSpecification.basicUtilities')
            ->with('blockSpecification.blockLists')
            ->with('roomDetails')
            ->with('roomDetails.roomFurnitureDetails')
            ->with('roomDetails.direction')
            ->with('roomDetails.furnitureQuality')
            ->get();
    }

    /**
     * @param $objects
     * @return array
     */
    public function estimateRecognizedFrontSiteAsset($objects): array
    {
        $search = [
            'location' => $objects['location'] ?? null,
            'distance' => $objects['distance'] ?? null,
            'front_side' => $objects['front_side'] ?? null,
            'asset_types' => EstimateAssetDefault::LAND_ASSET_TYPE,
            'land_type_purpose' => null,
            'province_id' => $objects['province_id'] ?? null,
            'district_id' => $objects['district_id'] ?? null,
            'street_id' => $objects['street_id'] ?? null,
            'not_street_id' => null,
            'main_road_length' => null,
            'main_road_length_to' => null,
        ];
        $result = [];
        if (isset($objects['recognized'])) {
            foreach ($objects['recognized'] as $value) {
                $search['land_type_purpose'] = $value['land_type_purpose'];
                $assets = $this->findAssetEstimate($search);
                $ids = [];

                foreach ($assets as $asset) {
                    $ids[] = $asset['id'];
                }
                $data = $this->findByIds(json_encode($ids));
                $distance = null;

                foreach ($data as $record) {
                    $distance = !$distance ? ($record->distance->name ?? null) : $distance;
                }
                if (!$distance) {
                    $distance = $this->findDistance($objects['street_id']);
                    $distance = $distance->name ?? null;
                }
                $unitPrice = $this->findUnitPriceEstimate($value['land_type_purpose_name'], $distance, $objects['street'], $objects['ward'], $objects['district'], $objects['province']);
                if ($unitPrice) {
                    $averageLandUnitPrice = round((int)($unitPrice->vt1 ?? 0), -5);
                    $result['recognized'][] = [
                        'land_type_purpose' => $value['land_type_purpose'],
                        'land_type_purpose_name' => $value['land_type_purpose_name'],
                        'area' => $value['area'],
                        'average_land_unit_price' => $averageLandUnitPrice,
                        'estimate_price' => round($averageLandUnitPrice * $value['area'],-5),
                    ];
                    $result['status'] = ValueDefault::STATUS_SUCCESS;
                    $result['steps'] = EstimateAssetDefault::STEP_1_1;
                    $result['error_message'] = null;

                } else {
                    $result['recognized'][] = [
                        'land_type_purpose' => $value['land_type_purpose'],
                        'land_type_purpose_name' => $value['land_type_purpose_name'],
                        'area' => $value['area'],
                        'average_land_unit_price' => 0,
                        'estimate_price' => 0,
                    ];
                    $result['steps'] = EstimateAssetDefault::STEP_1_1;
                    $result['status'] = ValueDefault::STATUS_ERROR;
                    $result['error_message'] = EstimateAssetDefault::ERROR_MESSAGE;
                }
            }
        }
        return $result;
    }

    /**
     * @param $objects
     * @param $defaultLandType
     * @return array
     */
    public function estimateUnrecognizedFrontSiteAsset($objects, $defaultLandType): array
    {
        $result = [];
        $returnAsset = [];
        $caches = [];

        if ($objects['unrecognized']) {
            $assets = $this->getUnrecognizedFrontSiteAssetWithStreet($objects, $defaultLandType);
            $result['steps'] = EstimateAssetDefault::STEP_1_2;
            foreach ($assets as $asset) {
                $returnAsset[] = $asset;
            }
            if (count($assets)  !=0 ) {
                foreach ($objects['unrecognized'] as $value) {
                    $totalLandUnitPrice = 0;
                    $count = 0;
                    foreach ($assets as $asset) {
                        foreach ($asset['land_type_purpose_price'] as $land_type_purpose_price) {
                            if ($land_type_purpose_price['id'] == $value['land_type_purpose']) {
                                $totalLandUnitPrice += $land_type_purpose_price['price_land'] ?? 0;
                                $count += 1;
                            }
                        }
                    }
                    $averageLandUnitPrice = round((int)($totalLandUnitPrice / $count), -5);
                    $result['unrecognized'][] = [
                        'land_type_purpose' => $value['land_type_purpose'],
                        'land_type_purpose_name' => $value['land_type_purpose_name'],
                        'area' => $value['area'],
                        'average_land_unit_price' => $averageLandUnitPrice,
                        'estimate_price' => round($averageLandUnitPrice * $value['area'],-5),
                    ];
                }

                    if (count($returnAsset) >= 2) {
                        $reliability = ValueDefault::RELIABILITY_HIGHT;
                    } else {
                        $reliability = ValueDefault::RELIABILITY_NORMAL;
                    }

                $result['reliability'] = $reliability;
                $result['assets'] = array_values(array_map("unserialize", array_unique(array_map("serialize", $returnAsset))));
                $result['status'] = ValueDefault::STATUS_SUCCESS;
                $result['error_message'] = null;
                return $result;
            } else {
//                $caches = $this->getUnrecognizedFrontSiteAssetWithoutStreet($objects, $defaultLandType, $assets);
//                if (count($caches) == 0) {
//                    $caches = $this->getUnrecognizedFrontSiteAssetWithoutStreetByUnitPrice($objects, $defaultLandType, $assets);
//                }
//                foreach ($caches as $cache) {
//                    $returnAsset[] = $cache;
//                }
                $assets = $this->getUnrecognizedFrontSiteAssetWithStreetInGroupLandType($objects, count($returnAsset));
                $result['steps'] = EstimateAssetDefault::STEP_1_3;
                if (count($assets) == 0) {
                    $result = $this->getResult($objects, $defaultLandType, $result);
                } else {
                    foreach ($assets as $asset) {
                        $returnAsset[] = $asset;
                    }
                    $returnAsset = array_values(array_map("unserialize", array_unique(array_map("serialize", $returnAsset))));
                    if (count($returnAsset) > 0) {
                        $totalGroupLandUnitPrice = 0;
                        $averageGroupLandUnitPrice = 0;
                        $countGroupLand = 0;
                        foreach ($returnAsset as $asset) {
                            foreach ($asset['land_type_purpose_price'] as $land_type_purpose_price) {
                                if (in_array($land_type_purpose_price['id'], EstimateAssetDefault::GROUP_LAND_TYPE)) {
                                    $totalGroupLandUnitPrice += $land_type_purpose_price['price_land'] ?? 0;
                                    $countGroupLand += 1;
                                }
                            }
                        }
                        if ($countGroupLand != 0)
                            $averageGroupLandUnitPrice = (int)($totalGroupLandUnitPrice / $countGroupLand);
                        foreach ($objects['unrecognized'] as $value) {
                            $totalLandUnitPrice = 0;
                            $averageLandUnitPrice = 0;
                            $count = 0;
                            foreach ($returnAsset as $asset) {
                                foreach ($asset['land_type_purpose_price'] as $land_type_purpose_price) {
                                    if (in_array($value['land_type_purpose'], EstimateAssetDefault::GROUP_LAND_TYPE)) {
                                        $totalLandUnitPrice += $averageGroupLandUnitPrice ?? 0;
                                        $count += 1;
                                    } elseif ($land_type_purpose_price['id'] == $value['land_type_purpose']) {
                                        $totalLandUnitPrice += $land_type_purpose_price['price_land'] ?? 0;
                                        $count += 1;
                                    }
                                }
                            }
                            if ($count != 0) {
                                $averageLandUnitPrice = round((int)($totalLandUnitPrice / $count), -5);
                            }
                            if (count($returnAsset) >= 3) {
                                if (count($assets) >= 2) {
                                    $reliability = ValueDefault::RELIABILITY_HIGHT;
                                } else {
                                    $reliability = ValueDefault::RELIABILITY_NORMAL;
                                }
                            } else {
                                if (count($returnAsset) >= 2) {
                                    $reliability = ValueDefault::RELIABILITY_HIGHT;
                                } else {
                                    $reliability = ValueDefault::RELIABILITY_NORMAL;
                                }
                            }

                            $result['unrecognized'][] = [
                                'land_type_purpose' => $value['land_type_purpose'],
                                'land_type_purpose_name' => $value['land_type_purpose_name'],
                                'area' => $value['area'],
                                'average_land_unit_price' => $averageLandUnitPrice,
                                'estimate_price' => round($averageLandUnitPrice * $value['area'],-5),
                                ];
                        }
                        $result['reliability'] = $reliability;
                        $result['assets'] = array_values(array_map("unserialize", array_unique(array_map("serialize", $returnAsset))));
                        $result['status'] = ValueDefault::STATUS_SUCCESS;
                        $result['error_message'] = null;
                        return $result;
                    } else {
                        $result = $this->getResult($objects, $defaultLandType, $result);
                        return $result;
                    }
                }
            }
        }
        return $result;
    }

    public function getUnrecognizedFrontSiteAssetWithStreet($objects, $defaultLandType): array
    {
        $search = [
            'location' => $objects['location'] ?? null,
            'front_side' => $objects['front_side'] ?? null,
            'asset_types' => EstimateAssetDefault::LAND_ASSET_TYPE,
            'transaction_type_ids' => EstimateAssetDefault::TRANSACTION_TYPE,
            'land_type_purpose' => null,
            'province_id' => $objects['province_id'] ?? null,
            'district_id' => $objects['district_id'] ?? null,
            'street_id' => $objects['street_id'] ?? null,
        ];
        if (in_array($objects['district_id'], EstimateAssetDefault::URBAN_ASSET)) {
            $search['distance'] = 1;
        } else {
            $search['distance'] = 2;
        }
        $result = [];
        $landTypePurpose = [];
        if ($objects['unrecognized']) {
            foreach ($objects['unrecognized'] as $value) {
                $landTypePurpose[] = $value['land_type_purpose'];
            }
            $search['land_type_purpose'] = $landTypePurpose;

            $assets = $this->findAssetEstimate($search);
        }
        return $assets;
    }

    public function getUnrecognizedFrontSiteAssetWithStreetInGroupLandType($objects, $count = 0): array
    {
        $search = [
            'location' => $objects['location'] ?? null,
            'front_side' => $objects['front_side'] ?? null,
            'asset_types' => EstimateAssetDefault::LAND_ASSET_TYPE,
            'transaction_type_ids' => EstimateAssetDefault::TRANSACTION_TYPE,
            'land_type_purpose' => null,
            'province_id' => $objects['province_id'] ?? null,
            'district_id' => $objects['district_id'] ?? null,
            'street_id' => $objects['street_id'] ?? null,
        ];
        if (in_array($objects['district_id'], EstimateAssetDefault::URBAN_ASSET)) {
            $search['distance'] = 1;
        } else {
            $search['distance'] = 2;
        }
        $result = [];
        $landTypePurpose = [];
        if ($objects['unrecognized']) {
            foreach ($objects['unrecognized'] as $value) {
                $landTypePurpose[] = $value['land_type_purpose'];
                if (in_array($value['land_type_purpose'], EstimateAssetDefault::GROUP_LAND_TYPE)) {
                    $search['group_land_type_purpose'] = EstimateAssetDefault::GROUP_LAND_TYPE;
                }
            }
            $search['land_type_purpose'] = $landTypePurpose;

            $assets = $this->findAssetEstimate($search, 3 - $count);
        }
        return array_values(array_map("unserialize", array_unique(array_map("serialize", $assets))));

    }

    public function getUnrecognizedFrontSiteAssetWithoutStreet($objects, $defaultLandType, $caches): array
    {
        $search = [
            'location' => $objects['location'] ?? null,
            'front_side' => $objects['front_side'] ?? null,
            'asset_types' => EstimateAssetDefault::LAND_ASSET_TYPE,
            'transaction_type_ids' => EstimateAssetDefault::TRANSACTION_TYPE,
            'land_type_purpose' => null,
            'province_id' => $objects['province_id'] ?? null,
            'district_id' => $objects['district_id'] ?? null,
            'not_street_id' => $objects['street_id'] ?? null,
            'distance' => 1,
        ];

        $unitPrice = $this->getUnitPrice($objects, $defaultLandType);
        $circularUnitPrice = (int)($unitPrice->vt1 ?? 0);
        if ($circularUnitPrice == 0) {
            foreach ($caches as $cache) {
                foreach ($cache['land_type_purpose_price'] as $item) {
                    if ($item['id'] == $defaultLandType) {
                        $circularUnitPrice = $item['circular_unit_price'];
                    }
                }
            }
        }

        $landTypePurpose = [];
        if ($objects['unrecognized']) {
            foreach ($objects['unrecognized'] as $value) {
                $landTypePurpose[] = $value['land_type_purpose'];
            }
            $search['land_type_purpose'] = $landTypePurpose;
        }
        $search['street_id'] = null;
        $search['not_street_id'] = $objects['street_id'] ?? null;
        $search['circular_unit_price'] = $circularUnitPrice;
        $assets = $this->findAssetEstimate($search, 3 - count($caches));
        return array_values(array_map("unserialize", array_unique(array_map("serialize", $assets))));
    }

    public function getUnrecognizedFrontSiteAssetWithoutStreetByUnitPrice($objects, $defaultLandType, $caches): array
    {
        $search = [
            'location' => $objects['location'] ?? null,
            'front_side' => $objects['front_side'] ?? null,
            'asset_types' => EstimateAssetDefault::LAND_ASSET_TYPE,
            'transaction_type_ids' => EstimateAssetDefault::TRANSACTION_TYPE,
            'land_type_purpose' => null,
            'province_id' => $objects['province_id'] ?? null,
            'district_id' => $objects['district_id'] ?? null,
            'not_street_id' => $objects['street_id'] ?? null,
            'distance' => 1,
        ];

        $landTypePurpose = [];
        if ($objects['unrecognized']) {
            foreach ($objects['unrecognized'] as $value) {
                $landTypePurpose[] = $value['land_type_purpose'];
            }
            $search['land_type_purpose'] = $landTypePurpose;
        }
        $result = [];
        $unitPrice = $this->getUnitPrice($objects, $defaultLandType);
        $circularUnitPrice = (int)($unitPrice->vt1 ?? 0);
        if ($circularUnitPrice == 0) {
            foreach ($caches as $cache) {
                foreach ($cache['land_type_purpose_price'] as $item) {
                    $circularUnitPrice = $item['circular_unit_price'];
                }
            }
        }

        $assets = $this->findAssetEstimate($search, 100);

        foreach ($assets as $asset) {
            if (count($result) < (3 - count($caches))) {
                $cacheUnitPrice = $this->getUnitPrice($asset, $defaultLandType);
                $cacheCircularUnitPrice = (int)($cacheUnitPrice->vt1 ?? 0);
                if ($circularUnitPrice <= ($cacheCircularUnitPrice * 1.2) && $circularUnitPrice >= ($cacheCircularUnitPrice * 0.8)) {
                    $result[] = $asset;
                }
            }
        }
        return array_values(array_map("unserialize", array_unique(array_map("serialize", $result))));
    }

    public function getUnrecognizedFrontSiteAssetWithStreetEachLand($objects, $defaultLandType): array
    {
        $search = [
            'location' => $objects['location'] ?? null,
            'front_side' => $objects['front_side'] ?? null,
            'asset_types' => EstimateAssetDefault::LAND_ASSET_TYPE,
            'transaction_type_ids' => EstimateAssetDefault::TRANSACTION_TYPE,
            'land_type_purpose' => null,
            'province_id' => $objects['province_id'] ?? null,
            'district_id' => $objects['district_id'] ?? null,
            'street_id' => $objects['street_id'] ?? null,
        ];
        if (in_array($objects['district_id'], EstimateAssetDefault::URBAN_ASSET)) {
            $search['distance'] = 1;
        } else {
            $search['distance'] = 2;
        }
        $search['land_type_purpose_id'] = $defaultLandType;
        $assets = $this->findAssetEstimate($search, 3, true);
        return array_values(array_map("unserialize", array_unique(array_map("serialize", $assets))));
    }

    public function getUnrecognizedFrontSiteAssetWithStreetEachLandGroup($objects, $defaultLandType): array
    {
        $search = [
            'location' => $objects['location'] ?? null,
            'front_side' => $objects['front_side'] ?? null,
            'asset_types' => EstimateAssetDefault::LAND_ASSET_TYPE,
            'transaction_type_ids' => EstimateAssetDefault::TRANSACTION_TYPE,
            'land_type_purpose' => null,
            'province_id' => $objects['province_id'] ?? null,
            'district_id' => $objects['district_id'] ?? null,
            'street_id' => $objects['street_id'] ?? null,
        ];
        if (in_array($objects['district_id'], EstimateAssetDefault::URBAN_ASSET)) {
            $search['distance'] = 1;
        } else {
            $search['distance'] = 2;
        }
        if (in_array($defaultLandType, EstimateAssetDefault::GROUP_LAND_TYPE)) {
            $search['group_land_type_purpose'] = EstimateAssetDefault::GROUP_LAND_TYPE;
        } else {
            $search['land_type_purpose_id'] = $defaultLandType;
        }
        $assets = $this->findAssetEstimate($search, 3, true);
        return array_values(array_map("unserialize", array_unique(array_map("serialize", $assets))));
    }


    public function getUnrecognizedFrontSiteAssetWithoutStreetEachLand($objects, $landTypePurpose, $defaultLandType, $caches): array
    {
        $search = [
            'location' => $objects['location'] ?? null,
            'front_side' => $objects['front_side'] ?? null,
            'asset_types' => EstimateAssetDefault::LAND_ASSET_TYPE,
            'transaction_type_ids' => EstimateAssetDefault::TRANSACTION_TYPE,
            'land_type_purpose' => null,
            'province_id' => $objects['province_id'] ?? null,
            'district_id' => $objects['district_id'] ?? null,
            'not_street_id' => $objects['street_id'] ?? null,
            'distance' => 1,
        ];

        if ($objects['unrecognized']) {
            $search['land_type_purpose_id'] = $landTypePurpose;
        }
        $result = [];
        $unitPrice = $this->getUnitPrice($objects, $defaultLandType);
        $circularUnitPrice = (int)($unitPrice->vt1 ?? 0);
        if ($circularUnitPrice == 0) {
            foreach ($caches as $cache) {
                foreach ($cache['land_type_purpose_price'] as $item) {
                    $circularUnitPrice = $item['circular_unit_price'];
                }
            }
        }

        $assets = $this->findAssetEstimate($search, 100);
        foreach ($assets as $asset) {
            if (count($result) < (3 - count($caches))) {
                $cacheUnitPrice = $this->getUnitPrice($asset, $defaultLandType);
                $cacheCircularUnitPrice = (int)($cacheUnitPrice->vt1 ?? 0);
                if ($circularUnitPrice <= ($cacheCircularUnitPrice * 1.2) && $circularUnitPrice >= ($cacheCircularUnitPrice * 0.8)) {
                    $result[] = $asset;
                }
            }
        }
        return array_values(array_map("unserialize", array_unique(array_map("serialize", $result))));
    }


    /**
     * @param $objects
     * @param $defaultLandType
     * @return array
     */
    public function estimateUnrecognizedFrontSiteEachLandTypeAsset($objects, $defaultLandType): array
    {
        $result = [];
        $ids = [];
        $totalAsset = [];
        if ($objects['unrecognized']) {
            foreach ($objects['unrecognized'] as $value) {
                $returnAsset = [];
                $assets = $this->getUnrecognizedFrontSiteAssetWithStreetEachLand($objects, $value['land_type_purpose']);
                $result['steps'] = EstimateAssetDefault::STEP_1_4;
                foreach ($assets as $asset) {
                    $ids[] = $asset['id'];
                    $returnAsset[] = $asset;
                }
                if (count($assets) ==0) {
                    $caches = $this->getUnrecognizedFrontSiteAssetWithStreetEachLandGroup($objects, $value['land_type_purpose']);
                    $result['steps'] = EstimateAssetDefault::STEP_1_5;

                    $returnAsset = [];
                    foreach ($caches as $cache) {
                        $ids[] = $cache['id'];
                        $returnAsset[] = $cache;
                    }
                }
                $returnAsset = array_values(array_map("unserialize", array_unique(array_map("serialize", $returnAsset))));
                foreach ($returnAsset as $asset) {
                    $totalAsset[] = $asset;
                }
                if (count($returnAsset) >= 3) {
                    $totalGroupLandUnitPrice = 0;
                    $averageGroupLandUnitPrice = 0;
                    $countGroupLand = 0;
                    foreach ($returnAsset as $asset) {
                        foreach ($asset['land_type_purpose_price'] as $land_type_purpose_price) {
                            if (in_array($land_type_purpose_price['id'], EstimateAssetDefault::GROUP_LAND_TYPE)) {
                                $totalGroupLandUnitPrice += $land_type_purpose_price['price_land'] ?? 0;
                                $countGroupLand += 1;
                            }
                        }
                    }
                    if ($countGroupLand != 0)
                        $averageGroupLandUnitPrice = (int)($totalGroupLandUnitPrice / $countGroupLand);
                    $totalLandUnitPrice = 0;
                    $averageLandUnitPrice = 0;
                    $count = 0;
                    foreach ($returnAsset as $asset) {
                        foreach ($asset['land_type_purpose_price'] as $land_type_purpose_price) {
                            if (in_array($value['land_type_purpose'], EstimateAssetDefault::GROUP_LAND_TYPE)) {
                                $totalLandUnitPrice += $averageGroupLandUnitPrice ?? 0;
                                $count += 1;
                            } elseif ($land_type_purpose_price['id'] == $value['land_type_purpose']) {
                                $totalLandUnitPrice += $land_type_purpose_price['price_land'] ?? 0;
                                $count += 1;
                            }
                        }
                    }
                    if ($count != 0) {
                        $averageLandUnitPrice = round((int)($totalLandUnitPrice / $count), -5);
                    }
                    $result['unrecognized'][] = [
                        'land_type_purpose' => $value['land_type_purpose'],
                        'land_type_purpose_name' => $value['land_type_purpose_name'],
                        'area' => $value['area'],
                        'average_land_unit_price' => $averageLandUnitPrice,
                        'estimate_price' => round($averageLandUnitPrice * $value['area'],-5),
                    ];
                    $result['reliability'] = (isset($result['reliability']) && $result['reliability'] > ValueDefault::RELIABILITY_NORMAL) ? $result['reliability'] : ValueDefault::RELIABILITY_NORMAL;
                    $result['status'] = (isset($result['status']) && $result['status'] < ValueDefault::STATUS_SUCCESS) ? $result['status'] : ValueDefault::STATUS_SUCCESS;
                    $result['error_message'] = $result['error_message'] ?? null;
                } else {
//                    $caches = $this->getUnrecognizedFrontSiteAssetWithoutStreetEachLand($objects, $value['land_type_purpose'], $defaultLandType, $returnAsset);
//                    foreach ($caches as $cache) {
//                        $ids[] = $cache['id'];
//                        $returnAsset[] = $cache;
//                        $totalAsset[] = $cache;
//                    }
                    if (count($returnAsset) >= 1) {
                        $totalGroupLandUnitPrice = 0;
                        $averageGroupLandUnitPrice = 0;
                        $countGroupLand = 0;
                        foreach ($returnAsset as $asset) {
                            foreach ($asset['land_type_purpose_price'] as $land_type_purpose_price) {
                                if (in_array($land_type_purpose_price['id'], EstimateAssetDefault::GROUP_LAND_TYPE)) {
                                    $totalGroupLandUnitPrice += $land_type_purpose_price['price_land'] ?? 0;
                                    $countGroupLand += 1;
                                }
                            }
                        }
                        if ($countGroupLand != 0)
                            $averageGroupLandUnitPrice = (int)($totalGroupLandUnitPrice / $countGroupLand);
                        $totalLandUnitPrice = 0;
                        $averageLandUnitPrice = 0;
                        $count = 0;
                        foreach ($returnAsset as $asset) {
                            foreach ($asset['land_type_purpose_price'] as $land_type_purpose_price) {
                                if (in_array($value['land_type_purpose'], EstimateAssetDefault::GROUP_LAND_TYPE)) {
                                    $totalLandUnitPrice += $averageGroupLandUnitPrice ?? 0;
                                    $count += 1;
                                } elseif ($land_type_purpose_price['id'] == $value['land_type_purpose']) {
                                    $totalLandUnitPrice += $land_type_purpose_price['price_land'] ?? 0;
                                    $count += 1;
                                }
                            }
                        }
                        if ($count != 0) {
                            $averageLandUnitPrice = round((int)($totalLandUnitPrice / $count), -5);
                        }
                        if (count($returnAsset) >= 2) {
                            $reliability = ValueDefault::RELIABILITY_NORMAL;
                        } else {
                            $reliability = ValueDefault::RELIABILITY_LOW;
                        }
                        $result['unrecognized'][] = [
                            'land_type_purpose' => $value['land_type_purpose'],
                            'land_type_purpose_name' => $value['land_type_purpose_name'],
                            'area' => $value['area'],
                            'average_land_unit_price' => $averageLandUnitPrice,
                            'estimate_price' => round($averageLandUnitPrice * $value['area'],-5)
                        ];
                        $result['reliability'] = (isset($result['reliability']) && $result['reliability'] < $reliability) ? $result['reliability'] : $reliability;
                        $result['status'] = (isset($result['status']) && $result['status'] < ValueDefault::STATUS_SUCCESS) ? $result['status'] : ValueDefault::STATUS_SUCCESS;
                        $result['error_message'] = $result['error_message'] ?? null;
                    } else {
                        $result['unrecognized'][] = [
                            'land_type_purpose' => $value['land_type_purpose'],
                            'land_type_purpose_name' => $value['land_type_purpose_name'],
                            'area' => $value['area'],
                            'average_land_unit_price' => 0,
                            'estimate_price' => 0,
                        ];
                        $result['reliability'] = ValueDefault::RELIABILITY_LOW;
                        $result['status'] = ValueDefault::STATUS_ERROR;
                        $result['error_message'] = EstimateAssetDefault::ERROR_MESSAGE;
                    }
                }
            }
        }
        $result['assets'] = array_values(array_map("unserialize", array_unique(array_map("serialize", $totalAsset))));
        return $result;
    }

    /**
     * @param $objects
     * @return array
     */
    public function estimateUnrecognizedUnfrontSiteAsset($objects): array
    {
        $search = [
            'location' => $objects['location'] ?? null,
            'front_side' => $objects['front_side'] ?? null,
            'asset_types' => EstimateAssetDefault::LAND_ASSET_TYPE,
            'transaction_type_ids' => EstimateAssetDefault::TRANSACTION_TYPE,
            'land_type_purpose' => null,
            'street_id' => null,
            'not_street_id' => null,
            'main_road_length' => $objects['main_road_length'] ?? null,
            'main_road_length_to' => null,
            'land_type_purpose_id' => null,
            'circular_unit_price' => null,
        ];
        if (in_array($objects['district_id'], EstimateAssetDefault::URBAN_ASSET)) {
            $search['distance'] = 1;
        } else {
            $search['distance'] = 2;
        }
        $result = [];
        $landTypePurpose = [];
        $returnAsset = [];
        if ($objects['unrecognized']) {
            foreach ($objects['unrecognized'] as $value) {
                $landTypePurpose[] = $value['land_type_purpose'];
            }
            $search['land_type_purpose'] = $landTypePurpose;
            $assets = $this->findAssetEstimate($search);
            $result['steps'] = EstimateAssetDefault::STEP_1_6;
            $ids = [];
            foreach ($assets as $asset) {
                $ids[] = $asset['id'];
                $returnAsset[] = $asset;
            }
            if (count($assets) < 3) {
                foreach ($objects['unrecognized'] as $value) {
                    if (in_array($value['land_type_purpose'], EstimateAssetDefault::GROUP_LAND_TYPE)) {
                        $search['group_land_type_purpose'] = EstimateAssetDefault::GROUP_LAND_TYPE;
                    }
                    $landTypePurpose[] = $value['land_type_purpose'];

                }
                $search['land_type_purpose'] = $landTypePurpose;
                $assets = $this->findAssetEstimate($search);
                $result['steps'] = EstimateAssetDefault::STEP_1_7;
                foreach ($assets as $asset) {
                    $ids[] = $asset['id'];
                    $returnAsset[] = $asset;
                }
            }
            $returnAsset = array_values(array_map("unserialize", array_unique(array_map("serialize", $returnAsset))));
            if (count($returnAsset) == 3) {
                foreach ($objects['unrecognized'] as $value) {
                    $totalGroupLandUnitPrice = 0;
                    $averageGroupLandUnitPrice = 0;
                    $countGroupLand = 0;
                    foreach ($returnAsset as $asset) {
                        foreach ($asset['land_type_purpose_price'] as $land_type_purpose_price) {
                            if (in_array($land_type_purpose_price['id'], EstimateAssetDefault::GROUP_LAND_TYPE)) {
                                $totalGroupLandUnitPrice += $land_type_purpose_price['price_land'] ?? 0;
                                $countGroupLand += 1;
                            }
                        }
                    }
                    if ($countGroupLand != 0)
                        $averageGroupLandUnitPrice = (int)($totalGroupLandUnitPrice / $countGroupLand);
                    $totalLandUnitPrice = 0;
                    $averageLandUnitPrice = 0;
                    $count = 0;
                    foreach ($returnAsset as $asset) {
                        foreach ($asset['land_type_purpose_price'] as $land_type_purpose_price) {
                            if (in_array($value['land_type_purpose'], EstimateAssetDefault::GROUP_LAND_TYPE)) {
                                $totalLandUnitPrice += $averageGroupLandUnitPrice ?? 0;
                                $count += 1;
                            } elseif ($land_type_purpose_price['id'] == $value['land_type_purpose']) {
                                $totalLandUnitPrice += $land_type_purpose_price['price_land'] ?? 0;
                                $count += 1;
                            }
                        }
                    }
                    if ($count != 0) {
                        $averageLandUnitPrice = round((int)($totalLandUnitPrice / $count), -5);
                    }
                    $result['unrecognized'][] = [
                        'land_type_purpose' => $value['land_type_purpose'],
                        'land_type_purpose_name' => $value['land_type_purpose_name'],
                        'area' => $value['area'],
                        'average_land_unit_price' => $averageLandUnitPrice,
                        'estimate_price' => round($averageLandUnitPrice * $value['area'],-5),
                    ];
                }

                $result['reliability'] = ValueDefault::RELIABILITY_HIGHT;
                $result['assets'] = array_values(array_map("unserialize", array_unique(array_map("serialize", $returnAsset))));
                $result['status'] = ValueDefault::STATUS_SUCCESS;
                $result['error_message'] = null;
            } else {
                if ($objects['main_road_length'] >= 4) {
                    $search['main_road_length'] = (($objects['main_road_length'] * 0.8) >= 4) ? $objects['main_road_length'] * 0.8 : 4;
                    $search['main_road_length_to'] = $objects['main_road_length'] * 1.2;
                } else {
                    $search['main_road_length'] = $objects['main_road_length'] * 0.8;
                    $search['main_road_length_to'] = (($objects['main_road_length'] * 1.2) <= 4) ? $objects['main_road_length'] * 1.2 : 4;
                }
                foreach ($objects['unrecognized'] as $value) {
                    $landTypePurpose[] = $value['land_type_purpose'];
                }
                $search['land_type_purpose'] = $landTypePurpose;
                $search['not_in_ids'] = $ids;
                $caches = $this->findAssetEstimate($search, 3 - count($returnAsset));
                $result['steps'] = EstimateAssetDefault::STEP_1_8;
                $ids = [];
                foreach ($caches as $cache) {
                    $ids[] = $cache['id'];
                    $returnAsset[] = $cache;
                }
                if (count($returnAsset) < 3) {
                    foreach ($objects['unrecognized'] as $value) {
                        if (in_array($value['land_type_purpose'], EstimateAssetDefault::GROUP_LAND_TYPE)) {
                            $search['group_land_type_purpose'] = EstimateAssetDefault::GROUP_LAND_TYPE;
                        }
                        $landTypePurpose[] = $value['land_type_purpose'];

                    }
                    $search['land_type_purpose'] = $landTypePurpose;
                    $search['not_in_ids'] = $ids;
                    $caches = $this->findAssetEstimate($search, 3 - count($returnAsset));
                    $result['steps'] = EstimateAssetDefault::STEP_1_9;
                    foreach ($caches as $cache) {
                        $ids[] = $cache['id'];
                        $returnAsset[] = $cache;
                    }
                }

                $returnAsset = array_values(array_map("unserialize", array_unique(array_map("serialize", $returnAsset))));
                if (count($returnAsset) == 0) {
                    foreach ($objects['unrecognized'] as $value) {
                        $result['unrecognized'][] = [
                            'land_type_purpose' => $value['land_type_purpose'],
                            'land_type_purpose_name' => $value['land_type_purpose_name'],
                            'area' => $value['area'],
                            'average_land_unit_price' => 0,
                            'estimate_price' => 0,
                        ];
                    }
                    $result['reliability'] = ValueDefault::RELIABILITY_LOW;
                    $result['assets'] = array_values(array_map("unserialize", array_unique(array_map("serialize", $returnAsset))));
                    $result['status'] = ValueDefault::STATUS_ERROR;
                    $result['error_message'] = EstimateAssetDefault::ERROR_MESSAGE;
                    return $result;
                } else {
                    if (count($returnAsset) > 0) {
                        foreach ($objects['unrecognized'] as $value) {
                            $totalGroupLandUnitPrice = 0;
                            $averageGroupLandUnitPrice = 0;
                            $countGroupLand = 0;
                            foreach ($returnAsset as $asset) {
                                foreach ($asset['land_type_purpose_price'] as $land_type_purpose_price) {
                                    if (in_array($land_type_purpose_price['id'], EstimateAssetDefault::GROUP_LAND_TYPE)) {
                                        $totalGroupLandUnitPrice += $land_type_purpose_price['price_land'] ?? 0;
                                        $countGroupLand += 1;
                                    }
                                }
                            }
                            if ($countGroupLand != 0)
                                $averageGroupLandUnitPrice = (int)($totalGroupLandUnitPrice / $countGroupLand);
                            $totalLandUnitPrice = 0;
                            $averageLandUnitPrice = 0;
                            $count = 0;
                            foreach ($returnAsset as $asset) {
                                foreach ($asset['land_type_purpose_price'] as $land_type_purpose_price) {
                                    if (in_array($value['land_type_purpose'], EstimateAssetDefault::GROUP_LAND_TYPE)) {
                                        $totalLandUnitPrice += $averageGroupLandUnitPrice ?? 0;
                                        $count += 1;
                                    } elseif ($land_type_purpose_price['id'] == $value['land_type_purpose']) {
                                        $totalLandUnitPrice += $land_type_purpose_price['price_land'] ?? 0;
                                        $count += 1;
                                    }
                                }
                            }
                            if ($count != 0) {
                                $averageLandUnitPrice = round((int)($totalLandUnitPrice / $count), -5);
                            }
                            if (count($returnAsset) >= 3) {
                                if (count($assets) >= 2) {
                                    $reliability = ValueDefault::RELIABILITY_HIGHT;
                                } else {
                                    $reliability = ValueDefault::RELIABILITY_NORMAL;
                                }
                            } else {
                                if (count($returnAsset) >= 2) {
                                    $reliability = ValueDefault::RELIABILITY_NORMAL;
                                } else {
                                    $reliability = ValueDefault::RELIABILITY_LOW;
                                }
                            }

                            $result['unrecognized'][] = [
                                'land_type_purpose' => $value['land_type_purpose'],
                                'land_type_purpose_name' => $value['land_type_purpose_name'],
                                'area' => $value['area'],
                                'average_land_unit_price' => $averageLandUnitPrice,
                                'estimate_price' => round($averageLandUnitPrice * $value['area'],-5),
                            ];
                            $result['reliability'] = $reliability;
                        }

                        $result['assets'] = array_values(array_map("unserialize", array_unique(array_map("serialize", $returnAsset))));
                        $result['status'] = ValueDefault::STATUS_SUCCESS;
                        $result['error_message'] = null;
                        return $result;
                    } else {
                        foreach ($objects['unrecognized'] as $value) {
                            $result['unrecognized'][] = [
                                'land_type_purpose' => $value['land_type_purpose'],
                                'land_type_purpose_name' => $value['land_type_purpose_name'],
                                'area' => $value['area'],
                                'average_land_unit_price' => 0,
                                'estimate_price' => 0,
                            ];
                        }
                        $result['reliability'] = ValueDefault::RELIABILITY_LOW;
                        $result['assets'] = array_values(array_map("unserialize", array_unique(array_map("serialize", $returnAsset))));
                        $result['status'] = ValueDefault::STATUS_ERROR;
                        $result['error_message'] = EstimateAssetDefault::ERROR_MESSAGE;
                        return $result;
                    }
                }
            }
        }

        return $result;
    }


    public function estimateApartmentAsset($objects): array
    {
        $search = [
            'location' => $objects['location'] ?? null,
            'distance' => $objects['distance'] ?? null,
            'asset_types' => EstimateAssetDefault::APARTMENT_ASSET_TYPE,
            'apartment_id' => $objects['apartment_id'] ?? null,
            'transaction_type_ids' => EstimateAssetDefault::TRANSACTION_TYPE,
        ];

        $result = [];
        $returnAsset = [];

        if ($objects['apartment']) {
            $search['bedroom_num'] = $objects['apartment']['bedroom_num'];
            $assets = $this->findAssetEstimate($search, EstimateAssetDefault::ESTIMATE_APARTMENT_LIMIT);
            $ids = [];
            foreach ($assets as $asset) {
                $ids[] = $asset['id'];
                $returnAsset[] = $asset;
            }
            $data = $this->findByIds(json_encode($ids));
            if (count($data) >= 2) {
                $totalUnitPrice = 0;
                foreach ($data as $record) {
                    $totalUnitPrice += $record->average_land_unit_price ?? 0;
                }
                $averageUnitPrice = round((int)($totalUnitPrice / count($data)), -5);

                $result['apartment'] = [
                    'average_unit_price' => $averageUnitPrice,
                    'estimate_price' => round($averageUnitPrice * $objects['apartment']['area'],-5),
                ];
                $result['reliability'] = ValueDefault::RELIABILITY_HIGHT;
                $result['assets'] = array_values(array_map("unserialize", array_unique(array_map("serialize", $returnAsset))));
                $result['status'] = ValueDefault::STATUS_SUCCESS;
                $result['error_message'] = null;
                $result['steps'] = EstimateAssetDefault::STEP_2_1;
                return $result;
            }
            if (count($data) == 1) {
                $totalUnitPrice = 0;
                foreach ($data as $record) {
                    $totalUnitPrice += $record->average_land_unit_price ?? 0;
                }
                $averageUnitPrice = round((int)($totalUnitPrice / count($data)), -5);
                $result['apartment'] = [
                    'average_unit_price' => $averageUnitPrice,
                    'estimate_price' => round($averageUnitPrice * $objects['apartment']['area'],-5),
                ];
                $result['reliability'] = ValueDefault::RELIABILITY_NORMAL;
                $result['assets'] = array_values(array_map("unserialize", array_unique(array_map("serialize", $returnAsset))));
                $result['status'] = ValueDefault::STATUS_SUCCESS;
                $result['error_message'] = null;
                $result['steps'] = EstimateAssetDefault::STEP_2_1;
                return $result;
            }
            if (count($data) == 0) {
                $search['bedroom_num'] = null;
                $assets = $this->findAssetEstimate($search, EstimateAssetDefault::ESTIMATE_APARTMENT_LIMIT);
                $ids = [];
                foreach ($assets as $asset) {
                    $ids[] = $asset['id'];
                    $returnAsset[] = $asset;
                }
                $data = $this->findByIds(json_encode($ids));
                if (count($data) > 0) {
                    $totalUnitPrice = 0;
                    foreach ($data as $record) {
                        $totalUnitPrice += $record->average_land_unit_price ?? 0;
                    }
                    $averageUnitPrice = round((int)($totalUnitPrice / count($data)), -5);
                    $result['apartment'] = [
                        'average_unit_price' => $averageUnitPrice,
                        'estimate_price' => round($averageUnitPrice * $objects['apartment']['area']),
                    ];
                    $result['reliability'] = ValueDefault::RELIABILITY_NORMAL;
                    $result['assets'] = array_values(array_map("unserialize", array_unique(array_map("serialize", $returnAsset))));
                    $result['status'] = ValueDefault::STATUS_SUCCESS;
                    $result['error_message'] = null;
                    $result['steps'] = EstimateAssetDefault::STEP_2_2;
                    return $result;
                } else {
                    if (in_array($objects['district_id'], EstimateAssetDefault::URBAN_ASSET)) {
                        $search['distance'] = 1;
                    } else {
                        $search['distance'] = 2;
                    }
                    $search['apartment_id'] = null;
                    $search['bedroom_num'] = null;

                    $search['location'] = $objects['location'] ?? null;
                    $apartments = $this->findAssetEstimate($search, 1);

                    if (count($apartments) > 0) {
                        $apartmentId = null;
                        foreach ($apartments as $apartment) {
                            $apartmentId = $apartment['apartment_id'];
                        }
                        $search['apartment_id'] = $apartmentId;
                        $search['bedroom_num'] = $objects['apartment']['bedroom_num'];
                        $assets = $this->findAssetEstimate($search, EstimateAssetDefault::ESTIMATE_APARTMENT_LIMIT);
                        $ids = [];
                        foreach ($assets as $asset) {
                            $ids[] = $asset['id'];
                            $returnAsset[] = $asset;
                        }
                        $data = $this->findByIds(json_encode($ids));
                        if (count($data) > 0) {
                            $totalUnitPrice = 0;
                            foreach ($data as $record) {
                                $totalUnitPrice += $record->average_land_unit_price ?? 0;
                            }
                            $averageUnitPrice = round((int)($totalUnitPrice / count($data)));

                            $result['apartment'] = [
                                'average_unit_price' => $averageUnitPrice,
                                'estimate_price' => round($averageUnitPrice * $objects['apartment']['area'],-5),
                            ];
                            $result['reliability'] = ValueDefault::RELIABILITY_LOW;
                            $result['assets'] = array_values(array_map("unserialize", array_unique(array_map("serialize", $returnAsset))));
                            $result['status'] = ValueDefault::STATUS_SUCCESS;
                            $result['error_message'] = null;
                            $result['steps'] = EstimateAssetDefault::STEP_2_3;
                            return $result;
                        } else {
                            $search['bedroom_num'] = null;
                            $assets = $this->findAssetEstimate($search, EstimateAssetDefault::ESTIMATE_APARTMENT_LIMIT);
                            $ids = [];
                            foreach ($assets as $asset) {
                                $ids[] = $asset['id'];
                                $returnAsset[] = $asset;
                            }
                            $data = $this->findByIds(json_encode($ids));
                            if (count($data) > 0) {
                                $totalUnitPrice = 0;
                                foreach ($data as $record) {
                                    $totalUnitPrice += $record->average_land_unit_price ?? 0;
                                }
                                $averageUnitPrice = round((int)($totalUnitPrice / count($data)), -5);

                                $result['apartment'] = [
                                    'average_unit_price' => $averageUnitPrice,
                                    'estimate_price' => round($averageUnitPrice * $objects['apartment']['area'],-5),
                                ];
                                $result['reliability'] = ValueDefault::RELIABILITY_LOW;
                                $result['assets'] = array_values(array_map("unserialize", array_unique(array_map("serialize", $returnAsset))));
                                $result['status'] = ValueDefault::STATUS_SUCCESS;
                                $result['error_message'] = null;
                                $result['steps'] = EstimateAssetDefault::STEP_2_4;
                                return $result;
                            }
                        }

                    } else {
                        $result['apartment'] = [
                            'average_unit_price' => 0,
                            'estimate_price' => 0,
                        ];
                        $result['reliability'] = ValueDefault::RELIABILITY_LOW;
                        $result['assets'] = [];
                        $result['status'] = ValueDefault::STATUS_ERROR;
                        $result['error_message'] = EstimateAssetDefault::ERROR_MESSAGE;
                        $result['steps'] = EstimateAssetDefault::STEP_2_4;
                        return $result;
                    }

                }

            }
        }
        return $result;
    }

    /**
     * @param $landTypePurpose
     * @param $object
     * @return int
     */
    public
    function getPurposePrice($landTypePurpose, $object): int
    {
        foreach ($object->properties as $properties) {
            foreach ($properties->propertyDetail as $detail) {
                if ($detail->land_type_purpose == $landTypePurpose) {
                    return $detail->circular_unit_price;
                }
            }
        }

        return 0;
    }


    /**
     * @param $search
     * @param int $limit
     * @param bool $eachland
     * @return array
     */
    public
    function findAssetEstimate($search, int $limit = 3, bool $eachland = false)
    {
        try {
            $provinceId = $search['province_id'] ?? null;
            $districtId = $search['district_id'] ?? null;
            $streetId = $search['street_id'] ?? null;
            $wardId = $search['ward_id'] ?? null;
            $transactionTypeIds = $search['transaction_type_ids'] ?? null;
            $assetTypes = $search['asset_types'] ?? null;
            $landTypePurpose = $search['land_type_purpose'] ?? null;
            $groupLandTypePurpose = $search['group_land_type_purpose'] ?? null;
            $location = $search['location'] ?? null;
            $distance = $search['distance'] ?? null;
            $frontSide = $search['front_side'] ?? null;
            $notStreetId = $search['not_street_id'] ?? null;
            $mainRoadLength = $search['main_road_length'] ?? null;
            $mainRoadLengthTo = $search['main_road_length_to'] ?? null;
            $averageLandUnitPrice = $search['average_land_unit_price'] ?? null;
            $landTypePurposePrice = $search['land_type_purpose_price'] ?? null;
            $landTypePurposeId = $search['land_type_purpose_id'] ?? null;
            $circularUnitPrice = $search['circular_unit_price'] ?? null;
            $bedroomNum = $search['bedroom_num'] ?? null;
            $apartmentId = $search['apartment_id'] ?? null;
            $ids = $search['ids'] ?? null;
            $notIds = $search['not_in_ids'] ?? null;
            $array ['bool']['must'] = [];
            $array['bool']['must'][] = [
                'match' => [
                    'status' => 1
                ]
            ];

            if (!empty($ids)) {
                $array['bool']['must'][] = [
                    'terms' => [
                        'id' => $ids
                    ]
                ];
            }
            if (!empty($notIds)) {
                $array['bool']['must_not'][] = [
                    'terms' => [
                        'id' => $notIds
                    ]
                ];
            }

            if (!empty($frontSide)) {
                $array['bool']['must'][] = [
                    'match' => [
                        'front_side' => $frontSide
                    ]
                ];
            }
            if (!empty($bedroomNum)) {
                $array['bool']['must'][] = [
                    'match' => [
                        'bedroom_num' => $bedroomNum
                    ]
                ];
            }
            if (!empty($apartmentId)) {
                $array['bool']['must'][] = [
                    'match' => [
                        'apartment_id' => $apartmentId
                    ]
                ];
            }

            if (!empty($mainRoadLength)) {
                $array['bool']['must'][] = [
                    'range' => [
                        'main_road_length' => [
                            'gte' => $mainRoadLength,
                            'lte' => $mainRoadLengthTo ?? $mainRoadLength
                        ]
                    ]
                ];
            }
            if (!empty($landTypePurposeId)) {
                $array['bool']['must'][] = [
                    'match' => [
                        'land_type_purpose_price.id' => $landTypePurposeId
                    ]
                ];
            }
            if (isset($circularUnitPrice)) {
                $array['bool']['must'][] = [
                    'range' => [
                        'land_type_purpose_price.circular_unit_price' => [
                            'gte' => (int)($circularUnitPrice - ($circularUnitPrice * 0.2)),
                            'lte' => (int)($circularUnitPrice + ($circularUnitPrice * 0.2))
                        ]
                    ]
                ];
            }

            $array['bool']['must'][] = [
                'range' => [
                    'public_date' => [
                        'gte' => now()->addMonths(-24)->format('d-m-Y'),
                        'lte' => now()->format('d-m-Y'),
                        'format' => 'dd-MM-yyyy||yyyy',
                    ]
                ]
            ];

            if (!empty($assetTypes)) {
                $array['bool']['must'][] = [
                    'terms' => [
                        'asset_type_id' => $assetTypes
                    ]
                ];
            }
            if (!empty($provinceId)) {
                $array['bool']['must'][] = [
                    'match' => [
                        'province_id' => $provinceId
                    ]
                ];
            }
            if (!empty($districtId)) {
                $array['bool']['must'][] = [
                    'match' => [
                        'district_id' => $districtId
                    ]
                ];
            }
            if (!empty($wardId)) {
                $array['bool']['must'][] = [
                    'match' => [
                        'ward_id' => $wardId
                    ]
                ];
            }
            if (!empty($streetId) && empty($notStreetId)) {
                $array['bool']['must'][] = [
                    'match' => [
                        'street_id' => $streetId
                    ]
                ];
            }

            if (!empty($notStreetId)) {
                $array['bool']['must_not'][] = [
                    'match' => [
                        'street_id' => $notStreetId
                    ]
                ];
            }

            if (!empty($transactionTypeIds)) {
                $array['bool']['must'][] = [
                    'terms' => [
                        'transaction_type' => $transactionTypeIds,
                    ]
                ];
            }

            if (!empty($landTypePurpose)) {
                if (!empty($groupLandTypePurpose)) {
                    foreach ($landTypePurpose as $value) {
                        if (!in_array($value, $groupLandTypePurpose)) {
                            $array['bool']['must'][] = [
                                'match' => [
                                    'land_type_purpose.id' => $value,
                                ]
                            ];
                        }
                    }
                    $array['bool']['must'][] = [
                        'terms' => [
                            'land_type_purpose.id' => $groupLandTypePurpose,
                        ]
                    ];
                } else {
                    foreach ($landTypePurpose as $value) {
                        $array['bool']['must'][] = [
                            'match' => [
                                'land_type_purpose.id' => $value,
                            ]
                        ];
                    }
                }
            }
            $landTypes = $this->getAllLandType();
            if (!$eachland == true) {
                foreach ($landTypes as $landType) {
                    if ($groupLandTypePurpose && $landTypePurpose) {
                        if (!in_array($landType->id, $landTypePurpose) && !in_array($landType->id, $groupLandTypePurpose)) {
                            $array['bool']['must_not'][] = [
                                'match' => [
                                    'land_type_purpose.id' => $landType->id,
                                ]
                            ];
                        }
                    } elseif ($landTypePurpose && !in_array($landType->id, $landTypePurpose)) {
                        $array['bool']['must_not'][] = [
                            'match' => [
                                'land_type_purpose.id' => $landType->id,
                            ]
                        ];
                    }
                }
            }

            if (!empty($averageLandUnitPrice)) {
                $array['bool']['must'][] = [
                    'range' => [
                        'average_land_unit_price' => [
                            'gte' => (int)($averageLandUnitPrice - ($averageLandUnitPrice * 0.2)),
                            'lte' => (int)($averageLandUnitPrice + ($averageLandUnitPrice * 0.2))
                        ]
                    ]
                ];
            }
            $sortBy = [];
            if (!empty($location) && !empty($distance)) {
                $location = explode(',', $location);
                $array['bool']['must'][] = [
                    'geo_distance' => [
                        'distance' => $distance . 'km',
                        'pin.location' => [
                            'lat' => (float)$location[0] ?? null,
                            'lon' => (float)$location[1] ?? null,
                        ]
                    ]
                ];
                $sortBy = [
                    '_geo_distance' => [
                        'pin.location' => [
                            'lat' => (float)$location[0] ?? null,
                            'lon' => (float)$location[1] ?? null,
                        ],
                        'order' => 'asc',
                        'unit' => 'm',
                        'mode' => 'min',
                        'distance_type' => 'arc',
                        'ignore_unmapped' => true,
                    ]
                ];
            }
            $respones = CompareAssetGeneral::searchByQuery($array, null, null, $limit, null, $sortBy);
            $result = [];
            foreach ($respones->getHits()['hits'] as $hit) {
                $hit['_source']['sort'] = $hit['sort'][0] ?? null;
                array_push($result, $hit['_source']);
            }
            return $result;
        } catch (Exception $exception) {
            Log::error($exception);
            return [];
        }
    }


    /**
     * @param $landType
     * @param null $distance
     * @param null $street
     * @param null $ward
     * @param null $district
     * @param null $province
     * @return Builder|Model|object|null
     */
    public
    function findUnitPriceEstimate($landType, $distance = null, $street = null, $ward = null, $district = null, $province = null)
    {
        $result = null;
        if (empty($landType)) {
            $landType = '';
        }
        $query = 'land_type ilike ' . "'" . urldecode($landType) . "'";

        if (!empty($province)) {
            $query = $query . ' and province ilike ' . "'" . urldecode($province) . "'";
        }

        if (!empty($district)) {
            $query = $query . ' and district ilike ' . "'" . urldecode($district) . "'";
        }
        $queryNoWard = $query . ' and ward is null';
        if (!empty($ward)) {
            $ward = str_replace(['Xã ', 'Phường '], '', urldecode($ward));
            $query = $query . ' and ward ilike ' . "'%" . urldecode($ward) . "%'";
        }
        $queryDistance = $query;
        if (!empty($distance)) {
            $queryDistance = $queryDistance . ' and street ilike ' . "'" . urldecode($street) . "'";
            $queryDistance = $queryDistance . ' and distance ilike ' . "'" . urldecode($distance) . "'";
            $result = UnitPrice::query()
                ->whereRaw($queryDistance)
                ->first();
            if (empty($result)) {
                $queryDistance = $queryNoWard . ' and distance ilike ' . "'" . urldecode($distance) . "'";
                $queryDistance = $queryDistance . ' and street ilike ' . "'" . urldecode($street) . "'";
                $result = UnitPrice::query()
                    ->whereRaw($queryDistance)
                    ->first();
            }
        }


        $queryStreet = $query;
        if (!empty($street) && empty($result)) {
            $queryStreet = $queryStreet . ' and street ilike ' . "'" . urldecode($street) . "' and distance is null";
            $result = UnitPrice::query()
                ->whereRaw($queryStreet)
                ->first();
            if (empty($result)) {
                $queryStreet = $queryNoWard . ' and street ilike ' . "'" . urldecode($street) . "' and distance is null";
                $result = UnitPrice::query()
                    ->whereRaw($queryStreet)
                    ->first();
            }
        }
        $queryWard = $query;
        if (!empty($ward) && empty($result)) {
            $queryWard = $queryWard . ' and ward ilike ' . "'%" . urldecode($ward) . "%' and street is null";
            $result = UnitPrice::query()
                ->whereRaw($queryWard)
                ->first();
        }
        return $result;
    }

    public function findDistance($streetId)
    {
        return Distance::query()
            ->where('street_id', '=', $streetId)
            ->first();
    }

    public function getUnitPrice($objects, $defaultLandType)
    {

        $search = [
            'location' => $objects['location'] ?? null,
            'distance' => $objects['distance'] ?? null,
            'asset_types' => EstimateAssetDefault::LAND_ASSET_TYPE,
            'land_type_purpose' => null,
            'province_id' => $objects['province_id'] ?? null,
            'district_id' => $objects['district_id'] ?? null,
            'street_id' => $objects['street_id'] ?? null,
        ];
        $assets = $this->findAssetEstimate($search);
        $ids = [];

        foreach ($assets as $asset) {
            $ids[] = $asset['id'];
        }
        $data = $this->findByIds(json_encode($ids));
        $distance = null;

        foreach ($data as $record) {
            $distance = !$distance ? ($record->distance->name ?? null) : $distance;
        }
        if (!$distance) {
            $distance = $this->findDistance($objects['street_id']);
            $distance = $distance->name ?? null;
        }
        if (in_array($objects['district_id'], EstimateAssetDefault::URBAN_ASSET)) {
            $defaultLandType = EstimateAssetDefault::DEFAULT_UBAN_LAND_TYPE;
        } else {
            $defaultLandType = EstimateAssetDefault::DEFAULT_COUNTRY_LAND_TYPE;
        }
        $resuil = $this->findUnitPriceEstimate($defaultLandType, $distance, $objects['street'], $objects['ward'], $objects['district'], $objects['province']);

        return $resuil;
    }

    /**
     * @param $objects
     * @param $defaultLandType
     * @param array $result
     * @return array
     */
    public function getResult($objects, $defaultLandType, array $result): array
    {
        $response = $this->estimateUnrecognizedFrontSiteEachLandTypeAsset($objects, $defaultLandType);
        $result['unrecognized'] = $response['unrecognized'];
        $result['reliability'] = $response['reliability'];
        $result['assets'] = $response['assets'];
        $result['status'] = $response['status'];
        $result['error_message'] = $response['error_message'];
        $result['steps'] = $response['steps'] ?? null;
        return $result;
    }

    public function getAllLandType()
    {
        return Dictionary::query()
            ->where('type', '=', EstimateAssetDefault::DICTIONARY_LAND_TYPE)
            ->get('id');
    }
}
