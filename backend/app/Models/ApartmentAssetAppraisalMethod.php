<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApartmentAssetAppraisalMethod extends Model
{
    use SoftDeletes;
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    protected $casts =[
        'id' => 'integer',
    ];
    protected $fillable =[
        'apartment_asset_id',
        'slug',
        'slug_value',
        'value',
        'description',
    ];
}
