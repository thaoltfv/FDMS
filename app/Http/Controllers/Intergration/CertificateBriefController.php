<?php

namespace App\Http\Controllers\Intergration;

use App\Contracts\CertificateRepository;
use App\Contracts\UserRepository;
use App\Enum\ErrorMessage;
use App\Enum\EstimateAssetDefault;
use App\Http\Controllers\Controller;
use App\Notifications\BroadcastNotification;
use App\Services\AppraiseVersionService;
use App\Services\CommonService;
use App\Services\Excel\ExportCertificateBriefs;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Notification;
use Validator;

class CertificateBriefController extends Controller
{
    private CertificateRepository $certificateRepository;
    private UserRepository $userRepository;

    private array $permissionView =['VIEW_CERTIFICATE_BRIEF'];
    private array $permissionAdd =['ADD_CERTIFICATE_BRIEF'];
    private array $permissionEdit =['EDIT_CERTIFICATE_BRIEF'];
    private array $permissionExport =['EXPORT_CERTIFICATE_BRIEF'];

    #region Contruct
    public function __construct(
        CertificateRepository $certificateRepository,
        UserRepository $userRepository

    )
    {
        $this->certificateRepository = $certificateRepository;
        $this->userRepository = $userRepository;

    }
    #endregion

    public function findPaging(Request $request)
    {
        if(! CommonService::checkUserPermission($this->permissionView))
            return $this->respondWithErrorData( ['message' => ErrorMessage::CERTIFICATE_CHECK_VIEW ,'exception' =>''], 403);
        $result =  $this->certificateRepository->findPaging_v2();
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
            'petitioner_identity_card' => 'nullable|string',
            'appraise_purpose_id' => 'required',
            //'appraiser_confirm_id' => 'integer',
            'appraiser_manager_id' => 'nullable',
            // 'appraise_purpose_id' => 'required',
            'appraiser_sale_id' => 'required',
            'appraiser_perform_id' => 'nullable',
            'appraiser_control_id' => 'nullable',
            'appraiser_id' => 'nullable',
            'service_fee' => 'integer|max:2000000000|min:0',
            'appraise_date' => 'required|string|max:255',
            //'document_date' => 'string|max:255',
            //'certificate_date' => 'string|max:255',
            'customer'=>'array|sometimes',
            'customer.name' => 'nullable|string|max:255',
            'customer.address' => 'required_with:customer.name|nullable|string|max:255',
            'customer.phone' => 'required_with:customer.name|nullable|numeric',
            'commission_fee' => 'numeric',
            'note' => 'nullable|string',
        ];

        $customAttributes = [
            'petitioner_name' => 'Tên khách hàng yêu cầu',
            'petitioner_phone' => 'Điện thoại',
            'petitioner_identity_card' => 'CMND/MST/CCCD/Passport',
            'petitioner_address' => 'Địa chỉ',
            'appraiser_confirm_id' => 'Đại diện ủy quyền',
            'appraiser_manager_id' => 'Đại diện theo pháp luật',
            'appraiser_control_id' => 'Kiểm soát viên',
            'appraise_purpose_id' => 'Mục đích thẩm định',
            'document_num' => 'Số hợp đồng',
            'document_date' => 'Ngày hợp đồng',
            'appraise_date' => 'Thời điểm thẩm định',
            'service_fee' => 'Tổng phí dịch vụ',
            'appraiser_sale_id' => 'Nhân viên kinh doanh',
            'appraiser_perform_id' => 'Chuyên viên thực hiện',
            'certificate_date' => 'Ngày chứng thư',
            'certificate_num' => 'Số chứng thư',
            'customer.name' => 'Họ tên đối tác',
            'customer.address' => 'Địa chỉ',
            'customer.phone' => 'Điện thoại',
            'commission_fee' => 'Chiết khấu',
            'note' => 'Ghi chú',
        ];
        $validator = Validator::make($request->toArray(), $rules, $this->messages, $customAttributes);
        if ($validator->passes()) {
            //TODO Handle your data
            $result = $this->certificateRepository->postGeneralInfomation($request->toArray(), $id);
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

        $result =  $this->certificateRepository->getGeneralInfomation($id);
        return $this->respondWithCustomData($result);
    }

    public function updateStatus(int $id, Request $request )
    {
        if(! CommonService::checkUserPermission($this->permissionEdit))
            return $this->respondWithErrorData( ['message' => ErrorMessage::CERTIFICATE_CHECK_UPDATE ,'exception' =>''], 403);

        $rules = [
            'status' => 'integer|required|between:1,6',
        ];
        $customAttributes = [
            'status' => 'Trạng thái phiếu',
        ];
        $validator = Validator::make($request->toArray(), $rules, $this->messages, $customAttributes);
        if ($validator->passes()) {
            //TODO Handle your data
            $result = $this->certificateRepository->updateStatus_v2($id , $request->toArray());
            if(isset($result['message']) && isset($result['exception']))
                return $this->respondWithErrorData( $result);

            return $this->respondWithCustomData($result);
        } else {
            //TODO Handle your error
            $data = ['message' => $validator->errors()->all(), 'exception' => null];
            return $this->respondWithErrorData( $data);
        }

    }

    public function findAppraisePaging(){
        if(! CommonService::checkUserPermission($this->permissionView))
            return $this->respondWithErrorData( ['message' => ErrorMessage::CERTIFICATE_CHECK_VIEW ,'exception' =>''], 403);

        $result =  $this->certificateRepository->findAppraisePaging();
        if(isset($result['message']) && isset($result['exception']))
            return $this->respondWithErrorData( $result);
        return $this->respondWithCustomData($result);
    }

    public function updateCertificateV3(Request $request, int $certificateId )
    {
        if(! CommonService::checkUserPermission($this->permissionEdit))
            return $this->respondWithErrorData( ['message' => ErrorMessage::CERTIFICATE_CHECK_UPDATE ,'exception' =>''], 403);

        $rules = [
            'appraises' => 'array|sometimes',
            'appraises.*.appraise_id' => 'integer|required_with:appraises'
        ];
        $customAttributes = [
            'appraises' => 'Thông tin tài sản thẩm định',
            'appraises.*.appraise_id' => 'Số tài sản thẩm định'
        ];
        $validator = Validator::make($request->toArray(), $rules, $this->messages, $customAttributes);
        if ($validator->passes()) {
            //TODO Handle your data
            $result = $this->certificateRepository->updateCertificateV3($request->toArray(),$certificateId);
            if(isset($result['message']) && isset($result['exception']))
                return $this->respondWithErrorData( $result);
            return $this->respondWithCustomData($result);
        } else {
            //TODO Handle your error
            $data = ['message' => $validator->errors()->all(), 'exception' => null];
            return $this->respondWithErrorData( $data);
        }

    }

    public function getCertificate(int $id){
        if(! CommonService::checkUserPermission($this->permissionView))
            return $this->respondWithErrorData( ['message' => ErrorMessage::CERTIFICATE_CHECK_VIEW ,'exception' =>''], 403);

        $result =  $this->certificateRepository->getCertificate($id);
        if(isset($result['message']) && isset($result['exception']))
            return $this->respondWithErrorData( $result);
        return $this->respondWithCustomData($result);
    }

    public function updateCertificateGeneral(int $id , Request $request )
    {
        $rules = [
            'petitioner_name' => 'nullable|string',
            'petitioner_phone'=> 'nullable|string',
            'petitioner_address' => 'nullable|string',
            'appraise_purpose_id' => 'nullable|integer',
            'appraise_date' => 'nullable|string',
            'document_num' => 'nullable|string',
            'document_date' => 'nullable|string',
            'service_fee' => 'nullable|integer',
            'certificate_date' => 'nullable|string',
            'certificate_num' => 'nullable|string',
        ];

        $customAttributes = [
            'petitioner_name' => 'Tên khách hàng yêu cầu',
            'petitioner_phone' => 'Điện thoại',
            'petitioner_address' => 'Địa chỉ',
            'appraise_purpose_id' => '',
            'appraise_date' => 'Thời điểm thẩm định',
            'document_num' => 'Số hợp đồng',
            'document_date' => 'Ngày hợp đồng',
            'service_fee' => 'Tổng phí dịch vụ',
            'certificate_date' => 'Ngày chứng thư',
            'certificate_num' => 'Số chứng thư',
        ];


        $validator = Validator::make($request->toArray(), $rules, $this->messages, $customAttributes);
        if ($validator->passes()) {
            //TODO Handle your data
            $result = $this->certificateRepository->updateCertificateGeneral($id, $request->toArray());
            if(isset($result['message']) && isset($result['exception']))
                return $this->respondWithErrorData( $result);
            return $this->respondWithCustomData($result);
        } else {
            //TODO Handle your error
            $data = ['message' => $validator->errors()->all(), 'exception' => null];
            return $this->respondWithErrorData( $data);
        }

    }

    public function getProcessingTime(){
        $result =  $this->certificateRepository->getProcessingTime();
        return $this->respondWithCustomData($result);
    }

    public function updateAppraisersTeam(int $id , Request $request )
    {
        if(! CommonService::checkUserPermission($this->permissionEdit))
            return $this->respondWithErrorData( ['message' => ErrorMessage::CERTIFICATE_CHECK_UPDATE ,'exception' =>''], 403);

        $rules = [
        ];

        $customAttributes = [
        ];

        $validator = Validator::make($request->toArray(), $rules, $this->messages, $customAttributes);
        if ($validator->passes()) {
            //TODO Handle your data
            $result = $this->certificateRepository->updateAppraisersTeam($id, $request->toArray());
            if(isset($result['message']) && isset($result['exception']))
                return $this->respondWithErrorData( $result);
            return $this->respondWithCustomData($result);
        } else {
            //TODO Handle your error
            $data = ['message' => $validator->errors()->all(), 'exception' => null];
            return $this->respondWithErrorData( $data);
        }

    }
    public function getComparisonAppraise(Request $request){
        $result =  $this->certificateRepository->getComparisonAppraise($request->toArray());
        return $this->respondWithCustomData($result);
    }

    public function exportCertificateBriefs(Request $request)
    {
        if(! CommonService::checkUserPermission($this->permissionExport))
            return $this->respondWithErrorData( ['message' => ErrorMessage::CERTIFICATE_CHECK_EXPORT ,'exception' =>''], 403);

        $result =  $this->certificateRepository->exportCertificateBriefs();
        if(isset($result['message']) && isset($result['exception']))
            return $this->respondWithErrorData( $result);
            // return $this->respondWithCustomData($result);

        return $this->respondWithCustomData((new ExportCertificateBriefs())->exportBrieft($result));
    }

    public function exportCustomizeCertificateBriefs(Request $request)
    {
        if(! CommonService::checkUserPermission($this->permissionExport))
            return $this->respondWithErrorData( ['message' => ErrorMessage::CERTIFICATE_CHECK_EXPORT ,'exception' =>''], 403);

        $result =  $this->certificateRepository->exportSelectedCertificateAssets();
        if(isset($result['message']) && isset($result['exception']))
            return $this->respondWithErrorData( $result);
            // return $this->respondWithCustomData($result);

        return $this->respondWithCustomData((new ExportCertificateBriefs())->exportCustomizeBrieft($result));
    }

    public function updateCertificateVersion(Request $request, int $certificateId )
    {
        if(! CommonService::checkUserPermission($this->permissionEdit))
            return $this->respondWithErrorData( ['message' => ErrorMessage::CERTIFICATE_CHECK_UPDATE ,'exception' =>''], 403);

        $rules = [
            // 'appraises' => 'array|sometimes',
            // 'appraises.*.appraise_id' => 'integer|required_with:appraises'
        ];
        $customAttributes = [
            // 'appraises' => 'Thông tin tài sản thẩm định',
            // 'appraises.*.appraise_id' => 'Số tài sản thẩm định'
        ];
        $validator = Validator::make($request->toArray(), $rules, $this->messages, $customAttributes);
        if ($validator->passes()) {
            //TODO Handle your data
            $result = $this->certificateRepository->updateCertificateVersion($certificateId, $request->toArray());
            // $result = AppraiseVersionService::updateCertificateVersion($certificateId, $request->toArray());
            if(isset($result['message']) && isset($result['exception']))
                return $this->respondWithErrorData( $result);
            return $this->respondWithCustomData($result);
        } else {
            //TODO Handle your error
            $data = ['message' => $validator->errors()->all(), 'exception' => null];
            return $this->respondWithErrorData( $data);
        }

    }
    public function getVersionAppraises(Request $request)
    {
        if(! CommonService::checkUserPermission($this->permissionView))
            return $this->respondWithErrorData( ['message' => ErrorMessage::CERTIFICATE_CHECK_VIEW ,'exception' =>''], 403);

        $result =  AppraiseVersionService::getVersionAppraises($request->toArray());
        if(isset($result['message']) && isset($result['exception']))
            return $this->respondWithErrorData( $result);
            // return $this->respondWithCustomData($result);

        return $this->respondWithCustomData((new ExportCertificateBriefs())->exportAsset($result));
    }

    public function getCertificateStatus(int $id)
    {
        $result =  $this->certificateRepository->getCertificateStatus($id);
        if(isset($result['message']) && isset($result['exception']))
            return $this->respondWithErrorData( $result);
        return $this->respondWithCustomData((new ExportCertificateBriefs())->exportAsset($result));
    }
    public function updateSubStatusFromConfig(Request $request)
    {
        $rules = [
            'principle' => 'array|required',
            'principle.*.status' => 'integer|required',
            'principle.*.sub_status' => 'integer|required',
            'principle.*.isActive' => 'integer|required',
        ];
        $customAttributes = [
            'principle' => 'Cấu hình kandboard',
            'principle.*.status' => 'status',
            'principle.*.sub_status' => 'sub_status',
            'principle.*.isActive' => 'isActive',
        ];
        $validator = Validator::make($request->toArray(), $rules, $this->messages, $customAttributes);
        if ($validator->passes()) {
            //TODO Handle your data
            $result = $this->certificateRepository->updateSubStatusFromConfig($request->toArray());
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
            return $this->respondWithCustomData($this->certificateRepository->uploadDocument($id, $description, $request));
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => $exception->getMessage(), 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    public function downloadDocument($id, Request $request)
    {
        try {
            $item = $this->certificateRepository->otherDocumentDownload($id, $request);
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
            $result = $this->certificateRepository->deleteDocument($id);
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
}
