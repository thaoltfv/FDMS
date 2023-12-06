<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Province
 * @package App\Models
 */
class PreCertificateOtherDocuments extends Model
{
    use SoftDeletes;
    protected $table = 'pre_certificate_other_documents';
    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'certificate_id',
        'name',
        'link',
        'type',
        'size',
        'description',
        'created_by',
        'type_document'
    ];
	
	public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
