<?php

namespace App\Contracts;

interface EstimatePriceRepository extends BaseRepository
{
    public function estimateRecognizedFrontSiteAsset($objects);

    public function estimateUnrecognizedFrontSiteAsset($objects, $defaultLandType);

    public function estimateUnrecognizedUnfrontSiteAsset($objects);

    public function estimateApartmentAsset($objects);
}
