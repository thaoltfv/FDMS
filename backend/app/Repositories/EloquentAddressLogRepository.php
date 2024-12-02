<?php

namespace App\Repositories;

use App\Contracts\AddressLogRepository;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\QueryBuilder;

class EloquentAddressLogRepository extends EloquentRepository implements AddressLogRepository
{

    private string $allowedSorts = 'updated_at';
    private string $defaultSort = 'updated_at';

    /**
     * @return LengthAwarePaginator
     */
    public function findPaging(): LengthAwarePaginator
    {
        $perPage = (int)request()->get('limit');
        $page = (int)request()->get('page');

        return QueryBuilder::for($this->model)
            ->orderByDesc($this->allowedSorts )
            ->forPage($page, $perPage)
            ->paginate($perPage);
    }

    /**
     * @return Builder[]|Collection
     */
    public function findAll()
    {
        return $this->model->query() ->select()->orderByDesc($this->defaultSort)->get();
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
     * @param $id
     * @param array $objects
     * @return int
     */
    public function updateEstimatePriceLog($id, array $objects): int
    {
        return $this->model->query()
            ->where('id', $id)
            ->update(['input' => $objects['input']]);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteEstimatePriceLog($id)
    {
        return $this->model->query()
            ->where('id', $id)
            ->delete();
    }

    public function createLog(array $objects): int
    {
        return $this->model->query()->insertGetId($objects);
    }


}

