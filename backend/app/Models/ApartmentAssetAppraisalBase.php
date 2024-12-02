<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApartmentAssetAppraisalBase extends Model
{
    use SoftDeletes;
    protected $table = 'apartment_asset_appraisal_base';
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
        'approach_id',
        'method_used_id',
        'principle_id',
        'basis_property_id',
        'description'
    ];

    public function approach():BelongsTo
    {
        return $this->belongsTo(AppraiseOtherInformation::class,'approach_id', 'id');
    }
    public function methodUsed():BelongsTo
    {
        return $this->belongsTo(AppraiseOtherInformation::class,'method_used_id', 'id');
    }
    public function priciple():BelongsTo
    {
        return $this->belongsTo(AppraiseOtherInformation::class,'principle_id', 'id');
    }
    public function basicProperty():BelongsTo
    {
        return $this->belongsTo(AppraiseOtherInformation::class,'basis_property_id', 'id');
    }
}
