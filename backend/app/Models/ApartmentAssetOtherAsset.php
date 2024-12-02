<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApartmentAssetOtherAsset extends Model
{
    use SoftDeletes;

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    protected $casts = [
        'id' => 'integer',
        'quantity' => 'float',
        'unit_price' => 'integer',
        'total_price' => 'integer',
    ];

    protected $fillable = [
        'apartment_asset_id',
        'name',
        'quantity',
        'unit',
        'unit_price',
        'total_price',
        'description',
    ];
}
