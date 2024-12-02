<?php

use App\Models\Dictionary;
use Illuminate\Database\Seeder;
use Rap2hpoutre\FastExcel\FastExcel;


class UpdateDictionariesAppraisePicTableSeeder extends Seeder
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
            $sheets = (new FastExcel())->configureCsv()->import(database_path('mocks/dictionaries_appraise.csv'));
            $sheets->map(function ($value) {
                return Dictionary::query()
                    ->updateOrCreate(
                        array(
                            'type'                        => $value['TYPE'],
                            'description'                 => $value['DESCRIPTION'],
                            'useful_year'                 => (int)$value['USEFUL_YEAR'],
                            'created_by'                 => 'admin',
                        )
                    );
            });
        });
    }
}
