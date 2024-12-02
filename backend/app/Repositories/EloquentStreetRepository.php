<?php

namespace App\Repositories;

use App\Contracts\StreetRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\QueryBuilder;

class EloquentStreetRepository extends EloquentRepository implements StreetRepository
{
    private string $defaultSort = 'name';

    private string $allowedSorts = 'updated_at';


    public function findPaging(): LengthAwarePaginator
    {
        $perPage = (int)request()->get('limit');
        $page = (int)request()->get('page');
        $districtId = request()->get('district_id');
        $provinceId = request()->get('province_id');
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
        return QueryBuilder::for($this->model)
            ->with(['district', 'province'])
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
        if(!empty($query)) {
            return QueryBuilder::for($this->model)
                ->with(['district', 'province'])
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
            ->with(['district', 'province'])
            ->where('id', $id)->first();
    }

    /**
     * @param array $objects
     * @return int
     */
    public function createStreet(array $objects): int
    {
        return $this->model->query()->insertGetId($objects);
    }

    /**
     * @param $id
     * @param array $objects
     * @return int
     */
    public function updateStreet($id, array $objects): int
    {
        return $this->model->query()
            ->where('id', $id)
            ->update(['name' => $objects['name'],
                'district_id' => $objects['district_id'],
                'province_id' => $objects['province_id']]);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteStreet($id)
    {
        return $this->model->query()
            ->where('id', $id)
            ->delete();
    }

    public function findByName($name, $districtId)
    {
        $result = $this->model->query()
            ->where('name', '=', $name)
            ->where('district_id', '=', $districtId)
            ->first();
        if (!$result) {
            $query = 'name ilike ' . "'" . 'Đường '.$name . "'";
            $result = $this->model->query()
                ->whereRaw($query)
                ->where('district_id', '=', $districtId)
                ->first();
        }
        if (!$result) {
            $query = 'name ilike ' . "'" . 'Đường '.$name . "%'";
            $result = $this->model->query()
                ->whereRaw($query)
                ->where('district_id', '=', $districtId)
                ->first();
        }
        if (!$result) {
            $name = str_replace('đường ', '', urldecode(mb_strtolower($name)));
            $query = 'name ilike ' . "'" . $name . "'";
            $result = $this->model->query()
                ->whereRaw($query)
                ->where('district_id', '=', $districtId)
                ->first();
        }
        if (!$result) {
            $query = 'name ilike ' . "'" . $name . "%'";
            $result = $this->model->query()
                ->whereRaw($query)
                ->where('district_id', '=', $districtId)
                ->first();
        }
        if (!$result) {
            $query = 'name ilike ' . "'%" . $name . "%'";
            $result = $this->model->query()
                ->whereRaw($query)
                ->where('district_id', '=', $districtId)
                ->first();
        }
        return $result;
    }
}
