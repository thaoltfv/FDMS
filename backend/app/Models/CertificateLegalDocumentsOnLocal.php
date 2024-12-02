<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Province
 * @package App\Models
 */
class CertificateLegalDocumentsOnLocal extends Model
{
    protected $table = 'certificate_legal_documents_on_local';
    use SoftDeletes;

    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'certificate_id',
        'certificate_law_id',
    ];
    public function legalDocumentsOnLocal(): BelongsTo
    {
        return $this->belongsTo(AppraiseLawDocument::class,'certificate_law_id','id');
    }
}
