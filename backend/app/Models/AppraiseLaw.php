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
class AppraiseLaw extends Model
{
    use SoftDeletes;
    protected $table = 'appraise_law';

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
        'origin_of_use',
        'content',
        'duration',
        'law_date',
        'note',
        'document_file'
    ];

    public function law(): BelongsTo
    {
        return $this->belongsTo(AppraiseLawDocument::class,'appraise_law_id','id');
    }

    public function lawDetails(): HasMany
    {
        return $this->hasMany(AppraiseLawDetail::class, 'appraise_law_id');
    }

	public function landDetails(): HasMany
    {
        return $this->hasMany(AppraiseLawLandDetail::class, 'appraise_law_id');
    }

    public function purposeDetails(): HasMany
    {
        return $this->hasMany(AppraiseLawPurposeDetail::class, 'appraise_law_id');
    }
}
