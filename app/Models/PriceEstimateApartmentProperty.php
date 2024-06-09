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
class PriceEstimateApartmentProperty extends Model
{
    use SoftDeletes;
    protected $table = 'price_estimate_apartment_properties';

    protected $fillable = [
        'price_estimate_id',
        'block_id',
        'floor_id',
        'area',
        'apartment_name',
        'bedroom_num',
        'wc_num',
        'handover_year',
        'direction_id',
        'furniture_quality_id',
        'description',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function block(): BelongsTo
    {
        return $this->belongsTo(Block::class, 'block_id', 'id');
    }

    public function floor(): BelongsTo
    {
        return $this->belongsTo(Floor::class, 'floor_id', 'id');
    }

    public function direction(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class, 'direction_id', 'id');
    }

    public function furnitureQuality(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class, 'furniture_quality_id', 'id');
    }
    public function priceEstimate(): BelongsTo
    {
        return $this->belongsTo(PriceEstimate::class, 'price_estimate_id', 'id');
    }
}
