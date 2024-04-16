<?php

namespace App\Repositories;

use App\Contracts\PriceEstimateRepository;
use App\Contracts\UserRepository;
use App\Repositories\EloquentUserRepository;
use App\Models\User;

use App\Enum\CompareMaterData;
use App\Enum\EstimateAssetDefault;
use App\Enum\PriceEstimateOtherInformationEnum;
use App\Enum\ErrorMessage;
use App\Enum\ValueDefault;

use App\Models\PriceEstimate;
use App\Models\PriceEstimateVersion;
use App\Models\PriceEstimateProperty;
use App\Models\PriceEstimatePropertyDetail;
use App\Models\PriceEstimatePropertyTurningTime;
use App\Models\PriceEstimateHasAsset;
use App\Models\PriceEstimateFinal;
use App\Models\PriceEstimatePic;
use App\Models\PriceEstimateApartmentFinal;
use App\Models\ApartmentAsset;
use App\Models\PriceEstimateApartmentProperty;


use App\Models\Appraise;
use App\Models\RealEstate;

use App\Notifications\ActivityLog;
use App\Services\PriceEstimateVersionService;
use Elasticsearch\ClientBuilder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\QueryBuilder\QueryBuilder;
use Exception;
use Throwable;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Input;

use App\Services\CommonService;
use DateInterval;
use Error;
use Illuminate\Support\Facades\Date;
use IntlTimeZone;
use LDAP\Result;
use Nette\Utils\Json;
use OpenApi\Examples\SwaggerSpec\PetstoreSimple\ErrorModel;
use PHP_CodeSniffer\Util\Common;
use Psy\Exception\BreakException;

use function PHPUnit\Framework\isEmpty;

class  EloquentPriceEstimateRepository extends EloquentRepository implements PriceEstimateRepository
{

    use ActivityLog;

    private string $defaultSort = 'id';

    private string $allowedSorts = 'id';

    /**
     * @return bool
     */
    public function updateStatus($id, $request)
    {
        return DB::transaction(function () use ($id, $request) {
            try {
                $result = 0;
                $priceEstimate = $this->findById($id);
                $status = $request->input('status');
                if (isset($status)) {
                    $allow = 1;
                    if (!isset($priceEstimate->priceEstimateLaw) || !count($priceEstimate->priceEstimateLaw)) {
                        $allow = 2;
                    }
                    if (!isset($priceEstimate->assetGeneral) || !count($priceEstimate->assetGeneral)) {
                        $allow = 3;
                    }
                    if ($allow == 1 || $status == 5) {
                        /* if (empty($priceEstimate->assetGeneral) && $status!=1) {
                            $status = 1;
                        } */
                        $result = $this->model->query()
                            ->where('id', '=', $id)
                            ->update([
                                'status' => $status
                            ]);

                        return $result;
                    }

                    if ($allow == 2) {
                        return "Chưa khai báo thông tin pháp lý cho Tài Sản Thẩm Định.";
                    }
                    if ($allow == 3) {
                        return "Chưa chọn Tài Sản So Sánh cho Tài Sản Thẩm Định trước khi xác nhận.";
                    }
                }
            } catch (Exception $exception) {
                Log::error($exception);
                throw $exception;
            }
        });
    }

    /**
     * @return LengthAwarePaginator
     */
    public function findPaging()
    {
        $perPage = (int)request()->get('limit');
        $page = (int)request()->get('page');
        $search = request()->get('search');
        $status = request()->get('status');

        $betweenTotal = ValueDefault::TOTAL_PRICE_PERCENT;
        $user = CommonService::getUser();
        $result = $this->model->query()->with([
            'province',
            'district',
            'ward',
            'street',
            'properties',
            'properties.propertyDetail.landTypePurpose',
            'createdBy',
            'version',
            'assetType',
            'apartmentProperties',
            'apartmentProperties.floor',
            'landFinalEstimate'
        ]);
        $role = $user->roles->last();
        if (($role->name !== 'ROOT_ADMIN' && $role->name !== 'SUB_ADMIN')) {
            $result = $result->where('created_by', $user->id);
        }
        if (isset($search)) {
            $filterSubstr = substr($search, 0, 1);
            $filterData = substr($search, 1);
            switch ($filterSubstr) {
                case '!':
                    if (floatval($filterData) >= 0) {
                        $result = $result->where('total_area', floatval($filterData));
                    }
                    break;
                case '@':
                    $result = $result->where(function ($q) use ($filterData) {
                        $q = $q->whereHas('createdBy', function ($has) use ($filterData) {
                            $has->where('name', 'ILIKE', '%' . $filterData . '%');
                        });
                    });
                    break;
                case '&':
                    $data = explode('/', $filterData);
                    $doc_no = $data[0];
                    $land_no = isset($data[1]) ? $data[1] : -1;
                    if (intval($doc_no) >= 0) {
                        $result = $result->where(function ($q) use ($doc_no, $land_no) {
                            $q = $q->where('doc_no', '=', $doc_no);
                            if (intval($land_no) >= 0)
                                $q = $q->Where('land_no', '=', $land_no);
                        });
                    }
                    break;
                case '$':
                    if (floatval($filterData) >= 0) {
                        $fromValue = floatval($filterData) - floatval($filterData) * $betweenTotal;
                        $toValue = floatval($filterData) + floatval($filterData) * $betweenTotal;
                        $result = $result->whereBetween('total_price', [$fromValue, $toValue]);
                    }
                    break;
                default:
                    $result = $result->where(function ($q) use ($search) {
                        $q = $q->where('id', 'like', strval($search));
                        $q = $q->orwhere('appraise_asset', 'ILIKE', '%' . $search . '%');
                        $q = $q->orwhereHas('assetType', function ($has) use ($search) {
                            $has->where('description', 'ILIKE', '%' . $search . '%');
                        });
                        $q = $q->orwhereHas('createdBy', function ($has) use ($search) {
                            $has->where('name', 'ILIKE', '%' . $search . '%');
                        });
                    });
            }
        }
        if (isset($status)) {
            $result = $result->whereIn('status', $status);
        }
        $result = $result->orderByDesc('updated_at');
        $result = $result->orderByDesc('id');
        $result = $result->forPage($page, $perPage)
            ->paginate($perPage);
        return $result;
    }

    public function findPaging2(): LengthAwarePaginator
    {
        $user = CommonService::getUser();

        $perPage = (int)request()->get('limit');
        $page = (int)request()->get('page');
        $search = request()->get('search');
        $status = request()->get('status');
        $filters = request()->get('filters');
        $sortField = request()->get('sortField');
        $sortOrder = request()->get('sortOrder');
        $popup = request()->get('popup');

        $query = request()->get('query');
        if (!empty($query)) {
            $query = json_decode($query);
        } else {
            $query = [];
        }
        // \DB::enableQueryLog();

        $result = QueryBuilder::for($this->model)
            ->with([
                'province',
                'district',
                'ward',
                'street',
                'properties',
                'properties.propertyDetail.landTypePurpose',
                'createdBy',
                'version',
                'assetType',
                'apartmentProperties',
                'apartmentProperties.floor',
            ])

            ->whereHas('assetType', function ($q) use ($query) {
                if (isset($query->asset_type_id) && !empty($query->asset_type_id)) {
                    return $q->where('id', '=', $query->asset_type_id);
                }
            })
            ->whereHas('createdBy', function ($q) use ($query, $sortField, $user, $popup, $sortOrder) {
                if (isset($query->created_by) && ($query->created_by != 'Tất cả người tạo')) {
                    return $q->where('name', '=', $query->created_by);
                }
                if ($sortField == 'created_by.name') {
                    if ($sortOrder == 'descend') {
                        $q->orderBy('name', 'DESC');
                    } else {
                        $q->orderBy('name', 'ASC');
                    }
                }

                $role = $user->roles->last();
                if ((($role->name !== 'ROOT_ADMIN' && $role->name !== 'SUB_ADMIN')) || (!empty($popup))) {
                    return $q->where('id', $user->id);
                }
            })
            ->whereHas('province', function ($q) use ($query) {
                if (isset($query->province_id) && !empty($query->province_id)) {
                    return $q->where('id', '=', $query->province_id);
                }
            })
            ->whereHas('district', function ($q) use ($query) {
                if (isset($query->district_id) && !empty($query->district_id)) {
                    return $q->where('id', '=', $query->district_id);
                }
            })
            ->whereHas('ward', function ($q) use ($query) {
                if (isset($query->ward_id) && !empty($query->ward_id)) {
                    return $q->where('id', '=', $query->ward_id);
                }
            })
            // ->whereHas('properties', function ($q) use ($query, $sortField, $sortOrder) {
            //     if ($sortField == 'properties[0].front_side') {
            //         if ($sortOrder == 'descend') {
            //             $q->orderBy('front_side', 'DESC');
            //         } else {
            //             $q->orderBy('front_side', 'ASC');
            //         }
            //     } else if ($sortField == 'properties[0].appraise_land_sum_area') {
            //         if ($sortOrder == 'descend') {
            //             $q->orderBy('appraise_land_sum_area', 'DESC');
            //         } else {
            //             $q->orderBy('appraise_land_sum_area', 'ASC');
            //         }
            //     }
            // })
            ->whereHas('street', function ($q) use ($query, $sortField, $sortOrder) {
                if (isset($query->street_id) && !empty($query->street_id)) {
                    return $q->where('id', '=', $query->street_id);
                }
                if ($sortField == 'street') {
                    if ($sortOrder == 'descend') {
                        $q->orderBy('name', 'DESC');
                    } else {
                        $q->orderBy('name', 'ASC');
                    }
                }
            });

        if (isset($query->front_side) && !empty($query->front_side)) {
            $query->front_side = intval($query->front_side) - 1;
            $query->whereHas('properties', function ($q) use ($query, $sortField, $sortOrder) {
                if ($sortField == 'properties[0].front_side') {
                    if ($sortOrder == 'descend') {
                        $q->orderBy('front_side', 'DESC');
                    } else {
                        $q->orderBy('front_side', 'ASC');
                    }
                } else if ($sortField == 'properties[0].appraise_land_sum_area') {
                    if ($sortOrder == 'descend') {
                        $q->orderBy('appraise_land_sum_area', 'DESC');
                    } else {
                        $q->orderBy('appraise_land_sum_area', 'ASC');
                    }
                }
                return $q->where('front_side', '=', $query->front_side);
            });
        }
        if (isset($query->status) && !empty($query->status)) {
            $result = $result->where('status', $query->status);
        } else if (isset($status)) {
            $result = $result->where('status', $status);
        }
        if (isset($query->land_no) && !empty($query->land_no)) {
            $result = $result->where('land_no', $query->land_no);
        }
        if (isset($query->doc_no) && !empty($query->doc_no)) {
            $result = $result->where('doc_no', $query->doc_no);
        }
        if (isset($query->id) && !empty($query->id)) {
            $result = $result->where('id', $query->id);
        }
        if (isset($query->coordinates) && !empty($query->coordinates)) {
            $result = $result->where('coordinates', $query->coordinates);
        }
        if (isset($query->public_date_from) && !empty($query->public_date_from)) {
            $result =  $result->where('created_at', '>=', date('Y-m-d', strtotime($query->public_date_from)) . ' 00:00:00');
        }
        if (isset($query->public_date_to) && !empty($query->public_date_to)) {
            $result = $result->where('created_at', '<=', date('Y-m-d', strtotime($query->public_date_to)) . ' 00:00:00');
        }

        if (isset($query->search) && !empty($query->search)) {
            // $result = $result->where('id', '=', $query->search);
            $search = $query->search;
            $result = $result->where(function ($q) use ($search) {
                $q = $q->where('id', 'like', strval($search));
                $q = $q->orwhere('priceEstimate_asset', 'ILIKE', '%' . $search . '%');
            });
        }

        if (!empty($filters)) {
            $filters = json_decode($filters);
            if (isset($filters->status) && !empty($filters->status)) {
                $result = $result->orWhereIn('status', $filters->status);
            }
        }

        $result = $result->orderByDesc('updated_at');

        if (!empty($sortField)) {
            if ($sortField == 'id') {
                if ($sortOrder == 'descend') {
                    $result = $result->orderByDesc('id');
                } else {
                    $result = $result->orderBy('id');
                }
            } else if ($sortField == 'created_at') {
                if ($sortOrder == 'descend') {
                    $result = $result->orderByDesc('created_at');
                } else {
                    $result = $result->orderBy('created_at');
                }
            } else if ($sortField == 'status') {
                if ($sortOrder == 'descend') {
                    $result = $result->orderByDesc('status');
                } else {
                    $result = $result->orderBy('status');
                }
            } else {
                $result = $result->orderByDesc($this->allowedSorts);
            }
        } else {
            $result = $result->orderByDesc($this->allowedSorts);
        }
        /* $sql = $result->toSql();
        $bindings = $result->getBindings();
        dd($bindings); */
        $result = $result
            ->forPage($page, $perPage)
            ->paginate($perPage);

        foreach ($result as $stt => $item) {
            // $result[$stt]->append('status_text');
            // $result[$stt]->append('total_asset_price');
            //$result[$stt]->append('total_asset_price_round');
        }
        // dd(\DB::getQueryLog());

        return $result;
    }

    /**
     * @return Builder[]|Collection
     */
    public function findAll()
    {
        return $this->model->query()
            ->select()
            ->orderByDesc($this->defaultSort)
            ->get();
    }

    public function findById($id)
    {
        // remove duplicate assetUnitPrice
        $checkAssetUnitPrices = [];
        // $assetUnitPrices = PriceEstimateUnitPrice::wherePriceEstimateId($id)->get();
        // foreach ($assetUnitPrices as $item) {
        //     if (!isset($checkAssetUnitPrices[$item->priceEstimate_id][$item->asset_general_id][$item->land_type_id][$item->position_type_id])) {
        //         $checkAssetUnitPrices[$item->priceEstimate_id][$item->asset_general_id][$item->land_type_id][$item->position_type_id] = 1;
        //     } else {
        //         PriceEstimateUnitPrice::whereId($item->id)->forceDelete();
        //     }
        // }

        // $checkAssetUnitAreas = [];
        // $assetUnitAreas = PriceEstimateUnitArea::wherePriceEstimateId($id)->get();
        // foreach ($assetUnitAreas as $item) {
        //     if (!isset($checkAssetUnitAreas[$item->priceEstimate_id][$item->asset_general_id][$item->land_type_id][$item->position_type_id])) {
        //         $checkAssetUnitAreas[$item->priceEstimate_id][$item->asset_general_id][$item->land_type_id][$item->position_type_id] = 1;
        //     } else {
        //         PriceEstimateUnitArea::whereId($item->id)->forceDelete();
        //     }
        // }

        $version = request()->get('version');
        $result = null;
        if ($version && !is_array($version)) {
            $result = $this->findVersionById($id, $version);
        }
        if (!$result) {
            $result = $this->model->query()
                ->where('id', '=', $id)
                ->with('province')
                ->with('district')
                ->with('ward')
                ->with('street')
                ->with('distance')
                ->with('assetType')
                // ->with('pic.picType')
                // ->with('topographic')
                // ->with('priceEstimateApproach')
                // ->with('priceEstimatePrinciple')
                // ->with('priceEstimateMethodUsed')
                // ->with('priceEstimateBasisProperty')
                ->with('properties.propertyDetail')
                ->with('properties.propertyTurningTime')
                ->with('properties.propertyTurningTime.material')
                ->with('properties.propertyDetail.landTypePurpose')
                // ->with('properties.propertyDetail.positionType')
                // ->with('properties.legal')
                // ->with('properties.zoning')
                // ->with('properties.landType')
                // ->with('properties.landShape')
                // ->with('properties.business')
                // ->with('properties.electricWater')
                // ->with('properties.socialSecurity')
                // ->with('properties.fengShui')
                // ->with('properties.paymenMethod')
                // ->with('properties.conditions')
                ->with('properties.material')
                // ->with('otherAssets')
                // ->with('constructionCompany.constructionCompany')
                ->with('createdBy')
                ->with('version')
                ->first();
        }

        return $result;
    }

    public function findByIdTest($id)
    {
        // remove duplicate assetUnitPrice
        $checkAssetUnitPrices = [];

        $version = request()->get('version');
        $result = null;
        if ($version && !is_array($version)) {
            $result = $this->findVersionById($id, $version);
        }
        if (!$result) {

            $result = $this->model->query()
                ->where('id', '=', $id)
                ->with('province')
                ->with('district')
                ->with('ward')
                ->with('street')
                ->with('distance')
                ->with('assetType')
                // ->with('pic.picType')
                // ->with('topographic')
                // ->with('priceEstimateApproach')
                // ->with('priceEstimatePrinciple')
                // ->with('priceEstimateMethodUsed')
                // ->with('priceEstimateBasisProperty')
                ->with('properties.propertyDetail')
                ->with('properties.propertyTurningTime')
                ->with('properties.propertyTurningTime.material')
                ->with('properties.propertyDetail.landTypePurpose')
                // ->with('properties.propertyDetail.positionType')
                // ->with('properties.legal')
                // ->with('properties.zoning')
                // ->with('properties.landType')
                // ->with('properties.landShape')
                // ->with('properties.business')
                // ->with('properties.electricWater')
                // ->with('properties.socialSecurity')
                // ->with('properties.fengShui')
                // ->with('properties.paymenMethod')
                // ->with('properties.conditions')
                ->with('properties.material')
                // ->with('constructionCompany.constructionCompany')
                ->with('createdBy')
                ->with('version')
                ->first();
        }

        return $result;
    }


    /**
     * @param $ids
     * @return Builder[]|Collection
     */
    public function findByIds($ids)
    {
        $ids = json_decode($ids);
        $items = $this->model->query()
            ->whereIn('id', $ids, 'or', false)
            ->with('province')
            ->with('district')
            ->with('ward')
            ->with('street')
            ->with('distance')
            ->with('assetType')
            ->with('properties.propertyDetail')
            ->with('properties.propertyTurningTime')
            ->with('properties.propertyTurningTime.material')
            ->with('properties.propertyDetail.landTypePurpose')
            ->with('properties.material')
            ->with('otherAssets')
            // ->with('constructionCompany.constructionCompany')
            ->with('createdBy')
            ->with('version')
            ->get();

        return $items;
    }

    /**
     * @param array $objects
     * @return object
     * @throws Throwable
     */
    public function createPriceEstimate(array $objects): object
    {
        return DB::transaction(function () use ($objects) {
            try {
                $objects['created_by'] = is_array($objects['created_by']) ? $objects['created_by']['id'] : $objects['created_by'];
                $objects["updated_at"] = date("Y-m-d H:i:s");
                $objects['status'] = 1;
                /* if (!isset($objects['asset_general']) || empty($objects['asset_general'])) {
                    $objects['status'] = 1;
                } */
                $priceEstimate = new PriceEstimate($objects);
                $priceEstimateId = QueryBuilder::for(PriceEstimate::class)
                    ->insertGetId($priceEstimate->attributesToArray());

                $countVersion = PriceEstimateVersion::query()
                    ->where('priceEstimate_id', '=', $priceEstimateId)
                    ->where('status', '=', $priceEstimate->status)
                    ->count();
                $version['version'] = $priceEstimate->status . '.' . $countVersion;
                $version['status'] = $priceEstimate->status;
                $version['priceEstimate_id'] = $priceEstimateId;
                PriceEstimateVersion::query()->insert($version);


                if (isset($objects['properties'])) {
                    foreach ($objects['properties'] as $propertyData) {
                        $propertyData['priceEstimate_id'] = $priceEstimateId;

                        $property = new PriceEstimateProperty($propertyData);
                        $propertyId = QueryBuilder::for($property)
                            ->insertGetId($property->attributesToArray());
                        if (isset($propertyData['property_detail'])) {
                            foreach ($propertyData['property_detail'] as $propertyDetail) {
                                $propertyDetail['priceEstimate_property_id'] = $propertyId;
                                $propertyDetail['k_rate'] = isset($propertyDetail['k_rate']) ? intval($propertyDetail['k_rate']) : 0;
                                $propertyDetail['planning_area'] = isset($propertyDetail['planning_area']) ? floatval($propertyDetail['planning_area']) : 0;
                                $propertyDetail['estimation_value'] = isset($propertyDetail['estimation_value']) ? doubleval($propertyDetail['estimation_value']) : 0;
                                $propertyDetail = array_map(function ($v) {
                                    return (is_null($v)) ? "" : $v;
                                }, $propertyDetail);
                                $detail = new PriceEstimatePropertyDetail($propertyDetail);
                                QueryBuilder::for($detail)
                                    ->insert($detail->attributesToArray());
                            }
                        }

                        if (isset($propertyData['property_turning_time'])) {
                            foreach ($propertyData['property_turning_time'] as $propertyTurningTime) {
                                $propertyTurningTime['priceEstimate_property_id'] = $propertyId;
                                $detail = new PriceEstimatePropertyTurningTime($propertyTurningTime);
                                QueryBuilder::for($detail)
                                    ->insert($detail->attributesToArray());
                            }
                        }
                    }
                }
                $rows = $this->findById($priceEstimateId);
                $rows->asset = isset($objects['assets']) ?? null;

                // if (isset($objects['asset_general']) && !empty($objects['asset_general'])) {
                //     CommonService::getAssetPriceTotal($rows);
                // }

                return $rows;
            } catch (Exception $exception) {
                Log::error($exception);
                throw $exception;
            }
        });
    }

    /**
     * @param $id
     * @param array $objects
     * @return object
     * @throws Throwable
     */
    public function updatePriceEstimate($id, array $objects): object
    {
        if (isset($objects['created_by']) && CommonService::isJSON($objects['created_by'])) {
            $objects['created_by'] = json_decode($objects['created_by']);
            $objects['created_by'] = $objects['created_by']->id;
        }
        return DB::transaction(function () use ($id, $objects) {
            try {
                $objects['created_by'] = is_array($objects['created_by']) ? $objects['created_by']['id'] : $objects['created_by'];
                $objects["updated_at"] = date("Y-m-d H:i:s");
                if (!isset($objects['asset_general']) || empty($objects['asset_general'])) {
                    $objects['status'] = 1;
                }
                $priceEstimate = new PriceEstimate($objects);
                $priceEstimateId = $id;
                $priceEstimate->newQuery()->updateOrInsert(['id' => $id], $priceEstimate->attributesToArray());

                PriceEstimateProperty::query()->where('priceEstimate_id', '=', $priceEstimateId)->delete();
                if (isset($objects['properties'])) {
                    foreach ($objects['properties'] as $propertyData) {
                        $propertyData['asset_priceEstimate_id'] = $priceEstimateId;
                        $propertyData['priceEstimate_id'] = $priceEstimateId;
                        $property = new PriceEstimateProperty($propertyData);
                        $propertyId = QueryBuilder::for($property)
                            ->insertGetId($property->attributesToArray());
                        if (isset($propertyData['property_detail'])) {
                            foreach ($propertyData['property_detail'] as $propertyDetail) {
                                $propertyDetail['priceEstimate_property_id'] = $propertyId;
                                $propertyDetail['k_rate'] = isset($propertyDetail['k_rate']) ? intval($propertyDetail['k_rate']) : 0;
                                $propertyDetail['planning_area'] = isset($propertyDetail['planning_area']) ? floatval($propertyDetail['planning_area']) : 0;
                                $propertyDetail['estimation_value'] = isset($propertyDetail['estimation_value']) ? doubleval($propertyDetail['estimation_value']) : 0;
                                $propertyDetail = array_map(function ($v) {
                                    return (is_null($v)) ? "" : $v;
                                }, $propertyDetail);
                                $detail = new PriceEstimatePropertyDetail($propertyDetail);
                                QueryBuilder::for($detail)
                                    ->insert($detail->attributesToArray());
                            }
                        }

                        if (isset($propertyData['property_turning_time'])) {
                            foreach ($propertyData['property_turning_time'] as $propertyTurningTime) {
                                $propertyTurningTime['priceEstimate_property_id'] = $propertyId;
                                $detail = new PriceEstimatePropertyTurningTime($propertyTurningTime);
                                QueryBuilder::for($detail)
                                    ->insert($detail->attributesToArray());
                            }
                        }
                    }
                }

                $isAssetGeneralChange = false;
                $oldPriceEstimate = PriceEstimate::where('id', $id)->first();
                $oldAssetGeneralIds = [];
                if (isset($oldPriceEstimate->assetGeneral)) {
                    foreach ($oldPriceEstimate->assetGeneral as $assetGeneral) {
                        $oldAssetGeneralIds[$assetGeneral->id] = 1;
                    }
                }

                $this->comparisonFactor($priceEstimateId, $objects, $oldAssetGeneralIds);

                $rows = $this->findById($priceEstimateId);
                $rows->asset = $objects['assets'] ?? null;

                // if (isset($objects['asset_general']) && !empty($objects['asset_general'])) {
                //     CommonService::getAssetPriceTotal($rows);
                // }


                return $rows;
            } catch (Exception $exception) {
                Log::error($exception);
                throw $exception;
            }
        });
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deletePriceEstimate($id)
    {
        return $this->model->query()
            ->where('id', $id)
            ->delete();
    }

    /**
     * @param $name
     * @return Builder|Model|object|null
     */
    public function findByName($name)
    {
        $query = 'name ilike ' . "'%" . $name . "%'";

        return $this->model->query()
            ->whereRaw($query)
            ->first();
    }

    /**
     * @param $data
     * @return mixed
     */
    public function indexData($data)
    {
        $assetVersion = null;
        if (isset($data->version)) {
            foreach ($data->version as $version) {
                $assetVersion = ($assetVersion == null) ? $version : ($assetVersion->id > $version->id ? $assetVersion : $version);
            }
        }
        $client = ClientBuilder::create()
            ->setSSLVerification(false)
            ->setHosts(config('elasticquent.config.hosts'))
            ->build();
        $request = [
            'index' => env('ELASTIC_APPRAISE_VERSION_INDEX'),
            'type' => '_doc',
            'id' => trim($data->id) . '-' . ($assetVersion->version ?? 1),
            'body' => json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        ];
        $client->index($request);
        return $data;
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
        $search_result = PriceEstimateVersion::searchByQuery($array, null, null, 1, 0, null);
        if (isset($search_result[0])) {
            return $search_result[0];
        } else {
            return null;
        }
    }

    public function getPriceEstimateDataFull($priceEstimateId)
    {
        $check = $this->checkAuthorization($priceEstimateId);
        if (!empty($check))
            return $check;
        $select = ['id', 'step', 'status', 'coordinates', 'asset_type_id', 'created_by', 'land_no', 'doc_no', 'address_number', 'appraise_asset', 'filter_year', 'updated_at', 'created_at', 'appraise_id', 'apartment_asset_id', 'pre_certificate_id'];
        $with = [
            'createdBy:id,name',
            'lastVersion',
            'apartmentProperties',
            'apartmentProperties.floor',
        ];
        $result = PriceEstimate::with($with)
            ->select($select)
            ->where('id', $priceEstimateId);

        $result = $result->first();
        unset($result->pic);


        $result->append('general_infomation');
        $result->append('map_img');
        $result->append('distance_max');
        $version = PriceEstimateVersionService::getVersionPriceEstimate($priceEstimateId);
        $result['max_version'] = $version;
        $result->append(
            'assets_general'
        );
        $result->append(
            'final_estimate'
        );
        if ($result->asset_type_id !== 39) {
            $result->append('land_details');
            $result->append('total_area');
            $result->append('planning_area');
            $result->append('traffic_infomation');
            $geo = PriceEstimateProperty::where('price_estimate_id', $priceEstimateId)->first();
            $result['traffic_infomation'] = $geo;
        }

        return $result;
    }

    public function getPriceEstimateDataFullForPreCertificate($request)
    {
        $page = $request->query('page', 1);
        $limit = $request->query('limit', 15);
        $status = $request->query('status');
        $popup = $request->query('popup');
        $preCertificateId = $request->query('pre_certificate_id');

        $select = ['id', 'step', 'status', 'coordinates', 'asset_type_id', 'created_by', 'land_no', 'doc_no', 'address_number', 'appraise_asset', 'filter_year', 'updated_at', 'created_at', 'appraise_id', 'apartment_asset_id'];
        $user = CommonService::getUser();
        $with = [
            'createdBy:id,name',
            'assetType',
            'landFinalEstimate',
            'landFinalEstimate.lands',
            'landFinalEstimate.apartmentFinals'
        ];
        $result = PriceEstimate::with($with)
            ->select($select)
            ->where(function ($query) use ($preCertificateId) {
                $query->whereNull('pre_certificate_id')
                    ->orWhere('pre_certificate_id', $preCertificateId);
            })
            ->where('step', 3)
            ->where('created_by', $user->id)
            ->orderBy('updated_at', 'desc')
            ->paginate($limit, ['*'], 'page', $page);

        return $result;
    }


    private function checkAuthorization($id)
    {
        // $check = null;
        // if ($this->model->query()->where('id', $id)->exists()) {
        //     $user = CommonService::getUser();
        //     $role = $user->roles->last();
        //     $result = $this->model->query()->where('id', $id);
        //     $userId = $user->id;
        //     if (($role->name !== 'ROOT_ADMIN' && $role->name !== 'SUB_ADMIN')) {
        //         $result = $result->where('created_by', $userId);
        //     }
        //     $result = $result->first();
        //     if (empty($result))
        //         $check = ['message' => 'Bạn không có quyền ở TSSB ' . $id, 'exception' => '', 'statusCode' => 403];
        // } else {
        //     $check = ['message' => ErrorMessage::PE_CHECK_EXIT . ' ' . $id, 'exception' => '', 'statusCode' => 403];
        // }
        // return $check;
    }
    private function getComparisonFactors(int $priceEstimateId)
    {
        $result = [];
        // if (PriceEstimateComparisonFactor::where('price_estimate_id', $priceEstimateId)->exists()) {
        //     $select = [
        //         'type'
        //     ];
        //     $with = [];
        //     $comparisonFators = PriceEstimateComparisonFactor::with($with)
        //         ->select($select)
        //         ->where(['price_estimate_id' => $priceEstimateId])
        //         ->where(['status' => 1])
        //         ->where('type', '!=', 'yeu_to_khac')
        //         ->distinct()
        //         ->get();
        //     $stt = 0;
        //     foreach ($comparisonFators as $item) {
        //         $result[$stt]  = $item['type'];
        //         $stt++;
        //     }
        // }

        return $result;
    }

    //Step 1 - Post Data
    public function postGeneralInfomation(array $objects, int $id = null)
    {
        DB::beginTransaction();
        try {
            $user = CommonService::getUser();
            $totalArea = $objects['total_area'];
            $planningArea = $objects['planning_area'];
            $isDuplicate = $this->checkDuplicateLandTypePurpose($totalArea);
            if ($isDuplicate) {
                return ['message' => 'Trùng mục đích sử dụng. Vui lòng kiểm tra lại', 'exception' => ''];
            }
            $check = $this->checkPlanningArea1($totalArea, $planningArea);
            if (isset($check))
                return $check;
            #check $id , nếu null => tạo mới , có => update
            if (isset($id)) {
                # block xác thực
                $check = $this->beforeSave($id);
                if (isset($check)) {
                    return $check;
                }

                $generalInfomation = $objects['general_infomation'];
                // $pictureInfomation = $objects['picture_infomation'];
                // $economicInfomation = $objects['economic_infomation'];
                $trafficInfomation = $objects['traffic_infomation'];
                // $geographical_location = $objects['geographical_location'];
                $priceEstimateId = $id;

                PriceEstimate::where('id', $priceEstimateId)->update([
                    'appraise_asset' => $generalInfomation['appraise_asset'],
                    'asset_type_id' => $generalInfomation['asset_type_id'],
                    'coordinates' => $generalInfomation['coordinates'],
                    'distance_id' => $generalInfomation['distance_id'],
                    'district_id' => $generalInfomation['district_id'],
                    'province_id' => $generalInfomation['province_id'],
                    'street_id' => $generalInfomation['street_id'],
                    'ward_id' => $generalInfomation['ward_id'],
                    'full_address' => $generalInfomation['full_address'],
                    'full_address_street' => $generalInfomation['full_address_street'],
                    'address_number' => $generalInfomation['address_number'],
                    'doc_no' => $generalInfomation['doc_no'],
                    'land_no' => $generalInfomation['land_no'],

                ]);

                // $this->update_activity_log($priceEstimateId, $this->model, $generalInfomation, $log, $logName);

                // $priceEstimate = new PriceEstimate($generalInfomation);
                //
                // $priceEstimate->newQuery()->updateOrInsert(['id' => $id], $priceEstimate->attributesToArray());
                #Delete priceEstimate pic - add new
                // PriceEstimatePic::query()->where('price_estimate_id', '=', $priceEstimateId)->delete();
                // if (isset($pictureInfomation)) {
                //     foreach ($pictureInfomation as $PriceEstimatePic) {
                //         $PriceEstimatePic['price_estimate_id'] = $priceEstimateId;
                //         $pic = new PriceEstimatePic($PriceEstimatePic);
                //         QueryBuilder::for($pic)
                //             ->insert($pic->attributesToArray());
                //     }
                // }
                # delete turning time ,  update priceEstimate properties - insert turning time
                $propertieId = PriceEstimateProperty::where('price_estimate_id', $priceEstimateId)->get()->first()->id;
                // PriceEstimatePropertyTurningTime::query()->where('price_estimate_property_id', '=', $propertieId)->delete();
                if (PriceEstimatePropertyTurningTime::where(['price_estimate_property_id' => $propertieId])->exists()) {
                    PriceEstimatePropertyTurningTime::where(['price_estimate_property_id' => $propertieId])->delete();
                }
                if (isset($trafficInfomation)) {
                    if (isset($propertieId)) {
                        PriceEstimateProperty::where('price_estimate_id', $priceEstimateId)->where('id', $propertieId)->update([
                            'front_side' => $trafficInfomation['front_side'],
                            // 'individual_road' => $trafficInfomation['individual_road'],
                            'main_road_length' => $trafficInfomation['main_road_length'] ?? null,
                            'material_id' => $trafficInfomation['material_id'] ?? null,
                            // 'two_sides_land' => isset($trafficInfomation['two_sides_land']) ? $trafficInfomation['two_sides_land'] : null,
                            'description' => $trafficInfomation['description'],
                            // 'description_width' => $trafficInfomation['description_width'],
                            // 'description_before' => $trafficInfomation['description_before'],
                            // 'property_advantages' => $trafficInfomation['property_advantages'],
                            // 'other_prefix' => $trafficInfomation['other_prefix'],
                            // 'property_disadvantages' => $trafficInfomation['property_disadvantages'],
                            // 'advantages' => $trafficInfomation['advantages'],
                            // 'disadvantages' => $trafficInfomation['disadvantages'],
                            // 'geographical_location' => $geographical_location
                        ]);
                    } else {
                        $trafficInfomation['price_estimate_id'] = $priceEstimateId;
                        $trafficInfomation['description'] = $generalInfomation['description'];
                        // $trafficInfomation['geographical_location'] = $geographical_location;
                        $priceEstimateProperties = new PriceEstimateProperty($trafficInfomation);
                        $propertieId = QueryBuilder::for($priceEstimateProperties)
                            ->insertGetId($priceEstimateProperties->attributesToArray());
                    }
                    if ($trafficInfomation['front_side'] == 0) {
                        if (isset($trafficInfomation['property_turning_time'])) {
                            foreach ($trafficInfomation['property_turning_time'] as $turningTime) {
                                $turningTime['price_estimate_property_id'] =  $propertieId;
                                $propertyTurningTime = new PriceEstimatePropertyTurningTime($turningTime);
                                QueryBuilder::for($propertyTurningTime)
                                    ->insert($propertyTurningTime->attributesToArray());
                            }
                        } else {
                            $data = ['message' => ErrorMessage::PE_CHECK_TUNNING, 'exception' =>  ''];
                            return $data;
                        }
                    }
                }
                // if (isset($economicInfomation)) {
                //     if (isset($propertieId)) {
                //         PriceEstimateProperty::where('price_estimate_id', $priceEstimateId)->where('id', $propertieId)->update([
                //             'business_id' => $economicInfomation['business_id'],
                //             'social_security_id' => $economicInfomation['social_security_id'],
                //             'feng_shui_id' => $economicInfomation['feng_shui_id'],
                //             'zoning_id' => $economicInfomation['zoning_id'],
                //             'condition_id' => $economicInfomation['condition_id'],
                //         ]);
                //     } else {
                //         $economicInfomation['price_estimate_id'] = $priceEstimateId;
                //         $priceEstimateProperties = new PriceEstimateProperty($economicInfomation);
                //         QueryBuilder::for($priceEstimateProperties)
                //             ->insert($priceEstimateProperties->attributesToArray());
                //     }
                // }
                //kiểm tra chi tiết về công trình khi thay đổi loại - đất trống - xoá thông tin công trình xây dựng
                // $assetTypeId = $generalInfomation['asset_type_id'];
                // $assetTypeDescription = Dictionary::where(['id' => $assetTypeId])->select(['description'])->first();

                // if ($assetTypeDescription['description'] == 'ĐẤT TRỐNG') {
                //     if (PriceEstimateTangibleAsset::where('price_estimate_id', '=', $id)->exists()) {
                //         PriceEstimateTangibleAsset::where('price_estimate_id', '=', $id)->delete();
                //     }
                //     if (ConstructionCompany::where('price_estimate_id', '=', $id)->exists()) {
                //         ConstructionCompany::where('price_estimate_id', '=', $id)->delete();
                //     }
                //     if (PriceEstimateTangibleComparisonFactor::where('price_estimate_id', $priceEstimateId)->exists())
                //         PriceEstimateTangibleComparisonFactor::where('price_estimate_id', $priceEstimateId)->delete();
                // }
                // update step, status
                $this->updatePriceEstimateStep($priceEstimateId, 1);
                $this->postLandDetailInfomation($objects, $id);

                $data = PriceEstimate::where('id', $priceEstimateId)->first();
                $this->CreateActivityLog($data, $data, 'update_data', 'cập nhật thông tin chung');
            } else {
                $generalInfomation = $objects['general_infomation'];
                // $pictureInfomation = $objects['picture_infomation'];
                // $economicInfomation = $objects['economic_infomation'];
                $trafficInfomation = $objects['traffic_infomation'];
                // $geographical_location = $objects['geographical_location'];
                $generalInfomation['created_by'] = $user->id;
                $generalInfomation['status'] = 1;
                $generalInfomation['front_side'] = $trafficInfomation['front_side'];
                $generalInfomation['step'] = 1;
                // $realEstateId = $this->createRealEstate($generalInfomation);
                // $generalInfomation['real_estate_id'] = $realEstateId;

                $priceEstimate = new PriceEstimate($generalInfomation);
                $appraiseArr = $priceEstimate->attributesToArray();
                $data = $this->model->query()->create($appraiseArr);
                // $this->model->query()->where('id', $data->id)->update(['id' => $realEstateId, 'real_estate_id' => $realEstateId]);
                $priceEstimateId = $data->id;
                if (isset($trafficInfomation)) {
                    $trafficInfomation['price_estimate_id'] = $priceEstimateId;
                    // $trafficInfomation['two_sides_land'] = isset($trafficInfomation['two_sides_land']) ? $trafficInfomation['two_sides_land'] : null;
                    // $trafficInfomation['geographical_location'] = $geographical_location;

                    $priceEstimateProperties = new PriceEstimateProperty($trafficInfomation);
                    $propertieId =   QueryBuilder::for($priceEstimateProperties)
                        ->insertGetId($priceEstimateProperties->attributesToArray());

                    if ($trafficInfomation['front_side'] == 0) {
                        if (isset($trafficInfomation['property_turning_time'])) {
                            foreach ($trafficInfomation['property_turning_time'] as $turningTime) {
                                $turningTime['price_estimate_property_id'] =  $propertieId;
                                $propertyTurningTime = new PriceEstimatePropertyTurningTime($turningTime);
                                QueryBuilder::for($propertyTurningTime)
                                    ->insert($propertyTurningTime->attributesToArray());
                            }
                        } else {
                            $data = ['message' => ErrorMessage::PE_CHECK_TUNNING, 'exception' =>  ''];
                            return $data;
                        }
                    }
                }
                // if (isset($economicInfomation)) {
                //     if (isset($propertieId)) {
                //         PriceEstimateProperty::where('price_estimate_id', $priceEstimateId)->where('id', $propertieId)->update([
                //             'business_id' => $economicInfomation['business_id'],
                //             'social_security_id' => $economicInfomation['social_security_id'],
                //             'feng_shui_id' => $economicInfomation['feng_shui_id'],
                //             'zoning_id' => $economicInfomation['zoning_id'],
                //             'condition_id' => $economicInfomation['condition_id'],
                //         ]);
                //     } else {
                //         $economicInfomation['price_estimate_id'] = $priceEstimateId;
                //         $priceEstimateProperties = new PriceEstimateProperty($economicInfomation);
                //         QueryBuilder::for($priceEstimateProperties)
                //             ->insert($priceEstimateProperties->attributesToArray());
                //     }
                // }
                # CẬP NHẬT THÔNG TIN CHUNG - KHÔNG CÓ ID -> TẠO MỚI
                # activity-log
                $this->postLandDetailInfomation($objects, $priceEstimateId);
                $data = PriceEstimate::where('id', $priceEstimateId)->first();
                $this->CreateActivityLog($data, $data, 'create', 'tạo mới');
            }
            $this->processAfterSave($priceEstimateId);
            DB::commit();
            return $this->getInfomation($priceEstimateId);
        } catch (Exception $ex) {
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' =>  $ex->getMessage()];
            return $data;
            DB::rollBack();
        }
    }
    private function checkDuplicateLandTypePurpose($data)
    {
        $check = [];
        foreach ($data as $item) {
            if (empty($check)) {
                $check[$item['land_type_purpose_id']] = $item['land_type_purpose_id'];
            } else {
                if (isset($check[$item['land_type_purpose_id']])) {
                    return true;
                }
            }
        }
        return false;
    }
    private function checkPlanningArea1($totalArea, $planningArea)
    {
        $result = null;
        foreach ($totalArea as $item) {
            $landTypeId = $item['land_type_purpose_id'];
            $total = $item['total_area'];
            $planArea = 0;
            foreach ($planningArea as $y) {
                if ($y['land_type_purpose_id'] == $landTypeId) {
                    $planArea = $planArea + intval($y['planning_area']);
                }
            }
            if ($planArea > $total) {
                $result = ['message' => 'Diện tích quy hoạch ' . strtolower($item['land_type_purpose']['description']) . ' không được lớn hơn diện sử dụng', 'exception' => ''];
            }
        }
        return $result;
    }
    public function postLandDetailInfomation(array $objects, int $priceEstimateId)
    {
        // DB::beginTransaction();
        // try {
        //     $check = $this->beforeSave($priceEstimateId);
        //     if (isset($check)) {
        //         return $check;
        //     }
        //     // dd(1);
        //     // $landDetail = $objects['land_details'];
        $totalArea = $objects['total_area'];
        $planningArea = $objects['planning_area'];
        // $isDuplicate = $this->checkDuplicateLandTypePurpose($totalArea);
        // if ($isDuplicate) {
        //     return ['message' => 'Trùng mục đích sử dụng. Vui lòng kiểm tra lại', 'exception' => ''];
        // }
        // $check = $this->checkPlanningArea1($totalArea, $planningArea);

        // if (isset($check))
        //     return $check;
        if (PriceEstimateProperty::where('price_estimate_id', '=', $priceEstimateId)->exists()) {
            $propertieId = PriceEstimateProperty::where('price_estimate_id', '=', $priceEstimateId)->first()->id;
            // if (isset($landDetail)) {
            //     PriceEstimateProperty::where('id', $propertieId)->update([
            //         'coordinates' => $landDetail['coordinates'],
            //         'front_side_width' => $landDetail['front_side_width'],
            //         'insight_width' => $landDetail['insight_width'],
            //         'land_shape_id' => $landDetail['land_shape_id'],
            //         'appraise_land_sum_area' => $landDetail['appraise_land_sum_area'],
            //         'tong_phu_hop' => $landDetail['tong_phu_hop'],
            //         'tong_cong_nhan' => $landDetail['tong_cong_nhan'],
            //         'tong_thuc_te' => $landDetail['tong_thuc_te'],
            //         'legal_id' => 1,
            //     ]);
            //     PriceEstimate::where('id', $priceEstimateId)->update([
            //         'topographic_id' => isset($landDetail['topographic']['topographic_id']) ? $landDetail['topographic']['topographic_id'] : null,
            //     ]);
            // }
            if (PriceEstimatePropertyDetail::where(['price_estimate_property_id' => $propertieId])->exists()) {
                PriceEstimatePropertyDetail::where(['price_estimate_property_id' => $propertieId])->delete();
            }
            $PriceEstimatePropertyDetailData = [];
            $isMain = false;
            if (isset($totalArea) && count($totalArea) > 0) {
                foreach ($totalArea as $total) {
                    if (!$isMain) {
                        if ($total['is_transfer_facility'])
                            $isMain = true;
                    } else {
                        if ($total['is_transfer_facility']) {
                            DB::rollBack();
                            $data = ['message' => ErrorMessage::APPRAISE_CHECK_MULTIPLE_MDSDD, 'exception' =>  ''];
                            return $data;
                        }
                    }
                    $PriceEstimatePropertyDetailData[] = [
                        'price_estimate_property_id' => $propertieId,
                        'land_type_purpose_id' => $total['land_type_purpose_id'],
                        'is_transfer_facility' => $total['is_transfer_facility'],
                        'total_area' =>  $total['total_area'],
                        'main_area' => $total['total_area'],
                        // 'circular_unit_price' => 0,
                        // 'position_type_id' => null,
                        'planning_area' => 0,
                        'type_zoning' => '',
                        // 'is_zoning' => false,
                    ];
                }
                // $key =array_search(61, array_column($PriceEstimatePropertyDetailData, 'land_type_purpose_id'));
                if (isset($planningArea) && count($planningArea) > 0) {
                    foreach ($planningArea as $planning) {
                        $key = array_search($planning['land_type_purpose_id'], array_column($PriceEstimatePropertyDetailData, 'land_type_purpose_id'));
                        if (($key === false)) {
                            $PriceEstimatePropertyDetailData[] = [
                                'price_estimate_property_id' => $propertieId,
                                'land_type_purpose_id' => $planning['land_type_purpose_id'],
                                'is_transfer_facility' => false,
                                'total_area' =>  $planning['planning_area'],
                                'main_area' => 0,
                                // 'circular_unit_price' => 0,
                                'position_type_id' => null,
                                'planning_area' =>  $planning['planning_area'],
                                'type_zoning' =>  $planning['type_zoning'],
                                // 'is_zoning' =>  true,
                                // 'extra_planning' => [
                                //     [
                                //         'land_type_purpose_id' => $planning['land_type_purpose_id'],
                                //         'planning_area' =>  $planning['planning_area'],
                                //         'type_zoning' =>  $planning['type_zoning'],
                                //         'price_estimate_property_id' => $propertieId,
                                //         'pp_tinh' =>  $planning['pp_tinh'],
                                //         'he_so' =>  $planning['he_so'],
                                //     ]
                                // ]
                            ];
                        } else {
                            $mainArea = $PriceEstimatePropertyDetailData[$key]['main_area'] - $planning['planning_area'];
                            $planningArea = $PriceEstimatePropertyDetailData[$key]['planning_area'] + $planning['planning_area'];
                            $PriceEstimatePropertyDetailData[$key]['main_area'] =  $mainArea > 0 ? $mainArea : 0;
                            $PriceEstimatePropertyDetailData[$key]['planning_area'] =  $planningArea;
                            $PriceEstimatePropertyDetailData[$key]['type_zoning'] =  $planning['type_zoning'];
                            // $PriceEstimatePropertyDetailData[$key]['is_zoning'] =  true;
                            // $PriceEstimatePropertyDetailData[$key]['extra_planning'][] = [
                            //     'land_type_purpose_id' => $planning['land_type_purpose_id'],
                            //     'planning_area' =>  $planning['planning_area'],
                            //     'type_zoning' =>  $planning['type_zoning'],
                            //     'price_estimate_property_id' => $propertieId,
                            //     'pp_tinh' =>  $planning['pp_tinh'],
                            //     'he_so' =>  $planning['he_so'],
                            // ];
                        }
                    }
                    // dd($PriceEstimatePropertyDetailData);
                }
                // if (isset($thamkhaoArea) && count($thamkhaoArea) > 0) {
                //     foreach ($thamkhaoArea as $planningtk) {
                //         $key = array_search($planningtk['land_type_purpose_id'], array_column($PriceEstimatePropertyDetailData, 'land_type_purpose_id'));
                //         if (($key !== false)) {
                //             $PriceEstimatePropertyDetailData[$key]['thamkhao_planning'][] = [
                //                 'land_type_purpose_id' => $planningtk['land_type_purpose_id'],
                //                 'thamkhao_area' =>  $planningtk['thamkhao_area'],
                //                 'type_zoning' =>  $planningtk['type_zoning'],
                //                 'price_estimate_property_id' => $propertieId,
                //                 'pp_tinh' =>  $planningtk['pp_tinh'],
                //                 'he_so' =>  $planningtk['he_so'],
                //             ];
                //         } else {
                //             $PriceEstimatePropertyDetailData[] = [
                //                 'price_estimate_property_id' => $propertieId,
                //                 'land_type_purpose_id' => $planningtk['land_type_purpose_id'],
                //                 'is_transfer_facility' => false,
                //                 'total_area' =>  0,
                //                 'main_area' => 0,
                //                 'circular_unit_price' => 0,
                //                 'position_type_id' => null,
                //                 'planning_area' =>  0,
                //                 'type_zoning' =>  $planningtk['type_zoning'],
                //                 'is_zoning' =>  true,
                //                 'thamkhao_planning' => [
                //                     [
                //                         'land_type_purpose_id' => $planningtk['land_type_purpose_id'],
                //                         'thamkhao_area' =>  $planningtk['thamkhao_area'],
                //                         'type_zoning' =>  $planningtk['type_zoning'],
                //                         'price_estimate_property_id' => $propertieId,
                //                         'pp_tinh' =>  $planningtk['pp_tinh'],
                //                         'he_so' =>  $planningtk['he_so'],
                //                     ]
                //                 ]
                //             ];
                //         }
                //     }
                //     // dd($key, $PriceEstimatePropertyDetailData);
                // }
                if (!$isMain) {
                    DB::rollBack();
                    $data = ['message' => ErrorMessage::PE_CHECK_MDSDD, 'exception' =>  ''];
                    return $data;
                }
                // if (isset($ubndPrice)) {
                //     foreach ($ubndPrice as $UNBD) {
                //         $key = array_search($UNBD['land_type_purpose_id'], array_column($PriceEstimatePropertyDetailData, 'land_type_purpose_id'));
                //         if (!($key === false)) {
                //             $PriceEstimatePropertyDetailData[$key]['position_type_id'] =  $UNBD['position_type_id'];
                //             $PriceEstimatePropertyDetailData[$key]['circular_unit_price'] =  $UNBD['circular_unit_price'];
                //             if (isset($UNBD['interpretation']) && !empty($UNBD['interpretation'])) {
                //                 $PriceEstimatePropertyDetailData[$key]['interpretation'] =  $UNBD['interpretation'];
                //             }
                //         }
                //     }
                // } else {
                //     DB::rollBack();
                //     $data = ['message' => ErrorMessage::APPRAISE_CHECK_UBND_PRICE, 'exception' =>  ''];
                //     return $data;
                // }
                $sumArea = 0.00;
                // dd($PriceEstimatePropertyDetailData);
                foreach ($PriceEstimatePropertyDetailData as $data) {
                    // if (!isset($data['position_type_id'])) {
                    //     DB::rollBack();
                    //     $data = ['message' => ErrorMessage::APPRAISE_CHECK_UBND_PRICE, 'exception' =>  ''];
                    //     return $data;
                    // }
                    $sumArea  = $sumArea + $data['total_area'];
                    // if (isset($data['extra_planning'])) {
                    //     $data['extra_planning'] = json_encode($data['extra_planning']);
                    // }
                    // if (isset($data['thamkhao_planning'])) {
                    //     $data['thamkhao_planning'] = json_encode($data['thamkhao_planning']);
                    // }

                    $propertieDetail = new PriceEstimatePropertyDetail($data);
                    QueryBuilder::for($propertieDetail)
                        ->insert($propertieDetail->attributesToArray());
                }
                PriceEstimateProperty::where('id', $propertieId)->update([
                    'appraise_land_sum_area' => $sumArea
                ]);
            } else {
                DB::rollBack();
                $data = ['message' => ErrorMessage::PE_CHECK_MAIN_EREA, 'exception' =>  ''];
                return $data;
            }
        } else {
            DB::rollBack();
            $data = ['message' => 'Vui lòng nhập thông tin giao thông, đặc điểm kinh tế ở bước 1', 'exception' =>  ''];
            return $data;
        }
        // if (isset($real_estate)) {
        //     // $result = RealEstate::find($real_estate['id']);
        //     $result = RealEstate::find($priceEstimateId);
        //     if ($result) {
        //         $result->update($real_estate);
        //     }
        // }
        //     $data = PriceEstimate::where('id', $priceEstimateId)->first();

        //     # activity-log
        //     $this->CreateActivityLog($data, $data, 'update_data', 'cập nhật dữ liệu quyền sử dụng đất');
        //     // $this->updateAppraiseStep($priceEstimateId, 2);

        //     $this->processAfterSave($priceEstimateId);
        //     DB::commit();
        //     // return $this->getLandInfomation($priceEstimateId);
        //     return $priceEstimateId;
        // } catch (Exception $ex) {
        //     DB::rollBack();
        //     Log::error($ex);
        //     $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' =>  $ex->getMessage()];
        //     return $data;
        // }
    }
    private function beforeSave(int $priceEstimateId)
    {
        if (PriceEstimate::where('id', $priceEstimateId)->exists()) {
            $checkCreatedBy = $this->checkUser($priceEstimateId, 'SAVE');
            if (isset($checkCreatedBy)) {
                return $checkCreatedBy;
            }
            $result = null;
            $status = [4, 5];
            $data = PriceEstimate::where('id', $priceEstimateId)->whereIn('status', $status)->get(['id', 'status'])->first();
            if (isset($data)) {
                $result = ['message' => ErrorMessage::PE_CHECK_STATUS_FOR_UPDATE . $data->status_text, 'exception' => ''];
            }
        } else {
            $result = ['message' => ErrorMessage::PE_CHECK_EXIT . $priceEstimateId, 'exception' => ''];
        }
        return $result;
    }

    private function checkUser(int $priceEstimateId, string $type = 'VIEW')
    {
        $result = null;
        $user = CommonService::getUser();
        if ($user->hasRole(['ROOT_ADMIN', 'SUPER_ADMIN', 'SUB_ADMIN'])) {
            return $result;
        }
        $data = PriceEstimate::where('id', $priceEstimateId)->where('created_by', $user->id)->first();
        if (!isset($data)) {
            if ($type == 'VIEW') {
                if ($user->roles->last()->name == 'USER') {
                    $result = ['message' => ErrorMessage::PE_CHECK_VIEW . $priceEstimateId . '.', 'exception' => ''];
                }
            } else {
                $result = ['message' => ErrorMessage::PE_CHECK_UPDATE . $priceEstimateId . '.', 'exception' => ''];
            }
        }
        return $result;
    }
    public function getInfomation(int $id)
    {
        $generalInfomation = [];
        $traffic =  [];
        if (PriceEstimate::where('id', '=', $id)->exists()) {
            // $propertieId = PriceEstimateProperty::where('price_estimate_id', '=',$id)->first()->id;
            $generalInfomation = $this->getGeneralInfomation($id);
            $traffic =  $this->getTraffic($id);
        }
        $result = array_merge(
            ['general_infomation' => $generalInfomation],
            ['traffic_infomation' => $traffic],
            // ['economic_infomation' => $economic],
            // ['picture_infomation' => $pic]
        );
        return $result;
    }
    private function getGeneralInfomation(int $id)
    {
        $select = [
            'id',
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
            'appraise_asset',
            'created_by',
            'updated_at',
            'step'
        ];
        $with = [
            'createdBy:id,name',
            'assetType:id,description',
            'distance:id,name,street_id',
            'province:id,name',
            'district:id,name',
            'ward:id,name',
            'street:id,name'
        ];
        $result = $this->model->with($with)
            ->select($select)
            ->where(['id' => $id])
            ->get()->first();

        return $result;
    }

    private function getTraffic(int $id)
    {
        $select = [
            'id',
            'price_estimate_id',
            'description',
            'front_side',
            'main_road_length',
            'material_id',
        ];
        $with = [
            'material:id,type,description',
            'propertyTurningTime:id,price_estimate_property_id,main_road_length,turning,material_id,main_road_distance',
            'propertyTurningTime.material:id,type,description'
        ];
        $result = PriceEstimateProperty::with($with)
            ->select($select)
            ->where(['price_estimate_id' => $id])
            ->get()->first();
        return $result;
    }
    private function updatePriceEstimateStep(int $priceEstimateId, int $step)
    {
        if (PriceEstimate::where('id', $priceEstimateId)->exists()) {
            // $priceEstimate = PriceEstimate::where('id', $priceEstimateId)->first();
            // if ($priceEstimate->status === 3) {
            //     $status = 3;
            // } else {
            //     if ($step == 6 || $step == 7) {
            //         $status = 2;
            //     } else {
            //         $status = 1;
            //         if (!empty($priceEstimate->appraiseHasAssets))
            //             $this->resetDataStep6($priceEstimateId);
            //     }
            // }
            $update = [
                'step' => $step,
                // 'status' => $status
            ];
            // if ($priceEstimate->status != $status) {
            //     $edited = $priceEstimate;
            //     $edited->step = $step;
            //     $this->CreateActivityLog($priceEstimate, $edited, 'update_status', 'cập nhật trạng thái');
            // }
            PriceEstimate::where('id', $priceEstimateId)->update($update);

            // $this->updateRealEstates($priceEstimateId);
        }
        return $update;
    }
    private function processAfterSave($priceEstimateId)
    {
        $this->createPriceEstimateVersion($priceEstimateId);
    }
    private function createPriceEstimateVersion($priceEstimateId)
    {
        $priceEstimate = $this->model->query()->with('version')->where('id', $priceEstimateId)->whereHas('version')->first();
        // $priceEstimate = $this->model->query()->with('version')->where('id', $priceEstimateId)->first();
        $data = [];
        if (!empty($priceEstimate)) {
            if (!empty($priceEstimate->certificate_id)) {
                $max = $priceEstimate->version->max('version');
                $data = [
                    'price_estimate_id' => $priceEstimateId,
                    'status' => 1,
                    'version' => $max + 1
                ];
            }
        } else {
            $data = [
                'price_estimate_id' => $priceEstimateId,
                'status' => 1,
                'version' => 1
            ];
        }
        if (!empty($data)) {
            PriceEstimateVersion::query()->create($data);
        }
    }


    public function updateStep2(array $objects, int $priceEstimateId)
    {
        DB::beginTransaction();
        try {
            $check = $this->beforeSave($priceEstimateId);
            if (isset($check)) {
                return $check;
            }

            // $user = CommonService::getUser();
            // $comparisonFactors = $objects['comparison_factor'];
            // $dictionaries = $this->findAllAppraiseDictionaries();
            // $appraiseData = $this->getAppraiseDataComparison($priceEstimateId);
            // $appraise = $this->model->query()->where('id', $priceEstimateId)->first();
            // $baseUBNDPrice = $this->getBaseUBNDPrice($priceEstimateId);
            // dd(json_encode($baseUBNDPrice) );
            // $cpcmdsd = 0;
            if (isset($objects['map_img'])) {
                $link  = $objects['map_img'];
                $type_id = 153;
                PriceEstimatePic::query()->where('price_estimate_id', '=', $priceEstimateId)->where('type_id', $type_id)->delete();
                $map = [
                    'price_estimate_id' => $priceEstimateId,
                    'link' => $objects['map_img'],
                    'type_id' => $type_id,
                ];
                $pic = new PriceEstimatePic($map);
                QueryBuilder::for($pic)
                    ->insert($pic->attributesToArray());
            }

            if (isset($objects['assets_general'])) {
                if (count($objects['assets_general']) > 3) {
                    DB::rollBack();
                    $data = ['message' => ErrorMessage::PE_CHECK_ASSET_NUMBER, 'exception' =>  ''];
                    return $data;
                }

                $asset_general = $objects['assets_general'];

                $oldPriceEstimateHasAssets = PriceEstimateHasAsset::where('price_estimate_id', $priceEstimateId)->get();
                $oldPriceEstimateHasAssets = json_decode(json_encode($oldPriceEstimateHasAssets), true);
                //add new asset and update
                // $stt = 0;
                // $assetPrice = [];
                foreach ($asset_general as $asset) {
                    $asset_general_id = $asset['id'];
                    $version = $asset['version'];
                    if (isset($oldPriceEstimateHasAssets) && count($oldPriceEstimateHasAssets) > 0) {
                        $key = array_search($asset['id'], array_column($oldPriceEstimateHasAssets, 'asset_general_id'));
                        if (!($key === false)) {
                            //step 5 -> step 6 delete all appraise asset ... => khi có thay đổi về đơn giá UBND hoặc diện tích đất thì không cần sử lý ở đây
                            //update appraie comparison factor
                            if ($oldPriceEstimateHasAssets[$key]['version'] != $version) {
                                //delete data
                                // AppraiseComparisonFactor::query()->where('price_estimate_id', '=', $priceEstimateId)->where('asset_general_id', '=', $asset_general_id)->forceDelete();
                                // AppraiseComparisonFactor::query()->where('price_estimate_id', '=', $priceEstimateId)->where('type', '=', 'yeu_to_khac')->forceDelete();
                                // AppraiseUnitPrice::query()->where('price_estimate_id', '=', $priceEstimateId)->where('asset_general_id', '=', $asset_general_id)->forceDelete();
                                // AppraiseUnitArea::query()->where('price_estimate_id', '=', $priceEstimateId)->where('asset_general_id', '=', $asset_general_id)->forceDelete();
                                PriceEstimateHasAsset::query()->where('price_estimate_id', '=', $priceEstimateId)->where('asset_general_id', '=', $asset_general_id)->forceDelete();
                                // AppraiseAdapter::query()->where('price_estimate_id', '=', $priceEstimateId)->where('asset_general_id', '=', $asset_general_id)->forceDelete();
                                goto createNewAsset;
                            }
                            $oldAssetGeneralId = $asset_general_id;
                            // $this->postComparisonFactor($dictionaries, $appraise->properties[0], $comparisonFactors, $asset, $priceEstimateId, $asset_general_id, $oldAssetGeneralId);
                            continue;
                        }
                    }
                    // // add thêm để dự phòng trường hợp không qua được điều kiện trên
                    // if (PriceEstimateHasAsset::where('price_estimate_id', $priceEstimateId)->where('asset_general_id', $asset_general_id)->exists()) {
                    //     $this->postComparisonFactor($dictionaries, $appraise->properties[0], $comparisonFactors, $asset, $priceEstimateId, $asset_general_id, $asset_general_id);
                    //     continue;
                    // }
                    createNewAsset:
                    //create appraise has asset
                    $version = $asset['version'];
                    $hasAsset['price_estimate_id'] = $priceEstimateId;
                    $hasAsset['version'] = $version;
                    $hasAsset['asset_general_id'] = $asset_general_id;
                    $hasAsset['asset_property_detail_id'] = isset($asset['properties']) && isset($asset['properties'][0]) &&  isset($asset['properties'][0]['id']) ? $asset['properties'][0]['id'] : null;
                    $priceEstimateHasAsset = new PriceEstimateHasAsset($hasAsset);
                    QueryBuilder::for($priceEstimateHasAsset)
                        ->insert($priceEstimateHasAsset->attributesToArray());
                    //create appraie comparison factor
                    // $this->postComparisonFactor($dictionaries, $appraise->properties[0], $comparisonFactors, $asset, $priceEstimateId, $asset_general_id);
                    //create appraise unit area , appraise unit price
                    // $assetDetails = $asset['properties'][0]['property_detail'];
                    // $key = array_search($baseUBNDPrice['land_type_purpose_id'], array_column($assetDetails, 'land_type_purpose'));
                    // if ($key === false) {
                    //     $basePrice = $baseUBNDPrice['circular_unit_price'];

                    //     $area_price['price_estimate_id'] = $priceEstimateId;
                    //     $area_price['asset_general_id'] = $asset['id'];
                    //     $area_price['position_type_id'] = $baseUBNDPrice['position_type_id'];
                    //     $area_price['land_type_id'] = $baseUBNDPrice['land_type_purpose_id'];
                    //     $area_price['original_value'] = $baseUBNDPrice['circular_unit_price'];
                    //     $area_price['update'] = 1;
                    //     $area_price['created_by'] = $user->id;
                    //     $appraiseUnitPrice = new appraiseUnitPrice($area_price);

                    //     QueryBuilder::for($appraiseUnitPrice)
                    //         ->insert($appraiseUnitPrice->attributesToArray());
                    // } else {
                    //     $basePrice = $assetDetails[$key]['circular_unit_price'];
                    // }
                    // $cpcmdsd = 0;
                    // foreach ($assetDetails as $detail) {

                    //     $cpcmdsd = $cpcmdsd + CommonService::calAssetCPCMDSD($basePrice, $detail['circular_unit_price'], $detail['total_area']);
                    //     $area_price['price_estimate_id'] = $priceEstimateId;
                    //     $area_price['asset_general_id'] = $asset['id'];
                    //     $area_price['position_type_id'] = $detail['position_type_id'];
                    //     $area_price['land_type_id'] = $detail['land_type_purpose'];
                    //     $area_price['original_value'] = $detail['circular_unit_price'];
                    //     $area_price['update'] = 1;
                    //     $area_price['created_by'] = $user->id;

                    //     $appraiseUnitArea = new AppraiseUnitArea($area_price);
                    //     $appraiseUnitPrice = new appraiseUnitPrice($area_price);

                    //     QueryBuilder::for($appraiseUnitPrice)
                    //         ->insert($appraiseUnitPrice->attributesToArray());
                    //     QueryBuilder::for($appraiseUnitArea)
                    //         ->insert($appraiseUnitArea->attributesToArray());
                    // }
                    //update - insert appraise asset adapter (percent ,cpcdmdsd)
                    // $appraiseAdapterData = [
                    //     'price_estimate_id' => $priceEstimateId,
                    //     'asset_general_id' => $asset_general_id,
                    //     'percent' => floatval($asset['adjust_percent']) + 100,
                    //     'change_purpose_price' => $cpcmdsd,
                    //     'change_violate_price' => 0,
                    //     'change_negotiated_price' => $asset['adjust_amount'],
                    // ];
                    // $appraiseAdatter = new AppraiseAdapter($appraiseAdapterData);
                    // QueryBuilder::for($appraiseAdatter)
                    //     ->insert($appraiseAdatter->attributesToArray());
                }
                //calculate price
                // $this->getAppraiseCalculate($priceEstimateId);
                //delete old asset
                if (isset($oldPriceEstimateHasAssets) && count($oldPriceEstimateHasAssets) > 0) {
                    foreach ($oldPriceEstimateHasAssets as  $oldAsset) {
                        $key = array_search($oldAsset['asset_general_id'], array_column($asset_general, 'id'), true);
                        if ($key === false) {
                            $oldId = $oldAsset['asset_general_id'];
                            // AppraiseComparisonFactor::query()->where('price_estimate_id', '=', $priceEstimateId)->where('asset_general_id', '=', $oldId)->forceDelete();
                            // AppraiseComparisonFactor::query()->where('price_estimate_id', '=', $priceEstimateId)->where('type', '=', 'yeu_to_khac')->forceDelete();
                            // AppraiseUnitPrice::query()->where('price_estimate_id', '=', $priceEstimateId)->where('asset_general_id', '=', $oldId)->forceDelete();
                            // AppraiseUnitArea::query()->where('price_estimate_id', '=', $priceEstimateId)->where('asset_general_id', '=', $oldId)->forceDelete();
                            PriceEstimateHasAsset::query()->where('price_estimate_id', '=', $priceEstimateId)->where('asset_general_id', '=', $oldId)->forceDelete();
                            // AppraiseAdapter::query()->where('price_estimate_id', '=', $priceEstimateId)->where('asset_general_id', '=', $oldId)->forceDelete();
                        }
                    }
                }
            }

            // delete priceEstimateFinal nếu cập nhật lại thông tin TSSB
            $priceEstimateFinals = PriceEstimateFinal::where('price_estimate_id', $priceEstimateId)->get();
            foreach ($priceEstimateFinals as $priceEstimateFinal) {
                // Delete related records
                $priceEstimateFinal->lands()->delete();
                $priceEstimateFinal->tangibleAssets()->delete();
                $priceEstimateFinal->apartmentFinals()->delete();
                // Delete the record itself
            }

            // dd(PriceEstimateHasAsset::where('price_estimate_id',$priceEstimateId)->count('asset_general_id'));
            if (PriceEstimateHasAsset::where('price_estimate_id', $priceEstimateId)->count('asset_general_id') > 3) {
                DB::rollBack();
                $data = ['message' => ErrorMessage::PE_CHECK_ASSET_NUMBER, 'exception' =>  ''];
                return $data;
            }
            $data = PriceEstimate::where('id', $priceEstimateId)->first();
            # CẬP NHẬT THÔNG TIN VỀ TÀI SẢN SO SÁNH
            # activity-log
            $this->CreateActivityLog($data, $data, 'update_data', 'cập nhật dữ liệu tài sản so sánh');
            $this->updatePriceEstimateStep($priceEstimateId, 2);
            $this->processAfterSave($priceEstimateId);
            DB::commit();
            PriceEstimate::where('id', $priceEstimateId)->update([
                'filter_year' => $objects['filter_year'],
            ]);
            $result = PriceEstimate::where('id', $priceEstimateId)->get(['id', 'step', 'coordinates'])->first();
            $result->append('distance_max');
            return $result;
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error($ex);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' =>  $ex->getMessage()];
            return $data;
        }
    }

    public function step3Final($data, $id = null)
    {
        DB::beginTransaction();
        try {
            if ($id) {
                // Delete old record
                $priceEstimateFinals = PriceEstimateFinal::where('price_estimate_id', $id)->get();

                foreach ($priceEstimateFinals as $priceEstimateFinal) {
                    // Delete related records
                    $priceEstimateFinal->lands()->delete();
                    $priceEstimateFinal->tangibleAssets()->delete();

                    // Delete the record itself
                    $priceEstimateFinal->delete();
                }
            }

            // Create new record
            $priceEstimateFinal = PriceEstimateFinal::create($data);

            // Create related rows
            if (isset($data['total_area'])) {
                foreach ($data['total_area'] as $land) {
                    $land['price_estimate_final_id'] = $priceEstimateFinal->id;
                    $priceEstimateFinal->totalArea()->create($land);
                }
            }

            if (isset($data['planning_area'])) {
                foreach ($data['planning_area'] as $land) {
                    $land['price_estimate_final_id'] = $priceEstimateFinal->id;
                    $priceEstimateFinal->planningArea()->create($land);
                }
            }

            if (isset($data['tangible_assets'])) {
                foreach ($data['tangible_assets'] as $tangibleAsset) {
                    $tangibleAsset['price_estimate_final_id'] = $priceEstimateFinal->id;
                    $priceEstimateFinal->tangibleAssets()->create($tangibleAsset);
                }
            }
            $this->CreateActivityLog(
                $priceEstimateFinal,
                $priceEstimateFinal,
                'update_data',
                'Cập nhật giá trị tài sản'
            );
            $this->updatePriceEstimateStep($id, 3);
            $this->processAfterSave($id);
            DB::commit();
            $priceEstimateFinal = $priceEstimateFinal->load('planningArea', 'totalArea', 'tangibleAssets', 'appraisePurpose', 'assetType');


            return $priceEstimateFinal;
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error($ex);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' =>  $ex->getMessage()];
            return $data;
        }
    }
    public function step3FinalApartment($data, $id = null)
    {
        DB::beginTransaction();
        try {
            if ($id) {
                // Delete old record
                $priceEstimateFinals = PriceEstimateFinal::where('price_estimate_id', $id)->get();

                foreach ($priceEstimateFinals as $priceEstimateFinal) {
                    // Delete related records
                    $priceEstimateFinal->apartmentFinals()->delete();

                    // Delete the record itself
                    $priceEstimateFinal->delete();
                }
            }

            // Create new record
            $priceEstimateFinal = PriceEstimateFinal::create($data);

            // Create related rows
            if (isset($data['apartment_finals'])) {
                foreach ($data['apartment_finals'] as $apartment) {
                    $apartment['price_estimate_final_id'] = $priceEstimateFinal->id;
                    $priceEstimateFinal->apartmentFinals()->create($apartment);
                }
            }

            $this->CreateActivityLog(
                $priceEstimateFinal,
                $priceEstimateFinal,
                'update_data',
                'Cập nhật giá trị tài sản'
            );
            $this->updatePriceEstimateStep($id, 3);
            $this->processAfterSave($id);
            DB::commit();
            $priceEstimateFinal = $priceEstimateFinal->load('apartmentFinals', 'appraisePurpose', 'assetType');


            return $priceEstimateFinal;
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error($ex);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' =>  $ex->getMessage()];
            return $data;
        }
    }
    public function moveToAppraise($id = null)
    {
        DB::beginTransaction();
        try {
            if ($id) {
                $user = CommonService::getUser();

                $priceEstimateCheck = PriceEstimate::find($id);
                if (!$priceEstimateCheck) {
                    $data = ['message' => ErrorMessage::PE_CHECK_EXIT, 'exception' =>  ''];
                    return $data;
                }
                if ($priceEstimateCheck->appraise_id || $priceEstimateCheck->apartment_asset_id) {
                    $data = ['message' => ErrorMessage::PE_APPRAISE_EXIT, 'exception' =>  ''];
                    return $data;
                }

                if (Appraise::where('price_estimate_id', $id)->exists() || ApartmentAsset::where('price_estimate_id', $id)->exists()) {
                    $data = ['message' => ErrorMessage::APPRAISE_PE_EXIT, 'exception' =>  ''];
                    return $data;
                }
                // Fetch the PriceEstimate model instance with its relations
                $priceEstimate = $this->model->query()
                    ->where('id', $id)
                    ->with('properties.propertyDetail')
                    ->with('properties.propertyTurningTime')
                    ->with('version')
                    ->first();

                $realEstate = new RealEstate;
                $realEstate->asset_type_id = $priceEstimate->asset_type_id;
                $realEstate->appraise_asset = $priceEstimate->appraise_asset;
                $realEstate->coordinates = $priceEstimate->coordinates;
                $realEstate->created_by = $user->id;
                if ($priceEstimate->properties->isNotEmpty() && isset($priceEstimate->properties->first()->appraise_land_sum_area)) {
                    $realEstate->total_area = $priceEstimate->properties->first()->appraise_land_sum_area;
                }
                if ($priceEstimate->properties->isNotEmpty() && isset($priceEstimate->properties->first()->front_side)) {
                    $realEstate->front_side = $priceEstimate->properties->first()->front_side;
                }
                $realEstate->status = 1;
                $realEstate->sub_status = 1;
                $realEstate->save();
                // Create a new Appraise model instance and assign the data from the PriceEstimate model
                $appraise = new Appraise;
                $fillable = $appraise->getFillable();
                $appraise->apartment_number = $priceEstimate->address_number;
                foreach ($priceEstimate->getAttributes() as $key => $value) {
                    if ($key != $priceEstimate->getKeyName() && in_array($key, $fillable) && $value) {
                        $appraise->$key = $value;
                    }
                }
                $appraise->plot_num = $priceEstimate->land_no;
                $appraise->created_by = $user->id;
                $appraise->price_estimate_id = $priceEstimate->id;
                $appraise->id = $realEstate->id;
                $appraise->real_estate_id = $realEstate->id;
                $appraise->step = 1;
                // Save the Appraise model instance
                $appraise->save();
                $message = "Chuyển chính thức thành công sang TSTĐ: " . $appraise->id;

                // Fetch the relations of the PriceEstimate model, change the price_estimate_id to appraise_id, and attach them to the Appraise model instance
                foreach ($priceEstimate->relationsToArray() as $relation => $items) {
                    if ($relation == 'properties' || $relation == 'version') {
                        foreach ($items as $item) {
                            $newRelation = $appraise->$relation()->make($item);
                            $newRelation->save();

                            if ($relation == 'properties') {
                                if (isset($item['property_turning_time'])) {
                                    foreach ($item['property_turning_time'] as $turningTime) {
                                        $newRelation->propertyTurningTime()->create($turningTime);
                                    }
                                }

                                if (isset($item['property_detail'])) {
                                    foreach ($item['property_detail'] as $detail) {
                                        if (isset($detail['planning_area']) && $detail['planning_area'] > 0) {
                                            $detail['is_zoning'] = true;
                                            $detail['extra_planning'] = json_encode([
                                                [
                                                    "land_type_purpose_id" => $detail['land_type_purpose_id'],
                                                    "planning_area" => $detail['planning_area'],
                                                    "type_zoning" => "",
                                                    "appraise_property_id" => $newRelation->id,
                                                    "pp_tinh" => "",
                                                    "he_so" => null
                                                ]
                                            ]);
                                        }
                                        $newRelation->propertyDetail()->create($detail);
                                    }
                                }
                            }
                        }
                    }
                }
                $priceEstimate->update(['appraise_id' => $appraise->id]);
                $this->CreateActivityLog(
                    $priceEstimate,
                    $priceEstimate,
                    'update_data',
                    $message
                );
                DB::commit();
            }


            return $appraise;
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error($ex);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' =>  $ex->getMessage()];
            return $data;
        }
    }
    public function moveToApartmentAsset($id = null)
    {
        DB::beginTransaction();
        try {
            if ($id) {
                $user = CommonService::getUser();

                $priceEstimateCheck = PriceEstimate::find($id);
                if (!$priceEstimateCheck) {
                    $data = ['message' => ErrorMessage::PE_CHECK_EXIT, 'exception' =>  ''];
                    return $data;
                }
                if ($priceEstimateCheck->appraise_id || $priceEstimateCheck->apartment_asset_id) {
                    $data = ['message' => ErrorMessage::PE_APPRAISE_EXIT, 'exception' =>  ''];
                    return $data;
                }

                if (Appraise::where('price_estimate_id', $id)->exists() || ApartmentAsset::where('price_estimate_id', $id)->exists()) {
                    $data = ['message' => ErrorMessage::APPRAISE_PE_EXIT, 'exception' =>  ''];
                    return $data;
                }
                // Fetch the PriceEstimate model instance with its relations
                $priceEstimate = $this->model->query()
                    ->where('id', $id)
                    ->with('apartmentProperties')
                    ->with('version')
                    ->first();

                $realEstate = new RealEstate;
                $realEstate->asset_type_id = $priceEstimate->asset_type_id;
                $realEstate->appraise_asset = $priceEstimate->appraise_asset;
                $realEstate->coordinates = $priceEstimate->coordinates;
                $realEstate->created_by = $user->id;
                if ($priceEstimate->properties->isNotEmpty() && isset($priceEstimate->properties->first()->appraise_land_sum_area)) {
                    $realEstate->total_area = $priceEstimate->properties->first()->appraise_land_sum_area;
                }
                if ($priceEstimate->properties->isNotEmpty() && isset($priceEstimate->properties->first()->front_side)) {
                    $realEstate->front_side = $priceEstimate->properties->first()->front_side;
                }
                $realEstate->status = 1;
                $realEstate->sub_status = 1;
                $realEstate->save();
                // Create a new Appraise model instance and assign the data from the PriceEstimate model
                $apartment = new ApartmentAsset;
                $fillable = $apartment->getFillable();
                $apartment->apartment_number = $priceEstimate->address_number;
                foreach ($priceEstimate->getAttributes() as $key => $value) {
                    if ($key != $priceEstimate->getKeyName() && in_array($key, $fillable) && $value) {
                        $apartment->$key = $value;
                    }
                }
                $apartment->created_by = $user->id;
                $apartment->price_estimate_id = $priceEstimate->id;
                $apartment->id = $realEstate->id;
                $apartment->real_estate_id = $realEstate->id;
                $apartment->step = 1;
                // Save the Appraise model instance
                $apartment->save();
                $message = "Chuyển chính thức thành công sang TSTĐ: " . $apartment->id;

                // Fetch the relations of the PriceEstimate model, change the price_estimate_id to appraise_id, and attach them to the Appraise model instance
                foreach ($priceEstimate->relationsToArray() as $relation => $items) {
                    if ($relation == 'apartment_properties') {
                        foreach ($items as $item) {
                            $item['apartment_asset_id'] = $apartment->id;
                            $item['description'] = null;
                            $newRelation = $apartment->apartmentAssetProperties()->make($item);
                            $newRelation->save();
                        }
                    } else if ($relation == 'version') {
                        foreach ($items as $item) {
                            $item['apartment_asset_id'] =
                                $apartment->id;
                            $newRelation = $apartment->$relation()->make($item);
                            $newRelation->save();
                        }
                    }
                }
                $priceEstimate->update(['apartment_asset_id' => $apartment->id]);
                $this->CreateActivityLog(
                    $priceEstimate,
                    $priceEstimate,
                    'update_data',
                    $message
                );
                DB::commit();
            }


            return $apartment;
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error($ex);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' =>  $ex->getMessage()];
            return $data;
        }
    }
    public function getPriceEstimateFinal($price_estimate_id)
    {
        return PriceEstimateFinal::with('planningArea', 'totalArea', 'tangibleAssets', 'appraisePurpose', 'assetType')
            ->where('price_estimate_id', $price_estimate_id)
            ->get();
    }

    //step 1
    public function postApartmentInformation(array $objects, int $id = null)
    {
        DB::beginTransaction();
        try {
            $user = CommonService::getUser();
            if (isset($id)) {
                # block xác thực
                $check = $this->beforeSave($id);
                if (isset($check)) {
                    return $check;
                }

                $generalInfomation = $objects['general_infomation'];
                $apartmentProperties = $objects['apartment_properties'];
                $priceEstimateId = $id;
                PriceEstimate::where('id', $priceEstimateId)->update([
                    'appraise_asset' => $generalInfomation['appraise_asset'],
                    'asset_type_id' => $generalInfomation['asset_type_id'],
                    'project_id' => $generalInfomation['project_id'],
                    'coordinates' => $generalInfomation['coordinates'],
                    'distance_id' => $generalInfomation['distance_id'],
                    'district_id' => $generalInfomation['district_id'],
                    'province_id' => $generalInfomation['province_id'],
                    'street_id' => $generalInfomation['street_id'],
                    'ward_id' => $generalInfomation['ward_id'],
                    'full_address' => $generalInfomation['full_address'],
                    'full_address_street' => $generalInfomation['full_address_street'],
                    // 'address_number' => $generalInfomation['address_number'],
                ]);

                if (isset($apartmentProperties)) {
                    $propertieId =
                        PriceEstimateApartmentProperty::where('price_estimate_id', $priceEstimateId)->get()->first()->id;
                    if (isset($propertieId)) {
                        PriceEstimateApartmentProperty::where('price_estimate_id', $priceEstimateId)->where('id', $propertieId)->update([
                            'block_id' => $apartmentProperties['block_id'],
                            'floor_id' => $apartmentProperties['floor_id'],
                            'area' => $apartmentProperties['area'],
                            'apartment_name' => $apartmentProperties['apartment_name'],
                            'description' => $apartmentProperties['description'] ?? null,
                            'bedroom_num' => $apartmentProperties['bedroom_num'] ?? null,
                            'wc_num' => $apartmentProperties['wc_num'] ?? null,
                            'handover_year' => $apartmentProperties['handover_year'] ?? null,
                            'direction_id' => $apartmentProperties['direction_id'] ?? null,
                            'furniture_quality_id' => $apartmentProperties['furniture_quality_id'] ?? null,

                        ]);
                    } else {
                        $apartmentProperties['price_estimate_id'] = $priceEstimateId;
                        $priceEstimateProperties = new PriceEstimateApartmentProperty($apartmentProperties);
                        $propertieId = QueryBuilder::for($priceEstimateProperties)
                            ->insertGetId($priceEstimateProperties->attributesToArray());
                    }
                }
                $data = PriceEstimate::where(
                    'id',
                    $priceEstimateId
                )->first();
                $this->CreateActivityLog($data, $data, 'update_data', 'cập nhật thông tin chung');
            } else {
                $generalInfomation = $objects['general_infomation'];
                $apartmentProperties = $objects['apartment_properties'];
                $generalInfomation['created_by'] = $user->id;
                $generalInfomation['status'] = 1;
                $generalInfomation['step'] = 1;

                $priceEstimate = new PriceEstimate($generalInfomation);
                $appraiseArr = $priceEstimate->attributesToArray();
                $data = $this->model->query()->create($appraiseArr);
                $priceEstimateId = $data->id;
                if (isset($apartmentProperties)) {
                    $apartmentProperties['price_estimate_id'] = $priceEstimateId;

                    $priceEstimateProperties = new PriceEstimateApartmentProperty($apartmentProperties);
                    $propertieId =   QueryBuilder::for($priceEstimateProperties)
                        ->insertGetId($priceEstimateProperties->attributesToArray());
                }
                # activity-log
                $data = PriceEstimate::where('id', $priceEstimateId)->first();
                $this->CreateActivityLog($data, $data, 'create', 'tạo mới');
            }
            $this->processAfterSave($priceEstimateId);
            DB::commit();
            return $this->getInfomation($priceEstimateId);
        } catch (Exception $ex) {
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' =>  $ex->getMessage()];
            return $data;
            DB::rollBack();
        }
    }
}
