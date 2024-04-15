<?php

namespace App\Contracts;

interface PriceEstimateRepository extends BaseRepository
{

    public function findPaging();

    public function findAll();

    public function findByIdTest($id);

    public function findById($id);

    public function findByIds($ids);

    public function createPriceEstimate(array $objects);

    public function updatePriceEstimate($id, array $objects);

    public function deletePriceEstimate($id);

    public function getPriceEstimateDataFull($priceEstimateId);

    public function postGeneralInfomation(array $object, int $id = null);

    public function updateStep2(array $objects, int $appraiseId);

    public function step3Final(array $object, int $id = null);

    public function getPriceEstimateFinal($price_estimate_id);

    public function moveToAppraise($id);

    public function postApartmentInformation(array $object, int $id = null);

    public function step3FinalApartment(array $object, int $id = null);

    public function moveToApartmentAsset($id);

    public function getPriceEstimateDataFullForPreCertificate($preCertificateId);
}
