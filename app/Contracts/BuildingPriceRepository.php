<?php

namespace App\Contracts;

interface BuildingPriceRepository extends BaseRepository
{
    public function findPaging();

    public function findAll();

    public function findById($id);

    public function createBuildingPrice(array $objects);

    public function updateBuildingPrice($id, array $objects);

    public function deleteBuildingPrice($id);

    public function getAverageBuildPrice();

    public function getAverageBuildEstimatePrice($object);

    public function getAverageBuildPriceV3($object) : int;

    public function getPP2($object);
}
