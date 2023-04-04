<?php

namespace App\Contracts;

interface DistrictRepository extends BaseRepository
{
    public function findPaging();

    public function findAll();

    public function findById($id);

    public function createDistrict(array $objects);

    public function updateDistrict($id, array $objects);

    public function deleteDistrict($id);

    public function findByName($name, $provinceId);

    public function findAllByProvince();
}
