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
class CompareTangibleAsset extends Model
{
    use SoftDeletes;

    protected $casts = [
        'id' => 'integer',
        'total_construction_area' => 'float',
        'remaining_quality' => 'float',
        'total_construction_base' => 'float',
    ];

    protected $fillable = [
        'asset_general_id',
        'building_type_id',
        'building_category_id',
        'total_construction_area',
        'floor',
        'gpxd',
        'remaining_quality',
        'estimation_value',
        'total_construction_base',
        'unit_price_m2',
        'plot_num',
        'start_using_year',
        'compare_property_id',
        'rate_id',
        'structure_id',
        'crane_id',
        'aperture_id',
        'factory_type_id',
        'other_building',
        'description',
    ];

    public function general(): BelongsTo
    {
        return $this->belongsTo(CompareAssetGeneral::class,'asset_general_id','id');
    }

    public function pic(): HasMany
    {
        return $this->hasMany(CompareTangiblePic::class,'compare_tangible_id');
    }

    public function buildingType(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class,'building_type_id','id');
    }

    public function buildingCategory(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class,'building_category_id','id');
    }

    public function compareProperty(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class,'compare_property_id','id');
    }

    public function rate(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class,'rate_id','id');
    }
    public function structure(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class,'structure_id','id');
    }
    public function crane(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class,'crane_id','id');
    }
    public function aperture(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class,'aperture_id','id');
    }
    public function factoryType(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class,'factory_type_id','id');
    }
}
