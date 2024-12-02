<?php

namespace App\Repositories;

use App\Contracts\VerhicleCertificateAssetRepository;
use App\Models\PersonalProperty;
use App\Models\VerhicleCertificateAssetLaw;
use App\Models\VerhicleCertificateAssetLawInfo;
use App\Models\VerhicleCertificateAssetPrice;
use App\Notifications\ActivityLog;
use App\Services\CommonService;
use Exception;
use Illuminate\Support\Facades\DB;

class EloquentVerhicleCertificateAssetRepository extends EloquentRepository implements VerhicleCertificateAssetRepository
{
    use ActivityLog;
    private function checkSave(int $id = null)
    {
        $result = null;
        if (isset($id))
            if (!$this->model->query()->where('id', $id)->exists()) {
                $result = ['message' => 'Không tồn tại Phương tiện vận tải ' . $id];
            }
        return $result;
    }

    private function checkLoad(int $id)
    {
        $result = null;
        if (!$this->model->query()->where('id', $id)->exists()) {
            $result = ['message' => 'Không tồn tại Phương tiện vận tải ' . $id];
        }
        return $result;
    }

    private function updatePersonalProperties($verhicleAssetId = null, $data)
    {
        $eloquentPersonalPropertiesRepository = new EloquentPersonalPropertiesRepository(new PersonalProperty());
        if (isset($verhicleAssetId)) {
            $verhicle = $this->model->query()->find($verhicleAssetId, ['id', 'personal_property_id']);
            return $eloquentPersonalPropertiesRepository->updatePersonalProperties($verhicle->personal_property_id, $data);
        } else {
            return $eloquentPersonalPropertiesRepository->createPersonalProperties($data);
        }
    }
    public function postStep1(array $object, int $id = null)
    {
        $result = [];
        $check = $this->checkSave($id);
        if (isset($check))
            return $check;
        DB::beginTransaction();
        try {
            if (!isset($id)) {
                $user = CommonService::getUser();
                // $asset_type_id = Dictionary::query()->where(['type' => 'NHOM_TAI_SAN', 'acronym' => 'DS'])->first();
                $personalPropertiesData = [
                    'asset_type_id' => $object['asset_type_id'],
                    'created_by' => $user->id,
                    'name' => $object['name'],
                    'status' => 1,
                ];
                $asset = $this->updatePersonalProperties($id, $personalPropertiesData);
                $object['personal_property_id'] = $asset->id;
                $verhicleAsset = new $this->model($object);
                $data = $this->model->query()->create($verhicleAsset->toArray());
                $id = $data->id;
                # activity-log tạo mới
                $this->CreateActivityLog($data, $verhicleAsset, 'create', 'tạo mới tài sản');

            } else {
                $dataUpdate = [
                    'name' => $object['name'],
                    'status' => 1,
                ];
                $this->updatePersonalProperties($id, $dataUpdate);
                $verhicleAsset = new $this->model($object);
                $this->model->query()->where('id', $id)->update($verhicleAsset->toArray());
                $data = $this->model->where('id', $id)->first();
                # activity-log cập nhật thông tin chung
                $this->CreateActivityLog($data, $verhicleAsset, 'update_data', 'cập nhật thông tin chung');

            }
            DB::commit();
            return $this->getDataAfterPost($id);
        } catch (Exception $ex) {
            DB::rollBack();
            return ['message' => $ex->getMessage(), 'exception' => ''];
        }
    }

    public function getStep1(int $id)
    {
        $check = $this->checkLoad($id);
        if (isset($check))
            return $check;

        $with  = [
            'assetType:id,description',
        ];
        $select = [
            'id', 'asset_type_id', 'name', 'description'
        ];
        $result = $this->model->query()->with($with)->where('id', $id)->select($select)->firstOrFail();
        return $result;
    }

    public function postStep2(array $object, int $verhicleAssetId)
    {
        $check = $this->checkSave($verhicleAssetId);
        if (isset($check)) {
            return $check;
        }
        DB::beginTransaction();
        try {
            VerhicleCertificateAssetLaw::where('verhicle_asset_id', $verhicleAssetId)->delete();
            $stt = 0;
            foreach ($object['law'] as $item) {
                unset($item['id']);
                $item['verhicle_asset_id'] = $verhicleAssetId;
                $law = new VerhicleCertificateAssetLaw($item);
                VerhicleCertificateAssetLaw::query()->create($law->toArray());
                $stt++;
            }
            $this->updateStep($verhicleAssetId, 2);
            $dataUpdate = [
                'step' => 2,
                'status' => 1,
            ];
            $this->updatePersonalProperties($verhicleAssetId, $dataUpdate);
            $data = $this->model->where('id', $verhicleAssetId)->first();
            # activity-log pháp lý tài sản
            $this->CreateActivityLog($data, $law, 'update_data', 'cập nhật pháp lý tài sản');

            DB::commit();
            return $this->getDataAfterPost($verhicleAssetId);
        } catch (Exception $ex) {
            DB::rollBack();
            return ['message' => $ex->getMessage(), 'exception' => ''];
        }
    }

    public function getStep2(int $verhicleAssetId)
    {
        $check = $this->checkLoad($verhicleAssetId);
        if (isset($check))
            return $check;

        $with  = [];
        $select = [
            'id',
            'verhicle_asset_id',
            'document_num',
            'document_date',
            'description',
            'legal_name_holder',
            'origin_of_use',
            'content',
            'duration',

        ];
        $result = VerhicleCertificateAssetLaw::query()->with($with)->where('verhicle_asset_id', $verhicleAssetId)->select($select)->get();
        return $result;
    }

    public function postStep3(array $object, int $verhicleAssetId)
    {
        DB::beginTransaction();
        try {
            $check = $this->checkSave($verhicleAssetId);
            if (isset($check)) {
                return $check;
            }
            VerhicleCertificateAssetLawInfo::query()->where('verhicle_asset_id', $verhicleAssetId)->delete();
            $object = $object['other_infomation'];
            $object['verhicle_asset_id'] = $verhicleAssetId;
            unset($object['id']);
            $lawinfo = new VerhicleCertificateAssetLawInfo($object);
            VerhicleCertificateAssetLawInfo::query()->create($lawinfo->toArray());
            $this->updateStep($verhicleAssetId, 3);
            $dataUpdate = [
                'step' => 3,
                'status' => 1,
            ];
            $this->updatePersonalProperties($verhicleAssetId, $dataUpdate);
            $data = $this->model->where('id', $verhicleAssetId)->first();
            # activity-log cơ sở thẩm định
            $this->CreateActivityLog($data, $lawinfo, 'update_data', 'cập nhật cơ sở thẩm định');

            DB::commit();
            return $this->getDataAfterPost($verhicleAssetId);
        } catch (Exception $ex) {
            DB::rollBack();
            return ['message' => $ex->getMessage(), 'exception' => ''];
        }
    }

    public function getStep3(int $verhicleAssetId)
    {
        $check = $this->checkLoad($verhicleAssetId);
        if (isset($check))
            return $check;

        $with  = [
            'otherInfomation',
        ];
        $select = [
            'id',
        ];
        $result = $this->model->query()->with($with)->where('id', $verhicleAssetId)->select($select)->first();
        return $result;
    }

    public function postStep4(array $object, int $verhicleAssetId)
    {
        DB::beginTransaction();
        try {
            $check = $this->checkSave($verhicleAssetId);
            if (isset($check)) {
                return $check;
            }
            VerhicleCertificateAssetPrice::query()->where('verhicle_asset_id', $verhicleAssetId)->delete();
            $object = $object['price'];
            unset($object['id']);
            $object['verhicle_asset_id'] = $verhicleAssetId;
            $price = new VerhicleCertificateAssetPrice($object);
            $this->updateStep($verhicleAssetId, 4);
            $data = VerhicleCertificateAssetPrice::query()->create($price->toArray());
            $dataUpdate = [
                'status' => 2,
                'step' => 4,
                'total_price' => $object['total_price'],
            ];
            $this->updatePersonalProperties($verhicleAssetId, $dataUpdate);
            $data = $this->model->where('id', $verhicleAssetId)->first();
            # activity-log giá trị tài sản
            $this->CreateActivityLog($data, $price, 'update_data', 'cập nhật giá trị tài sản');

            DB::commit();
            return $this->getDataAfterPost($verhicleAssetId);
        } catch (Exception $ex) {
            DB::rollBack();
            return ['message' => $ex->getMessage(), 'exception' => ''];
        }
    }

    public function getStep4(int $verhicleAssetId)
    {
        $check = $this->checkLoad($verhicleAssetId);
        if (isset($check))
            return $check;

        $with  = [];
        $select = [
            'id',
            'verhicle_asset_id',
            'quantity',
            'remaining_quality',
            'unit',
            'unit_price',
            'total_price'
        ];
        $result = VerhicleCertificateAssetPrice::query()->with($with)->where('id', $verhicleAssetId)->select($select)->first();
        return $result;
    }

    public function updateStep(int $id, int $step)
    {
        if($step == 4){
            $status = 2;
        }else{
            $status = 1;
        }
        $data = [
            'step' => $step,
            'status' => $status,
        ];
        $this->model->query()->where('id', $id)->update($data);
    }
    public function getAll(int $id)
    {
        $check = $this->checkLoad($id);
        if (isset($check))
            return $check;

        $with = [
            'assetType:id,description',
            'law:id,verhicle_asset_id,appraise_law_id,document_num,document_date,description,legal_name_holder,origin_of_use,content,duration,certifying_agency',
            'law.lawDocument:id,content',
            'otherInfomation:id,verhicle_asset_id,principle_id,basis_property_id,approach_id,method_used_id,document_description',
            'price:id,verhicle_asset_id,quantity,unit,unit_price,remaining_quality,total_price',
            'manufacturer:id,description',
            'manufacturerCountry:id,description',
            'fuel:id,description',
            'verhicle:id,description',
            'transport:id,description',
        ];
        $select = [
            'id',
            'asset_type_id',
            'name',
            'description',
            'status',
            'step',
            DB::raw("case status
                        when 1 then 'Mới'
                        when 2 then 'Đang thẩm định'
                        when 3 then 'Đang duyệt'
                        when 4 then 'Hoàn thành'
                        else 'Hủy'
                    end as status_text
            "),
            'transport_id',
            'vehicle_id',
            'manufacturer_id',
            'manufacturer_country_id',
            'model',
            'fuel_id',
            'manufacturer_year',
            'using_year',
        ];

        $result = $this->model->query()->with($with)->select($select)->where('id', $id)->first();
        return $result;
    }
    public function getAllByPersonalPropertyId(int $id)
    {
        // $check = $this->checkLoad($id);
        // if (isset($check))
        //     return $check;

        $with = [
            'assetType:id,description',
            'law:id,verhicle_asset_id,appraise_law_id,document_num,document_date,description,legal_name_holder,origin_of_use,content,duration,certifying_agency',
            'law.lawDocument:id,content',
            'otherInfomation:id,verhicle_asset_id,principle_id,basis_property_id,approach_id,method_used_id,document_description',
            'price:id,verhicle_asset_id,quantity,unit,unit_price,remaining_quality,total_price',
            'manufacturer:id,description',
            'manufacturerCountry:id,description',
            'fuel:id,description',
            'verhicle:id,description',
            'transport:id,description',
        ];
        $select = [
            'id',
            'asset_type_id',
            'name',
            'description',
            'status',
            'step',
            DB::raw("case status
                        when 1 then 'Mới'
                        when 2 then 'Đang thẩm định'
                        when 3 then 'Đang duyệt'
                        when 4 then 'Hoàn thành'
                        else 'Hủy'
                    end as status_text
            "),
            'transport_id',
            'vehicle_id',
            'manufacturer_id',
            'manufacturer_country_id',
            'model',
            'fuel_id',
            'manufacturer_year',
            'using_year',
        ];

        $result = $this->model->query()->with($with)->select($select)->where('personal_property_id', $id)->first();
        return $result;
    }

    private function getDataAfterPost(int $id){
        $result = $this->model->query()->where('id',$id)->first(['id','name','status','step']);
        $result->append('status_text');
        return $result;
    }
}
