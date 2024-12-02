<?php

namespace App\Repositories;

use App\Contracts\TechnologyCertificateAssetRepository;
use App\Models\PersonalProperty;
use App\Models\TechnologicalLineCertificateAssetLaw;
use App\Models\TechnologicalLineCertificateAssetLawInfo;
use App\Models\TechnologicalLineCertificateAssetPrice;
use App\Models\VerhicleCertificateAssetPrice;
use App\Services\CommonService;
use Exception;
use Illuminate\Support\Facades\DB;

class EloquentTechnologyCertificateAssetRepository extends EloquentRepository implements TechnologyCertificateAssetRepository
{
    private function checkSave(int $id = null)
    {
        $result = null;
        if (isset($id))
            if (!$this->model->query()->where('id', $id)->exists()) {
                $result = ['message' => 'Không tồn tại Công nghệ ' . $id];
            }
        return $result;
    }

    private function checkLoad(int $id)
    {
        $result = null;
        if (!$this->model->query()->where('id', $id)->exists()) {
            $result = ['message' => 'Không tồn tại Công nghệ ' . $id];
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
            } else {
                $dataUpdate = [
                    'name' => $object['name'],
                    'status' => 1,
                ];
                $this->updatePersonalProperties($id, $dataUpdate);
                $verhicleAsset = new $this->model($object);
                $this->model->query()->where('id', $id)->update($verhicleAsset->toArray());
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
            TechnologicalLineCertificateAssetLaw::where('technology_asset_id', $verhicleAssetId)->delete();
            $stt = 0;
            foreach ($object['law'] as $item) {
                unset($item['id']);
                $item['technology_asset_id'] = $verhicleAssetId;
                $law = new TechnologicalLineCertificateAssetLaw($item);
                TechnologicalLineCertificateAssetLaw::query()->create($law->toArray());
                $stt++;
            }
            $this->updateStep($verhicleAssetId, 2);
            $dataUpdate = [
                'step' => 2,
                'status' => 1,
            ];
            $this->updatePersonalProperties($verhicleAssetId, $dataUpdate);
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
            'technology_asset_id',
            'document_num',
            'document_date',
            'description',
            'legal_name_holder',
            'origin_of_use',
            'content',
            'duration'
        ];
        $result = TechnologicalLineCertificateAssetLaw::query()->with($with)->where('technology_asset_id', $verhicleAssetId)->select($select)->get();
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
            TechnologicalLineCertificateAssetLawInfo::query()->where('technology_asset_id', $verhicleAssetId)->delete();
            $object = $object['other_infomation'];
            $object['technology_asset_id'] = $verhicleAssetId;
            unset($object['id']);
            $lawinfo = new TechnologicalLineCertificateAssetLawInfo($object);
            TechnologicalLineCertificateAssetLawInfo::query()->create($lawinfo->toArray());
            $this->updateStep($verhicleAssetId, 3);
            $dataUpdate = [
                'step' => 3,
                'status' => 1,
            ];
            $this->updatePersonalProperties($verhicleAssetId, $dataUpdate);

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
            TechnologicalLineCertificateAssetPrice::query()->where('technology_asset_id', $verhicleAssetId)->delete();
            $object = $object['price'];
            unset($object['id']);
            $object['technology_asset_id'] = $verhicleAssetId;
            $price = new TechnologicalLineCertificateAssetPrice($object);
            $this->updateStep($verhicleAssetId, 4);
            TechnologicalLineCertificateAssetPrice::query()->create($price->toArray());
            $dataUpdate = [
                'status' => 2,
                'step' => 4,
                'total_price' => $object['total_price'],
            ];
            $this->updatePersonalProperties($verhicleAssetId, $dataUpdate);

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
            'technology_asset_id',
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
            'law:id,technology_asset_id,appraise_law_id,document_num,document_date,description,legal_name_holder,origin_of_use,content,duration,certifying_agency',
            'law.lawDocument:id,content',
            'otherInfomation:id,technology_asset_id,principle_id,basis_property_id,approach_id,method_used_id,document_description',
            'price:id,technology_asset_id,quantity,unit,unit_price,remaining_quality,total_price'
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
            'law:id,technology_asset_id,appraise_law_id,document_num,document_date,description,legal_name_holder,origin_of_use,content,duration,certifying_agency',
            'law.lawDocument:id,content',
            'otherInfomation:id,technology_asset_id,principle_id,basis_property_id,approach_id,method_used_id,document_description',
            'price:id,technology_asset_id,quantity,unit,unit_price,remaining_quality,total_price'
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
