<?php

namespace App\Repositories;

use App;
use App\Contracts\CertificateRepository;
use App\Enum\CompareMaterData;
use App\Enum\ErrorMessage;
use App\Models\Certificate;
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
use App\Models\CertificateOtherDocuments;
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
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\TemplateProcessor;

use App\Mail\JustTesting;
use Illuminate\Support\Facades\Mail;
use App\Models\PreCertificatePayments;

use function PHPUnit\Framework\isEmpty;

class  EloquentCertificateRepository extends EloquentRepository implements CertificateRepository
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
                    if ($status == 4) {
                        // if(!$this->checkExistsAppraise($id))
                        // {
                        //     return ['message' => 'Bạn không thể xét duyệt do chưa có thông tin tài sản thẩm định. ','exception' => '' ];
                        // }
                        $oldCertificate = Certificate::where('id', $id)->first();
                        $appraises = $oldCertificate->appraises;
                        foreach ($appraises as $appraiseTmp) {
                            Appraise::where('id', $appraiseTmp->appraise_id)->update(['status' => 4]); // updateStatus : 4 = closed
                            CertificateAsset::where('appraise_id', $appraiseTmp->appraise_id)->update(['status' => 4]);
                        }
                    } else if ($status == 5) {
                        $oldCertificate = Certificate::where('id', $id)->first();
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
    public function otherDocumentUpload($id, $request)
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
                            'certificate_id' => $id,
                            'name' => $fileName,
                            'link' => $fileUrl,
                            'type' => $fileType,
                            'size' => $fileSize,
                            'description' => 'appendix',
                            'created_by' => $user->id,
                        ];

                        $item = new CertificateOtherDocuments($item);
                        QueryBuilder::for($item)->insert($item->attributesToArray());
                        $result[] = $item;
                    }
                    $edited = Certificate::where('id', $id)->first();
                    $edited2 = CertificateOtherDocuments::where('certificate_id', $id)->first();
                    # activity-log upload file
                    $this->CreateActivityLog($edited, $edited2, 'upload_file', 'tải phụ lục');
                    // chưa lấy ra được model user và id user
                }

                $result = CertificateOtherDocuments::where('certificate_id', $id)
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
    public function otherDocumentOriginalUpload($id, $request)
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
                            'certificate_id' => $id,
                            'name' => $fileName,
                            'link' => $fileUrl,
                            'type' => $fileType,
                            'size' => $fileSize,
                            'description' => 'original',
                            'created_by' => $user->id,
                        ];

                        $item = new CertificateOtherDocuments($item);
                        QueryBuilder::for($item)->insert($item->attributesToArray());
                        $result[] = $item;
                    }
                    $edited = Certificate::where('id', $id)->first();
                    $edited2 = CertificateOtherDocuments::where('certificate_id', $id)->first();
                    # activity-log upload file
                    $this->CreateActivityLog($edited, $edited2, 'upload_file', 'tải hồ sơ gốc');
                    // chưa lấy ra được model user và id user
                }

                $result = CertificateOtherDocuments::where('certificate_id', $id)
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
    public function testDocumentUpload($request)
    {
        return DB::transaction(function () use ($request) {
            try {
                $result = [];
                $file = $request->file('files');
                // dd($files);
                $user = CommonService::getUser();

                // Lưu dữ liệu binary vào một tệp tạm thời
                $tempFilePath = storage_path('app/public');
                // Lưu file tạm thời
                $file->move($tempFilePath, '/temp.docx');
                // Đường dẫn đến mẫu DOCX
                $templatePath = $tempFilePath . '/temp.docx';

                // Khởi tạo TemplateProcessor với mẫu DOCX
                $templateProcessor = new TemplateProcessor($templatePath);

                // Dữ liệu thực tế để thay thế placeholder
                $data = [
                    'name' => 'John Doe',
                    'address' => '123 Main Street',
                ];

                // Thay thế giá trị placeholder trong mẫu bằng dữ liệu thực tế
                foreach ($data as $key => $value) {
                    $templateProcessor->setValue($key, $value);
                }

                // Lưu tệp DOCX sau khi thực hiện thay thế
                $outputPath = $tempFilePath . '/output.docx';
                $templateProcessor->saveAs($outputPath);

                echo 'File DOCX created successfully!';

                // Xóa tệp tạm thời
                unlink($tempFilePath . '/temp.docx');

                return;
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
                $certificateId = CertificateOtherDocuments::select('certificate_id')->where('id', $id)->get();
                $item = CertificateOtherDocuments::where('id', $id)->delete();
                $edited = Certificate::where('id', $certificateId[0]->certificate_id)->first();
                $edited2 = CertificateOtherDocuments::where('id', $id)->get();
                $item = CertificateOtherDocuments::where('id', $id)->delete();
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
                $item = CertificateOtherDocuments::where('id', $id)->first();
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
                if (request()->has('is_guest')) {
                } else {
                    if (isset($query->created_by) && ($query->created_by != 'Tất cả người tạo')) {
                        return $q->where('name', '=', $query->created_by);
                    }

                    $role = $user->roles->last();
                    if (($role->name !== 'SUPER_ADMIN' && $role->name !== 'ROOT_ADMIN' && $role->name !== 'SUB_ADMIN' && $role->name !== 'ADMIN' && $role->name !== 'Accounting')) {
                        return $q->where('id', $user->id);
                    }
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

        if (request()->has('is_guest')) {
            if (isset($user->customer_group_id)) {
                $result = $result->where('customer_group_id', '=', $user->customer_group_id);
                $result = $result
                    ->forPage($page, $perPage)
                    ->paginate($perPage);

                foreach ($result as $stt => $item) {
                    $result[$stt]->append('total_asset_price');
                    //$result[$stt]->append('total_asset_price_round');
                }
            } else {
                $result = [];
            }
        } else {
            $result = $result
                ->forPage($page, $perPage)
                ->paginate($perPage);

            foreach ($result as $stt => $item) {
                $result[$stt]->append('total_asset_price');
                //$result[$stt]->append('total_asset_price_round');
            }
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
                ->with('constructionCompany')
                ->with('comparisonFactor')
                ->with('createdBy')
                ->with('appraisePurpose')
                ->with('appraises')

                ->with('appraises.province')
                ->with('appraises.district')
                ->with('appraises.ward')
                ->with('appraises.street')
                ->with('appraises.distance')
                ->with('appraises.assetType')
                ->with('appraises.pic.picType')
                ->with('appraises.topographic')
                ->with('appraises.appraiseApproach')
                ->with('appraises.appraisePrinciple')
                ->with('appraises.appraiseMethodUsed')
                ->with('appraises.appraiseBasisProperty')
                ->with('appraises.properties.propertyDetail')
                ->with('appraises.properties.propertyTurningTime')
                ->with('appraises.properties.propertyTurningTime.material')
                ->with('appraises.properties.propertyDetail.landTypePurpose')
                ->with('appraises.properties.propertyDetail.positionType')
                ->with('appraises.properties.legal')
                ->with('appraises.properties.zoning')
                ->with('appraises.properties.landType')
                ->with('appraises.properties.landShape')
                ->with('appraises.properties.business')
                ->with('appraises.properties.electricWater')
                ->with('appraises.properties.socialSecurity')
                ->with('appraises.properties.fengShui')
                ->with('appraises.properties.paymenMethod')
                ->with('appraises.properties.conditions')
                ->with('appraises.properties.material')
                ->with('appraises.tangibleAssets.buildingType')
                ->with('appraises.tangibleAssets.buildingCategory')
                ->with('appraises.tangibleAssets.rate')
                ->with('appraises.tangibleAssets.structure')
                ->with('appraises.tangibleAssets.crane')
                ->with('appraises.tangibleAssets.aperture')
                ->with('appraises.tangibleAssets.factoryType')
                ->with('appraises.otherAssets')
                //->with('appraises.constructionCompany.constructionCompany')
                ->with('appraises.constructionCompany')
                ->with('appraises.appraiseLaw.law')
                ->with('appraises.appraiseLaw.lawDetails')
                ->with('appraises.appraiseLaw.lawDetails.landTypePurpose')
                ->with('appraises.appraiseLaw.landDetails')
                //->with('appraises.assetGeneral')
                ->with('appraises.appraiseHasAssets')
                ->with('appraises.createdBy')
                ->with('appraises.comparisonFactor')
                ->with('appraises.appraiseAdapter')
                ->with('appraises.comparisonTangibleFactor')
                ->with('appraises.version')
                /* ->with('appraises.assetGeneral.createdBy')
                ->with('appraises.assetGeneral.province')
                ->with('appraises.assetGeneral.district')
                ->with('appraises.assetGeneral.ward')
                ->with('appraises.assetGeneral.street')
                ->with('appraises.assetGeneral.distance')
                ->with('appraises.assetGeneral.assetType')
                ->with('appraises.assetGeneral.source')
                ->with('appraises.assetGeneral.transactionType')
                ->with('appraises.assetGeneral.apartment')
                ->with('appraises.assetGeneral.pic')
                ->with('appraises.assetGeneral.version')
                ->with('appraises.assetGeneral.topographicData')
                ->with('appraises.assetGeneral.properties.propertyDetail')
                ->with('appraises.assetGeneral.properties.comparePropertyTurningTime')
                ->with('appraises.assetGeneral.properties.comparePropertyTurningTime.material')
                ->with('appraises.assetGeneral.properties.propertyDetail.landTypePurposeData')
                ->with('appraises.assetGeneral.properties.propertyDetail.positionType')
                ->with('appraises.assetGeneral.properties.comparePropertyDoc')
                ->with('appraises.assetGeneral.properties.pic')
                ->with('appraises.assetGeneral.properties.legal')
                ->with('appraises.assetGeneral.properties.zoning')
                ->with('appraises.assetGeneral.properties.landType')
                ->with('appraises.assetGeneral.properties.landShape')
                ->with('appraises.assetGeneral.properties.business')
                ->with('appraises.assetGeneral.properties.electricWater')
                ->with('appraises.assetGeneral.properties.socialSecurity')
                ->with('appraises.assetGeneral.properties.fengShui')
                ->with('appraises.assetGeneral.properties.paymenMethod')
                ->with('appraises.assetGeneral.properties.conditions')
                ->with('appraises.assetGeneral.properties.material')
                ->with('appraises.assetGeneral.tangibleAssets.buildingType')
                ->with('appraises.assetGeneral.tangibleAssets.buildingCategory')
                ->with('appraises.assetGeneral.tangibleAssets.compareProperty')
                ->with('appraises.assetGeneral.tangibleAssets.pic')
                ->with('appraises.assetGeneral.tangibleAssets.rate')
                ->with('appraises.assetGeneral.tangibleAssets.structure')
                ->with('appraises.assetGeneral.tangibleAssets.crane')
                ->with('appraises.assetGeneral.tangibleAssets.aperture')
                ->with('appraises.assetGeneral.tangibleAssets.factoryType')
                ->with('appraises.assetGeneral.otherAssets.otherTypeAsset')
                ->with('appraises.assetGeneral.otherAssets.pic')
                ->with('appraises.assetGeneral.blockSpecification')
                ->with('appraises.assetGeneral.blockSpecification.basicUtilities')
                ->with('appraises.assetGeneral.blockSpecification.blockLists')
                ->with('appraises.assetGeneral.roomDetails')
                ->with('appraises.assetGeneral.roomDetails.roomFurnitureDetails')
                ->with('appraises.assetGeneral.roomDetails.direction')
                ->with('appraises.assetGeneral.roomDetails.furnitureQuality') */
                ->with('appraises.assetUnitPrice')
                ->with('appraises.assetUnitPrice.landTypeData')
                ->with('appraises.assetUnitPrice.createdBy')
                ->with('otherDocuments')
                ->with('otherDocuments.createdBy')
                ->with('assetPrice')
                ->with('appraises.appraisalMethods')
                ->with('exportDocuments')
                ->first();
        }

        foreach ($result->appraises as $stt => $asset) {
            $result->appraises[$stt]->comparison_factor_custom = $this->getComparisonFactor($asset->id);
            //$result->appraises[$stt]->construction_company_custom = $this->getConstructionCompany($id, $asset->id);
            //$result->appraises[$stt]->construction_company_custom = $this->getConstructionCompany($id, $asset->id);
            $eloquentBuildingPriceRepository = new EloquentBuildingPriceRepository(new BuildingPrice());
            if (isset($asset->tangibleAssets[0])) {
                $result->appraises[$stt]->building_price = $eloquentBuildingPriceRepository->getAverageBuildPriceV2($asset->tangibleAssets[0]);
            } else {
                $result->appraises[$stt]->building_price = 0;
            }

            // $result->appraises[$stt]->append('tangible_assets');
            // $result->appraises[$stt]->append('appraise_law');
            $result->appraises[$stt]->append('asset_general');
            $result->appraises[$stt]->tangibleAssets = $asset->tangibleAssets;
            $result->appraises[$stt]->appraiseLaw = $asset->appraiseLaw;
            $result->appraises[$stt]->assetGeneral = $result->appraises[$stt]->asset_general;
            // $result->appraises[$stt]->tangibleAssets = $result->appraises[$stt]->tangible_assets;

            $asset->assetGeneral = $result->appraises[$stt]->asset_general;

            $result->appraises[$stt]->append('layer_cutting_procedure');
            $result->appraises[$stt]->append('layer_cutting_price');
            $result->appraises[$stt]->append('unify_indicative_price_slug');
            $result->appraises[$stt]->append('composite_land_remaning_slug');
            $result->appraises[$stt]->append('composite_land_remaning_value');
            $result->appraises[$stt]->append('planning_violation_price_slug');
            $result->appraises[$stt]->append('planning_violation_price_value');

            $asset1 = $asset->assetGeneral[0] ?? null;
            $asset2 = $asset->assetGeneral[1] ?? null;
            $asset3 = $asset->assetGeneral[2] ?? null;
            $isExistAsset1 = false;
            $isExistAsset2 = false;
            $isExistAsset3 = false;
            if (isset($asset1) && isset($asset2) && isset($asset3)) {
                foreach ($result->appraises[$stt]->appraiseAdapter as $item) {
                    if ($item->asset_general_id == $asset1->id) {
                        $isExistAsset1 = true;
                    }
                    if ($item->asset_general_id == $asset2->id) {
                        $isExistAsset2 = true;
                    }
                    if ($item->asset_general_id == $asset3->id) {
                        $isExistAsset3 = true;
                    }
                }
                if (!$isExistAsset1) {
                    $result->appraises[$stt]->appraiseAdapter[] = [
                        'appraise_id' => $asset->id,
                        'asset_general_id' => $asset1->id,
                        'percent' => intval($asset1->adjust_percent) + 100,
                    ];
                }
                if (!$isExistAsset2) {
                    $result->appraises[$stt]->appraiseAdapter[] = [
                        'appraise_id' => $asset->id,
                        'asset_general_id' => $asset2->id,
                        'percent' => intval($asset2->adjust_percent) + 100,
                    ];
                }
                if (!$isExistAsset3) {
                    $result->appraises[$stt]->appraiseAdapter[] = [
                        'appraise_id' => $asset->id,
                        'asset_general_id' => $asset3->id,
                        'percent' => intval($asset3->adjust_percent) + 100,
                    ];
                }
            }
        }
        $result->append('round_certificate_total');

        //CommonService::getCertificateAssetPriceTotal($result);

        /* $constructionCompanies = [];
        foreach($result->constructionCompany as $constructionCompany) {
            $constructionCompanies[$constructionCompany->appraise_id]['construction_company'][$constructionCompany->id] = $constructionCompany;
        }
        $result->construction_company_custom = $constructionCompanies;  */
        return $result;
    }
    public function dataPrintExport($id)
    {
        $version = request()->get('version');
        $result = null;
        if ($version && !is_array($version)) {
            $result = $this->findVersionById($id, $version);
        }
        if (!$result) {
            $result =  $this->model->query()
                ->where('id', '=', $id)
                ->with('appraiser')
                ->with('appraiserManager')
                ->with('appraiserManager.appraisePosition:id,description')
                ->with('appraiserConfirm')
                ->with('appraiserConfirm.appraisePosition:id,description')
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
                ->with('constructionCompany')
                ->with('comparisonFactor')
                ->with('createdBy')
                ->with('appraisePurpose')

                ->with('apartmentAssetPrint')
                ->with('apartmentAssetPrint.apartmentAssetProperties')
                ->with('apartmentAssetPrint.law')
                ->with('appraises')

                ->with('appraises.province')
                ->with('appraises.district')
                ->with('appraises.ward')
                ->with('appraises.street')
                ->with('appraises.distance')
                ->with('appraises.assetType')
                ->with('appraises.pic.picType')
                ->with('appraises.topographic')
                ->with('appraises.appraiseApproach')
                ->with('appraises.appraisePrinciple')
                ->with('appraises.appraiseMethodUsed')
                ->with('appraises.appraiseBasisProperty')
                ->with('appraises.properties.propertyDetail')
                ->with('appraises.properties.propertyTurningTime')
                ->with('appraises.properties.propertyTurningTime.material')
                ->with('appraises.properties.propertyDetail.landTypePurpose')
                ->with('appraises.properties.propertyDetail.positionType')
                ->with('appraises.properties.legal')
                ->with('appraises.properties.zoning')
                ->with('appraises.properties.landType')
                ->with('appraises.properties.landShape')
                ->with('appraises.properties.business')
                ->with('appraises.properties.electricWater')
                ->with('appraises.properties.socialSecurity')
                ->with('appraises.properties.fengShui')
                ->with('appraises.properties.paymenMethod')
                ->with('appraises.properties.conditions')
                ->with('appraises.properties.material')
                ->with('appraises.tangibleAssets.buildingType')
                ->with('appraises.tangibleAssets.buildingCategory')
                ->with('appraises.tangibleAssets.rate')
                ->with('appraises.tangibleAssets.structure')
                ->with('appraises.tangibleAssets.crane')
                ->with('appraises.tangibleAssets.aperture')
                ->with('appraises.tangibleAssets.factoryType')
                ->with('appraises.otherAssets')
                ->with('appraises.constructionCompany')
                ->with('appraises.appraiseLaw.law')
                ->with('appraises.appraiseLaw.lawDetails')
                ->with('appraises.appraiseLaw.lawDetails.landTypePurpose')
                ->with('appraises.appraiseLaw.landDetails')
                ->with('appraises.appraiseHasAssets')
                ->with('appraises.createdBy')
                ->with('appraises.comparisonFactor')
                ->with('appraises.appraiseAdapter')
                ->with('appraises.comparisonTangibleFactor')
                ->with('appraises.version')
                ->with('appraises.assetUnitPrice')
                ->with('appraises.assetUnitPrice.landTypeData')
                ->with('appraises.assetUnitPrice.createdBy')
                ->with('otherDocuments')
                ->with('otherDocuments.createdBy')
                ->with('assetPrice')
                ->with('appraises.appraisalMethods')
                ->first();
        }

        foreach ($result->appraises as $stt => $asset) {
            $result->appraises[$stt]->comparison_factor_custom = $this->getComparisonFactor($asset->id);
            //$result->appraises[$stt]->construction_company_custom = $this->getConstructionCompany($id, $asset->id);
            //$result->appraises[$stt]->construction_company_custom = $this->getConstructionCompany($id, $asset->id);
            $eloquentBuildingPriceRepository = new EloquentBuildingPriceRepository(new BuildingPrice());
            if (isset($asset->tangibleAssets[0])) {
                $result->appraises[$stt]->building_price = $eloquentBuildingPriceRepository->getAverageBuildPriceV2($asset->tangibleAssets[0]);
            } else {
                $result->appraises[$stt]->building_price = 0;
            }

            // $result->appraises[$stt]->append('tangible_assets');
            // $result->appraises[$stt]->append('appraise_law');
            $result->appraises[$stt]->append('asset_general');
            $result->appraises[$stt]->tangibleAssets = $asset->tangibleAssets;
            $result->appraises[$stt]->appraiseLaw = $asset->appraiseLaw;
            $result->appraises[$stt]->assetGeneral = $result->appraises[$stt]->asset_general;
            // $result->appraises[$stt]->tangibleAssets = $result->appraises[$stt]->tangible_assets;

            $asset->assetGeneral = $result->appraises[$stt]->asset_general;

            $result->appraises[$stt]->append('layer_cutting_procedure');
            $result->appraises[$stt]->append('layer_cutting_price');
            $result->appraises[$stt]->append('unify_indicative_price_slug');
            $result->appraises[$stt]->append('composite_land_remaning_slug');
            $result->appraises[$stt]->append('composite_land_remaning_value');
            $result->appraises[$stt]->append('planning_violation_price_slug');
            $result->appraises[$stt]->append('planning_violation_price_value');

            $asset1 = $asset->assetGeneral[0] ?? null;
            $asset2 = $asset->assetGeneral[1] ?? null;
            $asset3 = $asset->assetGeneral[2] ?? null;
            $isExistAsset1 = false;
            $isExistAsset2 = false;
            $isExistAsset3 = false;
            if (isset($asset1) && isset($asset2) && isset($asset3)) {
                foreach ($result->appraises[$stt]->appraiseAdapter as $item) {
                    if ($item->asset_general_id == $asset1->id) {
                        $isExistAsset1 = true;
                    }
                    if ($item->asset_general_id == $asset2->id) {
                        $isExistAsset2 = true;
                    }
                    if ($item->asset_general_id == $asset3->id) {
                        $isExistAsset3 = true;
                    }
                }
                if (!$isExistAsset1) {
                    $result->appraises[$stt]->appraiseAdapter[] = [
                        'appraise_id' => $asset->id,
                        'asset_general_id' => $asset1->id,
                        'percent' => intval($asset1->adjust_percent) + 100,
                    ];
                }
                if (!$isExistAsset2) {
                    $result->appraises[$stt]->appraiseAdapter[] = [
                        'appraise_id' => $asset->id,
                        'asset_general_id' => $asset2->id,
                        'percent' => intval($asset2->adjust_percent) + 100,
                    ];
                }
                if (!$isExistAsset3) {
                    $result->appraises[$stt]->appraiseAdapter[] = [
                        'appraise_id' => $asset->id,
                        'asset_general_id' => $asset3->id,
                        'percent' => intval($asset3->adjust_percent) + 100,
                    ];
                }
            }
        }
        $result->append('round_certificate_total');

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
        $version = request()->get('version');
        $result = null;
        if ($version && !is_array($version)) {
            $result = $this->findVersionById($id, $version);
        }
        if (!$result) {
            $result =  $this->model->query()
                ->where('id', '=', $id)
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
                ->with('constructionCompany')
                ->with('comparisonFactor')
                ->with('createdBy')
                ->with('appraisePurpose')
                ->with('appraises')

                ->with('appraises.province')
                ->with('appraises.district')
                ->with('appraises.ward')
                ->with('appraises.street')
                ->with('appraises.distance')
                ->with('appraises.assetType')
                ->with('appraises.pic.picType')
                ->with('appraises.topographic')
                ->with('appraises.appraiseApproach')
                ->with('appraises.appraisePrinciple')
                ->with('appraises.appraiseMethodUsed')
                ->with('appraises.appraiseBasisProperty')
                ->with('appraises.properties.propertyDetail')
                ->with('appraises.properties.propertyTurningTime')
                ->with('appraises.properties.propertyTurningTime.material')
                ->with('appraises.properties.propertyDetail.landTypePurpose')
                ->with('appraises.properties.propertyDetail.positionType')
                ->with('appraises.properties.legal')
                ->with('appraises.properties.zoning')
                ->with('appraises.properties.landType')
                ->with('appraises.properties.landShape')
                ->with('appraises.properties.business')
                ->with('appraises.properties.electricWater')
                ->with('appraises.properties.socialSecurity')
                ->with('appraises.properties.fengShui')
                ->with('appraises.properties.paymenMethod')
                ->with('appraises.properties.conditions')
                ->with('appraises.properties.material')
                ->with('appraises.tangibleAssets.buildingType')
                ->with('appraises.tangibleAssets.buildingCategory')
                ->with('appraises.tangibleAssets.rate')
                ->with('appraises.tangibleAssets.structure')
                ->with('appraises.tangibleAssets.crane')
                ->with('appraises.tangibleAssets.aperture')
                ->with('appraises.tangibleAssets.factoryType')
                ->with('appraises.otherAssets')
                //->with('appraises.constructionCompany.constructionCompany')
                ->with('appraises.constructionCompany')
                ->with('appraises.appraiseLaw.law')
                ->with('appraises.appraiseLaw.lawDetails')
                ->with('appraises.appraiseLaw.lawDetails.landTypePurpose')
                ->with('appraises.appraiseLaw.landDetails')
                ->with('appraises.assetGeneral')
                ->with('appraises.appraiseHasAssets')
                ->with('appraises.createdBy')
                ->with('appraises.comparisonFactor')
                ->with('appraises.appraiseAdapter')
                ->with('appraises.comparisonTangibleFactor')
                ->with('appraises.version')
                ->with('appraises.assetGeneral.createdBy')
                ->with('appraises.assetGeneral.province')
                ->with('appraises.assetGeneral.district')
                ->with('appraises.assetGeneral.ward')
                ->with('appraises.assetGeneral.street')
                ->with('appraises.assetGeneral.distance')
                ->with('appraises.assetGeneral.assetType')
                ->with('appraises.assetGeneral.source')
                ->with('appraises.assetGeneral.transactionType')
                ->with('appraises.assetGeneral.apartment')
                ->with('appraises.assetGeneral.pic')
                ->with('appraises.assetGeneral.version')
                ->with('appraises.assetGeneral.topographicData')
                ->with('appraises.assetGeneral.properties.propertyDetail')
                ->with('appraises.assetGeneral.properties.comparePropertyTurningTime')
                ->with('appraises.assetGeneral.properties.comparePropertyTurningTime.material')
                ->with('appraises.assetGeneral.properties.propertyDetail.landTypePurposeData')
                ->with('appraises.assetGeneral.properties.propertyDetail.positionType')
                ->with('appraises.assetGeneral.properties.comparePropertyDoc')
                ->with('appraises.assetGeneral.properties.pic')
                ->with('appraises.assetGeneral.properties.legal')
                ->with('appraises.assetGeneral.properties.zoning')
                ->with('appraises.assetGeneral.properties.landType')
                ->with('appraises.assetGeneral.properties.landShape')
                ->with('appraises.assetGeneral.properties.business')
                ->with('appraises.assetGeneral.properties.electricWater')
                ->with('appraises.assetGeneral.properties.socialSecurity')
                ->with('appraises.assetGeneral.properties.fengShui')
                ->with('appraises.assetGeneral.properties.paymenMethod')
                ->with('appraises.assetGeneral.properties.conditions')
                ->with('appraises.assetGeneral.properties.material')
                ->with('appraises.assetGeneral.tangibleAssets.buildingType')
                ->with('appraises.assetGeneral.tangibleAssets.buildingCategory')
                ->with('appraises.assetGeneral.tangibleAssets.compareProperty')
                ->with('appraises.assetGeneral.tangibleAssets.pic')
                ->with('appraises.assetGeneral.tangibleAssets.rate')
                ->with('appraises.assetGeneral.tangibleAssets.structure')
                ->with('appraises.assetGeneral.tangibleAssets.crane')
                ->with('appraises.assetGeneral.tangibleAssets.aperture')
                ->with('appraises.assetGeneral.tangibleAssets.factoryType')
                ->with('appraises.assetGeneral.otherAssets.otherTypeAsset')
                ->with('appraises.assetGeneral.otherAssets.pic')
                ->with('appraises.assetGeneral.blockSpecification')
                ->with('appraises.assetGeneral.blockSpecification.basicUtilities')
                ->with('appraises.assetGeneral.blockSpecification.blockLists')
                ->with('appraises.assetGeneral.roomDetails')
                ->with('appraises.assetGeneral.roomDetails.roomFurnitureDetails')
                ->with('appraises.assetGeneral.roomDetails.direction')
                ->with('appraises.assetGeneral.roomDetails.furnitureQuality')
                ->with('appraises.assetUnitPrice')
                ->with('appraises.assetUnitPrice.landTypeData')
                ->with('appraises.assetUnitPrice.createdBy')
                ->with('otherDocuments')
                ->with('otherDocuments.createdBy')
                ->with('assetPrice')
                ->with('appraises.appraisalMethods')
                ->first();
        }

        foreach ($result->appraises as $stt => $asset) {
            $result->appraises[$stt]->comparison_factor_custom = $this->getComparisonFactor($asset->id);
            //$result->appraises[$stt]->construction_company_custom = $this->getConstructionCompany($id, $asset->id);
            $eloquentBuildingPriceRepository = new EloquentBuildingPriceRepository(new BuildingPrice());
            if (isset($asset->tangibleAssets[0])) {
                $result->appraises[$stt]->building_price = $eloquentBuildingPriceRepository->getAverageBuildPriceV2($asset->tangibleAssets[0]);
            } else {
                $result->appraises[$stt]->building_price = 0;
            }

            $result->appraises[$stt]->append('layer_cutting_procedure');
            $result->appraises[$stt]->append('layer_cutting_price');
            $result->appraises[$stt]->append('unify_indicative_price_slug');
            $result->appraises[$stt]->append('composite_land_remaning_slug');
            $result->appraises[$stt]->append('composite_land_remaning_value');
            $result->appraises[$stt]->append('planning_violation_price_slug');
            $result->appraises[$stt]->append('planning_violation_price_value');

            $asset1 = $asset->assetGeneral[0] ?? null;
            $asset2 = $asset->assetGeneral[1] ?? null;
            $asset3 = $asset->assetGeneral[2] ?? null;
            $isExistAsset1 = false;
            $isExistAsset2 = false;
            $isExistAsset3 = false;
            foreach ($result->appraises[$stt]->appraiseAdapter as $item) {
                if ($item->asset_general_id == $asset1->id) {
                    $isExistAsset1 = true;
                }
                if ($item->asset_general_id == $asset2->id) {
                    $isExistAsset2 = true;
                }
                if ($item->asset_general_id == $asset3->id) {
                    $isExistAsset3 = true;
                }
            }
            if (!$isExistAsset1) {
                $result->appraises[$stt]->appraiseAdapter[] = [
                    'appraise_id' => $asset->id,
                    'asset_general_id' => $asset1->id,
                    'percent' => intval($asset1->adjust_percent) + 100,
                ];
            }
            if (!$isExistAsset2) {
                $result->appraises[$stt]->appraiseAdapter[] = [
                    'appraise_id' => $asset->id,
                    'asset_general_id' => $asset2->id,
                    'percent' => intval($asset2->adjust_percent) + 100,
                ];
            }
            if (!$isExistAsset3) {
                $result->appraises[$stt]->appraiseAdapter[] = [
                    'appraise_id' => $asset->id,
                    'asset_general_id' => $asset3->id,
                    'percent' => intval($asset3->adjust_percent) + 100,
                ];
            }
        }

        $result->append('round_certificate_total');

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
     * @return $objects
     */
    public function getConstructionCompany($certificateId, $appraiseId): object
    {
        $items = CertificateAssetConstructionCompany::where('certificate_id', '=', $certificateId)
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
        $stt = 0;
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
                                    "adjust_coefficient" => $comparisonFactor->adjust_coefficient,
                                ];
                                $checked[$comparisonFactor->type]++;
                            }
                        }
                    if ($index) continue;
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
        $certificate = $this->model->query()
            ->where('id', '=', $id)
            ->with('appraises')
            ->first();

        $result = [];
        if (isset($certificate->appraises)) {
            foreach ($certificate->appraises as $index => $appraise) {
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
                    if ($index1) continue;
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
    public function createCertificate(array $objects)
    {
        return DB::transaction(function () use ($objects) {
            try {
                if (isset($objects['appraises']) && !empty($objects['appraises'])) {
                    foreach ($objects['appraises'] as $appraise) {
                        $appraiseData = Appraise::where('id', $appraise['appraise_id'])->first();
                        if (!isset($appraiseData)) continue;
                        if ($appraiseData->status == 3) {
                            $certificateAsset = CertificateAsset::where('appraise_id', $appraiseData->id)->orderBy('id', 'DESC')->first();
                            $certificateHasAppraise = CertificateHasAppraise::where('appraise_id', $certificateAsset->id)->first();
                            if (isset($certificateHasAppraise->certificate_id)) {
                                return "Tài sản thẩm định đã được chọn trong hồ sơ thẩm định " . $certificateHasAppraise->certificate_id . ". Vui lòng kiểm tra lại.";
                            }
                        }
                    }
                    $objects["status"] = 2;
                } else {
                    $objects["status"] = 1;
                }

                $objects['created_by'] = is_array($objects['created_by']) ? $objects['created_by']['id'] : $objects['created_by'];

                $objects["document_description"] = isset($objects["document_description"]) ? $objects["document_description"] : "";
                $objects["updated_at"] = date("Y-m-d H:i:s");
                $certificate = new Certificate($objects);
                $certificateId = QueryBuilder::for(Certificate::class)
                    ->insertGetId($certificate->attributesToArray());

                $certificateAssetIds = [];
                if (isset($objects['appraises']) && !empty($objects['appraises'])) {
                    foreach ($objects['appraises'] as $appraise) {
                        $appraise['certificate_id'] = $certificateId;
                        $appraiseData = Appraise::where('id', $appraise['appraise_id'])->first();
                        if (!isset($appraiseData)) continue;

                        $appraiseData->appraise_id = $appraise['appraise_id'];
                        $certificateAsset = new CertificateAsset($appraiseData->toArray());
                        $certificateAssetId = QueryBuilder::for($certificateAsset)
                            ->insertGetId($certificateAsset->attributesToArray());
                        $certificateAssetIds[$appraise['appraise_id']] = $certificateAssetId;
                        Appraise::where('id', $appraise['appraise_id'])->update(['status' => 3]); // 3 = locked

                        $appraiseId = $appraise['appraise_id'];
                        $appraise['appraise_id'] = $certificateAssetId;
                        $appraise = new CertificateHasAppraise($appraise);
                        $assetGeneralId = QueryBuilder::for($appraise)
                            ->insertGetId($appraise->attributesToArray());

                        $itemDatas = AppraiseHasAsset::where('appraise_id', $appraiseId)->get();
                        //echo '<pre>';
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

                        $appraiseUnitPriceDatas = AppraiseUnitPrice::where('appraise_id', $appraiseId)->get();
                        CertificateAssetUnitPrice::where('certificate_id', $certificateId)
                            ->where('appraise_id', $certificateAssetId)
                            ->forceDelete();

                        foreach ($appraiseUnitPriceDatas as $itemData) {
                            if (isset($itemData)) {
                                $itemData->certificate_id = $certificateId;
                                $itemData->appraise_id = $certificateAssetId;
                                $item = new CertificateAssetUnitPrice($itemData->toArray());
                                $itemId = QueryBuilder::for($item)->insertGetId($item->attributesToArray());
                            }
                        }

                        $appraiseUnitAreaDatas = AppraiseUnitArea::where('appraise_id', $appraiseId)->get();
                        CertificateAssetUnitArea::where('certificate_id', $certificateId)
                            ->where('appraise_id', $certificateAssetId)
                            ->forceDelete();

                        foreach ($appraiseUnitAreaDatas as $itemData) {
                            if (isset($itemData)) {
                                $itemData->certificate_id = $certificateId;
                                $itemData->appraise_id = $certificateAssetId;
                                $item = new CertificateAssetUnitArea($itemData->toArray());
                                $itemId = QueryBuilder::for($item)->insertGetId($item->attributesToArray());
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
                            }
                        }

                        $itemDatas = AppraiseTangibleComparisonFactor::where('appraise_id', $appraiseId)->get();
                        foreach ($itemDatas as $itemData) {
                            if (isset($itemData)) {
                                $itemData->appraise_id = $certificateAssetId;
                                $item = new CertificateAssetTangibleComparisonFactor($itemData->toArray());
                                $itemId = QueryBuilder::for($item)->insertGetId($item->attributesToArray());
                            }
                        }

                        $itemDatas = AppraiseVersion::where('appraise_id', $appraiseId)->get();
                        foreach ($itemDatas as $itemData) {
                            if (isset($itemData)) {
                                $itemData->appraise_id = $certificateAssetId;
                                $item = new CertificateAssetVersion($itemData->toArray());
                                $itemId = QueryBuilder::for($item)->insertGetId($item->attributesToArray());
                            }
                        }

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
                }

                if (isset($objects['comparison_factor'])) {
                    foreach ($objects['comparison_factor'] as $appraise) {
                        if (isset($certificateAssetIds[$appraise['id']])) {
                            $this->comparisonFactor($certificateAssetIds[$appraise['id']], $appraise);
                        }
                    }
                }

                /* if (isset($objects['construction_company_Tem'])) {
                    foreach ($objects['construction_company_Tem'] as $constructionCompanyData) {
                        foreach ($constructionCompanyData['table'] as $constructionCompanyId) {
                            $item = AppraisalConstructionCompany::where('id', $constructionCompanyId)->first();
                            $dataConstructionCompany = [
                                'certificate_id' => $certificateId,
                                'appraise_id' => $certificateAssetIds[$constructionCompanyData["id"]],
                                'company_id' => $constructionCompanyId,
                                'name' => $item->name,
                                'address' => $item->address,
                                'phone_number' => $item->phone_number,
                                'manager_name' => $item->manager_name,
                                'unit_price_m2' => $item->unit_price_m2,
                                'is_defaults' => $item->is_defaults,
                            ];

                            $dataConstructionCompany = new CertificateAssetConstructionCompany($dataConstructionCompany);
                            $constructionCompanyId = QueryBuilder::for($dataConstructionCompany)
                                ->insertGetId($dataConstructionCompany->attributesToArray());
                        }
                    }
                } */
                if (isset($objects['construction_company'])) {
                    foreach ($objects['construction_company'] as $constructionCompanyData) {
                        foreach ($constructionCompanyData['construction_company'] as $item) {
                            $item = (object) $item;

                            $dataConstructionCompany = [
                                'certificate_id' => $certificateId,
                                'appraise_id' => $certificateAssetIds[$constructionCompanyData["id"]],
                                'company_id' => $item->construction_company_id,
                                'name' => $item->name,
                                'address' => $item->address,
                                'phone_number' => $item->phone_number,
                                'manager_name' => $item->manager_name,
                                'unit_price_m2' => $item->unit_price_m2,
                                'is_defaults' => $item->is_defaults,
                            ];

                            $dataConstructionCompany = new CertificateAssetConstructionCompany($dataConstructionCompany);
                            $constructionCompanyId = QueryBuilder::for($dataConstructionCompany)
                                ->insertGetId($dataConstructionCompany->attributesToArray());
                        }
                    }
                }

                if (isset($objects['certificate_approach'])) {
                    foreach ($objects['certificate_approach'] as $certificateApproachData) {
                        $certificateApproachData['certificate_id'] = $certificateId;
                        $certificateApproachData = new CertificateApproach($certificateApproachData);
                        $certificateApproachId = QueryBuilder::for($certificateApproachData)
                            ->insertGetId($certificateApproachData->attributesToArray());
                    }
                }

                if (isset($objects['certificate_method_used'])) {
                    foreach ($objects['certificate_method_used'] as $certificateMethodUsedData) {
                        $certificateMethodUsedData['certificate_id'] = $certificateId;
                        $certificateMethodUsedData = new CertificateMethodUsed($certificateMethodUsedData);
                        $certificateMethodUsedId = QueryBuilder::for($certificateMethodUsedData)
                            ->insertGetId($certificateMethodUsedData->attributesToArray());
                    }
                }

                if (isset($objects['certificate_basis_property'])) {
                    foreach ($objects['certificate_basis_property'] as $certificateBasisPropertyData) {
                        $certificateBasisPropertyData['certificate_id'] = $certificateId;
                        $certificateBasisPropertyData = new CertificateBasisProperty($certificateBasisPropertyData);
                        $certificateBasisPropertyId = QueryBuilder::for($certificateBasisPropertyData)
                            ->insertGetId($certificateBasisPropertyData->attributesToArray());
                    }
                }

                if (isset($objects['certificate_principle'])) {
                    foreach ($objects['certificate_principle'] as $certificatePrincipleData) {
                        $certificatePrincipleData['certificate_id'] = $certificateId;
                        $certificatePrincipleData = new CertificatePrinciple($certificatePrincipleData);
                        $certificatePrincipleId = QueryBuilder::for($certificatePrincipleData)
                            ->insertGetId($certificatePrincipleData->attributesToArray());
                    }
                }

                if (isset($objects['legal_documents_on_valuation'])) {
                    foreach ($objects['legal_documents_on_valuation'] as $certificateDocumentsOnValuationData) {
                        $certificateDocumentsOnValuationData['certificate_id'] = $certificateId;
                        $certificateDocumentsOnValuationData = new CertificateLegalDocumentsOnValuation($certificateDocumentsOnValuationData);
                        $certificateDocumentsOnValuationId = QueryBuilder::for($certificateDocumentsOnValuationData)
                            ->insertGetId($certificateDocumentsOnValuationData->attributesToArray());
                    }
                }

                if (isset($objects['legal_documents_on_construction'])) {
                    foreach ($objects['legal_documents_on_construction'] as $certificateDocumentsOnConstructionData) {
                        $certificateDocumentsOnConstructionData['certificate_id'] = $certificateId;
                        $certificateDocumentsOnConstructionData = new CertificateLegalDocumentsOnConstruction($certificateDocumentsOnConstructionData);
                        $certificateDocumentsOnConstructionId = QueryBuilder::for($certificateDocumentsOnConstructionData)
                            ->insertGetId($certificateDocumentsOnConstructionData->attributesToArray());
                    }
                }

                if (isset($objects['legal_documents_on_land'])) {
                    foreach ($objects['legal_documents_on_land'] as $certificateDocumentsOnLandData) {
                        $certificateDocumentsOnLandData['certificate_id'] = $certificateId;
                        $certificateDocumentsOnLandData = new CertificateLegalDocumentsOnLand($certificateDocumentsOnLandData);
                        $certificateDocumentsOnConstructionId = QueryBuilder::for($certificateDocumentsOnLandData)
                            ->insertGetId($certificateDocumentsOnLandData->attributesToArray());
                    }
                }

                if (isset($objects['legal_documents_on_local'])) {
                    foreach ($objects['legal_documents_on_local'] as $certificateDocumentsOnLocalData) {
                        $certificateDocumentsOnLocalData['certificate_id'] = $certificateId;
                        $certificateDocumentsOnLocalData = new CertificateLegalDocumentsOnLocal($certificateDocumentsOnLocalData);
                        $certificateDocumentsOnConstructionId = QueryBuilder::for($certificateDocumentsOnLocalData)
                            ->insertGetId($certificateDocumentsOnLocalData->attributesToArray());
                    }
                }

                /* if (isset($objects['comparison_factor'])) {
                    foreach ($objects['comparison_factor'] as $comparisonFactor) {
                        $comparisonFactor['certificate_id'] = $certificateId;
                        $comparisonFactor = new CertificateComparisonFactor($comparisonFactor);
                        $comparisonFactorId= QueryBuilder::for($comparisonFactor)
                            ->insertGetId($comparisonFactor->attributesToArray());
                    }
                }  */

                $this->updateSelectComparisonFactor($certificateId);

                return $certificateId;
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
    //public function updateCertificate($id, array $objects): int
    public function updateCertificate($id, array $objects)
    {
        return DB::transaction(function () use ($id, $objects) {
            try {
                $oldCertificate = Certificate::where('id', $id)->first();
                $oldAppraises = $oldCertificate->appraises;
                $oldCertificateAssetIds = [];
                foreach ($oldAppraises as $oldAppraise) {
                    $oldCertificateAssetIds[$oldAppraise->appraise_id] = $oldAppraise->id;
                }

                $objects['created_by'] = is_array($objects['created_by']) ? $objects['created_by']['id'] : $objects['created_by'];

                if (isset($objects['appraises']) && !empty($objects['appraises'])) {
                    foreach ($objects['appraises'] as $appraise) {
                        if (!isset($oldCertificateAssetIds[$appraise['appraise_id']])) {
                            $appraiseData = Appraise::where('id', $appraise['appraise_id'])->first();
                            if (!isset($appraiseData)) continue;
                            if ($appraiseData->status == 3) {
                                $certificateAsset = CertificateAsset::where('appraise_id', $appraiseData->id)->orderBy('id', 'DESC')->first();
                                $certificateHasAppraise = CertificateHasAppraise::where('appraise_id', $certificateAsset->id)->first();
                                if (isset($certificateHasAppraise->certificate_id)) {
                                    return "Tài sản thẩm định đã được chọn trong hồ sơ thẩm định " . $certificateHasAppraise->certificate_id . ". Vui lòng kiểm tra lại.";
                                }
                            }
                        }
                    }
                    $objects["status"] = 2;
                } else {
                    $objects["status"] = 1;
                }

                $objects["document_description"] = isset($objects["document_description"]) ? $objects["document_description"] : "";
                $objects["updated_at"] = date("Y-m-d H:i:s");
                $certificate = new Certificate($objects);
                $certificateId = $id;
                $certificate->newQuery()->updateOrInsert(['id' => $id], $certificate->attributesToArray());

                $certificateAssetIds = [];
                if (isset($objects['appraises'])) {
                    CertificateHasAppraise::query()->where('certificate_id', '=', $certificateId)->forceDelete();
                    foreach ($objects['appraises'] as $appraise) {
                        if (!isset($oldCertificateAssetIds[$appraise['appraise_id']])) {
                            $appraiseData = Appraise::where('id', $appraise['appraise_id'])->first();
                            if (!isset($appraiseData)) continue;
                            $appraiseId = $appraise['appraise_id'];
                            Appraise::where('id', $appraiseId)->update(['status' => 3]); // updateStatus : updateStatus : 3 = locked
                            RealEstate::query()->whereHas('appraiises', function ($has) use ($appraiseId) {
                                $has->where('id', $appraiseId);
                            })->update(['status' => 3]);
                            $appraiseData->appraise_id = $appraise['appraise_id'];
                            $certificateAsset = new CertificateAsset($appraiseData->toArray());
                            $certificateAssetId = QueryBuilder::for($certificateAsset)
                                ->insertGetId($certificateAsset->attributesToArray());

                            $appraiseId = $appraise['appraise_id'];

                            $appraise['certificate_id'] = $certificateId;
                            $appraise['appraise_id'] = $certificateAssetId;
                            $appraise['version'] = '2.0';
                            $appraise = new CertificateHasAppraise($appraise);
                            $assetGeneralId = QueryBuilder::for($appraise)
                                ->insertGetId($appraise->attributesToArray());

                            //echo '<pre>';
                            $itemDatas = AppraiseHasAsset::where('appraise_id', $appraiseId)->get();
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
                            CertificateAssetUnitPrice::where('certificate_id', $certificateId)
                                ->where('appraise_id', $certificateAssetId)
                                ->forceDelete();
                            foreach ($appraiseUnitPriceDatas as $itemData) {
                                if (isset($itemData)) {
                                    $itemData->certificate_id = $certificateId;
                                    $itemData->appraise_id = $certificateAssetId;
                                    $item = new CertificateAssetUnitPrice($itemData->toArray());
                                    $itemId = QueryBuilder::for($item)->insertGetId($item->attributesToArray());
                                }
                            }

                            $appraiseUnitPriceDatas = AppraiseUnitArea::where('appraise_id', $appraiseId)->get();
                            CertificateAssetUnitArea::where('certificate_id', $certificateId)
                                ->where('appraise_id', $certificateAssetId)
                                ->forceDelete();
                            foreach ($appraiseUnitPriceDatas as $itemData) {
                                if (isset($itemData)) {
                                    $itemData->certificate_id = $certificateId;
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
                                }
                            }

                            $itemDatas = AppraiseTangibleComparisonFactor::where('appraise_id', $appraiseId)->get();
                            foreach ($itemDatas as $itemData) {
                                if (isset($itemData)) {
                                    $itemData->appraise_id = $certificateAssetId;
                                    $item = new CertificateAssetTangibleComparisonFactor($itemData->toArray());
                                    $itemId = QueryBuilder::for($item)->insertGetId($item->attributesToArray());
                                }
                            }

                            $itemDatas = AppraiseVersion::where('appraise_id', $appraiseId)->get();
                            foreach ($itemDatas as $itemData) {
                                if (isset($itemData)) {
                                    $itemData->appraise_id = $certificateAssetId;
                                    $item = new CertificateAssetVersion($itemData->toArray());
                                    $itemId = QueryBuilder::for($item)->insertGetId($item->attributesToArray());
                                }
                            }

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

                            $certificateAssetIds[$appraiseId] = $certificateAssetId;
                        } else {
                            $certificateAssetId = $oldCertificateAssetIds[$appraise['appraise_id']];
                            $appraiseTmp = $appraise;
                            $appraiseTmp['certificate_id'] = $certificateId;
                            $appraiseTmp['appraise_id'] = $certificateAssetId;
                            $appraiseTmp['version'] = '2.0';
                            $appraiseTmp = new CertificateHasAppraise($appraiseTmp);
                            $assetGeneralId = QueryBuilder::for($appraiseTmp)
                                ->insertGetId($appraiseTmp->attributesToArray());
                            $certificateAssetIds[$appraise['appraise_id']] = $certificateAssetId;
                        }
                    }
                    foreach ($oldAppraises as $appraiseTmp) {
                        if (isset($appraiseTmp->appraise_id) && !isset($certificateAssetIds[$appraiseTmp->appraise_id])) {
                            Appraise::where('id', $appraiseTmp->appraise_id)->update(['status' => 2]); // updateStatus : 3 = locked
                        } else {
                            Appraise::where('id', $appraiseTmp->appraise_id)->update(['status' => 3]); // updateStatus : 3 = locked
                        }
                    }
                }

                if (isset($objects['comparison_factor'])) {
                    foreach ($objects['comparison_factor'] as $appraise) {
                        if (isset($appraise["oldID"])) {
                            $this->comparisonFactor($appraise['id'], $appraise);
                        } else if (!isset($appraise["oldID"]) && isset($certificateAssetIds[$appraise['id']])) {
                            $this->comparisonFactor($certificateAssetIds[$appraise['id']], $appraise);
                        }
                    }
                }

                /* if (isset($objects['construction_company_Tem'])) {
                    CertificateAssetConstructionCompany::where('certificate_id', $certificateId)->delete();
                    $constructionCompanies =
                        CertificateAssetConstructionCompany::where('certificate_id', $certificateId)
                            ->withTrashed()->get();
                    foreach ($objects['construction_company_Tem'] as $constructionCompanyData) {
                        if(isset($constructionCompanyData["id"])) {
                            foreach ($constructionCompanyData['table'] as $constructionCompanyId) {
                                if(isset($constructionCompanyData["oldID"])) {
                                    $constructionCompany = $constructionCompanies->where('appraise_id', $constructionCompanyData["id"])
                                        ->where('company_id', $constructionCompanyId)->first();

                                    if(isset($constructionCompany)) {
                                        $constructionCompany = $constructionCompany;
                                        $constructionCompany->restore();
                                    } else {
                                        $item = AppraisalConstructionCompany::where('id', $constructionCompanyId)->first();
                                        $dataConstructionCompany = [
                                            'certificate_id' => $certificateId,
                                            'appraise_id' => $constructionCompanyData["id"],
                                            'company_id' => $constructionCompanyId,
                                            'name' => $item->name,
                                            'address' => $item->address,
                                            'phone_number' => $item->phone_number,
                                            'manager_name' => $item->manager_name,
                                            'unit_price_m2' => $item->unit_price_m2,
                                            'is_defaults' => $item->is_defaults,
                                        ];

                                        $dataConstructionCompany = new CertificateAssetConstructionCompany($dataConstructionCompany);
                                        $constructionCompanyId = QueryBuilder::for($dataConstructionCompany)
                                            ->insertGetId($dataConstructionCompany->attributesToArray());
                                    }
                                } else {
                                    if(isset($certificateAssetIds[$constructionCompanyData["id"]])) {
                                        $item = AppraisalConstructionCompany::where('id', $constructionCompanyId)->first();
                                        $dataConstructionCompany = [
                                            'certificate_id' => $certificateId,
                                            'appraise_id' => $certificateAssetIds[$constructionCompanyData["id"]],
                                            'company_id' => $constructionCompanyId,
                                            'name' => $item->name,
                                            'address' => $item->address,
                                            'phone_number' => $item->phone_number,
                                            'manager_name' => $item->manager_name,
                                            'unit_price_m2' => $item->unit_price_m2,
                                            'is_defaults' => $item->is_defaults,
                                        ];

                                        $dataConstructionCompany = new CertificateAssetConstructionCompany($dataConstructionCompany);
                                        $constructionCompanyId = QueryBuilder::for($dataConstructionCompany)
                                            ->insertGetId($dataConstructionCompany->attributesToArray());
                                    }
                                }
                            }
                        }
                    }
                } */

                if (isset($objects['construction_company'])) {
                    CertificateAssetConstructionCompany::where('certificate_id', $certificateId)->delete();

                    foreach ($objects['construction_company'] as $constructionCompanyData) {
                        if (isset($constructionCompanyData["id"]) && isset($constructionCompanyData["oldID"])) {
                            $constructionCompanies = CertificateAssetConstructionCompany::withTrashed()
                                ->where('certificate_id', $certificateId)
                                ->where('appraise_id', $constructionCompanyData["id"])
                                ->get();

                            if (isset($constructionCompanies) && $constructionCompanies->count()) {
                                foreach ($constructionCompanies as $constructionCompany) {
                                    $constructionCompany->restore();
                                }
                            }
                        } else if (isset($constructionCompanyData['construction_company'])) {
                            $constructionCompanies = CertificateAssetConstructionCompany::withTrashed()
                                ->where('certificate_id', $certificateId)
                                ->where('appraise_id', $certificateAssetIds[$constructionCompanyData["id"]])
                                ->get();
                            if (isset($constructionCompanies) && $constructionCompanies->count()) {
                                foreach ($constructionCompanies as $constructionCompany) {
                                    $constructionCompany->restore();
                                }
                            } else {
                                foreach ($constructionCompanyData['construction_company'] as $item) {
                                    $item = (object) $item;
                                    if (isset($certificateAssetIds[$constructionCompanyData["id"]])) {
                                        $dataConstructionCompany = [
                                            'certificate_id' => $certificateId,
                                            'appraise_id' => $certificateAssetIds[$constructionCompanyData["id"]],
                                            'company_id' => $item->construction_company_id,
                                            'name' => $item->name,
                                            'address' => $item->address,
                                            'phone_number' => $item->phone_number,
                                            'manager_name' => $item->manager_name,
                                            'unit_price_m2' => $item->unit_price_m2,
                                            'is_defaults' => $item->is_defaults,
                                        ];

                                        $dataConstructionCompany = new CertificateAssetConstructionCompany($dataConstructionCompany);
                                        $constructionCompanyId = QueryBuilder::for($dataConstructionCompany)
                                            ->insertGetId($dataConstructionCompany->attributesToArray());
                                    }
                                }
                            }
                        }
                    }
                }

                CertificateConstructionCompany::query()->where('certificate_id', '=', $certificateId)->forceDelete();
                if (isset($objects['construction_company'])) {
                    foreach ($objects['construction_company'] as $constructionCompanyData) {
                        $constructionCompanyData['certificate_id'] = $certificateId;
                        $constructionCompanyData = new CertificateConstructionCompany($constructionCompanyData);
                        $constructionCompanyId = QueryBuilder::for($constructionCompanyData)
                            ->insertGetId($constructionCompanyData->attributesToArray());
                    }
                }

                CertificateApproach::query()->where('certificate_id', '=', $certificateId)->forceDelete();
                if (isset($objects['certificate_approach'])) {
                    foreach ($objects['certificate_approach'] as $certificateApproachData) {
                        $certificateApproachData['certificate_id'] = $certificateId;
                        $certificateApproachData = new CertificateApproach($certificateApproachData);
                        $certificateApproachId = QueryBuilder::for($certificateApproachData)
                            ->insertGetId($certificateApproachData->attributesToArray());
                    }
                }

                CertificateMethodUsed::query()->where('certificate_id', '=', $certificateId)->forceDelete();
                if (isset($objects['certificate_method_used'])) {
                    foreach ($objects['certificate_method_used'] as $certificateMethodUsedData) {
                        $certificateMethodUsedData['certificate_id'] = $certificateId;
                        $certificateMethodUsedData = new CertificateMethodUsed($certificateMethodUsedData);
                        $certificateMethodUsedId = QueryBuilder::for($certificateMethodUsedData)
                            ->insertGetId($certificateMethodUsedData->attributesToArray());
                    }
                }

                CertificateBasisProperty::query()->where('certificate_id', '=', $certificateId)->forceDelete();
                if (isset($objects['certificate_basis_property'])) {
                    foreach ($objects['certificate_basis_property'] as $certificateBasisPropertyData) {
                        $certificateBasisPropertyData['certificate_id'] = $certificateId;
                        $certificateBasisPropertyData = new CertificateBasisProperty($certificateBasisPropertyData);
                        $certificateBasisPropertyId = QueryBuilder::for($certificateBasisPropertyData)
                            ->insertGetId($certificateBasisPropertyData->attributesToArray());
                    }
                }

                CertificatePrinciple::query()->where('certificate_id', '=', $certificateId)->forceDelete();
                if (isset($objects['certificate_principle'])) {
                    foreach ($objects['certificate_principle'] as $certificatePrincipleData) {
                        $certificatePrincipleData['certificate_id'] = $certificateId;
                        $certificatePrincipleData = new CertificatePrinciple($certificatePrincipleData);
                        $certificatePrincipleId = QueryBuilder::for($certificatePrincipleData)
                            ->insertGetId($certificatePrincipleData->attributesToArray());
                    }
                }

                CertificateLegalDocumentsOnValuation::query()->where('certificate_id', '=', $certificateId)->forceDelete();
                if (isset($objects['legal_documents_on_valuation'])) {
                    foreach ($objects['legal_documents_on_valuation'] as $certificateDocumentsOnValuationData) {
                        $certificateDocumentsOnValuationData['certificate_id'] = $certificateId;
                        $certificateDocumentsOnValuationData = new CertificateLegalDocumentsOnValuation($certificateDocumentsOnValuationData);
                        $certificateDocumentsOnValuationId = QueryBuilder::for($certificateDocumentsOnValuationData)
                            ->insertGetId($certificateDocumentsOnValuationData->attributesToArray());
                    }
                }

                CertificateLegalDocumentsOnConstruction::query()->where('certificate_id', '=', $certificateId)->forceDelete();
                if (isset($objects['legal_documents_on_construction'])) {
                    foreach ($objects['legal_documents_on_construction'] as $certificateDocumentsOnConstructionData) {
                        $certificateDocumentsOnConstructionData['certificate_id'] = $certificateId;
                        $certificateDocumentsOnConstructionData = new CertificateLegalDocumentsOnConstruction($certificateDocumentsOnConstructionData);
                        $certificateDocumentsOnConstructionId = QueryBuilder::for($certificateDocumentsOnConstructionData)
                            ->insertGetId($certificateDocumentsOnConstructionData->attributesToArray());
                    }
                }

                CertificateLegalDocumentsOnLand::query()->where('certificate_id', '=', $certificateId)->forceDelete();
                if (isset($objects['legal_documents_on_land'])) {
                    foreach ($objects['legal_documents_on_land'] as $certificateDocumentsOnLandData) {
                        $certificateDocumentsOnLandData['certificate_id'] = $certificateId;
                        $certificateDocumentsOnLandData = new CertificateLegalDocumentsOnLand($certificateDocumentsOnLandData);
                        $certificateDocumentsOnConstructionId = QueryBuilder::for($certificateDocumentsOnLandData)
                            ->insertGetId($certificateDocumentsOnLandData->attributesToArray());
                    }
                }

                CertificateLegalDocumentsOnLocal::query()->where('certificate_id', '=', $certificateId)->forceDelete();
                if (isset($objects['legal_documents_on_local'])) {
                    foreach ($objects['legal_documents_on_local'] as $certificateDocumentsOnLocalData) {
                        $certificateDocumentsOnLocalData['certificate_id'] = $certificateId;
                        $certificateDocumentsOnLocalData = new CertificateLegalDocumentsOnLocal($certificateDocumentsOnLocalData);
                        $certificateDocumentsOnConstructionId = QueryBuilder::for($certificateDocumentsOnLocalData)
                            ->insertGetId($certificateDocumentsOnLocalData->attributesToArray());
                    }
                }

                if (isset($objects['delete_other_documents'])) {
                    foreach ($objects['delete_other_documents'] as $otherDocument) {
                        if (isset($otherDocument['id'])) {
                            CertificateOtherDocuments::where('id', $otherDocument['id'])->delete();
                        }
                    }
                }

                /* CertificateComparisonFactor::query()->where('certificate_id', '=', $certificateId)->forceDelete();
                if (isset($objects['comparison_factor'])) {
                    foreach ($objects['comparison_factor'] as $comparisonFactor) {
                        $comparisonFactor['certificate_id'] = $certificateId;
                        $comparisonFactor = new CertificateComparisonFactor($comparisonFactor);
                        $comparisonFactorId= QueryBuilder::for($comparisonFactor)
                            ->insertGetId($comparisonFactor->attributesToArray());
                    }
                } */

                /*
                CertificateHasAppraise::query()->where('certificate_id', '=', $certificateId)->forceDelete();
                if (isset($objects['appraises'])) {
                    foreach ($objects['appraises'] as $appraise) {
                        $appraise['certificate_id'] = $certificateId;
                        $appraise = new CertificateHasAppraise($appraise);
                        $assetGeneralId = QueryBuilder::for($appraise)
                            ->insertGetId($appraise->attributesToArray());
                    }
                }*/

                $this->updateSelectComparisonFactor($certificateId);
                return $certificateId;
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

    public function updateSelectComparisonFactor($certificateId): bool
    {
        $certificateHasAppraise = CertificateHasAppraise::query()->where('certificate_id', '=', $certificateId)->get();
        $certificateComparisonFactor = CertificateComparisonFactor::query()->where('certificate_id', '=', $certificateId)->get();
        foreach ($certificateHasAppraise as $appraise) {
            $appraiseComparisonFactor = AppraiseComparisonFactor::query()->where('appraise_id', '=', $appraise->appraise_id)->get();
            foreach ($appraiseComparisonFactor as $comparisonFactorAppraise) {
                $status = 0;
                foreach ($certificateComparisonFactor as $comparisonFactor) {
                    if ($comparisonFactor->comparison_factor == 'Pháp lý' && $comparisonFactorAppraise->type == 'phap_ly') {
                        $status = 1;
                    }
                    if ($comparisonFactor->comparison_factor == 'Hình dáng đất' && $comparisonFactorAppraise->type == 'hinh_dang_dat') {
                        $status = 1;
                    }
                    if ($comparisonFactor->comparison_factor == 'Giao thông' && $comparisonFactorAppraise->type == 'ket_cau_duong') {
                        $status = 1;
                    }
                    if ($comparisonFactor->comparison_factor == 'Kinh doanh' && $comparisonFactorAppraise->type == 'kinh_doanh') {
                        $status = 1;
                    }
                    if ($comparisonFactor->comparison_factor == 'Điều kiện hạ tầng' && $comparisonFactorAppraise->type == 'dieu_kien_ha_tang') {
                        $status = 1;
                    }
                    if ($comparisonFactor->comparison_factor == 'An ninh, môi trường sống' && $comparisonFactorAppraise->type == 'an_ninh_moi_truong_song') {
                        $status = 1;
                    }
                    if ($comparisonFactor->comparison_factor == 'Phong thủy' && $comparisonFactorAppraise->type == 'phong_thuy') {
                        $status = 1;
                    }
                    if ($comparisonFactor->comparison_factor == 'Quy mô' && $comparisonFactorAppraise->type == 'quy_mo') {
                        $status = 1;
                    }
                    if ($comparisonFactor->comparison_factor == 'Chiều sâu khu đất' && $comparisonFactorAppraise->type == 'chieu_sau_khu_dat') {
                        $status = 1;
                    }
                    if ($comparisonFactor->comparison_factor == 'Chiều rộng mặt tiền' && $comparisonFactorAppraise->type == 'chieu_rong_mat_tien') {
                        $status = 1;
                    }
                    if ($comparisonFactor->comparison_factor == 'Bề rộng đường' && $comparisonFactorAppraise->type == 'do_rong_duong') {
                        $status = 1;
                    }

                    $comparisonFactorAppraise->status = $status;
                    $comparisonFactorId = QueryBuilder::for($comparisonFactorAppraise)
                        ->updateOrInsert(['id' => $comparisonFactorAppraise['id']], $comparisonFactorAppraise->attributesToArray());
                }
            }
        }
        return true;
    }

    public function updateTangibleComparisonFactor($id, array $objects): int
    {
        $tangibleComparisonFactor = new CertificateTangibleComparisonFactor($objects);
        $tangibleComparisonFactorId = CertificateTangibleComparisonFactor::query()
            ->updateOrInsert(['id' => $id], $tangibleComparisonFactor->attributesToArray());
        return $id;
    }

    #region version 2
    public function findAllCerificate(array $request)
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
                if ((($role->name !== 'SUPER_ADMIN' && $role->name !== 'ROOT_ADMIN' && $role->name !== 'SUB_ADMIN' && $role->name !== 'ADMIN' && $role->name !== 'Accounting')) || (!empty($popup))) {
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
        $timeFilterFrom = null;
        $timeFilterTo = null;
        $betweenTotal = ValueDefault::TOTAL_PRICE_PERCENT;
        if (request()->has('data')) {
            $dataJson = request()->get('data');
            $dataTemp = json_decode($dataJson);
            if (isset($dataTemp) && isset($dataTemp->fromDate)) {
                $timeFilterFrom = $dataTemp->fromDate;
            }
            if (isset($dataTemp) && isset($dataTemp->toDate)) {
                $timeFilterTo = $dataTemp->toDate;
            }
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
            'appraiser_control_id',
            'administrative_id',
            'business_manager_id',
            'customer_id',
            'customer_group_id',
            DB::raw("case status
                    when 1
                    then 'Mới'
                when 2
                    then 'Thẩm định'
                when 3
                    then 'Duyệt giá'
                when 4
                    then 'Hoàn thành'
                when 5
                    then 'Huỷ'
                when 7
                    then 'Duyệt phát hành'
                when 8
                    then 'In hồ sơ'
                when 9
                    then 'Bàn giao khách hàng'
                when 10
                    then 'Phân hồ sơ'
            end as status_text
            "),
            Db::raw("cast(certificate_prices.value as bigint) as total_price"),
            'commission_fee',

            'document_type',
            'service_fee',
            'status_expired_at',
            'status_updated_at',
            'sub_status',
            'certificates.updated_at'
        ];
        $with = [
            'createdBy:id,name',
            'appraiser:id,name',
            // 'appraiserManager:id,name',
            // 'appraiserConfirm:id,name',
            // 'appraiserSale:id,name',
            'appraiserPerform:id,name',
            'appraisePurpose:id,name',
            'appraiserSale:id,name,user_id',
            'appraiserControl:id,name',
            'administrative:id,name,user_id',
            'appraiserBusinessManager:id,name,user_id',
            'customer:id,name,phone,address',
            'customerGroup:id,description,name_lv_1,name_lv_2,name_lv_3,name_lv_4',
            'realEstate.appraises',
            'realEstate.appraises.certificateAppraiseLaw',
            'realEstate.apartment',
            'realEstate.apartment.law',
            'payments',

            // 'appraises:id,appraise_id',
            // 'appraises.appraiseLaw:id,appraise_id',
            // 'appraises.appraiseLaw.landDetails:id,appraise_law_id,doc_no,land_no',

            // 'assetPrice' => function($query){
            //     $query->where('slug','=','total_asset_price')
            //         ->select(['id','certificate_id','slug','value']);
            // },
        ];
        \DB::enableQueryLog();

        $result = QueryBuilder::for($this->model)
            ->with($with)
            ->select($select)
            ->leftjoin('certificate_prices', function ($join) {
                $join->on('certificates.id', '=', 'certificate_prices.certificate_id')
                    ->where('slug', '=', 'total_asset_price')
                    ->select(['id', 'certificate_id', 'slug', 'value'])
                    ->limit(1);
            });

        //// command tạm - sẽ xử lý phân quyền sau
        $role = $user->roles->last();
        // dd($role->name);
        if (request()->has('is_guest')) {
        } elseif ($role->name == 'SUB_ADMIN') {
            $result = $result->where(function ($query) use ($user) {
                $query = $query->whereHas('branch', function ($q) use ($user) {
                    if ($user->branch_id) {
                        return $q->where('id', $user->branch_id);
                    }
                });
            });
        } elseif (($role->name !== 'ROOT_ADMIN' && $role->name !== 'ADMIN' && $role->name !== 'Accounting')) {
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
                $query = $query->orwhereHas('administrative', function ($q) use ($user) {
                    return $q->where('user_id', $user->id);
                });
                $query = $query->orwhereHas('appraiserBusinessManager', function ($q) use ($user) {
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
                case '%':
                    $result = $result->where(function ($q) use ($filter) {
                        $q = $q->where('petitioner_name', 'ILIKE', '%' . $filter . '%');
                    });
                    break;
                case '^':
                    $result = $result->where(function ($q) use ($filterData) {
                        $q->whereHas('realEstate', function ($has) use ($filterData) {
                            $has->whereHas('appraises', function ($query) use ($filterData) {
                                $query->where('full_address', 'ILIKE', '%' . $filterData . '%');
                            })->orWhereHas('apartment', function ($query) use ($filterData) {
                                $query->where('full_address', 'ILIKE', '%' . $filterData . '%');
                            });
                        });
                    });
                    break;
                default:
                    $result = $result->where(function ($q) use ($filter) {
                        $q = $q->where('certificates.id', 'like', strval($filter));
                    });
            }
        }

        if (isset($timeFilterFrom) && isset($timeFilterTo)) {
            $startDate = date('Y-m-d', strtotime($timeFilterFrom));
            $endDate = date('Y-m-d', strtotime($timeFilterTo));
            $result = $result->whereBetween('certificates.created_at', [$timeFilterFrom, $timeFilterTo])
                ->whereBetween('certificates.updated_at', [$timeFilterFrom, $timeFilterTo]);
        } elseif (isset($timeFilterFrom)) {
            $startDate = date('Y-m-d', strtotime($timeFilterFrom));
            $result = $result->where('certificates.created_at', '>=', $timeFilterFrom)
                ->where('certificates.updated_at', '>=', $timeFilterFrom);
        } elseif (isset($timeFilterTo)) {
            $endDate = date('Y-m-d', strtotime($timeFilterTo));
            $result = $result->where('certificates.created_at', '<=', $timeFilterTo)
                ->where('certificates.updated_at', '<=', $timeFilterTo);
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
        if (request()->has('is_guest')) {
            if (isset($user->name_lv_1)) {
                // $result = $result->where('customer_group_id', '=', $user->customer_group_id);
                $result = $result->where(function ($q) use ($user) {
                    $q = $q->whereHas('customerGroup', function ($has) use ($user) {
                        if ($user->name_lv_1 && $user->name_lv_1 != '') {
                            $has->where('name_lv_1', 'ILIKE', '%' . $user->name_lv_1 . '%');
                        }
                        if ($user->name_lv_2 && $user->name_lv_2 != '') {
                            $has->where('name_lv_2', 'ILIKE', '%' . $user->name_lv_2 . '%');
                        }
                        if ($user->name_lv_3 && $user->name_lv_3 != '') {
                            $has->where('name_lv_3', 'ILIKE', '%' . $user->name_lv_3 . '%');
                        }
                        if ($user->name_lv_4 && $user->name_lv_4 != '') {
                            $has->where('name_lv_4', 'ILIKE', '%' . $user->name_lv_4 . '%');
                        }
                    });
                });

                $result = $result
                    ->forPage($page, $perPage)
                    ->paginate($perPage);

                foreach ($result as $stt => $item) {
                    $result[$stt]->append('detail_list_id');
                    // $result[$stt]->append('certificate_asset_price');
                }
            } else {
                $result = [];
            }
        } else {
            $result = $result
                ->forPage($page, $perPage)
                ->paginate($perPage);

            foreach ($result as $stt => $item) {
                $result[$stt]->append('detail_list_id');
                // $result[$stt]->append('certificate_asset_price');
            }
        }


        return $result;
    }

    public function exportCertificateAccounting()
    {
        $status = request()->get('status');
        $fromDate = request()->get('fromDate');
        $toDate = request()->get('toDate');
        if (isset($fromDate) && isset($toDate)) {
            $fromDate =  \Carbon\Carbon::createFromFormat('d/m/Y', $fromDate);
            $toDate =  \Carbon\Carbon::createFromFormat('d/m/Y', $toDate);
            $diff = $toDate->diff($fromDate);
            if ($diff->days > 186) {
                return ['message' => 'Chỉ được tìm kiếm tối đa 6 tháng.', 'exception' => ''];
            }
        } else {
            return ['message' => 'Vui lòng nhập khoảng thời gian cần tìm', 'exception' => ''];
        }

        if (!empty($status)) {
            $status = explode(',', $status);
        }

        $select = ['*'];
        $with = [
            'certificate',
            'certificate.appraiserSale',
            'certificate.appraiserPerform',
            'certificate.appraiser',
            'certificate.appraiserManager',
            'certificate.payments',
            'preCertificate.payments',
            'preCertificate',
        ];
        $result = PreCertificatePayments::with($with)->select($select);
        $result = $result->whereNotNull('certificate_id');

        if (isset($status)) {
            $result = $result->whereHas('certificate', function ($query) use ($status) {
                $query->whereIn('status', $status);
            });
        }

        if (isset($fromDate) && isset($toDate)) {
            $result = $result->whereBetween('pay_date', [$fromDate->format('Y-m-d'), $toDate->format('Y-m-d')]);
        }

        $result = $result->orderBy('pay_date', 'desc')->get();
        return $result;
    }

    public function getCertificateWorkFlow()
    {
        $user = CommonService::getUser();

        $filter = request()->get('search_input');
        $query = request()->get('query');
        $page = request()->get('page');
        $limit = request()->get('limit');
        $status = request()->get('status');
        $timeFilterFrom = null;
        $timeFilterTo = null;
        if (request()->has('data')) {
            $dataJson = request()->get('data');
            $dataTemp = json_decode($dataJson);
            if (isset($dataTemp) && isset($dataTemp->fromDate)) {
                $timeFilterFrom = $dataTemp->fromDate;
            }
            if (isset($dataTemp) && isset($dataTemp->toDate)) {
                $timeFilterTo = $dataTemp->toDate;
            }
        }
        if (!empty($query)) {
            $query = json_decode($query);
        } else {
            $query = [];
        }
        $betweenTotal = ValueDefault::TOTAL_PRICE_PERCENT;

        $select = [
            'certificates.id', 'status', 'certificates.created_by', 'petitioner_name',
            'certificates.updated_at', 'status_updated_at',
            'appraiser_perform_id',
            'appraiser_manager_id', 'appraiser_confirm_id', 'appraiser_id',
            'appraiser_sale_id', 'appraiser_control_id', 'administrative_id',
            'pre_certificate_id', 'business_manager_id',
            'customer_id',
            // 'users.image',
            DB::raw("concat('HSTD_', certificates.id) AS slug"),
            DB::raw("case status
                when 1
                    then 'Mới'
                when 2
                    then 'Thẩm định'
                when 3
                    then 'Duyệt giá'
                when 4
                    then 'Hoàn thành'
                when 5
                    then 'Huỷ'
                when 7
                    then 'Duyệt phát hành'
                when 8
                    then 'In hồ sơ'
                when 9
                    then 'Bàn giao khách hàng'
                when 10
                    then 'Phân hồ sơ'
            end as status_text
            "),
            Db::raw("cast(certificate_prices.value as bigint) as total_price"),
            'commission_fee',
            Db::raw("COALESCE(document_count,0) as document_count"),
            'status_expired_at',
            DB::raw("case status
                        when 1
                            then u2.image
                        when 2
                            then u3.image
                        when 3
                            then u1.image
                        when 4
                            then u3.image
                        when 5
                            then users.image
                        when 7
                            then u4.image
                        when 8
                            then u5.image
                            
                        when 9
                            then u2.image
                        when 10
                            then u6.image    
                    end as image
                "),
            DB::raw("case status
                when 1
                    then u2.name
                when 2
                    then u3.name
                when 3
                    then u1.name
                when 4
                    then u3.name
                when 5
                    then users.name
                when 7
                    then u4.name
                when 8
                    then u5.name
                when 9
                    then u2.name
                when 10
                    then u6.name
            end as name_nv
        "),
            'sub_status',
        ];
        $with = [
            'appraiser:id,name,user_id',
            'branch',
            // 'appraiser.appraiserUser:id,appraisers_number,image',
            // 'appraiserManager:id,name,appraiser_number',
            // 'appraiserManager.appraiserUser:id,appraisers_number,image',
            // 'appraiserConfirm:id,name,appraiser_number',
            // 'appraiserConfirm.appraiserUser:id,appraisers_number,image',
            'appraiserSale:id,name,user_id',
            // 'appraiserSale.appraiserUser:id,appraisers_number,image',
            'appraiserPerform:id,name,user_id',
            'appraiserControl:id,name,user_id',
            // 'appraiserPerform.appraiserUser:appraisers_number,image',
            // 'createdBy:id,image',
            // 'assetPrice' => function($query){
            //     $query->where('slug','=','total_asset_price')
            //         ->select(['id','certificate_id','slug','value'])
            //         ->first();
            // },
            // 'appraises:id,appraise_id',
            // 'appraises.appraiseLaw:id,appraise_id',
            // 'appraises.appraiseLaw.landDetails:id,appraise_law_id,doc_no',
            'realEstate:id,real_estate_id',
            'personalProperties:id,personal_property_id',
            'administrative:id,name,user_id',
            'appraiserBusinessManager:id,name,user_id',
            'customer:id,name,phone,address',
        ];
        // dd($this->model);
        DB::enableQueryLog();
        $result = $this->model->with($with)
            ->leftjoin('users', function ($join) {
                $join->on('certificates.created_by', '=', 'users.id')
                    ->select(['id', 'image'])
                    ->limit(1);
            })
            ->leftjoin('certificate_prices', function ($join) {
                $join->on('certificates.id', '=', 'certificate_prices.certificate_id')
                    ->where('slug', '=', 'total_asset_price')
                    ->select(['id', 'certificate_id', 'slug', 'value'])
                    ->limit(1);
            })
            ->leftjoin(
                DB::raw('(select certificate_id , count(certificate_id) as document_count
                                    from certificate_other_documents
                                    where deleted_at is null
                                    group by certificate_id) as "tbCount"'),
                function ($join) {
                    $join->on('certificates.id', '=', 'tbCount.certificate_id')
                        ->select(['certificate_id', 'document_count']);
                }
            )
            ->leftjoin('appraisers', function ($join) {
                $join->on('appraisers.id', '=', 'certificates.appraiser_id')
                    ->join('users as u1', function ($j) {
                        $j->on('appraisers.user_id', '=', 'u1.id');
                    })
                    ->select('u1.image')
                    ->limit(1);
            })
            ->leftjoin('appraisers as sale', function ($join) {
                $join->on('sale.id', '=', 'certificates.appraiser_sale_id')
                    ->join('users as u2', function ($j) {
                        $j->on('sale.user_id', '=', 'u2.id');
                    })
                    ->select('u2.image')
                    ->limit(1);
            })
            ->leftjoin('appraisers as perform', function ($join) {
                $join->on('perform.id', '=', 'certificates.appraiser_perform_id')
                    ->join('users as u3', function ($j) {
                        $j->on('perform.user_id', '=', 'u3.id');
                    })
                    ->select('u3.image')
                    ->limit(1);
            })
            ->leftjoin('appraisers as control', function ($join) {
                $join->on('control.id', '=', 'certificates.appraiser_control_id')
                    ->join('users as u4', function ($j) {
                        $j->on('control.user_id', '=', 'u4.id');
                    })
                    ->select('u4.image')
                    ->limit(1);
            })
            ->leftjoin('appraisers as administrative', function ($join) {
                $join->on('administrative.id', '=', 'certificates.administrative_id')
                    ->join('users as u5', function ($j) {
                        $j->on('administrative.user_id', '=', 'u5.id');
                    })
                    ->select('u5.image')
                    ->limit(1);
            })
            ->leftjoin('appraisers as businessmanager', function ($join) {
                $join->on('businessmanager.id', '=', 'certificates.business_manager_id')
                    ->join('users as u6', function ($j) {
                        $j->on('businessmanager.user_id', '=', 'u6.id');
                    })
                    ->select('u6.image')
                    ->limit(1);
            })
            ->select($select);

        //// command tạm - sẽ xử lý phân quyền sau
        $role = $user->roles->last();
        // dd($role->name);
        if ($role->name == 'SUB_ADMIN') {
            $result = $result->where(function ($query) use ($user) {
                $query = $query->whereHas('branch', function ($q) use ($user) {
                    if ($user->branch_id) {
                        return $q->where('id', $user->branch_id);
                    }
                });
            });
        } elseif (($role->name !== 'ROOT_ADMIN' && $role->name !== 'ADMIN' && $role->name !== 'Accounting')) {
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
                $query = $query->orwhereHas('administrative', function ($q) use ($user) {
                    return $q->where('user_id', $user->id);
                });
                $query = $query->orwhereHas('appraiserBusinessManager', function ($q) use ($user) {
                    return $q->where('user_id', $user->id);
                });
            });
        }
        if (!empty($status)) {
            $result = $result->whereIn('status', $status);
        }
        if (isset($query->public_date_from) && !empty($query->public_date_from)) {
            $result =  $result->where('updated_at', '>=', date('Y-m-d', strtotime($query->public_date_from)) . ' 00:00:00');
        }
        if (isset($query->public_date_to) && !empty($query->public_date_to)) {
            $result = $result->where('updated_at', '<=', date('Y-m-d', strtotime($query->public_date_to)) . ' 00:00:00');
        }
        if (isset($timeFilterFrom) && isset($timeFilterTo)) {
            $startDate = date('Y-m-d', strtotime($timeFilterFrom));
            $endDate = date('Y-m-d', strtotime($timeFilterTo));
            $result = $result->whereBetween('certificates.created_at', [$timeFilterFrom, $timeFilterTo])
                ->whereBetween('certificates.updated_at', [$timeFilterFrom, $timeFilterTo]);
        } elseif (isset($timeFilterFrom)) {
            $startDate = date('Y-m-d', strtotime($timeFilterFrom));
            $result = $result->where('certificates.created_at', '>=', $timeFilterFrom)
                ->where('certificates.updated_at', '>=', $timeFilterFrom);
        } elseif (isset($timeFilterTo)) {
            $endDate = date('Y-m-d', strtotime($timeFilterTo));
            $result = $result->where('certificates.created_at', '<=', $timeFilterTo)
                ->where('certificates.updated_at', '<=', $timeFilterTo);
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
                    //         $q = $q->whereHas('appraises.appraiseLaw.landDetails',function($has) use($doc_no, $land_no){
                    //             $has->where('doc_no', '=', $doc_no );
                    //             if(intval($land_no) >= 0)
                    //                 $has= $has->Where('land_no', '=', $land_no);
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
                            $q->whereBetween('certificate_prices.value', [$fromValue, $toValue]);
                        });
                    }
                    break;
                case '%':
                    $result = $result->where(function ($q) use ($filter) {
                        $q = $q->where('petitioner_name', 'ILIKE', '%' . $filter . '%');
                    });
                    break;
                case '^':
                    $result = $result->where(function ($q) use ($filterData) {
                        $q->whereHas('realEstate', function ($has) use ($filterData) {
                            $has->whereHas('appraises', function ($query) use ($filterData) {
                                $query->where('full_address', 'ILIKE', '%' . $filterData . '%');
                            })->orWhereHas('apartment', function ($query) use ($filterData) {
                                $query->where('full_address', 'ILIKE', '%' . $filterData . '%');
                            });
                        });
                    });
                    break;
                default:
                    $result = $result->where(function ($q) use ($filter) {
                        $q = $q->where('certificates.id', 'like', strval($filter));
                    });
            }
        }

        $result = $result->orderByDesc('certificates.updated_at');
        $result = $result->get();
        // $status1 = $result;
        // $status2 = $result;
        // $status3 = $result;
        // $status4 = $result;
        // $status5 = $result;
        // $status1 = $status1->where('status', 1);
        // $status2 = $status2->where('status', 2);
        // $status3 = $status3->where('status', 3);
        // $status4 = $status4->where('status', 4);
        // $status5 = $status5->where('status', 5);
        // $status1 = $status1->forPage($page, $limit)
        //     ->paginate($limit);
        // $status2 = $status2->forPage($page, $limit)
        //     ->paginate($limit);
        // $status2->setPageName('status2');
        return $result;
    }


    //Step 1 - Post Data
    public function postGeneralInfomation(array $objects, int $id = null)
    {
        DB::beginTransaction();
        try {
            $check = $this->checkDuplicateData($objects, $id);
            if (isset($check)) {
                return $check;
            }
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
            if (isset($objects['appraiser_sale_id'])) {
                $branch_id = Appraiser::query()->where('id', $objects['appraiser_sale_id'])->first()->branch_id;
            } else {
                $branch_id = Appraiser::query()->where('user_id', $user->id)->first()->branch_id;
            }
            $data = $objects;
            // dd('note', $data);
            $data['document_date'] = isset($objects['document_date']) ? \Carbon\Carbon::createFromFormat('d/m/Y', $objects['document_date'])->format('Y-m-d') : null;
            $data['appraise_date'] = isset($objects['appraise_date']) ? \Carbon\Carbon::createFromFormat('d/m/Y', $objects['appraise_date'])->format('Y-m-d') : null;
            $data['certificate_date'] = isset($objects['certificate_date']) ? \Carbon\Carbon::createFromFormat('d/m/Y', $objects['certificate_date'])->format('Y-m-d') : null;
            $data['branch_id'] = $branch_id;
            $data['customer_id'] = $customerId;
            $data['issue_date_card'] = isset($objects['issue_date_card']) ? \Carbon\Carbon::createFromFormat('d/m/Y', $objects['issue_date_card'])->format('Y-m-d') : null;
            $data['survey_time'] = isset($objects['survey_time']) ? \Carbon\Carbon::createFromFormat('d-m-Y H:i', $objects['survey_time'])->format('Y-m-d H:i') : null;
            if (isset($id)) {
                $check = $this->beforeSave($id);
                if (isset($check)) {
                    return $check;
                }
                $oldCertificate = Certificate::where('id', '=', $id)->first();
                if (!isset($oldCertificate)) {
                    $data = ['message' => ErrorMessage::CERTIFICATE_NOTEXISTS . $id, 'exception' =>  ''];
                    return $data;
                }
                $certificateId = $id;
                $status = $oldCertificate->status;
                if (!isset($oldCertificate['created_by']))
                    $data['created_by'] = $user->id;

                $certificateArr = new Certificate($data);
                Certificate::where('id', $certificateId)->update($certificateArr->attributesToArray());
                $edited = Certificate::where('id', $certificateId)->first();
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
                $certificateArr = new Certificate($data);
                // dd($certificateArr);
                $certificateCreate = Certificate::query()->create($certificateArr->attributesToArray());
                $certificateId = $certificateCreate->id;
                $this->saveMethod($certificateId);
                # Activity Log "create if id = null"
                $edited = Certificate::where('id', $certificateId)->first();
                $this->CreateActivityLog($edited, $edited, 'create', 'tạo mới');
                $this->updateDocumentDescription($certificateId);
            }
            DB::commit();
            return [
                'id' => $certificateId,
                'status' => $data['status'],
                'sub_status' => $data['sub_status']
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
        // $check = $this->checkAuthorizationCertificate($id);
        // if (!empty($check))
        //     return $check;

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
            'phone_contact',
            'name_contact',
            'survey_location',
            'survey_time',
            'customer_group_id',
            'issue_date_card',
            'issue_place_card'
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
            'customerGroup:id,description',

        ];
        $result = $this->model->query()
            ->with($with)
            ->where('id', $id)
            ->select($select)
            ->first();
        $result->append(['status_text']);


        return $result;
    }

    public function getCertificate(int $id)
    {
        $result = [];
        // $check = $this->checkAuthorizationCertificate($id);
        // if (!empty($check))
        //     return $check;
        $select = [
            'id',
            'petitioner_name',
            'petitioner_phone',
            'petitioner_identity_card',
            'petitioner_address',
            'appraiser_id',
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
            'sub_status',
            'commission_fee',
            'note',
            'status_expired_at',
            'created_by',
            'document_type',
            'pre_certificate_id',
            'total_preliminary_value',
            'pre_type_id',
            'administrative_id',
            'business_manager_id',
            'document_alter_by_bank',
            'phone_contact',
            'name_contact',
            'survey_location',
            'survey_time',
            'customer_group_id',
            'issue_date_card',
            'issue_place_card'
        ];
        $with = [
            'appraiser:id,name,user_id',
            'appraiserManager:id,name,user_id',
            'appraiserControl:id,name,user_id',
            'appraiserConfirm:id,name,user_id',
            'appraiserControl:id,name,user_id',
            'appraiserSale:id,name,user_id',
            'appraiserPerform:id,name,user_id',
            'appraisePurpose:id,name',
            'customer:id,name,phone,address',
            'customerGroup:id,description',
            'otherDocuments',
            'saleDocuments' => function ($q) {
                $q->where('description', '=', 'other');
            },
            'personalProperties',
            'realEstate',
            'realEstate.appraises',
            'realEstate.appraises.lastVersion',
            'realEstate.appraises.appraiseHasAssets',
            'realEstate.appraises.assetPrice',
            'realEstate.apartment',
            'realEstate.apartment.apartmentAssetProperties',
            'realEstate.apartment.apartmentHasAssets',
            'realEstate.apartment.lastVersion',
            'realEstate.apartment.assetPrice',
            'payments:id,pay_date,amount,for_payment_of,pre_certificate_id,certificate_id',
            'preType:id,description',
            'administrative:id,name,user_id',
            'appraiserBusinessManager:id,name,user_id',
            'exportDocuments'
        ];
        $result = $this->model->query()
            ->with($with)
            ->where('id', $id)
            ->select($select)
            ->first();
        $result->append(['status_text', 'general_asset']);
        $result['checkVersion'] = AppraiseVersionService::checkVersionByCertificate($id);
        if ($result['status'] == 5) {
            $user = User::query()
                ->where('id', '=', $result['created_by'])
                ->first();
            $result['image'] = $user->image;
        }
        if ($result['status'] == 4 || $result['status'] == 9) {
            $appraiser = Appraiser::query()
                ->where('id', '=', $result['appraiser_sale_id'])
                ->first();
            if (isset($appraiser)) {
                $user = User::query()
                    ->where('id', '=', $appraiser->user_id)
                    ->first();
                $result['image'] = $user->image;
            } else {
                $result['image'] = '';
            }
        }
        if ($result['status'] == 1) {
            $appraiser = Appraiser::query()
                ->where('id', '=', $result['business_manager_id'])
                ->first();
            if (isset($appraiser)) {
                $user = User::query()
                    ->where('id', '=', $appraiser->user_id)
                    ->first();
                $result['image'] = $user->image;
            } else {
                $result['image'] = '';
            }
        }
        if ($result['status'] == 2) {
            $appraiser = Appraiser::query()
                ->where('id', '=', $result['appraiser_perform_id'])
                ->first();
            if (isset($appraiser)) {
                $user = User::query()
                    ->where('id', '=', $appraiser->user_id)
                    ->first();
                $result['image'] = $user->image;
            } else {
                $result['image'] = '';
            }
        }
        if ($result['status'] == 3) {
            $appraiser = Appraiser::query()
                ->where('id', '=', $result['appraiser_id'])
                ->first();
            if (isset($appraiser)) {
                $user = User::query()
                    ->where('id', '=', $appraiser->user_id)
                    ->first();
                $result['image'] = $user->image;
            } else {
                $result['image'] = '';
            }
        }
        if ($result['status'] == 7) {
            $appraiser = Appraiser::query()
                ->where('id', '=', $result['appraiser_control_id'])
                ->first();
            if (isset($appraiser)) {
                $user = User::query()
                    ->where('id', '=', $appraiser->user_id)
                    ->first();
                $result['image'] = $user->image;
            } else {
                $result['image'] = '';
            }
        }

        if ($result['status'] == 8) {
            $appraiser = Appraiser::query()
                ->where('id', '=', $result['administrative_id'])
                ->first();
            if (isset($appraiser)) {
                $user = User::query()
                    ->where('id', '=', $appraiser->user_id)
                    ->first();
                $result['image'] = $user->image;
            } else {
                $result['image'] = '';
            }
        }

        return $result;
    }

    private function checkExistsAppraise(int $id)
    {
        $result = false;
        $certificate = $this->model->query()->where('id', $id)->first();
        if (isset($certificate)) {
            $result = true;
        }
        // $documentType = $certificate->document_type;
        // if (!isset($documentType)) {
        //     $documentType[0] = 'BDS';
        // }
        // if ($documentType[0] == 'BDS') {
        //     if (CertificateHasAppraise::where('certificate_id', $id)->exists()) {
        //         $result = true;
        //     }
        // } else {
        //     if (CertificateHasPersonalProperty::where('certificate_id', $id)->exists()) {
        //         $result = true;
        //     }
        // }
        return $result;
    }

    private function updateAppraiseStatus($certificateId, $status, $subStatus)
    {
        $certificate = $this->model->query()->with(['realEstate', 'personalProperties'])->where('id', $certificateId)->first(['id', 'status', 'sub_status']);
        $realEstate = $certificate->realEstate;
        $personalProperties = $certificate->personalProperties;
        $updateData = [
            'status' => $status,
            'sub_status' => $subStatus
        ];
        if (!empty($realEstate)) {
            $assetTypeCC = Dictionary::query()->where(['type' => 'LOAI_TAI_SAN', 'acronym' => 'CC'])->first('id')->id;
            foreach ($realEstate as $item) {
                if (!empty($assetTypeCC) && $item->asset_type_id == $assetTypeCC) {
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
                $certificate = $this->model->query()->where('id', $id)->first();
                // Phân lại hồ sơ
                if ($certificate->status ==  $request['status']) {
                    $check = $this->beforeUpdateStatusRedistribute($id);
                    if (isset($check)) {
                        return $check;
                    }
                } else {
                    // # đang tắt khối block xác thực
                    $check = $this->beforeUpdateStatus($id);
                    if (isset($check)) {
                        return $check;
                    }
                }

                // $certificate = $this->model->query()->where('id', $id)->first();
                $currentStatus = $certificate->status;
                $currentSubStatus = $certificate->sub_status;
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
                    switch ($status) {
                        case 10: //Move to first step in workflow -> remove all asset in certificate
                            // $this->updateAppraiseStatus($id, $baseStatus, $baseSubStatus);
                            $this->removeAssetInCertificate($id);
                            break;
                        default:
                            $this->updateAppraiseStatus($id, $status, $subStatus);
                    }
                    $updateArray = [
                        'status' => $status,
                        'sub_status' => $subStatus,
                        'status_updated_at' => date('Y-m-d H:i:s'),
                        'status_expired_at' => $status_expired_at,
                    ];

                    if (isset($request['appraiser_sale_id'])) {
                        $updateArray['appraiser_sale_id'] = $request['appraiser_sale_id'];
                    }
                    if (isset($request['appraiser_perform_id'])) {
                        $updateArray['appraiser_perform_id'] = $request['appraiser_perform_id'];
                    }
                    if (isset($request['appraiser_control_id'])) {
                        $updateArray['appraiser_control_id'] = $request['appraiser_control_id'];
                    }
                    if (isset($request['administrative_id'])) {
                        $updateArray['administrative_id'] = $request['administrative_id'];
                    }
                    if (isset($request['business_manager_id'])) {
                        $updateArray['business_manager_id'] = $request['business_manager_id'];
                    }

                    $result = $this->model->query()
                        ->where('id', '=', $id)
                        ->update($updateArray);

                    # Chuyển status từ số sang text
                    $edited = Certificate::where('id', $id)->first();
                    if ($current > $next) {
                        // $logDescription = $request['status_description'] . ' '.  $request['status_config']['description'];
                        $description = $currentConfig !== false ? $currentConfig['description'] : '';
                        $logDescription = 'từ chối ' .  $description;
                        if ($logDescription == "từ chối Phân hồ sơ") {
                            $description = $nextConfig !== false ? $nextConfig['description'] : '';
                            $logDescription = 'cập nhật trạng thái ' . $description;
                        }
                    } elseif ($current == $next) {
                        // Phân lại hồ sơ
                        $logDescription = 'phân lại hồ sơ ';
                    } else {
                        $description = $nextConfig !== false ? $nextConfig['description'] : '';
                        $logDescription = 'cập nhật trạng thái ' . $description;
                    }
                    $logName = 'update_status';
                    // activity-log Update status
                    $note = $request['status_note'] ?? '';
                    $reason_id = $request['status_reason_id'] ?? null;
                    $this->CreateActivityLog($edited, $edited, $logName, $logDescription, $note, $reason_id);

                    $this->notifyChangeStatus($id, $status);
                }
                // $result = $this->getAppraisalTeam($id);
                $result = $this->getCertificate($id);

                return $result;
            } catch (Exception $exception) {
                Log::error($exception);
                throw $exception;
            }
        });
    }

    private function sqlRealEstate($realEstateIds, $assetTypeIds, $where, $perPage, $page = 1)
    {
        $select = [
            'real_estates.id',
            'real_estates.updated_at',
            'real_estates.asset_type_id',
            'real_estates.created_at',
            'appraises.full_address as address_nd',
            'apartment_assets.full_address as address_cc',
            DB::raw("real_estates.appraise_asset as name, total_area,
                coalesce(case
                    when  real_estates.round_total > 0
                    then ceil(real_estates.total_price / power(10, real_estates.round_total)) * power(10, real_estates.round_total)
                    when   real_estates.round_total < 0
                        then floor( real_estates.total_price * abs(power(10, real_estates.round_total))  ) / abs(power(10, real_estates.round_total))
                    else
                        real_estates.total_price
                end, 0) as total_price
                "),
            'users.name as created_by.name'
        ];
        $result = RealEstate::query()
            ->select($select)
            ->join('users', 'users.id', '=', 'real_estates.created_by')
            ->leftjoin('appraises', 'appraises.real_estate_id', '=', 'real_estates.id')
            ->leftjoin('apartment_assets', 'apartment_assets.real_estate_id', '=', 'real_estates.id')
            ->where($where)
            ->whereNull('real_estates.certificate_id');

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
            DB::raw("null as address_nd"),
            DB::raw("null as address_cc"),
            DB::raw("0 as total_area"),
            'total_price',
            'users.name as created_by.name'
        ];

        $result = PersonalProperty::query()
            ->select($select)
            ->join('users', 'users.id', '=', 'personal_properties.created_by')
            ->where($where)
            ->whereNull('certificate_id');

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
        $certificateId = request()->get('certificate_id');
        $certificate = Certificate::where('id', $certificateId)->first();
        $realEstateIds = [];
        $propertyIds = [];
        if ($page == 1) {
            $realEstates = $certificate->realEstate;
            foreach ($realEstates as $realEstate) {
                $realEstateIds[] = $realEstate->real_estate_id;
            }
            $personals = $certificate->personalProperties;
            foreach ($personals as $personal) {
                $propertyIds[] = $personal->personal_property_id;
            }
        }
        $whereR = ['real_estates.created_by' => $user->id, 'real_estates.status' => $status];
        $where = ['created_by' => $user->id, 'status' => $status];
        $result = null;

        $realEstateTyeIds = [];
        $sqlRealEstate = $this->sqlRealEstate($realEstateIds, $realEstateTyeIds, $whereR, $perPage, $page);

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

    private function saveDocumentLaw(int $certificateId, array $realEstateList, string $type)
    {
        if (isset($realEstateList)) {
            $provine = [];
            if ($type == 'apartment') {
                foreach ($realEstateList as $realEstateId) {
                    $data = ApartmentAsset::with('province:id,name')->where('real_estate_id', $realEstateId)->select('province_id')->first();
                    $provine[] = $data['province']['name'] ?? 'Tất cả';
                    // dd($realEstateId,$data);
                }
            } else {
                foreach ($realEstateList as $realEstateId) {
                    $data = Appraise::with('province:id,name')->where('real_estate_id', $realEstateId)->select('province_id')->first();
                    $provine[] = $data['province']['name'] ?? 'Tất cả';
                }
            }
            $provine[] = 'Tất cả';
            $lawDocument = AppraiseLawDocument::whereIn('provinces', $provine)->orderBy('position')->get();
            CertificateLegalDocumentsOnValuation::query()->where('certificate_id', '=', $certificateId)->forceDelete();
            CertificateLegalDocumentsOnConstruction::query()->where('certificate_id', '=', $certificateId)->forceDelete();
            CertificateLegalDocumentsOnLand::query()->where('certificate_id', '=', $certificateId)->forceDelete();
            CertificateLegalDocumentsOnLocal::query()->where('certificate_id', '=', $certificateId)->forceDelete();

            if (isset($lawDocument)) {
                foreach ($lawDocument as $law) {
                    if ($law['type'] == 'XAY_DUNG') {
                        $data = [];
                        $data['certificate_id'] = $certificateId;
                        $data['certificate_law_id'] = $law['id'];
                        $insertData = new CertificateLegalDocumentsOnConstruction($data);
                        QueryBuilder::for($insertData)
                            ->insert($insertData->attributesToArray());
                    } elseif ($law['type'] == 'DIA_PHUONG') {
                        $data = [];
                        $data['certificate_id'] = $certificateId;
                        $data['certificate_law_id'] = $law['id'];
                        $insertData = new CertificateLegalDocumentsOnLocal($data);
                        QueryBuilder::for($insertData)
                            ->insert($insertData->attributesToArray());
                    } elseif ($law['type'] == 'THAM_DINH_GIA') {
                        $data = [];
                        $data['certificate_id'] = $certificateId;
                        $data['certificate_law_id'] = $law['id'];
                        $insertData = new CertificateLegalDocumentsOnValuation($data);
                        QueryBuilder::for($insertData)
                            ->insert($insertData->attributesToArray());
                    } elseif ($law['type'] == 'DAT_DAI') {
                        $data = [];
                        $data['certificate_id'] = $certificateId;
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

    private function saveMethod(int $certificateId)
    {
        if (isset($certificateId)) {
            if (
                CertificateMethodUsed::query()->where('certificate_id', '=', $certificateId)->doesntExist()
                || CertificateBasisProperty::query()->where('certificate_id', '=', $certificateId)->doesntExist()
                || CertificatePrinciple::query()->where('certificate_id', '=', $certificateId)->doesntExist()
                || CertificateApproach::query()->where('certificate_id', '=', $certificateId)->doesntExist()
            ) {
                CertificateMethodUsed::query()->where('certificate_id', '=', $certificateId)->forceDelete();
                CertificateBasisProperty::query()->where('certificate_id', '=', $certificateId)->forceDelete();
                CertificatePrinciple::query()->where('certificate_id', '=', $certificateId)->forceDelete();
                CertificateApproach::query()->where('certificate_id', '=', $certificateId)->forceDelete();
                $otherDocument = AppraiseOtherInformation::where('is_defaults', true)->get();
                if (isset($otherDocument)) {
                    foreach ($otherDocument as $other) {
                        if ($other['type'] == 'CO_SO_THAM_DINH') {
                            $data = [];
                            $data['certificate_id'] = $certificateId;
                            $data['certificate_basis_property_id'] = $other['id'];
                            $insertData = new CertificateBasisProperty($data);
                            QueryBuilder::for($insertData)
                                ->insert($insertData->attributesToArray());
                        } elseif ($other['type'] == 'NGUYEN_TAC_THAM_DINH') {
                            $data = [];
                            $data['certificate_id'] = $certificateId;
                            $data['certificate_principle_id'] = $other['id'];
                            $insertData = new CertificatePrinciple($data);
                            QueryBuilder::for($insertData)
                                ->insert($insertData->attributesToArray());
                        } elseif ($other['type'] == 'CACH_TIEP_CAN_CHI_PHI') {
                            $data = [];
                            $data['certificate_id'] = $certificateId;
                            $data['certificate_approach_id'] = $other['id'];
                            $insertData = new CertificateApproach($data);
                            QueryBuilder::for($insertData)
                                ->insert($insertData->attributesToArray());
                        } elseif ($other['type'] == 'PHUONG_PHAP_THAM_DINH_SU_DUNG') {
                            $data = [];
                            $data['certificate_id'] = $certificateId;
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

    private function updateDocumentDescription(int $certificateId)
    {
        // ApartmentAssetAppraisalBase
        if (Certificate::where('id', $certificateId)->exists()) {
            // $cert = Certificate::where('id', $certificateId)->first()->toArray();
            // if ($cert['document_type'] && $cert['document_type'][0] == 'CC'){
            //     $ccu =  RealEstate::where('certificate_id', $certificateId)->first()->toArray();
            //     $apartment = ApartmentAsset::query()->where('real_estate_id', $ccu['id'])->first()->toArray();
            //     $apartmentId = $apartment['id'];
            //     $bases = ApartmentAssetAppraisalBase::query()->where('apartment_asset_id', $apartmentId)->first()->toArray();
            //     Certificate::where('id', $certificateId)
            //     ->update(['document_description' => $bases['description']]);
            // } else {
            //     Certificate::where('id', $certificateId)
            //     ->update(['document_description' => ValueDefault::CERTIFICATE_DESCRIPTION]);
            // }
            Certificate::where('id', $certificateId)
                ->update(['document_description' => ValueDefault::CERTIFICATE_DESCRIPTION]);
        }
    }

    private function saveConstructionCompany(int $certificateId, int $certificateAppraiseId, int $appraiseId, int $justDelete = 0, int $appraise_tangible_id = null, int $certificate_tangbile_id = null)
    {
        if ($justDelete == 1) {
            CertificateAssetConstructionCompany::query()->where('certificate_id', $certificateId)->where('appraise_id', $certificateAppraiseId)->forceDelete();
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
                $item['certificate_id'] = $certificateId;
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

    public function updateCertificate_v2(array $objects, int $certificateId)
    {
        $result =  [];

        DB::beginTransaction();
        try {
            // # khóa khối block xác thực
            $check = $this->beforeSave($certificateId);
            if (isset($check)) {
                return $check;
            }

            if (Certificate::where('id', $certificateId)->where('status', 2)->exists()) {
                $oldCertificate = Certificate::where('id', $certificateId)->first();
                $documentType = $oldCertificate->document_type;
                if (!isset($documentType))
                    $documentType = ['BDS'];
                if ($documentType == ['BDS']) {
                    if (isset($objects['general_asset'])) {
                        foreach ($objects['general_asset'] as $appraise) {
                            $check = CommonService::checkValidAppraise($appraise['general_asset_id']);
                            if (isset($check))
                                return $check;
                        }
                    }
                    $oldAppraises = $oldCertificate->appraises;
                    $oldCertificateAssetIds = [];
                    foreach ($oldAppraises as $oldAppraise) {
                        $oldCertificateAssetIds[$oldAppraise->appraise_id] = $oldAppraise->id;
                    }
                    // $this->saveMethod($certificateId);
                    $this->saveDocumentLaw($certificateId, $objects['general_asset'], 'land');
                    $this->saveMethod($certificateId);
                    $this->updateDocumentDescription($certificateId);

                    if (isset($objects['general_asset'])) {
                        CertificateHasAppraise::query()->where('certificate_id', '=', $certificateId)->forceDelete();

                        foreach ($objects['general_asset'] as $appraise) {
                            if (!isset($oldCertificateAssetIds[$appraise['general_asset_id']])) {
                                $appraiseData = Appraise::where('id', $appraise['general_asset_id'])->first();
                                if (!isset($appraiseData)) continue;
                                $oldAppraiseId = $appraise['general_asset_id'];

                                $appraiseId = $appraise['general_asset_id'];
                                Appraise::where('id',  $appraiseId)->update(['status' => 3]); // updateStatus : updateStatus : 3 = locked
                                RealEstate::query()->whereHas('appraises', function ($has) use ($appraiseId) {
                                    $has->where('id', $appraiseId);
                                })->update(['status' => 3]);

                                CertificateAsset::query()->where('appraise_id', '=', $oldAppraiseId)->forceDelete();
                                $appraiseData->appraise_id = $appraise['general_asset_id'];

                                $certificateAsset = new CertificateAsset($appraiseData->toArray());
                                $certificateAssetId = QueryBuilder::for($certificateAsset)
                                    ->insertGetId($certificateAsset->attributesToArray());

                                $appraise['certificate_id'] = $certificateId;
                                $appraise['appraise_id'] = $certificateAssetId;
                                $appraise['version'] = '2.0';
                                $appraise = new CertificateHasAppraise($appraise);
                                $assetGeneralId = QueryBuilder::for($appraise)
                                    ->insertGetId($appraise->attributesToArray());

                                #region 1
                                //echo '<pre>';
                                $itemDatas = AppraiseHasAsset::where('appraise_id', $appraiseId)->get();
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
                                CertificateAssetUnitPrice::where('certificate_id', $certificateId)
                                    ->where('appraise_id', $certificateAssetId)
                                    ->forceDelete();
                                foreach ($appraiseUnitPriceDatas as $itemData) {
                                    if (isset($itemData)) {
                                        $itemData->certificate_id = $certificateId;
                                        $itemData->appraise_id = $certificateAssetId;
                                        $item = new CertificateAssetUnitPrice($itemData->toArray());
                                        $itemId = QueryBuilder::for($item)->insertGetId($item->attributesToArray());
                                    }
                                }

                                $appraiseUnitPriceDatas = AppraiseUnitArea::where('appraise_id', $appraiseId)->get();
                                CertificateAssetUnitArea::where('certificate_id', $certificateId)
                                    ->where('appraise_id', $certificateAssetId)
                                    ->forceDelete();
                                foreach ($appraiseUnitPriceDatas as $itemData) {
                                    if (isset($itemData)) {
                                        $itemData->certificate_id = $certificateId;
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
                                        $this->saveConstructionCompany($certificateId, $certificateAssetId, $oldAppraiseId, 0, $itemData->id, $itemId);
                                    }
                                }

                                $itemDatas = AppraiseVersion::where('appraise_id', $appraiseId)->get();
                                foreach ($itemDatas as $itemData) {
                                    if (isset($itemData)) {
                                        $itemData->appraise_id = $certificateAssetId;
                                        $item = new CertificateAssetVersion($itemData->toArray());
                                        $itemId = QueryBuilder::for($item)->insertGetId($item->attributesToArray());
                                    }
                                }

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
                                #endregion
                                $certificateAssetIds[$appraiseId] = $certificateAssetId;
                            } else {
                                $certificateAssetId = $oldCertificateAssetIds[$appraise['general_asset_id']];
                                $appraiseTmp = $appraise;
                                $oldAppraiseId = $appraise['general_asset_id'];
                                $appraiseTmp['certificate_id'] = $certificateId;
                                $appraiseTmp['appraise_id'] = $certificateAssetId;
                                $appraiseTmp['version'] = '2.0';
                                $appraiseTmp = new CertificateHasAppraise($appraiseTmp);
                                $assetGeneralId = QueryBuilder::for($appraiseTmp)
                                    ->insertGetId($appraiseTmp->attributesToArray());
                                $certificateAssetIds[$appraise['appraise_id']] = $certificateAssetId;
                            }
                        }
                        foreach ($oldAppraises as $appraiseTmp) {
                            if (isset($appraiseTmp->appraise_id) && !isset($certificateAssetIds[$appraiseTmp->appraise_id])) {
                                $this->saveConstructionCompany($certificateId, $appraiseTmp->id, $appraiseTmp->appraise_id, 1);
                                $appraiseId = $appraiseTmp->appraise_id;
                                Appraise::where('id', $appraiseId)->update(['status' => 2]); // updateStatus : 3 = locked
                                RealEstate::query()->whereHas('appraises', function ($has) use ($appraiseId) {
                                    $has->where('id', $appraiseId);
                                })->update(['status' => 2]);
                            } else {
                                $appraiseId = $appraiseTmp->appraise_id;
                                Appraise::where('id', $appraiseId)->update(['status' => 3]); // updateStatus : 3 = locked
                                RealEstate::query()->whereHas('appraises', function ($has) use ($appraiseId) {
                                    $has->where('id', $appraiseId);
                                })->update(['status' => 3]);
                            }
                        }
                    }
                    CommonService::getCertificateAssetPriceTotal_v2($certificateId);
                } else {
                    if (isset($objects['general_asset'])) {
                        $oldAppraises = $oldCertificate->personalProperties;
                        CertificateHasPersonalProperty::query()->where('certificate_id', '=', $certificateId)->forceDelete();

                        $oldCertificateAssetIds = [];
                        foreach ($oldAppraises as $oldAppraise) {
                            $oldCertificateAssetIds[$oldAppraise->personal_property_id] = $oldAppraise->id;
                        }
                        foreach ($objects['general_asset'] as $appraise) {
                            if (!isset($oldCertificateAssetIds[$appraise['general_asset_id']])) {
                                $appraiseData = PersonalProperty::where('id', $appraise['general_asset_id'])->first();
                                if (!isset($appraiseData)) continue;

                                $oldAppraiseId = $appraise['general_asset_id'];
                                PersonalProperty::where('id', $appraise['general_asset_id'])->update(['status' => 3]); // updateStatus : updateStatus : 3 = locked

                                CertificatePersonalProperty::query()->where('personal_property_id', '=', $oldAppraiseId)->forceDelete();
                                $appraiseData->personal_property_id = $appraise['general_asset_id'];
                                // dd($appraiseData);
                                // $certificateAsset = new CertificatePersonalProperty($appraiseData->toArray());
                                // // dd($certificateAsset);
                                // $certificateAssetId = QueryBuilder::for($certificateAsset)
                                //     ->insertGetId($certificateAsset->attributesToArray());

                                $certificateAsset = new CertificatePersonalProperty($appraiseData->toArray());
                                $certificatePersonalProperty = CertificatePersonalProperty::query()->create($certificateAsset->attributesToArray());
                                $certificateAssetId = $certificatePersonalProperty->id;
                                $appraiseId = $appraise['general_asset_id'];
                                $appraise['certificate_id'] = $certificateId;
                                $appraise['personal_property_id'] = $certificateAssetId;
                                $appraise['version'] = '2.0';
                                $appraise = new CertificateHasPersonalProperty($appraise);
                                CertificateHasPersonalProperty::query()->create($appraise->attributesToArray());
                                // $appraise = new CertificateHasPersonalProperty($appraise);
                                // $assetGeneralId = QueryBuilder::for($appraise)
                                //     ->insertGetId($appraise->attributesToArray());

                                $this->UpdatePersonaltyData($oldAppraiseId, $certificateAssetId, $appraiseData->assetType->acronym);
                            } else {
                                $certificateAssetId = $oldCertificateAssetIds[$appraise['general_asset_id']];
                                $appraiseTmp = $appraise;
                                $oldAppraiseId = $appraise['general_asset_id'];
                                $appraiseTmp['certificate_id'] = $certificateId;
                                $appraiseTmp['personal_property_id'] = $certificateAssetId;
                                $appraiseTmp['version'] = '2.0';
                                $appraiseTmp = new CertificateHasPersonalProperty($appraiseTmp);
                                $assetGeneralId = QueryBuilder::for($appraiseTmp)
                                    ->insertGetId($appraiseTmp->attributesToArray());
                                $certificateAssetIds[$appraise['general_asset_id']] = $certificateAssetId;
                            }
                        }
                        foreach ($oldAppraises as $appraiseTmp) {
                            $personal = new EloquentPersonalPropertiesRepository(new PersonalProperty());
                            if (isset($appraiseTmp->personal_property_id) && !isset($certificateAssetIds[$appraiseTmp->personal_property_id])) {
                                CertificatePersonalProperty::query()->where('personal_property_id', '=', $appraiseTmp->personal_property_id)->forceDelete();
                                $personal->updateStatus($appraiseTmp->personal_property_id, 2);
                            } else {
                                $personal->updateStatus($appraiseTmp->personal_property_id, 3);
                            }
                        }
                    }
                    $this->updatePersonaltyPrice($certificateId);
                }
                $edited = Certificate::where('id', $certificateId)->first();

                // activity-log cập nhật thông tin chi tiết
                $this->CreateActivityLog($edited, $edited, 'update_data', 'cập nhật thông tin chi tiết');
            } else {
                $result = ['message' => ErrorMessage::CERTIFICATE_CHOOSE_APPRAISE, 'exception' => ''];
                return $result;
            }
            DB::commit();
            $result = $this->getCertificateAppraise($certificateId);
        } catch (exception $ex) {
            DB::rollBack();
            Log::error($ex);
            $result = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $ex->getMessage()];
        }
        return $result;
    }

    public function updateCertificateVersion($certificateId, $object)
    {
        try {
            DB::beginTransaction();
            if (!empty($object['general_asset'])) {
                $appraiseType = Dictionary::query()->whereIn('acronym', ['DCN', 'DT', 'CC'])->get()->toArray();
                $appraiseTypeIds = Arr::pluck($appraiseType, 'id');
                foreach ($object['general_asset'] as $item) {
                    if (array_search($item['asset_type_id'], $appraiseTypeIds) !== false) {
                        $certificateRealEstateId = $item['real_estate_id'];
                        $realEstateId = $item['general_asset_id'];
                        $realEstate = RealEstate::query()->where('id', $realEstateId)->first();
                        $certificateRealEstate = CertificateRealEstate::query()->find($certificateRealEstateId);
                        $certificateRealEstate->update($realEstate->toArray());
                        // dd($realEstate->apartment);
                        if (!empty($realEstate->appraises)) {
                            $this->insertAppraiseData($realEstateId, $certificateRealEstateId, $certificateId);
                        }
                        if (!empty($realEstate->apartment)) {
                            $this->insertApartmentData($realEstateId, $certificateRealEstateId, $certificateId);
                        }
                    }
                }
            }
            CommonService::getCertificateAssetPriceTotal_v2($certificateId);
            // $this->updateTotalPrie($certificateId);
            DB::commit();
            $certificate = $this->getCertificateAppraise($certificateId);
            return $certificate;
        } catch (Exception $ex) {
            DB::rollBack();
            return ['message' => $ex->getMessage(), 'exception' => ''];
        }
    }
    private function insertAppraiseData($realEstateId, int $certificateRealEstateId, int $certificateId)
    {
        if (CertificateAsset::query()->where('real_estate_id', $certificateRealEstateId)->exists()) {
            CertificateAsset::query()->where('real_estate_id', $certificateRealEstateId)->forceDelete();
        }
        if (CertificateHasAppraise::query()->where('certificate_id', $certificateId)->exists()) {
            CertificateHasAppraise::query()->where('certificate_id', $certificateId)->forceDelete();
        }
        $appraise = Appraise::query()->where('real_estate_id', $realEstateId)->first()->toArray();
        $appraiseId = $appraise['id'];
        $appraise['real_estate_id'] = $certificateRealEstateId;
        $appraise['appraise_id'] = $appraiseId;
        //appraise
        $appraiseData = new CertificateAsset($appraise);
        $certificateAppraise =  $appraiseData->newQuery()->create($appraiseData->attributesToArray());
        $certificateAssetId = $certificateAppraise->id;
        $appraise['certificate_id'] = $certificateId;
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
        CertificateAssetUnitPrice::where('certificate_id', $certificateId)
            ->where('appraise_id', $certificateAssetId)
            ->forceDelete();
        foreach ($appraiseUnitPriceDatas as $itemData) {
            if (isset($itemData)) {
                $itemData->certificate_id = $certificateId;
                $itemData->appraise_id = $certificateAssetId;
                $item = new CertificateAssetUnitPrice($itemData->toArray());
                $itemId = QueryBuilder::for($item)->insertGetId($item->attributesToArray());
            }
        }

        $appraiseUnitPriceDatas = AppraiseUnitArea::where('appraise_id', $appraiseId)->get();
        CertificateAssetUnitArea::where('certificate_id', $certificateId)
            ->where('appraise_id', $certificateAssetId)
            ->forceDelete();
        foreach ($appraiseUnitPriceDatas as $itemData) {
            if (isset($itemData)) {
                $itemData->certificate_id = $certificateId;
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
                $this->saveConstructionCompany($certificateId, $certificateAssetId, $appraiseId, 0, $itemData->id, $itemId);
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
    private function updateDetailRealEstateAppraise($certificateId, $realEstates, $realEstateAppraiseIds)
    {
        $oldRealEstates = $realEstates;
        // $appraiseRepo = new EloquentAppraiseRepository(new Appraise());
        CertificateHasRealEstate::query()
            ->where('certificate_id', $certificateId)
            ->whereHas('realEstates', function ($has) use ($realEstateAppraiseIds) {
                $has->whereIn('real_estate_id', $realEstateAppraiseIds);
            })->forceDelete();
        foreach ($oldRealEstates as $realEstate) {
            $oldCertificateAssetIds[$realEstate->real_estate_id] = $realEstate->id;
        }
        $this->saveDocumentLaw($certificateId, $realEstateAppraiseIds, 'land');
        $this->saveMethod($certificateId);
        $this->updateDocumentDescription($certificateId);

        foreach ($realEstateAppraiseIds as $realEstateId) {
            if (!isset($oldCertificateAssetIds[$realEstateId])) {
                $assetData = RealEstate::where('id', $realEstateId)->first();
                if (!isset($assetData)) continue;
                CertificateRealEstate::query()->where('real_estate_id', $realEstateId)->forceDelete();
                $assetData->real_estate_id = $realEstateId;
                $certificateRealEstate = new CertificateRealEstate($assetData->toArray());
                $certificateAssetId = CertificateRealEstate::query()->insertGetId($certificateRealEstate->attributesToArray());
                $realEstateData['certificate_id'] = $certificateId;
                $realEstateData['real_estate_id'] = $certificateAssetId;
                $realEstateData['version'] = '1.0';
                CertificateHasRealEstate::query()->create($realEstateData);
                $this->insertAppraiseData($realEstateId, $certificateAssetId, $certificateId);
                // $appraiseRepo->updateRealEstateStatus($realEstateId, 3);
                $this->updateRealEstateCertificateId($realEstateId, $certificateId);
            } else {
                $certificateAssetId = $oldCertificateAssetIds[$realEstateId];
                $realEstate = [];
                $realEstate['certificate_id'] = $certificateId;
                $realEstate['real_estate_id'] = $certificateAssetId;
                $realEstate['version'] = '2.0';
                CertificateHasRealEstate::query()->create($realEstate);
            }
        }
        $oldRealEstateIds = Arr::pluck($oldRealEstates, 'real_estate_id');
        $diffs = array_diff($oldRealEstateIds, $realEstateAppraiseIds);

        if (!empty($diffs)) {
            foreach ($diffs as $diff) {
                // $appraiseRepo->updateRealEstateStatus($diff, 2);
                $this->updateRealEstateCertificateId($diff);
                $realEstate = CertificateRealEstate::query()->where('real_estate_id', $diff)->first();
                $realEstateId = $realEstate->id;
                $certificateAsset = CertificateAsset::query()->where('real_estate_id', $realEstateId)->first();
                if (isset($certificateAsset))
                    $this->saveConstructionCompany($certificateId, $certificateAsset->id, $certificateAsset->appraise_id, 1);
                CertificateRealEstate::query()->where('real_estate_id', $diff)->forceDelete();
            }
        }
    }
    private function insertApartmentData(int $realEstateId, int $certificateAssetId, int $certificateId)
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
        $certificateApartment = CertificateApartment::query()->create($apartmentData->attributesToArray());
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
    private function updateDetailRealEstateApartment($certificateId, $realEstates, $realEstateApartmentIds)
    {
        $oldRealEstates = $realEstates;
        // $apartmentRepo = new EloquentApartmentAssetRepository(new ApartmentAsset());
        CertificateHasRealEstate::query()
            ->where('certificate_id', $certificateId)
            ->whereHas('realEstates', function ($has) use ($realEstateApartmentIds) {
                $has->whereIn('real_estate_id', $realEstateApartmentIds);
            })->forceDelete();

        $this->saveDocumentLaw($certificateId, $realEstateApartmentIds, 'apartment');
        $this->saveMethod($certificateId);
        $this->updateDocumentDescription($certificateId);

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
                $realEstateData['certificate_id'] = $certificateId;
                $realEstateData['real_estate_id'] = $certificateAssetId;
                $realEstateData['version'] = '1.0';
                CertificateHasRealEstate::query()->create($realEstateData);
                $this->insertApartmentData($realEstateId, $certificateAssetId, $certificateId);
                // $apartmentRepo->updateStatus($realEstateId, 3);
                $this->updateRealEstateCertificateId($realEstateId, $certificateId, false);
            } else {
                $certificateAssetId = $oldCertificateAssetIds[$realEstateId];
                $realEstate = [];
                $realEstate['certificate_id'] = $certificateId;
                $realEstate['real_estate_id'] = $certificateAssetId;
                $realEstate['version'] = '2.0';
                CertificateHasRealEstate::query()->create($realEstate);
            }
        }
        $oldRealEstateIds = Arr::pluck($oldRealEstates, 'real_estate_id');
        $diffs = array_diff($oldRealEstateIds, $realEstateApartmentIds);
        if (!empty($diffs)) {
            foreach ($diffs as $diff) {
                // $apartmentRepo->updateStatus($diff, 2);
                $this->updateRealEstateCertificateId($realEstateId, null, false);
                CertificateRealEstate::query()->where('real_estate_id', '=', $diff)->forceDelete();
            }
        }
    }
    private function updateDetailPersonalProperty($certificateId, $oldPersonals, $personalIds)
    {
        CertificateHasPersonalProperty::query()->where('certificate_id', '=', $certificateId)->forceDelete();
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

                $personalData['certificate_id'] = $certificateId;
                $personalData['personal_property_id'] = $certificateAssetId;
                $personalData['version'] = '1.0';
                CertificateHasPersonalProperty::query()->create($personalData);

                $this->UpdatePersonaltyData($personalId, $certificateAssetId, $assetData->assetType->acronym);
                $this->updatePersonalPropertyCertificateId($personalId, $certificateId);
            } else {
                $certificateAssetId = $oldCertificateAssetIds[$personalId];
                $personal = [];
                $personal['certificate_id'] = $certificateId;
                $personal['personal_property_id'] = $certificateAssetId;
                $personal['version'] = '2.0';
                CertificateHasPersonalProperty::query()->create($personal);
            }
        }
        $oldPersonalIds = Arr::pluck($oldPersonals, 'personal_property_id');
        $diffs = array_diff($oldPersonalIds, $personalIds);
        if (!empty($diffs)) {
            foreach ($diffs as $diff) {
                // $personalRep->updateStatus($diff, 2);
                $this->updatePersonalPropertyCertificateId($diff);
                CertificatePersonalProperty::query()->where('personal_property_id', '=', $diff)->forceDelete();
            }
        }
    }
    public function updateCertificateV3(array $objects, int $certificateId)
    {
        $result =  [];

        DB::beginTransaction();
        try {
            // # khóa khối block xác thực
            $check = $this->beforeSave($certificateId);
            if (isset($check)) {
                return $check;
            }
            $chekcPrice = $objects['check_price'] ?? false;
            if (Certificate::where('id', $certificateId)->where('status', 2)->exists()) {
                if (isset($objects['general_asset'])) {
                    $generalAsset = $objects['general_asset'];
                    $personalIds = [];
                    $realEstateApartmentIds = [];
                    $realEstateAppraiseIds = [];
                    if (count($generalAsset) > 0) {
                        $dictionary = Dictionary::query()->where(['type' => 'LOAI_TAI_SAN', 'status' => 1])->get();
                        $appraiseType = $dictionary->whereIn('acronym', ['DCN', 'DT']);
                        $apartmentType = $dictionary->whereIn('acronym', ['CC']);
                        $personalType = $dictionary->whereNotIn('acronym', ['DCN', 'DT', 'CC']);
                        $appraiseTypeIds = null;
                        $apartmentTypeIds = null;
                        $personalTypeIds = null;
                        $appraiseTypeIds = Arr::pluck($appraiseType, 'id');
                        $apartmentTypeIds = Arr::pluck($apartmentType, 'id');
                        $personalTypeIds = Arr::pluck($personalType, 'id');
                        // $personalIds = [];
                        // $realEstateApartmentIds = [];
                        // $realEstateAppraiseIds = [];
                        foreach ($generalAsset as $asset) {
                            $assetTypeId = $asset['asset_type_id'];
                            $assetId = $asset['general_asset_id'];
                            if (array_search($assetTypeId, $appraiseTypeIds) !== false) {
                                if ($chekcPrice) {
                                    $check = CommonService::checkValidAppraise($assetId);
                                    if (isset($check))
                                        return $check;
                                }
                                $realEstateAppraiseIds[] = $assetId;
                            } elseif (array_search($assetTypeId, $apartmentTypeIds) !== false) {
                                if ($chekcPrice) {
                                    $check = CommonService::checkValidAppraise($assetId);
                                    if (isset($check))
                                        return $check;
                                }
                                $realEstateApartmentIds[] = $assetId;
                            } elseif (array_search($assetTypeId, $personalTypeIds) !== false) {
                                $personalIds[] = $assetId;
                            }
                        }
                        if (count($personalIds) > 0) {
                            $this->updateDetailPersonalProperty($certificateId, $oldPersonals ?? [], $personalIds);
                        }
                        if (count($realEstateApartmentIds) > 0) {
                            $this->updateDetailRealEstateApartment($certificateId, $oldRealEstateApartment ?? [], $realEstateApartmentIds);
                        }
                        if (count($realEstateAppraiseIds) > 0) {
                            $this->updateDetailRealEstateAppraise($certificateId, $oldRealEstateAppraise ?? [], $realEstateAppraiseIds);
                        }
                    }
                    $this->removeAssetInCertificate($certificateId, $personalIds, $realEstateApartmentIds, $realEstateAppraiseIds);
                }
                $edited = Certificate::where('id', $certificateId)->first();
                // activity-log cập nhật thông tin chi tiết
                $this->CreateActivityLog($edited, $edited, 'update_data', 'cập nhật thông tin kết quả thẩm định');
            } else {
                $result = ['message' => ErrorMessage::CERTIFICATE_CHOOSE_APPRAISE, 'exception' => ''];
                return $result;
            }
            DB::commit();
            $result = $this->updateDocumentType($certificateId);
        } catch (exception $ex) {
            DB::rollBack();
            Log::error($ex);
            $result = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $ex->getMessage()];
        }
        return $result;
    }
    private function removeAssetInCertificate($certificateId, $personalKeep = [], $realEstateApartmentKeep = [], $realEstateAppraiseKeep = [])
    {
        $oldCertificate = Certificate::where('id', $certificateId)->first();
        $oldPersonals = $oldCertificate->personalProperties;
        $realEstates = $oldCertificate->realEstate;
        $oldRealEstateApartment = $realEstates->where('assetType.acronym', 'CC');
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
                    ->where('certificate_id',  $certificateId)
                    ->whereHas('personalProperties', function ($has) use ($personalId) {
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
                    ->where('certificate_id', '=', $certificateId)
                    ->whereHas('realEstates', function ($has) use ($realEstateId) {
                        $has->where('real_estate_id', $realEstateId);
                    })->forceDelete();
                CertificateRealEstate::query()->where('real_estate_id', '=', $realEstateId)->forceDelete();
                // $apartmentRepo->updateStatus($realEstateId, 2);
                $this->updateRealEstateCertificateId($realEstateId, null, false);
            }
        }
        if (count($realEstateAppraiseDelete) > 0) {
            // $appraiseRepo = new EloquentAppraiseRepository(new Appraise());
            foreach ($realEstateAppraiseDelete as $realEstateId) {
                CertificateHasRealEstate::query()
                    ->where('certificate_id', '=', $certificateId)
                    ->whereHas('realEstates', function ($has) use ($realEstateId) {
                        $has->where('real_estate_id', $realEstateId);
                    })->forceDelete();
                CertificateRealEstate::query()->where('real_estate_id', '=', $realEstateId)->forceDelete();
                // Remove construction infomation in certificate
                $certificateAsset = CertificateAsset::query()->where('real_estate_id', $realEstateId)->first();
                if (isset($certificateAsset))
                    $this->saveConstructionCompany($certificateId, $certificateAsset->id, $certificateAsset->appraise_id, 1);
                $this->updateRealEstateCertificateId($realEstateId);
            }
        }
        CommonService::getCertificateAssetPriceTotal_v2($certificateId);
        $this->updateTotalPrie($certificateId);
    }
    private function getCertificateAppraise(int $id)
    {
        $result = [];
        if (Certificate::where('id', $id)->exists()) {
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
            $result = Certificate::with($with)
                ->where('id', $id)
                ->select($select)
                ->first();
            $result->append(['status_text', 'general_asset']);
        }

        return $result;
    }

    public function updateCertificateGeneral(int $id, array $object)
    {
        $result = [];
        // # đang tắt khối block xác thực
        $check = $this->beforeSave($id);
        if (isset($check)) {
            return $check;
        }
        $check = $this->checkDuplicateData($object, $id);
        if (isset($check)) {
            return $check;
        }
        if (Certificate::where('id', $id)->exists()) {
            Certificate::where('id', $id)->update([
                // Data mới
                'survey_location' => $object['survey_location'],
                'survey_time' => isset($object['survey_time']) ? \Carbon\Carbon::createFromFormat('d-m-Y H:i', $object['survey_time'])->format('Y-m-d H:i') : null,
                'issue_date_card' =>  isset($object['issue_date_card']) ? \Carbon\Carbon::createFromFormat('d/m/Y', $object['issue_date_card'])->format('Y-m-d') : null,
                'issue_place_card' => $object['issue_place_card'],
                'name_contact' => $object['name_contact'],
                'phone_contact' => $object['phone_contact'],
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
                'document_alter_by_bank' => isset($object['document_alter_by_bank']) ? $object['document_alter_by_bank'] : 0,
                'note' => $object['note']
            ]);

            $edited = Certificate::where('id', $id)->first();

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
            $updateArray = [
                'appraiser_id' => $object['appraiser_id'],
                'appraiser_manager_id' => $object['appraiser_manager_id'],
                'appraiser_confirm_id' => $object['appraiser_confirm_id'],
                'appraiser_perform_id' => $object['appraiser_perform_id'],
                'appraiser_control_id' => $object['appraiser_control_id'],
            ];

            if (isset($object['administrative_id'])) {
                $updateArray['administrative_id'] = $object['administrative_id'];
            } else {
                $updateArray['administrative_id'] = null;
            }
            if (isset($object['business_manager_id'])) {
                $updateArray['business_manager_id'] = $object['business_manager_id'];
            }

            Certificate::where('id', $id)->update($updateArray);
        }
    }
    private function getAppraisalTeam(int $id)
    {
        $result = [];
        if (Certificate::where('id', $id)->exists()) {
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
                'status',
                'administrative_id',
                'business_manager_id',
            ];
            $with = [
                'appraiser:id,name,user_id',
                'appraiserPerform:id,name,user_id',
                'appraiserManager:id,name,user_id',
                'appraiserConfirm:id,name,user_id',
                'appraiserControl:id,name,user_id',
                'administrative:id,name,user_id',
                'appraiserBusinessManager:id,name,user_id',
            ];
            $result = Certificate::with($with)->where('id', $id)->select($select)->first();
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
                if (isset($appraiser)) {
                    $user = User::query()
                        ->where('id', '=', $appraiser->user_id)
                        ->first();
                    $result['image'] = $user->image;
                } else {
                    $result['image'] = '';
                }
            }
            if ($result['status'] == 2) {
                $appraiser = Appraiser::query()
                    ->where('id', '=', $result['appraiser_perform_id'])
                    ->first();
                if (isset($appraiser)) {
                    $user = User::query()
                        ->where('id', '=', $appraiser->user_id)
                        ->first();
                    $result['image'] = $user->image;
                } else {
                    $result['image'] = '';
                }
            }
            if ($result['status'] == 3 || $result['status'] == 4) {
                $appraiser = Appraiser::query()
                    ->where('id', '=', $result['appraiser_id'])
                    ->first();
                if (isset($appraiser)) {
                    $user = User::query()
                        ->where('id', '=', $appraiser->user_id)
                        ->first();
                    $result['image'] = $user->image;
                } else {
                    $result['image'] = '';
                }
            }
            if ($result['status'] == 6) {
                $appraiser = Appraiser::query()
                    ->where('id', '=', $result['appraiser_control_id'])
                    ->first();
                if (isset($appraiser)) {
                    $user = User::query()
                        ->where('id', '=', $appraiser->user_id)
                        ->first();
                    $result['image'] = $user->image;
                } else {
                    $result['image'] = '';
                }
            }
        }
        return $result;
    }

    private function getCertificateGeneral(int $id)
    {
        $result = [];
        if (Certificate::where('id', $id)->exists()) {
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
                'note',
                'phone_contact',
                'name_contact',
                'survey_location',
                'survey_time',
                'customer_group_id',
                'issue_date_card',
                'issue_place_card'
            ];
            $with = [
                'appraisePurpose:id,name',
                'customerGroup:id,description',
            ];
            $result = Certificate::with($with)->where('id', $id)->select($select)->first();
        }
        return $result;
    }

    private function beforeSave(int $id)
    {
        $result = null;

        if (Certificate::where('id', $id)->exists()) {
            $user = CommonService::getUser();
            if (!$user->hasRole(['ROOT_ADMIN', 'SUPER_ADMIN', 'SUB_ADMIN', 'ADMIN'])) {
                $data = Certificate::where('id', $id)->get()->first();
                switch ($data['status']) {
                    case 1:
                        if (!($data->created_by == $user->id) && !($data->appraiserSale->user_id == $user->id))
                            $result = ['message' => ErrorMessage::CERTIFICATE_CHECK_UPDATE . $data->status_text . '. Chỉ có người tạo phiếu và nhân viên Sale mới có quyền chỉnh sửa.', 'exception' => ''];
                        break;
                    case 2:
                        if (!($data->appraiserPerform->user_id == $user->id))
                            $result = ['message' => ErrorMessage::CERTIFICATE_CHECK_UPDATE . $data->status_text . '. Chỉ có chuyên viên thẩm định mới có quyền chỉnh sửa.', 'exception' => ''];
                        break;
                    case 7:
                        if (!($data->appraiserControl &&  $data->appraiserControl->user_id == $user->id))
                            $result = ['message' => ErrorMessage::CERTIFICATE_CHECK_UPDATE . $data->status_text . '. Chỉ có kiểm soát viên mới có quyền chỉnh sửa.', 'exception' => ''];
                        break;
                    case 10:
                        if (!($data->appraiserBusinessManager->user_id == $user->id))
                            $result = ['message' => ErrorMessage::CERTIFICATE_CHECK_UPDATE . $data->status_text . '. Chỉ có Quản lý nghiệp vụ mới có quyền chỉnh sửa.', 'exception' => ''];
                        break;
                    default:
                        $result = ['message' => ErrorMessage::CERTIFICATE_CHECK_UPDATE . $data->status_text, 'exception' => ''];
                        break;
                }
            }
        } else {
            $result = ['message' => ErrorMessage::CERTIFICATE_NOTEXISTS, 'exception' => ''];
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
                            'certificate_id' => $id,
                            'name' => $fileName,
                            'link' => $fileUrl,
                            'type' => $fileType,
                            'size' => $fileSize,
                            'description' => 'other',
                            'created_by' => $user->id,
                        ];

                        $item = new CertificateOtherDocuments($item);
                        QueryBuilder::for($item)->insert($item->attributesToArray());
                        $result[] = $item;
                    }
                    $edited = Certificate::where('id', $id)->first();
                    $edited2 = CertificateOtherDocuments::where('certificate_id', $id)->first();
                    # activity-log upload file
                    $this->CreateActivityLog($edited, $edited2, 'upload_file', 'tải phụ lục');
                    // chưa lấy ra được model user và id user
                }

                $result = CertificateOtherDocuments::where('certificate_id', $id)
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

        if (Certificate::where('id', $id)->exists()) {
            $required = request()->get('required');
            $user = CommonService::getUser();
            $data = Certificate::where('id', $id)->get()->first();
            $appraiser = [];
            if (!empty($required)) {
                $ischeckPrice = $required['check_price'];
                $isCheckLegal =  $required['check_legal'];
                $isCheckVersion =  $required['check_version'];
                $isCheckAppraiser =  $required['appraiser'];
                $isCheckItemList =  $required['appraise_item_list'];

                if ($isCheckVersion) {
                    if (!empty($data->realEstate)) {
                        $check = AppraiseVersionService::checkVersionByCertificate($id);
                        if (!empty($check)) {
                            return ['message' => 'Tài sản thẩm định đã được chỉnh sửa. Vui lòng cập nhật version.', 'exception' => ''];
                        }
                    }
                }
                if ($ischeckPrice) {
                    if (!empty($data->realEstate)) {
                        foreach ($data->realEstate as $item) {
                            $check = CommonService::checkValidAppraise($item->real_estate_id);
                            if (isset($check))
                                return $check;
                        }
                    }
                }
                if ($isCheckLegal) {
                    if (!empty($data->realEstate)) {
                        foreach ($data->realEstate as $item) {
                            $check = CommonService::checkExistsAppraiseLaw($item->real_estate_id);
                            if (isset($check))
                                return $check;
                        }
                    }
                }
                if ($isCheckAppraiser) {
                    $appraiser['appraiser_id'] =  request()->get('appraiser_id');
                    $appraiser['appraiser_manager_id'] =  request()->get('appraiser_manager_id');
                    $appraiser['appraiser_confirm_id'] =  request()->get('appraiser_confirm_id');
                    $appraiser['appraiser_perform_id'] =  request()->get('appraiser_perform_id');
                    $appraiser['appraiser_control_id'] =  request()->get('appraiser_control_id');
                    if (request()->get('administrative_id')) {
                        $appraiser['administrative_id'] = request()->get('administrative_id');
                    }
                    if (request()->get('business_manager_id')) {
                        $appraiser['business_manager_id'] = request()->get('business_manager_id');
                    }
                    if (empty($appraiser['appraiser_id']) || empty($appraiser['appraiser_manager_id']) || empty($appraiser['appraiser_perform_id'])) {
                        return ['message' => ErrorMessage::CERTIFICATE_APPRAISERTEAM, 'exception' => ''];
                    }
                }
                if ($isCheckItemList) {
                    $data->append('general_asset');
                    if (empty($data->general_asset)) {
                        return ['message' => 'Chưa có thông tin tài sản thẩm định.', 'exception' => ''];
                    }
                }
            }
            //Check role and permision
            if (!$user->hasRole(['ROOT_ADMIN', 'SUPER_ADMIN', 'SUB_ADMIN', 'ADMIN'])) {
                switch ($data['status']) {
                    case 1:
                        if (!($data->appraiserSale && $data->appraiserSale->user_id == $user->id))
                            $result = ['message' => ErrorMessage::CERTIFICATE_CHECK_STATUS_FOR_UPDATE . $data->status_text . '. Chỉ có nhân viên kinh doanh mới có quyền này.', 'exception' => ''];
                        break;
                    case 2:
                        if (!($data->appraiserPerform && $data->appraiserPerform->user_id == $user->id))
                            $result = ['message' => ErrorMessage::CERTIFICATE_CHECK_STATUS_FOR_UPDATE . $data->status_text . '. Chỉ có chuyên viên thẩm định mới có quyền này.', 'exception' => ''];
                        break;
                    case 3:
                    case 4:
                        if (!($data->appraiser && $data->appraiser->user_id == $user->id))
                            $result = ['message' => ErrorMessage::CERTIFICATE_CHECK_STATUS_FOR_UPDATE . $data->status_text . '. Chỉ có thẩm định viên mới có quyền này.', 'exception' => ''];
                        break;
                    case 6:
                    case 7:
                        if (!($data->appraiserControl && $data->appraiserControl->user_id == $user->id))
                            $result = ['message' => ErrorMessage::CERTIFICATE_CHECK_STATUS_FOR_UPDATE . $data->status_text . '. Chỉ có kiểm soát viên mới có quyền này.', 'exception' => ''];
                        break;
                    case 8:
                        if (!($data->administrative && $data->administrative->user_id == $user->id))
                            $result = ['message' => ErrorMessage::CERTIFICATE_CHECK_STATUS_FOR_UPDATE . $data->status_text . '. Chỉ có hành chính viên mới có quyền này.', 'exception' => ''];
                        break;
                    case 9:
                        if (!($data->appraiserSale && $data->appraiserSale->user_id == $user->id))
                            $result = ['message' => ErrorMessage::CERTIFICATE_CHECK_STATUS_FOR_UPDATE . $data->status_text . '. Chỉ có nhân viên kinh doanh mới có quyền này.', 'exception' => ''];
                        break;
                    case 10:
                        if (!($data->appraiserBusinessManager->user_id == $user->id))
                            $result = ['message' => ErrorMessage::CERTIFICATE_CHECK_STATUS_FOR_UPDATE . $data->status_text . '. Chỉ có quản lý nghiệp vụ mới có quyền này.', 'exception' => ''];
                        break;
                    default:
                        $result = ['message' => ErrorMessage::CERTIFICATE_CHECK_STATUS_FOR_UPDATE . $data->status_text, 'exception' => ''];
                        break;
                }
            }
            if (!empty($appraiser)) {
                $this->updateAppraisalTeam($id, $appraiser);
            }
        } else {
            $result = ['message' => ErrorMessage::CERTIFICATE_NOTEXISTS, 'exception' => ''];
        }
        return $result;
    }

    private function beforeUpdateStatusRedistribute(int $id)
    {
        $result = null;

        if (Certificate::where('id', $id)->exists()) {
            $user = CommonService::getUser();
            $data = Certificate::where('id', $id)->get()->first();
            $appraiser = [];
            $appraiser['appraiser_id'] =  request()->get('appraiser_id');
            $appraiser['appraiser_manager_id'] =  request()->get('appraiser_manager_id');
            $appraiser['appraiser_confirm_id'] =  request()->get('appraiser_confirm_id');
            $appraiser['appraiser_perform_id'] =  request()->get('appraiser_perform_id');
            $appraiser['appraiser_control_id'] =  request()->get('appraiser_control_id');
            if (request()->get('administrative_id')) {
                $appraiser['administrative_id'] = request()->get('administrative_id');
            }
            if (request()->get('business_manager_id')) {
                $appraiser['business_manager_id'] = request()->get('business_manager_id');
            }
            if (empty($appraiser['appraiser_id']) || empty($appraiser['appraiser_manager_id']) || empty($appraiser['appraiser_perform_id'])) {
                return ['message' => ErrorMessage::CERTIFICATE_APPRAISERTEAM, 'exception' => ''];
            }
            //Check role and permision
            if (!$user->hasRole(['ROOT_ADMIN', 'SUPER_ADMIN', 'SUB_ADMIN', 'ADMIN'])) {
                if (!($data->appraiserBusinessManager->user_id == $user->id)) {
                    $result = ['message' => 'Chỉ có quản lý nghiệp vụ mới có quyền phân lại hồ sơ này.', 'exception' => ''];
                }
            }
            if (!empty($appraiser)) {
                $this->updateAppraisalTeam($id, $appraiser);
            }
        } else {
            $result = ['message' => ErrorMessage::CERTIFICATE_NOTEXISTS, 'exception' => ''];
        }
        return $result;
    }

    public function updateAppraisersTeam(int $id, $request)
    {
        $result = [];
        if (Certificate::where('id', $id)->exists()) {
            $user = CommonService::getUser();
            $data = Certificate::where('id', $id)->get()->first();
            if (
                $user->hasRole(['ROOT_ADMIN', 'SUPER_ADMIN', 'SUB_ADMIN', 'ADMIN'])
                || ((isset($data->appraiser) && $data->appraiser->user_id == $user->id)
                    || (isset($data->appraiserManager) && $data->appraiserManager->user_id == $user->id)
                    || (isset($data->appraiserControl) && $data->appraiserControl->user_id == $user->id)
                    || (isset($data->appraiserConfirm) && $data->appraiserConfirm->user_id == $user->id)
                    || (isset($data->appraiserSale) && $data->appraiserSale->user_id == $user->id)
                    || (isset($data->appraiserPerform) && $data->appraiserPerform->user_id == $user->id)
                    || (isset($data->administrative) && $data->administrative->user_id == $user->id)
                    || (isset($data->createdBy) && $data->createdBy->id == $user->id))
                || (isset($data->appraiserBusinessManager) && $data->appraiserBusinessManager->user_id == $user->id)
            ) {
                $this->updateAppraisalTeam($id, $request);
                $edited = Certificate::where('id', $id)->first();

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
        } else {
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
                    inner join certificate_has_appraises t2 on t1.id = t2.certificate_id
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
        } else {
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
                                inner join certificate_has_real_estates t2 on t1.id = t2.certificate_id
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
            $pic = array_merge($pic, $anh);
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
        if (Certificate::where('id', $id)->exists()) {
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
            $certificate = Certificate::with($with)->where('id', $id)->get($select)->first();
            $eloquenUser = new EloquentUserRepository(new User());

            if (isset($certificate->appraiserSale) && isset($certificate->appraiserSale->user_id))
                if ($certificate->appraiserSale->user_id != $loginUser->id) {
                    $users[] =  $eloquenUser->getUser($certificate->appraiserSale->user_id);
                }
            if (isset($certificate->appraiserPerform) && isset($certificate->appraiserPerform->user_id))
                if ($certificate->appraiserPerform->user_id != $loginUser->id) {
                    $users[] =  $eloquenUser->getUser($certificate->appraiserPerform->user_id);
                }
            if (isset($certificate->appraiser) && isset($certificate->appraiser->user_id))
                if ($certificate->appraiser->user_id != $loginUser->id) {
                    $users[] =  $eloquenUser->getUser($certificate->appraiser->user_id);
                }
            if (isset($certificate->appraiserControl) && isset($certificate->appraiserControl->user_id))
                if ($certificate->appraiserControl->user_id != $loginUser->id) {
                    $users[] =  $eloquenUser->getUser($certificate->appraiserControl->user_id);
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
                case 7:
                    $statusText = 'Duyệt phát hành';
                    break;
                case 8:
                    $statusText = 'In hồ sơ';
                    break;
                case 9:
                    $statusText = 'Bàn giao khách hàng';
                    break;
                case 10:
                    $statusText = 'Phân hồ sơ';
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
            // $this->sendEmail($users, $data);
        }
    }
    private function sendEmail($users, $data)
    {
        $usersString = json_encode($users);
        $dataString = json_encode($data);
        $emailReceive = 'clonebds1@gmail.com';
        $subject = '[HSTD - 218] Chuyển sang trạng thái thẩm địnhsdsdv';
        $markdown = 'emails.notifications.update';
        $name = 'Lê Phi Longx';
        $message = $usersString . $dataString;
        $link = '#';

        Mail::send(new JustTesting($emailReceive, $subject, $markdown, $name, $message, $link));
    }
    private function checkDuplicateData(array $object, int $certificateId = null)
    {
        $result = null;
        // Certificate brief id must be checked with year.
        // It might raise bug if created success with same number but after that update same year.
        // Temp return fale.
        return $result;
        $paramList = CompareMaterData::CERTIFICATE_BRIEF_CHECK_DUPLICATE;
        if ($paramList != null && count($paramList) > 0) {
            $paramKeys = array_keys($paramList);
            foreach ($paramKeys as $key) {
                if (isset($object[$key])) {
                    if (Certificate::where($key, $object[$key])->where('id', '<>', $certificateId ?? -1)->exists()) {
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
                    then 'Thẩm định'
                when 3
                    then 'Duyệt giá'
                when 4
                    then 'Hoàn thành'
                when 5
                    then 'Huỷ'
                when 7
                    then 'Duyệt phát hành'
                when 8
                    then 'In hồ sơ'
                when 9
                    then 'Bàn giao khách hàng'
                when 10
                    then 'Phân hồ sơ'
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
        $result = Certificate::with($with)
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

        if (isset($status)) {
            $status = explode(',', $status);
            $query = $query->whereIn('status', $status);
            $query1 = $query1->whereIn('status', $status);
        }
        if (isset($fromDate) && isset($toDate)) {
            $fromDate =  \Carbon\Carbon::createFromFormat('d/m/Y', $fromDate)->format('Y-m-d');
            $toDate =  \Carbon\Carbon::createFromFormat('d/m/Y', $toDate)->format('Y-m-d');
            $query = $query->whereRaw("to_char(created_at , 'YYYY-MM-dd') between '" . $fromDate . "' and '" . $toDate . "'");
            $query1 = $query1->whereRaw("to_char(created_at , 'YYYY-MM-dd') between '" . $fromDate . "' and '" . $toDate . "'");
        } elseif (isset($fromDate)) {
            $fromDate =  \Carbon\Carbon::createFromFormat('d/m/Y', $fromDate);
            $query = $query->whereRaw("to_char(created_at , 'YYYY-MM-dd') >= '" . $fromDate->format('Y-m-d') . "'");
            $query1 = $query1->whereRaw("to_char(created_at , 'YYYY-MM-dd') >= '" . $fromDate->format('Y-m-d') . "'");
        } elseif (isset($toDate)) {
            $toDate =  \Carbon\Carbon::createFromFormat('d/m/Y', $toDate);
            $query = $query->whereRaw("to_char(created_at , 'YYYY-MM-dd') <= '" . $toDate->format('Y-m-d') . "'");
            $query1 = $query1->whereRaw("to_char(created_at , 'YYYY-MM-dd') <= '" . $toDate->format('Y-m-d') . "'");
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
        // return $result->merge($result1)->sortBy('certificate_id');
        Log::info($result->merge($result1)->sortByDesc('created_at'));

        return $result->merge($result1)->sortByDesc('created_at');
    }

    private function updatePersonaltyPrice(int $id)
    {
        CertificatePrice::where('certificate_id', $id)->forceDelete();
        $totalPrice = Certificate::query()->withCount(['personalProperties as total_price'  => function ($query) {
            $query->select(DB::raw('SUM(total_price)::bigint as total'));
        }])->where('id', $id)->first(['id', 'total_price']);
        if (isset($totalPrice->total_price)) {
            CertificatePrice::query()->create(['certificate_id' => $id, 'slug' => 'total_asset_price', 'value' => $totalPrice->total_price]);
        }
    }

    private function updateTotalPrie(int $id)
    {
        CertificatePrice::where('certificate_id', $id)->forceDelete();
        $certificate = Certificate::query()->where('id', $id)->first();
        $priceArr = $certificate->general_asset;
        $price = array_sum(array_column($priceArr, 'total_price'));
        if (isset($price)) {
            CertificatePrice::query()->create(['certificate_id' => $id, 'slug' => 'total_asset_price', 'value' => $price]);
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

    private function updateDocumentType($id)
    {
        $certificate = $this->getCertificateAppraise($id);
        $assetGenerals = $certificate['general_asset'];
        $documentType = [];
        if (!empty($assetGenerals)) {
            $assetTypeIds = Arr::pluck($assetGenerals, 'asset_type_id');
            $assetType = Dictionary::query()->whereIn('id', $assetTypeIds)->get();
            $documentType = Arr::pluck($assetType, 'acronym');
        }
        $certificate['document_type'] = $documentType;
        $certificate->update(['document_type' => $documentType]);
        return $certificate;
    }

    private function updateRealEstateCertificateId($realEstateId, $id = null, $isAppraise = true, $status = 2, $sub_status = 1)
    {
        $dataUpdate = [
            'certificate_id' => $id,
            'status' => $status,
            'sub_status' => $sub_status
        ];
        RealEstate::query()->where('id', $realEstateId)->update($dataUpdate);
        if ($isAppraise)
            Appraise::query()->where('id', $realEstateId)->update($dataUpdate);
        else
            ApartmentAsset::query()->where('id', $realEstateId)->update($dataUpdate);
    }
    private function updatePersonalPropertyCertificateId($personalId, $id = null, $status = 2, $sub_status = 1)
    {
        $dataUpdate = [
            'certificate_id' => $id,
            'status' => $status,
            'sub_status' => $sub_status
        ];
        PersonalProperty::query()->where('id', $personalId)->update($dataUpdate);
    }
    public function getCertificateStatus($id)
    {
        if (Certificate::query()->where('id', $id)->exists()) {
            return Certificate::query()->where('id', $id)->first(['id', 'status', 'sub_status']);
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
            'assetPrice:id,certificate_id,slug,value',
            'createdBy:id,name',
            'appraiserManager:id,name,appraiser_number,appraise_position_id',
            'appraiserManager.appraisePosition:id,description',
            'legalDocumentsOnValuation:id,document_type,date,content,provinces,position',
            'legalDocumentsOnConstruction:id,document_type,date,content,provinces,position',
            'legalDocumentsOnLand:id,document_type,date,content,provinces,position',
            'legalDocumentsOnLocal:id,document_type,date,content,provinces,position'
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
        return DB::transaction(function () use ($id, $description, $request) {
            try {
                $result = [];
                // $check = $this->checkAuthorizationCertificate($id);
                // if (!empty($check))
                //     return $check;
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
                            'certificate_id' => $id,
                            'name' => $fileName,
                            'link' => $fileUrl,
                            'type' => $fileType,
                            'size' => $fileSize,
                            'description' => $description,
                            'created_by' => $user->id,
                        ];
                        $item = new CertificateOtherDocuments($item);
                        CertificateOtherDocuments::query()->updateOrCreate(['certificate_id' => $id, 'description' => $description], $item->attributesToArray());
                    }
                    $edited = Certificate::where('id', $id)->first();
                    $edited2 = CertificateOtherDocuments::where('certificate_id', $id)->first();
                    # activity-log upload file
                    $this->CreateActivityLog($edited, $edited2, 'upload_file', 'upload ' . $logDescription);
                }

                $result = CertificateOtherDocuments::where('certificate_id', $id)
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
            $other = CertificateOtherDocuments::query()->where('id', $id)->first();
            $certificateId = $other->certificate_id;
            // $check = $this->checkAuthorizationCertificate($certificateId);
            // if (!empty($check))
            //     return $check;
            $description = $other->description;
            $logDescription = $this->getOtherDescription($description);
            $path = env('STORAGE_OTHERS') . '/' . 'comparison_brief/upload/' . $id . '/';
            $url = $other->link;
            $arrUrl = explode('/', $url);
            $fileName = array_reverse($arrUrl)[0];
            $name = $path . $fileName;
            Storage::disk(env('FILESYSTEM_DRIVER'))->delete($name);
            $other->delete();
            $certificate = $this->model->query()->where('id', $certificateId)->with('otherDocuments')->first('id');
            $this->CreateActivityLog($certificate, $certificate, 'upload_document', 'xóa file ' . $logDescription);
            return $certificate->otherDocuments;
        } catch (Exception $ex) {
            Log::error($ex);
            return ['message' => $ex->getMessage(), 'exception' => $ex];
        }
    }
    private function getOtherDescription($description)
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
    private function removeUploadFile($id, $description, $path)
    {
        try {
            $others = CertificateOtherDocuments::query()->where(['certificate_id' => $id, 'description' => $description])->get();
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

    private function checkAuthorizationCertificate($id)
    {
        $check = null;
        if ($this->model->query()->where('id', $id)->exists()) {
            $user = CommonService::getUser();
            $role = $user->roles->last();
            $result = $this->model->query()->where('id', $id);
            $userId = $user->id;
            if ($role->name !== 'ROOT_ADMIN'  && $role->name !== 'ADMIN' && $role->name !== 'Accounting') {
                $result = $result->where(function ($query) use ($userId) {
                    $query = $query->whereHas('createdBy', function ($q) use ($userId) {
                        return $q->where('id', $userId);
                    });
                    $query = $query->orwhereHas('appraiser', function ($q) use ($userId) {
                        return $q->where('user_id', $userId);
                    });
                    $query = $query->orwhereHas('appraiserManager', function ($q) use ($userId) {
                        return $q->where('user_id', $userId);
                    });
                    $query = $query->orwhereHas('appraiserBusinessManager', function ($q) use ($userId) {
                        return $q->where('user_id', $userId);
                    });
                    $query = $query->orwhereHas('appraiserConfirm', function ($q) use ($userId) {
                        return $q->where('user_id', $userId);
                    });
                    $query = $query->orwhereHas('appraiserSale', function ($q) use ($userId) {
                        return $q->where('user_id', $userId);
                    });
                    $query = $query->orwhereHas('appraiserPerform', function ($q) use ($userId) {
                        return $q->where('user_id', $userId);
                    });

                    $query = $query->orwhereHas('appraiserControl', function ($q) use ($userId) {
                        return $q->where('user_id', $userId);
                    });
                    $query = $query->orwhereHas('administrative', function ($q) use ($userId) {
                        return $q->where('user_id', $userId);
                    });
                });
            }
            $result = $result->first();
            if (empty($result))
                $check = ['message' => 'Bạn không có quyền ở HSTĐ ' . $id, 'exception' => '', 'statusCode' => 403];
        } else {
            $check = ['message' => ErrorMessage::CERTIFICATE_NOTEXISTS . ' ' . $id, 'exception' => '', 'statusCode' => 403];
        }
        return $check;
    }
}
