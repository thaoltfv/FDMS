<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @package App\Models
 */
class AppraisePrice extends Model
{
    use SoftDeletes;
    protected $table = 'appraise_prices';
    protected $casts = [
        'id' => 'integer',
        'value' => 'float',
    ];

    protected $fillable = [
        'appraise_id',
        'slug',
        'value',
        'description',
    ];
}
