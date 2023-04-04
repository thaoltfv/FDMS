<?php

namespace App\Console\Commands;

use App\Models\Appraise;
use App\Models\AppraiseVersion;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetSequenceAndUpdateVersion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-appraise-version:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        try {
            DB::beginTransaction();
            printf('Bắt đầu thực hiện' . "\n");
            DB::statement("DROP SEQUENCE IF EXISTS appraise_versions_id_seq CASCADE");
            DB::statement("CREATE SEQUENCE appraise_versions_id_seq owned by appraise_versions.id");
            DB::statement("CREATE SEQUENCE If NOT EXISTS certificate_asset_versions_id_seq owned by certificate_asset_versions.id");
            DB::statement("SELECT SETVAL('appraise_versions_id_seq', (SELECT MAX(id) + 1 FROM appraise_versions))");
            DB::statement("SELECT SETVAL('certificate_asset_versions_id_seq', (SELECT MAX(id) + 1 FROM certificate_asset_versions))");
            DB::statement("ALTER TABLE appraise_versions ALTER COLUMN id set default nextval('appraise_versions_id_seq')");
            DB::statement("ALTER TABLE certificate_asset_versions ALTER COLUMN id set default nextval('certificate_asset_versions_id_seq')");
            DB::statement("update certificate_asset_versions set version = 1");
            DB::statement("update appraise_versions set version = 1");
            $this->createAppraiseHaveNotVersion();
            DB::commit();
            printf('Đã xong');
        }catch(Exception $e) {
            printf('Bị lỗi ' . $e->getMessage());
            DB::rollBack();
        }
    }
    private function createAppraiseHaveNotVersion() {
        $appraises = Appraise::query()->whereDoesntHave('version')->get();
        if (!empty($appraises)) {
            foreach($appraises as $item) {
                $version = [
                    'appraise_id' => $item->id,
                    'status' => 1,
                    'version' => 1
                ];
                AppraiseVersion::query()->create($version);
            }
        }
    }
}
