<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Queue\SerializesModels;

class TwoFactorAuthenticationWasDisabled
{
    use SerializesModels;

    public $user;

    /**
     * TwoFactorAuthenticationWasDisabled constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
