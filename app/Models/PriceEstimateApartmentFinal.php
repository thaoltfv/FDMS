<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Province
 * @package App\Models
 */
class PriceEstimateApartmentFinal extends Model
{
    use SoftDeletes;
    protected $table = 'price_estimate_apartment_finals';

    protected $fillable = [
        'price_estimate_final_id',
        'unit_price',
        'name',
        'total_price',
        'total_area',
    ];
}
