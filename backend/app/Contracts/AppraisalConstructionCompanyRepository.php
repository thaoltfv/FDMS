<?php

namespace App\Contracts;

interface AppraisalConstructionCompanyRepository extends BaseRepository
{
    public function findPaging();

    public function findAll();

    public function findById($id);

    public function createAppraiserConstructionCompany(array $objects);

    public function updateAppraiserConstructionCompany($id, array $objects);

    public function deleteAppraiserConstructionCompany($id);
}
