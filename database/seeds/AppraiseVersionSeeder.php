<?php

use App\Models\Appraise;
use App\Models\AppraiseVersion;
use App\Models\CompareAssetGeneral;
use App\Models\CompareAssetVersion;
use Illuminate\Database\Seeder;


class AppraiseVersionSeeder extends Seeder
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
            $sheets = Appraise::query()->select('id')->get();
            AppraiseVersion::query()->forceDelete();
            $sheets->map(function ($value) {
                AppraiseVersion::query()
                    ->insertGetId(
                        array(
                            'appraise_id' => trim($value['id']),
                            'version' => '2.0',
                            'status' => 2,
                        )
                    );
            });
        });
    }
}
