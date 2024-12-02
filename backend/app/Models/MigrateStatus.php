<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Province
 * @package App\Models
 */
class MigrateStatus extends Model
{
    use SoftDeletes;
    protected $table = 'migrate_status';

    protected $casts = [
        'id' => 'integer',
        'limit' => 'integer',
        'page' => 'integer',
        'total_records' => 'integer',
    ];

    protected $fillable = [
        'limit',
        'page',
        'total_records',
        'type',
    ];
}
