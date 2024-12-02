<?php

namespace App\Listeners\Observers;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Ramsey\Uuid\Uuid;

class PermissionObserver
{
    /**
     * @param Model $model
     */
    public function creating(Model $model)
    {
        $model->setAttribute('id', $model->getAttribute('id') ?? Uuid::uuid4()->toString());
        $model->guard_name = 'api';
    }

    /**
     * @param Permission $permission
     */
    public function updated(Permission $permission)
    {
        $this->created($permission);
    }

    /**
     * @param Permission $permission
     */
    public function created(Permission $permission)
    {
        Cache::forget('spatie.permission.cache');
    }
}
