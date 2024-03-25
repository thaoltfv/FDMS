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

class PriceEstimateFinal extends Model
{
    use  SoftDeletes;

    protected $table = 'price_estimate_finals';

    protected $fillable = [
        'price_estimate_id',
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
        'created_by'
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
        return $this->hasMany(PriceEstimateFinalLand::class, 'price_estimate_final_id');
    }
    public function totalArea(): HasMany
    {
        return $this->hasMany(PriceEstimateFinalLand::class, 'price_estimate_final_id')
            ->where('total_area', '>', 0);
    }

    public function planningArea(): HasMany
    {
        return $this->hasMany(PriceEstimateFinalLand::class, 'price_estimate_final_id')
            ->where('planning_area', '>', 0);
    }


    public function tangibleAssets(): HasMany
    {
        return $this->hasMany(PriceEstimateFinalTangibleAsset::class, 'price_estimate_final_id');
    }
}
