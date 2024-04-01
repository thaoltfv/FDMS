<?php

namespace App\Http\Controllers\PriceEstimate;

use App\Contracts\PriceEstimateRepository;
use App\Enum\ErrorMessage;
use App\Enum\ValueDefault;
use App\Http\Controllers\Controller;
use App\Http\Requests\Appraise\CreateAppraiseRequest;
use App\Http\Requests\Appraise\UpdateAppraiseRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Services\CommonService;
use Validator;

class PriceEstimateController extends Controller
{
    private PriceEstimateRepository $priceEstimateRepository;
    /**
     * ProvinceController constructor.
     */
    public function __construct(
        PriceEstimateRepository $priceEstimateRepository
    ) {
        $this->priceEstimateRepository = $priceEstimateRepository;
    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->priceEstimateRepository->findPaging());
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
            return $this->respondWithCustomData($this->priceEstimateRepository->findAll());
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
    public function show($id): JsonResponse
    {
        try {
            $test = request()->get('test');
            if (isset($test)) {
                $result = $this->priceEstimateRepository->findByIdTest($id);
            } else {
                $result = $this->priceEstimateRepository->findById($id);
            }

            if (isset($result->assetGeneral) && !empty($result->assetGeneral)) {
                CommonService::getAssetPriceTotal($result);
            }

            return $this->respondWithCustomData($result);
        } catch (\Exception $exception) {
            dd($exception);
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /**
     * @param $ids
     * @return JsonResponse
     */
    public function findByIds($ids): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->priceEstimateRepository->findByIds($ids));
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /**
     * @param CreateAppraiseRequest $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $objects = $request->toArray();
            if (isset($objects['properties'])) {
                foreach ($objects['properties'] as $property) {
                    if (isset($property['property_detail'])) {
                        $property_detail  = $property['property_detail'];
                        foreach ($property_detail as $item) {
                            if ($item['circular_unit_price'] <= ValueDefault::PRICE_VALIDATION_VALUE) {
                                $data = ['message' => ValueDefault::PRICE_VALIDATION_MESSAGE_UBND, 'exception' => null];
                                return $this->respondWithErrorData($data);
                            }
                        }
                    }
                }
            }
            return $this->respondWithCustomData($this->priceEstimateRepository->createPriceEstimate($request->toArray()));
        } catch (\Exception $exception) {
            dd($exception);
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }

    /**
     * @param $id
     * @param UpdatePriceEstimateRequest $request
     * @return JsonResponse
     */


    private array $permissionView = ['VIEW_CERTIFICATE_ASSET'];
    private array $permissionAdd = ['ADD_CERTIFICATE_ASSET'];
    private array $permissionEdit = ['EDIT_CERTIFICATE_ASSET'];
    private array $permissionExport = ['EXPORT_CERTIFICATE_ASSET'];

    public function getPriceEstimateDataFull(int $id)
    {
        if (!CommonService::checkUserPermission($this->permissionView)) {
            return $this->respondWithErrorData(['message' => ErrorMessage::PE_CHECK_VIEW, 'exception' => ''], 403);
        }
        $result = $this->priceEstimateRepository->getPriceEstimateDataFull($id);
        // dd($result->toJSON(JSON_UNESCAPED_UNICODE));
        if (isset($result['message']) && isset($result['exception']))
            return $this->respondWithErrorData($result);
        return $this->respondWithCustomData($result);
    }

    public function update($id, Request $request): JsonResponse
    {
        try {
            $objects = $request->toArray();
            if (isset($objects['properties'])) {
                foreach ($objects['properties'] as $property) {
                    if (isset($property['property_detail'])) {
                        $property_detail  = $property['property_detail'];
                        foreach ($property_detail as $item) {
                            if ($item['circular_unit_price'] <= ValueDefault::PRICE_VALIDATION_VALUE) {
                                $data = ['message' => ValueDefault::PRICE_VALIDATION_MESSAGE_UBND, 'exception' => null];
                                return $this->respondWithErrorData($data);
                            }
                        }
                    }
                }
            }
            return $this->respondWithCustomData($this->priceEstimateRepository->updatePriceEstimate($id, $request->toArray()));
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
    public function destroy($id): JsonResponse
    {
        try {
            return $this->respondWithCustomData($this->priceEstimateRepository->deletePriceEstimate($id));
        } catch (\Exception $exception) {
            Log::error($exception);
            $data = ['message' => ErrorMessage::SYSTEM_ERROR, 'exception' => $exception->getMessage()];
            return $this->respondWithErrorData($data);
        }
    }
    public function postGeneralInfomation(Request $request, int $id = null)
    {
        if (!isset($id)) {
            if (!CommonService::checkUserPermission($this->permissionAdd))
                return $this->respondWithErrorData(['message' => ErrorMessage::PE_CHECK_ADD, 'exception' => ''], 403);
        } else {
            if (!CommonService::checkUserPermission($this->permissionEdit))
                return $this->respondWithErrorData(['message' => ErrorMessage::PE_CHECK_UPDATE, 'exception' => ''], 403);
        }

        $rules = [
            'general_infomation' => 'array|required',
            'general_infomation.asset_type_id' => 'required|integer',
            'general_infomation.province_id' => 'required|integer',
            'general_infomation.district_id' => 'required|integer',
            'general_infomation.ward_id' => 'required|integer',
            'general_infomation.street_id' => 'required|integer',
            'general_infomation.appraise_asset' => 'required|string|max:255',
            'general_infomation.coordinates' => 'required|string|max:255',
            'traffic_infomation' => 'array|required',
            'traffic_infomation.description' => 'required|string|max:1000',
            'traffic_infomation.front_side' => 'required',
            'traffic_infomation.two_sides_land' => 'nullable',

            'total_area' => 'array|required',
            'total_area.*.is_transfer_facility' => 'required_with:total_area|boolean',
            'total_area.*.land_type_purpose_id' => 'required_with:total_area|integer',
            'total_area.*.total_area' => 'required_with:total_area|numeric',
            'planning_area' => 'sometimes|array',
            'planning_area.*.land_type_purpose_id' => 'required_with:planning_area|integer',
            'planning_area.*.planning_area' => 'required_with:planning_area|numeric',
            'planning_area.*.type_zoning' => 'required_with:planning_area|string',
        ];

        $customAttributes = [
            'general_infomation' => 'Thông tin chung',
            'general_infomation.asset_type_id' => 'Loại tài sản',
            'general_infomation.province_id' => 'Tỉnh/Thành phố',
            'general_infomation.district_id' => 'Quận/Huyện',
            'general_infomation.ward_id' => 'Phường/Xã',
            'general_infomation.street_id' => 'Đường',
            'general_infomation.appraise_asset' => 'Tên tài sản',
            'general_infomation.coordinates' => 'Toạ độ',
            'traffic_infomation' => 'Thông tin giao thông',
            'traffic_infomation.description' => 'Mô tả vị trí',
            'traffic_infomation.front_side' => 'Mặt tiền',
            'traffic_infomation.two_sides_land' => 'Căn góc',

            'total_area.*.is_transfer_facility' => 'Phân mục đích',
            'total_area.*.land_type_purpose_id' => 'Mục đích sử dụng[PHQH]',
            'total_area.*.total_area' => 'Diện tích theo mục đích sử dụng',
            'total_area' => 'Diện tích theo mục đích sử dụng',
            'planning_area' => 'Diện tích vi phạm quy hoạch',
            'planning_area.*.land_type_purpose_id' => 'Mục đích sử dụng[VPQH]',
            'planning_area.*.planning_area' => 'Diện tích vi phạm quy hoạch',
            'planning_area.*.type_zoning' => 'Loại quy hoạch',
        ];
        $validator = Validator::make($request->toArray(), $rules, $this->messages, $customAttributes);
        if ($validator->passes()) {
            //TODO Handle your data
            $result = $this->priceEstimateRepository->postGeneralInfomation($request->toArray(), $id);
            if (isset($result['message']) && isset($result['exception']))
                return $this->respondWithErrorData($result);
            return $this->respondWithCustomData($result);
        } else {
            //TODO Handle your error
            $data = ['message' => $validator->errors()->all(), 'exception' => null];
            return $this->respondWithErrorData($data);
        }
    }

    public function updateStep2(Request $request, int $id)
    {
        if (!CommonService::checkUserPermission($this->permissionEdit))
            return $this->respondWithErrorData(['message' => ErrorMessage::PE_CHECK_UPDATE, 'exception' => ''], 403);

        $rules = [
            'assets_general' => 'required|array|min:1|max:3',
            'assets_general.*.id' => 'required|integer',
            'assets_general.*.version' => 'required|integer',
        ];

        $customAttributes = [
            'assets_general' => 'Tài sản so sánh',
            'assets_general.*.id' => 'Id tài sản so sánh',
            'assets_general.*.version' => 'Version tài sản so sánh',
        ];

        $validator = Validator::make($request->toArray(), $rules, $this->messages, $customAttributes);
        if ($validator->passes()) {
            //TODO Handle your data
            $result = $this->priceEstimateRepository->updateStep2($request->toArray(), $id);
            if (isset($result['message']) && isset($result['exception']))
                return $this->respondWithErrorData($result);
            return $this->respondWithCustomData($result);
        } else {
            //TODO Handle your error
            $data = ['message' => $validator->errors()->all(), 'exception' => null];
            return $this->respondWithErrorData($data);
        }
    }

    public function step3Final(Request $request, int $id)
    {
        if (!CommonService::checkUserPermission($this->permissionEdit))
            return $this->respondWithErrorData(['message' => ErrorMessage::PE_CHECK_UPDATE, 'exception' => ''], 403);

        $rules = [
            'price_estimate_id' => 'required|integer',
            'asset_type_id' => 'required|integer',
            'appraise_purpose_id' => 'required|integer',
            'request_date' => 'required|date',
            'appraise_asset' => 'required|string|max:255',
            'coordinates' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'petitioner_name' => 'required|string|max:255',
            'coordinates' => 'required|string|max:255',
            'full_address' => 'required|string|max:255',
            'img_map' => 'nullable|string|max:255',

            'total_area' => 'array|required',
            'total_area.*.land_type_purpose_id' => 'required_with:total_area|integer',
            'total_area.*.total_area' => 'required_with:total_area|numeric',
            'total_area.*.main_area' => 'required_with:total_area|numeric',
            'total_area.*.unit_price' => 'required_with:total_area|numeric',
            'total_area.*.total_price' => 'required_with:total_price|numeric',
            'planning_area' => 'sometimes|array',
            'planning_area.*.land_type_purpose_id' => 'required_with:planning_area|integer',
            'planning_area.*.planning_area' => 'required_with:planning_area|numeric',
            'planning_area.*.unit_price' => 'required_with:planning_area|numeric',
            'planning_area.*.total_price' => 'required_with:planning_area|numeric',
        ];

        $customAttributes = [
            'asset_type_id' => 'Loại tài sản',
            'appraise_asset' => 'Tên tài sản',
            'coordinates' => 'Toạ độ',
            'description' => 'Mô tả vị trí',
            'petitioner_name' => 'Tên người yêu cầu',
            'request_date' => 'Ngày yêu cầu',
            'appraise_purpose_id' => 'Mục đích thẩm định',
            'full_address' => 'Địa chỉ tài sản',
            'img_map' => 'Sơ đồ vị trí',

            'total_area.*.land_type_purpose_id' => 'Mục đích sử dụng[PHQH]',
            'total_area.*.total_area' => 'Diện tích theo mục đích sử dụng',
            'total_area.*.main_area' => 'Diện tích phù hợp quy hoạch',
            'total_area.*.unit_price' => 'Đơn giá phù hợp quy hoạch',
            'total_area.*.total_price' => 'Thành tiền phù hợp quy hoạch',
            'total_area' => 'Diện tích theo mục đích sử dụng',
            'planning_area' => 'Diện tích vi phạm quy hoạch',
            'planning_area.*.land_type_purpose_id' => 'Mục đích sử dụng[VPQH]',
            'planning_area.*.planning_area' => 'Diện tích vi phạm quy hoạch',
            'planning_area.*.unit_price' => 'Đơn giá vị phạm quy hoạch',
            'planning_area.*.total_price' => 'Thành tiền vi phạm quy hoạch',
        ];

        $validator = Validator::make($request->toArray(), $rules, $this->messages, $customAttributes);
        if ($validator->passes()) {
            //TODO Handle your data
            $result = $this->priceEstimateRepository->step3Final($request->toArray(), $id);
            if (isset($result['message']) && isset($result['exception']))
                return $this->respondWithErrorData($result);
            return $this->respondWithCustomData($result);
        } else {
            //TODO Handle your error
            $data = ['message' => $validator->errors()->all(), 'exception' => null];
            return $this->respondWithErrorData($data);
        }
    }

    public function moveToAppraise(int $id)
    {
        if (!CommonService::checkUserPermission($this->permissionEdit))
            return $this->respondWithErrorData(['message' => ErrorMessage::PE_CHECK_UPDATE, 'exception' => ''], 403);



        //TODO Handle your data
        $result = $this->priceEstimateRepository->moveToAppraise($id);
        if (isset($result['message']) && isset($result['exception']))
            return $this->respondWithErrorData($result);
        return $this->respondWithCustomData($result);
    }

    public function getPriceEstimateFinal(int $id)
    {
        if (!CommonService::checkUserPermission($this->permissionView)) {
            return $this->respondWithErrorData(['message' => ErrorMessage::PE_CHECK_VIEW, 'exception' => ''], 403);
        }
        $result = $this->priceEstimateRepository->getPriceEstimateFinal($id);
        // dd($result->toJSON(JSON_UNESCAPED_UNICODE));
        if (isset($result['message']) && isset($result['exception']))
            return $this->respondWithErrorData($result);
        return $this->respondWithCustomData($result);
    }


    public function postApartmentInformation(Request $request, int $id = null)
    {
        if (!isset($id)) {
            if (!CommonService::checkUserPermission($this->permissionAdd))
                return $this->respondWithErrorData(['message' => ErrorMessage::PE_CHECK_ADD, 'exception' => ''], 403);
        } else {
            if (!CommonService::checkUserPermission($this->permissionEdit))
                return $this->respondWithErrorData(['message' => ErrorMessage::PE_CHECK_UPDATE, 'exception' => ''], 403);
        }

        $customAttributes = [

            'traffic_infomation' => 'Thông tin giao thông',
            'traffic_infomation.description' => 'Mô tả vị trí',
            'traffic_infomation.front_side' => 'Mặt tiền',
            'traffic_infomation.two_sides_land' => 'Căn góc',

            'total_area.*.is_transfer_facility' => 'Phân mục đích',
            'total_area.*.land_type_purpose_id' => 'Mục đích sử dụng[PHQH]',
            'total_area.*.total_area' => 'Diện tích theo mục đích sử dụng',
            'total_area' => 'Diện tích theo mục đích sử dụng',
            'planning_area' => 'Diện tích vi phạm quy hoạch',
            'planning_area.*.land_type_purpose_id' => 'Mục đích sử dụng[VPQH]',
            'planning_area.*.planning_area' => 'Diện tích vi phạm quy hoạch',
            'planning_area.*.type_zoning' => 'Loại quy hoạch',
        ];
        $rules = [
            'general_infomation' => 'array|required',
            'general_infomation.asset_type_id' => 'required|integer',
            'general_infomation.project_id' => 'required|integer',
            'general_infomation.province_id' => 'required|integer',
            'general_infomation.district_id' => 'required|integer',
            'general_infomation.ward_id' => 'required|integer',
            // 'general_infomation.street_id' => 'required|integer',
            'general_infomation.appraise_asset' => 'required|string|max:255',
            'general_infomation.coordinates' => 'required|string|max:255',

            'apartment_properties' => 'array|required',
            'apartment_properties.block_id' => 'integer|required_with:apartment_properties',
            'apartment_properties.floor_id' => 'integer|required_with:apartment_properties',
            'apartment_properties.apartment_name' => 'string|required_with:apartment_properties',
            'apartment_properties.area' => 'numeric|required_with:apartment_properties|min:0',
        ];

        $customAttributes = [
            'general_infomation' => 'Thông tin chung',
            'general_infomation.asset_type_id' => 'Loại tài sản',
            'general_infomation.project_id' => 'Chung cư',
            'general_infomation.province_id' => 'Tỉnh/Thành phố',
            'general_infomation.district_id' => 'Quận/Huyện',
            'general_infomation.ward_id' => 'Phường/Xã',
            // 'general_infomation.street_id' => 'Đường',
            'general_infomation.appraise_asset' => 'Tên tài sản',
            'general_infomation.coordinates' => 'Toạ độ',

            'apartment_properties' => 'Chi tiết căn hộ',
            'apartment_properties.block_id' => 'Block',
            'apartment_properties.floor_id' => 'Tầng',
            'apartment_properties.apartment_name' => 'Mã căn hộ',
            'apartment_properties.area' => 'Diện tích',

        ];
        $validator = Validator::make($request->toArray(), $rules, $this->messages, $customAttributes);
        if ($validator->passes()) {
            //TODO Handle your data
            $result = $this->priceEstimateRepository->postApartmentInformation($request->toArray(), $id);
            if (isset($result['message']) && isset($result['exception']))
                return $this->respondWithErrorData($result);
            return $this->respondWithCustomData($result);
        } else {
            //TODO Handle your error
            $data = ['message' => $validator->errors()->all(), 'exception' => null];
            return $this->respondWithErrorData($data);
        }
    }

    public function step3FinalApartment(Request $request, int $id)
    {
        if (!CommonService::checkUserPermission($this->permissionEdit))
            return $this->respondWithErrorData(['message' => ErrorMessage::PE_CHECK_UPDATE, 'exception' => ''], 403);

        $rules = [
            'price_estimate_id' => 'required|integer',
            'asset_type_id' => 'required|integer',
            'appraise_purpose_id' => 'required|integer',
            'request_date' => 'required|date',
            'appraise_asset' => 'required|string|max:255',
            'coordinates' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'petitioner_name' => 'required|string|max:255',
            'coordinates' => 'required|string|max:255',
            'full_address' => 'required|string|max:255',
            'img_map' => 'nullable|string|max:255',

            'apartment_finals' => 'array|required',
            'apartment_finals.*.name' => 'required_with:apartment_finals|string',
            'apartment_finals.*.total_area' => 'required_with:apartment_finals|numeric',
            'apartment_finals.*.unit_price' => 'required_with:apartment_finals|numeric',
            'apartment_finals.*.total_price' => 'required_with:apartment_finals|numeric',
        ];

        $customAttributes = [
            'asset_type_id' => 'Loại tài sản',
            'appraise_asset' => 'Tên tài sản',
            'coordinates' => 'Toạ độ',
            'description' => 'Mô tả vị trí',
            'petitioner_name' => 'Tên người yêu cầu',
            'request_date' => 'Ngày yêu cầu',
            'appraise_purpose_id' => 'Mục đích thẩm định',
            'full_address' => 'Địa chỉ tài sản',
            'img_map' => 'Sơ đồ vị trí',

            'apartment_finals.*.total_price' => 'Thành tiền',
            'apartment_finals.*.unit_price' => 'Đơn giá',
            'apartment_finals.*.total_area' => 'Diện tích',
            'apartment_finals.*.name' => 'Tên tài sản',
            'apartment_finals' => '',
        ];

        $validator = Validator::make($request->toArray(), $rules, $this->messages, $customAttributes);
        if ($validator->passes()) {
            //TODO Handle your data
            $result = $this->priceEstimateRepository->step3FinalApartment($request->toArray(), $id);
            if (isset($result['message']) && isset($result['exception']))
                return $this->respondWithErrorData($result);
            return $this->respondWithCustomData($result);
        } else {
            //TODO Handle your error
            $data = ['message' => $validator->errors()->all(), 'exception' => null];
            return $this->respondWithErrorData($data);
        }
    }

    public function moveToApartmentAsset(int $id)
    {
        if (!CommonService::checkUserPermission($this->permissionEdit))
            return $this->respondWithErrorData(['message' => ErrorMessage::PE_CHECK_UPDATE, 'exception' => ''], 403);

        //TODO Handle your data
        $result = $this->priceEstimateRepository->moveToApartmentAsset($id);
        if (isset($result['message']) && isset($result['exception']))
            return $this->respondWithErrorData($result);
        return $this->respondWithCustomData($result);
    }
}
