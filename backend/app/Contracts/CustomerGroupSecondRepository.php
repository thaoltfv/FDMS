<?php

namespace App\Contracts;

interface CustomerGroupSecondRepository extends BaseRepository
{
    public function findPaging();

    public function findAll();

    public function findById($id);

    public function createCGS(array $objects);

    public function updateCGS($id, array $objects);

    public function deleteCGS($id);

    public function findByName($name, $provinceId);

    public function findAllByFirstGroup();
}
