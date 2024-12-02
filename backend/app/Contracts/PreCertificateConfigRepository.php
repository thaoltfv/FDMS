<?php

namespace App\Contracts;

interface PreCertificateConfigRepository extends BaseRepository
{

    public function findAll();
    public function findByName($name);
    public function updateConfig($id, $name);

}
