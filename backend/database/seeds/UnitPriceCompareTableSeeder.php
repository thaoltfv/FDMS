<?php

use App\Models\Distance;
use App\Models\Street;
use App\Models\UnitPrice;
use Illuminate\Database\Seeder;
use Rap2hpoutre\FastExcel\FastExcel;


class UnitPriceCompareTableSeeder extends Seeder
{
    public array $validate =[];

    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Throwable
     */
    public function run()
    {
        DB::transaction(function () {
//            UnitPrice::query()->forceDelete();
            $sheets = (new FastExcel())->configureCsv()->import(database_path('mocks/Don_Gia_UBND_DN_TrangBom.csv'));
            $sheets->map(function ($value) {
                $unitPrice = [
                    'province' => 'Đồng Nai',
                    'district' => $value['Quận/Huyện']?? null,
                    'ward' =>$value['Phường'] ==''? null:trim($value['Phường']),
                    'street' =>$value['Đường'] ==''? null:trim($value['Đường']),
                    'distance' =>$value['Đoạn'] ==''? null:trim($value['Đoạn']),
                    'vt1' => (int)$value['VT1'] * 1000,
                    'vt2' => (int)$value['VT2'] * 1000,
                    'vt3' => (int)$value['VT3'] * 1000,
                    'vt4' => (int)$value['VT4'] * 1000,
                    'land_type' => trim($value['Loại đất'])?? null,
                ];
//                if ($value['Đường'] !=''&&$value['Đường'] !=null){
//                   $validateStreet =  Street::query()->whereRaw('name ilike '. "'" . trim($value['Đường']). "'" )->first();
//                    if (!isset($validateStreet->name)){
//                        var_dump($value['Đường']);
//                       $this->validate[]= $unitPrice;
//                   }
//                }
//                if ($value['Đoạn'] !=''&&$value['Đoạn'] !=null){
//                    $validateDistance =  Distance::query()->whereRaw('name ilike '. "'" . trim($value['Đoạn']). "'" )->first();
//                    if (!isset($validateDistance->name)){
//                        $this->validate[]= $unitPrice;
//                    }
//                }
                $unitPrice = new UnitPrice($unitPrice);
                $unitPrice->newQuery()
                    ->insertGetId($unitPrice->attributesToArray());
            });
//            (new FastExcel(  array_values(array_map("unserialize", array_unique(array_map("serialize", $this->validate))))))->export(database_path('compare.xlsx'));

            $sheets = (new FastExcel())->configureCsv()->import(database_path('mocks/Don_Gia_UBND_DN_VinhCuu.csv'));
            $sheets->map(function ($value) {
                $unitPrice = [
                    'province' => 'Đồng Nai',
                    'district' => $value['Quận/Huyện']?? null,
                    'ward' =>$value['Phường'] ==''? null:trim($value['Phường']),
                    'street' =>$value['Đường'] ==''? null:trim($value['Đường']),
                    'distance' =>$value['Đoạn'] ==''? null:trim($value['Đoạn']),
                    'vt1' => (int)$value['VT1'] * 1000,
                    'vt2' => (int)$value['VT2'] * 1000,
                    'vt3' => (int)$value['VT3'] * 1000,
                    'vt4' => (int)$value['VT4'] * 1000,
                    'land_type' => trim($value['Loại đất'])?? null,
                ];
                $unitPrice = new UnitPrice($unitPrice);
                $unitPrice->newQuery()
                    ->insertGetId($unitPrice->attributesToArray());
            });
            $sheets = (new FastExcel())->configureCsv()->import(database_path('mocks/Don_Gia_UBND_DN_XuanLoc.csv'));
            $sheets->map(function ($value) {
                $unitPrice = [
                    'province' => 'Đồng Nai',
                    'district' => $value['Quận/Huyện']?? null,
                    'ward' =>$value['Phường'] ==''? null:trim($value['Phường']),
                    'street' =>$value['Đường'] ==''? null:trim($value['Đường']),
                    'distance' =>$value['Đoạn'] ==''? null:trim($value['Đoạn']),
                    'vt1' => (int)$value['VT1'] * 1000,
                    'vt2' => (int)$value['VT2'] * 1000,
                    'vt3' => (int)$value['VT3'] * 1000,
                    'vt4' => (int)$value['VT4'] * 1000,
                    'land_type' => trim($value['Loại đất'])?? null,
                ];
                $unitPrice = new UnitPrice($unitPrice);
                $unitPrice->newQuery()
                    ->insertGetId($unitPrice->attributesToArray());
            });
        });
    }
}
