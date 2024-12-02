<?php

namespace App\Repositories;

use App\Contracts\AppraiseDictionaryRepository;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\QueryBuilder;

class EloquentAppraiseDictionaryRepository extends EloquentRepository implements AppraiseDictionaryRepository
{
    private string $defaultSort = 'id';

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
        return QueryBuilder::for($this->model)
            ->where('type', $search)
            ->orderBy($this->allowedSorts)
            ->forPage($page, $perPage)
            ->paginate($perPage);
    }

    /**
     * @return Builder[]
     */
    public function findAll(): array
    {
        $dictionaries = $this->model->query()->select()->orderBy($this->defaultSort)->get();
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
        $type = mb_strtoupper($type);
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
        ->first();
    }

    /**
     * @param array $objects
     * @return bool
     */
    public function createDictionary(array $objects): bool
    {
        $objects = $this->formatCase($objects);
        return $this->model->query()->insertGetId($objects);
    }

    /**
     * @param $id
     * @param array $objects
     * @return int
     */
    public function updateDictionary($id, array $objects): int
    {
        $objects = $this->formatCase($objects);
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

    /**
     * @param $string
     * @return string
     */
    private function formatCase($objects)
    {
        if(isset($objects["appraise_title"])) {
            $objects["appraise_title"] = mb_strtoupper($objects["appraise_title"]);
        }
        if(isset($objects["asset_title"])) {
            $objects["asset_title"] = mb_strtoupper($objects["asset_title"]);
        } 
        if(isset($objects["description"])) {
            $objects["description"] = mb_strtoupper($objects["description"]);
        } 
        if(isset($objects["type"])) {
            $objects["type"] = mb_strtolower($objects["type"]);
        } 

        return $objects;
    }
}
