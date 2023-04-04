<?php

namespace App\Console\Commands;

use App\Models\Certificate;
use Exception;
use Illuminate\Console\Command;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\DB;

class UpdateCertificateStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update_certificate_status:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update certifications status base on imported data';

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
            $sheets = (new FastExcel())->configureCsv(';')->import(database_path('mocks/donava_finished_certificate_brief.csv'));
            $this->output->progressStart(count($sheets));
            $sheets->map(function ($value) {
                $certifications = Certificate::where('certificate_num', $value['certificate_num'])->get();
                foreach ($certifications as $certificate) {
                    $certificate->update([
                        'status' => 4,
                        'service_fee' => $value['service_fee'],
                        'status_updated_at' => $value['certificate_date'],
                        'updated_at' => $value['certificate_date']
                    ]);
                    foreach ($certificate->appraises as $appraise) {
                        $appraise->update([
                            'status' => 4,
                            'updated_at' => $value['certificate_date']

                        ]);
                    }
                    foreach ($certificate->realEstate as $realEstate) {
                        $realEstate->update([
                            'status' => 4,
                            'updated_at' => $value['certificate_date']

                        ]);
                    }
                }
                $this->output->progressAdvance();
                usleep(10);
            });
            $this->output->progressFinish();
            DB::commit();
        } catch (Exception $e) {
            printf($e->getMessage());
            DB::rollBack();
        }
    }
}
