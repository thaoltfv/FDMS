<?php

namespace App\Contracts;

interface BlockListRepository extends BaseRepository
{
    public function findPaging();

    public function findAll();

    public function createBlockList(array $objects);

    public function updateBlockList($id, array $objects);

    public function deleteBlockList($id);
}
