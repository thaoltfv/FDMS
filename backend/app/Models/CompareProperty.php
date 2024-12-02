<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Province
 * @package App\Models
 */
class CompareProperty extends Model
{
    use SoftDeletes;

    protected $casts = [
        'id' => 'integer',
        'asset_general_land_sum_area' => 'float',
        'front_side' => 'float',
        'individual_road' => 'float',
        'front_side_width' => 'float',
        'insight_width' => 'float',
        'main_road_length' => 'float',
    ];
    protected $fillable = [
        'asset_general_id',
        'coordinates',
        'legal_id',
        'asset_general_land_sum_area',
        'front_side',
        'individual_road',
        'zoning_id',
        'land_type_id',
        'asset_general_value_sum_area',
        'front_side_width',
        'insight_width',
        'land_shape_id',
        'size_description',
        'main_road_length',
        'material_id',
        'business_id',
        'electric_water_id',
        'social_security_id',
        'feng_shui_id',
        'paymen_method_id',
        'condition_id',
        'description',
        'two_sides_land',
    ];

    public function general(): BelongsTo
    {
        return $this->belongsTo(CompareAssetGeneral::class,'asset_general_id','id');
    }

    public function propertyDetail(): HasMany
    {
        return $this->hasMany(ComparePropertyDetail::class,'compare_property_id');
    }

    public function comparePropertyId(): HasMany
    {
        return $this->hasMany(ComparePropertyDetail::class,'compare_property_id');
    }

    public function comparePropertyTurningTime(): HasMany
    {
        return $this->hasMany(ComparePropertyTurningTime::class,'compare_property_id');
    }

    public function comparePropertyDoc(): HasMany
    {
        return $this->hasMany(ComparePropertyDoc::class,'compare_property_id');
    }

    public function pic(): HasMany
    {
        return $this->hasMany(ComparePropertyPic::class,'compare_property_id');
    }

    public function legal(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class,'legal_id','id');
    }

    public function zoning(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class,'zoning_id','id');
    }

    public function landType(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class,'land_type_id','id');
    }

    public function landShape(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class,'land_shape_id','id');
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class,'material_id','id');
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class,'business_id','id');
    }

    public function electricWater(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class,'electric_water_id','id');
    }

    public function socialSecurity(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class,'social_security_id','id');
    }

    public function fengShui(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class,'feng_shui_id','id');
    }

    public function paymenMethod(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class,'paymen_method_id','id');
    }

    public function conditions(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class,'condition_id','id');
    }

}
