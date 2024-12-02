<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OtherCertificateAssetPrice extends Model
{
    use SoftDeletes;
    protected $hidden = [
        'laravel_through_key'
     ];
    protected $casts = [
        'id' => 'integer',
        'quantity' => 'float',
        'remaining_quality' => 'float',
        'total_price' => 'float',
        'unit_price' => 'float',

    ];
    protected $fillable = [
        'other_asset_id',
        'quantity',
        'remaining_quality',
        'unit',
        'total_price',
        'unit_price'
    ];
}
