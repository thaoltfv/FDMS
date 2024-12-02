<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;
    protected $hidden = [
        // 'created_at',
        // 'updated_at',
        'deleted_at',
    ];
    protected $casts = [
        'id' => 'integer',
        'total_blocks' => 'integer',
        'total_apartments' => 'integer',
        'utilities' => 'array',
        'basement' => 'array',
        'elevator' => 'array',
        'rank' => 'array',
    ];
    protected $fillable = [
        'name',
        'province_id',
        'district_id',
        'ward_id',
        'street_id',
        'rank',
        'total_blocks',
        'total_apartments',
        'nb_swim_dens',
        'coordinates',
        'status',
        'utilities',
        'basement',
        'elevator',
        'handover_year',
    ];

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class,'province_id','id');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class,'district_id','id');
    }

    public function ward(): BelongsTo
    {
        return $this->belongsTo(Ward::class,'ward_id','id');
    }

    public function street(): BelongsTo
    {
        return $this->belongsTo(Street::class,'street_id','id');
    }

    public function block(): HasMany
    {
        return $this->hasMany(Block::class,'project_id' , 'id');
    }
}
