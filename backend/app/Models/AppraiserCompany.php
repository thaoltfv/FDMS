<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Province
 * @package App\Models
 */
class AppraiserCompany extends Model
{
    use SoftDeletes;

    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'name',
        'down_line',
        'acronym',
        'address',
        'phone_number',
        'fax_number',
        'appraiser_id',
        'link',
        'appraiser'
    ];

    public function appraiser(): BelongsTo
    {
        return $this->belongsTo(Appraiser::class, 'appraiser_id', 'id');
    }
}
