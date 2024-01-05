<?php

namespace App\Contracts;

interface PreCertificateRepository extends BaseRepository
{
    public function updateStatus($id, $request);

    // public function transferToCertificate($id);
    public function getPreCertificateWorkFlow();

    public function findPaging();

    public function findAll();

    public function findByIdTest($id);

    public function findById($id);

    public function getComparisonFactor($id);

    public function createPreCertificate(array $objects);

    // public function updatePreCertificate($id, array $objects);

    public function updateTangibleComparisonFactor($id, array $objects);

    // public function deletePreCertificate($id);

    public function findAllPreCertificate(array $request);

    public function findPaging_v2();

    // public function getPreCertificateWorkFlow();

    public function postGeneralInfomation(array $object , int $id = null);

    public function getGeneralInfomation(int $id);

    public function updateStatus_v2($id, $request);
    public function updateToOffical($id, $request);

    public function findAppraisePaging();


    public function getPreCertificate(int $id);

    // public function updatePreCertificateGeneral(int $id , array $object);

    public function otherDocumentRemove($id, $request);

    public function otherDocumentDownload($id, $request);

    public function otherDocumentUpload($id, $typeDocument, $request);

    public function saleDocumentUpload($id, $request);

    public function getProcessingTime();

    public function updateAppraisersTeam(int $id , $request);

    // public function getFinishPreCertificateAssets();

    // public function getFinishPreCertificateApartment();

    public function getComparisonAppraise( array $ids);

    // public function exportPreCertificateBriefs();

    // public function exportSelectedPreCertificateAssets();


    // public function updatePreCertificateVersion(int $PreCertificateId, array $object);

    // public function getPreCertificateStatus(int $id);

    // public function getPreCertificateAppraiseReportData($id);

    public function updateSubStatusFromConfig($object);

    public function uploadDocument($id, $description, $request);

    public function deleteDocument($id);
}
