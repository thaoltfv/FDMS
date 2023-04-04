<?php

namespace App\Repositories;

use App\Contracts\EstimatePriceLogRepository;
use App\Models\CompareAssetGeneral;
use App\Models\EstimatePriceLog;
use Carbon\Carbon;
use Elasticquent\ElasticquentResultCollection;
use Elasticsearch\ClientBuilder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\QueryBuilder;

class EloquentEstimatePriceLogRepository extends EloquentRepository implements EstimatePriceLogRepository
{

    private string $allowedSorts = 'updated_at';

    /**
     * @return array
     */
    public function findPaging(): array
    {

        $perPage = (int)request()->get('limit');
        $page = ((int)request()->get('page')) - 1;
        $dateFrom = request()->get('date_from');
        $dateTo = request()->get('date_to');
        $ids = request()->get('ids');
        $createdBy = request()->get('create_by');
        $isPrint = request()->get('is_print');
        $all = request()->get('all');
        $array['bool']['must'] = [];
        if (!empty($ids)) {
            $array['bool']['must'][] = [
                'terms' => [
                    'id' => json_decode($ids),
                ]
            ];
        }
        if (!empty($dateFrom)) {
            $array['bool']['must'][] = [
                'range' => [
                    'report.create_date' => [
                        'gte' => $dateFrom,
                        'format' => 'dd-MM-yyyy||yyyy',
                    ]
                ]
            ];
        }
        if (!empty($dateTo)) {
            $array['bool']['must'][] = [
                'range' => [
                    'report.create_date' => [
                        'lte' => $dateTo,
                        'format' => 'dd-MM-yyyy||yyyy',
                    ]
                ]
            ];
        }
        if (!empty($createdBy)) {
            $array['bool']['must'][] = [
                'match' => [
                    'report.create_by' => urldecode($createdBy),
                ]
            ];
        }
        if ($isPrint) {
            $array['bool']['must_not'][] = [
                'exists' => [
                    'field' => 'ids'
                ]
            ];
        }

        if(empty($all)){
            $array['bool']['must'][] = [
                'range' => [
                    'report.total_price_update' => [
                        'gt' => 0,
                    ]
                ]
            ];
        }

        $sortBy['id'] = ['order' => 'desc'];
        $search_result = EstimatePriceLog::searchByQuery($array, null, null, $perPage, $page * $perPage, $sortBy);
        return $this->responseByResult($search_result, $page, $perPage);
    }

    /**
     * @return ElasticquentResultCollection
     */
    public function findAll(): ElasticquentResultCollection
    {
        $ids = request()->get('ids');
        $array = [];
        if (!empty($ids)) {
            $array['bool']['must'][] = [
                'terms' => [
                    'id' => json_decode($ids),
                ]
            ];
        }
        $search_result = EstimatePriceLog::searchByQuery($array, null, null, 1000, 0, null);
        return $search_result;
    }

    /**
     * @param $id
     * @return ElasticquentResultCollection
     */
    public function findById($id)
    {
        $array['bool']['must'] = [];
        $array['bool']['must'] = [
            'match' => [
                'id' => $id
            ]
        ];
        $search_result = EstimatePriceLog::searchByQuery($array, null, null, null, null, null);
        return $search_result;
    }

    /**
     * @param array $objects
     * @return array
     */
    public function createEstimatePriceLog(array $objects): array
    {
        $objects['report']['create_date'] = Carbon::now();
        $objects['report']['total_price_difference'] = null;
        $objects['report']['percent_difference'] = null;
        $objects['report']['total_price_update'] = $objects['report']['total_price_update'] ?? $objects['report']['total_price'];
        if ($objects['report']['total_price'] && $objects['report']['total_price_update']) {
            $objects['report']['total_price_difference'] = $objects['report']['total_price_update'] - $objects['report']['total_price'];
            $objects['report']['percent_difference'] = (int)(($objects['report']['total_price_difference']) / $objects['report']['total_price'] * 100);
        }

        $objects['report']['is_update_land_value'] = false;
        $objects['report']['percent_difference_land'] = null;
        $totalPriceUpdate = null;
        $totalPrice = null;
        $totalArea = null;
        foreach ($objects['report_detail']['land'] as $key => $value) {
            $averageLandUnitDifference = null;
            $estimatePriceDifference = null;
            $totalEstimatePrice = null;
            $totalEstimatePriceUpdate = null;
            $totalArea += (float)$value['area'];
            $totalPrice += $value['estimate_price'];
            if ($value['estimate_price'] && $value['estimate_price_update']) {
                $estimatePriceDifference = $value['estimate_price_update'] - $value['estimate_price'];
                $averageLandUnitDifference = $value['average_unit_price_update'] - $value['average_unit_price'];
                $totalEstimatePrice += $value['estimate_price'];

            }
            if ($value['estimate_price_update']) {
                $totalEstimatePriceUpdate += $value['estimate_price_update'];
            }
            $objects['report_detail']['land'][$key]['estimate_price_difference'] = $estimatePriceDifference;
            $objects['report_detail']['land'][$key]['average_unit_difference'] = $averageLandUnitDifference;
            $objects['report_detail']['land'][$key]['percent_difference'] = ($totalEstimatePrice && $totalEstimatePriceUpdate) ? (int)(($totalEstimatePriceUpdate - $totalEstimatePrice) / $totalEstimatePrice * 100) : null;
        }
        $totalPriceUpdate = $objects['report']['total_land_price_update'] ?? $totalPriceUpdate;
        $objects['report']['percent_difference_land'] = ($totalPriceUpdate && $totalPrice) ? (int)(($totalPriceUpdate - $totalPrice) / $totalPrice * 100) : $objects['report']['percent_difference_land'];;

        foreach ($objects['report_detail']['land'] as $key => $value) {
            $objects['report_detail']['land'][$key]['total_price_update'] = null;
            $objects['report_detail']['land'][$key]['total_price'] = null;
            $objects['report_detail']['land'][$key]['total_percent_difference'] = null;
            $objects['report_detail']['land'][$key]['total_price_update'] = $totalPriceUpdate;
            $objects['report_detail']['land'][$key]['total_price'] = $totalPrice;
            if ($value['estimate_price'] && $value['average_unit_price']) {
                $objects['report_detail']['land'][$key]['total_percent_difference'] = ($totalPriceUpdate && $totalPrice) ? (int)(($totalPriceUpdate - $totalPrice) / $totalPrice * 100) : null;
            }

            $objects['report_detail']['land'][$key]['is_update'] = false;
            if ($value['estimate_price_update'] && ($value['estimate_price'] != $value['estimate_price_update'])) {
                $objects['report']['is_update_land_value'] = true;
                $objects['report_detail']['land'][$key]['is_update'] = true;
            }
        }
        $objects['report']['average_unit_price'] = ($totalPriceUpdate && $totalArea) ? round((int)($totalPriceUpdate / $totalArea), -5) : null;
        $objects['report']['is_update_building_value'] = false;
        $objects['report']['percent_difference_building'] = null;

        $totalPriceUpdate = null;
        $totalPrice = null;
        foreach ($objects['report_detail']['building'] as $key => $value) {
            $averageLandUnitDifference = null;
            $estimatePriceDifference = null;
            $totalEstimatePrice = null;
            $totalEstimatePriceUpdate = null;
            $totalPrice += $value['estimate_price'];
            if ($value['estimate_price'] && $value['estimate_price_update']) {
                $estimatePriceDifference = $value['estimate_price_update'] - $value['estimate_price'];
                $averageLandUnitDifference = $value['average_unit_price_update'] - $value['average_unit_price'];
                $totalEstimatePrice += $value['estimate_price'];

            }
            if ($value['estimate_price_update']) {
                $totalEstimatePriceUpdate += $value['estimate_price_update'];
            }
            $objects['report_detail']['building'][$key]['estimate_price_difference'] = $estimatePriceDifference;
            $objects['report_detail']['building'][$key]['average_unit_difference'] = $averageLandUnitDifference;
            $objects['report_detail']['building'][$key]['percent_difference'] = ($totalEstimatePrice && $totalEstimatePriceUpdate) ? (int)(($totalEstimatePriceUpdate - $totalEstimatePrice) / $totalEstimatePrice * 100) : null;
        }
        $totalPriceUpdate = $objects['report']['total_building_price_update'] ?? $totalPriceUpdate;
        $objects['report']['percent_difference_building'] = ($totalPriceUpdate && $totalPrice) ? (int)(($totalPriceUpdate - $totalPrice) / $totalPrice * 100) : $objects['report']['percent_difference_building'];;
        foreach ($objects['report_detail']['building'] as $key => $value) {
            $objects['report_detail']['building'][$key]['total_price_update'] = null;
            $objects['report_detail']['building'][$key]['total_price'] = null;
            $objects['report_detail']['building'][$key]['total_percent_difference'] = null;
            $objects['report_detail']['building'][$key]['total_price_update'] = $totalPriceUpdate;
            $objects['report_detail']['building'][$key]['total_price'] = $totalPrice;
            if ($value['estimate_price'] && $value['average_unit_price']) {
                $objects['report_detail']['building'][$key]['total_percent_difference'] = ($totalPriceUpdate && $totalPrice) ? (int)(($totalPriceUpdate - $totalPrice) / $totalPrice * 100) : null;
            }
            $objects['report_detail']['building'][$key]['is_update'] = false;
            if ($value['estimate_price_update'] && ($value['estimate_price'] != $value['estimate_price_update'])) {
                $objects['report']['is_update_building_value'] = true;
                $objects['report_detail']['building'][$key]['is_update'] = true;
            }
        }
        $client = ClientBuilder::create()
            ->setSSLVerification(false)
            ->setHosts(config('elasticquent.config.hosts'))
            ->build();
        $request = [
            'index' => env('ELASTIC_REPORT_DEFAULT_INDEX'),
            'type' => '_doc',
            'id' => $objects['id'],
            'body' => json_encode($objects, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        ];
        $client->index($request);
        return $objects;
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
            ->update(['name' => $objects['name']]);
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

    public function countAll()
    {
        $search_result = EstimatePriceLog::searchByQuery(null, null, null, null, null, null);
        if ($search_result->totalHits() && is_array($search_result->totalHits())) {
            return ($search_result->totalHits())['value'];
        }
        return 0;
    }

    public function createLog(array $objects): int
    {
        return $this->model->query()->insertGetId($objects);
    }

    public function getLog($id)
    {
        return $this->model->query()
            ->where('id', '=', $id)
            ->first();
    }

    public function createIndex(): array
    {
        $client = ClientBuilder::create()
            ->setSSLVerification(false)
            ->setHosts(config('elasticquent.config.hosts'))
            ->build();
        $params = [
            'index' => env('ELASTIC_REPORT_DEFAULT_INDEX'),
            'body' => [
                'mappings' => [
                    "properties" => [
                        "create_date" => [
                            "type" => "date"
                        ]
                    ]
                ]
            ]
        ];
        return $client->indices()->create($params);
    }
}

