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
class CompareOtherAsset extends Model
{
    use SoftDeletes;

    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'asset_general_id',
        'other_type_asset_id',
        'total_amount',
        'other_asset',
    ];

    public function general(): BelongsTo
    {
        return $this->belongsTo(CompareAssetGeneral::class,'asset_general_id','id');
    }

    public function pic(): HasMany
    {
        return $this->hasMany(CompareOtherPic::class,'asset_other_id');
    }

    public function otherTypeAsset(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class,'other_type_asset_id','id');
    }
}
