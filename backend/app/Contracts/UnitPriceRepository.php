<?php

namespace App\Contracts;

interface UnitPriceRepository extends BaseRepository
{
    public function findPaging();

    public function findAll();

    public function findUnitPrice();

    public function findById($id);

    public function createUnitPrice(array $objects);

    public function updateUnitPrice($id, array $objects);

    public function deleteUnitPrice($id);
}
