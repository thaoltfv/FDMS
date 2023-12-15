<?php

namespace App\Http\Controllers\AppraiseDictionary;

use App\Contracts\AppraiseAssetRepository;
use App\Contracts\AppraiserCompanyRepository;
use App\Contracts\AppraiseRepository;
use App\Contracts\BuildingPriceRepository;
use App\Contracts\PreCertificateRepository;
use App\Contracts\CompareAssetGeneralRepository;
use App\Contracts\DictionaryRepository;
use App\Contracts\UserRepository;
use App\Enum\ErrorMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Appraise\CreateAppraiseRequest;
use App\Http\Requests\Appraise\UpdateAppraiseRequest;
use App\Services\AppraiseAsset\AppraiseAsset;
use App\Services\Document\PhuLuc1;
use App\Services\Document\AssetReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use Storage;
use App\Services\CommonService;

use Validator;
class PreCertificateController extends Controller
{

    private PreCertificateRepository $preCertificateRepository;
    public CompareAssetGeneralRepository $compareAssetGeneralRepository;
    public UserRepository $userRepository;
    public DictionaryRepository $dictionaryRepository;
    public BuildingPriceRepository $buildingPriceRepository;
    private AppraiseAssetRepository $appraiseAssetRepository;
    private AppraiserCompanyRepository $appraiserCompanyRepository;

    /**
     * ProvinceController constructor.
     */
    public function __construct(PreCertificateRepository         $preCertificateRepository,
                                CompareAssetGeneralRepository $compareAssetGeneralRepository,
                                UserRepository                $userRepository,
                                DictionaryRepository          $dictionaryRepository,
                                BuildingPriceRepository       $buildingPriceRepository,
                                AppraiseAssetRepository       $appraiseAssetRepository,
                                AppraiserCompanyRepository    $appraiserCompanyRepository)
    {
        $this->preCertificateRepository = $preCertificateRepository;
        $this->compareAssetGeneralRepository = $compareAssetGeneralRepository;
        $this->userRepository = $userRepository;
        $this->dictionaryRepository = $dictionaryRepository;
        $this->buildingPriceRepository = $buildingPriceRepository;
        $this->appraiseAssetRepository = $appraiseAssetRepository;
        $this->appraiserCompanyRepository = $appraiserCompanyRepository;
    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->preCertificateRepository->findPaging());
        } catch (\Exception $exception) {
            dd($exception);
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /**
     * @return JsonResponse
     */
    public function findAll(): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->preCertificateRepository->findAll());
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    // public function updateStatus($id, Request $request): JsonResponse
    // {
    //     try {
    //         return $this->respondWithCustomData($this->preCertificateRepository->updateStatus($id, $request));
    //     } catch (\Exception $exception) {
    //         dd($exception);
    //         Log::error($exception);
    //         $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
    //         return $this->respondWithErrorData($data);
    //     }
    // }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function otherDocumentUpload($id, $typeDocument, Request $request): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->preCertificateRepository->otherDocumentUpload($id,$typeDocument, $request));
        } catch (\Exception $exception) {
            dd($exception);
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function otherDocumentRemove($id, Request $request): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->preCertificateRepository->otherDocumentRemove($id, $request));
        } catch (\Exception $exception) {
            dd($exception);
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function otherDocumentDownload($id, Request $request)
    {
        try {
            $item = $this->preCertificateRepository->otherDocumentDownload($id, $request);
            if(isset($item->link)) {
                return response()->streamDownload(function () use ($item) {
                    echo file_get_contents($item->link);
                }, $item->name);
            }
        } catch (\Exception $exception) {
            dd($exception);
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            $test = request()->get('test');
            if(isset($test)) {
                $result = $this->preCertificateRepository->findByIdTest($id);
            } else {
                $result = $this->preCertificateRepository->findById($id);
            }

            // CommonService::getPreCertificateAssetPriceTotal($result);
            return $this->respondWithCustomData($result);
        } catch (\Exception $exception) {
            dd($exception);
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $result = $this->preCertificateRepository->createPreCertificate($request->toArray());
            if(is_numeric($result)) {
                return $this->respondWithCustomData($result);
            } else {
                $data = ['message' => $result, 'exception' => []];
                return $this->respondWithErrorData($data);
            }
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /**
     * @param $id
     * @param Request $request
     * @return JsonResponse
     */
    public function update($id, Request $request): JsonResponse
    {
        try {
            $result = $this->preCertificateRepository->update($id, $request->toArray());
            if(is_numeric($result)) {
                return $this->respondWithCustomData($result);
            } else {
                $data = ['message' => $result, 'exception' => []];
                return $this->respondWithErrorData($data);
            }
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    public function updateTangibleComparisonFactor($id, Request $request): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->preCertificateRepository->updateTangibleComparisonFactor($id, $request->toArray()));
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    // /**
    //  * @param $id
    //  * @return JsonResponse
    //  */
    // public function destroy($id): JsonResponse
    // {
    //     try {
    //         return $this->respondWithCustomData($this->preCertificateRepository->deletePreCertificate($id));
    //     } catch (\Exception $exception) {
    //         Log::error($exception);
    //         $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
    //         return $this->respondWithErrorData($data);
    //     }
    // }

    public function appraiseAsset(Request $request): JsonResponse
    {
        try {
            return $this->respondWithCustomData((new AppraiseAsset(
                $this->appraiseAssetRepository,
                $this->dictionaryRepository
            ))
                ->AppraiseAsset($request->toArray()));

        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    // public function print($id): JsonResponse
    // {
    //     try {
    //         $format = 'docx';
    //         $company = $this->appraiserCompanyRepository->getOneAppraiserCompany();
    //         $appraise = $this->preCertificateRepository->findById($id);
    //         $assets = [];
    //         if ($appraise->assetGeneral) {
    //             $ids = [];
    //             foreach ($appraise->assetGeneral as $assetGeneral) {
    //                 $ids[] = $assetGeneral->id;
    //             }
    //             $assets = $this->compareAssetGeneralRepository->findByIds(json_encode($ids));
    //         }
    //         return $this->respondWithCustomData((new PhuLuc1())->generateDocx($appraise, $company, $assets, $format));
    //     } catch (\Exception $exception) {
    //         Log::error($exception);
    //         $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
    //         return $this->respondWithErrorData($data);
    //     }
    // }

    // /**
    //  * @param $id
    //  * @return JsonResponse
    //  */
    public function saleDocumentUpload($id, Request $request): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->preCertificateRepository->saleDocumentUpload($id, $request));
        } catch (\Exception $exception) {
            dd($exception);
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    
    private array $permissionView =['VIEW_CERTIFICATE_BRIEF'];
    private array $permissionAdd =['ADD_CERTIFICATE_BRIEF'];
    private array $permissionEdit =['EDIT_CERTIFICATE_BRIEF'];
    private array $permissionExport =['EXPORT_CERTIFICATE_BRIEF'];

    public function getPreCertificate(int $id){
        if(! CommonService::checkUserPermission($this->permissionView))
            return $this->respondWithErrorData( ['message' => ErrorMessage::CERTIFICATE_CHECK_VIEW ,'exception' =>''], 403);

        $result =  $this->preCertificateRepository->getPreCertificate($id);
        if(isset($result['message']) && isset($result['exception']))
            return $this->respondWithErrorData( $result);
        return $this->respondWithCustomData($result);
    }

    public function findPaging(Request $request)
    {
        if(! CommonService::checkUserPermission($this->permissionView))
            return $this->respondWithErrorData( ['message' => ErrorMessage::CERTIFICATE_CHECK_VIEW ,'exception' =>''], 403);
        $result =  $this->preCertificateRepository->findPaging_v2();
        if(isset($result['message']) && isset($result['exception']))
                return $this->respondWithErrorData( $result);
        return $this->respondWithCustomData($result);
    }

    #step 1 insert - update
    public function postGeneralInfomation(Request $request, int $id = null){
        if(! isset($id)){
            if(! CommonService::checkUserPermission($this->permissionAdd))
                return $this->respondWithErrorData( ['message' => ErrorMessage::CERTIFICATE_CHECK_ADD ,'exception' =>''], 403);
        }else{
            if(! CommonService::checkUserPermission($this->permissionEdit))
                return $this->respondWithErrorData( ['message' => ErrorMessage::CERTIFICATE_CHECK_UPDATE ,'exception' =>''], 403);
        }
        $rules = [
            'petitioner_name' => 'string|max:255',
            'petitioner_phone' => 'nullable|numeric',
            'petitioner_address' => 'nullable|string',
            'petitioner_identity_card' => 'nullable|string',
            'appraise_purpose_id' => 'nullable',
            'appraiser_sale_id' => 'nullable',
            'business_manager_id' => 'nullable',
            'appraiser_perform_id' => 'nullable',
            'customer'=>'array|sometimes',
            'customer.name' => 'nullable|string|max:255',
            'customer.address' => 'required_with:customer.name|nullable|string|max:255',
            'customer.phone' => 'required_with:customer.name|nullable|numeric',
            'total_preliminary_value' => 'nullable|integer|max:2000000000|min:0',
            'note' => 'nullable|string',
            'cancel_reason' => 'nullable|string',
            'pre_type' => 'nullable|string',
        ];

        $customAttributes = [
            'petitioner_name' => 'Tên khách hàng yêu cầu',
            'petitioner_phone' => 'Điện thoại',
            'petitioner_address' => 'Địa chỉ',
            'petitioner_identity_card' => 'CMND/MST/CCCD/Passport',
            'appraise_purpose_id' => 'Mục đích thẩm định',
            'appraiser_sale_id' => 'Nhân viên kinh doanh',
            'business_manager_id' => 'Quản lý nghiệp vụ',
            'appraiser_perform_id' => 'chuyên viên thực hiện',
            'customer.name' => 'Họ tên đối tác',
            'customer.address' => 'Địa chỉ',
            'customer.phone' => 'Điện thoại',
            'total_preliminary_value' => 'Tổng giá trị sơ bộ',
            'note' => 'Ghi chú',
            'cancel_reason' => 'Lý do hủy sơ bộ',
            'pre_type' => 'Loại sơ bộ',
        ];
        $validator = Validator::make($request->toArray(), $rules, $this->messages, $customAttributes);
        if ($validator->passes()) {
            //TODO Handle your data
            $result = $this->preCertificateRepository->postGeneralInfomation($request->toArray(), $id);
            if(isset($result['message']) && isset($result['exception']))
                return $this->respondWithErrorData( $result);

            return $this->respondWithCustomData($result);
        } else {
            //TODO Handle your error
            $data = ['message' => $validator->errors()->all(), 'exception' => null];
            return $this->respondWithErrorData( $data);
        }

    }
    #endregion

    public function getGeneralInfomation(int $id){
        if(! CommonService::checkUserPermission($this->permissionView))
            return $this->respondWithErrorData( ['message' => ErrorMessage::CERTIFICATE_CHECK_VIEW,'exception' =>''], 403);

        $result =  $this->preCertificateRepository->getGeneralInfomation($id);
        return $this->respondWithCustomData($result);
    }

    public function updateStatus(int $id, Request $request )
    {
        if(! CommonService::checkUserPermission($this->permissionEdit))
            return $this->respondWithErrorData( ['message' => ErrorMessage::PRE_CERTIFICATE_CHECK_UPDATE ,'exception' =>''], 403);

        $rules = [
            'status' => 'integer|required|between:1,6',
        ];
        $customAttributes = [
            'status' => 'Trạng thái phiếu',
        ];
        $validator = Validator::make($request->toArray(), $rules, $this->messages, $customAttributes);
        if ($validator->passes()) {
            //TODO Handle your data
            $result = $this->preCertificateRepository->updateStatus_v2($id , $request->toArray());
            if(isset($result['message']) && isset($result['exception']))
                return $this->respondWithErrorData( $result);

            return $this->respondWithCustomData($result);
        } else {
            //TODO Handle your error
            $data = ['message' => $validator->errors()->all(), 'exception' => null];
            return $this->respondWithErrorData( $data);
        }

    }


    public function uploadDocument($id, $description, Request $request): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->preCertificateRepository->uploadDocument($id, $description, $request));
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => $exception->getMessage(), 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    public function downloadDocument($id, Request $request)
    {
        try {
            $item = $this->preCertificateRepository->otherDocumentDownload($id, $request);
            if(isset($item->link)) {
                return $this->respondWithCustomData(['file_name' => $item->name, 'url' => $item->link]);
            } else {
                return $this->respondWithErrorData(['message' => 'Không tìm thấy link tải', 'exception' => '']);
            }
        } catch (\Exception $exception) {
            dd($exception);
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }
    public function deleteDocument($id)
    {
        try {
            $result = $this->preCertificateRepository->deleteDocument($id);
            if(isset($result['message']) && isset($result['exception']))
                return $this->respondWithErrorData( $result);
            return $this->respondWithCustomData($result);
        } catch (\Exception $exception) {
            dd($exception);
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }
    public function getPreCertificateWorkFlow(Request $request){

        $HSTD =$this->preCertificateRepository->getPreCertificateWorkFlow();
        $result = ['HSTD' => $HSTD];
        // dd($HSTD);
        return $this->respondWithCustomData($result);
    }
}
