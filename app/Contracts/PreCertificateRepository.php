<?php

namespace App\Contracts;

interface PreCertificateRepository extends BaseRepository
{

    // public function transferToCertificate($id);
    public function getPreCertificateWorkFlow();


    public function findAll();

    public function findByIdTest($id);

    public function findById($id);

    public function createPreCertificate(array $objects);

    public function findPaging_v2();

    public function postGeneralInfomation(array $object , int $id = null);


    public function updateStatus_v2($id, $request);
    public function updateToOffical($id, $request);


    public function getPreCertificate(int $id);


    public function otherDocumentRemove($id, $request);

    public function otherDocumentDownload($id, $request);

    public function otherDocumentUpload($id, $typeDocument, $request);


    public function getProcessingTime();


}
