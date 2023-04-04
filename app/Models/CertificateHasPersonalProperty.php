<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CertificateHasPersonalProperty extends Model
{
    use SoftDeletes;
    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'personal_property_id',
        'certificate_id',
        'version',
    ];

    public function personalProperties(): BelongsTo
    {
        return $this->belongsTo(CertificatePersonalProperty::class,'personal_property_id','id');
    }
}
