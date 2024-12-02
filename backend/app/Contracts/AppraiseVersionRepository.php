<?php

namespace App\Contracts;

interface AppraiseVersionRepository extends BaseRepository
{
    public function findByAppraiseId($id);

    public function createVersionIndex();
}
