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
			// Lấy tất cả ID của tài sản so sánh cần di chuyển
            $compareAssetGeneralIds = CompareAssetGeneral::select('id')
            ->whereMigrateStatus('TSS')
            ->whereStatus(1)
            ->orderBy('id')
            ->pluck('id');
    
        $nb = count($compareAssetGeneralIds);
        $interval = intval(ceil($nb / 50)); // Điều chỉnh khoảng thời gian dựa trên kích thước lô mong muốn
    
        $this->output->progressStart($nb);
        foreach ($compareAssetGeneralIds->chunk(50) as $chunk) { // Xử lý theo lô 50
            foreach ($chunk as $index => $itemId) {
                if ($index > 0 && $index % $interval === 0) { // Cập nhật tiến trình sau mỗi `$interval` mục
                    $this->output->progressAdvance($interval);
                }
    
                // Lấy và lập chỉ mục dữ liệu tài sản so sánh
                $rows = $compareAssetGeneralRepository->findById($itemId);
                $compareAssetGeneralRepository->indexData($rows);
    
                usleep(10); // Trễ ngắn giữa các mục xử lý (tùy chọn)
            }
        }
            $this->output->progressFinish();
            Log::info('Migration estates in Donava is end!');
        } catch (\Exception $e) {
            Log::error('Migration estates in Donava is error with message  ' . $e);
        }
    }
}
