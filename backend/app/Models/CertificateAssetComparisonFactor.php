<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Province
 * @package App\Models
 */
class CertificateAssetComparisonFactor extends Model
{
    protected $table = 'certificate_asset_comparison_factor';
    use SoftDeletes;

    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'appraise_id',
        'asset_general_id',
        'status',
        'type',
        'appraise_title',
        'asset_title',
        'description',
        'adjust_percent',
        'name',
        'position',
        'adjust_coefficient'
    ];
}
