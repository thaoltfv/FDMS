<?php

namespace App\Repositories;

use App\Contracts\DistrictRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Spatie\QueryBuilder\QueryBuilder;

class EloquentDistrictRepository extends EloquentRepository implements DistrictRepository
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
        $provinceId = (int)request()->get('province_id');
        $search = request()->get('search');
        if (empty($search)) {
            $search = '';
        }

        $query = 'name ilike ' . "'%%" . strtolower($search) . "%%'";
        if ($provinceId > 0) {
            $query = $query . ' and province_id = ' . $provinceId;
        }
        return QueryBuilder::for($this->model)
            ->with('province')
            ->whereRaw($query)
            ->orderByDesc($this->allowedSorts)
            ->forPage($page, $perPage)
            ->paginate($perPage);
    }


    /**
     * @return Collection
     */
    public function findAll(): Collection
    {
        $provinceId = (int)request()->get('province_id');
        if ($provinceId > 0) {
            return QueryBuilder::for($this->model)
                ->with('province')
                ->where('province_id', '=', $provinceId)
                ->orderByDesc($this->defaultSort)
                ->get();
        } else {
            return QueryBuilder::for($this->model)
                ->with('province')
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
            ->with('province')
            ->where('id', $id)->first();
    }

    /**
     * @param array $objects
     * @return bool
     */
    public function createDistrict(array $objects): bool
    {
        return $this->model->query()->insertGetId($objects);
    }

    /**
     * @param $id
     * @param array $objects
     * @return int
     */
    public function updateDistrict($id, array $objects): int
    {
        return $this->model->query()
            ->where('id', $id)
            ->update(['name' => $objects['name'],
                'province_id' => $objects['province_id']]);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteDistrict($id)
    {
        return $this->model->query()
            ->where('id', $id)
            ->delete();

    }

    /**
     * @param $name
     * @param $provinceId
     * @return Builder|Model|object|null
     */
    public function findByName($name, $provinceId)
    {
        $query = 'name ilike ' . "'%" . $name . "%'";

        return $this->model->query()
            ->whereRaw($query)
            ->where('province_id','=',$provinceId)
            ->first();
    }

    public function findAllByProvince()
    {
        $provinceId = (int)request()->get('province_id');

        $select = ['id','name'];
        $with = ['streets:id,district_id,name',
                'wards:id,district_id,name',
                'streets.distances:id,district_id,name,street_id'
        ];
        // return \Cache::remember('District'.$provinceId, 3600, function() use($provinceId,$with,$select) {
            return $this->model->query()
            ->with($with)
            ->where('province_id',$provinceId)
            ->select($select)
            ->get();
        // });

    }
}
