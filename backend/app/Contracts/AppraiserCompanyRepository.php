<?php

namespace App\Contracts;

interface AppraiserCompanyRepository extends BaseRepository
{
    public function findPaging();

    public function findAll();

    public function findById($id);

    public function getCompany();

    public function getOneAppraiserCompany();

    public function createAppraiserCompany(array $objects);

    public function updateAppraiserCompany($id, array $objects);

    public function deleteAppraiserCompany($id);
}
