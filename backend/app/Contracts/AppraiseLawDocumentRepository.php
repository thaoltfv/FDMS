<?php

namespace App\Contracts;

interface AppraiseLawDocumentRepository extends BaseRepository
{
    public function findPaging();

    public function findAll();

    public function findById($id);

    public function createAppraiseLawDocument(array $objects);

    public function updateAppraiseLawDocument($id, array $objects);

    public function deleteAppraiseLawDocument($id);
}
