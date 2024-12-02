<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * Class Province
 * @package App\Models
 */
class DonavaOldEsImage extends Model
{
    protected $table = 'es_images';
    protected $connection = 'mysql_donava_old';

    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
    ];
}
