<?php

use App\Models\District;
use App\Models\Ward;
use Illuminate\Database\Seeder;
use Rap2hpoutre\FastExcel\FastExcel;


class WardsTableSeeder extends Seeder
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
            $sheets = (new FastExcel())->configureCsv()->import(database_path('mocks/ward.csv'));
            $sheets->map(function ($value) {
                $district = District::query()->select()->where('id','=',$value['district_id'])->first();
                return Ward::query()
                    ->updateOrCreate(
                        array(
                            'id'                          => $value['id'],
                            'name'                        => $value['name'],
                            'district_id'                 => $value['district_id'],
                            'province_id'                 => $district->province_id,
                        )
                    );
            });
        });
        $lastIndex = Ward::query()->latest('id')->first();
        if (isset($lastIndex)) {
            $index = $lastIndex['id'] + 1;
            DB::update("ALTER SEQUENCE wards_id_seq RESTART WITH ".$index.";");
        }
    }
}
