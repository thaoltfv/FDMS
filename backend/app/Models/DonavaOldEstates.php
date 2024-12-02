<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Province
 * @package App\Models
 */
class DonavaOldEstates extends Model
{
    protected $table = 'estates';
    protected $connection = 'mysql_donava_old';

    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
    ];

    public function esLand(): HasMany
    {
        return $this->hasMany(DonavaOldEsLand::class,'es_id');
    }
    public function esBuild(): HasMany
    {
        return $this->hasMany(DonavaOldEsBuild::class,'es_id');
    }
    public function esValue(): HasMany
    {
        return $this->hasMany(DonavaOldEstatesValue ::class,'es_id');
    }
    public function esRoundabout(): HasMany
    {
        return $this->hasMany(DonavaOldEsRoundabout ::class,'es_id');
    }
    public function esLandAverage(): HasMany
    {
        return $this->hasMany(DonavaOldEsLandAverage ::class,'es_id');
    }
    public function esImage(): HasMany
    {
        return $this->hasMany(DonavaOldEsImage ::class,'es_id');
    }

    public function esApartment(): HasMany
    {
        return $this->hasMany(DonavaOldApartment ::class,'es_id');
    }

    public function esShape(): belongsTo
    {
        return $this->belongsTo(DonavaOldEsShape::class, 'shape_id', 'id');
    }

    public function esTradeType(): belongsTo
    {
        return $this->belongsTo(DonavaOldEsTradeType::class, 'trade_type', 'id');
    }

}
