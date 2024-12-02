<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PreCertificateHasPriceEstimate extends Model
{
    use SoftDeletes;

    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'price_estimate_id',
        'pre_certificate_id',
        'version',
    ];
    public function priceEstimates(): BelongsTo
    {
        return $this->belongsTo(PreCertificatePriceEstimate::class, 'price_estimate_id', 'id');
    }
}
