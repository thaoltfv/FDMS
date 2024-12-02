<?php

use App\Models\Branch;
use App\Models\Distance;
use App\Models\Street;
use App\Models\UnitPrice;
use App\Models\Ward;
use Illuminate\Database\Seeder;
use Rap2hpoutre\FastExcel\FastExcel;


class UnitPriceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Throwable
     */
    public function run()
    {
        try {
            $sheets = (new FastExcel())->import(database_path('mocks/unit_prices.xlsx'));
            foreach ($sheets as $value) {
                $ward = [];
                $treet = [];
                $distance = [];
                if ($value['Tỉnh/Thành'] == 'THÀNH PHỐ BIÊN HÒA ') {
                    $districtId = 432;
                } else {
                    $districtId = 433;
                }
                if ($value['Phường']) {
                    $name = str_replace(['xã ', 'phường '], '', mb_strtolower($value['Phường']));
                    $query = 'name ilike ' . "'%" . $name . "%'";
                    $ward = Ward::query()->select()
                        ->whereRaw($query)
                        ->with(['district', 'province'])
                        ->where('district_id', '=', $districtId,)
                        ->first();
                }
                if ($value['Đường']) {
                    $name = str_replace(['đường ', 'từ '], '', mb_strtolower($value['Đường']));
                    $query = 'name ilike ' . "'%" . $name . "%'";
                    $treet = Street::query()->select()
                        ->with(['district', 'province'])
                        ->whereRaw($query)
                        ->where('district_id', '=', $districtId,)
                        ->first();
                }
                if ($value['Đoạn']) {
                    $name = str_replace(['đoạn ', 'từ '], '', mb_strtolower($value['Đoạn']));
                    $query = 'detail ilike ' . "'%" . $name . "%'";
                    $distance = Distance::query()->select()
                        ->with(['district', 'province', 'street'])
                        ->whereRaw($query)
                        ->where('district_id', '=', $districtId,)
                        ->first();

                }
                if ($distance) {
                    $unitPrice = [
                        'province' => $distance->province->name,
                        'district' => $distance->district->name,
                        'ward' => null,
                        'street' => $distance->street->name,
                        'distance' => $distance->detail,
                        'vt1' => (int)$value['VT1']* 1000,
                        'vt2' => (int)$value['VT2']* 1000,
                        'vt3' => (int)$value['VT3']* 1000,
                        'vt4' => (int)$value['VT4']* 1000,
                        'land_type' => $value['Loại đất']
                    ];

                    $unitPrice = new UnitPrice($unitPrice);
                    $unitPrice->newQuery()
                        ->insertGetId($unitPrice->attributesToArray());
                }

                if ($treet) {
                    $unitPrice = [
                        'province' => $treet->province->name,
                        'district' => $treet->district->name,
                        'ward' => null,
                        'street' => $treet->name,
                        'distance' => null,
                        'vt1' => (int)$value['VT1']* 1000,
                        'vt2' => (int)$value['VT2']* 1000,
                        'vt3' => (int)$value['VT3']* 1000,
                        'vt4' => (int)$value['VT4']* 1000,
                        'land_type' => $value['Loại đất']
                    ];

                    $unitPrice = new UnitPrice($unitPrice);
                    $unitPrice->newQuery()
                        ->insertGetId($unitPrice->attributesToArray());
                }

                if ($ward) {
                    $unitPrice = [
                        'province' => $ward->province->name,
                        'district' => $ward->district->name,
                        'ward' => $ward->name,
                        'street' => null,
                        'distance' => null,
                        'vt1' => (int)$value['VT1'] * 1000,
                        'vt2' => (int)$value['VT2'] * 1000,
                        'vt3' => (int)$value['VT3'] * 1000,
                        'vt4' => (int)$value['VT4'] * 1000,
                        'land_type' => $value['Loại đất']
                    ];

                    $unitPrice = new UnitPrice($unitPrice);
                    $unitPrice->newQuery()
                        ->insertGetId($unitPrice->attributesToArray());
                }
            }
        } catch (Exception $exception) {
            error_log($exception);
        }

    }
}
