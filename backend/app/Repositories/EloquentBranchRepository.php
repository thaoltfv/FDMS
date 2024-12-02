<?php

namespace App\Repositories;

use App\Contracts\BranchRepository;
use App\Contracts\DistrictRepository;
use App\Models\Province;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\QueryBuilder;

class EloquentBranchRepository extends EloquentRepository implements BranchRepository
{
    private string $defaultSort = 'name';

    private string $allowedSorts = 'updated_at';

    /**
     * @return LengthAwarePaginator
     */
    public function findPaging(): LengthAwarePaginator
    {
        $perPage = (int) request()->get('limit');
        $page = (int) request()->get('page');
        $search = request()->get('search');
        if (empty($search)) {
            $search = '';
        }
        $query = 'name ilike '."'%".$search."%'";
        return QueryBuilder::for($this->model)
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
        return $this->model->query() ->select()->orderBy($this->defaultSort)->get();
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
    public function createBranch(array $objects): bool
    {
        return $this->model->query()->insertGetId($objects);
    }

    /**
     * @param $id
     * @param array $objects
     * @return int
     */
    public function updateBranch($id, array $objects): int
    {
        return $this->model->query()
            ->where('id', $id)
            ->update($objects);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteBranch($id)
    {
        return $this->model->query()
            ->where('id', $id)
            ->delete();
    }
}
