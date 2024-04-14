<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Province
 * @package App\Models
 */
class PreCertificatePriceEstimatePic extends Model
{
    use SoftDeletes;
    protected $table = 'pre_certificate_price_estimate_pics';
    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'pre_certificate_price_estimate_id',
        'link',
        'type_id',
    ];

    public function picType(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class, 'type_id', 'id');
    }
}
