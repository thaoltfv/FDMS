<?php

namespace App\Console\Commands;

use App\Models\Appraise;
use App\Models\Certificate;
use App\Models\Customer;
use App\Models\RealEstate;
use Exception;
use Illuminate\Console\Command;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
            Log::info("Bắt đầu update");
            // $sheets = (new FastExcel())->configureCsv(';')->import(database_path('mocks/donava_base_status_01.01.22_31.03.23.csv'));
            $sheets = (new FastExcel())->configureCsv(';')->import(database_path('mocks/donava_update_done_quyII_2023.csv'));
            // Log::info("file csv: ".$sheets );
            $this->output->progressStart(count($sheets));
            $sheets->map(function ($value) {
                Log::info("Dòng dữ liệu: ".json_encode($value) );
                if ($value['certificate_date'] && $value['certificate_num'] && strpos($value['certificate_num'], ',') > 0) {
                    $year = date('Y' , strtotime($value['certificate_date']));
                    $value['certificate_num'] = str_replace(',', '/', $value['certificate_num']);
                    $certifications = Certificate::where('certificate_num', $value['certificate_num'])->whereYear('certificate_date', $year)->get();
                    $customer = null;
                    if ($value['customer']) {
                        $customer = Customer::firstOrCreate([
                            'name' => $value['customer'],
                            'phone' => $value['phone']
                        ], [
                            'status' => 'active',
                            'address' => $value['address']
                        ]);
                    }
                    Log::info("list hồ sơ: ".json_encode($certifications) );
                    Log::info("đối tác: ".json_encode($customer) );
                    foreach ($certifications as $certificate) {
                        Log::info(" hồ sơ: ".json_encode($certificate) );
                        $certificate->update([
                            'status' => 4,
                            'sub_status' => 1,
                            // 'service_fee' => $value['service_fee'],
                            'customer_id' => $customer ? $customer->id : null,
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
