<?php

namespace App\Repositories;

use App\Contracts\MachineCertificateAssetRepository;
use App\Models\MachineCertificateAsset;
use App\Models\MachineCertificateAssetLaw;
use App\Models\MachineCertificateAssetLawInfo;
use App\Models\MachineCertificateAssetPrice;
use App\Models\PersonalProperty;
use App\Notifications\ActivityLog;
use App\Services\CommonService;
use Exception;
use Illuminate\Support\Facades\DB;

class EloquentMachineCertificateAssetRepository extends EloquentRepository implements MachineCertificateAssetRepository
{
    use ActivityLog;

    private function checkSave(int $id = null)
    {
        $result = null;
        if (isset($id))
            if (!$this->model->query()->where('id', $id)->exists()) {
                $result = ['message' => 'Không tồn tại Tài sản khác ' . $id];
            }
        return $result;
    }

    private function checkLoad(int $id)
    {
        $result = null;
        if (!$this->model->query()->where('id', $id)->exists()) {
            $result = ['message' => 'Không tồn tại Tài sản khác ' . $id];
        }
        return $result;
    }

    private function updatePersonalProperties($machineAssetId = null, $data)
    {
        $eloquentPersonalPropertiesRepository = new EloquentPersonalPropertiesRepository(new PersonalProperty());
        if (isset($machineAssetId)) {
            $machine = $this->model->query()->find($machineAssetId, ['id', 'personal_property_id']);
            return $eloquentPersonalPropertiesRepository->updatePersonalProperties($machine->personal_property_id, $data);
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
                    'step' => 1,
                    'status' => 1,
                ];
                $asset = $this->updatePersonalProperties($id, $personalPropertiesData);
                $object['personal_property_id'] = $asset->id;
                $machineAsset = new MachineCertificateAsset($object);
                $data = $this->model->query()->create($machineAsset->toArray());
                $id = $data->id;
                # activity-log tạo mới thông tin chung
                $this->CreateActivityLog($data, $machineAsset, 'create', 'tạo mới tài sản');
            } else {
                $dataUpdate = [
                    'name' => $object['name'],
                    'step' => 1,
                    'status' => 1,
                ];
                $this->updatePersonalProperties($id, $dataUpdate);
                $machineAsset = new MachineCertificateAsset($object);
                $this->model->query()->where('id', $id)->update($machineAsset->toArray());
                # activity-log cập nhật thông tin chung
                $data = $this->model->where('id', $id)->first();
                $this->CreateActivityLog($data, $machineAsset, 'update_data', 'cập nhật thông tin chung');
            }
            $this->updateStep($id,1);
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

    public function postStep2(array $object, int $machineAssetId)
    {
        $check = $this->checkSave($machineAssetId);
        if (isset($check)) {
            return $check;
        }
        DB::beginTransaction();
        try {
            MachineCertificateAssetLaw::where('machine_asset_id', $machineAssetId)->delete();
            $stt = 0;
            foreach ($object['law'] as $item) {
                unset($item['id']);
                $item['machine_asset_id'] = $machineAssetId;
                $law = new MachineCertificateAssetLaw($item);
                MachineCertificateAssetLaw::query()->create($law->toArray());
                $stt++;
            }
            $this->updateStep($machineAssetId, 2);
            $dataUpdate = [
                'step' => 2,
                'status' => 1,
            ];
            $this->updatePersonalProperties($machineAssetId, $dataUpdate);
            # activity-log pháp lý tài sản
            $data = $this->model->where('id', $machineAssetId)->first();
            $this->CreateActivityLog($data, $law, 'update_data', 'cập nhật pháp lý tài sản');

            DB::commit();
            return $this->getDataAfterPost($machineAssetId);
        } catch (Exception $ex) {
            DB::rollBack();
            return ['message' => $ex->getMessage(), 'exception' => ''];
        }
    }

    public function getStep2(int $machineAssetId)
    {
        $check = $this->checkLoad($machineAssetId);
        if (isset($check))
            return $check;

        $with  = [];
        $select = [
            'id',
            'machine_asset_id',
            'document_num',
            'document_date',
            'description',
            'legal_name_holder',
            'origin_of_use',
            'content',
            'duration'
        ];
        $result = MachineCertificateAssetLaw::query()->with($with)->where('machine_asset_id', $machineAssetId)->select($select)->get();
        return $result;
    }

    public function postStep3(array $object, int $machineAssetId)
    {
        DB::beginTransaction();
        try {
            $check = $this->checkSave($machineAssetId);
            if (isset($check)) {
                return $check;
            }
            MachineCertificateAssetLawInfo::query()->where('machine_asset_id', $machineAssetId)->delete();
            $object = $object['other_infomation'];
            $object['machine_asset_id'] = $machineAssetId;
            unset($object['id']);
            $lawinfo = new MachineCertificateAssetLawInfo($object);
            MachineCertificateAssetLawInfo::query()->create($lawinfo->toArray());
            $this->updateStep($machineAssetId, 3);
            $dataUpdate = [
                'step' => 3,
                'status' => 1,
            ];
            $this->updatePersonalProperties($machineAssetId, $dataUpdate);
            # activity-log cơ sở thẩm định
            $data = $this->model->where('id', $machineAssetId)->first();
            $this->CreateActivityLog($data, $lawinfo, 'update_data', 'cập nhật cơ sở thẩm định');

            DB::commit();
            return $this->getDataAfterPost($machineAssetId);
        } catch (Exception $ex) {
            DB::rollBack();
            return ['message' => $ex->getMessage(), 'exception' => ''];
        }
    }

    public function getStep3(int $machineAssetId)
    {
        $check = $this->checkLoad($machineAssetId);
        if (isset($check))
            return $check;

        $with  = [
            'otherInfomation',
        ];
        $select = [
            'id',
        ];
        $result = $this->model->query()->with($with)->where('id', $machineAssetId)->select($select)->first();
        return $result;
    }

    public function postStep4(array $object, int $machineAssetId)
    {
        DB::beginTransaction();
        try {
            $check = $this->checkSave($machineAssetId);
            if (isset($check)) {
                return $check;
            }
            MachineCertificateAssetPrice::query()->where('machine_asset_id', $machineAssetId)->delete();
            $object = $object['price'];
            unset($object['id']);
            $object['machine_asset_id'] = $machineAssetId;
            $price = new MachineCertificateAssetPrice($object);
            $this->updateStep($machineAssetId, 4);
            MachineCertificateAssetPrice::query()->create($price->toArray());
            $dataUpdate = [
                'step' => 4,
                'total_price' => $object['total_price'],
                'status' => 2,
            ];
            $this->updatePersonalProperties($machineAssetId, $dataUpdate);
            # activity-log giá trị tài sản
            $data = $this->model->where('id', $machineAssetId)->first();
            $this->CreateActivityLog($data, $price, 'update_data', 'cập nhật giá trị tài sản');

            DB::commit();
            return $this->getDataAfterPost($machineAssetId);
        } catch (Exception $ex) {
            DB::rollBack();
            return ['message' => $ex->getMessage(), 'exception' => ''];
        }
    }

    public function getStep4(int $machineAssetId)
    {
        $check = $this->checkLoad($machineAssetId);
        if (isset($check))
            return $check;

        $with  = [];
        $select = [
            'id',
            'machine_asset_id',
            'quantity',
            'remaining_quality',
            'unit',
            'unit_price',
            'total_price'
        ];
        $result = MachineCertificateAssetPrice::query()->with($with)->where('id', $machineAssetId)->select($select)->first();
        return $result;
    }

    public function updateStep(int $id, int $step)
    {
        $status = 1;
        if($step == 4)
            $status = 2;
        $dataUpdate = ['step' => $step , 'status' => $status];
        $this->model->query()->where('id', $id)->update($dataUpdate);
    }
    public function getAll(int $id)
    {
        $check = $this->checkLoad($id);
        if (isset($check))
            return $check;

        $with = [
            'assetType:id,description',
            'law:id,machine_asset_id,appraise_law_id,document_num,document_date,description,legal_name_holder,origin_of_use,content,duration,certifying_agency',
            'law.lawDocument:id,content',
            'otherInfomation:id,machine_asset_id,principle_id,basis_property_id,approach_id,method_used_id,document_description',
            'price:id,machine_asset_id,quantity,unit,unit_price,remaining_quality,total_price',
            'manufacturer:id,description',
            'manufacturerCountry:id,description',
            'fuel:id,description',
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
            'law:id,machine_asset_id,appraise_law_id,document_num,document_date,description,legal_name_holder,origin_of_use,content,duration,certifying_agency',
            'law.lawDocument:id,content',
            'otherInfomation:id,machine_asset_id,principle_id,basis_property_id,approach_id,method_used_id,document_description',
            'price:id,machine_asset_id,quantity,unit,unit_price,remaining_quality,total_price',
            'manufacturer:id,description',
            'manufacturerCountry:id,description',
            'fuel:id,description',
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
