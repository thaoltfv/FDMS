<?php

namespace App\Console\Commands;

use App\Models\ApartmentAsset;
use App\Models\ApartmentAssetVersion;
use App\Models\Certificate;
use App\Models\CertificateApartmentVersion;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrationInsertApartmentVersion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migration-insert-apartment-version:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Insert apartment version';

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
        printf("Cập nhật version cho chung cư - TSTĐ \n");
        $this->updateApartmentAssetVersion();
        printf("\n Cập nhật version cho chung cư - STĐ \n");
        $this->updateCertificateApartmentAssetVersion();
    }

    private function updateApartmentAssetVersion()
    {
        DB::transaction(function () {
            $apartments = ApartmentAsset::query()->whereDoesntHave('version')->get();
            if (!empty($apartments) &&  count($apartments) > 0) {
                $count = count($apartments);
                $this->output->progressStart($count);
                foreach ($apartments as $apartment) {
                    $apartmentData = [
                        'apartment_asset_id' => $apartment->id,
                        'version' => 1,
                        'status' => 1
                    ];
                    ApartmentAssetVersion::query()->create($apartmentData);
                    $this->output->progressAdvance();
                }
                $this->output->progressFinish();
            }
        });
    }
    private function updateCertificateApartmentAssetVersion()
    {
        DB::transaction(function () {
            $certificates = Certificate::query()->whereHas('realEstate', function ($has) {
                $has->whereHas('apartment', function ($q) {
                    $q->whereDoesntHave('version');
                });
            })->get();
            if (!empty($certificates) &&  count($certificates) > 0) {
                $count = count($certificates);
                $this->output->progressStart($count);
                foreach ($certificates as $certificate) {
                    $realEstates = $certificate->realEstate;
                    foreach ($realEstates as $realEstate) {
                        $apartment = $realEstate->apartment;
                        if (!empty($apartment)) {
                            $apartmentData = [
                                'apartment_asset_id' => $apartment->id,
                                'version' => 1,
                                'status' => 1
                            ];
                            CertificateApartmentVersion::query()->create($apartmentData);
                        }
                    }
                    $this->output->progressAdvance();
                }
                $this->output->progressFinish();
            }
        });
    }
}
