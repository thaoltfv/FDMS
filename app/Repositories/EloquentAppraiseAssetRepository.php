<?php

namespace App\Repositories;

use App\Contracts\AppraiseAssetRepository;
use App\Enum\EstimateAssetDefault;
use App\Enum\ValueDefault;
use App\Models\CompareAssetGeneral;
use Exception;
use Illuminate\Support\Facades\Log;
use LDAP\Result;

use const App\Enum\ERROR_MESSAGE;

class EloquentAppraiseAssetRepository extends EloquentRepository implements AppraiseAssetRepository
{
    /**
     * @param $objects
     * @return array
     */
    public function estimateUnrecognizedFrontSiteAsset($objects): array
    {
        $result = [];
        if ($objects['unrecognized']) {
            $assets = $this->getUnrecognizedFrontSiteAssetWithStreetInGroupLandType($objects);
            $result['steps'] = EstimateAssetDefault::STEP_1_5;
            $result['assets'] = array_values(array_map("unserialize", array_unique(array_map("serialize", $assets))));
            $result['status'] = ValueDefault::STATUS_SUCCESS;
            $result['error_message'] = null;
            return $result;
        }
        return $result;
    }

    public function getUnrecognizedFrontSiteAssetWithStreetInGroupLandType($objects): array
    {
        $search = [
            'location' => $objects['location'] ?? null,
            'front_side' => $objects['front_side'] ?? null,
            'distance' => $objects['distance'] ?? null,
            'asset_types' => EstimateAssetDefault::LAND_ASSET_TYPE,
            'transaction_type_ids' => EstimateAssetDefault::TRANSACTION_TYPE,
            'land_type_purpose' => null,
            'province_id' => $objects['province_id'] ?? null,
            'district_id' => $objects['district_id'] ?? null,
            'street_id' => $objects['street_id'] ?? null,
        ];

        $landTypePurpose = [];
        if ($objects['unrecognized']) {
            foreach ($objects['unrecognized'] as $value) {
                $landTypePurpose[] = $value['land_type_purpose'];
                if (in_array($value['land_type_purpose'], EstimateAssetDefault::GROUP_LAND_TYPE)) {
                    $search['group_land_type_purpose'] = EstimateAssetDefault::GROUP_LAND_TYPE;
                }
            }
            $search['land_type_purpose'] = $landTypePurpose;
            $assets = $this->findAssetEstimate($search);
        }
        return array_values(array_map("unserialize", array_unique(array_map("serialize", $assets))));
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
            'distance' => $objects['distance'] ?? null,
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
        $landTypePurpose = [];
        $returnAsset = [];
        $ids=[];
        if ($objects['unrecognized']) {
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
            if (count($assets) == 3) {
                $result['assets'] = array_values(array_map("unserialize", array_unique(array_map("serialize", $assets))));
                $result['status'] = ValueDefault::STATUS_SUCCESS;
                $result['error_message'] = null;
            } else {
                if(isset($objects['main_road_length'])) {
                    if ($objects['main_road_length'] >= 4) {
                        $search['main_road_length'] = (($objects['main_road_length'] * 0.8) >= 4) ? $objects['main_road_length'] * 0.8 : 4;
                        $search['main_road_length_to'] = $objects['main_road_length'] * 1.2;
                    } else {
                        $search['main_road_length'] = $objects['main_road_length'] * 0.8;
                        $search['main_road_length_to'] = (($objects['main_road_length'] * 1.2) <= 4) ? $objects['main_road_length'] * 1.2 : 4;
                    }
                }
                $search['not_in_ids'] = $ids;
                $caches = $this->findAssetEstimate($search, 3 - count($returnAsset));
                $result['steps'] = EstimateAssetDefault::STEP_1_9;
                foreach ($caches as $cache) {
                    $ids[] = $cache['id'];
                    $returnAsset[] = $cache;
                }

                $returnAsset = array_values(array_map("unserialize", array_unique(array_map("serialize", $returnAsset))));
                if (count($returnAsset) == 0) {
                    $result['assets'] = array_values(array_map("unserialize", array_unique(array_map("serialize", $returnAsset))));
                    $result['status'] = ValueDefault::STATUS_ERROR;
                    $result['error_message'] = EstimateAssetDefault::ERROR_MESSAGE;
                    return $result;
                } else {
                    if (count($returnAsset) > 0) {
                        $result['assets'] = array_values(array_map("unserialize", array_unique(array_map("serialize", $returnAsset))));
                        $result['status'] = ValueDefault::STATUS_SUCCESS;
                        $result['error_message'] = null;
                        return $result;
                    } else {
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

    /**
     * @param $search
     * @param int $limit
     * @return array
     */
    private  function findAssetEstimate($search, int $limit = 3): array
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
            $landTypePurposeId = $search['land_type_purpose_id'] ?? null;
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
            if (isset($frontSide)) {
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

            $array['bool']['must'][] = [
                'exists' => [
                    'field' => 'street'
                ]
            ];

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
                /* if (!empty($groupLandTypePurpose)) {
                    foreach ($landTypePurpose as $value) {
                        if (!in_array($value, $groupLandTypePurpose)) {
                            $array['bool']['should'][] = [
                                'match' => [
                                    'land_type_purpose.id' => $value,
                                ]
                            ];
                        }
                    }
                    $array['bool']['should'][] = [
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
                } */
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

            //remove TSC
            $array['bool']['must_not'][] = [
                'match' => [
                    'migrate_status' => "TSC"
                ]
            ];

            $sourceFields = [
                // 'id',
                // 'version'
            ];
            $response = CompareAssetGeneral::searchByQuery($array, null,  $sourceFields, $limit, null, $sortBy);
            $result = [];

            foreach ($response->getHits()['hits'] as $hit) {
                $hit['_source']['sort'] = $hit['sort'][0] ?? null;
                array_push($result, $hit['_source']);
            }
            $result =  collect($result)->sortBy('id',SORT_DESC,true)->toArray();
            return $result;
        } catch (Exception $exception) {
            Log::error($exception);
            return [];
        }
    }

    /**
     * @param $search
     * @param int $limit
     * @return array
     */
    private  function findApartmentAsset($search, int $limit = 3): array
    {
        try {
            $transactionTypeIds = $search['transaction_type_ids'] ?? null;
            $assetTypeId = $search['assetTypeId'] ?? null;
            $location = $search['location'] ?? null;
            $distance = $search['distance'] ?? null;
            $bedroomNum = $search['bedroom_num'] ?? null;

            $array ['bool']['must'] = [];
            $array['bool']['must'][] = [
                'match' => [
                    'status' => 1
                ]
            ];
            $array['bool']['must'][] = [
                'terms' => [
                    'asset_type_id' => $assetTypeId
                ]
            ];
            if (!empty($projectId)) {
                $array['bool']['must'][] = [
                    'match' => [
                        'project_id' => $projectId
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

            $array['bool']['must'][] = [
                'range' => [
                    'public_date' => [
                        'gte' => now()->addMonths(-24)->format('d-m-Y'),
                        'lte' => now()->format('d-m-Y'),
                        'format' => 'dd-MM-yyyy||yyyy',
                    ]
                ]
            ];

            if (!empty($transactionTypeIds)) {
                $array['bool']['must'][] = [
                    'terms' => [
                        'transaction_type' => $transactionTypeIds,
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
            //remove TSC
            $array['bool']['must_not'][] = [
                'match' => [
                    'migrate_status' => "TSC"
                ]
            ];

            $sourceFields = [
                // 'id',
                // 'version'
            ];
            $response = CompareAssetGeneral::searchByQuery($array, null,  $sourceFields, $limit, null, $sortBy);
            $result = [];

            foreach ($response->getHits()['hits'] as $hit) {
                $hit['_source']['sort'] = $hit['sort'][0] ?? null;
                array_push($result, $hit['_source']);
            }
            $result =  collect($result)->sortBy('id',SORT_DESC,true)->toArray();
            return $result;
        } catch (Exception $exception) {
            Log::error($exception);
            return [];
        }
    }

}
