<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApartmentAssetHasAsset extends Model
{
    use SoftDeletes;
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $casts =[
        'id' => 'integer',
    ];

    protected $fillable = [
        'apartment_asset_id',
        'asset_general_id',
        'version'
    ];

}
