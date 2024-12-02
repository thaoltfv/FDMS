<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Province
 * @package App\Models
 */
class DonavaOldEsLand extends Model
{
    protected $table = 'es_lands';
    protected $connection = 'mysql_donava_old';

    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
    ];

    public function landType(): belongsTo
    {
        return $this->belongsTo(DonavaOldLandType::class, 'land_type', 'id');
    }
}
