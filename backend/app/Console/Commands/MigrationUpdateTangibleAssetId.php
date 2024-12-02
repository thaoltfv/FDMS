<?php

namespace App\Console\Commands;

use App\Contracts\AppraiseRepository;
use App\Contracts\BuildingPriceRepository;
use App\Contracts\CertificateRepository;
use App\Models\AppraiseTangibleAsset;
use App\Models\AppraiseTangibleComparisonFactor;
use App\Models\CertificateAsset;
use App\Models\CertificateAssetConstructionCompany;
use App\Models\CertificateAssetTangibleAsset;
use App\Models\CertificateAssetTangibleComparisonFactor;
use App\Models\ConstructionCompany;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class MigrationUpdateTangibleAssetId extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migration_update_tangible_asset_id:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private BuildingPriceRepository $buildingPriceRepository;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct( BuildingPriceRepository $buildingPriceRepository )
    {
        parent::__construct();

        $this->buildingPriceRepository =  $buildingPriceRepository;
    }

    private function updateAppraise(){
        DB::beginTransaction();
        try{
            Log::info("Migration update tangible Appraise Table is start!");
            $appraiseList = ConstructionCompany::whereNull('tangible_asset_id')->select('appraise_id')->groupby('appraise_id')->get();
            if(isset($appraiseList)){
                foreach($appraiseList as $appraise){
                    $tangible = AppraiseTangibleAsset::where('appraise_id',$appraise->appraise_id)->first();
                    if(isset($tangible)){
                        ConstructionCompany::where('appraise_id',$appraise->appraise_id)->update([
                            'tangible_asset_id' => $tangible->id,
                        ]);
                    }
                }
            }

            $appraiseList = AppraiseTangibleComparisonFactor::whereNull('tangible_asset_id')->select('appraise_id')->groupby('appraise_id')->get();
            if(isset($appraiseList)){
                foreach($appraiseList as $appraise){
                    $tangible = AppraiseTangibleAsset::where('appraise_id',$appraise->appraise_id)->first();
                    if(isset($tangible)){
                        AppraiseTangibleComparisonFactor::where('appraise_id',$appraise->appraise_id)->update([
                            'tangible_asset_id' => $tangible->id,
                        ]);
                    }
                }
            }
            DB::commit();
            Log::info('Migration update tangible Appraise is end!');
        }catch(\Exception $e){
            DB::rollBack();
            Log::error('Migration update tangible Appraise is error with message  ' . $e);
        }

    }

    private function updateCertificate(){
        DB::beginTransaction();
        try{
            Log::info("Migration update tangible Certificate Table is start!");
            $appraiseList = CertificateAssetConstructionCompany::whereNull('tangible_asset_id')->select('appraise_id')->groupby('appraise_id')->get();
            if(isset($appraiseList)){
                foreach($appraiseList as $appraise){
                    $tangible = CertificateAssetTangibleAsset::where('appraise_id',$appraise->appraise_id)->first();
                    if(isset($tangible)){
                        CertificateAssetConstructionCompany::where('appraise_id',$appraise->appraise_id)->update([
                            'tangible_asset_id' => $tangible->id,
                        ]);
                    }
                }
            }

            $appraiseList = CertificateAssetTangibleComparisonFactor::whereNull('tangible_asset_id')->select('appraise_id')->groupby('appraise_id')->get();
            if(isset($appraiseList)){
                foreach($appraiseList as $appraise){
                    $tangible = CertificateAssetTangibleAsset::where('appraise_id',$appraise->appraise_id)->first();
                    if(isset($tangible)){
                        CertificateAssetTangibleComparisonFactor::where('appraise_id',$appraise->appraise_id)->update([
                            'tangible_asset_id' => $tangible->id,
                        ]);
                    }
                }
            }
            DB::commit();
            Log::info('Migration update tangible Certificate is end!');
        }catch(\Exception $e){
            DB::rollBack();
            Log::error('Migration update tangible Certificate is error with message  ' . $e);
        }

    }
    private function updateAppraiseDesicionAverage(){
        DB::beginTransaction();
        try{
            Log::info("Migration update Appraise Tangible Asset is start!");
            $tangibleList = AppraiseTangibleAsset::whereNull('total_desicion_average')->get();
            // dd(count($tangibleList));
            if(isset($tangibleList)){
                foreach($tangibleList as $tangible){
                    $desicionAverage = $this->buildingPriceRepository->getAverageBuildPriceV3($tangible);
                    // dd(AppraiseTangibleAsset::where('id',$tangible->id)->first());
                    AppraiseTangibleAsset::where('id',$tangible->id)
                                            ->update([
                                                    'total_desicion_average' =>  $desicionAverage,
                                            ]);
                }
            }
            DB::commit();
            Log::info('Migration update Appraise Tangible Asset is end!');
        }catch(\Exception $e){
            DB::rollBack();
            Log::error('Migration update Appraise Tangible Asset is error with message  ' . $e);
        }
    }
    private function updateCertificateDesicionAverage(){
        DB::beginTransaction();
        try{
            Log::info("Migration update Certificate Tangible Asset is start!");
            $tangibleList = CertificateAssetTangibleAsset::whereNull('total_desicion_average')->get();
            // dd(count($tangibleList));
            if(isset($tangibleList)){
                foreach($tangibleList as $tangible){
                    $desicionAverage = $this->buildingPriceRepository->getAverageBuildPriceV3($tangible);
                    // dd(AppraiseTangibleAsset::where('id',$tangible->id)->first());
                    CertificateAssetTangibleAsset::where('id',$tangible->id)
                                            ->update([
                                                    'total_desicion_average' =>  $desicionAverage,
                                            ]);
                }
            }
            DB::commit();
            Log::info('Migration update Certificate Tangible Asset is end!');
        }catch(\Exception $e){
            DB::rollBack();
            Log::error('Migration update Certificate Tangible Asset is error with message  ' . $e);
        }
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::info("Migration update tangible asset id in Donava is start!");
        try {
            $this->updateAppraise();
            $this->updateCertificate();
            $this->updateAppraiseDesicionAverage();
            $this->updateCertificateDesicionAverage();
        Log::info('Migration update tangible asset id in Donava is end!');
        } catch (\Exception $e) {
            Log::error('Migration update tangible asset id in Donava is error with message  ' . $e);
        }
    }
}
