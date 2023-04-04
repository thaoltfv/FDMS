<?php

namespace App\Repositories;

use App\Contracts\PersonalPropertiesRepository;
use App\Models\Dictionary;
use App\Models\MachineCertificateAsset;
use App\Models\PersonalProperty;
use App\Models\OtherCertificateAsset;
use App\Models\TechnologicalLineCertificateAsset;
use App\Models\VerhicleCertificateAsset;
use App\Services\CommonService;
use Illuminate\Support\Facades\DB;

class EloquentPersonalPropertiesRepository extends EloquentRepository implements PersonalPropertiesRepository
{

    public function findPaging()
    {
        $perPage = (int)request()->get('limit');
        $page = (int)request()->get('page');
        $query = request()->get('query');
        $sortField = request()->get('sortField');
        $sortOrder = request()->get('sortOrder');
        $filter = request()->get('search');
        $status = request()->get('status');
        $select = [
            'id',
            'asset_type_id',
            'name',
            'total_price',
            'status',
            'created_by',
            'created_at',
            'updated_at',
            'step',
            DB::raw("case status
                        when 1 then 'Mới'
                        when 2 then 'Đang thực hiện'
                        when 3 then 'Đang duyệt'
                        when 4 then 'Hoàn thành'
                        else 'Hủy'
                    end as status_text
            "),
        ];
        $with = [
            'assetType:id,description,type,acronym,dictionary_acronym',
            'createdBy:id,name',
            'price'
        ];
        $result = $this->model
                    ->query()
                    ->with($with);

        $user = CommonService::getUser();
        $role = $user->roles->last();
        if(($role->name == 'USER')){
            $result= $result->where('created_by', $user->id);
        }
        if(isset($status)){
            $result = $result->whereIn('status', $status);
        }
        if(isset($filter)){
            $result=$result->where(function ($q) use ($filter) {
                $q = $q->where('id', 'like',strval($filter) );
                $q = $q->orwhere('name', 'ILIKE', '%' . $filter . '%');
                $q = $q->orwhereHas('createdBy',function($has) use($filter){
                    $has->where('name', 'ILIKE', '%'. $filter . '%');
                });
            });
        }

        $result= $result->orderByDesc('updated_at');
        $result = $result->select($select);
        $result = $result
            ->forPage($page, $perPage)
            ->paginate($perPage);

        return $result;
    }
    public function updatePersonalProperties(int $id , array $data)
    {
        $personalProperties = $this->model->query()->where('id', $id)->firstOrFail();

        if(! ($personalProperties == ModelNotFoundException::class))
        {
            $personalProperties->update($data);
        }
    }
    public function createPersonalProperties(array $data)
    {
        return $this->model->query()->create($data);
    }

    public function findOneByIdAssetType()
    {
        $id = request()->get('id');
        $asset_type_id = request()->get('asset_type_id');
        if(isset($id) && isset($asset_type_id)){
            $assetType = Dictionary::query()->where('id', $asset_type_id)->first(['acronym']);
            if(isset($assetType)){
                switch($assetType->acronym){
                    case 'GTDN':
                    case 'DCCN':
                    case 'TSK':
                        $repository = new EloquentOtherCertificateAssetRepository(new OtherCertificateAsset());
                        $data = $repository->getAllByPersonalPropertyId($id);
                        break;
                    case 'MMTB':
                        $repository = new EloquentMachineCertificateAssetRepository(new MachineCertificateAsset());
                        $data = $repository->getAllByPersonalPropertyId($id);
                        break;
                    case 'PTVT':
                        $repository = new EloquentVerhicleCertificateAssetRepository(new VerhicleCertificateAsset());
                        $data = $repository->getAllByPersonalPropertyId($id);
                        break;
                    // case 'DCCN':
                    //     $repository = new EloquentTechnologyCertificateAssetRepository(new TechnologicalLineCertificateAsset());
                    //     $data = $repository->getAll($id);
                    //     break;
                    default:
                        $data = ['message' => 'Loại tài sản này chưa triển khai ', 'exception' => ''];
                }
            }else{
                $data = ['message' => 'Không tìm được loại tài sản '. $asset_type_id, 'exception' => ''];
            }
        }else{
            $data = ['message' => 'Vui lòng chọn id và loại tài sản cần tìm', 'exception' => ''];
        }
        return $data;
    }
    public function updateStatus(int $id, int $status)
    {
        DB::transaction( function() use ($id , $status){
            $personalProperties = $this->model->query()->where('id', $id)->first();
            $dataUpdate = ['status' => $status];
            switch($personalProperties->assetType->acronym){
                case 'GTDN':
                case 'DTDN':
                case 'TSK':
                    OtherCertificateAsset::query()->where(['personal_property_id' => $id])->update($dataUpdate);
                    break;
                case 'MMTB':
                    MachineCertificateAsset::query()->where(['personal_property_id' => $id])->update($dataUpdate);
                    break;
                case 'PTVT':
                    VerhicleCertificateAsset::query()->where(['personal_property_id' => $id])->update($dataUpdate);
                    break;
                default:
            }
            $personalProperties->update($dataUpdate);
        });
    }
    public function updateStatusV2(int $id, int $status, int $subStatus)
    {
        DB::transaction( function() use ($id , $status, $subStatus){
            $personalProperties = $this->model->query()->where('id', $id)->first();
            $dataUpdate = ['status' => $status, 'sub_status' => $subStatus];
            switch($personalProperties->assetType->acronym){
                case 'GTDN':
                case 'DTDN':
                case 'TSK':
                    OtherCertificateAsset::query()->where(['personal_property_id' => $id])->update($dataUpdate);
                    break;
                case 'MMTB':
                    MachineCertificateAsset::query()->where(['personal_property_id' => $id])->update($dataUpdate);
                    break;
                case 'PTVT':
                    VerhicleCertificateAsset::query()->where(['personal_property_id' => $id])->update($dataUpdate);
                    break;
                default:
            }
            $personalProperties->update($dataUpdate);
        });
    }
}
