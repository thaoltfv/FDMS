<?php

namespace App\Repositories;

use App\Contracts\BuildingPriceRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\QueryBuilder;

class EloquentBuildingPriceRepository extends EloquentRepository implements BuildingPriceRepository
{
    private string $defaultSort = 'updated_at';

    private string $allowedSorts = 'updated_at';

    /**
     * @return LengthAwarePaginator
     */
    public function findPaging(): LengthAwarePaginator
    {
        $perPage = (int)request()->get('limit');
        $page = (int)request()->get('page');
        $province_id = (int)request()->get('province_id');
        $query = '';
        if ($province_id > 0) {
            $query = $query . ' and province_id = ' . $province_id;
        }
        return QueryBuilder::for($this->model)
            ->with([
                'categoryBuilding:id,description',
                'factoryType:id,description',
                'level:id,description',
                'structure:id,description',
                'crane:id,description',
                'aperture:id,description',
                'factoryType:id,description',
                'rate:id,description',
                'provinces:id,name',
                ])
            ->orderByDesc($this->allowedSorts)
            ->forPage($page, $perPage)
            ->paginate($perPage);
    }


    /**
     * @return Collection
     */
    public function findAll(): Collection
    {
        return $this->model->query()->select()->with(['categoryBuilding'])->orderBy($this->defaultSort)->get();
    }

    /**BuildingPriceRepository
     * @param $id
     * @return Builder|Model|object
     */
    public function findById($id)
    {
        return $this->model->query()->with(['categoryBuilding'])->where('id', $id)->first();
    }

    /**
     * @param array $objects
     * @return bool
     */
    public function createBuildingPrice(array $objects): bool
    {
        return $this->model->query()->insertGetId($objects);
    }

    /**
     * @param $id
     * @param array $objects
     * @return int
     */
    public function updateBuildingPrice($id, array $objects): int
    {
        unset($objects['category_building']);
        return $this->model->query()
            ->where('id', $id)
            ->update($objects);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteBuildingPrice($id)
    {
        return $this->model->query()
            ->where('id', $id)
            ->delete();
    }

    /**
     * @return int
     */
    public function getAverageBuildPrice(): int
    {
        $buildingCategory = request()->get('building_category');
        $level = request()->get('level');
        $rate = request()->get('rate');
        $structure = request()->get('structure');
        $crane = request()->get('crane');
        $aperture = request()->get('aperture');
        $factoryType = request()->get('factory_type');
        $query = 'building_category = '."'".$buildingCategory."'";
        if ($level > 0 && $buildingCategory == 41 ) {
            $query = $query . ' and level = ' . $level;
        } else {
            $query = $query . ' and level is null';
        }

        if ($rate > 0 && ($buildingCategory == 41 || $buildingCategory ==42)) {
            $query = $query . ' and rate = ' . $rate;
        } else {
            $query = $query . ' and rate is null';
        }

        if ($structure > 0 && $buildingCategory == 42 ) {
            $query = $query . ' and structure = ' . $structure;
        } else {
            $query = $query . ' and structure is null';
        }

        if ($crane > 0 && $buildingCategory == 43) {
            $query = $query . ' and crane = ' . $crane;
        } else {
            $query = $query . ' and crane is null';
        }

        if ($aperture > 0 && $buildingCategory == 43) {
            $query = $query . ' and aperture = ' . $aperture;
        } else {
            $query = $query . ' and aperture is null';
        }

        if ($factoryType > 0 && $buildingCategory == 43 )  {
            $query = $query . ' and factory_type = ' . $factoryType;
        } else {
            $query = $query . ' and factory_type is null';
        }

        $result= $this->model->query()
            ->whereRaw($query)
            ->where('effect_from', '<=', Carbon::now()->format('Y-m-d'))
            ->where('effect_to', '>=', Carbon::now()->format('Y-m-d'))
            ->orWhereNull('effect_to')
            ->avg('unit_price_m2');
        return $result?(int)$result:0;
    }

    /**
     * @return int
     */
    public function getAverageBuildPriceV2($asset): int
    {
        $buildingCategory = $asset->building_type_id;
        $level = $asset->building_category_id;
        $rate = $asset->rate_id;
        $structure = $asset->structure_id;
        $crane = $asset->crane_id;
        $aperture = $asset->aperture_id;
        $factoryType = $asset->factory_type_id;
        $query = 'building_category = '."'".$buildingCategory."'";
        if ($level > 0) {
            $query = $query . ' and level = ' . $level;
        } else {
            $query = $query . ' and level is null';
        }

        if ($rate > 0) {
            $query = $query . ' and rate = ' . $rate;
        } else {
            $query = $query . ' and rate is null';
        }

        if ($structure > 0) {
            $query = $query . ' and structure = ' . $structure;
        } else {
            $query = $query . ' and structure is null';
        }

        if ($crane > 0) {
            $query = $query . ' and crane = ' . $crane;
        } else {
            $query = $query . ' and crane is null';
        }

        if ($aperture > 0) {
            $query = $query . ' and aperture = ' . $aperture;
        } else {
            $query = $query . ' and aperture is null';
        }

        if ($factoryType > 0) {
            $query = $query . ' and factory_type = ' . $factoryType;
        } else {
            $query = $query . ' and factory_type is null';
        }

        $result= $this->model->query()
            ->whereRaw($query)
            ->where('effect_from', '<=', Carbon::now()->format('Y-m-d'))
            ->where('effect_to', '>=', Carbon::now()->format('Y-m-d'))
            ->orWhereNull('effect_to')
            ->avg('unit_price_m2');

        return $result?(int)$result:0;
    }

    public function getAverageBuildEstimatePrice($object): int
    {
        $buildingCategory = $object['building_category']??null;
        $query = 'building_category = '."'".$buildingCategory."'";
        $result= $this->model->query()
            ->whereRaw($query)
            ->where('effect_from', '<=', Carbon::now()->format('Y-m-d'))
            ->where('effect_to', '>=', Carbon::now()->format('Y-m-d'))
            ->orWhereNull('effect_to')
            ->avg('unit_price_m2');
        return $result?(int)$result:0;
    }

    /**
     * @return int
     */
    public function getAverageBuildPriceV3($object): int
    {
        $buildingCategory = $object['building_type_id'];
        $level = $object['building_category_id'] ;
        $rate = $object['rate_id'];
        $structure = $object['structure_id'];
        $crane = $object['crane_id'];
        $aperture = $object['aperture_id'];
        $factoryType = $object['factory_type_id'];

        if(!isset($buildingCategory))
            return 0;
        $query = 'building_category = ' . "'" . $buildingCategory . "'";
        if ($level > 0 && $buildingCategory == 41 ) {
            $query = $query . ' and level = ' . $level;
        } else {
            $query = $query . ' and level is null';
        }

        if ($rate > 0 && ($buildingCategory == 41 || $buildingCategory ==42)) {
            $query = $query . ' and rate = ' . $rate;
        } else {
            $query = $query . ' and rate is null';
        }

        if ($structure > 0 && $buildingCategory == 42 ) {
            $query = $query . ' and structure = ' . $structure;
        } else {
            $query = $query . ' and structure is null';
        }

        if ($crane > 0 && $buildingCategory == 43) {
            $query = $query . ' and crane = ' . $crane;
        } else {
            $query = $query . ' and crane is null';
        }

        if ($aperture > 0 && $buildingCategory == 43) {
            $query = $query . ' and aperture = ' . $aperture;
        } else {
            $query = $query . ' and aperture is null';
        }

        if ($factoryType > 0 && $buildingCategory == 43 )  {
            $query = $query . ' and factory_type = ' . $factoryType;
        } else {
            $query = $query . ' and factory_type is null';
        }
        // DB::enableQueryLog();
        $result= $this->model->query()
            ->whereRaw($query)
            ->where('effect_from', '<=', Carbon::now()->format('Y-m-d'))
            ->where('effect_to', '>=', Carbon::now()->format('Y-m-d'))
            ->orWhereNull('effect_to')
            ->avg('unit_price_m2');

        //   dd( DB::getQueryLog()) ;
        return $result?(int)$result:0;
    }

    public function getPP2($object)
    {
        $buildingCategory = $object['building_type_id'];
        $level = $object['building_category_id'] ;
        $rate = $object['rate_id'];
        $structure = $object['structure_id'];
        $crane = $object['crane_id'];
        $aperture = $object['aperture_id'];
        $factoryType = $object['factory_type_id'];

        if(!isset($buildingCategory))
            return 0;
        $query = 'building_category = ' . "'" . $buildingCategory . "'";
        if ($level > 0 && $buildingCategory == 41 ) {
            $query = $query . ' and level = ' . $level;
        } else {
            $query = $query . ' and level is null';
        }

        if ($rate > 0 && ($buildingCategory == 41 || $buildingCategory ==42)) {
            $query = $query . ' and rate = ' . $rate;
        } else {
            $query = $query . ' and rate is null';
        }

        if ($structure > 0 && $buildingCategory == 42 ) {
            $query = $query . ' and structure = ' . $structure;
        } else {
            $query = $query . ' and structure is null';
        }

        if ($crane > 0 && $buildingCategory == 43) {
            $query = $query . ' and crane = ' . $crane;
        } else {
            $query = $query . ' and crane is null';
        }

        if ($aperture > 0 && $buildingCategory == 43) {
            $query = $query . ' and aperture = ' . $aperture;
        } else {
            $query = $query . ' and aperture is null';
        }

        if ($factoryType > 0 && $buildingCategory == 43 )  {
            $query = $query . ' and factory_type = ' . $factoryType;
        } else {
            $query = $query . ' and factory_type is null';
        }
        // DB::enableQueryLog();
        $result= $this->model->query()
            ->whereRaw($query)
            ->where('effect_from', '<=', Carbon::now()->format('Y-m-d'))
            ->where('effect_to', '>=', Carbon::now()->format('Y-m-d'))
            ->orWhereNull('effect_to')
            ->orderByDesc('updated_at')
            ->first();

        return $result;
    }
}
