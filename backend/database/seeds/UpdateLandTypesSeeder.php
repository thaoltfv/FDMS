<?php

use App\Enum\EstimateAssetDefault;
use App\Models\Dictionary;
use Illuminate\Database\Seeder;
use Rap2hpoutre\FastExcel\FastExcel;


class UpdateLandTypesSeeder extends Seeder
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
            $sheets = (new FastExcel())->configureCsv()->import(database_path('mocks/dictionaries_update.csv'));
            $landTypes = Dictionary::query()
                ->where('type', '=', EstimateAssetDefault::DICTIONARY_LAND_TYPE)
                ->get();
            foreach ($landTypes as $landType) {
                $status = false;
                foreach ($sheets as $value) {
                    if ($value['DESCRIPTION'] == $landType->description) {
                        $status = true;
                        Dictionary::query()
                            ->where('id', $landType->id)
                            ->update([
                                'acronym' => $value['acronym'],
                            ]);
                    }
                }
                if ($status == false) {
                    Dictionary::query()
                        ->where('id', $landType->id)
                        ->update([
                            'status' => 0
                        ]);
                }
            }
        });
    }
}
