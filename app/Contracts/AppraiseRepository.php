<?php

namespace App\Contracts;

interface AppraiseRepository extends BaseRepository
{
    public function updateStatus($id, $request);

    public function findPaging();

    public function findAll();

    public function findByIdTest($id);

    public function findById($id);

    public function findByIds($ids);

    public function createAppraise(array $objects);

    public function updateAppraise($id, array $objects);

    public function updateComparisonFactor($objects);

    public function getComparisonFactor($id);

    public function updateTangibleComparisonFactor($objects);

    public function deleteAppraise($id);

    public function findAllAppraises(array $request);

    public function getInfomation(int $id);

    public function postGeneralInfomation(array $object , int $id = null);

    public function updateDistance(int $object , int $id = null);
    public function updateMucdichchinh(int $object , int $id = null);

    public function updateNoteHienTrang($object , int $id = null);

    public function getLandInfomation(int $appraiseId);

    public function postLandDetailInfomation(array $objects , int $appraiseId);

    public function getConstruction(int $appraiseId);

    public function postConstructionInfomation(array $objects , int $appraiseId);

    public function getLaw(int $appraiseId);

    public function postLawInfomation(array $objects , int $appraiseId);

    public function getAppraisalFacility(int $appraiseId);

    public function postAppraisalFacility(array $objects , int $appraiseId);

    public function getAssets(int $appraiseId);

    public function checkAppraiseExists(int $appraiseId);

    public function postAssets(array $objects , int $appraiseId);

    public function getAppraiseStep(int $appraiseId = null);

    public function getAppraiseCalculatelData(int $appraiseId);

    public function getAppraiseData(int $appraiseId);

    public function postOtherAssets(array $object , int $appraiseId);

    // public function resetDataStep6(int $appraiseId);

    public function getApraiseDataStepOneToSix(int $appraiseId);

    public function updateComparisonFactor_V2($objects,int $id);

    public function updateComparisonFactor_V2_ver1($objects,int $id);

    public function findPaging_v2();

    public function updateConstructionCompany(array $object , int $appraiseId);

    public function updateRoundAppraiseTotal($appraiseId, $objects);

    public function updateTangibleComparisonFactor_V2($objects , int $appraiseId);

    public function exportCertificateAssets();

    public function countAppraiseAsset();

    public function updateEstimateAssetPrice(int $id);

    public function getAppraiseDetail(int $id);

    public function getApartmentDetail(int $id);
}
