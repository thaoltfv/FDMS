<?php

namespace App\Contracts;

interface PermissionRepository extends BaseRepository
{
    public function getPermissions($roleIds);

    public function getAllPermissions();

    public function getAllScreen();

    public function findByPermissionNames($permissions);

}
