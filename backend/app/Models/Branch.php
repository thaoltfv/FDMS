<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mekaeil\LaravelUserManagement\Entities\Department;

/**
 * Class Province
 * @package App\Models
 */
class Branch extends Model
{
    use SoftDeletes;

    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'name',
        'address',
        'acronym',
    ];

}
