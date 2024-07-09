<?php

namespace App\Repositories;

use App\Contracts\CustomerGroupSecondRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Spatie\QueryBuilder\QueryBuilder;

class EloquentCustomerGroupSecondRepository extends EloquentRepository implements CustomerGroupSecondRepository
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
        $firstId = (int)request()->get('first_id');
        $search = request()->get('search');
        if (empty($search)) {
            $search = '';
        }

        $query = 'name ilike ' . "'%%" . strtolower($search) . "%%'";
        if ($firstId > 0) {
            $query = $query . ' and first_id = ' . $firstId;
        }
        return QueryBuilder::for($this->model)
            ->with('firstGroup')
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
        $firstId = (int)request()->get('first_id');
        if ($firstId > 0) {
            return QueryBuilder::for($this->model)
                ->with('firstGroup')
                ->where('first_id', '=', $firstId)
                ->orderByDesc($this->defaultSort)
                ->get();
        } else {
            return QueryBuilder::for($this->model)
                ->with('firstGroup')
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
            ->with('firstGroup')
            ->where('id', $id)->first();
    }

    /**
     * @param array $objects
     * @return bool
     */
    public function createCGS(array $objects): bool
    {
        return $this->model->query()->insertGetId($objects);
    }

    /**
     * @param $id
     * @param array $objects
     * @return int
     */
    public function updateCGS($id, array $objects): int
    {
        return $this->model->query()
            ->where('id', $id)
            ->update([
                'name' => $objects['name'],
                'first_id' => $objects['first_id']
            ]);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteCGS($id)
    {
        return $this->model->query()
            ->where('id', $id)
            ->delete();
    }

    /**
     * @param $name
     * @param $firstId
     * @return Builder|Model|object|null
     */
    public function findByName($name, $firstId)
    {
        $query = 'name ilike ' . "'%" . $name . "%'";

        return $this->model->query()
            ->whereRaw($query)
            ->where('first_id', '=', $firstId)
            ->first();
    }

    public function findAllByFirstGroup()
    {
        $firstId = (int)request()->get('first_id');

        $select = ['id', 'name'];
        $with = [
            'thirdGroups:id,second_id,name',
            'thirdGroups.fourthGroups:id,second_id,name,third_id'
        ];
        // return \Cache::remember('District'.$firstId, 3600, function() use($firstId,$with,$select) {
        return $this->model->query()
            ->with($with)
            ->where('first_id', $firstId)
            ->select($select)
            ->get();
        // });

    }
}
