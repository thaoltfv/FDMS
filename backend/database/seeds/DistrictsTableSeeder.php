<?php

use App\Models\District;
use Illuminate\Database\Seeder;
use Rap2hpoutre\FastExcel\FastExcel;


class DistrictsTableSeeder extends Seeder
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
            $sheets = (new FastExcel())->configureCsv()->import(database_path('mocks/district.csv'));
            $sheets->map(function ($value) {
                return District::query()
                    ->updateOrCreate(
                        array(
                            'id'                          => $value['id'],
                            'name'                        => $value['name'],
                            'province_id'                 => $value['province_id'],
                        )
                    );
            });
        });
        $lastIndex = District::query()->latest('id')->first();
        if (isset($lastIndex)) {
            $index = $lastIndex['id'] + 1;
            DB::update("ALTER SEQUENCE districts_id_seq RESTART WITH ".$index.";");
        }
    }
}
