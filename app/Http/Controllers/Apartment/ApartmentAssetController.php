<?php

namespace App\Http\Controllers\Apartment;

use App\Contracts\ApartmentAssetRepository;
use App\Contracts\CompareAssetGeneralRepository;
use App\Enum\ErrorMessage;
use App\Enum\EstimateAssetDefault;
use App\Enum\ValueDefault;
use App\Http\Controllers\Controller;
use App\Services\CommonService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ApartmentAssetController extends Controller
{
    protected ApartmentAssetRepository $apartment;
    private CompareAssetGeneralRepository $compareAssetGeneral;
    private array $permissionView =['VIEW_CERTIFICATE_ASSET'];
    private array $permissionAdd =['ADD_CERTIFICATE_ASSET'];
    private array $permissionEdit =['EDIT_CERTIFICATE_ASSET'];
    public function __construct(ApartmentAssetRepository $apartment,
                                CompareAssetGeneralRepository $compareAssetGeneralRepository
    )
    {
        $this->apartment = $apartment;
        $this->compareAssetGeneral = $compareAssetGeneralRepository;
    }
    /**
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            if(! CommonService::checkUserPermission($this->permissionView)){
                return $this->respondWithErrorData( ['message' => ErrorMessage::APPRAISE_CHECK_VIEW ,'exception' =>''], 403);
            }
            $result = $this->apartment->getApartmentAssetById($id);
            if (isset($result['message']) && isset($result['exception']))
                return $this->respondWithErrorData($result);
            return $this->respondWithCustomData($result);
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => $exception->getMessage(), 'exception' => $exception];
            return $this->respondWithErrorData($data);
        }
    }

    public function postApartmentAsset(Request $request, int $id = null)
    {
        $rules = [
            'asset_type_id' => 'integer|required',
            'project_id' => 'integer|required',
            'appraise_asset' => 'string|required|max:255',
            'province_id' => 'integer|required',
            'district_id' => 'integer|nullable',
            'ward_id' => 'integer|nullable',
            'street_id' => 'integer|nullable',
            'coordinates' => 'string|required',
            'apartment_asset_properties' => 'array|required',
            'apartment_asset_properties.block_id' => 'integer|required_with:apartment_asset_properties',
            'apartment_asset_properties.floor_id' => 'integer|required_with:apartment_asset_properties',
            'apartment_asset_properties.apartment_name' => 'string|required_with:apartment_asset_properties',
            'apartment_asset_properties.handover_year' => 'integer|required_with:apartment_asset_properties',
            'apartment_asset_properties.bedroom_num' => 'integer|required_with:apartment_asset_properties',
            'apartment_asset_properties.wc_num' => 'integer|required_with:apartment_asset_properties',
            'apartment_asset_properties.area' => 'numeric|required_with:apartment_asset_properties|min:0',
            // 'apartment_asset_properties.legal_id' => 'integer|required_with:apartment_asset_properties',
            'apartment_asset_properties.direction_id' => 'integer|required_with:apartment_asset_properties',
            'apartment_asset_properties.furniture_quality_id' => 'integer|required_with:apartment_asset_properties',
            'apartment_asset_properties.description' => 'string|nullable',
            'apartment_asset_properties.utilities' => 'array|nullable',
        ];

        $customAttributes = [
            'asset_type_id' => 'Loại tài sản',
            'project_id' => 'Chung cư',
            'appraise_asset' => 'Tên tài sản',
            'province_id' => 'Tỉnh thành',
            'district_id' => 'Quận/Huyện',
            'ward_id' => 'Phường/Xã',
            'street_id' => 'Đường',
            'coordinates' => 'Vị trí',
            'apartment_asset_properties' => 'Chi tiết căn hộ',
            'apartment_asset_properties.block_id' => 'Block',
            'apartment_asset_properties.floor_id' => 'Tầng',
            'apartment_asset_properties.apartment_name' => 'Mã căn hộ',
            'apartment_asset_properties.handover_year' => 'Năm bàn giao',
            'apartment_asset_properties.bedroom_num' => 'Số phòng ngủ',
            'apartment_asset_properties.wc_num' => 'Số phòng vệ sinh',
            'apartment_asset_properties.area' => 'Diện tích',
            // 'apartment_asset_properties.legal_id' => 'Pháp lý',
            'apartment_asset_properties.direction_id' => 'Hướng nhà',
            'apartment_asset_properties.furniture_quality_id' => 'Chất lượng nội thất',
            'apartment_asset_properties.description' => 'Mô tả',
            'apartment_asset_properties.utilities' => 'Tiện ích',
        ];
        $validator = Validator::make($request->toArray(), $rules, $this->messages, $customAttributes);
        if ($validator->passes()) {
            //TODO Handle your data
            if(isset($id)){
                $result = $this->apartment->updateApartmentAsset($id, $request->toArray());
            }else{
                $result = $this->apartment->createApartmentAsset($request->toArray());
            }
            if (isset($result['message']) && isset($result['exception']))
                return $this->respondWithErrorData($result);
            return $this->respondWithCustomData($result);
        } else {
            //TODO Handle your error
            $data = ['message' => $validator->errors()->all(), 'exception' => null];
            return $this->respondWithErrorData($data);
        }
    }

    public function postApartmentAssetLaw(int $id, Request $request )
    {
        $rules = [
            'law'=>'required|array',
            'law.*.appraise_law_id' => 'integer|nullable',
            'law.*.legal_name_holder' => 'nullable|required_unless:law.*.appraise_law_id,0|string',
            'law.*.certifying_agency' => 'nullable|string',
            'law.*.content' => 'nullable|string',
            'law.*.document_num' => 'required|string',
            'law.*.document_date' => 'nullable',
            'law.*.duration' => 'nullable|string',
            'law.*.origin_of_use' => 'nullable|string',
        ];

        $customAttributes = [
            'law' => "Thông tin pháp lý",
            'law.*.document_num' => 'Số pháp lý',
            'law.*.document_date' => 'Ngày pháp lý',
            'law.*.appraise_law_id' => 'Loại pháp lý',
            'law.*.legal_name_holder' => 'Người đứng tên pháp lý',
            'law.*.certifying_agency' => 'Cơ quan xác nhận',
            'law.*.content' => 'Nội dung',
            'law.*.duration' => 'Thời hạn sử dụng',
            'law.*.origin_of_use' => 'Nguồn gốc sử dụng',
        ];
        $validator = Validator::make($request->toArray(), $rules, $this->messages, $customAttributes);
        if ($validator->passes()) {
            //TODO Handle your data
            $result = $this->apartment->postApartmentAssetLaw($id, $request->toArray());
            if (isset($result['message']) && isset($result['exception']))
                return $this->respondWithErrorData($result);
            return $this->respondWithCustomData($result);
        } else {
            //TODO Handle your error
            $data = ['message' => $validator->errors()->all(), 'exception' => null];
            return $this->respondWithErrorData($data);
        }
    }

    public function postApartmentAssetAppraisal(Request $request, int $id)
    {
        $rules = [
            'appraisal_methods'=>'required|array|min:3|max:3',
            'appraisal_methods.thong_nhat_muc_gia_chi_dan' => 'array|required|min:2',
            'appraisal_methods.thong_nhat_muc_gia_chi_dan.slug_value' => ['string','required',Rule::in(['cao-nhat','thap-nhat','trung-binh'])],
            'value_base_and_approach'=>'required|array',
            'value_base_and_approach.basis_property_id'=>'required_with:value_base_and_approach|integer',
            'value_base_and_approach.principle_id'=>'required_with:value_base_and_approach|integer',
            'value_base_and_approach.approach_id'=>'required_with:value_base_and_approach|integer',
            'value_base_and_approach.method_used_id'=>'required_with:value_base_and_approach|integer',
            'value_base_and_approach.description'=>'required_with:value_base_and_approach|string',
            ];
        $customAttributes = [
            'appraisal_methods' => 'Phương pháp tính toán',
            'appraisal_methods.thong_nhat_muc_gia_chi_dan' => 'Thống nhất mức giá chỉ dẫn',
            'appraisal_methods.thong_nhat_muc_gia_chi_dan.slug_value' => 'Thống nhất mức giá chỉ dẫn',
            'value_base_and_approach'=>'Cơ sở giá trị và cách tiếp cận',
            'value_base_and_approach.basis_property_id'=>'Cơ sở giá trị của tài sản thẩm định giá',
            'value_base_and_approach.principle_id'=>'Nguyên tắc thẩm định',
            'value_base_and_approach.approach_id'=>'Cách tiếp cận',
            'value_base_and_approach.method_used_id'=>'Phương pháp sử dụng',
            'value_base_and_approach.description'=>'Giả thiết và giả thiết đặc biệt',
            ];

        $validator = Validator::make($request->toArray(), $rules, $this->messages, $customAttributes);
        if ($validator->passes()) {
            //TODO Handle your data
            $result = $this->apartment->postApartmentAssetAppraisal($id, $request->toArray());
            if (isset($result['message']) && isset($result['exception']))
                return $this->respondWithErrorData($result);
            return $this->respondWithCustomData($result);
        } else {
            //TODO Handle your error
            $data = ['message' => $validator->errors()->all(), 'exception' => null];
            return $this->respondWithErrorData($data);
        }
    }

    public function postAparmentAssetHasAsset(Request $request, int $id)
    {
        $rules = [
            'comparison_factor' => 'required|array|min:1',
            'assets_general' => 'required|array|min:3|max:3',
            'assets_general.*.id' => 'required|integer',
            'assets_general.*.version' => 'required|integer',
            'map_img' => 'required|url'
            ];

        $customAttributes = [
            'comparison_factor' => 'Yếu tố so sánh',
            'assets_general' => 'Tài sản so sánh',
            'map_img' => 'Hình bản đồ',
            'assets_general.*.id' => 'Id tài sản so sánh',
            'assets_general.*.version' => 'Version tài sản so sánh',
            // 'map_img' => 'Hình bản đồ',
            ];
        $validator = Validator::make($request->toArray(), $rules, $this->messages, $customAttributes);
        if ($validator->passes()) {
            //TODO Handle your data
            $result = $this->apartment->postApartmentAssetHasAsset($id, $request->toArray());
            if (isset($result['message']) && isset($result['exception']))
                return $this->respondWithErrorData($result);
            return $this->respondWithCustomData($result);
        } else {
            //TODO Handle your error
            $data = ['message' => $validator->errors()->all(), 'exception' => null];
            return $this->respondWithErrorData($data);
        }
    }

    public function getApartmentVersionById(Request $request)
    {
        $result = [];
        if(isset($request['assets'])){
            $stt=0;
            foreach($request['assets'] as $req){
                $id = $req['id'];
                $version =  isset($req['version']) ? $req['version'] : 1;
                if(isset($id)){
                    $result[$stt] = $this->compareAssetGeneral->findApartmentVersionById($id,$version);
                    if(isset($result[$stt]))
                        $result[$stt]['version'] =  $version;
                }
                $stt++;
            }
        }
        return $this->respondWithCustomData($result);
    }

    public function getApartmentAllStep(int $id): JsonResponse
    {
        try {
            $result = $this->apartment->getApartmentAllStep($id);
            return $this->respondWithCustomData($result);
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => $exception->getMessage(), 'exception' => $exception];
            return $this->respondWithErrorData($data);
        }
    }

    public function postAparmentOtherAsset(Request $request, int $id)
    {
        $rules = [
            'other_assets' => 'array|sometimes',
            'other_assets.*.name' => 'required_with:other_assets|string',
            'other_assets.*.quantity' => 'required_with:other_assets|integer',
            'other_assets.*.unit' => 'required_with:other_assets|string',
            'other_assets.*.unit_price' => 'required_with:other_assets|integer',
            'other_assets.*.total_price' => 'required_with:other_assets|integer',
            ];

        $customAttributes = [
            'other_assets' => 'Tài sản khác',
            'other_assets.*.name' => 'Tên',
            'other_assets.*.quantity' => 'Số lượng',
            'other_assets.*.unit' => 'Đơn vị tính',
            'other_assets.*.unit_price' => 'Đơn giá',
            'other_assets.*.total_price' => 'Thành tiền',
            ];
        $validator = Validator::make($request->toArray(), $rules, $this->messages, $customAttributes);
        if ($validator->passes()) {
            //TODO Handle your data
            $result = $this->apartment->postOtherAssets($id, $request->toArray());
            if (isset($result['message']) && isset($result['exception']))
                return $this->respondWithErrorData($result);
            return $this->respondWithCustomData($result);
        } else {
            //TODO Handle your error
            $data = ['message' => $validator->errors()->all(), 'exception' => null];
            return $this->respondWithErrorData($data);
        }
    }

    public function updateComparisonFactor(Request $request, int $id)
    {
        $rules = [
            ];
        $customAttributes = [
            ];
        $validator = Validator::make($request->toArray(), $rules, $this->messages, $customAttributes);
        if ($validator->passes()) {
            //TODO Handle your data
            $result = $this->apartment->updateComparisonFactor($id, $request->toArray());
            if (isset($result['message']) && isset($result['exception']))
                return $this->respondWithErrorData($result);
            return $this->respondWithCustomData($result);
        } else {
            //TODO Handle your error
            $data = ['message' => $validator->errors()->all(), 'exception' => null];
            return $this->respondWithErrorData($data);
        }
    }

    public function updateRoundTotal(Request $request, int $id)
    {
        $rules = [
            ];
        $customAttributes = [
            ];
        $validator = Validator::make($request->toArray(), $rules, $this->messages, $customAttributes);
        if ($validator->passes()) {
            //TODO Handle your data
            $result = $this->apartment->updateRoundTotal($id, $request->toArray());
            if (isset($result['message']) && isset($result['exception']))
                return $this->respondWithErrorData($result);
            return $this->respondWithCustomData($result);
        } else {
            //TODO Handle your error
            $data = ['message' => $validator->errors()->all(), 'exception' => null];
            return $this->respondWithErrorData($data);
        }
    }

    public function getAutomaticAsset(Request $request, int $id)
    {
        $data = $this->apartment->getApartmentAllStep($id);
        $search['project_id'] = $data->project_id;
        $search['asset_type'] = array($data->asset_type_id);
        $search['bedroom_num'] = $data->apartmentAssetProperties->bedroom_num??0;
        $search['floor_name'] = $data->apartmentAssetProperties->floor->name??'';
        $search['distance'] = ValueDefault::RADIUS_SCAN;
        $search['location'] = $data->coordinates;
        $result = $this->compareAssetGeneral->autoApartmentAsset($search);
        $total = $result['total'];
        $distanceMax = ValueDefault::RADIUS_SCAN;
        if (isset($result['assets'])){
            $stt = 0;
            foreach ($result['assets'] as $key => $asset){
                $assetId = $asset['id'];
                $version = $asset['version'];
                $item = $this->compareAssetGeneral->findApartmentVersionById($assetId, $version);
                $result['assets'][$key] = $item;
                $asset = $item;
                $assetLocation =  $asset['coordinates'];
                if ($data->coordinates != $assetLocation){
                    $calDistance =  CommonService::calAppraiseAssetDistance($data->coordinates, $assetLocation);
                    if ($calDistance > $distanceMax){
                        $distanceMax = $calDistance;
                    }
                }
                $asset['version'] = $version;
                $stt ++;
            }
        }
        if ($total > 0){
            $result['message'] = 'Đã tìm được ' . $total .' tài sản so sánh';
        }else {
            $result['message'] = ErrorMessage::APPRAISE_AUTOMATIC_ASSET;
        }
        $result['distance_max'] = $distanceMax;
        $result['comparison_factors'] = EstimateAssetDefault::COMPARATION_FACTORS_APARTMENT;
        return $this->respondWithCustomData($result);
    }

    public function updateEstimateAssetPrice(Request $request, int $id){
        $rules = [

        ];
        $customAttributes = [
        ];
        $validator = Validator::make($request->toArray(), $rules, $this->messages, $customAttributes);
        if ($validator->passes()) {
            //TODO Handle your data
            $result = $this->apartment->updateEstimateAssetPrice($id);
            if(isset($result['message']) && isset($result['exception']))
                return $this->respondWithErrorData( $result);
            return $this->respondWithCustomData($result);
        } else {
            //TODO Handle your error
            $data = ['message' => $validator->errors()->all(), 'exception' => null];
            return $this->respondWithErrorData( $data);
        }
    }
}
