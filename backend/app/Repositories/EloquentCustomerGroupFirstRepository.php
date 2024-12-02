<?php

namespace App\Repositories;

use App\Contracts\CustomerGroupFirstRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\QueryBuilder;

class EloquentCustomerGroupFirstRepository extends EloquentRepository implements CustomerGroupFirstRepository
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
        $query = 'name ilike ' . "'%" . $search . "%'";
        return QueryBuilder::for($this->model)
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
        return $this->model->query()->select()->orderByDesc($this->defaultSort)->get();
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
    public function createCGF(array $objects): bool
    {
        return $this->model->query()->insertGetId($objects);
    }

    /**
     * @param $id
     * @param array $objects
     * @return int
     */
    public function updateCGF($id, array $objects): int
    {
        return $this->model->query()
            ->where('id', $id)
            ->update(['name' => $objects['name']]);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteCGF($id)
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

    public function getAllFirstGroup()
    {
        $select = ['id', 'name'];
        $with = [
            'secondGroups:id,first_id,name',
            'secondGroups.thirdGroups:id,second_id,name',
            'secondGroups.thirdGroups.fourthGroups:id,third_id,name'
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
