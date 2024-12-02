<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Province
 * @package App\Models
 */
class AppraisePropertyDetail extends Model
{
    use SoftDeletes;
    protected $table = 'appraise_property_details';
    protected $casts = [
        'id' => 'integer',
        'total_area' => 'float',
        'main_area' => 'float',
        'planning_area' => 'float',
        'circular_unit_price' => 'float',
        'is_transfer_facility' => 'boolean',

    ];

    protected $fillable = [
        'appraise_property_id',
        'land_type_purpose_id',
        // 'estimation_value',
        'position_type_id',
        'total_area',
        // 'price_land',
        'circular_unit_price',
        'is_transfer_facility',
        // 'k_rate',
        'is_zoning',
        'type_zoning',
        'planning_area',
        'main_area',
        'extra_planning'
    ];

    public function landTypePurpose(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class,'land_type_purpose_id','id');
    }

    public function positionType(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class,'position_type_id','id');
    }
}
