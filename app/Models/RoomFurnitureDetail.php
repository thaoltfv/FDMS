<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Province
 * @package App\Models
 */
class RoomFurnitureDetail extends Model
{
    use SoftDeletes;

    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'room_detail_id',
        'name',
        'number',
        'description',

    ];

    public function roomDetail(): BelongsTo
    {
        return $this->belongsTo(RoomDetail::class,'room_detail_id','id');
    }

}
