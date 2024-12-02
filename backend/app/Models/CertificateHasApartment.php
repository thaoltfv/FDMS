<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CertificateHasApartment extends Model
{
    use SoftDeletes;

    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'certificate_asset_id',
        'certificate_id',
        'version',
    ];

    public function apartment(): BelongsTo
    {
        return $this->belongsTo(CertificateApartment::class,'certificate_asset_id','id');
    }

}
