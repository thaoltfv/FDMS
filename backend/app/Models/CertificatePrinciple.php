<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Province
 * @package App\Models
 */
class CertificatePrinciple extends Model
{
    protected $table = 'certificate_principle';
    use SoftDeletes;

    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'certificate_id',
        'certificate_principle_id',
    ];
    public function certificatePrinciple(): BelongsTo
    {
        return $this->belongsTo(AppraiseOtherInformation::class,'certificate_principle_id','id');
    }
}
