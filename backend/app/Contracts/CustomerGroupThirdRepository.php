<?php

namespace App\Contracts;

interface CustomerGroupThirdRepository extends BaseRepository
{
    public function findPaging();

    public function findAll();

    public function findById($id);

    public function createCGT(array $objects);

    public function updateCGT($id, array $objects);

    public function deleteCGT($id);

    public function findByName($name, $districtId);
}
