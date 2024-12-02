<?php

namespace App\Console\Commands;

use App\Models\Certificate;
use App\Models\User;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class updatePerformAppraiserOldCertificate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-perform-appraiser-old-certificate:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update perform appraiser old certificate';

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
            printf('Cập nhật lại dữ liệu chuyển viên thẩm định' . "\n");
            $this->update();
            DB::commit();
            printf('Cập nhật thành công');
        }catch (Exception $e) {
            printf('Cập nhật thất bại '. $e->getMessage());
            DB::rollBack();
        }
    }
    private function update() {
        $count = Certificate::query()->whereNull('appraiser_perform_id')->get('id')->count();
        printf("Có $count HSTĐ cần cập nhật \n");
        if ($count > 0) {
            $userList = User::query()->whereHas('appraiser')->get();
            foreach ($userList as $user) {
                $appraiserId = $user->appraiser->id;
                if (isset($appraiserId)) {
                    if (Certificate::query()->whereNull('appraiser_perform_id')->where('created_by', $user->id)->exists())
                        Certificate::query()->whereNull('appraiser_perform_id')->where('created_by', $user->id)->update([
                            'appraiser_perform_id' => $appraiserId,
                            'updated_at' => DB::raw('updated_at')
                        ]);
                }
            }
        }
    }
}
