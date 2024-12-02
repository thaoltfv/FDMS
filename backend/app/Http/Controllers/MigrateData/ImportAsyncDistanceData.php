<?php

namespace App\Http\Controllers\MigrateData;

use App\Contracts\MigrateStatusRepository;
use App\Models\Distance;
use App\Models\Street;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Rap2hpoutre\FastExcel\FastExcel;
use Spatie\QueryBuilder\QueryBuilder;

class ImportAsyncDistanceData implements ShouldQueue
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
            Log::info('Import distance data start');
            $sheets = (new FastExcel())->configureCsv()->import(database_path('mocks/distances.csv'));
            Log::info('Import distance data size' . sizeof($sheets));
            $migrateStatusId = $this->migrateStatusRepository->createMigrateStatus([
                'limit' => sizeof($sheets),
                'page' => 1,
                'status' => 0,
                'type' => 2,
                'total_records' => sizeof($sheets),
            ]);
            foreach ($sheets as $sheet) {
                $street = [];
                if ($sheet['Tỉnh/Thành'] == 'THÀNH PHỐ BIÊN HÒA ') {
                    $districtId = 432;
                } else {
                    $districtId = 433;
                }
                if ($sheet['Đoạn'] && $sheet['Đường']) {
                    $name = str_replace(['Đường '], '',$sheet['Đường']);
                    $query = 'name ilike ' . "'%" . $name . "%'";
                    $street = Street::query()->select()
                        ->with(['district', 'province'])
                        ->whereRaw($query)
                        ->where('district_id', '=', $districtId,)
                        ->first();
                }

                if (isset($street->id)) {
                    $distance = array(
                        'name' => $sheet['Đoạn'],
                        'detail' => $sheet['Đường'] . ' ' . $sheet['Đoạn'],
                        'district_id' => $street->district_id,
                        'province_id' =>  $street->province_id,
                        'street_id' => $street['id']
                    );
                } else{
                    Log::info($sheet['Đường']);

                    $streetId =Street::query()->insertGetId(array(
                        'name'                        => $sheet['Đường'],
                        'district_id'                 => $districtId,
                        'province_id'                 => 45,
                    ));
                    $distance = array(
                        'name' => $sheet['Đoạn'],
                        'detail' => $sheet['Đường'] . ' ' . $sheet['Đoạn'],
                        'district_id' => $districtId,
                        'province_id' =>  45,
                        'street_id' => $streetId
                    );
                }
                Distance::query()->insertGetId($distance);
            }
            $this->migrateStatusRepository->updateMigrateStatus($migrateStatusId, ['status' => 1]);
            Log::info('Import distance data end');
        }catch (\Exception $e){
            Log::error('Import distance data error with message  ' . $e);
        }
    }
}
