<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Province
 * @package App\Models
 */
class BlockSpecification extends Model
{
    use SoftDeletes;

    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'block_list_id',
        'asset_general_id',
        'built_year',
        'total_floor',
        'basement_floor',
        'commercial_floor',
        'living_floor',
        'lift_number',
        'other_utilities',
    ];

    public function blockLists(): BelongsTo
    {
        return $this->belongsTo(BlockList::class,'block_list_id','id');
    }

    public function basicUtilities(): belongsToMany
    {
        return $this->belongsToMany(Dictionary::class,'block_specification_has_basic_utilities','block_specification_id','basic_utility_id');
    }

}
