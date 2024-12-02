<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OtherCertificateBriefLawInfo extends Model
{
    use SoftDeletes;
    protected $casts = [
        'id' => 'integer',
    ];
    protected $fillable = [
        'other_asset_id',
        'principle_id',
        'basis_property_id',
        'approach_id',
        'method_used_id',
        'document_description',
    ];
}
