<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Province
 * @package App\Models
 */
class PreCertificatePriceEstimateFinalTangibleAsset extends Model
{
    use SoftDeletes;
    protected $table = 'pre_certificate_price_estimate_final_tangible_assets';
    protected $casts = [
        'id' => 'integer',
        'total_area' => 'float',
        'main_area' => 'float',
        'planning_area' => 'float',
    ];

    protected $fillable = [
        'pre_certificate_price_estimate_final_id',
        'unit_price',
        'total_price',
        'total_construction_area',
        'remaining_quality',
        'building_type_id',
    ];

    public function buildingType(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class, 'building_type_id', 'id');
    }
}
