<?php

namespace App\Repositories;

use App\Contracts\AddressLogRepository;

use App\Contracts\CompareAssetVersionRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\QueryBuilder;

class EloquentAssetVersionRepository extends EloquentRepository implements CompareAssetVersionRepository
{

    public function findByGeneralId($id)
    {
        return $this->model->query()
            ->where('asset_general_id', $id)
            ->orderBy('version')
            ->get();
    }
}

