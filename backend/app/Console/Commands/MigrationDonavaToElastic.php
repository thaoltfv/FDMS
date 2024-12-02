<?php

namespace App\Console\Commands;

use App\Contracts\CompareAssetGeneralRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class MigrationDonavaToElastic extends Command

{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migration_donava_to_elastic:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private CompareAssetGeneralRepository $compareAssetGeneralRepository;

    /**
     * Create a new command instance.
     *
     * @param CompareAssetGeneralRepository $compareAssetGeneralRepository
     */

    public function __construct(CompareAssetGeneralRepository $compareAssetGeneralRepository)
    {
        parent::__construct();
        $this->compareAssetGeneralRepository = $compareAssetGeneralRepository;
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
            $perPage = 1000;
            $totalRecord = $this->compareAssetGeneralRepository->totalRecord();
            $totalPage = (int)($totalRecord / $perPage) + 1;
            Log::info("Migration estates in Donava total record: " . $totalRecord);
            for ($page = 1; $page <= $totalPage; $page++) {
                $objects = $this->compareAssetGeneralRepository->findDataPaging($perPage, $page);
                foreach ($objects as $object) {
                    $this->compareAssetGeneralRepository->indexData($object);
                }
            }
            Log::info('Migration estates in Donava is end!');
        } catch (\Exception $e) {
            Log::error('Migration estates in Donava is error with message  ' . $e);
        }
    }
}
