<?php

namespace App\Console\Commands;

use App\Models\CompareAssetGeneral;
use App\Repositories\EloquentCompareAssetGeneralRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class MigrationComparationAssetToElastic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migration_comparation_asset_to_elastic:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tool to create mapping data and synchronize comparate asset to elastic';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::info("Migration estates in Donava is start!");
        try {
            $compareAssetGeneralRepository = new EloquentCompareAssetGeneralRepository(new CompareAssetGeneral());
            // $compareAssetGeneralRepository->createIndex();
            // $compareAssetGeneralRepository->createVersionIndex();
			$compareAssetGenerals = CompareAssetGeneral::select('id')->whereMigrateStatus('TSS')->whereStatus(1)->orderby('id')->get();
            $nb = count($compareAssetGenerals);

            $interval = intval($nb * 2 / 100);
            $prePost = $interval;

            $this->output->progressStart($nb);
			foreach($compareAssetGenerals as $index => $item) {
                if ($index > $prePost) {
                    $prePost += $interval;
                    $this->output->progressAdvance($interval);
                }
                $rows = $compareAssetGeneralRepository->findById($item->id);
                $compareAssetGeneralRepository->indexData($rows);
                usleep(10);
			}
            $this->output->progressFinish();
            Log::info('Migration estates in Donava is end!');
        } catch (\Exception $e) {
            Log::error('Migration estates in Donava is error with message  ' . $e);
        }
    }
}
