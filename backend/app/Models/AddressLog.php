<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * Class Province
 * @package App\Models
 */
class AddressLog extends Model
{
    use SoftDeletes;
    protected $casts = [
        'id' => 'integer',
    ];
    protected $fillable = [
        'input'
    ];
}
