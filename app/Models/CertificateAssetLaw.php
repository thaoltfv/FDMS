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
class CertificateAssetLaw extends Model
{
    use SoftDeletes;
    protected $table = 'certificate_asset_law';

    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'appraise_id',
        'appraise_law_id',
        'date',
        'description',
        'legal_name_holder',
        'certifying_agency',
        'start_date',
        'origin_of_use',
        'doc_no',
        'land_no',
        'content',
        'law_date',
    ];

    public function law(): BelongsTo
    {
        return $this->belongsTo(AppraiseLawDocument::class,'appraise_law_id','id');
    }

    public function lawDetails(): HasMany
    {
        return $this->hasMany(CertificateAssetLawDetail::class, 'appraise_law_id');
    }

	public function landDetails(): HasMany
    {
        return $this->hasMany(CertificateAssetLawLandDetail::class, 'appraise_law_id');
    }
}
