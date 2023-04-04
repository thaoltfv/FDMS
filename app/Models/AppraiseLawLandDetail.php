<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Province
 * @package App\Models
 */
class AppraiseLawLandDetail extends Model
{
    use SoftDeletes;
    protected $table = 'appraise_law_land_details';

    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'appraise_law_id',
        'doc_no',
        'land_no',
        'doc_no_old',
        'land_no_old',
        'deleted_at',
    ];
}
