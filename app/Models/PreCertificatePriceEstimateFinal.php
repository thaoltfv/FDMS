<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Services\CommonService;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

class PreCertificatePriceEstimateFinal extends Model
{
    use  SoftDeletes;

    protected $table = 'pre_certificate_price_estimate_finals';

    protected $fillable = [
        'pre_certificate_price_estimate_id',
        'petitioner_name',
        'request_date',
        'appraise_purpose_id',
        'asset_type_id',
        'appraise_asset',
        'full_address',
        'full_address_street',
        'coordinates',
        'description',
        'img_map',
        'created_by',
        'total_price'
    ];


    public function appraisePurpose(): BelongsTo
    {
        return $this->belongsTo(AppraiseOtherInformation::class, 'appraise_purpose_id');
    }
    public function assetType(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class, 'asset_type_id', 'id');
    }
    public function lands(): HasMany
    {
        return $this->hasMany(PreCertificatePriceEstimateFinalLand::class, 'pre_certificate_price_estimate_final_id');
    }
    public function totalArea(): HasMany
    {
        return $this->hasMany(PreCertificatePriceEstimateFinalLand::class, 'pre_certificate_price_estimate_final_id')
            ->where('total_area', '>', 0);
    }

    public function planningArea(): HasMany
    {
        return $this->hasMany(PreCertificatePriceEstimateFinalLand::class, 'pre_certificate_price_estimate_final_id')
            ->where('planning_area', '>', 0);
    }


    public function tangibleAssets(): HasMany
    {
        return $this->hasMany(PreCertificatePriceEstimateFinalTangibleAsset::class, 'pre_certificate_price_estimate_final_id');
    }

    public function apartmentFinals(): HasMany
    {
        return $this->hasMany(PreCertificatePriceEstimateApartmentFinal::class, 'pre_certificate_price_estimate_final_id');
    }
}
