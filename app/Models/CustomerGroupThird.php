<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * Class Province
 * @package App\Models
 */
class CustomerGroupThird extends Model
{
    use SoftDeletes;

    protected $table = 'customer_groups_third';

    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'name',
        'first_id',
        'second_id',
    ];

    public function fisrtGroup(): BelongsTo
    {
        return $this->belongsTo(CustomerGroupFirst::class, 'first_id', 'id');
    }

    public function secondGroup(): BelongsTo
    {
        return $this->belongsTo(CustomerGroupSecond::class, 'second_id', 'id');
    }

    public function fourthGroups(): HasMany
    {
        return $this->HasMany(CustomerGroupFourth::class, 'third_id', 'id');
    }
}
