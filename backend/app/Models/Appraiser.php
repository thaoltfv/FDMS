<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Province
 * @package App\Models
 */
class Appraiser extends Model
{
    use SoftDeletes;

    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'name',
        'appraiser_number',
        'appraise_position_id',
        'user_id',
        'branch_id',
    ];

    public function appraisePosition(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class, 'appraise_position_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function appraiserBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
}
