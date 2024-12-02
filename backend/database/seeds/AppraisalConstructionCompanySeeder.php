<?php

use App\Models\AppraisalConstructionCompany;
use App\Models\AppraiseOtherInformation;
use Illuminate\Database\Seeder;
use Rap2hpoutre\FastExcel\FastExcel;


class AppraisalConstructionCompanySeeder extends Seeder
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
            $sheets = (new FastExcel())->configureCsv()->import(database_path('mocks/appraisal_construction_companies.csv'));
            $sheets->map(function ($value) {
                return AppraisalConstructionCompany::query()
                    ->updateOrCreate(
                        array(
                            'name' => $value['name'],
                            'address' => $value['address'],
                            'phone_number' => $value['phone_number'],
                        ),
                        array(
                            'manager_name' => $value['manager_name'],
                            'unit_price_m2' => $value['unit_price_m2'],
                            'is_defaults' => true,
                        )
                    );
            });
        });

    }
}
