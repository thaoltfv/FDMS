<?php

namespace App\Repositories;

use App\Contracts\ApartmentAssetRepository;
use App\Enum\CompareMaterData;
use App\Enum\ErrorMessage;
use App\Enum\EstimateAssetDefault;
use App\Enum\ValueDefault;
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
use App\Models\RealEstate;
use App\Notifications\ActivityLog;
use App\Services\CommonService;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Log;
use Spatie\QueryBuilder\QueryBuilder;

class EloquentApartmentAssetRepository extends EloquentRepository implements ApartmentAssetRepository
{
    use ActivityLog;
    private function beforeSave(int $id)
    {
        if(! $this->model->query()->where('id', $id)->exists()){
            return ['message' => 'Không tồn tại TSTĐ chung cư '. $id, 'exception' => ''];
        }
        return null;
    }

    private function afterSave(int $id , int $step)
    {
        switch($step){
            case 4:
            case 5:
                $status = 2;
                break;
            default:
                $status = 1;
        }
        $dataUpdate = [
            'step' => $step,
            'status' => $status,
        ];
        $this->model->query()->where('id', $id)->update($dataUpdate);
        $this->updateRealEstate($id);
        $this->createAppraiseVersion($id);
    }

    public function updateStatus(int $realEstateId, int $status)
    {
        $dataUpdate = [
            'status' => $status,
        ];
        $this->model->query()->where('real_estate_id', $realEstateId)->update($dataUpdate);
        RealEstate::query()->where('id', $realEstateId)->update($dataUpdate);
    }

    //step 1
    public function createApartmentAsset(array $objects)
    {
        try{
            DB::beginTransaction();
            $user = CommonService::getUser();
            $objects['created_by'] = $user->id;
            $objects['step'] = 1;
            $objects['status'] = 1;
            $realEstateId = $this->createRealEstate($objects);
            $apartment = new ApartmentAsset($objects);
            $apartmentAssetArr = $apartment->attributesToArray();
            $apartmentAssetArr['id'] = $realEstateId;
            $apartmentAssetArr['real_estate_id'] = $realEstateId;
            $data = $this->model->query()->create($apartmentAssetArr);
            $this->model->query()->where('id', $data->id)->update(['id' => $realEstateId]);
            $id = $realEstateId;
            if(isset($objects['apartment_asset_properties'])){
                $properties = $objects['apartment_asset_properties'];
                $properties['apartment_asset_id'] = $id;
                $properties['legal_id'] = $properties['legal_id'] ?? 1;
                $propertiesArr = new ApartmentAssetProperty($properties);
                ApartmentAssetProperty::query()->create($propertiesArr->attributesToArray());
            }
            if(isset($objects['pic'])){
                $pics = $objects['pic'];
                foreach ($pics as $pic) {
                    $pic['apartment_asset_id'] = $id;
                    $picArr = new ApartmentAssetPic($pic);
                    ApartmentAssetPic::query()->create($picArr->attributesToArray());
                }
            }
            $realEstate = [];
            if (isset($objects['real_estate']))  $realEstate = $objects['real_estate'];
            $this->updateRealEstate($id, $realEstate);

            DB::commit();
            $result = $this->getApartmentAllStep($id);
            $this->CreateActivityLog($result, $result, 'create', 'tạo mới');
            return $result;
        }catch(Exception $ex){
            DB::rollBack();
            return ['message' => $ex->getMessage(), 'exception' => $ex];
        }
    }

    public function updateApartmentAsset(int $id, array $objects)
    {
        $check = $this->beforeSave($id);
        if(isset($check)){
            return $check;
        }
        try{
            DB::beginTransaction();
            $objects['step'] = 1;
            $objects['status'] = 1;
            $apartmentAssetArr = new ApartmentAsset($objects);
            $this->model->query()->where('id', $id)->update($apartmentAssetArr->attributesToArray());
            if(isset($objects['apartment_asset_properties'])){
                $properties = $objects['apartment_asset_properties'];
                $properties['apartment_asset_id'] = $id;
                $properties['legal_id'] = $properties['legal_id'] ?? 1;
                $propertiesArr = new ApartmentAssetProperty($properties);
                ApartmentAssetProperty::query()->updateOrCreate(['apartment_asset_id' => $id], $propertiesArr->attributesToArray());
            }
            ApartmentAssetPic::query()->where('apartment_asset_id', $id)->forceDelete();
            if(isset($objects['pic'])){
                $pics = $objects['pic'];
                foreach ($pics as $pic) {
                    $pic['apartment_asset_id'] = $id;
                    $picArr = new ApartmentAssetPic($pic);
                    ApartmentAssetPic::query()->create($picArr->attributesToArray());
                }
            }

            $additional = [];
            if (isset($objects['real_estate'])) {
                $realEstate = $objects['real_estate'];
                $additional['planning_info'] = $realEstate['planning_info'] ?? '';
                $additional['planning_source'] = $realEstate['planning_source'] ?? '';
                $additional['contact_person'] = $realEstate['contact_person'] ?? '';
                $additional['contact_phone'] = $realEstate['contact_phone'] ?? '';
            }
            $this->updateRealEstate($id, $additional);

            $this->deleteComparisonAsset($id);
            DB::commit();
            $result = $this->getApartmentAllStep($id);
            $this->CreateActivityLog($result, $result, 'update_data', 'cập nhật thông tin tài sản');
            return $result;
        }catch(Exception $ex){
            DB::rollBack();
            return ['message' => $ex->getMessage(), 'exception' => $ex];
        }
    }

    //step 2
    public function postApartmentAssetLaw(int $id, array $objects)
    {
        try{
            DB::beginTransaction();
            $check = $this->beforeSave($id);
            if(isset($check)){
                return $check;
            }
            if(ApartmentAssetLaw::query()->where('apartment_asset_id', $id)->exists()){
                ApartmentAssetLaw::query()->where('apartment_asset_id', $id)->delete();
            }
            foreach($objects['law'] as $data){
                $data['apartment_asset_id'] = $id;
                // $data['document_date'] = Carbon::createFromFormat('d/m/Y', $data['document_date'])->format('Y-m-d');
                $lawArr = new ApartmentAssetLaw($data);
                // dd($lawArr->attributesToArray());
                ApartmentAssetLaw::query()->create($lawArr->attributesToArray());
            }
            $this->afterSave($id, 2);
            $this->deleteComparisonAsset($id);
            DB::commit();
            $result = $this->getApartmentAllStep($id);
            $this->CreateActivityLog($result, $result, 'update_data', 'cập nhật thông tin pháp lý tài sản');
            return $result;
        }catch(Exception $ex){
            DB::rollBack();
            return ['message' => $ex->getMessage(), 'exception' => $ex];
        }
    }
    //step 3
    public function postApartmentAssetAppraisal(int $id, array $objects)
    {
        try{
            DB::beginTransaction();
            $check = $this->beforeSave($id);
            if(isset($check)){
                return $check;
            }
            $appraisalMethods = $objects['appraisal_methods'];
            $methodKeys = array_keys($appraisalMethods);
            ApartmentAssetAppraisalMethod::query()->where(['apartment_asset_id' => $id])->delete();
            foreach($methodKeys as $method){
                $methodData= $appraisalMethods[$method];
                $methodData['slug'] = $method;
                $methodData['apartment_asset_id'] = $id;
                $appraisalMethodsArr = new ApartmentAssetAppraisalMethod($methodData);
                ApartmentAssetAppraisalMethod::query()->create($appraisalMethodsArr->attributesToArray());
            }
            $appraisalBase = $objects['value_base_and_approach'];
            $appraisalBase['apartment_asset_id'] = $id;
            $appraisalBaseArr = new ApartmentAssetAppraisalBase($appraisalBase);
            ApartmentAssetAppraisalBase::query()->where(['apartment_asset_id' => $id])->delete();
            ApartmentAssetAppraisalBase::query()->create($appraisalBaseArr->attributesToArray());
            $this->afterSave($id, 3);
            $this->deleteComparisonAsset($id);
            DB::commit();
            $result = $this->getApartmentAllStep($id);
            // $result = EstimateAssetDefault::COMPARATION_FACTORS_APARTMENT;
            $this->CreateActivityLog($result, $result, 'update_data', 'cập nhật thông tin cơ sở thẩm định');
            return $result;
        }catch(Exception $ex){
            DB::rollBack();
            return ['message' => $ex->getMessage(), 'exception' => $ex];
        }
    }
    //step 4
    public function postApartmentAssetHasAsset(int $id, array $objects)
    {
        try{
            DB::beginTransaction();
            $check = $this->beforeSave($id);
            if(isset($check)){
                return $check;
            }
            $comparisons = $objects['comparison_factor'];
            $assets = $objects['assets_general'];
            $link  = $objects['map_img'];
            if(isset($link)){
                $type_id = 153;
                ApartmentAssetPic::query()->where('apartment_asset_id', '=', $id)->where('type_id', $type_id)->delete();
                $map =[
                    'apartment_asset_id' => $id,
                    'link' => $link,
                    'type_id' => $type_id,
                ];
                $pic = new ApartmentAssetPic($map);
                ApartmentAssetPic::query()->create($pic->attributesToArray());
            }

            if(isset($assets) && isset($comparisons)){
                $dictionaries = [];
                $apartmentData = $this->getApartmentAssetById($id);
                if(count($objects['assets_general']) > 3){
                    DB::rollBack();
                    $data = ['message' => ErrorMessage::APPRAISE_CHECK_ASSET_NUMBER, 'exception' =>  ''];
                    return $data;
                }
                $oldAppraiseHasAssets = ApartmentAssetHasAsset::query()->where('apartment_asset_id', $id)->get()->toArray();
                foreach($assets as $asset){
                    // dd($asset['adjust_percent']);
                    $assetId = $asset['id'];
                    $version = $asset['version'];
                    if(isset($oldAppraiseHasAssets) && count($oldAppraiseHasAssets)>0){
                        if(isset($oldAppraiseHasAssets) && count($oldAppraiseHasAssets)>0){
                            $key =array_search($asset['id'], array_column($oldAppraiseHasAssets, 'asset_general_id'));
                            if (!($key === false)){
                                if ($oldAppraiseHasAssets[$key]['version'] != $version){
                                    ApartmentAssetComparisonFactor::query()->where('apartment_asset_id', $id)->where('asset_general_id', $assetId)->forceDelete();
                                    ApartmentAssetHasAsset::query()->where('apartment_asset_id', $id)->where('asset_general_id', $assetId)->forceDelete();
                                    ApartmentAssetAdapter::query()->where('apartment_asset_id', $id)->where('asset_general_id', $assetId)->forceDelete();
                                    goto createNewAsset;
                                }
                            }
                            $oldAssetGeneralId = $assetId;
                            $this->postComparisonFactor($dictionaries, $apartmentData->toArray(), $comparisons , $asset , $id , $assetId, $oldAssetGeneralId) ;
                            continue;
                        }
                    }
                    // add thêm để dự phòng trường hợp không qua được điều kiện trên
                    if(ApartmentAssetHasAsset::where('apartment_asset_id',$id)->where('asset_general_id',$assetId)->exists()){
                        $this->postComparisonFactor($dictionaries, $apartmentData->toArray() , $comparisons , $asset , $id, $assetId,$oldAssetGeneralId) ;
                        continue;
                    }
                    createNewAsset:
                    //create appraise has asset
                    $version = $asset['version'];
                    $hasAsset['apartment_asset_id'] = $id;
                    $hasAsset['version'] = $version;
                    $hasAsset['asset_general_id'] = $assetId;
                    $appraiseHasAsset = new ApartmentAssetHasAsset($hasAsset);
                    ApartmentAssetHasAsset::query()->create($appraiseHasAsset->attributesToArray());
                    //adapter
                    $adapter = [
                        'apartment_asset_id' => $id,
                        'asset_general_id' => $assetId,
                        'percent' => intval($asset['adjust_percent']??0)+100,
                    ];
                    $adapterArr = new ApartmentAssetAdapter($adapter);
                    ApartmentAssetAdapter::query()->create($adapterArr->attributesToArray());
                    //create comparison fator
                    $this->postComparisonFactor($dictionaries,  $apartmentData->toArray(), $comparisons , $asset , $id, $assetId) ;
                }
                if(isset($oldAppraiseHasAssets) && count($oldAppraiseHasAssets)>0){
                    foreach($oldAppraiseHasAssets as  $oldAsset){
                        $key =array_search($oldAsset['asset_general_id'], array_column($assets, 'id'),true);
                        if($key === false){
                            $oldId = $oldAsset['asset_general_id'];
                            ApartmentAssetComparisonFactor::query()->where('apartment_asset_id', $id)->where('asset_general_id', $oldId)->forceDelete();
                            ApartmentAssetHasAsset::query()->where('apartment_asset_id', $id)->where('asset_general_id', $oldId)->forceDelete();
                            ApartmentAssetAdapter::query()->where('apartment_asset_id', $id)->where('asset_general_id', $oldId)->forceDelete();
                        }
                    }
                }
            }
            $this->calculatePriceNew($id);
            $this->afterSave($id, 4);
            DB::commit();
            $result = $this->getApartmentAssetById($id);
            $this->CreateActivityLog($result, $result, 'update_data', 'cập nhật thông tin tài sản so sánh');
            return $result;
        }catch(Exception $ex){
            DB::rollBack();
            Log::error($ex);
            return ['message' => $ex->getMessage(), 'exception' => $ex];
        }
    }
    public function postOtherAssets(int $id , array $objects)
    {
        try{
            DB::beginTransaction();
            $check = $this->beforeSave($id);
            if(isset($check))
                return $check;
            if(ApartmentAssetOtherAsset::query()->where('apartment_asset_id', $id)->exists()){
                ApartmentAssetOtherAsset::query()->where('apartment_asset_id', $id)->forceDelete();
            }
            $others = $objects['other_assets'];
            $otherPrice = 0;
            if(isset($others)){
                foreach($others as $other){
                    $other['apartment_asset_id'] = $id;
                    $otherArr = new ApartmentAssetOtherAsset($other);
                    ApartmentAssetOtherAsset::query()->create($otherArr->attributesToArray());
                }
                $otherPrice = array_sum(array_column($others, 'total_price'));
            }
            $this->afterSave($id, 5);
            $this->calculatePrice($id);
            DB::commit();
            $result = $this->getApartmentAssetById($id);
            $this->CreateActivityLog($result, $result, 'update_data', 'cập nhật thông tin tài sản khác');
            return $this->getTotalPrice($id);
        }catch(Exception $ex){
            DB::rollBack();
            return ['message' => $ex->getMessage(), 'exception' => $ex];
        }
    }

    public function updateComparisonFactor(int $id, array $objects)
    {
        try{
            DB::beginTransaction();
            $comparisons = $objects['comparison_factor'];
            $otherComparisons = $objects['other_comparison'];
            $deleteComparisons = $objects['delete_other_comparison'];
            $roundTotal = $objects['round_total'];
            if (isset($objects['apartment_asset_price'])){
                $apartment_asset_price = $objects['apartment_asset_price'];
            }
            
            $adapters = $objects['apartment_adapter'];
            if (isset($roundTotal)){
                $slug = 'round_total';
                $this->updateOrCreatePrice($id, $slug, $roundTotal);
            }
            if (isset($apartment_asset_price)){
                $slug = 'apartment_asset_price';
                $this->updateOrCreatePrice($id, $slug, $apartment_asset_price);
            }
            if (isset($adapters)){
                foreach ($adapters as $adapter){
                    ApartmentAssetAdapter::query()->where('id', $adapter['id'])->update(['percent' => $adapter['percent'],'change_negotiated_price' => $adapter['change_negotiated_price']]);
                }
            }
            if (isset($comparisons)){
                if (isset($otherComparisons)){
                    $comparisons = array_merge($comparisons, $otherComparisons);
                }
                foreach ($comparisons as $comparison){
                    $comparison = array_map(function($v){
                        return (is_null($v)) ? "" : $v;
                    },$comparison);

                    if ($comparison['adjust_percent']  > 0) {
                        $comparison['description'] = CompareMaterData::COMPARISONS_DESCRIPTION['kem_thuan_loi'];
                    } else if ($comparison['adjust_percent']  < 0) {
                        $comparison['description'] = CompareMaterData::COMPARISONS_DESCRIPTION['thuan_loi'];
                    } else {
                        $comparison['description'] = CompareMaterData::COMPARISONS_DESCRIPTION['tuong_dong'];
                    }
                    $comparisonArr = new ApartmentAssetComparisonFactor($comparison);
                    ApartmentAssetComparisonFactor::query()->updateOrCreate(['id' => $comparison['id']??-1], $comparisonArr->attributesToArray());
                }
            }
            if (isset($deleteComparisons)) {
                foreach ($deleteComparisons as $comparison) {
                    if (isset($comparison['id'])) {
                        ApartmentAssetComparisonFactor::where('id', $comparison['id'])->forceDelete();
                    }
                }
            }
            $this->calculatePrice($id);
            $this->afterSave($id, 5);
            DB::commit();
            $result = $this->getApartmentAssetById($id);
            $this->CreateActivityLog($result, $result, 'update_data', 'cập nhật bảng tổng hợp và bảng điều chỉnh QSDĐ');
            return $this->getTotalPrice($id);
        }catch(Exception $ex){
            DB::rollBack();
            return ['message' => $ex->getMessage(), 'exception' => ''];
        }
    }

    public function updateRoundTotal(int $id, $objects)
    {
        try{
            DB::beginTransaction();
            $roundTotal = $objects['apartment_round_total'];
            $slug = 'apartment_round_total';
            $this->updateOrCreatePrice($id, $slug, $roundTotal);
            $this->afterSave($id, 5);
            DB::commit();
            $result = $this->getApartmentAssetById($id);
            $this->CreateActivityLog($result, $result, 'update_data', 'cập nhật thông tin bảng tổng hợp kết quả');
            return $this->getTotalPrice($id);
        }catch(Exception $ex){
            DB::rollBack();
            return ['message' => $ex->getMessage(), 'exception' => ''];
        }
    }

    #region comparison Factor
    private function postComparisonFactor(array $dictionaries, array $apartment ,array $comparison, array $asset , int $id,int $assetGeneralId, int  $oldAssetGeneralId = null  )
    {
        $allComparisonFactor = [
            'phap_ly', 'loai_can', 'so_phong_ngu', 'dien_tich', 'tang', 'so_phong_wc', 'loai_can_ho'
        ];
        // dd($apartment['apartment_asset_properties']);
        // dd($comparison,$allComparisonFactor,$oldAssetGeneralId);
        foreach ($allComparisonFactor as $comparisonFactorTmp) {
            if(isset($oldAssetGeneralId)){
                if(in_array($comparisonFactorTmp, $comparison)) {
                    ApartmentAssetComparisonFactor::where('apartment_asset_id', $id)
                        ->where('type', $comparisonFactorTmp)
                        ->update(['status' => 1]);
                } else {
                    if(($comparisonFactorTmp != "yeu_to_khac") && $comparisonFactorTmp != "phap_ly") {
                        ApartmentAssetComparisonFactor::where('apartment_asset_id', $id)
                            ->where('type', $comparisonFactorTmp)
                            ->update(['status' => 0]);
                    }
                }
                continue;
            }
            // dd('làm nè');
            if($comparisonFactorTmp == 'phap_ly'){
                $apartmentValue = $apartment['apartment_asset_properties']['legal']['description']??'Không biết';
                $assetValue = $asset['room_details'][0]['legal']['description']??'Không biết';
                ////phap ly always true
                $status = true;
                // $dictionary = $dictionaries['phap_ly'];
                $type = 'phap_ly';
                $name = 'Pháp lý';
                $this->comparisonNoDictionary($apartmentValue, $assetValue, $status , $id, $assetGeneralId, $type, $name);
            }elseif($comparisonFactorTmp == 'loai_can'){
                $apartmentValue = $apartment['apartment_asset_properties']['block']['rank']['description'] ?? 'Không biết';
                $assetValue = $asset['apartment_specification']['rank']['description'] ?? 'Không biết';
                $status = false;
                if(in_array($comparisonFactorTmp, $comparison)){
                    $status = true;
                }
                $type = 'loai_can';
                $name = 'Loại chung cư';
                $this->comparisonNoDictionary($apartmentValue, $assetValue, $status , $id, $assetGeneralId, $type, $name);
            }elseif($comparisonFactorTmp == 'so_phong_ngu'){
                $apartmentValue = $apartment['apartment_asset_properties']['bedroom_num'] ?? 0;
                $assetValue = $asset['room_details'][0]['bedroom_num'] ?? 0;
                $status = false;
                if(in_array($comparisonFactorTmp, $comparison)){
                    $status = true;
                }
                $type = 'so_phong_ngu';
                $name = 'Số phòng ngủ';
                $this->comparisonNoDictionary($apartmentValue, $assetValue, $status , $id, $assetGeneralId, $type, $name);
            }elseif($comparisonFactorTmp == 'dien_tich'){
                $apartmentValue = $apartment['apartment_asset_properties']['area'] ?? 0;
                $assetValue = $asset['room_details'][0]['area'] ?? 0;
                $status = false;
                if(in_array($comparisonFactorTmp, $comparison)){
                    $status = true;
                }
                $type = 'dien_tich';
                $name = 'Diện tích';
                $this->comparisonNoDictionary($apartmentValue, $assetValue, $status , $id, $assetGeneralId, $type, $name);
            }elseif($comparisonFactorTmp == 'tang'){
                $apartmentValue = $apartment['apartment_asset_properties']['floor']['name'] ?? 'Không biết';
                $assetValue = $asset['floor']['name'] ?? 'Không biết';
                $status = false;
                if(in_array($comparisonFactorTmp, $comparison)){
                    $status = true;
                }
                $type = 'tang';
                $name = 'Tầng';
                $this->comparisonNoDictionary($apartmentValue, $assetValue, $status , $id, $assetGeneralId, $type, $name);
            }elseif($comparisonFactorTmp == 'so_phong_wc'){
                $apartmentValue = $apartment['apartment_asset_properties']['wc_num'] ?? 0;
                $assetValue = $asset['room_details'][0]['wc_num'] ?? 0;
                $status = false;
                if(in_array($comparisonFactorTmp, $comparison)){
                    $status = true;
                }
                $type = 'so_phong_wc';
                $name = 'Số phòng vệ sinh';
                $this->comparisonNoDictionary($apartmentValue, $assetValue, $status , $id, $assetGeneralId, $type, $name);
            }elseif($comparisonFactorTmp == 'loai_can_ho'){
                $apartmentValue = $apartment['apartment_asset_properties']['loaicanho']['description']??'Không biết';
                $assetValue = $asset['room_details'][0]['loaicanho']['description']??'Không biết';
                $status = false;
                if(in_array($comparisonFactorTmp, $comparison)){
                    $status = true;
                }
                $type = 'loai_can_ho';
                $name = 'Loại căn hộ';
                $this->comparisonNoDictionary($apartmentValue, $assetValue, $status , $id, $assetGeneralId, $type, $name);
            }
        }
    }

    private function comparisonNoDictionary(string $apartmentValue,string $assetValue, int $status , int $id,int $assetGeneralId , string $type , string $name)
    {
        $description = CompareMaterData::COMPARISONS_DESCRIPTION['tuong_dong'];
        $comparisonFactor = [
            'apartment_asset_id' => $id,
            'asset_general_id' => $assetGeneralId,
            'status' => $status,
            'type' => $type,
            'apartment_title' => $apartmentValue,
            'asset_title' => $assetValue,
            'description' => $description,
            'adjust_percent' => 0,
            'name' => $name,
        ];
        $comparisonFactor = new ApartmentAssetComparisonFactor($comparisonFactor);
        ApartmentAssetComparisonFactor::query()->create($comparisonFactor->attributesToArray());
    }

    private function comparisionHasDictionary( $apartmentValue, $assetValue, int $status , int $id,int $assetGeneralId  ,array $dictionaries , string $type , string $name )
    {
        $comparisonFactor = [
            'apartment_id' => $id,
            'asset_general_id' => $assetGeneralId,
            'status' => $status,
            'type' => $type,
            'apartment_title' => $apartmentValue,
            'asset_title' => $assetValue,
            'description' => CompareMaterData::COMPARISONS_DESCRIPTION['tuong_dong'],
            'adjust_percent' => 0,
            'name' => $name
        ];
        $comparisonFactor = new ApartmentAssetComparisonFactor($comparisonFactor);
        $comparisonFactorId = QueryBuilder::for($comparisonFactor)
            ->insertGetId($comparisonFactor->attributesToArray());
        foreach ($dictionaries as $dictionary) {
            if (($apartmentValue == $dictionary['apartment_title']) && ($assetValue == $dictionary['asset_title'])) {
                $comparisonFactor = [
                    'apartment_asset_id' => $id,
                    'asset_general_id' => $assetGeneralId,
                    'status' => $status,
                    'type' => $type,
                    'apartment_title' => $apartmentValue,
                    'asset_title' => $assetValue,
                    'description' => $dictionary['description'],
                    'adjust_percent' => $dictionary['adjust_percent'],
                    'name' => $name
                ];
                $comparisonFactor = new ApartmentAssetComparisonFactor($comparisonFactor);
                $comparisonFactorId = QueryBuilder::for($comparisonFactor)
                    ->updateOrInsert(['id' => $comparisonFactorId], $comparisonFactor->attributesToArray());
            }
        }
    }
    #endregion

    #region deleted assets
    private function deleteComparisonAsset(int $id)
    {
        if(ApartmentAssetHasAsset::query()->where('apartment_asset_id', $id)->exists()){
            ApartmentAssetHasAsset::query()->where('apartment_asset_id', $id)->forceDelete();
        }
        if(ApartmentAssetPic::query()->where('apartment_asset_id', $id)->where('type_id', 153)->exists()){
            ApartmentAssetPic::query()->where('apartment_asset_id', $id)->where('type_id', 153)->forceDelete();
        }
        if(ApartmentAssetComparisonFactor::query()->where('apartment_asset_id', $id)->exists()){
            ApartmentAssetComparisonFactor::query()->where('apartment_asset_id', $id)->forceDelete();
        }
        if(ApartmentAssetAdapter::query()->where('apartment_asset_id', $id)->exists()){
            ApartmentAssetAdapter::query()->where('apartment_asset_id', $id)->forceDelete();
        }
        if(ApartmentAssetPrice::query()->where('apartment_asset_id', $id)->exists()){
            ApartmentAssetPrice::query()->where('apartment_asset_id', $id)->forceDelete();
        }
        if(ApartmentAssetOtherAsset::query()->where('apartment_asset_id', $id)->exists()){
            ApartmentAssetOtherAsset::query()->where('apartment_asset_id', $id)->forceDelete();
        }
    }
    #endregion

    #region calculate price
    private function updateOrCreatePrice($id, $slug, $value)
    {
        $apartmentPriceData = ['apartment_asset_id' => $id, 'slug' => $slug, 'value' => $value];
        $apartmentCheckExists = [ 'apartment_asset_id' => $id, 'slug' => $slug];
        $apartmentPriceArr = new ApartmentAssetPrice($apartmentPriceData);
        ApartmentAssetPrice::query()->updateOrCreate($apartmentCheckExists, $apartmentPriceArr->attributesToArray());
    }

    private function calculatePrice($id)
    {
        $data = [];
        $apartmentAsset = $this->getApartmentAssetById($id);
        $assets = $apartmentAsset->assets_general;
        $adapters = $apartmentAsset->apartmentAdapter;
        $comparisons = $apartmentAsset->comparisonFactor;
        $methods = $apartmentAsset->appraisal_methods;
        $apartmentArea = $apartmentAsset->apartmentAssetProperties->area??0;
        $assetPrice = $apartmentAsset->price;
        $otherAssets = $apartmentAsset->otherAssets;
        if(isset($assets) && isset($adapters) && isset($comparisons)){
            $total = 0;
            $min = 0;
            $max = 0;
            foreach($assets as $asset){
                $adapter = $adapters->where('asset_general_id', $asset->id)->first();
                $comparison = $comparisons->where('asset_general_id', $asset->id);
                $legal = $comparison->where('type','phap_ly')->first();
                $notLegal = $comparison->whereNotIn('type',['phap_ly']);
                $assetId = $asset->id;
                $totalAmount = $asset->total_amount;
                $percent = $adapter->percent;
                $amount = $totalAmount * $percent/100;
                $area =floatval($asset->room_details[0]['area']??0);
                $unitPrice = $amount/$area;
                $legalRate = $legal->adjust_percent;
                $legalUnitPrice = $unitPrice + $unitPrice * $legalRate/100;
                $notLegalRate = $notLegal->sum('adjust_percent');
                $price = $legalUnitPrice + $legalUnitPrice * $notLegalRate/100;
                $total+= $price;
                if($min ==0){
                    $min = $price;
                }elseif($price < $min){
                    $min = $price;
                }
                if($max ==0){
                    $max = $price;
                }elseif($price > $min){
                    $max = $price;
                }

                $data[$asset->id]['asset_general_id'] = $assetId;
                $data[$asset->id]['total_amount'] = $totalAmount;
                $data[$asset->id]['amount'] = $amount;
                $data[$asset->id]['percent'] = $percent;
                $data[$asset->id]['area'] = $area;
                $data[$asset->id]['unit_price'] = $unitPrice;
                $data[$asset->id]['legal_rate'] = $legalRate;
                $data[$asset->id]['legal_unit_price'] = $legalUnitPrice;
                $data[$asset->id]['not_legal_rate'] = $notLegalRate;
                $data[$asset->id]['price'] = $price;
            }
            // $apartmentPrice = 0;
            // if(isset($methods)){
            //     $method = $methods['thong_nhat_muc_gia_chi_dan']??null;
            //     if(isset($method)){
            //         switch($method['slug_value']){
            //             case 'thap-nhat':
            //                 $apartmentPrice = $min;
            //                 break;
            //             case 'cao-nhat':
            //                 $apartmentPrice = $max;
            //                 break;
            //             default:
            //                 $apartmentPrice = $total/3;
            //         }
            //         $slug = 'apartment_asset_price';
            //         $this->updateOrCreatePrice($id, $slug, $apartmentPrice??0);
            //     }
            // }
            $slug = 'apartment_area';
            $this->updateOrCreatePrice($id, $slug, $apartmentArea??0);
            //total
            $roundTotal = 0;
            $otherPrice = 0;
            $apartmentPrice  =  0;
            if(isset($assetPrice)){
                $priceRound = $assetPrice->where('slug', 'round_total')->first();
                $roundTotal = $priceRound->value??0;
                $apartmentPrice = $assetPrice->where('slug', 'apartment_asset_price')->first()->value??0;
            }
            if(isset($otherAssets)){
                $otherPrice = $otherAssets->sum('total_price');
                $slug = 'other_asset_price';
                $this->updateOrCreatePrice($id, $slug, $otherPrice??0);
            }
            $apartmentTotal = CommonService::roundPrice($apartmentPrice, $roundTotal) * $apartmentArea;
            $slug = 'apartment_total_price';
            $this->updateOrCreatePrice($id, $slug, $apartmentTotal??0);

            $assetTotal = $apartmentTotal +  $otherPrice;
            $slug = 'total_price';
            $this->updateOrCreatePrice($id, $slug, $assetTotal??0);
        }
    }
    private function calculatePriceNew($id)
    {
        if(ApartmentAssetPrice::query()->where('apartment_asset_id', $id)->exists()){
            ApartmentAssetPrice::query()->where('apartment_asset_id', $id)->forceDelete();
        }
        $this->calculatePrice($id);
    }

    #endregion
    #region Get Data
    public function getApartmentAllStep(int $id)
    {
        // return $this->getApartmentAssetById($id);
        $select = [
            '*',
        ];
        $with = [
            'apartmentAssetProperties',
            'apartmentAssetProperties.floor',
            'law',
            'valueBaseAndApproach',
            'pic',
            'comparisonFactor',
            'price'
        ];
        $result = $this->model->query()->with($with)->where('id', $id)->first($select);
        $result->append(['status_text', 'appraisal_methods','assets_general']);
        if (isset($result->comparisonFactor) && count($result->comparisonFactor) >0){
            $comparisons= $result->comparisonFactor->where('asset_general_id' , $result->assets_general[0]['id']??-1)->where('status',1)->whereNotIn('type', 'yeu_to_khac')->toArray();
            $comparisonArr = Arr::pluck($comparisons, 'type');
            $result->comparison_factors = $comparisonArr;
        }else{
            $result->comparison_factors = EstimateAssetDefault::COMPARATION_FACTORS_APARTMENT;
        }
        $distanceMax = ValueDefault::RADIUS_SCAN;
        $assets = $result->assets_general;
        if (isset($assets) && count($assets)>0){
            $stt = 0;
            foreach ($assets as $asset){
                $assetLocation =  $asset['coordinates'];
                if ($result->coordinates != $assetLocation){
                    $calDistance =  CommonService::calAppraiseAssetDistance($result->coordinates, $assetLocation);
                    if ($calDistance > $distanceMax){
                        $distanceMax = $calDistance;
                    }
                }
                $stt ++;
            }
        }
        $map_img = $result->pic->where('type_id' , 153)->first();
        $result->map_img = $map_img->link??'';
        $result->distance_max = $distanceMax;
        return $result;
    }
    private function checkAuthorization ($id)
    {
        $check = null;
        if ($this->model->query()->where('id', $id)->exists()) {
            $user = CommonService::getUser();
            $role = $user->roles->last();
            $result = $this->model->query()->where('id', $id);
            $userId = $user->id;
            if (($role->name !== 'SUPER_ADMIN' && $role->name !== 'ROOT_ADMIN' && $role->name !== 'SUB_ADMIN' && $role->name !== 'ADMIN')) {
                $result = $result->where('created_by', $userId);
            }
            $result = $result->first();
            if (empty($result))
                $check = ['message' => 'Bạn không có quyền ở TSTĐ '. $id , 'exception' => '', 'statusCode' => 403];
        } else {
            $check = ['message' => ErrorMessage::APPRAISE_NOTEXISTS . ' ' . $id, 'exception' => '', 'statusCode' => 403];
        }
        return $check;
    }
    public function getApartmentAssetById(int $id)
    {
        $check = $this->checkAuthorization($id);
        if (!empty($check))
            return $check;
        $with = [
            'apartmentAssetProperties',
            'apartmentAssetProperties.block',
            'apartmentAssetProperties.block.rank',
            'apartmentAssetProperties.floor',
            'apartmentAssetProperties.apartment',
            'apartmentAssetProperties.furnitureQuality:id,description',
            'apartmentAssetProperties.legal:id,description',
            'apartmentAssetProperties.direction:id,description',
            'assetType:id,description,acronym',
            'project',
            'province:id,name',
            'district:id,name',
            'ward:id,name',
            'street:id,name',
            'law',
            'law.lawDocument:id,content',
            'valueBaseAndApproach',
            'valueBaseAndApproach.approach:id,name',
            'valueBaseAndApproach.methodUsed:id,name',
            'valueBaseAndApproach.priciple:id,name',
            'valueBaseAndApproach.basicProperty:id,name',
            'comparisonFactor',
            'apartmentHasAsset',
            'pic',
            'pic.picType',
            'apartmentAdapter',
            'price',
            'otherAssets',
            'createdBy:id,name',
            'certificate:id,status,sub_status',
            'realEstate'
        ];
        $select = [
            '*',
        ];
        $result = $this->model->query()->with($with)->where('id', $id)->first($select);
        if (! isset($result))
            return ['message' => 'TSTD này không tồn tại.', 'exception' => ''];
        $result->append(['status_text', 'appraisal_methods','assets_general','max_version']);
        $comparisons= $result->comparisonFactor->where('asset_general_id' , $result->assets_general[0]['id']??-1)->where('status',1)->whereNotIn('type', 'yeu_to_khac')->toArray();
        $map_img = $result->pic->where('type_id' , 153)->first();
        $comparisonArr = Arr::pluck($comparisons, 'type');
        $result->comparison_factors = $comparisonArr;
        $result->map_img = $map_img->link??'';
        $distanceMax = ValueDefault::RADIUS_SCAN;
        $assets = $result->assets_general;
        $utilities = CommonService::getUtilities();

        if (isset($assets) && count($assets)>0){
            for ($i =0; $i<count($assets); $i++){
                $asset = $assets[$i];
                $assetLocation =  $asset['coordinates'];
                if ($result->coordinates != $assetLocation){
                    $calDistance =  CommonService::calAppraiseAssetDistance($result->coordinates, $assetLocation);
                    if ($calDistance > $distanceMax){
                        $distanceMax = $calDistance;
                    }
                }
                // if (isset($asset->apartment_specification))
                // {
                //     $utilityDesc = [];
                //     $assetUti = $asset->apartment_specification['utilities']??null;
                //     if (isset($assetUti) && ! empty($assetUti)){
                //         foreach($assetUti as $uti){
                //             $des = $utilities->where('acronym', 'ilike', strval($uti))->first();
                //             if (isset($des)){
                //                 $utilityDesc[] = $des->description??'';
                //             }
                //         }
                //     }
                //     // $arr = json_decode(json_encode($asset->apartment_specification));
                //     // $arr->utility_description = $utilityDesc;
                //     // $asset->apartment_specification =$arr;
                // }
            }

        }
        $result->distance_max = $distanceMax;
        if (isset($result->apartmentAssetProperties))
        {
            $apartmentUti = $result->apartmentAssetProperties->utilities;
            $utilityDesc = [];
            if (isset($apartmentUti) ){
                foreach($apartmentUti as $uti){
                    $des = $utilities->where('acronym','ilike',strval($uti))->first();
                    if (isset($des)){
                        $utilityDesc[] = $des->description??'';
                    }
                }
            }
            $result->apartmentAssetProperties->utility_description = $utilityDesc;
        }

        return $result;
    }

    private function getTotalPrice(int $id)
    {
        $with = ['price','comparisonFactor'];
        $result = $this->model->query()->with($with)->where('id', $id)->first(['id', 'status', 'step']);
        return $result;
    }

    private function getAssets(int $id)
    {
        $with = [
            'comparisonFactor',
            'pic',
        ];
        $data = $this->model->query()->with($with)->where('id', $id)->first('id');
        $data->append(['assets_general']);
        $asset= $data->assets_general;
        $comparisons= $data->comparisonFactor->where('status',1)->whereNotIn('type', 'yeu_to_khac')->get();
        $comparisonFactors = Arr::pluck($comparisons, 'type');
        $pic = $data->pic->where('type_id', 153)->first();
        $result = array_merge(['assets_general' => $asset],[ 'comparison_factor' => $comparisonFactors ],['map_img' => $pic]);
        return $result;
    }

    #endregion

    #region lưu estate
    private function updateRealEstate(int $id, array $additional = []){
        $select = [
            'id',
            'appraise_asset',
            'coordinates',
            'created_at',
            'updated_at',
            'asset_type_id',
            'status',
            'created_by',
            'real_estate_id',
        ];
        $with = [
            'price' => function($query){
                $query->whereIn('slug', ['total_price', 'apartment_area', 'apartment_round_total'])
                    ->select(['id', 'apartment_asset_id', 'slug', 'value']);
            },
            'apartmentAssetProperties:id,apartment_asset_id,area'
        ];
        $data = $this->model::query()->with($with)->where('id', $id)->first($select);
        if (isset($data)){
            $total_price = 0;
            $total_area = 0;
            $round_total = 0;

            $round = $data->price->where('slug','apartment_round_total')->first();
            if (isset($round))
                $round_total = $round->value;
            $price = $data->price->where('slug','total_price')->first();
            if (isset($price))
                $total_price = CommonService::roundPrice($price->value, $round_total);

            $area = $data->apartmentAssetProperties;
            if (isset($area))
                $total_area = $area->area;

            $realEstateArr = [
                'asset_type_id' => $data->asset_type_id,
                'total_price' => $total_price,
                'total_area' => $total_area,
                'round_total' => $round_total,
                'appraise_asset' => $data->appraise_asset,
                'created_by' => $data->created_by,
                'coordinates' => $data->coordinates,
                'status' => $data->status,
            ];
            $realEstate = RealEstate::find($id);
            if (isset($realEstate)) {
                $realEstate->fill($realEstateArr);
                if (!empty($additional)) $realEstate->fill($additional);
                $realEstate->save();
            }
        }
    }
    private function createRealEstate(array $object)
    {
        $realEstateRep = new EloquentRealEstateRepository(new RealEstate());
        $data = new RealEstate($object);
        $realCreated = $realEstateRep->store($data->attributesToArray());
        $realId = $realCreated->id;
        return $realId;

    }
    #endregion
    private function createAppraiseVersion($id) {
        $apartment = $this->model->query()->with('version')->where('id', $id)->whereHas('version')->first();
        // $apartment = $this->model->query()->with('version')->where('id', $apartmentId)->first();
        $data = [];
        if (!empty($apartment)) {
            if (!empty($apartment->certificate_id)) {
                $max = $apartment->version->max('version');
                $data = [
                    'apartment_asset_id' => $id,
                    'status' => 1,
                    'version' => $max + 1
                ];
            }
        } else {
            $data = [
                'apartment_asset_id' => $id,
                'status' => 1,
                'version' => 1
            ];
        }
        if (!empty($data)) {
            ApartmentAssetVersion::query()->create($data);
        }
    }
    public function updateEstimateAssetPrice(int $id)
    {
        $select = ['id', 'coordinates', 'asset_type_id', 'created_by', 'updated_at', 'appraise_asset', 'full_address', 'project_id'];
        $with = [
                'price' => function ($q) {
                    $q->whereIn('slug', ['apartment_asset_price', 'round_total', 'total_price']);
                },
                'assetType:id,description','createdBy:id,name',
                'apartmentAssetProperties:id,apartment_asset_id,block_id,floor_id,apartment_name,area,bedroom_num,wc_num',
                'apartmentAssetProperties.block:id,name',
                'apartmentAssetProperties.floor:id,name',
                'project:id,name'
            ];
        $apartment = $this->model->query()->with($with)->where('id', $id)->first($select);
        $total =  $apartment->price->where('slug', 'total_price')->first();
        $price =  $apartment->price->where('slug', 'apartment_asset_price')->first();
        $round =  $apartment->price->where('slug', 'round_total')->first();
        $apartment->total = $total ? $total->value : 0;
        $apartment->unit_price = $price ? CommonService::roundPrice($price->value , $round ? $round->value : 0) : 0;
        return $apartment;
    }
}
