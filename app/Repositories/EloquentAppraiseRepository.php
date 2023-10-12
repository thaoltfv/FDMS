<?php

namespace App\Repositories;

use App\Contracts\AppraiseRepository;
use App\Contracts\CompareAssetGeneralRepository;
use App\Contracts\UserRepository;
use App\Repositories\EloquentUserRepository;
use App\Models\User;

use App\Enum\CompareMaterData;
use App\Enum\EstimateAssetDefault;
use App\Enum\AppraiseOtherInformationEnum;
use App\Enum\ErrorMessage;
use App\Enum\ValueDefault;
use App\Models\Certificate;

use App\Models\Appraise;
use App\Models\AppraiseComparisonFactor;
use App\Models\AppraiseAdapter;
use App\Models\AppraiseTangibleComparisonFactor;
use App\Models\ConstructionCompany;

use App\Models\AppraiseAppraisalMethods;
use App\Models\AppraisalConstructionCompany;
use App\Models\AppraiseDictionary;
use App\Models\AppraiseHasAsset;
use App\Models\AppraiseLaw;
use App\Models\AppraiseLawDetail;
use App\Models\AppraiseLawLandDetail;
use App\Models\AppraiseOtherAsset;
use App\Models\AppraisePic;
use App\Models\AppraisePrice;
use App\Models\AppraiseProperty;
use App\Models\AppraisePropertyDetail;
use App\Models\AppraisePropertyTurningTime;
use App\Models\AppraiseTangibleAsset;
use App\Models\AppraiseVersion;
use App\Models\AppraiseUnitPrice;
use App\Models\AppraiseUnitArea;
use App\Models\BuildingPrice;
use App\Models\CertificateAsset;
use App\Models\CompareAssetGeneral;
use App\Models\CompareProperty;
use App\Models\Dictionary;
use App\Models\RealEstate;

use App\Models\ApartmentAsset;

use App\Notifications\ActivityLog;
use App\Services\AppraiseVersionService;
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

class  EloquentAppraiseRepository extends EloquentRepository implements AppraiseRepository
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
                $appraise = $this->findById($id);
                $status = $request->input('status');
                if (isset($status)) {
                    $allow = 1;
                    if (!isset($appraise->appraiseLaw) || !count($appraise->appraiseLaw)) {
                        $allow = 2;
                    }
                    if (!isset($appraise->assetGeneral) || !count($appraise->assetGeneral)) {
                        $allow = 3;
                    }
                    if ($allow == 1 || $status == 5) {
                        /* if (empty($appraise->assetGeneral) && $status!=1) {
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
    public function findPaging(): LengthAwarePaginator
    {
        $user = CommonService::getUser();

        $perPage = (int)request()->get('limit');
        $page = (int)request()->get('page');
        $search = request()->get('search');
        $status = request()->get('status');
        $certificateId = request()->get('certificate_id');
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
                'tangibleAssets',
                'createdBy',
                'version',
                'assetType',
                'assetPrice'
            ])
            ->whereHas('properties', function ($q) use ($query) {
                if (isset($query->front_side) && !empty($query->front_side)) {
                    $query->front_side = intval($query->front_side) - 1;
                    return $q->where('front_side', '=', $query->front_side);
                }
            })
            ->whereHas('assetType', function ($q) use ($query) {
                if (isset($query->asset_type_id) && !empty($query->asset_type_id)) {
                    return $q->where('id', '=', $query->asset_type_id);
                }
            })
            ->whereHas('createdBy', function ($q) use ($query, $sortField, $user, $popup,$sortOrder) {
                if(isset($query->created_by)&&($query->created_by!='Tất cả người tạo')) {
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
                if (($role->name == 'USER') || (!empty($popup))) {
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
            ->whereHas('properties', function ($q) use($query, $sortField,$sortOrder) {
                if($sortField=='properties[0].front_side') {
                    if($sortOrder=='descend') {
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
                } else if ($sortField == 'properties[0].total_construction_area') {
                    if ($sortOrder == 'descend') {
                        $q->orderBy('total_construction_area', 'DESC');
                    } else {
                        $q->orderBy('total_construction_area', 'ASC');
                    }
                }
            })
            ->whereHas('street', function ($q) use($query, $sortField,$sortOrder) {
                if(isset($query->street_id)&&!empty($query->street_id)) {
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
        if (isset($query->total_area_from) || isset($query->total_area_to) || isset($query->total_construction_area_from) || isset($query->total_construction_area_to) || isset($query->total_amount_from) || isset($query->total_amount_to)) {
            $result = $result
                ->whereHas('assetPrice', function ($q) use ($query) {
                    if (isset($query->total_area_from)) {
                        $q->where('slug', '=', 'total_asset_area')->where('value', '>=', $query->total_area_from);
                    }
                    if (isset($query->total_area_to) && (floatval($query->total_area_to) != 0)) {
                        $q->where('slug', '=', 'total_asset_area')->where('value', '<=', $query->total_area_to);
                    }
                })
                ->whereHas('assetPrice', function ($q) use ($query) {
                    if (isset($query->total_construction_area_from)) {
                        $q->where('slug', '=', 'land_asset_price')->where('value', '>=', $query->total_construction_area_from);
                    }
                    if (isset($query->total_construction_area_to) && (floatval($query->total_construction_area_to) != 0)) {
                        $q->where('slug', '=', 'land_asset_price')->where('value', '<=', $query->total_construction_area_to);
                    }
                })
                ->whereHas('assetPrice', function ($q) use ($query) {
                    if (isset($query->total_amount_from)) {
                        $q->where('slug', '=', 'total_asset_price')->where('value', '>=', $query->total_amount_from);
                    }
                    if (isset($query->total_amount_to) && (floatval($query->total_amount_to) != 0)) {
                        $q->where('slug', '=', 'total_asset_price')->where('value', '<=', $query->total_amount_to);
                    }
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
                $q = $q->orwhere('appraise_asset', 'ILIKE', '%' . $search . '%');
            });
        }

        if (!empty($certificateId) && ($page == 1)) {
            $certificate = Certificate::where('id', $certificateId)->first();
            $appraises = $certificate->appraises;
            $appraiseIds = [];
            foreach ($appraises as $appraise) {
                $appraiseIds[] = $appraise->appraise_id;
            }
            $result = $result->orWhereIn('id', $appraiseIds);
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
            $result[$stt]->append('status_text');
            $result[$stt]->append('total_asset_price');
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
        $assetUnitPrices = AppraiseUnitPrice::whereAppraiseId($id)->get();
        foreach ($assetUnitPrices as $item) {
            if (!isset($checkAssetUnitPrices[$item->appraise_id][$item->asset_general_id][$item->land_type_id][$item->position_type_id])) {
                $checkAssetUnitPrices[$item->appraise_id][$item->asset_general_id][$item->land_type_id][$item->position_type_id] = 1;
            } else {
                AppraiseUnitPrice::whereId($item->id)->forceDelete();
            }
        }

        $checkAssetUnitAreas = [];
        $assetUnitAreas = AppraiseUnitArea::whereAppraiseId($id)->get();
        foreach ($assetUnitAreas as $item) {
            if (!isset($checkAssetUnitAreas[$item->appraise_id][$item->asset_general_id][$item->land_type_id][$item->position_type_id])) {
                $checkAssetUnitAreas[$item->appraise_id][$item->asset_general_id][$item->land_type_id][$item->position_type_id] = 1;
            } else {
                AppraiseUnitArea::whereId($item->id)->forceDelete();
            }
        }

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
                ->with('pic.picType')
                ->with('topographic')
                ->with('appraiseApproach')
                ->with('appraisePrinciple')
                ->with('appraiseMethodUsed')
                ->with('appraiseBasisProperty')
                ->with('properties.propertyDetail')
                ->with('properties.propertyTurningTime')
                ->with('properties.propertyTurningTime.material')
                ->with('properties.propertyDetail.landTypePurpose')
                ->with('properties.propertyDetail.positionType')
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
                ->with('tangibleAssets.rate')
                ->with('tangibleAssets.structure')
                ->with('tangibleAssets.crane')
                ->with('tangibleAssets.aperture')
                ->with('tangibleAssets.factoryType')
                ->with('otherAssets')
                // ->with('constructionCompany.constructionCompany')
                ->with('constructionCompany')
                ->with('appraiseLaw.law')
                ->with('appraiseLaw.lawDetails')
                ->with('appraiseLaw.lawDetails.landTypePurpose')
                ->with('appraiseLaw.landDetails')
                ->with('appraiseHasAssets')
                ->with('createdBy')
                ->with('comparisonFactor')
                ->with('appraiseAdapter')
                ->with('comparisonTangibleFactor')
                ->with('version')
                ->with('assetUnitPrice')
                ->with('assetUnitPrice.landTypeData')
                ->with('assetUnitPrice.createdBy')
                ->with('assetUnitArea')
                ->with('assetUnitArea.landTypeData')
                ->with('assetUnitArea.createdBy')
                ->with('assetPrice')
                ->with('appraisalMethods')
                ->first();
        }
        $result->append('asset_general');
        $asset = $result;
        $asset->assetGeneral = $asset->asset_general;

        $result->append('layer_cutting_procedure');
        $result->append('layer_cutting_price');
        $result->append('unify_indicative_price_slug');
        $result->append('composite_land_remaning_slug');
        $result->append('composite_land_remaning_value');
        $result->append('planning_violation_price_slug');
        $result->append('planning_violation_price_value');
        $result->append('round_total');
        $result->append('round_composite');
        $result->append('round_violation_composite');
        $result->append('round_violation_facility');
        $result->append('round_appraise_total');
        $result->append('price_land_asset');
        $result->append('price_tangible_asset');
        $result->append('price_other_asset');
        $result->append('price_total_asset');

        if(!empty($asset->assetGeneral)) {
            $user = CommonService::getUser();
            foreach ($asset->assetGeneral as $assetGeneral) {
                $isExist = 0;
                if (isset($asset->assetUnitPrice) && !empty($asset->assetUnitPrice)) {
                    foreach ($asset->assetUnitPrice as $assetUnitPrice) {
                        if ($assetUnitPrice->asset_general_id === $assetGeneral->id) $isExist = 1;
                    }
                }
                if ($isExist) {
                    continue;
                }
                foreach ($assetGeneral->properties as $property) {
                    $property->propertyDetail = isset($property->propertyDetail) ? $property->propertyDetail : $property->property_detail;
                    foreach ($property->propertyDetail as $propertyDetail) {
                        $result->assetUnitPrice()->insert([
                            'appraise_id' => $asset->id,
                            'asset_general_id' => $assetGeneral->id,
                            'land_type_id' => $propertyDetail->land_type_purpose,
                            'update' => 1,
                            'original_value' => $propertyDetail->circular_unit_price,
                            //'update_value',
                            'position_type_id' => $propertyDetail->position_type_id,
                            'created_by' => $user->id,
                        ]);
                        $result->assetUnitArea()->insert([
                            'appraise_id' => $asset->id,
                            'asset_general_id' => $assetGeneral->id,
                            'land_type_id' => $propertyDetail->land_type_purpose,
                            'position_type_id' => $propertyDetail->position_type_id,
                            'created_by' => $user->id,
                        ]);
                    }
                }
            }

            $asset1 = $asset->assetGeneral[0] ?? null;
            $asset2 = $asset->assetGeneral[1] ?? null;
            $asset3 = $asset->assetGeneral[2] ?? null;

            $detail1 = null;
            $detail2 = null;
            $detail3 = null;
            foreach ($asset->appraiseHasAssets as $appraiseHasAssets) {
                if ($appraiseHasAssets->asset_general_id == $asset1->id) {
                    foreach ($asset1->properties as $properties) {
                        if ($properties->id == $appraiseHasAssets->asset_property_detail_id) {
                            $detail1 = $properties;
                        }/*  else {
                            //delete not choose asset general properties
                            foreach ($properties->propertyDetail as $propertyDetail) {
                                AppraiseUnitPrice::where('appraise_id', $asset->id)
                                    ->where('asset_general_id', $appraiseHasAssets->asset_general_id)
                                    ->where('land_type_id', $propertyDetail->land_type_purpose)
                                    ->forceDelete();
                            }
                        } */
                    }
                }
                if ($appraiseHasAssets->asset_general_id == $asset2->id) {
                    foreach ($asset2->properties as $properties) {
                        if ($properties->id == $appraiseHasAssets->asset_property_detail_id) {
                            $detail2 = $properties;
                        }/*  else {
                            //delete not choose asset general properties
                            foreach ($properties->propertyDetail as $propertyDetail) {
                                AppraiseUnitPrice::where('appraise_id', $asset->id)
                                    ->where('asset_general_id', $appraiseHasAssets->asset_general_id)
                                    ->where('land_type_id', $propertyDetail->land_type_purpose)
                                    ->forceDelete();
                            }
                        } */
                    }
                }
                if ($appraiseHasAssets->asset_general_id == $asset3->id) {
                    foreach ($asset3->properties as $properties) {
                        if ($properties->id == $appraiseHasAssets->asset_property_detail_id) {
                            $detail3 = $properties;
                        }/*  else {
                            //delete not choose asset general properties
                            foreach ($properties->propertyDetail as $propertyDetail) {
                                AppraiseUnitPrice::where('appraise_id', $asset->id)
                                    ->where('asset_general_id', $appraiseHasAssets->asset_general_id)
                                    ->where('land_type_id', $propertyDetail->land_type_purpose)
                                    ->forceDelete();
                            }
                        } */
                    }
                }
            }


            $isExistAsset1 = false;
            $isExistAsset2 = false;
            $isExistAsset3 = false;
            $appraiseAdapterTmp = [];
            $existAssetGeneralIds = [];

            foreach ($result->appraiseAdapter as $item) {
                if ($item->asset_general_id == $asset1->id) {
                    $isExistAsset1 = true;
                    $appraiseAdapterTmp[] = $item;
                    $existAssetGeneralIds[] = $item->id;
                }
                if ($item->asset_general_id == $asset2->id) {
                    $isExistAsset2 = true;
                    $appraiseAdapterTmp[] = $item;
                    $existAssetGeneralIds[] = $item->id;
                }
                if ($item->asset_general_id == $asset3->id) {
                    $isExistAsset3 = true;
                    $appraiseAdapterTmp[] = $item;
                    $existAssetGeneralIds[] = $item->id;
                }
            }
            AppraiseAdapter::where('appraise_id', $id)->whereNotIn('id', $existAssetGeneralIds)->delete();

            if (!$isExistAsset1) {
                $result->appraiseAdapter[] = [
                    'appraise_id' => $asset->id,
                    'asset_general_id' => $asset1->id,
                    'percent' => intval($asset1->adjust_percent) + 100,
                    'change_purpose_price' => CommonService::getCPCDMDSD($asset->id, $asset1->id),
                ];
                AppraiseAdapter::insert([
                    'appraise_id' => $asset->id,
                    'asset_general_id' => $asset1->id,
                    'percent' => intval($asset1->adjust_percent) + 100,
                    'change_purpose_price' => CommonService::getCPCDMDSD($asset->id, $asset1->id),
                ]);
            }
            if (!$isExistAsset2) {
                $result->appraiseAdapter[] = [
                    'appraise_id' => $asset->id,
                    'asset_general_id' => $asset2->id,
                    'percent' => intval($asset2->adjust_percent) + 100,
                    'change_purpose_price' => CommonService::getCPCDMDSD($asset->id, $asset2->id),
                ];
                AppraiseAdapter::insert([
                    'appraise_id' => $asset->id,
                    'asset_general_id' => $asset2->id,
                    'percent' => intval($asset2->adjust_percent) + 100,
                    'change_purpose_price' => CommonService::getCPCDMDSD($asset->id, $asset2->id),
                ]);
            }
            if (!$isExistAsset3) {
                $result->appraiseAdapter[] = [
                    'appraise_id' => $asset->id,
                    'asset_general_id' => $asset3->id,
                    'percent' => intval($asset3->adjust_percent) + 100,
                    'change_purpose_price' => CommonService::getCPCDMDSD($asset->id, $asset3->id),
                ];
                AppraiseAdapter::insert([
                    'appraise_id' => $asset->id,
                    'asset_general_id' => $asset3->id,
                    'percent' => intval($asset3->adjust_percent) + 100,
                    'change_purpose_price' => CommonService::getCPCDMDSD($asset->id, $asset3->id),
                ]);
            }

            $result->appraiseAdapter->sortByDesc('asset_general_id');


            $dgxd1 = isset($asset1->tangibleAssets[0]) ? $asset1->tangibleAssets[0]->unit_price_m2 : 0;
            $dgxd2 = isset($asset2->tangibleAssets[0]) ? $asset2->tangibleAssets[0]->unit_price_m2 : 0;
            $dgxd3 = isset($asset3->tangibleAssets[0]) ? $asset3->tangibleAssets[0]->unit_price_m2 : 0;

            $clcl1 = $asset1->tangibleAssets[0]->remaining_quality ?? 0;
            $clcl2 = $asset2->tangibleAssets[0]->remaining_quality ?? 0;
            $clcl3 = $asset3->tangibleAssets[0]->remaining_quality ?? 0;


            $baseUnitPrice = 0;
            $baseAcronymId = '';
            $basePositionTypeId = '';
            if ((isset($asset->properties[0])) && (count($asset->properties[0]->propertyDetail) == 1)) {
                $baseUnitPrice = $asset->properties[0]->propertyDetail[0]->circular_unit_price;
                $baseAcronymId = $asset->properties[0]->propertyDetail[0]->land_type_purpose_id;
                $basePositionTypeId = $asset->properties[0]->propertyDetail[0]->position_type_id;
            } else {
                foreach ($asset->properties[0]->propertyDetail as $index => $item) {
                    if ($item->is_transfer_facility || !$index) {
                        $baseUnitPrice = $item->circular_unit_price;
                        $baseAcronymId = $item->land_type_purpose_id;
                        $basePositionTypeId = $item->position_type_id;
                    }
                }
            }

            $baseUnitPrice = floatval($baseUnitPrice);
            if (isset($baseAcronymId) && !empty($baseAcronymId)) {
                foreach ($asset->assetGeneral as $assetGeneral) {
                    $item = AppraiseUnitPrice::where('appraise_id', $asset->id)
                        ->where('asset_general_id', $assetGeneral->id)
                        ->where('land_type_id', $baseAcronymId)
                        ->first();
                    if (!isset($item)) {
                        $result->assetUnitPrice()->insert([
                            'appraise_id' => $asset->id,
                            'asset_general_id' => $assetGeneral->id,
                            'land_type_id' => $baseAcronymId,
                            'original_value' => $baseUnitPrice,
                            'position_type_id' => $basePositionTypeId,
                            'created_by' => $user->id,
                            'update' => 1,
                        ]);
                    }
                }
            }

            $price1 = 0;
            if (isset($detail1->propertyDetail)) {
                foreach ($detail1->propertyDetail as $item) {
                    $unitPrice = $baseUnitPrice - floatval($item->circular_unit_price);
                    $price1 += $unitPrice * floatval($item->total_area);
                }
            }
            $price2 = 0;
            if (isset($detail1->propertyDetail)) {
                foreach ($detail1->propertyDetail as $item) {
                    $unitPrice = $baseUnitPrice - floatval($item->circular_unit_price);
                    $price2 += $unitPrice * floatval($item->total_area);
                }
            }
            $price3 = 0;
            if (isset($detail1->propertyDetail)) {
                foreach ($detail1->propertyDetail as $item) {
                    $unitPrice = $baseUnitPrice - floatval($item->circular_unit_price);
                    $price3 += $unitPrice * floatval($item->total_area);
                }
            }


            $totalPrice1 = ($asset1->total_estimate_amount ?? 0) + $price1 - ($dgxd1 * $clcl1 / 100);
            $totalPrice2 = ($asset2->total_estimate_amount ?? 0) + $price2 - ($dgxd2 * $clcl2 / 100);
            $totalPrice3 = ($asset3->total_estimate_amount ?? 0) + $price3 - ($dgxd3 * $clcl3 / 100);

            $dgd1 = isset($detail1->asset_general_land_sum_area) ? ($totalPrice1 / $detail1->asset_general_land_sum_area) : 0;
            $dgd2 = isset($detail2->asset_general_land_sum_area) ? ($totalPrice2 / $detail2->asset_general_land_sum_area) : 0;
            $dgd3 = isset($detail3->asset_general_land_sum_area) ? ($totalPrice3 / $detail3->asset_general_land_sum_area) : 0;

            $result->cpcmdsd = [$price1, $price2, $price3];
            $result->dgbq = [$dgd1, $dgd2, $dgd3];

            foreach ($result->properties as $property) {
                foreach ($property->propertyDetail as $item) {
                    if (!$item->is_transfer_facility) {
                        $landTypePurpose = (isset($item->landTypePurpose) && isset($item->landTypePurpose->acronym)) ? $item->landTypePurpose->acronym : '';
                        $result->remaining_price = [
                            'land_type' => $landTypePurpose,
                            'remaining_commerce_price' => CommonService::getAppraisePrice($asset, 'land_asset_purpose_' . $landTypePurpose . '_price')
                        ];
                    }
                }
            }
        }

        $result->unify_indicative_price = [];
        foreach (AppraiseOtherInformationEnum::DATA['thong_nhat_muc_gia_chi_dan'] as $item) {
            if ($item['slug'] == $result->unify_indicative_price_slug) {
                $result->unify_indicative_price = $item;
            }
        }

        $result->composite_land_remaning = [];
        foreach (AppraiseOtherInformationEnum::DATA['tinh_gia_dat_hon_hop_con_lai'] as $item) {
            if ($item['slug'] == $result->composite_land_remaning_slug) {
                $result->composite_land_remaning = $item;
            }
        }
        $result->planning_violation_price = [];
        foreach (AppraiseOtherInformationEnum::DATA['tinh_gia_dat_vi_pham_quy_hoach'] as $item) {
            if ($item['slug'] == $result->planning_violation_price_slug) {
                $result->planning_violation_price = $item;
            }
        }

        //CommonService::getAssetPriceTotal($result);

        return $result;
    }

    public function findByIdTest($id)
    {
        // remove duplicate assetUnitPrice
        $checkAssetUnitPrices = [];
        $assetUnitPrices = AppraiseUnitPrice::whereAppraiseId($id)->get();
        foreach ($assetUnitPrices as $item) {
            if (!isset($checkAssetUnitPrices[$item->appraise_id][$item->asset_general_id][$item->land_type_id][$item->position_type_id])) {
                $checkAssetUnitPrices[$item->appraise_id][$item->asset_general_id][$item->land_type_id][$item->position_type_id] = 1;
            } else {
                AppraiseUnitPrice::whereId($item->id)->forceDelete();
            }
        }

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
                ->with('pic.picType')
                ->with('topographic')
                ->with('appraiseApproach')
                ->with('appraisePrinciple')
                ->with('appraiseMethodUsed')
                ->with('appraiseBasisProperty')
                ->with('properties.propertyDetail')
                ->with('properties.propertyTurningTime')
                ->with('properties.propertyTurningTime.material')
                ->with('properties.propertyDetail.landTypePurpose')
                ->with('properties.propertyDetail.positionType')
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
                ->with('tangibleAssets.rate')
                ->with('tangibleAssets.structure')
                ->with('tangibleAssets.crane')
                ->with('tangibleAssets.aperture')
                ->with('tangibleAssets.factoryType')
                ->with('otherAssets')
                ->with('constructionCompany.constructionCompany')
                ->with('appraiseLaw.law')
                ->with('appraiseLaw.lawDetails')
                ->with('appraiseLaw.lawDetails.landTypePurpose')
                ->with('appraiseLaw.landDetails')
                ->with('assetGeneral')
                ->with('appraiseHasAssets')
                ->with('createdBy')
                ->with('comparisonFactor')
                ->with('appraiseAdapter')
                ->with('comparisonTangibleFactor')
                ->with('version')
                ->with('assetGeneral.createdBy')
                ->with('assetGeneral.province')
                ->with('assetGeneral.district')
                ->with('assetGeneral.ward')
                ->with('assetGeneral.street')
                ->with('assetGeneral.distance')
                ->with('assetGeneral.assetType')
                ->with('assetGeneral.source')
                ->with('assetGeneral.transactionType')
                ->with('assetGeneral.apartment')
                ->with('assetGeneral.pic')
                ->with('assetGeneral.version')
                ->with('assetGeneral.topographicData')
                ->with('assetGeneral.properties.propertyDetail')
                ->with('assetGeneral.properties.comparePropertyTurningTime')
                ->with('assetGeneral.properties.comparePropertyTurningTime.material')
                ->with('assetGeneral.properties.propertyDetail.landTypePurposeData')
                ->with('assetGeneral.properties.propertyDetail.positionType')
                ->with('assetGeneral.properties.comparePropertyDoc')
                ->with('assetGeneral.properties.pic')
                ->with('assetGeneral.properties.legal')
                ->with('assetGeneral.properties.zoning')
                ->with('assetGeneral.properties.landType')
                ->with('assetGeneral.properties.landShape')
                ->with('assetGeneral.properties.business')
                ->with('assetGeneral.properties.electricWater')
                ->with('assetGeneral.properties.socialSecurity')
                ->with('assetGeneral.properties.fengShui')
                ->with('assetGeneral.properties.paymenMethod')
                ->with('assetGeneral.properties.conditions')
                ->with('assetGeneral.properties.material')
                ->with('assetGeneral.tangibleAssets.buildingType')
                ->with('assetGeneral.tangibleAssets.buildingCategory')
                ->with('assetGeneral.tangibleAssets.compareProperty')
                ->with('assetGeneral.tangibleAssets.pic')
                ->with('assetGeneral.tangibleAssets.rate')
                ->with('assetGeneral.tangibleAssets.structure')
                ->with('assetGeneral.tangibleAssets.crane')
                ->with('assetGeneral.tangibleAssets.aperture')
                ->with('assetGeneral.tangibleAssets.factoryType')
                ->with('assetGeneral.otherAssets.otherTypeAsset')
                ->with('assetGeneral.otherAssets.pic')
                ->with('assetGeneral.blockSpecification')
                ->with('assetGeneral.blockSpecification.basicUtilities')
                ->with('assetGeneral.blockSpecification.blockLists')
                ->with('assetGeneral.roomDetails')
                ->with('assetGeneral.roomDetails.roomFurnitureDetails')
                ->with('assetGeneral.roomDetails.direction')
                ->with('assetGeneral.roomDetails.furnitureQuality')
                ->with('assetUnitPrice')
                ->with('assetUnitPrice.landTypeData')
                ->with('assetUnitPrice.createdBy')
                ->with('assetUnitArea')
                ->with('assetUnitArea.landTypeData')
                ->with('assetUnitArea.createdBy')
                ->with('assetPrice')
                ->with('appraisalMethods')
                ->first();
        }

        $asset = $result;

        $user = CommonService::getUser();

        foreach ($asset->assetGeneral as $assetGeneral) {
            $isExist = 0;
            if (isset($asset->assetUnitPrice) && !empty($asset->assetUnitPrice)) {
                foreach ($asset->assetUnitPrice as $assetUnitPrice) {
                    if ($assetUnitPrice->asset_general_id === $assetGeneral->id) $isExist = 1;
                }
            }
            if ($isExist) {
                continue;
            }
            foreach ($assetGeneral->properties as $property) {
                foreach ($property->propertyDetail as $propertyDetail) {
                    AppraiseUnitPrice::insert([
                        'appraise_id' => $asset->id,
                        'asset_general_id' => $assetGeneral->id,
                        'land_type_id' => $propertyDetail->land_type_purpose,
                        'update' => 1,
                        'original_value' => $propertyDetail->circular_unit_price,
                        //'update_value',
                        'position_type_id' => $propertyDetail->position_type_id,
                        'created_by' => $user->id,
                    ]);
                }
            }
        }

        $asset1 = $asset->assetGeneral[0] ?? null;
        $asset2 = $asset->assetGeneral[1] ?? null;
        $asset3 = $asset->assetGeneral[2] ?? null;

        $detail1 = null;
        $detail2 = null;
        $detail3 = null;
        foreach ($asset->appraiseHasAssets as $appraiseHasAssets) {
            if ($appraiseHasAssets->asset_general_id == $asset1->id) {
                foreach ($asset1->properties as $properties) {
                    if ($properties->id == $appraiseHasAssets->asset_property_detail_id) {
                        $detail1 = $properties;
                    }/*  else {
                        //delete not choose asset general properties
                        foreach ($properties->propertyDetail as $propertyDetail) {
                            AppraiseUnitPrice::where('appraise_id', $asset->id)
                                ->where('asset_general_id', $appraiseHasAssets->asset_general_id)
                                ->where('land_type_id', $propertyDetail->land_type_purpose)
                                ->forceDelete();
                        }
                    } */
                }
            }
            if ($appraiseHasAssets->asset_general_id == $asset2->id) {
                foreach ($asset2->properties as $properties) {
                    if ($properties->id == $appraiseHasAssets->asset_property_detail_id) {
                        $detail2 = $properties;
                    }/*  else {
                        //delete not choose asset general properties
                        foreach ($properties->propertyDetail as $propertyDetail) {
                            AppraiseUnitPrice::where('appraise_id', $asset->id)
                                ->where('asset_general_id', $appraiseHasAssets->asset_general_id)
                                ->where('land_type_id', $propertyDetail->land_type_purpose)
                                ->forceDelete();
                        }
                    } */
                }
            }
            if ($appraiseHasAssets->asset_general_id == $asset3->id) {
                foreach ($asset3->properties as $properties) {
                    if ($properties->id == $appraiseHasAssets->asset_property_detail_id) {
                        $detail3 = $properties;
                    }/*  else {
                        //delete not choose asset general properties
                        foreach ($properties->propertyDetail as $propertyDetail) {
                            AppraiseUnitPrice::where('appraise_id', $asset->id)
                                ->where('asset_general_id', $appraiseHasAssets->asset_general_id)
                                ->where('land_type_id', $propertyDetail->land_type_purpose)
                                ->forceDelete();
                        }
                    } */
                }
            }
        }

        $isExistAsset1 = false;
        $isExistAsset2 = false;
        $isExistAsset3 = false;
        $appraiseAdapterTmp = [];
        $existAssetGeneralIds = [];
        foreach ($result->appraiseAdapter as $item) {
            if ($item->asset_general_id == $asset1->id) {
                $isExistAsset1 = true;
                $appraiseAdapterTmp[] = $item;
                $existAssetGeneralIds[] = $item->id;
            }
            if ($item->asset_general_id == $asset2->id) {
                $isExistAsset2 = true;
                $appraiseAdapterTmp[] = $item;
                $existAssetGeneralIds[] = $item->id;
            }
            if ($item->asset_general_id == $asset3->id) {
                $isExistAsset3 = true;
                $appraiseAdapterTmp[] = $item;
                $existAssetGeneralIds[] = $item->id;
            }
        }
        AppraiseAdapter::where('appraise_id', $id)->whereNotIn('id', $existAssetGeneralIds)->delete();

        if (!$isExistAsset1) {
            $result->appraiseAdapter[] = [
                'appraise_id' => $asset->id,
                'asset_general_id' => $asset1->id,
                'percent' => intval($asset1->adjust_percent) + 100,
            ];
        }
        if (!$isExistAsset2) {
            $result->appraiseAdapter[] = [
                'appraise_id' => $asset->id,
                'asset_general_id' => $asset2->id,
                'percent' => intval($asset2->adjust_percent) + 100,
            ];
        }
        if (!$isExistAsset3) {
            $result->appraiseAdapter[] = [
                'appraise_id' => $asset->id,
                'asset_general_id' => $asset3->id,
                'percent' => intval($asset3->adjust_percent) + 100,
            ];
        }

        $dgxd1 = isset($asset1->tangibleAssets[0]) ? $asset1->tangibleAssets[0]->unit_price_m2 : 0;
        $dgxd2 = isset($asset2->tangibleAssets[0]) ? $asset2->tangibleAssets[0]->unit_price_m2 : 0;
        $dgxd3 = isset($asset3->tangibleAssets[0]) ? $asset3->tangibleAssets[0]->unit_price_m2 : 0;

        $clcl1 = $asset1->tangibleAssets[0]->remaining_quality ?? 0;
        $clcl2 = $asset2->tangibleAssets[0]->remaining_quality ?? 0;
        $clcl3 = $asset3->tangibleAssets[0]->remaining_quality ?? 0;

        $baseUnitPrice = 0;
        if ((isset($asset->properties[0])) && (count($asset->properties[0]->propertyDetail) == 1)) {
            $baseUnitPrice = $asset->properties[0]->propertyDetail[0]->circular_unit_price;
        } else {
            foreach ($asset->properties[0]->propertyDetail as $index => $item) {
                if ($item->is_transfer_facility || !$index) {
                    $baseUnitPrice = $item->circular_unit_price;
                }
            }
        }
        $baseUnitPrice = floatval($baseUnitPrice);

        $price1 = 0;
        //echo '<pre>';
        if (isset($detail1->propertyDetail)) {
            foreach ($detail1->propertyDetail as $item) {
                $unitPrice = $baseUnitPrice - floatval($item->circular_unit_price);
                $price1 += $unitPrice * floatval($item->total_area);
                //var_dump(floatval($item->total_area));
                //var_dump($unitPrice);
            }
        }
        $price2 = 0;
        //echo '<pre>';
        if (isset($detail1->propertyDetail)) {
            foreach ($detail1->propertyDetail as $item) {
                $unitPrice = $baseUnitPrice - floatval($item->circular_unit_price);
                $price2 += $unitPrice * floatval($item->total_area);
                //var_dump(floatval($item->total_area));
                //var_dump($unitPrice);
            }
        }
        $price3 = 0;
        //echo '<pre>';
        if (isset($detail1->propertyDetail)) {
            foreach ($detail1->propertyDetail as $item) {
                $unitPrice = $baseUnitPrice - floatval($item->circular_unit_price);
                $price3 += $unitPrice * floatval($item->total_area);
                //var_dump(floatval($item->total_area));
                //var_dump($unitPrice);
            }
        }

        $totalPrice1 = ($asset1->total_estimate_amount ?? 0) + $price1 - ($dgxd1 * $clcl1 / 100);
        $totalPrice2 = ($asset2->total_estimate_amount ?? 0) + $price2 - ($dgxd2 * $clcl2 / 100);
        $totalPrice3 = ($asset3->total_estimate_amount ?? 0) + $price3 - ($dgxd3 * $clcl3 / 100);

        $dgd1 = isset($detail1->asset_general_land_sum_area) ? ($totalPrice1 / $detail1->asset_general_land_sum_area) : 0;
        $dgd2 = isset($detail2->asset_general_land_sum_area) ? ($totalPrice2 / $detail2->asset_general_land_sum_area) : 0;
        $dgd3 = isset($detail3->asset_general_land_sum_area) ? ($totalPrice3 / $detail3->asset_general_land_sum_area) : 0;

        $result->cpcmdsd = [$price1, $price2, $price3];
        $result->dgbq = [$dgd1, $dgd2, $dgd3];

        $result->append('layer_cutting_procedure');
        $result->append('layer_cutting_price');
        $result->append('unify_indicative_price_slug');
        $result->append('composite_land_remaning_slug');
        $result->append('composite_land_remaning_value');
        $result->append('planning_violation_price_slug');
        $result->append('planning_violation_price_value');
        $result->append('round_total');
        $result->append('round_composite');
        $result->append('round_violation_composite');
        $result->append('round_violation_facility');
        $result->append('round_appraise_total');
        $result->append('price_land_asset');
        $result->append('price_tangible_asset');
        $result->append('price_other_asset');
        $result->append('price_total_asset');

        //$result->price_land_asset = CommonService::roundAssetPrice($result, $result->price_land_asset);

        foreach ($result->properties as $property) {
            foreach ($property->propertyDetail as $item) {
                if (!$item->is_transfer_facility) {
                    $landTypePurpose = (isset($item->landTypePurpose) && isset($item->landTypePurpose->acronym)) ? $item->landTypePurpose->acronym : '';
                    $result->remaining_price = [
                        'land_type' => $landTypePurpose,
                        'remaining_commerce_price' => CommonService::getAppraisePrice($asset, 'land_asset_purpose_' . $landTypePurpose . '_price')
                    ];
                }
            }
        }

        $result->unify_indicative_price = [];
        foreach (AppraiseOtherInformationEnum::DATA['thong_nhat_muc_gia_chi_dan'] as $item) {
            if ($item['slug'] == $result->unify_indicative_price_slug) {
                $result->unify_indicative_price = $item;
            }
        }

        $result->composite_land_remaning = [];
        foreach (AppraiseOtherInformationEnum::DATA['tinh_gia_dat_hon_hop_con_lai'] as $item) {
            if ($item['slug'] == $result->composite_land_remaning_slug) {
                $result->composite_land_remaning = $item;
            }
        }
        $result->planning_violation_price = [];
        foreach (AppraiseOtherInformationEnum::DATA['tinh_gia_dat_vi_pham_quy_hoach'] as $item) {
            if ($item['slug'] == $result->planning_violation_price_slug) {
                $result->planning_violation_price = $item;
            }
        }

        //$result->comparison_factor = $result->comparisonFactor->where('type', '<>', 'yeu_to_khac');

        //CommonService::getAssetPriceTotal($result);

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
            ->with('pic.picType')
            ->with('topographic')
            ->with('appraiseApproach')
            ->with('appraisePrinciple')
            ->with('appraiseMethodUsed')
            ->with('appraiseBasisProperty')
            ->with('properties.propertyDetail')
            ->with('properties.propertyTurningTime')
            ->with('properties.propertyTurningTime.material')
            ->with('properties.propertyDetail.landTypePurpose')
            ->with('properties.propertyDetail.positionType')
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
            ->with('tangibleAssets.rate')
            ->with('tangibleAssets.structure')
            ->with('tangibleAssets.crane')
            ->with('tangibleAssets.aperture')
            ->with('tangibleAssets.factoryType')
            ->with('otherAssets')
            // ->with('constructionCompany.constructionCompany')
            ->with('constructionCompany')
            ->with('appraiseLaw.law')
            ->with('appraiseLaw.lawDetails')
            ->with('appraiseLaw.lawDetails.landTypePurpose')
            ->with('appraiseHasAssets')
            ->with('comparisonFactor')
            ->with('comparisonTangibleFactor')
            ->with('createdBy')
            ->with('version')
            /* ->with('assetGeneral.createdBy')
            ->with('assetGeneral.province')
            ->with('assetGeneral.district')
            ->with('assetGeneral.ward')
            ->with('assetGeneral.street')
            ->with('assetGeneral.distance')
            ->with('assetGeneral.assetType')
            ->with('assetGeneral.source')
            ->with('assetGeneral.transactionType')
            ->with('assetGeneral.apartment')
            ->with('assetGeneral.pic')
            ->with('assetGeneral.version')
            ->with('assetGeneral.topographicData')
            ->with('assetGeneral.properties.propertyDetail')
            ->with('assetGeneral.properties.comparePropertyTurningTime')
            ->with('assetGeneral.properties.comparePropertyTurningTime.material')
            ->with('assetGeneral.properties.propertyDetail.landTypePurposeData')
            ->with('assetGeneral.properties.propertyDetail.positionType')
            ->with('assetGeneral.properties.comparePropertyDoc')
            ->with('assetGeneral.properties.pic')
            ->with('assetGeneral.properties.legal')
            ->with('assetGeneral.properties.zoning')
            ->with('assetGeneral.properties.landType')
            ->with('assetGeneral.properties.landShape')
            ->with('assetGeneral.properties.business')
            ->with('assetGeneral.properties.electricWater')
            ->with('assetGeneral.properties.socialSecurity')
            ->with('assetGeneral.properties.fengShui')
            ->with('assetGeneral.properties.paymenMethod')
            ->with('assetGeneral.properties.conditions')
            ->with('assetGeneral.properties.material')
            ->with('assetGeneral.tangibleAssets.buildingType')
            ->with('assetGeneral.tangibleAssets.buildingCategory')
            ->with('assetGeneral.tangibleAssets.compareProperty')
            ->with('assetGeneral.tangibleAssets.pic')
            ->with('assetGeneral.tangibleAssets.rate')
            ->with('assetGeneral.tangibleAssets.structure')
            ->with('assetGeneral.tangibleAssets.crane')
            ->with('assetGeneral.tangibleAssets.aperture')
            ->with('assetGeneral.tangibleAssets.factoryType')
            ->with('assetGeneral.otherAssets.otherTypeAsset')
            ->with('assetGeneral.otherAssets.pic')
            ->with('assetGeneral.blockSpecification')
            ->with('assetGeneral.blockSpecification.basicUtilities')
            ->with('assetGeneral.blockSpecification.blockLists')
            ->with('assetGeneral.roomDetails')
            ->with('assetGeneral.roomDetails.roomFurnitureDetails')
            ->with('assetGeneral.roomDetails.direction')
            ->with('assetGeneral.roomDetails.furnitureQuality') */
            ->get();

        foreach ($items as $stt => $item) {
            $items[$stt]->comparison_factor_custom = $this->getComparisonFactor($item->id);
            $items[$stt]->append('asset_general');
        }

        return $items;
    }

    /**
     * @param array $objects
     * @return object
     * @throws Throwable
     */
    public function createAppraise(array $objects): object
    {
        return DB::transaction(function () use ($objects) {
            try {
                $objects['created_by'] = is_array($objects['created_by']) ? $objects['created_by']['id'] : $objects['created_by'];
                $objects["updated_at"] = date("Y-m-d H:i:s");
                $objects['status'] = 1;
                /* if (!isset($objects['asset_general']) || empty($objects['asset_general'])) {
                    $objects['status'] = 1;
                } */
                $appraise = new Appraise($objects);
                $appraiseId = QueryBuilder::for(Appraise::class)
                    ->insertGetId($appraise->attributesToArray());

                $countVersion = AppraiseVersion::query()
                    ->where('appraise_id', '=', $appraiseId)
                    ->where('status', '=', $appraise->status)
                    ->count();
                $version['version'] = $appraise->status . '.' . $countVersion;
                $version['status'] = $appraise->status;
                $version['appraise_id'] = $appraiseId;
                AppraiseVersion::query()->insert($version);


                if (isset($objects['pic'])) {
                    foreach ($objects['pic'] as $appraisePic) {
                        $appraisePic['appraise_id'] = $appraiseId;
                        $pic = new AppraisePic($appraisePic);
                        QueryBuilder::for($pic)
                            ->insert($pic->attributesToArray());
                    }
                }
                if (isset($objects['properties'])) {
                    foreach ($objects['properties'] as $propertyData) {
                        $propertyData['appraise_id'] = $appraiseId;

                        $property = new AppraiseProperty($propertyData);
                        $propertyId = QueryBuilder::for($property)
                            ->insertGetId($property->attributesToArray());
                        if (isset($propertyData['property_detail'])) {
                            foreach ($propertyData['property_detail'] as $propertyDetail) {
                                $propertyDetail['appraise_property_id'] = $propertyId;
                                $propertyDetail['k_rate'] = isset($propertyDetail['k_rate']) ? intval($propertyDetail['k_rate']) : 0;
                                $propertyDetail['planning_area'] = isset($propertyDetail['planning_area']) ? floatval($propertyDetail['planning_area']) : 0;
                                $propertyDetail['estimation_value'] = isset($propertyDetail['estimation_value']) ? doubleval($propertyDetail['estimation_value']) : 0;
                                $propertyDetail = array_map(function ($v) {
                                    return (is_null($v)) ? "" : $v;
                                }, $propertyDetail);
                                $detail = new AppraisePropertyDetail($propertyDetail);
                                QueryBuilder::for($detail)
                                    ->insert($detail->attributesToArray());
                            }
                        }

                        if (isset($propertyData['property_turning_time'])) {
                            foreach ($propertyData['property_turning_time'] as $propertyTurningTime) {
                                $propertyTurningTime['appraise_property_id'] = $propertyId;
                                $detail = new AppraisePropertyTurningTime($propertyTurningTime);
                                QueryBuilder::for($detail)
                                    ->insert($detail->attributesToArray());
                            }
                        }
                    }
                }

                if (isset($objects['tangible_assets'])) {
                    foreach ($objects['tangible_assets'] as $tangibleAssetData) {
                        $tangibleAssetData['appraise_id'] = $appraiseId;
                        $tangibleAsset = new AppraiseTangibleAsset($tangibleAssetData);
                        $tangibleId = QueryBuilder::for($tangibleAsset)
                            ->insertGetId($tangibleAsset->attributesToArray());
                    }
                }

                if (isset($objects['other_assets'])) {
                    foreach ($objects['other_assets'] as $otherAssetData) {
                        $otherAssetData['appraise_id'] = $appraiseId;
                        $otherAssetData["unit_price"] = isset($otherAssetData["unit_price"]) ? intval($otherAssetData["unit_price"]) : 0;
                        $otherAssetData["total_price"] = isset($otherAssetData["total_price"]) ? intval($otherAssetData["total_price"]) : 0;
                        $otherAssetData = array_map(function ($v) {
                            return (is_null($v)) ? "" : $v;
                        }, $otherAssetData);
                        $otherAsset = new AppraiseOtherAsset($otherAssetData);
                        $otherId = QueryBuilder::for($otherAsset)
                            ->insertGetId($otherAsset->attributesToArray());
                    }
                }

                if (isset($objects['appraise_law'])) {
                    foreach ($objects['appraise_law'] as $lawData) {
                        $lawData['appraise_id'] = $appraiseId;
                        if (!$lawData['appraise_law_id']) $lawData['appraise_law_id'] = null;
                        $law = new AppraiseLaw($lawData);
                        $lawId = QueryBuilder::for($law)
                            ->insertGetId($law->attributesToArray());
                        if (isset($lawData['law_details'])) {
                            foreach ($lawData['law_details'] as $lawDetail) {
                                $lawDetail['appraise_law_id'] = $lawId;
                                $lawDetail = new AppraiseLawDetail($lawDetail);
                                QueryBuilder::for($lawDetail)
                                    ->insert($lawDetail->attributesToArray());
                            }
                        }
                        if (isset($lawData['land_details'])) {
                            foreach ($lawData['land_details'] as $landDetail) {
                                $landDetail['appraise_law_id'] = $lawId;
                                $landDetail = new AppraiseLawLandDetail($landDetail);
                                QueryBuilder::for($landDetail)
                                    ->insert($landDetail->attributesToArray());
                            }
                        }
                    }
                }

                if (isset($objects['construction_company'])) {
                    foreach ($objects['construction_company'] as $constructionCompanyData) {
                        $constructionCompanyData['appraise_id'] = $appraiseId;
                        $constructionCompany = AppraisalConstructionCompany::where('id', $constructionCompanyData['construction_company_id'])->first();
                        if (isset($constructionCompany)) {
                            $constructionCompanyData['name'] = $constructionCompany->name;
                            $constructionCompanyData['manager_name'] = $constructionCompany->manager_name;
                            $constructionCompanyData['phone_number'] = $constructionCompany->phone_number;
                            $constructionCompanyData['address'] = $constructionCompany->address;
                            $constructionCompanyData['unit_price_m2'] = $constructionCompany->unit_price_m2;
                            $constructionCompanyData['is_defaults'] = $constructionCompany->is_defaults;
                        }
                        $constructionCompanyData = new ConstructionCompany($constructionCompanyData);
                        $constructionCompanyId = QueryBuilder::for($constructionCompanyData)
                            ->insertGetId($constructionCompanyData->attributesToArray());
                    }
                }

                if (isset($objects['asset_general'])) {
                    foreach ($objects['asset_general'] as $assetGeneralData) {
                        $assetGeneralData['appraise_id'] = $appraiseId;
                        $assetGeneralData = new AppraiseHasAsset($assetGeneralData);
                        $assetGeneralId = QueryBuilder::for($assetGeneralData)
                            ->insertGetId($assetGeneralData->attributesToArray());
                    }
                }

                $tangibleComparisonFactor['appraise_id'] = $appraiseId;
                $tangibleComparisonFactor = new AppraiseTangibleComparisonFactor($tangibleComparisonFactor);
                $tangibleComparisonFactorId = QueryBuilder::for($tangibleComparisonFactor)
                    ->insertGetId($tangibleComparisonFactor->attributesToArray());

                $this->comparisonFactor($appraiseId, $objects);

                if (isset($objects['unify_indicative_price_slug'])) {
                    $items = AppraiseAppraisalMethods::where('appraise_id', $appraiseId)->where('slug', 'thong_nhat_muc_gia_chi_dan')->get();
                    if (count($items) == 1) {
                        AppraiseAppraisalMethods::where('appraise_id', $appraiseId)->where('slug', 'thong_nhat_muc_gia_chi_dan')->update([
                            'appraise_id' => $appraiseId,
                            'slug' => "thong_nhat_muc_gia_chi_dan",
                            'slug_value' => $objects['unify_indicative_price_slug'],
                        ]);
                    } else {
                        if (count($items) > 1) {
                            AppraiseAppraisalMethods::where('appraise_id', $appraiseId)->where('slug', 'thong_nhat_muc_gia_chi_dan')->forceDelete();
                        }
                        AppraiseAppraisalMethods::create([
                            'appraise_id' => $appraiseId,
                            'slug' => "thong_nhat_muc_gia_chi_dan",
                            'slug_value' => $objects['unify_indicative_price_slug'],
                        ]);
                    }
                }

                if (isset($objects['composite_land_remaning_slug'])) {
                    $items = AppraiseAppraisalMethods::where('appraise_id', $appraiseId)->where('slug', 'tinh_gia_dat_hon_hop_con_lai')->get();
                    if (count($items) == 1) {
                        AppraiseAppraisalMethods::where('appraise_id', $appraiseId)->where('slug', 'tinh_gia_dat_hon_hop_con_lai')->update([
                            'appraise_id' => $appraiseId,
                            'slug' => "tinh_gia_dat_hon_hop_con_lai",
                            'slug_value' => $objects['composite_land_remaning_slug'],
                            'value' => isset($objects['composite_land_remaning_value']) ? $objects['composite_land_remaning_value'] : null,
                        ]);
                    } else {
                        if (count($items) > 1) {
                            AppraiseAppraisalMethods::where('appraise_id', $appraiseId)->where('slug', 'tinh_gia_dat_hon_hop_con_lai')->forceDelete();
                        }
                        AppraiseAppraisalMethods::create([
                            'appraise_id' => $appraiseId,
                            'slug' => "tinh_gia_dat_hon_hop_con_lai",
                            'slug_value' => $objects['composite_land_remaning_slug'],
                            'value' => isset($objects['composite_land_remaning_value']) ? $objects['composite_land_remaning_value'] : null,
                        ]);
                    }
                }

                if (isset($objects['planning_violation_price_slug'])) {
                    $items = AppraiseAppraisalMethods::where('appraise_id', $appraiseId)->where('slug', 'tinh_gia_dat_vi_pham_quy_hoach')->get();
                    if (count($items) == 1) {
                        AppraiseAppraisalMethods::where('appraise_id', $appraiseId)->where('slug', 'tinh_gia_dat_vi_pham_quy_hoach')->update([
                            'appraise_id' => $appraiseId,
                            'slug' => "tinh_gia_dat_vi_pham_quy_hoach",
                            'slug_value' => $objects['planning_violation_price_slug'],
                            'value' => isset($objects['planning_violation_price_value']) ? $objects['planning_violation_price_value'] : null,
                        ]);
                    } else {
                        if (count($items) > 1) {
                            AppraiseAppraisalMethods::where('appraise_id', $appraiseId)->where('slug', 'tinh_gia_dat_vi_pham_quy_hoach')->forceDelete();
                        }
                        AppraiseAppraisalMethods::create([
                            'appraise_id' => $appraiseId,
                            'slug' => "tinh_gia_dat_vi_pham_quy_hoach",
                            'slug_value' => $objects['planning_violation_price_slug'],
                            'value' => isset($objects['planning_violation_price_value']) ? $objects['planning_violation_price_value'] : null,
                        ]);
                    }
                }


                //insert appraise unit price
                $user = CommonService::getUser();

                AppraiseUnitPrice::where('appraise_id', $appraiseId)->forceDelete();
                AppraiseUnitArea::where('appraise_id', $appraiseId)->forceDelete();

                if (isset($objects['asset_general'])) {
                    foreach ($objects["asset_general"] as $item) {
                        $assetGeneral = CompareAssetGeneral::whereId($item["asset_general_id"])->first();
                        if (isset($assetGeneral)) {
                            foreach ($assetGeneral->properties as $property) {
                                if ($property->id == $item["asset_property_detail_id"]) {
                                    foreach ($property->propertyDetail as $propertyDetail) {
                                        AppraiseUnitPrice::insert([
                                            'appraise_id' => $appraiseId,
                                            'asset_general_id' => $assetGeneral->id,
                                            'land_type_id' => $propertyDetail->land_type_purpose,
                                            'update' => 1,
                                            'original_value' => $propertyDetail->circular_unit_price,
                                            //'update_value',
                                            'position_type_id' => $propertyDetail->position_type_id,
                                            'created_by' => $user->id,
                                        ]);
                                        AppraiseUnitArea::insert([
                                            'appraise_id' => $appraiseId,
                                            'asset_general_id' => $assetGeneral->id,
                                            'land_type_id' => $propertyDetail->land_type_purpose,
                                            'position_type_id' => $propertyDetail->position_type_id,
                                            'created_by' => $user->id,
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }

                $rows = $this->findById($appraiseId);
                $rows->asset = isset($objects['assets']) ?? null;
                //$this->indexData($rows);

                if (isset($objects['asset_general']) && !empty($objects['asset_general'])) {
                    CommonService::getAssetPriceTotal($rows);
                }

                // $checkprice = AppraisePrice::where('appraise_id', $appraiseId)->where('value', '<', 0)->get()->first();
                // if(isset($checkprice))
                // {
                //     DB::rollBack();
                //     $data =(object) ['message' => 'Giá trị sau khi tính toán bị âm, vui long kiểm tra lại dữ liệu.' , 'exception' => null];
                //     return $data;
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
    public function updateAppraise($id, array $objects): object
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
                $appraise = new Appraise($objects);
                $appraiseId = $id;
                $appraise->newQuery()->updateOrInsert(['id' => $id], $appraise->attributesToArray());

                AppraisePic::query()->where('appraise_id', '=', $appraiseId)->delete();
                if (isset($objects['pic'])) {
                    foreach ($objects['pic'] as $appraisePic) {
                        $appraisePic['appraise_id'] = $appraiseId;
                        $pic = new AppraisePic($appraisePic);
                        QueryBuilder::for($pic)
                            ->insert($pic->attributesToArray());
                    }
                }
                AppraiseProperty::query()->where('appraise_id', '=', $appraiseId)->delete();
                if (isset($objects['properties'])) {
                    foreach ($objects['properties'] as $propertyData) {
                        $propertyData['asset_appraise_id'] = $appraiseId;
                        $propertyData['appraise_id'] = $appraiseId;
                        $property = new AppraiseProperty($propertyData);
                        $propertyId = QueryBuilder::for($property)
                            ->insertGetId($property->attributesToArray());
                        if (isset($propertyData['property_detail'])) {
                            foreach ($propertyData['property_detail'] as $propertyDetail) {
                                $propertyDetail['appraise_property_id'] = $propertyId;
                                $propertyDetail['k_rate'] = isset($propertyDetail['k_rate']) ? intval($propertyDetail['k_rate']) : 0;
                                $propertyDetail['planning_area'] = isset($propertyDetail['planning_area']) ? floatval($propertyDetail['planning_area']) : 0;
                                $propertyDetail['estimation_value'] = isset($propertyDetail['estimation_value']) ? doubleval($propertyDetail['estimation_value']) : 0;
                                $propertyDetail = array_map(function ($v) {
                                    return (is_null($v)) ? "" : $v;
                                }, $propertyDetail);
                                $detail = new AppraisePropertyDetail($propertyDetail);
                                QueryBuilder::for($detail)
                                    ->insert($detail->attributesToArray());
                            }
                        }

                        if (isset($propertyData['property_turning_time'])) {
                            foreach ($propertyData['property_turning_time'] as $propertyTurningTime) {
                                $propertyTurningTime['appraise_property_id'] = $propertyId;
                                $detail = new AppraisePropertyTurningTime($propertyTurningTime);
                                QueryBuilder::for($detail)
                                    ->insert($detail->attributesToArray());
                            }
                        }
                    }
                }

                AppraiseTangibleAsset::query()->where('appraise_id', '=', $appraiseId)->delete();
                if (isset($objects['tangible_assets'])) {
                    foreach ($objects['tangible_assets'] as $tangibleAssetData) {
                        $tangibleAssetData['appraise_id'] = $appraiseId;
                        $tangibleAsset = new AppraiseTangibleAsset($tangibleAssetData);
                        $tangibleId = QueryBuilder::for($tangibleAsset)
                            ->insertGetId($tangibleAsset->attributesToArray());
                    }
                }

                AppraiseOtherAsset::query()->where('appraise_id', '=', $appraiseId)->delete();
                if (isset($objects['other_assets'])) {
                    foreach ($objects['other_assets'] as $otherAssetData) {
                        $otherAssetData['appraise_id'] = $appraiseId;
                        $otherAssetData["unit_price"] = isset($otherAssetData["unit_price"]) ? intval($otherAssetData["unit_price"]) : 0;
                        $otherAssetData["total_price"] = isset($otherAssetData["total_price"]) ? intval($otherAssetData["total_price"]) : 0;
                        $otherAssetData = array_map(function ($v) {
                            return (is_null($v)) ? "" : $v;
                        }, $otherAssetData);
                        $otherAsset = new AppraiseOtherAsset($otherAssetData);
                        $otherId = QueryBuilder::for($otherAsset)
                            ->insertGetId($otherAsset->attributesToArray());
                    }
                }

                AppraiseLaw::query()->where('appraise_id', '=', $appraiseId)->delete();
                if (isset($objects['appraise_law'])) {
                    foreach ($objects['appraise_law'] as $lawData) {
                        $lawData['appraise_id'] = $appraiseId;
                        if (!$lawData['appraise_law_id']) $lawData['appraise_law_id'] = null;
                        $law = new AppraiseLaw($lawData);
                        $lawId = QueryBuilder::for($law)
                            ->insertGetId($law->attributesToArray());

                        if (isset($lawData['law_details'])) {
                            foreach ($lawData['law_details'] as $lawDetail) {
                                $lawDetail['appraise_law_id'] = $lawId;
                                $lawDetail = new AppraiseLawDetail($lawDetail);
                                QueryBuilder::for($lawDetail)
                                    ->insert($lawDetail->attributesToArray());
                            }
                        }

                        if (isset($lawData['land_details'])) {
                            foreach ($lawData['land_details'] as $landDetail) {
                                $landDetail['appraise_law_id'] = $lawId;
                                $landDetail = new AppraiseLawLandDetail($landDetail);
                                QueryBuilder::for($landDetail)
                                    ->insert($landDetail->attributesToArray());
                            }
                        }
                    }
                }

                ConstructionCompany::query()->where('appraise_id', '=', $appraiseId)->delete();
                if (isset($objects['construction_company'])) {
                    foreach ($objects['construction_company'] as $constructionCompanyData) {
                        $constructionCompanyData['appraise_id'] = $appraiseId;
                        $constructionCompany = AppraisalConstructionCompany::where('id', $constructionCompanyData['construction_company_id'])->first();
                        if (isset($constructionCompany)) {
                            $constructionCompanyData['name'] = $constructionCompany->name;
                            $constructionCompanyData['manager_name'] = $constructionCompany->manager_name;
                            $constructionCompanyData['phone_number'] = $constructionCompany->phone_number;
                            $constructionCompanyData['address'] = $constructionCompany->address;
                            $constructionCompanyData['unit_price_m2'] = $constructionCompany->unit_price_m2;
                            $constructionCompanyData['is_defaults'] = $constructionCompany->is_defaults;
                        }
                        $constructionCompanyData = new ConstructionCompany($constructionCompanyData);
                        $constructionCompanyId = QueryBuilder::for($constructionCompanyData)
                            ->insertGetId($constructionCompanyData->attributesToArray());
                    }
                }

                $isAssetGeneralChange = false;
                $oldAppraise = Appraise::where('id', $id)->first();
                $oldAssetGeneralIds = [];
                if (isset($oldAppraise->assetGeneral)) {
                    foreach ($oldAppraise->assetGeneral as $assetGeneral) {
                        $oldAssetGeneralIds[$assetGeneral->id] = 1;
                    }
                }

                AppraiseHasAsset::query()->where('appraise_id', '=', $appraiseId)->forceDelete();
                if (isset($objects['asset_general'])) {
                    foreach ($objects['asset_general'] as $assetGeneralData) {
                        if (isset($assetGeneralData['asset_general_id']) && !isset($oldAssetGeneralIds[$assetGeneralData['asset_general_id']])) {
                            $isAssetGeneralChange = true;
                        }
                        $assetGeneralData['appraise_id'] = $appraiseId;
                        $assetGeneralData = new AppraiseHasAsset($assetGeneralData);
                        $assetGeneralId = QueryBuilder::for($assetGeneralData)
                            ->insertGetId($assetGeneralData->attributesToArray());
                    }
                }
                // thêm kiểm ra appraise adaper
                // if (isset($objects['appraise_adapter'])&&!empty($objects['appraise_adapter'])) {
                //     foreach($objects['appraise_adapter'] as $item) {
                //         $appraiseAdapter = AppraiseAdapter::where('appraise_id', $item['appraise_id'])
                //             ->where('asset_general_id', $item['asset_general_id'])->first();
                //         if(isset($appraiseAdapter)) {
                //             AppraiseAdapter::where('id', $appraiseAdapter->id)->update([
                //                 'percent' => $item['percent'],
                //                 'change_purpose_price' => $item['change_purpose_price']
                //             ]);
                //             $existAssetGeneralIds[] = $appraiseAdapter->id;
                //         } else {
                //             $appraiseAdapterId = AppraiseAdapter::insert([
                //                 'appraise_id' => $item['appraise_id'],
                //                 'asset_general_id' => $item['asset_general_id'],
                //                 'percent' => $item['percent'],
                //                 'change_purpose_price' => $item['change_purpose_price']
                //             ]);
                //             $existAssetGeneralIds[] = $appraiseAdapterId;
                //         }
                //     }
                //     //AppraiseAdapter::where('appraise_id', $appraiseId)->whereNotIn('id', $existAssetGeneralIds)->delete();
                // }

                if (isset($objects['unify_indicative_price_slug'])) {
                    $items = AppraiseAppraisalMethods::where('appraise_id', $appraiseId)->where('slug', 'thong_nhat_muc_gia_chi_dan')->get();
                    if (count($items) == 1) {
                        AppraiseAppraisalMethods::where('appraise_id', $appraiseId)->where('slug', 'thong_nhat_muc_gia_chi_dan')->update([
                            'appraise_id' => $appraiseId,
                            'slug' => "thong_nhat_muc_gia_chi_dan",
                            'slug_value' => $objects['unify_indicative_price_slug'],
                        ]);
                    } else {
                        if (count($items) > 1) {
                            AppraiseAppraisalMethods::where('appraise_id', $appraiseId)->where('slug', 'thong_nhat_muc_gia_chi_dan')->forceDelete();
                        }
                        AppraiseAppraisalMethods::create([
                            'appraise_id' => $appraiseId,
                            'slug' => "thong_nhat_muc_gia_chi_dan",
                            'slug_value' => $objects['unify_indicative_price_slug'],
                        ]);
                    }
                }

                if (isset($objects['composite_land_remaning_slug'])) {
                    $items = AppraiseAppraisalMethods::where('appraise_id', $appraiseId)->where('slug', 'tinh_gia_dat_hon_hop_con_lai')->get();
                    if (count($items) == 1) {
                        AppraiseAppraisalMethods::where('appraise_id', $appraiseId)->where('slug', 'tinh_gia_dat_hon_hop_con_lai')->update([
                            'appraise_id' => $appraiseId,
                            'slug' => "tinh_gia_dat_hon_hop_con_lai",
                            'slug_value' => $objects['composite_land_remaning_slug'],
                            'value' => isset($objects['composite_land_remaning_value']) ? $objects['composite_land_remaning_value'] : null,
                        ]);
                    } else {
                        if (count($items) > 1) {
                            AppraiseAppraisalMethods::where('appraise_id', $appraiseId)->where('slug', 'tinh_gia_dat_hon_hop_con_lai')->forceDelete();
                        }
                        AppraiseAppraisalMethods::create([
                            'appraise_id' => $appraiseId,
                            'slug' => "tinh_gia_dat_hon_hop_con_lai",
                            'slug_value' => $objects['composite_land_remaning_slug'],
                            'value' => isset($objects['composite_land_remaning_value']) ? $objects['composite_land_remaning_value'] : null,
                        ]);
                    }
                }

                if (isset($objects['planning_violation_price_slug'])) {
                    $items = AppraiseAppraisalMethods::where('appraise_id', $appraiseId)->where('slug', 'tinh_gia_dat_vi_pham_quy_hoach')->get();
                    if (count($items) == 1) {
                        AppraiseAppraisalMethods::where('appraise_id', $appraiseId)->where('slug', 'tinh_gia_dat_vi_pham_quy_hoach')->update([
                            'appraise_id' => $appraiseId,
                            'slug' => "tinh_gia_dat_vi_pham_quy_hoach",
                            'slug_value' => $objects['planning_violation_price_slug'],
                            'value' => isset($objects['planning_violation_price_value']) ? $objects['planning_violation_price_value'] : null,
                        ]);
                    } else {
                        if (count($items) > 1) {
                            AppraiseAppraisalMethods::where('appraise_id', $appraiseId)->where('slug', 'tinh_gia_dat_vi_pham_quy_hoach')->forceDelete();
                        }
                        AppraiseAppraisalMethods::create([
                            'appraise_id' => $appraiseId,
                            'slug' => "tinh_gia_dat_vi_pham_quy_hoach",
                            'slug_value' => $objects['planning_violation_price_slug'],
                            'value' => isset($objects['planning_violation_price_value']) ? $objects['planning_violation_price_value'] : null,
                        ]);
                    }
                }

                //AppraiseComparisonFactor::query()->where('appraise_id', '=', $appraiseId)->forceDelete();
                //$this->comparisonFactor($appraiseId, $objects);
                $this->comparisonFactor($appraiseId, $objects, $oldAssetGeneralIds);

                $rows = $this->findById($appraiseId);
                $rows->asset = $objects['assets'] ?? null;
                //$this->indexData($rows);

                if (isset($objects['asset_general']) && !empty($objects['asset_general'])) {
                    CommonService::getAssetPriceTotal($rows);
                }

                // $checkprice = AppraisePrice::where('appraise_id',$appraiseId)->where('value', '<', 0)->get()->first();
                // if(isset($checkprice))
                // {
                //     DB::rollBack();
                //     $data =(object) ['message' => 'Giá trị sau khi tính toán bị âm, vui long kiểm tra lại dữ liệu.' , 'exception' => null];
                //     return $data;
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
    public function deleteAppraise($id)
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
        $search_result = AppraiseVersion::searchByQuery($array, null, null, 1, 0, null);
        if (isset($search_result[0])) {
            return $search_result[0];
        } else {
            return null;
        }
    }

    /**
     * @param $id
     * @return $objects
     */
    public function getComparisonFactor($id): array
    {
        $label = [
            "phap_ly" => "Pháp lý",
            "quy_mo" => "Quy mô",
            "chieu_rong_mat_tien" => "Chiều rộng mặt tiền",
            "chieu_sau_khu_dat" => "Chiều sâu khu đất",
            "hinh_dang_dat" => "Hình dáng đất",
            "giao_thong" => "Giao thông",
            "ket_cau_duong" => "Kết cấu đường",
            "do_rong_duong" => "Bề rộng đường",
            "dieu_kien_ha_tang" => "Điều kiện hạ tầng",
            "kinh_doanh" => "Kinh doanh",
            "an_ninh_moi_truong_song" => "An ninh, môi trường sống",
            "phong_thuy" => "Phong thủy",
            "quy_hoach" => "Quy hoạch/hiện trạng",
            "dieu_kien_thanh_toan" => "Điều kiện thanh toán",
            "khoang_cach" => "Khoảng cách TSSS đến TSTĐ",
            "muc_dich_chinh"  => "Mục đích sử dụng đất chính",
            //'yeu_to_khac' => 'Yếu tố khác',
        ];

        $checked = [
            "phap_ly" => 0,
            "quy_mo" => 0,
            "chieu_rong_mat_tien" => 0,
            "chieu_sau_khu_dat" => 0,
            "hinh_dang_dat" => 0,
            "giao_thong" => 0,
            "ket_cau_duong" => 0,
            "do_rong_duong" => 0,
            "dieu_kien_ha_tang" => 0,
            "kinh_doanh" => 0,
            "an_ninh_moi_truong_song" => 0,
            "phong_thuy" => 0,
            "quy_hoach" => 0,
            "dieu_kien_thanh_toan" => 0,
            "khoang_cach" => 0,
            "muc_dich_chinh" => 0
            //"yeu_to_khac" => 0
        ];
        $items = $this->model->query()
            ->where('id', '=', $id)
            //->with('comparisonFactor')
            //->with('assetGeneral')
            ->get();
        foreach ($items as $stt => $item) {
            $items[$stt]->append('comparisonFactor');
            $items[$stt]->append('asset_general');
            $items[$stt]->assetGeneral = $items[$stt]->asset_general;
        }
        $result = [];
        foreach ($items as $item) {
            if (isset($item->assetGeneral) && !empty($item->assetGeneral)) {
                foreach ($item->assetGeneral as $index => $assetGeneral) {
                    foreach ($item->comparisonFactor as $comparisonFactor) {
                        if (($comparisonFactor->appraise_id == $id)
                            && ($comparisonFactor->asset_general_id == $assetGeneral->id)
                            && (isset($label[$comparisonFactor->type]))
                        ) {
                            if (($checked[$comparisonFactor->type]) || (!$comparisonFactor->status)) continue;
                            $result[$id][] = [
                                "type" => $comparisonFactor->type,
                                "label" => $label[$comparisonFactor->type],
                                "status" => $comparisonFactor->status,
                                "asset_id" => $assetGeneral->id,
                                "asset_general_id" => $comparisonFactor->asset_general_id,
                            ];
                            $checked[$comparisonFactor->type]++;
                        }
                    }
                    if ($index) continue;
                }
            } else {
                $result[$id][] = [
                    "type" => "phap_ly",
                    "label" => $label["phap_ly"],
                    "status" => 1
                ];
            }
        }

        return $result;
    }

    /**
     * @param $id
     * @return void
     */
    public function comparisonFactor($id, $datas, $oldAssetGeneralIds = [])
    {
        $appraiseId = $id;
        $allComparisonFactor = [
            "phap_ly",
            "quy_mo",
            "chieu_rong_mat_tien",
            "chieu_sau_khu_dat",
            "hinh_dang_dat",
            "giao_thong",

            "ket_cau_duong",

            "do_rong_duong",
            "dieu_kien_ha_tang",
            "kinh_doanh",
            "an_ninh_moi_truong_song",
            "phong_thuy",

            "quy_hoach",
            "dieu_kien_thanh_toan",

            "yeu_to_khac",
            "khoang_cach",
            "muc_dich_chinh",
        ];

        $object = $this->findById($id);
        $dictionaries = $this->findAllAppraiseDictionaries();
        $comparisonFactorInput = [];
        foreach ($datas['comparison_factor'] as $item) {
            switch ($item['comparison_factor']) {
                case "Pháp lý":
                    $comparisonFactorInput[] = "phap_ly";
                    break;
                case "Quy mô":
                    $comparisonFactorInput[] = "quy_mo";
                    break;
                case "Chiều rộng mặt tiền":
                    $comparisonFactorInput[] = "chieu_rong_mat_tien";
                    break;
                case "Chiều sâu khu đất":
                    $comparisonFactorInput[] = "chieu_sau_khu_dat";
                    break;
                case "Hình dáng đất":
                    $comparisonFactorInput[] = "hinh_dang_dat";
                    break;
                case "Giao thông":
                    $comparisonFactorInput[] = "giao_thong";
                    break;
                case "Kết cấu đường":
                    $comparisonFactorInput[] = "ket_cau_duong";
                    break;
                case "Bề rộng đường":
                    $comparisonFactorInput[] = "do_rong_duong";
                    break;
                case "Điều kiện hạ tầng":
                    $comparisonFactorInput[] = "dieu_kien_ha_tang";
                    break;
                case "Kinh doanh":
                    $comparisonFactorInput[] = "kinh_doanh";
                    break;
                case "An ninh, môi trường sống":
                    $comparisonFactorInput[] = "an_ninh_moi_truong_song";
                    break;
                case "Phong thủy":
                    $comparisonFactorInput[] = "phong_thuy";
                    break;
                case "Quy hoạch/hiện trạng":
                    $comparisonFactorInput[] = "quy_hoach";
                    break;
                case "Điều kiện thanh toán":
                    $comparisonFactorInput[] = "dieu_kien_thanh_toan";
                    break;
                case "Khoảng cách TSSS đến TSTĐ":
                    $comparisonFactorInput[] = "khoang cach";
                    break;
                default:
                    $comparisonFactorInput[] = "yeu_to_khac";
                    break;
            }
        }

        foreach ($allComparisonFactor as $comparisonFactorTmp) {
            foreach ($object->appraiseHasAssets as $appraiseHasAsset) {
                if (isset($appraiseHasAsset['asset_general_id']) && isset($oldAssetGeneralIds[$appraiseHasAsset['asset_general_id']])) {
                    //update ComparisonFactor
                    $oldAssetGeneralIds[$appraiseHasAsset['asset_general_id']] = 0;
                    if (in_array($comparisonFactorTmp, $comparisonFactorInput)) {
                        AppraiseComparisonFactor::where('appraise_id', $appraiseId)
                            ->where('type', $comparisonFactorTmp)
                            ->update(['status' => 1]);
                    } else {
                        if (($comparisonFactorTmp != "yeu_to_khac") && $comparisonFactorTmp != "phap_ly") {
                            AppraiseComparisonFactor::where('appraise_id', $appraiseId)
                                ->where('type', $comparisonFactorTmp)
                                ->update(['status' => 0]);
                        }
                    }
                    continue;
                }
                $assets = $this->findAssetGeneralById($appraiseHasAsset->asset_general_id);
                $asset = null;
                foreach ($assets->properties as $property) {
                    if ($appraiseHasAsset->asset_property_detail_id == $property->id) {
                        $asset = $property;
                    }
                }

                $this->getUnitPrice($object, $asset, $appraiseHasAsset);
                if ($comparisonFactorTmp == 'phap_ly') {
                    $assetLegal = $asset->legal->description ?? 'Không biết';
                    $appraiseLegal = $object->properties[0]->legal->description ?? 'Không biết';
                    $comparisonFactor = [
                        'appraise_id' => $id,
                        'asset_general_id' => $appraiseHasAsset->asset_general_id,
                        'status' => (in_array('phap_ly', $comparisonFactorInput)),
                        'type' => 'phap_ly',
                        'appraise_title' => $appraiseLegal,
                        'asset_title' => $assetLegal,
                        'description' => CompareMaterData::COMPARISONS_DESCRIPTION['tuong_dong'],
                        'adjust_percent' => 0,
                        'name' => 'Pháp lý'
                    ];
                    $comparisonFactor = new AppraiseComparisonFactor($comparisonFactor);
                    $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                        ->insertGetId($comparisonFactor->attributesToArray());

                    foreach ($dictionaries['phap_ly'] as $dictionary) {
                        if (($appraiseLegal == $dictionary->appraise_title) && ($assetLegal == $dictionary->asset_title)) {
                            $comparisonFactor = [
                                'appraise_id' => $id,
                                'asset_general_id' => $appraiseHasAsset->asset_general_id,
                                'status' => (in_array('phap_ly', $comparisonFactorInput)),
                                'type' => 'phap_ly',
                                'appraise_title' => $appraiseLegal,
                                'asset_title' => $assetLegal,
                                'description' => $dictionary->description,
                                'adjust_percent' => $dictionary->adjust_percent,
                                'name' => 'Pháp lý'
                            ];
                            $comparisonFactor = new AppraiseComparisonFactor($comparisonFactor);
                            $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                                ->updateOrInsert(['id' => $comparisonFactorId], $comparisonFactor->attributesToArray());
                        }
                    }
                } else if ($comparisonFactorTmp == 'hinh_dang_dat') {
                    $assetlandShape = $asset->landShape->description ?? 'Không biết';
                    $appraiselandShape = $object->properties[0]->landShape->description ?? 'Không biết';

                    $comparisonFactor = [
                        'appraise_id' => $id,
                        'asset_general_id' => $appraiseHasAsset->asset_general_id,
                        'status' => (in_array('hinh_dang_dat', $comparisonFactorInput)),
                        'type' => 'hinh_dang_dat',
                        'appraise_title' => $appraiselandShape,
                        'asset_title' => $assetlandShape,
                        'description' => CompareMaterData::COMPARISONS_DESCRIPTION['tuong_dong'],
                        'adjust_percent' => 0,
                        'name' => 'Hình dáng đất'
                    ];

                    $comparisonFactor = new AppraiseComparisonFactor($comparisonFactor);
                    $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                        ->insertGetId($comparisonFactor->attributesToArray());
                    foreach ($dictionaries['hinh_dang_dat'] as $dictionary) {
                        if (($appraiselandShape == $dictionary->appraise_title) && ($assetlandShape == $dictionary->asset_title)) {
                            $comparisonFactor = [
                                'appraise_id' => $id,
                                'asset_general_id' => $appraiseHasAsset->asset_general_id,
                                'status' => (in_array('hinh_dang_dat', $comparisonFactorInput)),
                                'type' => 'hinh_dang_dat',
                                'appraise_title' => $appraiselandShape,
                                'asset_title' => $assetlandShape,
                                'description' => $dictionary->description,
                                'adjust_percent' => $dictionary->adjust_percent,
                                'name' => 'Hình dáng đất'
                            ];
                            $comparisonFactor = new AppraiseComparisonFactor($comparisonFactor);
                            $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                                ->updateOrInsert(['id' => $comparisonFactorId], $comparisonFactor->attributesToArray());
                        }
                    }
                } else if ($comparisonFactorTmp == 'giao_thong') {
                    $appraiseMainTurning  = "KHÔNG ĐẤU NỐI ĐƯỜNG CHÍNH";
                    $assetMainTurning = "KHÔNG ĐẤU NỐI ĐƯỜNG CHÍNH";
                    if (isset($asset->properties)) {
                        foreach ($asset->properties as $property) {
                            if (isset($property->propertyTurningTime) && count($property->propertyTurningTime)) {
                                $assetMainTurning = "ĐẤU NỐI TRỰC TIẾP ĐƯỜNG CHÍNH";
                                break;
                            }
                        }
                    }
                    if (isset($object->properties)) {
                        foreach ($object->properties as $property) {
                            if (isset($property->propertyTurningTime) && count($property->propertyTurningTime)) {
                                $appraiseMainTurning = "ĐẤU NỐI TRỰC TIẾP ĐƯỜNG CHÍNH";
                                break;
                            }
                        }
                    }

                    $comparisonFactor = [
                        'appraise_id' => $id,
                        'asset_general_id' => $appraiseHasAsset->asset_general_id,
                        'status' => (in_array('giao_thong', $comparisonFactorInput)),
                        'type' => 'hinh_dang_dat',
                        'appraise_title' => $appraiseMainTurning,
                        'asset_title' => $assetMainTurning,
                        'description' => CompareMaterData::COMPARISONS_DESCRIPTION['tuong_dong'],
                        'adjust_percent' => 0,
                        'name' => 'Giao thông'
                    ];

                    $comparisonFactor = new AppraiseComparisonFactor($comparisonFactor);
                    $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                        ->insertGetId($comparisonFactor->attributesToArray());

                    foreach ($dictionaries['giao_thong'] as $dictionary) {
                        if (($appraiseMainTurning == $dictionary->appraise_title) && ($assetMainTurning == $dictionary->asset_title)) {
                            $comparisonFactor = [
                                'appraise_id' => $id,
                                'asset_general_id' => $appraiseHasAsset->asset_general_id,
                                'status' => (in_array('giao_thong', $comparisonFactorInput)),
                                'type' => 'giao_thong',
                                'appraise_title' => $appraiseMainTurning,
                                'asset_title' => $assetMainTurning,
                                'description' => $dictionary->description,
                                'adjust_percent' => $dictionary->adjust_percent,
                                'name' => 'Giao thông'
                            ];
                            $comparisonFactor = new AppraiseComparisonFactor($comparisonFactor);
                            $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                                ->updateOrInsert(['id' => $comparisonFactorId], $comparisonFactor->attributesToArray());
                        }
                    }
                } else if ($comparisonFactorTmp == 'kinh_doanh') {
                    $assetBusiness = $asset->business->description ?? 'Không biết';
                    $appraiseBusiness = $object->properties[0]->business->description ?? 'Không biết';

                    $comparisonFactor = [
                        'appraise_id' => $id,
                        'asset_general_id' => $appraiseHasAsset->asset_general_id,
                        'status' => (in_array('kinh_doanh', $comparisonFactorInput)),
                        'type' => 'kinh_doanh',
                        'appraise_title' => $appraiseBusiness,
                        'asset_title' => $assetBusiness,
                        'description' => CompareMaterData::COMPARISONS_DESCRIPTION['tuong_dong'],
                        'adjust_percent' => 0,
                        'name' => 'Kinh doanh'
                    ];
                    $comparisonFactor = new AppraiseComparisonFactor($comparisonFactor);
                    $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                        ->insertGetId($comparisonFactor->attributesToArray());

                    foreach ($dictionaries['kinh_doanh'] as $dictionary) {
                        if (($appraiseBusiness == $dictionary->appraise_title) && ($assetBusiness == $dictionary->asset_title)) {
                            $comparisonFactor = [
                                'appraise_id' => $id,
                                'asset_general_id' => $appraiseHasAsset->asset_general_id,
                                'status' => (in_array('kinh_doanh', $comparisonFactorInput)),
                                'type' => 'kinh_doanh',
                                'appraise_title' => $appraiseBusiness,
                                'asset_title' => $assetBusiness,
                                'description' => $dictionary->description,
                                'adjust_percent' => $dictionary->adjust_percent,
                                'name' => 'Kinh doanh'
                            ];
                            $comparisonFactor = new AppraiseComparisonFactor($comparisonFactor);
                            $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                                ->updateOrInsert(['id' => $comparisonFactorId], $comparisonFactor->attributesToArray());
                        }
                    }
                } else if ($comparisonFactorTmp == 'dieu_kien_ha_tang') {
                    $assetConditions = $asset->conditions->description ?? 'Không biết';
                    $appraiseConditions = $object->properties[0]->conditions->description ?? 'Không biết';
                    $comparisonFactor = [
                        'appraise_id' => $id,
                        'asset_general_id' => $appraiseHasAsset->asset_general_id,
                        'status' => (in_array('dieu_kien_ha_tang', $comparisonFactorInput)),
                        'type' => 'dieu_kien_ha_tang',
                        'appraise_title' => $appraiseConditions,
                        'asset_title' => $assetConditions,
                        'description' => CompareMaterData::COMPARISONS_DESCRIPTION['tuong_dong'],
                        'adjust_percent' => 0,
                        'name' => 'Điều kiện hạ tầng'
                    ];
                    $comparisonFactor = new AppraiseComparisonFactor($comparisonFactor);
                    $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                        ->insertGetId($comparisonFactor->attributesToArray());
                    foreach ($dictionaries['dieu_kien_ha_tang'] as $dictionary) {
                        if (($appraiseConditions == $dictionary->appraise_title) && ($assetConditions == $dictionary->asset_title)) {
                            $comparisonFactor = [
                                'appraise_id' => $id,
                                'asset_general_id' => $appraiseHasAsset->asset_general_id,
                                'status' => (in_array('dieu_kien_ha_tang', $comparisonFactorInput)),
                                'type' => 'dieu_kien_ha_tang',
                                'appraise_title' => $appraiseConditions,
                                'asset_title' => $assetConditions,
                                'description' => $dictionary->description,
                                'adjust_percent' => $dictionary->adjust_percent,
                                'name' => 'Điều kiện hạ tầng'
                            ];
                            $comparisonFactor = new AppraiseComparisonFactor($comparisonFactor);
                            $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                                ->updateOrInsert(['id' => $comparisonFactorId], $comparisonFactor->attributesToArray());
                        }
                    }
                } else if ($comparisonFactorTmp == 'an_ninh_moi_truong_song') {
                    $assetSocialSecurity = $asset->socialSecurity->description ?? 'Không biết';
                    $appraiseSocialSecurity = $object->properties[0]->socialSecurity->description ?? 'Không biết';
                    $comparisonFactor = [
                        'appraise_id' => $id,
                        'asset_general_id' => $appraiseHasAsset->asset_general_id,
                        'status' => (in_array('an_ninh_moi_truong_song', $comparisonFactorInput)),
                        'type' => 'an_ninh_moi_truong_song',
                        'appraise_title' => $appraiseSocialSecurity,
                        'asset_title' => $assetSocialSecurity,
                        'description' => CompareMaterData::COMPARISONS_DESCRIPTION['tuong_dong'],
                        'adjust_percent' => 0,
                        'name' => 'An ninh, môi trường sống'
                    ];
                    $comparisonFactor = new AppraiseComparisonFactor($comparisonFactor);
                    $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                        ->insertGetId($comparisonFactor->attributesToArray());
                    foreach ($dictionaries['an_ninh_moi_truong_song'] as $dictionary) {
                        if (($appraiseSocialSecurity == $dictionary->appraise_title) && ($assetSocialSecurity == $dictionary->asset_title)) {
                            $comparisonFactor = [
                                'appraise_id' => $id,
                                'asset_general_id' => $appraiseHasAsset->asset_general_id,
                                'status' => (in_array('an_ninh_moi_truong_song', $comparisonFactorInput)),
                                'type' => 'an_ninh_moi_truong_song',
                                'appraise_title' => $appraiseSocialSecurity,
                                'asset_title' => $assetSocialSecurity,
                                'description' => $dictionary->description,
                                'adjust_percent' => $dictionary->adjust_percent,
                                'name' => 'An ninh, môi trường sống'
                            ];
                            $comparisonFactor = new AppraiseComparisonFactor($comparisonFactor);
                            $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                                ->updateOrInsert(['id' => $comparisonFactorId], $comparisonFactor->attributesToArray());
                        }
                    }
                } else if ($comparisonFactorTmp == 'phong_thuy') {
                    $assetFengShui = $asset->fengShui->description ?? 'Không biết';
                    $appraiseFengShui = $object->properties[0]->fengShui->description ?? 'Không biết';
                    $comparisonFactor = [
                        'appraise_id' => $id,
                        'asset_general_id' => $appraiseHasAsset->asset_general_id,
                        'status' => (in_array('phong_thuy', $comparisonFactorInput)),
                        'type' => 'phong_thuy',
                        'appraise_title' => $appraiseFengShui,
                        'asset_title' => $assetFengShui,
                        'description' => CompareMaterData::COMPARISONS_DESCRIPTION['tuong_dong'],
                        'adjust_percent' => 0,
                        'name' => 'Phong thủy'
                    ];
                    $comparisonFactor = new AppraiseComparisonFactor($comparisonFactor);
                    $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                        ->insertGetId($comparisonFactor->attributesToArray());
                    foreach ($dictionaries['phong_thuy'] as $dictionary) {
                        if (($appraiseFengShui == $dictionary->appraise_title) && ($assetFengShui == $dictionary->asset_title)) {
                            $comparisonFactor = [
                                'appraise_id' => $id,
                                'asset_general_id' => $appraiseHasAsset->asset_general_id,
                                'status' => (in_array('phong_thuy', $comparisonFactorInput)),
                                'type' => 'phong_thuy',
                                'appraise_title' => $appraiseFengShui,
                                'asset_title' => $assetFengShui,
                                'description' => $dictionary->description,
                                'adjust_percent' => $dictionary->adjust_percent,
                                'name' => 'Phong thủy'
                            ];
                            $comparisonFactor = new AppraiseComparisonFactor($comparisonFactor);
                            $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                                ->updateOrInsert(['id' => $comparisonFactorId], $comparisonFactor->attributesToArray());
                        }
                    }
                } else if ($comparisonFactorTmp == 'quy_mo') {
                    $assetLandSumArea = $asset->asset_general_land_sum_area ?? 0;
                    $appraiseLandSumArea = $object->properties[0]->appraise_land_sum_area ?? 0;
                    $description = CompareMaterData::COMPARISONS_DESCRIPTION['tuong_dong'];

                    $comparisonFactor = [
                        'appraise_id' => $id,
                        'asset_general_id' => $appraiseHasAsset->asset_general_id,
                        'status' => (in_array('quy_mo', $comparisonFactorInput)),
                        'type' => 'quy_mo',
                        /* 'appraise_title' => number_format($appraiseLandSumArea, 1, ',', '.'),
						'asset_title' => number_format($assetLandSumArea, 1, ',', '.'), */
                        'appraise_title' => $appraiseLandSumArea,
                        'asset_title' => $assetLandSumArea,
                        'description' => $description,
                        'adjust_percent' => 0,
                        'name' => 'Quy mô'
                    ];
                    $comparisonFactor = new AppraiseComparisonFactor($comparisonFactor);
                    $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                        ->insertGetId($comparisonFactor->attributesToArray());
                } else if ($comparisonFactorTmp == 'chieu_rong_mat_tien') {
                    $assetFrontSide = $asset->front_side_width ?? 0;
                    $appraiseFrontSide = $object->properties[0]->front_side_width ?? 0;

                    $description = CompareMaterData::COMPARISONS_DESCRIPTION['tuong_dong'];

                    $comparisonFactor = [
                        'appraise_id' => $id,
                        'asset_general_id' => $appraiseHasAsset->asset_general_id,
                        'status' => (in_array('chieu_rong_mat_tien', $comparisonFactorInput)),
                        'type' => 'chieu_rong_mat_tien',
                        'appraise_title' => $appraiseFrontSide,
                        'asset_title' => $assetFrontSide,
                        'description' => $description,
                        'adjust_percent' => 0,
                        'name' => 'Chiều rộng mặt tiền'
                    ];

                    $comparisonFactor = new AppraiseComparisonFactor($comparisonFactor);
                    $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                        ->insertGetId($comparisonFactor->attributesToArray());
                } else if ($comparisonFactorTmp == 'chieu_sau_khu_dat') {
                    $assetInsight = $asset->insight_width ?? 0;
                    $appraiseInsight = $object->properties[0]->insight_width ?? 0;

                    $description = CompareMaterData::COMPARISONS_DESCRIPTION['tuong_dong'];

                    $comparisonFactor = [
                        'appraise_id' => $id,
                        'asset_general_id' => $appraiseHasAsset->asset_general_id,
                        'status' => (in_array('chieu_sau_khu_dat', $comparisonFactorInput)),
                        'type' => 'chieu_sau_khu_dat',
                        'appraise_title' => $appraiseInsight,
                        'asset_title' => $assetInsight,
                        'description' => $description,
                        'adjust_percent' => 0,
                        'name' => 'Chiều sâu khu đất'
                    ];
                    $comparisonFactor = new AppraiseComparisonFactor($comparisonFactor);
                    $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                        ->insertGetId($comparisonFactor->attributesToArray());
                } else if ($comparisonFactorTmp == 'do_rong_duong') {
                    $assetRoad = (float)($asset->main_road_length ?? 0);
                    if ($assetRoad == 0) {
                        if (isset($asset->comparePropertyTurningTime)) {
                            foreach ($asset->comparePropertyTurningTime as $comparePropertyTurningTime) {
                                $assetRoad = (float)$comparePropertyTurningTime->main_road_length;
                            }
                        }
                    }

                    $appraiseRoad = (float)($object->properties[0]->main_road_length ?? 0);

                    if ($appraiseRoad == 0) {
                        if (isset($object->properties[0]->propertyTurningTime)) {
                            foreach ($object->properties[0]->propertyTurningTime as $propertyTurningTime) {
                                $appraiseRoad = (float)$propertyTurningTime->main_road_length;
                            }
                        }
                    }

                    $assetRoadLength = 'Không biết';
                    $appraiseRoadLength = 'Không biết';

                    if ($assetRoad != 0 && $assetRoad < 2) {
                        $assetRoadLength = '< 2m';
                    }
                    if ($assetRoad >= 2 && $assetRoad < 3) {
                        $assetRoadLength = '≥ 2m đến < 3m';
                    }
                    if ($assetRoad >= 3 && $assetRoad < 5) {
                        $assetRoadLength = '≥ 3m đến < 5m';
                    }
                    if ($assetRoad >= 5) {
                        $assetRoadLength = '≥ 5m';
                    }

                    if ($appraiseRoad != 0 && $appraiseRoad < 2) {
                        $appraiseRoadLength = '< 2m';
                    }
                    if ($appraiseRoad >= 2 && $appraiseRoad < 3) {
                        $appraiseRoadLength = '≥ 2m đến < 3m';
                    }
                    if ($appraiseRoad >= 3 && $appraiseRoad < 5) {
                        $appraiseRoadLength = '≥ 3m đến < 5m';
                    }
                    if ($appraiseRoad >= 5) {
                        $appraiseRoadLength = '≥ 5m';
                    }

                    $description = CompareMaterData::COMPARISONS_DESCRIPTION['tuong_dong'];

                    $comparisonFactor = [
                        'appraise_id' => $id,
                        'asset_general_id' => $appraiseHasAsset->asset_general_id,
                        'status' => (in_array('do_rong_duong', $comparisonFactorInput)),
                        'type' => 'do_rong_duong',
                        'appraise_title' => $appraiseRoad,
                        'asset_title' => $assetRoad,
                        'description' => $description,
                        'adjust_percent' => 0,
                        'name' => 'Bề rộng đường'
                    ];
                    $comparisonFactor = new AppraiseComparisonFactor($comparisonFactor);
                    $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                        ->insertGetId($comparisonFactor->attributesToArray());
                    foreach ($dictionaries['do_rong_duong'] as $dictionary) {
                        if (($appraiseRoadLength == $dictionary->appraise_title) && ($assetRoadLength == $dictionary->asset_title)) {
                            $comparisonFactor = [
                                'appraise_id' => $id,
                                'asset_general_id' => $appraiseHasAsset->asset_general_id,
                                'status' => (in_array('do_rong_duong', $comparisonFactorInput)),
                                'type' => 'do_rong_duong',
                                'appraise_title' => $appraiseRoad,
                                'asset_title' => $assetRoad,
                                'description' => $dictionary->description,
                                'adjust_percent' => $dictionary->adjust_percent,
                                'name' => 'Bề rộng đường'
                            ];

                            $comparisonFactor = new AppraiseComparisonFactor($comparisonFactor);
                            $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                                ->updateOrInsert(['id' => $comparisonFactorId], $comparisonFactor->attributesToArray());
                        }
                    }
                } else if ($comparisonFactorTmp == 'ket_cau_duong') {
                    $assetMaterial = $asset->material->description ?? 'Không biết';
                    $appraiseMaterial = $object->properties[0]->material->description ?? 'Không biết';
                    if ($assetMaterial == 'Không biết') {
                        if (isset($asset->comparePropertyTurningTime)) {
                            foreach ($asset->comparePropertyTurningTime as $comparePropertyTurningTime) {
                                $assetMaterial = $comparePropertyTurningTime->material->description ?? $assetMaterial;
                            }
                        }
                    }

                    if ($appraiseMaterial == 'Không biết') {
                        if (isset($object->properties[0]->propertyTurningTime)) {
                            foreach ($object->properties[0]->propertyTurningTime as $propertyTurningTime) {
                                $appraiseMaterial = $propertyTurningTime->material->description ?? $appraiseMaterial;
                            }
                        }
                    }

                    $comparisonFactor = [
                        'appraise_id' => $id,
                        'asset_general_id' => $appraiseHasAsset->asset_general_id,
                        'status' => (in_array('ket_cau_duong', $comparisonFactorInput)),
                        'type' => 'ket_cau_duong',
                        'appraise_title' => $appraiseMaterial,
                        'asset_title' => $assetMaterial,
                        'description' => CompareMaterData::COMPARISONS_DESCRIPTION['tuong_dong'],
                        'adjust_percent' => 0,
                        'name' => 'Kết cấu đường'
                    ];
                    $comparisonFactor = new AppraiseComparisonFactor($comparisonFactor);
                    $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                        ->insertGetId($comparisonFactor->attributesToArray());
                    foreach ($dictionaries['ket_cau_duong'] as $dictionary) {
                        if (($appraiseMaterial == $dictionary->appraise_title) && ($assetMaterial == $dictionary->asset_title)) {
                            $comparisonFactor = [
                                'appraise_id' => $id,
                                'asset_general_id' => $appraiseHasAsset->asset_general_id,
                                'status' => (in_array('ket_cau_duong', $comparisonFactorInput)),
                                'type' => 'ket_cau_duong',
                                'appraise_title' => $appraiseMaterial,
                                'asset_title' => $assetMaterial,
                                'description' => $dictionary->description,
                                'adjust_percent' => $dictionary->adjust_percent,
                                'name' => 'Kết cấu đường'
                            ];
                            $comparisonFactor = new AppraiseComparisonFactor($comparisonFactor);
                            $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                                ->updateOrInsert(['id' => $comparisonFactorId], $comparisonFactor->attributesToArray());
                        }
                    }
                } else if ($comparisonFactorTmp == 'dieu_kien_thanh_toan') {
                    $assetPaymenMethod = $asset->paymenMethod->description ?? 'Không biết';
                    $appraisePaymenMethod = $object->properties[0]->paymenMethod->description ?? 'Không biết';
                    $comparisonFactor = [
                        'appraise_id' => $id,
                        'asset_general_id' => $appraiseHasAsset->asset_general_id,
                        'status' => (in_array('dieu_kien_thanh_toan', $comparisonFactorInput)),
                        'type' => 'dieu_kien_thanh_toan',
                        'appraise_title' => $appraisePaymenMethod,
                        'asset_title' => $assetPaymenMethod,
                        'description' => CompareMaterData::COMPARISONS_DESCRIPTION['tuong_dong'],
                        'adjust_percent' => 0,
                        'name' => 'Điều kiện thanh toán'
                    ];

                    $comparisonFactor = new AppraiseComparisonFactor($comparisonFactor);
                    $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                        ->insertGetId($comparisonFactor->attributesToArray());
                    foreach ($dictionaries['dieu_kien_thanh_toan'] as $dictionary) {
                        if (($appraisePaymenMethod == $dictionary->appraise_title) && ($assetPaymenMethod == $dictionary->asset_title)) {
                            $comparisonFactor = [
                                'appraise_id' => $id,
                                'asset_general_id' => $appraiseHasAsset->asset_general_id,
                                'status' => (in_array('dieu_kien_thanh_toan', $comparisonFactorInput)),
                                'type' => 'dieu_kien_thanh_toan',
                                'appraise_title' => $appraisePaymenMethod,
                                'asset_title' => $assetPaymenMethod,
                                'description' => $dictionary->description,
                                'adjust_percent' => $dictionary->adjust_percent,
                                'name' => 'Điều kiện thanh toán'
                            ];
                            $comparisonFactor = new AppraiseComparisonFactor($comparisonFactor);
                            $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                                ->updateOrInsert(['id' => $comparisonFactorId], $comparisonFactor->attributesToArray());
                        }
                    }
                } else if ($comparisonFactorTmp == 'quy_hoach') {
                    $assetLegal = isset($asset->zoning->description) ? $asset->zoning->description : 'Không biết';
                    $appraiseLegal = isset($object->properties[0]->zoning->description) ? $object->properties[0]->zoning->description : 'Không biết';
                    $comparisonFactor = [
                        'appraise_id' => $id,
                        'asset_general_id' => $appraiseHasAsset->asset_general_id,
                        'status' => (in_array('quy_hoach', $comparisonFactorInput)),
                        'type' => 'quy_hoach',
                        'appraise_title' => $appraiseLegal,
                        'asset_title' => $assetLegal,
                        'description' => CompareMaterData::COMPARISONS_DESCRIPTION['tuong_dong'],
                        'adjust_percent' => 0,
                        'name' => 'Quy hoạch/hiện trạng',
                    ];
                    $comparisonFactor = new AppraiseComparisonFactor($comparisonFactor);
                    $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                        ->insertGetId($comparisonFactor->attributesToArray());
                } else if ($comparisonFactorTmp == 'khoang_cach') {
                    continue;
                    $comparisonFactor = [
                        'appraise_id' => $appraiseId,
                        'asset_general_id' => $appraiseHasAsset->asset_general_id,
                        'status' => (in_array('khoang_cach', $comparisonFactorInput)),
                        'type' => 'khoang_cach',
                        'name' => 'Khoảng cách TSSS đến TSTĐ',
                        'appraise_title' => 0,
                        'asset_title' => 0,
                        'description' => 'Không xác định',
                        'adjust_percent' => 0,
                    ];
                    $comparisonFactor = new AppraiseComparisonFactor($comparisonFactor);
                    $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                        ->insertGetId($comparisonFactor->attributesToArray());
                } else if ($comparisonFactorTmp == 'muc_dich_chinh') {
                    continue;
                    $comparisonFactor = [
                        'appraise_id' => $appraiseId,
                        'asset_general_id' => $appraiseHasAsset->asset_general_id,
                        'status' => (in_array('muc_dich_chinh', $comparisonFactorInput)),
                        'type' => 'muc_dich_chinh',
                        'name' => 'Mục đích sử dụng đất chính',
                        'appraise_title' => 0,
                        'asset_title' => 0,
                        'description' => 'Không xác định',
                        'adjust_percent' => 0,
                    ];
                    $comparisonFactor = new AppraiseComparisonFactor($comparisonFactor);
                    $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                        ->insertGetId($comparisonFactor->attributesToArray());
                } else if ($comparisonFactorTmp == 'yeu_to_khac') {
                    continue;
                    $comparisonFactor = [
                        'appraise_id' => $appraiseId,
                        'asset_general_id' => $appraiseHasAsset->asset_general_id,
                        'status' => (in_array('yeu_to_khac', $comparisonFactorInput)),
                        'type' => 'yeu_to_khac',
                        'name' => 'Yếu tố khác',
                        'appraise_title' => '',
                        'asset_title' => '',
                        'description' => CompareMaterData::COMPARISONS_DESCRIPTION['tuong_dong'],
                        'adjust_percent' => 0,
                    ];
                    $comparisonFactor = new AppraiseComparisonFactor($comparisonFactor);
                    $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                        ->insertGetId($comparisonFactor->attributesToArray());
                } else {
                }
            }
        }
        $isAddnew = 0;
        foreach ($oldAssetGeneralIds as $assetGeneralId=>$value) {
			if($value) {
                $isAddnew =1 ;
				AppraiseComparisonFactor::query()->where('appraise_id', '=', $appraiseId)->where('asset_general_id', '=', $assetGeneralId)->forceDelete();
				AppraiseUnitPrice::query()->where('appraise_id', '=', $appraiseId)->where('asset_general_id', '=', $assetGeneralId)->forceDelete();
				AppraiseUnitArea::query()->where('appraise_id', '=', $appraiseId)->where('asset_general_id', '=', $assetGeneralId)->forceDelete();
			}
		}
        foreach ($oldAssetGeneralIds as $assetGeneralId=>$value) {
            if(!$value&&$isAddnew) {
                AppraiseComparisonFactor::query()->where('appraise_id', '=', $appraiseId)
                    ->where('asset_general_id', '=', $assetGeneralId)
                    ->where('type', '=', 'yeu_to_khac')
                    ->forceDelete();
            }
        }
    }

    /**
     * @param $id
     * @return Builder|Model|object|null
     */
    public function findAssetGeneralById($id)
    {
        return CompareAssetGeneral::query()
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
            ->first();
    }

    /**
     * @param $objects
     * @return bool
     */
    public function updateComparisonFactor($objects): bool
    {
        $appraiseId = null;
        $user = CommonService::getUser();

        if (isset($objects['comparison_factor'])) {
            $comparisonFactorDatas = isset($objects['other_comparison']) ? array_merge($objects['comparison_factor'], $objects['other_comparison']) : $objects['comparison_factor'];
            foreach ($comparisonFactorDatas as $comparisonFactorData) {
                $comparisonFactorData = array_map(function ($v) {
                    return (is_null($v)) ? "" : $v;
                }, $comparisonFactorData);

                if (!isset($appraiseId) && (isset($comparisonFactorData['appraise_id'])))
                    $appraiseId = $comparisonFactorData['appraise_id'];

                if (!isset($comparisonFactorData['adjust_percent'])) {
                    $comparisonFactorData['adjust_percent'] = 0;
                }
                if ($comparisonFactorData['adjust_percent']  > 0) {
                    $comparisonFactorData['description'] = CompareMaterData::COMPARISONS_DESCRIPTION['kem_thuan_loi'];
                } else if ($comparisonFactorData['adjust_percent']  < 0) {
                    $comparisonFactorData['description'] = CompareMaterData::COMPARISONS_DESCRIPTION['thuan_loi'];
                } else {
                    $comparisonFactorData['description'] = CompareMaterData::COMPARISONS_DESCRIPTION['tuong_dong'];
                }

                if (!$comparisonFactorData['status'] && ($comparisonFactorData['type'] == 'yeu_to_khac')) {
                    $comparisonFactorData['status'] = 1;
                }

                $comparisonFactor = new AppraiseComparisonFactor($comparisonFactorData);
                if (isset($comparisonFactorData['id'])) {
                    $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                        ->updateOrInsert(['id' => $comparisonFactorData['id']], $comparisonFactor->attributesToArray());
                } else {
                    $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                        ->insert($comparisonFactor->attributesToArray());
                }
            }
        }

        if (isset($objects['delete_other_comparison'])) {
            foreach ($objects['delete_other_comparison'] as $comparisonFactorData) {
                if (isset($comparisonFactorData['id'])) {
                    AppraiseComparisonFactor::where('id', $comparisonFactorData['id'])->forceDelete();
                }
            }
        }

        if (isset($objects['asset_unit_price'])) {
            foreach ($objects['asset_unit_price'] as $item) {
                unset($item['land_type_data']);
                $item['created_by'] = $user->id;

                $appraiseUnitPrice = new AppraiseUnitPrice($item);
                if (isset($item['id'])) {
                    $appraiseUnitPriceId = QueryBuilder::for($appraiseUnitPrice)
                        ->updateOrInsert(['id' => $item['id']], $appraiseUnitPrice->attributesToArray());
                } else {
                    $appraiseUnitPriceId = QueryBuilder::for($appraiseUnitPrice)
                        ->insert($appraiseUnitPrice->attributesToArray());
                }
            }
        }

        if (isset($objects['asset_unit_area'])) {
            foreach ($objects['asset_unit_area'] as $item) {
                unset($item['land_type_data']);
                $item['created_by'] = $user->id;

                $appraiseUnitArea = new AppraiseUnitArea($item);
                if (isset($item['id'])) {
                    $appraiseUnitAreaId = QueryBuilder::for($appraiseUnitArea)
                        ->updateOrInsert(['id' => $item['id']], $appraiseUnitArea->attributesToArray());
                } else {
                    $appraiseUnitAreaId = QueryBuilder::for($appraiseUnitArea)
                        ->insert($appraiseUnitArea->attributesToArray());
                }
            }
        }

        if (isset($appraiseId)) {
            $appraise = $this->findById($appraiseId);
            if (isset($objects['remaining_price']) && !empty($objects['remaining_price']) && isset($appraise->composite_land_remaning_slug) && $appraise->composite_land_remaning_slug == 'theo-phuong-phap-doc-lap') {
                $remainingPrice = $objects['remaining_price'];
                if (isset($remainingPrice['remaining_commerce_price']) && isset($remainingPrice['land_type'])) {
                    CommonService::setAppraisePrice($appraise, $remainingPrice['remaining_commerce_price'], 'land_asset_purpose_' . $remainingPrice['land_type'] . '_price');
                }
            }
            $existAssetGeneralIds = [];
            if (isset($objects['appraise_adapter'])&&!empty($objects['appraise_adapter'])) {
                foreach($objects['appraise_adapter'] as $item) {
                    $appraiseAdapter = AppraiseAdapter::where('appraise_id', $item['appraise_id'])
                        ->where('asset_general_id', $item['asset_general_id'])->first();
                    if (isset($appraiseAdapter)) {
                        AppraiseAdapter::where('id', $appraiseAdapter->id)->update([
                            'percent' => $item['percent'],
                            'change_purpose_price' => $item['change_purpose_price']
                        ]);
                        $existAssetGeneralIds[] = $appraiseAdapter->id;
                    } else {
                        $appraiseAdapterId = AppraiseAdapter::insert([
                            'appraise_id' => $item['appraise_id'],
                            'asset_general_id' => $item['asset_general_id'],
                            'percent' => $item['percent'],
                            'change_purpose_price' => $item['change_purpose_price']
                        ]);
                        $existAssetGeneralIds[] = $appraiseAdapterId;
                    }
                }
            }

            if (isset($objects['layer_cutting_procedure'])) {
                $items = AppraiseAppraisalMethods::where('appraise_id', $appraiseId)->where('slug', 'layer_cutting_procedure')->get();
                if (count($items) == 1) {
                    AppraiseAppraisalMethods::where('appraise_id', $appraiseId)->where('slug', 'layer_cutting_procedure')->update([
                        'appraise_id' => $appraiseId,
                        'slug' => "layer_cutting_procedure",
                        'slug_value' => $objects['layer_cutting_procedure'],
                    ]);
                } else {
                    if (count($items) > 1) {
                        AppraiseAppraisalMethods::where('appraise_id', $appraiseId)->where('slug', 'layer_cutting_procedure')->forceDelete();
                    }
                    AppraiseAppraisalMethods::create([
                        'appraise_id' => $appraiseId,
                        'slug' => "layer_cutting_procedure",
                        'slug_value' => $objects['layer_cutting_procedure'],
                    ]);
                }
                if ($objects['layer_cutting_procedure']) {
                    if (isset($objects['layer_cutting_price']) && !empty($objects['layer_cutting_price'])) {
                        CommonService::setAppraisePrice($appraise, $objects['layer_cutting_price'], 'layer_cutting_price');
                    }
                }
            }
            $appraise = Appraise::where('id', $appraiseId)->first();
            if (isset($objects['round_total'])) {
                CommonService::setAppraisePrice($appraise, $objects['round_total'], 'round_total');
            }
            if (isset($objects['round_composite'])) {
                CommonService::setAppraisePrice($appraise, $objects['round_composite'], 'round_composite');
            }
            if (isset($objects['round_violation_composite'])) {
                CommonService::setAppraisePrice($appraise, $objects['round_violation_composite'], 'round_violation_composite');
            }
            if (isset($objects['round_violation_facility'])) {
                CommonService::setAppraisePrice($appraise, $objects['round_violation_facility'], 'round_violation_facility');
            }

            CommonService::getAssetPriceTotal($appraise);
        }
        $this->updateAppraiseStep($appraiseId, 7);

        return true;
    }

    public function updateBaoCao($appraiseId, $objects): bool
    {

        $appraise = Appraise::where('id', $appraiseId)->first();
        CommonService::setAppraisePrice($appraise, $objects['round_appraise_total'], 'round_appraise_total');

        return true;
    }
    /**
     * @param $objects
     * @return bool
     */
    public function updateTangibleComparisonFactor($objects)
    {

        $appraiseId = null;
        $result = [];
        if (isset($objects['comparison_tangible_factor'])) {
            foreach ($objects['comparison_tangible_factor'] as $comparisonFactorData) {
                if(! isset($appraiseId)){
                    $appraiseId = $comparisonFactorData['appraise_id'];
                    $check = $this->beforeSave($appraiseId);
                    if(isset($check)){
                        return $check;
                    }
                }
                $comparisonFactor = new AppraiseTangibleComparisonFactor($comparisonFactorData);
                $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                    ->updateOrInsert(['id' => $comparisonFactorData['id']], $comparisonFactor->attributesToArray());
            }
        }

        if (isset($objects['construction_company'])) {
            foreach ($objects['construction_company'] as $item) {
                if(! isset($appraiseId)){
                    $appraiseId = $item['appraise_id'];
                    $check = $this->beforeSave($appraiseId);
                    if(isset($check)){
                        return $check;
                    }
                }
                /* if(isset($item['construction_company'])) {
                    $constructionCompanyData = $item['construction_company'];
                    $constructionCompany = new AppraisalConstructionCompany($constructionCompanyData);
                    $constructionCompanyId = QueryBuilder::for($constructionCompany)
                        ->updateOrInsert(['id' => $constructionCompanyData['id']], $constructionCompany->attributesToArray());
                } */
                if (isset($item['unit_price_m2'])) {
                    ConstructionCompany::whereId($item['id'])->update([
                        'unit_price_m2' => $item['unit_price_m2']
                    ]);
                }
            }
        }
        if(isset($appraiseId)){
            $check = $this->beforeSave($appraiseId);
            if(isset($check)){
                return $check;
            }

            if(isset($objects['total_desicion_average'])){
                if(AppraisePrice::where(['appraise_id' => $appraiseId, 'slug' => 'total_desicion_average'])->exists()){
                    AppraisePrice::where(['appraise_id' => $appraiseId, 'slug' => 'total_desicion_average'])
                                ->update([
                                    'value' => intval($objects['total_desicion_average']),
                                ]);
                }
                else{
                    $data = [
                        'appraise_id' => $appraiseId,
                        'slug' => 'total_desicion_average',
                        'value' => intval($objects['total_desicion_average']),
                        'description' => '',
                    ];
                    $appraisePrice = new AppraisePrice($data);
                    QueryBuilder::for($appraisePrice)
                        ->insert($appraisePrice->attributesToArray());
                }
            }
            $this->getAppraiseCalculate($appraiseId);
            $this->updateAppraiseStep($appraiseId,7);
            $result = $this->getPriceById($appraiseId);
        }

        return $result;
    }

    public function getUnitPrice($asset, $property, $appraiseHasAsset): bool
    {
        $area = 0;
        $baseUnitPrice = 0;
        $unitPrice = 0;
        $landType = null;
        foreach ($asset->properties[0]->propertyDetail as $propertyDetail) {
            if (!$propertyDetail->is_transfer_facility) {
                $area = $propertyDetail->total_area;
                $unitPrice = $propertyDetail->circular_unit_price;
            } else {
                $baseUnitPrice = $propertyDetail->circular_unit_price;
                $landType = $propertyDetail;
            }
        }
        $price = $area * ($unitPrice - $baseUnitPrice);

        $area = 0;
        $baseUnitPrice = 0;
        $unitPrice = 0;
        if (isset($property->propertyDetail)) {
            foreach ($property->propertyDetail as $propertyDetail1) {
                if (($propertyDetail1->landTypePurposeData->acronym == $landType->landTypePurpose->acronym)
                    || (in_array($propertyDetail1->landTypePurposeData->id, EstimateAssetDefault::GROUP_LAND_TYPE) && in_array($landType->landTypePurpose->id, EstimateAssetDefault::GROUP_LAND_TYPE))
                ) {
                    $area = $propertyDetail1->total_area;
                    $unitPrice = $propertyDetail1->circular_unit_price;
                } else {
                    $baseUnitPrice = $propertyDetail1->circular_unit_price;
                }
            }
        }
        $price1 = $area * ($unitPrice - $baseUnitPrice);

        $appraiseHasAsset->asset_price = $price1;
        $appraiseHasAsset->appraise_price = $price;
        $assetGeneralId = AppraiseHasAsset::query()
            ->updateOrInsert(['id' => $appraiseHasAsset->id], $appraiseHasAsset->attributesToArray());
        return true;
    }


    /**
     * @return array
     */
    public function findAllAppraiseDictionaries(): array
    {
        $dictionaries = AppraiseDictionary::query()->select()->orderBy($this->defaultSort)->get();
        $result = [];
        foreach ($dictionaries as $dictionary => $value) {
            $result[mb_strtolower($value->type)][] = $value;
        }
        return $result;
    }

    public function findAllAppraises(array $request)
    {
        $user = request()->get('user');
        $query = request()->get('query');
        $sortOrder = request()->get('sortOrder');
        $sortField = request()->get('sortField');
        $popup = request()->get('popup');
        if (!empty($query)) {
            $query = json_decode($query);
        } else {
            $query = [];
        }
        $result = $this->model->with(['createdBy:id,name,image', 'assetType:id,description'])
            ->whereHas('createdBy', function ($q) use ($query, $user, $sortField, $sortOrder, $popup) {
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
                if (($role->name == 'USER') || (!empty($popup))) {
                    return $q->where('id', $user->id);
                }
            })
            ->whereHas('assetType', function ($q) use ($query) {
                if (isset($query->asset_type_description) && !empty($query->asset_type_description)) {
                    $q->where('description', 'ILIKE', '%' . $query->asset_type_description . '%');
                }
            })
            ->select(
                'id',
                'status',
                'created_by',
                'asset_type_id',
                'updated_at',
                DB::raw("case when DATE_PART('day', now() - updated_at) = 1
                                        then concat(DATE_PART('day', now() - updated_at) , ' day ago')
                                    when DATE_PART('day', now() - updated_at) > 1
                                        then concat(DATE_PART('day', now() - updated_at) , ' days ago')
                                    else
                                        case when DATE_PART('hour', now() - updated_at) = 1
                                                then concat(DATE_PART('hour', now() - updated_at) , ' hour ago')
                                            when DATE_PART('hour', now() - updated_at) > 1
                                                then concat(DATE_PART('hour', now() - updated_at) , ' hours ago')
                                    end
                                end AS days"),
                DB::raw("concat('TSTD_', id) AS slug")
            )
            ->wherein('status', ['1', '2']);
        if (isset($query->public_date_from) && !empty($query->public_date_from)) {
            $result =  $result->where('updated_at', '>=', date('Y-m-d', strtotime($query->public_date_from)) . ' 00:00:00');
        }
        if (isset($query->public_date_to) && !empty($query->public_date_to)) {
            $result = $result->where('updated_at', '<=', date('Y-m-d', strtotime($query->public_date_to)) . ' 00:00:00');
        }
        if (isset($query->search) && !empty($query->search)) {
            $search = $query->search;
            $result = $result->where(function ($q) use ($search) {
                $q = $q->where('id', 'ILIKE', '%' . $search . '%');
                // $q = $q->orwhere('appraise_asset', 'ILIKE', '%' . $search . '%');
            });
        }
        $result = $result->orderByDesc('updated_at')->get();

        foreach ($result as $stt => $item) {
            $result[$stt]->append('status_text');
            $result[$stt]->append('total_asset_price');
        }
        return $result;
    }
    #region version 2
    //step 1 - view data
    private function getGeneralInfomation(int $id)
    {
        $select = ['id', 'status', 'asset_type_id', 'province_id', 'district_id', 'ward_id', 'street_id', 'distance_id', 'topographic_id', 'coordinates', 'appraise_asset', 'full_address'];
        $with = [
            'createdBy:id,name',
            'topographic:id,description',
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
        $select = ['id', 'appraise_id', 'front_side', 'individual_road', 'main_road_length', 'material_id', 'two_sides_land', 'description'];
        $with = [
            'material:id,type,description',
            'propertyTurningTime:id,appraise_property_id,main_road_length,is_alley_with_connection,turning,material_id,main_road_distance',
            'propertyTurningTime.material:id,type,description'
        ];
        $result = AppraiseProperty::with($with)
            ->select($select)
            ->where(['appraise_id' => $id])
            ->get()->first();
        return $result;
    }

    private function getEconomy(int $id)
    {
        $select = ['id', 'appraise_id', 'business_id', 'social_security_id', 'feng_shui_id', 'zoning_id', 'condition_id'];
        $with = [
            'business:id,type,description',
            'socialSecurity:id,type,description',
            'fengShui:id,type,description',
            'conditions:id,type,description',
            'zoning:id,type,description',
        ];
        $result = AppraiseProperty::with($with)
            ->select($select)
            ->where(['appraise_id' => $id])
            ->get()->first();
        return $result;
    }

    private function getPic(int $id, array $type_id)
    {
        $result = [];
        if (AppraisePic::where(['appraise_id' => $id])->wherein('type_id', $type_id)->exists()) {
            $select = ['id', 'appraise_id', 'link', 'type_id'];
            $with = [
                'picType:id,description'
            ];
            $result = AppraisePic::with($with)
                ->select($select)
                ->where(['appraise_id' => $id])
                ->wherein('type_id', $type_id)
                ->get();
        }

        return $result;
    }

    public function getInfomation(int $id)
    {
        $generalInfomation = [];
        $traffic =  [];
        $economic =  [];
        $pic =  [];
        $picTypeId = [149, 150, 151, 152];
        if (Appraise::where('id', '=', $id)->exists()) {
            // $propertieId = AppraiseProperty::where('appraise_id', '=',$id)->first()->id;
            $generalInfomation = $this->getGeneralInfomation($id);
            $traffic =  $this->getTraffic($id);
            $economic =  $this->getEconomy($id);
            $pic =  $this->getPic($id, $picTypeId);
        }
        $result = array_merge(
            ['general_infomation' => $generalInfomation],
            ['traffic_infomation' => $traffic],
            ['economic_infomation' => $economic],
            ['picture_infomation' => $pic]
        );
        return $result;
    }

    //Step 1 - Post Data
    public function postGeneralInfomation(array $objects, int $id = null)
    {
        DB::beginTransaction();
        try {
            $user = CommonService::getUser();

            #check $id , nếu null => tạo mới , có => update
            if (isset($id)) {
                # block xác thực
                $check = $this->beforeSave($id);
                if (isset($check)) {
                    return $check;
                }

                $generalInfomation = $objects['general_infomation'];
                $pictureInfomation = $objects['picture_infomation'];
                $economicInfomation = $objects['economic_infomation'];
                $trafficInfomation = $objects['traffic_infomation'];
                $geographical_location = $objects['geographical_location'];
                $appraiseId = $id;

                Appraise::where('id', $appraiseId)->update([
                    'appraise_asset' => $generalInfomation['appraise_asset'],
                    'asset_type_id' => $generalInfomation['asset_type_id'],
                    'coordinates' => $generalInfomation['coordinates'],
                    'distance_id' => $generalInfomation['distance_id'],
                    'district_id' => $generalInfomation['district_id'],
                    'province_id' => $generalInfomation['province_id'],
                    'street_id' => $generalInfomation['street_id'],
                    'ward_id' => $generalInfomation['ward_id'],
                    'full_address' => $generalInfomation['full_address'],
                ]);

                $edited = Appraise::where('id', $appraiseId)->first();

                # activity-log có id -> cập nhật
                $this->CreateActivityLog($edited, $edited, 'update_data', 'cập nhật dữ liệu');

                // $this->update_activity_log($appraiseId, $this->model, $generalInfomation, $log, $logName);

                // $appraise = new Appraise($generalInfomation);
                //
                // $appraise->newQuery()->updateOrInsert(['id' => $id], $appraise->attributesToArray());
                #Delete appraise pic - add new
                AppraisePic::query()->where('appraise_id', '=', $appraiseId)->delete();
                if (isset($pictureInfomation)) {
                    foreach ($pictureInfomation as $appraisePic) {
                        $appraisePic['appraise_id'] = $appraiseId;
                        $pic = new AppraisePic($appraisePic);
                        QueryBuilder::for($pic)
                            ->insert($pic->attributesToArray());
                    }
                }
                # delete turning time ,  update appraise properties - insert turning time
                $propertieId = AppraiseProperty::where('appraise_id', $appraiseId)->get()->first()->id;
                // AppraisePropertyTurningTime::query()->where('appraise_property_id', '=', $propertieId)->delete();
                if (AppraisePropertyTurningTime::where(['appraise_property_id' => $propertieId])->exists()) {
                    AppraisePropertyTurningTime::where(['appraise_property_id' => $propertieId])->delete();
                }
                if (isset($trafficInfomation)) {
                    if (isset($propertieId)) {
                        AppraiseProperty::where('appraise_id', $appraiseId)->where('id', $propertieId)->update([
                            'front_side' => $trafficInfomation['front_side'],
                            'individual_road' => $trafficInfomation['individual_road'],
                            'main_road_length' => $trafficInfomation['main_road_length'],
                            'material_id' => $trafficInfomation['material_id'],
                            'two_sides_land' => isset($trafficInfomation['two_sides_land']) ? $trafficInfomation['two_sides_land'] : null,
                            'description' => $trafficInfomation['description'],
                            'geographical_location' => $geographical_location
                        ]);
                    } else {
                        $trafficInfomation['appraise_id'] = $appraiseId;
                        $trafficInfomation['description'] = $generalInfomation['description'];
                        $trafficInfomation['geographical_location'] = $geographical_location;
                        $appraiseProperties = new AppraiseProperty($trafficInfomation);
                        $propertieId = QueryBuilder::for($appraiseProperties)
                            ->insertGetId($appraiseProperties->attributesToArray());
                    }
                    if ($trafficInfomation['front_side'] == 0) {
                        if (isset($trafficInfomation['property_turning_time'])) {
                            foreach ($trafficInfomation['property_turning_time'] as $turningTime) {
                                $turningTime['appraise_property_id'] =  $propertieId;
                                $propertyTurningTime = new AppraisePropertyTurningTime($turningTime);
                                QueryBuilder::for($propertyTurningTime)
                                    ->insert($propertyTurningTime->attributesToArray());
                            }
                        } else {
                            $data = ['message' => ErrorMessage::APPRAISE_CHECK_TUNNING, 'exception' =>  ''];
                            return $data;
                        }
                    }
                }
                if (isset($economicInfomation)) {
                    if (isset($propertieId)) {
                        AppraiseProperty::where('appraise_id', $appraiseId)->where('id', $propertieId)->update([
                            'business_id' => $economicInfomation['business_id'],
                            'social_security_id' => $economicInfomation['social_security_id'],
                            'feng_shui_id' => $economicInfomation['feng_shui_id'],
                            'zoning_id' => $economicInfomation['zoning_id'],
                            'condition_id' => $economicInfomation['condition_id'],
                        ]);
                    } else {
                        $economicInfomation['appraise_id'] = $appraiseId;
                        $appraiseProperties = new AppraiseProperty($economicInfomation);
                        QueryBuilder::for($appraiseProperties)
                            ->insert($appraiseProperties->attributesToArray());
                    }
                }
                //kiểm tra chi tiết về công trình khi thay đổi loại - đất trống - xoá thông tin công trình xây dựng
                $assetTypeId = $generalInfomation['asset_type_id'];
                $assetTypeDescription = Dictionary::where(['id' => $assetTypeId])->select(['description'])->first();

                if ($assetTypeDescription['description'] == 'ĐẤT TRỐNG') {
                    if (AppraiseTangibleAsset::where('appraise_id', '=', $id)->exists()) {
                        AppraiseTangibleAsset::where('appraise_id', '=', $id)->delete();
                    }
                    if (ConstructionCompany::where('appraise_id', '=', $id)->exists()) {
                        ConstructionCompany::where('appraise_id', '=', $id)->delete();
                    }
                    if (AppraiseTangibleComparisonFactor::where('appraise_id', $appraiseId)->exists())
                        AppraiseTangibleComparisonFactor::where('appraise_id', $appraiseId)->delete();
                }
                // update step, status
                $this->updateAppraiseStep($appraiseId, 1);
            } else {
                $generalInfomation = $objects['general_infomation'];
                $pictureInfomation = $objects['picture_infomation'];
                $economicInfomation = $objects['economic_infomation'];
                $trafficInfomation = $objects['traffic_infomation'];
                $geographical_location = $objects['geographical_location'];
                $generalInfomation['created_by'] = $user->id;
                $generalInfomation['status'] = 1;
                $generalInfomation['front_side'] = $trafficInfomation['front_side'];
                $generalInfomation['step'] = 1;
                $realEstateId = $this->createRealEstate($generalInfomation);
                $generalInfomation['real_estate_id'] = $realEstateId;
                $appraise = new Appraise($generalInfomation);
                $appraiseArr = $appraise->attributesToArray();
                $data = $this->model->query()->create($appraiseArr);
                $this->model->query()->where('id', $data->id)->update(['id' => $realEstateId, 'real_estate_id' => $realEstateId]);
                $appraiseId = $realEstateId;
                if (isset($trafficInfomation)) {
                    $trafficInfomation['appraise_id'] = $appraiseId;
                    $trafficInfomation['two_sides_land'] = isset($trafficInfomation['two_sides_land']) ? $trafficInfomation['two_sides_land'] : null;
                    $trafficInfomation['geographical_location'] = $geographical_location;

                    $appraiseProperties = new AppraiseProperty($trafficInfomation);
                    $propertieId =   QueryBuilder::for($appraiseProperties)
                        ->insertGetId($appraiseProperties->attributesToArray());

                    if ($trafficInfomation['front_side'] == 0) {
                        if (isset($trafficInfomation['property_turning_time'])) {
                            foreach ($trafficInfomation['property_turning_time'] as $turningTime) {
                                $turningTime['appraise_property_id'] =  $propertieId;
                                $propertyTurningTime = new AppraisePropertyTurningTime($turningTime);
                                QueryBuilder::for($propertyTurningTime)
                                    ->insert($propertyTurningTime->attributesToArray());
                            }
                        } else {
                            $data = ['message' => ErrorMessage::APPRAISE_CHECK_TUNNING, 'exception' =>  ''];
                            return $data;
                        }
                    }
                }
                if (isset($economicInfomation)) {
                    if (isset($propertieId)) {
                        AppraiseProperty::where('appraise_id', $appraiseId)->where('id', $propertieId)->update([
                            'business_id' => $economicInfomation['business_id'],
                            'social_security_id' => $economicInfomation['social_security_id'],
                            'feng_shui_id' => $economicInfomation['feng_shui_id'],
                            'zoning_id' => $economicInfomation['zoning_id'],
                            'condition_id' => $economicInfomation['condition_id'],
                        ]);
                    } else {
                        $economicInfomation['appraise_id'] = $appraiseId;
                        $appraiseProperties = new AppraiseProperty($economicInfomation);
                        QueryBuilder::for($appraiseProperties)
                            ->insert($appraiseProperties->attributesToArray());
                    }
                }
                if (isset($economicInfomation)) {
                    if (isset($propertieId)) {
                        AppraiseProperty::where('appraise_id', $appraiseId)->where('id', $propertieId)->update([
                            'business_id' => $economicInfomation['business_id'],
                            'social_security_id' => $economicInfomation['social_security_id'],
                            'feng_shui_id' => $economicInfomation['feng_shui_id'],
                            'zoning_id' => $economicInfomation['zoning_id'],
                            'condition_id' => $economicInfomation['condition_id'],
                        ]);
                    } else {
                        $economicInfomation['appraise_id'] = $appraiseId;
                        $appraiseProperties = new AppraiseProperty($economicInfomation);
                        QueryBuilder::for($appraiseProperties)
                            ->insert($appraiseProperties->attributesToArray());
                    }
                }
                if (isset($pictureInfomation)) {
                    foreach ($pictureInfomation as $appraisePic) {
                        $appraisePic['appraise_id'] = $appraiseId;
                        $pic = new AppraisePic($appraisePic);
                        QueryBuilder::for($pic)
                            ->insert($pic->attributesToArray());
                    }
                }
                # CẬP NHẬT THÔNG TIN CHUNG - KHÔNG CÓ ID -> TẠO MỚI
                # activity-log
                $data = Appraise::where('id', $appraiseId)->first();
                $this->CreateActivityLog($data, $data, 'create', 'tạo mới');
            }
            $this->processAfterSave($appraiseId);
            DB::commit();
            return $this->getInfomation($appraiseId);
        } catch (Exception $ex) {
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' =>  $ex->getMessage()];
            return $data;
            DB::rollBack();
        }
    }

    //Step 2 - view data
    private function getLandDetails(int $id)
    {
        $select = ['id', 'appraise_id', 'front_side_width', 'insight_width', 'land_shape_id', 'appraise_land_sum_area', 'coordinates', 'legal_id'];

        $with = [
            'landShape:id,type,description',
            'legal:id,type,description',
        ];

        $result = AppraiseProperty::with($with)
            ->select($select)
            ->where(['id' => $id])
            ->get()
            ->first();

        $result->append('topographic');
        return $result;
    }

    private function getTotalArea(int $propertieId)
    {
        $select = ['id', 'appraise_property_id', 'land_type_purpose_id', 'total_area', 'is_transfer_facility'];
        $with = ['landTypePurpose:id,type,description'];

        $result = AppraisePropertyDetail::with($with)
            ->select($select)
            ->where(['appraise_property_id' => $propertieId])
            ->where('total_area', '>', 0)
            ->get();
        return $result;
    }

    private function getPlanningArea(int $propertieId)
    {
        $select = ['id', 'appraise_property_id', 'land_type_purpose_id', 'planning_area', 'type_zoning'];
        $with = ['landTypePurpose:id,type,description'];

        $result = AppraisePropertyDetail::with($with)
            ->select($select)
            ->where(['appraise_property_id' => $propertieId])
            ->where(['is_zoning' => true])
            ->get();
        return $result;
    }
    #region version 2
    //step 1 - view data
    private function getUBNDPrice(int $propertieId)
    {
        $select = ['id','appraise_property_id','land_type_purpose_id','circular_unit_price','position_type_id'];
        $with=['positionType:id,type,description','landTypePurpose:id,type,description'];

        $result = AppraisePropertyDetail::with($with)
            ->select($select)
            ->where(['appraise_property_id'=>$propertieId])
            ->get();
        return $result;
    }
    public function getLandInfomation(int $appraiseId)
    {
        $totalArea = [];
        $planningArea =  [];
        $ubndPrice =  [];
        $landDetail = [];
        $with = [
            'propertyDetail totalArea',
            'propertyDetail landDetail',
        ];
        if (AppraiseProperty::where('appraise_id', '=',$appraiseId)->exists()) {
            $propertieId = AppraiseProperty::where('appraise_id', '=',$appraiseId)->first()->id;
            $landDetail = $this->getLandDetails($propertieId);
            if (AppraisePropertyDetail::where('appraise_property_id', '=',$propertieId)->exists()) {
                $totalArea =  $this->getTotalArea($propertieId);
                $planningArea =  $this->getPlanningArea($propertieId);
                $ubndPrice =  $this->getUBNDPrice($propertieId);
            }
        }
        $result = array_merge(
            ['land_details'=> $landDetail],
            ['total_area'=>$totalArea],
            ['planning_area'=>$planningArea],
            ['UBND_price'=>$ubndPrice]
        );
        return $result;

    }
    //Step 2 - post data
    private function checkDuplicateLandTypePurpose ($data) {
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
    private function checkPlanningArea($totalArea , $planningArea) {
        $result = null;
        foreach ($totalArea as $item) {
            $landTypeId = $item['land_type_purpose_id'];
            $key =array_search($landTypeId, array_column($planningArea, 'land_type_purpose_id'));
            $total = $item['total_area'];
            $planArea = 0;
            if ($key !== false) {
                $planArea = $planningArea[$key]['planning_area'];
            }
            if ($planArea > $total) {
                $result = ['message' => 'Diện tích quy hoạch không được lớn hơn diện sử dụng', 'exception' => ''];
            }
        }
        return $result;
    }

    public function postLandDetailInfomation(array $objects , int $appraiseId)
    {
        DB::beginTransaction();
        try{
            $check = $this->beforeSave($appraiseId);
            if(isset($check)){
                return $check;
            }
            // dd(1);
            $landDetail = $objects['land_details'];
            $totalArea = $objects['total_area'];
            $planningArea = $objects['planning_area'];
            $ubndPrice = $objects['UBND_price'];
            $real_estate = $objects['real_estate'] ?? [];
            $isDuplicate = $this->checkDuplicateLandTypePurpose($totalArea);
            if (!$isDuplicate)
                if (isset($planningArea))
                    $isDuplicate = $this->checkDuplicateLandTypePurpose($planningArea);
            if (!$isDuplicate)
                $isDuplicate = $this->checkDuplicateLandTypePurpose($ubndPrice);
            if ($isDuplicate) {
                return ['message' => 'Trùng mục đích sử dụng. Vui lòng kiểm tra lại' , 'exception' => ''];
            }
            $check = $this->checkPlanningArea($totalArea, $planningArea);
            if (isset($check))
                return $check;
            if (AppraiseProperty::where('appraise_id', '=',$appraiseId)->exists()) {
                $propertieId = AppraiseProperty::where('appraise_id', '=',$appraiseId)->first()->id;
                if(isset($landDetail)){
                    AppraiseProperty::where('id', $propertieId)->update([
                        'coordinates' => $landDetail['coordinates'],
                        'front_side_width' => $landDetail['front_side_width'],
                        'insight_width' => $landDetail['insight_width'],
                        'land_shape_id' => $landDetail['land_shape_id'],
                        'appraise_land_sum_area' => $landDetail['appraise_land_sum_area'],
                        'legal_id' => 1,
                    ]);
                    Appraise::where('id', $appraiseId)->update([
                        'topographic_id'=> isset($landDetail['topographic']['topographic_id']) ? $landDetail['topographic']['topographic_id'] : null,
                    ]);
                }
                if (AppraisePropertyDetail::where(['appraise_property_id'=>$propertieId])->exists()) {
                    AppraisePropertyDetail::where(['appraise_property_id'=>$propertieId])->delete();
                }
                $AppraisePropertyDetailData=[];
                $isMain = false;
                if(isset($totalArea) && count($totalArea) >0){
                    foreach($totalArea as $total){
                        if(!$isMain){
                            if($total['is_transfer_facility'])
                                $isMain = true;
                        }
                        else{
                            if($total['is_transfer_facility']){
                                DB::rollBack();
                                $data = ['message' => ErrorMessage::APPRAISE_CHECK_MULTIPLE_MDSDD, 'exception' =>  ''];
                                return $data;
                            }
                        }
                        $AppraisePropertyDetailData[] =[
                            'appraise_property_id'=> $propertieId,
                            'land_type_purpose_id'=> $total['land_type_purpose_id'],
                            'is_transfer_facility'=> $total['is_transfer_facility'],
                            'total_area'=>  $total['total_area'],
                            'main_area'=> $total['total_area'],
                            'circular_unit_price'=> 0,
                            'position_type_id'=> null,
                            'planning_area'=> 0,
                            'type_zoning'=> '',
                            'is_zoning'=> false,
                        ];
                    }
                    // $key =array_search(61, array_column($AppraisePropertyDetailData, 'land_type_purpose_id'));
                    if(isset($planningArea) && count($planningArea) >0){
                        foreach($planningArea as $planning){
                            $key =array_search($planning['land_type_purpose_id'], array_column($AppraisePropertyDetailData, 'land_type_purpose_id'));
                            if( ($key === false) ){
                                $AppraisePropertyDetailData[] =[
                                    'appraise_property_id'=> $propertieId,
                                    'land_type_purpose_id'=> $planning['land_type_purpose_id'],
                                    'is_transfer_facility'=> false,
                                    'total_area'=>  $planning['planning_area'],
                                    'main_area'=> 0,
                                    'circular_unit_price'=> 0,
                                    'position_type_id'=> null,
                                    'planning_area'=>  $planning['planning_area'],
                                    'type_zoning'=>  $planning['type_zoning'],
                                    'is_zoning'=>  true,
                                ];
                            }
                            else
                            {
                                $mainArea = $AppraisePropertyDetailData[$key]['main_area'] - $planning['planning_area'];
                                $AppraisePropertyDetailData[$key]['main_area'] =  $mainArea > 0 ? $mainArea : 0 ;
                                $AppraisePropertyDetailData[$key]['planning_area'] =  $planning['planning_area'] ;
                                $AppraisePropertyDetailData[$key]['type_zoning'] =  $planning['type_zoning'] ;
                                $AppraisePropertyDetailData[$key]['is_zoning'] =  true ;
                            }
                        }
                    }
                    if(! $isMain){
                        DB::rollBack();
                        $data = ['message' => ErrorMessage::APPRAISE_CHECK_MDSDD, 'exception' =>  ''];
                        return $data;
                    }
                    if(isset($ubndPrice)){
                        foreach($ubndPrice as $UNBD){
                            $key =array_search($UNBD['land_type_purpose_id'], array_column($AppraisePropertyDetailData, 'land_type_purpose_id'));
                            if( !($key === false)  ){
                                $AppraisePropertyDetailData[$key]['position_type_id'] =  $UNBD['position_type_id'] ;
                                $AppraisePropertyDetailData[$key]['circular_unit_price'] =  $UNBD['circular_unit_price'] ;
                            }
                        }
                    }
                    else{
                        DB::rollBack();
                        $data = ['message' => ErrorMessage::APPRAISE_CHECK_UBND_PRICE, 'exception' =>  ''];
                        return $data;
                    }
                    $sumArea = 0.00;
                    // dd($AppraisePropertyDetailData);
                    foreach($AppraisePropertyDetailData as $data)
                    {
                        if(!isset($data['position_type_id'])){
                            DB::rollBack();
                            $data = ['message' => ErrorMessage::APPRAISE_CHECK_UBND_PRICE, 'exception' =>  ''];
                            return $data;
                        }
                        $sumArea  = $sumArea + $data['total_area'];
                        $propertieDetail = new AppraisePropertyDetail($data);
                        QueryBuilder::for($propertieDetail)
                        ->insert($propertieDetail->attributesToArray());
                    }
                    AppraiseProperty::where('id', $propertieId)->update([
                        'appraise_land_sum_area'=> $sumArea
                    ]);
                }
                else
                {
                    DB::rollBack();
                    $data = ['message' => ErrorMessage::APPRAISE_CHECK_MAIN_EREA, 'exception' =>  ''];
                    return $data;
                }
            }
            else{
                DB::rollBack();
                $data = ['message' => 'Vui lòng nhập thông tin giao thông, đặc điểm kinh tế ở bước 1', 'exception' =>  ''];
                return $data;
            }
            // if (isset($real_estate) && isset($real_estate['id'])) {
            if (isset($real_estate)){
                // $result = RealEstate::find($real_estate['id']);
                $result = RealEstate::find($appraiseId);
                if ($result) {
                    $result->update($real_estate);
                }
            }
            $data = Appraise::where('id', $appraiseId)->first();

            # activity-log
            $this->CreateActivityLog($data, $data, 'update_data', 'cập nhật dữ liệu quyền sử dụng đất');
            $this->updateAppraiseStep($appraiseId,2);

            $this->processAfterSave($appraiseId);
            DB::commit();
            // return $this->getLandInfomation($appraiseId);
            return $appraiseId;
        }catch(Exception $ex) {
            DB::rollBack();
            Log::error($ex);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' =>  $ex->getMessage()];
            return $data;
        }
    }

    //Step 3
    public function getConstruction(int $appraiseId)
    {
        $result =[];
        if (AppraiseTangibleAsset::where('appraise_id', '=', $appraiseId)->exists()) {
            $select = ['id', 'appraise_id', 'building_type_id', 'building_category_id',
                        'floor', 'remaining_quality', 'total_construction_base',
                        'total_construction_area', 'start_using_year', 'duration',
                        'description', 'other_building', 'rate_id', 'structure_id',
                        'crane_id', 'aperture_id', 'factory_type_id', 'contruction_description',
                        'gpxd','created_at', 'tangible_name'
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
                ->where(['appraise_id'=>$appraiseId])
                ->get();
        }
        return  $result;

    }

    public function postConstructionInfomation(array $objects , int $appraiseId)
    {
        DB::beginTransaction();
        try{
            $check = $this->beforeSave($appraiseId);
            if(isset($check)){
                return $check;
            }
            if (AppraiseTangibleAsset::where('appraise_id', '=', $appraiseId)->exists()) {
                AppraiseTangibleAsset::where(['appraise_id'=>$appraiseId])->delete();
            }
            if (ConstructionCompany::where('appraise_id', $appraiseId)->exists()) {
                ConstructionCompany::where('appraise_id', $appraiseId)->delete();
            }
            if(AppraiseTangibleComparisonFactor::where('appraise_id',$appraiseId)->exists())
                AppraiseTangibleComparisonFactor::where('appraise_id', $appraiseId)->delete();

            $buildingPriceRepository = new EloquentBuildingPriceRepository(new BuildingPrice());
            foreach($objects['construction'] as $construct){
                $desicionAverage = $buildingPriceRepository->getAverageBuildPriceV3($construct);
                $construction = $construct;
                $construction['appraise_id']= $appraiseId;
                $construction['total_desicion_average']= $desicionAverage;
                $tangibleAsset = new AppraiseTangibleAsset($construction);
                $tangibleAssetId =   QueryBuilder::for($tangibleAsset)
                        ->insertGetId($tangibleAsset->attributesToArray());

                $constructionCompany = $this->getContructionCompanyDefault();
                foreach ($constructionCompany as $company) {
                    $companyCons['appraise_id'] = $appraiseId;
                    $companyCons['construction_company_id'] = $company['construction_company_id'];
                    $companyCons['name'] = $company['name'];
                    $companyCons['manager_name'] = $company['manager_name'];
                    $companyCons['phone_number'] = $company['phone_number'];
                    $companyCons['address'] = $company['address'];
                    $companyCons['unit_price_m2'] = $company['unit_price_m2'];
                    $companyCons['is_defaults'] = $company['is_defaults'];
                    $companyCons['tangible_asset_id'] = $tangibleAssetId;
                    $constructionCompanyData = new ConstructionCompany($companyCons);
                        QueryBuilder::for($constructionCompanyData)
                        ->insert($constructionCompanyData->attributesToArray());
                }
                $pp2 = $buildingPriceRepository->getPP2($construct);

                $tangibleComparisonFactor = [];
                $tangibleComparisonFactor['appraise_id'] = $appraiseId;
                $tangibleComparisonFactor['tangible_asset_id'] = $tangibleAssetId;
                if(isset($pp2)){
                    $tangibleComparisonFactor['h1'] = $pp2['h1'];
                    $tangibleComparisonFactor['h2'] = $pp2['h2'];
                    $tangibleComparisonFactor['h3'] = $pp2['h3'];
                    $tangibleComparisonFactor['h4'] = $pp2['h4'];
                    $tangibleComparisonFactor['h5'] = $pp2['h5'];

                    $tangibleComparisonFactor['p1'] = $pp2['p1'];
                    $tangibleComparisonFactor['p2'] = $pp2['p2'];
                    $tangibleComparisonFactor['p3'] = $pp2['p3'];
                    $tangibleComparisonFactor['d4'] = $pp2['p4'];
                    $tangibleComparisonFactor['p5'] = $pp2['p5'];
                }

                $tangibleComparisonFactor = new AppraiseTangibleComparisonFactor($tangibleComparisonFactor);
                QueryBuilder::for($tangibleComparisonFactor)
                        ->insert($tangibleComparisonFactor->attributesToArray());

            }

            $data = Appraise::where('id', $appraiseId)->first();

            #  CẬP NHẬT THÔNG TIN VỀ CÔNG TRÌNH XÂY DỰNG
            # activity-log
            $this->CreateActivityLog($data, $data, 'update_data', 'cập nhật dữ liệu công trình xây dựng');
            $this->updateAppraiseStep($appraiseId,3);
            $this->processAfterSave($appraiseId);
            DB::commit();
            // return $this->getConstruction($appraiseId);
            return ['id' => $appraiseId , 'step'=> 3];
        }catch(Exception $ex){
            DB::rollBack();
            Log::error($ex);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' =>  $ex->getMessage()];
            return $data;
        }
    }

    //STEP 4
    public function getLaw(int $appraiseId)
    {
        $result =[];
        if (AppraiseLaw::where('appraise_id', '=', $appraiseId)->exists()) {
            $select = ['id', 'appraise_id', DB::raw("coalesce(appraise_law_id,0) as appraise_law_id"), 'date',
                        'description', 'legal_name_holder', 'certifying_agency',
                        'origin_of_use', 'content', 'duration',
                        // DB::raw("to_char(law_date , 'DD-MM-YYYY')  as law_date")
                        'law_date',
                    ];
            $with= [
                    'law:id,type,content',
                    'landDetails:id,appraise_law_id,doc_no,land_no',
                    ];
            $result = AppraiseLaw::with($with)
                ->select($select)
                ->where(['appraise_id'=>$appraiseId])
                ->get();
        }
        return  $result;
    }

    public function postLawInfomation(array $objects , int $appraiseId)
    {
        DB::beginTransaction();
        try{
            $check = $this->beforeSave($appraiseId);
            if(isset($check)){
                return $check;
            }
            if(Appraise::where('id',$appraiseId)->exists())
            {
                if (AppraiseLaw::where('appraise_id', '=', $appraiseId)->exists()) {
                    $lawData = AppraiseLaw::where('appraise_id', '=', $appraiseId)->get(['id']);
                    foreach($lawData as $lawId){
                        AppraiseLawLandDetail::where(['appraise_law_id'=>$lawId['id']])->delete();
                    }
                    AppraiseLaw::where(['appraise_id'=>$appraiseId])->delete();
                }
                $laws = $objects['law'];
                foreach($laws as $law){
                    $law['appraise_id']= $appraiseId;
                    $law['appraise_law_id']=  $law['appraise_law_id']==0 ? null : $law['appraise_law_id'];
                    $law['law_date']= isset($law['law_date']) ?  $law['law_date'] : null;

                    $appraiseLaw = new AppraiseLaw($law);
                    $lawId = QueryBuilder::for($appraiseLaw)
                        ->insertGetId($appraiseLaw->attributesToArray());
                    $lawLandDetails = $law['land_details'];
                    if(isset($law['appraise_law_id']) && $law['appraise_law_id']!=0 ){
                        foreach($lawLandDetails as $land){
                            $land['appraise_law_id'] = $lawId;
                            $lawLandDetail = new AppraiseLawLandDetail($land);
                            QueryBuilder::for($lawLandDetail)
                            ->insertGetId($lawLandDetail->attributesToArray());
                        }
                    }
                }
                $data = Appraise::where('id', $appraiseId)->first();

                # CẬP NHẬT THÔNG TIN VỀ PHÁP LÝ TÀI SẢN
                # activity-log
                $this->CreateActivityLog($data, $data, 'update_data', 'cập nhật dữ liệu pháp lý tài sản');

                $this->updateAppraiseStep($appraiseId,4);
                $this->processAfterSave($appraiseId);
                DB::commit();
                return $this->getAppraisalFacility($appraiseId);
            }
            else{
                $data = ['message' => ErrorMessage::APPRAISE_NOTEXISTS . $appraiseId, 'exception' =>  ''];
                return $data;
            }
        }catch(Exception $ex){
            DB::rollBack();
            Log::error($ex);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' =>  $ex->getMessage()];
            return $data;
        }
    }

    //Step 5
    private function getAppraisalMethod(int $appraiseId)
    {
        $result = [];
        $result1 = [];
        $result2 = [];
        $result3 = [];
        if (AppraiseAppraisalMethods::where('appraise_id', '=', $appraiseId)->exists()) {
            $select = [ 'slug_value','value',
                    ];
            $with= [
                    ];
            $result1 = AppraiseAppraisalMethods::with($with)
                ->select($select)
                ->where(['appraise_id'=>$appraiseId])
                ->where('slug',['thong_nhat_muc_gia_chi_dan'])
                ->get()->first();
            $result2 = AppraiseAppraisalMethods::with($with)
                ->select($select)
                ->where(['appraise_id'=>$appraiseId])
                ->where('slug',['tinh_gia_dat_hon_hop_con_lai'])
                ->get()->first();
            $result3 = AppraiseAppraisalMethods::with($with)
                ->select($select)
                ->where(['appraise_id'=>$appraiseId])
                ->where('slug',['tinh_gia_dat_vi_pham_quy_hoach'])
                ->get()->first();
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
        }
        $result = array_merge(['thong_nhat_muc_gia_chi_dan' => $result1],
                                ['tinh_gia_dat_hon_hop_con_lai' => $result2],
                                ['tinh_gia_dat_vi_pham_quy_hoach' => $result3]
                            );
        return $result;
    }

    private function getValueApproach(int $appraiseId)
    {
        // $result = [];
        if (Appraise::where('id', '=', $appraiseId)->exists()) {
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
                ->where(['id'=>$appraiseId])
                ->whereNotNull('appraise_approach_id')
                ->get()
                ->first();

            // add default value
            if(! isset($result)){
                $result = Appraise::where('id', '=', $appraiseId)->get('id')->first();
                $result->append('appraise_approach_id');
                $result->append('appraise_basis_property_id');
                $result->append('appraise_method_used_id');
                $result->append('appraise_principle_id');
                $result->append('document_description');
            }
        }
        return  $result;
    }

    public function getAppraisalFacility(int $appraiseId)
    {
        $appraisalMethod = $this->getAppraisalMethod($appraiseId);
        $valueApproach = $this->getValueApproach($appraiseId);
        $result = array_merge(['appraisal_methods' => $appraisalMethod], ['value_base_and_approach' => $valueApproach]);
        return $result;
    }

    //Step 5 - Save data
    public function postAppraisalFacility(array $objects , int $appraiseId)
    {
        DB::beginTransaction();
        try{
            $check = $this->beforeSave($appraiseId);
            if(isset($check)){
                return $check;
            }
            if (Appraise::where('id', $appraiseId)->exists()) {
                $appraisal_methods = $objects['appraisal_methods'];
                $value_base_and_approach = $objects['value_base_and_approach'];
                Appraise::where('id', $appraiseId)->update([
                    'appraise_basis_property_id'=> $value_base_and_approach['appraise_basis_property_id'],
                    'document_description'=> $value_base_and_approach['document_description'],
                    'appraise_approach_id'=> $value_base_and_approach['appraise_approach_id'],
                    'appraise_method_used_id'=> $value_base_and_approach['appraise_method_used_id'],
                    'appraise_principle_id'=> $value_base_and_approach['appraise_principle_id'],
                ]);

                if(AppraiseAppraisalMethods::where('appraise_id',$appraiseId)->exists()){
                    foreach(array_keys($appraisal_methods) as $item){
                        if(AppraiseAppraisalMethods::where('appraise_id', $appraiseId)->where('slug','=',$item)->exists())
                            AppraiseAppraisalMethods::where('appraise_id', $appraiseId)->where('slug','=',$item)->update([
                                'slug_value'=> $appraisal_methods[$item]['slug_value'],
                                'value' => $appraisal_methods[$item]['value'],
                            ]);
                        else{
                            $data = [
                                'appraise_id'=>$appraiseId,
                                'slug'=>$item,
                                'slug_value'=>$appraisal_methods[$item]['slug_value'],
                                'value'=>$appraisal_methods[$item]['value'],
                            ];
                            $appraiseAppraisalMethods = new AppraiseAppraisalMethods($data);
                            QueryBuilder::for($appraiseAppraisalMethods)
                            ->insert($appraiseAppraisalMethods->attributesToArray());
                        }
                    }
                }
                else{
                    foreach(array_keys($appraisal_methods) as $item){
                        $data = [
                            'appraise_id'=>$appraiseId,
                            'slug'=>$item,
                            'slug_value'=>$appraisal_methods[$item]['slug_value'],
                            'value'=>$appraisal_methods[$item]['value'],
                        ];
                        $appraiseAppraisalMethods = new AppraiseAppraisalMethods($data);
                        QueryBuilder::for($appraiseAppraisalMethods)
                        ->insert($appraiseAppraisalMethods->attributesToArray());
                    }
                }
            }
            else{
                DB::rollBack();
                $data = ['message' => ErrorMessage::APPRAISE_NOTEXISTS . $appraiseId, 'exception' => ''];
                return $data;
            }
            $data = Appraise::where('id', $appraiseId)->first();
            # CẬP NHẬT THÔNG TIN VỀ CƠ SỞ THẨM ĐỊNH
            # activity-log
            $this->CreateActivityLog($data, $data, 'update_data', 'cập nhật dữ liệu cơ sở thẩm định');

            $this->updateAppraiseStep($appraiseId,5);
            $this->processAfterSave($appraiseId);
            DB::commit();
            // return $this->getAppraisalFacility($appraiseId);
            return ['id' => $appraiseId , 'step'=> 5 ,'distance_max' => 2];
        }catch(Exception $ex){
            DB::rollBack();
            Log::error($ex);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' =>  $ex->getMessage()];
            return $data;
        }
    }

    #region Step 6

    private function getAppraiseHasAssets(int $appraiseId)
    {
        $result = [];
        if (AppraiseHasAsset::where('appraise_id', $appraiseId)->exists()) {
            $select = [
                        'asset_general_id',
                        'version',
                    ];
            $with= [
                    ];
            $hasAssets = AppraiseHasAsset::with($with)
                ->select($select)
                ->where(['appraise_id'=>$appraiseId])
                ->orderby('asset_general_id','desc')
                ->get();
            $asset_general =[];
            $stt = 0;
            $appraise = Appraise::where('id',$appraiseId)->select('coordinates')->first();
            $appraiseLocation = $appraise['coordinates'];
            // $distance = 2;
            $distanceMax = 0;
            $compareAssetGeneralRepository = new EloquentCompareAssetGeneralRepository(new CompareAssetGeneral());
            foreach($hasAssets as $asset){
                $asset_general[$stt] = $compareAssetGeneralRepository->findVersionById_v2($asset['asset_general_id'],$asset['version']);
                $asset_general[$stt]['version'] = $asset['version'];

                $assetLocation = isset($asset_general[$stt]['coordinates']) ? $asset_general[$stt]['coordinates'] : $appraiseLocation;
                $calDistance =  CommonService::calAppraiseAssetDistance($appraiseLocation,$assetLocation);
                if($calDistance > $distanceMax){
                    $distanceMax = $calDistance;
                }
                $stt++;
            }
            $result= $asset_general;
        }
        if($distanceMax ==0)
            $distanceMax = 2;
        $result['distance_max']= CommonService::roundDistance($distanceMax);
        return  $result ;
    }

    private function getComparisonFactors(int $appraiseId)
    {
        $result = [];
        if (AppraiseComparisonFactor::where('appraise_id',$appraiseId)->exists()){
            $select = ['type'
                ];
            $with= [
                ];
            $comparisonFators = AppraiseComparisonFactor::with($with)
                ->select($select)
                ->where(['appraise_id'=>$appraiseId])
                ->where(['status'=>1])
                ->where('type','!=','yeu_to_khac')
                ->distinct()
                ->get();
            $stt = 0;
            foreach($comparisonFators as $item){
                $result[$stt]  = $item['type'];
                $stt++;
            }
        }

        return $result ;
    }

    public function getAssets(int $appraiseId)
    {
        $result =[];
        $picTypeId=[153];
        $asset = $this->getAppraiseHasAssets($appraiseId);
        $comparisonFators = $this->getComparisonFactors($appraiseId);
        $pic =  $this->getPic($appraiseId, $picTypeId);
        if(count($pic)>0)
            $pic = ['map_img' => $pic[0]['link']];
        $asset = ['assets_general' => $asset];
        $result = array_merge($asset,[ 'comparison_factor' => $comparisonFators ],$pic);
        return $result;
    }

    public function checkAppraiseExists(int $appraiseId)
    {
        $result = [];
        if (Appraise::where('id', $appraiseId)->exists()) {
            $select = ['id','coordinates',
            'district_id', 'province_id', 'street_id', 'ward_id',
                    ];
            $with= [
                    'properties:id,appraise_id,front_side',
                    'properties.propertyDetail:id,appraise_property_id,land_type_purpose_id,total_area'
                    ];
            $data = Appraise::with($with)
                ->select($select)
                ->where(['id'=>$appraiseId])
                ->get()->first();

            if(isset($data)){
                $result['distance']=2;
                $result['ward_id'] = $data['ward_id'];
                $result['district_id'] = $data['district_id'];
                $result['province_id'] = $data['province_id'];
                $result['street_id'] = $data['street_id'];
                $result['location'] = $data['coordinates'];
                $result['front_side'] = $data['properties'][0]['front_side'];
                $stt = 0;
                foreach($data['properties'][0]['propertyDetail']  as $item){
                    $result['unrecognized'][$stt]['area'] = strval($item['total_area']);
                    $result['unrecognized'][$stt]['land_type_purpose'] = $item['land_type_purpose_id'];
                    $stt ++;
                }
            }
        }
        return $result;
    }

    private function getAppraiseDataComparison(int $appraiseId)
    {
        $result = [];
        if (Appraise::where('id', $appraiseId)->exists()) {
            $select = [
                    'id',
                    ];
            $with= [
                    'properties:id,appraise_id,front_side,feng_shui_id,paymen_method_id,condition_id,social_security_id,electric_water_id,business_id,material_id,land_shape_id,land_type_id,zoning_id,legal_id,front_side_width,main_road_length,insight_width,appraise_land_sum_area',
                    'properties.legal:id,description',
                    'properties.zoning:id,description',
                    'properties.landType:id,description',
                    'properties.landShape:id,description',
                    'properties.material:id,description',
                    'properties.business:id,description',
                    'properties.electricWater:id,description',
                    'properties.socialSecurity:id,description',
                    'properties.fengShui:id,description',
                    'properties.paymenMethod:id,description',
                    'properties.conditions:id,description',
                    'properties.propertyTurningTime',
                ];
            $result = Appraise::with($with)
                ->select($select)
                ->where(['id'=>$appraiseId])
                ->get()->first();
        }
        return $result;
    }

    public function postAssets(array $objects , int $appraiseId)
    {
        DB::beginTransaction();
        try{
            $check = $this->beforeSave($appraiseId);
            if(isset($check)){
                return $check;
            }

            $user = CommonService::getUser();
            $comparisonFactors =$objects['comparison_factor'];
            $dictionaries = $this->findAllAppraiseDictionaries();
            // $appraiseData = $this->getAppraiseDataComparison($appraiseId);
            $appraise = $this->model->query()->where('id', $appraiseId)->first();
            $baseUBNDPrice = $this->getBaseUBNDPrice($appraiseId);
            // dd(json_encode($baseUBNDPrice) );
            $cpcmdsd = 0;
            if(isset($objects['map_img'])){
                $link  = $objects['map_img'];
                $type_id = 153;
                AppraisePic::query()->where('appraise_id', '=', $appraiseId)->where('type_id',$type_id)->delete();
                $map =[
                    'appraise_id' => $appraiseId,
                    'link' => $objects['map_img'],
                    'type_id' => $type_id,
                ];
                $pic = new AppraisePic($map);
                QueryBuilder::for($pic)
                    ->insert($pic->attributesToArray());
            }

            if(isset($objects['assets_general']))
            {
                if(count($objects['assets_general'])>3){
                    DB::rollBack();
                    $data = ['message' => ErrorMessage::APPRAISE_CHECK_ASSET_NUMBER, 'exception' =>  ''];
                    return $data;
                }

                $asset_general = $objects['assets_general'];

                $oldAppraiseHasAssets = AppraiseHasAsset::where('appraise_id',$appraiseId)->get();
                $oldAppraiseHasAssets= json_decode(json_encode($oldAppraiseHasAssets),true);
                //add new asset and update
                $stt=0;
                $assetPrice = [];
                foreach($asset_general as $asset)
                {
                    $asset_general_id = $asset['id'];
                    $version = $asset['version'];
                    if(isset($oldAppraiseHasAssets) && count($oldAppraiseHasAssets)>0){
                        $key =array_search($asset['id'], array_column($oldAppraiseHasAssets, 'asset_general_id'));
                        if(!($key === false)){
                            //step 5 -> step 6 delete all appraise asset ... => khi có thay đổi về đơn giá UBND hoặc diện tích đất thì không cần sử lý ở đây
                            //update appraie comparison factor
                            if($oldAppraiseHasAssets[$key]['version']!=$version){
                                //delete data
                                AppraiseComparisonFactor::query()->where('appraise_id', '=', $appraiseId)->where('asset_general_id', '=', $asset_general_id)->forceDelete();
                                AppraiseComparisonFactor::query()->where('appraise_id', '=', $appraiseId)->where('type', '=', 'yeu_to_khac')->forceDelete();
                                AppraiseUnitPrice::query()->where('appraise_id', '=', $appraiseId)->where('asset_general_id', '=', $asset_general_id)->forceDelete();
                                AppraiseUnitArea::query()->where('appraise_id', '=', $appraiseId)->where('asset_general_id', '=', $asset_general_id)->forceDelete();
                                AppraiseHasAsset::query()->where('appraise_id', '=', $appraiseId)->where('asset_general_id', '=', $asset_general_id)->forceDelete();
                                AppraiseAdapter::query()->where('appraise_id', '=', $appraiseId)->where('asset_general_id', '=', $asset_general_id)->forceDelete();
                                goto createNewAsset;
                            }
                            $oldAssetGeneralId = $asset_general_id;
                            $this->postComparisonFactor($dictionaries, $appraise->properties[0] , $comparisonFactors , $asset , $appraiseId, $asset_general_id,$oldAssetGeneralId) ;
                            continue;
                        }
                    }
                    // add thêm để dự phòng trường hợp không qua được điều kiện trên
                    if(AppraiseHasAsset::where('appraise_id',$appraiseId)->where('asset_general_id',$asset_general_id)->exists()){
                        $this->postComparisonFactor($dictionaries, $appraise->properties[0] , $comparisonFactors , $asset , $appraiseId, $asset_general_id,$asset_general_id) ;
                        continue;
                    }
                    createNewAsset:
                    //create appraise has asset
                    $version = $asset['version'];
                    $hasAsset['appraise_id'] = $appraiseId;
                    $hasAsset['version'] = $version;
                    $hasAsset['asset_general_id'] = $asset_general_id;
                    $hasAsset['asset_property_detail_id'] = $asset['properties'][0]['id'];
                    $appraiseHasAsset = new AppraiseHasAsset($hasAsset);
                    QueryBuilder::for($appraiseHasAsset)
                        ->insert($appraiseHasAsset->attributesToArray());
                    //create appraie comparison factor
                    $this->postComparisonFactor($dictionaries, $appraise->properties[0] , $comparisonFactors , $asset , $appraiseId, $asset_general_id) ;
                    //create appraise unit area , appraise unit price
                    $assetDetails = $asset['properties'][0]['property_detail'];
                    $key =array_search($baseUBNDPrice['land_type_purpose_id'], array_column($assetDetails, 'land_type_purpose'));
                    if($key === false){
                        $basePrice = $baseUBNDPrice['circular_unit_price'];

                        $area_price['appraise_id'] = $appraiseId;
                        $area_price['asset_general_id'] = $asset['id'];
                        $area_price['position_type_id'] = $baseUBNDPrice['position_type_id'];
                        $area_price['land_type_id'] = $baseUBNDPrice['land_type_purpose_id'];
                        $area_price['original_value'] = $baseUBNDPrice['circular_unit_price'];
                        $area_price['update'] = 1;
                        $area_price['created_by'] = $user->id;
                        $appraiseUnitPrice = new appraiseUnitPrice($area_price);

                        QueryBuilder::for($appraiseUnitPrice)
                            ->insert($appraiseUnitPrice->attributesToArray());
                    }
                    else{
                        $basePrice = $assetDetails[$key]['circular_unit_price'];
                    }
                    $cpcmdsd = 0;
                    foreach($assetDetails as $detail){

                        $cpcmdsd = $cpcmdsd + CommonService::calAssetCPCMDSD($basePrice,$detail['circular_unit_price'],$detail['total_area']);
                        $area_price['appraise_id'] = $appraiseId;
                        $area_price['asset_general_id'] = $asset['id'];
                        $area_price['position_type_id'] = $detail['position_type_id'];
                        $area_price['land_type_id'] = $detail['land_type_purpose'];
                        $area_price['original_value'] = $detail['circular_unit_price'];
                        $area_price['update'] = 1;
                        $area_price['created_by'] = $user->id;

                        $appraiseUnitArea = new AppraiseUnitArea($area_price);
                        $appraiseUnitPrice = new appraiseUnitPrice($area_price);

                        QueryBuilder::for($appraiseUnitPrice)
                            ->insert($appraiseUnitPrice->attributesToArray());
                        QueryBuilder::for($appraiseUnitArea)
                            ->insert($appraiseUnitArea->attributesToArray());
                    }
                    //update - insert appraise asset adapter (percent ,cpcdmdsd)
                    $appraiseAdapterData = [
                        'appraise_id' => $appraiseId,
                        'asset_general_id' => $asset_general_id,
                        'percent' => intval($asset['adjust_percent'])+100,
                        'change_purpose_price' => $cpcmdsd,
                        'change_violate_price' => 0,
                    ];
                    $appraiseAdatter = new AppraiseAdapter($appraiseAdapterData);
                    QueryBuilder::for($appraiseAdatter)
                    ->insert($appraiseAdatter->attributesToArray());
                }
                //calculate price
                $this->getAppraiseCalculate($appraiseId);
                //delete old asset
                if(isset($oldAppraiseHasAssets) && count($oldAppraiseHasAssets)>0){
                    foreach($oldAppraiseHasAssets as  $oldAsset){
                        $key =array_search($oldAsset['asset_general_id'], array_column($asset_general, 'id'),true);
                        if($key === false){
                            $oldId=$oldAsset['asset_general_id'];
                            AppraiseComparisonFactor::query()->where('appraise_id', '=', $appraiseId)->where('asset_general_id', '=', $oldId)->forceDelete();
                            AppraiseComparisonFactor::query()->where('appraise_id', '=', $appraiseId)->where('type', '=', 'yeu_to_khac')->forceDelete();
                            AppraiseUnitPrice::query()->where('appraise_id', '=', $appraiseId)->where('asset_general_id', '=', $oldId)->forceDelete();
                            AppraiseUnitArea::query()->where('appraise_id', '=', $appraiseId)->where('asset_general_id', '=', $oldId)->forceDelete();
                            AppraiseHasAsset::query()->where('appraise_id', '=', $appraiseId)->where('asset_general_id', '=', $oldId)->forceDelete();
                            AppraiseAdapter::query()->where('appraise_id', '=', $appraiseId)->where('asset_general_id', '=', $oldId)->forceDelete();
                        }
                    }
                }
            }
            // dd(AppraiseHasAsset::where('appraise_id',$appraiseId)->count('asset_general_id'));
            if(AppraiseHasAsset::where('appraise_id',$appraiseId)->count('asset_general_id') >3 ){
                DB::rollBack();
                $data = ['message' => ErrorMessage::APPRAISE_CHECK_ASSET_NUMBER, 'exception' =>  ''];
                return $data;
            }
            $data = Appraise::where('id', $appraiseId)->first();
            # CẬP NHẬT THÔNG TIN VỀ TÀI SẢN SO SÁNH
            # activity-log
            $this->CreateActivityLog($data, $data, 'update_data', 'cập nhật dữ liệu tài sản so sánh');
            $this->updateAppraiseStep($appraiseId, 6);
            $this->processAfterSave($appraiseId);
            DB::commit();
            Appraise::where('id', $appraiseId)->update([
                'filter_year' => $objects['filter_year'],
            ]);
            $result = Appraise::where('id',$appraiseId)->get(['id','step','coordinates'])->first();
            $result->append('distance_max');
            return $result;
        }catch(Exception $ex){
            DB::rollBack();
            Log::error($ex);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' =>  $ex->getMessage()];
            return $data;
        }
    }

    #region comparison Factor
    private function postComparisonFactor(array $dictionaries, $property ,array $comparison,array $asset , int $appraiseId,int $assetGeneralId, int  $oldAssetGeneralId = null  )
    {
        $allComparisonFactor = [
            "phap_ly",
            "quy_mo",
            "chieu_rong_mat_tien",
            "chieu_sau_khu_dat",
            "do_rong_duong",
            "hinh_dang_dat",
            "ket_cau_duong",
            "giao_thong",
            "dieu_kien_ha_tang",
            "kinh_doanh",
            "an_ninh_moi_truong_song",
            "phong_thuy",
            "quy_hoach",
            "dieu_kien_thanh_toan",
            "yeu_to_khac",
            "khoang_cach",
            "muc_dich_chinh"
        ];
        // dd(json_encode($object));
        foreach ($allComparisonFactor as $comparisonFactorTmp) {
            if(isset($oldAssetGeneralId)){
                if(in_array($comparisonFactorTmp, $comparison)) {
                    AppraiseComparisonFactor::where('appraise_id', $appraiseId)
                        ->where('type', $comparisonFactorTmp)
                        ->update(['status' => 1]);
                } else {
                    if(($comparisonFactorTmp != "yeu_to_khac") && $comparisonFactorTmp != "phap_ly") {
                        AppraiseComparisonFactor::where('appraise_id', $appraiseId)
                            ->where('type', $comparisonFactorTmp)
                            ->update(['status' => 0]);
                    }
                }
                continue;
            }
            if($comparisonFactorTmp == 'phap_ly'){
                $appraiseValue = $property->legal ? $property->legal->description : '';
                $assetValue = isset($asset['properties'][0]['legal'])?$asset['properties'][0]['legal']['description']: '';
                ////phap ly always true
                $status = true;
                $dictionary = $dictionaries['phap_ly'];
                // $this->comparisionLegal( $dataAppraise,$dataAsset,$status , $appraiseId, $assetGeneralId,$dictionary );
                $type = 'phap_ly';
                $name = 'Pháp lý';
                $this->comparisionHasDictionary( $appraiseValue,$assetValue,$status , $appraiseId, $assetGeneralId,$dictionary,$type,$name );
            }elseif($comparisonFactorTmp == 'quy_mo'){
                $appraiseValue = $property->appraise_land_sum_area ?? 0;
                $assetValue = $asset['properties'][0]['asset_general_land_sum_area'] ?? 0;
                $status = false;
                if(in_array($comparisonFactorTmp, $comparison)){
                    $status = true;
                }
                // $this->comparisonSize( $appraiseValue, $assetValue, $status, $appraiseId, $assetGeneralId );
                $type = 'quy_mo';
                $name = 'Quy mô';
                $this->comparisonNoDictionary( $appraiseValue,$assetValue,$status , $appraiseId, $assetGeneralId,$type,$name );
            }elseif($comparisonFactorTmp == 'chieu_rong_mat_tien'){
                $appraiseValue = $property->front_side_width ?? 0;
                $assetValue = $asset['properties'][0]['front_side_width'] ?? 0;
                $status = false;
                if(in_array($comparisonFactorTmp, $comparison)){
                    $status = true;
                }
                // $this->comparisonFrontSideWidth( $appraiseValue, $assetValue, $status, $appraiseId, $assetGeneralId );
                $type = 'chieu_rong_mat_tien';
                $name = 'Chiều rộng mặt tiền';
                $this->comparisonNoDictionary( $appraiseValue,$assetValue,$status , $appraiseId, $assetGeneralId,$type,$name );
            }elseif($comparisonFactorTmp == 'chieu_sau_khu_dat'){
                $appraiseValue =  $property->insight_width ?? 0;
                $assetValue = $asset['properties'][0]['insight_width'] ?? 0;
                $status = false;
                if(in_array($comparisonFactorTmp, $comparison)){
                    $status = true;
                }
                // $this->comparisonInsightWitdh($appraiseValue, $assetValue, $status, $appraiseId, $assetGeneralId );
                $type = 'chieu_sau_khu_dat';
                $name = 'Chiều sâu khu đất';
                $this->comparisonNoDictionary( $appraiseValue,$assetValue,$status , $appraiseId, $assetGeneralId,$type,$name );
            }elseif($comparisonFactorTmp == 'do_rong_duong'){
                $appraiseValue = $property->main_road_length ?? 0;
                if($appraiseValue==0){
                    if ($property->propertyTurningTime) {
                        foreach ($property->propertyTurningTime as $propertyTurningTime) {
                            $appraiseValue = $propertyTurningTime->main_road_length ?? 0;
                        }
                    }
                }
                $assetValue = $asset['properties'][0]['main_road_length'] ?? 0;
                if($assetValue==0){
                    if(isset($asset['properties'][0]['compare_property_turning_time'])) {
                        foreach ($asset['properties'][0]['compare_property_turning_time'] as $propertyTurningTime) {
                            $assetValue = $propertyTurningTime['main_road_length'] ?? 0;
                        }
                    }
                }
                $status = false;
                if(in_array($comparisonFactorTmp, $comparison)){
                    $status = true;
                }
                $dictionary = $dictionaries['do_rong_duong'];
                $this->comparisonMainRoadLength($appraiseValue, $assetValue, $status, $appraiseId, $assetGeneralId, $dictionary);
            }elseif($comparisonFactorTmp == 'hinh_dang_dat'){
                $appraiseValue = $property->landShape ? $property->landShape->description :'Không biết';
                $assetValue = isset($asset['properties'][0]['land_shape'])?$asset['properties'][0]['land_shape']['description']: 'Không biết' ;
                $status = false;
                if(in_array($comparisonFactorTmp, $comparison)){
                    $status = true;
                }
                $dictionary = $dictionaries['hinh_dang_dat'];
                // $this->comparisonLandSharp( $appraiseValue,$assetValue,$status , $appraiseId, $assetGeneralId,$dictionary );
                $type = 'hinh_dang_dat';
                $name = 'Hình dáng đất';
                $this->comparisionHasDictionary( $appraiseValue,$assetValue,$status , $appraiseId, $assetGeneralId,$dictionary,$type,$name );
            }elseif($comparisonFactorTmp == 'ket_cau_duong'){
                $appraiseValue = $property->material ? $property->material->description :'Không biết';
                // $assetValue = isset($asset['landShape'])?$asset['landShape']: 'Không biết' ;
                if($appraiseValue=='Không biết'){
                    if(isset($property->propertyTurningTime)) {
                        foreach ($property->propertyTurningTime as $propertyTurningTime) {
                            $appraiseValue = $propertyTurningTime->material ? $propertyTurningTime->material->description : 'Không biết';
                        }
                    }
                }
                $assetValue = isset($asset['properties'][0]['material']) ? $asset['properties'][0]['material']['description']: 'Không biết';
                if($assetValue=='Không biết'){
                    if(isset($asset['properties'][0]['compare_property_turning_time'])) {
                        foreach ($asset['properties'][0]['compare_property_turning_time'] as $propertyTurningTime) {
                            $assetValue = $propertyTurningTime['material']['description'] ?? 'Không biết';
                        }
                    }
                }
                $status = false;
                if(in_array($comparisonFactorTmp, $comparison)){
                    $status = true;
                }
                $dictionary = $dictionaries['ket_cau_duong'];
                // $this->comparisonMaterial( $appraiseValue,$assetValue,$status , $appraiseId, $assetGeneralId,$dictionary );
                $type = 'ket_cau_duong';
                $name = 'Kết cấu đường';
                $this->comparisionHasDictionary( $appraiseValue,$assetValue,$status , $appraiseId, $assetGeneralId,$dictionary,$type,$name );

            }elseif($comparisonFactorTmp == 'giao_thong'){
                $appraiseValue ='KHÔNG ĐẤU NỐI ĐƯỜNG CHÍNH';
                $assetValue = "KHÔNG ĐẤU NỐI ĐƯỜNG CHÍNH";
                if(isset($object[0]['propertyTurningTime'])) {
                    $appraiseValue = "ĐẤU NỐI TRỰC TIẾP ĐƯỜNG CHÍNH";
                }

                if(isset($asset['properties'][0]['compare_property_turning_time'])) {
                    $assetValue = "ĐẤU NỐI TRỰC TIẾP ĐƯỜNG CHÍNH";
                }
                $status = false;
                if(in_array($comparisonFactorTmp, $comparison)){
                    $status = true;
                }
                $dictionary = $dictionaries['giao_thong'];
                // $this->comparisonTurning( $appraiseValue,$assetValue,$status , $appraiseId, $assetGeneralId,$dictionary );
                $type = 'giao_thong';
                $name = 'Giao thông';
                $this->comparisionHasDictionary( $appraiseValue,$assetValue,$status , $appraiseId, $assetGeneralId,$dictionary,$type,$name );

            }elseif($comparisonFactorTmp == 'dieu_kien_ha_tang'){
                $appraiseValue =isset($property->conditions) ? $property->conditions->description :'Không biết' ;
                $assetValue = isset($asset['properties'][0]['conditions'])?$asset['properties'][0]['conditions']['description']: 'Không biết' ;
                $status = false;

                if(in_array($comparisonFactorTmp, $comparison)){
                    $status = true;
                }
                $dictionary = $dictionaries['dieu_kien_ha_tang'];

                // $this->comparisonConditions( $appraiseValue,$assetValue,$status , $appraiseId, $assetGeneralId,$dictionary );
                $type = 'dieu_kien_ha_tang';
                $name = 'Điều kiện hạ tầng';
                $this->comparisionHasDictionary( $appraiseValue,$assetValue,$status , $appraiseId, $assetGeneralId,$dictionary,$type,$name );
            }elseif($comparisonFactorTmp == 'kinh_doanh'){
                $appraiseValue =isset($property->business)?$property->business->description : 'Không biết';
                $assetValue = isset($asset['properties'][0]['business'])?$asset['properties'][0]['business']['description']: 'Không biết' ;
                $status = false;
                if(in_array($comparisonFactorTmp, $comparison)){
                    $status = true;
                }
                $dictionary = $dictionaries['kinh_doanh'];
                // $this->comparisonBussiness( $appraiseValue,$assetValue,$status , $appraiseId, $assetGeneralId,$dictionary );
                $type = 'kinh_doanh';
                $name = 'Kinh doanh';
                $this->comparisionHasDictionary( $appraiseValue,$assetValue,$status , $appraiseId, $assetGeneralId,$dictionary,$type,$name );
            }elseif($comparisonFactorTmp == 'an_ninh_moi_truong_song'){
                $appraiseValue =isset($property->socialSecurity)?$property->socialSecurity->description :'Không biết';
                $assetValue = isset($asset['properties'][0]['social_security'])?$asset['properties'][0]['social_security']['description']: 'Không biết' ;
                $status = false;
                if(in_array($comparisonFactorTmp, $comparison)){
                    $status = true;
                }
                $dictionary = $dictionaries['an_ninh_moi_truong_song'];
                // $this->comparisonSecurity( $appraiseValue,$assetValue,$status , $appraiseId, $assetGeneralId,$dictionary );
                $type = 'an_ninh_moi_truong_song';
                $name = 'An ninh môi trường sống';
                $this->comparisionHasDictionary( $appraiseValue,$assetValue,$status , $appraiseId, $assetGeneralId,$dictionary,$type,$name );
            }elseif($comparisonFactorTmp == 'phong_thuy'){
                $appraiseValue = isset($property->fengShui)?$property->fengShui->description :'Không biết';
                $assetValue = isset($asset['properties'][0]['feng_shui'])?$asset['properties'][0]['feng_shui']['description']: 'Không biết' ;
                $status = false;
                if(in_array($comparisonFactorTmp, $comparison)){
                    $status = true;
                }
                $dictionary = $dictionaries['phong_thuy'];
                // $this->comparisonFengShui( $appraiseValue,$assetValue,$status , $appraiseId, $assetGeneralId,$dictionary );
                $type = 'phong_thuy';
                $name = 'Phong thủy';
                $this->comparisionHasDictionary( $appraiseValue,$assetValue,$status , $appraiseId, $assetGeneralId,$dictionary,$type,$name );
            }elseif($comparisonFactorTmp == 'quy_hoach'){
                $appraiseValue =isset($property->zoning) ? $property->zoning->description :'Không biết';
                $assetValue = isset($asset['properties'][0]['zoning'])?$asset['properties'][0]['zoning']['description']: 'Không biết' ;
                $status = false;
                if(in_array($comparisonFactorTmp, $comparison)){
                    $status = true;
                }
                // $this->comparisonZoning( $appraiseValue, $assetValue, $status , $appraiseId, $assetGeneralId );
                $type = 'quy_hoach';
                $name = 'Quy hoạch/Hiện trạng';
                $this->comparisonNoDictionary( $appraiseValue,$assetValue,$status , $appraiseId, $assetGeneralId,$type,$name );
            }elseif($comparisonFactorTmp == 'dieu_kien_thanh_toan'){
                $appraiseValue =isset($property->paymenMethod)?$property->paymenMethod->description:'Không biết' ;
                $assetValue = isset($asset['properties'][0]['paymen_method'])?$asset['properties'][0]['paymen_method']['description']: 'Không biết' ;
                $status = false;
                if(in_array($comparisonFactorTmp, $comparison)){
                    $status = true;
                }
                $dictionary = $dictionaries['dieu_kien_thanh_toan'];
                // $this->comparisonPayment( $appraiseValue,$assetValue,$status , $appraiseId, $assetGeneralId,$dictionary );
                $type = 'dieu_kien_thanh_toan';
                $name = 'Điều kiện thanh toán';
                $this->comparisionHasDictionary( $appraiseValue,$assetValue,$status , $appraiseId, $assetGeneralId,$dictionary,$type,$name );
            }elseif($comparisonFactorTmp == 'khoang_cach'){
                $appraiseValue = 0 ;
                $assetValue = 0;
                $status = false;
                if(in_array($comparisonFactorTmp, $comparison)){
                    $status = true;
                }
                // $dictionary = $dictionaries['khoang_cach'];
                // $this->comparisonPayment( $appraiseValue,$assetValue,$status , $appraiseId, $assetGeneralId,$dictionary );
                $type = 'khoang_cach';
                $name = 'Khoảng cách TSSS đến TSTĐ';
                $this->comparisionDistance( $appraiseValue,$assetValue,$status , $appraiseId, $assetGeneralId,$type,$name );
            }elseif($comparisonFactorTmp == 'muc_dich_chinh'){
                $appraiseValue = 'false' ;
                // $assetValue = 0;
                $assetValue = $asset['properties'][0]['property_detail'][0]['land_type_purpose_data']['acronym'];
                // dd('sdsdsds', $asset['properties'][0]['property_detail'][0]['land_type_purpose_data']['acronym']);
                $status = false;
                if(in_array($comparisonFactorTmp, $comparison)){
                    $status = true;
                }
                // $dictionary = $dictionaries['khoang_cach'];
                // $this->comparisonPayment( $appraiseValue,$assetValue,$status , $appraiseId, $assetGeneralId,$dictionary );
                $type = 'muc_dich_chinh';
                $name = 'Mục đích sử dụng đất chính';
                $this->comparisionDistance( $appraiseValue,$assetValue,$status , $appraiseId, $assetGeneralId,$type,$name );
            }
        }
    }

    private function comparisionDistance(string $appraiseValue,string $assetValue, int $status , int $appraiseId,int $assetGeneralId , string $type , string $name){
        $comparisonFactor = [
            'appraise_id' => $appraiseId,
            'asset_general_id' => $assetGeneralId,
            'status' => $status,
            'type' => $type,
            'appraise_title' => $appraiseValue,
            'asset_title' => $assetValue,
            'description' => 'Không xác định',
            'adjust_percent' => 0,
            'name' => $name,
        ];
        $comparisonFactor = new AppraiseComparisonFactor($comparisonFactor);
        $comparisonFactorId = QueryBuilder::for($comparisonFactor)
            ->insertGetId($comparisonFactor->attributesToArray());
    }
    private function comparisonNoDictionary(string $appraiseValue,string $assetValue, int $status , int $appraiseId,int $assetGeneralId , string $type , string $name){
        $description = CompareMaterData::COMPARISONS_DESCRIPTION['tuong_dong'];
        $comparisonFactor = [
            'appraise_id' => $appraiseId,
            'asset_general_id' => $assetGeneralId,
            'status' => $status,
            'type' => $type,
            'appraise_title' => $appraiseValue,
            'asset_title' => $assetValue,
            'description' => $description,
            'adjust_percent' => 0,
            'name' => $name,
        ];
        $comparisonFactor = new AppraiseComparisonFactor($comparisonFactor);
        $comparisonFactorId = QueryBuilder::for($comparisonFactor)
            ->insertGetId($comparisonFactor->attributesToArray());
    }

    private function comparisionHasDictionary( $appraiseValue, $assetValue, int $status , int $appraiseId,int $assetGeneralId  ,array $dictionaries , string $type , string $name ){
        $comparisonFactor = [
            'appraise_id' => $appraiseId,
            'asset_general_id' => $assetGeneralId,
            'status' => $status,
            'type' => $type,
            'appraise_title' => $appraiseValue,
            'asset_title' => $assetValue,
            'description' => CompareMaterData::COMPARISONS_DESCRIPTION['tuong_dong'],
            'adjust_percent' => 0,
            'name' => $name
        ];
        $comparisonFactor = new AppraiseComparisonFactor($comparisonFactor);
        $comparisonFactorId = QueryBuilder::for($comparisonFactor)
            ->insertGetId($comparisonFactor->attributesToArray());
        foreach ($dictionaries as $dictionary) {
            if (($appraiseValue == $dictionary['appraise_title']) && ($assetValue == $dictionary['asset_title'])) {
                $comparisonFactor = [
                    'appraise_id' => $appraiseId,
                    'asset_general_id' => $assetGeneralId,
                    'status' => $status,
                    'type' => $type,
                    'appraise_title' => $appraiseValue,
                    'asset_title' => $assetValue,
                    'description' => $dictionary['description'],
                    'adjust_percent' => $dictionary['adjust_percent'],
                    'name' => $name
                ];
                $comparisonFactor = new AppraiseComparisonFactor($comparisonFactor);
                $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                    ->updateOrInsert(['id' => $comparisonFactorId], $comparisonFactor->attributesToArray());
            }
        }
    }

    private function comparisionLegal(  string $appraiseValue,string $assetValue, int $status , int $appraiseId,int $assetGeneralId ,array $dictionaries = null)
    {

        $comparisonFactor = [
            'appraise_id' => $appraiseId,
            'asset_general_id' => $assetGeneralId,
            'status' => $status,
            'type' => 'phap_ly',
            'appraise_title' => $appraiseValue,
            'asset_title' => $assetValue,
            'description' => CompareMaterData::COMPARISONS_DESCRIPTION['tuong_dong'],
            'adjust_percent' => 0,
            'name' => 'Pháp lý'
        ];
        $comparisonFactor = new AppraiseComparisonFactor($comparisonFactor);
        $comparisonFactorId = QueryBuilder::for($comparisonFactor)
            ->insertGetId($comparisonFactor->attributesToArray());
        foreach ($dictionaries as $dictionary) {
            if (($appraiseValue == $dictionary['appraise_title']) && ($assetValue == $dictionary['asset_title'])) {
                $comparisonFactor = [
                    'appraise_id' => $appraiseId,
                    'asset_general_id' => $assetGeneralId,
                    'status' => $status,
                    'type' => 'phap_ly',
                    'appraise_title' => $appraiseValue,
                    'asset_title' => $assetValue,
                    'description' => $dictionary['description'],
                    'adjust_percent' => $dictionary['adjust_percent'],
                    'name' => 'Pháp lý'
                ];
                $comparisonFactor = new AppraiseComparisonFactor($comparisonFactor);
                $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                    ->updateOrInsert(['id' => $comparisonFactorId], $comparisonFactor->attributesToArray());
            }
        }
    }

    private function comparisonSize( float $appraiseValue,float $assetValue, int $status , int $appraiseId,int $assetGeneralId ,array $dictionaries = null)
    {
        $description = CompareMaterData::COMPARISONS_DESCRIPTION['tuong_dong'];
        $comparisonFactor = [
            'appraise_id' => $appraiseId,
            'asset_general_id' => $assetGeneralId,
            'status' => $status,
            'type' => 'quy_mo',
            'appraise_title' => $appraiseValue,
            'asset_title' => $assetValue,
            'description' => $description,
            'adjust_percent' => 0,
            'name' => 'Quy mô'
        ];
        $comparisonFactor = new AppraiseComparisonFactor($comparisonFactor);
        $comparisonFactorId = QueryBuilder::for($comparisonFactor)
            ->insertGetId($comparisonFactor->attributesToArray());
    }

    private function comparisonFrontSideWidth( float $appraiseValue,float $assetValue, int $status , int $appraiseId,int $assetGeneralId ,array $dictionaries = null)
    {
        $description = CompareMaterData::COMPARISONS_DESCRIPTION['tuong_dong'];

        $comparisonFactor = [
            'appraise_id' => $appraiseId,
            'asset_general_id' => $assetGeneralId,
            'status' => $status,
            'type' => 'chieu_rong_mat_tien',
            'appraise_title' => $appraiseValue,
            'asset_title' => $assetValue,
            'description' => $description,
            'adjust_percent' => 0,
            'name' => 'Chiều rộng mặt tiền'
        ];

        $comparisonFactor = new AppraiseComparisonFactor($comparisonFactor);
        $comparisonFactorId = QueryBuilder::for($comparisonFactor)
            ->insertGetId($comparisonFactor->attributesToArray());
    }

    private function comparisonInsightWitdh( float $appraiseValue,float $assetValue, int $status , int $appraiseId,int $assetGeneralId, array $dictionaries = null )
    {
        $description = CompareMaterData::COMPARISONS_DESCRIPTION['tuong_dong'];

        $comparisonFactor = [
            'appraise_id' => $appraiseId,
            'asset_general_id' => $assetGeneralId,
            'status' => $status,
            'type' => 'chieu_sau_khu_dat',
            'appraise_title' => $appraiseValue,
            'asset_title' => $assetValue,
            'description' => $description,
            'adjust_percent' => 0,
            'name' => 'Chiều sâu khu đất'
        ];

        $comparisonFactor = new AppraiseComparisonFactor($comparisonFactor);
        $comparisonFactorId = QueryBuilder::for($comparisonFactor)
            ->insertGetId($comparisonFactor->attributesToArray());
    }

    private function comparisonMainRoadLength(float $appraiseRoad,float $assetRoad, int $status , int $appraiseId,int $assetGeneralId, array $dictionaries = null)
    {
        $assetRoadLength = 'Không biết';
        $appraiseRoadLength = 'Không biết';

        if ($assetRoad != 0 && $assetRoad < 2) {
            $assetRoadLength = '< 2m';
        }
        if ($assetRoad >= 2 && $assetRoad < 3) {
            $assetRoadLength = '≥ 2m đến < 3m';
        }
        if ($assetRoad >= 3 && $assetRoad < 5) {
            $assetRoadLength = '≥ 3m đến < 5m';
        }
        if ($assetRoad >= 5) {
            $assetRoadLength = '≥ 5m';
        }

        if ($appraiseRoad != 0 && $appraiseRoad < 2) {
            $appraiseRoadLength = '< 2m';
        }
        if ($appraiseRoad >= 2 && $appraiseRoad < 3) {
            $appraiseRoadLength = '≥ 2m đến < 3m';
        }
        if ($appraiseRoad >= 3 && $appraiseRoad < 5) {
            $appraiseRoadLength = '≥ 3m đến < 5m';
        }
        if ($appraiseRoad >= 5) {
            $appraiseRoadLength = '≥ 5m';
        }

        $description = CompareMaterData::COMPARISONS_DESCRIPTION['tuong_dong'];

        $comparisonFactor = [
            'appraise_id' => $appraiseId,
            'asset_general_id' => $assetGeneralId,
            'status' => $status,
            'type' => 'do_rong_duong',
            'appraise_title' => $appraiseRoad,
            'asset_title' => $assetRoad,
            'description' => $description,
            'adjust_percent' => 0,
            'name' => 'Bề rộng đường'
        ];
        $comparisonFactor = new AppraiseComparisonFactor($comparisonFactor);
        $comparisonFactorId = QueryBuilder::for($comparisonFactor)
            ->insertGetId($comparisonFactor->attributesToArray());

        foreach ($dictionaries as $dictionary) {
            if (($appraiseRoadLength == $dictionary['appraise_title']) && ($assetRoadLength == $dictionary['asset_title'])) {
                $comparisonFactor = [
                    'appraise_id' => $appraiseId,
                    'asset_general_id' => $assetGeneralId,
                    'status' => $status,
                    'type' => 'do_rong_duong',
                    'appraise_title' => $appraiseRoad,
                    'asset_title' => $assetRoad,
                    'description' => $dictionary['description'],
                    'adjust_percent' => $dictionary['adjust_percent'],
                    'name' => 'Bề rộng đường'
                ];

                $comparisonFactor = new AppraiseComparisonFactor($comparisonFactor);
                $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                    ->updateOrInsert(['id' => $comparisonFactorId], $comparisonFactor->attributesToArray());
            }
        }
    }

    private function comparisonLandSharp(string $appraiseValue,string $assetValue, int $status , int $appraiseId,int $assetGeneralId ,array $dictionaries = null)
    {

        $comparisonFactor = [
            'appraise_id' => $appraiseId,
            'asset_general_id' => $assetGeneralId,
            'status' => $status,
            'type' => 'hinh_dang_dat',
            'appraise_title' => $appraiseValue,
            'asset_title' => $assetValue,
            'description' => CompareMaterData::COMPARISONS_DESCRIPTION['tuong_dong'],
            'adjust_percent' => 0,
            'name' => 'Hình dáng đất'
        ];
        $comparisonFactor = new AppraiseComparisonFactor($comparisonFactor);
        $comparisonFactorId = QueryBuilder::for($comparisonFactor)
            ->insertGetId($comparisonFactor->attributesToArray());

        foreach ($dictionaries as $dictionary) {
            if (($appraiseValue == $dictionary['appraise_title']) && ($assetValue == $dictionary['asset_title'])) {
                $comparisonFactor = [
                    'appraise_id' => $appraiseId,
                    'asset_general_id' => $assetGeneralId,
                    'status' => $status,
                    'type' => 'hinh_dang_dat',
                    'appraise_title' => $appraiseValue,
                    'asset_title' => $assetValue,
                    'description' => $dictionary['description'],
                    'adjust_percent' => $dictionary['adjust_percent'],
                    'name' => 'Hình dáng đất'
                ];
                $comparisonFactor = new AppraiseComparisonFactor($comparisonFactor);
                $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                    ->updateOrInsert(['id' => $comparisonFactorId], $comparisonFactor->attributesToArray());
            }
        }
    }

    private function comparisonMaterial(string $appraiseValue,string $assetValue, int $status , int $appraiseId,int $assetGeneralId ,array $dictionaries = null)
    {
        $comparisonFactor = [
            'appraise_id' => $appraiseId,
            'asset_general_id' => $assetGeneralId,
            'status' => $status,
            'type' => 'ket_cau_duong',
            'appraise_title' => $appraiseValue,
            'asset_title' => $assetValue,
            'description' => CompareMaterData::COMPARISONS_DESCRIPTION['tuong_dong'],
            'adjust_percent' => 0,
            'name' => 'Kết cấu đường'
        ];
        $comparisonFactor = new AppraiseComparisonFactor($comparisonFactor);
        $comparisonFactorId = QueryBuilder::for($comparisonFactor)
            ->insertGetId($comparisonFactor->attributesToArray());
        foreach ($dictionaries as $dictionary) {
            if (($appraiseValue == $dictionary['appraise_title']) && ($assetValue == $dictionary['asset_title'])) {
                $comparisonFactor = [
                    'appraise_id' => $appraiseId,
                    'asset_general_id' => $assetGeneralId,
                    'status' => $status,
                    'type' => 'ket_cau_duong',
                    'appraise_title' => $appraiseValue,
                    'asset_title' => $assetValue,
                    'description' => $dictionary['description'],
                    'adjust_percent' => $dictionary['adjust_percent'],
                    'name' => 'Kết cấu đường'
                ];
                $comparisonFactor = new AppraiseComparisonFactor($comparisonFactor);
                $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                    ->updateOrInsert(['id' => $comparisonFactorId], $comparisonFactor->attributesToArray());
            }
        }
    }

    private function comparisonTurning(string $appraiseValue,string $assetValue, int $status , int $appraiseId,int $assetGeneralId ,array $dictionaries = null)
    {
        $comparisonFactor = [
            'appraise_id' => $appraiseId,
            'asset_general_id' => $assetGeneralId,
            'status' => $status,
            'type' => 'giao_thong',
            'appraise_title' => $appraiseValue,
            'asset_title' => $assetValue,
            'description' => CompareMaterData::COMPARISONS_DESCRIPTION['tuong_dong'],
            'adjust_percent' => 0,
            'name' => 'Giao thông'
        ];

        $comparisonFactor = new AppraiseComparisonFactor($comparisonFactor);
        $comparisonFactorId = QueryBuilder::for($comparisonFactor)
            ->insertGetId($comparisonFactor->attributesToArray());

        foreach ($dictionaries as $dictionary) {
            if (($appraiseValue == $dictionary['appraise_title']) && ($assetValue == $dictionary['asset_title'])) {
                $comparisonFactor = [
                    'appraise_id' => $appraiseId,
                    'asset_general_id' => $assetGeneralId,
                    'status' => $status,
                    'type' => 'giao_thong',
                    'appraise_title' => $appraiseValue,
                    'asset_title' => $assetValue,
                    'description' => $dictionary['description'],
                    'adjust_percent' => $dictionary['adjust_percent'],
                    'name' => 'Giao thông'
                ];
                $comparisonFactor = new AppraiseComparisonFactor($comparisonFactor);
                $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                    ->updateOrInsert(['id' => $comparisonFactorId], $comparisonFactor->attributesToArray());
            }

        }
    }

    private function comparisonConditions(string $appraiseValue,string $assetValue, int $status , int $appraiseId,int $assetGeneralId ,array $dictionaries = null)
    {
        $comparisonFactor = [
            'appraise_id' => $appraiseId,
            'asset_general_id' => $assetGeneralId,
            'status' => $status,
            'type' => 'dieu_kien_ha_tang',
            'appraise_title' => $appraiseValue,
            'asset_title' => $assetValue,
            'description' => CompareMaterData::COMPARISONS_DESCRIPTION['tuong_dong'],
            'adjust_percent' => 0,
            'name' => 'Điều kiện hạ tầng'
        ];
        $comparisonFactor = new AppraiseComparisonFactor($comparisonFactor);
        $comparisonFactorId = QueryBuilder::for($comparisonFactor)
            ->insertGetId($comparisonFactor->attributesToArray());
        foreach ($dictionaries as $dictionary) {
            if (($appraiseValue == $dictionary['appraise_title']) && ($assetValue == $dictionary['asset_title'])) {
                $comparisonFactor = [
                    'appraise_id' => $appraiseId,
                    'asset_general_id' => $assetGeneralId,
                    'status' => $status,
                    'type' => 'dieu_kien_ha_tang',
                    'appraise_title' => $appraiseValue,
                    'asset_title' => $assetValue,
                    'description' => $dictionary['description'],
                    'adjust_percent' => $dictionary['adjust_percent'],
                    'name' => 'Điều kiện hạ tầng'
                ];
                $comparisonFactor = new AppraiseComparisonFactor($comparisonFactor);
                $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                    ->updateOrInsert(['id' => $comparisonFactorId], $comparisonFactor->attributesToArray());
            }

        }
    }

    private function comparisonSecurity(string $appraiseValue,string $assetValue, int $status , int $appraiseId,int $assetGeneralId ,array $dictionaries = null)
    {
        $comparisonFactor = [
            'appraise_id' => $appraiseId,
            'asset_general_id' => $assetGeneralId,
            'status' => $status,
            'type' => 'an_ninh_moi_truong_song',
            'appraise_title' => $appraiseValue,
            'asset_title' => $assetValue,
            'description' => CompareMaterData::COMPARISONS_DESCRIPTION['tuong_dong'],
            'adjust_percent' => 0,
            'name' => 'An ninh môi trường sống'
        ];
        $comparisonFactor = new AppraiseComparisonFactor($comparisonFactor);
        $comparisonFactorId = QueryBuilder::for($comparisonFactor)
            ->insertGetId($comparisonFactor->attributesToArray());
        foreach ($dictionaries as $dictionary) {
            if (($appraiseValue == $dictionary['appraise_title']) && ($assetValue == $dictionary['asset_title'])) {
                $comparisonFactor = [
                    'appraise_id' => $appraiseId,
                    'asset_general_id' => $assetGeneralId,
                    'status' => $status,
                    'type' => 'an_ninh_moi_truong_song',
                    'appraise_title' => $appraiseValue,
                    'asset_title' => $assetValue,
                    'description' => $dictionary['description'],
                    'adjust_percent' => $dictionary['adjust_percent'],
                    'name' => 'An ninh môi trường sống'
                ];
                $comparisonFactor = new AppraiseComparisonFactor($comparisonFactor);
                $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                    ->updateOrInsert(['id' => $comparisonFactorId], $comparisonFactor->attributesToArray());
            }

        }
    }

    private function comparisonBussiness(string $appraiseValue,string $assetValue, int $status , int $appraiseId,int $assetGeneralId ,array $dictionaries = null)
    {
        $comparisonFactor = [
            'appraise_id' => $appraiseId,
            'asset_general_id' => $assetGeneralId,
            'status' => $status,
            'type' => 'kinh_doanh',
            'appraise_title' => $appraiseValue,
            'asset_title' => $assetValue,
            'description' => CompareMaterData::COMPARISONS_DESCRIPTION['tuong_dong'],
            'adjust_percent' => 0,
            'name' => 'Kinh doanh'
        ];
        $comparisonFactor = new AppraiseComparisonFactor($comparisonFactor);
        $comparisonFactorId = QueryBuilder::for($comparisonFactor)
            ->insertGetId($comparisonFactor->attributesToArray());
        foreach ($dictionaries as $dictionary) {
            if (($appraiseValue == $dictionary['appraise_title']) && ($assetValue == $dictionary['asset_title'])) {
                $comparisonFactor = [
                    'appraise_id' => $appraiseId,
                    'asset_general_id' => $assetGeneralId,
                    'status' => $status,
                    'type' => 'kinh_doanh',
                    'appraise_title' => $appraiseValue,
                    'asset_title' => $assetValue,
                    'description' => $dictionary['description'],
                    'adjust_percent' => $dictionary['adjust_percent'],
                    'name' => 'Kinh doanh'
                ];
                $comparisonFactor = new AppraiseComparisonFactor($comparisonFactor);
                $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                    ->updateOrInsert(['id' => $comparisonFactorId], $comparisonFactor->attributesToArray());
            }

        }
    }

    private function comparisonFengShui(string $appraiseValue,string $assetValue, int $status , int $appraiseId,int $assetGeneralId ,array $dictionaries = null)
    {
        $comparisonFactor = [
            'appraise_id' => $appraiseId,
            'asset_general_id' => $assetGeneralId,
            'status' => $status,
            'type' => 'phong_thuy',
            'appraise_title' => $appraiseValue,
            'asset_title' => $assetValue,
            'description' => CompareMaterData::COMPARISONS_DESCRIPTION['tuong_dong'],
            'adjust_percent' => 0,
            'name' => 'Phong thuỷ'
        ];
        $comparisonFactor = new AppraiseComparisonFactor($comparisonFactor);
        $comparisonFactorId = QueryBuilder::for($comparisonFactor)
            ->insertGetId($comparisonFactor->attributesToArray());
        foreach ($dictionaries as $dictionary) {
            if (($appraiseValue == $dictionary['appraise_title']) && ($assetValue == $dictionary['asset_title'])) {
                $comparisonFactor = [
                    'appraise_id' => $appraiseId,
                    'asset_general_id' => $assetGeneralId,
                    'status' => $status,
                    'type' => 'phong_thuy',
                    'appraise_title' => $appraiseValue,
                    'asset_title' => $assetValue,
                    'description' => $dictionary['description'],
                    'adjust_percent' => $dictionary['adjust_percent'],
                    'name' => 'Phong thuỷ'
                ];
                $comparisonFactor = new AppraiseComparisonFactor($comparisonFactor);
                $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                    ->updateOrInsert(['id' => $comparisonFactorId], $comparisonFactor->attributesToArray());
            }

        }
    }

    private function comparisonZoning(string $appraiseValue,string $assetValue, int $status , int $appraiseId,int $assetGeneralId ,array $dictionaries = null)
    {
        $comparisonFactor = [
            'appraise_id' => $appraiseId,
            'asset_general_id' => $assetGeneralId,
            'status' => $status,
            'type' => 'quy_hoach',
            'appraise_title' => $appraiseValue,
            'asset_title' => $assetValue,
            'description' => CompareMaterData::COMPARISONS_DESCRIPTION['tuong_dong'],
            'adjust_percent' => 0,
            'name' => 'Quy hoạch/Hiện trạng'
        ];
        $comparisonFactor = new AppraiseComparisonFactor($comparisonFactor);
        $comparisonFactorId = QueryBuilder::for($comparisonFactor)
            ->insertGetId($comparisonFactor->attributesToArray());
        $comparisonFactor = [
            'appraise_id' => $appraiseId,
            'asset_general_id' => $assetGeneralId,
            'status' => $status,
            'type' => 'quy_hoach',
            'appraise_title' => $appraiseValue,
            'asset_title' => $assetValue,
            'description' => CompareMaterData::COMPARISONS_DESCRIPTION['tuong_dong'],
            'adjust_percent' => 0,
            'name' => 'Quy hoạch/Hiện trạng'
        ];
        $comparisonFactor = new AppraiseComparisonFactor($comparisonFactor);
        $comparisonFactorId = QueryBuilder::for($comparisonFactor)
            ->updateOrInsert(['id' => $comparisonFactorId], $comparisonFactor->attributesToArray());

    }

    private function comparisonPayment(string $appraiseValue,string $assetValue, int $status , int $appraiseId,int $assetGeneralId ,array $dictionaries = null)
    {
        $comparisonFactor = [
            'appraise_id' => $appraiseId,
            'asset_general_id' => $assetGeneralId,
            'status' => $status,
            'type' => 'dieu_kien_thanh_toan',
            'appraise_title' => $appraiseValue,
            'asset_title' => $assetValue,
            'description' => CompareMaterData::COMPARISONS_DESCRIPTION['tuong_dong'],
            'adjust_percent' => 0,
            'name' => 'Điều kiện thanh toán'
        ];
        $comparisonFactor = new AppraiseComparisonFactor($comparisonFactor);
        $comparisonFactorId = QueryBuilder::for($comparisonFactor)
            ->insertGetId($comparisonFactor->attributesToArray());
        foreach ($dictionaries as $dictionary) {
            if (($appraiseValue == $dictionary['appraise_title']) && ($assetValue == $dictionary['asset_title'])) {
                $comparisonFactor = [
                    'appraise_id' => $appraiseId,
                    'asset_general_id' => $assetGeneralId,
                    'status' => $status,
                    'type' => 'dieu_kien_thanh_toan',
                    'appraise_title' => $appraiseValue,
                    'asset_title' => $assetValue,
                    'description' => $dictionary['description'],
                    'adjust_percent' => $dictionary['adjust_percent'],
                    'name' => 'Điều kiện thanh toán'
                ];
                $comparisonFactor = new AppraiseComparisonFactor($comparisonFactor);
                $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                    ->updateOrInsert(['id' => $comparisonFactorId], $comparisonFactor->attributesToArray());
            }

        }
    }
    #endregion

    #region calculate price
    private function getAppraiseCalculate(int $appraiseId){
        $version = request()->get('version');
        $result = null;
        if ($version && !is_array($version)) {
            $result = $this->findVersionById($appraiseId, $version);
        }
        if (!$result) {
            $result = $this->model->query()
                ->where('id', '=', $appraiseId)
                ->first();
        }
        $result->append('assets_general');
        $asset = $result;
        $asset->assetGeneral = $asset->asset_general;

        $result->append('layer_cutting_procedure');
        $result->append('layer_cutting_price');
        $result->append('unify_indicative_price_slug');
        $result->append('composite_land_remaning_slug');
        $result->append('composite_land_remaning_value');
        $result->append('planning_violation_price_slug');
        $result->append('planning_violation_price_value');
        $result->append('round_total');
        $result->append('round_composite');
        $result->append('round_violation_composite');
        $result->append('round_violation_facility');
        $result->append('round_appraise_total');
        $result->append('price_land_asset');
        $result->append('price_tangible_asset');
        $result->append('price_other_asset');
        $result->append('price_total_asset');

        if (isset($result['asset_general']) && !empty($result['asset_general'])) {
            CommonService::getAssetPriceTotal($result);
        }
    }

    private function getBaseUBNDPrice(int $appraiseId)
    {
        $result = [];
        if (AppraiseProperty::where('appraise_id',$appraiseId)->exists()){
            $propertyId = AppraiseProperty::where('appraise_id',$appraiseId)->get()->first()->id;
            $select = ['id','appraise_property_id','circular_unit_price','land_type_purpose_id','position_type_id'
                ];
            $with= [
                ];
            $result = AppraisePropertyDetail::with($with)
                ->select($select)
                ->where(['appraise_property_id' => $propertyId])
                ->where(['is_transfer_facility' => 1])
                ->get()
                ->first();
        }
        return $result;
    }

    public function getAppraiseCalculatelData(int $appraiseId)
    {
        $appraiseAdapter  = $this->getAppraiseAdapter($appraiseId);
        $assetUnitPrice  = $this->getAssetUnitPrice($appraiseId);
        $assetUnitAreas  = $this->getAssetUnitArea($appraiseId);
        $comparisonFactor = $this->getComparison($appraiseId);
        $appraisePrice = $this->getAppraisePrice($appraiseId);
        $constructionCompany = $this->getContructionCompany($appraiseId);
        $getTangibleComparison = $this->getTangibleComparison($appraiseId);
        $otherAsset = $this->getOtherAsset($appraiseId);
        return [
            'appraise_adapter' => $appraiseAdapter,
            'asset_unit_area' => $assetUnitAreas,
            'asset_unit_price' => $assetUnitPrice,
            'comparison_factor' => $comparisonFactor,
            'asset_price' => $appraisePrice,
            'construction_company' => $constructionCompany,
            'comparison_tangible_factor' => $getTangibleComparison,
            'other_assets' => $otherAsset,

        ];

    }

    private function getAssetUnitPrice(int $appraiseId,int $asset_general_id = null)
    {
        $result = [];
        if (AppraiseUnitPrice::where('appraise_id',$appraiseId)->exists()){
            $select = ['id','appraise_id','asset_general_id','land_type_id','update_value','original_value'
                ];
            $with= [
                ];
            $result = AppraiseUnitPrice::with($with)
                ->select($select)
                ->where(['appraise_id'=> $appraiseId]);
            if(isset($asset_general_id))
                $result = $result->where(['asset_general_id'=>$asset_general_id]);
            $result = $result->get();

        }
        return $result;
    }

    private function getAssetUnitArea(int $appraiseId,int $asset_general_id=null)
    {
        $result = [];
        if (AppraiseUnitArea::where('appraise_id',$appraiseId)->exists()){
            $select = ['id','appraise_id','asset_general_id','land_type_id','violation_asset_area'
                ];
            $with= [
                ];
            $result = AppraiseUnitArea::with($with)
                ->select($select)
                ->where(['appraise_id'=> $appraiseId]);
            if(isset($asset_general_id))
                $result = $result->where(['asset_general_id'=>$asset_general_id]);
            $result = $result->get();
        }
        return $result;
    }

    private function getComparison(int $appraiseId, int $asset_general_id = null)
    {
        $result = [];
        if (AppraiseComparisonFactor::where('appraise_id',$appraiseId)->exists()){
            $select = ['id','appraise_id','asset_general_id','adjust_percent','appraise_title','asset_title','description'
                        ,'name','type','position','status'
                ];
            $with= [
                ];
            $result = AppraiseComparisonFactor::with($with)
                ->select($select)
                ->where(['appraise_id'=>$appraiseId])
                ->orderbyDesc('asset_general_id')
                ->get();
        }
        return $result ;
    }

    private function getAppraiseAdapter(int $appraiseId,int $asset_general_id=null)
    {
        $result = [];
        if (AppraiseAdapter::where('appraise_id',$appraiseId)->exists()){
            $select = ['id','appraise_id','asset_general_id','change_purpose_price','percent'
                ];
            $with= [
                ];
            $result = AppraiseAdapter::with($with)
                ->select($select)
                ->where(['appraise_id'=>$appraiseId]);
            if(isset($asset_general_id))
                $result = $result->where(['asset_general_id'=>$asset_general_id]);
            $result = $result->get();
            if(isset($asset_general_id))
                $result = $result->first();
        }
        return $result;
    }

    private function getAppraisePrice(int $appraiseId)
    {
        $result = [];
        if (AppraisePrice::where('appraise_id',$appraiseId)->exists()){
            $select = ['id','appraise_id','slug','value','description'
                ];
            $with= [
                ];
            $result = AppraisePrice::with($with)
                ->select($select)
                ->where(['appraise_id'=>$appraiseId]);
            $result = $result->get();
        }
        return $result;
    }

    private function getContructionCompanyDefault()
    {
        $result = [];

        $select = ['id','name','address','phone_number','manager_name','unit_price_m2','is_defaults',
                    DB::raw("id as construction_company_id"),
                ];
        $with= [
            ];
        $result = AppraisalConstructionCompany::with($with)
            ->select($select)
            ->orderbyDesc('is_defaults')
            ->orderby('id')
            ->take(3)
            ->get();

        return $result;
    }
    private function getContructionCompany(int $appraiseId)
    {
        $result = [];
        if (ConstructionCompany::where('appraise_id',$appraiseId)->exists()){
            $select = ['id','appraise_id','construction_company_id','name','address','unit_price_m2','is_defaults'
                ];
            $with= [
                ];
            $result = ConstructionCompany::with($with)
                ->select($select)
                ->where(['appraise_id'=>$appraiseId]);
            $result = $result->get();
            }
        return $result;
    }

    private function getTangibleComparison(int $appraiseId)
    {
        $result = [];
        if (AppraiseTangibleComparisonFactor::where('appraise_id',$appraiseId)->exists()){
            $select = ['id','appraise_id',
                            'p1', 'h1',  'p2',  'h2',    'p3',  'h3','d4', 'h4','p5','h5',
                ];
            $with= [
                ];
            $result = AppraiseTangibleComparisonFactor::with($with)
                ->select($select)
                ->where(['appraise_id'=>$appraiseId]);
            $result = $result->get();
        }
        return $result;
    }

    private function getOtherAsset(int $appraiseId)
    {
        $result = [];
        if (AppraiseOtherAsset::where('appraise_id',$appraiseId)->exists()){
            $select = ['id','appraise_id',
                        'name', 'total','dvt',
                        'description','total_area',
                        'unit_price','total_price',
                ];
            $with= [
                ];
            $result = AppraiseOtherAsset::with($with)
                ->select($select)
                ->where(['appraise_id'=>$appraiseId]);
            $result = $result->get();
        }
        return $result;
    }
    #endregion

    #endregion
    // update and reset data if step < 6
    private function updateAppraiseStep(int $appraiseId, int $step){
        if(Appraise::where('id',$appraiseId)->exists()){
            $appraise = Appraise::where('id',$appraiseId)->first();
            if ($appraise->status === 3) {
                $status = 3;
            } else {
                if ($step == 6 || $step == 7) {
                    $status = 2;
                } else {
                    $status = 1;
                    if (!empty($appraise->appraiseHasAssets))
                        $this->resetDataStep6($appraiseId);
                }
            }
            $update = ['step' => $step,
                    'status' => $status
                ];
            if($appraise->status != $status){
                $edited = $appraise;
                $edited->step = $step;
                $edited->status = $status;
                $this->CreateActivityLog($appraise, $edited, 'update_status', 'cập nhật trạng thái');
            }
            Appraise::where('id',$appraiseId)->update($update);

            $this->updateRealEstates($appraiseId);
        }
        return $update;
    }

    private function updateAppraiseStepVer1(int $appraiseId, int $step){
        if(Appraise::where('id',$appraiseId)->exists()){
            $appraise = Appraise::where('id',$appraiseId)->first();
            if ($appraise->status === 3) {
                $status = 3;
            } else {
                if ($step == 6 || $step == 7) {
                    $status = 2;
                } else {
                    $status = 1;
                    if (!empty($appraise->appraiseHasAssets))
                        $this->resetDataStep6($appraiseId);
                }
            }
            $update = ['step' => $step,
                    'status' => $status
                ];
            if($appraise->status != $status){
                $edited = $appraise;
                $edited->step = $step;
                $edited->status = $status;
                // $this->CreateActivityLog($appraise, $edited, 'update_status', 'cập nhật trạng thái');
            }
            Appraise::where('id',$appraiseId)->update($update);

            $this->updateRealEstates($appraiseId);
        }
        return $update;
    }

    public function getAppraiseStep(int $appraiseId = null){
        $result = ['step' => 1];
        if(isset($appraiseId))
            if(Appraise::where('id',$appraiseId)->exists()){
                $result= Appraise::where('id',$appraiseId)->get('step')->first();
            }
        return $result;
    }

    public function getAppraiseData(int $appraiseId){
        if(Appraise::where('id',$appraiseId)->exists())
        {
            $check = $this->checkUser($appraiseId);
            if(isset($check)){
                return $check;
            }
            $version = request()->get('version');
            $result = null;
            if ($version && !is_array($version)) {
                $result = $this->findVersionById($appraiseId, $version);
            }
            if (!$result) {
                $result = $this->model->query()
                    ->where('id', '=', $appraiseId)
                    // ->with('filter_year')
                    ->with('province')
                    ->with('district')
                    ->with('ward')
                    ->with('street')
                    ->with('distance')
                    ->with('assetType')
                    ->with('pic.picType')
                    ->with('topographic')
                    ->with('appraiseApproach')
                    ->with('appraisePrinciple')
                    ->with('appraiseMethodUsed')
                    ->with('appraiseBasisProperty')
                    ->with('properties.propertyDetail')
                    ->with('properties.propertyTurningTime')
                    ->with('properties.propertyTurningTime.material')
                    ->with('properties.propertyDetail.landTypePurpose')
                    ->with('properties.propertyDetail.positionType')
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
                    ->with('tangibleAssets.rate')
                    ->with('tangibleAssets.structure')
                    ->with('tangibleAssets.crane')
                    ->with('tangibleAssets.aperture')
                    ->with('tangibleAssets.factoryType')
                    ->with('tangibleAssets.constructionCompany')
                    ->with('tangibleAssets.comparisonTangibleFactor')
                    ->with('otherAssets')
                    // ->with('constructionCompany.constructionCompany')
                    ->with('constructionCompany')
                    ->with('appraiseLaw.law')
                    ->with('appraiseLaw.lawDetails')
                    ->with('appraiseLaw.lawDetails.landTypePurpose')
                    ->with('appraiseLaw.landDetails')
                    ->with('appraiseHasAssets')
                    ->with('createdBy')
                    ->with('comparisonFactor')
                    // ->with('comparisonFactor')
                    ->with('appraiseAdapter')
                    ->with('comparisonTangibleFactor')
                    ->with('version')
                    ->with('assetUnitPrice')
                    ->with('assetUnitPrice.landTypeData')
                    ->with('assetUnitPrice.createdBy')
                    ->with('assetUnitArea')
                    ->with('assetUnitArea.landTypeData')
                    ->with('assetUnitArea.createdBy')
                    ->with('assetPrice')
                    ->with('appraisalMethods')
                    ->with('certificate:id,status,sub_status')
                    ->first();
            }
            // dd($result->appraiseMethodUsed);
            $result->append('asset_general');
            $asset = $result;
            // $asset->assetGeneral = $asset->asset_general;
            $result->append('layer_cutting_procedure');
            $result->append('layer_cutting_price');
            $result->append('unify_indicative_price_slug');
            $result->append('composite_land_remaning_slug');
            $result->append('composite_land_remaning_value');
            $result->append('planning_violation_price_slug');
            $result->append('planning_violation_price_value');
            $result->append('round_total');
            $result->append('round_composite');
            $result->append('round_violation_composite');
            $result->append('round_violation_facility');
            $result->append('round_appraise_total');
            $result->append('price_land_asset');
            $result->append('price_tangible_asset');
            $result->append('price_other_asset');
            $result->append('price_total_asset');
            $result->append('total_desicion_average');
            // $result->append('filter_year');

            // if(isset($result->comparisonFactor) && count($result->comparisonFactor) > 0){
            //     foreach($result->comparisonFactor as $comparison){
            //         // $comparison->append('description');
            //         $comparison->append('description_capitalize');
            //         $comparison->append('appraise_title_capitalize');
            //         $comparison->append('asset_title_capitalize');
            //     }
            // }

            $result->unify_indicative_price = [];
            foreach(AppraiseOtherInformationEnum::DATA['thong_nhat_muc_gia_chi_dan'] as $item) {
                if($item['slug'] == $result->unify_indicative_price_slug) {
                    $result->unify_indicative_price = $item;
                }
            }

            $result->composite_land_remaning = [];
            foreach(AppraiseOtherInformationEnum::DATA['tinh_gia_dat_hon_hop_con_lai'] as $item) {
                if($item['slug'] == $result->composite_land_remaning_slug) {
                    $result->composite_land_remaning = $item;
                }
            }
            $result->planning_violation_price = [];
            foreach(AppraiseOtherInformationEnum::DATA['tinh_gia_dat_vi_pham_quy_hoach'] as $item) {
                if($item['slug'] == $result->planning_violation_price_slug) {
                    $result->planning_violation_price = $item;
                }
            }
            return $result;
        }else{
            return ['message' => ErrorMessage::APPRAISE_NOTEXISTS . $appraiseId, 'exception' => ''];
        }

    }

    public function postOtherAssets(array $objects , int $appraiseId)
    {
        DB::beginTransaction();
        try{
            $check = $this->beforeSave($appraiseId);
            if(isset($check)){
                return $check;
            }

            if(Appraise::where('id',$appraiseId)->exists())
            {
                $otherAssetTotal = 0;
                if(AppraiseOtherAsset::where('appraise_id',$appraiseId)->exists())
                    AppraiseOtherAsset::where('appraise_id',$appraiseId)->delete();

                    if (isset($objects['other_assets']) && count($objects['other_assets'])>0 ) {
                        foreach ($objects['other_assets'] as $otherAssetData) {
                            $otherAssetTotal = $otherAssetTotal + $otherAssetData['total_price'];

                            $otherAssetData['appraise_id'] = $appraiseId;
                            $otherAssetData['unit_price'] = $otherAssetData['unit_price'];
                            $otherAssetData['total_price'] = $otherAssetData['total_price'];
                            $otherAssetData['description'] = isset($otherAssetData['description'])?$otherAssetData['description']:'';
                            $otherAssetData['name'] = $otherAssetData['name'];
                            $otherAssetData['total'] = $otherAssetData['total'];
                            $otherAssetData['dvt'] = $otherAssetData['dvt'];
                            $otherAssetData['total_area'] = 0; // not use but data not null -> must be 0

                            $otherAsset = new AppraiseOtherAsset($otherAssetData);
                            QueryBuilder::for($otherAsset)
                                ->insert($otherAsset->attributesToArray());
                    }
                }
                $appraise = $this->model->query()->where('id', $appraiseId)->first();
                $tangibleTotal = CommonService::getOtherAssetPriceTotal($appraise);
                AppraisePrice::query()->updateOrCreate(['appraise_id' => $appraiseId, 'slug' => 'other_asset_price'], ['value' => $tangibleTotal]);
                $this->calculateTotalPrice($appraiseId);
                # cập nhật thông tin tài sản khác
                # activity-log
                $this->CreateActivityLog($appraise, $appraise, 'update-data', 'cập nhật dữ liệu thông tin tài sản khác');

                $this->updateAppraiseStep($appraiseId, 7);
                $this->processAfterSave($appraiseId);
                DB::commit();
                $result = $this->getPriceById($appraiseId);
                return $result;
            }

        }
        catch(Exception $ex){
            DB::rollBack();
            Log::error($ex);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' =>  $ex->getMessage()];
            return $data;
        }
    }

    private function resetDataStep6(int $appraiseId)
    {
        $certificate = $this->model->query()->where('id', $appraiseId)->first();
        if ($certificate->status < 3) {
             //delete comparison factor
            if(AppraiseComparisonFactor::query()->where('appraise_id', '=', $appraiseId)->exists())
            AppraiseComparisonFactor::query()->where('appraise_id', '=', $appraiseId)->forceDelete();
            //delete appraise unit price
            if(AppraiseUnitPrice::query()->where('appraise_id', '=', $appraiseId)->exists())
                AppraiseUnitPrice::query()->where('appraise_id', '=', $appraiseId)->forceDelete();
            //delete appraise unit area
            if(AppraiseUnitArea::query()->where('appraise_id', '=', $appraiseId)->exists())
                AppraiseUnitArea::query()->where('appraise_id', '=', $appraiseId)->forceDelete();
            //delete appraise has assets
            if(AppraiseHasAsset::query()->where('appraise_id', '=', $appraiseId)->exists())
                AppraiseHasAsset::query()->where('appraise_id', '=', $appraiseId)->forceDelete();
            //delete appraise adapter
            if(AppraiseAdapter::query()->where('appraise_id', '=', $appraiseId)->exists())
                AppraiseAdapter::query()->where('appraise_id', '=', $appraiseId)->forceDelete();
            //delete appraise price
            if(AppraisePrice::query()->where('appraise_id', '=', $appraiseId)->where('slug' ,'<>', 'estimate_asset_price')->exists())
                AppraisePrice::query()->where('appraise_id', '=', $appraiseId)->where('slug' ,'<>', 'estimate_asset_price')->forceDelete();
            //delete map image
            if(AppraisePic::query()->where('appraise_id', '=', $appraiseId)->where('type_id',153)->exists())
                AppraisePic::query()->where('appraise_id', '=', $appraiseId)->where('type_id',153)->forceDelete();
            //delete layer_cutting_procedure
            if( AppraiseAppraisalMethods::where('appraise_id', $appraiseId)->where('slug', 'layer_cutting_procedure')->exists())
                AppraiseAppraisalMethods::where('appraise_id', $appraiseId)->where('slug', 'layer_cutting_procedure')->forceDelete();
        }
    }

    public function getApraiseDataStepOneToSix(int $appraiseId){
        $check = $this->checkAuthorization($appraiseId);
        if (!empty($check))
            return $check;
        $select = ['id','step','status','coordinates','created_by', 'sub_status', 'certificate_id', 'real_estate_id', 'filter_year'];
        $with = [
            'pic:id,appraise_id,link,type_id',
            'pic.picType:id,description',
            'certificate:id,status,sub_status',
            'createdBy:id,name',
            'lastVersion',
            'realEstate'
            ];
        $result= Appraise::with($with)
            ->select($select)
            ->where('id',$appraiseId);

        $result = $result->first();
        $result['picture_infomation'] = $result['pic'];
        unset($result->pic);
        $result->append('land_details');
        $result->append('total_area');
        $result->append('planning_area');
        $result->append('UBND_price');
        $result->append('traffic_infomation');
        $result->append('economic_infomation');
        $result->append('general_infomation');
        $result->append('appraisal_methods');
        $result->append('value_base_and_approach');
        $result->append('map_img');
        $result->append('assets_general');
        $result->append('construction');
        $result->append('status_text');
        $result->append('distance_max');
        // $result->append('comparison_factor');
        $comparisonFactor = $this->getComparisonFactors($appraiseId);
        $result['comparison_factor'] = $comparisonFactor;

        $law = $this->getLaw($appraiseId);
        $result['law'] = $law;

        $version = AppraiseVersionService::getVersionAppraise($appraiseId);
        $result['max_version'] = $version;
        $geo = AppraiseProperty::where('appraise_id',$appraiseId)->first();
        $result['traffic_infomation'] = $geo;
        $result['geographical_location'] = $geo->geographical_location;
        return $result;
    }

    public function updateComparisonFactor_V2($objects , int $id)
    {
        $appraiseId = $id;
        // CommonService::getComparisonAsset($appraiseId);
        // CommonService::getComparisonAppraise($appraiseId);
        $check = $this->beforeSave($appraiseId);
        if(isset($check)){
            return $check;
        }
        $user = CommonService::getUser();

        if (isset($objects['comparison_factor'])) {
            $comparisonFactorDatas = isset($objects['other_comparison']) ? array_merge($objects['comparison_factor'], $objects['other_comparison']) : $objects['comparison_factor'];
            foreach ($comparisonFactorDatas as $comparisonFactorData) {
                $comparisonFactorData = array_map(function($v){
                    return (is_null($v)) ? "" : $v;
                },$comparisonFactorData);

                if(!isset($appraiseId)&&(isset($comparisonFactorData['appraise_id'])))
                    $appraiseId = $comparisonFactorData['appraise_id'];

                if (!isset($comparisonFactorData['adjust_percent'])) {
                    $comparisonFactorData['adjust_percent'] = 0;
                }
                if ($comparisonFactorData['adjust_percent']  > 0) {
                    $comparisonFactorData['description'] = CompareMaterData::COMPARISONS_DESCRIPTION['kem_thuan_loi'];
                } else if ($comparisonFactorData['adjust_percent']  < 0) {
                    $comparisonFactorData['description'] = CompareMaterData::COMPARISONS_DESCRIPTION['thuan_loi'];
                }
                else {
                    $comparisonFactorData['description'] = CompareMaterData::COMPARISONS_DESCRIPTION['tuong_dong'];
                }

                if(!$comparisonFactorData['status']&&($comparisonFactorData['type']=='yeu_to_khac')) {
                    $comparisonFactorData['status'] = 1;
                }

                $comparisonFactor = new AppraiseComparisonFactor($comparisonFactorData);
                if(isset($comparisonFactorData['id'])) {
                    $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                        ->updateOrInsert(['id' => $comparisonFactorData['id']], $comparisonFactor->attributesToArray());
                } else {
                    $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                        ->insert($comparisonFactor->attributesToArray());
                }

            }
        }

        if (isset($objects['delete_other_comparison'])) {
            foreach ($objects['delete_other_comparison'] as $comparisonFactorData) {
                if(isset($comparisonFactorData['id'])) {
                    AppraiseComparisonFactor::where('id', $comparisonFactorData['id'])->forceDelete();
                }
            }
        }

        if (isset($objects['asset_unit_price'])) {
            foreach ($objects['asset_unit_price'] as $item) {
                unset($item['land_type_data']);
                $item['created_by'] = $user->id;

                $appraiseUnitPrice = new AppraiseUnitPrice($item);
                if(isset($item['id'])) {
                    $appraiseUnitPriceId = QueryBuilder::for($appraiseUnitPrice)
                        ->updateOrInsert(['id' => $item['id']], $appraiseUnitPrice->attributesToArray());
                } else {
                    $appraiseUnitPriceId = QueryBuilder::for($appraiseUnitPrice)
                        ->insert($appraiseUnitPrice->attributesToArray());
                }
            }
        }

        if (isset($objects['asset_unit_area'])) {
            foreach ($objects['asset_unit_area'] as $item) {
                unset($item['land_type_data']);
                $item['created_by'] = $user->id;

                $appraiseUnitArea = new AppraiseUnitArea($item);
                if(isset($item['id'])) {
                    $appraiseUnitAreaId = QueryBuilder::for($appraiseUnitArea)
                        ->updateOrInsert(['id' => $item['id']], $appraiseUnitArea->attributesToArray());
                } else {
                    $appraiseUnitAreaId = QueryBuilder::for($appraiseUnitArea)
                        ->insert($appraiseUnitArea->attributesToArray());
                }
            }
        }

        if(isset($appraiseId)) {
            $appraise = $this->findById($appraiseId);

            if (isset($objects['remaining_price'])&&!empty($objects['remaining_price'])&&isset($appraise->composite_land_remaning_slug)&&$appraise->composite_land_remaning_slug=='theo-phuong-phap-doc-lap') {
                $remainingPrice = $objects['remaining_price'];
                if(isset($remainingPrice['remaining_commerce_price'])&&isset($remainingPrice['land_type'])) {
                    CommonService::setAppraisePrice($appraise, $remainingPrice['remaining_commerce_price'], 'land_asset_purpose_'.$remainingPrice['land_type'].'_price');
                }
            }
            $existAssetGeneralIds = [];
            if (isset($objects['appraise_adapter'])&&!empty($objects['appraise_adapter'])) {
                foreach($objects['appraise_adapter'] as $item) {
                    $appraiseAdapter = AppraiseAdapter::where('appraise_id', $item['appraise_id'])
                        ->where('asset_general_id', $item['asset_general_id'])->first();
                    if(isset($appraiseAdapter)) {
                        AppraiseAdapter::where('id', $appraiseAdapter->id)->update([
                            'percent' => $item['percent'],
                            'change_purpose_price' => $item['change_purpose_price'],
                            'change_violate_price' => isset($item['change_violate_price']) ? $item['change_violate_price'] : 0,
                        ]);
                        $existAssetGeneralIds[] = $appraiseAdapter->id;
                    } else {
                        $appraiseAdapterId = AppraiseAdapter::insert([
                            'appraise_id' => $item['appraise_id'],
                            'asset_general_id' => $item['asset_general_id'],
                            'percent' => $item['percent'],
                            'change_purpose_price' => $item['change_purpose_price'],
                            'change_violate_price' => isset($item['change_violate_price']) ? $item['change_violate_price'] : 0,
                        ]);
                        $existAssetGeneralIds[] = $appraiseAdapterId;
                    }
                }
            }

            if (isset($objects['layer_cutting_procedure'])) {
                $items = AppraiseAppraisalMethods::where('appraise_id', $appraiseId)->where('slug', 'layer_cutting_procedure')->get();
                if(count($items)==1) {
                    AppraiseAppraisalMethods::where('appraise_id', $appraiseId)->where('slug', 'layer_cutting_procedure')->update([
                        'appraise_id' => $appraiseId,
                        'slug' => "layer_cutting_procedure",
                        'slug_value' => $objects['layer_cutting_procedure'],
                    ]);
                } else {
                    if(count($items)>1) {
                        AppraiseAppraisalMethods::where('appraise_id', $appraiseId)->where('slug', 'layer_cutting_procedure')->forceDelete();
                    }
                    AppraiseAppraisalMethods::create([
                        'appraise_id' => $appraiseId,
                        'slug' => "layer_cutting_procedure",
                        'slug_value' => $objects['layer_cutting_procedure'],
                    ]);
                }
                if($objects['layer_cutting_procedure']) {
                    if (isset($objects['layer_cutting_price'])&&!empty($objects['layer_cutting_price'])) {
                        CommonService::setAppraisePrice($appraise, $objects['layer_cutting_price'], 'layer_cutting_price');
                    }
                }else
                {
                    AppraisePrice::where('appraise_id', $appraiseId)->where('slug','layer_cutting_price')->update(['value' => 0]);
                }
            }else{
                if(AppraisePrice::where('appraise_id', $appraiseId)->where('slug','layer_cutting_price')->exists()){
                    AppraisePrice::where('appraise_id', $appraiseId)->where('slug','layer_cutting_price')->update(['value' => 0]);
                }
            }
            $appraise = Appraise::where('id', $appraiseId)->first();
            if (isset($objects['round_total'])) {
                CommonService::setAppraisePrice($appraise, $objects['round_total'], 'round_total');
            }
            if (isset($objects['round_composite'])) {
                CommonService::setAppraisePrice($appraise, $objects['round_composite'], 'round_composite');
            }
            if (isset($objects['round_violation_composite'])) {
                CommonService::setAppraisePrice($appraise, $objects['round_violation_composite'], 'round_violation_composite');
            }
            if (isset($objects['round_violation_facility'])) {
                CommonService::setAppraisePrice($appraise, $objects['round_violation_facility'], 'round_violation_facility');
            }
            $landPrice = 0;

            if (!empty($objects['main_price'])) {
                $mainPrice = $objects['main_price'];
                $islayerCuttingPirce = $mainPrice['islayerCuttingPirce'];
                $layerCuttingPirce = $mainPrice['layerCuttingPirce'];
                AppraiseAppraisalMethods::query()->updateOrCreate([
                    'appraise_id' => $appraiseId,
                    'slug' => 'layer_cutting_procedure'
                ], [
                    'slug_value' => $islayerCuttingPirce ? 1 : 0
                ]);
                if ($islayerCuttingPirce) {
                    CommonService::setAppraisePrice($appraise, $layerCuttingPirce, 'layer_cutting_price');
                } else {
                    if(AppraisePrice::where('appraise_id', $appraiseId)->where('slug','layer_cutting_price')->exists()){
                        AppraisePrice::where('appraise_id', $appraiseId)->where('slug','layer_cutting_price')->update(['value' => 0]);
                    }
                }
                $price = intval($mainPrice['price']);
                $round = $mainPrice['round'] ? $mainPrice['round'] : 0;
                $slug_round = $mainPrice['slug_round'];
                $slug_price = $mainPrice['slug_price'];
                $area = $mainPrice['area'];
                CommonService::setAppraisePrice($appraise, $price, $slug_price);
                CommonService::setAppraisePrice($appraise, $round, $slug_round);

                $landPrice += CommonService::roundPrice($islayerCuttingPirce ?  $layerCuttingPirce : $price, $round) * $area;
            }
            if (!empty($objects['purpose_price'])) {
                foreach ($objects['purpose_price'] as $purposePrice) {
                    $price = $purposePrice['price'];
                    $round =  $purposePrice['round'];
                    $area =  $purposePrice['area'];
                    $slug_round = $purposePrice['slug_round'];
                    $slug_price = $purposePrice['slug_price'];
                    CommonService::setAppraisePrice($appraise, $price, $slug_price);
                    CommonService::setAppraisePrice($appraise, $round, $slug_round);
                    $landPrice += CommonService::roundPrice($price, $round) * $area;
                }
            }
            if (!empty($objects['violate_price'])) {
                foreach ($objects['violate_price'] as $violatePrice) {
                    $price = $violatePrice['price'];
                    $area = $violatePrice['area'];
                    $round =  $violatePrice['round'];
                    $slug_round = $violatePrice['slug_round'];
                    $slug_price = $violatePrice['slug_price'];
                    CommonService::setAppraisePrice($appraise, $price, $slug_price);
                    CommonService::setAppraisePrice($appraise, $round, $slug_round);
                    $landPrice += CommonService::roundPrice($price, $round) * $area;
                }
            }

            AppraisePrice::query()->updateOrCreate(['appraise_id' => $appraiseId, 'slug' => 'land_asset_price'],['value' =>$landPrice]);
            $this->calculateTotalPrice($appraiseId);
        }
        $this->updateAppraiseStep($appraiseId, 7);
        $price = $this->getPriceById($id);
        $price['comparison_factor'] =$this->getComparisonFactorList($id);
        $data = Appraise::where('id', $appraiseId)->first();

        # cập nhật bảng tổng hợp thông tin
        # activity-log
        $this->CreateActivityLog($data, $data, 'update-data', 'cập nhật dữ liệu bảng tổng hợp thông tin');
        $this->processAfterSave($appraiseId);
        return $price;
    }

    public function updateComparisonFactor_V2_ver1($objects , int $id)
    {
        $appraiseId = $id;
        // CommonService::getComparisonAsset($appraiseId);
        // CommonService::getComparisonAppraise($appraiseId);
        $check = $this->beforeSave($appraiseId);
        if(isset($check)){
            return $check;
        }
        $user = CommonService::getUser();

        if (isset($objects['comparison_factor'])) {
            $comparisonFactorDatas = isset($objects['other_comparison']) ? array_merge($objects['comparison_factor'], $objects['other_comparison']) : $objects['comparison_factor'];
            foreach ($comparisonFactorDatas as $comparisonFactorData) {
                $comparisonFactorData = array_map(function($v){
                    return (is_null($v)) ? "" : $v;
                },$comparisonFactorData);

                if(!isset($appraiseId)&&(isset($comparisonFactorData['appraise_id'])))
                    $appraiseId = $comparisonFactorData['appraise_id'];

                if (!isset($comparisonFactorData['adjust_percent'])) {
                    $comparisonFactorData['adjust_percent'] = 0;
                }
                if ($comparisonFactorData['adjust_percent']  > 0) {
                    $comparisonFactorData['description'] = CompareMaterData::COMPARISONS_DESCRIPTION['kem_thuan_loi'];
                } else if ($comparisonFactorData['adjust_percent']  < 0) {
                    $comparisonFactorData['description'] = CompareMaterData::COMPARISONS_DESCRIPTION['thuan_loi'];
                }
                else {
                    $comparisonFactorData['description'] = CompareMaterData::COMPARISONS_DESCRIPTION['tuong_dong'];
                }

                if(!$comparisonFactorData['status']&&($comparisonFactorData['type']=='yeu_to_khac')) {
                    $comparisonFactorData['status'] = 1;
                }

                $comparisonFactor = new AppraiseComparisonFactor($comparisonFactorData);
                if(isset($comparisonFactorData['id'])) {
                    $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                        ->updateOrInsert(['id' => $comparisonFactorData['id']], $comparisonFactor->attributesToArray());
                } else {
                    $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                        ->insert($comparisonFactor->attributesToArray());
                }

            }
        }

        if (isset($objects['delete_other_comparison'])) {
            foreach ($objects['delete_other_comparison'] as $comparisonFactorData) {
                if(isset($comparisonFactorData['id'])) {
                    AppraiseComparisonFactor::where('id', $comparisonFactorData['id'])->forceDelete();
                }
            }
        }

        if (isset($objects['asset_unit_price'])) {
            foreach ($objects['asset_unit_price'] as $item) {
                unset($item['land_type_data']);
                $item['created_by'] = $user->id;

                $appraiseUnitPrice = new AppraiseUnitPrice($item);
                if(isset($item['id'])) {
                    $appraiseUnitPriceId = QueryBuilder::for($appraiseUnitPrice)
                        ->updateOrInsert(['id' => $item['id']], $appraiseUnitPrice->attributesToArray());
                } else {
                    $appraiseUnitPriceId = QueryBuilder::for($appraiseUnitPrice)
                        ->insert($appraiseUnitPrice->attributesToArray());
                }
            }
        }

        if (isset($objects['asset_unit_area'])) {
            foreach ($objects['asset_unit_area'] as $item) {
                unset($item['land_type_data']);
                $item['created_by'] = $user->id;

                $appraiseUnitArea = new AppraiseUnitArea($item);
                if(isset($item['id'])) {
                    $appraiseUnitAreaId = QueryBuilder::for($appraiseUnitArea)
                        ->updateOrInsert(['id' => $item['id']], $appraiseUnitArea->attributesToArray());
                } else {
                    $appraiseUnitAreaId = QueryBuilder::for($appraiseUnitArea)
                        ->insert($appraiseUnitArea->attributesToArray());
                }
            }
        }

        if(isset($appraiseId)) {
            $appraise = $this->findById($appraiseId);

            if (isset($objects['remaining_price'])&&!empty($objects['remaining_price'])&&isset($appraise->composite_land_remaning_slug)&&$appraise->composite_land_remaning_slug=='theo-phuong-phap-doc-lap') {
                $remainingPrice = $objects['remaining_price'];
                if(isset($remainingPrice['remaining_commerce_price'])&&isset($remainingPrice['land_type'])) {
                    CommonService::setAppraisePrice($appraise, $remainingPrice['remaining_commerce_price'], 'land_asset_purpose_'.$remainingPrice['land_type'].'_price');
                }
            }
            $existAssetGeneralIds = [];
            if (isset($objects['appraise_adapter'])&&!empty($objects['appraise_adapter'])) {
                foreach($objects['appraise_adapter'] as $item) {
                    $appraiseAdapter = AppraiseAdapter::where('appraise_id', $item['appraise_id'])
                        ->where('asset_general_id', $item['asset_general_id'])->first();
                    if(isset($appraiseAdapter)) {
                        AppraiseAdapter::where('id', $appraiseAdapter->id)->update([
                            'percent' => $item['percent'],
                            'change_purpose_price' => $item['change_purpose_price'],
                            'change_violate_price' => isset($item['change_violate_price']) ? $item['change_violate_price'] : 0,
                        ]);
                        $existAssetGeneralIds[] = $appraiseAdapter->id;
                    } else {
                        $appraiseAdapterId = AppraiseAdapter::insert([
                            'appraise_id' => $item['appraise_id'],
                            'asset_general_id' => $item['asset_general_id'],
                            'percent' => $item['percent'],
                            'change_purpose_price' => $item['change_purpose_price'],
                            'change_violate_price' => isset($item['change_violate_price']) ? $item['change_violate_price'] : 0,
                        ]);
                        $existAssetGeneralIds[] = $appraiseAdapterId;
                    }
                }
            }

            if (isset($objects['layer_cutting_procedure'])) {
                $items = AppraiseAppraisalMethods::where('appraise_id', $appraiseId)->where('slug', 'layer_cutting_procedure')->get();
                if(count($items)==1) {
                    AppraiseAppraisalMethods::where('appraise_id', $appraiseId)->where('slug', 'layer_cutting_procedure')->update([
                        'appraise_id' => $appraiseId,
                        'slug' => "layer_cutting_procedure",
                        'slug_value' => $objects['layer_cutting_procedure'],
                    ]);
                } else {
                    if(count($items)>1) {
                        AppraiseAppraisalMethods::where('appraise_id', $appraiseId)->where('slug', 'layer_cutting_procedure')->forceDelete();
                    }
                    AppraiseAppraisalMethods::create([
                        'appraise_id' => $appraiseId,
                        'slug' => "layer_cutting_procedure",
                        'slug_value' => $objects['layer_cutting_procedure'],
                    ]);
                }
                if($objects['layer_cutting_procedure']) {
                    if (isset($objects['layer_cutting_price'])&&!empty($objects['layer_cutting_price'])) {
                        CommonService::setAppraisePrice($appraise, $objects['layer_cutting_price'], 'layer_cutting_price');
                    }
                }else
                {
                    AppraisePrice::where('appraise_id', $appraiseId)->where('slug','layer_cutting_price')->update(['value' => 0]);
                }
            }else{
                if(AppraisePrice::where('appraise_id', $appraiseId)->where('slug','layer_cutting_price')->exists()){
                    AppraisePrice::where('appraise_id', $appraiseId)->where('slug','layer_cutting_price')->update(['value' => 0]);
                }
            }
            $appraise = Appraise::where('id', $appraiseId)->first();
            if (isset($objects['round_total'])) {
                CommonService::setAppraisePrice($appraise, $objects['round_total'], 'round_total');
            }
            if (isset($objects['round_composite'])) {
                CommonService::setAppraisePrice($appraise, $objects['round_composite'], 'round_composite');
            }
            if (isset($objects['round_violation_composite'])) {
                CommonService::setAppraisePrice($appraise, $objects['round_violation_composite'], 'round_violation_composite');
            }
            if (isset($objects['round_violation_facility'])) {
                CommonService::setAppraisePrice($appraise, $objects['round_violation_facility'], 'round_violation_facility');
            }
            $landPrice = 0;

            if (!empty($objects['main_price'])) {
                $mainPrice = $objects['main_price'];
                $islayerCuttingPirce = $mainPrice['islayerCuttingPirce'];
                $layerCuttingPirce = $mainPrice['layerCuttingPirce'];
                AppraiseAppraisalMethods::query()->updateOrCreate([
                    'appraise_id' => $appraiseId,
                    'slug' => 'layer_cutting_procedure'
                ], [
                    'slug_value' => $islayerCuttingPirce ? 1 : 0
                ]);
                if ($islayerCuttingPirce) {
                    CommonService::setAppraisePrice($appraise, $layerCuttingPirce, 'layer_cutting_price');
                } else {
                    if(AppraisePrice::where('appraise_id', $appraiseId)->where('slug','layer_cutting_price')->exists()){
                        AppraisePrice::where('appraise_id', $appraiseId)->where('slug','layer_cutting_price')->update(['value' => 0]);
                    }
                }
                $price = intval($mainPrice['price']);
                $round = $mainPrice['round'] ? $mainPrice['round'] : 0;
                $slug_round = $mainPrice['slug_round'];
                $slug_price = $mainPrice['slug_price'];
                $area = $mainPrice['area'];
                CommonService::setAppraisePrice($appraise, $price, $slug_price);
                CommonService::setAppraisePrice($appraise, $round, $slug_round);

                $landPrice += CommonService::roundPrice($islayerCuttingPirce ?  $layerCuttingPirce : $price, $round) * $area;
            }
            if (!empty($objects['purpose_price'])) {
                foreach ($objects['purpose_price'] as $purposePrice) {
                    $price = $purposePrice['price'];
                    $round =  $purposePrice['round'];
                    $area =  $purposePrice['area'];
                    $slug_round = $purposePrice['slug_round'];
                    $slug_price = $purposePrice['slug_price'];
                    CommonService::setAppraisePrice($appraise, $price, $slug_price);
                    CommonService::setAppraisePrice($appraise, $round, $slug_round);
                    $landPrice += CommonService::roundPrice($price, $round) * $area;
                }
            }
            if (!empty($objects['violate_price'])) {
                foreach ($objects['violate_price'] as $violatePrice) {
                    $price = $violatePrice['price'];
                    $area = $violatePrice['area'];
                    $round =  $violatePrice['round'];
                    $slug_round = $violatePrice['slug_round'];
                    $slug_price = $violatePrice['slug_price'];
                    CommonService::setAppraisePrice($appraise, $price, $slug_price);
                    CommonService::setAppraisePrice($appraise, $round, $slug_round);
                    $landPrice += CommonService::roundPrice($price, $round) * $area;
                }
            }

            AppraisePrice::query()->updateOrCreate(['appraise_id' => $appraiseId, 'slug' => 'land_asset_price'],['value' =>$landPrice]);
            $this->calculateTotalPrice($appraiseId);
        }
        $this->updateAppraiseStepVer1($appraiseId, 7);
        $price = $this->getPriceById($id);
        $price['comparison_factor'] =$this->getComparisonFactorList($id);
        $data = Appraise::where('id', $appraiseId)->first();

        # cập nhật bảng tổng hợp thông tin
        # activity-log
        // $this->CreateActivityLog($data, $data, 'update-data', 'cập nhật dữ liệu bảng tổng hợp thông tin');
        // $this->processAfterSave($appraiseId);
        return $price;
    }
    private function getComparisonFactorList(int $appraiseId)
    {
       $result = AppraiseComparisonFactor::where('appraise_id',$appraiseId)->orderByDesc('asset_general_id')->orderBy('position')->get();

       return $result;
    }

    public function updateTangibleComparisonFactor_V2($objects , int $appraiseId)
    {
        $result = [];
        $check = $this->beforeSave($appraiseId);
        if(isset($check)){
            return $check;
        }
        DB::transaction(function () use($objects,$appraiseId) {
            if(isset($objects['xac_dinh_clcl'])) {
                $data = $objects['xac_dinh_clcl'];
                $require = ['appraise_id' => $appraiseId, 'slug' => $data['type']];
                $insert = array_merge($require,['slug_value' => $data['slug'], 'description' => $data['description']]);
                AppraiseAppraisalMethods::query()->updateOrCreate($require, $insert);
            } else {
                return [ 'message' => 'Vui lòng chọn phương pháp xác định chất lượng còn lại', 'exception' => ''];
            }
            if(isset($objects['xac_dinh_dgxd'])) {
                $data = $objects['xac_dinh_dgxd'];
                $require = ['appraise_id' => $appraiseId, 'slug' => $data['type']];
                $insert = array_merge($require,['slug_value' => $data['slug'], 'description' => $data['description']]);
                AppraiseAppraisalMethods::query()->updateOrCreate($require, $insert);
            }else {
                return [ 'message' => 'Vui lòng chọn loại đơn giá xây dựng', 'exception' => ''];
            }
            if(isset($objects['tangible_assets'])){
                foreach($objects['tangible_assets'] as $object){
                    if(isset($object['total_desicion_average'])){
                        AppraiseTangibleAsset::where('id',$object['id'])->update(['total_desicion_average' => $object['total_desicion_average']]);
                    }
                    if(isset($object['comparison_tangible_factor'])){
                        $comparisonFactorData = $object['comparison_tangible_factor'];
                        $comparisonFactorId = $comparisonFactorData['id'];
                        AppraiseTangibleComparisonFactor::whereId($comparisonFactorId)->update([
                            'p1' => $comparisonFactorData['p1'],
                            'p2' => $comparisonFactorData['p2'],
                            'p3' => $comparisonFactorData['p3'],
                            'd4' => $comparisonFactorData['d4'],
                            'p5' => $comparisonFactorData['p5'],
                            'h1' => $comparisonFactorData['h1'],
                            'h2' => $comparisonFactorData['h2'],
                            'h3' => $comparisonFactorData['h3'],
                            'h4' => $comparisonFactorData['h4'],
                            'h5' => $comparisonFactorData['h5'],
                        ]);

                    }
                    if(isset($object['construction_company'])){
                        foreach($object['construction_company'] as $item){
                            if(isset($item['unit_price_m2'])) {
                                ConstructionCompany::whereId($item['id'])->update([
                                    'unit_price_m2' => $item['unit_price_m2']
                                ]);
                            }
                        }
                    }
                }
                $appraise = $this->model->query()->where('id', $appraiseId)->first();
                $tangibleTotal = CommonService::getTangibleAssetPriceTotal($appraise);
                AppraisePrice::query()->updateOrCreate(['appraise_id' => $appraiseId, 'slug' => 'tangible_asset_price'], ['value' => $tangibleTotal]);
                $this->calculateTotalPrice($appraiseId);
                $this->updateAppraiseStep($appraiseId,7);
                $this->processAfterSave($appraiseId);
                # cập nhât bảng điều chỉnh công trình xây dựng
                # activity-log
                $this->CreateActivityLog($appraise, $appraise, 'update-data', 'cập nhật dữ liệu bảng điều chỉnh công trình xây dựng');
            }

        });

        $result = $this->getPriceById($appraiseId);
        return $result;
    }

    public function findPagingRealEstates(): LengthAwarePaginator
    {
        $perPage = (int)request()->get('limit');
        $page = (int)request()->get('page');
        $search = request()->get('search');
        $betweenTotal = ValueDefault::TOTAL_PRICE_PERCENT;
        $user = CommonService::getUser();
        $result = RealEstate::query()->with(['createdBy','assetType']);
        // $result = $result->where('asset_type_id', 39);
        $role = $user->roles->last();
        if(($role->name == 'USER')){
            $result= $result ->whereHas('createdBy', function ($has) use ($user) {
                $has->where('id', $user->id);
            });
        }
        if (isset($search)){
            $filterSubstr = substr($search,0,1);
            $filterData = substr($search,1);
            switch($filterSubstr) {
                case '!':
                    if(floatval($filterData)>=0){
                        $result=$result->where('total_area', floatval($filterData));
                    }
                    break;
                case '@':
                    $result=$result->where(function ($q) use ($filterData) {
                        $q = $q->whereHas('createdBy',function($has) use($filterData){
                            $has->where('name', 'ILIKE', '%'. $filterData . '%');
                        });
                    });
                    break;
                case '&':
                    $data = explode('/',$filterData);
                    $doc_no = $data[0];
                    $land_no = isset($data[1]) ? $data[1] : -1;
                    if(intval($doc_no)>=0 ){
                        // return ['message' => 'Sau "&" phải là "số tờ/số thửa". Vui lòng nhập đúng định dạng"', 'exception' => ''];
                        $result=$result->where(function ($q) use ($doc_no,$land_no) {
                            $q = $q->whereHas('appraises.appraiseLaw.landDetails',function($has) use($doc_no,$land_no){
                                $has->where('doc_no', '=', $doc_no );
                                if(intval($land_no) >=0)
                                    $has=$has->Where('land_no','=',$land_no);
                            });
                        });
                    }
                    break;
                case '$':
                    if(floatval($filterData)>=0){
                        $fromValue = floatval($filterData) - floatval($filterData) * $betweenTotal;
                        $toValue = floatval($filterData) + floatval($filterData) * $betweenTotal;
                        $result=$result->whereBetween('total_price', [$fromValue, $toValue]);
                    }
                    break;
                default:
                    $result=$result->where(function ($q) use ($search) {
                        $q = $q->where('id', 'like',strval($search) );
                        $q = $q->orwhere('appraise_asset', 'ILIKE', '%' . $search . '%');
                        $q = $q->orwhereHas('assetType',function($has) use($search){
                            $has->where('description', 'ILIKE', '%'. $search . '%');
                        });
                        $q = $q->orwhereHas('createdBy',function($has) use($search){
                            $has->where('name', 'ILIKE', '%'. $search . '%');
                        });
                    });
            }
        }
        $result = $result->orderByDesc('updated_at');
        $result = $result->forPage($page, $perPage)
            ->paginate($perPage);
        return $result;
    }
    public function findPaging_v2()//: LengthAwarePaginator
    {
        $user = CommonService::getUser();
        $perPage = (int)request()->get('limit');
        $page = (int)request()->get('page');
        $sortField = request()->get('sortField');
        $sortOrder = request()->get('sortOrder');
        $query = request()->get('query');
        $sortField = request()->get('sortField');
        $sortOrder = request()->get('sortOrder');
        $filter = request()->get('search');
        $status = request()->get('status');

        if (!empty($query)) {
            $query = json_decode($query);
        } else {
            $query = [];
        }

        $select = [
            'appraises.id',
            'appraises.created_at',
            'appraises.status',
            'appraises.updated_at',
            'appraises.asset_type_id',
            'appraises.appraise_asset',
            'appraises.created_by',
            DB::raw("case when appraise_properties.front_side = 1 then true else false end as is_check_frontside"),
            'appraises.updated_at',
            'tbTotal.total_price',
            'appraise_properties.appraise_land_sum_area',
            'totalTangible.total_construction_area',
        ];
        $with = [
            'assetType:id,description',
            'createdBy:id,name',
            'appraiseLaw:id,appraise_id',
            'appraiseLaw.landDetails:id,appraise_law_id,doc_no,land_no',
        ];
        // \DB::enableQueryLog();
        $betweenTotal = ValueDefault::TOTAL_PRICE_PERCENT;
        $tbName = '"tbTotal"';

        $result = QueryBuilder::for(appraise::class)
                ->with($with)
                ->leftjoin(DB::raw("
                        (select t0.id,
                            coalesce(case
                                when  max(t2.value) > 0
                                then ceil(max(t1.value) / power(10, max(t2.value))) * power(10, max(t2.value))
                                when   max(t2.value) < 0
                                    then floor( max(t1.value) * abs(power(10, max(t2.value)))  ) / abs(power(10, max(t2.value)))
                                else
                                    max(t1.value)
                            end, 0) as total_price
                        from appraises t0
                            left join appraise_prices t1  on t0.id = t1.appraise_id and t1.slug = 'total_asset_price'
                            left join appraise_prices t2 on t1.appraise_id = t2.appraise_id and t2.slug = 'round_appraise_total'
                        group by t0.id
                        ) as " .$tbName ), function($join){
                            $join->on('appraises.id','=','tbTotal.id');
                        })
                ->leftjoin('appraise_properties',function ($join){
                        $join->on( 'appraises.id','=', 'appraise_properties.appraise_id')
                            ->whereNull('appraise_properties.deleted_at')
                            ->limit(1);
                })
                ->leftjoin(DB::raw('(select appraise_id , sum(total_construction_area) as total_construction_area
                                        from appraise_tangible_assets
                                        where deleted_at is null
                                        group by appraise_id) as "totalTangible"') ,function($join){
                                    $join->on('appraises.id','=','totalTangible.appraise_id')
                                    ->select(['appraise_id','total_construction_area']);
                             })
                ->select($select)->distinct();
        $role = $user->roles->last();
        if(($role->name == 'USER')){
            $result= $result ->whereHas('createdBy', function ($has) use ( $user) {
                $has->where('id', $user->id);
            });
        }

        if(isset($filter))
        {
            $filterSubstr = substr($filter,0,1);
            $filterData = substr($filter,1);
            switch($filterSubstr) {
                case '!':
                    if(floatval($filterData)>=0){
                        // return ['message' => 'Sau "!" phải là số để tìm kiếm theo tổng diện tích đất', 'exception' => ''];
                        $result=$result->where(function ($q) use ($filterData) {
                            $q->where('appraise_land_sum_area' , '=' , floatval($filterData));
                            if(floatval($filterData) ==0){
                                $q = $q->orwhereNull('appraise_land_sum_area');
                            }
                        });
                    }
                    break;
                case '@':
                    $result=$result->where(function ($q) use ($filterData) {
                        $q = $q->whereHas('createdBy',function($has) use($filterData){
                            $has->where('name', 'ILIKE', '%'. $filterData . '%');
                        });
                    });
                    break;
                case '&':
                    $data = explode('/',$filterData);
                    $doc_no = $data[0];
                    $land_no = isset($data[1]) ? $data[1] : -1;
                    if(intval($doc_no)>=0 ){
                        // return ['message' => 'Sau "&" phải là "số tờ/số thửa". Vui lòng nhập đúng định dạng"', 'exception' => ''];
                        $result=$result->where(function ($q) use ($doc_no,$land_no) {
                            $q = $q->whereHas('appraiseLaw.landDetails',function($has) use($doc_no,$land_no){
                                $has->where('doc_no', '=', $doc_no );
                                if(intval($land_no) >=0)
                                    $has=$has->Where('land_no','=',$land_no);
                            });
                        });
                    }

                    break;
                case '$':
                    if(floatval($filterData)>=0){
                        // return ['message' => 'Sau "$" phải là số để tìm kiếm theo tổng giá trị.', 'exception' => ''];
                        $fromValue = floatval($filterData) - floatval($filterData) * $betweenTotal;
                        $toValue = floatval($filterData) + floatval($filterData) * $betweenTotal;
                        // dd($fromValue .'-'. $toValue);
                        $result=$result->where(function ($q) use ($fromValue,$toValue) {
                            $q->whereBetween('tbTotal.total_price' , [$fromValue, $toValue]);
                        });
                    }

                    break;
                default:
                    $result=$result->where(function ($q) use ($filter) {
                        $q = $q->where('appraises.id', 'like',strval($filter) );
                        $q = $q->orwhere('appraise_asset', 'ILIKE', '%' . $filter . '%');
                        $q = $q->orwhereHas('assetType',function($has) use($filter){
                            $has->where('description', 'ILIKE', '%'. $filter . '%');
                        });
                        $q = $q->orwhereHas('createdBy',function($has) use($filter){
                            $has->where('name', 'ILIKE', '%'. $filter . '%');
                        });
                    });
            }


        }

        if(isset($status))
        {
            $result=$result->whereIn('status',$status);
        }

        if(isset($sortField) && isset($sortOrder)){
            if($sortField=='asset_type.description')
                if($sortOrder=='descend')
                    $result=  $result->orderBy('asset_type_id', 'DESC');
                else
                    $result=  $result->orderBy('asset_type_id', 'ASC');
            if($sortField=='is_check_frontside')
                if($sortOrder=='descend')
                    $result=  $result->orderBy('is_check_frontside', 'DESC');
                else
                    $result=  $result->orderBy('is_check_frontside', 'ASC');
            if($sortField=='appraise_asset')
                if($sortOrder=='descend')
                    $result=  $result->orderBy('appraise_asset', 'DESC');
                else
                    $result=  $result->orderBy('appraise_asset', 'ASC');
            if($sortField=='status')
                if($sortOrder=='descend')
                    $result=  $result->orderBy('status', 'DESC');
                else
                    $result=  $result->orderBy('status', 'ASC');
            if($sortField=='created_at')
                if($sortOrder=='descend')
                    $result=  $result->orderBy('created_at', 'DESC');
                else
                    $result=  $result->orderBy('created_at', 'ASC');
            if($sortField=='created_by.name')
                if($sortOrder=='descend')
                    $result=  $result->orderBy('created_by', 'DESC');
                else
                    $result=  $result->orderBy('created_by', 'ASC');
            if($sortField=='total_price')
                if($sortOrder=='descend')
                    $result=  $result->orderBy('appraise_prices.value', 'DESC');
                else
                    $result=  $result->orderBy('appraise_prices.value', 'ASC');
            if($sortField=='appraise_land_sum_area')
                if($sortOrder=='descend')
                    $result=  $result->orderBy('appraise_properties.appraise_land_sum_area', 'DESC');
                else
                    $result=  $result->orderBy('appraise_properties.appraise_land_sum_area', 'ASC');
            if($sortField=='total_construction_area')
                if($sortOrder=='descend')
                    $result=  $result->orderBy('appraise_tangible_assets.total_construction_area', 'DESC');
                else
                    $result=  $result->orderBy('appraise_tangible_assets.total_construction_area', 'ASC');

        }else
            $result = $result->orderby('updated_at','desc');

        $result = $result
        ->forPage($page, $perPage)
        ->paginate($perPage);

        // foreach($result as $item)
        // {
        //     $item->append('status_text');
        // }

        // dd(\DB::getQueryLog());

        return $result;
    }

    public function updateConstructionCompany(array $object , int $appraiseId)
    {
        DB::beginTransaction();
        try{
            // dd($object);
            $check = $this->beforeSave($appraiseId);
            if(isset($check)){
                return $check;
            }

            $update = $object['contruction_company_update'];
            $default = $object['contruction_company_default'];

            $deleteList = array_diff($default,$update);
            $addNewList = array_diff($update,$default);
            // if(count($deleteList) ==0 && count($addNewList) ==0){
            //     return 'Dữ liệu không thay đổi';
            // }

            if(count($deleteList)>0){
                foreach($deleteList as $oldId){
                    if($oldId == 0)
                        ConstructionCompany::where('appraise_id',$appraiseId)->whereNull('construction_company_id')->delete() ;

                    ConstructionCompany::where('appraise_id',$appraiseId)->where('construction_company_id',$oldId)->delete() ;
                }
            }

            if(count($addNewList)>0){
                $select = ['id','name','address','phone_number','manager_name','unit_price_m2','is_defaults'];
                $tangibleAssets = AppraiseTangibleAsset::where('appraise_id',$appraiseId)->get('id');
                if(! isset($tangibleAssets)){
                    DB::rollBack();
                    $data = ['message' => ErrorMessage::APPRAISE_NOTEXISTS_TANGIBLE, 'exception' => ''];
                    return $data;
                }
                foreach($tangibleAssets as $tangible){
                    foreach($addNewList as $newId){
                        $company = AppraisalConstructionCompany::where('id',$newId)->select($select)->first();
                        $construction['appraise_id'] = $appraiseId;
                        $construction['construction_company_id'] = $company['id'];
                        $construction['name'] = $company['name'];
                        $construction['manager_name'] = $company['manager_name'];
                        $construction['phone_number'] = $company['phone_number'];
                        $construction['address'] = $company['address'];
                        $construction['unit_price_m2'] = $company['unit_price_m2'];
                        $construction['is_defaults'] = $company['is_defaults'];
                        $construction['tangible_asset_id'] = $tangible->id;
                        $constructionCompanyData = new ConstructionCompany($construction);
                         QueryBuilder::for($constructionCompanyData)
                            ->insert($constructionCompanyData->attributesToArray());

                    }
                }

            }
            $this->updateAppraiseStep($appraiseId, 7);
            $data = Appraise::where('id', $appraiseId)->first();

            # cập nhật đơn vị xây dựng
            #  activity-log
            $this->CreateActivityLog($data, $data, 'update-data', 'cập nhật dữ liệu bảng đơn vị xây dựng');
            $this->processAfterSave($appraiseId);
            DB::commit();
            $result = $this->getPriceById($appraiseId);
            $tangibles = AppraiseTangibleAsset::with(['constructionCompany' ])->where('appraise_id', $appraiseId)->get('id');
            $result['tangible_assets'] = $tangibles;
            return $result;
        }catch(Exception $ex)
        {
            DB::rollBack();
            Log::error($ex);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' =>  $ex->getMessage()];
            return $data;
        }
    }

    private function beforeSave(int $appraiseId)
    {
        if(Appraise::where('id',$appraiseId)->exists()){
            $checkCreatedBy = $this->checkUser($appraiseId,'SAVE');
            if(isset($checkCreatedBy)){
                return $checkCreatedBy;
            }
            $result = null;
            $status = [4 , 5];
            $data = Appraise::where('id',$appraiseId)->whereIn('status' , $status)->get(['id','status'])->first();
            if(isset($data)){
                $result = ['message' => ErrorMessage::APPRAISE_CHECK_STATUS_FOR_UPDATE . $data->status_text , 'exception' => ''];
            }
        }else{
            $result = ['message' => ErrorMessage::APPRAISE_NOTEXISTS . $appraiseId, 'exception' => ''];
        }
        return $result;
    }

    public function updateRoundAppraiseTotal($appraiseId, $objects)
    {
        $result =  [];

        $check = $this->beforeSave($appraiseId);
        if(isset($check)){
            return $check;
        }
        $appraise = Appraise::where('id', $appraiseId)->first();
        CommonService::setAppraisePrice($appraise, $objects['round_appraise_total'], 'round_appraise_total');

        $this->updateAppraiseStep($appraiseId,7);

        $data = Appraise::where('id', $appraiseId)->first();

        # cập nhật tổng hợp kết quả
        # activity-log
        $this->CreateActivityLog($data, $data, 'update-data', 'cập nhật dữ liệu bảng tổng hợp kết quả');

        $result = $this->getPriceById($appraiseId);
        $this->processAfterSave($appraiseId);
        return $result;
    }

    private function getPriceById(int $appraiseId){
        $data = Appraise::where('id',$appraiseId)->with('assetPrice')->get(['id','step'])->first();
        if(isset($data)){
            $data->append('price_land_asset');
            $data->append('price_tangible_asset');
            $data->append('price_other_asset');
            $data->append('price_total_asset');
            $data->append('round_appraise_total');
        }
        return $data;
    }

    private function checkUser(int $appraiseId, string $type = 'VIEW' ){
        $result = null;
        $user = CommonService::getUser();
        if($user->hasRole(['ROOT_ADMIN', 'SUPER_ADMIN', 'SUB_ADMIN'])) {
            return $result;
        }
        $data = Appraise::where('id',$appraiseId)->where('created_by',$user->id)->first();
        if(! isset($data)) {
            if($type == 'VIEW'){
                if($user->roles->last()->name =='USER'){
                    $result = ['message' => ErrorMessage::APPRAISE_CHECK_VIEW . $appraiseId . '.', 'exception' => ''];
                }
            } else {
                $result = ['message' => ErrorMessage::APPRAISE_CHECK_UPDATE . $appraiseId . '.', 'exception' => ''];
            }
        }
        return $result;
    }

    public function exportCertificateAssets()
    {
        $status = request()->get('status');
        $fromDate = request()->get('fromDate');
        $toDate = request()->get('toDate');
        $users = request()->get('created_by');
        if(isset($fromDate) && isset($toDate)){
            $fromDate =  \Carbon\Carbon::createFromFormat('d/m/Y', $fromDate);
            $toDate =  \Carbon\Carbon::createFromFormat('d/m/Y', $toDate);
            $diff = $toDate->diff($fromDate);
            if($diff->days >93){
                return ['message' => 'Chỉ được tìm kiếm tối đa 3 tháng.', 'exception' => ''];
            }
            // dd($diff->days);
        }else{
            return ['message' => 'Vui lòng nhập khoảng thời gian cần tìm', 'exception' => ''];
        }
        if (!empty($status)) {
            $status = explode(',', $status);
        }
        // dd(explode(',',request()->get('created_by')));
        if (!empty($users)) {
            $users = explode(',', $users);
        }
        // dd($users);
        $select = [
            'appraises.id',
            'appraises.created_at',
            'appraises.status',
            'appraises.updated_at',
            'appraises.asset_type_id',
            'appraises.appraise_asset',
            'appraises.created_by',
            'appraises.updated_at',
            DB::raw('coalesce(cast(case
                        when  round.value > 0
                           then ceil(appraise_prices.value / power(10,round.value)) * power(10,round.value)
                        when  round.value < 0
                            then floor(appraise_prices.value * abs(power(10,round.value))  ) / abs(power(10,round.value))
                        else
                             appraise_prices.value
                    end as bigint),0) as total_price
                '),
            DB::raw("case appraises.status
                        when 1 then 'Mới'
                        when 2 then 'Đang Thực Hiện'
                        when 3 then 'Đang Duyệt'
                        when 4 then 'Hoàn Thành'
                        else 'Hủy' end as status_text"),
            // DB::raw("case appraises.is_check_frontside
            //             when false then 'Hẻm'
            //            else 'Mặt tiền' end as side"),
            // 'appraise_properties.appraise_land_sum_area',
            'totalTangible.total_construction_area',
        ];
        $with = [
            'assetType:id,description',
            'createdBy:id,name',
            'properties:id,appraise_id,appraise_land_sum_area,front_side',
            // 'tangibleAssets:id,appraise_id,total_construction_area'
        ];

        $result = Appraise::with($with)
                    // ->join('users', function ($join){
                    //     $join->on('appraises.created_by', '=', 'users.id');
                    // })
                    ->leftjoin('appraise_prices',function ($join){
                        $join->on( 'appraises.id','=','appraise_prices.appraise_id')
                            ->whereNull('appraise_prices.deleted_at')
                            ->where('appraise_prices.slug','=','total_asset_price')
                            ->limit(1)
                            ->distinct();
                    })
                    ->leftjoin('appraise_prices as round',function ($join){
                        $join->on( 'appraises.id','=','round.appraise_id')
                            ->whereNull('round.deleted_at')
                            ->where('round.slug','=','round_appraise_total')
                            ->limit(1)
                            ->distinct();
                    })
                    // ->leftjoin('appraise_properties',function ($join){
                    //         $join->on( 'appraises.id','=', 'appraise_properties.appraise_id')
                    //             ->whereNull('appraise_properties.deleted_at')
                    //             ->limit(1);
                    // })
                    ->leftjoin(DB::raw('(select appraise_id , sum(total_construction_area) as total_construction_area
                                        from appraise_tangible_assets
                                        where deleted_at is null
                                        group by appraise_id) as "totalTangible"') ,function($join){
                                    $join->on('appraises.id', '=', 'totalTangible.appraise_id')
                                    ->select(['appraise_id','total_construction_area']);
                             })
                    ->select($select);

        if(isset($status)){
            $result=$result->whereIn('appraises.status',$status);
        }
        if(isset($users)){
            $result=$result->whereIn('appraises.created_by',$users);
        }
        if(isset($fromDate) && isset($toDate)){
            $result=$result->whereRaw("to_char(appraises.created_at , 'YYYY-MM-dd') between '".$fromDate->format('Y-m-d')."' and '".$toDate->format('Y-m-d')."'");
        }
        $result = $result->orderBy('id');
        $result = $result->get();
        // $now = Carbon::now()->timezone('Asia/Ho_Chi_Minh');
        // $fileName = 'HSTD';

        // $path =  env('STORAGE_DOCUMENTS') . '/'. 'comparison_brief/' . $now->format('Y') . '/' . $now->format('m') . '/';
        // $result= (new FastExcel($result))->export(storage_path('app/public/'. $path. '/'. $fileName .'.xlsx'));
        return $result;
    }
    #endregion

    public function countAppraiseAsset()
    {
        // $date_from= request()->get('date_from');
        // $date_to= request()->get('date_to');
        // if(! isset($date_from) || ! isset($date_to)){
        //     return ['message' => 'Vui lòng chọn ngày', 'exception' => ''];
        // }
        $result = CertificateAsset::query()
            ->select('province_id', DB::raw('count(*) as total'))
            ->groupBy('province_id')
            ->with('province:id,name')
            ->where('status', 4)
            ->get();
        return $result;
    }

     #region lưu estate
    private function updateRealEstates(int $id){
        $select = [
            'id',
            'appraise_asset',
            'coordinates',
            'created_at',
            'updated_at',
            'asset_type_id',
            'status',
            'created_by',
            'real_estate_id'
        ];
        $with = [
            'assetPrice' => function($query){
                $query->whereIn('slug', ['total_asset_price', 'total_asset_area', 'round_appraise_total'])
                    ->select(['id', 'appraise_id', 'slug', 'value']);
            },
            'properties:id,appraise_id,front_side,appraise_land_sum_area',
        ];
        $data = $this->model::query()->with($with)->where('id', $id)->first($select);
        if (isset($data)){
            $total_price = 0;
            $total_area = 0;
            $round_total = 0;
            $round = $data->assetPrice->where('slug','round_appraise_total')->first();
            if (isset($round))
                $round_total = $round->value;
            $price = $data->assetPrice->where('slug','total_asset_price')->first();
            if (isset($price))
                $total_price = CommonService::roundPrice($price->value,$round_total) ;

            $area = $data->properties->first();
            if (isset($area))
                $total_area = $area->appraise_land_sum_area;
            $frontSide = $data->properties->first();
            $realEstate = [
                'asset_type_id' => $data->asset_type_id,
                'total_price' => $total_price,
                'total_area' => $total_area,
                'round_total' => $round_total,
                'appraise_asset' => $data->appraise_asset,
                'created_by' => $data->created_by,
                'coordinates' => $data->coordinates,
                'front_side' => $frontSide->front_side??null,
                'status' => $data->status,
                'real_estate_id' => $data->real_estate_id,
            ];
            $realEstateData = new RealEstate($realEstate);
            RealEstate::query()->where('id', $id)->update($realEstateData->attributesToArray());
            if (!isset($data->real_estate_id))
                Appraise::query()->where('id', $id)->update(['real_estate_id' => $id]);
        }
    }
    private function createRealEstate(array $object){
        $realEstateRep = new EloquentRealEstateRepository(new RealEstate());
        $data = new RealEstate($object);
        $realCreated = $realEstateRep->store($data->attributesToArray());
        $realId = $realCreated->id;
        return $realId;
    }
    #endregion

    public function updateRealEstateStatus(int $realEstateId, int $status)
    {
        $dataUpdate = [
            'status' => $status,
        ];
        $this->model->query()->where('real_estate_id', $realEstateId)->update($dataUpdate);
        RealEstate::query()->where('id', $realEstateId)->update($dataUpdate);
    }

    public function updateEstimateAssetPrice(int $id) {
        $select = ['id', 'coordinates', 'asset_type_id', 'created_by', 'updated_at'];
        $with = ['assetPrice', 'properties', 'properties.propertyDetail', 'properties.propertyDetail.landTypePurpose',
                'assetType','createdBy',
                'tangibleAssets' , 'tangibleAssets.constructionCompany', 'tangibleAssets.comparisonTangibleFactor',
                'appraiseLaw', 'appraiseLaw.landDetails'
            ];
        $data = $this->model->query()->with($with)->where('id', $id)->first($select);
        // dd($data);
        $propertyDetail = $data->properties[0]->propertyDetail;
        $assetPrice = $data->assetPrice->where('value', '>=', 0);
        $tangibleAssets = $data->tangibleAssets;
        $appraiseLaw = $data->appraiseLaw->first();
        $assetData = [];
        $tangibleData = [];
        $cuttingPrice = $assetPrice->whereNotNull('value')->where('slug', 'layer_cutting_price')->where('value', '>' , 0)->first();
        if (isset($propertyDetail)) {
            $stt = 0;
            foreach ($propertyDetail as $detail) {
                $acronym = $detail->landTypePurpose->acronym ?? '';
                $slug = 'land_asset_purpose_'. $acronym;
                $slugViolate = 'land_asset_purpose_'. $acronym . '_violation';
                $purposePrice = $assetPrice->where('slug', $slug .'_price')->first();
                $violatePrice = $assetPrice->where('slug', $slugViolate . '_price')->first();
                $landTypeDescription = $detail->landTypePurpose->description ?? '';
                if (isset($purposePrice)) {
                    $area =  $detail->main_area ?? 0;
                    if ($area > 0) {
                        $round = $assetPrice->where('slug', $slug . '_round')->first()->value ?? 0;
                        if ($detail->is_transfer_facility && !empty($cuttingPrice)) {
                            $price =  CommonService::roundPrice($cuttingPrice->value ?? 0, $round);
                        } else {
                            $price =  CommonService::roundPrice($purposePrice->value ?? 0, $round);
                        }
                        $assetData[$stt]['description'] = 'Diện tích đất phù hợp quy hoạch';
                        $assetData[$stt]['land_type_description'] = $landTypeDescription;
                        $total = CommonService::roundPrice($price * $area, 0);
                        $assetData[$stt]['area'] = $area;
                        $assetData[$stt]['price'] = $price;
                        $assetData[$stt]['total'] = $total;
                        $stt ++;
                    }
                }
                if (isset($violatePrice)) {
                    $area =  $detail->planning_area ?? 0;
                    if ($area > 0) {
                        // $round = $assetPrice->where('slug', 'round_violation_facility')->first()->value ?? 0;
                        $round = $assetPrice->where('slug', $slugViolate . '_round')->first()->value ?? 0;
                        $assetData[$stt]['description'] = 'Diện tích đất vi phạm quy hoạch';
                        $assetData[$stt]['land_type_description'] = $landTypeDescription;
                        $price =  CommonService::roundPrice($violatePrice->value ?? 0 , $round);
                        $total = CommonService::roundPrice($price * $area, 0);
                        $assetData[$stt]['area'] = $area;
                        $assetData[$stt]['price'] = $price;
                        $assetData[$stt]['total'] = $total;
                        $stt ++;
                    }
                }
            }
        }
        if (isset($tangibleAssets) && ! empty($tangibleAssets)) {
            $clcl = 0;
            $stt = 0;
			$appraisalMethod = AppraiseAppraisalMethods::query()->where('appraise_id', $id)->whereIn('slug', ['XAC_DINH_DON_GIA_XAY_DUNG', 'XAC_DINH_CHAT_LUONG_CON_LAI'])->get();
            $priceMethod = $appraisalMethod->where('slug' , 'XAC_DINH_DON_GIA_XAY_DUNG')->first();
            $priceMethod = $priceMethod->slug_value ?? '';
            $remainMethod = $appraisalMethod->where('slug' , 'XAC_DINH_CHAT_LUONG_CON_LAI')->first();
            $remainMethod = $remainMethod->slug_value ?? '';
            foreach ($tangibleAssets as $tangible) {
                $tangibleName = $tangible->tangible_name;
                $area = $tangible->total_construction_base;
                $price = CommonService::getSelectedTangibleAssetPrice($tangible, $priceMethod);
                $clcl = CommonService::getSelectedRemain($tangible, $remainMethod);
                $total =CommonService::roundPrice($area * $price * $clcl / 100, 0);
                $tangibleData[$stt]['name'] = $tangibleName;
                $tangibleData[$stt]['area'] = $area;
                $tangibleData[$stt]['clcl'] = $clcl;
                $tangibleData[$stt]['price'] = $price;
                $tangibleData[$stt]['total'] = $total;
                $stt ++;
            }
        }
        $doc_num = '';
        if (!empty($appraiseLaw)) {
            if (!empty($appraiseLaw->landDetails)) {
                foreach ($appraiseLaw->landDetails as $detail) {
                    if ($doc_num == '') {
                        $doc_num = 'Thửa đất số '. $detail->land_no . ' tờ bản đồ số '. $detail->doc_no;
                    } else {
                        $doc_num = $doc_num . ', thửa đất số '. $detail->land_no . ' tờ bản đồ số '. $detail->doc_no;
                    }
                }
            }
        }
        $result = $data->toArray();
        usort($assetData, function($a, $b) {
            return strcmp($a["description"], $b["description"]);
        });
        $result = array_merge($result, ['assets' => $assetData], ['tangibles' => $tangibleData]);
        $landTotal = array_sum(array_column($assetData, 'total'));
        $tangibleTotal = array_sum(array_column($tangibleData, 'total'));
        $landArea = array_sum(array_column($assetData, 'area'));
        $tangibleArea = array_sum(array_column($tangibleData, 'area'));
        $result['land_area'] = $landArea;
        $result['land_total'] = $landTotal;
        $result['tangible_area'] = $tangibleArea;
        $result['tangible_total'] = $tangibleTotal;
        $total = $landTotal + $tangibleTotal;
        $result['total'] = $total;
        $slug = 'estimate_asset_price';
        $result['doc_num'] = $doc_num;
        AppraisePrice::query()->updateOrCreate(['appraise_id' => $id, 'slug' => $slug], ['value' => $total]);
        return $result;
    }
    private function processAfterSave($appraiseId) {
        $this->createAppraiseVersion($appraiseId);
    }
    private function createAppraiseVersion($appraiseId) {
        $appraise = $this->model->query()->with('version')->where('id', $appraiseId)->whereHas('version')->first();
        // $appraise = $this->model->query()->with('version')->where('id', $appraiseId)->first();
        $data = [];
        if (!empty($appraise)) {
            if (!empty($appraise->certificate_id)) {
                $max = $appraise->version->max('version');
                $data = [
                    'appraise_id' => $appraiseId,
                    'status' => 1,
                    'version' => $max + 1
                ];
            }
        } else {
            $data = [
                'appraise_id' => $appraiseId,
                'status' => 1,
                'version' => 1
            ];
        }
        if (!empty($data)) {
            AppraiseVersion::query()->create($data);
        }
    }
    private function calculateTotalPrice ($id)
    {
        $whereIn = ['land_asset_price', 'tangible_asset_price', 'other_asset_price'];
        $prices = AppraisePrice::query()->where('appraise_id', $id)->whereIn('slug', $whereIn)->get()->toArray();
        if (!empty($prices)) {
            $total = array_sum(array_column($prices, 'value'));

            AppraisePrice::query()->updateOrCreate(['appraise_id' => $id, 'slug' => 'total_asset_price'], ['value' => $total]);
        }
    }
    public function getAppraiseDetail ($id)
    {
        $select = [
            'id',
            'province_id',
            'district_id',
            'ward_id',
            'street_id',
            'asset_type_id',
            'topographic_id',
            'certificate_id',
            'updated_at'
        ];
        $with = [
            'topographic:id,description',
            'assetType:id,description',
            'assetPrice:id,appraise_id,slug,value',
            'properties:id,appraise_id,front_side_width,insight_width,land_shape_id,appraise_land_sum_area,front_side,main_road_length',
            'properties.landShape:id,description',
            'properties.propertyTurningTime',
            'properties.propertyDetail:id,appraise_property_id,land_type_purpose_id',
            'properties.propertyDetail.landTypePurpose:id,description,acronym',
            'certificate:id,appraiser_perform_id,appraise_date,certificate_num,certificate_date',
            'certificate.appraiserPerform:id,name',
            'pic',
            'appraiseLaw:id,appraise_id',
            'appraiseLaw.landDetails:id,doc_no,land_no',
            'province:id,name',
            'district:id,name',
            'street:id,name',
            'ward:id,name',
            'tangibleAssets:id,building_type_id',
            'tangibleAssets.buildingType:id,description'
        ];
        $result = $this->model->query()->with($with)->where('id', $id)->first($select);
        return $result;
    }

    public function getApartmentDetail ($id)
    {
        $select = [
            'id',
            'province_id',
            'district_id',
            'ward_id',
            'street_id',
            'asset_type_id',
            // 'topographic_id',
            'certificate_id',
            'updated_at'
        ];
        $with = [
            // 'topographic:id,description',
            'assetType:id,description',
            'price:id,apartment_asset_id,slug,value',
            // 'apartmentAssetProperties:id,appraise_id,front_side_width,insight_width,land_shape_id,appraise_land_sum_area,front_side,main_road_length',
            // 'apartmentAssetProperties.landShape:id,description',
            // 'apartmentAssetProperties.propertyTurningTime',
            // 'apartmentAssetProperties.propertyDetail:id,appraise_property_id,land_type_purpose_id',
            // 'apartmentAssetProperties.propertyDetail.landTypePurpose:id,description,acronym',
            'certificate:id,appraiser_perform_id,appraise_date,certificate_num,certificate_date',
            'certificate.appraiserPerform:id,name',
            'pic',
            // 'law:id,appraise_id',
            // 'law.landDetails:id,doc_no,land_no',
            'province:id,name',
            'district:id,name',
            'street:id,name',
            'ward:id,name'
            // 'tangibleAssets:id,building_type_id',
            // 'tangibleAssets.buildingType:id,description'
        ];
        // $result = ApartmentAsset::query()->with($with)->where('id', $id)->first($select);
        $result = ApartmentAsset::query()->where('id', $id)->with($with)->first($select);
        // dd($result);
        return $result;
    }
    private function checkAuthorization ($id)
    {
        $check = null;
        if ($this->model->query()->where('id', $id)->exists()) {
            $user = CommonService::getUser();
            $role = $user->roles->last();
            $result = $this->model->query()->where('id', $id);
            $userId = $user->id;
            if ($role->name == 'USER') {
                $result = $result->where('created_by', $userId);
            }
            $result = $result->first();
            if (empty($result))
                $check = ['message' => 'Bạn không có quyền ở TSTĐ '. $id , 'exception' => '', 'statusCode' => 403];
        } else {
            $check = ['message' => ErrorMessage::APPRAISE_NOTEXISTS . ' ' . $id, 'exception' => '', 'statusCode' => 403];
        }
        return $check;
    }

    public function updateDistance(int $objects, int $id = null)
    {
        return AppraiseComparisonFactor::query()
                ->where('id', $id)
                ->update(['asset_title' => $objects]);
            }
    
    public function updateMucdichchinh(string $objects, int $id = null)
    {
        return AppraiseComparisonFactor::query()
                ->where('id', $id)
                ->update(['asset_title' => $objects]);
            }

    public function updateNoteHienTrang($objects, int $id = null)
    {
        return AppraiseTangibleComparisonFactor::query()
                ->where('id', $id)
                ->update(['note' => $objects]);
            }
}
