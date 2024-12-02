<?php

namespace App\Repositories;

use App\Contracts\ProvinceRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\QueryBuilder;

class EloquentProvinceRepository extends EloquentRepository implements ProvinceRepository
{
    private string $defaultSort = 'name';

    private string $allowedSorts = 'updated_at';

    /**
     * @return LengthAwarePaginator
     */
    public function findPaging(): LengthAwarePaginator
    {
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

    /**
     * @return Builder[]|Collection
     */
    public function findAll()
    {
        return $this->model->query() ->select()->orderByDesc($this->defaultSort)->get();
    }

    /**
     * @param $id
     * @return Builder|Model|object
     */
    public function findById($id)
    {
        return $this->model->query()->where('id', $id)->first();
    }

    /**
     * @param array $objects
     * @return bool
     */
    public function createProvince(array $objects): bool
    {
        return $this->model->query()->insertGetId($objects);
    }

    /**
     * @param $id
     * @param array $objects
     * @return int
     */
    public function updateProvince($id, array $objects): int
    {
        return $this->model->query()
            ->where('id', $id)
            ->update(['name' => $objects['name']]);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteProvince($id)
    {
        return $this->model->query()
            ->where('id', $id)
            ->delete();
    }

    /**
     * @param $name
     * @return Builder|Model|object|null
     */
    public function findByName($name)
    {
        $query = 'name ilike ' . "'%" . $name . "%'";

        return $this->model->query()
            ->whereRaw($query)
            ->first();
    }

    public function getAllProvince()
    {
        $select = ['id','name'];
        $with = ['districts:id,province_id,name',
                'districts.streets:id,district_id,name',
                'districts.wards:id,district_id,name',
                'districts.streets.distances:id,street_id,name'
        ];
        // return \Cache::remember('Provinces', 3600, function() use($with,$select) {
            return $this->model->query()
                ->with($with)
                ->select($select)
                ->orderByDesc($this->defaultSort)
                ->get();
        // });
    }
}
