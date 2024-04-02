<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Province
 * @package App\Models
 */
class PreCertificateExportDocuments extends Model
{
    use SoftDeletes;
    protected $table = 'pre_certificate_export_documents';
    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'pre_certificate_id',
        'certificate_id',
        'name',
        'link',
        'type',
        'size',
        'description',
        'created_by',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
