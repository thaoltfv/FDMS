<?php

namespace App\Models;


use Elasticquent\ElasticquentTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Province
 * @package App\Models
 */
class CertificateAssetVersion extends Model
{
    use SoftDeletes;
    use ElasticquentTrait;
	
	protected $table = 'certificate_asset_versions';
	
    protected $casts = [
        'id' => 'integer',
        'version' => 'integer',
    ];

    protected $fillable = [
        'appraise_id',
        'version',
		'status',
    ];

    function getIndexName()
    {
        return env('ELASTIC_APPRAISE_VERSION_INDEX');
    }
    function getTypeName()
    {
        return '_doc';
    }
}
