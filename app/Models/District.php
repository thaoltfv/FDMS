<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mekaeil\LaravelUserManagement\Entities\Department;

/**
 * Class Province
 * @package App\Models
 */
class District extends Model
{
    use SoftDeletes;

    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'name',
        'province_id',
    ];

    protected $hidden =[
        'province_id',
        'deleted_at'
    ];

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class,'province_id');
    }

    public function wards():HasMany
    {
        return $this->hasMany(Ward::class,'district_id','id');
    }

    public function streets() :HasMany
    {
        return $this->hasMany(Street::class,'district_id','id');
    }

    public function distances():HasMany
    {
        return $this->hasMany(Distance::class,'district_id','id');
    }
}
