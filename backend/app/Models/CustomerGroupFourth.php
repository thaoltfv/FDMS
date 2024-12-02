<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * Class Province
 * @package App\Models
 */
class CustomerGroupFourth extends Model
{
    use SoftDeletes;

    protected $table = 'customer_groups_fourth';

    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'name',
        'first_id',
        'second_id',
        'third_id'
    ];



    public function firstGroup(): BelongsTo
    {
        return $this->belongsTo(CustomerGroupFirst::class, 'first_id', 'id');
    }

    public function secondGroup(): BelongsTo
    {
        return $this->belongsTo(CustomerGroupSecond::class, 'second_id', 'id');
    }

    public function thirdGroup(): BelongsTo
    {
        return $this->belongsTo(CustomerGroupThird::class, 'third_id', 'id');
    }
}
