<?php

namespace App\Contracts;

interface DonavaOldEstatesRepository extends BaseRepository
{
    public function findAll();
    public function findPaging($perPage, $page);
}
