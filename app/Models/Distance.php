<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * Class Province
 * @package App\Models
 */
class Distance extends Model
{
    use SoftDeletes;
    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'name',
        'detail',
        'vt1',
        'vt2',
        'vt3',
        'vt4',
        'street_id',
        'district_id',
        'province_id',
    ];

    protected $hidden =[
        'province_id',
        'district_id',
        'deleted_at'
    ];

    public function street(): BelongsTo
    {
        return $this->belongsTo(Street::class,'street_id','id');
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class,'province_id','id');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class,'district_id','id');
    }
}
