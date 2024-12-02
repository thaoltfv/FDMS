<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Province
 * @package App\Models
 */
class AppraiseDictionary extends Model
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
        //'created_by'
    ];
}
