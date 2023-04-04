<?php

namespace App\Http\Controllers\MigrateData;

use App\Contracts\MigrateStatusRepository;
use App\Models\District;
use App\Models\Street;
use Illuminate\Support\Facades\DB;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Rap2hpoutre\FastExcel\FastExcel;

class ImportAsyncStreetData implements ShouldQueue
{

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private MigrateStatusRepository $migrateStatusRepository;
    private $index;
    private $size;
    public function __construct(MigrateStatusRepository       $migrateStatusRepository,$index, $size)
    {
        $this->migrateStatusRepository = $migrateStatusRepository;
        $this->index = $index;
        $this->size = $size;
    }

    /**
     *
     */
    public function handle()
    {
        try {
            Log::info('Import street data start');
            $sheets = (new FastExcel())->configureCsv()->import(database_path('mocks/street.csv'));
            Log::info('Import street data size' . sizeof($sheets));
            $district = null;
            $sheets = array_slice($sheets->toArray(), $this->index, $this->size);
            Log::info('Import street data size slice' . sizeof($sheets));
            $migrateStatusId = $this->migrateStatusRepository->createMigrateStatus([
                'limit' => $this->size,
                'page' => $this->index,
                'status' => 0,
                'type' => 1,
                'total_records' => sizeof($sheets),
            ]);
            foreach ($sheets as $sheet) {
                if ($district === null || $district['id'] !== $sheet['district_id']) {
                    $district = District::query()->select()->where('id','=',$sheet['district_id'])->first();
                }
                Street::query()->insert(array(
                    'id'                          => $sheet['id'],
                    'name'                        => $sheet['name'],
                    'district_id'                 => $sheet['district_id'],
                    'province_id'                 => $district->province_id,
                ));
            }
            $lastIndex = Street::query()->latest('id')->first();
            if (isset($lastIndex)) {
                $index = $lastIndex['id'] + 1;
                DB::update("ALTER SEQUENCE streets_id_seq RESTART WITH ".$index.";");
            }
            $this->migrateStatusRepository->updateMigrateStatus($migrateStatusId, ['status' => 1]);
            Log::info('Import street data end');
        } catch (\Exception $e) {
            Log::error('Import street data error with message  ' . $e);
        }
    }
}
