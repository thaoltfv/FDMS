<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Province
 * @package App\Models
 */
class AppraisePic extends Model
{
    use SoftDeletes;
    protected $table = 'appraise_pics';
    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'appraise_id',
        'link',
        'type_id',
    ];

    public function picType(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class, 'type_id', 'id');
    }
}
