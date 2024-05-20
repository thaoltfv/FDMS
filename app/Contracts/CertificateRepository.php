<?php

namespace App\Contracts;

interface CertificateRepository extends BaseRepository
{
    public function updateStatus($id, $request);

    public function findPaging();

    public function findAll();

    public function findByIdTest($id);

    public function findById($id);

    public function dataPrintExport($id);

    public function getComparisonFactor($id);

    public function createCertificate(array $objects);

    public function updateCertificate($id, array $objects);

    public function updateTangibleComparisonFactor($id, array $objects);

    public function deleteCertificate($id);

    public function findAllCerificate(array $request);

    public function findPaging_v2();

    public function getCertificateWorkFlow();

    public function postGeneralInfomation(array $object, int $id = null);

    public function getGeneralInfomation(int $id);

    public function updateStatus_v2($id, $request);

    public function findAppraisePaging();

    public function updateCertificate_v2(array $object, int $certificateId);

    public function getCertificate(int $id);

    public function updateCertificateGeneral(int $id, array $object);

    public function otherDocumentRemove($id, $request);

    public function otherDocumentDownload($id, $request);

    public function otherDocumentUpload($id, $request);

    public function otherDocumentOriginalUpload($id, $request);

    public function testDocumentUpload($request);

    public function saleDocumentUpload($id, $request);

    public function getProcessingTime();

    public function updateAppraisersTeam(int $id, $request);

    public function getFinishCertificateAssets();

    public function getFinishCertificateApartment();

    public function getComparisonAppraise(array $ids);

    public function exportCertificateBriefs();

    public function exportSelectedCertificateAssets();

    public function updateCertificateV3(array $object, int $certificateId);

    public function updateCertificateVersion(int $certificateId, array $object);

    public function getCertificateStatus(int $id);

    public function getCertificateAppraiseReportData($id);

    public function updateSubStatusFromConfig($object);

    public function uploadDocument($id, $description, $request);

    public function deleteDocument($id);
}
