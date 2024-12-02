<?php

namespace App\Console\Commands;

use App\Models\ApartmentAsset;
use App\Models\Appraise;
use App\Models\Certificate;
use App\Models\CertificateAsset;
use App\Models\CertificateHasRealEstate;
use App\Models\CertificateRealEstate;
use App\Models\Dictionary;
use App\Models\RealEstate;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Log;

class MigrationFillCertificateRealEstates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migration-insert-certificate-real-estates:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fill Certificate Real Estates from Appraise and Apartment';

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
        try{
            DB::beginTransaction();
            $this->insertFromAppraise();
            DB::commit();
        }catch(Exception $ex){
            Log::error($ex);
            printf("cập nhật thất bại");
            DB::rollBack();
        }

    }

    private function insertFromAppraise()
    {
        $datas = Certificate::query()->with(['appraises','personalProperties'])->whereHas('appraises', function($has){
            $has->whereNull('real_estate_id');
        })->orderBy('created_at')->get('id');
        print("Cập nhật ". count($datas). " HSTĐ \n");
        $dictionary = Dictionary::query()->where('type', 'LOAI_TAI_SAN')->WHERE('status', 1)->get();
        
        foreach ($datas as $data) {
            $certificateId = $data->id;
            $appraises = $data->appraises;
            $personalProperties = $data->personalProperties;
            // CertificateHasRealEstate::query()->where('certificate_id', $certificateId)->forceDelete();
            foreach ($appraises as $appraise) {
                $appraiseId = $appraise->appraise_id;
                $oldId = $appraise->id;
                $realEstate = RealEstate::query()->whereHas('appraises', function ($has) use ($appraiseId) {
                    $has->where('id', $appraiseId);
                })->first();
                $realEstateId = $realEstate->id;
                CertificateRealEstate::query()->where('real_estate_id', '=', $realEstateId)->forceDelete();
                $assetData = $realEstate;
                $assetData->real_estate_id = $realEstateId;
                $certificateRealEstate = new CertificateRealEstate($assetData->toArray());
                $certificateAssetId = CertificateRealEstate::query()->insertGetId($certificateRealEstate->attributesToArray());
                $realEstateData['certificate_id'] = $certificateId;
                $realEstateData['real_estate_id'] = $certificateAssetId;
                $realEstateData['version'] = '1.0';
                CertificateHasRealEstate::query()->create($realEstateData);
                CertificateAsset::query()->where('id', $oldId)->update(['real_estate_id' => $certificateAssetId]);
            }
            $this->updateCertificateDocumentType($certificateId, $appraises, $personalProperties, $dictionary);
        }
        printf("Cập nhật thành công");
    }

    private function updateCertificateDocumentType($id, $appraises , $personalProperties, $dictionary)
    {
        $documentType =[];
        $appraiseAcronym = [];
        $personalAcronym = [];
        $dictionaryArr = Arr::pluck($dictionary,'id');
        if (!empty($appraises)) {
            $appraiseType = Arr::pluck($appraises,'asset_type_id');
            $diffAppraise = array_intersect($dictionaryArr, $appraiseType);
            $appraiseAcronym = $dictionary->whereIn('id', $diffAppraise);
        }

        if (!empty($personalProperties)) {
            $personalType = Arr::pluck($personalProperties,'asset_type_id');
            $diffPersonal = array_intersect($dictionaryArr, $personalType);
            $personalAcronym = $dictionary->whereIn('id', $diffPersonal);
        }
        $documentType = array_merge(Arr::pluck($appraiseAcronym , 'acronym'), Arr::pluck($personalAcronym , 'acronym'));
        Certificate::query()->where('id', $id)->update(['document_type' => $documentType, 'updated_at' => DB::raw('updated_at')]);
    }
}
