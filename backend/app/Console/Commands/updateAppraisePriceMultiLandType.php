<?php

namespace App\Console\Commands;

use App\Models\Appraise;
use App\Models\AppraisePrice;
use App\Models\Certificate;
use App\Models\CertificateAssetPrice;
use DB;
use Exception;
use Illuminate\Console\Command;

class updateAppraisePriceMultiLandType extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update_appraise_price_by_land_type:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update appraise price round - multi land type';

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
        $this->updatePriceRound();
        $this->updateCertificatePriceRound();
    }
    private function updatePriceRound()
    {
        try {
            printf("Bắt đầu cập nhật\n");
            DB::beginTransaction();
            $appraises = Appraise::query()->with(['assetPrice', 'properties', 'properties.propertyDetail'])->whereHas('assetPrice')->orderBy('id', 'desc')->get(['id']);
            if (!empty($appraises)) {
                foreach ($appraises as $appraise) {
                    $landDetails = $appraise->properties[0]->propertyDetail;
                    $prices = $appraise->assetPrice;
                    foreach ($landDetails as $detail) {
                        $slug = 'land_asset_purpose_' . $detail->landTypePurpose->acronym;
                        $slugViolate = $slug . '_violation';
                        $price = $prices->where('slug', 'ilike', $slug . '_price')->first();
                        $priceViolate = $prices->where('slug', 'ilike', $slugViolate . '_price')->first();
                        if (!empty($price) && !empty($price->value) && $price->value > 0) {
                            $slugRound = $slug . '_round';
                            $roundData = $prices->where('slug', $slugRound)->first();
                            if (empty($roundData)) {
                                if ($detail->is_transfer_facility) {
                                    $round = $prices->where('slug', 'round_total')->first();
                                } else {
                                    $round = $prices->where('slug', 'round_composite')->first();
                                }
                                AppraisePrice::query()->updateOrCreate(['appraise_id' => $appraise->id, 'slug' => $slugRound], ['value' => $round ? $round->value : 0]);
                            }
                        }
                        if (!empty($priceViolate) && !empty($price->value) && $price->value > 0) {
                            $slugRound = $slugViolate . '_round';
                            $roundData = $prices->where('slug', $slugRound)->first();
                            if (empty($roundData)) {
                                if ($detail->is_transfer_facility) {
                                    $round = $prices->where('slug', 'round_violation_composite')->first();
                                } else {
                                    $round = $prices->where('slug', 'round_violation_facility')->first();
                                }
                                AppraisePrice::query()->updateOrCreate(['appraise_id' => $appraise->id, 'slug' => $slugRound], ['value' => $round ? $round->value : 0]);
                            }
                        }
                    }
                }
            }
            DB::commit();
            printf("Đã hoàn thành cập nhật");
        } catch (Exception $e) {
            printf("Cập nhật thất bại " . $e->getMessage());
            DB::rollBack();
        }
    }

    private function updateCertificatePriceRound()
    {
        try {
            printf("Bắt đầu cập nhật cho TSCT \n");
            DB::beginTransaction();
            // $appraises = Appraise::query()->with(['assetPrice','properties', 'properties.propertyDetail'])->whereHas('assetPrice')->orderBy('id', 'desc')->get(['id']);

            $certificates = Certificate::query()->with(['realEstate', 'realEstate.appraises', 'realEstate.appraises.assetPrice', 'realEstate.appraises.properties.propertyDetail'])->whereHas('realEstate')->orderBy('id', 'desc')->get(['id']);
            if (!empty($certificates)) {
                // foreach($certificates as $certificate) {
                //     dd($certificate->realEstate->toArray());
                // }
                // dd($certificate->realEstate->toArray());
                // dd($certificate['real_estate']);
                foreach ($certificates as $certificate) {
                    foreach ($certificate->realEstate as $realEstate) {
                        $appraise = $realEstate->appraises;
                        if (!empty($appraise)) {
                            $landDetails = $appraise->properties[0]->propertyDetail;
                            $prices = $appraise->assetPrice;
                            foreach ($landDetails as $detail) {
                                $slug = 'land_asset_purpose_' . $detail->landTypePurpose->acronym;
                                $slugViolate = $slug . '_violation';
                                $price = $prices->where('slug', 'ilike', $slug . '_price')->first();
                                $priceViolate = $prices->where('slug', 'ilike', $slugViolate . '_price')->first();
                                if (!empty($price) && !empty($price->value) && $price->value > 0) {
                                    $slugRound = $slug . '_round';
                                    $roundData = $prices->where('slug', $slugRound)->first();
                                    if (empty($roundData)) {
                                        if ($detail->is_transfer_facility) {
                                            $round = $prices->where('slug', 'round_total')->first();
                                        } else {
                                            $round = $prices->where('slug', 'round_composite')->first();
                                        }
                                        CertificateAssetPrice::query()->updateOrCreate(['appraise_id' => $appraise->id, 'slug' => $slugRound], ['value' => $round ? $round->value : 0]);
                                    }
                                }
                                if (!empty($priceViolate) && !empty($price->value) && $price->value > 0) {
                                    $slugRound = $slugViolate . '_round';
                                    $roundData = $prices->where('slug', $slugRound)->first();
                                    if (empty($roundData)) {
                                        if ($detail->is_transfer_facility) {
                                            $round = $prices->where('slug', 'round_violation_composite')->first();
                                        } else {
                                            $round = $prices->where('slug', 'round_violation_facility')->first();
                                        }
                                        CertificateAssetPrice::query()->updateOrCreate(['appraise_id' => $appraise->id, 'slug' => $slugRound], ['value' => $round ? $round->value : 0]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
            DB::commit();
            printf("Đã hoàn thành cập nhật");
        } catch (Exception $e) {
            printf("Cập nhật thất bại " . $e->getMessage());
            DB::rollBack();
        }
    }
}
