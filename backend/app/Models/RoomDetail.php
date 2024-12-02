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
class RoomDetail extends Model
{
    use SoftDeletes;

    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'block_list_id',
        'asset_general_id',
        'direction_id',
        'furniture_quality_id',
        'two_sides_room',
        'area',
        'bedroom_num',
        'room_num',
        'floor',
        'wc_num',
        'unit_price',
        'description',
        'legal_id',
        'loai_can_ho_id'
    ];

    public function blockLists(): BelongsTo
    {
        return $this->belongsTo(BlockList::class,'block_list_id','id');
    }

    public function direction(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class,'direction_id','id');
    }

    public function furnitureQuality(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class,'furniture_quality_id','id');
    }

    public function loaicanho(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class,'loai_can_ho_id','id');
    }

    public function roomFurnitureDetails(): HasMany
    {
        return $this->hasMany(RoomFurnitureDetail::class,'room_detail_id');
    }
    public function legal(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class,'legal_id','id');
    }
}
