<?php

namespace App\Repositories;

use App\Contracts\DonavaOldEstatesRepository;

class EloquentDonavaOldEstatesRepository extends EloquentRepository implements DonavaOldEstatesRepository
{
    public function findAll()
    {
        return $this->model
            ->query()
            ->with([
                'esLand',
                'esLandAverage',
                'esBuild',
                'esValue',
                'esRoundabout',
                'esImage',
                'esShape',
                'esTradeType',
                'esLand.landType',
                'esApartment',
            ])
            ->get();
    }

    public function findPaging($perPage, $page)
    {
        return $this->model
            ->query()
            ->with([
                'esLand',
                'esLandAverage',
                'esBuild',
                'esValue',
                'esRoundabout',
                'esImage',
                'esShape',
                'esTradeType',
                'esLand.landType',
                'esApartment',
            ])
            ->forPage($page, $perPage)
            ->get();
    }
}
