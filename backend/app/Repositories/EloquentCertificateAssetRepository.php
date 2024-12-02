<?php

namespace App\Repositories;

use App\Contracts\CertificateAssetRepository;
use App\Enum\CompareMaterData;
use App\Enum\EstimateAssetDefault;

use App\Models\CertificateAsset;
use App\Models\CertificateAssetComparisonFactor;
use App\Models\CertificateAssetCTangibleComparisonFactor;
use App\Models\CertificateAssetConstructionCompany;
use App\Models\CertificateConstructionCompany;

use App\Models\AppraiseDictionary;
use App\Models\CertificateAssetHasAsset;
use App\Models\CertificateAssetLaw;
use App\Models\AppraiseLawDetail;
use App\Models\AppraiseTangibleAsset;
use App\Models\CertificateAssetOtherAsset;
use App\Models\CertificateAssetPic;
use App\Models\CertificateAssetProperty;
use App\Models\CertificateAssetPropertyDetail;
use App\Models\CertificateAssetPropertyTurningTime;
use App\Models\CertificateAssetTangibleAsset;
use App\Models\CertificateAssetVersion;
use App\Models\CertificateAssetUnitPrice;
use App\Models\CertificateAssetUnitArea;
use App\Models\CompareAssetGeneral;
use App\Models\CompareProperty;
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
use App\Repositories\EloquentUserRepository;
use App\Models\User;

use App\Services\CommonService;

class  EloquentCertificateAssetRepository extends EloquentRepository implements CertificateAssetRepository
{
    private string $defaultSort = 'id';

    private string $allowedSorts = 'id';

    /**
     * @return LengthAwarePaginator
     */
    public function findPaging(): LengthAwarePaginator
    {
        $perPage = (int)request()->get('limit');
        $page = (int)request()->get('page');
        $search = request()->get('search');
        if (empty($search)) {
            $search = '';
        }
        return QueryBuilder::for($this->model)
            ->with('province')
            ->with('district')
            ->with('ward')
            ->with('street')
            ->with('properties')
            ->with('properties.propertyDetail.landTypePurpose')
            ->with('tangibleAssets')
            ->with('createdBy')
            ->with('version')
            ->with('assetType')
            ->orderByDesc($this->allowedSorts)
            ->forPage($page, $perPage)
            ->paginate($perPage);
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

    /**
     * @param $id
     * @return Builder|Model|object
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
                //->with('constructionCompany.constructionCompany')
                ->with('constructionCompany')
                ->with('appraiseLaw.law')
                ->with('appraiseLaw.lawDetails')
                ->with('appraiseLaw.lawDetails.landTypePurpose')
                //->with('assetGeneral')
                ->with('appraiseHasAssets')
                ->with('createdBy')
                ->with('comparisonFactor')
                ->with('comparisonTangibleFactor')
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
                ->with('assetUnitPrice')
                ->with('assetUnitPrice.landTypeData')
                ->with('assetUnitPrice.createdBy')
                ->with('assetUnitArea')
                ->with('assetUnitArea.landTypeData')
                ->with('assetUnitArea.createdBy')
                ->first();
        }
        if(isset($result)) {
            $result->append('asset_general');
        }
        $asset = $result;
        $asset->assetGeneral = $asset->asset_general;

        $asset1 = $asset->assetGeneral[0] ?? null;
        $asset2 = $asset->assetGeneral[1] ?? null;
        $asset3 = $asset->assetGeneral[2] ?? null;

        $isExistAsset1 = false;
        $isExistAsset2 = false;
        $isExistAsset3 = false;
        foreach($result->appraiseAdapter as $item) {
            if($item->asset_general_id==$asset1->id) {
                $isExistAsset1 = true;
            }
            if($item->asset_general_id==$asset2->id) {
                $isExistAsset2 = true;
            }
            if($item->asset_general_id==$asset3->id) {
                $isExistAsset3 = true;
            }
        }
        if(!$isExistAsset1) {
            $result->appraiseAdapter[] = [
                'appraise_id' => $asset->id,
                'asset_general_id' => $asset1->id,
                'percent' => intval($asset1->adjust_percent)+100,
                'change_purpose_price' => null,
            ];
        }
        if(!$isExistAsset2) {
            $result->appraiseAdapter[] = [
                'appraise_id' => $asset->id,
                'asset_general_id' => $asset2->id,
                'percent' => intval($asset2->adjust_percent)+100,
                'change_purpose_price' => null,
            ];
        }
        if(!$isExistAsset3) {
            $result->appraiseAdapter[] = [
                'appraise_id' => $asset->id,
                'asset_general_id' => $asset3->id,
                'percent' => intval($asset3->adjust_percent)+100,
                'change_purpose_price' => null,
            ];
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
        $result = $this->model->query()
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
            //->with('constructionCompany.constructionCompany')
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

        foreach($result as $stt=>$asset) {
            $result[$stt]->append('asset_general');
            $result[$stt]->assetGeneral = $result[$stt]->asset_general;
            $asset->assetGeneral = $result[$stt]->asset_general;

            $result[$stt]->append('round_total');
            $result[$stt]->append('round_composite');
            $result[$stt]->append('round_violation_composite');
            $result[$stt]->append('round_violation_facility');
            $result[$stt]->append('round_appraise_total');

            $asset1 = $asset->assetGeneral[0] ?? null;
            $asset2 = $asset->assetGeneral[1] ?? null;
            $asset3 = $asset->assetGeneral[2] ?? null;
            $isExistAsset1 = false;
            $isExistAsset2 = false;
            $isExistAsset3 = false;
            foreach($result[$stt]->appraiseAdapter as $item) {
                if($item->asset_general_id==$asset1->id) {
                    $isExistAsset1 = true;
                }
                if($item->asset_general_id==$asset2->id) {
                    $isExistAsset2 = true;
                }
                if($item->asset_general_id==$asset3->id) {
                    $isExistAsset3 = true;
                }
            }
            if(!$isExistAsset1) {
                $result[$stt]->appraiseAdapter[] = [
                    'appraise_id' => $asset->id,
                    'asset_general_id' => $asset1->id,
                    'percent' => intval($asset1->adjust_percent)+100,
                ];
            }
            if(!$isExistAsset2) {
                $result[$stt]->appraiseAdapter[] = [
                    'appraise_id' => $asset->id,
                    'asset_general_id' => $asset2->id,
                    'percent' => intval($asset2->adjust_percent)+100,
                ];
            }
            if(!$isExistAsset3) {
                $result[$stt]->appraiseAdapter[] = [
                    'appraise_id' => $asset->id,
                    'asset_general_id' => $asset3->id,
                    'percent' => intval($asset3->adjust_percent)+100,
                ];
            }
        }

        return $result;
    }

    /**
     * @param array $objects
     * @return int
     * @throws Throwable
     */
    public function createAppraise(array $objects): int
    {
        return DB::transaction(function () use ($objects) {
            try {
                $appraise = new CertificateAsset($objects);
                $appraiseId = QueryBuilder::for(CertificateAsset::class)
                    ->insertGetId($appraise->attributesToArray());

                $countVersion = CertificateAssetVersion::query()
                    ->where('appraise_id', '=', $appraiseId)
                    ->where('status', '=', $appraise->status)
                    ->count();
                $version['version'] = $appraise->status . '.' . $countVersion;
                $version['status'] = $appraise->status;
                $version['appraise_id'] = $appraiseId;
                CertificateAssetVersion::query()->insert($version);


                if (isset($objects['pic'])) {
                    foreach ($objects['pic'] as $appraisePic) {
                        $appraisePic['appraise_id'] = $appraiseId;
                        $pic = new CertificateAssetPic($appraisePic);
                        QueryBuilder::for($pic)
                            ->insert($pic->attributesToArray());
                    }
                }
                if (isset($objects['properties'])) {
                    foreach ($objects['properties'] as $propertyData) {
                        $propertyData['appraise_id'] = $appraiseId;

                        $property = new CertificateAssetProperty($propertyData);
                        $propertyId = QueryBuilder::for($property)
                            ->insertGetId($property->attributesToArray());
                        if (isset($propertyData['property_detail'])) {
                            foreach ($propertyData['property_detail'] as $propertyDetail) {
                                $propertyDetail['appraise_property_id'] = $propertyId;
                                $detail = new CertificateAssetPropertyDetail($propertyDetail);
                                QueryBuilder::for($detail)
                                    ->insert($detail->attributesToArray());
                            }
                        }

                        if (isset($propertyData['property_turning_time'])) {
                            foreach ($propertyData['property_turning_time'] as $propertyTurningTime) {
                                $propertyTurningTime['appraise_property_id'] = $propertyId;
                                $detail = new CertificateAssetPropertyTurningTime($propertyTurningTime);
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
                        $otherAsset = new CertificateAssetTangibleAsset($otherAssetData);
                        $otherId = QueryBuilder::for($otherAsset)
                            ->insertGetId($otherAsset->attributesToArray());
                    }
                }

                if (isset($objects['appraise_law'])) {
                    foreach ($objects['appraise_law'] as $lawData) {
                        $lawData['appraise_id'] = $appraiseId;
                        $law = new CertificateAssetLaw($lawData);
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
                    }
                }

                if (isset($objects['construction_company'])) {
                    foreach ($objects['construction_company'] as $constructionCompanyData) {
                        $constructionCompanyData['appraise_id'] = $appraiseId;
                        $constructionCompanyData = new CertificateAssetConstructionCompany($constructionCompanyData);
                        $constructionCompanyId = QueryBuilder::for($constructionCompanyData)
                            ->insertGetId($constructionCompanyData->attributesToArray());
                    }
                }

                if (isset($objects['asset_general'])) {
                    foreach ($objects['asset_general'] as $assetGeneralData) {
                        $assetGeneralData['appraise_id'] = $appraiseId;
                        $assetGeneralData = new CertificateAssetHasAsset($assetGeneralData);
                        $assetGeneralId = QueryBuilder::for($assetGeneralData)
                            ->insertGetId($assetGeneralData->attributesToArray());
                    }
                }

                $tangibleComparisonFactor['appraise_id'] = $appraiseId;
                $tangibleComparisonFactor = new CertificateAssetCTangibleComparisonFactor($tangibleComparisonFactor);
                $tangibleComparisonFactorId = QueryBuilder::for($tangibleComparisonFactor)
                    ->insertGetId($tangibleComparisonFactor->attributesToArray());


                $this->comparisonFactor($appraiseId);

                $rows = $this->findById($appraiseId);
                $rows->asset = $objects['assets'] ?? null;
                //$this->indexData($rows);
                return $appraiseId;
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
    public function updateAppraise($id, array $objects): int
    {
        return DB::transaction(function () use ($id, $objects) {
            try {
                $appraise = new CertificateAsset($objects);
                $appraiseId = $id;
                $appraise->newQuery()->updateOrInsert(['id' => $id], $appraise->attributesToArray());
                CertificateAssetPic::query()->where('appraise_id', '=', $appraiseId)->delete();
                if (isset($objects['pic'])) {
                    foreach ($objects['pic'] as $appraisePic) {
                        $appraisePic['appraise_id'] = $appraiseId;
                        $pic = new CertificateAssetPic($appraisePic);
                        QueryBuilder::for($pic)
                            ->insert($pic->attributesToArray());
                    }
                }

                CertificateAssetProperty::query()->where('appraise_id', '=', $appraiseId)->delete();
                if (isset($objects['properties'])) {
                    foreach ($objects['properties'] as $propertyData) {
                        $propertyData['asset_appraise_id'] = $appraiseId;

                        $property = new CertificateAssetProperty($propertyData);
                        $propertyId = QueryBuilder::for($property)
                            ->insertGetId($property->attributesToArray());
                        if (isset($propertyData['property_detail'])) {
                            foreach ($propertyData['property_detail'] as $propertyDetail) {
                                $propertyDetail['appraise_property_id'] = $propertyId;
                                $detail = new CertificateAssetPropertyDetail($propertyDetail);
                                QueryBuilder::for($detail)
                                    ->insert($detail->attributesToArray());
                            }
                        }

                        if (isset($propertyData['property_turning_time'])) {
                            foreach ($propertyData['property_turning_time'] as $propertyTurningTime) {
                                $propertyTurningTime['appraise_property_id'] = $propertyId;
                                $detail = new CertificateAssetPropertyTurningTime($propertyTurningTime);
                                QueryBuilder::for($detail)
                                    ->insert($detail->attributesToArray());
                            }
                        }
                    }
                }

                CertificateAssetTangibleAsset::query()->where('appraise_id', '=', $appraiseId)->delete();
                if (isset($objects['tangible_assets'])) {
                    foreach ($objects['tangible_assets'] as $tangibleAssetData) {
                        $tangibleAssetData['appraise_id'] = $appraiseId;
                        $tangibleAsset = new CertificateAssetTangibleAsset($tangibleAssetData);
                        $tangibleId = QueryBuilder::for($tangibleAsset)
                            ->insertGetId($tangibleAsset->attributesToArray());
                    }
                }

                CertificateAssetOtherAsset::query()->where('appraise_id', '=', $appraiseId)->delete();
                if (isset($objects['other_assets'])) {
                    foreach ($objects['other_assets'] as $otherAssetData) {
                        $otherAssetData['appraise_id'] = $appraiseId;
                        $otherAsset = new CertificateAssetOtherAsset($otherAssetData);
                        $otherId = QueryBuilder::for($otherAsset)
                            ->insertGetId($otherAsset->attributesToArray());
                    }
                }

                CertificateAssetLaw::query()->where('appraise_id', '=', $appraiseId)->delete();
                if (isset($objects['appraise_law'])) {
                    foreach ($objects['appraise_law'] as $lawData) {
                        $lawData['appraise_id'] = $appraiseId;
                        $law = new CertificateAssetLaw($lawData);
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
                    }
                }

                CertificateAssetConstructionCompany::query()->where('appraise_id', '=', $appraiseId)->delete();
                if (isset($objects['construction_company'])) {
                    foreach ($objects['construction_company'] as $constructionCompanyData) {
                        $constructionCompanyData['appraise_id'] = $appraiseId;
                        $constructionCompanyData = new CertificateAssetConstructionCompany($constructionCompanyData);
                        $constructionCompanyId = QueryBuilder::for($constructionCompanyData)
                            ->insertGetId($constructionCompanyData->attributesToArray());
                    }
                }

                CertificateAssetHasAsset::query()->where('appraise_id', '=', $appraiseId)->forceDelete();
                if (isset($objects['asset_general'])) {
                    foreach ($objects['asset_general'] as $assetGeneralData) {
                        $assetGeneralData['appraise_id'] = $appraiseId;
                        $assetGeneralData = new CertificateAssetHasAsset($assetGeneralData);
                        $assetGeneralId = QueryBuilder::for($assetGeneralData)
                            ->insertGetId($assetGeneralData->attributesToArray());
                    }
                }

                CertificateAssetComparisonFactor::query()->where('appraise_id', '=', $appraiseId)->forceDelete();

                $this->comparisonFactor($appraiseId);
                $rows = $this->findById($appraiseId);
                $rows->asset = $objects['assets'] ?? null;
                //$this->indexData($rows);
                return $appraiseId;
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
        $search_result = CertificateAssetVersion::searchByQuery($array, null, null, 1, 0, null);
        if (isset($search_result[0])) {
            return $search_result[0];
        } else {
            return null;
        }
    }

    /**
     * @param $id
     * @return void
     */
    public function comparisonFactor($id)
    {
        $object = $this->findById($id);
        $dictionaries = $this->findAllAppraiseDictionaries();
        foreach ($object->appraiseHasAssets as $appraiseHasAsset) {
            $assets = $this->findAssetGeneralById($appraiseHasAsset->asset_general_id);
            $asset = null;
            foreach ($assets->properties as $property) {
                if ($appraiseHasAsset->asset_property_detail_id == $property->id) {
                    $asset = $property;
                }
            }

            $this->getUnitPrice($object, $asset, $appraiseHasAsset);

            $assetLegal = $asset->legal->description ?? 'Không biết';
            $appraiseLegal = $object->properties[0]->legal->description ?? 'Không biết';
            $comparisonFactor = [
                'appraise_id' => $id,
                'asset_general_id' => $appraiseHasAsset->asset_general_id,
                'status' => 1,
                'type' => 'phap_ly',
                'appraise_title' => $appraiseLegal,
                'asset_title' => $assetLegal,
                'description' => CompareMaterData::COMPARISONS_DESCRIPTION['tuong_dong'],
                'adjust_percent' => 0,
            ];
            $comparisonFactor = new CertificateAssetComparisonFactor($comparisonFactor);
            $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                ->insertGetId($comparisonFactor->attributesToArray());

            foreach ($dictionaries['phap_ly'] as $dictionary) {
                if (($appraiseLegal == $dictionary->appraise_title) && ($assetLegal == $dictionary->asset_title)) {
                    $comparisonFactor = [
                        'appraise_id' => $id,
                        'asset_general_id' => $appraiseHasAsset->asset_general_id,
                        'status' => 1,
                        'type' => 'phap_ly',
                        'appraise_title' => $appraiseLegal,
                        'asset_title' => $assetLegal,
                        'description' => $dictionary->description,
                        'adjust_percent' => $dictionary->adjust_percent,
                    ];
                    $comparisonFactor = new CertificateAssetComparisonFactor($comparisonFactor);
                    $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                        ->updateOrInsert(['id' => $comparisonFactorId], $comparisonFactor->attributesToArray());
                }

            }
            $assetlandShape = $asset->landShape->description ?? 'Không biết';
            $appraiselandShape = $object->properties[0]->landShape->description ?? 'Không biết';

            $comparisonFactor = [
                'appraise_id' => $id,
                'asset_general_id' => $appraiseHasAsset->asset_general_id,
                'status' => 1,
                'type' => 'hinh_dang_dat',
                'appraise_title' => $appraiselandShape,
                'asset_title' => $assetlandShape,
                'description' => CompareMaterData::COMPARISONS_DESCRIPTION['tuong_dong'],
                'adjust_percent' => 0,
            ];

            $comparisonFactor = new CertificateAssetComparisonFactor($comparisonFactor);
            $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                ->insertGetId($comparisonFactor->attributesToArray());
            foreach ($dictionaries['hinh_dang_dat'] as $dictionary) {
                if (($appraiselandShape == $dictionary->appraise_title) && ($assetlandShape == $dictionary->asset_title)) {
                    $comparisonFactor = [
                        'appraise_id' => $id,
                        'asset_general_id' => $appraiseHasAsset->asset_general_id,
                        'status' => 1,
                        'type' => 'hinh_dang_dat',
                        'appraise_title' => $appraiselandShape,
                        'asset_title' => $assetlandShape,
                        'description' => $dictionary->description,
                        'adjust_percent' => $dictionary->adjust_percent,
                    ];
                    $comparisonFactor = new CertificateAssetComparisonFactor($comparisonFactor);
                    $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                        ->updateOrInsert(['id' => $comparisonFactorId], $comparisonFactor->attributesToArray());
                }
            }

            $assetBusiness = $asset->business->description ?? 'Không biết';
            $appraiseBusiness = $object->properties[0]->business->description ?? 'Không biết';

            $comparisonFactor = [
                'appraise_id' => $id,
                'asset_general_id' => $appraiseHasAsset->asset_general_id,
                'status' => 1,
                'type' => 'kinh_doanh',
                'appraise_title' => $appraiseBusiness,
                'asset_title' => $assetBusiness,
                'description' => CompareMaterData::COMPARISONS_DESCRIPTION['tuong_dong'],
                'adjust_percent' => 0,
            ];
            $comparisonFactor = new CertificateAssetComparisonFactor($comparisonFactor);
            $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                ->insertGetId($comparisonFactor->attributesToArray());
            foreach ($dictionaries['kinh_doanh'] as $dictionary) {
                if (($appraiseBusiness == $dictionary->appraise_title) && ($assetBusiness == $dictionary->asset_title)) {
                    $comparisonFactor = [
                        'appraise_id' => $id,
                        'asset_general_id' => $appraiseHasAsset->asset_general_id,
                        'status' => 1,
                        'type' => 'kinh_doanh',
                        'appraise_title' => $appraiseBusiness,
                        'asset_title' => $assetBusiness,
                        'description' => $dictionary->description,
                        'adjust_percent' => $dictionary->adjust_percent,
                    ];
                    $comparisonFactor = new CertificateAssetComparisonFactor($comparisonFactor);
                    $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                        ->updateOrInsert(['id' => $comparisonFactorId], $comparisonFactor->attributesToArray());
                }
            }
            $assetConditions = $asset->conditions->description ?? 'Không biết';
            $appraiseConditions = $object->properties[0]->conditions->description ?? 'Không biết';
            $comparisonFactor = [
                'appraise_id' => $id,
                'asset_general_id' => $appraiseHasAsset->asset_general_id,
                'status' => 1,
                'type' => 'dieu_kien_ha_tang',
                'appraise_title' => $appraiseConditions,
                'asset_title' => $assetConditions,
                'description' => CompareMaterData::COMPARISONS_DESCRIPTION['tuong_dong'],
                'adjust_percent' => 0,
            ];
            $comparisonFactor = new CertificateAssetComparisonFactor($comparisonFactor);
            $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                ->insertGetId($comparisonFactor->attributesToArray());
            foreach ($dictionaries['dieu_kien_ha_tang'] as $dictionary) {
                if (($appraiseConditions == $dictionary->appraise_title) && ($assetConditions == $dictionary->asset_title)) {
                    $comparisonFactor = [
                        'appraise_id' => $id,
                        'asset_general_id' => $appraiseHasAsset->asset_general_id,
                        'status' => 1,
                        'type' => 'dieu_kien_ha_tang',
                        'appraise_title' => $appraiseConditions,
                        'asset_title' => $assetConditions,
                        'description' => $dictionary->description,
                        'adjust_percent' => $dictionary->adjust_percent,
                    ];
                    $comparisonFactor = new CertificateAssetComparisonFactor($comparisonFactor);
                    $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                        ->updateOrInsert(['id' => $comparisonFactorId], $comparisonFactor->attributesToArray());
                }

            }
            $assetSocialSecurity = $asset->socialSecurity->description ?? 'Không biết';
            $appraiseSocialSecurity = $object->properties[0]->socialSecurity->description ?? 'Không biết';

            $comparisonFactor = [
                'appraise_id' => $id,
                'asset_general_id' => $appraiseHasAsset->asset_general_id,
                'status' => 1,
                'type' => 'an_ninh_moi_truong_song',
                'appraise_title' => $appraiseSocialSecurity,
                'asset_title' => $assetSocialSecurity,
                'description' => CompareMaterData::COMPARISONS_DESCRIPTION['tuong_dong'],
                'adjust_percent' => 0,
            ];

            $comparisonFactor = new CertificateAssetComparisonFactor($comparisonFactor);
            $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                ->insertGetId($comparisonFactor->attributesToArray());
            foreach ($dictionaries['an_ninh_moi_truong_song'] as $dictionary) {
                if (($appraiseSocialSecurity == $dictionary->appraise_title) && ($assetSocialSecurity == $dictionary->asset_title)) {
                    $comparisonFactor = [
                        'appraise_id' => $id,
                        'asset_general_id' => $appraiseHasAsset->asset_general_id,
                        'status' => 1,
                        'type' => 'an_ninh_moi_truong_song',
                        'appraise_title' => $appraiseSocialSecurity,
                        'asset_title' => $assetSocialSecurity,
                        'description' => $dictionary->description,
                        'adjust_percent' => $dictionary->adjust_percent,
                    ];
                    $comparisonFactor = new CertificateAssetComparisonFactor($comparisonFactor);
                    $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                        ->updateOrInsert(['id' => $comparisonFactorId], $comparisonFactor->attributesToArray());
                }
            }

            $assetFengShui = $asset->fengShui->description ?? 'Không biết';
            $appraiseFengShui = $object->properties[0]->fengShui->description ?? 'Không biết';

            $comparisonFactor = [
                'appraise_id' => $id,
                'asset_general_id' => $appraiseHasAsset->asset_general_id,
                'status' => 1,
                'type' => 'phong_thuy',
                'appraise_title' => $appraiseFengShui,
                'asset_title' => $assetFengShui,
                'description' => CompareMaterData::COMPARISONS_DESCRIPTION['tuong_dong'],
                'adjust_percent' => 0,
            ];

            $comparisonFactor = new CertificateAssetComparisonFactor($comparisonFactor);
            $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                ->insertGetId($comparisonFactor->attributesToArray());
            foreach ($dictionaries['phong_thuy'] as $dictionary) {
                if (($appraiseFengShui == $dictionary->appraise_title) && ($assetFengShui == $dictionary->asset_title)) {
                    $comparisonFactor = [
                        'appraise_id' => $id,
                        'asset_general_id' => $appraiseHasAsset->asset_general_id,
                        'status' => 1,
                        'type' => 'phong_thuy',
                        'appraise_title' => $appraiseFengShui,
                        'asset_title' => $assetFengShui,
                        'description' => $dictionary->description,
                        'adjust_percent' => $dictionary->adjust_percent,
                    ];
                    $comparisonFactor = new CertificateAssetComparisonFactor($comparisonFactor);
                    $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                        ->updateOrInsert(['id' => $comparisonFactorId], $comparisonFactor->attributesToArray());
                }
            }

            $assetMaterial = $asset->material->description ?? 'Không biết';
            $appraiseMaterial = $object->properties[0]->material->description ?? 'Không biết';
            if ($assetMaterial == 'Không biết') {
                if(isset($asset->comparePropertyTurningTime)) {
                    foreach ($asset->comparePropertyTurningTime as $comparePropertyTurningTime) {
                        $assetMaterial = $comparePropertyTurningTime->material->description ?? $assetMaterial;
                    }
                }
            }

            if ($appraiseMaterial == 'Không biết') {
                if(isset($object->properties[0]->propertyTurningTime)) {
                    foreach ($object->properties[0]->propertyTurningTime as $propertyTurningTime) {
                        $appraiseMaterial = $propertyTurningTime->material->description ?? $appraiseMaterial;
                    }
                }
            }

            $comparisonFactor = [
                'appraise_id' => $id,
                'asset_general_id' => $appraiseHasAsset->asset_general_id,
                'status' => 1,
                'type' => 'ket_cau_duong',
                'appraise_title' => $appraiseMaterial,
                'asset_title' => $assetMaterial,
                'description' => CompareMaterData::COMPARISONS_DESCRIPTION['tuong_dong'],
                'adjust_percent' => 0,
            ];

            $comparisonFactor = new CertificateAssetComparisonFactor($comparisonFactor);
            $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                ->insertGetId($comparisonFactor->attributesToArray());
            foreach ($dictionaries['ket_cau_duong'] as $dictionary) {
                if (($appraiseMaterial == $dictionary->appraise_title) && ($assetMaterial == $dictionary->asset_title)) {
                    $comparisonFactor = [
                        'appraise_id' => $id,
                        'asset_general_id' => $appraiseHasAsset->asset_general_id,
                        'status' => 1,
                        'type' => 'ket_cau_duong',
                        'appraise_title' => $appraiseMaterial,
                        'asset_title' => $assetMaterial,
                        'description' => $dictionary->description,
                        'adjust_percent' => $dictionary->adjust_percent,
                    ];
                    $comparisonFactor = new CertificateAssetComparisonFactor($comparisonFactor);
                    $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                        ->updateOrInsert(['id' => $comparisonFactorId], $comparisonFactor->attributesToArray());
                }
            }

            $assetPaymenMethod = $asset->paymenMethod->description ?? 'Không biết';
            $appraisePaymenMethod = $object->properties[0]->paymenMethod->description ?? 'Không biết';
            $comparisonFactor = [
                'appraise_id' => $id,
                'asset_general_id' => $appraiseHasAsset->asset_general_id,
                'status' => 1,
                'type' => 'dieu_kien_thanh_toan',
                'appraise_title' => $appraisePaymenMethod,
                'asset_title' => $assetPaymenMethod,
                'description' => CompareMaterData::COMPARISONS_DESCRIPTION['tuong_dong'],
                'adjust_percent' => 0,
            ];

            $comparisonFactor = new CertificateAssetComparisonFactor($comparisonFactor);
            $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                ->insertGetId($comparisonFactor->attributesToArray());
            foreach ($dictionaries['dieu_kien_thanh_toan'] as $dictionary) {
                if (($appraisePaymenMethod == $dictionary->appraise_title) && ($assetPaymenMethod == $dictionary->asset_title)) {
                    $comparisonFactor = [
                        'appraise_id' => $id,
                        'asset_general_id' => $appraiseHasAsset->asset_general_id,
                        'status' => 1,
                        'type' => 'dieu_kien_thanh_toan',
                        'appraise_title' => $appraisePaymenMethod,
                        'asset_title' => $assetPaymenMethod,
                        'description' => $dictionary->description,
                        'adjust_percent' => $dictionary->adjust_percent,
                    ];
                    $comparisonFactor = new CertificateAssetComparisonFactor($comparisonFactor);
                    $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                        ->updateOrInsert(['id' => $comparisonFactorId], $comparisonFactor->attributesToArray());
                }
            }

            $assetFrontSide = $asset->front_side_width ?? 0;
            $appraiseFrontSide = $object->properties[0]->front_side_width ?? 0;

            $description = CompareMaterData::COMPARISONS_DESCRIPTION['tuong_dong'];

            $comparisonFactor = [
                'appraise_id' => $id,
                'asset_general_id' => $appraiseHasAsset->asset_general_id,
                'status' => 1,
                'type' => 'chieu_rong_mat_tien',
                'appraise_title' => $appraiseFrontSide,
                'asset_title' => $assetFrontSide,
                'description' => $description,
                'adjust_percent' => 0,
            ];

            $comparisonFactor = new CertificateAssetComparisonFactor($comparisonFactor);
            $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                ->insertGetId($comparisonFactor->attributesToArray());


            $assetRoad = (float)($asset->main_road_length ?? 0);
            if ($assetRoad == 0) {
                if(isset($asset->comparePropertyTurningTime)) {
                    foreach ($asset->comparePropertyTurningTime as $comparePropertyTurningTime) {
                        $assetRoad = (float)$comparePropertyTurningTime->main_road_length;
                    }
                }
            }

            $appraiseRoad = (float)($object->properties[0]->main_road_length ?? 0);

            if ($appraiseRoad == 0) {
                if(isset($object->properties[0]->propertyTurningTime)) {
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
                'status' => 1,
                'type' => 'do_rong_duong',
                'appraise_title' => $appraiseRoad,
                'asset_title' => $assetRoad,
                'description' => $description,
                'adjust_percent' => 0,
            ];
            $comparisonFactor = new CertificateAssetComparisonFactor($comparisonFactor);
            $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                ->insertGetId($comparisonFactor->attributesToArray());
            foreach ($dictionaries['do_rong_duong'] as $dictionary) {
                if (($appraiseRoadLength == $dictionary->appraise_title) && ($assetRoadLength == $dictionary->asset_title)) {
                    $comparisonFactor = [
                        'appraise_id' => $id,
                        'asset_general_id' => $appraiseHasAsset->asset_general_id,
                        'status' => 1,
                        'type' => 'do_rong_duong',
                        'appraise_title' => $appraiseRoad,
                        'asset_title' => $assetRoad,
                        'description' => $dictionary->description,
                        'adjust_percent' => $dictionary->adjust_percent,
                    ];

                    $comparisonFactor = new CertificateAssetComparisonFactor($comparisonFactor);
                    $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                        ->updateOrInsert(['id' => $comparisonFactorId], $comparisonFactor->attributesToArray());
                }
            }

            $assetInsight = $asset->insight_width ?? 0;
            $appraiseInsight = $object->properties[0]->insight_width ?? 0;

            $description = CompareMaterData::COMPARISONS_DESCRIPTION['tuong_dong'];

            $comparisonFactor = [
                'appraise_id' => $id,
                'asset_general_id' => $appraiseHasAsset->asset_general_id,
                'status' => 1,
                'type' => 'chieu_sau_khu_dat',
                'appraise_title' => $appraiseInsight,
                'asset_title' => $assetInsight,
                'description' => $description,
                'adjust_percent' => 0,
            ];
            $comparisonFactor = new CertificateAssetComparisonFactor($comparisonFactor);
            $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                ->insertGetId($comparisonFactor->attributesToArray());

            $assetLandSumArea = $asset->appraise_land_sum_area ?? 0;
            $appraiseLandSumArea = $object->properties[0]->appraise_land_sum_area ?? 0;
            $description = CompareMaterData::COMPARISONS_DESCRIPTION['tuong_dong'];

            $comparisonFactor = [
                'appraise_id' => $id,
                'asset_general_id' => $appraiseHasAsset->asset_general_id,
                'status' => 1,
                'type' => 'quy_mo',
                'appraise_title' => $appraiseLandSumArea,
                'asset_title' => $assetLandSumArea,
                'description' => $description,
                'adjust_percent' => 0,
            ];
            $comparisonFactor = new CertificateAssetComparisonFactor($comparisonFactor);
            $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                ->insertGetId($comparisonFactor->attributesToArray());
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
     * @param $id
     * @return $objects
     */
    public function getComparisonFactor($id): array
    {
        $label = [
            'phap_ly' => 'Pháp lý',
            'hinh_dang_dat' => 'Hình dáng đất',
            'giao_thong' => 'Giao thông',
            'kinh_doanh' => 'Kinh doanh',
            'dieu_kien_ha_tang' => 'Điều kiện hạ tầng',
            'an_ninh_moi_truong_song' => 'An ninh, môi trường sống',
            'phong_thuy' => 'Phong thủy',
            'quy_mo' => 'Quy mô',
            'chieu_rong_mat_tien' => 'Chiều rộng mặt tiền',
            'chieu_sau_khu_dat' => 'Chiều sâu khu đất',
            'do_rong_duong' => 'Bề rộng đường',
            'phong_thuy' => 'Phong thủy',
            'yeu_to_khac' => 'Yếu tố khác',
        ];

        $checked = [
            'phap_ly' => 0,
            'hinh_dang_dat' => 0,
            'giao_thong' => 0,
            'kinh_doanh' => 0,
            'dieu_kien_ha_tang' => 0,
            'an_ninh_moi_truong_song' => 0,
            'phong_thuy' => 0,
            'quy_mo' => 0,
            'chieu_rong_mat_tien' => 0,
            'chieu_sau_khu_dat' => 0,
            'do_rong_duong' => 0,
            'phong_thuy' => 0,
            'yeu_to_khac' => 0,
        ];
        $item = $this->model->query()
                ->where('id', '=', $id)
                ->with('comparisonFactor')
                //->with('assetGeneral')
                ->first();
        $item->append('asset_general');
        $item->assetGeneral = $item->asset_general;

        $result = [];
        if(isset($item->assetGeneral)) {
            foreach($item->assetGeneral as $index=>$assetGeneral) {
                foreach($item->comparisonFactor as $comparisonFactor) {

                    if(($comparisonFactor->appraise_id==$id)
                        &&($comparisonFactor->asset_general_id==$assetGeneral->id)
                        &&(isset($label[$comparisonFactor->type]))
                    ) {
                        if($checked[$comparisonFactor->type]) continue;
                        $result[$id][] = [
                            "type" => $comparisonFactor->type,
                            "label" => $label[$comparisonFactor->type],
                            "status" => $comparisonFactor->status,
                        ];
                        $checked[$comparisonFactor->type]++;
                    }
                }
                if($index) continue;
            }
        }

        return $result;
    }

    /**
     * @param $objects
     * @return bool
     */
    /* public function updateTangibleComparisonFactor($objects): bool
    {
        if (isset($objects['comparison_tangible_factor'])) {
            foreach ($objects['comparison_tangible_factor'] as $comparisonFactorData) {
                $comparisonFactor = new CertificateAssetCTangibleComparisonFactor($comparisonFactorData);
                $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                    ->updateOrInsert(['id' => $comparisonFactorData['id']], $comparisonFactor->attributesToArray());
            }
        }
        return true;
    } */
    public function updateTangibleComparisonFactor($objects): bool
    {

        if (isset($objects['comparison_tangible_factor'])) {
            foreach ($objects['comparison_tangible_factor'] as $comparisonFactorData) {
                $comparisonFactor = new CertificateAssetCTangibleComparisonFactor($comparisonFactorData);
                $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                    ->updateOrInsert(['id' => $comparisonFactorData['id']], $comparisonFactor->attributesToArray());
            }
        }

        if (isset($objects['construction_company'])) {
            foreach ($objects['construction_company'] as $companies) {
                foreach ($companies as $item) {
                    if(isset($item)) {
                        $constructionCompanyData = $item;
                        $constructionCompany = new CertificateAssetConstructionCompany($constructionCompanyData);
                        $constructionCompanyId = QueryBuilder::for($constructionCompany)
                            ->updateOrInsert(['id' => $constructionCompanyData['id']], $constructionCompany->attributesToArray());
                    }
                }
            }
        }

        return true;
    }

    /**
     * @param $objects
     * @return bool
     */
    public function updateComparisonFactor($objects): bool
    {
        if (isset($objects['comparison_factor'])) {
            $comparisonFactorDatas = isset($objects['other_comparison']) ? array_merge($objects['comparison_factor'], $objects['other_comparison']) : $objects['comparison_factor'];
            foreach ($comparisonFactorDatas as $factors) {
                foreach ($factors as $comparisonFactorData) {
                    $comparisonFactorData['appraise_title'] = isset($comparisonFactorData['appraise_title']) ? $comparisonFactorData['appraise_title'] : "";
                    $comparisonFactorData['asset_title'] = isset($comparisonFactorData['asset_title']) ? $comparisonFactorData['asset_title'] : "";
                    $comparisonFactorData['description'] = isset($comparisonFactorData['description']) ? $comparisonFactorData['description'] : "";

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

                    $comparisonFactor = new CertificateAssetComparisonFactor($comparisonFactorData);
                    if(isset($comparisonFactorData['id'])) {
                        $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                            ->updateOrInsert(['id' => $comparisonFactorData['id']], $comparisonFactor->attributesToArray());
                    } else {
                        $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                            ->insert($comparisonFactor->attributesToArray());
                    }
                }
            }
        }
        if (isset($objects['delete_other_comparison'])) {
            foreach ($objects['delete_other_comparison'] as $factors) {
                foreach ($factors as $comparisonFactorData) {
                    if(isset($comparisonFactorData['id'])) {
                        CertificateAssetComparisonFactor::where('id', $comparisonFactorData['id'])->forceDelete();
                    }
                }
            }
        }

        $certificateId = null;
        if (isset($objects['asset_unit_price'])) {
            $jwt = JWTAuth::getToken();
            $eloquentUserRepository = new EloquentUserRepository(new User());
            $user = $eloquentUserRepository->validateToken($jwt);
            foreach ($objects['asset_unit_price'] as $assetUnitPrice) {
                foreach ($assetUnitPrice as $item) {
                    if(!isset($certificateId)&&(isset($assetUnitPrice['certificate_id'])))
                        $certificateId = $assetUnitPrice['certificate_id'];

                    unset($item['land_type_data']);
                    $item['created_by'] = $user->id;

                    $appraiseUnitPrice = new CertificateAssetUnitPrice($item);
                    if(isset($item['id'])) {
                        $appraiseUnitPriceId = QueryBuilder::for($appraiseUnitPrice)
                            ->updateOrInsert(['id' => $item['id']], $appraiseUnitPrice->attributesToArray());
                    } else {
                        $appraiseUnitPriceId = QueryBuilder::for($appraiseUnitPrice)
                            ->insert($appraiseUnitPrice->attributesToArray());
                    }
                }
            }
        }
        if (isset($objects['asset_unit_area'])) {
            $jwt = JWTAuth::getToken();
            $eloquentUserRepository = new EloquentUserRepository(new User());
            $user = $eloquentUserRepository->validateToken($jwt);
            foreach ($objects['asset_unit_area'] as $assetUnitArea) {
                foreach ($assetUnitArea as $item) {
                    if(!isset($certificateId)&&(isset($assetUnitArea['certificate_id'])))
                        $certificateId = $assetUnitArea['certificate_id'];

                    unset($item['land_type_data']);
                    $item['created_by'] = $user->id;

                    $appraiseUnitArea = new CertificateAssetUnitPrice($item);
                    if(isset($item['id'])) {
                        $appraiseUnitAreaId = QueryBuilder::for($appraiseUnitArea)
                            ->updateOrInsert(['id' => $item['id']], $appraiseUnitArea->attributesToArray());
                    } else {
                        $appraiseUnitAreaId = QueryBuilder::for($appraiseUnitArea)
                            ->insert($appraiseUnitArea->attributesToArray());
                    }
                }
            }
        }

        if(isset($certificateId)) {
            /* $result = $this->findById($appraiseId);
            $commonService = new CommonService();
            $commonService->getCertificateAssetPriceTotal($result); */
        }

        return true;
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
                if (isset($landType->landTypePurpose->acronym)&&($propertyDetail1->landTypePurposeData->acronym == $landType->landTypePurpose->acronym)
                    || (in_array($propertyDetail1->landTypePurposeData->id, EstimateAssetDefault::GROUP_LAND_TYPE) && in_array($landType->landTypePurpose->id, EstimateAssetDefault::GROUP_LAND_TYPE))) {
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
        $assetGeneralId = CertificateAssetHasAsset::query()
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
}
