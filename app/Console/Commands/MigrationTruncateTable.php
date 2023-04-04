<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class MigrationTruncateTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migration_truncate_table:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $tableCertificate = [
        'certificate_approach',
        'certificate_asset_adapter',
        'certificate_asset_comparison_factor',
        'certificate_asset_construction_companies',
        'certificate_asset_has_assets',
        'certificate_asset_law_details',
        'certificate_asset_legal_documents_on_construction',
        'certificate_asset_legal_documents_on_land',
        'certificate_asset_legal_documents_on_local',
        'certificate_asset_legal_documents_on_valuation',
        'certificate_asset_other_assets',
        'certificate_asset_law',
        'certificate_asset_pics',
        'certificate_asset_unit_price',
        'certificate_asset_properties',
        'certificate_asset_property_details',
        'certificate_asset_property_turning_time',
        'certificate_asset_tangible_assets',
        'certificate_asset_tangible_comparison_factor',
        'certificate_asset_versions',
        'certificate_asset_prices',
        'certificate_has_appraises',
        'certificate_assets',
        'certificate_basis_property',
        'certificate_comparison_factor',
        'certificate_construction_company',
        'certificate_legal_documents_on_construction',
        'certificate_legal_documents_on_land',
        'certificate_legal_documents_on_local',
        'certificate_legal_documents_on_valuation',
        'certificate_method_used',
        'certificate_other_documents',
        'certificate_principle',
        'certificate_tangible_comparison_factor',
        'certificate_prices',
        'certificate_asset_appraisal_methods',
        'certificate_asset_law_land_details',
        'certificate_asset_unit_area',
        'certificates',
    ];

    protected $tableAppraise = [
        'appraise_appraisal_methods',
        'appraise_law_details',
        'appraise_comparison_factor',
        'appraise_has_assets',
        'appraise_law',
        'appraise_law_land_details',
        'appraise_legal_documents_on_construction',
        'appraise_legal_documents_on_land',
        'appraise_pics',
        'appraise_legal_documents_on_local',
        'appraise_legal_documents_on_valuation',
        'appraise_properties',
        'appraise_property_details',
        'appraise_tangible_assets',
        'appraise_prices',
        'appraise_property_turning_time',
        'appraise_tangible_comparison_factor',
        'appraise_versions',
        'appraise_unit_price',
        'appraise_other_assets',
        'appraise_adapter',
        'appraises',
        'appraise_unit_area',
        'construction_company'
    ];
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    private function truncateCertificate($tableCertificate){
        DB::beginTransaction();
        try{
            if(isset($tableCertificate)){
                Log::info("Migration truncate Certificate Table is start!");

                foreach($tableCertificate as $item){
                    if (Schema::hasTable($item)) {
                        DB::table($item)->truncate();
                    }
                }
                DB::commit();
                Log::info('Migration truncate Certificate Table is end!');
            }
        }catch(\Exception $e){
            DB::rollBack();
            Log::error('Migration truncate Certificate Table is error with message  ' . $e);
        }

    }

    private function truncateAppraise($tableAppraise){
        DB::beginTransaction();
        try{
            if(isset($tableAppraise)){
                Log::info("Migration truncate Appraise Table is start!");

                foreach($tableAppraise as $item){
                    if (Schema::hasTable($item)) {
                        DB::table($item)->truncate();
                    }
                }
                DB::commit();
                Log::info('Migration truncate Appraise Table is end!');
            }
        }catch(\Exception $e){
            DB::rollBack();
            Log::error('Migration truncate Appraise Table is error with message  ' . $e);
        }

    }
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->truncateAppraise($this->tableAppraise);
        $this->truncateCertificate($this->tableCertificate);
    }
}
