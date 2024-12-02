<?php

namespace App\Contracts;

interface AddressLogRepository extends BaseRepository
{
    public function findPaging();

    public function findAll();

    public function findById($id);

    public function createLog(array $objects);

}
