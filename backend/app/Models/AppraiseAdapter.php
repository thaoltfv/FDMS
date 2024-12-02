<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Province
 * @package App\Models
 */
class AppraiseAdapter extends Model
{
    protected $table = 'appraise_adapter';
    use SoftDeletes;

    protected $casts = [
        'id' => 'integer',
        'change_purpose_price' => 'float',
        'change_violate_price' => 'float',
        'change_negotiated_price' => 'float'
    ];

    protected $fillable = [
        'appraise_id',
        'asset_general_id',
        'percent',
        'change_purpose_price',
        'change_violate_price',
        'change_negotiated_price'
    ];

}
