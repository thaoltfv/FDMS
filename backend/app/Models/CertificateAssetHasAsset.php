<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Province
 * @package App\Models
 */
class CertificateAssetHasAsset  extends Model
{
    use SoftDeletes;
	protected $table = 'certificate_asset_has_assets';
	
    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'asset_general_id',
        'asset_property_detail_id',
        'appraise_id',
        'appraise_price',
        'asset_price',
        'version',
    ];

    public function assetGeneral(): BelongsTo
    {
        return $this->belongsTo(CompareAssetGeneral::class,'asset_general_id','id');
    }
}
