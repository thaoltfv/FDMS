<?php

use App\Models\CompareAssetGeneral;
use App\Models\CompareAssetVersion;
use Illuminate\Database\Seeder;


class CompareAssetGeneralSeeder extends Seeder
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
            $sheets = CompareAssetGeneral::query()->select('id')->get();
            CompareAssetVersion::query()->forceDelete();
            $sheets->map(function ($value) {
                var_dump($value['id']);
                CompareAssetVersion::query()
                    ->insertGetId(
                        array(
                            'asset_general_id' => trim($value['id']),
                            'version' => 1,
                        )
                    );
            });
        });
    }
}
