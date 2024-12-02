<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Province
 * @package App\Models
 */
class BasicUtility  extends Model
{
    use SoftDeletes;

    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'type',
        'description',
    ];

    public function blockSpecification(): belongsToMany
    {
        return $this->belongsToMany(BlockSpecification::class,'block_specification_has_basic_utilities');
    }
}
