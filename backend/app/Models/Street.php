<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * Class Province
 * @package App\Models
 */
class Street extends Model
{
    use SoftDeletes;
    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'name',
        'district_id',
        'province_id',
    ];

    protected $hidden =[
        'province_id',
        'district_id',
        'deleted_at'
    ];

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class,'province_id','id');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class,'district_id','id');
    }

    public function distances(): HasMany
    {
        return $this->HasMany(Distance::class,'street_id','id');
    }
}
