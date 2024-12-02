<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Province
 * @package App\Models
 */
class AppraiseLawPurposeDetail extends Model
{
    use SoftDeletes;
    protected $table = 'appraise_law_purpose_details';

    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'appraise_law_id',
        'land_type_purpose_id',
        'total_area',
        'deleted_at',
    ];
}
