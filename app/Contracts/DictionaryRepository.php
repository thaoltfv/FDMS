<?php

namespace App\Contracts;

interface DictionaryRepository extends BaseRepository
{
    public function findPaging();

    public function findAll();

    public function findByType($type);

    public function findAllByType($type);

    public function findByName($name);

    public function createDictionary(array $objects);

    public function updateDictionary($id, array $objects);

    public function getToken();

    public function deleteDictionary($id);
}
