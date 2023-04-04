<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Province
 * @package App\Models
 */
class CompareOtherPic extends Model
{
    use SoftDeletes;

    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'asset_other_id',
        'link',
        'picture_type',
    ];

    public function propertyPics(): BelongsTo
    {
        return $this->belongsTo(CompareOtherAsset::class,'asset_other_id','id');
    }
}
