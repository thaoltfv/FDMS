<?php

namespace App\Contracts;

interface CustomerRepository extends BaseRepository
{
    public function findPaging();

    public function findAll();

    public function findById($id);

    public function createCustomer(array $objects);

    public function updateCustomer($id, array $objects);

    public function updateCustomersStatus($ids);

    public function deleteCustomer($id);

}
