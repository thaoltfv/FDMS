<?php

use App\Models\Province;
use Illuminate\Database\Seeder;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\Console\Output\ConsoleOutput;


class ProvincesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Throwable
     */
    public function run()
    {
        $output = new ConsoleOutput();

        DB::transaction(function () {
            $sheets = (new FastExcel())->configureCsv()->import(database_path('mocks/province.csv'));
            $sheets->map(function ($value) {
                return Province::query()
                    ->updateOrCreate(
                        array(
                            'id'                          => $value['id'],
                            'name'                        => $value['name'],
                        )
                    );
            });
        });
        $lastIndex = Province::query()->latest('id')->first();
        if (isset($lastIndex)) {
            $index = $lastIndex['id'] + 1;
            DB::update("ALTER SEQUENCE provinces_id_seq RESTART WITH ".$index.";");
        }
    }
}
