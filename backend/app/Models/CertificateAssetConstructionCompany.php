<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Province
 * @package App\Models
 */
class CertificateAssetConstructionCompany extends Model
{
    use SoftDeletes;
	protected $table = 'certificate_asset_construction_companies';
    //protected $dateFormat = 'd-m-Y H:i:s';

    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
		'certificate_id',
		'appraise_id',
		'company_id',
        'name',
        'address',
        'phone_number',
        'manager_name',
        'unit_price_m2',
        'is_defaults',
        'tangible_asset_id',
    ];

	public function appraise(): BelongsTo
    {
        return $this->belongsTo(CertificateAsset::class, 'appraise_id', 'id');
    }

	public function certificate(): BelongsTo
    {
        return $this->belongsTo(Certificate::class, 'certificate_id', 'id');
    }

    /* public function constructionCompany(): BelongsTo
    {
        return $this->belongsTo(AppraisalConstructionCompany::class,'company_id','id');
    } */

    public function getConstructionCompanyAttribute()
    {
        return $this;
    }
}
