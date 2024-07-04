<?php

namespace App\Contracts;

interface CustomerGroupFirstRepository extends BaseRepository
{
    public function findPaging();

    public function findAll();

    public function findById($id);

    public function createCGF(array $objects);

    public function updateCGF($id, array $objects);

    public function deleteCGF($id);

    public function findByName($name);

    public function getAllFirstGroup();
}
