<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Floor extends Model
{
    use SoftDeletes;
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    protected $casts =  [
        'id' => 'integer',
    ];
    protected $fillable = [
        'name',
        'status',
        'block_id',
    ];

    public function apartment(): HasMany
    {
        return $this->hasMany(Apartment::class,'floor_id', 'id');
    }
}
