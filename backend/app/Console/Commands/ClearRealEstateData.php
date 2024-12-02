<?php

namespace App\Console\Commands;

use App\Models\ApartmentAsset;
use App\Models\Appraise;
use App\Models\CertificateApartment;
use App\Models\CertificateAsset;
use App\Models\RealEstate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ClearRealEstateData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear-real-estate-data:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear RealEstates before insert realEstate';

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
        DB::transaction(function () {
            printf('bắt đầu clear');
            $this->clearRealEstate();
            printf("\nđã clear xong");
        });
    }
    private function clearRealEstate()
    {
        //truncate table Real Estates
        if (Schema::hasTable('real_estates')) {
            DB::table('real_estates')->truncate();
            // DB::statement("ALTER SEQUENCE real_estates_id_seq RESTART WITH 1");
        }
        //truncate table certificate real_estates
        if (Schema::hasTable('certificate_real_estates')) {
            DB::table('certificate_real_estates')->truncate();
            DB::statement("ALTER SEQUENCE certificate_real_estates_id_seq RESTART WITH 1");

        }
        if (Schema::hasTable('certificate_has_real_estates')) {
            DB::table('certificate_has_real_estates')->truncate();
            DB::statement("ALTER SEQUENCE certificate_has_real_estates_id_seq RESTART WITH 1");

        }
        // update null real_estate_id ->Appraise, apartmentAssset, CertificateAsset
        if (Appraise::query()->whereNotNull('real_estate_id')->exists()) {
            Appraise::query()->whereNotNull('real_estate_id')->update(['real_estate_id' => null]);
        }
        if (ApartmentAsset::query()->whereNotNull('real_estate_id')->exists()) {
            ApartmentAsset::query()->whereNotNull('real_estate_id')->update(['real_estate_id' => null]);
        }
        if (CertificateAsset::query()->whereNotNull('real_estate_id')->exists()) {
            CertificateAsset::query()->whereNotNull('real_estate_id')->update(['real_estate_id' => null]);
        }
        if (CertificateApartment::query()->whereNotNull('real_estate_id')->exists()) {
            CertificateApartment::query()->whereNotNull('real_estate_id')->update(['real_estate_id' => null]);
        }
    }
}
