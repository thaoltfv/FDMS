<?php

namespace App\Repositories;

use App\Contracts\UnitPriceRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\QueryBuilder;

class EloquentUnitPriceRepository extends EloquentRepository implements UnitPriceRepository
{
    private string $defaultSort = 'name';

    private string $allowedSorts = 'updated_at';


    public function findPaging(): LengthAwarePaginator
    {
        $perPage = (int)request()->get('limit');
        $page = (int)request()->get('page');
        $district = request()->get('district');
        $province = request()->get('province');
        $street = request()->get('street');
        $ward = request()->get('ward');
        $distance = request()->get('distance');
        $landType = request()->get('land_type');
        if (empty($distance)) {
            $distance = '';
        }
        $query = 'distance ilike ' . "'%" . urldecode($distance) . "%'";
        if (!empty($province)) {
            $query = $query . ' and province ilike ' . "'%" . urldecode($province) . "%'";
        }
        if (!empty($district)) {
            $query = $query . ' and district ilike ' . "'%" . urldecode($district) . "%'";
        }
        if (!empty($street)) {
            $query = $query . ' and street ilike ' . "'%" . urldecode($street) . "%'";
        }
        if (!empty($ward)) {
            $query = $query . ' and ward ilike ' . "'%" . urldecode($ward) . "%'";
        }
        if (!empty($landType)) {
            $query = $query . ' and land_type ilike ' . "'%" . urldecode($landType) . "%'";
        }
        return QueryBuilder::for($this->model)
            ->whereRaw($query)
            ->orderByDesc($this->allowedSorts)
            ->forPage($page, $perPage)
            ->paginate($perPage);
    }

    /**
     * @return Builder|Model|object|null
     */
    public function findAll()
    {
        $result = null;
        $district = request()->get('district');
        $province = request()->get('province');
        $street = request()->get('street');
        $ward = request()->get('ward');
        $distance = request()->get('distance');
        $landType = request()->get('land_type');
        if (empty($landType)) {
            $landType = '';
        }
        $query = 'land_type ilike ' . "'" . urldecode($landType) . "'";

        if (!empty($province)) {
            $query = $query . ' and province ilike ' . "'" . urldecode($province) . "'";
        }

        if (!empty($district)) {
            $query = $query . ' and district ilike ' . "'" . urldecode($district) . "'";
        }
        $queryNoWard = $query.' and ward is null';

        if (!empty($ward)) {
            $ward = str_replace(['Xã ', 'Phường '], '', urldecode($ward));
            $query = $query . ' and ward ilike ' . "'%" . urldecode($ward) . "%'";
        }
        $queryDistance = $query;
        if (!empty($distance)) {
            $queryDistance = $queryDistance . ' and street ilike ' . "'" . urldecode($street) . "'";
            $queryDistance = $queryDistance . ' and distance ilike ' . "'" . urldecode($distance) . "'";
            $result = $this->model->query()
                ->whereRaw($queryDistance)
                ->first();
            if (empty($result)) {
                $queryDistance = $queryNoWard . ' and distance ilike ' . "'" . urldecode($distance) . "'";
                $queryDistance = $queryDistance . ' and street ilike ' . "'" . urldecode($street) . "'";
                $result = $this->model->query()
                    ->whereRaw($queryDistance)
                    ->first();
            }
        }

        $queryStreet = $query;
        if (!empty($street) && empty($result)) {
            $queryStreet = $queryStreet . ' and street ilike ' . "'" . urldecode($street) . "' and distance is null";
            $result = $this->model->query()
                ->whereRaw($queryStreet)
                ->first();
            if (empty($result)) {
                $queryStreet = $queryNoWard . ' and street ilike ' . "'" . urldecode($street) . "' and distance is null";
                $result = $this->model->query()
                    ->whereRaw($queryStreet)
                    ->first();
            }
        }
        $queryWard = $query;
        if (!empty($ward) && empty($result)) {
            $queryWard = $queryWard . ' and ward ilike ' . "'%" . urldecode($ward) . "%' and street is null";
            $result = $this->model->query()
                ->whereRaw($queryWard)
                ->first();
        }
        return $result;
    }

    /**
     * @param $id
     * @return Builder|Model|object|null
     */
    public function findById($id)
    {
        return $this->model->query()
            ->where('id', $id)->first();
    }

    /**
     * @return Builder|Model|object|null
     */
    public function findUnitPrice()
    {
        $district = request()->get('district');
        $province = request()->get('province');
        $street = request()->get('street');
        $ward = request()->get('ward');
        $distance = request()->get('distance');
        if (empty($province)) {
            $province = '';
        }
        $query = 'province ilike ' . "'%" . urldecode($province) . "%'";

        if (!empty($district)) {
            $query = $query . ' and district ilike ' . "'%" . urldecode($district) . "%'";
        }
        $queryWard = $query;
        if (!empty($ward)) {
            $queryWard = ' and ward ilike ' . "'%" . urldecode($ward) . "%'";
        }

        $queryStreet = $query;
        if (!empty($street)) {
            $queryStreet = ' and street ilike ' . "'%" . urldecode($street) . "%'";
        }

        $queryDistance = $query;
        if (!empty($distance)) {
            $queryDistance = ' and distance ilike ' . "'%" . urldecode($distance) . "%'";
        }

        $result = $this->model->query()
            ->whereRaw($queryDistance)
            ->orderByDesc($this->defaultSort)
            ->first();
        if (!$result) {
            $result = $this->model->query()
                ->whereRaw($queryStreet)
                ->orderByDesc($this->defaultSort)
                ->first();
        }
        if (!$result) {
            $result = $this->model->query()
                ->whereRaw($queryWard)
                ->orderByDesc($this->defaultSort)
                ->first();
        }
        return $result;
    }

    /**
     * @param array $objects
     * @return int
     */
    public function createUnitPrice(array $objects): int
    {
        return $this->model->query()->insertGetId($objects);
    }

    /**
     * @param $id
     * @param array $objects
     * @return int
     */
    public function updateUnitPrice($id, array $objects): int
    {
        return $this->model->query()
            ->where('id', $id)
            ->update($objects);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteUnitPrice($id)
    {
        return $this->model->query()
            ->where('id', $id)
            ->delete();
    }
}
