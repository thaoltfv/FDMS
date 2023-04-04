<?php

namespace App\Repositories;

use App\Contracts\AddressLogRepository;

use App\Contracts\AppraiseVersionRepository;
use App\Models\AppraiseVersion;
use Elasticsearch\ClientBuilder;

class EloquentAppraiseVersionRepository extends EloquentRepository implements AppraiseVersionRepository
{
    public function findByAppraiseId($id)
    {
        return $this->model->query()
            ->where('appraise_id', $id)
            ->orderByDesc('id')
            ->get();
    }

    public function createVersionIndex(): array
    {
        $client = ClientBuilder::create()
            ->setSSLVerification(false)
            ->setHosts(config('elasticquent.config.hosts'))
            ->build();
        $params = [
            'index' => env('ELASTIC_APPRAISE_VERSION_INDEX'),
            'body' => [
                'mappings' => [
                    "properties" => [
                        "asset" => [
                            "properties" => [
                                "main_road_length" => [
                                    "type" => "float"
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
        return $client->indices()->create($params);
    }

}

