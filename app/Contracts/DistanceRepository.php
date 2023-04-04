<?php

namespace App\Contracts;

interface DistanceRepository extends BaseRepository
{
    public function findPaging();

    public function findAll();

    public function findById($id);

    public function createDistance(array $objects);

    public function updateDistance($id, array $objects);

    public function deleteDistance($id);

    public function findByName($name, $streetId);
}
