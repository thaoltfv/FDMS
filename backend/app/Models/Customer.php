<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Customer extends Model
{

    public $incrementing = false;


    protected $casts = [
        'id'        => 'integer',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'phone',
        'status',
        'address',
        'tax_code',
        'customer_picture',
        'customer_group_id',
        'created_by',
        'created_date',
        'created_at',
    ];

    public function pic(): HasMany
    {
        return $this->hasMany(CustomerPic::class, 'customer_id');
    }
    public function customerGroup(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class, 'customer_group_id', 'id');
    }

    public function getFullInfoAttribute()
    {
        // return "{$this->name} {$this->phone} {$this->address}";
        return "{$this->name} {$this->phone}";
    }
}
