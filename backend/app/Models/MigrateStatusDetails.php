<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Province
 * @package App\Models
 */
class MigrateStatusDetails extends Model
{
    use SoftDeletes;
    protected $table = 'migrate_status_details';

    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'migrate_status_id',
        'asset_id',
        'status',
    ];
}
