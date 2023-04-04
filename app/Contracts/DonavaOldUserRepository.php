<?php

namespace App\Contracts;


interface DonavaOldUserRepository extends BaseRepository
{
    public function findAll();
    public function findById(string $id);
}
