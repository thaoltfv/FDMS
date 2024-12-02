<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Province
 * @package App\Models
 */
class ComparePropertyDetail extends Model
{
    use SoftDeletes;

    protected $casts = [
        'id' => 'integer',
        'total_area' => 'float',
    ];

    protected $fillable = [
        'compare_property_id',
        'land_type_purpose',
        'estimation_value',
        'position_type_id',
        'total_area',
        'price_land',
        'convert_fee',
        'circular_unit_price',
        'k_rate',
    ];

    public function landTypePurposeData(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class,'land_type_purpose','id');
    }

    public function positionType(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class,'position_type_id','id');
    }
}
