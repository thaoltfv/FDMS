<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @package App\Models
 */
class CertificatePrice extends Model
{
    use SoftDeletes;
    protected $table = 'certificate_prices';
    protected $casts = [
        'id' => 'integer',
		'value' => 'float',
    ];

    protected $fillable = [
        'certificate_id',
        'slug',
        'value',
        'description',
    ];
}
