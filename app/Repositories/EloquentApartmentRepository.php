<?php

namespace App\Repositories;

use App\Contracts\ApartmentRepository;
use App\Contracts\StreetRepository;
use App\Models\Apartment;
use App\Models\CompareAssetGeneral;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\QueryBuilder;

class EloquentApartmentRepository extends EloquentRepository implements ApartmentRepository
{
    private string $defaultSort = 'name';

    private string $allowedSorts = 'updated_at';

    public function findPaging(): LengthAwarePaginator
    {
        $perPage = (int)request()->get('limit');
        $page = (int)request()->get('page');
        $provinceId = (int)request()->get('province_id');
        $districtId = (int)request()->get('district_id');
        $wardId = (int)request()->get('ward_id');
        $streetId = (int)request()->get('street_id');
        $search = request()->get('search');
        if (empty($search)) {
            $search = '';
        }
        $query = 'name ilike '."'%".$search."%'";
        if ($provinceId > 0) {
            $query = $query . 'province_id = ' . $provinceId;
        }
        if ($districtId > 0) {
            if (empty($query)) {
                $query = $query . ' district_id = ' . $districtId;
            } else {
                $query = $query . ' and district_id = ' . $districtId;
            }
        }
        if ($wardId > 0) {
            if (empty($query)) {
                $query = $query . ' ward_id = ' . $wardId;
            } else {
                $query = $query . ' and ward_id = ' . $wardId;
            }
        }
        if ($streetId > 0) {
            if (empty($query)) {
                $query = $query . ' street_id = ' . $streetId;
            } else {
                $query = $query . ' and street_id = ' . $streetId;
            }
        }

        return QueryBuilder::for($this->model)
            ->with(['district', 'province', 'ward', 'street', 'blockLists'])
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
        $provinceId = (int)request()->get('province_id');
        $districtId = (int)request()->get('district_id');
        $wardId = (int)request()->get('ward_id');
        $streetId = (int)request()->get('street_id');
        $search = request()->get('search');
        if (empty($search)) {
            $search = '';
        }
        $query = 'name ilike '."'%".$search."%'";
        if ($provinceId > 0) {
            $query = $query . 'province_id = ' . $provinceId;
        }
        if ($districtId > 0) {
            if (empty($query)) {
                $query = $query . ' district_id = ' . $districtId;
            } else {
                $query = $query . ' and district_id = ' . $districtId;
            }
        }
        if ($wardId > 0) {
            if (empty($query)) {
                $query = $query . ' ward_id = ' . $wardId;
            } else {
                $query = $query . ' and ward_id = ' . $wardId;
            }
        }
        if ($streetId > 0) {
            if (empty($query)) {
                $query = $query . ' street_id = ' . $streetId;
            } else {
                $query = $query . ' and street_id = ' . $streetId;
            }
        }
        if (!empty($query)) {
            return QueryBuilder::for($this->model)
                ->with(['district', 'province', 'ward', 'street', 'blockLists'])
                ->whereRaw($query)
                ->orderBy($this->defaultSort)
                ->get();
        } else {
            return QueryBuilder::for($this->model)
                ->with(['district', 'province', 'ward', 'street'])
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
            ->with(['district', 'province', 'ward', 'street', 'blockLists'])
            ->where('id', $id)->first();
    }

    /**
     * @param array $objects
     * @return int
     */
    public function createApartment(array $objects): int
    {
        $apartment = new Apartment($objects);
        return $apartment->newQuery()
            ->insertGetId($apartment->attributesToArray());
    }

    /**
     * @param $id
     * @param array $objects
     * @return Builder|Model
     */
    public function updateApartment($id, array $objects)
    {
        $general = new Apartment($objects);
        return $general->newQuery()->updateOrCreate(['id' => $id], $general->attributesToArray());
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteApartment($id)
    {
        return $this->model->query()
            ->where('id', $id)
            ->delete();
    }
}
