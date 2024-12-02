<?php

use App\Models\Apartment;
use App\Models\BlockList;
use Illuminate\Database\Seeder;
use Rap2hpoutre\FastExcel\FastExcel;


class ApartmentsTableSeeder extends Seeder
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
            $sheets = (new FastExcel())->configureCsv()->import(database_path('mocks/apartment.csv'));
            $sheets->map(function ($value) {
                $apartmentId = Apartment::query()
                    ->insertGetId(
                        array(
                            'name' => $value['name'],
                            'province_id' => $value['province_id'],
                            'district_id' => $value['district_id'],
                            'coordinates' => $value['lat'].','.$value['lng'],
                        )
                    );
                return BlockList::query()
                    ->insertGetId(
                        array(
                            'name' => 'Block A',
                            'apartment_id' => $apartmentId,
                            'coordinates' => $value['lat'].','.$value['lng'],
                        )
                    );
            });
        });
    }
}
