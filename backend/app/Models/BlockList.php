<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Province
 * @package App\Models
 */
class BlockList extends Model
{
    use SoftDeletes;

    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'apartment_id',
        'name',
        'coordinates',
    ];

    public function apartment(): BelongsTo
    {
        return $this->belongsTo(Apartment::class,'asset_general_id','id');
    }

    public function blockSpecification(): HasMany
    {
        return $this->hasMany(BlockSpecification::class,'block_list_id');
    }

    public function roomDetails(): HasMany
    {
        return $this->hasMany(RoomDetail::class,'block_list_id');
    }

}
