<?php

namespace App\Repositories;

use App\Contracts\DictionaryRepository;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\QueryBuilder;

class EloquentDictionaryRepository extends EloquentRepository implements DictionaryRepository
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
        $status = request()->get('status');
        $searchStatus = [1];
        if (empty($search)) {
            $search = '';
        }
        // if (!empty($status)) {
        //     $searchStatus = [0, 1];
        // }
        return QueryBuilder::for($this->model)
            ->where('type', $search)
            ->whereIn('status', $searchStatus)
            ->orderBy($this->allowedSorts)
            ->forPage($page, $perPage)
            ->paginate($perPage);
    }

    /**
     * @return Builder[]
     */
    public function findAll(): array
    {
        $dictionaries = $this->model->query()->select()->where('status', '=',1)->orderBy($this->defaultSort)->get();
        $result = [];
        foreach ($dictionaries as $dictionary => $value) {
            $result[mb_strtolower($value->type)][] = $value;
        }
        return $result;
    }

    /**
     * @param $type
     * @return Builder[]|Collection
     */
    public function findByType($type)
    {
        return $this->model->query()->where('type', $type)->where('status', '=',1)->orderBy('id')->get();
    }

    /**
     * @param $type
     * @return Builder[]|Collection
     */
    public function findAllByType($type)
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

    public function findDictionary($type,$name)
    {
        return $this->model->query()
        ->where('type', '=', $type)
        ->where('description', '=', $name)
        ->where('status', '=',1)
        ->first();
    }

    /**
     * @param array $objects
     * @return bool
     */
    public function createDictionary(array $objects): bool
    {
        return $this->model->query()->insertGetId($objects);
    }

    /**
     * @param $id
     * @param array $objects
     * @return int
     */
    public function updateDictionary($id, array $objects): int
    {
        return $this->model->query()
            ->where('id', $id)
            ->update($objects);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteDictionary($id)
    {
        return $this->model->query()
            ->where('id', $id)
            ->delete();
    }
}
