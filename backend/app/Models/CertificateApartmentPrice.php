<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CertificateApartmentPrice extends Model
{
    use SoftDeletes;
    protected $casts = [
        'id' => 'integer',
        'value' => 'float',

    ];
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    protected $fillable = [
        'apartment_asset_id',
        'slug',
        'value',
        'description'
    ];
}
