<?php

namespace App\Contracts;

interface BranchRepository extends BaseRepository
{
    public function findPaging();

    public function findAll();

    public function findById($id);

    public function createBranch(array $objects);

    public function updateBranch($id, array $objects);

    public function deleteBranch($id);
}
