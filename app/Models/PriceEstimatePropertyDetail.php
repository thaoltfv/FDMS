<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Province
 * @package App\Models
 */
class PriceEstimatePropertyDetail extends Model
{
    use SoftDeletes;
    protected $table = 'price_estimate_property_details';
    protected $casts = [
        'id' => 'integer',
        'total_area' => 'float',
        'main_area' => 'float',
        'planning_area' => 'float',
        'is_transfer_facility' => 'boolean',
    ];

    protected $fillable = [
        'price_estimate_property_id',
        'land_type_purpose_id',
        // 'position_type_id',
        'total_area',
        'is_transfer_facility',
        'planning_area',
        'main_area',
        'type_zoning',
    ];

    public function landTypePurpose(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class, 'land_type_purpose_id', 'id');
    }

    // public function positionType(): BelongsTo
    // {
    //     return $this->belongsTo(Dictionary::class,'position_type_id','id');
    // }
}
