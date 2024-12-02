<?php

namespace App\Policies;

use App\Models\LoginHistory;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LoginHistoryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view a list of model.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user)
    {
        return $user->can('view any login histories');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param LoginHistory $model
     * @return bool
     */
    public function view(User $user, LoginHistory $model): bool
    {
        return $user->can('view login histories') || $user->id === $model->user_id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @return false
     */
    public function create(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return false
     */
    public function update(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return false
     */
    public function delete(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return false
     */
    public function restore(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return false
     */
    public function forceDelete(): bool
    {
        return false;
    }
}
