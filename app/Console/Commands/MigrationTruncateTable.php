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
    protected $description = 'DELETE all appraise data';

    protected $tableComparisonAssets = [
        'compare_asset_general_pends',
        'compare_asset_generals',
        'compare_asset_versions',
        'compare_foundation_assesses',
        'compare_general_pics',
        'compare_machine_pends',
        'compare_other_assets',
        'compare_other_pics',
        'compare_pic_pends',
        'compare_properties',
        'compare_property_details',
        'compare_property_doc',
        'compare_property_pends',
        'compare_property_pics',
        'compare_property_turning_time',
        'compare_tangible_asset_pends',
        'compare_tangible_assets',
        'compare_tangible_pics',
    ];

    protected $tableCertificate = [
        'certificate_apartment_adapters',
        'certificate_apartment_appraisal_base',
        'certificate_apartment_appraisal_methods',
        'certificate_apartment_comparison_factors',
        'certificate_apartment_has_assets',
        'certificate_apartment_laws',
        'certificate_apartment_other_assets',
        'certificate_apartment_pics',
        'certificate_apartment_prices',
        'certificate_apartment_properties',
        'certificate_apartment_versions',
        'certificate_apartments',

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
        'certificate_has_apartments',
        'certificate_has_personal_properties',
        'certificate_has_real_estates',
        'certificate_assets',
        'certificate_personal_properties',
        'certificate_real_estates',
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
        'real_estates',
        'apartment_asset_adapter',
        'apartment_asset_appraisal_base',
        'apartment_asset_appraisal_methods',
        'apartment_asset_comparison_factors',
        'apartment_asset_has_assets',
        'apartment_asset_laws',
        'apartment_asset_other_assets',
        'apartment_asset_pics',
        'apartment_asset_prices',
        'apartment_asset_properties',
        'apartment_asset_versions',
        'apartment_assets',
        'apartment_specifications',
        'apartments',

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
        'construction_company',

        'personal_properties',
        'machine_certificate_asset_law_infos',
        'machine_certificate_asset_laws',
        'machine_certificate_asset_prices',
        'machine_certificate_assets',
        'machine_certificate_brief_law_infos',
        'machine_certificate_brief_laws',
        'machine_certificate_brief_prices',
        'machine_certificate_briefs',
        'other_certificate_asset_law_infos',
        'other_certificate_asset_laws',
        'other_certificate_asset_prices',
        'other_certificate_assets',
        'other_certificate_brief_law_infos',
        'other_certificate_brief_laws',
        'other_certificate_brief_prices',
        'other_certificate_briefs',
        'technological_line_certificate_asset_law_infos',
        'technological_line_certificate_asset_laws',
        'technological_line_certificate_asset_prices',
        'technological_line_certificate_assets',
        'verhicle_certificate_asset_law_infos',
        'verhicle_certificate_asset_laws',
        'verhicle_certificate_asset_prices',
        'verhicle_certificate_assets',
        'verhicle_certificate_brief_law_infos',
        'verhicle_certificate_brief_laws',
        'verhicle_certificate_brief_prices',
        'verhicle_certificate_briefs',
    ];

    protected $tableUtilities = [
        'activity_log',
        'address_logs',
        'customer_pics',
        'customers',
        'estimate_price_logs',
        'migrate_status',
        'migrate_status_details',
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

    private function truncateTables($tables, $name){
        DB::beginTransaction();
        try{
            if(isset($tables)){
                Log::info("Migration truncate " . $name . " Table is start!");

                foreach($tables as $item){
                    if (Schema::hasTable($item)) {
                        DB::table($item)->truncate();
                    }
                }
                DB::commit();
                Log::info('Migration truncate ' . $name . ' Table is end!');
            }
        }catch(\Exception $e){
            DB::rollBack();
            Log::error('Migration truncate ' . $name . ' Table is error with message  ' . $e);
        }

    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->truncateTables($this->tableUtilities, 'Utilities');
        $this->truncateTables($this->tableComparisonAssets, 'Comparison Assets');
        $this->truncateTables($this->tableAppraise, 'Appraise Assets');
        $this->truncateTables($this->tableCertificate, 'Certificate Briefs');
    }
}
