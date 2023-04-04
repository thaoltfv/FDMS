<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Province
 * @package App\Models
 */
class AppraiseTangibleComparisonFactor extends Model
{
    protected $table = 'appraise_tangible_comparison_factor';
    use SoftDeletes;

    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'appraise_id',
        'p1',
        'h1',
        'p2',
        'h2',
        'p3',
        'h3',
        'd4',
        'h4',
        'p5',
        'h5',
        'tangible_asset_id',
    ];
}
