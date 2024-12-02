<?php

namespace App\Http\Controllers\MigrateData;

use App\Contracts\CompareAssetGeneralRepository;
use App\Contracts\MigrateStatusRepository;
use App\Models\MigrateStatusDetails;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MigrationAsyncDonavaToElastic implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private CompareAssetGeneralRepository $compareAssetGeneralRepository;
    private MigrateStatusRepository $migrateStatusRepository;

    private $perPage;
    private $page;
    private $id;

    /**
     * @param CompareAssetGeneralRepository $compareAssetGeneralRepository
     * @param MigrateStatusRepository $migrateStatusRepository
     * @param $perPage
     * @param $page
     * @param $id
     */

    public function __construct(CompareAssetGeneralRepository $compareAssetGeneralRepository, MigrateStatusRepository $migrateStatusRepository, $perPage, $page, $id)
    {
        $this->compareAssetGeneralRepository = $compareAssetGeneralRepository;
        $this->migrateStatusRepository = $migrateStatusRepository;
        $this->perPage = $perPage;
        $this->page = $page;
        $this->id = $id;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */

    public function handle()
    {
        Log::info("Migration estates in Donava is start!");
        try {
            $totalRecord = $this->compareAssetGeneralRepository->totalRecord();
            Log::info("Migration estates in Donava total records: " . $totalRecord);
            $objects = $this->compareAssetGeneralRepository->findDataPaging($this->perPage, $this->page);
            Log::info("Migration estates in Donava records : " . count($objects));

            foreach ($objects as $object) {
                $this->compareAssetGeneralRepository->indexData($object);
                MigrateStatusDetails::query()->insert(array(
                    'migrate_status_id' => $this->id,
                    'asset_id' => $object['id'],
                    'status' => 3,
                ));
            }
            $this->migrateStatusRepository->updateMigrateStatus($this->id, ['status' => 5]);
            Log::info('Migration estates in Donava is end!');
        } catch (\Exception $e) {
            Log::error('Migration estates in Donava is error with message  ' . $e);
        }
    }
}
