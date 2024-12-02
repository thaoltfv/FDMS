<?php

namespace App\Repositories;

use App\Contracts\MigrateStatusRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\QueryBuilder;

class EloquentMigrateStatusRepository extends EloquentRepository implements MigrateStatusRepository
{

    private string $allowedSorts = 'updated_at';

    /**
     * @return LengthAwarePaginator
     */
    public function findPaging(): LengthAwarePaginator
    {
        $perPage = (int)request()->get('limit');
        $page = (int)request()->get('page');
        $type = (int)request()->get('type');

        return QueryBuilder::for($this->model)
            ->where('type','=',$type)
            ->orderByDesc($this->allowedSorts )
            ->forPage($page, $perPage)
            ->paginate($perPage);
    }

    /**
     * @return Builder[]|Collection
     */
    public function findAll()
    {
        return $this->model->query() ->select()->orderByDesc($this->allowedSorts)->get();
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
     * @return int
     */
    public function createMigrateStatus(array $objects): int
    {
        return $this->model->query()->insertGetId($objects);
    }

    /**
     * @param $id
     * @param array $objects
     * @return int
     */
    public function updateMigrateStatus($id, array $objects): int
    {
        return $this->model->query()
            ->where('id', $id)
            ->update(['status' => $objects['status']]);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteMigrateStatus($id)
    {
        return $this->model->query()
            ->where('id', $id)
            ->delete();
    }

    /**
     * @param $limit
     * @param $page
     * @return Builder|Model|object|null
     */
    public function CheckProcessIsRunning($limit, $page)
    {
        return $this->model->query()
            ->where('limit','=',$limit)
            ->where('page','=',$page)
            ->orWhere('status','=',0)
            ->orWhere('status','=',2)
            ->orWhere('status','=',4)
            ->first();
    }

    /**
     * @param $id
     * @param $status
     * @return Builder|Model|object|null
     */
    public function CheckActiveRunning($id, $status)
    {
        return $this->model->query()
            ->where('id','=',$id)
            ->where('status','=',$status)
            ->first();
    }
}
