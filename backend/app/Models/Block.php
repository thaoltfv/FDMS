<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Block extends Model
{
    use SoftDeletes;
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    protected $casts =  [
        'id' => 'integer',
        'nb_living_floor' => 'integer',
        'total_floors' => 'integer',
        'first_floor' => 'integer',
        'last_floor' => 'integer',
        'apartments_per_floor' => 'integer',
        'nb_basement' => 'integer',
        'nb_elevator' => 'integer',
        'total_apartments' => 'integer',
    ];
    protected $fillable = [
        'name',
        'status',
        'project_id',
        'total_floors',
        'nb_living_floor',
        'first_floor',
        'last_floor',
        'apartments_per_floor',
        'nb_basement',
        'nb_elevator',
        'rank_id',
        'total_apartments',
        'handover_year',
    ];

    public function floor(): HasMany
    {
        return $this->hasMany(Floor::class,'block_id', 'id');
    }
    public function rank(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class,'rank_id', 'id');
    }
}
