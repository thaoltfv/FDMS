<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Province
 * @package App\Models
 */
class PriceEstimatePropertyTurningTime extends Model
{
    protected $table = 'price_estimate_property_turning_time';
    use SoftDeletes;

    protected $casts = [
        'id' => 'integer',
        'main_road_length' => 'float',
        'main_road_distance' => 'float',
    ];

    protected $fillable = [
        'price_estimate_property_id',
        'turning',
        'main_road_length',
        'material_id',
        'main_road_distance',
    ];

    public function material(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class, 'material_id', 'id');
    }
}
