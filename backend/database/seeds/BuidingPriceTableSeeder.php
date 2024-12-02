<?php

use App\Models\BuildingPrice;
use App\Models\Dictionary;
use Illuminate\Database\Seeder;
use Rap2hpoutre\FastExcel\FastExcel;


class BuidingPriceTableSeeder extends Seeder
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
            $sheets = (new FastExcel())->configureCsv()->import(database_path('mocks/building_prices.csv'));
            foreach ($sheets as $value) {
                $buildingCategory = Dictionary::query()
                    ->where('type', '=', 'LOAI_NHA')
                    ->where('description', '=', $value['building_category'])
                    ->first();

                $level = Dictionary::query()
                    ->where('type', '=', 'CAP_NHA')
                    ->where('description', '=', $value['level'])
                    ->first();

                $rate = Dictionary::query()
                    ->where('type', '=', 'HANG_NHA')
                    ->where('description', '=', $value['rate'])
                    ->first();

                $structure = Dictionary::query()
                    ->where('type', '=', 'CAU_TRUC_BIET_THU')
                    ->where('description', '=', $value['structure'])
                    ->first();

                $crane = Dictionary::query()
                    ->where('type', '=', 'CAU_TRUC_NHA_XUONG')
                    ->where('description', '=', $value['crane'])
                    ->first();

                $aperture = Dictionary::query()
                    ->where('type', '=', 'KHAU_DO')
                    ->where('description', '=', $value['aperture'])
                    ->first();

                $factoryType = Dictionary::query()
                    ->where('type', '=', 'LOAI_NHA_MAY')
                    ->where('description', '=', $value['factory_type'])
                    ->first();

                $building = [
                    'building_category' => $buildingCategory->id??null,
                    'level' => $level->id??null,
                    'rate' => $rate->id??null,
                    'structure' => $structure->id??null,
                    'crane' => $crane->id??null,
                    'aperture' => $aperture->id??null,
                    'factory_type' => $factoryType->id??null,
                    'unit_price_m2' => (int)$value['unit_price_m2'],
                    'effect_from' => '1/1/2021',
                    'effect_to' => '1/1/2022',
                ];
                BuildingPrice::query()->insertGetId($building);
            }

        });

    }
}
