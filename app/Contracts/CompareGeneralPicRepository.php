<?php

namespace App\Contracts;

interface CompareGeneralPicRepository extends BaseRepository
{
    public function findPaging();

    public function findAll();

    public function findByType($type);

    public function findByName($name);

    public function createGeneralPic(array $objects);

    public function updateGeneralPic($id, array $objects);

    public function deleteGeneralPic($id);
}
