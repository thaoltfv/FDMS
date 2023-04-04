<?php

namespace App\Repositories;

use App\Contracts\OtherCertificateAssetRepository;
use App\Models\PersonalProperty;
use App\Models\OtherCertificateAsset;
use App\Models\OtherCertificateAssetLaw;
use App\Models\OtherCertificateAssetLawInfo;
use App\Models\OtherCertificateAssetPrice;
use App\Notifications\ActivityLog;
use App\Services\CommonService;
use Exception;
use Illuminate\Support\Facades\DB;

class EloquentOtherCertificateAssetRepository extends EloquentRepository implements OtherCertificateAssetRepository
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

    private function updatePersonalProperties($otherAssetId = null, $data)
    {
        $eloquentPersonalPropertiesRepository = new EloquentPersonalPropertiesRepository(new PersonalProperty());
        if (isset($otherAssetId)) {
            $other = $this->model->query()->find($otherAssetId, ['id', 'personal_property_id']);
            return $eloquentPersonalPropertiesRepository->updatePersonalProperties($other->personal_property_id, $data);
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
                    'step' => 1,
                ];
                $asset = $this->updatePersonalProperties($id, $personalPropertiesData);
                $object['personal_property_id'] = $asset->id;
                $otherAsset = new $this->model($object);
                $data = $this->model->query()->create($otherAsset->toArray());
                $id = $data->id;
                # activity-log tạo mới thông tin chung
                $this->CreateActivityLog($data, $otherAsset, 'create', 'tạo mới tài sản');

            } else {
                $dataUpdate = [
                    'name' => $object['name'],
                    'status' => 1,
                    'step' => 1,
                ];
                $this->updatePersonalProperties($id, $dataUpdate);
                $otherAsset = new $this->model($object);
                $this->model->query()->where('id', $id)->update($otherAsset->toArray());
                $data = $this->model->where('id', $id)->first();
                # activity-log cập nhật thông tin chung
                $this->CreateActivityLog($data, $otherAsset, 'update_data', 'cập nhật thông tin chung');
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

    public function postStep2(array $object, int $otherAssetId)
    {
        $check = $this->checkSave($otherAssetId);
        if (isset($check)) {
            return $check;
        }
        DB::beginTransaction();
        try {
            OtherCertificateAssetLaw::where('other_asset_id', $otherAssetId)->delete();
            $stt = 0;
            foreach ($object['law'] as $item) {
                unset($item['id']);
                $item['other_asset_id'] = $otherAssetId;
                $law = new OtherCertificateAssetLaw($item);
                OtherCertificateAssetLaw::query()->create($law->toArray());
                $stt++;
            }
            $this->updateStep($otherAssetId, 2);
            $dataUpdate = [
                'step' => 2,
                'status' => 1,
            ];
            $this->updatePersonalProperties($otherAssetId, $dataUpdate);
            $data = $this->model->where('id', $otherAssetId)->first();
            # activity-log pháp lý tài sản
            $this->CreateActivityLog($data, $law, 'update_data', 'cập nhật pháp lý tài sản');

            DB::commit();
            return $this->getDataAfterPost($otherAssetId);
        } catch (Exception $ex) {
            DB::rollBack();
            return ['message' => $ex->getMessage(), 'exception' => ''];
        }
    }

    public function getStep2(int $otherAssetId)
    {
        $check = $this->checkLoad($otherAssetId);
        if (isset($check))
            return $check;

        $with  = [];
        $select = [
            'id',
            'other_asset_id',
            'document_num',
            'document_date',
            'description',
            'legal_name_holder',
            'origin_of_use',
            'content',
            'duration'
        ];
        $result = OtherCertificateAssetLaw::query()->with($with)->where('other_asset_id', $otherAssetId)->select($select)->get();
        return $result;
    }

    public function postStep3(array $object, int $otherAssetId)
    {
        DB::beginTransaction();
        try {
            $check = $this->checkSave($otherAssetId);
            if (isset($check)) {
                return $check;
            }
            OtherCertificateAssetLawInfo::query()->where('other_asset_id', $otherAssetId)->delete();
            $object = $object['other_infomation'];
            $object['other_asset_id'] = $otherAssetId;
            unset($object['id']);
            $lawinfo = new OtherCertificateAssetLawInfo($object);
            OtherCertificateAssetLawInfo::query()->create($lawinfo->toArray());
            $this->updateStep($otherAssetId, 3);
            $dataUpdate = [
                'step' => 3,
                'status' => 1,
            ];
            $this->updatePersonalProperties($otherAssetId, $dataUpdate);
            $data = $this->model->where('id', $otherAssetId)->first();
            # activity-log cơ sở thẩm định
            $this->CreateActivityLog($data, $lawinfo, 'update_data', 'cập nhật cơ sở thẩm định');

            DB::commit();
            return $this->getDataAfterPost($otherAssetId);
        } catch (Exception $ex) {
            DB::rollBack();
            return ['message' => $ex->getMessage(), 'exception' => ''];
        }
    }

    public function getStep3(int $otherAssetId)
    {
        $check = $this->checkLoad($otherAssetId);
        if (isset($check))
            return $check;

        $with  = [
            'otherInfomation',
        ];
        $select = [
            'id',
        ];
        $result = $this->model->query()->with($with)->where('id', $otherAssetId)->select($select)->first();
        return $result;
    }

    public function postStep4(array $object, int $otherAssetId)
    {
        DB::beginTransaction();
        try {
            $check = $this->checkSave($otherAssetId);
            if (isset($check)) {
                return $check;
            }
            OtherCertificateAssetPrice::query()->where('other_asset_id', $otherAssetId)->delete();
            $object = $object['price'];
            unset($object['id']);
            $object['other_asset_id'] = $otherAssetId;
            $price = new OtherCertificateAssetPrice($object);
            $this->updateStep($otherAssetId, 4);
            OtherCertificateAssetPrice::query()->create($price->toArray());
            $dataUpdate = [
                'status' => 2,
                'step' => 4,
                'total_price' => $object['total_price'],
            ];
            $this->updatePersonalProperties($otherAssetId, $dataUpdate);
            $data = $this->model->where('id', $otherAssetId)->first();
            # activity-log giá trị tài sản
            $this->CreateActivityLog($data, $price, 'update_data', 'cập nhật giá trị tài sản');

            DB::commit();
            return $this->getDataAfterPost($otherAssetId);
        } catch (Exception $ex) {
            DB::rollBack();
            return ['message' => $ex->getMessage(), 'exception' => ''];
        }
    }

    public function getStep4(int $otherAssetId)
    {
        $check = $this->checkLoad($otherAssetId);
        if (isset($check))
            return $check;

        $with  = [];
        $select = [
            'id',
            'other_asset_id',
            'quantity',
            'remaining_quality',
            'unit',
            'unit_price',
            'total_price'
        ];
        $result = OtherCertificateAssetPrice::query()->with($with)->where('id', $otherAssetId)->select($select)->first();
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
            'law:id,other_asset_id,appraise_law_id,document_num,document_date,description,legal_name_holder,origin_of_use,content,duration,certifying_agency',
            'law.lawDocument:id,content',
            'otherInfomation:id,other_asset_id,other_asset_id,principle_id,basis_property_id,approach_id,method_used_id,document_description',
            'price:id,other_asset_id,quantity,unit,unit_price,remaining_quality,total_price'
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
            'law:id,other_asset_id,appraise_law_id,document_num,document_date,description,legal_name_holder,origin_of_use,content,duration,certifying_agency',
            'law.lawDocument:id,content',
            'otherInfomation:id,other_asset_id,other_asset_id,principle_id,basis_property_id,approach_id,method_used_id,document_description',
            'price:id,other_asset_id,quantity,unit,unit_price,remaining_quality,total_price'
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
