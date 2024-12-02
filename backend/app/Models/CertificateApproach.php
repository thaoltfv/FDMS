<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Province
 * @package App\Models
 */
class CertificateApproach extends Model
{
    protected $table = 'certificate_approach';
    use SoftDeletes;

    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'certificate_id',
        'certificate_approach_id',
    ];
    public function certificateApproach(): BelongsTo
    {
        return $this->belongsTo(AppraiseOtherInformation::class,'certificate_approach_id','id');
    }
}
