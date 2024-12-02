<?php

namespace App\Contracts;

interface ApartmentRepository extends BaseRepository
{
    public function findPaging();

    public function findAll();

    public function findById($id);

    public function createApartment(array $objects);

    public function updateApartment($id, array $objects);

    public function deleteApartment($id);
}
