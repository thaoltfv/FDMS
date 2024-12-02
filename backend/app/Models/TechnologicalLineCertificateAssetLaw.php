<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TechnologicalLineCertificateAssetLaw extends Model
{
    use SoftDeletes;
    protected $casts = [
        'id' => 'integer',
    ];
    protected $fillable = [
        'id',
        'appraise_law_id',
        'technology_asset_id',
        'document_num',
        'document_date',
        'description',
        'legal_name_holder',
        'origin_of_use',
        'content',
        'duration',
        'certifying_agency',
    ];

    public function lawDocument():BelongsTo
    {
        return $this->belongsTo(AppraiseLawDocument::class,'appraise_law_id','id');
    }
}
