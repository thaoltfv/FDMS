<?php

namespace App\Contracts;

interface PreCertificateConfigRepository extends BaseRepository
{
    public function findPaging();

    // public function findAll();

    // public function findByName($name);

}
