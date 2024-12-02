<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * Class Province
 * @package App\Models
 */
class DonavaOldLandType extends Model
{
    protected $table = 'land_type';
    protected $connection = 'mysql_donava_old';

    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
    ];
}
