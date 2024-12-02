<?php

namespace App\Services;

use App\Models\PriceEstimate;
use App\Models\PriceEstimateVersion;
use Illuminate\Support\Facades\DB;

class PriceEstimateVersionService
{

    public static function getVersionPriceEstimate($priceEstimateId)
    {
        return PriceEstimateVersion::query()->where('price_estimate_id', $priceEstimateId)->orderBy('updated_at', 'desc')->first()->version ?? 0;
    }

    public static function getVersionPriceEstimates($ids)
    {
        return PriceEstimate::query()->whereIn('id', $ids)->with('lastVerion')->get('id');
    }
}
