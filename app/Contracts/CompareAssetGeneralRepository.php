<?php

namespace App\Contracts;

interface CompareAssetGeneralRepository extends BaseRepository
{
    public function findPaging();

    public function findAllInElastic();

    public function findAll();

    public function findById($id);

    public function findByIds($id);

    public function createCompareAssetGeneral(array $objects);

    public function updateCompareAssetGeneral($id, array $objects);

    public function updateStatusCompareAssetGeneral($id, array $objects);

    public function deleteCompareAssetGeneral($id);

    public function saveCompareAssetGeneral($object);

    public function createIndex();

    public function createVersionIndex();

    public function findVersionById($id, $version);

    public function estimateRecognizedFrontSiteAsset($objects);

    public function estimateUnrecognizedFrontSiteAsset($objects, $defaultLandType);

    public function estimateUnrecognizedUnfrontSiteAsset($objects);

    public function estimateApartmentAsset($objects);

    public function findAllInElastic_v2();

    public function findAllInElastic_v3();

    public function findVersionById_v2($id, $version);

    public function getTotalAssetByProvince();

    public function getTotalAssetByMonthOfYear();

    public function countCompareAssetGeneral();

    public function findApartmentVersionById($id, $version);

    public function autoApartmentAsset($objects);

    public function findPaging2();

    public function exportAsset();
}
