<?php

namespace App\Repositories;

use App\Contracts\RoleRepository;
use App\Enum\RoleDefault;
use App\Models\Role;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Facades\Log;

class EloquentRoleRepository extends EloquentRepository implements RoleRepository
{
    private string $defaultSort = 'name';

    private array $defaultSelect = [
        'id',
        'name',
        'role_name',
        'guard_name',
        'created_at',
        'updated_at',
    ];

    private array $allowedFilters = [];

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
     * @return LengthAwarePaginator
     */
    public function findPaging(): LengthAwarePaginator
    {
        $perPage = (int)request()->get('limit');
        $page = (int)request()->get('page');
        $search = request()->get('search');
        if (empty($search)) {
            $search = '';
        }
        $query = 'name like ' . "'%" . $search . "%'";
        return QueryBuilder::for($this->model)
            ->whereRaw($query)
            ->where('name', '<>', RoleDefault::ROOT_ADMIN['role'])
            ->orderBy($this->defaultSort)
            ->forPage($page, $perPage)
            ->paginate($perPage);
    }

    /**
     * @return Builder[]|Collection
     */
    public function findAll()
    {
        return $this->model->query()->select()->where('name', '<>', RoleDefault::ROOT_ADMIN['role'])->orderBy($this->defaultSort)->get();
    }

    /**
     * @param $id
     * @return Builder|Model|object|null
     */
    public function findById($id)
    {
        $role = $this->model->query()->where('id', $id)->first();
        $role->getAllPermissions();
        return $role;
    }

    /**
     * @param string $roleName
     * @return Builder|Model|object|null
     */
    public function findRoleByName(string $roleName)
    {
        return $this->model->query()->select()
            ->where('name', '=', $roleName)
            ->where('name', '<>', RoleDefault::ROOT_ADMIN['role'])
            ->first();
    }

    /**
     * @param array $objects
     * @return Builder|Builder[]|Collection|Model
     */
    public function createRole(array $objects)
    {
        $existingRole = $this->model->query()->firstWhere('name', $objects['name']);

        if ($existingRole) {
            return ['message' => 'Mã phân quyền đã tồn tại, vui lòng chọn mã khác', 'exception' => '', 'statusCode' => 403];
        } else {
            $roleId = $this->model->query()->insertGetId(
                [
                    'id' => Uuid::uuid4()->toString(),
                    'name' => $objects['name'],
                    'guard_name' => 'api',
                    'role_name' => $objects['role_name']
                ]
            );
            $role = $this->model->query()->find($roleId);
            $role->givePermissionTo($objects['permissions']);
            return $role;
        }
    }

    /**
     * @param $id
     * @param array $objects
     * @return Builder|Model|object
     */
    public function updateRole($id, array $objects)
    {
        DB::transaction(function () use ($id, $objects) {
            $roleUpdate = $this->model->query()
                ->where('id', '=', $id)
                ->update(['name' => $objects['name'], 'role_name' => $objects['role_name']]);
            $role = $this->model->query()->where('id', '=', $id)->first();
            $role->syncPermissions($objects['permissions']);
            return $role;
        });
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteRole($id)
    {
        return $this->model->query()
            ->where('id', $id)
            ->delete();
    }
}
