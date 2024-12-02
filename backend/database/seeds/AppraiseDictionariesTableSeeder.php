<?php

use App\Models\AppraiseDictionary;
use App\Models\Dictionary;
use Illuminate\Database\Seeder;
use Rap2hpoutre\FastExcel\FastExcel;


class AppraiseDictionariesTableSeeder extends Seeder
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
            $sheets = (new FastExcel())->configureCsv()->import(database_path('mocks/appraise_dictionary.csv'));
            $sheets->map(function ($value) {
                return AppraiseDictionary::query()
                    ->create(
                        array(
                            'type' => $value['type'],
                            'appraise_title' => $value['appraise_title'],
                            'appraise_point' => $value['appraise_point'],
                            'asset_title' => $value['asset_title'],
                            'asset_point' => $value['asset_point'],
                            'description' => $value['description'],
                            'difference_point' => $value['difference_point'],
                            'appraise_percent' => $value['appraise_percent'],
                            'asset_percent' => $value['asset_percent'],
                            'adjust_percent' => $value['adjust_percent'],
                        )
                    );
            });
        });

    }
}
