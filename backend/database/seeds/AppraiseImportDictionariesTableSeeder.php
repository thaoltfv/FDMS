<?php

use App\Models\AppraiseDictionary;
use App\Models\Dictionary;
use Illuminate\Database\Seeder;
use Rap2hpoutre\FastExcel\FastExcel;


class AppraiseImportDictionariesTableSeeder extends Seeder
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
            $sheets = (new FastExcel())->configureCsv()->import(database_path('mocks/appraise_dictionary_update.csv'));
            AppraiseDictionary::query()->forceDelete();
            $sheets->map(function ($value) {
                $description ='TƯƠNG ĐỒNG';
                if ($value['adjust_percent']>0){
                    $description = 'KÉM THUẬN LỢI HƠN';
                }
                if ($value['adjust_percent']<0){
                    $description = 'THUẬN LỢI HƠN';
                }

                return AppraiseDictionary::query()
                    ->updateOrCreate(
                        array(
                            'type' => $value['type'],
                            'appraise_title' => $value['appraise_title'],
                            'asset_title' => $value['asset_title'],
                        ),
                        array(
                            'appraise_point' => $value['appraise_point'],
                            'asset_point' => $value['asset_point'],
                            'description' => $description,
                            'difference_point' => 0,
                            'appraise_percent' => 0,
                            'asset_percent' => 0,
                            'adjust_percent' => $value['adjust_percent'],
                        )
                    );
            });
        });

    }
}
