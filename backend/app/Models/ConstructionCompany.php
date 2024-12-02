<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Province
 * @package App\Models
 */
class ConstructionCompany extends Model
{
    protected $table = 'construction_company';
    use SoftDeletes;

    protected $casts = [
        'id' => 'integer',
    ];

    protected $fillable = [
        'appraise_id',
        'construction_company_id',
        'name',
        'address',
        'phone_number',
        'manager_name',
        'unit_price_m2',
        'is_defaults',
        'tangible_asset_id',
    ];
    // public function constructionCompany(): BelongsTo
    // {
    //     return $this->belongsTo(AppraisalConstructionCompany::class,'construction_company_id','id');
    // }
}
