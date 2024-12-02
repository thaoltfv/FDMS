<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Province
 * @package App\Models
 */
class CertificateBasisProperty extends Model
{
    protected $table = 'certificate_basis_property';
    use SoftDeletes;

    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'certificate_id',
        'certificate_basis_property_id',
    ];
    public function certificateBasisProperty(): BelongsTo
    {
        return $this->belongsTo(AppraiseOtherInformation::class,'certificate_basis_property_id','id');
    }
}
