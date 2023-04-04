<?php

use App\Models\Dictionary;
use Illuminate\Database\Seeder;
use Rap2hpoutre\FastExcel\FastExcel;


class UpdateDictionariesTableSeeder extends Seeder
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
                return Dictionary::query()
                    ->create(
                        array(
                            'type'                        => 'LOAI_NHA',
                            'description'                 => 'CÔNG TRÌNH KHÁC',
                            'useful_year'                 => null,
                            'created_by'                 => 'admin',
                        )
                    );
        });

    }
}
