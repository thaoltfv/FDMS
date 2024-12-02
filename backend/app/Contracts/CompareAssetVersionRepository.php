<?php

namespace App\Contracts;

interface CompareAssetVersionRepository extends BaseRepository
{
    public function findByGeneralId($id);

}
