<?php

use App\Models\Distance;
use App\Models\Street;
use App\Models\UnitPrice;
use Illuminate\Database\Seeder;
use Rap2hpoutre\FastExcel\FastExcel;


class UpdateStreetTableSeeder extends Seeder
{
    public array $validate = [];

    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Throwable
     */
    public function run()
    {
        DB::transaction(function () {
            $sheets = (new FastExcel())->configureCsv()->import(database_path('mocks/street_cam_my.csv'));
            Distance::query()->where('district_id', '=', 441)->delete();
            $sheets->map(function ($value) {
                if ($value['name']) {
                        $exit = Street::query()
                            ->where('name', '=', trim($value['name']))
                            ->where('district_id', '=', $value['district_id'])
                            ->first();
                        if ($exit) {
                            $streetId = $exit->id;
                        } else {
                            $streetId = Street::query()
                                ->insertGetId(
                                    array(
                                        'name' => trim($value['name']),
                                        'district_id' => trim($value['district_id']),
                                        'province_id' => 45,
                                    )
                                );
                        }
                        if ($value['distance']) {
                            $distance = array(
                                'name' => trim($value['distance']),
                                'detail' => trim($value['distance']),
                                'district_id' => trim($value['district_id']),
                                'province_id' => 45,
                                'street_id' => $streetId,
                            );
                            $distanceExit = Distance::query()
                                ->where('name', '=', trim($value['distance']))
                                ->where('street_id', '=', $streetId)
                                ->first();
                            if ($distanceExit==null) {
                                Distance::query()->insert($distance);
                            }
                        }
                    }
            });
        });
    }
}
