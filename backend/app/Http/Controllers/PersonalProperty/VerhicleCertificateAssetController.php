<?php

namespace App\Http\Controllers\PersonalProperty;

use App\Contracts\VerhicleCertificateAssetRepository;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VerhicleCertificateAssetController extends Controller
{
    private VerhicleCertificateAssetRepository $verhicleAsset;

    public function __construct(VerhicleCertificateAssetRepository $verhicleAsset)
    {
        $this->verhicleAsset = $verhicleAsset;
    }
    public function postStep1(Request $request, int $id = null)
    {
        $rules = [
                'name' => 'required',
                'asset_type_id' => 'required|integer',
                'description' => 'nullable|string',
            ];

        $customAttributes = [
                'name' => 'Tên tài sản',
                'asset_type_id' => 'Loại tài sản',
                'description' => 'Mô tả',
            ];

        $validator = Validator::make($request->toArray(), $rules, $this->messages, $customAttributes);
        if ($validator->passes()) {
            //TODO Handle your data
            $result = $this->verhicleAsset->postStep1($request->toArray(), $id);
            if(isset($result['message']) && isset($result['exception']))
                return $this->respondWithErrorData( $result);
            return $this->respondWithCustomData($result);
        } else {
            //TODO Handle your error
            $data = ['message' => $validator->errors()->all(), 'exception' => null];
            return $this->respondWithErrorData( $data);
        }
    }
    public function getStep1(int $id)
    {
        try{
            $result = $this->verhicleAsset->getStep1($id);
            return $this->respondWithCustomData($result);
        }catch(Exception $ex){
            return $this->respondWithErrorData( ['message' => $ex->getMessage() , 'exception' => $ex->getCode()]);
        }
    }
    public function postStep2(Request $request, int $id)
    {
        $rules = [
                // 'other_asset_id' => 'required',
                'law.*.document_num' => 'required|string',
                'law.*.document_date' => 'nullable',
                // 'description' => 'nullable|string',
                // 'legal_name_holder' => 'nullable|string',
                // 'origin_of_use' => 'nullable|string',
                // 'content' => 'nullable|string',
                // 'duration' => 'nullable|string',
            ];

        $customAttributes = [
                'law.*.document_num' => 'Số pháp lý',
                'law.*.document_date' => 'Ngày pháp lý',
            ];
        $validator = Validator::make($request->toArray(), $rules, $this->messages, $customAttributes);
        if ($validator->passes()) {
            //TODO Handle your data
            $result = $this->verhicleAsset->postStep2($request->toArray(), $id);
            if(isset($result['message']) && isset($result['exception']))
                return $this->respondWithErrorData( $result);
            return $this->respondWithCustomData($result);
        } else {
            //TODO Handle your error
            $data = ['message' => $validator->errors()->all(), 'exception' => null];
            return $this->respondWithErrorData( $data);
        }
    }
    public function getStep2(int $id)
    {
        try{
            $result = $this->verhicleAsset->getStep2($id);
            if(isset($result['message']) && isset($result['exception']))
                return $this->respondWithErrorData( $result);
            return $this->respondWithCustomData($result);
        }catch(Exception $ex){
            return $this->respondWithErrorData( ['message' => $ex->getMessage() , 'exception' => $ex->getCode()]);
        }
    }
    public function postStep3(Request $request, int $id)
    {
        $rules = [
            'other_infomation.principle_id' => 'required|integer',
            'other_infomation.basis_property_id' => 'required|integer',
            'other_infomation.method_used_id' => 'required|integer',
            'other_infomation.approach_id' => 'required|integer',
            'other_infomation.document_description' => 'nullable|string'
        ];

        $customAttributes = [
            'other_infomation.principle_id' => 'Nguyên tắc cung cầu',
            'other_infomation.basis_property_id' => 'Cơ sở giá trị',
            'other_infomation.method_used_id' => 'Cách tiếp cận thị trường',
            'other_infomation.approach_id' => 'Phương pháp so sánh',
            'other_infomation.document_description' => 'Giả thiết'
            ];
        $validator = Validator::make($request->toArray(), $rules, $this->messages, $customAttributes);
        if ($validator->passes()) {
            //TODO Handle your data
            $result = $this->verhicleAsset->postStep3($request->toArray(), $id);
            if(isset($result['message']) && isset($result['exception']))
                return $this->respondWithErrorData( $result);
            return $this->respondWithCustomData($result);
        } else {
            //TODO Handle your error
            $data = ['message' => $validator->errors()->all(), 'exception' => null];
            return $this->respondWithErrorData( $data);
        }
    }

    public function getStep3(int $id)
    {
        try{
            $result = $this->verhicleAsset->getStep3($id);
            if(isset($result['message']) && isset($result['exception']))
                return $this->respondWithErrorData( $result);
            return $this->respondWithCustomData($result);
        }catch(Exception $ex){
            return $this->respondWithErrorData( ['message' => $ex->getMessage() , 'exception' => $ex->getCode()]);
        }
    }
    public function postStep4(Request $request, int $id)
    {
        $rules = [
            // 'other_asset_id' => 'required',
            'price.quantity' => 'required|numeric',
            'price.remaining_quality' => 'required|numeric',
            'price.unit_price' => 'required|numeric',
            'price.total_price' => 'required|numeric',
            'price.unit' => 'required|string',
        ];

        $customAttributes = [
            'price.quantity' => 'Số lượng',
            'price.remaining_quality' => 'Chất lượng còn lại',
            'price.price' => 'đơn giá',
            'price.total_price' => 'Thành tiền',
            'price.unit' => 'Đơn vị tính',
            ];
        $validator = Validator::make($request->toArray(), $rules, $this->messages, $customAttributes);
        if ($validator->passes()) {
            //TODO Handle your data
            $result = $this->verhicleAsset->postStep4($request->toArray(), $id);
            if(isset($result['message']) && isset($result['exception']))
                return $this->respondWithErrorData( $result);
            return $this->respondWithCustomData($result);
        } else {
            //TODO Handle your error
            $data = ['message' => $validator->errors()->all(), 'exception' => null];
            return $this->respondWithErrorData( $data);
        }
    }
    public function getStep4(int $id)
    {
        try{
            $result = $this->verhicleAsset->getStep4($id);
            if(isset($result['message']) && isset($result['exception']))
                return $this->respondWithErrorData( $result);
            return $this->respondWithCustomData($result);
        }catch(Exception $ex){
            return $this->respondWithErrorData( ['message' => $ex->getMessage() , 'exception' => $ex->getCode()]);
        }
    }

    public function getAll(int $id)
    {
        try{
            $result = $this->verhicleAsset->getAll($id);
            if(isset($result['message']) && isset($result['exception']))
                return $this->respondWithErrorData( $result);
            return $this->respondWithCustomData($result);
        }catch(Exception $ex){
            return $this->respondWithErrorData( ['message' => $ex->getMessage() , 'exception' => $ex->getCode()]);
        }
    }
}
