<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Province
 * @package App\Models
 */
class CompareTangiblePic extends Model
{
    use SoftDeletes;

    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'compare_tangible_id',
        'link',
        'picture_type',
    ];



    public function propertyPics(): BelongsTo
    {
        return $this->belongsTo(CompareTangibleAsset::class,'compare_tangible_id','id');
    }
}
