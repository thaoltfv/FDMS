<?php

namespace App\Contracts;

interface AppraiseOtherInformationRepository extends BaseRepository
{
    public function findPaging();

    public function findAll();

    public function findById($id);

    public function createAppraiseOtherInformation(array $objects);

    public function updateAppraiseOtherInformation($id, array $objects);

    public function deleteAppraiseOtherInformation($id);
}
