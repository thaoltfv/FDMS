<?php

use App\Models\Branch;
use App\Models\Distance;
use App\Models\Street;
use Illuminate\Database\Seeder;
use Rap2hpoutre\FastExcel\FastExcel;


class DistanceTableSeeder extends Seeder
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
            DB::transaction(function () {
                $sheets = (new FastExcel())->configureCsv()->import(database_path('mocks/distances.csv'));
                foreach ($sheets as $value) {
                    $street = [];
                    $distance = [];
                    if ($value['Tỉnh/Thành'] == 'THÀNH PHỐ BIÊN HÒA ') {
                        $districtId = 432;
                    } else {
                        $districtId = 433;
                    }
                    if ($value['Đoạn'] && $value['Đường']) {
                        $name = str_replace(['Đường ', 'Từ '], '', mb_strtolower($value['Đường']));
                        $query = 'name ilike ' . "'%" . $name . "%'";
                        $street = Street::query()->select()
                            ->with(['district', 'province'])
                            ->whereRaw($query)
                            ->where('district_id', '=', $districtId,)
                            ->first();
                    }

                    if ($street) {
                        $distance = array(
                            'name' => $value['Đoạn'],
                            'detail' => $value['Đường'] . ' ' . $value['Đoạn'],
                            'vt1' => (int)$value['VT1'],
                            'vt2' => (int)$value['VT2'],
                            'vt3' => (int)$value['VT3'],
                            'vt4' => (int)$value['VT4'],
                            'district_id' => $street['district_id'],
                            'province_id' => $street['province_id'],
                            'street_id' => $street['id']
                        );

                        Distance::query()->insertGetId($distance);
                    }
                }
            });

        }catch (Exception $exception){
            error_log($exception);
        }

    }
}
