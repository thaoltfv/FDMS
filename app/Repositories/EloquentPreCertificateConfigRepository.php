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

    public function findPaging()
    {
        
        $perPage = (int)request()->get('limit');
        $page = (int)request()->get('page');
        $search = request()->get('search');
        $id = request()->get('id');
        $name = request()->get('name');
        $config = request()->get('config');
        $orderBy = request()->get('order');
        $sort = request()->get('sort');

        if (!in_array($orderBy, ['asc', 'desc'])) {
            $orderBy = 'desc';
        }

        if (!$sort) {
            $sort = $this->allowedSorts;
        }
        if (empty($search)) {
            $search = '';
        }
        $search =urldecode($search);
        $query = '  name ilike ' . "'%" . $name . "%'" ;

        if (!in_array($status, [ValueDefault::ACTIVE_STATUS, ValueDefault::INACTIVE_STATUS])) {
            $status = ValueDefault::ACTIVE_STATUS;
        }
      
        return QueryBuilder::for($this->model)
            ->whereRaw($query)
            ->orderBy($sort, $orderBy)
            ->forPage($page, $perPage)
            ->paginate($perPage);
    }

   
}
