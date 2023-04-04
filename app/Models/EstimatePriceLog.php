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
class EstimatePriceLog extends Model
{
    use ElasticquentTrait;
    use SoftDeletes;
    protected $casts = [
        'id' => 'integer',
    ];
    protected $fillable = [
        'input',
        'output',
        'status',
        'type',
        'user',
    ];

    function getIndexName()
    {
        return env('ELASTIC_REPORT_DEFAULT_INDEX');
    }
    function getTypeName()
    {
        return '_doc';
    }
}
