<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * Class Province
 * @package App\Models
 */
class UnitPrice extends Model
{
    use SoftDeletes;
    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'province',
        'district',
        'ward',
        'street',
        'distance',
        'vt1',
        'vt2',
        'vt3',
        'vt4',
        'land_type',
    ];

}
