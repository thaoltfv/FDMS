<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * Class Province
 * @package App\Models
 */
class Province extends Model
{
    use SoftDeletes;

    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'name',
    ];

    protected $hidden =[
        'deleted_at'
    ];

    public function districts(): HasMany
    {
        return $this->hasMany(District::class,'province_id');
    }

    public function wards(): HasMany
    {
        return $this->hasMany(Ward::class,'province_id');
    }

    public function streets(): HasMany
    {
        return $this->hasMany(Street::class,'province_id');
    }
}
