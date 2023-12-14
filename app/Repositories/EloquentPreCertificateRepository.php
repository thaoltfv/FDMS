<?php

namespace App\Repositories;

use App;
use App\Contracts\PreCertificateRepository;
use App\Enum\CompareMaterData;
use App\Enum\ErrorMessage;
use App\Models\PreCertificate;
use App\Models\Customer;
use App\Models\CertificateApproach;
use App\Models\CertificateBasisProperty;
use App\Models\CertificateComparisonFactor;
use App\Models\CertificateConstructionCompany;
use App\Models\CertificateHasAppraise;
use App\Models\CertificateLegalDocumentsOnConstruction;
use App\Models\CertificateLegalDocumentsOnLand;
use App\Models\CertificateLegalDocumentsOnLocal;
use App\Models\CertificateLegalDocumentsOnValuation;
use App\Models\CertificateMethodUsed;
use App\Models\CertificatePrinciple;
use App\Models\CertificateTangibleComparisonFactor;
use App\Models\CertificateAssetUnitPrice;
use App\Models\CertificateAssetUnitArea;
use App\Models\PreCertificateOtherDocuments;
use App\Models\CertificateAssetAppraisalMethods;
use App\Models\CertificateAssetPrice;

use App\Models\Appraise;
use App\Models\AppraiseVersion;
use App\Models\AppraiseHasAsset;
use App\Models\AppraiseComparisonFactor;
use App\Models\AppraiseAdapter;
use App\Models\AppraiseLaw;
use App\Models\AppraiseLawDetail;
use App\Models\AppraiseLawLandDetail;
use App\Models\AppraiseLegalDocumentsOnConstruction;
use App\Models\AppraiseLegalDocumentsOnLand;
use App\Models\AppraiseLegalDocumentsOnLocal;
use App\Models\AppraiseLegalDocumentsOnValuation;
use App\Models\AppraiseOtherAsset;
use App\Models\AppraisePic;
use App\Models\AppraisePrice;
use App\Models\AppraiseProperty;
use App\Models\AppraisePropertyDetail;
use App\Models\AppraisePropertyTurningTime;
use App\Models\AppraiseTangibleAsset;
use App\Models\AppraiseTangibleComparisonFactor;
use App\Models\AppraiseUnitPrice;
use App\Models\AppraiseUnitArea;
use App\Models\AppraiseAppraisalMethods;

use App\Notifications\BroadcastNotification;
use Elasticsearch\ClientBuilder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\QueryBuilder\QueryBuilder;
use Ramsey\Uuid\Uuid;
use Storage;
use Exception;
use Throwable;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Repositories\EloquentUserRepository;
use App\Models\User;
use App\Enum\ValueDefault;
use App\Models\Apartment;
use App\Models\ApartmentAsset;
use App\Models\ApartmentAssetAdapter;
use App\Models\ApartmentAssetAppraisalBase;
use App\Models\ApartmentAssetAppraisalMethod;
use App\Models\ApartmentAssetComparisonFactor;
use App\Models\ApartmentAssetHasAsset;
use App\Models\ApartmentAssetLaw;
use App\Models\ApartmentAssetOtherAsset;
use App\Models\ApartmentAssetPic;
use App\Models\ApartmentAssetPrice;
use App\Models\ApartmentAssetProperty;
use App\Models\ApartmentAssetVersion;
use App\Models\CertificateAsset;
use App\Models\CertificateAssetVersion;
use App\Models\CertificateAssetHasAsset;
use App\Models\CertificateAssetComparisonFactor;
use App\Models\CertificateAssetAdapter;
use App\Models\CertificateAssetConstructionCompany;
use App\Models\CertificateAssetLaw;
use App\Models\CertificateAssetLawDetail;
use App\Models\CertificateAssetLawLandDetail;
use App\Models\CertificateAssetLegalDocumentsOnConstruction;
use App\Models\CertificateAssetLegalDocumentsOnLand;
use App\Models\CertificateAssetLegalDocumentsOnLocal;
use App\Models\CertificateAssetLegalDocumentsOnValuation;
use App\Models\CertificateAssetOtherAsset;
use App\Models\CertificateAssetPic;
use App\Models\CertificateAssetProperty;
use App\Models\CertificateAssetPropertyDetail;
use App\Models\CertificateAssetPropertyTurningTime;
use App\Models\CertificateAssetTangibleAsset;
use App\Models\CertificateAssetTangibleComparisonFactor;

use App\Models\AppraisalConstructionCompany;
use App\Models\AppraiseLawDocument;
use App\Models\AppraiseOtherInformation;
use App\Models\Appraiser;
use App\Repositories\EloquentCertificateAssetRepository;

use App\Models\BuildingPrice;
use App\Models\CertificateApartment;
use App\Models\CertificateApartmentAdapter;
use App\Models\CertificateApartmentAppraisalBase;
use App\Models\CertificateApartmentAppraisalMethod;
use App\Models\CertificateApartmentComparisonFactor;
use App\Models\CertificateApartmentHasAsset;
use App\Models\CertificateApartmentLaw;
use App\Models\CertificateApartmentOtherAsset;
use App\Models\CertificateApartmentPic;
use App\Models\CertificateApartmentPrice;
use App\Models\CertificateApartmentProperty;
use App\Models\CertificateApartmentVersion;
use App\Models\CertificateDictionary;
use App\Models\CertificateHasPersonalProperty;
use App\Models\CertificateHasRealEstate;
use App\Models\CertificatePersonalProperty;
use App\Models\CertificatePrice;
use App\Models\CertificateRealEstate;
use App\Models\ConstructionCompany;
use App\Models\Dictionary;
use App\Models\MachineCertificateAsset;
use App\Models\MachineCertificateAssetLaw;
use App\Models\MachineCertificateAssetLawInfo;
use App\Models\MachineCertificateAssetPrice;
use App\Models\MachineCertificateBrief;
use App\Models\MachineCertificateBriefLaw;
use App\Models\MachineCertificateBriefLawInfo;
use App\Models\MachineCertificateBriefPrice;
use App\Models\OtherCertificateAsset;
use App\Models\OtherCertificateAssetLaw;
use App\Models\OtherCertificateAssetLawInfo;
use App\Models\OtherCertificateAssetPrice;
use App\Models\OtherCertificateBrief;
use App\Models\OtherCertificateBriefLaw;
use App\Models\OtherCertificateBriefLawInfo;
use App\Models\OtherCertificateBriefPrice;
use App\Models\PersonalProperty;
use App\Models\RealEstate;
use App\Models\VerhicleCertificateAsset;
use App\Models\VerhicleCertificateAssetLaw;
use App\Models\VerhicleCertificateAssetLawInfo;
use App\Models\VerhicleCertificateAssetPrice;
use App\Models\VerhicleCertificateBrief;
use App\Models\VerhicleCertificateBriefLaw;
use App\Models\VerhicleCertificateBriefLawInfo;
use App\Models\VerhicleCertificateBriefPrice;
use App\Models\Views\ViewSelectedCertificateAsset;
use App\Models\Views\ViewSelectedCertificateApartment;
use App\Notifications\ActivityLog;
use App\Repositories\EloquentBuildingPriceRepository;
use App\Services\AppraiseVersionService;
use App\Services\CommonService;
use Carbon\Carbon;
use Illuminate\Support\Arr;


use function PHPUnit\Framework\isEmpty;

class  EloquentPreCertificateRepository extends EloquentRepository implements PreCertificateRepository
{

    use ActivityLog;

    private string $defaultSort = 'id';

    private string $allowedSorts = 'id';

    /**
     * @return bool
     */
    public function updateStatus($id, $request): bool
    {
        return DB::transaction(function () use ($id, $request) {
            try {
                $result = 0;
                $status = $request->input('status');
                if (isset($status)) {
                    $result = $this->model->query()
                        ->where('id', '=', $id)
                        ->update([
                            'status' => $request->input('status'),
                            'status_updated_at' => date('Y-m-d H:i:s')
                        ]);
                    if ($status == 2) {
                        // if(!$this->checkExistsAppraise($id))
                        // {
                        //     return ['message' => 'Bạn không thể xét duyệt do chưa có thông tin tài sản thẩm định. ','exception' => '' ];
                        // }
                        $oldCertificate = PreCertificate::where('id', $id)->first();
                        $appraises = $oldCertificate->appraises;
                        foreach ($appraises as $appraiseTmp) {
                            Appraise::where('id', $appraiseTmp->appraise_id)->update(['status' => 4]); // updateStatus : 4 = closed
                            CertificateAsset::where('appraise_id', $appraiseTmp->appraise_id)->update(['status' => 4]);
                        }
                    } else if ($status == 5) {
                        $oldCertificate = PreCertificate::where('id', $id)->first();
                        $appraises = $oldCertificate->appraises;
                        foreach ($appraises as $appraiseTmp) {
                            Appraise::where('id', $appraiseTmp->appraise_id)->update(['status' => 1]); // updateStatus : 1 = draft
                        }
                    }
                }
                return $result;
            } catch (Exception $exception) {
                Log::error($exception);
                throw $exception;
            }
        });
    }

    /**
     * @return bool
     */
    public function otherDocumentUpload($id, $typeDocument, $request)
    {
        return DB::transaction(function () use ($id,$typeDocument, $request) {
            try {
                $result = [];
                $now = Carbon::now()->timezone('Asia/Ho_Chi_Minh');
                $path = env('STORAGE_OTHERS') . '/' . 'comparison_brief/' . $now->year . '/' . $now->month . '/';

                $files = $request->file('files');

                $user = CommonService::getUser();

                if (isset($files) && !empty($files)) {
                    foreach ($files as $file) {
                        $fileName = $file->getClientOriginalName();
                        $fileType = $file->getClientOriginalExtension();
                        $fileSize = $file->getSize();
                        $name = $path . Uuid::uuid4()->toString() . '.' . $fileType;
                        Storage::put($name, file_get_contents($file));
                        $fileUrl = Storage::url($name);
                        $item = [
                            'pre_certificate_id' => $id,
                            'name' => $fileName,
                            'link' => $fileUrl,
                            'type' => $fileType,
                            'size' => $fileSize,
                            'description' => 'appendix',
                            'created_by' => $user->id,
                            'type_document' => $typeDocument
                        ];

                        $item = new PreCertificateOtherDocuments($item);
                        QueryBuilder::for($item)->insert($item->attributesToArray());
                        $result[] = $item;
                    }
                    $edited = PreCertificate::where('id', $id)->first();
                    $edited2 = PreCertificateOtherDocuments::where('pre_certificate_id', $id)->first();
                    # activity-log upload file
                    $this->CreateActivityLog($edited, $edited2, 'upload_file', 'tải phụ lục');
                    // chưa lấy ra được model user và id user
                }

                $result = PreCertificateOtherDocuments::where('pre_certificate_id', $id)
                    ->with('createdBy')
                    ->get();
                return $result;
            } catch (Exception $exception) {
                Log::error($exception);
                throw $exception;
            }
        });
    }

    /**
     * @return bool
     */
    public function otherDocumentRemove($id, $request)
    {
        return DB::transaction(function () use ($id, $request) {
            try {
                $preCertificateId = PreCertificateOtherDocuments::select('pre_certificate_id')->where('id', $id)->get();
                $item = PreCertificateOtherDocuments::where('id', $id)->delete();
                $edited = PreCertificate::where('id', $preCertificateId[0]->pre_certificate_id)->first();
                $edited2 = PreCertificateOtherDocuments::where('id', $id)->get();
                $item = PreCertificateOtherDocuments::where('id', $id)->delete();
                # activity-log delete file
                $this->CreateActivityLog($edited, $edited2, 'delete_file', 'xóa phụ lục');
                // chưa lấy ra được model user và id user
                return $item;
            } catch (Exception $exception) {
                Log::error($exception);
                throw $exception;
            }
        });
    }

    /**
     * @return bool
     */
    public function otherDocumentDownload($id, $request)
    {
        return DB::transaction(function () use ($id, $request) {
            try {
                $item = PreCertificateOtherDocuments::where('id', $id)->first();
                return $item;
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
        $filters = request()->get('filters');
        $sortField = request()->get('sortField');
        $sortOrder = request()->get('sortOrder');

        $query = request()->get('query');
        if (!empty($query)) {
            $query = json_decode($query);
        } else {
            $query = [];
        }

        $result = QueryBuilder::for($this->model)
            ->with('appraiser')
            ->with('appraiserManager')
            ->with('appraiserConfirm')
            ->with('appraiserControl')
            ->with('appraiserSale')
            ->with('appraiserPerform')
            ->with('certificateApproach')
            ->with('appraiseMethodUsed')
            ->with('appraiseBasisProperty')
            ->with('certificatePrinciple')
            ->with('legalDocumentsOnValuation')
            ->with('legalDocumentsOnConstruction')
            ->with('legalDocumentsOnLand')
            ->with('legalDocumentsOnLocal')
            ->with('createdBy')
            ->with('assetPrice')
            //->with('constructionCompany')
            ->with('comparisonFactor')
            ->whereHas('createdBy', function ($q) use ($query, $user) {
                if (isset($query->created_by) && ($query->created_by != 'Tất cả người tạo')) {
                    return $q->where('name', '=', $query->created_by);
                }

                $role = $user->roles->last();
                if ($role->name == 'USER') {
                    return $q->where('id', $user->id);
                }
            })
            ->whereHas('assetPrice', function ($q) use ($query) {
                if (isset($query->total_amount_from)) {
                    $q->where('slug', '=', 'total_asset_price')->where('value', '>=', $query->total_amount_from);
                }
                if (isset($query->total_amount_to) && (floatval($query->total_amount_to) != 0)) {
                    $q->where('slug', '=', 'total_asset_price')->where('value', '<=', $query->total_amount_to);
                }
            })
            ->with('appraisePurpose')
            ->with('appraises');

        if (isset($query->id) && !empty($query->id)) {
            $result = $result->where('id', $query->id);
        }
        if (isset($query->petitioner_name) && !empty($query->petitioner_name)) {
            $query->petitioner_name = trim(mb_strtolower($query->petitioner_name));
            $result = $result->whereRaw('LOWER(petitioner_name) LIKE ?', ['%' . $query->petitioner_name . '%']);
        }
        if (isset($query->ticket_num) && !empty($query->ticket_num)) {
            $result = $result->where('ticket_num', $query->ticket_num);
        }
        if (isset($query->certificate_num) && !empty($query->certificate_num)) {
            $result = $result->where('certificate_num', $query->certificate_num);
        }
        if (isset($query->document_num) && !empty($query->document_num)) {
            $result = $result->where('document_num', $query->document_num);
        }
        if (isset($query->document_date) && !empty($query->document_date)) {
            $result =  $result->where('document_date', date('Y-m-d', strtotime($query->document_date)));
        }
        if (isset($query->certificate_date) && !empty($query->certificate_date)) {
            $result =  $result->where('certificate_date', date('Y-m-d', strtotime($query->certificate_date)));
        }
        if (isset($query->appraise_purpose_id) && !empty($query->appraise_purpose_id)) {
            $result = $result->where('appraise_purpose_id', $query->appraise_purpose_id);
        }
        if (isset($query->status) && !empty($query->status)) {
            $result = $result->where('status', intval($query->status));
        } else if (isset($status)) {
            $result = $result->where('status', intval($status));
        }

        if (isset($query->search) && !empty($query->search)) {
            // $result = $result->where('id', '=', $query->search);
            $search = $query->search;
            $result = $result->where(function ($q) use ($search) {
                $q = $q->where('id', 'like', strval($search));
                $q = $q->orwhere('petitioner_name', 'ILIKE', '%' . $search . '%');
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
            if ($sortOrder == 'descend') {
                $result = $result->orderByDesc($sortField);
            } else {
                $result = $result->orderBy($sortField);
            }
        } else {
            $result = $result->orderByDesc($this->allowedSorts);
        }

        $result = $result
            ->forPage($page, $perPage)
            ->paginate($perPage);

        foreach ($result as $stt => $item) {
            $result[$stt]->append('total_asset_price');
            //$result[$stt]->append('total_asset_price_round');
        }

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
            $result =  $this->model->query()
                ->where('id', '=', $id)
                ->with('businessManager')
                ->with('appraiserSale')
                ->with('appraiserPerform')
                ->with('createdBy')
                ->with('appraisePurpose')

                // ->with('appraises.province')
                // ->with('appraises.district')
                // ->with('appraises.ward')
                // ->with('appraises.street')
                // ->with('appraises.distance')
                // ->with('appraises.assetType')
                // ->with('appraises.pic.picType')
                // ->with('appraises.topographic')
                // ->with('appraises.appraiseApproach')
                // ->with('appraises.appraisePrinciple')
                // ->with('appraises.appraiseMethodUsed')
                // ->with('appraises.appraiseBasisProperty')
                // ->with('appraises.properties.propertyDetail')
                // ->with('appraises.properties.propertyTurningTime')
                // ->with('appraises.properties.propertyTurningTime.material')
                // ->with('appraises.properties.propertyDetail.landTypePurpose')
                // ->with('appraises.properties.propertyDetail.positionType')
                // ->with('appraises.properties.legal')
                // ->with('appraises.properties.zoning')
                // ->with('appraises.properties.landType')
                // ->with('appraises.properties.landShape')
                // ->with('appraises.properties.business')
                // ->with('appraises.properties.electricWater')
                // ->with('appraises.properties.socialSecurity')
                // ->with('appraises.properties.fengShui')
                // ->with('appraises.properties.paymenMethod')
                // ->with('appraises.properties.conditions')
                // ->with('appraises.properties.material')
                // ->with('appraises.tangibleAssets.buildingType')
                // ->with('appraises.tangibleAssets.buildingCategory')
                // ->with('appraises.tangibleAssets.rate')
                // ->with('appraises.tangibleAssets.structure')
                // ->with('appraises.tangibleAssets.crane')
                // ->with('appraises.tangibleAssets.aperture')
                // ->with('appraises.tangibleAssets.factoryType')
                // ->with('appraises.otherAssets')
                // ->with('appraises.constructionCompany')
                // ->with('appraises.appraiseLaw.law')
                // ->with('appraises.appraiseLaw.lawDetails')
                // ->with('appraises.appraiseLaw.lawDetails.landTypePurpose')
                // ->with('appraises.appraiseLaw.landDetails')
                // ->with('appraises.appraiseHasAssets')
                // ->with('appraises.createdBy')
                // ->with('appraises.comparisonFactor')
                // ->with('appraises.appraiseAdapter')
                // ->with('appraises.comparisonTangibleFactor')
                // ->with('appraises.version')
                // ->with('appraises.assetUnitPrice')
                // ->with('appraises.assetUnitPrice.landTypeData')
                // ->with('appraises.assetUnitPrice.createdBy')
                ->with('otherDocuments')
                ->with('otherDocuments.createdBy')
                // ->with('appraises.appraisalMethods')
                ->first();
        }

        // $result->append('round_certificate_total');

        //CommonService::getCertificateAssetPriceTotal($result);

        /* $constructionCompanies = [];
        foreach($result->constructionCompany as $constructionCompany) {
            $constructionCompanies[$constructionCompany->appraise_id]['construction_company'][$constructionCompany->id] = $constructionCompany;
        }
        $result->construction_company_custom = $constructionCompanies;  */
        return $result;
    }

    /**
     * @param $id
     * @return Builder|Model|object
     */
    public function findByIdTest($id)
    {
        return ;
    }

    /**
     * @param $id
     * @return $objects
     */
    public function getConstructionCompany($preCertificateId, $appraiseId): object
    {
        $items = CertificateAssetConstructionCompany::where('pre_certificate_id', '=', $preCertificateId)
            ->where('appraise_id', '=', $appraiseId)
            ->get();
        $result = $items;

        return $result;
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
            //"yeu_to_khac" => 0
        ];
        $items = CertificateAsset::where('id', '=', $id)
            ->with('comparisonFactor')
            //->with('assetGeneral')
            ->get();
        foreach ($items as $stt => $item) {
            $items[$stt]->append('asset_general');
            $items[$stt]->assetGeneral = $items[$stt]->asset_general;
        }

        //$item->assetGeneral = $item->asset_general;
        $result = [];
        $stt=0;
        foreach ($items as $item) {
            if (isset($item->assetGeneral)) {
                foreach ($item->assetGeneral as $index => $assetGeneral) {
                    if (isset($assetGeneral))
                    foreach ($item->comparisonFactor as $comparisonFactor) {
                        if (($comparisonFactor->appraise_id == $id)
                                && ($comparisonFactor->asset_general_id == $assetGeneral->id)
                                && (isset($label[$comparisonFactor->type]))
                        ) {
                            if (($checked[$comparisonFactor->type]) || ((!$comparisonFactor->status) && ('phap_ly' != $comparisonFactor->type))) {
                                if ($checked[$comparisonFactor->type]) {
                                }
                                continue;
                            }
                            $result[$id][] = [
                                "type" => $comparisonFactor->type,
                                "label" => $label[$comparisonFactor->type],
                                "status" => $comparisonFactor->status,
                                "asset_id" => $assetGeneral->id,
                                "asset_general_id" => $comparisonFactor->asset_general_id,
                                "type" => $comparisonFactor->type,
                                "appraise_title" => $comparisonFactor->appraise_title,
                                "asset_title" => $comparisonFactor->asset_title,
                                "description" => $comparisonFactor->description,
                                "adjust_percent" => $comparisonFactor->adjust_percent,
                                "name" => $comparisonFactor->name,
                            ];
                            $checked[$comparisonFactor->type]++;
                        }
                    }
                    if  ($index) continue;
                }
            }
        }
        return $result;
    }
    /**
     * @param $id
     * @return $objects
     */
    public function getComparisonFactorByCertificate($id): array
    {
        $preCertificate = $this->model->query()
            ->where('id', '=', $id)
            ->with('appraises')
            ->first();

        $result = [];
        if (isset($preCertificate->appraises)) {
            foreach ($preCertificate->appraises as $index => $appraise) {
                $appraiseTmp = Appraise::where('id', '=', $appraise->id)
                    ->with('comparisonFactor')
                    //->with('assetGeneral')
                    ->first();
                $appraiseTmp->append('asset_general');
                $appraiseTmp->assetGeneral = $appraiseTmp->asset_general;

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
                    'yeu_to_khac' => 'Yếu tố khác',
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
                    "yeu_to_khac" => 0
                ];

                if (isset($appraiseTmp->assetGeneral)) {
                    foreach ($appraiseTmp->assetGeneral as $index1 => $assetGeneral) {
                        if (isset($appraiseTmp->comparisonFactor)) {
                            foreach ($appraiseTmp->comparisonFactor as $comparisonFactor) {
                                if (($comparisonFactor->appraise_id == $appraiseTmp->id)
                                    && ($comparisonFactor->asset_general_id == $assetGeneral->id)
                                    && (isset($label[$comparisonFactor->type]))
                                ) {
                                    if (($checked[$comparisonFactor->type]) || (!$comparisonFactor->status)) continue;
                                    $result[$index][] = [
                                        "type" => $comparisonFactor->type,
                                        "label" => $label[$comparisonFactor->type],
                                        "status" => $comparisonFactor->status,
                                        "asset_id" => $assetGeneral->id,
                                        "asset_general_id" => $comparisonFactor->asset_general_id,
                                    ];
                                    $checked[$comparisonFactor->type]++;
                                }
                            }
                        }
                    }
                    if  ($index1) continue;
                }
            }
        }

        return $result;
    }

    /**
     * @param array $objects
     * @return int
     * @throws Throwable
     */
    public function createPreCertificate(array $objects)
    {
        return DB::transaction(function () use ($objects) {
            try {
            
                $objects["status"] = 1;

                $objects['created_by'] = is_array($objects['created_by']) ? $objects['created_by']['id'] : $objects['created_by'];

                $objects["updated_at"] = date("Y-m-d H:i:s");
                $preCertificate = new PreCertificate($objects);
                $preCertificateId = QueryBuilder::for(PreCertificate::class)
                    ->insertGetId($preCertificate->attributesToArray());
                return $preCertificateId;
            } catch (Exception $exception) {
                dd($exception);
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
    //public function updatePreCertificate($id, array $objects): int
    public function updatePreCertificate($id, array $objects)
    {
        return DB::transaction(function () use ($id, $objects) {
            try {
                $oldCertificate = PreCertificate::where('id', $id)->first();
                $oldAppraises = $oldCertificate->appraises;
                $oldCertificateAssetIds = [];
                foreach ($oldAppraises as $oldAppraise) {
                    $oldCertificateAssetIds[$oldAppraise->appraise_id] = $oldAppraise->id;
                }

                $objects['created_by'] = is_array($objects['created_by']) ? $objects['created_by']['id'] : $objects['created_by'];

                $objects["updated_at"] = date("Y-m-d H:i:s");
                $preCertificate = new PreCertificate($objects);
                $preCertificateId = $id;
                $preCertificate->newQuery()->updateOrInsert(['id' => $id], $preCertificate->attributesToArray());

                return $preCertificateId;
            } catch (Exception $exception) {
                dd($exception);
                Log::error($exception);
                throw $exception;
            }
        });
    }

    /**
     * @param $id
     * @return void
     */
    public function comparisonFactor($appraiseId, $datas, $edit = false)
    {
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

            "yeu_to_khac"
        ];

        $comparisonFactorInput = [];
        foreach ($datas['comparison_factor'] as $item) {
            switch ($item['label']) {
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
                default:
                    $comparisonFactorInput[] = "yeu_to_khac";
                    break;
            }
        }

        $appraiseRepository = new EloquentCertificateAssetRepository(new CertificateAsset());
        $object = $appraiseRepository->findById($appraiseId);
        $dictionaries = $appraiseRepository->findAllAppraiseDictionaries();

        foreach ($allComparisonFactor as $comparisonFactorTmp) {
            if (in_array($comparisonFactorTmp, $comparisonFactorInput)) {
                CertificateAssetComparisonFactor::where('appraise_id', $appraiseId)
                    ->where('type', $comparisonFactorTmp)
                    ->update(['status' => 1]);
            } else {
                if (($comparisonFactorTmp != "yeu_to_khac") && ($comparisonFactorTmp != "phap_ly")) {
                    CertificateAssetComparisonFactor::where('appraise_id', $appraiseId)
                        ->where('type', $comparisonFactorTmp)
                        ->update(['status' => 0]);
                }
            }
        }
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteCertificate($id)
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
                $assetVersion = ($assetVersion == null) ?  $version : ($assetVersion->id > $version->id ? $assetVersion : $version);
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

    // public function updateSelectComparisonFactor($preCertificateId): bool
    // {
    //     $certificateHasAppraise = CertificateHasAppraise::query()->where('pre_certificate_id', '=', $preCertificateId)->get();
    //     $certificateComparisonFactor = CertificateComparisonFactor::query()->where('pre_certificate_id', '=', $preCertificateId)->get();
    //     foreach ($certificateHasAppraise as $appraise) {
    //         $appraiseComparisonFactor = AppraiseComparisonFactor::query()->where('appraise_id', '=', $appraise->appraise_id)->get();
    //         foreach ($appraiseComparisonFactor as $comparisonFactorAppraise) {
    //             $status = 0;
    //             foreach ($certificateComparisonFactor as $comparisonFactor) {
    //                 if ($comparisonFactor->comparison_factor == 'Pháp lý' && $comparisonFactorAppraise->type == 'phap_ly') {
    //                     $status = 1;
    //                 }
    //                 if ($comparisonFactor->comparison_factor == 'Hình dáng đất' && $comparisonFactorAppraise->type == 'hinh_dang_dat') {
    //                     $status = 1;
    //                 }
    //                 if ($comparisonFactor->comparison_factor == 'Giao thông' && $comparisonFactorAppraise->type == 'ket_cau_duong') {
    //                     $status = 1;
    //                 }
    //                 if ($comparisonFactor->comparison_factor == 'Kinh doanh' && $comparisonFactorAppraise->type == 'kinh_doanh') {
    //                     $status = 1;
    //                 }
    //                 if ($comparisonFactor->comparison_factor == 'Điều kiện hạ tầng' && $comparisonFactorAppraise->type == 'dieu_kien_ha_tang') {
    //                     $status = 1;
    //                 }
    //                 if ($comparisonFactor->comparison_factor == 'An ninh, môi trường sống' && $comparisonFactorAppraise->type == 'an_ninh_moi_truong_song') {
    //                     $status = 1;
    //                 }
    //                 if ($comparisonFactor->comparison_factor == 'Phong thủy' && $comparisonFactorAppraise->type == 'phong_thuy') {
    //                     $status = 1;
    //                 }
    //                 if ($comparisonFactor->comparison_factor == 'Quy mô' && $comparisonFactorAppraise->type == 'quy_mo') {
    //                     $status = 1;
    //                 }
    //                 if ($comparisonFactor->comparison_factor == 'Chiều sâu khu đất' && $comparisonFactorAppraise->type == 'chieu_sau_khu_dat') {
    //                     $status = 1;
    //                 }
    //                 if ($comparisonFactor->comparison_factor == 'Chiều rộng mặt tiền' && $comparisonFactorAppraise->type == 'chieu_rong_mat_tien') {
    //                     $status = 1;
    //                 }
    //                 if ($comparisonFactor->comparison_factor == 'Bề rộng đường' && $comparisonFactorAppraise->type == 'do_rong_duong') {
    //                     $status = 1;
    //                 }

    //                 $comparisonFactorAppraise->status = $status;
    //                 $comparisonFactorId = QueryBuilder::for($comparisonFactorAppraise)
    //                     ->updateOrInsert(['id' => $comparisonFactorAppraise['id']], $comparisonFactorAppraise->attributesToArray());
    //             }
    //         }
    //     }
    //     return true;
    // }

    public function updateTangibleComparisonFactor($id, array $objects): int
    {
        $tangibleComparisonFactor = new CertificateTangibleComparisonFactor($objects);
        $tangibleComparisonFactorId = CertificateTangibleComparisonFactor::query()
            ->updateOrInsert(['id' => $id], $tangibleComparisonFactor->attributesToArray());
        return $id;
    }

    #region version 2
    public function findAllPreCertificate(array $request)
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
        $result = $this->model->with(['createdBy:id,name,image'])
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
            ->select(
                'id',
                'status',
                'created_by',
                'petitioner_name',
                'updated_at',
                DB::raw("case when DATE_PART('day', now() - updated_at) =1
                                        then concat(DATE_PART('day', now() - updated_at) , ' day ago')
                                    when DATE_PART('day', now() - updated_at) >1
                                        then concat(DATE_PART('day', now() - updated_at) , ' days ago')
                                    else
                                        case when DATE_PART('hour', now() - updated_at) =1
                                                then concat(DATE_PART('hour', now() - updated_at) , ' hour ago')
                                            when DATE_PART('hour', now() - updated_at) >1
                                                then concat(DATE_PART('hour', now() - updated_at) , ' hours ago')
                                    end
                     end AS days"),
                DB::raw("concat('HSTD_', id) AS slug")
            )
            ->wherein('status', ['3', '2']);
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
            $result[$stt]->append('certificate_asset_price');
        }
        return $result;
    }

    public function findPaging_v2()
    {
        $user = CommonService::getUser();

        $perPage = (int)request()->get('limit');
        $page = (int)request()->get('page');
        $sortField = request()->get('sortField');
        $sortOrder = request()->get('sortOrder');
        $filter = request()->get('search');
        $status = request()->get('status');
        $betweenTotal = ValueDefault::TOTAL_PRICE_PERCENT;

        $select = [
            'certificates.id',
            'petitioner_name',
            'document_num',
            'appraise_date',
            'document_date',
            'certificate_date',
            'certificate_num',
            'status',
            'certificates.created_at',
            'appraise_purpose_id',
            'created_by',
            'appraiser_id',
            'appraiser_perform_id',
            'appraiser_control_id',
            DB::raw("case status
                    when 1
                        then 'Mới'
                    when 2
                        then 'Đang thẩm định'
                    when 3
                        then 'Đang duyệt'
                    when 4
                        then 'Hoàn thành'
                    when 5
                        then 'Huỷ'
                    when 6
                        then 'Đang kiểm soát'
                end as status_text
            "),
            Db::raw("cast(certificate_prices.value as bigint) as total_price"),
            'commission_fee',
            'document_type',
            'status_expired_at',
            'status_updated_at',
            'sub_status'
        ];
        $with = [
            'createdBy:id,name',
            'appraiser:id,name',
            // 'appraiserManager:id,name',
            // 'appraiserConfirm:id,name',
            // 'appraiserSale:id,name',
            'appraiserPerform:id,name',
            'appraisePurpose:id,name',
            'appraiserControl:id,name',

            // 'appraises:id,appraise_id',
            // 'appraises.appraiseLaw:id,appraise_id',
            // 'appraises.appraiseLaw.landDetails:id,appraise_law_id,doc_no,land_no',

            // 'assetPrice' => function($query){
            //     $query->where('slug','=','total_asset_price')
            //         ->select(['id','pre_certificate_id','slug','value']);
            // },
        ];
        \DB::enableQueryLog();
        $result = QueryBuilder::for($this->model)
            ->with($with)
            ->select($select);

        //// command tạm - sẽ xử lý phân quyền sau
        $role = $user->roles->last();
        // dd($role->name);
        if ($role->name == 'USER') {
            $result = $result->where(function ($query) use ($user) {
                $query = $query->whereHas('createdBy', function ($q) use ($user) {
                    return $q->where('id', $user->id);
                });
                $query = $query->orwhereHas('appraiser', function ($q) use ($user) {
                    return $q->where('user_id', $user->id);
                });
                $query = $query->orwhereHas('appraiserManager', function ($q) use ($user) {
                    return $q->where('user_id', $user->id);
                });
                $query = $query->orwhereHas('appraiserConfirm', function ($q) use ($user) {
                    return $q->where('user_id', $user->id);
                });
                $query = $query->orwhereHas('appraiserSale', function ($q) use ($user) {
                    return $q->where('user_id', $user->id);
                });
                $query = $query->orwhereHas('appraiserPerform', function ($q) use ($user) {
                    return $q->where('user_id', $user->id);
                });
                $query = $query->orwhereHas('appraiserControl', function ($q) use ($user) {
                    return $q->where('user_id', $user->id);
                });
            });
        }

        if (isset($filter) && !empty($filter)) {
            $filterSubstr = substr($filter, 0, 1);
            $filterData = substr($filter, 1);
            switch ($filterSubstr) {
                case '#':
                    $result = $result->where(function ($q) use ($filterData) {
                        $q->whereHas('realEstate', function ($has) use ($filterData) {
                            $has->where('certificate_real_estates.real_estate_id', $filterData);
                        });
                        $q->orWhereHas('personalProperties', function ($has) use ($filterData) {
                            $has->where('certificate_personal_properties.personal_property_id', $filterData);
                        });
                    });
                    break;
                case '!':
                    $result = $result->where(function ($q) use ($filterData) {
                        $q->where('certificate_num', $filterData)
                            ->orWhere('document_num', $filterData);
                    });
                    break;
                case '@':
                    $result = $result->where(function ($q) use ($filterData) {
                        $q = $q->whereHas('createdBy', function ($has) use ($filterData) {
                            $has->where('name', 'ILIKE', '%' . $filterData . '%');
                        });
                    });
                    break;
                case '&':
                    // $data = explode('/',$filterData);
                    // $doc_no = $data[0];
                    // $land_no = isset($data[1]) ? $data[1] : -1;
                    // if(intval($doc_no)>=0 ){
                    //     // return ['message' => 'Sau "&" phải là "số tờ/số thửa". Vui lòng nhập đúng định dạng"', 'exception' => ''];
                    //     $result=$result->where(function ($q) use ($doc_no,$land_no) {
                    //         $q = $q->whereHas('appraises.appraiseLaw.landDetails',function($has) use($doc_no,$land_no){
                    //             $has->where('doc_no', '=', $doc_no );
                    //             if(intval($land_no) >=0)
                    //                 $has=$has->Where('land_no','=',$land_no);
                    //         });
                    //     });
                    // }
                    break;
                case '$':
                    if (floatval($filterData)) {
                        // return ['message' => 'Sau "$" phải là số để tìm kiếm theo tổng giá trị', 'exception' => ''];
                        $fromValue = floatval($filterData) - floatval($filterData) * $betweenTotal;
                        $toValue = floatval($filterData) + floatval($filterData) * $betweenTotal;
                        $result = $result->where(function ($q) use ($fromValue, $toValue) {
                            $q->whereBetween('value', [$fromValue, $toValue]);
                        });
                    }
                    break;
                default:
                    $result = $result->where(function ($q) use ($filter) {
                        $q = $q->where('certificates.id', 'like', strval($filter));
                        $q = $q->orwhere('petitioner_name', 'ILIKE', '%' . $filter . '%');
                    });
            }
        }

        if (!empty($status)) {
            $result = $result->whereIn('status', $status);
        }

        if (isset($sortField) && !isEmpty($sortField)) {
            if ($sortField == 'document_num')
                if ($sortOrder == 'descend')
                    $result =  $result->orderBy('document_num', 'DESC');
                else
                    $result =  $result->orderBy('document_num', 'ASC');
            elseif ($sortField == 'document_date')
                if ($sortOrder == 'descend')
                    $result =  $result->orderBy('document_date', 'DESC');
                else
                    $result =  $result->orderBy('document_date', 'ASC');
            elseif ($sortField == 'certificate_num')
                if ($sortOrder == 'descend')
                    $result =  $result->orderBy('certificate_num', 'DESC');
                else
                    $result =  $result->orderBy('certificate_num', 'ASC');
            elseif ($sortField == 'certificate_date')
                if ($sortOrder == 'descend')
                    $result =  $result->orderBy('certificate_date', 'DESC');
                else
                    $result =  $result->orderBy('certificate_date', 'ASC');
            elseif ($sortField == 'petitioner_name')
                if ($sortOrder == 'descend')
                    $result =  $result->orderBy('petitioner_name', 'DESC');
                else
                    $result =  $result->orderBy('petitioner_name', 'ASC');
            // elseif($sortField=='created_by.name')
            //     if($sortOrder=='descend')
            //         $result=  $result->orderBy('created_by', 'DESC');
            //     else
            //         $result=  $result->orderBy('created_by', 'ASC');
        }

        $result = $result->orderByDesc('certificates.updated_at');

        $result = $result
            ->forPage($page, $perPage)
            ->paginate($perPage);

        foreach ($result as $stt => $item) {
            $result[$stt]->append('detail_list_id');
            // $result[$stt]->append('certificate_asset_price');
        }
        return $result;
    }

    public function getPreCertificateWorkFlow()
    {
        $user = CommonService::getUser();

        $filter = request()->get('search_input');
        $query = request()->get('query');
        $page = request()->get('page');
        $limit = request()->get('limit');

        if (!empty($query)) {
            $query = json_decode($query);
        } else {
            $query = [];
        }
        $betweenTotal = ValueDefault::TOTAL_PRICE_PERCENT;

        $select = [
            'pre_certificates.id', 'status', 'pre_certificates.created_by', 'petitioner_name',
            'pre_certificates.updated_at', 'status_updated_at',
            'business_manager_id', 
            'appraiser_sale_id', 
            'appraiser_perform_id', 
            // 'users.image',
            DB::raw("concat('HSTDSB_', pre_certificates.id) AS slug"),
            DB::raw("case status
                        when 1
                            then 'Mới'
                        when 2
                            then 'Đang thẩm định'
                        when 3
                            then 'Đang duyệt'
                        when 4
                            then 'Hoàn thành'
                        when 5
                            then 'Huỷ'
                        when 6
                            then 'Đang kiểm soát'
                    end as status_text
                "),
            'total_preliminary_value',
            Db::raw("COALESCE(document_count,0) as document_count"),
            'status_expired_at',
            DB::raw("case status
                        when 1
                            then u1.image
                        when 2
                            then u2.image
                        when 3
                            then u2.image
                        when 4
                            then u1.image
                        when 5
                            then u2.image
                        when 6
                            then u1.image
                    end as image
                "),
        ];
        $with = [
            'appraiserSale:id,name,user_id',
            'appraiserPerform:id,name,user_id',
            'appraiserBusinessManager:id,name,user_id',
        ];
        DB::enableQueryLog();
        $result = $this->model->with($with)
            ->leftjoin('users', function ($join) {
                $join->on('pre_certificates.created_by', '=', 'users.id')
                    ->select(['id', 'image'])
                    ->limit(1);
            })
            ->leftjoin(
                DB::raw('(select pre_certificate_id , count(pre_certificate_id) as document_count
                                    from pre_certificate_other_documents
                                    where deleted_at is null
                                    group by pre_certificate_id) as "tbCount"'),
                function ($join) {
                    $join->on('pre_certificates.id', '=', 'tbCount.pre_certificate_id')
                        ->select(['pre_certificate_id', 'document_count']);
                }
            )
            ->leftjoin('appraisers as sale', function ($join) {
                $join->on('sale.id', '=', 'pre_certificates.appraiser_sale_id')
                    ->join('users as u1', function ($j) {
                        $j->on('sale.user_id', '=', 'u1.id');
                    })
                    ->select('u1.image')
                    ->limit(1);
            })
            ->leftjoin('appraisers as perform', function ($join) {
                $join->on('perform.id', '=', 'pre_certificates.appraiser_perform_id')
                    ->join('users as u2', function ($j) {
                        $j->on('perform.user_id', '=', 'u2.id');
                    })
                    ->select('u2.image')
                    ->limit(1);
            })
            ->leftjoin('appraisers as buinesss', function ($join) {
                $join->on('buinesss.id', '=', 'pre_certificates.business_manager_id')
                    ->join('users as u3', function ($j) {
                        $j->on('buinesss.user_id', '=', 'u3.id');
                    })
                    ->select('u3.image')
                    ->limit(1);
            })
            ->select($select);

        //// command tạm - sẽ xử lý phân quyền sau
        $role = $user->roles->last();
        // dd($role->name);
        if ($role->name == 'USER') {
            $result = $result->where(function ($query) use ($user) {
                $query = $query->whereHas('createdBy', function ($q) use ($user) {
                    return $q->where('id', $user->id);
                });
                $query = $query->orwhereHas('appraiserSale', function ($q) use ($user) {
                    return $q->where('user_id', $user->id);
                });
                $query = $query->orwhereHas('appraiserPerform', function ($q) use ($user) {
                    return $q->where('user_id', $user->id);
                });
                $query = $query->orwhereHas('appraiserBusinessManager', function ($q) use ($user) {
                    return $q->where('user_id', $user->id);
                });
            });
        }

        if (isset($query->public_date_from) && !empty($query->public_date_from)) {
            $result =  $result->where('updated_at', '>=', date('Y-m-d', strtotime($query->public_date_from)) . ' 00:00:00');
        }
        if (isset($query->public_date_to) && !empty($query->public_date_to)) {
            $result = $result->where('updated_at', '<=', date('Y-m-d', strtotime($query->public_date_to)) . ' 00:00:00');
        }
        if (isset($filter) && !empty($filter)) {
            $filterSubstr = substr($filter, 0, 1);
            $filterData = substr($filter, 1);
            switch ($filterSubstr) {
                case '!':
                    $result = $result->where(function ($q) use ($filterData) {
                        $q->where('certificate_num', $filterData)
                            ->orWhere('document_num', $filterData);
                    });
                    break;
                case '@':
                    $result = $result->where(function ($q) use ($filterData) {
                        $q = $q->whereHas('createdBy', function ($has) use ($filterData) {
                            $has->where('name', 'ILIKE', '%' . $filterData . '%');
                        });
                    });
                    break;
                case '$':
                    if (floatval($filterData)) {
                        $fromValue = floatval($filterData) - floatval($filterData) * $betweenTotal;
                        $toValue = floatval($filterData) + floatval($filterData) * $betweenTotal;
                        $result = $result->where(function ($q) use ($fromValue, $toValue) {
                            $q->whereBetween('total_preliminary_value', [$fromValue, $toValue]);
                        });
                    }
                    break;
                default:
                    $result = $result->where(function ($q) use ($filter) {
                        $q = $q->where('pre_certificates.id', 'like', strval($filter));
                        $q = $q->orwhere('petitioner_name', 'ILIKE', '%' . $filter . '%');
                    });
            }
        }

        $result = $result->orderByDesc('pre_certificates.updated_at');
        $result= $result->get();
     
        return $result;
    }


    //Step 1 - Post Data
    public function postGeneralInfomation(array $objects, int $id = null)
    {
        DB::beginTransaction();
        try {
            // $check = $this->checkDuplicateData($objects, $id);
            // if (isset($check)) {
            //     return $check;
            // }
            $user = CommonService::getUser();
            $customerId = null;
            if (!empty($objects['customer']['name'])) {
                $customer = Customer::whereName($objects['customer']['name'])
                ->whereAddress($objects['customer']['address'])
                ->wherePhone($objects['customer']['phone'])
                ->first();
                if (isset($customer)) {
                    $customerId = $customer->id;
                } else {
                    $customerId = Customer::insertGetId([
                        'name' => $objects['customer']['name'],
                        'address' => $objects['customer']['address'],
                        'phone' => $objects['customer']['phone'],
                        'status' => ValueDefault::ACTIVE_STATUS,
                        'created_by' => $user->id,
                        'created_date' => date('d/m/Y'),
                    ]);
                }
            }

            $branch_id = null;
            if (isset( $objects['appraiser_sale_id'])) {
                $branch_id = Appraiser::query()->where('id', $objects['appraiser_sale_id'])->first()->branch_id;
            } else {
                $branch_id = Appraiser::query()->where('user_id', $user->id)->first()->branch_id;
            }
            
            $data = $objects;
            // dd('note', $data);
            $data['customer_id'] = $customerId;
            if (isset($id)) {
                $check = $this->beforeSave($id);
                if (isset($check)) {
                    return $check;
                }
                $oldCertificate = PreCertificate::where('id', '=', $id)->first();
                if (!isset($oldCertificate)) {
                    $data = ['message' => ErrorMessage::CERTIFICATE_NOTEXISTS . $id, 'exception' =>  ''];
                    return $data;
                }
                $preCertificateId = $id;
                $status = $oldCertificate->status;
                if (!isset($oldCertificate['created_by']))
                    $data['created_by'] = $user->id;

                $certificateArr = new PreCertificate($data);
                PreCertificate::where('id', $preCertificateId)->update($certificateArr->attributesToArray());
                $edited = PreCertificate::where('id', $preCertificateId)->first();
                $changeLog = $edited->fill($data);
                $edited->save();
                $this->CreateActivityLog($edited, $changeLog, 'update_data', 'cập nhật dữ liệu');
            } else {
                $data['created_by'] = $user->id;
                // $curDate = \Carbon\Carbon::now('Asia/Ho_Chi_Minh');
                $PROCESSING_TIME = CertificateDictionary::where(['type' => 'PROCESSING_TIME', 'acronym' => 'MOI'])->get('description')->first();
                $minutes = intval($PROCESSING_TIME->description);
                $status_expired_at = \Carbon\Carbon::now('Asia/Ho_Chi_Minh')->addMinutes($minutes)->format('Y-m-d H:i');
                $data['status_expired_at'] = $status_expired_at;
                $certificateArr = new PreCertificate($data);
                // dd($certificateArr);
                $certificateCreate = PreCertificate::query()->create($certificateArr->attributesToArray());
                $preCertificateId = $certificateCreate->id;
                // $this->saveMethod($preCertificateId);
                # Activity Log "create if id = null"
                $edited = PreCertificate::where('id', $preCertificateId)->first();
                $this->CreateActivityLog($edited, $edited, 'create', 'tạo mới');
                // $this->updateDocumentDescription($preCertificateId);
            }
            DB::commit();
            return [
                'id' => $preCertificateId,
                'status' => $data['status'],
            ];
        } catch (Exception $ex) {
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' =>  $ex->getMessage()];
            Log::error($ex);
            return $data;
            DB::rollBack();
        }
    }

    public function getGeneralInfomation(int $id)
    {
        $result = [];
        $check = $this->checkAuthorizationPreCertificate($id);
        if (!empty($check))
            return $check;

        $select = [
            'id',
            'petitioner_name',
            'petitioner_phone',
            'petitioner_identity_card',
            'petitioner_address',
            'appraiser_confirm_id',
            'appraiser_manager_id',
            'appraiser_control_id',
            'appraise_purpose_id',
            'document_num',
            'document_date',
            'appraise_date',
            'service_fee',
            'appraiser_sale_id',
            'appraiser_perform_id',
            'certificate_date',
            'certificate_num',
            'customer_id',
            'status',
            'commission_fee',
            'note',
            'status_expired_at',
            'created_by',
            'document_type',
        ];
        $with = [
            'appraiser:id,name,user_id',
            'appraiserManager:id,name,user_id',
            'appraiserConfirm:id,name,user_id',
            'appraiserControl:id,name,user_id',
            'appraiserSale:id,name,user_id',
            'appraiserPerform:id,name,user_id',
            'appraisePurpose:id,name,user_id',
            'customer:id,name,phone,address',

        ];
        $result = $this->model->query()
            ->with($with)
            ->where('id', $id)
            ->select($select)
            ->first();
        $result->append(['status_text']);


        return $result;
    }

    public function getPreCertificate(int $id)
    {
        // dd('vô tới đây');
        $result = [];
        $check = $this->checkAuthorizationPreCertificate($id);
        if (!empty($check))
            return $check;
        // dd($check);
        $select = [
            'id',
            'certificate_id',
            'petitioner_name',
            'petitioner_phone',
            'petitioner_identity_card',
            'petitioner_address',
            'customer_id',
            'appraise_purpose_id',
            'note',
            'appraiser_sale_id',
            'business_manager_id',
            'appraiser_perform_id',
            'total_preliminary_value',
            'cancel_reason',
            'status_updated_at',
            'created_at',
            'created_by',
            'updated_at',
            'updated_by',
            'status',
            'deleted_at',
            'status_expired_at',
        ];
        $with = [
            'appraiserSale:id,name,user_id',
            'appraiserPerform:id,name,user_id',
            'appraiserBusinessManager:id,name,user_id',
            'appraisePurpose:id,name',
            'customer:id,name,phone,address',
            'otherDocuments',
            'createdBy:id,name',
        ];
        // dd($this->model->query()->with($with)->where('id', $id)->select($select)->first());
        $result = $this->model->query()
            ->with($with)
            ->where('id', $id)
            ->select($select)
            ->first();
        // dd($result);
        // $result->append(['status_text', 'general_asset']);
        // $result['checkVersion'] = AppraiseVersionService::checkVersionByCertificate($id);
        // if ($result['status'] == 5) {
        //     $user = User::query()
        //     ->where('id', '=', $result['created_by'])
        //     ->first();
        //     $result['image'] = $user->image;
        // }
        if ($result['status'] == 1) {
            $appraiser = Appraiser::query()
            ->where('id', '=', $result['appraiser_sale_id'])
            ->first();
            $user = User::query()
            ->where('id', '=', $appraiser->user_id)
            ->first();
            $result['image'] = $user->image;
        }
        if ($result['status'] == 2) {
            $appraiser = Appraiser::query()
            ->where('id', '=', $result['appraiser_perform_id'])
            ->first();
            $user = User::query()
            ->where('id', '=', $appraiser->user_id)
            ->first();
            $result['image'] = $user->image;
        }
        if ($result['status'] == 3 || $result['status'] == 4) {
            $appraiser = Appraiser::query()
            ->where('id', '=', $result['appraiser_perform_id'])
            ->first();
            $user = User::query()
            ->where('id', '=', $appraiser->user_id)
            ->first();
            $result['image'] = $user->image;
        }
        if ($result['status'] == 6) {
            $appraiser = Appraiser::query()
            ->where('id', '=', $result['business_manager_id'])
            ->first();
            $user = User::query()
            ->where('id', '=', $appraiser->user_id)
            ->first();
            $result['image'] = $user->image;
        }

        return $result;
    }

    private function checkExistsAppraise(int $id)
    {
        $result = false;
        $preCertificate = $this->model->query()->where('id', $id)->first();
        if (isset($preCertificate)){
            $result = true;
        }
        // $documentType = $preCertificate->document_type;
        // if (!isset($documentType)) {
        //     $documentType[0] = 'BDS';
        // }
        // if ($documentType[0] == 'BDS') {
        //     if (CertificateHasAppraise::where('pre_certificate_id', $id)->exists()) {
        //         $result = true;
        //     }
        // } else {
        //     if (CertificateHasPersonalProperty::where('pre_certificate_id', $id)->exists()) {
        //         $result = true;
        //     }
        // }
        return $result;
    }

    private function updateAppraiseStatus($preCertificateId, $status, $subStatus) {
        $preCertificate = $this->model->query()->with(['realEstate', 'personalProperties'])->where('id', $preCertificateId)->first(['id', 'status', 'sub_status']);
        $realEstate = $preCertificate->realEstate;
        $personalProperties = $preCertificate->personalProperties;
        $updateData = [
            'status' => $status,
            'sub_status' => $subStatus
        ];
        if (!empty($realEstate)) {
            $assetTypeCC = Dictionary::query()->where(['type' => 'LOAI_TAI_SAN', 'acronym' => 'CC'])->first('id')->id;
            foreach ($realEstate as $item) {
                if (!empty($assetTypeCC) && $item->asset_type_id == $assetTypeCC ) {
                    ApartmentAsset::query()->where('id', $item->real_estate_id)->update($updateData);
                } else {
                    Appraise::query()->where('id', $item->real_estate_id)->update($updateData);
                }
                RealEstate::query()->where('id', $item->real_estate_id)->update($updateData);
            }
        }
        if (!empty($personalProperties)) {
            $personalRepository = new EloquentPersonalPropertiesRepository(new PersonalProperty());
            foreach ($personalProperties as $item) {
                $personalRepository->updateStatus($item->personal_property_id, $status, $subStatus);
            }
        }
    }
    public function updateStatus_v2($id, $request)
    {
        return DB::transaction(function () use ($id, $request) {
            try {
                $result = [];
                // # đang tắt khối block xác thực
                $check = $this->beforeUpdateStatus($id);
                if (isset($check)) {
                    return $check;
                }
                $preCertificate = $this->model->query()->where('id', $id)->first();
                $currentStatus = $preCertificate->status;
                $currentSubStatus = $preCertificate->sub_status;
                $current = intval($currentStatus . $currentSubStatus);
                $currentConfig = current(array_filter($request['status_config'], function ($val) use ($currentStatus, $currentSubStatus) {
                    return $val['status'] == $currentStatus && $val['sub_status'] == $currentSubStatus;
                }));
                $status = $request['status'];
                $subStatus = $request['sub_status'];
                $next = intval($status . $subStatus);
                $nextConfig = current(array_filter($request['status_config'], function ($val) use ($status, $subStatus) {
                    return $val['status'] == $status && $val['sub_status'] == $subStatus;
                }));
                $status_expired_at = isset($request['status_expired_at']) ? \Carbon\Carbon::createFromFormat('d-m-Y H:i', $request['status_expired_at'])->format('Y-m-d H:i') : null;

                if (isset($status) && isset($subStatus)) {
                    switch($status)  {
                        case 1: //Move to first step in workflow -> remove all asset in preCertificate
                            // $this->updateAppraiseStatus($id, $baseStatus, $baseSubStatus);
                            $this->removeAssetInCertificate($id);
                            break;
                        default:
                            $this->updateAppraiseStatus($id, $status, $subStatus);
                    }
                    $result = $this->model->query()
                        ->where('id', '=', $id)
                        ->update([
                            'status' => $status,
                            'sub_status' => $subStatus,
                            'status_updated_at' => date('Y-m-d H:i:s'),
                            'status_expired_at' => $status_expired_at,
                        ]);

                    # Chuyển status từ số sang text
                    $edited = PreCertificate::where('id', $id)->first();
                    if ($current > $next) {
                        // $logDescription = $request['status_description'] . ' '.  $request['status_config']['description'];
                        $description = $currentConfig !== false ? $currentConfig['description'] : '';
                        $logDescription = 'từ chối ' .  $description;
                    }
                    else {
                        $description = $nextConfig !== false ? $nextConfig['description'] : '';
                        $logDescription = 'cập nhật trạng thái '. $description;
                    }
                    $logName = 'update_status';
                    // activity-log Update status
                    $note = $request['status_note'] ?? '';
                    $reason_id = $request['status_reason_id'] ?? null;
                    $this->CreateActivityLog($edited, $edited, $logName, $logDescription, $note, $reason_id);

                    $this->notifyChangeStatus($id, $status);
                }
                // $result = $this->getAppraisalTeam($id);
                $result = $this->getPreCertificate($id);

                return $result;
            } catch (Exception $exception) {
                Log::error($exception);
                throw $exception;
            }
        });
    }

    private function sqlRealEstate($realEstateIds, $assetTypeIds, $where , $perPage, $page = 1)
    {
        $select = [
            'real_estates.id',
            'real_estates.updated_at',
            'asset_type_id',
            'real_estates.created_at',
            DB::raw("appraise_asset as name, total_area,
                coalesce(case
                    when  round_total > 0
                    then ceil(total_price / power(10, round_total)) * power(10, round_total)
                    when   round_total < 0
                        then floor( total_price * abs(power(10, round_total))  ) / abs(power(10, round_total))
                    else
                        total_price
                end, 0) as total_price
                "),
            'users.name as created_by.name'
        ];
        $result = RealEstate::query()
            ->select($select)
            ->join('users', 'users.id', '=', 'real_estates.created_by')
            ->where($where)
            ->whereNull('pre_certificate_id');

        if (!empty($realEstateIds) && ($page == 1)) {
            $result = $result->orWhereIn('real_estates.id', $realEstateIds);
        }

        return $result;
    }
    private function sqlPersonalProperty($propertyIds, $assetTypeIds, $where, $perPage, $page = 1)
    {
        $select = [
            'personal_properties.id',
            'personal_properties.updated_at',
            'asset_type_id',
            'personal_properties.created_at',
            'personal_properties.name as name',
            DB::raw("0 as total_area"),
            'total_price',
            'users.name as created_by.name'
        ];

        $result = PersonalProperty::query()
            ->select($select)
            ->join('users', 'users.id', '=', 'personal_properties.created_by')
            ->where($where)
            ->whereNull('pre_certificate_id');

        if (!empty($propertyIds) && ($page == 1)) {
            $result = $result->orWhereIn('personal_properties.id', $propertyIds);
        }
        return $result;
    }
    public function findAppraisePaging(): LengthAwarePaginator
    {
        $user = CommonService::getUser();
        $perPage = (int)request()->get('limit');
        $page = (int)request()->get('page');
        $status = request()->get('status');
        $preCertificateId = request()->get('pre_certificate_id');
        $preCertificate = PreCertificate::where('id', $preCertificateId)->first();
        $realEstateIds = [];
        $propertyIds = [];
        if ($page == 1) {
            $realEstates = $preCertificate->realEstate;
            foreach ($realEstates as $realEstate) {
                $realEstateIds[] = $realEstate->real_estate_id;
            }
            $personals = $preCertificate->personalProperties;
            foreach ($personals as $personal) {
                $propertyIds[] = $personal->personal_property_id;
            }
        }
        $where = ['created_by' => $user->id, 'status' => $status];
        $result = null;

        $realEstateTyeIds = [];
        $sqlRealEstate = $this->sqlRealEstate($realEstateIds, $realEstateTyeIds, $where, $perPage, $page);

        $personalTypeIds = [];
        $sqlPersonal = $this->sqlPersonalProperty($propertyIds, $personalTypeIds, $where, $perPage, $page);

        $result = $sqlRealEstate->union($sqlPersonal);

        $query =  DB::query()->fromSub($result, 'assets')
        ->select('assets.*', 'dictionaries.description as asset_type.description')
        ->join('dictionaries', 'dictionaries.id', '=', 'assets.asset_type_id');

        $paginated_data = $query
        ->orderByDesc('assets.updated_at')
        ->paginate($perPage);
        return $paginated_data;
    }

    private function saveDocumentLaw(int $preCertificateId, array $realEstateList, string $type)
    {
        if (isset($realEstateList)) {
            $provine = [];
            if ($type == 'apartment') {
                foreach ($realEstateList as $realEstateId) {
                    $data = ApartmentAsset::with('province:id,name')->where('real_estate_id', $realEstateId)->select('province_id')->first();
                    $provine[] = $data['province']['name']??'Tất cả';
                    // dd($realEstateId,$data);
                }
            } else {
                foreach ($realEstateList as $realEstateId) {
                    $data = Appraise::with('province:id,name')->where('real_estate_id', $realEstateId)->select('province_id')->first();
                    $provine[] = $data['province']['name']??'Tất cả';
                }
            }
            $provine[] = 'Tất cả';
            $lawDocument = AppraiseLawDocument::whereIn('provinces', $provine)->orderBy('position')->get();
            CertificateLegalDocumentsOnValuation::query()->where('pre_certificate_id', '=', $preCertificateId)->forceDelete();
            CertificateLegalDocumentsOnConstruction::query()->where('pre_certificate_id', '=', $preCertificateId)->forceDelete();
            CertificateLegalDocumentsOnLand::query()->where('pre_certificate_id', '=', $preCertificateId)->forceDelete();
            CertificateLegalDocumentsOnLocal::query()->where('pre_certificate_id', '=', $preCertificateId)->forceDelete();

            if (isset($lawDocument)) {
                foreach ($lawDocument as $law) {
                    if ($law['type'] == 'XAY_DUNG') {
                        $data = [];
                        $data['pre_certificate_id'] = $preCertificateId;
                        $data['certificate_law_id'] = $law['id'];
                        $insertData = new CertificateLegalDocumentsOnConstruction($data);
                        QueryBuilder::for($insertData)
                            ->insert($insertData->attributesToArray());
                    } elseif ($law['type'] == 'DIA_PHUONG') {
                        $data = [];
                        $data['pre_certificate_id'] = $preCertificateId;
                        $data['certificate_law_id'] = $law['id'];
                        $insertData = new CertificateLegalDocumentsOnLocal($data);
                        QueryBuilder::for($insertData)
                            ->insert($insertData->attributesToArray());
                    } elseif ($law['type'] == 'THAM_DINH_GIA') {
                        $data = [];
                        $data['pre_certificate_id'] = $preCertificateId;
                        $data['certificate_law_id'] = $law['id'];
                        $insertData = new CertificateLegalDocumentsOnValuation($data);
                        QueryBuilder::for($insertData)
                            ->insert($insertData->attributesToArray());
                    } elseif ($law['type'] == 'DAT_DAI') {
                        $data = [];
                        $data['pre_certificate_id'] = $preCertificateId;
                        $data['certificate_law_id'] = $law['id'];
                        $insertData = new CertificateLegalDocumentsOnLand($data);
                        QueryBuilder::for($insertData)
                            ->insert($insertData->attributesToArray());
                    }
                }
            }
            // $lawConstruction = AppraiseLawDocument::where('type','=','XAY_DUNG')
            // ->whereIn('provinces',$provine)
            // ->get();s
            // $lawConstruction = array_filter($appraiseLawDocument,array_colu 'XAY_DUNG');
            // $lawConstruction = array_filter(array($appraiseLawDocument), function ($var) {
            //     return ($var['type'] = 'XAY_DUNG');
            // });

            // dd($lawConstruction);
        }
        // return $appraiseLawDocument;
    }

    private function saveMethod(int $preCertificateId)
    {
        if (isset($preCertificateId)) {
            if (
                CertificateMethodUsed::query()->where('pre_certificate_id', '=', $preCertificateId)->doesntExist()
                || CertificateBasisProperty::query()->where('pre_certificate_id', '=', $preCertificateId)->doesntExist()
                || CertificatePrinciple::query()->where('pre_certificate_id', '=', $preCertificateId)->doesntExist()
                || CertificateApproach::query()->where('pre_certificate_id', '=', $preCertificateId)->doesntExist()
            ) {
                CertificateMethodUsed::query()->where('pre_certificate_id', '=', $preCertificateId)->forceDelete();
                CertificateBasisProperty::query()->where('pre_certificate_id', '=', $preCertificateId)->forceDelete();
                CertificatePrinciple::query()->where('pre_certificate_id', '=', $preCertificateId)->forceDelete();
                CertificateApproach::query()->where('pre_certificate_id', '=', $preCertificateId)->forceDelete();
                $otherDocument = AppraiseOtherInformation::where('is_defaults', true)->get();
                if (isset($otherDocument)) {
                    foreach ($otherDocument as $other) {
                        if ($other['type'] == 'CO_SO_THAM_DINH') {
                            $data = [];
                            $data['pre_certificate_id'] = $preCertificateId;
                            $data['certificate_basis_property_id'] = $other['id'];
                            $insertData = new CertificateBasisProperty($data);
                            QueryBuilder::for($insertData)
                                ->insert($insertData->attributesToArray());
                        } elseif ($other['type'] == 'NGUYEN_TAC_THAM_DINH') {
                            $data = [];
                            $data['pre_certificate_id'] = $preCertificateId;
                            $data['certificate_principle_id'] = $other['id'];
                            $insertData = new CertificatePrinciple($data);
                            QueryBuilder::for($insertData)
                                ->insert($insertData->attributesToArray());
                        } elseif ($other['type'] == 'CACH_TIEP_CAN_CHI_PHI') {
                            $data = [];
                            $data['pre_certificate_id'] = $preCertificateId;
                            $data['certificate_approach_id'] = $other['id'];
                            $insertData = new CertificateApproach($data);
                            QueryBuilder::for($insertData)
                                ->insert($insertData->attributesToArray());
                        } elseif ($other['type'] == 'PHUONG_PHAP_THAM_DINH_SU_DUNG') {
                            $data = [];
                            $data['pre_certificate_id'] = $preCertificateId;
                            $data['certificate_method_used_id'] = $other['id'];
                            $insertData = new CertificateMethodUsed($data);
                            QueryBuilder::for($insertData)
                                ->insert($insertData->attributesToArray());
                        }
                    }
                }
            }
        }
    }

    private function updateDocumentDescription(int $preCertificateId)
    {
        // ApartmentAssetAppraisalBase
        if (PreCertificate::where('id', $preCertificateId)->exists()) {
            // $cert = PreCertificate::where('id', $preCertificateId)->first()->toArray();
            // if ($cert['document_type'] && $cert['document_type'][0] == 'CC'){
            //     $ccu =  RealEstate::where('pre_certificate_id', $preCertificateId)->first()->toArray();
            //     $apartment = ApartmentAsset::query()->where('real_estate_id', $ccu['id'])->first()->toArray();
            //     $apartmentId = $apartment['id'];
            //     $bases = ApartmentAssetAppraisalBase::query()->where('apartment_asset_id', $apartmentId)->first()->toArray();
            //     PreCertificate::where('id', $preCertificateId)
            //     ->update(['document_description' => $bases['description']]);
            // } else {
            //     PreCertificate::where('id', $preCertificateId)
            //     ->update(['document_description' => ValueDefault::CERTIFICATE_DESCRIPTION]);
            // }
            PreCertificate::where('id', $preCertificateId)
                ->update(['document_description' => ValueDefault::CERTIFICATE_DESCRIPTION]);
        }
    }

    private function saveConstructionCompany(int $preCertificateId, int $certificateAppraiseId, int $appraiseId, int $justDelete = 0, int $appraise_tangible_id = null, int $certificate_tangbile_id = null)
    {
        if ($justDelete == 1) {
            CertificateAssetConstructionCompany::query()->where('pre_certificate_id', $preCertificateId)->where('appraise_id', $certificateAppraiseId)->forceDelete();
            return;
        }
        if (isset($appraise_tangible_id)) {
            $where = ['appraise_id' => $appraiseId, 'tangible_asset_id' => $appraise_tangible_id];
        } else {
            $where = ['appraise_id' => $appraiseId];
        }

        $data = ConstructionCompany::where($where)->get();
        if (isset($data)) {
            foreach ($data as $item) {
                $item['pre_certificate_id'] = $preCertificateId;
                $item['appraise_id'] = $certificateAppraiseId;
                $item['company_id'] = $item['construction_company_id'];
                $item['name'] = isset($item['name']) ? $item['name'] : '';
                $item['address'] = isset($item['address']) ? $item['address'] : '';
                $item['phone_number'] = isset($item['phone_number']) ? $item['phone_number'] : '';
                $item['manager_name'] = isset($item['manager_name']) ? $item['manager_name'] : '';
                $item['unit_price_m2'] = isset($item['unit_price_m2']) ? $item['unit_price_m2'] : 0;
                $item['is_defaults'] = isset($item['is_defaults']) ? $item['is_defaults'] : true;
                $item['tangible_asset_id'] = $certificate_tangbile_id;
                // $item['tangible_asset_id'] = $item['tangible_asset_id'];

                // dd(array($item));
                $certificateConstruction = new CertificateAssetConstructionCompany(json_decode(json_encode($item), true));
                // dd( $certificateConstruction);
                $id =   QueryBuilder::for($certificateConstruction)
                    ->insertGetId($certificateConstruction->attributesToArray());
            }
        }
    }


    public function updatePreCertificateVersion ($preCertificateId, $object)
    {
        try {
            DB::beginTransaction();
            if (!empty($object['general_asset'])) {
                $appraiseType = Dictionary::query()->whereIn('acronym', ['DCN', 'DT', 'CC'])->get()->toArray();
                $appraiseTypeIds = Arr::pluck($appraiseType, 'id');
                foreach ($object['general_asset'] as $item) {
                    if (array_search($item['asset_type_id'], $appraiseTypeIds) !==false) {
                        $certificateRealEstateId = $item['real_estate_id'];
                        $realEstateId = $item['general_asset_id'];
                        $realEstate = RealEstate::query()->where('id', $realEstateId)->first();
                        $certificateRealEstate = CertificateRealEstate::query()->find($certificateRealEstateId);
                        $certificateRealEstate->update($realEstate->toArray());
                        // dd($realEstate->apartment);
                        if (!empty($realEstate->appraises)) {
                            $this->insertAppraiseData($realEstateId, $certificateRealEstateId, $preCertificateId);
                        }
                        if (!empty($realEstate->apartment)) {
                            $this->insertApartmentData($realEstateId, $certificateRealEstateId, $preCertificateId);
                        }
                    }
                }
            }
            CommonService::getCertificateAssetPriceTotal_v2($preCertificateId);
            // $this->updateTotalPrie($preCertificateId);
            DB::commit();
            $preCertificate = $this->getCertificateAppraise($preCertificateId);
            return $preCertificate;
        } catch (Exception $ex) {
            DB::rollBack();
            return ['message' => $ex->getMessage(), 'exception' => ''];
        }
    }
    private function insertAppraiseData($realEstateId, int $certificateRealEstateId, int $preCertificateId)
    {
        if (CertificateAsset::query()->where('real_estate_id', $certificateRealEstateId)->exists()) {
            CertificateAsset::query()->where('real_estate_id', $certificateRealEstateId)->forceDelete();
        }
        if (CertificateHasAppraise::query()->where('pre_certificate_id', $preCertificateId)->exists()) {
            CertificateHasAppraise::query()->where('pre_certificate_id', $preCertificateId)->forceDelete();
        }
        $appraise = Appraise::query()->where('real_estate_id', $realEstateId)->first()->toArray();
        $appraiseId = $appraise['id'];
        $appraise['real_estate_id'] = $certificateRealEstateId;
        $appraise['appraise_id'] = $appraiseId;
        //appraise
        $appraiseData = new CertificateAsset($appraise);
        $certificateAppraise=  $appraiseData->newQuery()->create($appraiseData->attributesToArray());
        $certificateAssetId = $certificateAppraise->id;
        $appraise['pre_certificate_id'] = $preCertificateId;
        $appraise['appraise_id'] = $certificateAssetId;
        $appraise['version'] = '1.0';
        $appraiseData = new CertificateHasAppraise($appraise);
        QueryBuilder::for($appraiseData)
            ->insertGetId($appraiseData->attributesToArray());
        // dd($certificateAssetId);
        $itemDatas = AppraiseHasAsset::where('appraise_id', $appraiseId)->get();
        // dd($itemDatas);
        foreach ($itemDatas as $itemData) {
            if (isset($itemData)) {
                $itemData->appraise_id = $certificateAssetId;
                $item = new CertificateAssetHasAsset($itemData->toArray());
                $itemId = QueryBuilder::for($item)->insertGetId($item->attributesToArray());
                $certificateAssetGeneralId = $item->asset_general_id;

                $appraiseComparisonFactors = AppraiseComparisonFactor::where('appraise_id', $appraiseId)
                    ->where('asset_general_id', $itemData->asset_general_id)
                    ->get();

                foreach ($appraiseComparisonFactors as $appraiseComparisonFactorData) {
                    if (isset($appraiseComparisonFactorData)) {
                        $appraiseComparisonFactorData->appraise_id = $certificateAssetId;
                        $appraiseComparisonFactorData->asset_general_id = $certificateAssetGeneralId;
                        $appraiseComparisonFactor = new CertificateAssetComparisonFactor($appraiseComparisonFactorData->toArray());
                        $appraiseComparisonFactorId = QueryBuilder::for($appraiseComparisonFactor)->insertGetId($appraiseComparisonFactor->attributesToArray());
                    }
                }

                $appraiseAdapters = AppraiseAdapter::where('appraise_id', $appraiseId)
                    ->where('asset_general_id', $itemData->asset_general_id)
                    ->get();
                foreach ($appraiseAdapters as $appraiseAdapterData) {
                    if (isset($appraiseAdapterData)) {
                        $appraiseAdapterData->appraise_id = $certificateAssetId;
                        $appraiseAdapterData->asset_general_id = $certificateAssetGeneralId;
                        $appraiseAdapter = new CertificateAssetAdapter($appraiseAdapterData->toArray());
                        $appraiseAdapterId = QueryBuilder::for($appraiseAdapter)->insertGetId($appraiseAdapter->attributesToArray());
                    }
                }
            }
        }

        $oldAppraiseLawIds = [];
        $oldCertificateAssetLawIds = [];
        $itemDatas = AppraiseLaw::where('appraise_id', $appraiseId)->get();
        foreach ($itemDatas as $itemData) {
            if (isset($itemData)) {
                $itemData->appraise_id = $certificateAssetId;
                $item = new CertificateAssetLaw($itemData->toArray());
                $itemId = QueryBuilder::for($item)->insertGetId($item->attributesToArray());
                $oldAppraiseLawIds[] = $itemData->id;
                $oldCertificateAssetLawIds[$itemData->id] = $itemId;
            }
        }
        foreach ($oldAppraiseLawIds as $oldAppraiseLawId) {
            $itemDatas = AppraiseLawDetail::where('appraise_law_id', $oldAppraiseLawId)->get();
            foreach ($itemDatas as $itemData) {
                if (isset($itemData)) {
                    $itemData->appraise_law_id = $oldCertificateAssetLawIds[$oldAppraiseLawId];
                    $item = new CertificateAssetLawDetail($itemData->toArray());
                    $itemId = QueryBuilder::for($item)->insertGetId($item->attributesToArray());
                }
            }
        }
        foreach ($oldAppraiseLawIds as $oldAppraiseLawId) {
            $itemDatas = AppraiseLawLandDetail::where('appraise_law_id', $oldAppraiseLawId)->get();
            foreach ($itemDatas as $itemData) {
                if (isset($itemData)) {
                    $itemData->appraise_law_id = $oldCertificateAssetLawIds[$oldAppraiseLawId];
                    $item = new CertificateAssetLawLandDetail($itemData->toArray());
                    $itemId = QueryBuilder::for($item)->insertGetId($item->attributesToArray());
                }
            }
        }

        $appraiseUnitPriceDatas = AppraiseUnitPrice::where('appraise_id', $appraiseId)->get();
        CertificateAssetUnitPrice::where('pre_certificate_id', $preCertificateId)
            ->where('appraise_id', $certificateAssetId)
            ->forceDelete();
        foreach ($appraiseUnitPriceDatas as $itemData) {
            if (isset($itemData)) {
                $itemData->pre_certificate_id = $preCertificateId;
                $itemData->appraise_id = $certificateAssetId;
                $item = new CertificateAssetUnitPrice($itemData->toArray());
                $itemId = QueryBuilder::for($item)->insertGetId($item->attributesToArray());
            }
        }

        $appraiseUnitPriceDatas = AppraiseUnitArea::where('appraise_id', $appraiseId)->get();
        CertificateAssetUnitArea::where('pre_certificate_id', $preCertificateId)
            ->where('appraise_id', $certificateAssetId)
            ->forceDelete();
        foreach ($appraiseUnitPriceDatas as $itemData) {
            if (isset($itemData)) {
                $itemData->pre_certificate_id = $preCertificateId;
                $itemData->appraise_id = $certificateAssetId;
                $item = new CertificateAssetUnitArea($itemData->toArray());
                $itemId = QueryBuilder::for($item)->insertGetId($item->attributesToArray());
            }
        }

        $itemDatas = AppraiseLegalDocumentsOnConstruction::where('appraise_id', $appraiseId)->get();
        foreach ($itemDatas as $itemData) {
            if (isset($itemData)) {
                $itemData->appraise_id = $certificateAssetId;
                $item = new CertificateAssetLegalDocumentsOnConstruction($itemData->toArray());
                $itemId = QueryBuilder::for($item)->insertGetId($item->attributesToArray());
            }
        }

        $itemDatas = AppraiseLegalDocumentsOnLand::where('appraise_id', $appraiseId)->get();
        foreach ($itemDatas as $itemData) {
            if (isset($itemData)) {
                $itemData->appraise_id = $certificateAssetId;
                $item = new CertificateAssetLegalDocumentsOnLand($itemData->toArray());
                $itemId = QueryBuilder::for($item)->insertGetId($item->attributesToArray());
            }
        }

        $itemDatas = AppraiseLegalDocumentsOnLocal::where('appraise_id', $appraiseId)->get();
        foreach ($itemDatas as $itemData) {
            if (isset($itemData)) {
                $itemData->appraise_id = $certificateAssetId;
                $item = new CertificateAssetLegalDocumentsOnLocal($itemData->toArray());
                $itemId = QueryBuilder::for($item)->insertGetId($item->attributesToArray());
            }
        }

        $itemDatas = AppraiseLegalDocumentsOnValuation::where('appraise_id', $appraiseId)->get();
        foreach ($itemDatas as $itemData) {
            if (isset($itemData)) {
                $itemData->appraise_id = $certificateAssetId;
                $item = new CertificateAssetLegalDocumentsOnValuation($itemData->toArray());
                $itemId = QueryBuilder::for($item)->insertGetId($item->attributesToArray());
            }
        }

        $itemDatas = AppraisePic::where('appraise_id', $appraiseId)->get();
        foreach ($itemDatas as $itemData) {
            if (isset($itemData)) {
                $itemData->appraise_id = $certificateAssetId;
                $item = new CertificateAssetPic($itemData->toArray());
                $itemId = QueryBuilder::for($item)->insertGetId($item->attributesToArray());
            }
        }

        $appraisePropertyDatas = AppraiseProperty::where('appraise_id', $appraiseId)->get();
        foreach ($appraisePropertyDatas as $appraisePropertyData) {
            if (isset($appraisePropertyData)) {
                $appraisePropertyData->appraise_id = $certificateAssetId;
                $property = new CertificateAssetProperty($appraisePropertyData->toArray());
                $propertyId = QueryBuilder::for($property)->insertGetId($property->attributesToArray());

                if (isset($appraisePropertyData->id)) {
                    $itemDatas = AppraisePropertyDetail::where('appraise_property_id', $appraisePropertyData->id)->get();
                    foreach ($itemDatas as $itemData) {
                        if (isset($itemData)) {
                            $itemData->appraise_property_id = $propertyId;
                            $item = new CertificateAssetPropertyDetail($itemData->toArray());
                            $itemId = QueryBuilder::for($item)->insertGetId($item->attributesToArray());
                        }
                    }

                    $itemDatas = AppraisePropertyTurningTime::where('appraise_property_id', $appraisePropertyData->id)->get();
                    foreach ($itemDatas as $itemData) {
                        if (isset($itemData)) {
                            $itemData->appraise_property_id = $propertyId;
                            $item = new CertificateAssetPropertyTurningTime($itemData->toArray());
                            $itemId = QueryBuilder::for($item)->insertGetId($item->attributesToArray());
                        }
                    }
                }
            }
        }

        $itemDatas = AppraiseOtherAsset::where('appraise_id', $appraiseId)->get();
        foreach ($itemDatas as $itemData) {
            if (isset($itemData)) {
                $itemData->appraise_id = $certificateAssetId;
                $item = new CertificateAssetOtherAsset($itemData->toArray());
                $itemId = QueryBuilder::for($item)->insertGetId($item->attributesToArray());
            }
        }

        $itemDatas = AppraiseTangibleAsset::where('appraise_id', $appraiseId)->get();
        foreach ($itemDatas as $itemData) {
            if (isset($itemData)) {
                $itemData->appraise_id = $certificateAssetId;
                $item = new CertificateAssetTangibleAsset($itemData->toArray());
                $itemId = QueryBuilder::for($item)->insertGetId($item->attributesToArray());

                $itemTangibleFactors = AppraiseTangibleComparisonFactor::where(['appraise_id' => $appraiseId, 'tangible_asset_id' => $itemData->id])->get();
                foreach ($itemTangibleFactors as $tangibleFactor) {
                    if (isset($tangibleFactor)) {
                        $tangibleFactor->appraise_id = $certificateAssetId;
                        $tangibleFactor->tangible_asset_id = $itemId;
                        $item = new CertificateAssetTangibleComparisonFactor($tangibleFactor->toArray());
                        QueryBuilder::for($item)->insert($item->attributesToArray());
                    }
                }
                $this->saveConstructionCompany($preCertificateId, $certificateAssetId, $appraiseId, 0, $itemData->id, $itemId);
            }
        }

        $itemData = AppraiseVersion::where('appraise_id', $appraiseId)->orderBy('id', 'desc')->first();
        // foreach ($itemDatas as $itemData) {
            if (isset($itemData)) {
                $itemData->appraise_id = $certificateAssetId;
                $item = new CertificateAssetVersion($itemData->toArray());
                $itemId = QueryBuilder::for($item)->insertGetId($item->attributesToArray());
            }
        // }

        $itemDatas = AppraiseAppraisalMethods::where('appraise_id', $appraiseId)->get();
        foreach ($itemDatas as $itemData) {
            if (isset($itemData)) {
                $itemData->appraise_id = $certificateAssetId;
                $item = new CertificateAssetAppraisalMethods($itemData->toArray());
                $itemId = QueryBuilder::for($item)->insertGetId($item->attributesToArray());
            }
        }

        $itemDatas = AppraisePrice::where('appraise_id', $appraiseId)->get();

        foreach ($itemDatas as $itemData) {
            if (isset($itemData)) {
                $itemData->appraise_id = $certificateAssetId;
                $item = new CertificateAssetPrice($itemData->toArray());
                $itemId = QueryBuilder::for($item)->insertGetId($item->attributesToArray());
            }
        }
    }
    private function updateDetailRealEstateAppraise($preCertificateId, $realEstates, $realEstateAppraiseIds)
    {
        $oldRealEstates = $realEstates;
        // $appraiseRepo = new EloquentAppraiseRepository(new Appraise());
        CertificateHasRealEstate::query()
            ->where('pre_certificate_id' , $preCertificateId)
            ->whereHas('realEstates', function ($has) use ($realEstateAppraiseIds) {
                $has->whereIn('real_estate_id', $realEstateAppraiseIds);
            })->forceDelete();
        foreach ($oldRealEstates as $realEstate) {
            $oldCertificateAssetIds[$realEstate->real_estate_id] = $realEstate->id;
        }
        $this->saveDocumentLaw($preCertificateId, $realEstateAppraiseIds, 'land');
        $this->saveMethod($preCertificateId);
        $this->updateDocumentDescription($preCertificateId);

        foreach ($realEstateAppraiseIds as $realEstateId) {
            if (!isset($oldCertificateAssetIds[$realEstateId])) {
                $assetData = RealEstate::where('id', $realEstateId)->first();
                if (!isset($assetData)) continue;
                CertificateRealEstate::query()->where('real_estate_id', $realEstateId)->forceDelete();
                $assetData->real_estate_id = $realEstateId;
                $certificateRealEstate = new CertificateRealEstate($assetData->toArray());
                $certificateAssetId = CertificateRealEstate::query()->insertGetId($certificateRealEstate->attributesToArray());
                $realEstateData['pre_certificate_id'] = $preCertificateId;
                $realEstateData['real_estate_id'] = $certificateAssetId;
                $realEstateData['version'] = '1.0';
                CertificateHasRealEstate::query()->create($realEstateData);
                $this->insertAppraiseData($realEstateId, $certificateAssetId, $preCertificateId);
                // $appraiseRepo->updateRealEstateStatus($realEstateId, 3);
                $this->updateRealEstateCertificateId($realEstateId,$preCertificateId );
            }  else {
                $certificateAssetId = $oldCertificateAssetIds[$realEstateId];
                $realEstate = [];
                $realEstate['pre_certificate_id'] = $preCertificateId;
                $realEstate['real_estate_id'] = $certificateAssetId;
                $realEstate['version'] = '2.0';
                CertificateHasRealEstate::query()->create($realEstate);
            }
        }
        $oldRealEstateIds = Arr::pluck($oldRealEstates, 'real_estate_id');
        $diffs = array_diff($oldRealEstateIds, $realEstateAppraiseIds);

        if (! empty($diffs)) {
            foreach ($diffs as $diff) {
                // $appraiseRepo->updateRealEstateStatus($diff, 2);
                $this->updateRealEstateCertificateId($diff);
                $realEstate = CertificateRealEstate::query()->where('real_estate_id', $diff)->first();
                $realEstateId = $realEstate->id;
                $certificateAsset = CertificateAsset::query()->where('real_estate_id', $realEstateId)->first();
                if (isset($certificateAsset))
                    $this->saveConstructionCompany($preCertificateId, $certificateAsset->id, $certificateAsset->appraise_id, 1);
                CertificateRealEstate::query()->where('real_estate_id', $diff)->forceDelete();
            }
        }

    }
    private function insertApartmentData(int $realEstateId, int $certificateAssetId, int $preCertificateId)
    {
        if (CertificateApartment::query()->where('real_estate_id', $certificateAssetId)->exists()) {
            CertificateApartment::query()->where('real_estate_id', $certificateAssetId)->forceDelete();
        }

        $apartment = ApartmentAsset::query()->where('real_estate_id', $realEstateId)->first()->toArray();
        $apartmentId = $apartment['id'];
        $apartment['real_estate_id'] = $certificateAssetId;
        $apartment['apartment_asset_id'] = $apartmentId;
        //apartment
        $apartmentData = new CertificateApartment($apartment);
        $certificateApartment= CertificateApartment::query()->create($apartmentData->attributesToArray());
        $certificateApartmentId = $certificateApartment->id;
        //apartment Adapters
        $adapters = ApartmentAssetAdapter::query()->where('apartment_asset_id', $apartmentId)->get()->toArray();
        // $adapterData = [];
        foreach ($adapters as $adapter) {
            $adapter['apartment_asset_id'] = $certificateApartmentId;
            $adapterArr = new CertificateApartmentAdapter($adapter);
            $adapterData = $adapterArr->attributesToArray();
            CertificateApartmentAdapter::query()->create($adapterData);
        }
        //apartment Price
        $prices = ApartmentAssetPrice::query()->where('apartment_asset_id', $apartmentId)->get()->toArray();
        $priceData = [];
        foreach ($prices as $price) {
            $price['apartment_asset_id'] = $certificateApartmentId;
            $priceArr = new CertificateApartmentPrice($price);
            $priceData = $priceArr->attributesToArray();
            CertificateApartmentPrice::query()->create($priceData);
        }
        //apartment Law
        $laws = ApartmentAssetLaw::query()->where('apartment_asset_id', $apartmentId)->get()->toArray();
        $lawData = [];
        foreach ($laws as $law) {
            $law['apartment_asset_id'] = $certificateApartmentId;
            $lawArr = new CertificateApartmentLaw($law);
            $lawData = $lawArr->attributesToArray();
            CertificateApartmentLaw::query()->create($lawData);
        }
        //apartment Property
        $properties = ApartmentAssetProperty::query()->where('apartment_asset_id', $apartmentId)->get()->toArray();
        $propertyData = [];
        foreach ($properties as $property) {
            $property['apartment_asset_id'] = $certificateApartmentId;
            $propertyArr = new CertificateApartmentProperty($property);
            $propertyData = $propertyArr->attributesToArray();
            CertificateApartmentProperty::query()->create($propertyData);
        }
        //apartment methods
        $methods = ApartmentAssetAppraisalMethod::query()->where('apartment_asset_id', $apartmentId)->get()->toArray();
        $methodData = [];
        foreach ($methods as $method) {
            $method['apartment_asset_id'] = $certificateApartmentId;
            $methodArr = new CertificateApartmentAppraisalMethod($method);
            $methodData = $methodArr->attributesToArray();
            CertificateApartmentAppraisalMethod::query()->create($methodData);
        }
        // apartment appraisal base
        $bases = ApartmentAssetAppraisalBase::query()->where('apartment_asset_id', $apartmentId)->get()->toArray();
        $baseData = [];
        foreach ($bases as $base) {
            $base['apartment_asset_id'] = $certificateApartmentId;
            $baseArr = new CertificateApartmentAppraisalBase($base);
            $baseData = $baseArr->attributesToArray();
            CertificateApartmentAppraisalBase::query()->create($baseData);
        }
        // apartment comparison Factor
        $factors = ApartmentAssetComparisonFactor::query()->where('apartment_asset_id', $apartmentId)->get()->toArray();
        $factorData = [];
        foreach ($factors as $factor) {
            $factor['apartment_asset_id'] = $certificateApartmentId;
            $factorArr = new CertificateApartmentComparisonFactor($factor);
            $factorData = $factorArr->attributesToArray();
            CertificateApartmentComparisonFactor::query()->create($factorData);
        }
        // apartment has assets
        $hasAssets = ApartmentAssetHasAsset::query()->where('apartment_asset_id', $apartmentId)->get()->toArray();
        $hasAssetData = [];
        foreach ($hasAssets as $hasAsset) {
            $hasAsset['apartment_asset_id'] = $certificateApartmentId;
            $hasAssetArr = new CertificateApartmentHasAsset($hasAsset);
            $hasAssetData = $hasAssetArr->attributesToArray();
            CertificateApartmentHasAsset::query()->create($hasAssetData);
        }
        // apartment other assets
        $otherAssets = ApartmentAssetOtherAsset::query()->where('apartment_asset_id', $apartmentId)->get()->toArray();
        $otherAssetData = [];
        foreach ($otherAssets as $otherAsset) {
            $otherAsset['apartment_asset_id'] = $certificateApartmentId;
            $otherAssetArr = new CertificateApartmentOtherAsset($otherAsset);
            $otherAssetData = $otherAssetArr->attributesToArray();
            CertificateApartmentOtherAsset::query()->create($otherAssetData);
        }
        // apartment pics
        $pics = ApartmentAssetPic::query()->where('apartment_asset_id', $apartmentId)->get()->toArray();
        $picData = [];
        foreach ($pics as $pic) {
            $pic['apartment_asset_id'] = $certificateApartmentId;
            $picArr = new CertificateApartmentPic($pic);
            $picData = $picArr->attributesToArray();
            CertificateApartmentPic::query()->create($picData);
        }
        //version
        $version = ApartmentAssetVersion::query()->where('apartment_asset_id', $apartmentId)->orderBy('id', 'desc')->first()->toArray();
        $version['apartment_asset_id'] = $certificateApartmentId;
        $versionArr = new CertificateApartmentVersion($version);
        $versionData = $versionArr->attributesToArray();
        CertificateApartmentVersion::query()->create($versionData);
    }
    private function updateDetailRealEstateApartment($preCertificateId, $realEstates, $realEstateApartmentIds)
    {
        $oldRealEstates = $realEstates;
        // $apartmentRepo = new EloquentApartmentAssetRepository(new ApartmentAsset());
        CertificateHasRealEstate::query()
            ->where('pre_certificate_id' , $preCertificateId)
            ->whereHas('realEstates', function ($has) use ($realEstateApartmentIds) {
                $has->whereIn('real_estate_id', $realEstateApartmentIds);
            })->forceDelete();

        $this->saveDocumentLaw($preCertificateId, $realEstateApartmentIds, 'apartment');
        $this->saveMethod($preCertificateId);
        $this->updateDocumentDescription($preCertificateId);

        foreach ($oldRealEstates as $realEstate) {
            $oldCertificateAssetIds[$realEstate->real_estate_id] = $realEstate->id;
        }
        foreach ($realEstateApartmentIds as $realEstateId) {
            if (!isset($oldCertificateAssetIds[$realEstateId])) {
                $assetData = RealEstate::where('id', $realEstateId)->first();
                if (!isset($assetData)) continue;
                CertificateRealEstate::query()->where('real_estate_id', '=', $realEstateId)->forceDelete();
                $assetData->real_estate_id = $realEstateId;
                $certificateRealEstate = new CertificateRealEstate($assetData->toArray());
                $certificateAssetId = CertificateRealEstate::query()->insertGetId($certificateRealEstate->attributesToArray());
                $realEstateData['pre_certificate_id'] = $preCertificateId;
                $realEstateData['real_estate_id'] = $certificateAssetId;
                $realEstateData['version'] = '1.0';
                CertificateHasRealEstate::query()->create($realEstateData);
                $this->insertApartmentData($realEstateId, $certificateAssetId, $preCertificateId);
                // $apartmentRepo->updateStatus($realEstateId, 3);
                $this->updateRealEstateCertificateId($realEstateId, $preCertificateId, false);
            }  else {
                $certificateAssetId = $oldCertificateAssetIds[$realEstateId];
                $realEstate = [];
                $realEstate['pre_certificate_id'] = $preCertificateId;
                $realEstate['real_estate_id'] = $certificateAssetId;
                $realEstate['version'] = '2.0';
                CertificateHasRealEstate::query()->create($realEstate);
            }
        }
        $oldRealEstateIds = Arr::pluck($oldRealEstates, 'real_estate_id');
        $diffs = array_diff($oldRealEstateIds, $realEstateApartmentIds);
        if (! empty($diffs)) {
            foreach ($diffs as $diff) {
                // $apartmentRepo->updateStatus($diff, 2);
                $this->updateRealEstateCertificateId($realEstateId, null, false);
                CertificateRealEstate::query()->where('real_estate_id', '=', $diff)->forceDelete();
            }
        }
    }
    private function updateDetailPersonalProperty($preCertificateId, $oldPersonals, $personalIds)
    {
        CertificateHasPersonalProperty::query()->where('pre_certificate_id', '=', $preCertificateId)->forceDelete();
        $oldCertificateAssetIds = [];
        foreach ($oldPersonals as $personal) {
            $oldCertificateAssetIds[$personal->personal_property_id] = $personal->id;
        }
        foreach ($personalIds as $personalId) {
            if (!isset($oldCertificateAssetIds[$personalId])) {
                $assetData = PersonalProperty::where('id', $personalId)->first();
                if (!isset($assetData)) continue;
                CertificatePersonalProperty::query()->where('personal_property_id', '=', $personalId)->forceDelete();
                $assetData->personal_property_id = $personalId;
                $certificatePersonal = new CertificatePersonalProperty($assetData->toArray());
                $certificateAssetId = CertificatePersonalProperty::query()->insertGetId($certificatePersonal->attributesToArray());

                $personalData['pre_certificate_id'] = $preCertificateId;
                $personalData['personal_property_id'] = $certificateAssetId;
                $personalData['version'] = '1.0';
                CertificateHasPersonalProperty::query()->create($personalData);

                $this->UpdatePersonaltyData($personalId, $certificateAssetId, $assetData->assetType->acronym);
                $this->updatePersonalPropertyCertificateId($personalId, $preCertificateId);
            }  else {
                $certificateAssetId = $oldCertificateAssetIds[$personalId];
                $personal = [];
                $personal['pre_certificate_id'] = $preCertificateId;
                $personal['personal_property_id'] = $certificateAssetId;
                $personal['version'] = '2.0';
                CertificateHasPersonalProperty::query()->create($personal);
            }
        }
        $oldPersonalIds = Arr::pluck($oldPersonals, 'personal_property_id');
        $diffs = array_diff($oldPersonalIds, $personalIds);
        if (! empty($diffs)) {
            foreach ($diffs as $diff) {
                // $personalRep->updateStatus($diff, 2);
                $this->updatePersonalPropertyCertificateId($diff);
                CertificatePersonalProperty::query()->where('personal_property_id', '=', $diff)->forceDelete();
            }
        }
    }
   
    private function removeAssetInCertificate($preCertificateId, $personalKeep = [], $realEstateApartmentKeep = [], $realEstateAppraiseKeep = [])
    {
        $oldCertificate = PreCertificate::where('id', $preCertificateId)->first();
        $oldPersonals = $oldCertificate->personalProperties;
        $realEstates = $oldCertificate->realEstate;
        $oldRealEstateApartment = $realEstates->where('assetType.acronym','CC');
        $oldRealEstateAppraise = $realEstates->whereIn('assetType.acronym', ['DCN', 'DT']);
        $oldPersonalIds = Arr::pluck($oldPersonals, 'personal_property_id');
        $oldRealEstateApartmentIds = Arr::pluck($oldRealEstateApartment, 'real_estate_id');
        $oldRealEstateAppraiseIds = Arr::pluck($oldRealEstateAppraise, 'real_estate_id');

        $personalDelete  = array_diff($oldPersonalIds, $personalKeep);
        $realEstateAppraiseDelete  = array_diff($oldRealEstateAppraiseIds, $realEstateAppraiseKeep);
        $realEstateApartmentDelete  = array_diff($oldRealEstateApartmentIds, $realEstateApartmentKeep);

        if (count($personalDelete) > 0) {
            // $personalRepo = new EloquentPersonalPropertiesRepository(new PersonalProperty());
            foreach ($personalDelete as $personalId) {
                CertificateHasPersonalProperty::query()
                    ->where('pre_certificate_id',  $preCertificateId)
                    ->whereHas('personalProperties', function ($has) use($personalId){
                        $has->where('personal_property_id', $personalId);
                    })
                    ->forceDelete();
                CertificatePersonalProperty::query()->where('personal_property_id', '=', $personalId)->forceDelete();
                // $personalRepo->updateStatus($personalId, 2);
                $this->updatePersonalPropertyCertificateId($personalId);
            }
        }
        if (count($realEstateApartmentDelete) > 0) {
            // $apartmentRepo = new EloquentApartmentAssetRepository(new ApartmentAsset());
            foreach ($realEstateApartmentDelete as $realEstateId) {
                CertificateHasRealEstate::query()
                    ->where('pre_certificate_id', '=', $preCertificateId)
                    ->whereHas('realEstates', function ($has) use($realEstateId){
                        $has->where('real_estate_id', $realEstateId);
                    }) ->forceDelete();
                CertificateRealEstate::query()->where('real_estate_id', '=', $realEstateId)->forceDelete();
                // $apartmentRepo->updateStatus($realEstateId, 2);
                $this->updateRealEstateCertificateId($realEstateId, null, false);
            }
        }
        if (count($realEstateAppraiseDelete) > 0) {
            // $appraiseRepo = new EloquentAppraiseRepository(new Appraise());
            foreach ($realEstateAppraiseDelete as $realEstateId) {
                CertificateHasRealEstate::query()
                    ->where('pre_certificate_id', '=', $preCertificateId)
                    ->whereHas('realEstates', function ($has) use($realEstateId){
                        $has->where('real_estate_id', $realEstateId);
                    }) ->forceDelete();
                CertificateRealEstate::query()->where('real_estate_id', '=', $realEstateId)->forceDelete();
                // Remove construction infomation in preCertificate
                $certificateAsset = CertificateAsset::query()->where('real_estate_id', $realEstateId)->first();
                if (isset($certificateAsset))
                    $this->saveConstructionCompany($preCertificateId, $certificateAsset->id, $certificateAsset->appraise_id, 1);
                $this->updateRealEstateCertificateId($realEstateId);
            }
        }
        CommonService::getCertificateAssetPriceTotal_v2($preCertificateId);
        $this->updateTotalPrie($preCertificateId);
    }
    private function getCertificateAppraise(int $id)
    {
        $result = [];
        if (PreCertificate::where('id', $id)->exists()) {
            $select = [
                'id', 'status', 'document_type'
            ];
            $with = [
                // 'appraises:id,appraise_id,street_id,ward_id,district_id,province_id,asset_type_id,created_at,appraise_asset',
                // 'appraises.province:id,name',
                // 'appraises.district:id,name',
                // 'appraises.ward:id,name',
                // 'appraises.street:id,name',
                // 'appraises.properties:id,appraise_id',
                // 'appraises.properties.propertyDetail:id,appraise_property_id,land_type_purpose_id',
                // 'appraises.properties.propertyDetail.landTypePurpose:id,acronym,description',
                // 'appraises.assetType:id,description',
                // 'appraises.appraiseHasAssets:appraise_id,asset_general_id,version',
                // 'appraises.assetPrice' => function ($q){
                //     $q->where('slug','=','total_asset_price')
                //         ->orWhere('slug','=','total_asset_area')
                //         ->select(['appraise_id','slug','value'])
                //         ->orderBy('slug');
                // },
                // 'appraises.createdBy:id,name'
                'realEstate.appraises.appraiseHasAssets',
                'realEstate.apartment.apartmentHasAssets'
            ];
            $result = PreCertificate::with($with)
                ->where('id', $id)
                ->select($select)
                ->first();
            $result->append(['status_text', 'general_asset']);
        }

        return $result;
    }

    public function updatePreCertificateGeneral(int $id, array $object)
    {
        $result = [];
        // # đang tắt khối block xác thực
        $check = $this->beforeSave($id);
        if (isset($check)) {
            return $check;
        }
        // $check = $this->checkDuplicateData($object, $id);
        // if (isset($check)) {
        //     return $check;
        // }
        if (PreCertificate::where('id', $id)->exists()) {
            PreCertificate::where('id', $id)->update([
                'petitioner_name' => $object['petitioner_name'],
                'petitioner_phone' => $object['petitioner_phone'],
                'petitioner_identity_card' => $object['petitioner_identity_card'],
                'petitioner_address' => $object['petitioner_address'],
                'appraise_purpose_id' => $object['appraise_purpose_id'],
                'document_num' => $object['document_num'],
                'service_fee' => $object['service_fee'],
                'certificate_num' => $object['certificate_num'],
                'document_date' => isset($object['document_date']) ? \Carbon\Carbon::createFromFormat('d/m/Y', $object['document_date'])->format('Y-m-d') : null,
                'appraise_date' => isset($object['appraise_date']) ? \Carbon\Carbon::createFromFormat('d/m/Y', $object['appraise_date'])->format('Y-m-d') : null,
                'certificate_date' => isset($object['certificate_date']) ? \Carbon\Carbon::createFromFormat('d/m/Y', $object['certificate_date'])->format('Y-m-d') : null,
                'commission_fee' => $object['commission_fee'],
                'document_type' => $object['document_type'],
                'note' => $object['note']
            ]);

            $edited = PreCertificate::where('id', $id)->first();

            // activity-log cập nhật thông tin chung
            $this->CreateActivityLog($edited, $edited, 'update_data', 'cập nhật thông tin chung');

            $result = $this->getCertificateGeneral($id);
        } else {
            $result = ['message' => ErrorMessage::CERTIFICATE_NOTEXISTS, 'exception' => ''];
        }
        return $result;
    }

    private function updateAppraisalTeam(int $id, array $object = null)
    {
        if (isset($object)) {
            PreCertificate::where('id', $id)->update([
                'appraiser_id' => $object['appraiser_id'],
                'appraiser_manager_id' => $object['appraiser_manager_id'],
                'appraiser_confirm_id' => $object['appraiser_confirm_id'],
                'appraiser_perform_id' => $object['appraiser_perform_id'],
                'appraiser_control_id' => $object['appraiser_control_id'],
            ]);
        }
    }

    private function getAppraisalTeam(int $id)
    {
        $result = [];
        if (PreCertificate::where('id', $id)->exists()) {
            $select = [
                'id',
                'appraiser_perform_id',
                'appraiser_id',
                'appraiser_manager_id',
                'appraiser_control_id',
                'appraiser_confirm_id',
                'appraiser_sale_id',
                'status_expired_at',
                'updated_at',
                'status'
            ];
            $with = [
                'appraiser:id,name,user_id',
                'appraiserPerform:id,name,user_id',
                'appraiserManager:id,name,user_id',
                'appraiserConfirm:id,name,user_id',
                'appraiserControl:id,name,user_id',
            ];
            $result = PreCertificate::with($with)->where('id', $id)->select($select)->first();
            if ($result['status'] == 5) {
                $user = User::query()
                ->where('id', '=', $result['created_by'])
                ->first();
                $result['image'] = $user->image;
            }
            if ($result['status'] == 1) {
                $appraiser = Appraiser::query()
                ->where('id', '=', $result['appraiser_sale_id'])
                ->first();
                $user = User::query()
                ->where('id', '=', $appraiser->user_id)
                ->first();
                $result['image'] = $user->image;
            }
            if ($result['status'] == 2) {
                $appraiser = Appraiser::query()
                ->where('id', '=', $result['appraiser_perform_id'])
                ->first();
                $user = User::query()
                ->where('id', '=', $appraiser->user_id)
                ->first();
                $result['image'] = $user->image;
            }
            if ($result['status'] == 3 || $result['status'] == 4) {
                $appraiser = Appraiser::query()
                ->where('id', '=', $result['appraiser_id'])
                ->first();
                $user = User::query()
                ->where('id', '=', $appraiser->user_id)
                ->first();
                $result['image'] = $user->image;
            }
            if ($result['status'] == 6) {
                $appraiser = Appraiser::query()
                ->where('id', '=', $result['appraiser_control_id'])
                ->first();
                $user = User::query()
                ->where('id', '=', $appraiser->user_id)
                ->first();
                $result['image'] = $user->image;
            }
        }
        return $result;
    }

    private function getCertificateGeneral(int $id)
    {
        $result = [];
        if (PreCertificate::where('id', $id)->exists()) {
            $select = [
                'id',
                'petitioner_name',
                'petitioner_phone',
                'petitioner_identity_card',
                'petitioner_address',
                'appraise_purpose_id',
                'document_num',
                'document_date',
                'appraise_date',
                'service_fee',
                'certificate_date',
                'certificate_num',
                'commission_fee',
                'status_expired_at',
                'document_type',
                'note'
            ];
            $with = [
                'appraisePurpose:id,name',
            ];
            $result = PreCertificate::with($with)->where('id', $id)->select($select)->first();
        }
        return $result;
    }

    private function beforeSave(int $id)
    {
        $result = null;

        if (PreCertificate::where('id', $id)->exists()) {
            $user = CommonService::getUser();
            if (!$user->hasRole(['ROOT_ADMIN', 'SUPER_ADMIN', 'SUB_ADMIN'])) {
                $data = PreCertificate::where('id', $id)->get()->first();
                switch ($data['status']) {
                    case 1:
                        if (!($data->created_by == $user->id || $data->appraiserSale->user_id == $user->id))
                            $result = ['message' => ErrorMessage::CERTIFICATE_CHECK_STATUS_FOR_UPDATE . $data->status_text . '. Chỉ có người tạo phiếu và nhân viên Sale mới có quyền chỉnh sửa.', 'exception' => ''];
                        break;
                    case 2:
                        if (!($data->appraiserPerform->user_id == $user->id))
                            $result = ['message' => ErrorMessage::CERTIFICATE_CHECK_STATUS_FOR_UPDATE . $data->status_text . '. Chỉ có chuyên viên thẩm định mới có quyền chỉnh sửa.', 'exception' => ''];
                        break;
                    default:
                        $result = ['message' => ErrorMessage::CERTIFICATE_CHECK_STATUS_FOR_UPDATE . $data->status_text, 'exception' => ''];
                        break;
                }
            }
        } else {
            $result = ['message' => ErrorMessage::PRE_CERTIFICATE_NOTEXISTS, 'exception' => ''];
        }
        return $result;
    }

    public function saleDocumentUpload($id, $request)
    {
        return DB::transaction(function () use ($id, $request) {
            try {
                $result = [];
                $now = Carbon::now()->timezone('Asia/Ho_Chi_Minh');
                $path = env('STORAGE_OTHERS') . '/' . 'comparison_brief/' . $now->year . '/' . $now->month . '/';

                $files = $request->file('files');

                $user = CommonService::getUser();
                if (isset($files) && !empty($files)) {
                    foreach ($files as $file) {
                        $fileName = $file->getClientOriginalName();
                        $fileType = $file->getClientOriginalExtension();
                        $fileSize = $file->getSize();
                        $name = $path . Uuid::uuid4()->toString() . '.' . $fileType;
                        Storage::put($name, file_get_contents($file));
                        $fileUrl = Storage::url($name);
                        $item = [
                            'pre_certificate_id' => $id,
                            'name' => $fileName,
                            'link' => $fileUrl,
                            'type' => $fileType,
                            'size' => $fileSize,
                            'description' => 'other',
                            'created_by' => $user->id,
                        ];

                        $item = new PreCertificateOtherDocuments($item);
                        QueryBuilder::for($item)->insert($item->attributesToArray());
                        $result[] = $item;
                    }
                    $edited = PreCertificate::where('id', $id)->first();
                    $edited2 = PreCertificateOtherDocuments::where('pre_certificate_id', $id)->first();
                    # activity-log upload file
                    $this->CreateActivityLog($edited, $edited2, 'upload_file', 'tải phụ lục');
                    // chưa lấy ra được model user và id user
                }

                $result = PreCertificateOtherDocuments::where('pre_certificate_id', $id)
                    ->with('createdBy')
                    ->get();
                return $result;
            } catch (Exception $exception) {
                return ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
                Log::error($exception);
                throw $exception;
            }
        });
    }

    public function getProcessingTime()
    {
        $result = [];
        $select = [
            'id',
            'type',
            'description',
            'acronym',
            // 'status',
            DB::raw('cast(description as integer) as value'),
            // DB::raw('cast(description as integer)/60 as hour'),
            // DB::raw('cast(description as integer)%60 as minute'),
        ];
        $where = [
            'type' => 'PROCESSING_TIME',
            'status' => 1,
        ];
        $result = CertificateDictionary::where($where)->select($select)->get();
        return $result;
    }

    private function beforeUpdateStatus(int $id)
    {
        $result = null;

        if (PreCertificate::where('id', $id)->exists()) {
            $required = request()->get('required');
            $user = CommonService::getUser();
            $data = PreCertificate::where('id', $id)->get()->first();
            $appraiser = [];
            if (!empty($required)) {
                // $ischeckPrice = $required['check_price'];
                // $isCheckLegal =  $required['check_legal'];
                // $isCheckVersion =  $required['check_version'];
                $isCheckAppraiser =  $required['appraiser'];
                $isCheckTotalPreliminaryValue =  $required['total_preliminary_value'];

                // if ($isCheckVersion) {
                //     if (!empty($data->realEstate)) {
                //         $check = AppraiseVersionService::checkVersionByCertificate($id);
                //         if (!empty($check)) {
                //             return ['message' => 'Tài sản thẩm định đã được chỉnh sửa. Vui lòng cập nhật version.', 'exception' => ''];
                //         }
                //     }
                // }
        
                if ($isCheckAppraiser) {
                    $appraiser['appraiser_sale_id'] =  request()->get('appraiser_sale_id');
                    $appraiser['business_manager_id'] =  request()->get('business_manager_id');
                    $appraiser['appraiser_perform_id'] =  request()->get('appraiser_perform_id');
                    if (empty($appraiser['appraiser_sale_id']) || empty($appraiser['business_manager_id']) || empty($appraiser['appraiser_perform_id'])) {
                        return ['message' => ErrorMessage::CERTIFICATE_APPRAISERTEAM, 'exception' => ''];
                    }
                }
                if ($isCheckTotalPreliminaryValue) {
                    if (empty($data->total_preliminary_value)) {
                        return ['message' => 'Chưa có tổng giá trị sơ bộ.' , 'exception' => ''];
                    }
                }
            }
            //Check role and permision
            if (!$user->hasRole(['ROOT_ADMIN', 'SUPER_ADMIN', 'SUB_ADMIN'])) {
                switch ($data['status']) {
                    case 1:
                        if (!($data->created_by == $user->id || $data->appraiserSale->user_id == $user->id))
                            $result = ['message' => ErrorMessage::CERTIFICATE_CHECK_STATUS_FOR_UPDATE . $data->status_text . '. Chỉ có người tạo phiếu và nhân viên kinh doanh mới có quyền cập nhật.', 'exception' => ''];
                        break;
                    case 2:
                        if (!($data->appraiserPerform && $data->appraiserPerform->user_id == $user->id))
                            $result = ['message' => ErrorMessage::CERTIFICATE_CHECK_STATUS_FOR_UPDATE . $data->status_text . '. Chỉ có chuyên viên thẩm định mới có quyền cập nhật.', 'exception' => ''];
                        break;
                    case 3:
                         if (!($data->appraiserPerform && $data->appraiserPerform->user_id == $user->id))
                            $result = ['message' => ErrorMessage::CERTIFICATE_CHECK_STATUS_FOR_UPDATE . $data->status_text . '. Chỉ có chuyên viên thẩm định mới có quyền cập nhật.', 'exception' => ''];
                        break;
                    case 4:
                        if (!( $data->appraiserSale->user_id == $user->id))
                            $result = ['message' => ErrorMessage::CERTIFICATE_CHECK_STATUS_FOR_UPDATE . $data->status_text . '. Chỉ có nhân viên kinh doanh mới có quyền cập nhật.', 'exception' => ''];
                        break;
                    case 6:
                        if (!($data->appraiserPerform && $data->appraiserPerform->user_id == $user->id))
                            $result = ['message' => ErrorMessage::CERTIFICATE_CHECK_STATUS_FOR_UPDATE . $data->status_text . '. Chỉ có chuyên viên thẩm định mới có quyền cập nhật.', 'exception' => ''];
                        break;
                    default:
                        $result = ['message' => ErrorMessage::CERTIFICATE_CHECK_STATUS_FOR_UPDATE . $data->status_text, 'exception' => ''];
                        break;
                }
            }
        } else {
            $result = ['message' => ErrorMessage::CERTIFICATE_NOTEXISTS, 'exception' => ''];
        }
        return $result;
    }

    public function updateAppraisersTeam(int $id, $request)
    {
        $result = [];
        if (PreCertificate::where('id', $id)->exists()) {
            $user = CommonService::getUser();
            $data = PreCertificate::where('id', $id)->get()->first();
            if ($user->hasRole(['ROOT_ADMIN', 'SUPER_ADMIN', 'SUB_ADMIN'])
                || ((isset($data->appraiser) && $data->appraiser->user_id == $user->id)
                || (isset($data->appraiserManager) && $data->appraiserManager->user_id == $user->id)
                || (isset($data->appraiserControl) && $data->appraiserControl->user_id == $user->id)
                || (isset($data->appraiserConfirm) && $data->appraiserConfirm->user_id == $user->id)
                || (isset($data->appraiserSale) && $data->appraiserSale->user_id == $user->id)
                || (isset($data->appraiserPerform) && $data->appraiserPerform->user_id == $user->id)
                || (isset($data->createdBy) && $data->createdBy->id == $user->id))
            ) {
                $this->updateAppraisalTeam($id, $request);
                $edited = PreCertificate::where('id', $id)->first();

                // activity-log cập nhật tổ thẩm định
                $this->CreateActivityLog($edited, $edited, 'update_data', 'cập nhật dữ liệu tổ thẩm định');

                $result = $this->getAppraisalTeam($id);
            } else {
                $result = ['message' => ErrorMessage::CERTIFICATE_CHECK_UPDATE . $id, 'exception' => ''];
            }
        } else {
            $result = ['message' => ErrorMessage::CERTIFICATE_NOTEXISTS, 'exception' => ''];
        }

        return $result;
    }

    public function getFinishCertificateAssets()
    {
        $data = [];
        if (request()->get('is_appraise') == 'false') {
            return $data;
        }
        $year = request()->get('year');
        // $year =  Carbon::parse($year)->format('Y');
        $province_id = request()->get('province_id') ?? "%";
        $district_id = request()->get('district_id') ?? "%";
        $ward_id = request()->get('ward_id') ?? "%";
        $street_id = request()->get('street_id') ?? "%";
        $transaction_type = request()->get('transaction_type');
        $total_area_from = request()->get('total_area_from') ?? 0;
        $total_area_to = request()->get('total_area_to') ?? 1000000;
        $total_amount_from = request()->get('total_amount_from') ?? 0;
        $total_amount_to = request()->get('total_amount_to') ?? 100000000000000;
        $distance = request()->get('distance') ?? 10;
        $location = request()->get('location');
        $front_side = request()->get('front_side');
        if (isset($location)) {
            $location1 = explode(',', $location);
            $lat = (float)$location1[0] ?? null;
            $lon = (float)$location1[1] ?? null;
        }
        else {
            return [];
        }
        $earthRadius = 6371;
        $with = [
            'pic'
        ];
        $tbName = '"coord"';
        $stringSql = sprintf(
            "SELECT t3.appraise_id as id, t1.petitioner_name as contact_person,t1.petitioner_phone as contact_phone
                    ,t1.petitioner_identity_card
                    ,t1.appraise_date as public_date
                    ,coalesce(
                        case
                            when  t12.value::bigint > 0
                            then ceil(t4.value::bigint / power(10, t12.value::bigint)) * power(10, t12.value::bigint)
                            when   t12.value::bigint < 0
                                then floor( t4.value::bigint * abs(power(10, t12.value::bigint))  ) / abs(power(10, t12.value::bigint))
                            else
                                t4.value::bigint
                        end, 0)
                    as total_amount
                    ,t5.value::float as total_area
                    ,t3.coordinates
                    , CONCAT(t10.name , ', ' , t9.name , ', ' , t8.name , ', ' , t7.name) as full_address
                    ,'TSTD' as migrate_status
                    , 0 as transaction_type_id
                    , 0 as transaction_type
                    ,'ĐÃ THẨM ĐỊNH' as transaction_type_description
                    ,t11.description as asset_type
                    ,t1.created_at
                FROM certificates t1
                    inner join certificate_has_appraises t2 on t1.id = t2.pre_certificate_id
                    inner join certificate_assets t3 on t2.appraise_id = t3.id
                    left join certificate_asset_prices t4 on t3.id = t4.appraise_id and t4.slug ='total_asset_price'
                    left join certificate_asset_prices t5 on t3.id = t5.appraise_id and t5.slug ='total_asset_area'
                    inner join (select id ,  :earthRadius * 2 * asin(sqrt(power(SIN((pi()/180) * ( SPLIT_PART(coordinates , ',',1)::float - :lat) /2),2)
                                + cos((pi()/180) * SPLIT_PART(coordinates , ',',1)::float)
                                * cos((pi()/180) * :lat)
                                * POWER(sin( (pi()/180) * (SPLIT_PART(coordinates , ',',2)::float - :lon) /2),2)))
                                as distance
                            from certificate_assets ) t6 on t3.id = t6.id
                    inner join provinces t7 on t3.province_id = t7.id
                    inner join districts t8 on t3.district_id = t8.id
                    inner join wards t9 on t3.ward_id = t9.id
                    inner join streets t10 on t3.street_id = t10.id
                    inner join dictionaries t11 on t3.asset_type_id = t11.id
                    left join certificate_asset_prices t12 on t3.id = t12.appraise_id and t12.slug ='round_appraise_total'
                WHERE t1.status IN (3, 4)
                    and t1.created_at >= :year
                    and t6.distance <= :distance
                    -- and cast(t3.province_id as text) like  :province_id
                    -- and cast(t3.district_id as text) like  :district_id
                    -- and cast(t3.ward_id as text) like  :ward_id
                    -- and cast(t3.street_id as text) like  :street_id
                    and t5.value between :total_area_from and :total_area_to
                    and t4.value between :total_amount_from and :total_amount_to
            "
        );
        // dd($stringSql);
        DB::enableQueryLog();
        $data = DB::select($stringSql, [
            ":year" => $year,
            ":lat" => $lat,
            ":lon" => $lon,
            ":distance" => $distance,
            ":earthRadius" => $earthRadius,
            // ":province_id" => $province_id,
            // ":district_id" => $district_id,
            // ":ward_id" => $ward_id,
            // ":street_id" => $street_id,
            ":total_area_from" => $total_area_from,
            ":total_area_to" => $total_area_to,
            ":total_amount_from" => $total_amount_from,
            ":total_amount_to" => $total_amount_to,
        ]);
        // dd(DB::getQueryLog());
        $result = array_column($data, 'id');
        $pic = Appraise::with('pic')->where(['id' => $result])->get(['id'])->toArray();
        foreach ($data as $item) {
            $find = array_search($item->id, array_column($pic, 'id'));
            if ($find === false)
                $item->pic = [];
            else
                $item->pic = $pic[$find]['pic'];
        }
        // dd($data);
        return $data;
    }

    public function getFinishCertificateApartment()
    {
        $data = [];
        if (request()->get('is_appraise') == 'false') {
            return $data;
        }
        $year = request()->get('year');
        // $year =  Carbon::parse($year)->format('Y');
        $province_id = request()->get('province_id') ?? "%";
        $district_id = request()->get('district_id') ?? "%";
        $ward_id = request()->get('ward_id') ?? "%";
        $street_id = request()->get('street_id') ?? "%";
        $transaction_type = request()->get('transaction_type');
        $total_area_from = request()->get('total_area_from') ?? 0;
        $total_area_to = request()->get('total_area_to') ?? 1000000;
        $total_amount_from = request()->get('total_amount_from') ?? 0;
        $total_amount_to = request()->get('total_amount_to') ?? 100000000000000;
        $distance = request()->get('distance') ?? 10;
        $location = request()->get('location');
        $front_side = request()->get('front_side');
        if (isset($location)) {
            $location1 = explode(',', $location);
            $lat = (float)$location1[0] ?? null;
            $lon = (float)$location1[1] ?? null;
        }
        else {
            return [];
        }
        $earthRadius = 6371;
        $with = [
            'pic'
        ];
        $tbName = '"coord"';
        $stringSql = sprintf(
            "SELECT 
            t13.name as apartment_name, t3.apartment_asset_id as id, 
            t1.petitioner_name as contact_person,t1.petitioner_phone as contact_phone
                                ,t1.petitioner_identity_card
                                ,t1.appraise_date as public_date
                                ,coalesce(
                                    case
                                        when  t12.value::bigint > 0
                                        then ceil(t4.value::bigint / power(10, t12.value::bigint)) * power(10, t12.value::bigint)
                                        when   t12.value::bigint < 0
                                            then floor( t4.value::bigint * abs(power(10, t12.value::bigint))  ) / abs(power(10, t12.value::bigint))
                                        else
                                            t4.value::bigint
                                    end, 0)
                                as total_amount
                                ,t5.value::float as total_area
                                ,t3.coordinates
                                , CONCAT(t10.name , ', ' , t9.name , ', ' , t8.name , ', ' , t7.name) as full_address
                                ,'TSTD' as migrate_status
                                , 0 as transaction_type_id
                                , 0 as transaction_type
                                ,'ĐÃ THẨM ĐỊNH' as transaction_type_description
                                ,t11.description as asset_type
                                ,t1.created_at
                                ,'CC' as loaitaisan
                            FROM certificates t1
                                inner join certificate_has_real_estates t2 on t1.id = t2.pre_certificate_id
                                inner join certificate_apartments t3 on t2.real_estate_id = t3.real_estate_id
                                left join certificate_apartment_prices t4 on t3.id = t4.apartment_asset_id and t4.slug ='apartment_total_price'
                                left join certificate_apartment_prices t5 on t3.id = t5.apartment_asset_id and t5.slug ='apartment_area'
                                inner join (select id ,  :earthRadius * 2 * asin(sqrt(power(SIN((pi()/180) * ( SPLIT_PART(coordinates , ',',1)::float - :lat) /2),2)
                                            + cos((pi()/180) * SPLIT_PART(coordinates , ',',1)::float)
                                            * cos((pi()/180) * :lat)
                                            * POWER(sin( (pi()/180) * (SPLIT_PART(coordinates , ',',2)::float - :lon) /2),2)))
                                            as distance
                                        from certificate_apartments ) t6 on t3.id = t6.id
                                inner join provinces t7 on t3.province_id = t7.id
                                inner join districts t8 on t3.district_id = t8.id
                                inner join wards t9 on t3.ward_id = t9.id
                                left join streets t10 on t3.street_id = t10.id
                                inner join projects t13 on t3.project_id = t13.id
                                inner join dictionaries t11 on t3.asset_type_id = t11.id
                                left join certificate_apartment_prices t12 on t3.id = t12.apartment_asset_id and t12.slug ='round_total'
                            WHERE t1.status IN (3, 4)
                                and t1.created_at >= :year
                                and t6.distance <= :distance
                                -- and cast(t3.province_id as text) like  :province_id
                                -- and cast(t3.district_id as text) like  :district_id
                                -- and cast(t3.ward_id as text) like  :ward_id
                                -- and cast(t3.street_id as text) like  :street_id
                                and t5.value between :total_area_from and :total_area_to
                                and t4.value between :total_amount_from and :total_amount_to
            "
        );
        // dd($stringSql);
        DB::enableQueryLog();
        $data = DB::select($stringSql, [
            ":year" => $year,
            ":lat" => $lat,
            ":lon" => $lon,
            ":distance" => $distance,
            ":earthRadius" => $earthRadius,
            // ":province_id" => $province_id,
            // ":district_id" => $district_id,
            // ":ward_id" => $ward_id,
            // ":street_id" => $street_id,
            ":total_area_from" => $total_area_from,
            ":total_area_to" => $total_area_to,
            ":total_amount_from" => $total_amount_from,
            ":total_amount_to" => $total_amount_to,
        ]);
        // dd(DB::getQueryLog());
        $result = array_column($data, 'id');
        $pic = []; 
        foreach ($result as $id) {
            $anh = ApartmentAsset::with('pic')->where(['id' => $id])->get(['id'])->toArray();
            $pic = array_merge ( $pic, $anh);
        }

        // if ($result && $pic) {
        //     dd($result , $pic);
        // }
        
        foreach ($data as $item) {
            $find = array_search($item->id, array_column($pic, 'id'));
            if ($find === false)
                $item->pic = [];
            else
                $item->pic = $pic[$find]['pic'];
        }
        // if ($data) {
        //     dd($data);
        // }
        
        return $data;
    }

    public  function getComparisonAppraise(array $ids)
    {
        $ids = $ids['ids'];
        $ids = json_decode($ids);

        return CommonService::getComparisonAppraise($ids);
    }

    private function notifyChangeStatus(int $id, int $status)
    {
        if (PreCertificate::where('id', $id)->exists()) {
            $loginUser = CommonService::getUser();
            $users[] = $loginUser;
            // $users= $loginUser;
            $with = [
                'appraiser:id,user_id,name',
                'appraiserSale:id,user_id,name',
                'appraiserPerform:id,user_id,name',
                'appraiserControl:id,user_id,name',
                'createdBy:id,name',
            ];
            $select = [
                'id',
                'created_by',
                'appraiser_id',
                'appraiser_sale_id',
                'appraiser_perform_id',
                'appraiser_control_id',
            ];
            $preCertificate = PreCertificate::with($with)->where('id', $id)->get($select)->first();
            $eloquenUser = new EloquentUserRepository(new User());

            if (isset($preCertificate->appraiserSale->user_id))
                if ($preCertificate->appraiserSale->user_id != $loginUser->id) {
                    $users[] =  $eloquenUser->getUser($preCertificate->appraiserSale->user_id);
                }
            if (isset($preCertificate->appraiserPerform->user_id))
                if ($preCertificate->appraiserPerform->user_id != $loginUser->id) {
                    $users[] =  $eloquenUser->getUser($preCertificate->appraiserPerform->user_id);
                }
            if (isset($preCertificate->appraiser->user_id))
                if ($preCertificate->appraiser->user_id != $loginUser->id && $preCertificate->appraiser->user_id != $preCertificate->appraiserPerform->user_id) {
                    $users[] =  $eloquenUser->getUser($preCertificate->appraiser->user_id);
                }
            if (isset($preCertificate->appraiserControl->user_id))
                if ($preCertificate->appraiserControl->user_id != $loginUser->id) {
                    $users[] =  $eloquenUser->getUser($preCertificate->appraiserControl->user_id);
                }
            switch ($status) {
                case 2:
                    $statusText = 'Đang thẩm định';
                    break;
                case 3:
                    $statusText = 'Đang duyệt';
                    break;
                case 4:
                    $statusText = 'Đã hoàn thành';
                    break;
                case 5:
                    $statusText = 'Đã hủy';
                    break;
                case 6:
                    $statusText = 'Đang kiểm soát';
                    break;
                default:
                    $statusText = 'Mới';
            }

            $data = [
                'subject' => '[HSTD_' . $id . '] Chuyển sang trạng thái ' . $statusText,
                'message' => 'HSTD_' . $id . ' đã được ' . $loginUser->name . ' chuyển sang trạng thái ' . $statusText . '.',
                'user' => $loginUser,
                'id' => $id
            ];

            CommonService::callNotification($users, $data);
        }
    }

    private function checkDuplicateData(array $object, int $preCertificateId = null)
    {
        $result = null;
        // PreCertificate brief id must be checked with year.
        // It might raise bug if created success with same number but after that update same year.
        // Temp return fale.
        return $result;
        $paramList = CompareMaterData::CERTIFICATE_BRIEF_CHECK_DUPLICATE;
        if ($paramList != null && count($paramList) > 0) {
            $paramKeys = array_keys($paramList);
            foreach ($paramKeys as $key) {
                if (isset($object[$key])) {
                    if (PreCertificate::where($key, $object[$key])->where('id', '<>', $preCertificateId ?? -1)->exists()) {
                        $result = ['message' => $paramList[$key] . ' đã tồn tại. Vui lòng nhập ' . $paramList[$key] . ' khác', 'exception' => ''];
                    }
                }
            }
        }
        return $result;
    }
    #endregion

    public function exportCertificateBriefs()
    {
        $status = request()->get('status');
        $fromDate = request()->get('fromDate');
        $toDate = request()->get('toDate');
        $users = request()->get('created_by');
        $appraiser = request()->get('appraiser_id');
        $appraiserSale = request()->get('appraiser_sale_id');
        $appraiserConfirm = request()->get('appraiser_confirm_id');
        $customer = request()->get('customer_id');

        if (isset($fromDate) && isset($toDate)) {
            $fromDate =  \Carbon\Carbon::createFromFormat('d/m/Y', $fromDate);
            $toDate =  \Carbon\Carbon::createFromFormat('d/m/Y', $toDate);
            $diff = $toDate->diff($fromDate);
            if ($diff->days > 93) {
                return ['message' => 'Chỉ được tìm kiếm tối đa 3 tháng.', 'exception' => ''];
            }
        } else {
            return ['message' => 'Vui lòng nhập khoảng thời gian cần tìm', 'exception' => ''];
        }

        if (!empty($status)) {
            $status = explode(',', $status);
        }

        if (!empty($users)) {
            $users = explode(',', $users);
        }
        $select = [
            'certificates.id',
            'petitioner_name',
            'document_num',
            'appraise_date',
            'document_date',
            'certificate_date',
            'certificate_num',
            'status',
            'certificates.created_at',
            'appraise_purpose_id',
            'created_by',
            'appraiser_id',
            'appraiser_perform_id',
            'appraiser_sale_id',
            DB::raw("case status
                    when 1
                        then 'Mới'
                    when 2
                        then 'Đang thẩm định'
                    when 3
                        then 'Đang duyệt'
                    when 4
                        then 'Hoàn thành'
                    when 6
                        then 'Đang kiểm soát'
                    else 'Huỷ'
                end as status_text"),
            'commission_fee',
        ];
        $with = [
            'createdBy:id,name',
            'appraiser:id,name,user_id',
            // 'appraiserManager:id,name,user_id',
            // 'appraiserConfirm:id,name,user_id',
            'appraiserSale:id,name,user_id',
            'appraiserPerform:id,name,user_id',
            'appraisePurpose:id,name',
            'assetPrice' => function ($q) {
                $q->where('slug', '=', 'total_asset_price');
            },
        ];
        $result = PreCertificate::with($with)
            ->select($select);

        if (isset($status)) {
            $result = $result->whereIn('status', $status);
        }
        if (isset($users)) {
            $result = $result->whereIn('created_by', $users);
        }
        if (isset($fromDate) && isset($toDate)) {
            // $result=$result->whereRaw("to_char('created_at' ,'YYYY-MM-dd') between '". $fromDate->format('Y-m-d') ."' and '". $toDate->format('Y-m-d')."'");
            $result = $result->whereRaw("to_char(created_at , 'YYYY-MM-dd') between '" . $fromDate->format('Y-m-d') . "' and '" . $toDate->format('Y-m-d') . "'");
        }
        if (isset($appraiser)) {
            $result = $result->where('appraiser_id', $appraiser);
        }
        if (isset($appraiserSale)) {
            $result = $result->where('appraiser_sale_id', $appraiserSale);
        }
        if (isset($appraiserConfirm)) {
            $result = $result->where('appraiser_comfirm_id', $appraiserConfirm);
        }
        if (isset($customer)) {
            $result = $result->where('customer_id', $customer);
        }
        $result = $result->get();

        return $result;
    }

    public function exportSelectedCertificateAssets()
    {
        $status = request()->get('status');
        $fromDate = request()->get('fromDate');
        $toDate = request()->get('toDate');

        $isExportLandDetail = request()->get('land_detail') ?? true;
        $isExportLandZoningDetail = request()->get('land_detail_zoning') ?? true;
        $isExportTangibleDetail = request()->get('tangible_detail') ?? true;
        $with = [
            // 'firstTangible',
            // 'propertyDetail',
            'firstTangible:id,appraise_id,building_type_id,total_construction_base,remaining_quality,total_desicion_average',
            'propertyDetail:id,appraise_property_id,land_type_purpose_id,position_type_id,total_area,planning_area,is_zoning',
        ];
        $query = ViewSelectedCertificateAsset::query();
        $query1 = ViewSelectedCertificateApartment::query();

        // dd($query1);
        
        if (isset($status)){
            $status = explode(',', $status);
            $query=$query->whereIn('status',$status);
            $query1=$query1->whereIn('status',$status);
        }

        if (isset($fromDate)){
            $fromDate =  \Carbon\Carbon::createFromFormat('d/m/Y', $fromDate);
            $query=$query->whereRaw("to_char(created_at , 'YYYY-MM-dd') >= '" . $fromDate->format('Y-m-d') . "'");
            $query1=$query1->whereRaw("to_char(created_at , 'YYYY-MM-dd') >= '" . $fromDate->format('Y-m-d') . "'");
        }

        if (isset($toDate)){
            $toDate =  \Carbon\Carbon::createFromFormat('d/m/Y', $toDate);
            $query=$query->whereRaw("to_char(created_at , 'YYYY-MM-dd') <= '" . $toDate->format('Y-m-d') . "'");
            $query1=$query1->whereRaw("to_char(created_at , 'YYYY-MM-dd') <= '" . $toDate->format('Y-m-d') . "'");
        }
        // $result = $query->with($with)->limit(5)->get();
        $result = $query->with($with)->get();
        $result1 = $query1->get();
        if ($isExportLandDetail) {
            $result->append(array_keys(ValueDefault::CERTIFICATION_BRIEF_CUSTOMIZE_LAND_DETAIL_COLUMN_LIST));
            $result1->append(array_keys(ValueDefault::CERTIFICATION_BRIEF_CUSTOMIZE_LAND_DETAIL_COLUMN_LIST));
        }
        if ($isExportLandZoningDetail) {
            $result->append(array_keys(ValueDefault::CERTIFICATION_BRIEF_CUSTOMIZE_LAND_DETAIL_ZONING_COLUMN_LIST));
            $result1->append(array_keys(ValueDefault::CERTIFICATION_BRIEF_CUSTOMIZE_LAND_DETAIL_ZONING_COLUMN_LIST));
        }
        if ($isExportTangibleDetail) {
            $result->append(array_keys(ValueDefault::CERTIFICATION_BRIEF_CUSTOMIZE_TANGIBLE_DETAIL_COLUMN_LIST));
            $result1->append(array_keys(ValueDefault::CERTIFICATION_BRIEF_CUSTOMIZE_TANGIBLE_DETAIL_COLUMN_LIST));
        }
        // $result = $result->toArray();
        // $result1 = $result1->toArray();
        
        // $final_result = array_merge($result, $result1);
        // dd($final_result);
        return $result->merge($result1)->sortBy('pre_certificate_id');
    }

    private function updatePersonaltyPrice(int $id)
    {
        CertificatePrice::where('pre_certificate_id', $id)->forceDelete();
        $totalPrice = PreCertificate::query()->withCount(['personalProperties as total_price'  => function ($query) {
            $query->select(DB::raw('SUM(total_price)::bigint as total'));
        }])->where('id', $id)->first(['id', 'total_price']);
        if (isset($totalPrice->total_price)) {
            CertificatePrice::query()->create(['pre_certificate_id' => $id, 'slug' => 'total_asset_price', 'value' => $totalPrice->total_price]);
        }
    }

    private function updateTotalPrie(int $id)
    {
        CertificatePrice::where('pre_certificate_id', $id)->forceDelete();
        $preCertificate = PreCertificate::query()->where('id', $id)->first();
        $priceArr = $preCertificate->general_asset;
        $price = array_sum(array_column($priceArr,'total_price'));
        if (isset($price)) {
            CertificatePrice::query()->create(['pre_certificate_id' => $id, 'slug' => 'total_asset_price', 'value' => $price]);
        }
    }
    private function UpdatePersonaltyData(int $oldId, int $id, $acronym)
    {
        switch ($acronym) {
            case "TSK":
            case "DCCN":
            case "GTDN":
                if (OtherCertificateAsset::query()->where('personal_property_id', $oldId)->exists()) {
                    $other = OtherCertificateAsset::query()->where('personal_property_id', $oldId)->first()->toArray();
                    $otherLaws = OtherCertificateAssetLaw::query()->where('other_asset_id', $other['id'])->get()->toArray();
                    $otherPrice = OtherCertificateAssetPrice::query()->where('other_asset_id', $other['id'])->first()->toArray();
                    $otherLawInfo = OtherCertificateAssetLawInfo::query()->where('other_asset_id', $other['id'])->first()->toArray();

                    $other['personal_property_id'] = $id;
                    $otherData = new OtherCertificateBrief($other);
                    $otherNewId = OtherCertificateBrief::query()->create($otherData->attributesToArray())->id;
                    foreach ($otherLaws as $law) {
                        $law['other_asset_id'] = $otherNewId;
                        $lawData = new OtherCertificateBriefLaw($law);
                        OtherCertificateBriefLaw::query()->create($lawData->attributesToArray());
                    }
                    $otherPrice['other_asset_id'] = $otherNewId;
                    $otherPriceData = new OtherCertificateBriefPrice($otherPrice);
                    OtherCertificateBriefPrice::query()->create($otherPriceData->attributesToArray());
                    $otherLawInfo['other_asset_id'] = $otherNewId;
                    $otherLawInfoData = new OtherCertificateBriefLawInfo($otherLawInfo);
                    OtherCertificateBriefLawInfo::query()->create($otherLawInfoData->attributesToArray());
                }
                break;
            case "MMTB":
                if (MachineCertificateAsset::query()->where('personal_property_id', $oldId)->exists()) {
                    $other = MachineCertificateAsset::query()->where('personal_property_id', $oldId)->first()->toArray();
                    $otherLaws = MachineCertificateAssetLaw::query()->where('machine_asset_id', $other['id'])->get()->toArray();
                    $otherPrice = MachineCertificateAssetPrice::query()->where('machine_asset_id', $other['id'])->first()->toArray();
                    $otherLawInfo = MachineCertificateAssetLawInfo::query()->where('machine_asset_id', $other['id'])->first()->toArray();

                    $other['personal_property_id'] = $id;
                    $otherData = new MachineCertificateBrief($other);
                    $otherNewId = MachineCertificateBrief::query()->create($otherData->attributesToArray())->id;
                    foreach ($otherLaws as $law) {
                        $law['machine_asset_id'] = $otherNewId;
                        $lawData = new MachineCertificateBriefLaw($law);
                        MachineCertificateBriefLaw::query()->create($lawData->attributesToArray());
                    }
                    $otherPrice['machine_asset_id'] = $otherNewId;
                    $otherPriceData = new MachineCertificateBriefPrice($otherPrice);
                    MachineCertificateBriefPrice::query()->create($otherPriceData->attributesToArray());
                    $otherLawInfo['machine_asset_id'] = $otherNewId;
                    $otherLawInfoData = new MachineCertificateBriefLawInfo($otherLawInfo);
                    MachineCertificateBriefLawInfo::query()->create($otherLawInfoData->attributesToArray());
                }
                break;
            case "PTVT":
                if (VerhicleCertificateAsset::query()->where('personal_property_id', $oldId)->exists()) {
                    $other = VerhicleCertificateAsset::query()->where('personal_property_id', $oldId)->first()->toArray();
                    $otherLaws = VerhicleCertificateAssetLaw::query()->where('verhicle_asset_id', $other['id'])->get()->toArray();
                    $otherPrice = VerhicleCertificateAssetPrice::query()->where('verhicle_asset_id', $other['id'])->first()->toArray();
                    $otherLawInfo = VerhicleCertificateAssetLawInfo::query()->where('verhicle_asset_id', $other['id'])->first()->toArray();

                    $other['personal_property_id'] = $id;
                    $otherData = new VerhicleCertificateBrief($other);
                    $otherNewId = VerhicleCertificateBrief::query()->create($otherData->attributesToArray())->id;
                    foreach ($otherLaws as $law) {
                        $law['verhicle_asset_id'] = $otherNewId;
                        $lawData = new VerhicleCertificateBriefLaw($law);
                        VerhicleCertificateBriefLaw::query()->create($lawData->attributesToArray());
                    }
                    $otherPrice['verhicle_asset_id'] = $otherNewId;
                    $otherPriceData = new VerhicleCertificateBriefPrice($otherPrice);
                    VerhicleCertificateBriefPrice::query()->create($otherPriceData->attributesToArray());
                    $otherLawInfo['verhicle_asset_id'] = $otherNewId;
                    $otherLawInfoData = new VerhicleCertificateBriefLawInfo($otherLawInfo);
                    VerhicleCertificateBriefLawInfo::query()->create($otherLawInfoData->attributesToArray());
                }
                break;
            default:
        }
    }

    private function updateDocumentType ($id)
    {
        $preCertificate = $this->getCertificateAppraise($id);
        $assetGenerals = $preCertificate['general_asset'];
        $documentType = [];
        if (!empty($assetGenerals)) {
            $assetTypeIds = Arr::pluck($assetGenerals, 'asset_type_id');
            $assetType = Dictionary::query()->whereIn('id', $assetTypeIds)->get();
            $documentType = Arr::pluck($assetType, 'acronym');
        }
        $preCertificate['document_type'] = $documentType;
        $preCertificate->update(['document_type' => $documentType]);
        return $preCertificate;
    }

    private function updateRealEstateCertificateId($realEstateId, $id = null, $isAppraise = true , $status = 2, $sub_status = 1) {
        $dataUpdate = [
            'pre_certificate_id' => $id,
            'status' => $status,
            'sub_status' => $sub_status
        ];
        RealEstate::query()->where('id', $realEstateId)->update($dataUpdate);
        if ($isAppraise)
            Appraise::query()->where('id', $realEstateId)->update($dataUpdate);
        else
            ApartmentAsset::query()->where('id', $realEstateId)->update($dataUpdate);
    }
    private function updatePersonalPropertyCertificateId($personalId, $id = null, $status = 2, $sub_status = 1) {
        $dataUpdate = [
            'pre_certificate_id' => $id,
            'status' => $status,
            'sub_status' => $sub_status
        ];
        PersonalProperty::query()->where('id', $personalId)->update($dataUpdate);
    }
    public function getCertificateStatus($id) {
        if (PreCertificate::query()->where('id', $id)->exists()) {
            return PreCertificate::query()->where('id', $id)->first(['id', 'status', 'sub_status']);
        } else {
            return ['message' => ErrorMessage::CERTIFICATE_NOTEXISTS, 'exception' => ''];
        }
    }
    public function getCertificateAppraiseReportData($id)
    {
        $result = [];
        $select = [
            '*'
        ];
        $with = [
            'appraisePurpose:id,name',
            'appraiserConfirm:id,name,appraise_position_id,appraiser_number',
            'appraiserConfirm.appraisePosition:id,description',
            'appraiser:id,name,appraiser_number',
            'appraiserPerform:id,name,appraiser_number',
            'assetPrice:id,pre_certificate_id,slug,value',
            'createdBy:id,name',
            'appraiserManager:id,name,appraiser_number,appraise_position_id',
            'appraiserManager.appraisePosition:id,description',
            'legalDocumentsOnValuation:id,document_type,date,content',
            'legalDocumentsOnConstruction:id,document_type,date,content',
            'legalDocumentsOnLand:id,document_type,date,content',
            'legalDocumentsOnLocal:id,document_type,date,content'
        ];
        $result = $this->model->query()->where('id', $id)->with($with)->first($select);
        // dd($result);
        return $result;
    }

    public function updateSubStatusFromConfig($object)
    {
        try {
            $statusData = [1, 2, 3, 4, 5];
            foreach ($statusData as $status) {
                $configs = array_filter($object['principle'], function ($value) use ($status) {
                    return ($value['isActive'] == 1 && $value['status'] == $status);
                });
                $subStatus = Arr::pluck($configs, 'sub_status');
                $minSubStatus = min(array_column($configs, 'sub_status'));
                $this->model->query()->where('status', $status)->whereNotIn('sub_status', $subStatus)->update([
                    'sub_status' => $minSubStatus,
                    'updated_at' => DB::raw('updated_at')
                ]);
            }
            return ['message' => 'Cập nhật thành công'];
        } catch (Exception $ex) {
            return ['message' => $ex->getMessage(), 'exception' => $ex];
        }
    }
    public function uploadDocument($id, $description, $request)
    {
        return DB::transaction(function () use ($id, $description, $request){
            try {
                $result = [];
                $check = $this->checkAuthorizationPreCertificate($id);
                if (!empty($check))
                    return $check;
                // $now = Carbon::now()->timezone('Asia/Ho_Chi_Minh');
                // $path = env('STORAGE_OTHERS') . '/' . 'comparison_brief/' . $now->year . '/' . $now->month . '/' . $id . '/';
                $path = env('STORAGE_OTHERS') . '/' . 'comparison_brief/upload/' . $id . '/';
                $this->removeUploadFile($id, $description, $path);
                $files = $request->file('files');
                $user = CommonService::getUser();
                // $userName = CommonService::withoutAccents($user->name);
                $logDescription = '';
                // $fileName = '';
               $logDescription = $this->getOtherDescription($description);
                if (isset($files) && !empty($files)) {
                    foreach ($files as $file) {
                        $fileName = $file->getClientOriginalName();
                        $fileType = $file->getClientOriginalExtension();
                        $fileSize = $file->getSize();
                        $name = $path . Uuid::uuid4()->toString() . '.' . $fileType;
                        Storage::put($name, file_get_contents($file));
                        $fileUrl = Storage::url($name);
                        $item = [
                            'pre_certificate_id' => $id,
                            'name' => $fileName,
                            'link' => $fileUrl,
                            'type' => $fileType,
                            'size' => $fileSize,
                            'description' => $description,
                            'created_by' => $user->id,
                        ];
                        $item = new PreCertificateOtherDocuments($item);
                        PreCertificateOtherDocuments::query()->updateOrCreate(['pre_certificate_id' => $id, 'description' => $description], $item->attributesToArray());
                    }
                    $edited = PreCertificate::where('id', $id)->first();
                    $edited2 = PreCertificateOtherDocuments::where('pre_certificate_id', $id)->first();
                    # activity-log upload file
                    $this->CreateActivityLog($edited, $edited2, 'upload_file', 'upload '. $logDescription);
                }

                $result = PreCertificateOtherDocuments::where('pre_certificate_id', $id)
                    ->with('createdBy')
                    ->get();
                return $result;
            } catch (Exception $exception) {
                Log::error($exception);
                throw $exception;
            }
        });
    }
    public function deleteDocument($id)
    {
        try {
            $other = PreCertificateOtherDocuments::query()->where('id', $id)->first();
            $preCertificateId = $other->pre_certificate_id;
            $check = $this->checkAuthorizationPreCertificate($preCertificateId);
            if (!empty($check))
                return $check;
            $description = $other->description;
            $logDescription = $this->getOtherDescription($description);
            $path = env('STORAGE_OTHERS') . '/' . 'comparison_brief/upload/' . $id . '/';
            $url = $other->link;
            $arrUrl = explode('/', $url);
            $fileName = array_reverse($arrUrl)[0];
            $name = $path . $fileName;
            Storage::disk(env('FILESYSTEM_DRIVER'))->delete($name);
            $other->delete();
            $preCertificate = $this->model->query()->where('id', $preCertificateId)->with('otherDocuments')->first('id');
            $this->CreateActivityLog($preCertificate, $preCertificate, 'upload_document', 'xóa file '. $logDescription);
            return $preCertificate->otherDocuments;
        } catch (Exception $ex) {
            Log::error($ex);
            return ['message' => $ex->getMessage(), 'exception' => $ex];
        }
    }
    private function getOtherDescription ($description)
    {
        $array = [
            'certificate_report' => 'Chứng thư thẩm định',
            'appraisal_report' => 'Chứng thư thẩm định',
            'appendix1_report' => 'Bảng điều chỉnh QSDĐ',
            'appendix2_report' => 'Bảng điều chỉnh CTXD',
            'appendix3_report' => 'Hình ảnh, hiện trạng',
        ];
        if (isset($array[$description]))
            return $array[$description];
        else
            return 'Phiếu thu thập TSSS';
    }
    private function removeUploadFile ($id, $description, $path)
    {
        try {
            $others = PreCertificateOtherDocuments::query()->where(['pre_certificate_id' => $id, 'description' => $description])->get();
            if (!empty($others)) {
                foreach ($others as $other) {
                    $url = $other->link;
                    $arrUrl = explode('/', $url);
                    $fileName = array_reverse($arrUrl)[0];
                    $name = $path . $fileName;
                    Storage::disk(env('FILESYSTEM_DRIVER'))->delete($name);
                    $other->delete();
                }
            }
        } catch (Exception $ex) {
            Log::error($ex);
            return ['message' => $ex->getMessage(), 'exception' => $ex];
        }
    }

    private function checkAuthorizationPreCertificate ($id)
    {
        $check = null;
        if ($this->model->query()->where('id', $id)->exists()) {
            $user = CommonService::getUser();
            $role = $user->roles->last();
            $result = $this->model->query()->where('id', $id);
            $userId = $user->id;
            if ($role->name == 'USER') {
                $result = $result->where(function ($query) use ($userId) {
                    $query = $query->whereHas('createdBy', function ($q) use ($userId) {
                        return $q->where('id', $userId);
                    });
                    $query = $query->orwhereHas('appraiserSale', function ($q) use ($userId) {
                        return $q->where('user_id', $userId);
                    });
                    $query = $query->orwhereHas('appraiserPerform', function ($q) use ($userId) {
                        return $q->where('user_id', $userId);
                    });
                    $query = $query->orwhereHas('appraiserBusinessManager', function ($q) use ($userId) {
                        return $q->where('user_id', $userId);
                    });
                });
            }
            $result = $result->first();
            if (empty($result))
                $check = ['message' => 'Bạn không có quyền ở HSTĐSB '. $id , 'exception' => '', 'statusCode' => 403];
        } else {
            $check = ['message' => ErrorMessage::CERTIFICATE_NOTEXISTS . ' ' . $id, 'exception' => '', 'statusCode' => 403];
        }
        return $check;
    }
}
