<?php

namespace App\Repositories;

use App\Contracts\PermissionRepository;
use App\Enum\ScreensDefault;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Spatie\QueryBuilder\QueryBuilder;

class EloquentPermissionRepository extends EloquentRepository implements PermissionRepository
{
    private string $defaultSort = '-created_at';

    private array $defaultSelect = [
        'id',
        'name',
        'permission',
        'guard_name',
        'created_at',
        'updated_at',
    ];

    private array $allowedFilters = [
    ];

    private array $allowedSorts = [
        'updated_at',
        'created_at',
    ];

    /**
     * @return LengthAwarePaginator
     */
    public function findByFilters(): LengthAwarePaginator
    {
        $perPage = (int)request()->get('limit');
        $perPage = $perPage >= 1 && $perPage <= 100 ? $perPage : 20;

        return QueryBuilder::for(Role::class)
            ->select($this->defaultSelect)
            ->allowedFilters($this->allowedFilters)
            ->allowedSorts($this->allowedSorts)
            ->defaultSort($this->defaultSort)
            ->paginate($perPage);
    }

    /**
     * @return Builder[]|Collection
     */
    public function getAllPermissions()
    {
        return $this->model->query() ->select()
            ->get();
    }

    /**
     * @return array
     */
    public function getAllScreen(): array
    {
        return ScreensDefault::ALL_SCREENS;
    }

    /**
     * @param $roleIds
     * @return Collection|QueryBuilder[]
     */
    public function getPermissions($roleIds)
    {
        return QueryBuilder::for(Permission::class)
            ->select(['permissions.id','permissions.name'])
            ->leftJoin('role_has_permissions','permissions.id','=','role_has_permissions.permission_id')
            ->whereIn('role_has_permissions.role_id',  $roleIds)
            ->distinct()->get();
    }

    public function findByPermissionNames($permissions)
    {
        return $this->model->query() ->select()
            ->whereIn('name', $permissions)
            ->get();
    }
}
