<?php

namespace App\Console\Commands;

use App\Models\Appraise;
use App\Models\Certificate;
use App\Models\PersonalProperty;
use App\Models\RealEstate;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class UpdateCertificateIdToAppraisesTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-certificate-id-to-appraises:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update HSTĐ Id cho Tài sản thẩm định';

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
        $this->updateData();
    }
    private function updateData()
    {
        try {
            printf("\n Start");
            DB::beginTransaction();
            $certificates = Certificate::query()->whereHas('realEstate')->get(['id', 'status', 'sub_status']);
            if (!empty($certificates)) {
                $count = $certificates->count();
                $this->output->progressStart($count);
                foreach ($certificates as $certificate) {
                    $realEstates = $certificate->realEstate->toArray();
                    $realEstateIds = Arr::pluck($realEstates, 'real_estate_id');
                    $this->updateRealEstate($realEstateIds, $certificate);
                    $this->output->progressAdvance();
                }
                $this->output->progressFinish();
            }
            $certificates = Certificate::query()->whereHas('personalProperties')->get(['id', 'status', 'sub_status']);
            if (!empty($certificates)) {
                $count = $certificates->count();
                $this->output->progressStart($count);
                foreach ($certificates as $certificate) {
                    $personalProperties = $certificate->personalProperties->toArray();
                    $ids = Arr::pluck($personalProperties, 'personal_property_id');
                    $this->updatePersonalProperties($ids, $certificate);
                    $this->output->progressAdvance();
                }
                $this->output->progressFinish();
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            printf("\n Lỗi ". $e->getMessage());
        }
    }
    private function updatePersonalProperties ($ids, $certificate) {
        $updateData = [
            'certificate_id' => $certificate->id,
            'updated_at' => DB::raw('updated_at'),
            'status' => 2,
            'sub_status' => 1
        ];

        if ($certificate->status > 1) {
            $updateData['status'] = $certificate->status;
            $updateData['sub_status'] = $certificate->sub_status;
        } 
        PersonalProperty::query()->whereIn('id', $ids)->update($updateData);
    }
    private function updateRealEstate($realEstateIds, $certificate) {
        $updateData = [
            'certificate_id' => $certificate->id,
            'updated_at' => DB::raw('updated_at'),
            'status' => 2,
            'sub_status' => 1
        ];

        if ($certificate->status > 1) {
            $updateData['status'] = $certificate->status;
            $updateData['sub_status'] = $certificate->sub_status;
        } 
        RealEstate::query()->whereIn('id', $realEstateIds)->update($updateData);
        Appraise::query()->whereIn('id', $realEstateIds)->update($updateData);
    }
}
