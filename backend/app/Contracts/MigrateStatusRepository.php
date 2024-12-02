<?php

namespace App\Contracts;

interface MigrateStatusRepository extends BaseRepository
{
    public function findPaging();

    public function findAll();

    public function findById($id);

    public function CheckProcessIsRunning($limit, $page);

    public function createMigrateStatus(array $objects);

    public function updateMigrateStatus($id, array $objects);

    public function deleteMigrateStatus($id);
}
