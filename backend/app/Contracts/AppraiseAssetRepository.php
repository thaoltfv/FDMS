<?php

namespace App\Contracts;

interface AppraiseAssetRepository extends BaseRepository
{

    public function estimateUnrecognizedFrontSiteAsset($objects);

    public function estimateUnrecognizedUnfrontSiteAsset($objects);

}
