<?php

namespace App\Contracts;

interface StreetRepository extends BaseRepository
{
    public function findPaging();

    public function findAll();

    public function findById($id);

    public function createStreet(array $objects);

    public function updateStreet($id, array $objects);

    public function deleteStreet($id);

    public function findByName($name, $districtId);

}
