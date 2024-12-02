<?php

namespace App\Contracts;

interface LoginHistoryRepository extends BaseRepository
{
    /**
     * @param array $data
     * @return bool
     */
    public function loginsWithThisIpExists(array $data): bool;
}
