<?php

namespace App\Contracts;

interface AppraiserRepository extends BaseRepository
{
    public function findPaging();

    public function findAll();

    public function findById($id);

    public function createAppraiser(array $objects);

    public function updateAppraiser($id, array $objects);

    public function deleteAppraiser($id);
}
