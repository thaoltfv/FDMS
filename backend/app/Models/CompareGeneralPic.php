<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Province
 * @package App\Models
 */
class CompareGeneralPic extends Model
{
    use SoftDeletes;

    protected $casts = [
        'id' => 'integer',
        'asset_general_id' => 'integer',
        'link' => 'string',
        'picture_type' => 'string',
    ];

    protected $fillable = [
        'asset_general_id',
        'link',
        'picture_type',
    ];

    public function generalPics(): BelongsTo
    {
        return $this->belongsTo(CompareAssetGeneral::class,'asset_general_id','id');
    }
}
