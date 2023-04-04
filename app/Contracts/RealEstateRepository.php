<?php

namespace App\Contracts;

interface RealEstateRepository extends BaseRepository
{
    public function findPaging();
    public function getReportData($id);
    public function getDataForShinhan($id);
    public function updateRealEstateAditionalData(array $realEstate, $id);
}
