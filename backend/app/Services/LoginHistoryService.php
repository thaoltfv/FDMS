<?php

namespace App\Services;

use App\Contracts\LoginHistoryRepository;
use App\Models\User;
use App\Notifications\SuccessfulLoginFromIpNotification;
use Illuminate\Support\Facades\Notification;

class LoginHistoryService
{
    private LoginHistoryRepository $loginHistoryRepository;

    /**
     * LoginHistoryService constructor.
     * @param LoginHistoryRepository $loginHistoryRepository
     */
    public function __construct(LoginHistoryRepository $loginHistoryRepository)
    {
        $this->loginHistoryRepository = $loginHistoryRepository;
    }

    /**
     * @param User $user
     * @param array $data
     */
    public function store(User $user, array $data)
    {
        $this->sendNotificationIfNewIp($user, $data);
        $this->loginHistoryRepository->store($data);
    }

    /**
     * @param User $user
     * @param array $data
     */
    private function sendNotificationIfNewIp(User $user, array $data)
    {
        $loginsWithThisIpExists = $this->loginHistoryRepository->loginsWithThisIpExists($data);

        if (!$loginsWithThisIpExists) {
            // turn off send notify new IP
            // Notification::send($user, new SuccessfulLoginFromIpNotification($data));
        }
    }
}
