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
class CompareAssetVersion extends Model
{
    use SoftDeletes;
    use ElasticquentTrait;

    protected $casts = [
        'id' => 'integer',
        'asset_general_data' => 'array',
    ];

    protected $fillable = [
        'asset_general_id',
        'version',
        'asset_general_data',
    ];

    function getIndexName()
    {
        return env('ELASTIC_ASSET_VERSION_INDEX');
    }
    function getTypeName()
    {
        return '_doc';
    }
}
