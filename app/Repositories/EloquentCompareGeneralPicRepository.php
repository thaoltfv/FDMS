<?php

namespace App\Repositories;

use App\Contracts\CompareGeneralPicRepository;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\QueryBuilder;

class EloquentCompareGeneralPicRepository extends EloquentRepository implements CompareGeneralPicRepository
{
    private string $defaultSort = 'description';

    private string $allowedSorts = 'description';

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
        $query = 'type ilike ' . "'%" . $search . "%'";
        return QueryBuilder::for($this->model)
            ->whereRaw($query)
            ->orderBy($this->allowedSorts)
            ->forPage($page, $perPage)
            ->paginate($perPage);
    }

    /**
     * @return Builder[]|Collection
     */
    public function findAll()
    {
        return $this->model->query()->select()
            ->whereNull('link')
            ->get();
    }

    public function findImage()
    {
        return $this->model->query()->select()
            ->whereNull('deleted_at')
            ->whereNotNull('link')
            ->get();
    }

    /**
     * @param $type
     * @return Builder[]|Collection
     */
    public function findByType($type)
    {
        return $this->model->query()->where('type', $type)->orderBy('id')->get();
    }

    /**
     * @param $name
     * @return Builder|Model|object|null
     */
    public function findByName($name)
    {
        $query = 'description ilike ' . "'%" . $name . "%'";

        return $this->model->query()
            ->whereRaw($query)
            ->first();
    }

    /**
     * @param array $objects
     * @return bool
     */
    public function createGeneralPic(array $objects): bool
    {
        return $this->model->query()->insertGetId($objects);
    }

    /**
     * @param $id
     * @param array $objects
     * @return int
     */
    public function updateGeneralPic($id, array $objects): int
    {
            return $this->model->query()
                ->where('id', $id)
                ->update($objects);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteGeneralPic($id)
    {
        return $this->model->query()
            ->where('id', $id)
            ->delete();
    }
}
