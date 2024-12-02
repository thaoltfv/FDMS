<?php

namespace App\Contracts;

interface ProjectRepository extends BaseRepository
{
    public function findPaging();
    public function createProject(array $object);
    public function getProjectById(int $id);
    public function updateProject(int $id, array $object);
    public function updateOrCreateBlock(int $projectId, array $object);
    public function updateOrCreateFloor(int $blockId, array $object);
    public function updateOrCreateApartment(int $floorId, array $object);
    public function updateStatus(int $id,bool $status,string $type);
    public function getAll();
    public function getProjectActiveById(int $id);
    public function getAllActive();
    public function getApartmentByFloorId(int $floorId);
    public function getProjectByDistrictId();
}
