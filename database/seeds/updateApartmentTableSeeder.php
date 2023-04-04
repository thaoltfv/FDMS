<?php

use App\Models\Apartment;
use App\Models\Distance;
use App\Models\District;
use App\Models\Street;
use App\Models\UnitPrice;
use App\Models\Ward;
use Illuminate\Database\Seeder;
use Rap2hpoutre\FastExcel\FastExcel;


class updateApartmentTableSeeder extends Seeder
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
            $sheets = (new FastExcel())->configureCsv()->import(database_path('mocks/apartment_update.csv'));
            $sheets->map(function ($value) {
                $apartment =  Apartment::query()
                    ->where('name','=',$value['Tên chung cư'])
                    ->first();
                if ($apartment){
                    Apartment::query()
                        ->where('id', $apartment['id'])
                        ->update(
                            [
                                'name' => $value['Tên chung cư'],
                                'province_id' => $value['province_id'],
                                'district_id' => $value['district_id'],
                                'coordinates' => $value['Tọa độ'],
                                'address' => $value['Địa chỉ'],
                            ]
                        );
                }


            });

        });
    }
}
