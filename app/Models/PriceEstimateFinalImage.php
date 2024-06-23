<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Province
 * @package App\Models
 */
class PriceEstimateFinalImage extends Model
{
    use SoftDeletes;
    protected $table = 'price_estimate_final_images';
    protected $casts = [
        'id' => 'integer',
        'link' => 'string',
        'picture_type' => 'string',
    ];
    protected $fillable = [
        'price_estimate_final_id',
        'link',
        'picture_type',
    ];
}
