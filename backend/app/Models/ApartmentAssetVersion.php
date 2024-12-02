<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApartmentAssetVersion extends Model
{
    use SoftDeletes;
    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable  = [
        'apartment_asset_id',
        'version',
        'status',
    ];
    
    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}
