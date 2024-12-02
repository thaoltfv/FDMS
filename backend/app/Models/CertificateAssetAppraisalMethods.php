<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @package App\Models
 */
class CertificateAssetAppraisalMethods extends Model
{
    use SoftDeletes;
    protected $table = 'certificate_asset_appraisal_methods';
    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'appraise_id',
        'slug',
        'slug_value',
        'value',
        'description',
    ];
}
