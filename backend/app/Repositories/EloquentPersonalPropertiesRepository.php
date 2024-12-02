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
        if((($role->name !== 'SUPER_ADMIN' && $role->name !== 'ROOT_ADMIN' && $role->name !== 'SUB_ADMIN' && $role->name !== 'ADMIN'))){
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

    public function exportPersonalProperty()
    {
        // $assetTypeId = request()->get('asset_type_id');
        $status = request()->get('status');
        $createdBy = request()->get('created_by');
        $fromDate = request()->get('fromDate');
        $toDate = request()->get('toDate');
        $where = [];
        // dd('$status',$status);

        $select = [
            'id',
            'name',
            'asset_type_id',
            'status',
            'step',
            'total_price',
            'created_at',
            'created_by',
        ];
        $with = [
            'assetType:id,description',
            'createdBy:id,name',
        ];
        if ($status){
            $status = explode(",", $status);
            $result = $this->model->query()->with($with)->where($where)->whereIn('status', $status)->select($select);
        } else {
            $result = $this->model->query()->with($with)->where($where)->select($select);
        }
        
        // if (!empty($assetTypeId)) {
        //     $result->whereHas('assetType', function ($has) use ($assetTypeId) {
        //         $has->where('id', $assetTypeId);
        //     });
        // }
        // if (!empty($status)) {
        //     $result->WhereHas('status', function ($has) use ($status) {
        //         $has->where('status', 'ilike' , '%' . $status . '%');
        //     });
        // }

        if (!empty($createdBy)) {
            $result->WhereHas('createdBy', function ($has) use ($createdBy) {
                $has->where('name', 'ilike' , '%' . $createdBy . '%');
            });
        }
        if (!empty($fromDate) && $fromDate != 'Invalid date') {
            $result->whereRaw("created_at >= to_date('$fromDate', 'dd/MM/yyyy') ");
        }
        if (!empty($toDate) && $toDate != 'Invalid date') {
            $result->whereRaw("created_at <= to_date('$toDate', 'dd/MM/yyyy') + '1 day'::interval");
        }
        // dd($result->limit(5)->get()->append('total_construction_base')->toArray());
        $ketqua = $result->get();
        foreach ($ketqua as $k) {
            if ($k->asset_type_id  == 181){
                $stringSql = sprintf(
                    "select c2.unit, c2.quantity, c2.unit_price from machine_certificate_assets c1
                    join machine_certificate_asset_prices c2 on c2.machine_asset_id = c1.id
                    where c1.personal_property_id = :personal_property_id and c2.deleted_at is null"
                );
                DB::enableQueryLog();
                $data = DB::select($stringSql, [
                    ":personal_property_id" => $k->id,
                ]);
                // dd('máy móc thiết bị', $data);
                if (count($data) > 0){
                    $k->unit = $data[0]->unit;
                    $k->quantity = $data[0]->quantity;
                    $k->unit_price = $data[0]->unit_price;
                }
                
            } else if ($k->asset_type_id  == 182) {
                $stringSql = sprintf(
                    "select c2.unit, c2.quantity, c2.unit_price from verhicle_certificate_assets c1
                    join verhicle_certificate_asset_prices c2 on c2.verhicle_asset_id = c1.id
                    where c1.personal_property_id = :personal_property_id and c2.deleted_at is null"
                );
                DB::enableQueryLog();
                $data = DB::select($stringSql, [
                    ":personal_property_id" => $k->id,
                ]);
                // dd('phương tiện vận tải', $data);
                if (count($data) > 0){
                    $k->unit = $data[0]->unit;
                    $k->quantity = $data[0]->quantity;
                    $k->unit_price = $data[0]->unit_price;
                }
            } else {
                $stringSql = sprintf(
                    "select c2.unit, c2.quantity, c2.unit_price from other_certificate_assets c1
                    join other_certificate_asset_prices c2 on c2.other_asset_id = c1.id
                    where c1.personal_property_id = :personal_property_id and c2.deleted_at is null"
                );
                DB::enableQueryLog();
                $data = DB::select($stringSql, [
                    ":personal_property_id" => $k->id,
                ]);
                // dd('khác', $data);
                if (count($data) > 0){
                    $k->unit = $data[0]->unit;
                    $k->quantity = $data[0]->quantity;
                    $k->unit_price = $data[0]->unit_price;
                }
            }
        }
        return $ketqua;

    }
}
