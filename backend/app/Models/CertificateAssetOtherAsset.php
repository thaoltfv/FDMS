<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Province
 * @package App\Models
 */
class CertificateAssetOtherAsset extends Model
{
    use SoftDeletes;
    protected $table = 'certificate_asset_other_assets';
    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'appraise_id',
        'name',
        'total',
        'dvt',
        'description',
        'total_area',
        'unit_price',
        'total_price',
    ];

}
