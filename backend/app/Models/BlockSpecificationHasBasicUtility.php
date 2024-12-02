<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Province
 * @package App\Models
 */
class BlockSpecificationHasBasicUtility  extends Model
{
    use SoftDeletes;

    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'basic_utility_id',
        'block_specification_id',
    ];

    public function basicUtility(): BelongsTo
    {
        return $this->belongsTo(BasicUtility::class,'basic_utility_id','id');
    }
}
