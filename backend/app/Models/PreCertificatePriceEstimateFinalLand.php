<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Province
 * @package App\Models
 */
class PreCertificatePriceEstimateFinalLand extends Model
{
    use SoftDeletes;
    protected $table = 'pre_certificate_price_estimate_final_lands';
    protected $casts = [
        'id' => 'integer',
        'total_area' => 'float',
        'main_area' => 'float',
        'planning_area' => 'float',
    ];

    protected $fillable = [
        'pre_certificate_price_estimate_final_id',
        'land_type_purpose_id',
        'unit_price',
        'total_price',
        'total_area',
        'planning_area',
        'main_area',
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
