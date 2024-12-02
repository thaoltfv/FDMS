<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TechnologicalLineCertificateAssetLawInfo extends Model
{
    use SoftDeletes;
    protected $casts = [
        'id' => 'integer',
    ];
    protected $fillable = [
        'id',
        'technology_asset_id',
        'principle_id',
        'basis_property_id',
        'approach_id',
        'method_used_id',
        'document_description',
    ];

    // public function principle():BelongsTo
    // {
    //     return $this->belongsTo(AppraiseOtherInformation::class,'principle_id','id');
    // }

    // public function basicProperties():BelongsTo
    // {
    //     return $this->belongsTo(AppraiseOtherInformation::class,'basis_property_id','id');
    // }

    // public function approach():BelongsTo
    // {
    //     return $this->belongsTo(AppraiseOtherInformation::class,'approach_id','id');
    // }

    // public function methodUsed():BelongsTo
    // {
    //     return $this->belongsTo(AppraiseOtherInformation::class,'method_used_id','id');
    // }
}
