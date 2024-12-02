<?php

namespace App\Support;

use Exception;
use PragmaRX\Google2FALaravel\Support\Authenticator;

class TwoFactorAuthenticator extends Authenticator
{
    /**
     * @return bool
     * @throws Exception
     */
    protected function canPassWithoutCheckingOTP(): bool
    {
        return !$this->getUser()->google2fa_enable ||
            !$this->isEnabled() ||
            $this->noUserIsAuthenticated() ||
            $this->twoFactorAuthStillValid();
    }

    /**
     * @return mixed
     * @throws Exception
     */
    protected function getGoogle2FASecretKey()
    {
        return $this->getUser()->{$this->config('otp_secret_column')};
    }
}
