<?php

namespace App\Repositories;

use ApartmentsTableSeeder;
use App\Contracts\ProjectRepository;
use App\Models\Apartment;
use App\Models\Block;
use App\Models\Floor;
use App\Models\Project;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Calculation\Logical\Boolean;
use Spatie\QueryBuilder\QueryBuilder;

class EloquentProjectRepository extends EloquentRepository implements ProjectRepository
{
    private string $allowedSorts = 'updated_at';
    private function beforeSave(int $id, string $type)
    {
        $result = null;
        switch ($type) {
            case 'Block':
                if (Block::query()->where('project_id', $id)->doesntExist()) {
                    $result = ['message' => 'Chung cư này không tồn tại.', 'exception' => ''];
                }
                break;
            case 'Floor':
                if (Floor::query()->where('block_id', $id)->doesntExist()) {
                    $result = ['message' => 'Block này không tồn tại.', 'exception' => ''];
                }
                break;
            case 'Apartment':
                if (Apartment::query()->where('floor_id', $id)->doesntExist()) {
                    $result = ['message' => 'Tầng này không tồn tại.', 'exception' => ''];
                }
                break;
            default:
                if ($this->model->query()->where('id', $id)->doesntExist()) {
                    $result = ['message' => 'Chung cư này không tồn tại.', 'exception' => ''];
                }
        }
        return $result;
    }
    public function findPaging()
    {
        // $perPage = (int)request()->get('limit');
        // $page = (int)request()->get('page');
        // $select = [
        //     'id',
        //     'name',
        //     'province_id',
        //     'district_id',
        //     'ward_id',
        //     'street_id',
        //     'rank',
        //     'total_blocks',
        //     'total_apartments',
        //     'nb_swim_dens',
        //     'coordinates',
        //     'status',
        //     'utilities',
        //     'basement',
        //     'elevator',
        //     'created_at',
        //     'handover_year',
        // ];
        // $with = [
        //     'province:id,name',
        //     'district:id,name',
        //     'ward:id,name',
        // ];
        // $result = Project::query()
        //     ->with($with)
        //     ->select($select)
        //     ->orderByDesc('updated_at');

        // $result = $result
        //     ->forPage($page, $perPage)
        //     ->paginate($perPage);
        // return $result;
        $perPage = (int)request()->get('limit');
        $page = (int)request()->get('page');
        $search = request()->get('search');
        if (empty($search)) {
            $search = '';
        }
        $query = 'name ilike '."'%".$search."%'";
        return QueryBuilder::for($this->model)
            ->whereRaw($query)
            ->orderByDesc($this->allowedSorts )
            ->forPage($page, $perPage)
            ->paginate($perPage);
    }
    public function createProject(array $objects)
    {
        if (isset($objects)) {
            try {
                $result = [];
                DB::beginTransaction();
                $blockObjs = $objects['block'];
                // unset($objects['block']);
                $objects['status'] = true;
                // $objects['rank'] = explode(',', $objects['rank']);
                $project = new Project($objects);
                $dataProj = $this->model->query()->create($project->attributesToArray());
                if (isset($blockObjs)) {
                    foreach ($blockObjs as $block) {
                        $block['project_id'] = $dataProj->id;
                        $block['status'] = true;
                        $blockAtt = new Block($block);
                        $dataBlock = Block::query()->create($blockAtt->attributesToArray());
                        if (isset($block['floor']) && count($block['floor']) > 0) {
                            foreach ($block['floor'] as $floor) {
                                $floor['block_id'] = $dataBlock->id;
                                $floor['status'] = true;
                                $floorAtt = new Floor($floor);
                                Floor::query()->create($floorAtt->attributesToArray());
                            }
                        } else {
                            $startFloor = intval($block['first_floor']);
                            $endFloor = intval($block['last_floor']);
                            $totalFloor = intval($block['total_floors']);

                            $floorData = [];
                            for ($i=1; $i<=$totalFloor; $i++) {
                                $floorData[] = [
                                    'block_id' => $dataBlock->id,
                                    'status' => true,
                                    'name' => 'Tầng ' . $i
                                ];
                            }
                            Floor::insert($floorData);
                        }
                    }
                }
                DB::commit();
                $result = $this->getProjectById($dataProj->id);
                return $result;
            } catch (Exception $ex) {
                return ['message' => $ex->getMessage(), 'exception' => $ex];
                DB::rollBack();
            }
        }
    }
    public function getProjectById(int $id)
    {
        $select = [
            'id',
            'name',
            'province_id',
            'district_id',
            'ward_id',
            'street_id',
            'rank',
            'total_blocks',
            'total_apartments',
            'nb_swim_dens',
            'coordinates',
            'status',
            'utilities',
            'basement',
            'elevator',
            'handover_year',
        ];
        $with = [
            'province:id,name',
            'district:id,name',
            'ward:id,name',
            'block',
            'block.floor',
            'block.floor.apartment'
        ];
        return $this->model->query()->with($with)
            ->where('id', $id)
            ->first($select);
    }

    public function log_to_console($data) {
        $output = json_encode($data);
    
        echo "<script>console.log('{$output}' );</script>";
    }
    public function updateProject(int $id, array $objects)
    {
        $check = $this->beforeSave($id, 'Project');
        if (isset($check))
            return $check;
        if (isset($objects)) {
            try {
                $result = [];
                DB::beginTransaction();
                $blockObjs = $objects['block'];
                // $this->log_to_console($objects);
                // $objects['rank'] = explode(',', $objects['rank']);
                $projectAtt = new Project($objects);
                $this->model->find($id)->update($projectAtt->attributesToArray());
                if (isset($blockObjs)) {
                    foreach ($blockObjs as $block) {
                        $block['project_id'] = $id;
                        $blockAtt = new Block($block);
                        if(isset($block['id']))
                            $blockCheck = ['id' => $block['id']];
                        else
                            $blockCheck = ['project_id' => $id, 'name' => $blockAtt->name];

                        $dataBlock = Block::query()->updateOrCreate($blockCheck, $blockAtt->attributesToArray());
                        if (isset($block['floor']) && count($block['floor']) > 0) {
                            foreach ($block['floor'] as $floor) {
                                $floor['block_id'] = $floor['block_id']??$dataBlock->id;
                                $floorAtt = new Floor($floor);
                                if(isset($floor['id']))
                                    $floorCheck = ['id' => $floor['id']];
                                else
                                    $floorCheck = ['block_id' => $id, 'name' => $floorAtt->name];

                                Floor::query()->updateOrCreate($floorCheck, $floorAtt->attributesToArray());
                            }
                        }  else {
                            $startFloor = intval($block['first_floor']);
                            $endFloor = intval($block['last_floor']);

                            $floorData = [];
                            for ($i=$startFloor; $i<=$endFloor; $i++) {
                                $floorData[] = [
                                    'block_id' => $dataBlock->id,
                                    'status' => true,
                                    'name' => $i
                                ];
                            }
                            Floor::insert($floorData);
                        }
                    }
                }
                DB::commit();
                $result = $this->getProjectById($id);
                return $result;
            } catch (Exception $ex) {
                return ['message' => $ex->getMessage(), 'exception' => $ex];
                DB::rollBack();
            }
        }
    }
    public function updateOrCreateBlock(int $projectId, array $blocks)
    {
        $check = $this->beforeSave($projectId, 'Block');
        if (isset($check))
            return $check;
        if (isset($object)) {
            try {
                $result =[];
                DB::beginTransaction();
                foreach($blocks as $block){
                    $block['project_id'] = $projectId;
                    $blockAtt = new Block($block);
                    if(isset($block['id']))
                        $blockCheck = ['id' => $block['id']];
                    else
                        $blockCheck = ['project_id' => $projectId, 'name' => $blockAtt->name];

                    Block::query()->updateOrCreate($blockCheck, $blockAtt->attributesToArray());
                }
                DB::commit();
                $result= $this->getBlockByProject($projectId);
                return $result;
            } catch (Exception $ex) {
                DB::rollBack();
                return ['message' => $ex->getMessage(), 'exception' => $ex];
            }
        }
    }
    public function updateOrCreateFloor(int $blockId, array $floors)
    {
        $check = $this->beforeSave($blockId, 'Floor');
        if (isset($check))
            return $check;
        if(isset($floors)){
            try{
                $result =[];
                DB::beginTransaction();
                foreach($floors as $floor){
                    $floor['block_id'] = $blockId;
                    $floorAtt = new Floor($floor);
                    if(isset($floor['id']))
                        $floorCheck = ['id' => $floor['id']];
                    else
                        $floorCheck = ['block_id' => $blockId, 'name' => $floorAtt->name];

                    Floor::query()->updateOrCreate($floorCheck, $floorAtt->attributesToArray());
                }
                DB::commit();
                $result = $this->getFloorByBlock($blockId);
                return $result;
            }catch(Exception $ex){
                DB::rollBack();
                return ['message' => $ex->getMessage(), 'exception' => $ex];
            }
        }
    }
    public function updateOrCreateApartment(int $floorId, array $apartments)
    {
        $check = $this->beforeSave($floorId, 'Apartment');
        if (isset($check))
            return $check;
        if(isset($apartments)){
            try{
                $result =[];
                DB::beginTransaction();
                foreach($apartments as $apartment){
                    $apartment['floor_id'] = $floorId;
                    $apartmentAtt = new Apartment($apartment);
                    if(isset($apartment['id']))
                        $apartmentCheck = ['id' => $apartment['id']];
                    else
                        $apartmentCheck = ['floor_id' => $floorId, 'name' => $apartmentAtt->name];

                    Apartment::query()->updateOrCreate($apartmentCheck, $apartmentAtt->attributesToArray());
                }
                DB::commit();
                $result = $this->getApartmentByFloor($floorId);
                return $result;
            }catch(Exception $ex){
                DB::rollBack();
                return ['message' => $ex->getMessage(), 'exception' => $ex];
            }
        }
    }
    private function getBlockByProject(int $projectId)
    {
        $with = [
        ];
        $data = Block::query()->with($with)->where('project_id', $projectId)->get();
        return $data;
    }
    private function getFloorByBlock(int $blockId)
    {
        $with = [
        ];
        $data = Floor::query()->with($with)->where('block_id', $blockId)->get();
        return $data;
    }
    private function getApartmentByFloor(int $floorId)
    {
        $data = Apartment::query()->where('floor_id', $floorId)->get();
        return $data;
    }
    public function updateStatus(int $id,bool $status,string $type)
    {
        // $status = $object['status'];
        // $type = $object['type'];
        switch($type){
            case 'project':
                if($this->model->query()->where('id', $id)->exists()){
                    $this->model->query()->where('id', $id)->update([
                        'status' => $status,
                    ]);
                    if(Block::query()->where('project_id', $id)->exists()){
                        $blocks = Block::query()->where('project_id', $id)->get('id');
                        $blockIds = Arr::pluck($blocks, 'id');
                        Block::query()->where('project_id', $id)->update([
                            'status' => $status,
                        ]);
                        if(Floor::query()->whereIn('block_id', $blockIds)->exists()){
                            $floors = Floor::query()->whereIn('block_id', $blockIds)->get('id');
                            $floorIds = Arr::pluck($floors,'id');
                            Floor::query()->whereIn('block_id', $blockIds)->update([
                                'status' => $status,
                            ]);
                            Apartment::query()->whereIn('floor_id', $floorIds)->update([
                                'status' => $status,
                            ]);
                        }
                    }
                    return ['id' => $id, 'status' => $status,'type' => $type];
                }
                break;
            case 'block':
                if(Block::query()->where('id', $id)->exists()){
                    Block::query()->where('id', $id)->update([
                        'status' => $status,
                    ]);
                    if(Floor::query()->where('block_id', $id)->exists()){
                        $floors = Floor::query()->where('block_id', $id)->get('id');
                        $floorIds = Arr::pluck($floors,'id');
                        Floor::query()->where('block_id', $id)->update([
                            'status' => $status,
                        ]);
                        Apartment::query()->whereIn('floor_id', $floorIds)->update([
                            'status' => $status,
                        ]);
                    }
                    return ['id' => $id, 'status' => $status,'type' => $type];
                }
                break;
            case 'floor':
                if(Floor::query()->where('id', $id)->exists()){
                    $floors = Floor::query()->where('id', $id)->get('id');
                    Floor::query()->where('id', $id)->update([
                        'status' => $status,
                    ]);
                    Apartment::query()->where('floor_id', $id)->update([
                        'status' => $status,
                    ]);
                    return ['id' => $id, 'status' => $status,'type' => $type];
                }
                break;
            case 'apartment':
                if(Apartment::query()->where('id', $id)->exists()){
                    Apartment::query()->where('id', $id)->update([
                        'status' => $status,
                    ]);
                    return ['id' => $id, 'status' => $status,'type' => $type];
                }
                break;
            default:
                return ['message' => 'Không tồn tại loại '. $type, 'exception' => ''];
        }
    }
    public function getAll()
    {
        $select = [
            'id',
            'name',
            'province_id',
            'district_id',
            'ward_id',
            'street_id',
            'rank',
            'total_blocks',
            'total_apartments',
            'nb_swim_dens',
            'coordinates',
            'status',
            'utilities',
            'basement',
            'elevator',
            'created_at',
            'handover_year',
        ];
        $with = [
            'province:id,name',
            'district:id,name',
            'ward:id,name',
            'street:id,name',
        ];
        return $this->model->query()->with($with)->get($select);
    }
    public function getProjectActiveById(int $id)
    {
        if($this->model->where(['id' => $id, 'status' => true])->exists()){
            $select = [
                'id',
                'name',
                'province_id',
                'district_id',
                'ward_id',
                'street_id',
                'rank',
                'total_blocks',
                'total_apartments',
                'nb_swim_dens',
                'coordinates',
                'status',
                'utilities',
                'basement',
                'elevator',
                'handover_year',
            ];
            $with = [
                'province:id,name',
                'district:id,name',
                'ward:id,name',
                'street:id,name',
                'block' => function($q){
                    $q->where('status', true);
                },
                'block.floor' => function($q){
                    $q->where('status', true);
                },
                'block.floor.apartment' => function($q){
                    $q->where('status', true);
                },
            ];
            return $this->model->query()->with($with)->where(['id' => $id, 'status' => true])->get($select);
        }else{
            return ['message' => 'Chung cư không tồn tại hoặc chưa được kích hoạt.', 'exception' => ''];
        }
    }
    public function getAllActive()
    {
        $select = [
            'id',
            'name',
            'province_id',
            'district_id',
            'ward_id',
            'street_id',
            'rank',
            'total_blocks',
            'total_apartments',
            'nb_swim_dens',
            'coordinates',
            'status',
            'utilities',
            'basement',
            'elevator',
            'handover_year',
        ];
        $with = [
            'province:id,name',
            'district:id,name',
            'ward:id,name',
            'street:id,name',
            'block',
            'block.rank:id,description,acronym',
            'block.floor',
            // 'block.floor.apartment'
        ];
        return $this->model->query()->with($with)->where('status',true)->get($select);
    }

    public function getApartmentByFloorId(int $floorId)
    {
        return Apartment::query()->where('floor_id',$floorId)->where('status',true)->get(['id','name','floor_id']);
    }

    public function getProjectByDistrictId()
    {
        $districtId = request()->get('district_id');
        $select = [
            'id',
            'name',
            'province_id',
            'district_id',
            'ward_id',
            'street_id',
            'rank',
            'total_blocks',
            'total_apartments',
            'nb_swim_dens',
            'coordinates',
            'status',
            'utilities',
            'basement',
            'elevator',
            'handover_year',
        ];
        $with = [
            'province:id,name',
            'district:id,name',
            'ward:id,name',
            'street:id,name',
            'block',
            'block.rank:id,description,acronym',
            'block.floor',
        ];
        // return \Cache::remember('project_'.$districtId, 3600, function() use($districtId, $with, $select) {
            return $this->model->query()
            ->with($with)
            ->where('status', true)
            ->where('district_id', $districtId)
            ->get($select);
        // });
    }
}
