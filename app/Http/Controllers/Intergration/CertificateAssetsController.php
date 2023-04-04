<?php

namespace App\Http\Controllers\Intergration;

use App\Contracts\AppraiseAssetRepository;
use App\Contracts\AppraiseRepository;
use App\Contracts\CompareAssetGeneralRepository;
use App\Enum\ErrorMessage;
use App\Enum\EstimateAssetDefault;
use App\Http\Controllers\Controller;
use App\Services\AppraiseVersionService;
use App\Services\CommonService;
use App\Services\Excel\ExportCertificateAssets;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Rap2hpoutre\FastExcel\Facades\FastExcel;
use Validator;

class CertificateAssetsController extends Controller
{
    private AppraiseRepository $appraiseRepository;
    private AppraiseAssetRepository $appraiseAssetRepository;
    private CompareAssetGeneralRepository $compareAssetGeneralRepository;

    private array $permissionView =['VIEW_CERTIFICATE_ASSET'];
    private array $permissionAdd =['ADD_CERTIFICATE_ASSET'];
    private array $permissionEdit =['EDIT_CERTIFICATE_ASSET'];

    #region Contruct
    public function __construct(AppraiseRepository $appraiseRepository,
                                AppraiseAssetRepository $appraiseAssetRepository,
                                CompareAssetGeneralRepository $compareAssetGeneralRepository
                                )
    {
        $this->appraiseRepository = $appraiseRepository;
        $this->appraiseAssetRepository = $appraiseAssetRepository;
        $this->compareAssetGeneralRepository = $compareAssetGeneralRepository;
    }
    #endregion

    public function getAppraiseStep(int $id = null)
    {
        $result =  $this->appraiseRepository->getAppraiseStep($id);
        return $this->respondWithCustomData($result);
    }

    public function findPaging(Request $request)
    {
        if(! CommonService::checkUserPermission($this->permissionView)){
            return $this->respondWithErrorData( ['message' => ErrorMessage::APPRAISE_CHECK_VIEW ,'exception' =>''], 403);
        }

        $result =  $this->appraiseRepository->findPaging_v2();
        if(isset($result['message']) && isset($result['exception']))
            return $this->respondWithErrorData( $result);
        return $this->respondWithCustomData($result);
    }

    #region step 1
    public function getGeneralInfomation(int $id)
    {
        if(! CommonService::checkUserPermission($this->permissionView)){
            return $this->respondWithErrorData( ['message' => ErrorMessage::APPRAISE_CHECK_VIEW ,'exception' =>''], 403);
        }
        $result =  $this->appraiseRepository->getInfomation($id);
        return $this->respondWithCustomData($result);
    }

    #step 1 insert - update
    public function postGeneralInfomation(Request $request, int $id = null){
        if(! isset($id)){
            if(! CommonService::checkUserPermission($this->permissionAdd))
                return $this->respondWithErrorData( ['message' => ErrorMessage::APPRAISE_CHECK_ADD ,'exception' =>''], 403);
        }
        else{
            if(! CommonService::checkUserPermission($this->permissionEdit))
                return $this->respondWithErrorData( ['message' => ErrorMessage::APPRAISE_CHECK_UPDATE ,'exception' =>''], 403);
        }

        $rules = [
                'general_infomation'=>'array|required',
                'general_infomation.asset_type_id' => 'required|integer',
                'general_infomation.province_id' => 'required|integer',
                'general_infomation.district_id' => 'required|integer',
                'general_infomation.ward_id' => 'required|integer',
                'general_infomation.street_id' => 'required|integer',
                'general_infomation.appraise_asset' => 'required|string|max:255',
                'general_infomation.coordinates' => 'required|string|max:255',
                'traffic_infomation'=>'array|required',
                'traffic_infomation.description' => 'required|string|max:1000',
                'traffic_infomation.front_side' => 'required',
                'traffic_infomation.two_sides_land' => 'nullable',
                ];

        $customAttributes = [
                'general_infomation' => 'Thông tin chung',
                'general_infomation.asset_type_id' => 'Loại TS',
                'general_infomation.province_id' => 'Tỉnh/Thành phố',
                'general_infomation.district_id' => 'Quận/Huyện',
                'general_infomation.ward_id' => 'Phường/Xã',
                'general_infomation.street_id' => 'Đường',
                'general_infomation.appraise_asset' => 'Tên TS',
                'general_infomation.coordinates' => 'Toạ độ',
                'traffic_infomation'=> 'Thông tin giao thông',
                'traffic_infomation.description' => 'Mô tả vị trí',
                'traffic_infomation.front_side' => 'Mặt tiền',
                'traffic_infomation.two_sides_land' => 'Căn góc',
                ];
        $validator = Validator::make($request->toArray(), $rules, $this->messages, $customAttributes);
        if ($validator->passes()) {
            //TODO Handle your data
            $result = $this->appraiseRepository->postGeneralInfomation($request->toArray(), $id);
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

    #region #step 2
    public function getLandInfomation(int $id)
    {
        if(! CommonService::checkUserPermission($this->permissionView)){
            return $this->respondWithErrorData( ['message' => ErrorMessage::APPRAISE_CHECK_VIEW ,'exception' =>''], 403);
        }
        $result = $this->appraiseRepository->getLandInfomation($id);
        return $this->respondWithCustomData($result);

    }

    public function postLandDetailInfomation(Request $request, int $id)
    {
        if(! CommonService::checkUserPermission($this->permissionEdit))
            return $this->respondWithErrorData( ['message' => ErrorMessage::APPRAISE_CHECK_UPDATE ,'exception' =>''], 403);
        $rules = [
                'land_details'=>'required|array',
                'land_details.land_shape_id' => 'required|integer',
                'land_details.insight_width' => 'required|numeric|min:0|max:999999.99',
                'land_details.front_side_width' => 'required|numeric|min:0|max:999999.99',
                'land_details.topographic.topographic_id' => 'nullable|integer',
                'total_area'=>'array|required',
                'total_area.*.is_transfer_facility' => 'required_with:total_area|boolean',
                'total_area.*.land_type_purpose_id' => 'required_with:total_area|integer',
                'total_area.*.total_area' => 'required_with:total_area|numeric',
                'UBND_price' => 'required_with:total_area|array',
                'UBND_price.*.land_type_purpose_id' => 'required_with:UBND_price|integer',
                'UBND_price.*.position_type_id' => 'required_with:UBND_price|integer',
                'UBND_price.*.circular_unit_price' => 'required_with:UBND_price|numeric|min:9999',
                'planning_area' => 'sometimes|array',
                'planning_area.*.land_type_purpose_id' => 'required_with:planning_area|integer',
                'planning_area.*.planning_area' => 'required_with:planning_area|numeric',
                'planning_area.*.type_zoning' => 'required_with:planning_area|string',
                ];

        $customAttributes = [
                'land_details.land_shape_id' => 'Hình dáng đất',
                'land_details.insight_width' => 'Chiều dài',
                'land_details.front_side_width' => 'Chiều rộng mặt tiền',
                'land_details.topographic.topographic_id' => 'Địa hình',
                'total_area.*.is_transfer_facility' => 'Phân mục đích',
                'total_area.*.land_type_purpose_id' => 'Mục đích sử dụng[PHQH]',
                'total_area.*.total_area' => 'Diện tích theo mục đích sử dụng',
                'UBND_price' => 'Đơn giá UBND',
                'total_area' => 'Diện tích theo mục đích sử dụng',
                'planning_area' => 'Diện tích vi phạm quy hoạch',
                'UBND_price.*.land_type_purpose_id' => 'Mục đích sử dụng[nhà nước]',
                'UBND_price.*.position_type_id' => 'Vị trí',
                'UBND_price.*.circular_unit_price' => 'Đơn giá nhà nước',
                'planning_area.*.land_type_purpose_id' => 'Mục đích sử dụng[VPQH]',
                'planning_area.*.planning_area' => 'Diện tích vi phạm quy hoạch',
                'planning_area.*.type_zoning' => 'Loại quy hoạch',
                ];
        $validator = Validator::make($request->toArray(), $rules, $this->messages, $customAttributes);
        if ($validator->passes()) {
            //TODO Handle your data
            $result = $this->appraiseRepository->postLandDetailInfomation($request->toArray(), $id);
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

    #region Step 3
    // Step 3 - Construction -View
    public function getConstruction(int $appraiseId)
    {
        if(! CommonService::checkUserPermission($this->permissionView)){
            return $this->respondWithErrorData( ['message' => ErrorMessage::APPRAISE_CHECK_VIEW ,'exception' =>''], 403);
        }
        $result =  $this->appraiseRepository->getConstruction($appraiseId);
        return $this->respondWithCustomData(['construction' => $result]);
    }
    // Step 3 - Construction - submit
    public function postConstructionInfomation(Request $request, int $id)
    {
        if(! CommonService::checkUserPermission($this->permissionEdit))
            return $this->respondWithErrorData( ['message' => ErrorMessage::APPRAISE_CHECK_UPDATE ,'exception' =>''], 403);
        $rules = [
                'construction'=>'required|array',
                'construction.*.building_type_id' => 'required|integer',
                'construction.*.duration' => 'required|integer',
                'construction.*.total_construction_area' => 'required|numeric|min:0',
                'construction.*total_construction_base' => 'required|numeric|min:0',
                'construction.*.remaining_quality' => 'required|numeric|min:0',
                ];

        $customAttributes = [
                'construction' => 'Công trình xây dựng',
                'construction.*.building_type_id' => 'Loại công trình',
                'construction.*.duration' => 'Niện hạn',
                'construction.*.total_construction_area' => 'Diện tích xây dựng',
                'construction.*.total_construction_base' => 'Diện tích sàn',
                'construction.*.remaining_quality' => 'Chất lượng còn lại',
                ];
        $validator = Validator::make($request->toArray(), $rules, $this->messages, $customAttributes);
        if ($validator->passes()) {
            //TODO Handle your data
            $result = $this->appraiseRepository->postConstructionInfomation($request->toArray(), $id);
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

    #region Step 4
    public function getlaw(int $id)
    {
        if(! CommonService::checkUserPermission($this->permissionView)){
            return $this->respondWithErrorData( ['message' => ErrorMessage::APPRAISE_CHECK_VIEW ,'exception' =>''], 403);
        }
        $result = $this->appraiseRepository->getlaw($id);
        return $this->respondWithCustomData(['law' => $result]);

    }
     // Step 4 - law - submit
    public function postLawInfomation(Request $request, int $id)
    {
        if(! CommonService::checkUserPermission($this->permissionEdit))
            return $this->respondWithErrorData( ['message' => ErrorMessage::APPRAISE_CHECK_UPDATE ,'exception' =>''], 403);
        $rules = [
                'law'=>'required|array',
                'law.*.appraise_law_id' => 'integer|nullable',
                'law.*.date' => 'required|string|max:255',
                'law.*.legal_name_holder' => 'nullable|required_unless:law.*.appraise_law_id,0|string',
                'law.*.certifying_agency' => 'required|string',
                'law.*.content' => 'required|string',
                'law.*.land_details' => 'nullable|required_unless:law.*.appraise_law_id,0|array',
                'law.*.land_details.*.doc_no' => 'nullable|required_unless:law.*.appraise_law_id,0|integer',
                'law.*.land_details.*.land_no' => 'nullable|required_unless:law.*.appraise_law_id,0|integer',
                ];

        $customAttributes = [
                'law' => 'Pháp lý tài sản',
                'law.*.appraise_law_id' => 'Pháp lý tài sản',
                'law.*.date' => 'Số/Ngày',
                'law.*.legal_name_holder' => 'Người đứng tên pháp lý',
                'law.*.certifying_agency' => 'Cơ quan xác nhận',
                'law.*.content' => 'Nội dung',
                'law.*.land_details' => 'Thông tin số tờ , số thửa',
                'law.*.land_details.*.doc_no' => 'Số tờ',
                'law.*.land_details.*.land_no' => 'Số thửa',
                ];
        $validator = Validator::make($request->toArray(), $rules, $this->messages, $customAttributes);
        if ($validator->passes()) {
            //TODO Handle your data
            $result = $this->appraiseRepository->postLawInfomation($request->toArray(), $id);
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

    #region Step 5
    public function getAppraisalFacility(int $id)
    {
        if(! CommonService::checkUserPermission($this->permissionView)){
            return $this->respondWithErrorData( ['message' => ErrorMessage::APPRAISE_CHECK_VIEW ,'exception' =>''], 403);
        }
        $result = $this->appraiseRepository->getAppraisalFacility($id);
        return $this->respondWithCustomData($result);
    }
    // submit
    public function postAppraisalFacility(Request $request, int $id)
    {
        if(! CommonService::checkUserPermission($this->permissionEdit))
            return $this->respondWithErrorData( ['message' => ErrorMessage::APPRAISE_CHECK_UPDATE,'exception' =>''], 403);
        $rules = [
            'appraisal_methods'=>'required|array|min:3|max:3',
            'appraisal_methods.thong_nhat_muc_gia_chi_dan' => 'array|required|min:2',
            'appraisal_methods.tinh_gia_dat_hon_hop_con_lai' => 'array|required|min:2',
            'appraisal_methods.tinh_gia_dat_vi_pham_quy_hoach' => 'array|required|min:2',
            'appraisal_methods.tinh_gia_dat_hon_hop_con_lai.slug_value' => ['string','required',Rule::in(['theo-phuong-phap-doc-lap','theo-ty-le-gia-dat-co-so-chinh','theo-chi-phi-chuyen-mdsd-dat'])],
            'appraisal_methods.tinh_gia_dat_vi_pham_quy_hoach.slug_value' => ['string','required',Rule::in(['theo-gia-dat-qd-ubnd','theo-ty-le-gia-dat-thi-truong','khong-tinh'])],
            'appraisal_methods.thong_nhat_muc_gia_chi_dan.slug_value' => ['string','required',Rule::in(['cao-nhat','thap-nhat','trung-binh'])],
            'appraisal_methods.tinh_gia_dat_vi_pham_quy_hoach.value' => 'nullable|required_if:appraisal_methods.tinh_gia_dat_vi_pham_quy_hoach.slug_value,==,theo-ty-le-gia-dat-thi-truong|integer|min:1',
            'appraisal_methods.tinh_gia_dat_hon_hop_con_lai.value' => 'nullable|required_if:appraisal_methods.tinh_gia_dat_hon_hop_con_lai.slug_value,==,theo-ty-le-gia-dat-co-so-chinh|integer|min:1',
            'value_base_and_approach'=>'required|array',
            'value_base_and_approach.appraise_basis_property_id'=>'required_with:value_base_and_approach|integer',
            'value_base_and_approach.appraise_principle_id'=>'required_with:value_base_and_approach|integer',
            'value_base_and_approach.appraise_approach_id'=>'required_with:value_base_and_approach|integer',
            'value_base_and_approach.appraise_method_used_id'=>'required_with:value_base_and_approach|integer',
            'value_base_and_approach.document_description'=>'required_with:value_base_and_approach|string|max:255',
            ];

        $customAttributes = [
            'appraisal_methods' => 'Phương pháp tính toán',
            'appraisal_methods.thong_nhat_muc_gia_chi_dan' => 'Thống nhất mức giá chỉ dẫn',
            'appraisal_methods.tinh_gia_dat_hon_hop_con_lai' => 'Tính giá đất hỗn hợp còn lại',
            'appraisal_methods.tinh_gia_dat_vi_pham_quy_hoach' => 'Tính giá đất vi phạm quy hoạch',
            'appraisal_methods.tinh_gia_dat_vi_pham_quy_hoach.value' => 'Tỷ lệ giá đất theo thị trường',
            'appraisal_methods.tinh_gia_dat_hon_hop_con_lai.value' => 'Tỷ lệ giá đất theo cơ sở chính',
            'appraisal_methods.tinh_gia_dat_vi_pham_quy_hoach.slug_value' => 'Giá đất vi phạm quy hoạch',
            'appraisal_methods.tinh_gia_dat_hon_hop_con_lai.slug_value' => 'Giá đất hỗn hợp còn lại',
            'value_base_and_approach'=>'Cơ sở giá trị và cách tiếp cận',
            'value_base_and_approach.appraise_basis_property_id'=>'Cơ sở giá trị của tài sản thẩm định giá',
            'value_base_and_approach.appraise_principle_id'=>'Nguyên tắc thẩm định',
            'value_base_and_approach.appraise_approach_id'=>'Cách tiếp cận',
            'value_base_and_approach.appraise_method_used_id'=>'Phương pháp sử dụng',
            'value_base_and_approach.document_description'=>'Giả thiết và giả thiết đặc biệt',
            ];
        $validator = Validator::make($request->toArray(), $rules, $this->messages, $customAttributes);
        if ($validator->passes()) {
            //TODO Handle your data
            $result = $this->appraiseRepository->postAppraisalFacility($request->toArray(), $id);
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

    #region Step 6
    public function getAssetVersionById(Request $request)
    {
        $result = [];
        if(isset($request['assets'])){
            $stt=0;
            foreach($request['assets'] as $req){
                $id = $req['id'];
                $version =  isset($req['version']) ? $req['version'] : 1;
                if(isset($id)){
                    $result[$stt] = $this->compareAssetGeneralRepository->findVersionById_v2($id,$version);
                    if(isset($result[$stt]))
                        $result[$stt]['version'] =  $version;
                }
                $stt++;
            }
        }
        return $this->respondWithCustomData($result);
    }

    public function getAssetsAutomatic(int $id){
        ////reset step 6 data
        $comparisonFactor = EstimateAssetDefault::COMPARATION_FACTORS_V2;
        // $this->appraiseRepository->resetDataStep6($id);
        $request = $this->appraiseRepository->checkAppraiseExists($id);
        $request = json_decode(json_encode($request) , true);
        $result =[];
        $distance = 2;

        $appraiseLocation = $request['location'];

        $distanceMax= 0;
        $unrecognized =[];
        if(isset($request)){
            if ($request['front_side'] == 1) {
                $unrecognized = ($this->appraiseAssetRepository->estimateUnrecognizedFrontSiteAsset($request));
            } else {
                $unrecognized = $this->appraiseAssetRepository->estimateUnrecognizedUnfrontSiteAsset($request);
            }
            $assets = [];
            if(isset($unrecognized['assets'])){
                $stt = 0;
                foreach($unrecognized['assets'] as $data){
                    $assets[$stt] = $this->compareAssetGeneralRepository->findVersionById_v2($data['id'],$data['version']);
                    $assets[$stt]['version'] =  $data['version'];

                    $assetLocation =  $assets[$stt]['coordinates'];
                    $calDistance =  CommonService::calAppraiseAssetDistance($appraiseLocation,$assetLocation);
                    if($calDistance > $distanceMax){
                        $distanceMax = $calDistance;
                    }
                    $stt ++;
                }
            }
            $unrecognized['assets'] = $assets;
            if(!isset($assets)  || empty($assets)){
                // $unrecognized['error_message'] = 'Xin lỗi! Khu vực hiện tại chưa đủ dữ liệu để so sánh. Vui lòng chọn TSSS trên bản đồ.';
                $unrecognized['message'] = ErrorMessage::APPRAISE_AUTOMATIC_ASSET;
            }
            if(count($assets) == 3)
                $distance = $distanceMax;
            $result = $unrecognized;
        }
        $result['comparison_factor']=$comparisonFactor;
        $result['distance_max']= CommonService::roundDistance($distance);
        return $this->respondWithCustomData($result);
    }

    public function getAssets(int $id)
    {
        if(! CommonService::checkUserPermission($this->permissionView)){
            return $this->respondWithErrorData( ['message' => ErrorMessage::APPRAISE_CHECK_VIEW ,'exception' =>''], 403);
        }
        $result =  $this->appraiseRepository->getAssets($id);
        return $this->respondWithCustomData($result);
    }

    public function postAssets(Request $request, int $id){
        if(! CommonService::checkUserPermission($this->permissionEdit))
            return $this->respondWithErrorData( ['message' => ErrorMessage::APPRAISE_CHECK_UPDATE ,'exception' =>''], 403);

        $rules = [
            'comparison_factor' => 'required|array|min:1',
            'assets_general' => 'required|array|min:3|max:3',
            'assets_general.*.id' => 'required|integer',
            'assets_general.*.version' => 'required|integer',
            ];

        $customAttributes = [
            'comparison_factor' => 'Yếu tố so sánh',
            'assets_general' => 'Tài sản so sánh',
            'map_img' => 'Hình bản đồ',
            'assets_general.*.id' => 'Id tài sản so sánh',
            'assets_general.*.version' => 'Version tài sản so sánh',
            ];

        $validator = Validator::make($request->toArray(), $rules, $this->messages, $customAttributes);
        if ($validator->passes()) {
            //TODO Handle your data
            $result = $this->appraiseRepository->postAssets($request->toArray(), $id);
            if(isset($result['message']) && isset($result['exception']))
                return $this->respondWithErrorData( $result);
            return $this->respondWithCustomData($result);
        } else {
            //TODO Handle your error
            $data = ['message' => $validator->errors()->all(), 'exception' => null];
            return $this->respondWithErrorData( $data);
        }
    }

     /**
     * @return JsonResponse
     */
    public function findAllInElastic(): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->compareAssetGeneralRepository->findAllInElastic_v2());
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }
    #endregion

    #region Step 7
    public function getAppraiseFinallData(int $id){
        if(! CommonService::checkUserPermission($this->permissionView)){
            return $this->respondWithErrorData( ['message' => ErrorMessage::APPRAISE_CHECK_VIEW ,'exception' =>''], 403);
        }
        $result = $this->appraiseRepository->getAppraiseCalculatelData($id);
        if(isset($result['message']) && isset($result['exception']))
            return $this->respondWithErrorData( $result);
        return $this->respondWithCustomData($result);
    }

    public function getAppraiseData(int $id){
        if(! CommonService::checkUserPermission($this->permissionView)){
            return $this->respondWithErrorData( ['message' => ErrorMessage::APPRAISE_CHECK_VIEW ,'exception' =>''], 403);
        }
        $result = $this->appraiseRepository->getAppraiseData($id);
        if(isset($result['message']) && isset($result['exception']))
            return $this->respondWithErrorData( $result);
        return $this->respondWithCustomData($result);
    }

    public function getAppraiseDataStepOneToSix(int $id){
        if(! CommonService::checkUserPermission($this->permissionView)){
            return $this->respondWithErrorData( ['message' => ErrorMessage::APPRAISE_CHECK_VIEW ,'exception' =>''], 403);
        }
        $result = $this->appraiseRepository->getApraiseDataStepOneToSix($id);
        // dd($result->toJSON(JSON_UNESCAPED_UNICODE));
        if(isset($result['message']) && isset($result['exception']))
            return $this->respondWithErrorData( $result);
        return $this->respondWithCustomData($result);
    }

    // public function postConstructionCompany(Request $request, int $id){
    //     $rules = [
    //             'construction_company'=>'array|required|min:3',
    //         ];

    //     $customAttributes = [
    //             'construction_company'=>'Danh sách đơn vị xây dựng',
    //         ];
    //     $validator = Validator::make($request->toArray(), $rules, $this->messages, $customAttributes);
    //     if ($validator->passes()) {
    //         //TODO Handle your data
    //         $result = $this->appraiseRepository->postConstructionCompany($request->toArray(), $id);
    //         if(isset($result['message']) && isset($result['exception']))
    //             return $this->respondWithErrorData( $result);
    //         return $this->respondWithCustomData($result);
    //     } else {
    //         //TODO Handle your error
    //         $data = ['message' => $validator->errors()->all(), 'exception' => null];
    //         return $this->respondWithErrorData( $data);
    //     }
    // }
    public function postOtherAssets(Request $request, int $id){
        if(! CommonService::checkUserPermission($this->permissionEdit))
            return $this->respondWithErrorData( ['message' => ErrorMessage::APPRAISE_CHECK_UPDATE,'exception' =>''], 403);
        $rules = [
                'other_assets'=>'array|sometimes',
                'other_assets.*.unit_price' => 'required_with:other_assets|numeric|min:0',
                'other_assets.*.total_price' => 'required_with:other_assets|numeric|min:0',
                'other_assets.*.total' => 'required_with:other_assets|numeric|min:0',
                'other_assets.*.name' => 'required_with:other_assets|string|max:255',
                'other_assets.*.description' => 'nullable|string|max:255',
                'other_assets.*.dvt' => 'required_with:other_assets|string|max:255',
            ];

        $customAttributes = [
                'other_assets'=>'Danh sách tài sản khác',
                'other_assets.*.unit_price' => 'Đơn giá',
                'other_assets.*.total_price' => 'Tổng tiền',
                'other_assets.*.total' => 'Số lượng',
                'other_assets.*.name' => 'Tên tài sản',
                'other_assets.*.description' => 'Đặc điểm',
                'other_assets.*.dvt' => 'Đơn vị tính',
            ];
        $validator = Validator::make($request->toArray(), $rules, $this->messages, $customAttributes);
        if ($validator->passes()) {
            //TODO Handle your data
            $result = $this->appraiseRepository->postOtherAssets($request->toArray(), $id);
            if(isset($result['message']) && isset($result['exception']))
                return $this->respondWithErrorData( $result);
            return $this->respondWithCustomData($result);
        } else {
            //TODO Handle your error
            $data = ['message' => $validator->errors()->all(), 'exception' => null];
            return $this->respondWithErrorData( $data);
        }
    }

    public function updateComparisonFactor_V2(Request $request, int $id){
        if(! CommonService::checkUserPermission($this->permissionEdit)){
            return $this->respondWithErrorData( ['message' => ErrorMessage::APPRAISE_CHECK_UPDATE,'exception' =>''], 403);
        }
        $rules = [
                'appraise_adapter' => 'nullable|array|sometimes',
                'appraise_adapter.*.change_purpose_price' => 'required_with:appraise_adapter|numeric',
                'appraise_adapter.*.percent' => 'required_with:appraise_adapter|numeric',
                'appraise_adapter.*.change_violate_price' => 'required_with:appraise_adapter|numeric|min:0',
                'asset_unit_area' => 'nullable|array|sometimes',
                'asset_unit_area.*.violation_asset_area' => 'required_with:asset_unit_area|numeric',
                'asset_unit_price' => 'nullable|array|sometimes',
                'asset_unit_price.*.original_value' => 'required_with:asset_unit_price|numeric',
                'asset_unit_price.*.update_value' => 'required_with:asset_unit_price|numeric',
                // 'layer_cutting_price' => 'required|numeric',
                // 'round_composite' => 'required|numeric',
                // 'round_total' => 'required|numeric',
                // 'round_violation_composite' => 'required|numeric',
                // 'round_violation_facility' => 'required|numeric',
                // 'remaining_price' => 'nullable|array|sometimes',
                // 'remaining_price.remaining_commerce_price' => 'required_with:remaining_price|numeric',
            ];

        $customAttributes = [
                'appraise_adapter.*.change_purpose_price' => 'Chi phí chuyển mục đích sử dụng.',
                'appraise_adapter.*.change_violate_price' => 'Giá trị diện tích vi phạm quy hoạch.',
                'appraise_adapter.*.percent' => 'Tỷ lệ giá rao bán.',
                'asset_unit_area.*.violation_asset_area' => 'Đất vi phạm quy hoạch.',
                'asset_unit_price.*.original_value' => 'Đơn giá đất cơ sở.',
                'asset_unit_price.*.update_value' => 'Đơn giá đất cơ sở.',
                // 'layer_cutting_price' => 'Đơn giá sau cắt lớp.',
                // 'round_composite' => 'Làm tròn.',
                // 'round_total' => 'Làm tròn.',
                // 'round_violation_composite' => 'Làm tròn.',
                // 'round_violation_facility' => 'Làm tròn.',
                // 'remaining_price.remaining_commerce_price' => 'Đơn giá đất thị trường.',
            ];
        $validator = Validator::make($request->toArray(), $rules, $this->messages, $customAttributes);
        if ($validator->passes()) {
            //TODO Handle your data
            $data = $request->toArray();
            if (!empty($data['main_price']) && $data['main_price']['islayerCuttingPirce'] && $data['main_price']['layerCuttingPirce'] <= 0) {
                $data = ['message' => 'Đơn giá sau cắt lớp phải lớn hơn 0', 'exception' => null];
                return $this->respondWithErrorData($data);
            }

            $result = $this->appraiseRepository->updateComparisonFactor_V2($request->toArray(), $id);
            if(isset($result['message']) && isset($result['exception']))
                return $this->respondWithErrorData( $result);
            return $this->respondWithCustomData($result);
        } else {
            //TODO Handle your error
            $data = ['message' => $validator->errors()->all(), 'exception' => null];
            return $this->respondWithErrorData( $data);
        }
    }

    public function updateConstructionCompany(Request $request, int $id){
        if(! CommonService::checkUserPermission($this->permissionEdit))
            return $this->respondWithErrorData( ['message' => ErrorMessage::APPRAISE_CHECK_UPDATE,'exception' =>''], 403);
        $rules = [
                'contruction_company_update' => 'required|array',
                'contruction_company_default' => 'required|array',
            ];

        $customAttributes = [
            'contruction_company_update' => 'Danh sách đơn vị xây dựng mới',
            'contruction_company_default' => 'Danh sách đơn vị xây dựng cũ',
        ];
        $validator = Validator::make($request->toArray(), $rules, $this->messages, $customAttributes);
        if ($validator->passes()) {

            $result = $this->appraiseRepository->updateConstructionCompany($request->toArray(), $id);
            if(isset($result['message']) && isset($result['exception']))
                return $this->respondWithErrorData( $result);
            return $this->respondWithCustomData($result);
        } else {
            //TODO Handle your error
            $data = ['message' => $validator->errors()->all(), 'exception' => null];
            return $this->respondWithErrorData( $data);
        }
    }

    public function updateRoundAppraiseTotal(Request $request, int $id){
        if(! CommonService::checkUserPermission($this->permissionEdit))
            return $this->respondWithErrorData( ['message' => ErrorMessage::APPRAISE_CHECK_UPDATE,'exception' =>''], 403);
        $rules = [
            ];
        $customAttributes = [
            ];
        $validator = Validator::make($request->toArray(), $rules, $this->messages, $customAttributes);
        if ($validator->passes()) {
            //TODO Handle your data
            $result = $this->appraiseRepository->updateRoundAppraiseTotal( $id , $request->toArray());
            if(isset($result['message']) && isset($result['exception']))
                return $this->respondWithErrorData( $result);
            return $this->respondWithCustomData($result);
        } else {
            //TODO Handle your error
            $data = ['message' => $validator->errors()->all(), 'exception' => null];
            return $this->respondWithErrorData( $data);
        }
    }

    public function updateConstructionComparison(Request $request, int $id){
        if(! CommonService::checkUserPermission($this->permissionEdit))
            return $this->respondWithErrorData( ['message' => ErrorMessage::APPRAISE_CHECK_UPDATE,'exception' =>''], 403);
        $rules = [
            'tangible_assets' => 'required|array',
            'tangible_assets.*.comparison_tangible_factor' => 'nullable|array|sometimes',
            'tangible_assets.*.comparison_tangible_factor.h1' => 'required_with:tangible_assets.*.comparison_tangible_factor|numeric',
            'tangible_assets.*.comparison_tangible_factor.h2' => 'required_with:tangible_assets.*.comparison_tangible_factor|numeric',
            'tangible_assets.*.comparison_tangible_factor.h3' => 'required_with:tangible_assets.*.comparison_tangible_factor|numeric',
            'tangible_assets.*.comparison_tangible_factor.h4' => 'required_with:tangible_assets.*.comparison_tangible_factor|numeric',
            'tangible_assets.*.comparison_tangible_factor.h5' => 'required_with:tangible_assets.*.comparison_tangible_factor|numeric',

            'tangible_assets.*.comparison_tangible_factor.d4' => 'required_with:tangible_assets.*.comparison_tangible_factor|numeric',
            'tangible_assets.*.comparison_tangible_factor.p1' => 'required_with:tangible_assets.*.comparison_tangible_factor|numeric',
            'tangible_assets.*.comparison_tangible_factor.p2' => 'required_with:tangible_assets.*.comparison_tangible_factor|numeric',
            'tangible_assets.*.comparison_tangible_factor.p3' => 'required_with:tangible_assets.*.comparison_tangible_factor|numeric',
            'tangible_assets.*.comparison_tangible_factor.p5' => 'required_with:tangible_assets.*.comparison_tangible_factor|numeric',
            'tangible_assets.*.comparison_tangible_factor.id' => 'required_with:tangible_assets.*.comparison_tangible_factor|integer',

            'tangible_assets.*.construction_company' => 'nullable|array|sometimes',
            'tangible_assets.*.construction_company.*.unit_price_m2' => 'required_with:tangible_assets.*.construction_company|numeric|min:0',
            'tangible_assets.*.construction_company.*.id' => 'required_with:tangible_assets.*.construction_company|integer',

            'tangible_assets.*.total_desicion_average' => 'required|numeric|min:0|max:100000000',
            'tangible_assets.*.id' => 'required|integer',
            'xac_dinh_clcl' => 'required|array',
            'xac_dinh_dgxd' => 'required|array',
        ];
        $customAttributes = [
            'tangible_assets.*.comparison_tangible_factor' => 'Phương pháp chuyên gia.',
            'tangible_assets.*.comparison_tangible_factor.h1' => 'Móng cột (h)',
            'tangible_assets.*.comparison_tangible_factor.h2' => 'Tường (h)',
            'tangible_assets.*.comparison_tangible_factor.h3' => 'Nền, sàn (h)',
            'tangible_assets.*.comparison_tangible_factor.h4' => 'Kết cấu mái (h)',
            'tangible_assets.*.comparison_tangible_factor.h5' => 'Mái (h)',

            'tangible_assets.*.comparison_tangible_factor.d4' => 'Móng cột (p)',
            'tangible_assets.*.comparison_tangible_factor.p1' => 'Tường (p)',
            'tangible_assets.*.comparison_tangible_factor.p2' => 'Nền, sàn (p)',
            'tangible_assets.*.comparison_tangible_factor.p3' => 'Kết cấu mái (p)',
            'tangible_assets.*.comparison_tangible_factor.p5' => 'Mái (p)',
            'tangible_assets.*.comparison_tangible_factor.id' => 'Mã phương pháp chuyên gia',

            'tangible_assets.*.construction_company' => 'Đơn giá xây dựng',
            'tangible_assets.*.construction_company.*.unit_price_m2' => 'Đơn giá xây dựng',
            'tangible_assets.*.construction_company.*.id' => 'Mã đơn vị xây dựng',

            'tangible_assets.*.total_desicion_average' => 'Đơn giá quyết định',
            'tangible_assets.*.id' => 'Mã công trình xây dựng',
            'xac_dinh_clcl' => 'Phương pháp xác định chất lượng còn lại',
            'xac_dinh_dgxd' => 'Loại đơn giá xây dựng',
        ];
        $validator = Validator::make($request->toArray(), $rules, $this->messages, $customAttributes);
        if ($validator->passes()) {
            //TODO Handle your data

            $result = $this->appraiseRepository->updateTangibleComparisonFactor_V2($request->toArray(), $id);
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

    #region Service
    public function exportCertificateAssets(Request $request)
    {
        if(! CommonService::checkUserPermission($this->permissionView)){
            return $this->respondWithErrorData( ['message' => ErrorMessage::APPRAISE_CHECK_VIEW ,'exception' =>''], 403);
        }
        $result = $this->appraiseRepository->exportCertificateAssets();
        if(isset($result['message']) && isset($result['exception']))
            return $this->respondWithErrorData( $result);
        return $this->respondWithCustomData((new ExportCertificateAssets())->exportAsset($result));
    }
    #endregion

    public function updateEstimateAssetPrice(Request $request, int $id){
        if(! CommonService::checkUserPermission($this->permissionEdit))
            return $this->respondWithErrorData( ['message' => ErrorMessage::APPRAISE_CHECK_UPDATE,'exception' =>''], 403);
        $rules = [

        ];
        $customAttributes = [
        ];
        $validator = Validator::make($request->toArray(), $rules, $this->messages, $customAttributes);
        if ($validator->passes()) {
            //TODO Handle your data
            $result = $this->appraiseRepository->updateEstimateAssetPrice($id);
            if(isset($result['message']) && isset($result['exception']))
                return $this->respondWithErrorData( $result);
            return $this->respondWithCustomData($result);
        } else {
            //TODO Handle your error
            $data = ['message' => $validator->errors()->all(), 'exception' => null];
            return $this->respondWithErrorData( $data);
        }
    }
    public function getAppraiseDetail(int $id){
        $result = $this->appraiseRepository->getAppraiseDetail($id);
        if(isset($result['message']) && isset($result['exception']))
            return $this->respondWithErrorData( $result);
        return $this->respondWithCustomData($result);
    }
    // public function getCertificateByRealEstateId($id) {
    //     $result = AppraiseVersionService::getCertificateByRealEstateId($id);
    //     return $this->respondWithCustomData($result);
    // }
}
