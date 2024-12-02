<?php

namespace App\Providers;

use App\Events\TwoFactorAuthenticationWasDisabled;
use App\Listeners\Observers\LoginHistoryObserver;
use App\Listeners\Observers\PermissionObserver;
use App\Listeners\Observers\RoleObserver;
use App\Listeners\Observers\UserObserver;
use App\Models\LoginHistory;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
    ];

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        parent::boot();
        User::observe(UserObserver::class);
        LoginHistory::observe(LoginHistoryObserver::class);
        Permission::observe(PermissionObserver::class);
        Role::observe(RoleObserver::class);
    }
}
