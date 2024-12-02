<?php

namespace App\Repositories;

use App\Contracts\AppraiserCompanyRepository;
use App\Contracts\AppraiserRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\QueryBuilder;

class EloquentAppraiserCompanyRepository extends EloquentRepository implements AppraiserCompanyRepository
{
    private string $defaultSort = 'id';

    private string $allowedSorts = 'id';

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
            ->with('appraiser')
            ->orderByDesc($this->allowedSorts)
            ->forPage($page, $perPage)
            ->paginate($perPage);
    }

    /**
     * @return Builder[]|Collection
     */
    public function findAll()
    {
        return $this->model->query()
            ->with('appraiser')
            ->select()
            ->orderByDesc($this->defaultSort)
            ->get();
    }

    /**
     * @param $id
     * @return Builder|Model|object
     */
    public function findById($id)
    {
        return $this->model->query()->where('id', $id)->with('appraiser')->first();
    }

    /**
     * @param array $objects
     * @return bool
     */
    public function createAppraiserCompany(array $objects): bool
    {
        return $this->model->query()->insertGetId($objects);
    }

    /**
     * @param $id
     * @param array $objects
     * @return int
     */
    public function updateAppraiserCompany($id, array $objects): int
    {
        return $this->model->query()
            ->where('id', $id)
            ->update($objects);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteAppraiserCompany($id)
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

    public function getOneAppraiserCompany()
    {
        return $this->model->query()
            ->with('appraiser')
            ->select()
            ->orderByDesc($this->defaultSort)
            ->first();
    }
    public function getCompany()
    {
        return $this->model->query()
            ->select()
            ->orderByDesc($this->defaultSort)
            ->first();
    }
}
