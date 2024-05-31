<?php

namespace App\Repositories;

use App\Contracts\DictionaryRepository;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Facades\Http;

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
            // ->whereIn('status', $searchStatus)
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
        return $this->model->query()->where('type', $type)->orderBy('id')->get();
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

    public function findDictionary($type, $name)
    {
        return $this->model->query()
            ->where('type', '=', $type)
            ->where('description', '=', $name)
            ->where('status', '=', 1)
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

    public function changeStatusCustomerGroup($id, $status)
    {
        return $this->model->query()
            ->where('id', $id)
            ->update(['status' =>  $status]);
    }


    public function getToken()
    {
        // production
        $apiUrl = "https://app.estatemanner.com/api/v1/auth/credentials";
        $postinput =  [
            "client_id" => 'meI2rBIVba1F9SKTxKXhuYX9bBZqWWcU',
            "client_secret" => 'd197ac7cc0e3cbb8eb13ac7a7e241bc5120f4341d7a18139d00cf1020d6d8bf1'
        ];

        //trial
        // $apiUrl = "https://app-stg.estatemanner.com/api/v1/auth/credentials";
        // $postinput =  [
        //     "client_id" => 'ch3ELh1kAlH1QQP6SOq7PZHEEZ3p8qKS',
        //     "client_secret" => '370dfee19ee9db1fcc974e5492d254359c92b718ceebdf8c1603192565780845'
        // ];
        $header = [
            'Content-type' => 'application/json'
        ];
        $response = Http::withHeaders($header)->post($apiUrl, $postinput);
        $statusCode = $response->status();

        // if ($statusCode == 201) {
        $responseBody = json_decode($response->getBody(), true);
        $data = $responseBody;
        return $data;
        // }
    }

    /**
     * @param array $objects
     */
    public function getInfoByCoord(array $objects)
    {
        // production
        $apiUrl = "https://app.estatemanner.com/api/v1/map/feature/coord";

        // trial
        // $apiUrl = "https://app-stg.estatemanner.com/api/v1/map/feature/coord";

        $postinput =  [
            "lat" => $objects['lat'],
            "lng" => $objects['lng']
        ];
        $header = [
            'Content-type' => 'application/json',
            'Authorization' => 'Bearer ' . $objects['token']
        ];
        $response = Http::withHeaders($header)->post($apiUrl, $postinput);
        $statusCode = $response->status();

        // if ($statusCode == 201) {
        $responseBody = json_decode($response->getBody(), true);
        $data = $responseBody;
        if ($data == null) {
            $data['code'] = 1;
            $data['message'] = 'Hệ thống đang có lỗi xảy ra, vui lòng thử lại sau';
        }

        return $data;
        // }
    }

    /**
     * @param array $objects
     */
    public function getInfoByLand(array $objects)
    {
        if ($objects['land_plot'] && $objects['land_sheet']) {
            // production
            $apiUrl = "https://app.estatemanner.com/api/v1/map/feature/landplot";

            // trial
            // $apiUrl = "https://app-stg.estatemanner.com/api/v1/map/feature/landplot";

        } else {
            // production
            $apiUrl = "https://app.estatemanner.com/api/v1/map/feature/cadastral";

            // trial
            // $apiUrl = "https://app-stg.estatemanner.com/api/v1/map/feature/cadastral";

        }

        $postinput =  [
            "city_code" => $objects['city_code'],
            "district_code" => $objects['district_code'],
            "ward_code" => $objects['ward_code'],
            "land_plot" => $objects['land_plot'],
            "land_sheet" => $objects['land_sheet'],
        ];
        $header = [
            'Content-type' => 'application/json',
            'Authorization' => 'Bearer ' . $objects['token']
        ];
        $response = Http::withHeaders($header)->post($apiUrl, $postinput);
        $statusCode = $response->status();

        // if ($statusCode == 201) {
        $responseBody = json_decode($response->getBody(), true);
        $data = $responseBody;
        return $data;
        // }
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
