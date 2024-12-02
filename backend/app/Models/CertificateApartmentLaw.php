<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CertificateApartmentLaw extends Model
{
    use SoftDeletes;

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'apartment_asset_id',
        'appraise_law_id',
        'document_num',
        'document_date',
        'description',
        'legal_name_holder',
        'certifying_agency',
        'origin_of_use',
        'content',
        'duration',
    ];

    public function lawDocument(): BelongsTo
    {
        return $this->belongsTo(AppraiseLawDocument::class,'appraise_law_id','id');
    }
}
