<?php

namespace App\Repositories;

use App\Contracts\BlockListRepository;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\QueryBuilder;

class EloquentBlockListRepository extends EloquentRepository implements BlockListRepository
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
        $apartmentId = (int) request()->get('apartment_id');
        $search = request()->get('search');
        if (empty($search)) {
            $search = '';
        }
        $query = 'name ilike '."'%%".strtolower($search)."%%'";
        if ($apartmentId > 0) {
            $query = $query.' and apartment_id = '.$apartmentId;
        }
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
        $apartmentId = (int) request()->get('apartment_id');
        if($apartmentId > 0) {
            return QueryBuilder::for($this->model)
                ->where('apartment_id', '=', $apartmentId)
                ->orderBy($this->defaultSort)
                ->get();
        } else {
            return QueryBuilder::for($this->model)
                ->orderBy($this->defaultSort)
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
            ->where('id', $id)->first();
    }

    /**
     * @param array $objects
     * @return bool
     */
    public function createBlockList(array $objects): bool
    {
        return $this->model->query()->insertGetId($objects);
    }

    /**
     * @param $id
     * @param array $objects
     * @return int
     */
    public function updateBlockList($id, array $objects): int
    {
        return $this->model->query()
            ->where('id', $id)
            ->update(['name' => $objects['name'],
                'apartment_id' => $objects['apartment_id']]);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteBlockList($id)
    {
        return $this->model->query()
            ->where('id', $id)
            ->delete();
    }

}
