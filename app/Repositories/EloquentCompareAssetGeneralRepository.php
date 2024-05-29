<?php

namespace App\Repositories;

use App\Contracts\CompareAssetGeneralRepository;
use App\Enum\EstimateAssetDefault;
use App\Enum\ValueDefault;
use App\Models\Apartment;
use App\Models\ApartmentSpecification;
use App\Models\Block;
use App\Models\BlockSpecification;
use App\Models\BlockSpecificationHasBasicUtility;
use App\Models\CompareAssetGeneral;
use App\Models\CompareAssetVersion;
use App\Models\CompareGeneralPic;
use App\Models\CompareOtherAsset;
use App\Models\CompareOtherPic;
use App\Models\CompareProperty;
use App\Models\ComparePropertyDetail;
use App\Models\ComparePropertyDoc;
use App\Models\ComparePropertyPic;
use App\Models\ComparePropertyTurningTime;
use App\Models\CompareTangibleAsset;
use App\Models\CompareTangiblePic;
use App\Models\Dictionary;
use App\Models\Distance;
use App\Models\RoomDetail;
use App\Models\RoomFurnitureDetail;
use App\Models\Project;
use App\Models\UnitPrice;
use App\Services\CommonService;
use App\Notifications\ActivityLog;
use Carbon\Carbon;
use Elasticquent\ElasticquentResultCollection;
use Elasticsearch\ClientBuilder;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon as SupportCarbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\QueryBuilder\QueryBuilder;
use Throwable;
use const App\Enum\ERROR_MESSAGE;

class EloquentCompareAssetGeneralRepository extends EloquentRepository implements CompareAssetGeneralRepository
{
    private string $defaultSort = 'created_at';
    private string $allowedSorts = 'updated_at';

    use ActivityLog;

    /**
     * @return array|array[]
     */
    public function findPaging(): array
    {
        try {
            define("DONAVA_REMOVE_SEARCH_TEXT", 'TSS_');
            define("DONAVA_REMOVE_SEARCH_OLD_TEXT", 'TSC_');
            define("TRANSACTION_TYPE_SORT", 'transaction_type_description');
            define("ASSET_TYPE_SORT", 'asset_type');
            define("ASSET_CREATE_AT_SORT", 'created_at');
            define("SEARCH_ALL_USER", 'tất cả người tạo');
            $perPage = (int)request()->get('limit');
            $page = ((int)request()->get('page')) - 1;
            $sort = request()->get('sort');
            $order = request()->get('order');
            $search = request()->get('search');
            $id = request()->get('id');
            $coordinates = request()->get('coordinates');
            $createdBy = request()->get('created_by');
            $createdAt = request()->get('created_at');
            $updatedAt = request()->get('updated_at');
            $assetTypeId = request()->get('asset_type_id');
            $assetTypeIds = request()->get('asset_type_ids');
            $inputSource = request()->get('input_source');
            $landNo = request()->get('land_no');
            $docNo = request()->get('doc_no');
            $provinceId = request()->get('province_id');
            $districtId = request()->get('district_id');
            $wardId = request()->get('ward_id');
            $streetId = request()->get('street_id');
            $totalAreaFrom = request()->get('total_area_from');
            $totalAreaTo = request()->get('total_area_to');
            $totalConstructionAreaTo = request()->get('total_construction_area_to');
            $totalConstructionAreaFrom = request()->get('total_construction_area_from');
            $averageLandUnitPriceFrom = request()->get('average_land_unit_price_from');
            $averageLandUnitPriceTo = request()->get('average_land_unit_price_to');
            $totalAmountFrom = request()->get('total_amount_from');
            $totalAmountTo = request()->get('total_amount_to');
            $sourceId = request()->get('source_id');
            $contactPerson = request()->get('contact_person');
            $contactPhone = request()->get('contact_phone');
            $publicDate = request()->get('public_date');
            $publicDateFrom = request()->get('public_date_from');
            $publicDateTo = request()->get('public_date_to');
            $year = request()->get('year');
            $status = request()->get('status');
            $transactionTypeIds = request()->get('transaction_type');
            $location = request()->get('location');
            $distance = request()->get('distance');
            $frontSide = request()->get('front_side');
            $user = CommonService::getUser();
            $status = request()->get('status');
            $array['bool']['must'] = [];
            if (!empty($search) && ((mb_substr($search, 0, 4) != DONAVA_REMOVE_SEARCH_TEXT) || (mb_substr($search, 0, 4) != DONAVA_REMOVE_SEARCH_OLD_TEXT))) {
                $temp = str_replace(DONAVA_REMOVE_SEARCH_OLD_TEXT, '', urldecode(mb_strtoupper($search)));
                $array['bool']['must'][0]['bool']['should'] = [[
                    'wildcard' => [
                        'coordinates.keyword' => [
                            'value' => '*' . urldecode($search) . '*'
                        ]
                    ]
                ], [
                    'wildcard' => [
                        'created_by.keyword' => [
                            'value' => '*' . urldecode($search) . '*'
                        ]
                    ]
                ], [
                    'wildcard' => [
                        'full_address_search.keyword' => [
                            'value' => '*' . urldecode(mb_strtolower($search)) . '*'
                        ]
                    ]
                ], [
                    'match' => [
                        'id' => (int)str_replace(DONAVA_REMOVE_SEARCH_TEXT, '', urldecode(mb_strtoupper($temp)))
                    ]
                ]];
            }

            if (!empty($search) && ((mb_substr($search, 0, 4) == DONAVA_REMOVE_SEARCH_TEXT) || (mb_substr($search, 0, 4) == DONAVA_REMOVE_SEARCH_OLD_TEXT))) {
                $temp = str_replace(DONAVA_REMOVE_SEARCH_OLD_TEXT, '', urldecode(mb_strtoupper($search)));
                $array['bool']['must'][] = [
                    'match' => [
                        'id' => str_replace(DONAVA_REMOVE_SEARCH_TEXT, '', urldecode($temp))
                    ]
                ];
            }

            if (!empty($id)) {
                $temp = str_replace(DONAVA_REMOVE_SEARCH_OLD_TEXT, '', urldecode(mb_strtoupper($id)));
                $array['bool']['must'][] = [
                    'match' => [
                        'id' => str_replace(DONAVA_REMOVE_SEARCH_TEXT, '', urldecode($temp))
                    ]
                ];
            }
            if (empty($status)) {
                $array['bool']['must'][] = [
                    'match' => [
                        'status' => 1
                    ]
                ];
            } else {
                $array['bool']['must'][] = [
                    'match' => [
                        'status' => (int)$status
                    ]
                ];
            }
            if (!empty($coordinates)) {
                $array['bool']['must'][] = [
                    'wildcard' => [
                        'coordinates.keyword' => [
                            'value' => '*' . $coordinates . '*'
                        ]
                    ]
                ];
            }

            if (!empty($createdBy) && (mb_strtolower($createdBy) != urldecode(mb_strtolower(SEARCH_ALL_USER)))) {
                $array['bool']['must'][] = [
                    'wildcard' => [
                        'created_by.keyword' => [
                            'value' => '*' . urldecode($createdBy) . '*'
                        ]
                    ]
                ];
            }
            if (!empty($createdAt)) {
                $array['bool']['must'][] = [
                    'match' => [
                        'created_at' => $createdAt
                    ]
                ];
            }
            if (!empty($updatedAt)) {
                $array['bool']['must'][] = [
                    'match' => [
                        'updated_at' => $updatedAt
                    ]
                ];
            }
            if (!empty($assetTypeId)) {
                $array['bool']['must'][] = [
                    'match' => [
                        'asset_type_id' => $assetTypeId
                    ]
                ];
            }
            if (!empty($assetTypeIds)) {
                $array['bool']['must'][] = [
                    'terms' => [
                        'asset_type_id' => json_decode($assetTypeIds),
                    ]
                ];
            }
            if (!empty($inputSource)) {
                $array['bool']['must'][] = [
                    'wildcard' => [
                        'input_source.keyword' => [
                            'value' => '*' . urldecode($inputSource) . '*'
                        ]
                    ]
                ];
            }
            if (!empty($landNo)) {
                $array['bool']['must'][] = [
                    'match' => [
                        'land_no' => $landNo
                    ]
                ];
            }
            if (!empty($docNo)) {
                $array['bool']['must'][] = [
                    'match' => [
                        'doc_no' => $docNo
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
            if (!empty($streetId)) {
                $array['bool']['must'][] = [
                    'match' => [
                        'street_id' => $streetId
                    ]
                ];
            }

            if (!empty($totalAreaTo)) {
                $array['bool']['must'][] = [
                    'range' => [
                        'total_area' => [
                            'gte' => !empty($totalAreaFrom) ? (int)$totalAreaFrom : 0,
                            'lte' => (int)$totalAreaTo
                        ]
                    ]
                ];
            }
            if (!empty($totalConstructionAreaTo)) {
                $array['bool']['must'][] = [
                    'range' => [
                        'total_construction_area' => [
                            'gte' => !empty($totalConstructionAreaFrom) ? $totalConstructionAreaFrom : 0,
                            'lte' => $totalConstructionAreaTo
                        ]
                    ]
                ];
            }
            if (!empty($averageLandUnitPriceTo)) {
                $array['bool']['must'][] = [
                    'range' => [
                        'average_land_unit_price' => [
                            'gte' => !empty($averageLandUnitPriceFrom) ? $averageLandUnitPriceFrom : 0,
                            'lte' => $averageLandUnitPriceTo
                        ]
                    ]
                ];
            }
            if (!empty($totalAmountTo)) {
                $array['bool']['must'][] = [
                    'range' => [
                        'total_amount' => [
                            'gte' => !empty($totalAmountFrom) ? $totalAmountFrom : 0,
                            'lte' => $totalAmountTo
                        ]
                    ]
                ];
            }
            if (!empty($sourceId)) {
                $array['bool']['must'][] = [
                    'match' => [
                        'source_id' => $sourceId
                    ]
                ];
            }
            if (!empty($contactPerson)) {
                $array['bool']['must'][] = [
                    'wildcard' => [
                        'contact_person.keyword' => [
                            'value' => '*' . urldecode($contactPerson) . '*'
                        ]
                    ]
                ];
            }
            if (!empty($contactPhone)) {
                $array['bool']['must'][] = [
                    'wildcard' => [
                        'contact_phone.keyword' => [
                            'value' => '*' . $contactPhone . '*'
                        ]
                    ]
                ];
            }
            if (!empty($publicDate)) {
                $array['bool']['must'][] = [
                    'match' => [
                        'public_date' => $publicDate,
                    ]
                ];
            }
            if (!empty($publicDateFrom)) {
                $array['bool']['must'][] = [
                    'range' => [
                        'public_date' => [
                            'gte' => $publicDateFrom,
                            'format' => 'dd-MM-yyyy||yyyy',
                        ]
                    ]
                ];
            }
            if (!empty($publicDateTo)) {
                $array['bool']['must'][] = [
                    'range' => [
                        'public_date' => [
                            'lte' => $publicDateTo,
                            'format' => 'dd-MM-yyyy||yyyy',
                        ]
                    ]
                ];
            }
            if (!empty($year)) {
                $array['bool']['must'][] = [
                    'range' => [
                        'public_date' => [
                            'gte' => '01-01-' . $year,
                            'lte' => '31-12-' . $year,
                            'format' => 'dd-MM-yyyy||yyyy',
                        ]
                    ]
                ];
            }

            if (!empty($transactionTypeIds)) {
                $array['bool']['must'][] = [
                    'terms' => [
                        'transaction_type' => json_decode($transactionTypeIds),
                    ]
                ];
            }

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
            }
            if (isset($frontSide)) {
                $array['bool']['must'][] = [
                    'match' => [
                        'front_side' => $frontSide
                    ]
                ];
            }
            // if ($user->roles->last()->name === 'USER') {
            //     $array['bool']['must'][] = [
            //         'match' => [
            //             'created_by' => $user->name
            //         ]
            //     ];
            // }
            if (!empty($sort)) {
                if ($sort == TRANSACTION_TYPE_SORT) {
                    $sortBy[TRANSACTION_TYPE_SORT . '.keyword'] = ['order' => !empty($order) ? $order : 'asc'];
                } elseif ($sort == ASSET_TYPE_SORT) {
                    $sortBy[ASSET_TYPE_SORT . '.keyword'] = ['order' => !empty($order) ? $order : 'asc'];
                } elseif ($sort == ASSET_CREATE_AT_SORT) {
                    $sortBy[ASSET_CREATE_AT_SORT] = ['order' => !empty($order) ? $order : 'desc'];
                } else {
                    $sortBy[$sort] = ['order' => !empty($order) ? $order : 'asc'];
                }
            } else {
                $sortBy['created_at'] = ['order' => 'desc'];
                $sortBy['updated_at'] = ['order' => 'desc'];
                $sortBy['id'] = ['order' => 'desc'];
            }
            // dd($array);
            $search_result = CompareAssetGeneral::searchByQuery($array, null, null, $perPage, $page * $perPage, $sortBy);

            return $this->responseByResult($search_result, $page, $perPage);
        } catch (Exception $exception) {

            Log::error($exception);
            return ['data' => []];
        }
    }

    /**
     * @return array[]|ElasticquentResultCollection
     */
    public function findAllInElastic()
    {
        try {
            define("DONAVA_REMOVE_SEARCH_TEXT", 'TSSS_');
            define("TRANSACTION_TYPE_SORT", 'transaction_type_description');
            define("ASSET_TYPE_SORT", 'asset_type');
            define("ASSET_CREATE_AT_SORT", 'created_at');
            define("SEARCH_ALL_USER", 'tất cả người tạo');
            $sort = request()->get('sort');
            $order = request()->get('order');
            $search = request()->get('search');
            $id = request()->get('id');
            $coordinates = request()->get('coordinates');
            $createdBy = request()->get('created_by');
            $createdAt = request()->get('created_at');
            $updatedAt = request()->get('updated_at');
            $assetTypeId = request()->get('asset_type_id');
            $assetTypeIds = request()->get('asset_type_ids');
            $inputSource = request()->get('input_source');
            $landNo = request()->get('land_no');
            $docNo = request()->get('doc_no');
            $provinceId = request()->get('province_id');
            $districtId = request()->get('district_id');
            $wardId = request()->get('ward_id');
            $streetId = request()->get('street_id');
            $totalAreaFrom = request()->get('total_area_from');
            $totalAreaTo = request()->get('total_area_to');
            $totalConstructionAreaTo = request()->get('total_construction_area_to');
            $totalConstructionAreaFrom = request()->get('total_construction_area_from');
            $averageLandUnitPriceFrom = request()->get('average_land_unit_price_from');
            $averageLandUnitPriceTo = request()->get('average_land_unit_price_to');
            $totalAmountFrom = request()->get('total_amount_from');
            $totalAmountTo = request()->get('total_amount_to');
            $sourceId = request()->get('source_id');
            $contactPerson = request()->get('contact_person');
            $contactPhone = request()->get('contact_phone');
            $publicDate = request()->get('public_date');
            $publicDateFrom = request()->get('public_date_from');
            $publicDateTo = request()->get('public_date_to');
            $year = request()->get('year');
            $transactionTypeIds = request()->get('transaction_type');
            $location = request()->get('location');
            $distance = request()->get('distance');
            $frontSide = request()->get('front_side');
            $isCheckFrontside = request()->get('is_check_frontside');

            $status = request()->get('status');
            $array['bool']['must'] = [];
            if (!empty($search) && (mb_substr($search, 0, 5) != DONAVA_REMOVE_SEARCH_TEXT)) {
                $array['bool']['must'][0]['bool']['should'] = [[
                    'wildcard' => [
                        'coordinates.keyword' => [
                            'value' => '*' . urldecode($search) . '*'
                        ]
                    ]
                ], [
                    'wildcard' => [
                        'created_by.keyword' => [
                            'value' => '*' . urldecode($search) . '*'
                        ]
                    ]
                ], [
                    'wildcard' => [
                        'full_address_search.keyword' => [
                            'value' => '*' . urldecode(mb_strtolower($search)) . '*'
                        ]
                    ]
                ], [
                    'match' => [
                        'id' => (int)str_replace(DONAVA_REMOVE_SEARCH_TEXT, '', urldecode(mb_strtolower($search)))
                    ]
                ]];
            }

            if (!empty($search) && (mb_substr($search, 0, 5) == DONAVA_REMOVE_SEARCH_TEXT)) {
                $array['bool']['must'][] = [
                    'match' => [
                        'id' => str_replace(DONAVA_REMOVE_SEARCH_TEXT, '', urldecode($search))
                    ]
                ];
            }

            if (!empty($id)) {
                $array['bool']['must'][] = [
                    'match' => [
                        'id' => str_replace(DONAVA_REMOVE_SEARCH_TEXT, '', urldecode($id))
                    ]
                ];
            }
            if (empty($status)) {
                $array['bool']['must'][] = [
                    'match' => [
                        'status' => 1
                    ]
                ];
            } else {
                $array['bool']['must'][] = [
                    'match' => [
                        'status' => (int)$status
                    ]
                ];
            }
            if (!empty($coordinates)) {
                $array['bool']['must'][] = [
                    'wildcard' => [
                        'coordinates.keyword' => [
                            'value' => '*' . $coordinates . '*'
                        ]
                    ]
                ];
            }

            if (!empty($createdBy) && (mb_strtolower($createdBy) != urldecode(mb_strtolower(SEARCH_ALL_USER)))) {
                $array['bool']['must'][] = [
                    'wildcard' => [
                        'created_by.keyword' => [
                            'value' => '*' . urldecode($createdBy) . '*'
                        ]
                    ]
                ];
            }
            if (!empty($createdAt)) {
                $array['bool']['must'][] = [
                    'match' => [
                        'created_at' => $createdAt
                    ]
                ];
            }
            if (!empty($updatedAt)) {
                $array['bool']['must'][] = [
                    'match' => [
                        'updated_at' => $updatedAt
                    ]
                ];
            }
            if (!empty($assetTypeId)) {
                $array['bool']['must'][] = [
                    'match' => [
                        'asset_type_id' => $assetTypeId
                    ]
                ];
            }
            if (!empty($assetTypeIds)) {
                $array['bool']['must'][] = [
                    'terms' => [
                        'asset_type_id' => json_decode($assetTypeIds),
                    ]
                ];
            }
            if (!empty($inputSource)) {
                $array['bool']['must'][] = [
                    'wildcard' => [
                        'input_source.keyword' => [
                            'value' => '*' . urldecode($inputSource) . '*'
                        ]
                    ]
                ];
            }
            if (!empty($landNo)) {
                $array['bool']['must'][] = [
                    'match' => [
                        'land_no' => $landNo
                    ]
                ];
            }
            if (!empty($docNo)) {
                $array['bool']['must'][] = [
                    'match' => [
                        'doc_no' => $docNo
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
            if (isset($assetTypeIds) && (!in_array(39, json_decode($assetTypeIds)))) {
                if (!empty($streetId)) {
                    $array['bool']['must'][] = [
                        'match' => [
                            'street_id' => $streetId
                        ]
                    ];
                }
                $array['bool']['must'][] = [
                    'exists' => [
                        'field' => 'street'
                    ]
                ];
            }


            if (!empty($totalAreaTo)) {
                $array['bool']['must'][] = [
                    'range' => [
                        'total_area' => [
                            'gte' => !empty($totalAreaFrom) ? (int)$totalAreaFrom : 0,
                            'lte' => (int)$totalAreaTo
                        ]
                    ]
                ];
            }
            if (!empty($totalConstructionAreaTo)) {
                $array['bool']['must'][] = [
                    'range' => [
                        'total_construction_area' => [
                            'gte' => !empty($totalConstructionAreaFrom) ? $totalConstructionAreaFrom : 0,
                            'lte' => $totalConstructionAreaTo
                        ]
                    ]
                ];
            }
            if (!empty($averageLandUnitPriceTo)) {
                $array['bool']['must'][] = [
                    'range' => [
                        'average_land_unit_price' => [
                            'gte' => !empty($averageLandUnitPriceFrom) ? $averageLandUnitPriceFrom : 0,
                            'lte' => $averageLandUnitPriceTo
                        ]
                    ]
                ];
            }
            if (!empty($totalAmountTo)) {
                $array['bool']['must'][] = [
                    'range' => [
                        'total_amount' => [
                            'gte' => !empty($totalAmountFrom) ? $totalAmountFrom : 0,
                            'lte' => $totalAmountTo
                        ]
                    ]
                ];
            }
            if (!empty($sourceId)) {
                $array['bool']['must'][] = [
                    'match' => [
                        'source_id' => $sourceId
                    ]
                ];
            }
            if (!empty($contactPerson)) {
                $array['bool']['must'][] = [
                    'wildcard' => [
                        'contact_person.keyword' => [
                            'value' => '*' . urldecode($contactPerson) . '*'
                        ]
                    ]
                ];
            }
            if (!empty($contactPhone)) {
                $array['bool']['must'][] = [
                    'wildcard' => [
                        'contact_phone.keyword' => [
                            'value' => '*' . $contactPhone . '*'
                        ]
                    ]
                ];
            }
            if (!empty($publicDate)) {
                $array['bool']['must'][] = [
                    'match' => [
                        'public_date' => $publicDate,
                    ]
                ];
            }
            if (!empty($publicDateFrom)) {
                $array['bool']['must'][] = [
                    'range' => [
                        'public_date' => [
                            'gte' => $publicDateFrom,
                            'format' => 'dd-MM-yyyy||yyyy',
                        ]
                    ]
                ];
            }
            if (!empty($publicDateTo)) {
                $array['bool']['must'][] = [
                    'range' => [
                        'public_date' => [
                            'lte' => $publicDateTo,
                            'format' => 'dd-MM-yyyy||yyyy',
                        ]
                    ]
                ];
            }
            if (!empty($year)) {
                $array['bool']['must'][] = [
                    'range' => [
                        'public_date' => [
                            'gte' => '01-01-' . $year,
                            'lte' => '31-12-' . $year,
                            'format' => 'dd-MM-yyyy||yyyy',
                        ]
                    ]
                ];
            } else {
                $array['bool']['must'][] = [
                    'range' => [
                        'public_date' => [
                            'gte' => now()->addMonths(-24)->format('d-m-Y'),
                            'lte' => now()->format('d-m-Y'),
                            'format' => 'dd-MM-yyyy||yyyy',
                        ]
                    ]
                ];
            }

            if (!empty($transactionTypeIds)) {
                $array['bool']['must'][] = [
                    'terms' => [
                        //'transaction_type_id' => json_decode($transactionTypeIds),
                        'transaction_type' => json_decode($transactionTypeIds),
                    ]
                ];
            }

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
            }
            if (isset($isCheckFrontside)) {
                $isCheckFrontside = filter_var($isCheckFrontside, FILTER_VALIDATE_BOOLEAN);
                if (!$isCheckFrontside) {
                    if (isset($frontSide)) {
                        $array['bool']['must'][] = [
                            'match' => [
                                'front_side' => $frontSide
                            ]
                        ];
                    }
                }
            } else if (isset($frontSide)) {
                $array['bool']['must'][] = [
                    'match' => [
                        //'properties.front_side' => $frontSide
                        'front_side' => $frontSide
                    ]
                ];
            }

            //remove TSC
            if (empty($year)) {
                $array['bool']['must_not'][] = [
                    'match' => [
                        'migrate_status' => "TSC"
                    ]
                ];
            }

            if (!empty($sort)) {
                if ($sort == TRANSACTION_TYPE_SORT) {
                    $sortBy[TRANSACTION_TYPE_SORT . '.keyword'] = ['order' => !empty($order) ? $order : 'asc'];
                } elseif ($sort == ASSET_TYPE_SORT) {
                    $sortBy[ASSET_TYPE_SORT . '.keyword'] = ['order' => !empty($order) ? $order : 'asc'];
                } elseif ($sort == ASSET_CREATE_AT_SORT) {
                    $sortBy[ASSET_CREATE_AT_SORT] = ['order' => !empty($order) ? $order : 'desc'];
                } else {
                    $sortBy[$sort] = ['order' => !empty($order) ? $order : 'asc'];
                }
            } else {
                $sortBy['created_at'] = ['order' => 'desc'];
                $sortBy['updated_at'] = ['order' => 'desc'];
                $sortBy['id'] = ['order' => 'desc'];
            }

            $result = CompareAssetGeneral::searchByQuery($array, null, null, 10000, null, $sortBy);
            return $result;
        } catch (Exception $exception) {
            Log::error($exception);
            return ['data' => []];
        }
    }

    /**
     * @return array[]|ElasticquentResultCollection
     */
    public function findAllInElastic_v2()
    {
        try {
            define("DONAVA_REMOVE_SEARCH_TEXT", 'TSSS_');
            define("TRANSACTION_TYPE_SORT", 'transaction_type_description');
            define("ASSET_TYPE_SORT", 'asset_type');
            define("ASSET_CREATE_AT_SORT", 'created_at');
            define("SEARCH_ALL_USER", 'tất cả người tạo');
            $sort = request()->get('sort');
            $order = request()->get('order');
            $search = request()->get('search');
            $id = request()->get('id');
            $coordinates = request()->get('coordinates');
            $createdBy = request()->get('created_by');
            $createdAt = request()->get('created_at');
            $updatedAt = request()->get('updated_at');
            $assetTypeId = request()->get('asset_type_id');
            $assetTypeIds = request()->get('asset_type_ids');
            $inputSource = request()->get('input_source');
            $landNo = request()->get('land_no');
            $docNo = request()->get('doc_no');
            $provinceId = request()->get('province_id');
            $districtId = request()->get('district_id');
            $wardId = request()->get('ward_id');
            $streetId = request()->get('street_id');
            $totalAreaFrom = request()->get('total_area_from');
            $totalAreaTo = request()->get('total_area_to');
            $totalConstructionAreaTo = request()->get('total_construction_area_to');
            $totalConstructionAreaFrom = request()->get('total_construction_area_from');
            $averageLandUnitPriceFrom = request()->get('average_land_unit_price_from');
            $averageLandUnitPriceTo = request()->get('average_land_unit_price_to');
            $totalAmountFrom = request()->get('total_amount_from');
            $totalAmountTo = request()->get('total_amount_to');
            $sourceId = request()->get('source_id');
            $contactPerson = request()->get('contact_person');
            $contactPhone = request()->get('contact_phone');
            $publicDate = request()->get('public_date');
            $publicDateFrom = request()->get('public_date_from');
            $publicDateTo = request()->get('public_date_to');
            $year = request()->get('year');
            $transactionTypeIds = request()->get('transaction_type');
            $location = request()->get('location');
            $distance = request()->get('distance');
            $frontSide = request()->get('front_side');
            $isCheckFrontside = request()->get('is_check_frontside');

            $status = request()->get('status');
            $array['bool']['must'] = [];
            if (!empty($search) && (mb_substr($search, 0, 5) != DONAVA_REMOVE_SEARCH_TEXT)) {
                $array['bool']['must'][0]['bool']['should'] = [[
                    'wildcard' => [
                        'coordinates.keyword' => [
                            'value' => '*' . urldecode($search) . '*'
                        ]
                    ]
                ], [
                    'wildcard' => [
                        'created_by.keyword' => [
                            'value' => '*' . urldecode($search) . '*'
                        ]
                    ]
                ], [
                    'wildcard' => [
                        'full_address_search.keyword' => [
                            'value' => '*' . urldecode(mb_strtolower($search)) . '*'
                        ]
                    ]
                ], [
                    'match' => [
                        'id' => (int)str_replace(DONAVA_REMOVE_SEARCH_TEXT, '', urldecode(mb_strtolower($search)))
                    ]
                ]];
            }

            if (!empty($search) && (mb_substr($search, 0, 5) == DONAVA_REMOVE_SEARCH_TEXT)) {
                $array['bool']['must'][] = [
                    'match' => [
                        'id' => str_replace(DONAVA_REMOVE_SEARCH_TEXT, '', urldecode($search))
                    ]
                ];
            }

            if (!empty($id)) {
                $array['bool']['must'][] = [
                    'match' => [
                        'id' => str_replace(DONAVA_REMOVE_SEARCH_TEXT, '', urldecode($id))
                    ]
                ];
            }
            if (empty($status)) {
                $array['bool']['must'][] = [
                    'match' => [
                        'status' => 1
                    ]
                ];
            } else {
                $array['bool']['must'][] = [
                    'match' => [
                        'status' => (int)$status
                    ]
                ];
            }
            if (!empty($coordinates)) {
                $array['bool']['must'][] = [
                    'wildcard' => [
                        'coordinates.keyword' => [
                            'value' => '*' . $coordinates . '*'
                        ]
                    ]
                ];
            }

            if (!empty($createdBy) && (mb_strtolower($createdBy) != urldecode(mb_strtolower(SEARCH_ALL_USER)))) {
                $array['bool']['must'][] = [
                    'wildcard' => [
                        'created_by.keyword' => [
                            'value' => '*' . urldecode($createdBy) . '*'
                        ]
                    ]
                ];
            }
            if (!empty($createdAt)) {
                $array['bool']['must'][] = [
                    'match' => [
                        'created_at' => $createdAt
                    ]
                ];
            }
            if (!empty($updatedAt)) {
                $array['bool']['must'][] = [
                    'match' => [
                        'updated_at' => $updatedAt
                    ]
                ];
            }
            if (!empty($assetTypeId)) {
                $array['bool']['must'][] = [
                    'match' => [
                        'asset_type_id' => $assetTypeId
                    ]
                ];
            }
            if (!empty($assetTypeIds)) {
                $array['bool']['must'][] = [
                    'terms' => [
                        'asset_type_id' => json_decode($assetTypeIds),
                    ]
                ];
            }
            if (!empty($inputSource)) {
                $array['bool']['must'][] = [
                    'wildcard' => [
                        'input_source.keyword' => [
                            'value' => '*' . urldecode($inputSource) . '*'
                        ]
                    ]
                ];
            }
            if (!empty($landNo)) {
                $array['bool']['must'][] = [
                    'match' => [
                        'land_no' => $landNo
                    ]
                ];
            }
            if (!empty($docNo)) {
                $array['bool']['must'][] = [
                    'match' => [
                        'doc_no' => $docNo
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
            if (isset($assetTypeIds) && (!in_array(39, json_decode($assetTypeIds)))) {
                if (!empty($streetId)) {
                    $array['bool']['must'][] = [
                        'match' => [
                            'street_id' => $streetId
                        ]
                    ];
                }
                $array['bool']['must'][] = [
                    'exists' => [
                        'field' => 'street'
                    ]
                ];
            }


            if (!empty($totalAreaTo)) {
                $array['bool']['must'][] = [
                    'range' => [
                        'total_area' => [
                            'gte' => !empty($totalAreaFrom) ? (int)$totalAreaFrom : 0,
                            'lte' => (int)$totalAreaTo
                        ]
                    ]
                ];
            }
            if (!empty($totalConstructionAreaTo)) {
                $array['bool']['must'][] = [
                    'range' => [
                        'total_construction_area' => [
                            'gte' => !empty($totalConstructionAreaFrom) ? $totalConstructionAreaFrom : 0,
                            'lte' => $totalConstructionAreaTo
                        ]
                    ]
                ];
            }
            if (!empty($averageLandUnitPriceTo)) {
                $array['bool']['must'][] = [
                    'range' => [
                        'average_land_unit_price' => [
                            'gte' => !empty($averageLandUnitPriceFrom) ? $averageLandUnitPriceFrom : 0,
                            'lte' => $averageLandUnitPriceTo
                        ]
                    ]
                ];
            }
            if (!empty($totalAmountTo)) {
                $array['bool']['must'][] = [
                    'range' => [
                        'total_amount' => [
                            'gte' => !empty($totalAmountFrom) ? $totalAmountFrom : 0,
                            'lte' => $totalAmountTo
                        ]
                    ]
                ];
            }
            if (!empty($sourceId)) {
                $array['bool']['must'][] = [
                    'match' => [
                        'source_id' => $sourceId
                    ]
                ];
            }
            if (!empty($contactPerson)) {
                $array['bool']['must'][] = [
                    'wildcard' => [
                        'contact_person.keyword' => [
                            'value' => '*' . urldecode($contactPerson) . '*'
                        ]
                    ]
                ];
            }
            if (!empty($contactPhone)) {
                $array['bool']['must'][] = [
                    'wildcard' => [
                        'contact_phone.keyword' => [
                            'value' => '*' . $contactPhone . '*'
                        ]
                    ]
                ];
            }
            if (!empty($publicDate)) {
                $array['bool']['must'][] = [
                    'match' => [
                        'public_date' => $publicDate,
                    ]
                ];
            }
            if (!empty($publicDateFrom)) {
                $array['bool']['must'][] = [
                    'range' => [
                        'public_date' => [
                            'gte' => $publicDateFrom,
                            'format' => 'dd-MM-yyyy||yyyy',
                        ]
                    ]
                ];
            }
            if (!empty($publicDateTo)) {
                $array['bool']['must'][] = [
                    'range' => [
                        'public_date' => [
                            'lte' => $publicDateTo,
                            'format' => 'dd-MM-yyyy||yyyy',
                        ]
                    ]
                ];
            }
            // if (!empty($year)) {
            //     $array['bool']['must'][] = [
            //         'range' => [
            //             'public_date' => [
            //                 'gte' => '01-01-' . $year,
            //                 'lte' => '31-12-' . $year,
            //                 'format' => 'dd-MM-yyyy||yyyy',
            //             ]
            //         ]
            //     ];
            // } else {
            //     $array['bool']['must'][] = [
            //         'range' => [
            //             'public_date' => [
            //                 'gte' => now()->addMonths(-24)->format('d-m-Y'),
            //                 'lte' => now()->format('d-m-Y'),
            //                 'format' => 'dd-MM-yyyy||yyyy',
            //             ]
            //         ]
            //     ];
            // }

            if (!empty($year)) {
                $year = Carbon::parse($year)->format('d-m-Y');
                $array['bool']['must'][] = [
                    'range' => [
                        'public_date' => [
                            'gte' => $year,
                            'format' => 'dd-MM-yyyy||yyyy',
                        ]
                    ]
                ];
            } else {
                $year = now()->year;
                $array['bool']['must'][] = [
                    'range' => [
                        'public_date' => [
                            'gte' => now()->addMonths(-12)->format('d-m-Y'),
                            'format' => 'dd-MM-yyyy||yyyy',
                        ]
                    ]
                ];
            }

            if (!empty($transactionTypeIds)) {
                $array['bool']['must'][] = [
                    'terms' => [
                        //'transaction_type_id' => json_decode($transactionTypeIds),
                        'transaction_type' => json_decode($transactionTypeIds),
                    ]
                ];
            }

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
            }
            if (isset($isCheckFrontside)) {
                $isCheckFrontside = filter_var($isCheckFrontside, FILTER_VALIDATE_BOOLEAN);
                if (!$isCheckFrontside) {
                    if (isset($frontSide)) {
                        $array['bool']['must'][] = [
                            'match' => [
                                'front_side' => $frontSide
                            ]
                        ];
                    }
                }
            } else if (isset($frontSide)) {
                $array['bool']['must'][] = [
                    'match' => [
                        //'properties.front_side' => $frontSide
                        'front_side' => $frontSide
                    ]
                ];
            }

            //remove TSC
            $array['bool']['must_not'][] = [
                'match' => [
                    'migrate_status' => "TSC"
                ]
            ];

            if (!empty($sort)) {
                if ($sort == TRANSACTION_TYPE_SORT) {
                    $sortBy[TRANSACTION_TYPE_SORT . '.keyword'] = ['order' => !empty($order) ? $order : 'asc'];
                } elseif ($sort == ASSET_TYPE_SORT) {
                    $sortBy[ASSET_TYPE_SORT . '.keyword'] = ['order' => !empty($order) ? $order : 'asc'];
                } elseif ($sort == ASSET_CREATE_AT_SORT) {
                    $sortBy[ASSET_CREATE_AT_SORT] = ['order' => !empty($order) ? $order : 'desc'];
                } else {
                    $sortBy[$sort] = ['order' => !empty($order) ? $order : 'asc'];
                }
            } else {
                $sortBy['created_at'] = ['order' => 'desc'];
                $sortBy['updated_at'] = ['order' => 'desc'];
                $sortBy['id'] = ['order' => 'desc'];
            }

            $sourceField = [
                'id',
                'contact_person',
                'contact_phone',
                'public_date',
                'total_amount',
                'total_area',
                'coordinates',
                'full_address',
                'migrate_status',
                'transaction_type_id',
                'transaction_type',
                'transaction_type_description',
                'asset_type',
                'pic',
                'created_at',
                'asset_type_id',
                'total_estimate_amount',
                'updated_at',
                '*'
            ];

            $result = CompareAssetGeneral::searchByQuery($array, null, $sourceField, 10000, null, $sortBy);
            return $result;
        } catch (Exception $exception) {
            Log::error($exception);
            return ['data' => []];
        }
    }


    /**
     * @return array[]|ElasticquentResultCollection
     */
    public function findAllInElastic_v3()
    {
        try {
            define("DONAVA_REMOVE_SEARCH_TEXT", 'TSSS_');
            define("TRANSACTION_TYPE_SORT", 'transaction_type_description');
            define("ASSET_TYPE_SORT", 'asset_type');
            define("ASSET_CREATE_AT_SORT", 'created_at');
            define("SEARCH_ALL_USER", 'tất cả người tạo');
            $sort = request()->get('sort');
            $order = request()->get('order');
            $search = request()->get('search');
            $id = request()->get('id');
            $coordinates = request()->get('coordinates');
            $createdBy = request()->get('created_by');
            $createdAt = request()->get('created_at');
            $updatedAt = request()->get('updated_at');
            $assetTypeId = request()->get('asset_type_id');
            $assetTypeIds = request()->get('asset_type_ids');
            $inputSource = request()->get('input_source');
            $landNo = request()->get('land_no');
            $docNo = request()->get('doc_no');
            $provinceId = request()->get('province_id');
            $districtId = request()->get('district_id');
            $wardId = request()->get('ward_id');
            $streetId = request()->get('street_id');
            $totalAreaFrom = request()->get('total_area_from');
            $totalAreaTo = request()->get('total_area_to');
            $totalConstructionAreaTo = request()->get('total_construction_area_to');
            $totalConstructionAreaFrom = request()->get('total_construction_area_from');
            $averageLandUnitPriceFrom = request()->get('average_land_unit_price_from');
            $averageLandUnitPriceTo = request()->get('average_land_unit_price_to');
            $totalAmountFrom = request()->get('total_amount_from');
            $totalAmountTo = request()->get('total_amount_to');
            $sourceId = request()->get('source_id');
            $contactPerson = request()->get('contact_person');
            $contactPhone = request()->get('contact_phone');
            $publicDate = request()->get('public_date');
            $publicDateFrom = request()->get('public_date_from');
            $publicDateTo = request()->get('public_date_to');
            $year = request()->get('year');
            // if(isset($year))
            // $year =  Carbon::parse($year)->format('Y');
            $transactionTypeIds = request()->get('transaction_type');
            $location = request()->get('location');
            $distance = request()->get('distance');
            $frontSide = request()->get('front_side');
            $isCheckFrontside = request()->get('is_check_frontside');

            $status = request()->get('status');
            $property_type = request()->get('property_type');
            if ($property_type == null) {
                $whereIn = ['DCN', 'DT', 'CC'];
            } elseif ($property_type == 0) {
                $whereIn = ['DCN', 'DT'];
            } else {
                $whereIn = ['CC'];
            }
            $assetType = Dictionary::query()->whereIn('acronym', $whereIn)->get('id')->toArray();
            $assetTypeIds = json_encode(Arr::pluck($assetType, 'id'));
            $array['bool']['must'] = [];
            if (!empty($search) && (mb_substr($search, 0, 5) != DONAVA_REMOVE_SEARCH_TEXT)) {
                $array['bool']['must'][0]['bool']['should'] = [[
                    'wildcard' => [
                        'coordinates.keyword' => [
                            'value' => '*' . urldecode($search) . '*'
                        ]
                    ]
                ], [
                    'wildcard' => [
                        'created_by.keyword' => [
                            'value' => '*' . urldecode($search) . '*'
                        ]
                    ]
                ], [
                    'wildcard' => [
                        'full_address_search.keyword' => [
                            'value' => '*' . urldecode(mb_strtolower($search)) . '*'
                        ]
                    ]
                ], [
                    'match' => [
                        'id' => (int)str_replace(DONAVA_REMOVE_SEARCH_TEXT, '', urldecode(mb_strtolower($search)))
                    ]
                ]];
            }

            if (!empty($search) && (mb_substr($search, 0, 5) == DONAVA_REMOVE_SEARCH_TEXT)) {
                $array['bool']['must'][] = [
                    'match' => [
                        'id' => str_replace(DONAVA_REMOVE_SEARCH_TEXT, '', urldecode($search))
                    ]
                ];
            }

            if (!empty($id)) {
                $array['bool']['must'][] = [
                    'match' => [
                        'id' => str_replace(DONAVA_REMOVE_SEARCH_TEXT, '', urldecode($id))
                    ]
                ];
            }
            if (empty($status)) {
                $array['bool']['must'][] = [
                    'match' => [
                        'status' => 1
                    ]
                ];
            } else {
                $array['bool']['must'][] = [
                    'match' => [
                        'status' => (int)$status
                    ]
                ];
            }
            if (!empty($coordinates)) {
                $array['bool']['must'][] = [
                    'wildcard' => [
                        'coordinates.keyword' => [
                            'value' => '*' . $coordinates . '*'
                        ]
                    ]
                ];
            }

            if (!empty($createdBy) && (mb_strtolower($createdBy) != urldecode(mb_strtolower(SEARCH_ALL_USER)))) {
                $array['bool']['must'][] = [
                    'wildcard' => [
                        'created_by.keyword' => [
                            'value' => '*' . urldecode($createdBy) . '*'
                        ]
                    ]
                ];
            }
            if (!empty($createdAt)) {
                $array['bool']['must'][] = [
                    'match' => [
                        'created_at' => $createdAt
                    ]
                ];
            }
            if (!empty($updateAt)) {
                $array['bool']['must'][] = [
                    'match' => [
                        'updated_at' => $updateAt
                    ]
                ];
            }
            if (!empty($assetTypeId)) {
                $array['bool']['must'][] = [
                    'match' => [
                        'asset_type_id' => $assetTypeId
                    ]
                ];
            }
            if (!empty($assetTypeIds)) {
                $array['bool']['must'][] = [
                    'terms' => [
                        'asset_type_id' => json_decode($assetTypeIds),
                    ]
                ];
            }
            if (!empty($inputSource)) {
                $array['bool']['must'][] = [
                    'wildcard' => [
                        'input_source.keyword' => [
                            'value' => '*' . urldecode($inputSource) . '*'
                        ]
                    ]
                ];
            }
            if (!empty($landNo)) {
                $array['bool']['must'][] = [
                    'match' => [
                        'land_no' => $landNo
                    ]
                ];
            }
            if (!empty($docNo)) {
                $array['bool']['must'][] = [
                    'match' => [
                        'doc_no' => $docNo
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
            if (isset($assetTypeIds) && (!in_array(39, json_decode($assetTypeIds)))) {
                if (!empty($streetId)) {
                    $array['bool']['must'][] = [
                        'match' => [
                            'street_id' => $streetId
                        ]
                    ];
                }
                $array['bool']['must'][] = [
                    'exists' => [
                        'field' => 'street'
                    ]
                ];
            }


            if (!empty($totalAreaTo)) {
                $array['bool']['must'][] = [
                    'range' => [
                        'total_area' => [
                            'gte' => !empty($totalAreaFrom) ? (int)$totalAreaFrom : 0,
                            'lte' => (int)$totalAreaTo
                        ]
                    ]
                ];
            }
            if (!empty($totalConstructionAreaTo)) {
                $array['bool']['must'][] = [
                    'range' => [
                        'total_construction_area' => [
                            'gte' => !empty($totalConstructionAreaFrom) ? $totalConstructionAreaFrom : 0,
                            'lte' => $totalConstructionAreaTo
                        ]
                    ]
                ];
            }
            if (!empty($averageLandUnitPriceTo)) {
                $array['bool']['must'][] = [
                    'range' => [
                        'average_land_unit_price' => [
                            'gte' => !empty($averageLandUnitPriceFrom) ? $averageLandUnitPriceFrom : 0,
                            'lte' => $averageLandUnitPriceTo
                        ]
                    ]
                ];
            }
            if (!empty($totalAmountTo)) {
                $array['bool']['must'][] = [
                    'range' => [
                        'total_amount' => [
                            'gte' => !empty($totalAmountFrom) ? $totalAmountFrom : 0,
                            'lte' => $totalAmountTo
                        ]
                    ]
                ];
            }
            if (!empty($sourceId)) {
                $array['bool']['must'][] = [
                    'match' => [
                        'source_id' => $sourceId
                    ]
                ];
            }
            if (!empty($contactPerson)) {
                $array['bool']['must'][] = [
                    'wildcard' => [
                        'contact_person.keyword' => [
                            'value' => '*' . urldecode($contactPerson) . '*'
                        ]
                    ]
                ];
            }
            if (!empty($contactPhone)) {
                $array['bool']['must'][] = [
                    'wildcard' => [
                        'contact_phone.keyword' => [
                            'value' => '*' . $contactPhone . '*'
                        ]
                    ]
                ];
            }
            if (!empty($publicDate)) {
                $array['bool']['must'][] = [
                    'match' => [
                        'public_date' => $publicDate,
                    ]
                ];
            }
            if (!empty($publicDateFrom)) {
                $array['bool']['must'][] = [
                    'range' => [
                        'public_date' => [
                            'gte' => $publicDateFrom,
                            'format' => 'dd-MM-yyyy||yyyy',
                        ]
                    ]
                ];
            }
            if (!empty($publicDateTo)) {
                $array['bool']['must'][] = [
                    'range' => [
                        'public_date' => [
                            'lte' => $publicDateTo,
                            'format' => 'dd-MM-yyyy||yyyy',
                        ]
                    ]
                ];
            }
            if (!empty($year)) {
                $year = Carbon::parse($year)->format('d-m-Y');
                $array['bool']['must'][] = [
                    'range' => [
                        'public_date' => [
                            'gte' => $year,
                            // 'gte' => '01-01-' . $year,
                            // 'lte' => '31-12-' . $year,
                            'format' => 'dd-MM-yyyy||yyyy',
                        ]
                    ]
                ];
            } else {
                $year = now()->year;
                $array['bool']['must'][] = [
                    'range' => [
                        'public_date' => [
                            // 'gte' => now()->addMonths(-12)->format('d-m-Y'),
                            // 'lte' => now()->format('d-m-Y'),
                            'gte' => '01-01-' . $year,
                            'lte' => '31-12-' . $year,
                            'format' => 'dd-MM-yyyy||yyyy',
                        ]
                    ]
                ];
            }

            if (!empty($transactionTypeIds)) {
                $array['bool']['must'][] = [
                    'terms' => [
                        //'transaction_type_id' => json_decode($transactionTypeIds),
                        'transaction_type' => json_decode($transactionTypeIds),
                    ]
                ];
            }

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
            }
            if (isset($isCheckFrontside)) {
                $isCheckFrontside = filter_var($isCheckFrontside, FILTER_VALIDATE_BOOLEAN);
                if (!$isCheckFrontside) {
                    if (isset($frontSide)) {
                        $array['bool']['must'][] = [
                            'match' => [
                                'front_side' => $frontSide
                            ]
                        ];
                    }
                }
            } else if (isset($frontSide)) {
                $array['bool']['must'][] = [
                    'match' => [
                        //'properties.front_side' => $frontSide
                        'front_side' => $frontSide
                    ]
                ];
            }

            //remove TSC
            if (empty($year)) {
                $array['bool']['must_not'][] = [
                    'match' => [
                        'migrate_status' => "TSC"
                    ]
                ];
            }

            if (!empty($sort)) {
                if ($sort == TRANSACTION_TYPE_SORT) {
                    $sortBy[TRANSACTION_TYPE_SORT . '.keyword'] = ['order' => !empty($order) ? $order : 'asc'];
                } elseif ($sort == ASSET_TYPE_SORT) {
                    $sortBy[ASSET_TYPE_SORT . '.keyword'] = ['order' => !empty($order) ? $order : 'asc'];
                } elseif ($sort == ASSET_CREATE_AT_SORT) {
                    $sortBy[ASSET_CREATE_AT_SORT] = ['order' => !empty($order) ? $order : 'desc'];
                } else {
                    $sortBy[$sort] = ['order' => !empty($order) ? $order : 'asc'];
                }
            } else {
                $sortBy['created_at'] = ['order' => 'desc'];
                $sortBy['updated_at'] = ['order' => 'desc'];
                $sortBy['id'] = ['order' => 'desc'];
            }

            $sourceField = [
                'id',
                'contact_person',
                'contact_phone',
                'public_date',
                'total_amount',
                'total_area',
                'coordinates',
                'full_address',
                'migrate_status',
                'transaction_type_id',
                'transaction_type',
                'transaction_type_description',
                'asset_type',
                'pic',
                'created_at',
                'asset_type_id',
                'total_estimate_amount',
                'updated_at'
            ];

            $result = CompareAssetGeneral::searchByQuery($array, null, $sourceField, 10000, null, $sortBy);
            // dd($result);
            return $result;
        } catch (Exception $exception) {
            Log::error($exception);
            return ['data' => []];
        }
    }

    /**
     * @return Builder[]|Collection
     */
    public function findAll()
    {
        return $this->model->query()
            ->select()
            ->with('createdBy')
            ->with('province')
            ->with('district')
            ->with('ward')
            ->with('street')
            ->with('distance')
            ->with('assetType')
            ->with('source')
            ->with('transactionType')
            ->with('apartment')
            ->with('pic')
            ->with('version')
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
            // ->with('blockSpecification')
            // ->with('blockSpecification.basicUtilities')
            // ->with('blockSpecification.blockLists')
            ->with('roomDetails')
            // ->with('roomDetails.roomFurnitureDetails')
            ->with('roomDetails.direction')
            ->with('roomDetails.furnitureQuality')
            ->with('project')
            ->with('project.province:id,name')
            ->with('project.district:id,name')
            ->with('project.ward:id,name')
            ->with('block')
            ->with('floor')
            ->with('apartmentSpecification')
            ->orderByDesc($this->allowedSorts)
            ->get();
    }

    /**
     * @param $id
     * @return Builder|Model|mixed|object|null
     */
    public function findById($id)
    {
        $version = request()->get('version');
        $result = null;
        if ($version && !is_array($version)) {
            $result = $this->findVersionById($id, $version);
        }
        if (!$result) {
            $result = $this->model->query()
                ->where('id', '=', $id)
                ->with('createdBy')
                ->with('province')
                ->with('district')
                ->with('ward')
                ->with('street')
                ->with('distance')
                ->with('assetType')
                ->with('source')
                ->with('transactionType')
                ->with('apartment')
                ->with('pic')
                ->with('version')
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
                ->with('roomDetails')
                ->with('roomDetails.direction')
                ->with('roomDetails.furnitureQuality')
                ->with('roomDetails.legal')
                ->with('roomDetails.loaicanho')
                ->with('project')
                ->with('project.province:id,name')
                ->with('project.district:id,name')
                ->with('project.ward:id,name')
                ->with('block')
                ->with('block.rank:id,description,acronym')
                ->with('floor')
                ->with('apartmentSpecification')
                ->with('apartmentSpecification.rank:id,description,acronym')
                ->first();
        }
        return $result;
    }

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
            ->with('version')
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
            // ->with('blockSpecification')
            // ->with('blockSpecification.basicUtilities')
            // ->with('blockSpecification.blockLists')
            ->with('roomDetails')
            // ->with('roomDetails.roomFurnitureDetails')
            ->with('roomDetails.direction')
            ->with('roomDetails.furnitureQuality')
            ->with('project')
            ->with('project.province:id,name')
            ->with('project.district:id,name')
            ->with('project.ward:id,name')
            ->with('block')
            ->with('floor')
            ->with('apartmentSpecification')
            ->get();
    }

    private function checkDuplicateCoordinate($coordinates, $id = null)
    {
        if (empty($coordinates)) {
            return ['message' => 'Chưa có tọa đồ. Vui lòng kiểm tra lại.', 'exception' => ''];
        }
        $check = null;
        $where = [
            'coordinates' => $coordinates
        ];
        if (empty($id)) {
            if ($this->model->query()->where($where)->exists()) {
                $list = $this->model->query()->where($where)->get('id');
                $ids = Arr::pluck($list, 'id');
                $check = ['message' => 'Tọa độ ' . $coordinates . ' đã tồn tại ở TSSS (' . implode(', ', $ids) . '). Vui lòng chọn tọa độ khác.', 'exception' => ''];
            }
        } else {
            if ($this->model->query()->where($where)->whereKeyNot($id)->exists()) {
                $list = $this->model->query()->where($where)->whereKeyNot($id)->get('id');
                $ids = Arr::pluck($list, 'id');
                $check = ['message' => 'Tọa độ ' . $coordinates . ' đã tồn tại ở TSSS (' . implode(', ', $ids) . '). Vui lòng chọn tọa độ khác.', 'exception' => ''];
            }
        }

        return $check;
    }
    /**
     * @param array $objects
     * @return mixed
     * @throws Throwable
     */
    public function createCompareAssetGeneral(array $objects)
    {
        $assetTypeId = $objects['asset_type_id'];
        $assetType = Dictionary::query()->whereIn('acronym', ['DCN', 'DT'])->where('id', $assetTypeId)->first();
        if (!empty($assetType)) {
            $coordinates = $objects['coordinates'] ?? null;
            $check = $this->checkDuplicateCoordinate($coordinates);
            if (!empty($check)) {
                return $check;
            }
        }
        return DB::transaction(function () use ($objects) {
            try {
                $general = new CompareAssetGeneral($objects);
                $generalId = QueryBuilder::for(CompareAssetGeneral::class)
                    ->insertGetId($general->attributesToArray());

                if (isset($objects['pic'])) {
                    foreach ($objects['pic'] as $generalPic) {
                        $generalPic['asset_general_id'] = $generalId;
                        $pic = new CompareGeneralPic($generalPic);
                        QueryBuilder::for($pic)
                            ->insert($pic->attributesToArray());
                    }
                }


                $version['version'] = 1;
                $version['asset_general_id'] = $generalId;
                CompareAssetVersion::query()->insert($version);

                if (isset($objects['properties'])) {
                    foreach ($objects['properties'] as $propertyData) {
                        $propertyData['asset_general_id'] = $generalId;

                        $property = new CompareProperty($propertyData);
                        $propertyId = QueryBuilder::for($property)
                            ->insertGetId($property->attributesToArray());
                        if (isset($propertyData['property_detail'])) {
                            foreach ($propertyData['property_detail'] as $propertyDetail) {
                                $propertyDetail['compare_property_id'] = $propertyId;
                                $detail = new ComparePropertyDetail($propertyDetail);
                                QueryBuilder::for($detail)
                                    ->insert($detail->attributesToArray());
                            }
                        }

                        if (isset($propertyData['compare_property_turning_time'])) {
                            foreach ($propertyData['compare_property_turning_time'] as $propertyTurningTime) {
                                $propertyTurningTime['compare_property_id'] = $propertyId;
                                $detail = new ComparePropertyTurningTime($propertyTurningTime);
                                QueryBuilder::for($detail)
                                    ->insert($detail->attributesToArray());
                            }
                        }

                        if (isset($propertyData['compare_property_doc'])) {
                            foreach ($propertyData['compare_property_doc'] as $propertyPropertyDoc) {
                                $propertyPropertyDoc['compare_property_id'] = $propertyId;
                                $detail = new ComparePropertyDoc($propertyPropertyDoc);
                                QueryBuilder::for($detail)
                                    ->insert($detail->attributesToArray());
                            }
                        }
                        if (isset($propertyData['pic'])) {
                            foreach ($propertyData['pic'] as $propertyPic) {
                                $propertyPic['compare_property_id'] = $propertyId;
                                $pic = new ComparePropertyPic($propertyPic);
                                QueryBuilder::for($pic)
                                    ->insert($pic->attributesToArray());
                            }
                        }
                    }
                }

                if (isset($objects['tangible_assets'])) {
                    foreach ($objects['tangible_assets'] as $tangibleAssetData) {
                        $tangibleAssetData['asset_general_id'] = $generalId;
                        $tangibleAsset = new CompareTangibleAsset($tangibleAssetData);
                        $tangibleId = QueryBuilder::for($tangibleAsset)
                            ->insertGetId($tangibleAsset->attributesToArray());
                        if (isset($tangibleAssetData['pic'])) {
                            foreach ($tangibleAssetData['pic'] as $tangiblePic) {
                                $tangiblePic['compare_tangible_id'] = $tangibleId;
                                $pic = new CompareTangiblePic($tangiblePic);
                                QueryBuilder::for($pic)
                                    ->insert($pic->attributesToArray());
                            }
                        }
                    }
                }

                if (isset($objects['other_assets'])) {
                    foreach ($objects['other_assets'] as $otherAssetData) {
                        $otherAssetData['asset_general_id'] = $generalId;
                        $otherAsset = new CompareOtherAsset($otherAssetData);
                        $otherId = QueryBuilder::for($otherAsset)
                            ->insertGetId($otherAsset->attributesToArray());
                        if (isset($otherAssetData['pic'])) {
                            foreach ($otherAssetData['pic'] as $otherPic) {
                                $otherPic['asset_other_id'] = $otherId;
                                $pic = new CompareOtherPic($otherPic);
                                QueryBuilder::for($pic)
                                    ->insert($pic->attributesToArray());
                            }
                        }
                    }
                }
                $assetType = Dictionary::query()->where('id', $objects['asset_type_id'])->first('acronym');
                if (isset($assetType->acronym) && $assetType->acronym == 'CC') {
                    // if (isset($objects['block_specification'])) {
                    //     foreach ($objects['block_specification'] as $blockSpecification) {
                    //         $blockSpecification['asset_general_id'] = $generalId;
                    // $blockSpecificationObject = new BlockSpecification($blockSpecification);
                    //         $blockSpecificationId = QueryBuilder::for($blockSpecificationObject)
                    //             ->insertGetId($blockSpecificationObject->attributesToArray());
                    //         if (isset($blockSpecification['basic_utilities'])) {
                    //             $basicUtilities = [];
                    //             foreach ($blockSpecification['basic_utilities'] as $basicUtility) {
                    //                 $basicUtility['block_specification_id'] = $blockSpecificationId;
                    //                 $basicUtilityObject = new BlockSpecificationHasBasicUtility($basicUtility);
                    //                 array_push($basicUtilities, $basicUtilityObject->attributesToArray());
                    //             }
                    //             BlockSpecificationHasBasicUtility::query()->insert($basicUtilities);
                    //         }
                    //     }
                    // }
                    if (isset($objects['room_details'])) {
                        foreach ($objects['room_details'] as $roomDetail) {
                            $roomDetail['asset_general_id'] = $generalId;
                            $roomDetailObject = new RoomDetail($roomDetail);
                            $roomDetailId = QueryBuilder::for($roomDetailObject)
                                ->insertGetId($roomDetailObject->attributesToArray());
                            // if (isset($roomDetail['room_furniture_details'])) {
                            //     $roomFurnitureDetails = [];
                            //     foreach ($roomDetail['room_furniture_details'] as $roomFurnitureDetail) {
                            //         $roomFurnitureDetail['room_detail_id'] = $roomDetailId;
                            //         $roomFurnitureDetailObject = new RoomFurnitureDetail($roomFurnitureDetail);
                            //         array_push($roomFurnitureDetails, $roomFurnitureDetailObject->attributesToArray());
                            //     }
                            //     RoomFurnitureDetail::query()->insert($roomFurnitureDetails);
                            // }
                        }
                    }
                    if (isset($objects['apartment_specification'])) {
                        $block = Block::query()->where('id', $objects['block_id'])->first();
                        $apartmentSpecification = $block;
                        $apartmentSpecification['asset_general_id'] = $generalId;
                        $apartmentSpecification['utilities'] = $objects['apartment_specification']['utilities'] ?? $block->utilities;
                        $apartmentSpecification['handover_year'] = $objects['apartment_specification']['handover_year'] ?? $block->handover_year;
                        $apartmentSpecification['apartment_name'] = $objects['apartment_specification']['apartment_name'] ?? '';
                        $apartmentSpecificationArr = new ApartmentSpecification($apartmentSpecification->toArray());
                        ApartmentSpecification::query()->create($apartmentSpecificationArr->attributesToArray());
                    }
                }
                $rows = $this->findById($generalId);
                $this->indexData($rows);
                $user = CommonService::getUser();
                $data_log['updated_by'] = $user->name;
                $data_log['updated_at'] = now();
                $edited = CompareAssetGeneral::query()->where('id', $generalId)->first();
                $this->CreateActivityLog($edited, $data_log, 'taomoi_TSSS', 'Tạo mới tài sản so sánh');
                return $generalId;
            } catch (Exception $exception) {
                Log::error($exception);
                throw $exception;
            }
        });
    }

    /**
     * @param $id
     * @param array $objects
     * @return int
     * @throws Throwable
     */
    public function updateCompareAssetGeneral($id, array $objects)
    {
        $assetTypeId = $objects['asset_type_id'];
        $assetType = Dictionary::query()->whereIn('acronym', ['DCN', 'DT'])->where('id', $assetTypeId)->first();
        if (!empty($assetType)) {
            $coordinates = $objects['coordinates'] ?? null;
            $check = $this->checkDuplicateCoordinate($coordinates, $id);
            if (!empty($check)) {
                return $check;
            }
        }
        return DB::transaction(function () use ($id, $objects) {
            try {
                unset($objects['created_by']);
                $general = new CompareAssetGeneral($objects);
                $general->newQuery()->updateOrInsert(['id' => $id], $general->attributesToArray());
                CompareGeneralPic::query()->where('asset_general_id', '=', $id)->delete();
                if (isset($objects['pic'])) {
                    $pic = [];
                    foreach ($objects['pic'] as $generalPic) {
                        $generalPic['asset_general_id'] = $id;
                        $pic[] = (new CompareGeneralPic($generalPic))->attributesToArray();
                    }
                    CompareGeneralPic::query()->insert($pic);
                }

                $countVersion = CompareAssetVersion::query()->where('asset_general_id', '=', $id)->count();
                $version['version'] = $countVersion + 1;
                $version['asset_general_id'] = $id;
                CompareAssetVersion::query()->insert($version);

                CompareProperty::query()->where('asset_general_id', '=', $id)->delete();
                if (isset($objects['properties'])) {
                    foreach ($objects['properties'] as $propertyData) {
                        $propertyData['asset_general_id'] = $id;
                        $property = new CompareProperty($propertyData);
                        $propertyId = QueryBuilder::for($property)
                            ->insertGetId($property->attributesToArray());
                        if (isset($propertyData['property_detail'])) {
                            foreach ($propertyData['property_detail'] as $propertyDetail) {
                                $propertyDetail['compare_property_id'] = $propertyId;
                                $detail = new ComparePropertyDetail($propertyDetail);
                                QueryBuilder::for($detail)
                                    ->insert($detail->attributesToArray());
                            }
                        }

                        if (isset($propertyData['compare_property_turning_time'])) {
                            foreach ($propertyData['compare_property_turning_time'] as $propertyTurningTime) {
                                $propertyTurningTime['compare_property_id'] = $propertyId;
                                $detail = new ComparePropertyTurningTime($propertyTurningTime);
                                QueryBuilder::for($detail)
                                    ->insert($detail->attributesToArray());
                            }
                        }

                        if (isset($propertyData['compare_property_doc'])) {
                            foreach ($propertyData['compare_property_doc'] as $propertyPropertyDoc) {
                                $propertyPropertyDoc['compare_property_id'] = $propertyId;
                                $detail = new ComparePropertyDoc($propertyPropertyDoc);
                                QueryBuilder::for($detail)
                                    ->insert($detail->attributesToArray());
                            }
                        }

                        if (isset($propertyData['pic'])) {
                            foreach ($propertyData['pic'] as $propertyPic) {
                                $propertyPic['compare_property_id'] = $propertyId;
                                $pic = new ComparePropertyPic($propertyPic);
                                QueryBuilder::for($pic)
                                    ->insert($pic->attributesToArray());
                            }
                        }
                    }
                }

                CompareTangibleAsset::query()->where('asset_general_id', '=', $id)->delete();
                if (isset($objects['tangible_assets'])) {
                    foreach ($objects['tangible_assets'] as $tangibleAssetData) {
                        $tangibleAssetData['asset_general_id'] = $id;
                        $tangibleAsset = new CompareTangibleAsset($tangibleAssetData);
                        $tangibleId = QueryBuilder::for($tangibleAsset)
                            ->insertGetId($tangibleAsset->attributesToArray());
                        if (isset($tangibleAssetData['pic'])) {
                            foreach ($tangibleAssetData['pic'] as $tangiblePic) {
                                $tangiblePic['compare_tangible_id'] = $tangibleId;
                                $pic = new CompareTangiblePic($tangiblePic);
                                QueryBuilder::for($pic)
                                    ->insert($pic->attributesToArray());
                            }
                        }
                    }
                }

                CompareOtherAsset::query()->where('asset_general_id', '=', $id)->delete();
                if (isset($objects['other_assets'])) {
                    foreach ($objects['other_assets'] as $otherAssetData) {
                        $otherAssetData['asset_general_id'] = $id;
                        $otherAsset = new CompareOtherAsset($otherAssetData);
                        $otherId = QueryBuilder::for($otherAsset)
                            ->insertGetId($otherAsset->attributesToArray());
                        if (isset($otherAssetData['pic'])) {
                            foreach ($otherAssetData['pic'] as $otherPic) {
                                $otherPic['asset_other_id'] = $otherId;
                                $pic = new CompareOtherPic($otherPic);
                                QueryBuilder::for($pic)
                                    ->insert($pic->attributesToArray());
                            }
                        }
                    }
                }
                $assetType = Dictionary::query()->where('id', $objects['asset_type_id'])->first('acronym');
                if (isset($assetType->acronym) && $assetType->acronym == 'CC') {

                    // BlockSpecification::query()->where('asset_general_id', '=', $id)->delete();
                    // if (isset($objects['block_specification'])) {
                    //     foreach ($objects['block_specification'] as $blockSpecification) {
                    //         $blockSpecification['asset_general_id'] = $id;
                    //         $blockSpecificationObject = new BlockSpecification($blockSpecification);
                    //         $blockSpecificationId = QueryBuilder::for($blockSpecificationObject)
                    //             ->insertGetId($blockSpecificationObject->attributesToArray());
                    //         if (isset($blockSpecification['basic_utilities'])) {
                    //             $basicUtilities = [];
                    //             foreach ($blockSpecification['basic_utilities'] as $basicUtility) {
                    //                 $basicUtility['block_specification_id'] = $blockSpecificationId;
                    //                 $basicUtilityObject = new BlockSpecificationHasBasicUtility($basicUtility);
                    //                 array_push($basicUtilities, $basicUtilityObject->attributesToArray());
                    //             }
                    //             BlockSpecificationHasBasicUtility::query()->insert($basicUtilities);
                    //         }
                    //     }
                    // }

                    RoomDetail::query()->where('asset_general_id', '=', $id)->delete();
                    if (isset($objects['room_details'])) {
                        foreach ($objects['room_details'] as $roomDetail) {
                            $roomDetail['asset_general_id'] = $id;
                            $roomDetailObject = new RoomDetail($roomDetail);
                            $roomDetailId = QueryBuilder::for($roomDetailObject)
                                ->insertGetId($roomDetailObject->attributesToArray());
                            // if (isset($roomDetail['room_furniture_details'])) {
                            //     $roomFurnitureDetails = [];
                            //     foreach ($roomDetail['room_furniture_details'] as $roomFurnitureDetail) {
                            //         $roomFurnitureDetail['room_detail_id'] = $roomDetailId;
                            //         $roomFurnitureDetailObject = new RoomFurnitureDetail($roomFurnitureDetail);
                            //         array_push($roomFurnitureDetails, $roomFurnitureDetailObject->attributesToArray());
                            //     }
                            //     RoomFurnitureDetail::query()->insert($roomFurnitureDetails);
                            // }
                        }
                    }

                    ApartmentSpecification::query()->where('asset_general_id', '=', $id)->delete();
                    if (isset($objects['apartment_specification'])) {
                        $block = Block::query()->where('id', $objects['block_id'])->first();
                        $apartmentSpecification = $block;
                        $apartmentSpecification['asset_general_id'] = $id;
                        $apartmentSpecification['utilities'] = $objects['apartment_specification']['utilities'] ?? $block->utilities;
                        $apartmentSpecification['handover_year'] = $objects['apartment_specification']['handover_year'] ?? $block->handover_year;
                        $apartmentSpecification['apartment_name'] = $objects['apartment_specification']['apartment_name'] ?? '';
                        $apartmentSpecificationArr = new ApartmentSpecification($apartmentSpecification->toArray());
                        ApartmentSpecification::query()->create($apartmentSpecificationArr->attributesToArray());
                    }
                }
                $rows = $this->findById($id);
                $this->indexData($rows);
                // $user = CommonService::getUser();
                // $data_log['updated_by'] = $user->name;
                // $data_log['updated_at'] = now()->format('dd-mm-YYYY');
                // $this->CreateActivityLog($data_log, $data_log, 'capnhat_TSSS', 'Cập nhật tài sản so sánh');
                return $id;
            } catch (Exception $exception) {
                throw $exception;
            }
        });
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteCompareAssetGeneral($id)
    {
        $client = ClientBuilder::create()
            ->setSSLVerification(false)
            ->setHosts(config('elasticquent.config.hosts'))
            ->build();
        $requestRemove = [
            'index' => config('elasticquent.default_index'),
            'type' => '_doc',
            'id' => trim($id),
        ];
        $client->delete($requestRemove);
        return $this->model->query()
            ->where('id', $id)
            ->delete();
    }

    /**
     * @param $data
     * @return mixed
     */
    public function indexData($data)
    {
        $pics = [];
        $landTypePurposePrice = [];
        $landTypePurposePriceSearch = [];
        $frontSide = [];
        $landTypePurpose = [];
        $mainRoadLength = null;
        $bedroom_num = null;
        isset($data->total_area) ? $data->total_area = (float)$data->total_area : null;
        isset($data->total_construction_area) ? $data->total_construction_area = (float)$data->total_construction_area : null;
        isset($data->full_address) ? $data->full_address_search = mb_strtolower($data->full_address) : $data->full_address_search = '';
        isset($data->public_date) ? $data->public_date = Carbon::createFromFormat('d-m-Y', $data->public_date) : $data->public_date = '';
        foreach ($data->pic as $pic) {
            $pics[] = ($pic)->toArray();
        }
        if (isset($data->properties)) {
            foreach ($data->properties as $propertyData) {
                foreach ($propertyData->comparePropertyTurningTime as $comparePropertyTurningTime) {
                    $mainRoadLength = (float)$comparePropertyTurningTime->main_road_length;
                }
                foreach ($propertyData->propertyDetail as $propertyDetail) {
                    $landTypePurpose[] = [
                        'id' => $propertyDetail->land_type_purpose
                    ];
                    if (isset($propertyDetail->positionType)) {
                        $landTypePurposePrice[] = [
                            'id' => $propertyDetail->land_type_purpose,
                            'price_land' => $propertyDetail->price_land,
                            'circular_unit_price' => $propertyDetail->circular_unit_price,
                        ];
                    }

                    if (isset($propertyDetail->positionType) && ($propertyDetail->positionType->description == "VỊ TRÍ 1")) {
                        $landTypePurposePriceSearch[] = [
                            'id' => $propertyDetail->land_type_purpose,
                            'price_land' => $propertyDetail->price_land,
                            'circular_unit_price' => $propertyDetail->circular_unit_price,
                        ];
                    }
                }
                foreach ($propertyData->pic as $pic) {
                    $pics[] = ($pic)->toArray();
                }
                if (isset($propertyData->front_side)) {
                    $frontSide = (float)($propertyData->front_side);
                }
            }
        }

        if (isset($data->otherAssets)) {
            foreach ($data->otherAssets as $otherAssets) {
                foreach ($otherAssets->pic as $pic) {
                    $pics[] = ($pic)->toArray();
                }
            }
        }
        if (isset($data->tangibleAssets)) {
            foreach ($data->tangibleAssets as $tangibleAssets) {
                foreach ($tangibleAssets->pic as $pic) {
                    $pics[] = ($pic)->toArray();
                }
            }
        }

        if (isset($data->roomDetails)) {
            foreach ($data->roomDetails as $roomDetail) {
                $bedroom_num = $roomDetail->bedroom_num;
            }
        }
        $assetVersion = 1;
        if (isset($data->version)) {
            foreach ($data->version as $version) {
                $assetVersion = $assetVersion > $version->version ? $assetVersion : $version->version;
                if (isset($version->asset_general_data))
                    unset($version->asset_general_data);
            }
        }
        $location = explode(',', $data->coordinates);
        $item = [
            'id' => $data->id,
            'input_source' => $data->input_source,
            'contact_person' => $data->contact_person,
            'contact_phone' => $data->contact_phone,
            'land_no' => $data->land_no,
            'doc_no' => $data->doc_no,
            'status' => $data->status,
            'front_side' => $frontSide,
            'land_type_purpose' => $landTypePurpose,
            'main_road_length' => $mainRoadLength,
            'transaction_type_id' => $data->transaction_type_id,
            'source_id' => $data->source_id,
            'province_id' => $data->province_id,
            'province' => $data->province->name ?? null,
            'district_id' => $data->district_id,
            'district' => $data->district->name ?? null,
            'ward_id' => $data->ward_id,
            'ward' => $data->ward->name ?? null,
            'street_id' => $data->street->id ?? null,
            'street' => $data->street->name ?? null,
            'distance_id' => $data->distance_id,
            'distance' => $data->distance->name ?? null,
            'full_address' => $data->full_address,
            'coordinates' => $data->coordinates,
            'total_area' => $data->total_area,
            'total_amount' => $data->total_amount,
            'total_raw_amount' => $data->total_raw_amount,
            'total_estimate_amount' => $data->total_estimate_amount,
            'total_construction_area' => $data->total_construction_area,
            'total_construction_amount' => $data->total_construction_amount,
            'average_land_unit_price' => $data->average_land_unit_price,
            'full_address_search' => $data->full_address_search,
            'public_date' => $data->public_date != '' ? $data->public_date : null,
            'insight_width' => $data->insight_width,
            'asset_type' => $data->assetType->description ?? null,
            'asset_type_id' => $data->assetType->id ?? null,
            'transaction_type' => $data->transactionType->id ?? null,
            'transaction_type_description' => $data->transactionType->description ?? null,
            'created_by' => $data->createdBy->name ?? null,
            'created_at' => $data->created_at,
            'updated_at' => $data->updated_at,
            'migrate_status' => $data->migrate_status,
            'pic' => $pics,
            'land_type_purpose_price' => $landTypePurposePrice,
            'land_type_purpose_price_search' => $landTypePurposePriceSearch,
            'bedroom_num' => $bedroom_num,
            'apartment_id' => $data->apartment_id,
            'version' => $assetVersion ?? 1,
            'room_details' => $data->roomDetails,
            'project' => $data->project,
            'block' => $data->block,
            'floor' => $data->floor,
            'apartment_specification' => $data->apartmentSpecification,
            'pin' => [
                'location' => [
                    'lat' => ((float)$location[0] && ((float)$location[0]) < 90) ? (float)$location[0] : 0,
                    'lon' => (float)($location[1] ?? 0),
                ],
            ],

        ];

        $client = ClientBuilder::create()
            ->setSSLVerification(false)
            ->setHosts(config('elasticquent.config.hosts'))
            ->build();
        $request = [
            'index' => config('elasticquent.default_index'),
            'type' => '_doc',
            'id' => trim($data->id),
            'body' => json_encode($item, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        ];
        $client->index($request);

        $request = [
            'index' => env('ELASTIC_ASSET_VERSION_INDEX'),
            'type' => '_doc',
            'id' => trim($data->id) . '-' . ($assetVersion ?? 1),
            'body' => json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        ];
        $client->index($request);
        return $data;
    }

    /**
     * @param $ids
     * @param $objects
     * @return bool
     */
    public function updateStatusCompareAssetGeneral($ids, $objects): bool
    {
        foreach (json_decode($ids) as $id) {
            $general = new CompareAssetGeneral($objects);
            $general->newQuery()->updateOrInsert(['id' => $id], $general->attributesToArray());
            $rows = $this->findById($id);
            $data = $this->indexData($rows);
        }
        sleep(1);
        return true;
    }

    /**
     * @throws Throwable
     */
    public function saveCompareAssetGeneral($objects)
    {
        return DB::transaction(function () use ($objects) {
            try {
                $general = new CompareAssetGeneral($objects);
                $generalId = QueryBuilder::for(CompareAssetGeneral::class)
                    ->insertGetId($general->attributesToArray());

                if (isset($objects['pic'])) {
                    foreach ($objects['pic'] as $generalPic) {
                        $generalPic['asset_general_id'] = $generalId;
                        $pic = new CompareGeneralPic($generalPic);
                        QueryBuilder::for($pic)
                            ->insert($pic->attributesToArray());
                    }
                }
                if (isset($objects['properties'])) {
                    foreach ($objects['properties'] as $propertyData) {
                        $propertyData['asset_general_id'] = $generalId;
                        $property = new CompareProperty($propertyData);
                        $propertyId = QueryBuilder::for($property)
                            ->insertGetId($property->attributesToArray());
                        if (isset($propertyData['property_detail'])) {
                            foreach ($propertyData['property_detail'] as $propertyDetail) {
                                $propertyDetail['compare_property_id'] = $propertyId;
                                $detail = new ComparePropertyDetail($propertyDetail);
                                QueryBuilder::for($detail)
                                    ->insert($detail->attributesToArray());
                            }
                        }

                        if (isset($propertyData['compare_property_turning_time'])) {
                            foreach ($propertyData['compare_property_turning_time'] as $propertyTurningTime) {
                                $propertyTurningTime['compare_property_id'] = $propertyId;
                                $detail = new ComparePropertyTurningTime($propertyTurningTime);
                                QueryBuilder::for($detail)
                                    ->insert($detail->attributesToArray());
                            }
                        }
                        if (isset($propertyData['compare_property_doc'])) {
                            foreach ($propertyData['compare_property_doc'] as $propertyPropertyDoc) {
                                $propertyPropertyDoc['compare_property_id'] = $propertyId;
                                $detail = new ComparePropertyDoc($propertyPropertyDoc);
                                QueryBuilder::for($detail)
                                    ->insert($detail->attributesToArray());
                            }
                        }
                        if (isset($propertyData['pic'])) {
                            foreach ($propertyData['pic'] as $propertyPic) {
                                $propertyPic['compare_property_id'] = $propertyId;
                                $pic = new ComparePropertyPic($propertyPic);
                                QueryBuilder::for($pic)
                                    ->insert($pic->attributesToArray());
                            }
                        }
                    }
                }
                if (isset($objects['tangible_assets'])) {
                    foreach ($objects['tangible_assets'] as $tangibleAssetData) {
                        $tangibleAssetData['asset_general_id'] = $generalId;
                        $tangibleAsset = new CompareTangibleAsset($tangibleAssetData);
                        $tangibleId = QueryBuilder::for($tangibleAsset)
                            ->insertGetId($tangibleAsset->attributesToArray());
                        if (isset($tangibleAssetData['pic'])) {
                            foreach ($tangibleAssetData['pic'] as $tangiblePic) {
                                $tangiblePic['compare_tangible_id'] = $tangibleId;
                                $pic = new CompareTangiblePic($tangiblePic);
                                QueryBuilder::for($pic)
                                    ->insert($pic->attributesToArray());
                            }
                        }
                    }
                }
                if (isset($objects['other_assets'])) {
                    foreach ($objects['other_assets'] as $otherAssetData) {
                        $otherAssetData['asset_general_id'] = $generalId;
                        $otherAsset = new CompareOtherAsset($otherAssetData);
                        $otherId = QueryBuilder::for($otherAsset)
                            ->insertGetId($otherAsset->attributesToArray());
                        if (isset($otherAssetData['pic'])) {
                            foreach ($otherAssetData['pic'] as $otherPic) {
                                $otherPic['asset_other_id'] = $otherId;
                                $pic = new CompareOtherPic($otherPic);
                                QueryBuilder::for($pic)
                                    ->insert($pic->attributesToArray());
                            }
                        }
                    }
                }
                if (isset($objects['block_specification'])) {
                    foreach ($objects['block_specification'] as $blockSpecification) {
                        $blockSpecification['asset_general_id'] = $generalId;
                        $blockSpecificationObject = new BlockSpecification($blockSpecification);
                        $blockSpecificationId = QueryBuilder::for($blockSpecificationObject)
                            ->insertGetId($blockSpecificationObject->attributesToArray());
                        if (isset($blockSpecification['basic_utilities'])) {
                            $basicUtilities = [];
                            foreach ($blockSpecification['basic_utilities'] as $basicUtility) {
                                $basicUtility['block_specification_id'] = $blockSpecificationId;
                                $basicUtilityObject = new BlockSpecificationHasBasicUtility($basicUtility);
                                array_push($basicUtilities, $basicUtilityObject->attributesToArray());
                            }
                            BlockSpecificationHasBasicUtility::query()->insert($basicUtilities);
                        }
                    }
                }
                if (isset($objects['room_details'])) {
                    foreach ($objects['room_details'] as $roomDetail) {
                        $roomDetail['asset_general_id'] = $generalId;
                        $roomDetailObject = new RoomDetail($roomDetail);
                        $roomDetailId = QueryBuilder::for($roomDetailObject)
                            ->insertGetId($roomDetailObject->attributesToArray());
                        if (isset($roomDetail['room_furniture_details'])) {
                            $roomFurnitureDetails = [];
                            foreach ($roomDetail['room_furniture_details'] as $roomFurnitureDetail) {
                                $roomFurnitureDetail['room_detail_id'] = $roomDetailId;
                                $roomFurnitureDetailObject = new RoomFurnitureDetail($roomFurnitureDetail);
                                array_push($roomFurnitureDetails, $roomFurnitureDetailObject->attributesToArray());
                            }
                            RoomFurnitureDetail::query()->insert($roomFurnitureDetails);
                        }
                    }
                }
                return $generalId;
            } catch (Exception $exception) {
                Log::error($exception);
                throw $exception;
            }
        });
    }

    /**
     * @param $perPage
     * @param $page
     * @return Builder[]|Collection
     */

    public function findDataPaging($perPage, $page)
    {
        return $this->model->query()
            ->select()
            ->with('createdBy')
            ->with('province')
            ->with('district')
            ->with('ward')
            ->with('street')
            ->with('distance')
            ->with('assetType')
            ->with('source')
            ->with('transactionType')
            ->with('apartment')
            ->with('pic')
            ->with('version')
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
            // ->with('blockSpecification')
            // ->with('blockSpecification.basicUtilities')
            // ->with('blockSpecification.blockLists')
            ->with('roomDetails')
            // ->with('roomDetails.roomFurnitureDetails')
            ->with('roomDetails.direction')
            ->with('roomDetails.furnitureQuality')
            ->with('project')
            ->with('project.province:id,name')
            ->with('project.district:id,name')
            ->with('project.ward:id,name')
            ->with('block')
            ->with('floor')
            ->with('apartmentSpecification')
            ->forPage($page, $perPage)
            ->orderBy('id')
            ->get();
    }

    /**
     * @return int
     */
    public function totalRecord(): int
    {
        return $this->model->query()
            ->select()
            ->count();
    }

    /**
     * @return array
     */
    public function createIndex(): array
    {
        $client = ClientBuilder::create()
            ->setSSLVerification(false)
            ->setHosts(config('elasticquent.config.hosts'))
            ->build();

        $params = [
            'index' => config('elasticquent.default_index'),
            'body' => [
                'mappings' => [
                    "properties" => [
                        "asset_type" => [
                            "type" => "text",
                            "fields" => [
                                "keyword" => [
                                    "type" => "keyword",
                                    "ignore_above" => 256
                                ]
                            ]
                        ],
                        "asset_type_id" => [
                            "type" => "long"
                        ],
                        "front_side" => [
                            "type" => "long"
                        ],
                        "main_road_length" => [
                            "type" => "float"
                        ],
                        "land_type_purpose" => [
                            "properties" => [
                                "id" => [
                                    "type" => "long"
                                ]
                            ]
                        ],
                        "transaction_type_id" => [
                            "type" => "long"
                        ],
                        "average_land_unit_price" => [
                            "type" => "long"
                        ],
                        "contact_person" => [
                            "type" => "text",
                            "fields" => [
                                "keyword" => [
                                    "type" => "keyword",
                                    "ignore_above" => 256
                                ]
                            ]
                        ],
                        "contact_phone" => [
                            "type" => "text",
                            "fields" => [
                                "keyword" => [
                                    "type" => "keyword",
                                    "ignore_above" => 256
                                ]
                            ]
                        ],
                        "coordinates" => [
                            "type" => "text",
                            "fields" => [
                                "keyword" => [
                                    "type" => "keyword",
                                    "ignore_above" => 256
                                ]
                            ]
                        ],
                        "created_at" => [
                            "type" => "date"
                        ],
                        "created_by" => [
                            "type" => "text",
                            "fields" => [
                                "keyword" => [
                                    "type" => "keyword",
                                    "ignore_above" => 256
                                ]
                            ]
                        ],
                        "district_id" => [
                            "type" => "long"
                        ],
                        "doc_no" => [
                            "type" => "text",
                            "fields" => [
                                "keyword" => [
                                    "type" => "keyword",
                                    "ignore_above" => 256
                                ]
                            ]
                        ],
                        "full_address" => [
                            "type" => "text",
                            "fields" => [
                                "keyword" => [
                                    "type" => "keyword",
                                    "ignore_above" => 256
                                ]
                            ]
                        ],
                        "full_address_search" => [
                            "type" => "text",
                            "fields" => [
                                "keyword" => [
                                    "type" => "keyword",
                                    "ignore_above" => 256
                                ]
                            ]
                        ],
                        "id" => [
                            "type" => "long"
                        ],
                        "input_source" => [
                            "type" => "text",
                            "fields" => [
                                "keyword" => [
                                    "type" => "keyword",
                                    "ignore_above" => 256
                                ]
                            ]
                        ],
                        "land_no" => [
                            "type" => "text",
                            "fields" => [
                                "keyword" => [
                                    "type" => "keyword",
                                    "ignore_above" => 256
                                ]
                            ]
                        ],
                        "migrate_status" => [
                            "type" => "text",
                            "fields" => [
                                "keyword" => [
                                    "type" => "keyword",
                                    "ignore_above" => 256
                                ]
                            ]
                        ],
                        "pic" => [
                            "properties" => [
                                "link" => [
                                    "type" => "text",
                                    "fields" => [
                                        "keyword" => [
                                            "type" => "keyword",
                                            "ignore_above" => 256
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        "land_type_purpose_price" => [
                            "properties" => [
                                "id" => [
                                    "type" => "long"
                                ],
                                "price_land" => [
                                    "type" => "long"
                                ],
                                "circular_unit_price" => [
                                    "type" => "long"
                                ]
                            ]
                        ],
                        "land_type_purpose_price_search" => [
                            "properties" => [
                                "id" => [
                                    "type" => "long"
                                ],
                                "price_land" => [
                                    "type" => "long"
                                ],
                                "circular_unit_price" => [
                                    "type" => "long"
                                ]
                            ]
                        ],
                        "pin" => [
                            "properties" => [
                                "location" => [
                                    "type" => "geo_point"
                                ]
                            ]
                        ],
                        "province_id" => [
                            "type" => "long"
                        ],
                        "public_date" => [
                            "type" => "date"
                        ],
                        "source_id" => [
                            "type" => "long"
                        ],
                        "status" => [
                            "type" => "long"
                        ],
                        "street_id" => [
                            "type" => "long"
                        ],
                        "total_amount" => [
                            "type" => "long"
                        ],
                        "total_area" => [
                            "type" => "long"
                        ],
                        "total_construction_amount" => [
                            "type" => "long"
                        ],
                        "total_construction_area" => [
                            "type" => "long"
                        ],
                        "total_estimate_amount" => [
                            "type" => "long"
                        ],
                        "total_raw_amount" => [
                            "type" => "long"
                        ],
                        "transaction_type" => [
                            "type" => "long"
                        ],
                        "bedroom_num" => [
                            "type" => "long"
                        ],
                        "apartment_id" => [
                            "type" => "long"
                        ],
                        "transaction_type_description" => [
                            "type" => "text",
                            "fields" => [
                                "keyword" => [
                                    "type" => "keyword",
                                    "ignore_above" => 256
                                ]
                            ]
                        ],
                        "updated_at" => [
                            "type" => "date"
                        ],
                        "ward_id" => [
                            "type" => "long"
                        ],
                        "project_id" => [
                            "type" => "long"
                        ],
                        "block_id" => [
                            "type" => "long"
                        ],
                        "floor_id" => [
                            "type" => "long"
                        ]
                    ]
                ]
            ]
        ];
        return $client->indices()->create($params);
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
                $search['land_type_purpose'] = [$value['land_type_purpose']];
                $assets = $this->findAssetEstimate($search);
                $ids = [];

                foreach ($assets as $asset) {
                    $ids[] = $asset->id;
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
                    $averageLandUnitPrice = (int)($unitPrice->vt1 ?? 0);
                    $result['recognized'][] = [
                        'land_type_purpose' => $value['land_type_purpose'],
                        'area' => $value['area'],
                        'average_land_unit_price' => $averageLandUnitPrice,
                        'estimate_price' => (int)($averageLandUnitPrice * $value['area']),
                    ];
                    $result['status'] = ValueDefault::STATUS_SUCCESS;
                    $result['error_message'] = null;
                } else {
                    $result['recognized'][] = [
                        'land_type_purpose' => $value['land_type_purpose'],
                        'area' => $value['area'],
                        'average_land_unit_price' => 0,
                        'estimate_price' => 0,
                    ];
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
        $search = [
            'location' => $objects['location'] ?? null,
            'distance' => $objects['distance'] ?? null,
            'front_side' => $objects['front_side'] ?? null,
            'asset_types' => EstimateAssetDefault::LAND_ASSET_TYPE,
            'transaction_type' => EstimateAssetDefault::TRANSACTION_TYPE,
            'land_type_purpose' => null,
            'province_id' => $objects['province_id'] ?? null,
            'district_id' => $objects['district_id'] ?? null,
            'street_id' => $objects['street_id'] ?? null,
            'not_street_id' => null,
            'main_road_length' => null,
            'main_road_length_to' => null,
            'land_type_purpose_id' => null,
            'circular_unit_price' => null,
        ];

        $result = [];
        $landTypePurpose = [];
        $totalCache = [];
        $returnAsset = [];
        if ($objects['unrecognized']) {
            foreach ($objects['unrecognized'] as $value) {
                $landTypePurpose[] = $value['land_type_purpose'];
            }
            $search['land_type_purpose'] = $landTypePurpose;
            $assets = $this->findAssetEstimate($search);
            $ids = [];
            foreach ($assets as $asset) {
                $ids[] = $asset->id;
                $returnAsset[] = $asset;
            }
            $data = $this->findByIds(json_encode($ids));
            if (count($data) == 3) {
                foreach ($objects['unrecognized'] as $value) {
                    $totalLandUnitPrice = 0;
                    foreach ($data as $record) {
                        foreach ($record->properties as $property) {
                            foreach ($property->propertyDetail as $propertyDetail) {
                                if ($propertyDetail->land_type_purpose == $value['land_type_purpose']) {
                                    $totalLandUnitPrice += $propertyDetail->price_land ?? 0;
                                }
                            }
                        }
                    }
                    $averageLandUnitPrice = (int)($totalLandUnitPrice / 3);
                    $result['unrecognized'][] = [
                        'land_type_purpose' => $value['land_type_purpose'],
                        'area' => $value['area'],
                        'average_land_unit_price' => $averageLandUnitPrice,
                        'estimate_price' => $averageLandUnitPrice * $value['area'],
                    ];
                }

                $result['reliability'] = ValueDefault::RELIABILITY_HIGHT;
                $result['assets'] = array_values(array_map("unserialize", array_unique(array_map("serialize", $returnAsset))));
                $result['status'] = ValueDefault::STATUS_SUCCESS;
                $result['error_message'] = null;
                return $result;
            } else {
                $search['street_id'] = null;
                $search['land_type_purpose'] = null;
                $search['land_type_purpose_price_search'] = $defaultLandType;
                $caches = $this->findAssetEstimate($search, 3 - count($data));
                foreach ($caches as $cache) {
                    $ids[] = $cache->id;
                    $totalCache[] = $cache;
                    $returnAsset[] = $cache;
                }
                if (count($data) == 0) {
                    $response = $this->estimateUnrecognizedFrontSiteEachLandTypeAsset($objects, $defaultLandType);
                    $result['unrecognized'] = $response['unrecognized'];
                    $result['reliability'] = $response['reliability'];
                    $result['assets'] = $response['assets'];
                    $result['status'] = $response['status'];
                    $result['error_message'] = $response['error_message'];
                } else {
                    $circularUnitPrice = 0;
                    foreach ($caches as $cache) {
                        foreach ($cache['land_type_purpose_price'] as $item) {
                            if ($item['id'] == $defaultLandType) {
                                $circularUnitPrice = $item['circular_unit_price'];
                            }
                        }
                    }
                    $search['street_id'] = null;
                    $search['not_street_id'] = $objects['street_id'] ?? null;
                    $search['land_type_purpose'] = null;
                    $search['land_type_purpose_price_search'] = $defaultLandType;
                    $search['circular_unit_price'] = $circularUnitPrice;
                    $caches = $this->findAssetEstimate($search, 3 - count($data));
                    foreach ($caches as $cache) {
                        $ids[] = $cache->id;
                        $totalCache[] = $cache;
                        $returnAsset[] = $cache;
                    }
                    $data = $this->findByIds(json_encode($ids));
                    if (count($data) == 3) {
                        foreach ($objects['unrecognized'] as $value) {
                            $totalLandUnitPrice = 0;
                            foreach ($data as $record) {
                                foreach ($record->properties as $property) {
                                    foreach ($property->propertyDetail as $propertyDetail) {
                                        if ($propertyDetail->land_type_purpose == $value['land_type_purpose']) {
                                            $totalLandUnitPrice += $propertyDetail->price_land ?? 0;
                                        }
                                    }
                                }
                            }
                            $averageLandUnitPrice = (int)($totalLandUnitPrice / 3);
                            if (count($totalCache) < 2) {
                                $reliability = ValueDefault::RELIABILITY_HIGHT;
                            } else {
                                $reliability = ValueDefault::RELIABILITY_NORMAL;
                            }
                            $result['unrecognized'][] = [
                                'land_type_purpose' => $value['land_type_purpose'],
                                'area' => $value['area'],
                                'average_land_unit_price' => $averageLandUnitPrice,
                                'estimate_price' => $averageLandUnitPrice * $value['area'],
                            ];
                        }


                        $result['reliability'] = $reliability;
                        $result['assets'] = array_values(array_map("unserialize", array_unique(array_map("serialize", $returnAsset))));
                        $result['status'] = ValueDefault::STATUS_SUCCESS;
                        $result['error_message'] = null;
                        return $result;
                    } else {
                        if (count($data) == 0) {
                            $response = $this->estimateUnrecognizedFrontSiteEachLandTypeAsset($objects, $defaultLandType);
                            $result['unrecognized'] = $response['unrecognized'];
                            $result['reliability'] = $response['reliability'];
                            $result['assets'] = $response['assets'];
                            $result['status'] = $response['status'];
                            $result['error_message'] = $response['error_message'];
                            return $result;
                        } else {
                            foreach ($objects['unrecognized'] as $value) {
                                $totalLandUnitPrice = 0;
                                foreach ($data as $record) {
                                    foreach ($record->properties as $property) {
                                        foreach ($property->propertyDetail as $propertyDetail) {
                                            if ($propertyDetail->land_type_purpose == $value['land_type_purpose']) {
                                                $totalLandUnitPrice += $propertyDetail->price_land ?? 0;
                                            }
                                        }
                                    }
                                }
                                $averageLandUnitPrice = (int)($totalLandUnitPrice / count($data));

                                if (count($data) >= 2) {
                                    $reliability = ValueDefault::RELIABILITY_NORMAL;
                                } else {
                                    $reliability = ValueDefault::RELIABILITY_LOW;
                                }

                                $result['unrecognized'][] = [
                                    'land_type_purpose' => $value['land_type_purpose'],
                                    'area' => $value['area'],
                                    'average_land_unit_price' => $averageLandUnitPrice,
                                    'estimate_price' => $averageLandUnitPrice * $value['area'],
                                ];
                            }

                            $result['reliability'] = $reliability;
                            $result['assets'] = array_values(array_map("unserialize", array_unique(array_map("serialize", $returnAsset))));
                            $result['status'] = ValueDefault::STATUS_SUCCESS;
                            $result['error_message'] = null;
                            return $result;
                        }
                    }
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
    public function estimateUnrecognizedFrontSiteEachLandTypeAsset($objects, $defaultLandType): array
    {
        $search = [
            'location' => $objects['location'] ?? null,
            'distance' => $objects['distance'] ?? null,
            'front_side' => $objects['front_side'] ?? null,
            'asset_types' => EstimateAssetDefault::LAND_ASSET_TYPE,
            'transaction_type' => EstimateAssetDefault::TRANSACTION_TYPE,
            'land_type_purpose' => null,
            'province_id' => $objects['province_id'] ?? null,
            'district_id' => $objects['district_id'] ?? null,
            'street_id' => $objects['street_id'] ?? null,
            'not_street_id' => null,
            'main_road_length' => null,
            'main_road_length_to' => null,
            'land_type_purpose_id' => null,
            'circular_unit_price' => null,
        ];

        $result = [];
        $totalCache = [];
        $returnAsset = [];
        $ids = [];
        if ($objects['unrecognized']) {
            foreach ($objects['unrecognized'] as $value) {
                $landTypePurpose = $value['land_type_purpose'];
                $search['land_type_purpose'] = [$landTypePurpose];
                $assets = $this->findAssetEstimate($search);
                foreach ($assets as $asset) {
                    $ids[] = $asset->id;
                    $returnAsset[] = $asset;
                }
            }

            $data = $this->findByIds(json_encode($ids));
            if (count($data) >= 3) {
                foreach ($objects['unrecognized'] as $value) {
                    $count = 0;
                    $totalLandUnitPrice = 0;
                    foreach ($data as $record) {
                        foreach ($record->properties as $property) {
                            foreach ($property->propertyDetail as $propertyDetail) {
                                if ($propertyDetail->land_type_purpose == $value['land_type_purpose']) {
                                    $count += 1;
                                    $totalLandUnitPrice += $propertyDetail->price_land ?? 0;
                                }
                            }
                        }
                    }
                    $averageLandUnitPrice = $count > 0 ? ((int)($totalLandUnitPrice / $count)) : 0;
                    $result['unrecognized'][] = [
                        'land_type_purpose' => $value['land_type_purpose'],
                        'area' => $value['area'],
                        'average_land_unit_price' => $averageLandUnitPrice,
                        'estimate_price' => $averageLandUnitPrice * $value['area'],
                    ];
                }

                $result['reliability'] = ValueDefault::RELIABILITY_NORMAL;
                $result['assets'] = $this->findAssetEstimate(['ids' => $ids]);
                $result['status'] = ValueDefault::STATUS_SUCCESS;
                $result['error_message'] = null;
                return $result;
            } else {
                $search['street_id'] = null;
                $search['land_type_purpose'] = null;
                $search['land_type_purpose_price_search'] = $defaultLandType;
                $caches = $this->findAssetEstimate($search, 3 - count($data));
                foreach ($caches as $cache) {
                    $ids[] = $cache->id;
                    $totalCache[] = $cache;
                    $returnAsset[] = $cache;
                }
                if (count($data) == 0) {
                    foreach ($objects['unrecognized'] as $value) {
                        $result['unrecognized'][] = [
                            'land_type_purpose' => $value['land_type_purpose'],
                            'area' => $value['area'],
                            'average_land_unit_price' => 0,
                            'estimate_price' => 0,
                        ];
                    }
                    $result['reliability'] = ValueDefault::RELIABILITY_LOW;
                    $result['assets'] = [];
                    $result['status'] = ValueDefault::STATUS_ERROR;
                    $result['error_message'] = EstimateAssetDefault::ERROR_MESSAGE;
                    return $result;
                } else {
                    $circularUnitPrice = 0;
                    foreach ($caches as $cache) {
                        foreach ($cache['land_type_purpose_price_search'] as $item) {
                            if ($item['id'] == $defaultLandType) {
                                $circularUnitPrice = $item['circular_unit_price'];
                            }
                        }
                    }
                    $search['street_id'] = null;
                    $search['not_street_id'] = $objects['street_id'] ?? null;
                    $search['land_type_purpose'] = null;
                    $search['land_type_purpose_price_search'] = $defaultLandType;
                    $search['circular_unit_price'] = $circularUnitPrice;
                    $caches = $this->findAssetEstimate($search, 3 - count($data));
                    foreach ($caches as $cache) {
                        $ids[] = $cache->id;
                        $totalCache[] = $cache;
                        $returnAsset[] = $cache;
                    }
                    $data = $this->findByIds(json_encode($ids));
                    if (count($data) >= 3) {
                        foreach ($objects['unrecognized'] as $value) {
                            $totalLandUnitPrice = 0;
                            $count = 0;
                            foreach ($data as $record) {
                                foreach ($record->properties as $property) {
                                    foreach ($property->propertyDetail as $propertyDetail) {
                                        if ($propertyDetail->land_type_purpose == $value['land_type_purpose']) {
                                            $count += 1;
                                            $totalLandUnitPrice += $propertyDetail->price_land ?? 0;
                                        }
                                    }
                                }
                            }
                            $averageLandUnitPrice = $count > 0 ? ((int)($totalLandUnitPrice / $count)) : 0;
                            if (count($totalCache) < 2) {
                                $reliability = ValueDefault::RELIABILITY_NORMAL;
                            } else {
                                $reliability = ValueDefault::RELIABILITY_LOW;
                            }
                            $result['unrecognized'][] = [
                                'land_type_purpose' => $value['land_type_purpose'],
                                'area' => $value['area'],
                                'average_land_unit_price' => $averageLandUnitPrice,
                                'estimate_price' => $averageLandUnitPrice * $value['area'],
                            ];
                        }
                        $result['reliability'] = $reliability;
                        $result['assets'] = $this->findAssetEstimate(['ids' => $ids]);
                        $result['status'] = ValueDefault::STATUS_SUCCESS;
                        $result['error_message'] = null;
                        return $result;
                    } else {
                        if (count($data) == 0) {
                            foreach ($objects['unrecognized'] as $value) {
                                $result['unrecognized'][] = [
                                    'land_type_purpose' => $value['land_type_purpose'],
                                    'area' => $value['area'],
                                    'average_land_unit_price' => 0,
                                    'estimate_price' => 0,
                                ];
                            }

                            $result['reliability'] = ValueDefault::RELIABILITY_LOW;
                            $result['assets'] = [];
                            $result['status'] = ValueDefault::STATUS_ERROR;
                            $result['error_message'] = EstimateAssetDefault::ERROR_MESSAGE;
                            return $result;
                        } else {
                            foreach ($objects['unrecognized'] as $value) {
                                $count = 0;
                                $totalLandUnitPrice = 0;
                                foreach ($data as $record) {
                                    foreach ($record->properties as $property) {
                                        foreach ($property->propertyDetail as $propertyDetail) {
                                            if ($propertyDetail->land_type_purpose == $value['land_type_purpose']) {
                                                $count += 1;
                                                $totalLandUnitPrice += $propertyDetail->price_land ?? 0;
                                            }
                                        }
                                    }
                                }
                                $averageLandUnitPrice = $count > 0 ? ((int)($totalLandUnitPrice / $count)) : 0;
                                $reliability = ValueDefault::RELIABILITY_LOW;
                                $result['unrecognized'][] = [
                                    'land_type_purpose' => $value['land_type_purpose'],
                                    'area' => $value['area'],
                                    'average_land_unit_price' => $averageLandUnitPrice,
                                    'estimate_price' => $averageLandUnitPrice * $value['area'],
                                ];
                            }

                            $result['reliability'] = $reliability;
                            $result['assets'] = $this->findAssetEstimate(['ids' => $ids]);
                            $result['status'] = ValueDefault::STATUS_SUCCESS;
                            $result['error_message'] = null;
                            return $result;
                        }
                    }
                }
            }
        }
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
            'distance' => $objects['distance'] ?? null,
            'front_side' => $objects['front_side'] ?? null,
            'asset_types' => EstimateAssetDefault::LAND_ASSET_TYPE,
            'transaction_type' => EstimateAssetDefault::TRANSACTION_TYPE,
            'land_type_purpose' => null,
            'street_id' => null,
            'not_street_id' => null,
            'main_road_length' => $objects['main_road_length'] ?? null,
            'main_road_length_to' => null,
            'land_type_purpose_id' => null,
            'circular_unit_price' => null,
        ];

        $result = [];
        $landTypePurpose = [];
        $totalCache = [];
        $returnAsset = [];
        if ($objects['unrecognized']) {
            foreach ($objects['unrecognized'] as $value) {
                $landTypePurpose[] = $value['land_type_purpose'];
            }
            $search['land_type_purpose'] = $landTypePurpose;
            $assets = $this->findAssetEstimate($search);
            $ids = [];
            foreach ($assets as $asset) {
                $ids[] = $asset->id;
                $returnAsset[] = $asset;
            }
            $data = $this->findByIds(json_encode($ids));
            if (count($data) == 3) {
                foreach ($objects['unrecognized'] as $value) {
                    $totalLandUnitPrice = 0;
                    foreach ($data as $record) {
                        foreach ($record->properties as $property) {
                            foreach ($property->propertyDetail as $propertyDetail) {
                                if ($propertyDetail->land_type_purpose == $value['land_type_purpose']) {
                                    $totalLandUnitPrice += $propertyDetail->price_land ?? 0;
                                }
                            }
                        }
                    }
                    $averageLandUnitPrice = (int)($totalLandUnitPrice / 3);
                    $result['unrecognized'][] = [
                        'land_type_purpose' => $value['land_type_purpose'],
                        'area' => $value['area'],
                        'average_land_unit_price' => $averageLandUnitPrice,
                        'estimate_price' => $averageLandUnitPrice * $value['area'],
                    ];
                }

                $result['reliability'] = ValueDefault::RELIABILITY_HIGHT;
                $result['assets'] = array_values(array_map("unserialize", array_unique(array_map("serialize", $returnAsset))));
                $result['status'] = ValueDefault::STATUS_SUCCESS;
                $result['error_message'] = null;
            } else {
                $search['street_id'] = null;
                $caches = $this->findAssetEstimate($search, 3 - count($data));
                foreach ($caches as $cache) {
                    $ids[] = $cache->id;
                    $totalCache[] = $cache;
                    $returnAsset[] = $cache;
                }
                if (count($data) == 0) {
                    foreach ($objects['unrecognized'] as $value) {
                        $result['unrecognized'][] = [
                            'land_type_purpose' => $value['land_type_purpose'],
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
                    $search['street_id'] = null;
                    $search['not_street_id'] = $objects['street_id'] ?? null;
                    if ($objects['main_road_length'] >= 4) {
                        $search['main_road_length'] = (($objects['main_road_length'] * 0.8) <= 4) ? $objects['main_road_length'] * 0.8 : 4;
                        $search['main_road_length_to'] = $objects['main_road_length'] * 1.2;
                    } else {
                        $search['main_road_length'] = $objects['main_road_length'] * 0.8;
                        $search['main_road_length_to'] = (($objects['main_road_length'] * 1.2) >= 4) ? $objects['main_road_length'] * 1.2 : 4;
                    }
                    $caches = $this->findAssetEstimate($search, 3 - count($data));
                    foreach ($caches as $cache) {
                        $ids[] = $cache->id;
                        $totalCache[] = $cache;
                        $returnAsset[] = $cache;
                    }
                    $data = $this->findByIds(json_encode($ids));
                    if (count($data) == 3) {
                        foreach ($objects['unrecognized'] as $value) {
                            $totalLandUnitPrice = 0;
                            foreach ($data as $record) {
                                foreach ($record->properties as $property) {
                                    foreach ($property->propertyDetail as $propertyDetail) {
                                        if ($propertyDetail->land_type_purpose == $value['land_type_purpose']) {
                                            $totalLandUnitPrice += $propertyDetail->price_land ?? 0;
                                        }
                                    }
                                }
                            }
                            $averageLandUnitPrice = (int)($totalLandUnitPrice / 3);
                            if (count($totalCache) < 2) {
                                $reliability = ValueDefault::RELIABILITY_HIGHT;
                            } else {
                                $reliability = ValueDefault::RELIABILITY_NORMAL;
                            }
                            $result['unrecognized'][] = [
                                'land_type_purpose' => $value['land_type_purpose'],
                                'area' => $value['area'],
                                'average_land_unit_price' => $averageLandUnitPrice,
                                'estimate_price' => $averageLandUnitPrice * $value['area'],
                            ];
                            $result['reliability'] = $reliability;
                        }
                        $result['assets'] = array_values(array_map("unserialize", array_unique(array_map("serialize", $returnAsset))));
                        $result['status'] = ValueDefault::STATUS_SUCCESS;
                        $result['error_message'] = null;
                        return $result;
                    } else {
                        if (count($data) == 0) {
                            foreach ($objects['unrecognized'] as $value) {
                                $result['unrecognized'][] = [
                                    'land_type_purpose' => $value['land_type_purpose'],
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
                            foreach ($objects['unrecognized'] as $value) {
                                $totalLandUnitPrice = 0;
                                foreach ($data as $record) {
                                    foreach ($record->properties as $property) {
                                        foreach ($property->propertyDetail as $propertyDetail) {
                                            if ($propertyDetail->land_type_purpose == $value['land_type_purpose']) {
                                                $totalLandUnitPrice += $propertyDetail->price_land ?? 0;
                                            }
                                        }
                                    }
                                }
                                $averageLandUnitPrice = (int)($totalLandUnitPrice / count($data));
                                if (count($data) >= 2) {
                                    $reliability = ValueDefault::RELIABILITY_NORMAL;
                                } else {
                                    $reliability = ValueDefault::RELIABILITY_LOW;
                                }
                                $result['unrecognized'][] = [
                                    'land_type_purpose' => $value['land_type_purpose'],
                                    'area' => $value['area'],
                                    'average_land_unit_price' => $averageLandUnitPrice,
                                    'estimate_price' => $averageLandUnitPrice * $value['area'],
                                ];
                                $result['reliability'] = $reliability;
                            }
                            $result['assets'] = array_values(array_map("unserialize", array_unique(array_map("serialize", $returnAsset))));
                            $result['status'] = ValueDefault::STATUS_SUCCESS;
                            $result['error_message'] = null;
                            return $result;
                        }
                    }
                }
            }
        }
        return $result;
    }

    public function autoApartmentAsset($objects)
    {
        $searchDefault = [
            'asset_types' => $objects['asset_type'],
            'transaction_type' => EstimateAssetDefault::TRANSACTION_TYPE,
        ];
        $projectId = $objects['project_id'] ?? null;
        $bedroomNum = $objects['bedroom_num'];
        $floorName = $objects['floor_name'];
        $distance = $objects['distance'];
        $location = $objects['location'];
        $search = [
            'project_id' => $projectId,
            'bedroom_num' => $bedroomNum,
            'floor_name' => $floorName,
        ];
        $result = [];
        $returnAsset = [];
        $assets = $this->findApartmentAsset(array_merge($searchDefault, $search));
        $asset = array_values(array_map("unserialize", array_unique(array_map("serialize", $assets->toArray()))));
        $returnAsset = array_merge($returnAsset, $asset);
        if (count($asset) < 3) {
            $ids = Arr::pluck($returnAsset, 'id');
            $search = [
                'project_id' => $projectId,
                'bedroom_num' => $bedroomNum,
                'ids' => $ids,
            ];
            $limit = 3 - count($assets);
            $assets = $this->findApartmentAsset(array_merge($searchDefault, $search), $limit);
            $asset = array_values(array_map("unserialize", array_unique(array_map("serialize", $assets->toArray()))));
            $limit = $limit - count($asset);
            $returnAsset = array_merge($returnAsset, $asset);
            if ($limit > 0) {
                $ids = Arr::pluck($returnAsset, 'id');
                $search = [
                    'project_id' => $projectId,
                    'floor_name' => $floorName,
                    'ids' => $ids,
                ];
                $assets = $this->findApartmentAsset(array_merge($searchDefault, $search), $limit);
                $asset = array_values(array_map("unserialize", array_unique(array_map("serialize", $assets->toArray()))));
                $limit = $limit - count($asset);
                $returnAsset = array_merge($returnAsset, $asset);
                if ($limit > 0) {
                    $ids = Arr::pluck($returnAsset, 'id');
                    $search = [
                        'project_id' => $projectId,
                        'ids' => $ids,
                    ];
                    $assets = $this->findApartmentAsset(array_merge($searchDefault, $search), $limit);
                    $asset = array_values(array_map("unserialize", array_unique(array_map("serialize", $assets->toArray()))));
                    $limit = $limit - count($asset);
                    $returnAsset = array_merge($returnAsset, $asset);
                    if ($limit > 0) {
                        $ids = Arr::pluck($returnAsset, 'id');
                        $search = [
                            'distance' => $distance,
                            'location' => $location,
                            'bedroom_num' => $bedroomNum,
                            'floor_name' => $floorName,
                            'ids' => $ids,
                        ];
                        $assets = $this->findApartmentAsset(array_merge($searchDefault, $search), $limit);
                        $asset = array_values(array_map("unserialize", array_unique(array_map("serialize", $assets->toArray()))));
                        $limit = $limit - count($asset);
                        $returnAsset = array_merge($returnAsset, $asset);

                        if ($limit > 0) {
                            $ids = Arr::pluck($returnAsset, 'id');
                            $search = [
                                'distance' => $distance,
                                'location' => $location,
                                'bedroom_num' => $bedroomNum,
                                'ids' => $ids,
                            ];
                            $assets = $this->findApartmentAsset(array_merge($searchDefault, $search), $limit);
                            $asset = array_values(array_map("unserialize", array_unique(array_map("serialize", $assets->toArray()))));
                            $limit = $limit - count($asset);
                            $returnAsset = array_merge($returnAsset, $asset);
                            if ($limit > 0) {
                                $ids = Arr::pluck($returnAsset, 'id');
                                $search = [
                                    'distance' => $distance,
                                    'location' => $location,
                                    'floor_name' => $floorName,
                                    'ids' => $ids,
                                ];
                                $assets = $this->findApartmentAsset(array_merge($searchDefault, $search), $limit);
                                $asset = array_values(array_map("unserialize", array_unique(array_map("serialize", $assets->toArray()))));
                                $limit = $limit - count($asset);
                                $returnAsset = array_merge($returnAsset, $asset);
                                if ($limit > 0) {
                                    $ids = Arr::pluck($returnAsset, 'id');
                                    $search = [
                                        'distance' => $distance,
                                        'location' => $location,
                                        'ids' => $ids,
                                    ];
                                    $assets = $this->findApartmentAsset(array_merge($searchDefault, $search), $limit);
                                    $asset = array_values(array_map("unserialize", array_unique(array_map("serialize", $assets->toArray()))));
                                    $limit = $limit - count($asset);
                                    $returnAsset = array_merge($returnAsset, $asset);
                                }
                            }
                        }
                    }
                }
            }
        }
        $result['assets'] = $returnAsset;
        $result['status'] = ValueDefault::STATUS_SUCCESS;
        $result['total'] = count($returnAsset);
        $result['error_message'] = null;
        return $result;
    }
    public function estimateApartmentAsset($objects): array
    {
        $search = [
            //            'location' => $objects['location'] ?? null,
            'asset_types' => EstimateAssetDefault::APARTMENT_ASSET_TYPE,
            'apartment_id' => $objects['apartment_id'] ?? null,
            'transaction_type' => EstimateAssetDefault::TRANSACTION_TYPE,
        ];

        $result = [];
        $returnAsset = [];

        if ($objects['apartment']) {
            $search['bedroom_num'] = $objects['apartment']['bedroom_num'];
            $assets = $this->findAssetEstimate($search, EstimateAssetDefault::ESTIMATE_APARTMENT_LIMIT);
            $ids = [];
            foreach ($assets as $asset) {
                $ids[] = $asset->id;
                $returnAsset[] = $asset;
            }
            $data = $this->findByIds(json_encode($ids));
            if (count($data) >= 2) {
                $totalUnitPrice = 0;
                foreach ($data as $record) {
                    $totalUnitPrice += $record->average_land_unit_price ?? 0;
                }
                $averageUnitPrice = (int)($totalUnitPrice / count($data));

                $result['apartment'] = [
                    'average_unit_price' => $averageUnitPrice,
                    'estimate_price' => $averageUnitPrice * $objects['apartment']['area'],
                ];
                $result['reliability'] = ValueDefault::RELIABILITY_HIGHT;
                $result['assets'] = array_values(array_map("unserialize", array_unique(array_map("serialize", $returnAsset))));
                $result['status'] = ValueDefault::STATUS_SUCCESS;
                $result['error_message'] = null;
                return $result;
            }
            if (count($data) == 1) {
                $totalUnitPrice = 0;
                foreach ($data as $record) {
                    $totalUnitPrice += $record->average_land_unit_price ?? 0;
                }
                $averageUnitPrice = (int)($totalUnitPrice / count($data));
                $result['apartment'] = [
                    'average_unit_price' => $averageUnitPrice,
                    'estimate_price' => $averageUnitPrice * $objects['apartment']['area'],
                ];
                $result['reliability'] = ValueDefault::RELIABILITY_NORMAL;
                $result['assets'] = array_values(array_map("unserialize", array_unique(array_map("serialize", $returnAsset))));
                $result['status'] = ValueDefault::STATUS_SUCCESS;
                $result['error_message'] = null;
                return $result;
            }
            if (count($data) == 0) {
                $search['bedroom_num'] = null;
                $assets = $this->findAssetEstimate($search, EstimateAssetDefault::ESTIMATE_APARTMENT_LIMIT);
                $ids = [];
                foreach ($assets as $asset) {
                    $ids[] = $asset->id;
                    $returnAsset[] = $asset;
                }
                $data = $this->findByIds(json_encode($ids));
                if (count($data) > 0) {
                    $totalUnitPrice = 0;
                    foreach ($data as $record) {
                        $totalUnitPrice += $record->average_land_unit_price ?? 0;
                    }
                    $averageUnitPrice = (int)($totalUnitPrice / count($data));
                    $result['apartment'] = [
                        'average_unit_price' => $averageUnitPrice,
                        'estimate_price' => $averageUnitPrice * $objects['apartment']['area'],
                    ];
                    $result['reliability'] = ValueDefault::RELIABILITY_NORMAL;
                    $result['assets'] = array_values(array_map("unserialize", array_unique(array_map("serialize", $returnAsset))));
                    $result['status'] = ValueDefault::STATUS_SUCCESS;
                    $result['error_message'] = null;
                    return $result;
                } else {
                    if (in_array($objects['district_id'], EstimateAssetDefault::URBAN_ASSET)) {
                        $search['distance'] = 1;
                    } else {
                        $search['distance'] = 2;
                    }
                    $search['apartment_id'] = null;
                    $search['bedroom_num'] = $objects['apartment']['bedroom_num'];
                    $search['location'] = $objects['location'] ?? null;
                    $assets = $this->findAssetEstimate($search, EstimateAssetDefault::ESTIMATE_APARTMENT_LIMIT);
                    $ids = [];
                    foreach ($assets as $asset) {
                        $ids[] = $asset->id;
                        $returnAsset[] = $asset;
                    }
                    $data = $this->findByIds(json_encode($ids));
                    if (count($data) > 0) {
                        $totalUnitPrice = 0;
                        foreach ($data as $record) {
                            $totalUnitPrice += $record->average_land_unit_price ?? 0;
                        }
                        $averageUnitPrice = (int)($totalUnitPrice / count($data));

                        $result['apartment'] = [
                            'average_unit_price' => $averageUnitPrice,
                            'estimate_price' => $averageUnitPrice * $objects['apartment']['area'],
                        ];
                        $result['reliability'] = ValueDefault::RELIABILITY_LOW;
                        $result['assets'] = array_values(array_map("unserialize", array_unique(array_map("serialize", $returnAsset))));
                        $result['status'] = ValueDefault::STATUS_SUCCESS;
                        $result['error_message'] = null;
                        return $result;
                    } else {
                        if (in_array($objects['district_id'], EstimateAssetDefault::URBAN_ASSET)) {
                            $search['distance'] = 1;
                        } else {
                            $search['distance'] = 2;
                        }
                        $search['bedroom_num'] = null;
                        $search['location'] = $objects['location'] ?? null;
                        $assets = $this->findAssetEstimate($search, EstimateAssetDefault::ESTIMATE_APARTMENT_LIMIT);
                        $ids = [];
                        foreach ($assets as $asset) {
                            $ids[] = $asset->id;
                            $returnAsset[] = $asset;
                        }
                        $data = $this->findByIds(json_encode($ids));
                        if (count($data) > 0) {
                            $totalUnitPrice = 0;
                            foreach ($data as $record) {
                                $totalUnitPrice += $record->average_land_unit_price ?? 0;
                            }
                            $averageUnitPrice = (int)($totalUnitPrice / count($data));

                            $result['apartment'] = [
                                'average_unit_price' => $averageUnitPrice,
                                'estimate_price' => $averageUnitPrice * $objects['apartment']['area'],
                            ];
                            $result['reliability'] = ValueDefault::RELIABILITY_LOW;
                            $result['assets'] = array_values(array_map("unserialize", array_unique(array_map("serialize", $returnAsset))));
                            $result['status'] = ValueDefault::STATUS_SUCCESS;
                            $result['error_message'] = null;
                            return $result;
                        } else {
                            $result['apartment'] = [
                                'average_unit_price' => 0,
                                'estimate_price' => 0,
                            ];
                            $result['reliability'] = ValueDefault::RELIABILITY_LOW;
                            $result['assets'] = [];
                            $result['status'] = ValueDefault::STATUS_ERROR;
                            $result['error_message'] = EstimateAssetDefault::ERROR_MESSAGE;
                            return $result;
                        }
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
     * @return array|ElasticquentResultCollection
     *
     */
    public
    function findAssetEstimate($search, int $limit = 3)
    {
        try {
            $provinceId = $search['province_id'] ?? null;
            $districtId = $search['district_id'] ?? null;
            $streetId = $search['street_id'] ?? null;
            $wardId = $search['ward_id'] ?? null;
            $transactionTypeIds = $search['transaction_type_ids'] ?? null;
            $assetTypes = $search['asset_types'] ?? null;
            $landTypePurpose = $search['land_type_purpose'] ?? null;
            $location = $search['location'] ?? null;
            $distance = $search['distance'] ?? null;
            $frontSide = $search['front_side'] ?? null;
            $notStreetId = $search['not_street_id'] ?? null;
            $mainRoadLength = $search['main_road_length'] ?? null;
            $mainRoadLengthTo = $search['main_road_length_to'] ?? null;
            $averageLandUnitPrice = $search['average_land_unit_price'] ?? null;
            $landTypePurposeSearchId = $search['land_type_purpose_price_search'] ?? null;
            $landTypePurposeId = $search['land_type_purpose_id'] ?? null;
            $circularUnitPrice = $search['circular_unit_price'] ?? null;
            $bedroomNum = $search['bedroom_num'] ?? null;
            $apartmentId = $search['apartment_id'] ?? null;
            $ids = $search['ids'] ?? null;
            $array['bool']['must'] = [];
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
            if (!empty($landTypePurposeSearchId)) {
                $array['bool']['must'][] = [
                    'match' => [
                        'land_type_purpose_price_search.id' => $landTypePurposeSearchId
                    ]
                ];
            }
            if (!empty($circularUnitPrice)) {
                $array['bool']['must'][] = [
                    'range' => [
                        'land_type_purpose_price_search.circular_unit_price"' => [
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
                foreach ($landTypePurpose as $value) {
                    $array['bool']['must'][] = [
                        'match' => [
                            'land_type_purpose.id' => $value,
                        ]
                    ];
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
            return CompareAssetGeneral::searchByQuery($array, null, null, $limit, null, $sortBy);
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
        $queryNoWard = $query;

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

    public function createVersionIndex(): array
    {
        $client = ClientBuilder::create()
            ->setSSLVerification(false)
            ->setHosts(config('elasticquent.config.hosts'))
            ->build();
        $params = [
            'index' => env('ELASTIC_ASSET_VERSION_INDEX')
        ];
        return $client->indices()->create($params);
    }

    public function findVersionById($id, $version)
    {

        $array = [];
        if (!empty($id)) {
            $array = [
                'match' => [
                    '_id' => $id . '-' . ($version ?? 1),
                ]
            ];
        }
        $search_result = CompareAssetVersion::searchByQuery($array, null, null, 1, 0, null);

        // dd($search_result);
        if (isset($search_result[0])) {
            return $search_result[0];
        } else {
            return null;
        }
    }
    public function findVersionById_v2($id, $version)
    {
        $array = [];
        $sourceField = [];
        if (!empty($id)) {
            $array = [
                'match' => [
                    '_id' => $id . '-' . ($version ?? 1),
                ],
            ];
            $sourceField = [
                'id',
                'public_date',
                'input_source',
                'asset_type_id',
                'status',
                'asset_type.description',
                'full_address',
                'land_no',
                'doc_no',
                'coordinates',
                'contact_person',
                'contact_phone',
                'max_value_description',
                'migrate_status',
                'adjust_amount',
                'adjust_percent',
                'average_land_unit_price',
                'convert_fee_total',
                'total_amount',
                'adjust_percent',
                'total_area',
                'total_area_amount',
                'total_construction_amount',
                'total_construction_area',
                'total_estimate_amount',
                'total_land_unit_price',
                'total_order_amount',
                'total_raw_amount',
                'topographic',
                'created_at',
                'updated_at',
                'created_by',
                'transaction_type.type',
                'transaction_type.description',

                'properties.id',
                'properties.insight_width',
                'properties.main_road_length',
                'properties.front_side_width',
                'properties.front_side',
                'properties.individual_road',
                'properties.asset_general_land_sum_area',

                'properties.compare_property_turning_time.main_road_length',
                'properties.compare_property_turning_time.is_alley_with_connection',
                'properties.compare_property_turning_time.is_near_main_road',
                'properties.compare_property_turning_time.material.description',

                'properties.property_detail.total_area',
                'properties.property_detail.price_land',
                'properties.property_detail.land_type_purpose_data.acronym',
                'properties.property_detail.land_type_purpose_data.description',
                'properties.property_detail.total_area',
                'properties.property_detail.circular_unit_price',
                'properties.property_detail.position_type_id',
                'properties.property_detail.land_type_purpose',

                'properties.land_shape.description',
                'properties.land_type.description',
                'properties.material',
                'properties.material.description',
                'properties.social_security.description',
                'properties.zoning.description',
                'properties.legal.description',
                'properties.business.description',
                'properties.conditions.description',
                'properties.paymen_method.description',
                'properties.feng_shui.description',
                'properties.electric_water.description',
                'topographic_data.description',
                'tangible_assets.building_type.description',
                'pic',
                'pic.picture_type',
                'pic.link',
                'pic.asset_general_id',
                'pic.id',

            ];
        }
        // $search_result = CompareAssetVersion::searchByQuery($array, null, null, 1, 0, null);
        $search_result = CompareAssetVersion::searchByQuery($array, null, $sourceField, 1, 0, null);

        if (isset($search_result[0])) {
            return $search_result[0];
        } else {
            return null;
        }
    }

    public function getTotalAssetByProvince()
    {
        $fromDate = request()->get('fromDate');
        $toDate = request()->get('toDate');
        if (isset($fromDate) && isset($toDate)) {
            $fromDate =  \Carbon\Carbon::createFromFormat('d/m/Y', $fromDate)->format('Y-m-d');
            $toDate =  \Carbon\Carbon::createFromFormat('d/m/Y', $toDate)->format('Y-m-d');
        } else {
            return ['message' => 'Vui lòng chọn ngày.', 'exeption' => ''];
        }
        $result = $this->model->with('province:id,name')
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->selectRaw("province_id, count(id) as total_assets")
            ->groupBy('province_id')
            ->get();
        return $result;
    }

    public function getTotalAssetByMonthOfYear()
    {
        $year = request()->get('year');
        if (!isset($year))
            return ['message' => 'Bạn chưa chọn năm', 'exeption' => ''];
        $result = $this->model
            ->selectRaw("date_part('month',created_at)::integer as month, count(id) as total_assets")
            ->whereYear('created_at', $year)
            ->groupByRaw("date_part('month',created_at)")
            ->get();
        return $result;
    }
    public function countCompareAssetGeneral()
    {
        // $date_from = request()->get('date_from');
        // $date_to = request()->get('date_to');
        // if(!isset($date_from) || !isset($date_to))
        // {
        //     return ['message' => 'Vui lòng chọn ngày', 'exception' => ''];
        // }
        $result = $this->model->query()
            //->whereBetween('created_at', [$date_from, $date_to] )
            ->select('province_id', DB::raw('count(*) as total'))
            ->groupBy('province_id')
            ->with('province:id,name')
            ->get();

        return $result;
    }

    public function findApartmentVersionById($id, $version)
    {
        $array = [];
        $sourceField = [];
        if (!empty($id)) {
            $array = [
                'match' => [
                    '_id' => $id . '-' . ($version ?? 1),
                ],
            ];

            $sourceField = [
                'id',
                'public_date',
                'input_source',
                'asset_type_id',
                'status',
                'asset_type.description',
                'full_address',
                'coordinates',
                'contact_person',
                'contact_phone',
                'migrate_status',
                'adjust_amount',
                'adjust_percent',
                'average_land_unit_price',
                'convert_fee_total',
                'total_amount',
                'total_area',
                'total_area_amount',
                'total_construction_amount',
                'total_construction_area',
                'total_estimate_amount',
                'total_land_unit_price',
                'total_order_amount',
                'total_raw_amount',
                'transaction_type.type',
                'transaction_type.description',
                'project',
                'room_details',
                'room_details.legal.description',
                'apartment_specification',
                'pic',
                'pic.picture_type',
                'pic.link',
                'pic.asset_general_id',
                'pic.id',
                'block',
                'floor',
                'apartment',
                'created_at',
                'created_by'
            ];
        }
        $search_result = CompareAssetVersion::searchByQuery($array, null, $sourceField, 1, 0, null);
        if (isset($search_result[0])) {
            return $search_result[0];
        } else {
            return null;
        }
    }

    /**
     * @param $search
     * @param int $limit
     * @return array|ElasticquentResultCollection
     *
     */
    public
    function findApartmentAsset($search, int $limit = 3)
    {
        try {
            $transactionTypeIds = $search['transaction_type'] ?? null;
            $assetTypes = $search['asset_types'] ?? null;
            $location = $search['location'] ?? null;
            $distance = $search['distance'] ?? null;
            $bedroomNum = $search['bedroom_num'] ?? null;
            $projectId = $search['project_id'] ?? null;
            $floorName = $search['floor_name'] ?? null;
            $ids =  $search['ids'] ?? null;
            $array['bool']['must'] = [];
            // $array['bool']['must'][] = [
            //     'match' => [
            //         'id' => 17191
            //     ]
            // ];
            $array['bool']['must'][] = [
                'match' => [
                    'status' => 1
                ]
            ];
            if (!empty($ids)) {
                $array['bool']['must_not'][] = [
                    'terms' => [
                        'id' => $ids
                    ]
                ];
            }
            if (!empty($assetTypes)) {
                $array['bool']['must'][] = [
                    'terms' => [
                        'asset_type_id' => $assetTypes
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
            if (!empty($projectId)) {
                $array['bool']['must'][] = [
                    'match' => [
                        'project.id' => $projectId
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
            if (!empty($floorName)) {
                $array['bool']['must'][] = [
                    'match' => [
                        'floor.name' => $floorName
                    ]
                ];
            }
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
                // $sortBy = [
                //     '_geo_distance' => [
                //         'pin.location' => [
                //             'lat' => (float)$location[0] ?? null,
                //             'lon' => (float)$location[1] ?? null,
                //         ],
                //         'order' => 'asc',
                //         'unit' => 'm',
                //         'mode' => 'min',
                //         'distance_type' => 'arc',
                //         'ignore_unmapped' => true,
                //     ]
                // ];
            }
            $sortBy = [
                'public_date' => ['order' => 'desc'],
            ];
            $sourceField = [
                '*'
            ];

            return CompareAssetGeneral::searchByQuery($array, null, $sourceField, $limit, null, $sortBy);
        } catch (Exception $exception) {
            Log::error($exception);
            return [];
        }
    }
    public function findPaging2()
    {
        $perPage = (int)request()->get('limit');
        $page = (int)request()->get('page');
        $search = request()->get('search');
        $assetTypeId = request()->get('asset_type_id');
        $createdBy = request()->get('created_by');
        $coordinates = request()->get('coordinates');
        $provinceId = request()->get('province_id');
        $districtId = request()->get('district_id');
        $wardId = request()->get('ward_id');
        $streetId = request()->get('street_id');
        $sourceId = request()->get('source_id');
        $transactiontypeId = request()->get('transaction_type_id');
        $publicDateFrom = request()->get('public_date_from');
        $publicDateTo = request()->get('public_date_to');
        $contactPerson = request()->get('contact_person');
        $contactPhone = request()->get('contact_phone');
        $totalAreaFrom = request()->get('total_area_from');
        $totalAreaTo = request()->get('total_area_to');
        $constructionAreaFrom = request()->get('total_construction_area_from');
        $constructionAreaTo = request()->get('total_construction_area_to');
        $amountFrom = request()->get('total_amount_from');
        $amountTo = request()->get('total_amount_to');
        $avgPriceFrom = request()->get('average_land_unit_price_from');
        $avgPriceTo = request()->get('average_land_unit_price_to');
        $docNo = request()->get('doc_no');
        $landNo = request()->get('land_no');
        $user = CommonService::getUser();
        $where = [
            'migrate_status' => 'TSS',
            'status' => 1,
        ];
        $select = [
            'id',
            DB::raw("to_date(public_date, 'dd/MM/yyyy') as public_date"),
            'migrate_status',
            'full_address',
            'total_area',
            'total_construction_area',
            'total_amount',
            'status',
            'average_land_unit_price',
            'created_at',
            'updated_at',
            'created_by',
            'asset_type_id',
            'transaction_type_id',
            'project_id'
        ];
        $with = [
            'assetType:id,description',
            'transactionType:id,description',
            'createdBy:id,name',
            'project:id,name'
        ];
        DB::enableQueryLog();
        $result = $this->model->query()->with($with)->where($where)->select($select);
        if (!empty($search)) {
            $result = $result->where(function ($query) use ($search) {
                $query->where('full_address', 'ilike', '%' . $search . '%');
                $query->orWhereHas('createdBy', function ($has) use ($search) {
                    $has->where('name', 'ilike', '%' . $search . '%');
                });
                $query->orWhereHas('assetType', function ($has) use ($search) {
                    $has->where('description', 'ilike', '%' . $search . '%');
                });
                $query->orWhereHas('transactionType', function ($has) use ($search) {
                    $has->where('description', 'ilike', '%' . $search . '%');
                });
                $query->orWhereHas('project', function ($has) use ($search) {
                    $has->where('name', 'ilike', '%' . $search . '%');
                });
                if (intval($search))
                    $query->orWhere('id', intval($search));
            });
        }
        if (!empty($assetTypeId)) {
            $result->whereHas('assetType', function ($has) use ($assetTypeId) {
                $has->where('id', $assetTypeId);
            });
        }
        if (!empty($createdBy)) {
            $result->WhereHas('createdBy', function ($has) use ($createdBy) {
                $has->where('name', 'ilike', '%' . $createdBy . '%');
            });
        }
        if (!empty($coordinates)) {
            $result->where('coordinates', $coordinates);
        }
        if (!empty($provinceId)) {
            $result->where('province_id', $provinceId);
        }
        if (!empty($districtId)) {
            $result->where('district_id', $districtId);
        }
        if (!empty($wardId)) {
            $result->where('ward_id', $wardId);
        }
        if (!empty($streetId)) {
            $result->where('street_id', $streetId);
        }
        if (!empty($transactiontypeId)) {
            $result->where('transaction_type_id', $transactiontypeId);
        }
        if (!empty($sourceId)) {
            $result->where('source_id', $sourceId);
        }
        if (!empty($publicDateFrom) && $publicDateTo != 'Invalid date') {
            $result->whereRaw("to_date(public_date, 'dd/MM/yyyy') >= to_date('$publicDateFrom', 'dd/MM/yyyy') ");
        }
        if (!empty($publicDateTo) && $publicDateTo != 'Invalid date') {
            $result->whereRaw("to_date(public_date, 'dd/MM/yyyy') <= to_date('$publicDateTo', 'dd/MM/yyyy')");
        }
        if (!empty($contactPerson)) {
            $result->where('contact_person', $contactPerson);
        }
        if (!empty($contactPhone)) {
            $result->where('contact_phone', $contactPhone);
        }
        if (!empty($totalAreaFrom) && floatval($totalAreaFrom) && floatval($totalAreaFrom) >= 0) {
            $result->whereRaw("total_area >= $totalAreaFrom::float");
        }
        if (!empty($totalAreaTo) && floatval($totalAreaTo) && floatval($totalAreaTo) >= 0) {
            $result->whereRaw("total_area <= $totalAreaTo::float");
        }
        if (!empty($constructionAreaFrom) && floatval($constructionAreaFrom) && floatval($constructionAreaFrom) >= 0) {
            $result->whereRaw("total_construction_area >= $constructionAreaFrom::float");
        }
        if (!empty($constructionAreaTo) && floatval($constructionAreaTo) && floatval($constructionAreaTo) >= 0) {
            $result->whereRaw("total_construction_area <= $constructionAreaTo::float");
        }
        if (!empty($amountFrom) && floatval($amountFrom) && floatval($amountFrom) >= 0) {
            $result->whereRaw("total_amount >= $amountFrom::float");
        }
        if (!empty($amountTo) && floatval($amountTo) && floatval($amountTo) >= 0) {
            $result->whereRaw("total_amount <= $amountTo::float");
        }
        if (!empty($avgPriceFrom) && floatval($avgPriceFrom) && floatval($avgPriceFrom) >= 0) {
            $result->whereRaw("average_land_unit_price >= $avgPriceFrom::float");
        }
        if (!empty($avgPriceTo) && floatval($avgPriceTo) && floatval($avgPriceTo) >= 0) {
            $result->whereRaw("average_land_unit_price <= $avgPriceTo::float");
        }
        if (!empty($docNo)) {
            $result->where('doc_no', $docNo);
        }
        if (!empty($landNo)) {
            $result->where('land_no', $landNo);
        }
        $role = $user->roles->last();
        // if ($role->name === 'USER') {
        //     $result = $result->where('created_by', $user->id);
        // }
        $result = $result->orderBy('updated_at', 'desc');
        $result = $result->orderBy('transaction_type_id', 'desc');
        $result = $result->forPage($page, $perPage)
            ->paginate($perPage);
        // dd(DB::getQueryLog());
        foreach ($result as $item) {
            $item->append('area_total');
            if (isset($item['project_id'])) {
                $name_project_result = Project::query()->where('id', '=', $item['project_id'])->first();
                if ($name_project_result) {
                    $item['full_address'] = $name_project_result['name'] . ', ' . $item['full_address'];
                }
            }
        }

        return $result;
    }

    public function exportAsset()
    {
        $assetTypeId = request()->get('asset_type_id');
        $createdBy = request()->get('created_by');
        // $coordinates = request()->get('coordinates');
        // $provinceId = request()->get('province_id');
        // $districtId = request()->get('district_id');
        // $wardId = request()->get('ward_id');
        // $streetId = request()->get('street_id');
        // $sourceId = request()->get('source_id');
        $fromDate = request()->get('fromDate');
        $toDate = request()->get('toDate');
        // $contactPerson = request()->get('contact_person');
        // $contactPhone = request()->get('contact_phone');
        // $totalAreaFrom = request()->get('total_area_from');
        // $totalAreaTo = request()->get('total_area_to');
        // $constructionAreaFrom = request()->get('total_construction_area_from');
        // $constructionAreaTo = request()->get('total_construction_area_to');
        // $amountFrom = request()->get('total_amount_from');
        // $amountTo = request()->get('total_amount_to');
        // $avgPriceFrom = request()->get('average_land_unit_price_from');
        // $avgPriceTo = request()->get('average_land_unit_price_to');
        // $docNo = request()->get('doc_no');
        // $landNo = request()->get('land_no');
        $where = [
            'migrate_status' => 'TSS',
            'status' => 1,
        ];
        $select = [
            'id',
            DB::raw("to_date(public_date, 'dd/MM/yyyy') as public_date"),
            'full_address',
            'total_area',
            'total_construction_area',
            'total_amount',
            'average_land_unit_price',
            'created_at',
            'updated_at',
            'created_by',
            'asset_type_id',
            'transaction_type_id',
            'coordinates',
            'max_value_description',
            'province_id',
            'district_id',
            'ward_id',
            'street_id',
        ];
        $with = [
            'assetType:id,description',
            'transactionType:id,description',
            'properties:id,asset_general_id,main_road_length,land_type_id',
            'properties.landType:id,description',
            'createdBy:id,name',
            'province:id,name',
            'district:id,name',
            'ward:id,name',
            'street:id,name'
        ];
        $result = $this->model->query()->with($with)->where($where)->select($select);
        if (!empty($assetTypeId)) {
            $result->whereHas('assetType', function ($has) use ($assetTypeId) {
                $has->where('id', $assetTypeId);
            });
        }
        if (!empty($createdBy)) {
            $list_user = explode(',', $createdBy);
            $result = $result->whereIn('created_by', $list_user);
        }
        if (!empty($fromDate) && $fromDate != 'Invalid date') {
            $result->whereRaw("created_at >= to_date('$fromDate', 'dd/MM/yyyy') ");
        }
        if (!empty($toDate) && $toDate != 'Invalid date') {
            $result->whereRaw("created_at <= to_date('$toDate', 'dd/MM/yyyy') + '1 day'::interval");
        }
        // dd($result->limit(5)->get()->append(['area_total', 'front_side_text', 'land_type_text'])->toArray());
        return $result->get()->append(['area_total', 'front_side_text', 'land_type_text']);
    }
}
