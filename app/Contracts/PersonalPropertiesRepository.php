<?php

namespace App\Contracts;

use FunctionOrMethodName;

interface PersonalPropertiesRepository extends BaseRepository
{
    public function findPaging();
    public function updatePersonalProperties(int $id, array $data);
    public function createPersonalProperties(array $data);
    public function findOneByIdAssetType();

    // public function findOneMovableByWhere(array $where);

}
