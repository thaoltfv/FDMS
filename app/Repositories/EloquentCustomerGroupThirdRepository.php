<?php

namespace App\Repositories;

use App\Contracts\CustomerGroupThirdRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\QueryBuilder;

class EloquentCustomerGroupThirdRepository extends EloquentRepository implements CustomerGroupThirdRepository
{
    private string $defaultSort = 'name';

    private string $allowedSorts = 'updated_at';


    public function findPaging(): LengthAwarePaginator
    {
        $perPage = (int)request()->get('limit');
        $page = (int)request()->get('page');
        $secondId = request()->get('second_id');
        $firstId = request()->get('first_id');
        $search = request()->get('search');
        if (empty($search)) {
            $search = '';
        }
        $query = 'name ilike ' . "'%" . $search . "%'";
        if ($firstId > 0) {
            $query = $query . ' and first_id = ' . $firstId;
        }
        if ($secondId > 0) {
            $query = $query . ' and second_id = ' . $secondId;
        }
        return QueryBuilder::for($this->model)
            ->with(['secondGroup', 'firstGroup'])
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
        $firstId = (int) request()->get('first_id');
        $secondId = (int) request()->get('second_id');
        $query = '';
        if ($firstId > 0) {
            $query = $query . 'first_id = ' . $firstId;
        }
        if ($secondId > 0) {
            if (empty($query)) {
                $query = $query . ' second_id = ' . $secondId;
            } else {
                $query = $query . ' and second_id = ' . $secondId;
            }
        }
        if (!empty($query)) {
            return QueryBuilder::for($this->model)
                ->with(['secondGroup', 'firstGroup'])
                ->whereRaw($query)
                ->orderByDesc($this->defaultSort)
                ->get();
        } else {
            return QueryBuilder::for($this->model)
                ->with(['secondGroup', 'firstGroup'])
                ->orderByDesc($this->defaultSort)
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
            ->with(['secondGroup', 'firstGroup'])
            ->where('id', $id)->first();
    }

    /**
     * @param array $objects
     * @return int
     */
    public function createCGT(array $objects): int
    {
        return $this->model->query()->insertGetId($objects);
    }

    /**
     * @param $id
     * @param array $objects
     * @return int
     */
    public function updateCGT($id, array $objects): int
    {
        return $this->model->query()
            ->where('id', $id)
            ->update([
                'name' => $objects['name'],
                'second_id' => $objects['second_id'],
                'first_id' => $objects['first_id']
            ]);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteCGT($id)
    {
        return $this->model->query()
            ->where('id', $id)
            ->delete();
    }

    public function findByName($name, $secondId)
    {
        $result = $this->model->query()
            ->where('name', '=', $name)
            ->where('second_id', '=', $secondId)
            ->first();
        if (!$result) {
            $query = 'name ilike ' . "'" . $name . "%'";
            $result = $this->model->query()
                ->whereRaw($query)
                ->where('second_id', '=', $secondId)
                ->first();
        }

        return $result;
    }
}
