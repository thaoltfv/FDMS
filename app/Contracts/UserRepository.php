<?php

namespace App\Contracts;

interface UserRepository extends BaseRepository
{
    public function findPaging();

    public function findAll();

    public function createUser(array $objects);

    public function getUser($id);

    public function checkExitUserByEmail($email);

    public function validateToken($token);

    public function findUserByEmail($email);

    public function updateUser($id, array $objects, $roleUpdate);

    public function deleteUser($id);

    public function deactiveUser($id);

    public function activeUser($id);

    public function isntLegalUser($id);

    public function isLegal($id);

    public function resetUserPasswordNew($id);

}
