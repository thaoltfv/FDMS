<?php

namespace App\Contracts;

interface RoleRepository extends BaseRepository
{

    public function findPaging();

    public function findAll();

    public function findById($id);

    public function findRoleByName(string $roleName);

    public function createRole(array $objects);

    public function updateRole($id, array $objects);

    public function deleteRole($id);

}
