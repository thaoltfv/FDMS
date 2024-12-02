<?php

namespace App\Http\Controllers\MigrateData;

use App\Contracts\CompareAssetGeneralRepository;
use App\Contracts\MigrateStatusRepository;
use App\Enum\ValueDefault;
use App\Models\CompareAssetGeneral;
use App\Models\MigrateStatusDetails;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MigrationUpdateMigrationStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private CompareAssetGeneralRepository $compareAssetGeneralRepository;
    private MigrateStatusRepository $migrateStatusRepository;


    /**
     * @param CompareAssetGeneralRepository $compareAssetGeneralRepository
     * @param MigrateStatusRepository $migrateStatusRepository
     */

    public function __construct(CompareAssetGeneralRepository $compareAssetGeneralRepository, MigrateStatusRepository $migrateStatusRepository)
    {
        $this->compareAssetGeneralRepository = $compareAssetGeneralRepository;
        $this->migrateStatusRepository = $migrateStatusRepository;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */

    public function handle()
    {
        Log::info("Migration migrate status is start!");
        try {
            $totalRecord = $this->compareAssetGeneralRepository->totalRecord();
            Log::info("Migration migrate status total records: " . $totalRecord);
            $migrateStatusId = $this->migrateStatusRepository->createMigrateStatus([
                'limit' => $totalRecord,
                'page' => 1,
                'status' => 0,
                'type' => 5,
                'total_records' => $totalRecord,
            ]);
            for ($i = 1; $i <= 6; $i++) {
                $objects = $this->compareAssetGeneralRepository->findDataPaging(1100,$i);
                foreach ($objects as $object) {
                        CompareAssetGeneral::query()->where('id', $object->id)
                            ->update(['migrate_status' => ValueDefault::MIGRATION_STATUS_DEFAULT]);
                        $object->migrate_status = ValueDefault::MIGRATION_STATUS_DEFAULT;
                        $this->compareAssetGeneralRepository->indexData($object);
                }
            }
            $this->migrateStatusRepository->updateMigrateStatus($migrateStatusId, ['status' => 1]);
            Log::info('Migration migrate status is end!');
        } catch (\Exception $e) {
            Log::error('Migration migrate status is error with message  ' . $e);
        }
    }
}
