<?php

namespace App\Console\Commands;

use App\Models\Appraise;
use App\Models\RealEstate;
use App\Models\Certificate;
use DB;
use Exception;
use Illuminate\Console\Command;

class UpdateFinishedAppraiseStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update_finished_certificate_assets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update status of certificate assets have been finished';

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
            $certifications = Certificate::where('status', 4)->get();
            $this->output->progressStart(count($certifications));
            foreach ($certifications as $certificate) {
                foreach ($certificate->appraises as $appraise) {
                    if ($appraise && $appraise->status != 4) {
                        $appraise->update([
                            'status' => 4,
                            'updated_at' => DB::raw('updated_at')
                        ]);
                    }
                }
                foreach ($certificate->realEstate as $realEstate) {
                    if ($realEstate && $realEstate->status != 4) {
                        $realEstate->update([
                            'status' => 4,
                            'updated_at' => DB::raw('updated_at')
                        ]);
                    }
                }
                Appraise::where('certificate_id', $certificate->id)
                    ->update([
                        'status' => 4,
                        'updated_at' => DB::raw('updated_at')
                    ]);
                RealEstate::where('certificate_id', $certificate->id)
                    ->update([
                        'status' => 4,
                        'updated_at' => DB::raw('updated_at')
                    ]);
                $this->output->progressAdvance();
                usleep(10);
            }
            $this->output->progressFinish();
            DB::commit();
        } catch (Exception $e) {
            printf($e->getMessage());
            DB::rollBack();
        }
    }
}
