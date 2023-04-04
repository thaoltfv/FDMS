<?php

use App\Models\Dictionary;
use Illuminate\Database\Seeder;
use Rap2hpoutre\FastExcel\FastExcel;


class DictionariesTableSeeder extends Seeder
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
            $sheets = (new FastExcel())->configureCsv()->import(database_path('mocks/dictionaries.csv'));
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
        $lastIndex = Dictionary::query()->latest('id')->first();
        if (isset($lastIndex)) {
            $index = $lastIndex['id'] + 1;
            DB::update("ALTER SEQUENCE dictionaries_id_seq RESTART WITH ".$index.";");
        }
    }
}
