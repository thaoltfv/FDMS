<?php

namespace App\Http\Controllers\AppraiseDictionary;

use App\Contracts\PreCertificateRepository;
use App\Contracts\DictionaryRepository;
use App\Contracts\UserRepository;
use App\Enum\ErrorMessage;
use App\Http\Controllers\Controller;
use App\Services\Excel\ExportPreCertificate;
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
    public UserRepository $userRepository;
    public DictionaryRepository $dictionaryRepository;

    /**
     * ProvinceController constructor.
     */
    public function __construct(
        PreCertificateRepository         $preCertificateRepository,
        UserRepository                $userRepository,
        DictionaryRepository          $dictionaryRepository
    ) {
        $this->preCertificateRepository = $preCertificateRepository;
        $this->userRepository = $userRepository;
        $this->dictionaryRepository = $dictionaryRepository;;
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
            return $this->respondWithCustomData($this->preCertificateRepository->otherDocumentUpload($id, $typeDocument, $request));
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
            if (isset($item->link)) {
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
            if (isset($test)) {
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
            if (is_numeric($result)) {
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
            if (is_numeric($result)) {
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


    private array $permissionView = ['VIEW_PRE_CERTIFICATE'];
    private array $permissionAdd = ['ADD_PRE_CERTIFICATE'];
    private array $permissionEdit = ['EDIT_PRE_CERTIFICATE'];
    private array $permissionExport = ['EXPORT_PRE_CERTIFICATE'];

    public function getPreCertificate(int $id)
    {
        if (!CommonService::checkUserPermission($this->permissionView))
            return $this->respondWithErrorData(['message' => ErrorMessage::PRE_CERTIFICATE_CHECK_VIEW, 'exception' => ''], 403);

        $result =  $this->preCertificateRepository->getPreCertificate($id);
        if (isset($result['message']) && isset($result['exception']))
            return $this->respondWithErrorData($result);
        return $this->respondWithCustomData($result);
    }

    public function findPaging(Request $request)
    {
        if (!CommonService::checkUserPermission($this->permissionView))
            return $this->respondWithErrorData(['message' => ErrorMessage::PRE_CERTIFICATE_CHECK_VIEW, 'exception' => ''], 403);
        $result =  $this->preCertificateRepository->findPaging_v2();
        if (isset($result['message']) && isset($result['exception']))
            return $this->respondWithErrorData($result);
        return $this->respondWithCustomData($result);
    }

    #step 1 insert - update
    public function postGeneralInfomation(Request $request, int $id = null)
    {
        if (!isset($id)) {
            if (!CommonService::checkUserPermission($this->permissionAdd))
                return $this->respondWithErrorData(['message' => ErrorMessage::CERTIFICATE_CHECK_ADD, 'exception' => ''], 403);
        } else {
            if (!CommonService::checkUserPermission($this->permissionEdit))
                return $this->respondWithErrorData(['message' => ErrorMessage::CERTIFICATE_CHECK_UPDATE, 'exception' => ''], 403);
        }
        $rules = [
            'petitioner_name' => 'string|max:255',
            'petitioner_phone' => 'nullable|numeric',
            'petitioner_address' => 'nullable|string',
            'petitioner_identity_card' => 'nullable|string',
            'appraise_purpose_id' => 'required',
            'appraiser_sale_id' => 'required',
            'business_manager_id' => 'nullable',
            'appraiser_perform_id' => 'nullable',
            'customer' => 'array|sometimes',
            'customer.name' => 'nullable|string|max:255',
            'customer.address' => 'required_with:customer.name|nullable|string|max:255',
            'customer.phone' => 'required_with:customer.name|nullable|numeric',
            'total_preliminary_value' => 'nullable|integer|min:0',
            'note' => 'nullable|string',
            'cancel_reason' => 'nullable|string',
            'commission_fee' => 'numeric',
            'pre_date' => 'required|string|max:255',
            'pre_asset_name' => 'string|max:255',
            'total_service_fee' => 'nullable|integer|min:0',
            'pre_type_id' => 'required',
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
            'pre_type_id' => 'Loại sơ bộ',
            'commission_fee' => 'Chiết khấu',
            'pre_date' => 'Ngày sơ bộ',
            'total_service_fee' => 'Tổng phí dịch vụ',
            'pre_asset_name' => 'Tên tài sản sơ bộ',
        ];
        $validator = Validator::make($request->toArray(), $rules, $this->messages, $customAttributes);
        if ($validator->passes()) {
            //TODO Handle your data
            $result = $this->preCertificateRepository->postGeneralInfomation($request->toArray(), $id);
            if (isset($result['message']) && isset($result['exception']))
                return $this->respondWithErrorData($result);

            return $this->respondWithCustomData($result);
        } else {
            //TODO Handle your error
            $data = ['message' => $validator->errors()->all(), 'exception' => null];
            return $this->respondWithErrorData($data);
        }
    }
    #endregion



    public function updateStatus(int $id, Request $request)
    {
        if (!CommonService::checkUserPermission($this->permissionEdit))
            return $this->respondWithErrorData(['message' => ErrorMessage::PRE_CERTIFICATE_CHECK_UPDATE, 'exception' => ''], 403);

        $rules = [
            'status' => 'integer|required|between:1,6',
        ];
        $customAttributes = [
            'status' => 'Trạng thái phiếu',
        ];
        $validator = Validator::make($request->toArray(), $rules, $this->messages, $customAttributes);
        if ($validator->passes()) {
            //TODO Handle your data
            $result = $this->preCertificateRepository->updateStatus_v2($id, $request->toArray());
            if (isset($result['message']) && isset($result['exception']))
                return $this->respondWithErrorData($result);

            return $this->respondWithCustomData($result);
        } else {
            //TODO Handle your error
            $data = ['message' => $validator->errors()->all(), 'exception' => null];
            return $this->respondWithErrorData($data);
        }
    }

    private array $permissionViewAccount = ['VIEW_ACCOUNTING'];
    private array $permissionAddAccount  = ['ADD_ACCOUNTING'];
    private array $permissionEditAccount = ['EDIT_ACCOUNTING'];
    public function updatePayments(int $id, Request $request)
    {
        if (!CommonService::checkUserPermission($this->permissionEditAccount) || !CommonService::checkUserPermission($this->permissionAddAccount))
            return $this->respondWithErrorData(['message' => ErrorMessage::PAYMENT_CHECK_UPDATE, 'exception' => ''], 403);

        $rules = [
            '*.pay_date' => 'required|string|max:255',
            '*.amount' => 'integer|min:0',
        ];

        $data = $request->toArray();
        $messages = [];
        foreach ($data as $index => $item) {
            $messages[$index . '.pay_date.required'] = 'tại dòng ' . ($index + 1) . ': ngày thanh toán là bắt buộc';
            $messages[$index . '.pay_date.string'] = 'tại dòng ' . ($index + 1) . ': ngày thanh toán phải là chuỗi';
            $messages[$index . '.pay_date.max'] = 'tại dòng ' . ($index + 1) . ': ngày thanh toán không được vượt quá 255 ký tự';
            $messages[$index . '.amount.required'] = 'tại dòng ' . ($index + 1) . ': số tiền là bắt buộc';
            $messages[$index . '.amount.integer'] = 'tại dòng ' . ($index + 1) . ': số tiền phải là số nguyên';
            $messages[$index . '.amount.min'] = 'tại dòng ' . ($index + 1) . ': số tiền phải lớn hơn hoặc bằng 0';
        }

        $validator = Validator::make($data, $rules, $messages);

        if ($validator->passes()) {
            //TODO Handle your data
            $result = $this->preCertificateRepository->updatePayments($id, $data);
            if (isset($result['message']) && isset($result['exception']))
                return $this->respondWithErrorData($result);

            return $this->respondWithCustomData($result);
        } else {
            //TODO Handle your error
            $data = ['message' => $validator->errors()->all(), 'exception' => null];
            return $this->respondWithErrorData($data);
        }
    }
    private array $permissionAddCERTIFICATE = ['ADD_CERTIFICATE_BRIEF'];
    public function updateToOffical(int $id, Request $request)
    {
        if (!CommonService::checkUserPermission($this->permissionAddCERTIFICATE))
            return $this->respondWithErrorData(['message' => ErrorMessage::PRE_CERTIFICATE_CHECK_UPDATE_TO_OFFICAL, 'exception' => ''], 403);

        $result = $this->preCertificateRepository->updateToOffical($id,  $request->toArray());
        if (isset($result['message']) && isset($result['exception']))
            return $this->respondWithErrorData($result);

        return $this->respondWithCustomData($result);
    }


    public function downloadDocument($id, Request $request)
    {
        try {
            $item = $this->preCertificateRepository->otherDocumentDownload($id, $request);
            if (isset($item->link)) {
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

    public function getPreCertificateWorkFlow(Request $request)
    {

        $HSTD = $this->preCertificateRepository->getPreCertificateWorkFlow();
        $result = ['HSTD' => $HSTD];
        // dd($HSTD);
        return $this->respondWithCustomData($result);
    }
    public function exportPreCertificate(Request $request)
    {
        if (!CommonService::checkUserPermission($this->permissionExport))
            return $this->respondWithErrorData(['message' => ErrorMessage::PRE_CERTIFICATE_CHECK_EXPORT, 'exception' => ''], 403);

        $result =  $this->preCertificateRepository->exportPreCertificate();
        if (isset($result['message']) && isset($result['exception']))
            return $this->respondWithErrorData($result);
        // return $this->respondWithCustomData($result);

        return $this->respondWithCustomData((new ExportPreCertificate())->exportPre($result));
    }
    // public function exportCustomizePreCertificate(Request $request)
    // {
    //     if(! CommonService::checkUserPermission($this->permissionExport))
    //         return $this->respondWithErrorData( ['message' => ErrorMessage::PRE_CERTIFICATE_CHECK_EXPORT ,'exception' =>''], 403);

    //     $result =  $this->preCertificateRepository->exportSelectedCertificateAssets();
    //     if(isset($result['message']) && isset($result['exception']))
    //         return $this->respondWithErrorData( $result);
    //         // return $this->respondWithCustomData($result);

    //     return $this->respondWithCustomData((new ExportPreCertificate())->exportCustomizePreCertificate($result));
    // }
}
