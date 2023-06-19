<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Province
 * @package App\Models
 */
class ComparePropertyDoc extends Model
{
    protected $table = 'compare_property_doc';
    use SoftDeletes;

    protected $casts = [
        'id' => 'integer',
        'doc_num' => 'string',
        'plot_num' => 'string',
    ];

    protected $fillable = [
        'compare_property_id',
        // 'doc_num',
        // 'plot_num',
    ];

}
