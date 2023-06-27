<?php

namespace App\Http\Controllers\Intergration;

use App\Contracts\AppraiserCompanyRepository;
use App\Contracts\CompareAssetGeneralRepository;
use App\Contracts\RealEstateRepository;
use App\Enum\ErrorMessage;
use App\Http\Controllers\Controller;
use App\Models\ApartmentAsset;
use App\Models\Appraise;
use App\Models\DocumentDictionary;
use App\Models\RealEstate;
use App\Notifications\ActivityLog;
use App\Services\CommonService;
use App\Services\Document\AssetReport;
use App\Services\Excel\ExportCertificateAssets;
use App\Services\Excel\ExportShinhanCertificateAsset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Validator;

class RealEstateController extends Controller
{
    use ActivityLog;
    private RealEstateRepository $realEstateRep;
    private AppraiserCompanyRepository $appraiserCompanyRepository;
    private CompareAssetGeneralRepository $compareAssetGeneralRepository;

    private string $envDocument = '';

    private array $permissionView =['VIEW_CERTIFICATE_ASSET'];
    private array $permissionAdd =['ADD_CERTIFICATE_ASSET'];
    private array $permissionEdit =['EDIT_CERTIFICATE_ASSET'];
    private array $permissionExport =['EXPORT_CERTIFICATE_ASSET'];
    /**
     * RealEstateController constructor.
     */
    public function __construct(RealEstateRepository $realEstateRep,
                                AppraiserCompanyRepository    $appraiserCompanyRepository,
                                CompareAssetGeneralRepository $compareAssetGeneralRepository)
    {
        $this->realEstateRep = $realEstateRep;
        $this->appraiserCompanyRepository = $appraiserCompanyRepository;
        $this->compareAssetGeneralRepository = $compareAssetGeneralRepository;
        $this->envDocument = config('services.document_service.document_module');
    }

    public function index(){
        try {
            $result = $this->realEstateRep->findPaging();
            return $this->respondWithCustomData($result);
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => $exception->getMessage(), 'exception' => $exception];
            return $this->respondWithErrorData($data);
        }
    }
    public function printPL1($id): JsonResponse
    {
        try {
            $realEstates =  $this->realEstateRep->getReportData($id);
            if(is_countable($realEstates) && isset($realEstates['message']) && isset($realEstates['exception']))
                return $this->respondWithErrorData($realEstates);
            $service = 'App\\Services\\Document\\Appendix1\\ReportAppendix1'. $this->envDocument;
            $format = '.docx';
            $company = $this->appraiserCompanyRepository->getOneAppraiserCompany();
            $report = new $service;
            $documentConfig = DocumentDictionary::query()->get();
            $result = $report->generateDocx($company, $realEstates, $format, $documentConfig);
            $data = null;
            if (!empty($realEstates->realEstate->appraises))
                $data = $realEstates->realEstate->appraises;
            else
                $data = $realEstates->realEstate->apartment;
            if (!empty($data))
                $this->CreateActivityLog($data, $data, 'download', 'tải xuống Bảng điều chỉnh QSDĐ');
            return $this->respondWithCustomData($result);
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => $exception->getMessage(), 'exception' => $exception];
            return $this->respondWithErrorData($data);
        }
    }
    public function printPL2($id): JsonResponse
    {
        try {
            $realEstates =  $this->realEstateRep->getReportData($id);
            if(is_countable($realEstates) && isset($realEstates['message']) && isset($realEstates['exception']))
                return $this->respondWithErrorData($realEstates);
            $service = 'App\\Services\\Document\\Appendix2\\ReportAppendix2'. $this->envDocument;
            $format = '.docx';
            $company = $this->appraiserCompanyRepository->getOneAppraiserCompany();
            $report = new $service;
            $documentConfig = DocumentDictionary::query()->get();
            $result = $report->generateDocx($company, $realEstates, $format, $documentConfig);
            $data = null;
            if (!empty($realEstates->realEstate->appraises))
                $data = $realEstates->realEstate->appraises;
            else
                $data = $realEstates->realEstate->apartment;
            if (!empty($data))
                $this->CreateActivityLog($data, $data, 'download', 'tải xuống Bảng điều chỉnh CTXD');

            return $this->respondWithCustomData($result);
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => $exception->getMessage(), 'exception' => $exception];
            return $this->respondWithErrorData($data);
        }
    }
    public function printPL3($id): JsonResponse
    {
        try {
            $realEstates =  $this->realEstateRep->getReportData($id);
            if(is_countable($realEstates) && isset($realEstates['message']) && isset($realEstates['exception']))
                return $this->respondWithErrorData($realEstates);
            $service = 'App\\Services\\Document\\Appendix3\\ReportAppendix3'. $this->envDocument;
            $format = '.docx';
            $company = $this->appraiserCompanyRepository->getOneAppraiserCompany();
            $report = new $service;
            $documentConfig = DocumentDictionary::query()->get();
            // $result = 'test';
            $result = $report->generateDocx($company, $realEstates, $format, $documentConfig);
            $data = null;
            if (!empty($realEstates->realEstate->appraises))
                $data = $realEstates->realEstate->appraises;
            else
                $data = $realEstates->realEstate->apartment;
            if (!empty($data))
                $this->CreateActivityLog($data, $data, 'download', 'tải xuống Hình ảnh hiện trạng');
            return $this->respondWithCustomData($result);
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => $exception->getMessage(), 'exception' => $exception];
            return $this->respondWithErrorData($data);
        }
    }
    public function printTSS($id): JsonResponse
    {
        try {
            $format = 'docx';
            if (request()->get('format') == 'pdf') {
                $format = 'pdf';
            }
            $company = $this->appraiserCompanyRepository->getCompany();
            $result = $this->respondWithCustomData((new AssetReport())->generateDocx($company, ($this->compareAssetGeneralRepository->findByIds($id)), $format));
            $realEstateId = request()->get('real_estate_id');
            $data = null;
            if (!empty($realEstateId)){
                $realEstate = RealEstate::query()->with(['appraises', 'apartment'])->where('id', $realEstateId)->first();
                if (!empty($realEstate))
                    $data = $realEstate->appraises ? $realEstate->appraises : $realEstate->apartment;
            }
            if (!empty($data))
                $this->CreateActivityLog($data, $data, 'download', 'tải xuống phiếu thu thập thông tin tài sản');
            return $result;
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => $exception->getMessage(), 'exception' => $exception];
            return $this->respondWithErrorData($data);
        }
    }
    public function exportShinhanFormat($id): JsonResponse
    {
        try {
            $realEstates =  $this->realEstateRep->getDataForShinhan($id);
            if(is_countable($realEstates) && isset($realEstates['message']) && isset($realEstates['exception']))
                return $this->respondWithErrorData($realEstates);
            $result = $this->respondWithCustomData((new ExportShinhanCertificateAsset())->exportAsset($realEstates->realEstate));
            // if (!empty($data))
            //     $this->CreateActivityLog($data, $data, 'download', 'tải xuống dữ liệu import cho Shinhan');
            return $result;
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => $exception->getMessage(), 'exception' => $exception];
            return $this->respondWithErrorData($data);
        }
    }
    public function updateAditionalData (Request $request, $id)
    {
        if(! CommonService::checkUserPermission($this->permissionEdit))
            return $this->respondWithErrorData( ['message' => ErrorMessage::APPRAISE_CHECK_UPDATE ,'exception' =>''], 403);
        $rules = [
            // 'petitioner_name' => 'string|max:255',
        ];

        $customAttributes = [
            // 'petitioner_name' => 'Tên khách hàng yêu cầu',
        ];
        $validator = Validator::make($request->toArray(), $rules, $this->messages, $customAttributes);
        if ($validator->passes()) {
            //TODO Handle your data
            $result = $this->realEstateRep->updateRealEstateAditionalData($request->toArray(), $id);
            if(isset($result['message']) && isset($result['exception']))
                return $this->respondWithErrorData( $result);

            return $this->respondWithCustomData($result);
        } else {
            //TODO Handle your error
            $data = ['message' => $validator->errors()->all(), 'exception' => null];
            return $this->respondWithErrorData( $data);
        }
    }
    public function updateStatus(Request $request, $id)
    {
        try {
            $result = $this->realEstateRep->updateStatus($request->toArray(), $id);
            if(is_string($result)) {
                $data = ['message' => $result];
                return $this->respondWithErrorData($data);
            } else {
                return $this->respondWithCustomData($result);
            }
        } catch (\Exception $exception) {
            dd($exception);
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }
    public function exportCertificateAssets(Request $request)
    {
        if(! CommonService::checkUserPermission($this->permissionExport)){
            return $this->respondWithErrorData( ['message' => ErrorMessage::APPRAISE_CHECK_EXPORT ,'exception' =>''], 403);
        }
        $result = $this->realEstateRep->exportCertificateAssets();
        if(isset($result['message']) && isset($result['exception']))
            return $this->respondWithErrorData( $result);
        return $this->respondWithCustomData((new ExportCertificateAssets())->exportAsset($result));
    }
}
