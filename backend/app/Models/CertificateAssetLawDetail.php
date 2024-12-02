<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Province
 * @package App\Models
 */
class CertificateAssetLawDetail extends Model
{
    use SoftDeletes;
    protected $table = 'certificate_asset_law_details';

    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'appraise_law_id',
        'land_type_purpose_id',
        'total_area',
        'expiry_type',
        'expiry_date',
        'is_zoning',
    ];
    
    public function landTypePurpose(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class,'land_type_purpose_id','id');
    }
}
