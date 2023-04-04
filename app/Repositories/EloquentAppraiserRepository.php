<?php

namespace App\Repositories;

use App\Contracts\AppraiserRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\QueryBuilder;

use App\Models\Dictionary;

class EloquentAppraiserRepository extends EloquentRepository implements AppraiserRepository
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
            ->with('appraisePosition')
            ->orderByDesc($this->allowedSorts)
            ->forPage($page, $perPage)
            ->paginate($perPage);
    }

    /**
     * @return Builder[]|Collection
     */
    public function findAll()
    {
        $result = $this->model->query();
        $search = request()->get('search');
        if (!empty($search)) {
            if(is_numeric($search)) {
                $query = 'appraise_position_id = ' .  (int)$search ;
                $result = $result->whereRaw($query);
            } else {
                $acronyms = explode(',', $search);
                $chucVuIds = Dictionary::whereIn('acronym', $acronyms)->where('type', 'CHUC_VU')->pluck('id');
                if(count($chucVuIds)) {
                    $result = $result->whereIn('appraise_position_id', $chucVuIds);
                } else {
                    $query = 'appraise_position_id = 0';
                    $result = $result->whereRaw($query);
                }
            }
        }
        
        return $result
            ->with('appraisePosition')
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
        return $this->model->query()->where('id', $id)->with('appraisePosition')->first();
    }

    /**
     * @param array $objects
     * @return bool
     */
    public function createAppraiser(array $objects): bool
    {
        return $this->model->query()->insertGetId($objects);
    }

    /**
     * @param $id
     * @param array $objects
     * @return int
     */
    public function updateAppraiser($id, array $objects): int
    {
        return $this->model->query()
            ->where('id', $id)
            ->update($objects);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteAppraiser($id)
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
