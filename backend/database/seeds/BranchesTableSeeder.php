<?php

use App\Models\Branch;
use App\Models\District;
use App\Models\Ward;
use Illuminate\Database\Seeder;
use Rap2hpoutre\FastExcel\FastExcel;


class BranchesTableSeeder extends Seeder
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
            $sheets = (new FastExcel())->configureCsv()->import(database_path('mocks/branches.csv'));
            $sheets->map(function ($value) {
                return Branch::query()
                    ->updateOrCreate(
                        array(
                            'id'                          => $value['id'],
                            'name'                        => $value['name'],
                            'address'                     => $value['address'],
                            'acronym'                     => $value['acronym'],
                        )
                    );
            });
        });
        $lastIndex = Branch::query()->latest('id')->first();
        if (isset($lastIndex)) {
            $index = $lastIndex['id'] + 1;
            DB::update("ALTER SEQUENCE branches_id_seq RESTART WITH ".$index.";");
        }
    }
}
