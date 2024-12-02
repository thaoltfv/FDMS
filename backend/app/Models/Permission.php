<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

/**
 * App\Models\Permission
 *
 * @property string                                                                 $id
 * @property string                                                                 $name
 * @property string                                                                 $guard_name
 * @property Carbon|null                                        $created_at
 * @property Carbon|null                                        $updated_at
 * @property-read Collection|Permission[] $permissions
 * @property-read int|null                                                          $permissions_count
 * @property-read Collection|Role[] $roles
 * @property-read int|null                                                          $roles_count
 * @property-read Collection|User[] $users
 * @property-read int|null                                                          $users_count
 * @method static Builder|Permission newModelQuery()
 * @method static Builder|Permission newQuery()
 * @method static Builder|\Spatie\Permission\Models\Permission permission($permissions)
 * @method static Builder|Permission query()
 * @method static Builder|\Spatie\Permission\Models\Permission role($roles, $guard = null)
 * @method static Builder|Permission whereCreatedAt($value)
 * @method static Builder|Permission whereGuardName($value)
 * @method static Builder|Permission whereId($value)
 * @method static Builder|Permission whereName($value)
 * @method static Builder|Permission whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Permission extends \Spatie\Permission\Models\Permission
{

    public $incrementing = false;

    protected $keyType = 'string';

    protected $casts = [
        'id' => 'string',
    ];

    protected $fillable = [
        'name',
        'guard_name',
    ];
}
