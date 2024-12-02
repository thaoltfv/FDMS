<?php

namespace App\Http\Controllers\MigrateData;

use App\Contracts\MigrateStatusRepository;
use App\Models\Distance;
use App\Models\Street;
use App\Models\UnitPrice;
use App\Models\Ward;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Rap2hpoutre\FastExcel\FastExcel;

class ImportAsyncUnitPriceData implements ShouldQueue
{

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private MigrateStatusRepository $migrateStatusRepository;

    public function __construct(MigrateStatusRepository       $migrateStatusRepository)
    {
        $this->migrateStatusRepository = $migrateStatusRepository;
    }

    /**
     *
     */
    public function handle()
    {
        try {
            Log::info('Import unit price data start');
            $sheets = (new FastExcel())->import(database_path('mocks/unit_prices.xlsx'));
            Log::info('Import unit price data size' . sizeof($sheets));
            $migrateStatusId = $this->migrateStatusRepository->createMigrateStatus([
                'limit' =>  sizeof($sheets),
                'page' => 1,
                'status' => 0,
                'type' => 3,
                'total_records' => sizeof($sheets),
            ]);
            foreach ($sheets as $value) {
                $ward = [];
                $street = [];
                $distance = [];
                if ($value['Tỉnh/Thành'] == 'THÀNH PHỐ BIÊN HÒA ') {
                    $districtId = 432;
                } else {
                    $districtId = 433;
                }
                if ($value['Phường']) {
                    $name = str_replace(['Xã ', 'Phường '], '', $value['Phường']);
                    $query = 'name ilike ' . "'%" . $name . "%'";
                    $ward = Ward::query()->select()
                        ->whereRaw($query)
                        ->with(['district', 'province'])
                        ->where('district_id', '=', $districtId,)
                        ->first();
                }
                if ($value['Đường']) {
                    $name = str_replace(['Đường '], '', $value['Đường']);
                    $street = Street::query()->select()
                        ->with(['district', 'province'])
                        ->where('name', '=', $name)
                        ->where('district_id', '=', $districtId,)
                        ->first();
                    if (!$street){
                        $street = Street::query()->select()
                            ->with(['district', 'province'])
                            ->where('name', '=',  $value['Đường'])
                            ->where('district_id', '=', $districtId,)
                            ->first();
                    }
                    if (!$street){
                        $streetId =Street::query()->insertGetId(array(
                            'name'                        => $value['Đường'],
                            'district_id'                 => $districtId,
                            'province_id'                 => 45,
                        ));
                        $street = Street::query()->select()
                            ->with(['district', 'province'])
                            ->where('id', '=',  $streetId)
                            ->where('district_id', '=', $districtId,)
                            ->first();
                    }
                }
                if ($value['Đoạn']) {
                    $name = str_replace(['Đoạn '], '', mb_strtolower($value['Đoạn']));
                    $query = 'name ilike ' . "'%" . $name . "%'";
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
                        'ward' => $value['Phường']??null,
                        'street' => $distance->street->name,
                        'distance' => $distance->name,
                        'vt1' => (int)$value['VT1']* 1000,
                        'vt2' => (int)$value['VT2']* 1000,
                        'vt3' => (int)$value['VT3']* 1000,
                        'vt4' => (int)$value['VT4']* 1000,
                        'land_type' => $value['Loại đất']
                    ];

                    $unitPrice = new UnitPrice($unitPrice);
                    $unitPrice->newQuery()
                        ->insertGetId($unitPrice->attributesToArray());
                }elseif  ($street) {
                    $unitPrice = [
                        'province' => $street->province->name,
                        'district' => $street->district->name,
                        'ward' => $value['Phường']??null,
                        'street' => $street->name,
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
                }elseif  ($ward) {
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
            $this->migrateStatusRepository->updateMigrateStatus($migrateStatusId, ['status' => 1]);
            Log::info('Import unit price data end');
        } catch (\Exception $e) {
            Log::error('Import unit price data error with message  ' . $e);
        }
    }
}
