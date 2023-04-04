<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CertificateHasRealEstate extends Model
{
    use SoftDeletes;

    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'real_estate_id',
        'certificate_id',
        'version',
    ];
    public function realEstates():BelongsTo
    {
        return $this->belongsTo(CertificateRealEstate::class, 'real_estate_id', 'id');
    }
}
