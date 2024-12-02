<?php

namespace App\Repositories;

use App\Contracts\DistanceRepository;
use App\Contracts\StreetRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\QueryBuilder;

class EloquentDistanceRepository extends EloquentRepository implements DistanceRepository
{
    private string $defaultSort = 'name';

    private string $allowedSorts = 'updated_at';


    public function findPaging(): LengthAwarePaginator
    {
        $perPage = (int)request()->get('limit');
        $page = (int)request()->get('page');
        $districtId =(int) request()->get('district_id');
        $provinceId =(int) request()->get('province_id');
        $streetId = (int) request()->get('street_id');
        $search = request()->get('search');
        if (empty($search)) {
            $search = '';
        }
        $query = 'name ilike '."'%".$search."%'";
        if ($provinceId > 0) {
            $query = $query.' and province_id = '.$provinceId;
        }
        if ($districtId > 0) {
            $query = $query.' and district_id = '.$districtId;
        }
        if ($streetId > 0) {
            $query = $query.' and street_id = '.$streetId;
        }
        return QueryBuilder::for($this->model)
            ->with(['district', 'province', 'street'])
            ->whereRaw($query)
            ->orderByDesc($this->allowedSorts)
            ->forPage($page, $perPage)
            ->paginate($perPage);
    }

    /**
     * @return Builder[]|Collection
     */
    public function findAll()
    {
        $provinceId = (int) request()->get('province_id');
        $districtId = (int) request()->get('district_id');
        $streetId = (int) request()->get('street_id');
        $query = '';
        if ($provinceId > 0) {
            $query = $query.'province_id = '.$provinceId;
        }
        if ($districtId > 0) {
            if (empty($query)) {
                $query = $query.' district_id = '.$districtId;
            } else {
                $query = $query.' and district_id = '.$districtId;
            }
        }
        if ($streetId > 0) {
            if (empty($query)) {
                $query = $query.' street_id = '.$streetId;
            } else {
                $query = $query.' and street_id = '.$streetId;
            }
        }
        if(!empty($query)) {
            return QueryBuilder::for($this->model)
                ->with(['district', 'province', 'street'])
                ->whereRaw($query)
                ->orderByDesc($this->defaultSort)
                ->get();
        } else {
            return QueryBuilder::for($this->model)
                ->with(['district', 'province'])
                ->orderByDesc($this->defaultSort)
                ->get();
        }
    }

    /**
     * @param $id
     * @return Builder|Model|object
     */
    public function findById($id)
    {
        return $this->model->query()
            ->with(['district', 'province', 'street'])
            ->where('id', $id)->first();
    }

    /**
     * @param array $objects
     * @return int
     */
    public function createDistance(array $objects): int
    {
        return $this->model->query()->insertGetId($objects);
    }

    /**
     * @param $id
     * @param array $objects
     * @return int
     */
    public function updateDistance($id, array $objects): int
    {
        return $this->model->query()
            ->where('id', $id)
            ->update(['name' => $objects['name'],
                'street_id' => $objects['street_id'],
                'district_id' => $objects['district_id'],
                'province_id' => $objects['province_id']]);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteDistance($id)
    {
        return $this->model->query()
            ->where('id', $id)
            ->delete();
    }

    public function findByName($name, $streetId)
    {
        $result = $this->model->query()
            ->where('name', '=', $name)
            ->where('street_id', '=', $streetId)
            ->first();
        if (!$result) {
            $query = 'name ilike ' . "'%" . $name . "%'";
            $result = $this->model->query()
                ->whereRaw($query)
                ->where('street_id', '=', $streetId)
                ->first();
        }

        return $result;

    }
}
