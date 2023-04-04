<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Province
 * @package App\Models
 */
class AppraiseOtherInformation extends Model
{
    use SoftDeletes;

    protected $casts = [
        'id' => 'integer',
        'dictionary_acronym' => 'array',
        'status' => 'boolean',

    ];

    protected $fillable = [
        'name',
        'description',
        'type',
        'is_defaults',
        'dictionary_acronym',
        'status',
    ];
}
