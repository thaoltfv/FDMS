<?php

use App\Models\Dictionary;
use Illuminate\Database\Seeder;
use Rap2hpoutre\FastExcel\FastExcel;

class UpdateDictionariesAddChucVuSeeder extends Seeder
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
            $sheets = (new FastExcel())->configureCsv()->import(database_path('mocks/donava_positions.csv'));
            $sheets->map(function ($value) {
                return Dictionary::query()
                    ->updateOrCreate(
                        array(
                            'type'                        => $value['TYPE'],
                            'description'                 => $value['DESCRIPTION'],
                            'useful_year'                 => (int)$value['USEFUL_YEAR'],
                            'status'                      => (int)$value['STATUS'],
                            'created_by'                  => $value['CREATED_BY'],
                        ),
						array(
                            'acronym'                     => $value['ACRONYM'],
                        ),
                    );
            });
        });
    }
}
