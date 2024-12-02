<?php

namespace App\Providers;

use App\Models\LoginHistory;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Policies\LoginHistoryPolicy;
use App\Policies\PermissionPolicy;
use App\Policies\RolePolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        LoginHistory::class     => LoginHistoryPolicy::class,
        User::class             => UserPolicy::class,
        Permission::class       => PermissionPolicy::class,
        Role::class             => RolePolicy::class,
    ];

    /**
     *
     */
    public function boot()
    {
        $this->registerPolicies();
    }
}
