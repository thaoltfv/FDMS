<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApartmentDictionary extends Model
{
    use SoftDeletes;

    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'type',
        'appraise_title',
        'appraise_point',
        'asset_title',
        'asset_point',
        'description',
        'difference_point',
        'appraise_percent',
        'asset_percent',
        'adjust_percent',
    ];
}

