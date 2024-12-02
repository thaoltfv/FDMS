<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApartmentAssetComparisonFactor extends Model
{
    use SoftDeletes;

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $casts =[
        'id' => 'integer',
        'position' => 'integer',
        // 'adjust_percent' => 'integer',
    ];

    protected $fillable = [
        'apartment_asset_id',
        'asset_general_id',
        'status',
        'type',
        'apartment_title',
        'asset_title',
        'description',
        'adjust_percent',
        'name',
        'position',
        'adjust_coefficient'
    ];

}
