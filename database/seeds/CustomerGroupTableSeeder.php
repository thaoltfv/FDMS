<?php

use App\Models\Distance;
use App\Models\District;
use App\Models\Street;
use App\Models\Ward;
use Illuminate\Database\Seeder;
use Rap2hpoutre\FastExcel\FastExcel;


class CustomerGroupTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Throwable
     */
    public function run()
    {
        DB::transaction(function () {
            Distance::query()->where('province_id', '=', 45)->delete();
            Street::query()
                ->where('province_id', '=', 45)
                ->delete();
            $sheets = (new FastExcel())->configureCsv()->import(database_path('mocks/street_compare.csv'));
            $sheets->map(function ($value) {
                if ($value['new_name']) {
                    if ($value['id']) {
                        Street::onlyTrashed()
                            ->where('id', $value['id'])
                            ->restore();
                        Street::query()
                            ->where('id', $value['id'])
                            ->update(
                                array(
                                    'name' => trim($value['new_name']),
                                )
                            );
                        if ($value['distance']) {
                            $distance = array(
                                'name' => trim($value['distance']),
                                'detail' => trim($value['distance']),
                                'district_id' => trim($value['district_id']),
                                'province_id' => trim($value['province_id']),
                                'street_id' => trim($value['id']),
                            );
                            Distance::query()->insertGetId($distance);
                        }
                    } else {
                        $exit = Street::query()
                            ->where('name', '=', trim($value['new_name']))
                            ->where('district_id', '=', $value['district_id'])
                            ->where('province_id', '=', $value['province_id'])
                            ->first();
                        if ($exit) {
                            $streetId = $exit->id;
                        } else {
                            $streetId = Street::query()
                                ->insertGetId(
                                    array(
                                        'name' => trim($value['new_name']),
                                        'district_id' => trim($value['district_id']),
                                        'province_id' => trim($value['province_id']),
                                    )
                                );
                        }
                        if ($value['distance']) {
                            $distance = array(
                                'name' => trim($value['distance']),
                                'detail' => trim($value['distance']),
                                'district_id' => trim($value['district_id']),
                                'province_id' => trim($value['province_id']),
                                'street_id' => $streetId,
                            );
                            Distance::query()->insertGetId($distance);
                        }
                    }
                }
            });
        });
    }
}
