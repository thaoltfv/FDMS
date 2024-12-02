<?php

namespace App\Models;


use Elasticquent\ElasticquentTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Province
 * @package App\Models
 */
class Apartment extends Model
{
    use SoftDeletes;
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'name',
        'floor_id',
        'status',
    ];
}
