<?php

use App\Models\Apartment;
use App\Models\CompareAssetGeneral;
use App\Models\ComparePropertyDetail;
use App\Models\Distance;
use App\Models\District;
use App\Models\Street;
use App\Models\UnitPrice;
use App\Models\Ward;
use Illuminate\Database\Seeder;
use Rap2hpoutre\FastExcel\FastExcel;


class updateAssetGeneralTableSeeder extends Seeder
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
            $sheets = (new FastExcel())->configureCsv()->import(database_path('mocks/wrong_land_price.csv'));
            $sheets->map(function ($value) {
                $asset = CompareAssetGeneral::query()
                    ->where('id', '=', $value['IDTSSS'])
                    ->first();
                if ($asset) {
                    $max = 0;
                    $totalLandUnitPrice = $asset->total_raw_amount + $asset->convert_fee_total;
                    $general = new CompareAssetGeneral($asset->toArray());
                    $general->total_land_unit_price=$totalLandUnitPrice;
                    $general->newQuery()->updateOrInsert(['id' =>  $asset->id], $general->attributesToArray());
                    foreach ($asset->properties as $property) {
                        foreach ($property->propertyDetail as $propertyDetail) {
                            if ($propertyDetail->convert_fee == 0) {
                                $max = $propertyDetail->circular_unit_price;
                            }
                        }
                        foreach ($property->propertyDetail as $propertyDetail) {
                            $priceLand = ($totalLandUnitPrice / $property->asset_general_land_sum_area - ($max - $propertyDetail->circular_unit_price));
                            ComparePropertyDetail::query()
                                ->where('id', '=', $propertyDetail->id)
                                ->update([
                                    'price_land' => (int)$priceLand
                                ]);
                        }
                    }
                }

            });

        });
    }
}
