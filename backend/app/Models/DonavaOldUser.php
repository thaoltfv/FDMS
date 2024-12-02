<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * Class Province
 * @package App\Models
 */
class DonavaOldUser extends Model
{
    protected $table = 'users';
    protected $connection = 'mysql_donava_old';

    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
    ];
}
