<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Province
 * @package App\Models
 */
class ComparePropertyTurningTime extends Model
{
    protected $table = 'compare_property_turning_time';
    use SoftDeletes;

    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'compare_property_id',
        'turning',
        'description',
        'main_road_length',
        'material_id',
        'is_near_main_road',
        'is_alley_with_connection',

    ];

    public function material(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class,'material_id','id');
    }

}
