<?php

namespace App\Contracts;

interface ApartmentAssetRepository extends BaseRepository
{
   public function createApartmentAsset(array $objects);
   public function updateApartmentAsset(int $id, array $objects);
   public function getApartmentAssetById(int $id);
   public function postApartmentAssetLaw(int $id, array $objects);
   public function postApartmentAssetAppraisal(int $id, array $objects);
   public function postApartmentAssetHasAsset(int $id, array $objects);
   public function getApartmentAllStep(int $id);
   public function postOtherAssets(int $id , array $objects);
   public function updateComparisonFactor(int $id , array $objects);
   public function updateRoundTotal(int $id , array $objects);
   public function updateEstimateAssetPrice (int $id);
}
