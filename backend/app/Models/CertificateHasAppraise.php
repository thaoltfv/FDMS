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
class CertificateHasAppraise  extends Model
{
    protected $table = 'certificate_has_appraises';
    use SoftDeletes;

    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'appraise_id',
        'certificate_id',
        'version',
    ];

    public function appraise(): BelongsTo
    {
        return $this->belongsTo(CertificateAsset::class,'appraise_id','id');
    }
    public function pic(): HasMany
    {
        return $this->hasMany(CertificateAssetPic::class,'appraise_id','id');
    }
}
