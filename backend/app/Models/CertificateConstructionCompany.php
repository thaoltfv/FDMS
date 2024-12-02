<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Province
 * @package App\Models
 */
class CertificateConstructionCompany extends Model
{
    protected $table = 'certificate_construction_company';
    use SoftDeletes;

    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'certificate_id',
        'construction_company_id',
    ];
    public function constructionCompany(): BelongsTo
    {
        return $this->belongsTo(CertificateAssetConstructionCompany::class,'construction_company_id','id');
    }
}
