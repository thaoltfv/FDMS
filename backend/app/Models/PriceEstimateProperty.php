<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Province
 * @package App\Models
 */
class PriceEstimateProperty extends Model
{
    use SoftDeletes;
    protected $table = 'price_estimate_properties';
    protected $casts = [
        'id' => 'integer',
        'appraise_land_sum_area' => 'float',
        'front_side' => 'float',
        'main_road_length' => 'float',
    ];
    protected $fillable = [
        'price_estimate_id',
        'description',
        'front_side',
        'main_road_length',
        'material_id',
    ];

    public function propertyDetail(): HasMany
    {
        return $this->hasMany(PriceEstimatePropertyDetail::class, 'price_estimate_property_id')->orderBy('is_transfer_facility', 'DESC');
    }

    public function propertyTurningTime(): HasMany
    {
        return $this->hasMany(PriceEstimatePropertyTurningTime::class, 'price_estimate_property_id');
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class, 'material_id', 'id');
    }
}
