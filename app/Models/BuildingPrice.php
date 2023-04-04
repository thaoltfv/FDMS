<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class BuildingPrice
 * @package App\Models
 */
class BuildingPrice extends Model
{
    use SoftDeletes;

    protected $casts = [
        'id' => 'integer',
        'h1' => 'integer',
        'h2' => 'integer',
        'h3' => 'integer',
        'h4' => 'integer',
        'h5' => 'integer',
        'p1' => 'integer',
        'p2' => 'integer',
        'p3' => 'integer',
        'p4' => 'integer',
        'p5' => 'integer',
    ];

    protected $fillable = [
        'building_category',
        'level',
        'rate',
        'structure',
        'crane',
        'aperture',
        'factory_type',
        'unit_price_m2',
        'effect_from',
        'effect_to',
        'h1','h2','h3','h4','h5',
        'p1','p2','p3','p4','p5',
    ];

    public function categoryBuilding(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class, 'building_category', 'id');
    }
    public function level(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class, 'level', 'id');
    }
    public function structure(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class, 'structure', 'id');
    }
    public function crane(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class, 'crane', 'id');
    }
    public function aperture(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class, 'aperture', 'id');
    }
    public function factoryType(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class, 'factory_type', 'id');
    }
    public function rate(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class, 'rate', 'id');
    }
}
