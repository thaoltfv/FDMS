<?php

namespace App\Repositories;

use App\Contracts\AppraiseDictionaryRepository;

use App\Contracts\AppraiseOtherInformationRepository;
use App\Models\AppraiseOtherInformation;
use App\Enum\AppraiseOtherInformationEnum;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\QueryBuilder;

class EloquentAppraiseOtherInformationRepository extends EloquentRepository implements AppraiseOtherInformationRepository
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
        $query = 'type ilike ' . "'%" . $search . "%'";
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
        $search = request()->get('search');
        if (empty($search)) {
            $search = '';
        }
        $query = 'type ilike ' . "'%" . $search . "%'";
        $dictionaries =$this->model->query()
            ->where('status', true)
            ->whereRaw($query)
            ->select()
            ->orderBy($this->defaultSort)
            ->get();

        $result =[];
        foreach ($dictionaries as $dictionary => $value) {
            $result[mb_strtolower($value->type)][] = $value;
        }
        foreach(AppraiseOtherInformationEnum::DATA as $key => $value) {
            $result[mb_strtolower($key)] = $value;
        }

        return $result;
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
    public function createAppraiseOtherInformation(array $objects): bool
    {
        if(isset($objects['dictionary_acronym'])){
            $objects['dictionary_acronym'] = json_encode( $objects['dictionary_acronym']);
        }
        return $this->model->query()->insertGetId($objects);
    }

    /**
     * @param $id
     * @param array $objects
     * @return int
     */
    public function updateAppraiseOtherInformation($id, array $objects): int
    {
        return $this->model->query()
            ->where('id', $id)
            ->update($objects);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteAppraiseOtherInformation($id)
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
}
