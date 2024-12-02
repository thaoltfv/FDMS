<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Province
 * @package App\Models
 */
class AppraiseLegalDocumentsOnLand extends Model
{
    protected $table = 'appraise_legal_documents_on_land';
    use SoftDeletes;

    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'appraise_id',
        'appraise_law_id',
    ];
    public function legalDocumentsOnLand(): BelongsTo
    {
        return $this->belongsTo(AppraiseLawDocument::class,'appraise_law_id','id');
    }
}
