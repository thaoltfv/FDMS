<?php

namespace App\Contracts;

interface OtherCertificateAssetRepository extends BaseRepository
{
    public function postStep1(array $object, int $id = null);
    public function getStep1(int $id);
    public function postStep2(array $object, int $otherAssetId);
    public function getStep2(int $otherAssetId);
    public function postStep3(array $object, int $otherAssetId);
    public function getStep3(int $otherAssetId);
    public function postStep4(array $object, int $otherAssetId);
    public function getStep4(int $otherAssetId);
    public function getAll(int $otherAssetId);

}
