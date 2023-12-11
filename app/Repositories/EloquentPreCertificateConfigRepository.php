<?php

namespace App\Repositories;

use App\Contracts\PreCertificateConfigRepository;
use App\Enum\ValueDefault;
use App\Models\PreCertificateConfig;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\QueryBuilder;

class EloquentPreCertificateConfigRepository extends EloquentRepository implements PreCertificateConfigRepository
{
    private string $defaultSort = 'name';

    private string $allowedSorts = 'id';

    public function findAll()
    {
        return $this->model->query()
            ->select()
            ->orderByDesc($this->defaultSort)
            ->get();
    }

}
