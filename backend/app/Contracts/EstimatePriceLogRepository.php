<?php

namespace App\Contracts;

interface EstimatePriceLogRepository extends BaseRepository
{
    public function findPaging();

    public function findAll();

    public function findById($id);

    public function getLog($id);

    public function createEstimatePriceLog(array $objects);

    public function createLog(array $objects);

    public function updateEstimatePriceLog($id, array $objects);

    public function deleteEstimatePriceLog($id);

    public function countAll();

    public function createIndex();


}
