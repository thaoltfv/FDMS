<?php

namespace App\Contracts;

interface CertificateAssetRepository extends BaseRepository
{
    public function findPaging();

    public function findAll();

    public function findById($id);

    public function findByIds($ids);

    public function createAppraise(array $objects);

    public function updateAppraise($id, array $objects);

    public function updateComparisonFactor($objects);
	
    public function getComparisonFactor($id);

    public function updateTangibleComparisonFactor($objects);

    public function deleteAppraise($id);
}
