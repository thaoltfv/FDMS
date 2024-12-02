<?php

namespace App\Contracts;

interface WardRepository extends BaseRepository
{
    public function findPaging();

    public function findAll();

    public function findById($id);

    public function createWard(array $objects);

    public function updateWard($id, array $objects);

    public function deleteWard($id);

    public function findByName($name,$districtId);
}
