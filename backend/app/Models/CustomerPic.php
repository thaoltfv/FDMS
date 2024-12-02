<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Province
 * @package App\Models
 */
class CustomerPic extends Model
{
    use SoftDeletes;

    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'customer_id',
        'link',
        'picture_type',
    ];

}
