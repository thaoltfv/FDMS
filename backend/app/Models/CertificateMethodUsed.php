<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Province
 * @package App\Models
 */
class CertificateMethodUsed extends Model
{
    protected $table = 'certificate_method_used';
    use SoftDeletes;

    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'certificate_id',
        'certificate_method_used_id',
    ];
    public function certificateMethodUsed(): BelongsTo
    {
        return $this->belongsTo(AppraiseOtherInformation::class,'certificate_method_used_id','id');
    }
}
