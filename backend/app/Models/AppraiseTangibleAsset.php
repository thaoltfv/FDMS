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
class AppraiseTangibleAsset extends Model
{
    use SoftDeletes;
    protected $table = 'appraise_tangible_assets';
    protected $casts = [
        'id' => 'integer',
        'remaining_quality' => 'float',
        'total_construction_base' => 'float',
        'total_construction_area' => 'float',
        'remaining_quality' => 'float',
        'total_desicion_average' => 'integer',
    ];

    protected $fillable = [
        'appraise_id',
        'building_type_id',
        'building_category_id',
        'total_construction_area',
        'floor',
        'gpxd',
        'remaining_quality',
        'total_construction_base',
        'plot_num',
        'start_using_year',
        'rate_id',
        'structure_id',
        'crane_id',
        'aperture_id',
        'factory_type_id',
        'other_building',
        'description',
        'duration',
        'contruction_description',
        'total_desicion_average',
        'tangible_name',
    ];

    public function buildingType(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class,'building_type_id','id');
    }
    public function buildingCategory(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class,'building_category_id','id');
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

    public function constructionCompany(): HasMany
    {
        return $this->hasMany(ConstructionCompany::class, 'tangible_asset_id');
    }

    public function comparisonTangibleFactor(): BelongsTo
    {
        return $this->BelongsTo(AppraiseTangibleComparisonFactor::class, 'id', 'tangible_asset_id');
    }
}
