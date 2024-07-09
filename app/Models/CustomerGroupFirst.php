<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * Class Province
 * @package App\Models
 */
class CustomerGroupFirst extends Model
{
    use SoftDeletes;

    protected $table = 'customer_groups_first';

    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'name',
    ];

    protected $hidden = [
        'deleted_at'
    ];

    public function secondGroups(): HasMany
    {
        return $this->hasMany(CustomerGroupSecond::class, 'first_id');
    }

    public function thirdGroups(): HasMany
    {
        return $this->hasMany(CustomerGroupThird::class, 'first_id');
    }

    public function fourthGroups(): HasMany
    {
        return $this->hasMany(CustomerGroupFourth::class, 'first_id');
    }
}
