<?php

namespace App\Repositories;

use App\Contracts\DonavaOldUserRepository;

class EloquentDonavaOldUserRepository extends EloquentRepository implements DonavaOldUserRepository
{
    public function findAll()
    {
        return $this->model->query()->get();
    }

    public function findById(string $id)
    {
        return $this->model->query()
            ->where('id','=', $id)
            ->first();
    }
}
