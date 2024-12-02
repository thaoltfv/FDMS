<?php

use App\Models\AppraiseOtherInformation;
use Illuminate\Database\Seeder;
use Rap2hpoutre\FastExcel\FastExcel;


class AppraiseOtherInformationSeeder extends Seeder
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
            $sheets = (new FastExcel())->configureCsv()->import(database_path('mocks/appraise_other_information.csv'));
            $sheets->map(function ($value) {
                return AppraiseOtherInformation::query()
                    ->updateOrCreate(
                        array(
                            'name' => $value['name'],
                            'description' => $value['description'],
                            'type' => $value['type'],
                        ),
                        array(
                            'is_defaults' => $value['is_defaults'],
                        )
                    );
            });
        });

    }
}
