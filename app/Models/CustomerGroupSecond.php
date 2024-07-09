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
class CustomerGroupSecond extends Model
{
    use SoftDeletes;

    protected $table = 'customer_groups_second';

    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'name',
        'first_id',
    ];

    // protected $hidden =[
    //     'province_id',
    //     'deleted_at'
    // ];

    public function firstGroup(): BelongsTo
    {
        return $this->belongsTo(CustomerGroupFirst::class, 'first_id');
    }

    public function thirdGroups(): HasMany
    {
        return $this->hasMany(CustomerGroupThird::class, 'second_id', 'id');
    }

    public function fourthGroups(): HasMany
    {
        return $this->hasMany(CustomerGroupFourth::class, 'second_id', 'id');
    }
}
