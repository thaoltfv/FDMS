<?php

namespace App\Contracts;

interface ProvinceRepository extends BaseRepository
{
    public function findPaging();

    public function findAll();

    public function findById($id);

    public function createProvince(array $objects);

    public function updateProvince($id, array $objects);

    public function deleteProvince($id);

    public function findByName($name);

    public function getAllProvince();

}
